<?php
/*
* Plugin Name: Analytify Pro
* Plugin URI: https://analytify.io/
* Description: Analytify makes Google Analytics simple for everything in WordPress (posts,pages etc). It presents the statistics in a beautiful way under the WordPress Posts/Pages at front end, backend and in its own Dashboard. This provides Stats from Country, Referrers, Social media, General stats, New visitors, Returning visitors, Exit pages, Browser wise and Top keywords. This plugin provides the RealTime statistics in a new UI that is easy to understand and looks good.
* Version: 4.1.0
* Author: WPBrigade
* Author URI: https://wpbrigade.com/
* License: GPLv2+
* Min WP Version: 3.0
* Max WP Version: 5.7
* Text Domain: wp-analytify-pro
* Domain Path: /languages
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'ANALYTIFY_PRO_ROOT_PATH', dirname( __FILE__ ) );
define( 'ANALYTIFY_PRO_UPGRADE_PATH', __FILE__ );
define( 'ANALYTIFY_PRO_VERSION', '4.1.0' );

add_action( 'plugins_loaded', 'wp_analytify_pro_load', 15 );

function wp_analytify_pro_load() {
	update_option( 'analytify_license_key', 'B5E0B5F8DD8689E6ACA49DD6E6E1A930' );
	update_option( 'analytify_license_status', 'valid' );
	$prevent = false;

	if ( ! file_exists( WP_PLUGIN_DIR . '/wp-analytify/analytify-general.php' ) ) {
		add_action( 'admin_notices' , 'pa_install_free' );
		$prevent = true;
		// return;
	} else if ( ! class_exists( 'WP_Analytify' ) ) {
		add_action( 'admin_notices', 'pa_activate_free_plugin' );
		$prevent = true;
		// return;
	} else if ( ! class_exists( 'Analytify_General' ) ) {
		add_action( 'admin_notices' , 'pa_update_free' );
		$prevent = true;
		// return;
	}

	if ( $prevent ) {
		add_action( 'admin_enqueue_scripts', 'pa_notices_scripts' );
		return;
	}

	add_action( 'admin_menu', 'remove_go_pro_menu' );

	// Set to default if tracking mode not set.
	if ( ! defined( 'ANALYTIFY_TRACKING_MODE' ) ) {
		define( 'ANALYTIFY_TRACKING_MODE', 'ga' );
	}

	include ANALYTIFY_PRO_ROOT_PATH . '/classes/analytifypro_base.php';
	include ANALYTIFY_PRO_ROOT_PATH . '/classes/class-wp-analytify-pro.php';

	WP_Analytify_Pro::instance();

}

/**
 * Admin notices scripts.
 */
if ( ! function_exists( 'pa_notices_scripts' ) ) {
	function pa_notices_scripts() {
		wp_enqueue_style( 'wp-analytify-pro-notices', plugins_url( 'wp-analytify-pro/assets/css/wp-analytify-pro-notices.css', dirname( __FILE__ ) ), '', ANALYTIFY_PRO_VERSION );
	}
}

/**
 * Hide Go Pro submenu when Pro is activated.
 *
 * @since 2.0.5
 */
function remove_go_pro_menu() {

	remove_submenu_page( 'analytify-dashboard', 'analytify-go-pro' );
}

function pa_activate_free_plugin() {

	// Hide notice on plugin install screen.
	if ( isset( $_GET['action'] ) && 'install-plugin' == $_GET['action'] ) {
		return;
	}

	$action = 'activate';
	$slug   = 'wp-analytify/wp-analytify.php';
	$link   = wp_nonce_url( add_query_arg( array( 'action' => $action, 'plugin' => $slug ), admin_url( 'plugins.php' ) ), $action . '-plugin_' . $slug );

	$message = sprintf('%1$s <a href="%2$s">%3$s</a>' , esc_html__( 'The following required plugin is currently inactive &mdash;', 'wp-analytify-pro' ), $link, esc_html__( 'Click here to activate Analytify Core (Free)', 'wp-analytify-pro' ) );

	wp_analytify_pro_notice(  $message, 'wp-analytify-danger' );
	
	// printf('<div class="notice notice-error is-dismissible">
	// <p>%1$s<a href="%2$s" style="text-decoration:none">%3$s</a></p></div>' , esc_html__( 'The following required plugin is currently inactive &mdash; ', 'wp-analytify-pro' ), $link, esc_html__( 'Click here to activate Analytify Core (Free)', 'wp-analytify-pro' ) );
}

