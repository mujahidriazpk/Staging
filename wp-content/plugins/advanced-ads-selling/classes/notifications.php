<?php

/** 
 * handle notifications
 */

class Advanced_Ads_Selling_Notifications {
	
	public function __construct(){
		add_action( 'draft_to_pending', array( $this, 'notify_ad_manager_after_ad_submit' ) );
		add_action( 'pending_to_draft', array( $this, 'notify_client_after_ad_rejection' ) );
		add_action( 'pending_to_publish', array( $this, 'notify_client_after_publish' ) );
	}
	
	/**
	 * general function to send an email in order to allow manipulating the values
	 */
	public static function send( $options, $purpose ){
	    
		$options = apply_filters( 'advanced-ads-selling-email-options', $options, $purpose );
	    
		wp_mail( $options['recipient'], $options['subject'], $options['message'], $options['headers'] );
	}
	
	/**
	 * notify the client after the purchase – when the ads where created – so that he can edit them
	 *  technically: new advanced ad was created
	 *  ad must belong to an order
	 * 
	 * @param obj $post	post object
	 */
	public static function notify_client_after_purchase( $order_id ){
		
		// check post type
		/*if( $post->post_type !== Advanced_Ads::POST_TYPE_SLUG ){
		    return;
		}*/
	    
		// check, if this ad belongs to an order and if notifications to clients are allowed
		if( ! $order_id || Advanced_Ads_Selling_Plugin::hide_ad_setup() ){
			return;
		}
		
		// get order
		$order = wc_get_order( $order_id );
		
		// get sender email = store admin
		$options = Advanced_Ads_Selling_Plugin::get_instance()->options();
		$admin_email = sanitize_email( $options[ 'admin-email' ] );
		$sender_email = sanitize_email( $options[ 'sender-email' ] );
		
		$hash = get_post_meta( $order_id, 'advanced_ads_selling_setup_hash', true );
		if( ! $hash ){
		    return;
		}
		$ad_setup_url = Advanced_Ads_Selling_Plugin::get_instance()->get_ad_setup_url( $hash );
		
		$subject = sprintf( __( 'Edit your ads for order #%d', 'advanced-ads-selling' ), $order_id );
		$message = sprintf( __( 'Thank you for your order. You can already upload the content of your ads following the link below.', 'advanced-ads-selling' ), $order_id );
		$message .= "\n\n{$ad_setup_url}";
		$message .= "\n\n" . sprintf(__( "Sincerely,\nYour ad team\n%s", 'advanced-ads-selling' ), home_url() );
		$message .= "\n\n" . __( 'PS: If you want to contact the ad manager directly, just reply to this email.', 'advanced-ads-selling' );
		
		$headers = array(
		    'From: Ad Manager <' . $sender_email . '>',
		    "Reply-To: <{$admin_email}>",
		);
		    
		$billing = self::get_billing_information( $order );
		    
		// send email to ad admin
		self::send( array( 'recipient' => $billing['email'], 'subject' => $subject, 'message' => $message, 'headers' => $headers ), 'client_after_purchase' );
	}	
	
	/**
	 * notify the ad manager after an ad was set up in the frontend
	 *  technically: ad post type was changed from draft to pending
	 *  ad must belong to an order
	 * 
	 * @param obj $post post object
	 */
	public function notify_ad_manager_after_ad_submit( $post ){
	    
		// check post type
		if( $post->post_type !== Advanced_Ads::POST_TYPE_SLUG ){
		    return;
		}
		
		// check, if this ad belongs to an order
		$order_id = absint( get_post_meta( $post->ID, 'advanced_ads_selling_order', true ) );
		
		if( ! $order_id ){
			return;
		}
		
		// get order
		$order = wc_get_order( $order_id );
		
		// get recipients email = store admin
		$options = Advanced_Ads_Selling_Plugin::get_instance()->options();
		$admin_email = sanitize_email( $options[ 'admin-email' ] );
		$sender_email = sanitize_email( $options[ 'sender-email' ] );
		
		$subject = sprintf( __( 'Ad #%d needs approval', 'advanced-ads-selling' ), $post->ID );
		
		$message = sprintf( __( 'The ad content for ad #%d (order #%d) was submitted by the client. You can review it now using the following link.', 'advanced-ads-selling' ), $post->ID, $order_id );
		$message .= "\n\n" . self::get_edit_ad_link( $post->ID );
		$message .= "\n\n" . sprintf(__( "Sincerely,\nYour ad team\n%s", 'advanced-ads-selling' ), home_url() );
		$message .= "\n\n" . __( 'PS: If you want to contact the client directly, just reply to this email.', 'advanced-ads-selling' );
		
		$billing = self::get_billing_information( $order );
		
		$headers = array(
		    'From: Ad Manager <' . $sender_email . '>',
		    "Reply-To: {$billing['first_name']} {$billing['last_name']} <{$billing['email']}>",
		);
		    
		// send email to ad admin
		self::send( array( 'recipient' => $admin_email, 'subject' => $subject, 'message' => $message, 'headers' => $headers ), 'ad_manager_after_ad_submit' );
	}
	
