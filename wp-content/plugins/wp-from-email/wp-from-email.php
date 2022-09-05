<?php
/*
Plugin Name: Change From Email
Description: Override the default From: 'WordPress &lt;wordpress@mydomain.com&gt;' name and email address
Version: 1.2.1
Author: Marian Kadanka
Author URI: https://kadanka.net/
Text Domain: wp-from-email
Domain Path: /languages
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
GitHub Plugin URI: https://github.com/marian-kadanka/wp-from-email
*/

/**
 * Change From Email
 * Copyright (C) 2008-2017 Skullbit.com. All rights reserved.
 * Copyright (C) 2017-2020 Marian Kadanka. All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPFromEmail' ) ):

class WPFromEmail {

	function __construct() {

		if ( is_admin() ) {
			add_filter( 'plugin_action_links', array( $this, 'action_links' ), 10, 4 );

			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
			add_action( 'admin_init', array( $this, 'settings_init' ) );
		}

		add_filter('wp_mail_from', array( $this, 'get_from_email' ) );
		add_filter('wp_mail_from_name', array( $this, 'get_from_name' ) );

		load_plugin_textdomain( 'wp-from-email', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	function action_links( $links, $file ) {
		if ( $file == plugin_basename( __FILE__ ) ) {
			array_unshift( $links, '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=marian.kadanka@gmail.com&item_name=Donation+for+Marian+Kadanka" title="' . esc_attr__( 'Donate', 'wp-from-email' ) . '" target="_blank">' . esc_html__( 'Donate', 'wp-from-email' ) . '</a>' );

			array_unshift( $links, '<a href="' . network_admin_url( 'options-general.php?page=wp_from_email' ) . '" title="' . esc_attr__( 'Settings', 'wp-from-email' ) . '">' . esc_html__( 'Settings', 'wp-from-email' ) . '</a>' );
		}

		return $links;
	}

	function add_admin_menu() {
		add_options_page( esc_html__( 'Change From Email Settings', 'wp-from-email' ), esc_html__( 'Change From Email', 'wp-from-email' ), 'manage_options', 'wp_from_email', array( $this, 'options_page' ) );
	}

	function settings_init() {

		register_setting( 'wp_from_email_settings', 'wp_from_email' );

		add_settings_section(
		'wp_from_email_settings_section', 
		__( 'Change From Email Settings', 'wp-from-email' ), 
		array( $this, 'settings_section_callback' ), 
		'wp_from_email_settings'
		);

		add_settings_field( 
		'email', 
		__( 'From Email', 'wp-from-email' ), 
		array( $this, 'email_render' ), 
		'wp_from_email_settings', 
		'wp_from_email_settings_section' 
		);

		add_settings_field( 
		'name', 
		__( 'From Name', 'wp-from-email' ), 
		array( $this, 'name_render' ), 
		'wp_from_email_settings', 
		'wp_from_email_settings_section' 
		);
	}

	function email_render() {
		$options = get_option( 'wp_from_email' );
		?>
		<input type='text' name='wp_from_email[email]' value='<?php echo $options['email']; ?>'>
		<?php
	}

	function name_render() {
		$options = get_option( 'wp_from_email' );
		?>
		<input type='text' name='wp_from_email[name]' value='<?php echo $options['name']; ?>'>
		<?php
	}

	function settings_section_callback() {
		_e( 'Override the default From: WordPress &lt;wordpress@mydomain.com&gt; name and email address', 'wp-from-email' );
	}

	function options_page() {
		?>
		<div class="wrap">
		<form action='options.php' method='post'>

		<?php
		settings_fields( 'wp_from_email_settings' );
		do_settings_sections( 'wp_from_email_settings' );
		submit_button();
		?>

		</form>
		</div>
		<?php
	}

	function get_from_email( $email ) {
		$options = get_option( 'wp_from_email' );
		if ( $options && ! empty( $options['email'] ) ) {
			return $options['email'];
		}
		else {
			return $email;
		}
	}

	function get_from_name( $name ) {
		$options = get_option( 'wp_from_email' );
		if ( $options && ! empty( $options['name'] ) ) {
			return $options['name'];
		}
		else {
			return $name;
		}
	}
}
endif;

new WPFromEmail();
