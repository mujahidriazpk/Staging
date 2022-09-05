<?php

/**
 * functions to display details on the order edit page
 */
class Advanced_Ads_Selling_Admin_Order_Page {
    
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

		if ( ! class_exists('Advanced_Ads_Selling_Admin', false ) ) {
		    return;
		}

		// display the order edit screen in the admin panel
		add_action( 'add_meta_boxes', array( $this, 'add_order_meta_boxes' ), 30, 2 );
		// adjust labels of order item labels
		add_filter( 'woocommerce_attribute_label', array( $this, 'adjust_order_item_labels' ) );
		// list the link to the ad of the line item below item meta
		add_action( 'woocommerce_order_item_line_item_html', array( $this, 'show_additional_item_meta' ), 10, 3 );


	}
	
	/**
	 * load meta boxes to order screen
	 */
	public function add_order_meta_boxes( $post_type, $post ){
		
		if( isset( $post->ID ) && Advanced_Ads_Selling_Order::has_ads( $post->ID ) ){
			add_meta_box( 'advanced-ads-selling-order-data', __( 'Ad Data', 'advanced-ads-selling' ), array( $this, 'show_order_data' ), 'shop_order', 'normal', 'high' );
		}
	}
	
	/**
	 * show order data on order edit screen
	 * 
	 * @param   obj	$post	order post
	 */
	public function show_order_data( $post ){
		global $theorder;

		if ( ! is_object( $theorder ) ) {
			$theorder = wc_get_order( $post->ID );
		}

		$order = $theorder;
		
		$hash = get_post_meta( $post->ID, 'advanced_ads_selling_setup_hash', true );
		
		include( AASA_BASE_PATH . '/admin/views/ad-order-data.php');
		
	}
	
	/**
	 * show additional item meta
	 * * link to ad
	 * 
	 * @param   int	$item_id    id of the order item
	 * @param   arr	$item	    item information
	 * @param   obj	$order	    order object
	 */
	public function show_additional_item_meta( $item_id, $item, $order ){
	    // search for ad id looking for the item id in the ad post meta data
	
	    $args = array(
		    'post_type' => Advanced_Ads::POST_TYPE_SLUG,
		    'posts_per_page' => 1,
		    'post_status' => 'any',
		    'meta_query' => array(
			    array(
				'key'     => 'advanced_ads_selling_order_item',
				'value'   => $item_id,
			    )
		    )
	    );
	    
	    $ad = new WP_Query( $args );
	    
	    if( isset( $ad->post->ID ) && $ad->post->ID ){
		echo '<tr><th>' . __( 'Ad', 'advanced-ads-selling' ) . '</th><td><a href="' . get_edit_post_link( $ad->post->ID ) . '">' . $ad->post->post_title . '</a></td></tr>';
	    }
	    
	}
	
	/**
	 * use nice labels for order item meta instead of the raw key
	 * 
	 * @param string $label raw label
	 * @return string $label nice label
	 */
	public function adjust_order_item_labels( $label ){
    
	switch( $label ){
		case '_ad_pricing_option' : return __( 'Pricing Value', 'advanced-ads-selling' );
		case '_ad_pricing_label' : return __( 'Pricing Label', 'advanced-ads-selling' );
		case '_ad_sales_type' : return __( 'Sales Type', 'advanced-ads-selling' );
		case '_ad_placement' : return __( 'Placement', 'advanced-ads-selling' );
		case '_ad_types' : return __( 'Ad Types', 'advanced-ads-selling' );
	}

	return $label;
}	
	
}