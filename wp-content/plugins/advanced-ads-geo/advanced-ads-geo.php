<?php
/**
 * Advanced Ads – Geo Location
 *
 * Plugin Name:       Advanced Ads – Geo Targeting
 * Plugin URI:        https://wpadvancedads.com/add-ons/geo-targeting/
 * Description:       Display ads based on the geo location of the visitor.
 * Version:           1.3.3
 * Author:            Advanced Ads
 * Author URI:        https://wpadvancedads.com
 * Text Domain:       advanced-ads-geo
 * Domain Path:       /languages
 * 
 * This product includes GeoLite2 data created by MaxMind, available from http://www.maxmind.com.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// only load if not already existing (maybe within another plugin I created)
// important before anything breaks
register_activation_hook(__FILE__, 'advanced_ads_geo_activate');

if( !class_exists('Advanced_Ads_Geo') && version_compare(PHP_VERSION, '5.3.0') === 1 ) {
    
	// load basic path to the plugin
	define( 'AAGT_BASE_PATH', plugin_dir_path( __FILE__ ) );
	define( 'AAGT_BASE_URL', plugin_dir_url( __FILE__ ) );
	define( 'AAGT_BASE_DIR', dirname( plugin_basename( __FILE__ ) ) ); // directory of the plugin without any paths
	// general and global slug, e.g. to store options in WP, textdomain
	define( 'AAGT_SLUG', 'advanced-ads-geo' );
	define( 'AAGT_PLUGIN_NAME', 'Geo Targeting' );
	define( 'AAGT_URL', 'https://wpadvancedads.com/' );
	define( 'AAGT_VERSION', '1.3.3' );

	// load public functions (might be used by modules, other plugins or theme)
	include_once( AAGT_BASE_PATH . 'classes/plugin.php' );
	include_once( AAGT_BASE_PATH . 'classes/api.php' );

	include_once( AAGT_BASE_PATH . 'public/public.php' );
	new Advanced_Ads_Geo();
	
	if ( is_admin() ) {
	    include_once( AAGT_BASE_PATH . 'admin/admin.php' );
	    new Advanced_Ads_Geo_Admin();
	}
}

function advanced_ads_geo_activate() {
    if(version_compare(PHP_VERSION, '5.3.0', '<') == -1) { 
	deactivate_plugins(plugin_basename('advanced-ads-geo/advanced-ads-geo.php'));
	wp_die('Advanced Ads Geo Targeting requires PHP 5.3 or higher. Your server is using ' . PHP_VERSION . '. Please contact your server administrator for a PHP update. <a href="'. admin_url( 'plugins.php') .'">Back to Plugins</a>'); 
    } 
}