<?php

/*
Part of: responsive-thickbox
Description: admin notifiction messaging.
Version: 1.0.1
Author: Lyquidity Solutions Limited
Author URI: http://wordpress.wproute.com
Copyright: Lyquidity Solution Limited
License: Lyquidity Commercial Plugin
*/

namespace lyquidity\responsive_thickbox;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Admin Notices
 *
 * Outputs admin notices
 *
 * @package Spam Blaze
 * @since 1.0
*/

function admin_notices() {

	global $edd_options;

	$names = array( 
		RESPONSIVE_THICKBOX_ACTIVATION_ERROR_NOTICE,
		RESPONSIVE_THICKBOX_ACTIVATION_UPDATE_NOTICE,
		RESPONSIVE_THICKBOX_DEACTIVATION_ERROR_NOTICE,
		RESPONSIVE_THICKBOX_DEACTIVATION_UPDATE_NOTICE,
	);

	array_walk($names, function($name) {

		$message = get_transient( $name );
		delete_transient( $name );

		if ( empty( $message ) ) return;
		$class = strpos( $name, "UPDATE" ) === false ? "error" : "updated";

		// @codingStandardsIgnoreStart
		echo "<div class='$class'><p>$message</p></div>";
		// @codingStandardsIgnoreEnd

	});

}
add_action( 'admin_notices', '\lyquidity\responsive_thickbox\admin_notices' );
