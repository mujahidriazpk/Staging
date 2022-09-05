<?php

/**

 * WooCommerce Simple Auctions Functions

 *

 * Hooked-in functions for WooCommerce Simple Auctions related events on the front-end.

 *

 */





/**

 * Placed bid message

 *

 * @access public

 * @return void

 *

 */

function woocommerce__simple_auctions_place_bid_message( $product_id ) {

	global $woocommerce; 

	$product_data = wc_get_product($product_id);

	$current_user = wp_get_current_user();



	if($current_user->ID == $product_data->get_auction_current_bider()){

		if(!$product_data->is_reserve_met() && ('yes' !== $product_data->get_auction_sealed() ) ){

			$message = sprintf( __( 'Successfully placed bid for &quot;%s&quot; but it does not meet the reserve price!', 'wc_simple_auctions' ),$product_data -> get_title()  );
			
			

		} else{



			if($product_data->get_auction_proxy() && $product_data->get_auction_max_bid()){

				$message = sprintf( __( 'Successfully placed bid for &quot;%s&quot;! Your max bid is %s.', 'wc_simple_auctions' ),$product_data -> get_title(), wc_price($product_data->get_auction_max_bid())  );
				
			}else{

				$message = sprintf( __( 'Successfully placed bid for &quot;%s&quot;!', 'wc_simple_auctions' ),$product_data -> get_title()  );

			}

		}	

		

	} else {

		$message = sprintf( __( "Your bid was successful but you've been outbid for &quot;%s&quot;!", 'wc_simple_auctions' ),$product_data -> get_title()  );	

	}	



	wc_add_notice ( apply_filters('woocommerce_simple_auctions_placed_bid_message', $message,$product_id ) );



}





/**

 * Your bid is winning message

 *

 * @access public

 * @return void

 *

 */

function woocommerce__simple_auctions_winning_bid_message( $product_id ) {

	global $product, $woocommerce;



	if (!(method_exists( $product, 'get_type') && $product->get_type() == 'auction'))

					return FALSE;

	if ($product->is_closed())

					return FALSE;

	$current_user = wp_get_current_user();



	if (!$current_user-> ID)

					return FALSE;



	if ($product->get_auction_sealed() == 'yes')

					return FALSE;



	$message =   __('No need to bid. Your bid is winning! ', 'wc_simple_auctions');

	if ($current_user->ID == $product->get_auction_current_bider() &&  wc_notice_count () == 0   ) {

		//wc_add_notice( apply_filters('woocommerce_simple_auctions_winning_bid_message', $message) );

	}	

	

}





/**

 * Gets the url for the checkout page

 *

 * @return string url to page

 */

function simple_auction_get_checkout_url() {

	$checkout_page_id = wc_get_page_id('checkout');

	$checkout_url     = '';

	if ( $checkout_page_id ) {

		if ( is_ssl() || get_option('woocommerce_force_ssl_checkout') == 'yes' )

			$checkout_url = str_replace( 'http:', 'https:', get_permalink( $checkout_page_id ) );

		else

			$checkout_url = get_permalink( $checkout_page_id );

	}

	return apply_filters( 'woocommerce_get_checkout_url', $checkout_url );

}



if (!function_exists('wc_get_price_decimals')) {



	function wc_get_price_decimals() {

	return absint( get_option( 'wc_price_num_decimals', 2 ) );

	}



}





if ( ! function_exists( 'woocommerce_auctions_ordering' ) ) {



    /**

     * Output the product sorting options.

     *

     * @subpackage  Loop

     * 

     */

    function woocommerce_auctions_ordering() {

    	

        global $wp_query;



        if ( 1 === $wp_query->found_posts ) {

                return;

        }



        $orderby                 = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'wsa_default_auction_orderby', get_option( 'wsa_default_auction_orderby' ) );

        $show_default_orderby    = 'menu_order' === apply_filters( 'wsa_default_auction_orderby', get_option( 'wsa_default_auction_orderby' ) );

        $catalog_orderby_options = apply_filters( 'woocommerce_auctions_orderby', array(

                                'menu_order'       => __( 'Default sorting', 'woocommerce' ),

                                'date'             => __( 'Sort by newness', 'woocommerce' ),

                                'price'            => __( 'Sort by buynow price: low to high', 'wc_simple_auctions' ),

                                'price-desc'       => __( 'Sort by buynow price: high to low', 'wc_simple_auctions' ),

                                'bid_asc'          => __( 'Sort by current bid: Low to high', 'wc_simple_auctions' ),

                                'bid_desc'         => __( 'Sort by current bid: High to low', 'wc_simple_auctions' ),

                                'auction_end'      => __( 'Sort auction by ending soonest', 'wc_simple_auctions' ),

                                'auction_started'  => __( 'Sort auction by recently started', 'wc_simple_auctions' ),

                                'auction_activity' => __( 'Sort auction by most active', 'wc_simple_auctions' ),

        ) );



        if ( ! $show_default_orderby ) {

                unset( $catalog_orderby_options['menu_order'] );

        }

        

        wc_get_template( 'loop/orderby.php', array( 'catalog_orderby_options' => $catalog_orderby_options, 'orderby' => $orderby, 'show_default_orderby' => $show_default_orderby ) );

    }

    

    

}



if ( ! function_exists( 'wsa_get_finished_auctions_id' ) ) {



    /**

     * Return Finished auctions ids

     *

     * @subpackage  Loop

     * 

     */

    function wsa_get_finished_auctions_id() {

    		$args = array(

					'post_type' => 'product',

					'posts_per_page' => '-1',

					'show_past_auctions' => TRUE,

					'tax_query' => array(array('taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction')),

					'meta_query' => array(

						array(

							'key' => '_auction_closed',

							'value' => '2',

						)

					),

					'auction_arhive' => TRUE,

					'show_past_auctions' => TRUE,

					'fields' => 'ids',

			);

	    	$query = new WP_Query( $args );

	    	$wsa_finished_auctions_ids = $query->posts;

			return $wsa_finished_auctions_ids;

	}    

    

}



if ( ! function_exists( 'wsa_get_future_auctions_id' ) ) {



    /**

     * Return future auctions ids

     *

     * @subpackage  Loop

     * 

     */

    function wsa_get_future_auctions_id() {

    		$args = array(

					'post_type' => 'product',

					'posts_per_page' => '-1',

					'show_past_auctions' => TRUE,

					'tax_query' => array(array('taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction')),

					'meta_query' => array(

						array(

							'key' => '_auction_closed',

							'compare' => 'NOT EXIST',

						),

						array(

							'key' => '_auction_started',

							'value' => '0',

						)

					),

					'auction_arhive' => TRUE,

					'show_future_auctions' => TRUE,

					'fields' => 'ids',

			);

	    	$query = new WP_Query( $args );

	    	$wsa_future_auctions_ids = $query->posts;

			return $wsa_future_auctions_ids;

	}    

    

}