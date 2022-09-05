<?php
/**
 * Plugin Name: WPForms Custom Captcha
 * Plugin URI:  https://wpforms.com
 * Description: Captcha fields with WPForms.
 * Author:      WPForms
 * Author URI:  https://wpforms.com
 * Version:     1.1.2
 * Text Domain: wpforms-captcha
 * Domain Path: languages
 *
 * WPForms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WPForms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WPForms. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    WPFormsCaptcha
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Forms LLC
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin version.
define( 'WPFORMS_CAPTCHA_VERSION', '1.1.2' );

/**
 * Load the provider class.
 *
 * @since 1.0.0
 */
function wpforms_captcha() {

	// WPForms Pro is required.
	if ( ! class_exists( 'WPForms_Pro', true ) ) {
		return;
	}

	load_plugin_textdomain( 'wpforms-captcha', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	require_once plugin_dir_path( __FILE__ ) . 'class-captcha.php';
}
add_action( 'wpforms_loaded', 'wpforms_captcha' );

/**
 * Load the plugin updater.
 *
 * @since 1.0.0
 *
 * @param string $key WPForms license key.
 */
function wpforms_captcha_updater( $key ) {

	new WPForms_Updater(
		array(
			'plugin_name' => 'WPForms Captcha',
			'plugin_slug' => 'wpforms-captcha',
			'plugin_path' => plugin_basename( __FILE__ ),
			'plugin_url'  => trailingslashit( plugin_dir_url( __FILE__ ) ),
			'remote_url'  => WPFORMS_UPDATER_API,
			'version'     => WPFORMS_CAPTCHA_VERSION,
			'key'         => $key,
		)
	);
}
add_action( 'wpforms_updater', 'wpforms_captcha_updater' );
