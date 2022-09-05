<?php

/**
 * Admin bar functionality.
 */
class Advanced_Ads_Pro_Module_Admin_Bar {
	/**
	 * Constructor
	 */
	public function __construct() {
		// TODO load options
		// add admin bar item with current ads
		if ( ! is_admin() ) {
			add_action( 'admin_bar_menu', array( $this, 'admin_bar_current_ads' ), 999 );
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 11 );
		add_action( 'wp_footer', array( $this, 'output_items' ), 21 );
	}

	/**
	 * Add admin bar menu with current displayed ads and ad groups.
	 *
	 * @since 1.0.0
	 * @param WP_Admin_Bar $wp_admin_bar Admin bar class.
	 */
	public function admin_bar_current_ads( $wp_admin_bar ) {
		$cap = method_exists( 'Advanced_Ads_Plugin', 'user_cap' ) ? Advanced_Ads_Plugin::user_cap( 'advanced_ads_edit_ads' ) : 'manage_options';

		if ( ! current_user_can( $cap ) ) {
			return;
		}

		// Add main menu item.
		$args = array(
			'id'    => 'advads_current_ads',
			'title' => __( 'Ads', 'advanced-ads-pro' ),
			'href'  => false,
		);
		$wp_admin_bar->add_node( $args );

		$args = array(
			'parent' => 'advads_current_ads',
			'id'     => 'advads_no_ads_found',
			'title'  => __( 'No Ads found', 'advanced-ads-pro' ),
			'href'   => false,
		);
		$wp_admin_bar->add_node( $args );
	}

	/**
	 * Enqueue the admin bar script.
	 */
	public function enqueue_scripts() {
		if ( ! is_admin_bar_showing() ) {
			return;
		}

		$uri_rel_path = AAP_BASE_URL . 'assets/js/';

		$deps = array( 'jquery' );
		if ( wp_script_is( 'advanced-ads-pro/cache_busting' ) ) {
			$deps[] = 'advanced-ads-pro/cache_busting';
		}

		wp_enqueue_script( 'advanced-ads-pro/cache_busting_admin_bar', $uri_rel_path . 'admin_bar.js', $deps, AAP_VERSION, true );
	}

	/**
	 * Output items that do not use cache-busting.
	 */
	public function output_items() {
		// Add item for each ad
		$ads   = Advanced_Ads::get_instance()->current_ads;
		$nodes = array();

		foreach ( $ads as $_key => $_ad ) {
			// TODO $type not used .
			// TODO types are extendable through Advanced_Ads_Select.
			$type = '';
			switch ( $_ad['type'] ) {
				case 'ad':
					$type = esc_html__( 'ad', 'advanced-ads-pro' );
					break;
				case 'group':
					$type = esc_html__( 'group', 'advanced-ads-pro' );
					break;
				case 'placement':
					$type = esc_html__( 'placement', 'advanced-ads-pro' );
					break;
			}

			$nodes[] = array(
				'title' => esc_html( $_ad['title'] ),
				'type'  => $type,
			);
		}

		$content = sprintf( '<script>window.advads_admin_bar_items = %s;</script>', wp_json_encode( $nodes ) );

		if ( class_exists( 'Advanced_Ads_Utils' ) && method_exists( 'Advanced_Ads_Utils', 'get_inline_asset' ) ) {
			$content = Advanced_Ads_Utils::get_inline_asset( $content );
		}
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the output is already escaped, we can't escape it again without breaking the HTML.
		echo $content;
	}
}
