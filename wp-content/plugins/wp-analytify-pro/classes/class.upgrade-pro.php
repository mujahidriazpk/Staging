<?php
if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly.
}

class WP_Analytify_Pro_Compatibility_Upgrade{

	private $setting_front     = array();
	private $setting_dashboard = array();
	private $advanced_settings = array();

	function __construct() {
		// add_action( 'plugins_loaded' , array( $this, 'pro_upgrade_routine' ) );
		$this->pro_upgrade_routine();
	}

	public function pro_upgrade_routine() {
		$this->setting_front();
		$this->setting_dashboard();
		$this->advanced_settings();
	}

	public function setting_front() {
		if ( get_option( 'analytify_disable_front' ) == '1' ) {

			$this->setting_front['disable_front_end'] = 'on';
			delete_option( 'analytify_disable_front' );
		} else {
			delete_option( 'analytify_disable_front' );
		}

		if ( get_option( 'post_analytics_access' ) ) {

			$this->setting_front['show_analytics_roles_front_end'] = get_option( 'post_analytics_access' );
			delete_option( 'post_analytics_access' );
		}

		if ( get_option( 'analytify_posts_stats_front' ) ) {

			$this->setting_front['show_analytics_post_types_front_end'] = get_option( 'analytify_posts_stats_front' );
			delete_option( 'analytify_posts_stats_front' );
		}

		if ( get_option( 'post_analytics_settings_front' ) ) {

				$this->setting_front['show_panels_front_end'] = get_option( 'post_analytics_settings_front' );
				delete_option( 'post_analytics_settings_front' );
		}

		if ( get_option( 'post_analytics_exclude_posts_front' ) ) {

				$this->setting_front['exclude_pages_front_end'] = get_option( 'post_analytics_exclude_posts_front' );
				delete_option( 'post_analytics_exclude_posts_front' );
		}

		if ( ! empty( $this->setting_front ) ) {
			update_option( 'wp-analytify-front' , $this->setting_front );
		}

	}

	public function setting_dashboard() {
		if ( get_option( 'analytify_dashboard_cache' ) == '1' ) {

			$this->setting_dashboard['delete_dashboard_cache'] = 'on';
			delete_option( 'analytify_dashboard_cache' );
		} else {
			delete_option( 'analytify_dashboard_cache' );
		}

		if ( get_option( 'access_role_dashboard' ) ) {

				$this->setting_dashboard['show_analytics_roles_dashboard'] = get_option( 'access_role_dashboard' );
				delete_option( 'access_role_dashboard' );
		}

		if ( get_option( 'dashboard_panels' ) ) {

				$this->setting_dashboard['show_analytics_panels_dashboard'] = get_option( 'dashboard_panels' );
				delete_option( 'dashboard_panels' );
		}

		if ( ! empty( $this->setting_dashboard ) ) {
			update_option( 'wp-analytify-dashboard', $this->setting_dashboard );
		}

	}

	public function advanced_settings() {

		if ( get_option( 'ANALYTIFY_USER_KEYS' ) == 'Yes' ) {

			$this->advanced_settings['user_advanced_keys'] = 'on';
			delete_option( 'ANALYTIFY_USER_KEYS' );
		}

		if ( get_option( 'ANALYTIFY_CLIENTID' ) ) {

			$this->advanced_settings['client_id'] = get_option( 'ANALYTIFY_CLIENTID' );
			delete_option( 'ANALYTIFY_CLIENTID' );
		}

		if ( get_option( 'ANALYTIFY_CLIENTSECRET' ) ) {

			$this->advanced_settings['client_secret'] = get_option( 'ANALYTIFY_CLIENTSECRET' );
			delete_option( 'ANALYTIFY_CLIENTSECRET' );
		}

		if ( get_option( 'ANALYTIFY_REDIRECT_URI' ) ) {

			$this->advanced_settings['redirect_uri'] = get_option( 'ANALYTIFY_REDIRECT_URI' );
			delete_option( 'ANALYTIFY_REDIRECT_URI' );
		}

		if ( get_option( 'ANALYTIFY_DEV_KEY' ) ) {

			$this->advanced_settings['api_key'] = get_option( 'ANALYTIFY_DEV_KEY' );
			delete_option( 'ANALYTIFY_DEV_KEY' );
		}

		if ( ! empty( $this->advanced_settings ) ) {
			update_option( 'wp-analytify-advanced' ,$this->advanced_settings );
		}

	}
}

if ( ! get_option( 'analytify_pro_upgrade_routine' ) ) {
	$WP_Analytify_Pro_Compatibility_Upgrade_instance = new WP_Analytify_Pro_Compatibility_Upgrade();
	update_option( 'analytify_pro_upgrade_routine', 'done' );
}