<?php
/**
 * Background ads class.
 */
class Advanced_Ads_Pro_Module_Background_Ads {

	public function __construct() {
		add_action( 'wp_footer', array( $this, 'footer_injection' ), 20 );

		// Register output change hook.
		add_action( 'advanced-ads-output-final', array( $this, 'ad_output' ), 20, 3 );
	}

	public function footer_injection(){
		// stop, if main plugin doesn’t exist
		if ( ! class_exists( 'Advanced_Ads', false ) ) {
		    return;
		}

		// get placements
		$placements = get_option( 'advads-ads-placements', array() );
		if( is_array( $placements ) ){
			foreach ( $placements as $_placement_id => $_placement ){
				if ( isset($_placement['type']) && 'background' == $_placement['type'] ){
					// display the placement content with placement options
					$_options = isset( $_placement['options'] ) ? $_placement['options'] : array();
					echo Advanced_Ads_Select::get_instance()->get_ad_by_method( $_placement_id, 'placement', $_options );
				}
			}
		}
	}

	/**
	 * Change ad output.
	 *
	 * @param string          $output Ad output.
	 * @param Advanced_Ads_Ad $ad Ad object.
	 * @param array           $output_options Output options.
	 * @return string
	 */
	public function ad_output( $output, $ad, $output_options ) {
		if ( ! isset( $ad->args['placement_type'] ) || 'background' !== $ad->args['placement_type'] ) {
			return $output;
		}

		if( !isset( $ad->type ) || 'image' !== $ad->type ){
			return $output;
		}

		// get background color
		$bg_color = isset( $ad->args['bg_color'] ) ? sanitize_text_field( $ad->args['bg_color'] ) : false;

		// get tracking plugin
		$click_tracking_active = false;
		$plugin_active = class_exists( 'Advanced_Ads_Tracking_Plugin', false );
		if ( $plugin_active ) {
			$click_tracking_active = Advanced_Ads_Tracking_Plugin::get_instance()->check_ad_tracking_enabled( $ad, 'click' );
		}

		// get prefix and generate new body class
		$prefix = Advanced_Ads_Plugin::get_instance()->get_frontend_prefix();
		$class = $prefix . 'body-background';

		// get correct link
		if ( class_exists( 'Advanced_Ads_Tracking' ) && method_exists( 'Advanced_Ads_Tracking', 'build_click_tracking_url' ) ) {
		    $link = Advanced_Ads_Tracking::build_click_tracking_url( $ad );
		} elseif( !empty( $ad->url ) ) {
		    $link = $ad->url;
		} elseif( !empty( $ad->output['url'] ) ) { // might no longer be needed
		    $link = $ad->output['url'];
		} else {
		    $link = false;
		}

		// get image
		if( isset( $ad->output['image_id'] ) ){
		    $image = wp_get_attachment_image_src( $ad->output['image_id'], 'full' );
		    if ( $image ) {
			list( $image_url, $image_width, $image_height ) = $image;
		    }
		}

		if( empty( $image_url ) ){
		    return $output;
		}

		$selector = apply_filters( 'advanced-ads-pro-background-selector', 'body' );

		ob_start();
		?><style><?php echo $selector; ?> {
			    background: url(<?php echo $image_url; ?>) no-repeat fixed;
			    background-size: 100% auto;
			<?php if( $bg_color ) : ?>
			    background-color: <?php echo $bg_color; ?>;
			<?php endif; ?>
		    }
		<?php if( $link && ( ! function_exists( 'advads_is_amp' ) || ! advads_is_amp() )) : ?>
		    <?php /**
		    * We should not use links and other tags that should have cursor: pointer as direct childs of the $selector.
		    * That is, we need a nested container (e.g. body > div > a) to make it work corretly. */
		    echo $selector; ?> { cursor: pointer; } <?php echo $selector; ?> > * { cursor: default; }
		<?php endif; ?>
		</style>
		<?php /**
		 * Don't load any javascript on amp.
		 * Javascript output can be prevented by disabling click tracking and empty url field on ad level.
		 */
		if ( ( ! function_exists( 'advads_is_amp' ) || ! advads_is_amp() ) && ( $click_tracking_active || $link ) ) : ?>
			<script>
				( window.advanced_ads_ready || document.readyState === 'complete' ).call( null, function () {
					var pluginActive         = <?php echo( $plugin_active ? 'true' : 'false' ); ?>;
					var clickTrackingActive = <?php echo( $click_tracking_active ? 'true' : 'false' ); ?>;
					<?php if ( $link ) : ?>
					// Use event delegation because $selector may be not in the DOM yet.
					document.addEventListener( 'click', function ( e ) {

						if ( e.target.matches( '<?php echo $selector; ?>' ) ) {
							var url = '<?php echo $ad->url; ?>';

							if ( pluginActive ) {
								var cloaking = <?php echo( (bool) $ad->options( 'tracking.cloaking' ) ? 'true' : 'false' ); ?>;

								if ( cloaking || typeof AdvAdsClickTracker === 'undefined' ) {
									// Fallback to redirect url if cloaking is activated or AdvAdsClickTracker is not defined, e.g. Tracking 1.x.
									url = '<?php echo $link; ?>';
									e.target.setAttribute( 'data-advadsredirect', Number( cloaking ) );
								}

								// Use ajax click tracking if available.
								if ( typeof AdvAdsClickTracker !== 'undefined' && clickTrackingActive ) {
									// Gather information for tracking call.
									e.target.setAttribute( 'data-advadstrackid', <?php echo $ad->id; ?>);
									e.target.setAttribute( 'data-advadstrackbid', <?php echo get_current_blog_id(); ?>);

									AdvAdsClickTracker.ajaxSend( e.target );
								}
							}

							// Open url in new tab.
							window.open( url, '_blank' );
						}
					} );
					<?php endif; ?>
					document.querySelector( '<?php echo $selector; ?>' ).classList.add( '<?php echo $class; ?>' );
				} );
			</script>
		<?php endif; ?>
		<?php

		// add content of Custom Code option here since the normal hook can’t be used.
		$output_options = $ad->options( 'output' );

		if ( ! empty( $output_options['custom-code'] ) ) {
			echo $output_options['custom-code'];
		}

		return ob_get_clean();

		//return $output;

	}
}
