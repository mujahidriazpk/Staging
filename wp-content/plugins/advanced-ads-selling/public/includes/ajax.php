<?php

/**
 * handles frontend submission
 */

class Advanced_Ads_Selling_Ajax {

	/**
	 * Initialize the plugin
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

	    add_action( 'wp_ajax_nopriv_advads_selling_ad_setup', array( $this, 'save_ad_content' ) );
	    add_action( 'wp_ajax_advads_selling_ad_setup', array( $this, 'save_ad_content' ) );
	}

	/**
	 * save ad content sent from backend
	 */
	public function save_ad_content( ){

	    
		$data = array();
		if(isset($_POST['formdata'])){
		    parse_str($_POST['formdata'], $data);
		}
		
		// verify nonce
		if( ! wp_verify_nonce( $data['advads_selling_nonce'], 'advanced-ads-ad-setup-order-item-' . $data['advads_selling_order_item'] ) ) {
		    die( __( 'Invalid request.', 'advanced-ads-selling' ) );
		}
		
		switch( $data['advads_selling_ad_type'] ){
		    case 'image' :
			// todo: implement image submission
			break;
		    default : // handle plain text
			if( !isset( $data['advads_selling_ad_content'] ) || !trim ( $data['advads_selling_ad_content'] ) ){
			    die( __( 'Ad content missing.', 'advanced-ads-selling' ) );
			}
			
			$ad_id = Advanced_Ads_Selling_Order::order_item_id_to_ad_id( $data['advads_selling_order_item'] );
			
			// update the ad post
			$new_ad_content = array(
			    'ID'           => $ad_id,
			    'post_content' => trim( $data['advads_selling_ad_content'] ),
			    'post_status' => 'pending'
			);

			$return = wp_update_post( $new_ad_content );
			if( is_wp_error( $return ) ){
				error_log(print_r($return, true));
				die( __( 'Error when submitting the ad. Please contact the site admin.', 'advanced-ads-selling' ) );
			}
		}
		
		die();
	}
	
}
