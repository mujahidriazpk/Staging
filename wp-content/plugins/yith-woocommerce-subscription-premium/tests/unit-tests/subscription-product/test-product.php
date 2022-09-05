<?php
/**
 * Class YWSBS_Tests_Subscription_Product
 *
 * @package YITH
 */

/**
 * Sample test case.
 */
class YWSBS_Tests_Subscription_Product extends YWSBS_Unit_Test_Case_With_Store {

	/**
	 * Test is subscription product.
	 */
	function test_is_subscription_product() {
		$product = $this->create_and_store_subscription_product();

		$this->assertTrue( YITH_WC_Subscription()->is_subscription( $product ) );
		$this->assertTrue( YITH_WC_Subscription()->is_subscription( $product->get_id() ) );
	}

	/**
	 * Test is subscription variation product.
	 */
	function test_is_subscription_variation_product(){
		$product = $this->create_and_store_subscription_variation_product();

		$this->assertTrue( YITH_WC_Subscription()->is_subscription( $product ) );
		$this->assertTrue( YITH_WC_Subscription()->is_subscription( $product->get_id() ) );
	}

}
