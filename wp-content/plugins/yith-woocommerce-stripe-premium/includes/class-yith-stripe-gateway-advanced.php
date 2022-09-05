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

if ( ! class_exists( 'YITH_WCStripe_Gateway_Advanced' ) ) {
	/**
	 * WooCommerce Stripe gateway class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCStripe_Gateway_Advanced extends YITH_WCStripe_Gateway {

		/**
		 * @var string $save_cards (yes|no)
		 */
		public $save_cards;

		/**
		 * @var string $capture (yes|no)
		 */
		public $capture;

		/**
		 * @var string $debug (yes|no)
		 */
		public $debug;

		/**
		 * @var string $bitcoin (yes|no)
		 */
		public $bitcoin;

		/**
		 * @var bool $add_billing_fields
		 */
		public $add_billing_fields;

		/**
		 * @var bool $hosted_billing
		 */
		public $hosted_billing;

		/**
		 * @var bool $hosted_shipping
		 */
		public $hosted_shipping;

		/**
		 * @var bool $elements_show_zip
		 */
		public $elements_show_zip;

		/**
		 * @var bool $show_name_on_card
		 */
		public $show_name_on_card;

		/**
		 * @var string $save_cards_mode (prompt|register)
		 */
		public $save_cards_mode;

		/**
		 * @var $_current_customer \Stripe\Customer
		 */
		protected $_current_customer = null;

		/**
		 * Constructor.
		 *
		 * @return \YITH_WCStripe_Gateway_Advanced
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct();

			// gateway properties
			$this->order_button_text  = $this->get_option( 'button_label', __( 'Place order', 'yith-woocommerce-stripe' ) );
			$this->new_method_label   = __( 'Use a new card', 'yith-woocommerce-stripe' );
			$this->supports           = array(
				'products',
				'default_credit_card_form',
				'refunds'
			);

			// Add premium options
			$this->init_premium_fields();

			// Define user set variables
			$this->mode               = $this->get_option( 'mode', 'standard' );
			$this->debug              = strcmp( $this->get_option( 'debug' ), 'yes' ) == 0;
			$this->save_cards         = strcmp( $this->get_option( 'save_cards', 'yes' ), 'yes' ) == 0;
			$this->save_cards_mode    = $this->get_option( 'save_cards_mode', 'register' );
			$this->capture            = strcmp( $this->get_option( 'capture', 'no' ), 'yes' ) == 0;
			$this->bitcoin            = strcmp( $this->get_option( 'enable_bitcoin', 'no' ), 'yes' ) == 0 && strcmp( WC()->countries->get_base_country(), 'US' ) == 0 && strcmp( $this->get_currency(), 'USD' ) == 0 && $this->mode == 'hosted';
			$this->add_billing_fields = strcmp( $this->get_option( 'add_billing_fields', 'no' ), 'yes' ) == 0;
			$this->hosted_billing     = strcmp( $this->get_option( 'add_billing_hosted_fields', 'no' ), 'yes' ) == 0;
			$this->hosted_shipping    = strcmp( $this->get_option( 'add_shipping_hosted_fields', 'no' ), 'yes' ) == 0;
			$this->show_name_on_card  = strcmp( $this->get_option( 'show_name_on_card', 'yes' ), 'yes' ) == 0;
			$this->elements_show_zip  = strcmp( $this->get_option( 'elements_show_zip', 'yes' ), 'yes' ) == 0;
			$this->renew_mode         = $this->get_option( 'renew_mode', 'stripe' );

			// enable tokenization support if the option is enabled
			if ( in_array( $this->mode, array( 'standard', 'elements' ) ) && $this->save_cards ) {
				$this->supports[] = 'tokenization';
			}

			// Logs
			if ( $this->debug ) {
				$this->log = new WC_Logger();
			}

			// hooks
			add_filter( 'woocommerce_credit_card_form_fields', array( $this, 'credit_form_add_fields' ), 10, 2 );
			add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
			add_filter( 'woocommerce_stripe_hosted_args', array( $this, 'advanced_stripe_checkout_args' ), 10, 2 );
			add_filter( 'wc_payment_token_display_name', array( $this, 'token_display_name' ), 10, 2 );
		}

		/**
		 * Initialize form fields for the admin
		 *
		 * @since 1.0.0
		 */
		public function init_premium_fields() {

			$this->add_form_field( array(
				'capture' => array(
					'title'       => __( 'Capture', 'yith-woocommerce-stripe' ),
					'type'        => 'select',
					'description' => sprintf( __( 'Decide whether to immediately capture the charge or not. When "Authorize only & Capture later" is selected, the charge issues an authorization (or pre-authorization), and will be captured later. Uncaptured charges expire in %2$s7 days%3$s. %1$sFor further information, see %4$sauthorizing charges and settling later%5$s.', 'yith-woocommerce-stripe' ),
						'<br />',
						'<b>',
						'</b>',
						'<a href="https://support.stripe.com/questions/can-i-authorize-a-charge-and-then-wait-to-settle-it-later" target="_blank">',
						'</a>'
					),
					'default'     => 'no',
					'options'     => array(
						'no'  => __( 'Authorize only & Capture later', 'yith-woocommerce-stripe' ),
						'yes' => __( 'Authorize & Capture immediately', 'yith-woocommerce-stripe' )
					)
				),

				'mode' => array(
					'title'       => __( 'Payment Mode', 'yith-woocommerce-stripe' ),
					'type'        => 'select',
					'description' => sprintf( __( 'Standard will display credit card fields on your store (SSL required). %1$s Hosted mode will redirect to payment page, where customer will be able to open Stripe Checkout popup %1$s Stripe checkout will redirect the user to the checkout page hosted in Stripe. %1$s Elements will show an embedded form handled by Stripe', 'yith-woocommerce-stripe' ), '<br />' ),
					'default'     => 'standard',
					'options'     => array(
						'standard'   => __( 'Standard', 'yith-woocommerce-stripe' ),
						'hosted_std' => __( 'Hosted', 'yith-woocommerce-stripe' ),
						'hosted'     => __( 'Stripe Checkout', 'yith-woocommerce-stripe' ),
                        'elements'  => __( 'Stripe Elements', 'yith-woocommerce-stripe' )
					)
				),

				'save_cards' => array(
					'title'       => __( 'Save cards', 'yith-woocommerce-stripe' ),
					'type'        => 'checkbox',
					'label'       => __( 'Enable "Remember cards"', 'yith-woocommerce-stripe' ),
					'description' => __( "Save users' credit cards to let them use them for future payments.", 'yith-woocommerce-stripe' ),
					'default'     => 'yes'
				),

                'save_cards_mode' => array(
	                'title'       => __( 'Card registration mode', 'yith-woocommerce-stripe' ),
	                'type'        => 'select',
	                'description' => sprintf( __( 'If you choose to automatically register cards, every card used by the customer will be registered automatically %1$s Otherwise, system will register cards only when customer mark "Save card" checkbox %1$s Please note that this option does not affect Stripe, that register cards for internal processing anyway', 'yith-woocommerce-stripe' ), '<br />' ),
	                'default'     => 'standard',
	                'options'     => array(
		                'register'   => __( 'Register automatically', 'yith-woocommerce-stripe' ),
		                'prompt' => __( 'Let user choose', 'yith-woocommerce-stripe' )
	                )
                ),

                'add_billing_hosted_fields' => array(
                    'title'       => __( 'Add billing fields for Stripe Checkout', 'yith-woocommerce-stripe' ),
                    'type'        => 'checkbox',
                    'description' => __( "Option available only for \"Stripe Checkout\" payment mode.", 'yith-woocommerce-stripe' ),
                    'default'     => 'no',
                    'class'       => 'yith-billing'
                ),

                'add_shipping_hosted_fields' => array(
                    'title'       => __( 'Add shipping fields for Stripe Checkout', 'yith-woocommerce-stripe' ),
                    'type'        => 'checkbox',
                    'description' => __( "Option available only for \"Stripe Checkout\" payment mode.", 'yith-woocommerce-stripe' ),
                    'default'     => 'no',
                    'class'       => 'yith-shipping'
                ),

				'enable_bitcoin' => array(
					'title'       => __( 'Accepting Bitcoin', 'yith-woocommerce-stripe' ),
					'type'        => 'checkbox',
					'label'       => __( 'Accepting Bitcoin', 'yith-woocommerce-stripe' ),
					'description' => __( 'Option available only for "Stripe Checkout" payment mode. Stripe supports accepting Bitcoin alongside payments with credit cards.', 'yith-woocommerce-stripe' ) . '<br />'
				                     . __( 'You currently need a <b>United States bank account</b> to accept Bitcoin payments.', 'yith-woocommerce-stripe' ),
					'default'     => 'no',
					'disabled'    => strpos( 'US', WC()->countries->get_base_country() ) === false
				),

				'add_billing_fields' => array(
					'title'       => __( 'Add billing fields', 'yith-woocommerce-stripe' ),
					'type'        => 'checkbox',
					'description' => __( 'If you have installed any WooCommerce extension to edit checkout fields, this option allows you require some necessary information associated to the credit card, in order to further reduce the risk of fraudulent transactions.', 'yith-woocommerce-stripe' ),
					'default'     => 'no'
				),

				'show_name_on_card' => array(
					'title'       => __( 'Show Name on Card', 'yith-woocommerce-stripe' ),
					'type'        => 'checkbox',
					'description' => __( 'Show Name on Card field in Elements and Standard form; Name will be sent within card data, to let Stripe perform additional check over user and better evaluate risk', 'yith-woocommerce-stripe' ),
					'default'     => 'yes'
				),

                'elements_show_zip' => array(
                    'title'       => __( 'Show Zip Field', 'yith-woocommerce-stripe' ),
                    'type'        => 'checkbox',
                    'description' => __( 'Show Zip field in Elements form; ZIP code will be sent within card data, to let Stripe perform additional check over user and better evaluate risk', 'yith-woocommerce-stripe' ),
                    'default'     => 'yes'
                )
			), 'after', 'description' );

			if( defined( 'YITH_YWSBS_PREMIUM' ) && defined( 'YITH_YWSBS_VERSION' ) && version_compare( YITH_YWSBS_VERSION, '1.4.6', '>=' ) ) {
				$this->add_form_field( array(
					'subscription' => array(
						'title'       => __( 'Subscriptions', 'yith-woocommerce-stripe' ),
						'type'        => 'title',
						'description' => __( 'Choose option to integrate Stripe gateway with YITH WooCommerce Subscription Premium', 'yith-woocommerce-stripe' ),
					),

					'renew_mode' => array(
						'title'       => __( 'Subscriptions\' renew mode', 'yith-woocommerce-stripe' ),
						'type'        => 'select',
						'description' => sprintf( __( 'Select how you want to process Subscriptions\' renews. %1$s Stripe Classic will create subscriptions on Stripe side, and let Stripe manage renews automatically. %1$s YWSBS Renews will pay renews when YITH WooCommerce Subscription triggers them; this grants more flexibility.', 'yith-woocommerce-stripe' ), '<br/>' ),
						'default'     => 'stripe',
						'options'     => array(
							'stripe' => __( 'Stripe Classic', 'yith-woocommerce-stripe' ),
							'ywsbs'  => __( 'YWSBS Renews', 'yith-woocommerce-stripe' )
						)
					),

                    'retry_with_other_cards' => array(
	                    'title'       => __( 'When renew fails, try again with other cards', 'yith-woocommerce-stripe' ),
	                    'type'        => 'checkbox',
	                    'description' => __( 'When a renew fails, and customer have additional cards registered, try to process payment with other cards, before giving up', 'yith-woocommerce-stripe' ),
	                    'default'     => 'no'
                    )

				), 'after', 'elements_show_zip' );
			}

			$this->add_form_field( array(
				'button_label' => array(
					'title'       => __( 'Button label', 'yith-woocommerce-stripe' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => __( 'Define the label for the button on checkout.', 'yith-woocommerce-stripe' ),
					'default'     => __( 'Placeholder.', 'yith-woocommerce-stripe' )
				),
			), 'after', 'customization' );

			$this->add_form_field( array(
				'debug' => array(
					'title'       => __( 'Debug Log', 'yith-woocommerce-stripe' ),
					'type'        => 'checkbox',
					'label'       => __( 'Enable logging', 'yith-woocommerce-stripe' ),
					'default'     => 'no',
					'description' => sprintf( __( 'Log Stripe events inside <code>%s</code>', 'yith-woocommerce-stripe' ), wc_get_log_file_path( 'stripe' ) ) . '<br />' . sprintf( __( 'You can also consult the logs in your <a href="%s">Logs Dashboard</a>, without checking this option.', 'yith-woocommerce-stripe' ), 'https://dashboard.stripe.com/logs' )
				),
			), 'after', 'enabled_test_mode' );

			$webhook_already_processed = get_option( "yith_wcstripe_{$this->env}_webhook_processed", false );
			$generate_webhook_text = $webhook_already_processed ?
				sprintf( __( 'You already configured your webhook for %s environment. If you want to process them again use the following shortcut: ', 'yith-woocommerce-stripe' ), $this->env ) . '<button id="config_webhook" class="button-secondary" style="vertical-align: middle; margin-left: 15px;">' . __( 'Configure Webhooks', 'yith-woocommerce-stripe' ) . '</button>' :
				sprintf( __( 'You can automatically configure your webhooks for %s environment by using the following shortcut: ', 'yith-woocommerce-stripe' ), $this->env ) . '<button id="config_webhook" class="button-secondary" style="vertical-align: middle; margin-left: 15px;">' . __( 'Configure Webhooks', 'yith-woocommerce-stripe' ) . '</button>';

			$this->add_form_field( array(
				'webhooks'        => array(
					'title'       => __( 'Config Webhooks', 'yith-woocommerce-stripe' ),
					'type'        => 'title',
					'description' => sprintf( __( 'You can configure the webhook url %s in your <a href="%s">developers settings</a>. All the webhooks for your account will be sent to this endpoint.', 'yith-woocommerce-stripe' ), '<code>' . esc_url( add_query_arg( 'wc-api', 'stripe_webhook', site_url( '/' ) ) ) . '</code>', 'https://dashboard.stripe.com/account/webhooks' ) . '<br /><br />'
					                 . __( "It's important to note that only test webhooks will be sent to your development webhook url. Yet, if you are working on a live website, <b>both live and test</b> webhooks will be sent to your production webhook URL. This is due to the fact that you can create both live and test objects under a production application.", 'yith-woocommerce-stripe' ) . '<br /><br />'
					                 . sprintf( __( 'For more information about webhooks, see the <a href="%s">webhook documentation</a>', 'yith-woocommerce-stripe' ), 'https://stripe.com/docs/webhooks' ) . '<br /><br />'
					                 . $generate_webhook_text
				),
			), 'after', 'live_publishable_key' );

			$this->add_form_field( array(
				'security'         => array(
					'title'       => __( 'Security', 'yith-woocommerce-stripe' ),
					'type'        => 'title',
					'description' => __( 'Enable here the testing mode, to debug the payment system before going into production', 'yith-woocommerce-stripe' ),
				),
				'enable_blacklist'    => array(
					'title'   => __( 'Enable Blacklist', 'yith-woocommerce-stripe' ),
					'type'    => 'checkbox',
					'label'   => __( 'Hide gateway payment on frontend if the same user or the same IP address have already failed a payment. The blacklist table is available on WooCommerce -> Stripe Blacklist', 'yith-woocommerce-stripe' ),
					'default' => 'no'
				),
			), 'after', 'modal_image' );

		}

		/**
		 * Handling payment and processing the order.
		 *
		 * @param int $order_id
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function process_payment( $order_id ) {
			$order = wc_get_order( $order_id );
			$this->_current_order = $order;
			$this->log( 'Generating payment form for order ' . $order->get_order_number() . '.' );

			if ( 'hosted_std' == $this->mode ) {
				return $this->process_hosted_payment();
			} else {
				return $this->process_standard_payment();
			}
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

			if ( ! $transaction_id ) {
				return new WP_Error( 'yith_stripe_no_transaction_id',
					sprintf(
						__( "There isn't any charge linked to this order", 'yith-woocommerce-stripe' )
					)
				);
			}

			if ( yit_get_prop( $order, 'bitcoin_inbound_address' ) || yit_get_prop( $order, 'bitcoin_uri' ) ) {
				return new WP_Error( 'yith_stripe_no_bitcoin',
					sprintf(
						__( "Refund not supported for bitcoin", 'yith-woocommerce-stripe' )
					)
				);
			}

			try {

				// Initializate SDK and set private key
				$this->init_stripe_sdk();

				$params = array();

				// get the last refund object created before to process this method, to get own object
				$refund = array_shift( $order->get_refunds() );

				// If the amount is set, refund that amount, otherwise the entire amount is refunded
				if ( $amount ) {
					$params['amount'] = YITH_WCStripe::get_amount( $amount, $order_currency, $order );
				}

				// If a reason is provided, add it to the Stripe metadata for the refund
				if ( $reason && in_array( $reason, array( 'duplicate', 'fraudulent', 'requested_by_customer' ) ) ) {
					$params['reason'] = $reason;
				}

				$this->log( 'Stripe Refund Request: ' . print_r( $params, true ) );

				// Send the refund to the Stripe API
				$stripe_refund = $this->api->refund( $transaction_id, $params );
				yit_save_prop( $refund, '_refund_stripe_id', $stripe_refund->id );

				$this->log( 'Stripe Refund Response: ' . print_r( $stripe_refund, true ) );

				$order->add_order_note( sprintf( __( 'Refunded %1$s - Refund ID: %2$s', 'woocommerce' ), $amount, $stripe_refund['id'] ) );

				return true;

			} catch ( Error\Api $e ) {
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
		 * Extend arguments for Stripe checkout
		 *
		 * @since 1.0.0
		 *
		 * @param $args
		 * @param $order_id
		 *
		 * @return
		 */
		public function advanced_stripe_checkout_args( $args, $order_id ) {
			if ( $this->bitcoin ) {
				$args['bitcoin'] = 'true';
			}

			return $args;
		}

		/**
		 * Handling payment and processing the order.
		 *
		 * @param WC_Order $order
		 *
		 * @return array
		 * @since 1.0.0
		 */
		protected function process_standard_payment( $order = null ) {
			if ( empty( $order ) ) {
				$order = $this->_current_order;
			}

			$order_id = yit_get_order_id( $order );

			try {

				// Initialize SDK and set private key
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

				// pay
				$response = $this->pay( $order );

				if ( $response === true ) {
					$response = array(
						'result'   => 'success',
						'redirect' => $this->get_return_url( $order )
					);

				} elseif ( is_a( $response, 'WP_Error' ) ) {
					throw new Error\Api( $response->get_error_message( 'stripe_error' ), null );
				}

				return $response;

			} catch ( Error\Base $e ) {
			    $this->error_handling( $e, array(
			        'mode' => 'both',
                    'order' => $order
                ) );

				return array(
					'result'   => 'fail',
					'redirect' => ''
				);
			} catch( Exception $e ){
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
			// Initialize SDK and set private key
			$this->init_stripe_sdk();

			$order_id = yit_get_order_id( $order );

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

			$params = array(
			    'order_id' => $order_id,
                'amount' => $amount,
                'currency' => $currency
            );

			// set customer if there is one
			$customer = $this->get_customer( $order );
			$params['customer'] = $customer->id;

			// set card (token will contain stripe card id; it will be card just added, or an existing card)
			$params['source'] = $this->token;

			$charge = $this->process_charge( $params );

			// save card token
            $token = $this->save_token( $charge->source );

            if( $token ) {
                $order->add_payment_token( $token );
            }

			// Payment complete
			$order->payment_complete( $charge->id );

			// Add order note
			$order->add_order_note( sprintf( __( 'Stripe payment approved (ID: %s)', 'yith-woocommerce-stripe' ), $charge->id ) );

			// Remove cart
			WC()->cart->empty_cart();

			// update post meta
			yit_save_prop( $order, '_captured', ( $charge->captured ? 'yes' : 'no' ) );
			yit_save_prop( $order, '_stripe_customer_id', $customer->id );

			// Return thank you page redirect
			return true;
		}

		/**
		 * Process charge to Stripe, generating set of parameters to send via API
		 *
		 * @param $param array Array of params to populate charge API request
		 * @return \Stripe\Charge Charge object, if everything worked as expected
		 *
		 * @throws Error\Api Api error
		 * @throws Exception Generic error
		 */
		protected function process_charge( $params ){
			$order = wc_get_order( isset( $params['order_id'] ) ? $params['order_id'] : 0 );

			if( ! $order ){
				throw new Exception( __( 'No such order', 'yith-woocommerce-stripe' ) );
			}

		    $params['additional_params']['capture'] = apply_filters( 'yith_wcstripe_capture_payment', $this->capture, $order );

		    return parent::process_charge( $params );
		}

		/**
		 * Get credit card number from post
		 *
		 * @access protected
		 * @return string
		 * @author Francesco Licandro
		 */
		protected function get_credit_card_num() {

			$card_id = isset( $_POST['wc-yith-stripe-payment-token'] ) ? $_POST['wc-yith-stripe-payment-token'] : 'new';

			if ( 'new' != $card_id ) {
				$token = WC_Payment_Tokens::get( $card_id );
				if ( $token->get_user_id() === get_current_user_id() ) {
					$card_id = $token->get_token();
				}
			}

			return apply_filters( 'yith_stripe_selected_card', $card_id );
		}

		/* === FRONTEND METHODS === */

		/**
		 * Javascript library
		 *
		 * @since 1.0.0
		 */
		public function payment_scripts() {
		    global $wp;

			$load_scripts = false;

			if ( $this->is_available() && ( is_checkout() || is_wc_endpoint_url( 'add-payment-method' ) || apply_filters('yith_wcstripe_load_assets', false )) ) {
				$load_scripts = true;
			}

			if ( false === $load_scripts ) {
				return;
			}

			$suffix               = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$wc_assets_path       = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';

			// style
			if( in_array( $this->mode, array( 'standard', 'elements' ) ) ){
                wp_register_style( 'stripe-css', YITH_WCSTRIPE_URL . 'assets/css/stripe.css', array(), YITH_WCSTRIPE_VERSION );
                wp_enqueue_style( 'stripe-css' );
            }

			if ( 'standard' == $this->mode ) {
				wp_enqueue_style( 'woocommerce_prettyPhoto_css', $wc_assets_path . 'css/prettyPhoto.css' );
				wp_enqueue_script( 'prettyPhoto', $wc_assets_path . 'js/prettyPhoto/jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ), '3.1.5', true );
			}

			// scripts
			if ( 'hosted' == $this->mode ){
				wp_register_script( 'stripe-js', 'https://checkout.stripe.com/checkout.js', array('jquery'), YITH_WCSTRIPE_VERSION, true );
				wp_register_script( 'yith-stripe-js', YITH_WCSTRIPE_URL . 'assets/js/stripe-checkout.js', array('jquery', 'stripe-js'), YITH_WCSTRIPE_VERSION, true );
				wp_enqueue_script( 'yith-stripe-js' );

				if( is_checkout_pay_page() ){
					$order_id = absint( $wp->query_vars['order-pay'] );
					$order = wc_get_order( $order_id );
					$order_total = $order->get_total();
			    }
                else{
				    $cart_total = WC()->cart->total;
	                $order_total = $cart_total;
                }

				$order_currency = strtolower( $this->get_currency() );

				wp_localize_script( 'yith-stripe-js', 'yith_stripe_info', array(
					'public_key'      => $this->public_key,
					'mode'            => $this->mode,
					'amount'          => YITH_WCStripe::get_amount( $order_total, $order_currency, isset( $order ) ? $order : null ),
					'currency'        => $order_currency,
					'name'            => esc_html( get_bloginfo( 'name' ) ),
					'description'     => '',
					'image'           => $this->modal_image,
					'bitcoin'         => esc_attr( $this->bitcoin ? 'true' : 'false' ),
					'capture'         => esc_attr( $this->capture ? 'true' : 'false' ),
					'locale'          => apply_filters( 'yith_stripe_locale', substr( get_locale(), 0, 2 ) ),
					'billing_email'   => wp_get_current_user()->billing_email,
					'refresh_details' => wp_create_nonce( 'refresh-details' ),
					'ajaxurl'               => admin_url( 'admin-ajax.php' ),
                    'billing_address' => esc_attr( $this->hosted_billing ? 'true' : 'false' ),
                    'shipping_address' => esc_attr( $this->hosted_shipping ? 'true' : 'false' ),
				) );
			}
			elseif( 'standard' == $this->mode ) {
				if ( 'test' == $this->env ) {
					wp_register_script( 'stripe-js', 'https://js.stripe.com/v2/stripe-debug.js', array( 'jquery' ), false, true );
				} else {
					wp_register_script( 'stripe-js', 'https://js.stripe.com/v2/', array( 'jquery' ), false, true );
				}

				wp_register_script( 'yith-stripe-js', YITH_WCSTRIPE_URL . 'assets/js/yiths.js', array( 'jquery', 'stripe-js' ), YITH_WCSTRIPE_VERSION, true );
				wp_enqueue_script( 'yith-stripe-js' );

				wp_localize_script( 'yith-stripe-js', 'yith_stripe_info', array(
					'public_key'  => $this->public_key,
					'mode'        => $this->mode,
					'card.number' => apply_filters( 'yith_wcstripe_card_number_error_message', __( 'The credit card number appears to be invalid.', 'yith-woocommerce-stripe' ) ),
					'card.expire' => apply_filters( 'yith_wcstripe_card_expiration_error_message', __( 'The expiration date appears to be invalid.', 'yith-woocommerce-stripe' ) ),
					'card.cvc'    => apply_filters( 'yith_wcstripe_cvc_number_error_message', __( 'The CVC number appears to be invalid.', 'yith-woocommerce-stripe' ) ),
					'card.name'   => apply_filters( 'yith_wcstripe_name_on_card_error_message', __( 'A valid Name on Card is required.', 'yith-woocommerce-stripe' ) ),
					'card.zip'   => apply_filters( 'yith_wcstripe_name_on_card_error_message', __( 'Please enter card zip code.', 'yith-woocommerce-stripe' ) ),
					'billing.fields' => apply_filters( 'yith_wcstripe_billing_fields_error_message', __( '', 'yith-woocommerce-stripe' ) ),
					//'billing.fields' => apply_filters( 'yith_wcstripe_billing_fields_error_message', __( 'You have to add extra information to checkout.', 'yith-woocommerce-stripe' ) ),
				) );
			}
			elseif( 'elements' == $this->mode ) {
				wp_register_script( 'stripe-js', 'https://js.stripe.com/v3/', array( 'jquery' ), YITH_WCSTRIPE_VERSION, true );
				wp_register_script( 'yith-stripe-js', YITH_WCSTRIPE_URL . 'assets/js/stripe-elements.js', array('jquery', 'stripe-js'), YITH_WCSTRIPE_VERSION, true );
				wp_enqueue_script( 'yith-stripe-js' );

				wp_localize_script( 'yith-stripe-js', 'yith_stripe_info', array(
					'public_key'            => $this->public_key,
					'mode'                  => $this->mode,
					'elements_container_id' => '#' . esc_attr( $this->id ) . '-card-elements',
					'currency'              => strtolower( $this->get_currency() ),
					'show_zip'              => $this->elements_show_zip
				) );
			}
		}

		/**
		 * Payment form on checkout page
		 *
		 * @since 1.0.0
		 */
		public function payment_fields() {
			// backard compatibility for cards list on checkout, but we force themes users to use new woocommerce templates
			if ( strpos( wc_locate_template( 'stripe-checkout-cards.php', WC()->template_path() . 'checkout/', YITH_WCSTRIPE_DIR . 'templates/' ), WC()->template_path() ) !== false ) {
				_deprecated_file( 'stripe-checkout-cards.php', '1.2.9', null, __( 'Remove the template stripe-checkout-cards.php from woocommerce/checkout folder of your theme and stylize the cards list from CSS of the theme.', 'yits' ) );
				$this->legacy_payment_fields();
				return;
			}

			$description = $this->get_description();

			if ( 'test' == $this->env ) {
				$description .= ' ' . sprintf( __( 'TEST MODE ENABLED. Use a test card: %s', 'yith-woocommerce-stripe' ), '<a href="https://stripe.com/docs/testing">https://stripe.com/docs/testing</a>' );
			}

			if ( $description ) {
				echo wpautop( wptexturize( trim( $description ) ) );
			}

			if ( in_array( $this->mode, array( 'standard', 'elements' ) ) ) {

				if ( 'standard' == $this->mode && $this->bitcoin ) { ?>

					<div class="payment-mode-choise">
						<label class="stripe-mode">
							<input type="radio" name="yith-stripe-mode" id="yith-stripe-mode-card" value="card"<?php checked(true) ?> />
							<?php _e( 'Card', 'yith-woocommerce-stripe' ); ?>
						</label>

						<label>
							<input type="radio" name="yith-stripe-mode" id="yith-stripe-mode-bitcoin" value="bitcoin" />
							<?php _e( 'Bitcoin', 'yith-woocommerce-stripe' ); ?>
						</label>
					</div>

					<?php
				}

				WC_Payment_Gateway_CC::payment_fields();
			}
		}

		/**
		 * Payment form on checkout page
		 *
		 * @since 1.0.0
		 */
		public function legacy_payment_fields() {
			$description = $this->get_description();

			if ( 'test' == $this->env ) {
				$description .= ' ' . sprintf( __( 'TEST MODE ENABLED. Use a test card: %s', 'yith-woocommerce-stripe' ), '<a href="https://stripe.com/docs/testing">https://stripe.com/docs/testing</a>' );
			}

			if ( $description ) {
				echo wpautop( wptexturize( trim( $description ) ) );
			}

			if ( 'standard' == $this->mode ) {
				$card_saved = false;

				if ( $this->bitcoin ) { ?>

					<div class="payment-mode-choise">
						<label class="stripe-mode">
							<input type="radio" name="yith-stripe-mode" id="yith-stripe-mode-card" value="card"<?php checked(true) ?> />
							<?php _e( 'Card', 'yith-woocommerce-stripe' ); ?>
						</label>

						<label>
							<input type="radio" name="yith-stripe-mode" id="yith-stripe-mode-bitcoin" value="bitcoin" />
							<?php _e( 'Bitcoin', 'yith-woocommerce-stripe' ); ?>
						</label>
					</div>

					<?php
				}

				?>
				<div class="yith-stripe-mode-card"><?php

				// List saved cards
				if ( $this->save_cards && is_user_logged_in() ) {

					$this->init_stripe_sdk();
					$customer = YITH_WCStripe()->get_customer()->get_usermeta_info( get_current_user_id() );

					if ( ! empty( $customer['cards'] ) ) {
						$cards = array();

						foreach ( $customer['cards'] as $the ) {
							$card            = new stdClass();
							$card->id        = $the->id;
							$card->brand     = $the->brand;
							$card->slug      = array_values( array_keys( $this->cards, $card->brand ) );
							$card->slug      = array_shift( $card->slug );
							$card->icon      = WC_HTTPS::force_https_url( WC()->plugin_url() . '/assets/images/icons/credit-cards/' . $card->slug . '.png' );
							$card->last4     = $the->last4;
							$card->exp_month = str_pad( $the->exp_month, 2, '0', STR_PAD_LEFT );
							$card->exp_year  = str_pad( substr( $the->exp_year, - 2 ), 2, '0', STR_PAD_LEFT );

							$cards[] = $card;
						}

						wc_get_template( 'stripe-checkout-cards.php', array(
							'cards'    => $cards,
							'customer' => $customer
						), WC()->template_path() . 'checkout/', YITH_WCSTRIPE_DIR . 'templates/' );

						$card_saved = true;

					}

				}

				if ( ! $card_saved ) {
					?><input type="radio" value="new" name="wc-yith-stripe-payment-token" class="input-radio"
					         id="wc-yith-stripe-payment-token-new" checked="checked" style="display:none;"/><?php
				}

				$this->credit_card_form( array( 'fields_have_names' => false ) );

				?></div><?php

				if ( $this->bitcoin ) {

				    $order_currency = $this->get_currency();

					?><div class="yith-stripe-mode-bitcoin" style="display:none;">

					<input type="hidden" name="bitcoin-amount" value="<?php echo YITH_WCStripe::get_amount( $this->get_order_total(), $order_currency ) ?>" />
					<input type="hidden" name="bitcoin-signature" value="<?php echo strtoupper( md5( YITH_WCStripe::get_amount( $this->get_order_total(), $order_currency ) . $this->private_key ) ) ?>" />
					<input type="hidden" name="bitcoin-currency" value="<?php echo $order_currency ?>" />

					</div><?php

				}

				wp_enqueue_script( 'prettyPhoto' );
			}
		}

		/**
		 * Add checkbox to choose if save credit card or not
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function credit_form_add_fields( $fields, $id ) {
			if ( $id != $this->id ) {
				return $fields;
			}

			$fields = array( 'fields-container' => '<div class="' . esc_attr( $this->id ) . '-form-container ' . $this->mode . '">' );

            $form_row_first = ! wp_is_mobile() ? 'form-row-first' : '';
            $form_row_last = ! wp_is_mobile() ? 'form-row-last' : '';

			if ( 'standard' == $this->mode ) {
				$fields = array_merge( $fields,
				   /* $this->show_name_on_card ? array(
                        'card-name-field' => '<p class="form-row ' . $form_row_first . ' ">
                            <label for="' . esc_attr( $this->id ) . '-card-name">' . apply_filters( 'yith_wcstripe_name_on_card_label', __( 'Name on Card', 'yith-woocommerce-stripe' ) ) . ' <span class="required">*</span></label>
                            <input id="' . esc_attr( $this->id ) . '-card-name" class="input-text wc-credit-card-form-card-name" type="text" autocomplete="off" placeholder="' . __( 'Name on Card', 'yith-woocommerce-stripe' ) . '" ' . $this->field_name( 'card-name' ) . ' />
                        </p>',
                    ) : array(),*/
					array(),
					array(
                        'card-number-field' => '<p class="form-row form-row-first">
                            <label for="' . esc_attr( $this->id ) . '-card-number">' . apply_filters( 'yith_wcstripe_card_number_label', __( 'Card Number', 'yith-woocommerce-stripe' ) ) . ' <span class="required">*</span></label>
                            <input id="' . esc_attr( $this->id ) . '-card-number" name="card-number" class="input-text wc-credit-card-form-card-number" type="text" maxlength="20" autocomplete="off" placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" ' . $this->field_name( 'card-number' ) . ' />
                        </p>',

                        'card-expiry-field' => '<p class="form-row form-row-last">
                            <label for="' . esc_attr( $this->id ) . '-card-expiry">' . apply_filters( 'yith_wcstripe_card_expiry_label', __( 'Expiration Date (MM/YY)', 'yith-woocommerce-stripe' ) ) . ' <span class="required">*</span></label>
                            <input id="' . esc_attr( $this->id ) . '-card-expiry" class="input-text wc-credit-card-form-card-expiry" type="text" autocomplete="off" placeholder="' . esc_attr__( 'MM / YY', 'yith-woocommerce-stripe' ) . '" ' . $this->field_name( 'card-expiry' ) . ' />
                        </p>',
				    )
                );

			}
			elseif ( 'elements' == $this->mode ){
                $fields = array_merge( $fields,
	                $this->show_name_on_card ? array(
                        'card-name-field' => '<p class="form-row form-row-full">
                            <label for="' . esc_attr( $this->id ) . '-card-name">' . apply_filters( 'yith_wcstripe_name_on_card_label', __( 'Name on Card', 'yith-woocommerce-stripe' ) ) . ' <span class="required">*</span></label>
                            <input id="' . esc_attr( $this->id ) . '-card-name" class="input-text wc-credit-card-form-card-name" type="text" autocomplete="off" placeholder="' . __( 'Name on Card', 'yith-woocommerce-stripe' ) . '" ' . $this->field_name( 'card-name' ) . ' />
                        </p>',
                    ) : array(),
                    array(
                        'card-elements' => '
                            <label for="' . esc_attr( $this->id ) . '-card-elements">' . apply_filters( 'yith_wcstripe_name_on_card_label', __( 'Card Details', 'yith-woocommerce-stripe' ) ) . ' <span class="required">*</span></label>
                            <div id="' . esc_attr( $this->id ) . '-card-elements"></div>
                        ',
                    )
                );
            }
            // add cvc popup suggestion
			if ( 'standard' == $this->mode && ! $this->supports( 'credit_card_form_cvc_on_saved_method' ) ) {
				
				$fields['card-cvc-field'] = '<p class="form-row form-row-first card-field card-field-1">
					<label for="' . esc_attr( $this->id ) . '-card-cvc">' . apply_filters( 'yith_wcstripe_card_cvc_label', __( 'Security Code', 'yith-woocommerce-stripe' ) ) . ' <span class="required">*</span> <a href="#cvv-suggestion" class="cvv2-help" rel="prettyPhoto">' . apply_filters( 'yith_wcstripe_what_is_my_cvv_label', __( 'What is my CVC code?', 'yith-woocommerce-stripe' ) ) . '</a></label>
					<input id="' . esc_attr( $this->id ) . '-card-cvc" class="input-text wc-credit-card-form-card-cvc" type="text" autocomplete="off" placeholder="' . esc_attr__( 'CVC', 'woocommerce' ) . '" ' . $this->field_name( 'card-cvc' ) . ' />
				</p>
				
				
				<div id="cvv-suggestion">
					<p style="font-size: 13px;">
						<strong>' . __( 'Visa&reg;, Mastercard&reg;, and Discover&reg; cardholders:', 'yith-woocommerce-stripe' ) . '</strong><br>
						<a href="//www.cvvnumber.com/" target="_blank"><img height="192" src="//www.cvvnumber.com/csc_1.gif" width="351" align="left" border="0" alt="cvv" style="width: 220px; height:auto;"></a>
						' . __( 'Turn your card over and look at the signature box. You should see either the entire 16-digit credit card number or just the last four digits followed by a special 3-digit code. This 3-digit code is your CVV number / Card Security Code.', 'yith-woocommerce-stripe' ) . '
					</p>
					<p>&nbsp;</p>
					<p style="font-size: 13px;">
						<strong>' . __( 'American Express&reg; cardholders:', 'yith-woocommerce-stripe' ) . '</strong><br>
						<a href="//www.cvvnumber.com/" target="_blank"><img height="140" src="//www.cvvnumber.com/csc_2.gif" width="200" align="left" border="0" alt="cid" style="width: 220px; height:auto;"></a>
						' . __( 'Look for the 4-digit code printed on the front of your card just above and to the right of your main credit card number. This 4-digit code is your Card Identification Number (CID). The CID is the four-digit code printed just above the Account Number.', 'yith-woocommerce-stripe' ) . '
					</p>
				</div>';
				
				$fields['card-name-field'] = '<p class="form-row form-row-first woocommerce-validated card-field card-field-2" >
                            <label for="yith-stripe-card-name">Name on Card <span class="required">*</span></label>
                            <input id="yith-stripe-card-name" class="input-text wc-credit-card-form-card-name" type="text" autocomplete="off" placeholder="Name on Card">
                        </p>';
				$fields['card-zip-field'] ='<p class="form-row form-row-last card-field card-field-3 woocommerce-validated">
					<label for="' . esc_attr( $this->id ) . '-card-cvc">' . apply_filters( 'yith_wcstripe_card_cvc_label', __( 'Card Zip Code', 'yith-woocommerce-stripe' ) ) . ' <span class="required">*</span></label>
					<input id="' . esc_attr( $this->id ) . '-card-zip" class="input-text wc-credit-card-form-card-zip" type="text" autocomplete="off"' . $this->field_name( 'card-zip' ) . ' />
				</p>';
				
			}

			// add checkout fields for credit cart
			if ( in_array( $this->mode, array( 'standard', 'elements' ) ) && $this->add_billing_fields ) {
				$fields_to_check = array( 'billing_country', 'billing_city', 'billing_address_1', 'billing_address_2', 'billing_state', 'billing_postcode' );
				$original_fields = WC()->countries->get_address_fields( method_exists( WC()->customer, 'get_billing_country' ) ? WC()->customer->get_billing_country() :  WC()->customer->get_country(), 'billing_' );
				$shown_fields = is_checkout() ? WC()->checkout()->checkout_fields['billing'] : array();

				$fields['separator'] = '<hr style="clear: both;" />';

				foreach ( $fields_to_check as $i => $field_name ) {
					if ( isset( $shown_fields[ $field_name ] ) ) {
						unset( $fields_to_check[ $i ] );
						continue;
					}

					if ( is_checkout() ) {
						$value = WC()->checkout()->get_value( str_replace( array( 'billing_', 'address_1' ), array( '', 'address' ), $field_name ) );
					} else {
						$value = get_user_meta( get_current_user_id(), $field_name, true );
					}
					$fields[ $field_name ] = woocommerce_form_field( $field_name, array_merge( array( 'return' => true ), $original_fields[ $field_name ] ), $value );

				}

				if ( empty( $fields_to_check ) ) {
					unset( $fields['separator'] );
				}

			}

			$fields = array_merge(
			    $fields,
                array(
                    'fields-container-end' => '</div>'
                )
            );

			return $fields;
		}

		/**
		 * Outputs a checkbox for saving a new payment method to the database.
		 */
		public function save_payment_method_checkbox() {
		    if( $this->save_cards_mode == 'prompt' ) {
			    parent::save_payment_method_checkbox();
		    }
		    else{
		        return;
            }
		}

		/* === BLACKLIST METHODS === */

		/**
		 * Method to check blacklist (only for premium)
		 *
		 * @since 1.1.3
		 *
		 * @param bool $user_id
		 * @param bool $ip
		 *
		 * @return bool
		 */
		public function is_blocked( $user_id = false, $ip = false ) {
			if ( $this->get_option( 'enable_blacklist', 'no' ) == 'no' ) {
				return false;
			}

			global $wpdb;

			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}

			if ( ! $ip ) {
				$ip = $_SERVER['REMOTE_ADDR'];
			}

			$res = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM {$wpdb->yith_wc_stripe_blacklist} WHERE ( user_id = %d OR ip = %s ) AND unbanned = 0", $user_id, $ip ) );

			return $res > 0 ? true : false;
		}

		/**
		 * Check if the user is unbanned by admin
		 *
		 * @param bool $user_id
		 * @param bool $ip
		 *
		 * @return bool
		 */
		public function is_unbanned( $user_id = false, $ip = false ) {
			if ( $this->get_option( 'enable_blacklist', 'no' ) == 'no' ) {
				return false;
			}

			global $wpdb;

			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}

			if ( ! $ip ) {
				$ip = $_SERVER['REMOTE_ADDR'];
			}

			$res = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->yith_wc_stripe_blacklist} WHERE ( user_id = %d OR ip = %s ) AND unbanned = %d", $user_id, $ip, 1 ) );

			return $res > 0 ? true : false;
		}

		/**
		 * Register the block on blacklist
		 *
		 * @since 1.1.3
		 *
		 * @param array $args
		 *
		 * @return bool
		 *
		 */
		public function add_block( $args = array() ) {
			extract( wp_parse_args( $args,
				array(
					'user_id' => get_current_user_id(),
					'ip' => $_SERVER['REMOTE_ADDR'],
					'order_id' => 0,
					'ua' => ! empty( $_SERVER['HTTP_USER_AGENT'] ) ? wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) : ''
				)
			) );

			/**
			 * @var $user_id int
			 * @var $ip string
			 * @var $order_id int
			 * @var $ua string
			 */

			if ( $this->get_option( 'enable_blacklist', 'no' ) == 'no' || $this->have_purchased( $user_id ) || $this->is_blocked( $user_id, $ip ) || $this->is_unbanned( $user_id, $ip ) ) {
				return false;
			}

			global $wpdb;

			// add the user and the ip
			$wpdb->insert( $wpdb->yith_wc_stripe_blacklist, array(
				'user_id' => $user_id,
				'ip' => $ip,
				'order_id' => $order_id,
				'ua' => $ua,
				'ban_date' => current_time( 'mysql' ),
				'ban_date_gmt' => current_time( 'mysql', 1 )
			) );

			return true;
		}

		/* === TOKENS MANAGEMENT === */

		/**
         * Add payment method
         *
		 * @return array|bool
		 */
		public function add_payment_method() {
			try {

				// Initializate SDK and set private key
				$this->init_stripe_sdk();

				if ( empty( $this->token ) ) {
					$error_msg = __( 'Please make sure that your card details have been entered correctly and that your browser supports JavaScript.', 'yith-woocommerce-stripe' );

					if ( 'test' == $this->env ) {
						$error_msg .= ' ' . __( 'Developers: Please make sure that you\'re including jQuery and that there are no JavaScript errors in the page.', 'yith-woocommerce-stripe' );
					}

					$this->log( 'Wrong token ' . $this->token . ': ' . print_r( $_POST, true ) );

					throw new Error\Api( $error_msg );
				}

				$token = $this->save_token();

				return apply_filters( 'yith_wcstripe_add_payment_method_result', array(
					'result'   => 'success',
					'redirect' => wc_get_endpoint_url( 'payment-methods' ),
				), $token );

			} catch ( Error\Base $e ) {
			    $this->error_handling( $e );

				return false;
			}
		}

		/**
		 * Save the token on db
		 *
		 * @param \Stripe\Card $card
		 *
		 * @throws Error\Api
		 */
		public function save_token( $card = null ) {

			if( ! is_user_logged_in() || ! $this->save_cards || ( is_checkout() && $this->save_cards_mode == 'prompt' && ! isset( $_POST['wc-yith-stripe-new-payment-method'] ) ) ){
				return false;
			}

			// Initializate SDK and set private key
			$this->init_stripe_sdk();

			$user           = wp_get_current_user();
			$local_customer = YITH_WCStripe()->get_customer()->get_usermeta_info( $user->ID );
			$customer       = ! empty( $local_customer['id'] ) ? $this->api->get_customer( $local_customer['id'] ) : false;

			// add card
			if ( empty( $card ) ) {

				// get existing
				if ( $customer ) {
					$card = $this->api->create_card( $local_customer['id'], $this->token );
					$customer->sources->data[] = $card;
				} // create new one
				else {
					$params = array(
						'source'      => $this->token,
						'email'       => $user->billing_email,
						'description' => substr( $user->user_login . ' (#' . $user->ID . ' - ' . $user->user_email . ') ' . $user->billing_first_name . ' ' . $user->billing_last_name, 0, 350 ),
						'metadata'    => apply_filters( 'yith_wcstripe_metadata', array(
							'user_id'  => $user->ID,
							'instance' => $this->instance
						), 'create_customer' )
					);

					$customer = $this->api->create_customer( $params );
					foreach ( $customer->sources->data as $card ) {
						if ( $card->id == $customer->default_source ) {
							break;
						}
					}
				}
			}
			
			if ( empty( $card ) ) {
				throw new Error\Api( __( "Can't add credit card info.", 'yith-woocommerce-stripe' ) );
			}

			$already_registered = false;
			$already_registered_tokens = WC_Payment_Tokens::get_customer_tokens( $user->ID, $this->id );
			$registered_token = false;

			if( ! empty( $already_registered_tokens ) ){
				foreach( $already_registered_tokens as $registered_token ){
					/**
					 * @var $registered_token \WC_Payment_Token
					 */
					$registered_fingerprint = $registered_token->get_meta( 'fingerprint', true );

					if( $registered_fingerprint == $card->fingerprint || $registered_token->get_token() == $card->id ){
						$already_registered = true;
						break;
					}
				}
			}

			if( ! $already_registered ) {
				// save card
				$token = new WC_Payment_Token_CC();
				$token->set_token( $card->id );
				$token->set_gateway_id( $this->id );
				$token->set_user_id( $user->ID );

				$token->set_card_type( strtolower( $card->brand ) );
				$token->set_last4( $card->last4 );
				$token->set_expiry_month( ( 1 === strlen( $card->exp_month ) ? '0' . $card->exp_month : $card->exp_month ) );
				$token->set_expiry_year( $card->exp_year );
				$token->set_default( true );
				$token->add_meta_data( 'fingerprint', $card->fingerprint );

				if ( ! $token->save() ) {
					throw new Error\Api( __( 'Credit card info not valid', 'yith-woocommerce-stripe' ) );
				}

				// backard compatibility
                if( $customer ) {
	                YITH_WCStripe()->get_customer()->update_usermeta_info( $customer->metadata->user_id, array(
		                'id'             => $customer->id,
		                'cards'          => $customer->sources->data,
		                'default_source' => $customer->default_source
	                ) );
                }

				do_action( 'yith_wcstripe_created_card', $card->id, $customer );

				return $token;
			}
			else{
				return $registered_token;
			}
		}

		/**
         * Set one of the currently registered tokens as default
         *
         * @param $card_id string Card token
         * @return bool Operation status
		 */
		public function set_default_token( $card_id ) {
			if( ! is_user_logged_in() ){
				return false;
			}

			$user = wp_get_current_user();
			$already_registered_tokens = WC_Payment_Tokens::get_customer_tokens( $user->ID, $this->id );

			if( ! empty( $already_registered_tokens ) ){
				foreach( $already_registered_tokens as $registered_token ){
					/**
					 * @var $registered_token \WC_Payment_Token
					 */
					if( $registered_token->get_token() == $card_id ){
					    $registered_token->set_default( true );
					    $registered_token->save();
					    return true;
                    }
				}
			}

			return false;
		}

		/**
		 * Sync tokens on website from stripe $customer object
		 *
		 * @param int|WP_User $user
		 * @param \Stripe\Customer $customer
		 */
		public function sync_tokens( $user, $customer ) {
			if ( ! is_a( $user, 'WP_User' ) ) {
				$user = get_user_by( 'id', $user );
			}

			if( ! $this->save_cards || ( $this->save_cards_mode == 'prompt' ) ){
				return;
			}

			$sources = $customer->sources->data;
			$tokens = WC_Payment_Tokens::get_customer_tokens( $user->ID, $this->id );
			$to_add = $sources;
			
			/** @var WC_Payment_Token_CC $token */
			foreach ( $tokens as $token_id => $token ) {
				$found = false;

				foreach ( $sources as $k => $source ) {
					if ( $token->get_token() === $source->id ) {
						$found = true;
						break;
					}
				}

				// edit token if found if between stripe ones and if something is changed
				if ( $found ) {
					// remove the source from global array, to add the remaining on website
					unset( $to_add[ $k ] );

					$changed = false;

					if ( $token->get_last4() != $source->last4 ) {
						$token->set_last4( $source->last4 );
						$changed = true;
					}

					if ( $token->get_expiry_month() != ( 1 === strlen( $source->exp_month ) ? '0' . $source->exp_month : $source->exp_month ) ) {
						$token->set_expiry_month( ( 1 === strlen( $source->exp_month ) ? '0' . $source->exp_month : $source->exp_month ) );
						$changed = true;
					}

					if ( $token->get_expiry_year() != $source->exp_year ) {
						$token->set_expiry_year( $source->exp_year );
						$changed = true;
					}

					if ( $token->get_meta( 'fingerprint' ) != $source->fingerprint ) {
						$token->update_meta_data( 'fingerprint', $source->fingerprint );
						$changed = true;
					}

					if ( $token->get_token() === $customer->default_source && ! $token->is_default() ) {
						$token->set_default( true );
						$changed = true;
					}

					if ( $token->get_token() !== $customer->default_source && $token->is_default() ) {
						$token->set_default( false );
						$changed = true;
					}

					// save it if changed
					if ( $changed ) {
						$token->save();
					}
				}

				// if not found any token between stripe, remove token
				else {
					$token->delete();
				}
			}

			// add remaining sources not added as token on website yet
			foreach( $to_add as $source ) {
				$token = new WC_Payment_Token_CC();
				$token->set_token( $source->id );
				$token->set_gateway_id( $this->id );
				$token->set_user_id( $user->ID );

				$token->set_card_type( strtolower( $source->brand ) );
				$token->set_last4( $source->last4 );
				$token->set_expiry_month( ( 1 === strlen( $source->exp_month ) ? '0' . $source->exp_month : $source->exp_month ) );
				$token->set_expiry_year( $source->exp_year );
				$token->add_meta_data( 'fingerprint', $source->fingerprint );

				$token->save();
			}

			// back-compatibility
			YITH_WCStripe()->get_customer()->update_usermeta_info( $customer->metadata->user_id, array(
				'id'             => $customer->id,
				'cards'          => $customer->sources->data,
				'default_source' => $customer->default_source
			) );
		}

		/**
		 * Change display name on checkout page for token
		 *
		 * @param $display
		 * @param $token WC_Payment_Token_CC
		 *
		 * @return string
		 */
		public function token_display_name( $display, $token ) {
			$icon = WC_HTTPS::force_https_url( WC()->plugin_url() . '/assets/images/icons/credit-cards/' . $token->get_card_type() . '.png' );
			$display = '<img src="' . $icon . '" alt="' . $token->get_card_type() . '" style="width:40px;"/>';
			$display .= sprintf(
				'<span class="card-type">%s</span> <span class="card-number"><em>&bull;&bull;&bull;&bull;</em>%s</span> <span class="card-expire">(%s/%s)</span>',
				$token->get_card_type(),
				$token->get_last4(),
				$token->get_expiry_month(),
				$token->get_expiry_year()
			);

			return $display;
		}

		/* === HELPER METHODS === */

		/**
		 * Get customer ID of Stripe account from user ID
		 *
		 * @param $user_id
		 *
		 * @return integer
		 * @since 1.0.0
		 */
		public function get_customer_id( $user_id ) {
			$customer = YITH_WCStripe()->get_customer()->get_usermeta_info( $user_id );

			if ( ! isset( $customer['id'] ) ) {
				return 0;
			}

			return $customer['id'];
		}

		/**
		 * Get customer of Stripe account or create a new one if not exists
		 *
		 * @param $order WC_Order
		 * @return \Stripe\Customer
		 * @since 1.0.0
		 */
		public function get_customer( $order ) {
			if ( is_int( $order ) ) {
				$order = wc_get_order( $order );
			}

			$current_order_id = ! empty ( $this->_current_order ) ? yit_get_order_id( $this->_current_order ) : false;
			$order_id = yit_get_order_id( $order );

			if ( $current_order_id == $order_id && ! empty( $this->_current_customer ) ) {
				return $this->_current_customer;
			}

			$user_id = is_user_logged_in() ? $order->get_user_id() :false;
			$customer = is_user_logged_in() ? YITH_WCStripe()->get_customer()->get_usermeta_info( $user_id ) : false;

			// get existing
			if ( $customer ) {
				$customer = $this->api->get_customer( $customer['id'] );
				$selected_card = $this->get_credit_card_num();

				if ( 'new' == $selected_card ) {
					$user = $order->get_user();

					$card = $this->api->create_card( $customer, $this->token );
					$this->token = $card->id;

					try {
						$customer = $this->api->update_customer( $customer, array(
							'email'       => yit_get_prop( $order, 'billing_email' ),
							'description' => $user->user_login . ' (#' . $order->get_user_id() . ' - ' . $user->user_email . ') ' . yit_get_prop( $order, 'billing_first_name' ) . ' ' . yit_get_prop( $order, 'billing_last_name' )
						) );

					} catch( Exception $e ) {
						YITH_WCStripe()->customer->delete_usermeta_info( $user_id );
						$this->get_customer( $order );
					}

					// update user meta
					YITH_WCStripe()->get_customer()->update_usermeta_info( $user_id, array(
						'id'             => $customer->id,
						'cards'          => $customer->sources->data,
						'default_source' => $customer->default_source
					) );

					do_action( 'yith_wcstripe_created_card', $card->id, $customer );
				}

				if ( $current_order_id == $order_id ) {
					$this->_current_customer = $customer;
				}

				return $customer;

			}

			// create new one
			else {

				$user = is_user_logged_in() ? $order->get_user() : false;

				if( is_user_logged_in() ){
					$description = $user->user_login . ' (#' . $order->get_user_id() . ' - ' . $user->user_email . ') ' . yit_get_prop( $order, 'billing_first_name' ) . ' ' . yit_get_prop( $order, 'billing_last_name' );
				}
				else{
					$description = yit_get_prop( $order, 'billing_email' ) . ' (' . __( 'Guest', 'yith-woocommerce-stripe' ) . ' - ' . yit_get_prop( $order, 'billing_email' ) . ') ' . yit_get_prop( $order, 'billing_first_name' ) . ' ' . yit_get_prop( $order, 'billing_last_name' );
				}

				$params = array(
					'source' => $this->token,
					'email' => yit_get_prop( $order, 'billing_email' ),
					'description' => substr( $description, 0, 350 ),
					'metadata' => apply_filters( 'yith_wcstripe_metadata', array(
						'user_id' => is_user_logged_in() ? $order->get_user_id() : false,
						'instance' => $this->instance
					), 'create_customer' )
				);

				$customer = $this->api->create_customer( $params );
				$this->token = $customer->default_source;

				// update user meta
				if( is_user_logged_in() ) {
					YITH_WCStripe()->get_customer()->update_usermeta_info( $user_id, array(
						'id'             => $customer->id,
						'cards'          => $customer->sources->data,
						'default_source' => $customer->default_source
					) );
				}

				if ( $current_order_id == $order_id ) {
					$this->_current_customer = $customer;
				}

				return $customer;

			}

		}

		/**
		 * Say if the user in parameter have already purchased properly previously
		 *
		 * @since 1.1.3
		 *
		 * @param bool $user_id
		 *
		 * @return bool
		 */
		public function have_purchased( $user_id = false ) {
			global $wpdb;

			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}

			$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_status IN ( %s, %s ) AND post_author = %d", 'wc-completed', 'wc-processing', $user_id ) );
			return $count > 0 ? true : false;
		}

		/**
		 * Log to txt file
		 *
		 * @param $message
		 * @since 1.0.0
		 */
		public function log( $message ) {
			if ( isset( $this->log, $this->debug ) && $this->debug ) {
				$this->log->add( 'stripe', $message );
			}
		}

		/**
		 * Standard error handling for exceptions thrown by API class
		 *
		 * @param $e Stripe\Error\Base
		 * @return string Final error message
		 */
		public function error_handling( $e, $args = array() ) {
			$message = parent::error_handling( $e, $args );
			$body = $e->getJsonBody();

			// register error within log file
			$this->log( 'Stripe Error: ' . $e->getHttpStatus() . ' - ' . print_r( $e->getJsonBody(), true ) );

			// add block if there is an error on card
			if ( $body && isset( $args['order_id'] ) ) {
				$err = $body['error'];

				if ( isset( $err['type'] ) && $err['type'] == 'card_error' ) {
					$this->add_block( "order_id={$args['order_id']}" );
					WC()->session->refresh_totals = true;
				}
			}

			return $message;
		}

		/**
		 * Give ability to add options to $this->form_fields
		 *
		 * @param $field
		 * @param string $where (first, last, after, before) (optional, default: last)
		 * @param string $who (optional, default: empty string)
		 *
		 * @since  2.0.0
		 */
		private function add_form_field( $field, $where = 'last', $who = '' ) {
			switch ( $where ) {

				case 'first':
					$this->form_fields = array_merge( $field, $this->form_fields );
					break;

				case 'last':
					$this->form_fields = array_merge( $this->form_fields, $field );
					break;

				case 'before':
				case 'after' :
					if ( array_key_exists( $who, $this->form_fields ) ) {

						$who_position = array_search( $who, array_keys( $this->form_fields ) );

						if ( $where == 'after' ) {
							$who_position = ( $who_position + 1 );
						}

						$before = array_slice( $this->form_fields, 0, $who_position );
						$after  = array_slice( $this->form_fields, $who_position );

						$this->form_fields = array_merge( $before, $field, $after );
					}
					break;
			}
		}
	}
}