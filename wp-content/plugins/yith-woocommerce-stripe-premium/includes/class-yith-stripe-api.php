<?php
/**
 * Created by PhpStorm.
 * User: YourInspiration
 * Date: 20/02/2015
 * Time: 16:54
 */

use \Stripe\Stripe;
use \Stripe\Charge;
use \Stripe\Error;
use \Stripe\Customer;
use \Stripe\Plan;
use \Stripe\Subscription;
use \Stripe\Invoice;
use \Stripe\Event;
use \Stripe\Product;
use \Stripe\BalanceTransaction;
use \Stripe\WebhookEndpoint;

class YITH_Stripe_API {

	protected $private_key = '';

	/**
	 * Set the Stripe library
	 *
	 * @param $key
	 *
	 * @since 1.0.0
	 */
	public function __construct( $key ) {
		if ( ! class_exists( 'Stripe' ) ) {
			include_once( dirname( dirname( __FILE__ ) ) . '/vendor/autoload.php' );
		}

		$this->private_key = $key;
		Stripe::setAppInfo( 'YITH WooCommerce Stripe', YITH_WCSTRIPE_VERSION, 'https://yithemes.com' );
		Stripe::setApiVersion( YITH_WCSTRIPE_API_VERSION );
		Stripe::setApiKey( $this->private_key );
	}

	/**
	 * Returns Stripe's Private Key
	 *
	 * @return string
	 * @since 1.6.0
	 */
	public function get_private_key(){
		return apply_filters( 'yith_wcstripe_private_key', $this->private_key );
	}

	/**
	 * Create the charge
	 *
	 * @param $params
	 *
	 * @since 1.0.0
	 * @return Charge
	 */
	public function charge( $params ) {
		return Charge::create( $params, array(
			'idempotency_key' => self::generateRandomString(),
		) );
	}

	/**
	 * Retrieve the charge
	 *
	 * @param $transaction_id
	 *
	 * @return Charge
	 * @since 1.0.0
	 */
	public function get_charge( $transaction_id ) {
		return Charge::retrieve( $transaction_id );
	}

	/**
	 * Capture a charge
	 *
	 * @param $transaction_id
	 *
	 * @return Charge
	 * @since 1.0.0
	 */
	public function capture_charge( $transaction_id ) {
		$charge = $this->get_charge( $transaction_id );

		// exist if already captured
		if ( ! $charge->captured ) {
			$charge->capture();
		}

		return $charge;
	}

	/**
	 * Change a charge
	 *
	 * @param $transaction_id
	 * @param array $params
	 *
	 * @return Charge
	 * @since 1.0.0
	 */
	public function update_charge( $transaction_id, $params = array() ) {
		$charge = $this->get_charge( $transaction_id );
		$valid_properties = array(
			'description',
			'metadata',
			'receipt_email',
			'fraud_details',
			'shipping'
		);

		foreach ( $params as $param => $value ) {
			if ( in_array( $param, $valid_properties ) ) {
				$charge->{$param} = $value;
			}
		}

		$charge->save();
		return $charge;
	}

	/**
	 * Retrieve Balance Transaction
	 *
	 * @param $transaction_id string Transaction unique id
	 * @param $params array Additional parameters to be sent within the request
	 * @return BalanceTransaction Balance object
	 */
	public function get_balance_transaction( $transaction_id, $params = array() ) {
		return BalanceTransaction::retrieve( $transaction_id, $params );
	}

	/**
	 * Perform a refund
	 *
	 * @param $transaction_id
	 * @param $params
	 *
	 * @since 1.0.0
	 * @return Charge
	 */
	public function refund( $transaction_id, $params ) {
		$deposit = $this->get_charge( $transaction_id );

		return $deposit->refunds->create( $params );
	}

	/**
	 * New customer
	 *
	 * @param $params
	 *
	 * @since 1.0.0
	 * @return Customer
	 */
	public function create_customer( $params ) {
		return Customer::create( $params );
	}

	/**
	 * Retrieve customer
	 *
	 * @param $customer Customer object or ID
	 *
	 * @since 1.0.0
	 * @return Customer
	 */
	public function get_customer( $customer ) {
		if ( is_a( $customer, '\Stripe\Customer' ) ) {
			return $customer;
		}

		return Customer::retrieve( $customer );
	}

