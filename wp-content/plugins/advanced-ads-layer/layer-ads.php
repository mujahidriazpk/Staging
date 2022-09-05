<?php
/**
 * Advanced Ads – PopUp and Layer Ads
 *
 * @wordpress-plugin
 * Plugin Name:       Advanced Ads – PopUp and Layer Ads
 * Plugin URI:        https://wpadvancedads.com/add-ons/popup-and-layer-ads/
 * Description:       Create PopUp, Layer ads and Overlays
 * Version:           1.7.4
 * Author:            Advanced Ads GmbH
 * Author URI:        https://wpadvancedads.com
 * Text Domain:       advanced-ads-layer
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// only load if not already existing (maybe within another plugin I created)
if ( ! class_exists('Advanced_Ads_Layer') ) {

	// load basic path and url to the plugin
	define( 'AAPLDS_BASE_PATH', plugin_dir_path(__FILE__) );
	define( 'AAPLDS_BASE_URL', plugin_dir_url(__FILE__) );
	define( 'AAPLDS_BASE_DIR', dirname( plugin_basename( __FILE__ ) ) ); // directory of the plugin without any paths
	define( 'AAPLDS_SLUG', 'advanced-ads-layer');

	define( 'AAPLDS_VERSION', '1.7.4' );
	define( 'AAPLDS_PLUGIN_URL', 'https://wpadvancedads.com' );
	define( 'AAPLDS_PLUGIN_NAME', 'PopUp and Layer Ads' );

	include_once( plugin_dir_path( __FILE__ ) . 'classes/plugin.php' );

	/*----------------------------------------------------------------------------*
	 * Public-Facing Functionality
	 *----------------------------------------------------------------------------*/
	require_once( plugin_dir_path( __FILE__ ) . 'public/public.php' );

	$is_admin = is_admin();
	$is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

	$layer_ads = new Advanced_Ads_Layer( $is_admin, $is_ajax );


	/*----------------------------------------------------------------------------*
	 * Dashboard and Administrative Functionality
	 *----------------------------------------------------------------------------*/
	if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	    require_once( plugin_dir_path( __FILE__ ) . 'admin/admin.php' );
	    $layer_ads_admin = new Advanced_Ads_Layer_Admin();
	}
}
