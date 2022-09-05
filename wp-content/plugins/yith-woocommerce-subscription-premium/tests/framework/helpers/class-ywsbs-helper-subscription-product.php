<?php

/**
 * Class YWSBS_Helper_Subscription_Product.
 *
 * This helper class should ONLY be used for unit tests!.
 */
class YWSBS_Helper_Subscription_Product {
	/**
 * Create a subscription single product.
 *
 * @return WC_Product
 */
	public static function create_subscription_product( $args = array() ) {
		// Create the product
		$product = wp_insert_post( array(
			'post_title'  => 'Dummy Subscription Product',
			'post_type'   => 'product',
			'post_status' => 'publish',
		) );
		update_post_meta( $product, '_sku', 'DUMMY SKU' );
		update_post_meta( $product, '_manage_stock', 'no' );
		update_post_meta( $product, '_tax_status', 'taxable' );
		update_post_meta( $product, '_ywsbs_subscription', 'yes' );
		update_post_meta( $product, '_ywsbs_price_is_per', '1' );
		update_post_meta( $product, '_ywsbs_price_time_option', 'days' );

		if( $args ){
			foreach ( $args as $key =>$arg ) {
				update_post_meta( $product, $key, $arg );
			}
		}

		$product = new WC_Product( $product );

		// set 'Allow until' by default to 5 years to prevent issue with 'create_next_year_date'
		return $product;
	}

	/**
	 * Create a subscription variation product.
	 *
	 * @return WC_Product_Variation
	 */
	public static function create_subscription_variation_product( $args = array()) {

		$product    = new WC_Product_Variable();
		$product_id = $product->save();

		$variation = new WC_Product_Variation;
		$variation->set_parent_id( $product_id );
		$variation_id = $variation->save();

		if( $args ){
			foreach ( $args as $key =>$arg ) {
				update_post_meta( $product, $key, $arg );
			}
		}

		update_post_meta( $variation_id, '_ywsbs_subscription', 'yes' );
		update_post_meta( $variation_id, '_ywsbs_price_is_per', '1' );
		update_post_meta( $variation_id, '_ywsbs_price_time_option', 'days' );


		return $variation;
	}

	/**
	 * delete a product
	 *
	 * @param int|WC_Product $product
	 */
	public static function delete_product( $product ) {
		$product = wc_get_product( $product );
		$product && $product->delete( true );
	}
}
