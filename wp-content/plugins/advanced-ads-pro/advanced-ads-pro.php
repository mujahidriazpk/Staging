<?php
/**
 * Advanced Ads Pro
 *
 * @wordpress-plugin
 * Plugin Name:         Advanced Ads Pro
 * Plugin URI:          https://wpadvancedads.com/add-ons/advanced-ads-pro/
 * Description:         Advanced features to boost your ad revenue.
 * Version:             2.16.0
 * Author:              Advanced Ads GmbH
 * Author URI:          https://wpadvancedads.com
 * Text Domain:         advanced-ads-pro
 * Domain Path:         /languages
 */

if ( defined( 'AAP_SLUG' ) ) {
	return;
}

define( 'AAP_SLUG', 'advanced-ads-pro' );
define( 'AAP_PATH', __DIR__ );
define( 'AAP_BASE', plugin_basename( __FILE__ ) ); // Plugin base as used by WordPress to identify it.
define( 'AAP_BASE_PATH', plugin_dir_path( __FILE__ ) );
define( 'AAP_BASE_URL', plugin_dir_url( __FILE__ ) );
define( 'AAP_BASE_DIR', dirname( AAP_BASE ) ); // Directory of the plugin without any paths.
define( 'AAP_VERSION', '2.16.0' );
define( 'AAP_PLUGIN_NAME', 'Advanced Ads Pro' );

require_once AAP_BASE_PATH . 'lib/autoload.php';

// Autoload and activate.
Advanced_Ads_Pro::get_instance();

register_activation_hook( __FILE__, array( 'Advanced_Ads_Pro', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Advanced_Ads_Pro', 'deactivate' ) );
add_action( 'wpmu_new_blog', array( 'Advanced_Ads_Pro', 'activate_new_site' ) );