	/**
	 * notify the client when his ad was rejected which and allows him to edit it again
	 *  technically: ad post type was changed from pending to draft
	 *  ad must belong to an order
	 * 
	 * @param obj $post post object
	 */
	public function notify_client_after_ad_rejection( $post ){
	    
		// check post type
		if( $post->post_type !== Advanced_Ads::POST_TYPE_SLUG || Advanced_Ads_Selling_Plugin::hide_ad_setup() ){
		    return;
		}
		
		// check, if this ad belongs to an order
		$order_id = absint( get_post_meta( $post->ID, 'advanced_ads_selling_order', true ) );
		
		if( ! $order_id ){
			return;
		}
		
		// get order
		$order = wc_get_order( $order_id );
		
		// get sender email = store admin
		$options = Advanced_Ads_Selling_Plugin::get_instance()->options();
		$admin_email = sanitize_email( $options[ 'admin-email' ] );
		$sender_email = sanitize_email( $options[ 'sender-email' ] );
		
		$subject = sprintf( __( 'Ad for order #%d was rejected', 'advanced-ads-selling' ), $order_id );
		$message = sprintf( __( 'The ad content for ad #%s (order #%d) was rejected by the ad manager.', 'advanced-ads-selling' ), $post->ID, $order_id );
		$message .= "\n\n" . sprintf(__( "Sincerely,\nYour ad team\n%s", 'advanced-ads-selling' ), home_url() );
		$message .= "\n\n" . __( 'PS: If you want to contact the ad manager directly, just reply to this email.', 'advanced-ads-selling' );
		
		$headers = array(
		    'From: Ad Manager <' . $sender_email . '>',
		    "Reply-To: <{$admin_email}>",
		);
		    
		$billing = self::get_billing_information( $order );
		    
		// send email to ad admin
		self::send( array( 'recipient' => $billing['email'], 'subject' => $subject, 'message' => $message, 'headers' => $headers ), 'client_after_ad_rejection' );
	}
	
	/**
	 * notify the client when his ad was published
	 *  technically: ad post type was changed from pending to publish
	 *  ad must belong to an order
	 * 
	 * @param obj $post post object
	 */
	public function notify_client_after_publish( $post ){
	    
		// check post type
		if( $post->post_type !== Advanced_Ads::POST_TYPE_SLUG || Advanced_Ads_Selling_Plugin::hide_ad_setup() ){
		    return;
		}
		
		// check, if this ad belongs to an order
		$order_id = absint( get_post_meta( $post->ID, 'advanced_ads_selling_order', true ) );
		
		if( ! $order_id ){
			return;
		}
		
		// get order
		$order = wc_get_order( $order_id );
		
		// get sender email = store admin
		$options = Advanced_Ads_Selling_Plugin::get_instance()->options();
		$admin_email = sanitize_email( $options[ 'admin-email' ] );
		$sender_email = sanitize_email( $options[ 'sender-email' ] );
		
		$subject = sprintf( __( 'An ad for order #%d was published', 'advanced-ads-selling' ), $order_id );
		$message = sprintf( __( 'An ad for order #%d was published.', 'advanced-ads-selling' ), $order_id );
		$message .= "\n\n" . sprintf(__( "Sincerely,\nYour ad team\n%s", 'advanced-ads-selling' ), home_url() );
		$message .= "\n\n" . __( 'PS: If you want to contact the ad manager directly, just reply to this email.', 'advanced-ads-selling' );
		
		$headers = array(
		    'From: Ad Manager <' . $sender_email . '>',
		    "Reply-To: <{$admin_email}>",
		);
		    
		$billing = self::get_billing_information( $order );
		
		// send email to ad admin
		self::send( array( 'recipient' => $billing['email'], 'subject' => $subject, 'message' => $message, 'headers' => $headers ), 'client_after_purchase' );
	}
	
	/**
	 * generate default sender email
	 */
	public static function get_default_sender_email(){
	    
		if( isset( $_SERVER['SERVER_NAME'] ) && $_SERVER['SERVER_NAME'] ){
			return 'ad-manager@' . preg_replace( '#^www\.#', '', strtolower( $_SERVER['SERVER_NAME'] ) );
		} else {
			return get_bloginfo( 'admin_email' );
		}
		
	}
	
	/**
	 * get sender email
	 */
	public static function get_sender_email(){
	    
		$options = Advanced_Ads_Selling_Plugin::get_instance()->options();
		$sender_email = ( isset( $options['sender-email'] ) && $options['sender-email'] ) ? sanitize_email( $options['sender-email'] ) : '';
	    
		if( ! $sender_email ) {
			self::get_default_sender_email();
		}
		
		return $sender_email;
		
	}
	
	/**
	 * get edit post link for not-logged in users and ads
	 * since get_edit_post_link() does only work for users with the appropriate user rights and the ad post type might also not be registered yet
	 * 
	 * @param   int	$post_id	ID of WP_Post object
	 */
	public static function get_edit_ad_link( $post_id = '' ){
	    
		if( empty( $post_id ) ){
			return '';
		}
	    
		return admin_url( 'post.php?post=' . $post_id . '&action=edit' );
		
	}
	
	/**
	 * get billing information array based on the WooCommerce version
	 * 
	 * @param obj	$order
	 * @return arr	$billing array with information
	 */
	public static function get_billing_information( $order ){
	    
		$billing = array();
	    
		if ( Advanced_Ads_Selling_Plugin::version_check() ) { // WC 3.0 and higher
			$billing['email']	= $order->get_billing_email();
			$billing['first_name']	= $order->get_billing_first_name();
			$billing['last_name']	= $order->get_billing_last_name();
		} else { // WC below 3.0
			$billing['email']	= $order->billing_email;
			$billing['first_name']	= $order->billing_first_name;
			$billing['last_name']	= $order->billing_last_name;
		}
		
		return $billing;
	}
	
}
