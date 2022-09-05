<?php
if (!defined('WPINC')) {
	die;
}

class Aaabs_Adsense_Public_Facing
{
	/**
	 * Constructor
	 */
	public function __construct() {
		// register events when all plugins are loaded
		add_action( 'plugins_loaded', array( $this, 'wp_admin_plugins_loaded' ) );
	}

	/**
	 * load actions and filters
	 */
	public function wp_admin_plugins_loaded(){

		if( ! class_exists( 'Advanced_Ads', false ) ) {
			return;
		}

		// Filer function for responsive ad with custom css.
		add_filter('advanced-ads-gadsense-responsive-output', array($this, 'render_output'), 10, 3);
	}

    /**
     * render special responsive ads output
     *
     * @param str $output, obj $ad ad object
     * @return str $content, ad content prepared for frontend output
     */
    public function render_output($output, $ad, $pub_id) {
		$content = json_decode( stripslashes( $ad->content) );
		
		if (isset($content->unitType) && 'responsive' == $content->unitType && isset($content->resize) ) {
			switch( $content->resize ) {
			    case 'manual' :
				// The ad use custom css for resizing
				global $gadsense;
				$count = $gadsense['adsense_count'];

				$selector = 'gadsense_slot_' . $count;

				$output .= '<style type="text/css">' . "\n";

				// The last rule hide the ad
				$last_rule_hidden = null;

				if (isset($content->defaultHidden) && true == $content->defaultHidden) {
					$output .= '.' . $selector . '{display: none;}' . "\n";
					$last_rule_hidden = true;
				} else {
					if (!empty($ad->width) || !empty($ad->height)) {
						$w = (!empty($ad->width)) ? 'width: ' . $ad->width . 'px;' : '';
						$h = (!empty($ad->height)) ? 'height: ' . $ad->height . 'px;' : '';
						$output .= '.' . $selector . '{ display: inline-block; ' . $w . ' ' . $h . '}' . "\n";
					}
				}
				if (!empty($content->media)) {
					foreach ($content->media as $value) {

						$rule = explode(':', $value);
						$hidden = (isset($rule[3]) && '1' == $rule[3])? true : false;

						if ($hidden) {
							// the ad is hidden for this min-width
							$output .= '@media (min-width:' . $rule[0] . 'px) { .' . $selector . ' { display: none;} }' . "\n";

							// Mark this flag to true, so on the next iteration, the display attribute can be set to inline-block (if not hidden)
							$last_rule_hidden = true;

						} else {
							/**
							 * Not hidden, but firstly check if the lastly defined rule hide the ad
							 */
							if ($last_rule_hidden) {
								$output .= '@media (min-width:' . $rule[0] . 'px) { .' . $selector . ' { display: inline-block; width: ' . $rule[1] . 'px; height: ' . $rule[2] . 'px; } }' . "\n";
								$last_rule_hidden = false;
							} else {
								// do not touch the $last_rule_hidden var, it is already FALSE or NULL
								$output .= '@media (min-width:' . $rule[0] . 'px) { .' . $selector . ' { width: ' . $rule[1] . 'px; height: ' . $rule[2] . 'px; } }' . "\n";
							}
						}

					}
				}
				$output .= '</style>' . "\n";

				$output .= '<ins class="adsbygoogle ' . $selector . '" ';

				if (null === $last_rule_hidden) {
					/**
					 * If none of all the rules (including default sizes) has hidden the rule, this flag should be NULL
					 * So we can add the following style attribute.
					 */
					$output .= 'style="display:inline-block;" ';
				}

				$output .= 'data-ad-client="ca-' . $pub_id . '" ' . "\n";
				$output .= 'data-ad-slot="' . $content->slotId . '" ' . "\n";
				$output .= '></ins>' . "\n";
				$output .= '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>' . "\n";
				$output .= '<script> ' . "\n";
				$output .= '(adsbygoogle = window.adsbygoogle || []).push({}); ' . "\n";
				$output .= '</script>' . "\n";
				break;
			    case 'horizontal' :
			    case 'rectangle' :
			    case 'vertical' :
				$output .= '<ins class="adsbygoogle" ';
				$output .= 'style="display:block;" ' . "\n";
				$output .= 'data-ad-client="ca-' . $pub_id . '" ' . "\n";
				$output .= 'data-ad-slot="' . $content->slotId . '" ' . "\n";
				$output .= 'data-ad-format="' . $content->resize . '" ' . "\n";
				$output .= '></ins>' . "\n";
				$output .= '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>' . "\n";
				$output .= '<script>(adsbygoogle = window.adsbygoogle || []).push({}); </script>' . "\n";
				break;
			}
		}

		if ( isset( $content->unitType ) && 'matched-content' == $content->unitType ) {
			$settings = self::get_matched_content_settings( $content );
			if ( ! $settings['customize_enabled'] ) {
				return $output;
			}

			$output .= '<ins class="adsbygoogle" ';
			$output .= 'style="display:block;" ' . "\n";
			$output .= 'data-ad-client="ca-' . $pub_id . '" ' . "\n";
			$output .= 'data-ad-slot="' . $content->slotId . '" ' . "\n";
			$output .= 'data-ad-format="autorelaxed"' . "\n";
			$output .= sprintf( 'data-matched-content-ui-type="%s,%s"', $settings['ui_type_m'], $settings['ui_type'] ) . "\n";
			$output .= sprintf( 'data-matched-content-rows-num="%s,%s"', $settings['rows_num_m'], $settings['rows_num'] ) . "\n";
			$output .= sprintf( 'data-matched-content-columns-num="%s,%s"', $settings['columns_num_m'], $settings['columns_num'] ) . "\n";
			$output .= '></ins>' . "\n";
			$output .= '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>' . "\n";
			$output .= '<script>(adsbygoogle = window.adsbygoogle || []).push({}); </script>' . "\n";
		}

		return $output;
	}

	/**
	 * Get settings of Adsense matched content.
	 *
	 * @param obj $content parsed Ad content.
	 * @return array
	 */
	public static function get_matched_content_settings( $content ) {
		return array(
			'customize_enabled' => ! empty( $content->matched_content_customize_enabled ),
			'ui_type_m' => ( isset( $content->matched_content_ui_type_m ) ) ? $content->matched_content_ui_type_m: 'image_sidebyside',
			'ui_type' => ( isset( $content->matched_content_ui_type ) ) ? $content->matched_content_ui_type: 'image_sidebyside',
			'rows_num_m' => ! empty( $content->matched_content_rows_num_m ) ? absint( $content->matched_content_rows_num_m ) : 2,
			'rows_num' => ! empty( $content->matched_content_rows_num ) ? absint( $content->matched_content_rows_num ) : 2,
			'columns_num_m' => ! empty( $content->matched_content_columns_num_m ) ? absint( $content->matched_content_columns_num_m ) : 2,
			'columns_num' => ! empty( $content->matched_content_columns_num ) ? absint( $content->matched_content_columns_num ) : 2,
		);
	}
}
