<?php
/**
 * Class YWSBS_Tests_Subscription_Product
 *
 * @package YITH
 */

/**
 * Sample test case.
 */
class YWSBS_Test_Cart extends YWSBS_Unit_Test_Case_With_Store {

	/**
	 * Test is subscription product.
	 */
	function test_add_to_cart() {
		WC()->cart->empty_cart();

		$product = $this->create_and_store_subscription_product();
		$product->set_regular_price( '10' );
		$product->save();


		WC()->cart->add_to_cart( $product->get_id() );
		WC()->cart->calculate_totals();

		$this->assertEquals( '10', WC()->cart->get_total( 'raw' ) );
	}

	/**
	 * Test is subscription product.
	 */
	function test_add_to_cart_with_fee() {

		//Test different values of Fee
		$this->subscription_with_signup_in_cart( 10, true );
		$this->subscription_with_signup_in_cart( 0, false );
		$this->subscription_with_signup_in_cart( '', false );


		//add a subscription product on cart with a price and fee
		WC()->cart->empty_cart();
		$product = $this->create_and_store_subscription_product( array( '_ywsbs_fee' => '50.65' ) );
		$product->set_regular_price( '10.00' );
		$product->save();
		WC()->cart->add_to_cart( $product->get_id() );
		WC()->cart->calculate_totals();
		$this->assertEquals( '60.65', WC()->cart->get_total( 'raw' ) );

	}

	function subscription_with_signup_in_cart( $fee, $assert = true ) {
		WC()->cart->empty_cart();
		$product = $this->create_and_store_subscription_product( array( '_ywsbs_fee' => $fee ) );
		$product->set_regular_price( '10' );
		$product->save();
		WC()->cart->add_to_cart( $product->get_id() );
		$call = $assert ? 'assertTrue' : 'assertFalse';
		$this->$call( YWSBS_Subscription_Cart()->cart_has_subscription_with_signup() );
	}

}
