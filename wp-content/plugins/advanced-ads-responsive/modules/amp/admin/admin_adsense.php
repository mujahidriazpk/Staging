<?php
/**
 * Class Advanced_Ads_Responsive_Amp_Adsense_Admin
 */
class Advanced_Ads_Responsive_Amp_Adsense_Admin {
	/**
	 * Advanced_Ads_Responsive_Amp_Adsense_Admin constructor.
	 */
	public function __construct() {
		add_filter( 'advanced-ads-ad-notices', array( $this, 'ad_notices' ), 10, 3 );
		add_action( 'advanced-ads-gadsense-extra-ad-param', array( $this, 'extra_template' ), 10, 3 );
		add_filter( 'advanced-ads-save-options', array( $this, 'save_ad_options' ), 10, 2 );
	}

	/**
	 * Show warning if a non-AMP compatible option is selected.
	 *
	 * @param array   $notices Notices.
	 * @param array   $box meta box information.
	 * @param WP_Post $post WP post object.
	 * @return array $notices Notices.
	 */
	public function ad_notices( $notices, $box, $post ) {
		if ( Advanced_Ads_Responsive_Amp_Admin::has_amp_plugin() ) {
			switch ( $box['id'] ) {
				case 'ad-parameters-box':
					// Add warning if this is an non-AMP compatible AdSense ad.
					// Hidden by default and made visible with JS.
					$notices[] = array(
						'text'  => __( 'This ad type is not supported on AMP pages', 'advanced-ads-responsive' ),
						'class' => 'advanced-ads-adsense-amp-warning hidden',
					);
					break;
			}
		}

		return $notices;
	}

	/**
	 * Shows AMP related fields/inputs in adsense ad param meta box.
	 *
	 * @param array           $extra_params, array of extra parameters.
	 * @param string          $content ad content.
	 * @param Advanced_Ads_Ad $ad ad object.
	 */
	public function extra_template( $extra_params, $content, $ad = null ) {
		if ( ! $ad ) {
			return; }

		$is_supported = Advanced_Ads_Responsive_Amp::is_supported_adsense_type( $content );
		$options      = $ad->options( 'amp', array() );
		$option_name  = 'advanced_ad[amp]';
		$width        = absint( $ad->width );
		$height       = absint( $ad->height );

		$layout       = ! empty( $options['layout'] ) ? $options['layout'] : 'default';
		$width        = ! empty( $options['width'] ) ? absint( $options['width'] ) : ( $width ? $width : 300 );
		$height       = ! empty( $options['height'] ) ? absint( $options['height'] ) : ( $height ? $height : 250 );
		$fixed_height = ! empty( $options['fixed_height'] ) ? absint( $options['fixed_height'] ) : ( $height ? $height : 250 );

		include AAR_BASE_PATH . '/modules/amp/admin/views/adsense-size.php';
	}

	/**
	 * Sanitize and save ad options.
	 *
	 * @param array           $options ad options.
	 * @param Advanced_Ads_Ad $ad ad object.
	 * @return array $options
	 */
	public function save_ad_options( array $options, Advanced_Ads_Ad $ad ) {
		// phpcs:ignore
		if ( 'adsense' === $ad->type && isset( $_POST['advanced_ad']['amp'] ) ) {
			// phpcs:ignore
			foreach ( (array) $_POST['advanced_ad']['amp'] as $_field => $_data ) {
				$options['amp'][ sanitize_key( $_field ) ] = sanitize_key( $_data );
			}
		}
		return $options;
	}


}



