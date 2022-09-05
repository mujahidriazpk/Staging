<?php

class Analytify_AMP {

	public function __construct() {

		$this->admin_hooks();
		$this->public_hooks();
		$this->includes();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function admin_hooks() {

		add_filter( 'analytify_page_path_filter', array( $this, 'add_amp_pagepath' ), 20, 2 );
		add_action( 'admin_notices' , array( $this, 'notics_install_active_amp' ) );
	}

	/*
	 * check if the AMP plugin is installed or not and active or not
	 * exit if not installed/active
	 */
	public function notics_install_active_amp(){

		// don't show the alert on the update page
		$current_screen = get_current_screen();
		if ( isset($current_screen->base) AND 'update' === $current_screen->base ) {
			return;
		}
		
		// install check
		if ( ! file_exists( WP_PLUGIN_DIR . '/amp/amp.php' ) ) {
			$action = 'install-plugin';
			$slug   = 'amp';
			$link   = wp_nonce_url( add_query_arg( array( 'action' => $action, 'plugin' => $slug ), admin_url( 'update.php' ) ), $action . '_' . $slug );

			$message = sprintf('%1$s <a href="%2$s">%3$s</a>' , esc_html__( 'Analytify\'s AMP requires the AMP plugin to be installed &mdash;', 'wp-analytify-pro' ), $link, 'Click here to install the official plugin from the AMP project' );

			wp_analytify_pro_notice(  $message, 'wp-analytify-danger' );
		}
		
		if ( file_exists( WP_PLUGIN_DIR . '/amp/amp.php' ) AND ! is_plugin_active( 'amp/amp.php' ) ) {
			$action = 'activate';
			$slug   = 'amp/amp.php';
			$link   = wp_nonce_url( add_query_arg( array( 'action' => $action, 'plugin' => $slug ), admin_url( 'plugins.php' ) ), $action . '-plugin_' . $slug );
			
			$message = sprintf('%1$s <a href="%2$s">%3$s</a>' , esc_html__( 'Analytify\'s AMP requires the AMP plugin to be active &mdash;', 'wp-analytify-pro' ), $link, esc_html__( 'Click here to activate the AMP plugin', 'wp-analytify-pro' ) );
			
			wp_analytify_pro_notice(  $message, 'wp-analytify-danger' );
		}

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function public_hooks() {

		add_filter( 'analytify_tracking_code_create_option', array( $this, 'use_amp_client_id' ) );
		add_action( 'amp_post_template_head', array( $this, 'add_amp_client_id_meta' ), 15 );
		add_filter( 'amp_post_template_analytics', array( $this, 'add_analytics_code' ) );
	}

	public function includes() {

		require ANALYTIFY_PRO_ROOT_PATH	 . '/inc/modules/amp/includes/tracking.php';
	}

	/**
	 * Use AMP Client Api in tracking code..
	 *
	 * @since 1.0.0
	 */
	function use_amp_client_id( $create ) {

		$create['useAmpClientId'] = true;
		return $create;
	}

	/**
	 * Add AMP Client ID meta.
	 *
	 * @since 1.0.0
	 */
	function add_amp_client_id_meta( $tenplate ) {

		echo '<meta name="analytify-amp-google-client-id-api" content="googleanalytics">';
	}

	function add_amp_pagepath( $filter, $u_post ) {

		return $filter . ',ga:pagePath==' . $u_post['path'] . '?amp=1';
	}

	/**
	 * Add Analytics Code on Footer.
	 *
	 * @since 1.0.0
	 */
	function add_analytics_code( $analytics ) {

		// Check if user tracking is enabled.
		if ( false == analytify_is_track_user() ) {
			return $analytics;
		}

		$UA_CODE = WP_ANALYTIFY_FUNCTIONS::get_UA_code();

		// Check if UA code setup.
		if ( ! $UA_CODE ) {
			return $analytics;
		}

		$analytics['analytify-googleanalytics'] = array(
			'type'        => 'googleanalytics',
			'attributes'  => array(),
			'config_data' => array(
				'vars'     => array(
					'account' => $UA_CODE,
				),
				'triggers' => array(
					'trackPageview' => array(
						'on'      => 'visible',
						'request' => 'pageview',
					),
				),
			),
		);

		return $analytics;
	}
}

new Analytify_AMP();