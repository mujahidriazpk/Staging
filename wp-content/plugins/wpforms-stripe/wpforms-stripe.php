<?php
/**
 * Plugin Name: WPForms Stripe
 * Plugin URI:  https://wpforms.com
 * Description: Stripe integration with WPForms.
 * Author:      WPForms
 * Author URI:  https://wpforms.com
 * Version:     2.0.2
 * Text Domain: wpforms-stripe
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
 * @package    WPFormsStripe
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Forms LLC
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Requires PHP 5.3.3+.
 */
if ( version_compare( PHP_VERSION, '5.3.3', '<' ) ) {

	/**
	 * Deactivate plugin.
	 *
	 * @since 1.2.0
	 */
	function wpforms_stripe_deactivate() {
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}
	add_action( 'admin_init', 'wpforms_stripe_deactivate' );

	/**
	 * Display notice after deactivation.
	 *
	 * @since 1.2.0
	 */
	function wpforms_stripe_deactivate_msg() {

		echo '<div class="notice notice-error"><p>';
		printf(
			wp_kses(
				/* translators: %s - WPForms.com documentation page URL. */
				__( 'The WPForms Stripe plugin has been deactivated. Your site is running an outdated version of PHP that is no longer supported and is not compatible with the Stripe addon. <a href="%s" target="_blank" rel="noopener noreferrer">Read more</a> for additional information.', 'wpforms-stripe' ),
				array(
					'a' => array(
						'href'   => array(),
						'rel'    => array(),
						'target' => array(),
					),
				)
			),
			'https://wpforms.com/docs/supported-php-version/'
		);
		echo '</p></div>';

		if ( isset( $_GET['activate'] ) ) { // WPCS: CSRF ok.
			unset( $_GET['activate'] ); // WPCS: CSRF ok.
		}
	}
	add_action( 'admin_notices', 'wpforms_stripe_deactivate_msg' );

	return;
}

// Plugin constants.
define( 'WPFORMS_STRIPE_VERSION', '2.0.2' );
define( 'WPFORMS_STRIPE_FILE', __FILE__ );

require_once __DIR__ . '/autoloader.php';
