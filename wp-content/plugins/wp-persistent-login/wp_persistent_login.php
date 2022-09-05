<?php

/**
 *
 *   Plugin Name: WordPress Persistent Login
 *   Plugin URI: https://persistentlogin.com/
 *   Description: Keep users logged into your website securely, and allows you to limit the number of active logins.
 *   Author: Luke Seager
 *   Author URI:  https://lukeseager.com/
 *   Version: 2.0.8
 *
 *
 */
/*
	Copyright 2018 Luke Seager  (email : info@lukeseager.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'persistent_login' ) ) {
    persistent_login()->set_basename( false, __FILE__ );
} else {
    // definitions to use throughout application.
    define( 'WPPL_DATABASE_VERSION', '2.0.0' );
    define( 'WPPL_DATABASE_NAME', 'persistent_logins' );
    define( 'WPPL_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
    define( 'WPPL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
    define( 'WPPL_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
    define( 'WPPL_SETTINGS_AREA', get_admin_url() . 'users.php' );
    define( 'WPPL_SETTINGS_PAGE', WPPL_SETTINGS_AREA . '?page=wp-persistent-login' );
    define( 'WPPL_ACCOUNT_PAGE', WPPL_SETTINGS_AREA . '?page=wp-persistent-login-account' );
    define( 'WPPL_UPGRADE_PAGE', WPPL_SETTINGS_AREA . '?billing_cycle=annual&page=wp-persistent-login-pricing' );
    define( 'WPPL_SUPPORT_PAGE', WPPL_SETTINGS_AREA . '?page=wp-persistent-login-contact' );
    define( 'WPPL_TEXT_DOMAIN', 'wp-persitent-login' );
    // Load text domain.
    load_plugin_textdomain( WPPL_TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    // Load freemius.
    require WPPL_PLUGIN_PATH . '/includes/freemius.php';
    // Load installation file.
    require WPPL_PLUGIN_PATH . '/includes/install.php';
    register_activation_hook( __FILE__, 'persistent_login_activate' );
    // Load database upgrade file.
    require WPPL_PLUGIN_PATH . '/includes/database-upgrades.php';
    // Load uninstall cleanup file.
    require WPPL_PLUGIN_PATH . '/includes/uninstall.php';
    persistent_login()->add_action( 'after_uninstall', 'persistent_login_uninstall_cleanup' );
    // load composor packages.
    require WPPL_PLUGIN_PATH . '/vendor/autoload.php';
    // autoload wp persistent login classes.
    require WPPL_PLUGIN_PATH . '/classes/autoload.php';
    define( 'WPPL_PR', false );
    new WP_Persistent_Login();
    new WP_Persistent_Login_Admin();
    new WP_Persistent_Login_Profile();
    new WP_Persistent_Login_Active_Logins();
    new WP_Persistent_Login_User_Count();
    /**
     * Action hook to execute after WP Persistent Login plugin init.
     *
     * Use this hook to init addons.
     *
     * @since 2.0.0
     */
    do_action( 'wp_persistent_login_init' );
}

// end if persistent_login() exists.