<?php
if (!defined('WPINC')) {
	die;
}

class Aaabs_Adsense_Param_Hooking
{
	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter('advanced-ads-gadsense-ad-param-data', array($this, 'ad_param_data'), 10, 3);
		add_filter('advanced-ads-gadsense-responsive-sizing', array($this, 'enable_manual_css'), 10, 1);
		add_action('advanced-ads-gadsense-extra-ad-param', array($this, 'extra_template'), 10, 2);
	}

	/**
	 * Prepare data before rendering ad parameters
	 */
	public function ad_param_data($extra_params, $content, $ad) {
		if ('manual' == $content->resize) {
			$extra_params['default_width'] = $ad->width;
			$extra_params['default_height'] = $ad->height;
			$extra_params['default_hidden'] = (isset($content->defaultHidden) && $content->defaultHidden)? true : false;
			if( isset( $content->media ) ) foreach ($content->media as $rule) {
				$exploded = explode(':', $rule);
				$new_rule = array(
					'minw' => $exploded[0],
					'w' => $exploded[1],
					'h' => $exploded[2],
				);
				$hidden = (isset($exploded[3]) && '1' == $exploded[3])? true : false;
				$new_rule['hidden'] = $hidden;
				$extra_params['at_media'][] = $new_rule;
			}
		}
		return $extra_params;
	}

	/**
	 * Adds the manual css option to the list of available resizing mode.
	 * @param arr $resize, associative array with the mode's slug as key and the displayed text (within a <select />) as value.
	 *
	 * @return arr $resize, the modified list
	 */
	public function enable_manual_css($resize) {
		$resize['horizontal'] = __('horizontal', 'advanced-ads-responsive');
		$resize['rectangle'] = __('rectangle', 'advanced-ads-responsive');
		$resize['vertical'] = __('vertical', 'advanced-ads-responsive');
		$resize['manual'] = __('advanced', 'advanced-ads-responsive');
		return $resize;
	}

	/**
	 * Draws manual css fields/inputs on adsense ad param meta box
	 *
	 * @param array $extra_params, array of extra parameters; $content, the ad content object
	 */
	public function extra_template($extra_params, $content) {
		$is_responsive = (isset($content->unitType) && 'responsive' == $content->unitType) ? true : false;
		$use_manual_css = (isset($content->resize) && 'manual' == $content->resize) ? true : false;
		include(AAR_ADSENSE_PATH . 'admin/views/ad-params-manual-css.php');

		$unit_type = isset( $content->unitType ) ? $content->unitType : 'normal';
		$is_supported = 'matched-content' === $unit_type;
		$settings = Aaabs_Adsense_Public_Facing::get_matched_content_settings( $content );
		$types = array( 'image_sidebyside', 'image_card_sidebyside', 'image_stacked', 'image_card_stacked', 'text', 'text_card' );
		include(AAR_ADSENSE_PATH . 'admin/views/ad-params-responsive-matched-content.php');
	}
}
