<?php

/**
 * Class YWSBS_Unit_Test_Case_With_Store.
 *
 * This helper class should ONLY be used for unit tests!.
 */
class YWSBS_Unit_Test_Case_With_Store extends WP_UnitTestCase {
	/**
	 * @var WC_Product[] Array of products to clean up.
	 */
	protected $products = array();


	/**
	 * @var WP_Post[] Array of posts to clean up.
	 */
	protected $posts = array();

	/**
	 * Helper function to hold a reference to created product objects so they
	 * can be cleaned up properly at the end of each test.
	 *
	 * @param WC_Product $product The product object to store.
	 */
	protected function store_product( $product ) {
		$this->products[] = $product;
	}

	/**
	 * Helper function to hold a reference to created product objects so they
	 * can be cleaned up properly at the end of each test.
	 *
	 * @return WC_Product
	 */
	protected function create_and_store_subscription_product( $args = array() ) {
		$product = YWSBS_Helper_Subscription_Product::create_subscription_product( $args );
		$this->store_product( $product );

		return $product;
	}

	/**
	 * Helper function to hold a reference to created product objects so they
	 * can be cleaned up properly at the end of each test.
	 *
	 * @return WC_Product
	 */
	protected function create_and_store_subscription_variation_product( $args = array() ) {
		$product = YWSBS_Helper_Subscription_Product::create_subscription_variation_product( $args );
		$this->store_product( $product );

		return $product;
	}

	public function setUp() {
		parent::setUp();

		$this->products = array();
	}

	/**
	 * Clean up after each test. DB changes are reverted in parent::tearDown().
	 */
	public function tearDown() {

		foreach ( $this->products as $product ) {
			$product->delete( true );
		}

		parent::tearDown();
	}
}
