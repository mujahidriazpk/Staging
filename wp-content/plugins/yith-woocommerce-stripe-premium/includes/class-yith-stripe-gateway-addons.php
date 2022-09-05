<?php
/**
 * Main class
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Stripe
 * @version 1.0.0
 */

use \Stripe\Error;

if ( ! defined( 'YITH_WCSTRIPE' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCStripe_Gateway_Addons' ) ) {
	/**
	 * WooCommerce Stripe gateway class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCStripe_Gateway_Addons extends YITH_WCStripe_Gateway_Advanced {

		/**
		 * @var string $save_cards (yes|no)
		 */
		public $save_cards;

		/**
		 * Whether errors during charge should be registered as failed attempts
		 * (usually this turns to false when processing manual renew attempts)
		 *
		 * @var bool
		 */
		protected $_register_failed_attempt = true;

		/**
		 * Constructor.
		 *
		 * @return \YITH_WCStripe_Gateway_Addons
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct();

			// declare support to YWSBS
			if ( in_array( $this->mode, array( 'standard', 'elements' ) ) && $this->save_cards ) {
				$this->supports = array_merge( $this->supports, array(
					'yith_subscriptions',
					'yith_subscriptions_scheduling',
					'yith_subscriptions_pause',
					'yith_subscriptions_multiple',
					'yith_subscriptions_payment_date',
					'yith_subscriptions_recurring_amount'
				) );
			}
		}

		/* === PROCESS PAYMENTS === */

		/**
		 * Process the payment
		 *
		 * @param  int $order_id
		 * @return array
		 */
		public function process_payment( $order_id ) {
			$order      = wc_get_order( $order_id );
			$this->_current_order = $order;

			// Processing subscription
			if (
				in_array( $this->mode, array( 'standard', 'elements' ) ) &&
				$this->order_contains_subscription( $order_id ) &&
				defined( 'YITH_YWSBS_PREMIUM' ) &&
				$this->renew_mode == 'stripe'
			) {
				return $this->process_subscription();
			} else {

				return parent::process_payment( $order_id );
			}
		}

		/**
		 * Process renew, when YITH WooCommerce Subscription triggers payment
		 *
		 * @param $order \WC_Order Renew order
		 * @return bool Status of the renew operation
		 */
		public function process_renew( $order ) {
			if( ! $order ){
				$this->register_failed_renew( $order, __( 'Error while processing renew payment: no such renew order', 'yith-woocommerce-stripe' ) );
				return false;
			}

			$order_id = $order->get_id();
			$subscription_id = $this->get_subscription_id_by_order( $order_id );

			$this->log( 'Processing payment for renew order #' . $order_id );

			// Initialize SDK and set private key
			$this->init_stripe_sdk();

			if( ! $subscription_id ){
				return false;
			}

			if( $this->has_active_subscription( $subscription_id ) ){
				$this->log( sprintf( __( 'Error while processing renew payment: order #%d is part of an active Stripe Subscription. No manual renew is required', 'yith-woocommerce-stripe' ), $order_id ) );
				return false;
			}

			try {
				$result = $this->pay_renew( $order );

				if( is_wp_error( $result ) ){
					$this->register_failed_renew( $order, sprintf( __( 'Error while processing renew payment: %s', 'yith-woocommerce-stripe' ), $result->get_error_message( 'stripe_error' ) ) );
					return false;
				}

				return true;
			}
			catch( Exception $e ){
				$user_id = $order->get_customer_id();
				$retry_renew = 'yes' == $this->get_option( 'retry_with_other_cards' );

				// before registering fail, try to pay with other registered cards
				if ( $user_id && $retry_renew ) {
					$customer_tokens = WC_Payment_Tokens::get_customer_tokens( $user_id, $this->id );

					$customer_id = get_post_meta( $subscription_id, 'stripe_customer_id', true );

					$current_year = date( 'Y' );
					$current_month = date( 'm' );

					if( ! $customer_id && $user_id = $order->get_user_id() ){
						$customer = YITH_WCStripe()->get_customer()->get_usermeta_info( $user_id );
						$customer_id = $customer['id'];
					}

					if ( count( $customer_tokens ) > 1 ) {
						foreach ( $customer_tokens as $customer_token ) {
							/**
							 * @var $customer_token \WC_Payment_Token_CC
							 */
							$card_id = $customer_token->get_token();
							$exp_year = $customer_token->get_expiry_year();
							$exp_month = $customer_token->get_expiry_month();

							if ( ! $card_id ) {
								continue;
							}

							if( $exp_year < $current_year || ( $exp_year == $current_year && $exp_month < $current_month ) ){
								continue;
							}

							$params = array(
								'order_id' => $order_id,
								'source' => $card_id,
								'customer' => $customer_id
							);

							try {
								$charge = $this->process_charge( $params );

								// this attempt was successful! let's break this cycle, and move on with new Charge object
								if ( is_a( $charge, 'Stripe\Charge' ) ) {
									break;
								}
							}
							catch( Exception $e ){
								continue;
							}
						}
					}

					if( isset( $charge ) && is_a( $charge, 'Stripe\Charge' ) ){
						// Payment complete
						$order->payment_complete( $charge->id );

						// Add order note
						$order->add_order_note( sprintf( __( 'Stripe payment approved (ID: %s)', 'yith-woocommerce-stripe' ), $charge->id ) );

						// update post meta
						yit_save_prop( $order, '_captured', ( $charge->captured ? 'yes' : 'no' ) );
						yit_save_prop( $order, '_stripe_customer_id', $customer_id );

						// Return thank you page redirect
						return true;
					}
				}

				$this->register_failed_renew( $order, sprintf( __( 'Error while processing renew payment: %s', 'yith-woocommerce-stripe' ), $e->getMessage() ) );
				return false;
			}
		}

		/**
		 * Process manual renew, when user trigger it using Renew button on order page
		 *
		 * @param $order \WC_Order Renew order
		 * @return bool Status of the renew operation
		 */
		public function process_manual_renew( $order ) {
			$this->_register_failed_attempt = false;

			$order_id = $order->get_id();
			$user_id = $order->get_user_id();
			$subscription_id = $this->get_subscription_id_by_order( $order_id );

			if( ! $subscription_id ){
				return false;
			}

			// Initialize SDK and set private key
			$this->init_stripe_sdk();

			if( $this->has_active_subscription( $subscription_id ) ) {
				// old style subscription, that already registered failed attempts: we have invoice id in user meta!
				$parent_order_id = get_post_meta( $subscription_id, 'order_id', true );
				$parent_order_failed_attempts = get_post_meta( $parent_order_id, 'failed_attemps', true );

				if ( $user_id && ( $order->get_meta( 'failed_attemps' ) > 0 || $parent_order_failed_attempts > 0 ) ) {
					$failed_invoices = get_user_meta( $user_id, 'failed_invoices', true );
					$invoice_id      = isset( $failed_invoices[ $subscription_id ] ) ? $failed_invoices[ $subscription_id ] : false;

					if ( $invoice_id ) {
						try {
							$this->api->pay_invoice( $invoice_id );

							unset( $failed_invoices[ $subscription_id ] );
							update_user_meta( get_current_user_id(), 'failed_invoices', $failed_invoices );

							return true;
						} catch ( Exception $e ) {
							$this->register_failed_renew( $order, sprintf( __( 'Error while processing manual renew: %s', 'yith-woocommerce-stripe' ), $e->getMessage() ) );

							return false;
						}
					}
				}
			}
			else{
				// new style subscription, it doesn't matter if we have failed attempts, let's try to process charge
				return $this->process_renew( $order );
			}

			$this->_register_failed_attempt = true;

			return false;
		}

		/**
		 * Process refund
		 *
		 * Overriding refund method
		 *
		 * @access      public
		 * @param       int $order_id
		 * @param       float $amount
		 * @param       string $reason
		 * @return      mixed True or False based on success, or WP_Error
		 */
		public function process_refund( $order_id, $amount = null, $reason = '' ) {
			$order = wc_get_order( $order_id );
			$transaction_id = $order->get_transaction_id();
			$order_currency = $this->get_currency( $order );
			$refunds = $order->get_refunds();
			$refund = array_shift( $refunds );

			if ( isset( $order->bitcoin_inbound_address ) || isset( $order->bitcoin_uri ) ) {
				return new WP_Error( 'yith_stripe_no_bitcoin',
					sprintf(
						__( "Refund not supported for bitcoin", 'yith-woocommerce-stripe' )
					)
				);
			}

			// subdivide refund among items
			$amounts = array();

			foreach( $order->get_items() as $item_id => $item ){
				$charge_id = wc_get_order_item_meta( $item_id, '_subscription_charge_id', true );
				$index = $charge_id ? $charge_id : $transaction_id;

				if( ! isset( $amounts[ $index ] ) ){
					$amounts[ $index ] = array(
						'total' => 0,
						'refund' => 0
					);
				}

				$amounts[ $index ]['total'] += $order->get_line_total( $item, true );

				foreach ( $refund->get_items() as $refunded_item ) {
					if ( isset( $refunded_item['refunded_item_id'] ) && $refunded_item['refunded_item_id'] == $item_id ) {
						$amounts[ $index ]['refund'] += abs( $refund->get_line_total( $refunded_item, true ) );
					}
				}
			}

			foreach ( $refund->get_items( 'shipping' ) as $refunded_item ) {
				$amounts[ $transaction_id ] += abs( $refund->get_line_total( $refunded_item, true ) );
			}

			$remaining_amount = abs( $refund->get_total() ) - array_sum( array_column( $amounts, 'refund' ) );

			if( $remaining_amount > 0 ){
				foreach( $amounts as & $amount ){
					$amount['refund'] += $remaining_amount * $amount['total'] / $order->get_total();
				}
			}

			try {

				// Initializate SDK and set private key
				$this->init_stripe_sdk();
				$refund_ids = array();

				foreach( $amounts as $charge_id => $data ){
					$params = array(
						'amount' => YITH_WCStripe::get_amount( round( $data['refund'], 2 ), $order_currency, $order )
					);

					// If a reason is provided, add it to the Stripe metadata for the refund
					if ( $reason AND in_array( $reason, array( 'duplicate', 'fraudulent', 'requested_by_customer' ) ) ) {
						$params['reason'] = $reason;
					}

					$this->log( 'Stripe Refund Request: ' . print_r( $params, true ) );

					// Send the refund to the Stripe API
					$stripe_refund = $this->api->refund( $charge_id, $params );
					$refund_ids[] = $stripe_refund->id;

					$this->log( 'Stripe Refund Response: ' . print_r( $stripe_refund, true ) );
				}

				if( count( $refund_ids ) == 1 ){
					$refund_ids = array_pop( $refund_ids );
				}

				update_post_meta( $refund->get_id(), '_refund_stripe_id', $refund_ids );

				return true;

			} catch ( Error\Base $e ) {
				$message = $this->error_handling( $e, array(
					'mode' => 'note',
					'order' => $order,
					'format' => __( 'Stripe Credit Card Refund Failed with message: "%s"', 'yith-woocommerce-stripe' )
				) );

				// Something failed somewhere, send a message.
				return new WP_Error( 'yith_stripe_refund_error', $message );
			}
		}

		/**
		 * Process the subscription
		 *
		 * @param WC_Order $order
		 *
		 * @return array
		 * @internal param string $cart_token
		 */
		protected function process_subscription( $order = null ) {
			if ( empty( $order ) ) {
				$order = $this->_current_order;
			}

			$order_id = yit_get_order_id( $order );
			$order_items = $order->get_items( 'line_item' );
			$order_currency = $this->get_currency( $order );

			try {

				// Initializate SDK and set private key
				$this->init_stripe_sdk();

				// Card selected during payment
				$selected_card = $this->get_credit_card_num();

				// Set the token with card ID selected
				if ( $this->save_cards && 'new' != $selected_card && empty( $this->token ) ) {
					$this->token = $selected_card;
				}

				if ( empty( $this->token ) ) {
					$error_msg = __( 'Please make sure that your card details have been entered correctly and that your browser supports JavaScript.', 'yith-woocommerce-stripe' );

					if ( 'test' == $this->env ) {
						$error_msg .= ' ' . __( 'Developers: Please make sure that you\'re including jQuery and that there are no JavaScript errors in the page.', 'yith-woocommerce-stripe' );
					}

					$this->log( 'Wrong token ' . $this->token . ': ' . print_r( $_POST, true ) );

					throw new Error\Api( $error_msg );
				}

				// retrieve customer
				$customer = $this->get_customer( $order );

				// retrieve card from token and store it as default payment method for next subscriptions
				if( 'new' == $selected_card && ! empty( $this->token ) ){
					$card = $this->api->get_card( $customer, $this->token );

					if( $card ) {
						$this->api->set_default_card( $customer, $card->id );
						$this->save_token( $card );
					}
				}
				elseif( ! empty( $this->token ) ){
					$this->api->set_default_card( $customer, $this->token );
					$this->set_default_token( $this->token );
				}

				$subscription_total = 0;

				// create subscriptions
				if( $subscriptions = yit_get_prop( $order, 'subscriptions', true ) ) {
					foreach ( array_map( 'intval', $subscriptions ) as $subscription_id ) {
						$subscription       = ywsbs_get_subscription( $subscription_id );
						$plan               = $this->get_plan( $subscription );
						$order_item_id      = $subscription->order_item_id;
						$order_item         = isset( $order_items[ $order_item_id ] ) ? $order_items[ $order_item_id ] : false;
						$product            = method_exists( $order_item, 'get_product' ) ? $order_item->get_product() : $order->get_product_from_item( $order_item );
						$line_total         = $order->get_line_total( $order_item, true );
						$interval           = $subscription->price_time_option;
						$interval_count     = $subscription->price_is_per;
						$first_payment_time = strtotime( "+{$interval_count} {$interval}" );
						$first_payment_days = apply_filters( 'yith_wcstripe_first_payment_time', ( $first_payment_time - time() ) / DAY_IN_SECONDS, $subscription, $first_payment_time, $order );
						global $today_date_time;
						// create subscription on stripe; set billing cycle to start after one interval
						//Mujahid Code
						//echo $first_payment_days ."==".DAY_IN_SECONDS ."==".time();
						$stripe_subscription = $this->api->create_subscription( $customer, $plan->id, array_merge(
							array(
								'metadata' => apply_filters( 'yith_wcstripe_metadata', array(
									'subscription_id' => $subscription_id,
									'instance'        => $this->instance
								), 'create_subscription' )
							),
							! $plan->trial_period_days ? array(
								'prorate' => false,
								//'billing_cycle_anchor' => $first_payment_days * DAY_IN_SECONDS + time(),
							) : array(
								'trial_period_days' => $plan->trial_period_days
							)
						) );

						// create first invoice (automatic plan will start after 1 interval)
						if( $line_total ){

							// create invoice item for first payment
							$this->api->create_invoice_item( $customer, array(
								'amount'       => YITH_WCStripe::get_amount( $line_total, $order_currency, $order ), // Amount in cents!
								'currency'     => strtolower( $order_currency ),
								'description'  => apply_filters( 'yith_wcstripe_first_invoice_description', substr( strip_tags( html_entity_decode( $product->get_formatted_name() ) ), 0, 500 ) ),
								'discountable' => true,
								'subscription' => $stripe_subscription->id,
								'metadata'     => array(
									'subscription_id' => $subscription_id,
									'instance'        => $this->instance
								)
							) );

							// create invoice with previous invoice item
							$invoice = $this->api->create_invoice( $customer, array(
								'subscription' => $stripe_subscription->id,
								'description' => sprintf( __( '%s - Order %s', 'yith-woocommerce-stripe' ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ),
							) );

							// pay invoice
							$invoice = $invoice->pay();
							$charge_id = $invoice->charge;

							// update charge to add metadata and correct description
							$this->api->update_charge( $charge_id, array(
								'metadata' => array(
									'order_id' => $order_id,
									'instance' => $this->instance
								),
								'description' => apply_filters( 'yith_wcstripe_charge_description', sprintf( __( '%s - Order %s', 'yith-woocommerce-stripe' ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ),
							) );

							$subscription_total += $line_total;

							$order->add_order_note( sprintf( __( 'Subscription charged correctly (ID: <a href="%s">%s</a>)', 'yith-woocommerce-stripe' ), sprintf( $this->view_transaction_url, $charge_id ), $charge_id ) );
							$subscription->set( 'stripe_charge_id', $invoice->charge );
							wc_update_order_item_meta( $order_item_id, '_subscription_charge_id', $charge_id );
						}

						// set meta data
						$subscription->set( 'stripe_subscription_id', $stripe_subscription->id );
						$subscription->set( 'stripe_customer_id', $customer->id );
						$subscription->set( 'payment_due_date', $stripe_subscription->current_period_end );

						// set meta of order
						$user = $order->get_user();
						if ( $customer ) {
							yit_save_prop( $order, 'Subscriber ID', $customer->id );
						}
						if ( $user ) {
							yit_save_prop( $order, 'Subscriber first name', $user->first_name );
							yit_save_prop( $order, 'Subscriber last name', $user->last_name );
							yit_save_prop( $order, 'Subscriber address', $user->billing_email );
						}
						yit_save_prop( $order, 'Subscriber payment type', $this->id );
						yit_save_prop( $order, 'Stripe Subscribtion ID', $stripe_subscription->id );
					}
				}

				// pay
				update_post_meta( $order_id, '_stripe_subscription_total', $subscription_total );
				update_post_meta( $order_id, '_stripe_customer_id', $customer->id );
				$response = $this->pay( $order, $order->get_total() - $subscription_total );

				if ( $response === true ) {
					$response = array(
						'result'   => 'success',
						'redirect' => $this->get_return_url( $order )
					);
				}

				return $response;

			} catch ( Error\Base $e ) {
				$this->error_handling( $e, array(
					'mode' => 'both',
					'order' => $order,
				) );

				// if were creating a subscription, cancel it, since operation failed, and we cannot leave it active
				// this will also close pending invoices created for that subscription
				if( isset( $stripe_subscription ) ){
					$this->api->cancel_subscription( $customer, $stripe_subscription->id );
				}

				return array(
					'result'   => 'fail',
					'redirect' => ''
				);

			} catch ( Exception $e ) {
				// if were creating a subscription, cancel it, since operation failed, and we cannot leave it active
				// this will also close pending invoices created for that subscription
				if( isset( $stripe_subscription ) ){
					$this->api->cancel_subscription( $customer, $stripe_subscription->id );
				}

				wc_add_notice( $e->getMessage(), 'error' );

				return array(
					'result'   => 'fail',
					'redirect' => ''
				);

			}
		}

		/**
		 * Performs the payment on Stripe
		 *
		 * @param $order  WC_Order
		 * @param $amount float Amount to pay; if null, order total will be used instead
		 *
		 * @return bool|WP_Error
		 * @throws Error\Api|Exception
		 * @since 1.0.0
		 */
		protected function pay( $order = null, $amount = null ) {
			$result = parent::pay( $order, $amount );

			if( $result ){
				$subscriptions = $order->get_meta( 'subscriptions' );
				$customer_id = $order->get_meta( '_stripe_customer_id' );

				if( empty( $subscriptions) && ! is_null( WC()->session ) ){
					$order_args = WC()->session->get( 'ywsbs_order_args', array() );
					if( isset( $order_args['subscriptions'] ) ){
						$subscriptions = $order_args['subscriptions'];
					}

					WC()->session->set( 'ywsbs_order_args', array() );
				}

				if( ! empty( $subscriptions ) ){

					// we're processing a free sub; let's create customer & card for renews
					if( 0 == $amount || $amount * 100 < 50 ){
						$customer = $this->get_customer( $order );

						$customer_id = $customer->id;
						$order->update_meta_data( '_stripe_customer_id', $customer_id );
					}

					foreach( $subscriptions as $subscription_id ) {
						$subscription = ywsbs_get_subscription( $subscription_id );
						$subscription->set( 'stripe_customer_id', $customer_id );
					}
				}
			}

			return $result;
		}

		/**
		 * Performs the payment on Stripe
		 *
		 * @param $order  WC_Order
		 * @param $amount float Amount to pay; if null, order total will be used instead
		 *
		 * @return bool|WP_Error
		 * @throws Error\Api|Exception
		 * @since 1.0.0
		 */
		protected function pay_renew( $order = null, $amount = null ){
			// Initialize SDK and set private key
			$this->init_stripe_sdk();

			$order_id = yit_get_order_id( $order );
			$subscriptions = $order->get_meta( 'subscriptions' );
			$subscription_id = ! empty( $subscriptions ) ? array_pop( $subscriptions ) : false;

			// get amount
			$amount = ! is_null( $amount ) ? $amount : $order->get_total();

			if ( 0 == $amount ) {
				// Payment complete
				$order->payment_complete();

				return true;
			}

			if ( $amount * 100 < 50 ) {
				return new WP_Error( 'stripe_error', __( 'Sorry, the minimum allowed order total is 0.50 to use this payment method.', 'yith-woocommerce-stripe' ) );
			}

			$currency = $this->get_currency( $order );

			if( $subscription_id ){
				$customer_id = get_post_meta( $subscription_id, 'stripe_customer_id', true );
			}

			if( ! $customer_id && $user_id = $order->get_user_id() ){
				$customer = YITH_WCStripe()->get_customer()->get_usermeta_info( $user_id );
				$customer_id = $customer['id'];
			}

			if( ! $customer_id ){
				return new WP_Error( 'stripe_error', sprintf( __( 'Couldn\'t find any valid Stripe Customer ID for order #%d', 'yith-woocommerce-stripe'  ), $order_id ) );
			}

			if( $subscription_id ) {
				$subscription_source = get_post_meta( $subscription_id, 'yith_stripe_token', true );
			}

			if( $subscription_source ){
				try {
					// check card existance
					$card = $this->api->get_card( $customer_id, $subscription_source );
					$source = $card->id;
				}
				catch( Exception $e ){
					$source = false;
				}
			}

			$params = array(
				'order_id' => $order_id,
				'currency' => $currency,
				'customer' => $customer_id
			);

			if( ! empty( $source ) ){
				$params['source'] = $source;
			}

			$charge = $this->process_charge( $params );

			// charge has failed; return error message to customer
			if ( ! is_a( $charge, 'Stripe\Charge' ) && is_array( $charge ) && isset( $charge['error_charge'] ) ) {
				$error_message = sprintf( __( 'Error while processing renew payment: %s', 'yith-woocommerce-stripe' ), $charge['error_charge'] );
				return new WP_Error( 'stripe_error', $error_message );
			}

			// Payment complete
			$order->payment_complete( $charge->id );

			// Add order note
			$order->add_order_note( sprintf( __( 'Stripe payment approved (ID: %s)', 'yith-woocommerce-stripe' ), $charge->id ) );

			// update post meta
			yit_save_prop( $order, '_captured', ( $charge->captured ? 'yes' : 'no' ) );
			yit_save_prop( $order, '_stripe_customer_id', $customer_id );

			// Return thank you page redirect
			return true;
		}

		/* === HELPER METHODS === */

		/**
		 * Check if current renew order has an active subscription on Stripe side
		 *
		 * @param $subscription_id int Subscription id
		 * @return bool Whether or not a subscription was found on Stripe
		 */
		public function has_active_subscription( $subscription_id ) {
			$subscription = new YWSBS_Subscription( $subscription_id );
			$order_id = get_post_meta( $subscription_id, 'order_id', true );
			$stripe_subscription_id = get_post_meta( $subscription_id, 'stripe_subscription_id', true );

			if( $stripe_subscription_id ){
				return true;
			}

			if( ! $order = wc_get_order( $order_id ) ){
				return false;
			}

			// Initialize SDK and set private key
			$this->init_stripe_sdk();

			$plan = $this->get_plan( $subscription, $order, false );
			$customer_id = false;

			if( $subscription_id ) {
				$customer_id = get_post_meta( $subscription_id, 'stripe_customer_id', true );
			}

			if( ! $customer_id && $user_id = $order->get_user_id() ){
				$customer = YITH_WCStripe()->get_customer()->get_usermeta_info( $user_id );
				$customer_id = isset( $customer['id'] ) ? $customer['id'] : false;
			}

			if( ! $customer_id ){
				return false;
			}

			try {
				$stripe_subscriptions = $this->api->get_subscriptions( $customer_id, array( 'plan' => $plan, 'status' => 'active', 'limit' => 99 ) );

				if( ! isset( $stripe_subscriptions->data ) ){
					return false;
				}

				foreach( $stripe_subscriptions->data as $subscription ){
					if( isset( $subscription->metadata ) && isset( $subscription->metadata->subscription_id ) && $subscription->metadata->subscription_id == $subscription_id ){
						// subscription found among Stripe's subscriptions
						return true;
					}
				}

				// we couldn't locate any Stripe Subscription
				return false;
			}
			catch( Exception $e ){
				return false;
			}

		}

		/**
		 * Get subscription ID by stripe subscription id
		 */
		public function get_subscription_id( $stripe_subscription_id ) {
			global $wpdb;
			return $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID AND p.post_type = %s WHERE pm.meta_value = %s ORDER BY pm.post_id DESC LIMIT 1", 'ywsbs_subscription', $stripe_subscription_id ) );
		}

		/**
		 * Get subscription ID by one of his orders
		 *
		 * @param $order_id int Order id
		 * @return int|bool Subscription id; false if no subscription is found
		 */
		public function get_subscription_id_by_order( $order_id ) {
			$subscriptions = get_post_meta( $order_id, 'subscriptions', true );

			if( ! $subscriptions ){
				return false;
			}

			$subscription_id = array_pop( $subscriptions );

			return apply_filters( 'yith_wcstripe_subscription_from_order', $subscription_id, $order_id );
		}

		/**
		 * Retrieve the plan.
		 *
		 * If it doesn't exist, create a new one and returns it.
		 *
		 * @param $subscription YWSBS_Subscription
		 * @param $order \WC_Order Related order
		 * @param $create_if_not_exists bool Create plan if it doesn't exists
		 *
		 * @return \Stripe\Plan|bool Returns a plan if it finds one; otherwise return false $create_if_not_exists = false, or a brand new plan if it is true
		 */
		public function get_plan( $subscription, $order = null, $create_if_not_exists = true ) {
			$object_id = ! empty( $subscription->variation_id ) ? $subscription->variation_id : $subscription->product_id;
			$product = wc_get_product( $object_id );
			$order = $order ? $order : $this->_current_order;
			//$plan_amount = $subscription->line_total + $subscription->line_tax;
			//fixed the amount of the subscription plan
			$plan_amount = apply_filters( 'yith_wcstripe_subscription_amount', $subscription->subscription_total, $subscription, $order, $product );
			$plan_amount = apply_filters( 'yith_wcstripe_gateway_amount', $plan_amount, $order );

			// translate the option saved on subscription options of product to values requested by stripe
			$interval_periods = array(
				'days'   => 'day',
				'weeks'  => 'week',
				'months' => 'month',
				'years'  => 'year'
			);

			// calculate trial days
			$interval          = str_replace( array_keys( $interval_periods ), array_values( $interval_periods ), yit_get_prop( $product, '_ywsbs_price_time_option', true ) );
			$interval_count    = intval( yit_get_prop( $product, '_ywsbs_price_is_per', true ) );
			$trial_period      = yit_get_prop( $product, '_ywsbs_trial_per', true );
			$trial_time_option = yit_get_prop( $product, '_ywsbs_trial_time_option', true );

			if ( ! empty( $trial_period ) && in_array( $trial_time_option, array( 'days', 'weeks', 'months', 'years' ) ) ) {
				$trial_end = strtotime( "+{$trial_period} {$trial_time_option}" );
				$trial = ( $trial_end - time() ) / DAY_IN_SECONDS;
			} else {
				$trial_end = time();
				$trial = 0;
			}

			$trial_period_days = apply_filters( 'yith_wcstripe_plan_trial_period', intval( $trial ), $subscription, $trial_end, $order );

			// hash used to prevent differences between subscription configuration
			$hash = md5( $plan_amount . $interval . $interval_count . $trial_period_days . apply_filters( 'yith_wcstripe_gateway_currency', $subscription->order_currency, $subscription->order_id ) );

			// get plan if exists
			$plan_id = "product_{$object_id}_{$hash}";
			$plan = $this->api->get_plan( $plan_id );

			// if some parameter is changed with save plan, delete it to recreate it
			if ( $plan ) {
				return $plan;
			}

			if( ! $create_if_not_exists ){
				return false;
			}

			// retrieve order currency
			$currency = $this->get_currency( $order );

			// format the name of plan
			$product_name = strip_tags( html_entity_decode( $product->get_title() ) );
			$plan_name = '';

			if( $product->get_type() == 'variation' ){
				/**
				 * @var $product \WC_Product_Variation
				 */
				$plan_name .= wc_get_formatted_variation( $product, true );
				$plan_name .= ' - ';
			}

			$formatted_interval = $interval_count == 1 ? $interval : $interval_count . ' ' . $interval_periods[ $interval ];
			$plan_name .= sprintf( '%s / %s', wc_price( $plan_amount, array( 'currency' => $currency ) ), $formatted_interval );

			if( $trial_period_days ){
				$plan_name .= sprintf( __( ' - %s days trialing', 'yith-woocommerce-stripe' ), $trial_period_days );
			}

			$plan_name = strip_tags( html_entity_decode( $plan_name ) );

			// retrieve product, if it already exists: otherwise it will created with the plan
			$product_id = "product_{$subscription->product_id}";

			try {
				$stripe_product = $this->api->get_product( $product_id );
			}
			catch( Exception $e ){
				$stripe_product = false;
			}

			// if it doesn't exist, create it
			$plan = $this->api->create_plan( array(
				'id'                => $plan_id,
				'product'           => $stripe_product ? $stripe_product->id : array(
					'id'                => $product_id,
					'name'              => substr( $product_name, 0, 250 ),
				),
				'nickname'          => $plan_name,
				'currency'          => strtolower( $currency ),
				'interval'          => $interval,
				'interval_count'    => $interval_count,
				'amount'            => YITH_WCStripe::get_amount( $plan_amount, $currency ),
				'trial_period_days' => $trial_period_days,
				'metadata'          => apply_filters( 'yith_wcstripe_metadata', array(
					'product_id' => $object_id
				), 'create_plan' )
			) );

			return $plan;
		}

		/**
		 * Register failed renew attempt for an order, and related error message
		 *
		 * @param $order \WC_Order Renew order
		 * @param $message string Error message to log
		 * @return void
		 */
		public function register_failed_renew( $order, $message ) {
			if( $this->_register_failed_attempt ) {
				ywsbs_register_failed_payment( $order, $message );
			}

			$this->log( $message );
		}

		/**
		 * Check if order contains subscriptions.
		 *
		 * @param  int $order_id
		 * @return bool
		 */
		protected function order_contains_subscription( $order_id ) {
			return function_exists( 'YITH_WC_Subscription' ) && YITH_WC_Subscription()->order_has_subscription( $order_id );
		}
	}
}