	/**
	 * Update customer
	 *
	 * @param $customer Customer object or ID
	 * @param $params
	 *
	 * @since 1.0.0
	 * @return Customer
	 */
	public function update_customer( $customer, $params ) {
		$customer = $this->get_customer( $customer );

		// edit
		foreach ( $params as $key => $value ) {
			$customer->{$key} = $value;
		}

		// save
		return $customer->save();
	}

	/**
	 * Create a card
	 *
	 * @param $customer Customer object or ID
	 * @param $token
	 *
	 * @return Customer
	 *
	 * @since 1.0.0
	 */
	public function create_card( $customer, $token ) {
		$customer = $this->get_customer( $customer );

		$result = $customer->sources->create(
			array(
				'card' => $token
			)
		);

		do_action( 'yith_wcstripe_card_created', $customer, $token );

		return $result;
	}

	/**
	 * Create a card
	 *
	 * @param $customer Customer object or ID
	 * @param $card_id
	 *
	 * @return Customer
	 *
	 * @since 1.0.0
	 */
	public function delete_card( $customer, $card_id ) {
		$customer    = $this->get_customer( $customer );
		$customer_id = $customer->id;

		// delete card
		$customer->sources->retrieve( $card_id )->delete();

		do_action( 'yith_wcstripe_card_deleted', $customer, $card_id );

		return $this->get_customer( $customer_id );
	}

	/**
	 * Se the default card for the customer
	 *
	 * @param $customer Customer object or ID
	 * @param $card_id
	 *
	 * @return Customer
	 *
	 * @since 1.0.0
	 */
	public function set_default_card( $customer, $card_id ) {
		$result = $this->update_customer( $customer, array(
			'default_source' => $card_id
		) );

		do_action( 'yith_wcstripe_card_set_default', $customer, $card_id );

		return $result;
	}

	/**
	 * Delete a card
	 *
	 * @param $customer
	 * @param $params
	 *
	 * @return Customer
	 *
	 * @since 1.0.0
	 */
	public function get_cards( $customer, $params = array( 'limit' => 100 ) ) {
		$customer = $this->get_customer( $customer );

		return $customer->sources->all( $params )->data;
	}

	/**
	 * Retrieve a card object for the customer
	 *
	 * @param $customer Customer object or ID
	 * @param $card_id
	 *
	 * @return Customer
	 *
	 * @since 1.0.0
	 */
	public function get_card ( $customer, $card_id, $params = array() ) {
		$customer = $this->get_customer( $customer );

		$card = $customer->sources->retrieve( $card_id, $params );

		return $card;
	}

	/**
	 * Retrieve product
	 *
	 * @param $product Product|string Product object or ID
	 *
	 * @since 1.5.1
	 * @return Product
	 */
	public function get_product( $product ) {
		if( is_a( $product, '\Stripe\Product' ) ){
			return $product;
		}

		return Product::retrieve( $product );
	}

	/**
	 * Create a plan
	 *
	 * @param array $params
	 *
	 * @return Plan
	 */
	public function create_plan( $params = array() ) {
		return Plan::create( $params );
	}

	/**
	 * Create a plan
	 *
	 * @param $plan_id
	 *
	 * @return Plan
	 *
	 */
	public function delete_plan( $plan_id ) {
		$plan = $this->get_plan( $plan_id );
		$plan->delete();
	}

	/**
	 * Get a plan
	 *
	 * @param $plan_id
	 *
	 * @return Plan|bool
	 */
	public function get_plan( $plan_id ) {
		try{
			return Plan::retrieve( $plan_id );
		} catch ( \Stripe\Error\InvalidRequest $e ) {
			return false;
		}
	}

	/**
	 * Create an invoice
	 *
	 * @param $customer Customer Customer object
	 * @param $params array Array of parameters
	 *
	 * @return \Stripe\Invoice
	 */
	public function create_invoice( $customer, $params = array() ) {
		$customer = $this->get_customer( $customer );
		return Invoice::create( array_merge( array( 'customer' => $customer->id ), $params ) );
	}

