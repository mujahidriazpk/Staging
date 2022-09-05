<?php
/**
 * Advanced Ads – Sticky Ads
 *
 * @wordpress-plugin
 * Plugin Name:       Advanced Ads – Sticky Ads
 * Plugin URI:        http://wpadvancedads.com/add-ons/sticky-ads/
 * Description:       Advanced ad positioning.
 * Version:           1.8.3
 * Author:            Advanced Ads GmbH
 * Author URI:        http://wpadvancedads.com
 * Text Domain:       advanced-ads-sticky
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// only load if not already existing (maybe within another plugin I created)
if ( ! class_exists( 'Sticky_Ads' ) ) {

	// load basic path and url to the plugin
	define( 'AASADS_BASE_PATH', plugin_dir_path( __FILE__ ) );
	define( 'AASADS_BASE_DIR', dirname( plugin_basename( __FILE__ ) ) );
	define( 'AASADS_BASE_URL', plugin_dir_url( __FILE__ ) );
	define( 'AASADS_SLUG', 'advanced-ads-sticky-ads' );

	define( 'AASADS_VERSION', '1.8.3' );
	define( 'AASADS_PLUGIN_URL', 'https://wpadvancedads.com' );
	define( 'AASADS_PLUGIN_NAME', 'Sticky Ads' );

	include_once( plugin_dir_path( __FILE__ ) . 'classes/plugin.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'public/public.php' );

	$is_admin = is_admin();
	$is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

	new Advanced_Ads_Sticky( $is_admin, $is_ajax );

	// -TODO this basically renders for admin and for ajax (and is not needed for the latter)
	if ( $is_admin ) {
	    require_once( plugin_dir_path( __FILE__ ) . 'admin/admin.php' );
	    new Advanced_Ads_Sticky_Admin();
	}
}
