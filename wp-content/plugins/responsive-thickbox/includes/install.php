<?php
/**
 * Utility Functions
 *
 * @package     responsive-thickbox
 * @subpackage  Includes
 * @copyright   Copyright (c) 2016, Lyquidity Solutions Limited
 * @License:	Lyquidity Commercial
 * @since       1.0
 */

namespace lyquidity\responsive_thickbox;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Install
 *
 * Runs on plugin install by setting up the rule tables.
 *
 * @since 1.0
 * @global $wpdb
 * @param  bool $network_side If the plugin is being network-activated
 * @return void
 */
function install( $network_wide = false ) 
{
}
register_activation_hook( RESPONSIVE_THICKBOX_PLUGIN_FILE, '\lyquidity\responsive_thickbox\install' );

/**
 * Run the EDD Instsall process
 *
 * @since  1.0
 * @return void
 */
function run_install() 
{
}
