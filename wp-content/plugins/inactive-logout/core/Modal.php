<?php

namespace Codemanas\InactiveLogout;

/**
 * Class Modal
 * @package Codemanas\InactiveLogout
 */
class Modal {

	public function __construct() {
		add_action( 'wp_footer', array( $this, 'dialog_modal' ) );
		add_action( 'admin_footer', array( $this, 'dialog_modal' ) );
	}

	public function dialog_modal() {
		?>
        <!--START INACTIVE LOGOUT MODAL CONTENT-->
        <div id="ina-logout-modal-container" class="ina-logout-modal-container"></div>
        <!--END INACTIVE LOGOUT MODAL CONTENT-->
		<?php
	}

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
}