	/**
	 * Create an invoice item
	 *
	 * @param $customer Customer Customer object
	 * @param $params array Array of parameters
	 *
	 * @return \Stripe\InvoiceItem
	 */
	public function create_invoice_item( $customer, $params = array() ) {
		$customer = $this->get_customer( $customer );
		return $customer->addInvoiceItem( $params );
	}

	/**
	 * Create a subscription
	 *
	 * @param $customer
	 * @param $plan_id
	 *
	 * @return Subscription
	 */
	public function create_subscription( $customer, $plan_id, $params = array() ) {
		$customer = $this->get_customer( $customer );
		return $customer->subscriptions->create( array_merge( array( "plan" => $plan_id ), $params ) );
	}

	/**
	 * Create a subscription
	 *
	 * @param $customer
	 * @param $subscription_id
	 *
	 * @return Subscription
	 */
	public function get_subscription( $customer, $subscription_id ) {
		$customer = $this->get_customer( $customer );
		return $customer->subscriptions->retrieve( $subscription_id );
	}

	/**
	 * Retrieves subscriptions for a specific customer
	 *
	 * @param $customer
	 * @param $param
	 *
	 * @return \Stripe\Collection
	 */
	public function get_subscriptions( $customer, $params  = array() ) {
		$params = array_merge(
			$params,
			array( 'customer' => $customer )
		);

		return Subscription::all( $params );
	}

	/**
	 * Modify a subscription on stripe
	 *
	 * @param $customer
	 * @param $subscription_id
	 *
	 * @return Subscription
	 */
	public function update_subscription( $customer, $subscription_id, $params = array() ) {
		$subscription = $this->get_subscription( $customer, $subscription_id );

		foreach ( $params as $param => $value ) {
			// TODO find a better way to check for valid properties to set within subscription
			if ( in_array( $param, array( 'id', 'object', 'application_fee_percent', 'billing', 'billing_cycle_anchor', 'cancel_at_period_end', 'canceled_at', 'created', 'urrent_period_end', 'urrent_period_start', 'customer', 'days_until_due', 'discount', 'ended_at', 'items', 'livemode', 'metadata', 'plan', 'quantity', 'start', 'status', 'tax_percent', 'trial_end', 'trial_start ' ) ) ) {
				$subscription->{$param} = $value;
			}
		}

		return $subscription->save();
	}

	/**
	 * Cancel a subscription
	 *
	 * @param $customer
	 * @param $subscription_id
	 * @param $params
	 *
	 * @return Subscription
	 */
	public function cancel_subscription( $customer, $subscription_id, $params = array() ) {
		$subscription = $this->get_subscription( $customer, $subscription_id );
		return $subscription->cancel( $params );
	}

	/**
	 * Get an invoice for subscription
	 *
	 * @param $invoice_id
	 *
	 * @since 1.0.0
	 * @return Invoice
	 */
	public function get_invoice( $invoice_id ) {
		return Invoice::retrieve( $invoice_id );
	}

	/**
	 * Pay an invoice for subscription
	 *
	 * @param $invoice_id
	 *
	 * @since 1.0.0
	 * @return Invoice
	 */
	public function pay_invoice( $invoice_id ) {
		$invoice = $this->get_invoice( $invoice_id );
		$invoice->pay();
	}

	/**
	 * Retrieve an event from event ID
	 *
	 * @param $event_id string
	 * @return Event
	 */
	public function get_event( $event_id ){
		return Event::retrieve( $event_id );
	}

	/**
	 * Create webhook on Stripe
	 *
	 * @param $params array Parameters for webhook creations
	 * @return \PayPal\Api\WebhookEvent
	 */
	public function create_webhook( $params ) {
		return WebhookEndpoint::create( $params );
	}

	/**
	 * Genereate a semi-random string
	 *
	 * @since 1.0.0
	 */
	protected static function generateRandomString( $length = 24 ) {
		$characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTU';
		$charactersLength = strlen( $characters );
		$randomString     = '';
		for ( $i = 0; $i < $length; $i ++ ) {
			$randomString .= $characters[ rand( 0, $charactersLength - 1 ) ];
		}

		return $randomString;
	}

}