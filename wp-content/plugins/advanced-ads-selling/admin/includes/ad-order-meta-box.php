<?php

/**
 * render a meta box on the ad edit screen to display order information
 */
class Advanced_Ads_Selling_Admin_Ad_Order_Meta_Box {
    
	/**
	 * @since     1.0.0
	 */
	public function __construct() {

	    add_action('plugins_loaded', array($this, 'wp_admin_plugins_loaded'));
	}

	/**
	 * load actions and filters
	 */
	public function wp_admin_plugins_loaded() {

	    if ( ! class_exists('Advanced_Ads_Admin', false ) ) {
		return;
	    }

	    // whitelist meta box with Advanced Ads
	    add_filter( 'advanced-ads-ad-edit-allowed-metaboxes', array( $this, 'whitelist_meta_box' ) );
	    // add meta box
	    add_action( 'add_meta_boxes', array( $this, 'add_order_meta_box' ), 10 );
	}
	
	/**
	 * whitelist meta box
	 */
	public function whitelist_meta_box( $meta_boxes ){
		$meta_boxes[] = 'advanced-ads-selling-ad-order-data';
		return $meta_boxes;
	}
	/**
	 * load meta box with order information
	 */
	public function add_order_meta_box(){
		global $post;
		
		if( isset( $post->ID ) && Advanced_Ads_Selling_Plugin::is_ordered_ad( $post->ID ) ) {
		    add_meta_box( 'advanced-ads-selling-ad-order-data', __( 'Order Data', 'advanced-ads-selling' ), array( $this, 'show_order_data' ), Advanced_Ads::POST_TYPE_SLUG, 'normal', 'high' );
		}
	}
	
	/**
	 * show order data on order edit screen
	 * 
	 * @param   obj	$post	order post
	 */
	public function show_order_data( $post ){
		
		// load order data
		$order_id = get_post_meta( $post->ID, 'advanced_ads_selling_order', true );
		
		if( !$order_id ){
		    return;
		}
		
		$order = wc_get_order( $order_id );
		
		$item_id = get_post_meta( $post->ID, 'advanced_ads_selling_order_item', true );
		$product_id =  wc_get_order_item_meta( $item_id, '_product_id' );
		$product = wc_get_product( $product_id );
		
		$hash = get_post_meta( $order_id, 'advanced_ads_selling_setup_hash', true );
		
		include( AASA_BASE_PATH . '/admin/views/ad-order-meta-box.php');
		
	}	
}