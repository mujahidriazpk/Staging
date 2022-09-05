<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ACP_Filtering_Strategy_MSUser extends ACP_Filtering_Strategy_User {

	public function render_markup_hook() {
		add_action( 'in_admin_footer', array( $this, 'render_markup' ) );
		add_action( 'in_admin_footer', array( $this, 'render_button' ) );
	}

}
