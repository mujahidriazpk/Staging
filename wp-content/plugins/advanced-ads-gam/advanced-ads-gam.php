<?php
/**
 * Advanced Ads – Google Ad Manager Integration for WordPress
 *
 * Plugin Name:       Advanced Ads – Google Ad Manager Integration
 * Plugin URI:        https://wpadvancedads.com/add-ons/google-ad-manager/
 * Description:       Google Ad Manager Integration for WordPress
 * Version:           1.4.2
 * Author:            Advanced Ads GmbH
 * Author URI:        https://wpadvancedads.com
 * Text Domain:       advanced-ads-gam
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'AAGAM_BASE', plugin_basename( __FILE__ ) ); // plugin base as used by WordPress to identify it.
define( 'AAGAM_BASE_PATH', plugin_dir_path( __FILE__ ) );
define( 'AAGAM_BASE_URL', plugin_dir_url( __FILE__ ) );
define( 'AAGAM_BASE_DIR', dirname( plugin_basename( __FILE__ ) ) );
define( 'AAGAM_OPTION', 'advanced-ads-gam-options' );
define( 'AAGAM_APP_NAME', 'AdvadsGAM' );
define( 'AAGAM_API_KEY_OPTION', 'advanced-ads-gam-apikey' );
define( 'AAGAM_PLUGIN_NAME', 'Google Ad Manager Integration' );
define( 'AAGAM_SETTINGS', 'advanced-ads-gam' );
define( 'AAGAM_VERSION', '1.4.2' );

/**
 * Check if Advanced Ads is installed and active
 */
add_action( 'plugins_loaded', 'advanced_ads_gam_plugins_loaded' );

/**
 * Check if base Advanced Ads is installed on plugin loaded
 */
function advanced_ads_gam_plugins_loaded() {

	if ( ! class_exists( 'Advanced_Ads', false ) ) {
		// show admin notice.
		add_action( 'admin_notices', 'advanced_ads_gam_missing_base_plugin' );
	}

}

/**
 * Add an admin notice if Advanced Ads is missing
 */
function advanced_ads_gam_missing_base_plugin() {
	$plugins = get_plugins();

	if ( isset( $plugins['advanced-ads/advanced-ads.php'] ) ) {
		// is installed, but not active.
		$link = '<a class="button button-primary" href="' . wp_nonce_url( 'plugins.php?action=activate&amp;plugin=advanced-ads/advanced-ads.php&amp', 'activate-plugin_advanced-ads/advanced-ads.php' ) . '">' . __( 'Activate Now', 'advanced-ads-gam' ) . '</a>';
	} else {
		$link = '<a class="button button-primary" href="' . wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . 'advanced-ads' ), 'install-plugin_' . 'advanced-ads' ) . '">' . __( 'Install Now', 'advanced-ads-gam' ) . '</a>';
	}

	echo '<div class="error"><p>' . wp_kses( __( '<strong>Advanced Ads – Google Ad Manager Integration</strong> is an extension for the free Advanced Ads plugin.', 'advanced-ads-gam' ), array( 'strong' => array() ) ) . '&nbsp;'
		. wp_kses(
			$link,
			array(
				'a' => array(
					'class' => array(),
					'href'  => array(),
				),
			)
		) . '</p></div>';
}

add_action( 'advanced-ads-plugin-loaded', 'advanced_ads_gam_init_plugin' );
/**
 * Tasks on Advanced Ads loaded
 */
function advanced_ads_gam_init_plugin() {
	require_once 'autoload.php';
	$network = Advanced_Ads_Network_Gam::get_instance();
	$network->register();
	advanced_ads_gam_load_textdomain();
}

/**
 * Load the plugin text domain for translation.
 */
function advanced_ads_gam_load_textdomain() {
	load_plugin_textdomain( 'advanced-ads-gam', false, AAGAM_BASE_DIR . '/languages' );
}
