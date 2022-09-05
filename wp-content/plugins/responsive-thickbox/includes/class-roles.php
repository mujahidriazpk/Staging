<?php
/**
 * Roles
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

if ( ! defined( 'RESPONSIVE_THICKBOX_CAPABILITY' ) )
	define( 'RESPONSIVE_THICKBOX_CAPABILITY', 'responsive_thickbox' );

if ( ! defined( 'RESPONSIVE_THICKBOX_ADMINISTRATOR' ) )
	define( 'RESPONSIVE_THICKBOX_ADMINISTRATOR', 'responsive_thickbox_administrator' );

if ( ! defined( 'RESPONSIVE_THICKBOX_SUBSCRIBER' ) )
	define( 'RESPONSIVE_THICKBOX_SUBSCRIBER', 'responsive_thickbox_subscriber' );

/**
 * Roles class
*/
class Roles
{
	/**
	 * Get things going
	 *
	 * @since 1.0
	 */
	public function __construct() {}

	/**
	 * Add new shop roles with default responsive_thickbox capability
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function add_roles()
	{
		add_role( RESPONSIVE_THICKBOX_ADMINISTRATOR, __( 'Responsive Thickbox Administrator', 'responsive-thickbox' ), array(
			RESPONSIVE_THICKBOX_CAPABILITY	=> true,
		) );

		add_role( RESPONSIVE_THICKBOX_SUBSCRIBER, __( 'Responsive Thickbox Group Manager', 'responsive-thickbox' ), array(
			RESPONSIVE_THICKBOX_CAPABILITY	=> true,
		) );

		$admin_user = get_user_by( "login", "admin" );
		if ( $admin_user )
		{
			$admin_user->add_role( RESPONSIVE_THICKBOX_ADMINISTRATOR );
			$admin_user->add_role( RESPONSIVE_THICKBOX_SUBSCRIBER );
		}
	}

	/**
	 * Add new shop roles with default WP caps
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function remove_roles()
	{
		$admin_user = get_user_by( "login", "admin" );
		if ( $admin_user )
		{
			$admin_user->remove_role( RESPONSIVE_THICKBOX_ADMINISTRATOR );
			$admin_user->remove_role( RESPONSIVE_THICKBOX_SUBSCRIBER );
		}

		remove_role( RESPONSIVE_THICKBOX_ADMINISTRATOR );
		remove_role( RESPONSIVE_THICKBOX_SUBSCRIBER );
	}
}

?>