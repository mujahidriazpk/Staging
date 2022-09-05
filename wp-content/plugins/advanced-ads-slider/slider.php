<?php
/**
 * Advanced Ads Slider
 *
 * Plugin Name:       Advanced Ads – Slider
 * Plugin URI:        https://wpadvancedads.com/add-ons/slider/
 * Description:       Create a slider from your ads.
 * Version:           1.4.3
 * Author:            Thomas Maier
 * Author URI:        https://wpadvancedads.com
 * Text Domain:       slider-ads
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// only load if not already existing (maybe within another plugin I created)
if(!class_exists('Advanced_Ads_Slider_Plugin')) {

// load basic path and url to the plugin
define('AAS_BASE_PATH', plugin_dir_path(__FILE__));
define('AAS_BASE_URL', plugin_dir_url(__FILE__));
define('AAS_BASE_DIR', dirname( plugin_basename( __FILE__ ) ) ); // directory of the plugin without any paths

// plugin slug and textdoamin
define('AAS_SLUG', 'slider-ads');

define( 'AAS_VERSION', '1.4.3' );
define( 'AAS_PLUGIN_URL', 'https://wpadvancedads.com' );
define( 'AAS_PLUGIN_NAME', 'Ad Slider' );


include_once( plugin_dir_path( __FILE__ ) . 'classes/plugin.php' );

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
    include_once( plugin_dir_path( __FILE__ ) . 'admin/admin.php' );
    new Advanced_Ads_Slider_Admin();
} else {
    include_once( plugin_dir_path( __FILE__ ) . 'public/public.php' );
    new Advanced_Ads_Slider();
}

}