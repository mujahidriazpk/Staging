<?php
/**
 * Class containing information about the AMP ad type
 */
class Advanced_Ads_Ad_Type_Amp extends Advanced_Ads_Ad_Type_Abstract{

	/**
	 * ID - internal type of the ad type
	 */
	public $ID = 'amp';

	/**
	 * set basic attributes
	 */
	public function __construct() {
		$this->title = 'AMP'; // no translation, since it is an international acronym
		$this->description = __( 'Ads that are visible on Accelerated Mobile Pages', 'advanced-ads-responsive' );
		$this->parameters = array(
			'content' => ''
		);
	}

	/**
	 * output for the ad parameters metabox
	 *
	 * this will be loaded using ajax when changing the ad type radio buttons
	 * echo the output right away here
	 *
	 * @param obj $ad ad object
	 */
	public function render_parameters( $ad ) {
		$options = $ad->options();
		$fallback = ! empty( $options['amp']['fallback'] ) ? $options['amp']['fallback'] : '';

		if ( ! empty( $options['amp']['attributes'] ) && is_array( $options['amp']['attributes'] ) ) {
			$attributes = $options['amp']['attributes'];
		} else {
			$attributes = array( '' => '' );
		}

		include AAR_BASE_PATH . '/modules/amp/admin/views/ad-params.php';
	}

	/**
	 * prepare the ads frontend output
	 *
	 * @param obj $ad ad object
	 * @return str $content ad content prepared for frontend output
	 */
	public function prepare_output( $ad ) {
		$options = $ad->options();
		$attributes = ( ! empty( $options['amp']['attributes'] ) && is_array( $options['amp']['attributes'] ) ) ? $options['amp']['attributes']: array();

		if ( ! empty( $ad->width ) ) {
			$attributes['width'] = $ad->width;
		}

		if ( ! empty( $ad->height ) ) {
			$attributes['height'] = $ad->height;
		}

		$content = '';
		if ( ! empty( $attributes['type'] ) ) {
			$attr_string = array();
			foreach ( $attributes as $_attribute => $_data ) {
				$attr_string[] = sprintf( "%s='%s'", sanitize_key( $_attribute ), esc_attr( $_data ) );
			}
			$attr_string = implode( ' ', $attr_string );

			if ( ! empty( $options['amp']['fallback'] ) ) {
				$content = sprintf( '<div fallback>%s</div>', esc_html( $options['amp']['fallback'] ) );
			}

			return sprintf( '<%1$s %2$s>%3$s</%1$s>', 'amp-ad', $attr_string, $content );
		}

		return '';
	}
}