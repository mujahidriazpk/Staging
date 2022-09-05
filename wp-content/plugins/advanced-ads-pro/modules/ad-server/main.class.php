<?php

/**
 * Allow serving ads on external URLs.
 *
 * Class Advanced_Ads_Pro_Module_Ad_Server
 */
class Advanced_Ads_Pro_Module_Ad_Server {

	/**
	 * Advanced_Ads_Pro_Module_Ad_Server constructor.
	 */
	public function __construct() {
		// Register frontend AJAX calls.
		add_action( 'wp_ajax_aa-server-select', array( $this, 'get_placement' ) );
		add_action( 'wp_ajax_nopriv_aa-server-select', array( $this, 'get_placement' ) );

		// Add allowed HTTP origins.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			add_filter( 'allowed_http_origins', array( $this, 'add_allowed_origins' ) );
		}
	}

	/**
	 * Load placement content
	 *
	 * Based on Advanced_Ads_Ajax::advads_ajax_ad_select()
	 */
	public function get_placement() {
		$options           = Advanced_Ads_Pro::get_instance()->get_options();
		$block_no_referrer = ! empty( $options['ad-server']['block-no-referrer'] ); // True if option is set.

		// Prevent direct access through the URL.

		if ( $block_no_referrer && ! isset( $_SERVER['HTTP_REFERER'] ) ) {
			die( 'direct access forbidden' );
		}

		// Set correct frontend headers.
		header( 'X-Robots-Tag: noindex,nofollow' );
		header( 'Content-Type: text/html; charset=UTF-8' );

		$embedding_urls = $this->get_embedding_urls();

		// Allow request from specific URL.
		if ( $embedding_urls ) {
			// Allow this placement to being implemented through an iframe.
			// See https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/frame-ancestors.
			// Replaces 'X-Frame-Options: ALLOW-FROM.
			// This header works with multiple URLs that are separated by space.
			// Add_allowed_origins() is still needed.
			$embedding_urls_string = implode( ' ', $embedding_urls );
			header( 'Content-Security-Policy: frame-ancestors ' . $embedding_urls_string );
		}

		$public_slug = isset( $_REQUEST['p'] ) ? esc_attr( $_REQUEST['p'] ) : null;
		if ( empty( $public_slug || ! is_string( $public_slug ) ) ) {
			die( 'missing p parameter' );
		}

		// Get placement output by public slug.

		$placement_content = $this->get_placement_output_by_public_slug( $public_slug );

		include dirname( __FILE__ ) . '/views/frontend-template.php';

		die();
	}

	/**
	 * Get the content of a placement based on the public slug.
	 *
	 * @param string $public_slug placement ID or public slug.
	 */
	private function get_placement_output_by_public_slug( $public_slug = '' ) {
		if ( '' === $public_slug ) {
			return '';
		}

		// Load all placements.
		$placements = Advanced_Ads::get_instance()->get_model()->get_ad_placements_array();

		/**
		 * We need to force the ad to open in a new window when the link is created through Advanced Ads. Otherwise,
		 * clicking the ad in an iframe would load the target page in the iframe, too.
		 *
		 * 1. The Tracking add-on has a dedicated option on the ad edit page for this.
		 * We are setting it to open in a new window here and ignore the options the user might have set.
		 */
		$args['change-ad']['tracking']['target'] = 'new';
		
		// Ignore consent settings for ad-server ads.
		$args['privacy']['ignore-consent'] = true;

		/**
		 * 2. The Advanced Ads plugin adds target="_blank" based on a global option
		 * We change force that option to open ads in a new window by hooking into the advanced-ads-options filter below.
		 */
		add_filter(
			'advanced-ads-options',
			function( $options ) {
				$options['target-blank'] = 1;
				return $options;
			}
		);

		// Return placement if there is one with public_slug being the placement ID.
		if ( isset( $placements[ $public_slug ] ) ) {
				return Advanced_Ads_Placements::output( $public_slug, $args );
		}

		// Iterate through "ad-server" placements and look for the one with the public slug.
		foreach ( $placements as $_placement_slug => $_placement ) {
			if ( 'server' === $_placement['type']
				&& isset( $_placement['options']['ad-server-slug'] )
				&& $public_slug === $_placement['options']['ad-server-slug'] ) {
					return Advanced_Ads_Placements::output( $_placement_slug, $args );
			}
		}
	}

	/**
	 * Add allowed HTTP origins.
	 * Needed for the JavaScript-based implementation of the placement.
	 *
	 * @param array $origins Allowed HTTP origins.
	 * @return array $origins Allowed HTTP origins.
	 */
	public function add_allowed_origins( $origins ) {

		$embedding_urls = $this->get_embedding_urls();

		if ( is_array( $embedding_urls ) && count( $embedding_urls ) ) {
			$origins = array_merge( $origins, $embedding_urls );
		}
		return $origins;
	}

	/**
	 * Get the embedding URL array
	 *
	 * @return array $embedding_urls.
	 */
	public function get_embedding_urls() {
		$options              = Advanced_Ads_Pro::get_instance()->get_options();
		$embedding_url_option = isset( $options['ad-server']['embedding-url'] ) ? $options['ad-server']['embedding-url'] : false;

		$embedding_urls_raw = explode( ',', $embedding_url_option );

		$embedding_urls = array();
		foreach ( $embedding_urls_raw as $_url ) {
			$embedding_urls[] = esc_url_raw( $_url );
		}

		return $embedding_urls;
	}
}

