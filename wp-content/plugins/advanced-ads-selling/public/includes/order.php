<?php

/**
 * logic to handle order in the frontend
 */
class Advanced_Ads_Selling_Public_Order {

    /**
     *
     * @since     1.0.0
     */
    public function __construct() {

	add_action('plugins_loaded', array($this, 'wp_plugins_loaded'));
    }

    /**
     * load actions and filters
     */
    public function wp_plugins_loaded() {

	if (!class_exists('Advanced_Ads_Selling', false)) {
	    return;
	}

	// save and show order item meta in frontend
	if( Advanced_Ads_Selling_Plugin::version_check() ){
		add_action('woocommerce_new_order_item', array($this, 'add_order_item_meta'), 99, 3);
	} else {
		add_action('woocommerce_add_order_item_meta', array($this, 'add_order_item_meta'), 99, 3);
	}
	add_filter('woocommerce_get_item_data', array($this, 'render_meta_on_cart_and_checkout'), 99, 2);
	add_filter('woocommerce_add_cart_item_data', array($this, 'save_cart_ad_options'), 99, 2);

	// Add action to calculate totals
	add_action('woocommerce_before_calculate_totals', array($this, 'calculate_ad_price'), 999);

	// Add filter for validating the "Number of days" meta field
	// add_filter('woocommerce_add_to_cart_validation', array($this, 'add_to_cart_validation'), 10, 6);

	// Add filter to remove cart item quantity from Cart if product is of ad type
	add_filter( 'woocommerce_cart_item_quantity', array( $this, 'woocommerce_remove_cart_item_quantity' ), 10 , 2 );

	// Add filter to remove review tab from single product page if product is of ad type
	add_filter( 'woocommerce_product_tabs', array ( $this, 'woocommerce_remove_reviews_tab' ), 98 );
	
	// Link add-to-cart-link to link to product page
	add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'add_to_cart_link' ) );
    }
    
    /**
	 * Remove Review tab on single product page if product is advanced_ad type
	 */
	public function woocommerce_remove_reviews_tab ( $tabs ) {
		
		global $post;
		
		$post_id 			= $post->ID; // Get post_id
		$pro_details 	= wc_get_product( $post_id ); // Get product_details
		$product_type 	= $pro_details->get_type(); // Get product_type
		
		if ( $product_type == 'advanced_ad' ) {

			unset ( $tabs['reviews'] ); // If product is advanced_ad type then remove Review tab
		}
		
		return $tabs;
	}
	
	/**
	 * Remove Quantity field from cart if product is advanced_ad type
	 */
	public function woocommerce_remove_cart_item_quantity ( $product_quantity, $cart_item_key ) {
		
		global $woocommerce; // Get global $woocommerce

		foreach ( $woocommerce->cart->cart_contents as $cart_item_loop_key => $cart_item_loop_val ) { // Loop on cart contents
			
			if ( $cart_item_key == $cart_item_loop_key ) { // Check current_item_key with item_key we get
				
				$product_id = $cart_item_loop_val['product_id']; // Get product_id from cart
				$product 	= wc_get_product( $product_id ); // get product details
				
				if ( $product->get_type() == 'advanced_ad' ) { // If product is advacned_ad type then return
					return $cart_item_loop_val['quantity'];
				}
			}
		}
		
		return $product_quantity;
	}

    /**
     * render custom option in cart and checkout
     * @param type $cart_data
     * @param type $cart_item
     * @return string
     */
    function render_meta_on_cart_and_checkout($cart_data, $cart_item = null) {
	$meta_items = array();
	/* Woo 2.4.2 updates */
	if (!empty($cart_data)) {
	    $meta_items = $cart_data;
	}

	// render prices
	$prices = Advanced_Ads_Selling_Plugin::get_prices($cart_item['product_id']);
	if (isset($cart_item["option_ad_price"])) {
	    $label = $prices[$cart_item["option_ad_price"]]['label'];
	    $name  = __( 'Price option', 'advanced-ads-selling' );
	    $meta_items[] = array( "name" => $name, "value" => $label );
	}

	return $meta_items;
    }

    /**
     * save public custom fields in the cart
     * 
     * @param string $cart_item_data
     * @param type $product_id
     * @return string
     */
    public function save_cart_ad_options($cart_item_data, $product_id) {

	$product = wc_get_product($product_id);

	// save the price option
	if (isset($_POST['option_ad_price']) && $_POST['option_ad_price']) {
	    // get all price options
	    // save price only, if saved option is among them
	    $prices = Advanced_Ads_Selling_Plugin::get_prices($product_id);
	    $value = esc_attr($_POST['option_ad_price']);
	    if (isset($prices[$value])) {
		$cart_item_data["option_ad_price"] = $value;
	    }
	}

	// save the placement
	if (isset($_POST['option_ad_placement']) && $_POST['option_ad_placement']) {
	    // get all placement
	    // save placement only, if selected placement is among them
	    $model = Advanced_Ads::get_instance()->get_model();
	    $placements_raw = $model->get_ad_placements_array();
	    $value = esc_attr($_POST['option_ad_placement']);
	    if (isset($placements_raw[$value])) {
		$cart_item_data["option_ad_placement"] = $value;
	    }
	}

	// save the ad types retrieved from the product directly
	if ($value = get_post_meta($product_id, '_ad_types', true)) {
	    $cart_item_data["option_ad_types"] = $value;
	}
	
	// save the ad sales type retrieved from the product directly
	if ($value = get_post_meta($product_id, '_ad_sales_type', true)) {
	    $cart_item_data["option_ad_sales_type"] = $value;
	}

	return $cart_item_data;
    }

    /**
     * Add to cart Validation for "Number of days" field
     * @deprecated since version 1.0.0 after we switched to fixed selection of days
     */
    public function add_to_cart_validation($valid, $product_id, $quantity, $variation_id = '', $variations = array(), $cart_item_data = array()) {

	$product = wc_get_product($product_id); // Get product object
	$product_sale_type = get_post_meta($product_id, '_ad_sales_type', true); // Get product sale type
	// If product type is of 'advanced_ad' and product sale type is of 'advanced_ad' and days are set
	if ($product->get_type() == 'advanced_ad' && $product_sale_type == 'days' && isset($_POST['option_ad_expiry_days'][$product_id])) {

	    $days = absint($_POST['option_ad_expiry_days'][$product_id]);

	    if ($days === 0) {

		wc_add_notice('<p class="advanced-ads-selling-days-error">' . __("Please enter a positive number of days.", 'advanced-ads-selling') . '</p>', 'error');
		$valid = false;
	    }
	}

	return $valid;
    }

    /**
     * Add item meta to order, when created
     */
    public function add_order_item_meta( $item_id, $values, $order_id ) {

	//Get product ID
	// $_product_id	= isset( $values['product_id'] ) ? $values['product_id'] : '';

	$prices = Advanced_Ads_Selling_Plugin::get_prices($values["product_id"]);

	// added compatibility with WC version 3.0, even though this is a legacy fix
	if( Advanced_Ads_Selling_Plugin::version_check() ){
	    $values = $values->legacy_values;
	}

	// save pricing label
	if (isset($values["option_ad_price"])) {
	    $label = $prices[$values["option_ad_price"]]['label'];
	    wc_add_order_item_meta($item_id, "_ad_pricing_label", $label); // Ad Pricing Label
	}

	// save price value
	if (isset($values["option_ad_price"])) {
	    // $label = $prices[ $values["option_ad_price"] ]['label'];
	    wc_add_order_item_meta($item_id, "_ad_pricing_option", $values["option_ad_price"]); // Ad Pricing Option
	}
	
	// save sales type (e.g. days, flat, impressions)
	if (isset($values["option_ad_sales_type"])) {
	    // $label = $prices[ $values["option_ad_price"] ]['label'];
	    wc_add_order_item_meta($item_id, "_ad_sales_type", $values["option_ad_sales_type"]); // Ad Pricing Option
	}

	// save placements
	$model = Advanced_Ads::get_instance()->get_model();
	$placements_raw = $model->get_ad_placements_array();
	if (isset($values["option_ad_placement"])) {
	    wc_add_order_item_meta($item_id, "_ad_placement", $values["option_ad_placement"]); // Ad Placement
	}

	// save ad types retrieved from the original product
	if ($value = get_post_meta($values['product_id'], '_ad_types', true)) {
	    wc_add_order_item_meta($item_id, "_ad_types", implode(', ', $value)); // Ad Types
	}
    }

    /**
     * Calculate price if product is of Ad type and "Number of days" is set
     */
    public function calculate_ad_price($cart_object) {

	$aas_price_options = array();
	
	if (!WC()->session->__isset("reload_checkout")) {

	    foreach ($cart_object->cart_contents as $key => $value) { // Loop on Woocommerce Cart Contents
		if (isset($value["option_ad_price"])) {
		    $prices = Advanced_Ads_Selling_Plugin::get_prices( $value['data']->get_id() );
		    $value['data']->set_price( floatval($prices[$value["option_ad_price"]]['price']) );
		}

		/*if (isset($value["option_ad_expiry_days"])) {

		    $total_price = 0; // Initialise variables
		    $prices = Advanced_Ads_Selling_Plugin::get_prices($value['data']->get_id()); // Get all possible prices for the product
		    $reverse_prices = array_reverse($prices, true); // Reverse array preserving keys
		    $total_days = absint($value["option_ad_expiry_days"]); // Get "Number of Days" entered

		    if (!empty($reverse_prices) && is_array($reverse_prices)) { // If $reverse_prices is an array
			foreach ($reverse_prices as $price_key => $price_val) { // loop on $reverse_prices
			    $is_divisible = $total_days / $price_val['value']; // Divide $total_days by the available options
			    if (!empty(intval($is_divisible))) { // If $is_divisible not empty
				$multiple = intval($is_divisible); // Get integer value from $is_divisible
				$int_price = intval(str_replace(",", "", $price_val['price'])); // Remove Commas, if present from price
				$total_price += $multiple * $int_price; // Calculate price and add it to $total_price
				$total_days = $total_days - ( $multiple * $price_val['value'] ); // Substract total days for which price is calculated

				if (empty($total_days)) { // If remainning $total_days is empty then break loop
				    break;
				} else { // Else continue for next
				    continue;
				}
			    } else { // If $is_divisible is empty than continue
				continue;
			    }
			}
		    }

		    // Assign total price to the cart item
		    $value['data']->price = floatval($total_price);
		}*/
	    }
	}
    }
    
    /**
     * convert add-to-cart button to link to static page
     * 
     * @param string $link
     * @return string
     */
    public function add_to_cart_link( $link ) {
	    global $product;
	    
	    // If product type is of 'advanced_ad' and product sale type is of 'advanced_ad' and days are set
	    if ( $product->get_type() == 'advanced_ad' ) {
		    $link = '<a href="'. get_permalink() .'" data-product_id="' . $product->get_id() . '" data-product_sku="' . $product->get_sku() . '" data-quantity="1" class="button add_to_cart_button product_type_advanced_ad">'. 
			    __( 'Details', 'advanced-ads-selling' ) .'</a>';
	    }
	    
	    return $link;
    }

}