function pa_update_free() {

	// Hide notice on plugin install screen.
	if ( isset( $_GET['action'] ) && 'install-plugin' == $_GET['action'] ) {
		return;
	}

	$action = 'upgrade-plugin';
	$slug   = 'wp-analytify';
	$link   = wp_nonce_url( add_query_arg( array( 'action' => $action, 'plugin' => $slug ), admin_url( 'update.php' ) ), $action . '_' . $slug );

	// printf('<div class="notice notice-error is-dismissible">
	// <p>%1$s<a href="%2$s" style="text-decoration:none">%3$s</a></p></div>' , esc_html__( 'Please update Analytify Core to latest Free version to enable PRO features &mdash; ', 'wp-analytify-pro' ), $link, esc_html__( 'Update now' ), 'wp-analytify-pro' );

	$message = sprintf('%1$s <a href="%2$s">%3$s</a>' , esc_html__( 'Please update Analytify Core to latest Free version to enable PRO features &mdash; ', 'wp-analytify-pro' ), $link, esc_html__( 'Update now', 'wp-analytify-pro' ) );

	wp_analytify_pro_notice(  $message, 'wp-analytify-danger' );
}

function pa_install_free() {

	// Hide notice on plugin install screen.
	if ( isset( $_GET['action'] ) && 'install-plugin' == $_GET['action'] ) {
		return;
	}

	$action = 'install-plugin';
	$slug   = 'wp-analytify';
	$link   = wp_nonce_url( add_query_arg( array( 'action' => $action, 'plugin' => $slug ), admin_url( 'update.php' ) ), $action . '_' . $slug );

	// printf('<div class="notice notice-warning">
	// <p>%1$s<a href="%2$s" style="text-decoration:none">%3$s</a></p></div>' , esc_html__( 'The following required plugin is not installed &mdash; ', 'wp-analytify-pro' ), $link, esc_html__( 'Install Analytify Core (Free) now', 'wp-analytify-pro' ) );

	$message = sprintf('%1$s <a href="%2$s">%3$s</a>' , esc_html__( 'The following required plugin is not installed &mdash;', 'wp-analytify-pro' ), $link, esc_html__( 'Install Analytify Core (Free)', 'wp-analytify-pro' ) );

	wp_analytify_pro_notice(  $message, 'wp-analytify-danger' );
}

/**
 * Add custom admin notice
 * @param  string $message Custom Message
 * @param  string $class wp-analytify-success,wp-analytify-danger
 *
 */
function wp_analytify_pro_notice( $message, $class ) {

	echo '<div class="wp-analytify-notification '. $class .'">
	<a class="" href="#" aria-label="Dismiss the welcome panel"></a>
	<div class="wp-analytify-notice-logo">
	<img src="' . plugins_url( 'assets/images/logo.svg', __FILE__ ) . '" alt="analytify logo">
	</div>
	<div class="wp-analytify-notice-discription">
	<p>' . $message .'</p>
	</div>
	</div>';
}

/**
*
* @since       1.2.2
* @return      void
*/
function wp_analytify_pro_activation() {

	// Attempt to install core plugin.
	include ANALYTIFY_PRO_ROOT_PATH . '/classes/class-install-core.php';
	new WP_Analytify_Pro_Core_Installer();

	// Check if front_end settings are set
	$_front_settings = get_option( 'wp-analytify-front' );
	if ( 'on' ===  $_front_settings['disable_front_end']  && ! empty( $_front_settings['show_analytics_roles_front_end'] ) ) {
		return;
	}

	// Load default settings in Pro plugin.
	if ( ! get_option( 'analytify_pro_default_settings' ) ) {

		$front_tab_settings = array(
			'disable_front_end'                   => 'on',
			'show_analytics_roles_front_end'      => array( 'administrator', 'editor' ),
			'show_analytics_post_types_front_end' => array( 'post', 'page' ),
			'show_panels_front_end'               => array( 'show-overall-front', 'show-country-front', 'show-keywords-front', 'show-social-front', 'show-browser-front', 'show-referrer-front', 'show-mobile-front', 'show-os-front', 'show-city-front' )
		);

		update_option( 'wp-analytify-front', $front_tab_settings );
		update_option( 'analytify_pro_default_settings', 'done' );
	}
}
register_activation_hook( __FILE__, 'wp_analytify_pro_activation' );

/**
*
* @since       1.2.2
 * @return      void
 */
function wp_analytify_pro_de_activation() {

}
register_deactivation_hook( __FILE__, 'wp_analytify_pro_de_activation' );

/**
 * Delete settings on uninstall.
 *
 * @since 2.0.4
 */
function wp_analytify_pro_un_install() {

	// delete default settings check. So on installing it again. Default settings could be loaded again.
	delete_option( 'analytify_pro_default_settings' );
}
register_uninstall_hook( __FILE__, 'wp_analytify_pro_un_install' );

/**
 * Load TextDoamin
 *
 * @since 2.0.7
 */
function wp_analytify_pro_load_text_domain(){
	$plugin_dir = basename( dirname( __FILE__ ) );
	load_plugin_textdomain( 'wp-analytify-pro', false , $plugin_dir . '/languages/' );
}
add_action( 'init', 'wp_analytify_pro_load_text_domain' );
