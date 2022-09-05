<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\PaymentGateways;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use WC_AJAX;
use WC_Order;
use Exception;
use WeDevs\Dokan\Exceptions\DokanException;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;
use WeDevs\DokanPro\Modules\StripeExpress\Api\SetupIntent;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Settings;
use WeDevs\DokanPro\Modules\StripeExpress\Processors\Order;
use WeDevs\DokanPro\Modules\StripeExpress\Controllers\Token;
use WeDevs\DokanPro\Modules\StripeExpress\Support\OrderMeta;
use WeDevs\DokanPro\Modules\StripeExpress\Api\PaymentMethod;
use WeDevs\DokanPro\Modules\StripeExpress\Api\PaymentIntent;
use WeDevs\DokanPro\Modules\StripeExpress\Processors\Payment;
use WeDevs\DokanPro\Modules\StripeExpress\Processors\Customer;
use WeDevs\DokanPro\Modules\StripeExpress\Utilities\Abstracts\PaymentGateway;

/**
 * Gateway handler class.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\PaymentGateways
 */
class Stripe extends PaymentGateway {

    /**
     * ID for the gateway
     *
     * @since 3.6.1
     *
     * @param string
     */
    const ID = 'dokan_stripe_express';

    /**
     * Stripe intents that are treated as successfully created.
     *
     * @since 3.6.1
     *
     * @param array
     */
    const SUCCESSFUL_INTENT_STATUS = [ 'succeeded', 'requires_capture', 'processing' ];

    /**
     * Class constructor.
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function __construct() {
        // Load necessary fields info
        $this->init_fields();

        // Load the settings
        $this->init_form_fields();
        $this->init_settings();

        // Load necessary hooks
        $this->hooks();
    }

    /**
     * Initiates all required info for payment gateway
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function init_fields() {
        $this->has_fields           = true;
        $this->id                   = self::ID;
        $this->method_title         = __( 'Dokan Stripe Express', 'dokan' );
        $this->method_description   = __( 'Accept debit and credit cards in different currencies, methods such as iDEAL, and wallets like Google Pay or Apple Pay with one-touch checkout.', 'dokan' );
        $this->payment_methods      = Helper::get_available_method_instances();
        $this->order_button_text    = Helper::get_order_button_text();
        $this->title                = $this->get_option( 'title' );
        $this->testmode             = 'yes' === $this->get_option( 'testmode' );
        $this->secret_key           = $this->testmode ? $this->get_option( 'test_secret_key' ) : $this->get_option( 'secret_key' );
        $this->publishable_key      = $this->testmode ? $this->get_option( 'test_publishable_key' ) : $this->get_option( 'publishable_key' );
        $this->debug                = $this->get_option( 'debug' );
        $this->description          = $this->get_option( 'description' );
        $this->capture              = 'yes' === $this->get_option( 'capture', 'no' );
        $this->payment_request      = 'yes' === $this->get_option( 'payment_request', 'yes' );
        $this->enabled              = $this->get_option( 'enabled' );
        $this->saved_cards          = 'yes' === $this->get_option( 'saved_cards' );
        $this->icon                 = apply_filters( 'dokan_stripe_express_icon', '' );
        $this->statement_descriptor = $this->get_option( 'statement_descriptor' );
        $this->supports             = [
            'products',
            'refunds',
            'tokenization',
            'add_payment_method',
        ];

        $active_payment_methods = $this->get_enabled_payment_methods_at_checkout();
        if ( count( $active_payment_methods ) === 1 ) {
            $this->title = $this->payment_methods[ $active_payment_methods[0] ]->get_title();
        }

        if ( empty( $this->title ) ) {
            $this->title = __( 'Express Payment Methods', 'dokan' );
        }

        // Show the count of enabled payment methods on settings page.
        if ( isset( $_GET['page'] ) && 'wc-settings' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $total_enabled_payment_methods = count( Settings::get_enabled_payment_methods() );
            $this->title                   = $total_enabled_payment_methods
                /* translators: Count of enabled payment methods. */
                ? sprintf( _n( '%d payment method', '%d payment methods', $total_enabled_payment_methods, 'dokan' ), $total_enabled_payment_methods )
                : $this->method_title;
        }
    }

    /**
     * Initiates all necessary hooks
     *
     * @since 3.6.1
     *
     * @uses add_action() To add action hooks
     * @uses add_filter() To add filter hooks
     *
     * @return void
     */
    private function hooks() {
        add_action( "woocommerce_update_options_payment_gateways_{$this->id}", [ $this, 'process_admin_options' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'payment_scripts' ] );
        add_filter( 'woocommerce_payment_successful_result', [ $this, 'modify_successful_payment_result' ], 99999, 2 );
    }

    /**
     * Enqueues payment scripts.
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function payment_scripts() {
        if ( ! is_cart() && ! is_checkout() && ! isset( $_GET['pay_for_order'] ) && ! is_add_payment_method_page() ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return;
        }

        wp_localize_script(
            'dokan-stripe-express-checkout',
            'dokanStripeExpress',
            $this->localized_params()
        );

        wp_enqueue_script( 'dokan-stripe-express-checkout' );
        wp_enqueue_style( 'dokan-stripe-express-checkout' );
    }

    /**
     * Generates localized javascript parameters
     *
     * @since 3.6.1
     *
     * @return array
     */
    private function localized_params() {
        $stripe_params = [
            'title'                => $this->title,
            'key'                  => $this->publishable_key,
            'locale'               => Helper::convert_locale( get_locale() ),
            'billingFields'        => Helper::get_enabled_billing_fields(),
            'isCheckout'           => is_checkout() && empty( $_GET['pay_for_order'] ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            'errors'               => Helper::get_error_message(),
            'messages'             => Helper::get_payment_message(),
            'ajaxurl'              => WC_AJAX::get_endpoint( '%%endpoint%%' ),
            'nonce'                => wp_create_nonce( 'dokan_stripe_express_checkout' ),
            'paymentMethodsConfig' => $this->get_enabled_payment_method_config(),
            'addPaymentReturnURL'  => wc_get_account_endpoint_url( 'payment-methods' ),
            'accountDescriptor'    => $this->statement_descriptor,
            'genericErrorMessage'  => __( 'There was a problem processing the payment. Please check your email inbox and refresh the page to try again.', 'dokan' ),
            'assets'               => [
                'applePayLogo' => DOKAN_STRIPE_EXPRESS_ASSETS . 'images/apple-pay.svg',
            ],
            'i18n'                 => [
                'confirmApplePayment' => __( 'Proceed to payment via Apple Pay?', 'dokan' ),
                'proceed'             => __( 'Yes, Proceed', 'dokan' ),
                'decline'             => __( 'Decline', 'dokan' ),
                'emptyFields'         => __( 'Please fill all the fields', 'dokan' ),
                'paymentDismissed'    => __( 'Payment process dismissed', 'dokan' ),
                'tryAgain'            => __( 'An error was encountered when preparing the payment form. Please try again later.', 'dokan' ),
                'incompleteInfo'      => __( 'Your payment information is incomplete.', 'dokan' ),
            ],
            'sepaElementsOptions'  => apply_filters(
                'dokan_stripe_express_sepa_elements_options',
                [
                    'supportedCountries' => [ 'SEPA' ],
                    'placeholderCountry' => WC()->countries->get_base_country(),
                ]
            ),
        ];

        $order_id = null;

        if ( is_wc_endpoint_url( 'order-pay' ) ) {
            if ( $this->is_subscriptions_enabled() && $this->is_changing_payment_method_for_subscription() ) {
                $stripe_params['isChangingPayment']   = true;
                $stripe_params['addPaymentReturnURL'] = esc_url_raw( home_url( add_query_arg( [] ) ) );

                if ( Helper::is_setup_intent_success_creation_redirection() && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( wc_clean( wp_unslash( $_GET['_wpnonce'] ) ) ) ) {
                    $setup_intent_id                 = isset( $_GET['setup_intent'] ) ? wc_clean( wp_unslash( $_GET['setup_intent'] ) ) : '';
                    $token                           = $this->create_token_from_setup_intent( $setup_intent_id, wp_get_current_user() );
                    $stripe_params['newTokenFormId'] = '#wc-' . $token->get_gateway_id() . '-payment-token-' . $token->get_id();
                }
                return $stripe_params;
            }
            $order_id = absint( get_query_var( 'order-pay' ) );
            $order    = wc_get_order( $order_id );

            if ( is_a( $order, 'WC_Order' ) ) {
                $stripe_params['orderReturnURL'] = esc_url_raw(
                    add_query_arg(
                        [
                            'order_id'          => $order_id,
                            'wc_payment_method' => Helper::get_gateway_id(),
                            '_wpnonce'          => wp_create_nonce( 'dokan_stripe_express_process_redirect_order' ),
                        ],
                        $this->get_return_url( $order )
                    )
                );
            }

            $stripe_params['orderId']    = $order_id;
            $stripe_params['isOrderPay'] = true;
        }

        $stripe_params['isPaymentNeeded'] = Helper::is_payment_needed( $order_id );

        return $stripe_params;
    }

    /**
     * Adds a token to current user from a setup intent id.
     *
     * @since 3.6.1
     *
     * @param string  $setup_intent_id ID of the setup intent.
     * @param WP_User $user            User to add token to.
     *
     * @return object
     */
    public function create_token_from_setup_intent( $setup_intent_id, $user ) {
        try {
            $setup_intent = SetupIntent::get( $setup_intent_id );
            if ( ! empty( $setup_intent->last_payment_error ) ) {
                throw new Exception( __( 'We\'re not able to add this payment method. Please try again later.', 'dokan' ) );
            }

            $payment_method_id     = $setup_intent->payment_method;
            $payment_method_object = PaymentMethod::get( $payment_method_id );
            $payment_method        = $this->payment_methods[ $payment_method_object->type ];
            $customer              = Customer::set( get_current_user_id() );

            return $payment_method->create_payment_token_for_user( $user->ID, $payment_method_object );
        } catch ( Exception $e ) {
            wc_add_notice( $e->getMessage(), 'error', [ 'icon' => 'error' ] );
            Helper::log( 'Error when adding payment method: ' . $e->getMessage() );

            return [
                'result' => 'error',
            ];
        }
    }

    /**
     * Returns the list of enabled payment method types that will function with the current checkout.
     *
     * @since 3.6.1
     *
     * @param int|null $order_id
     *
     * @return string[]
     */
    public function get_enabled_payment_methods_at_checkout( $order_id = null ) {
        $available_method_ids = [];

        foreach ( Settings::get_enabled_payment_methods() as $payment_method_id ) {
            if ( ! isset( $this->payment_methods[ $payment_method_id ] ) ) {
                continue;
            }

            $method = $this->payment_methods[ $payment_method_id ];
            if ( ! $method->is_enabled_at_checkout( $order_id ) ) {
                continue;
            }

            if ( $this->capture && $method->requires_automatic_capture() ) {
                continue;
            }

            $available_method_ids[] = $payment_method_id;
        }

        return $available_method_ids;
    }

    /**
     * Gets payment method settings to pass to client scripts
     *
     * @return array
     */
    public function get_enabled_payment_method_config() {
        $settings                 = [];
        $enabled_payment_methods  = $this->get_enabled_payment_methods_at_checkout();
        $payment_method_instances = Helper::get_available_method_instances();

        foreach ( $enabled_payment_methods as $payment_method ) {
            $settings[ $payment_method ] = [
                'isReusable' => $payment_method_instances[ $payment_method ]->is_reusable(),
            ];
        }

        return $settings;
    }

    /**
     * Checks if the gateways is available for use.
     *
     * @since 3.6.1
     *
     * @return boolean
     */
    public function is_available() {
        if ( ! parent::is_available() ) {
            return false;
        }

        if ( is_add_payment_method_page() && count( Helper::get_reusable_payment_methods() ) === 0 ) {
            return false;
        }

        /*
         * This payment method can't be used if a Vendor is not connected
         * to Stripe express. So we need to traverse all the cart items
         * to check if any vendor is not connected.
         */
        return Helper::validate_cart_items();
    }

    /**
     * Initiates form fields for admin settings
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function init_form_fields() {
        $this->form_fields = require DOKAN_STRIPE_EXPRESS_TEMPLATE_PATH . 'admin/gateway-settings.php';
    }

    /**
     * Init settings for gateways.
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function init_settings() {
        parent::init_settings();
        $this->enabled = ! empty( $this->settings['enabled'] ) && 'yes' === $this->settings['enabled'] ? 'yes' : 'no';
    }

    /**
     * Processes the admin options.
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function process_admin_options() {
        parent::process_admin_options();

        /**
         * @uses \WeDevs\DokanPro\Modules\StripeExpress\Controllers\Webhook $webhook
         */
        $webhook = dokan_pro()->module->stripe_express->webhook;

        if ( 'yes' === $this->enabled ) {
            //if gateway is enabled, automatically create webhook for this site
            $webhook->register();
        } else {
            //if gateway is disabled, delete created webhook for this site
            $webhook->deregister();
        }
    }

    /**
     * Renders the input fields needed
     * to get the user's payment information
     * on the checkout page.
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function payment_fields() {
        try {
            $display_tokenization = $this->supports( 'tokenization' ) && is_checkout();

            $this->maybe_show_description();

            if ( $this->testmode ) {
                $this->testmode_description();
            }

            if ( $display_tokenization ) {
                $this->tokenization_script();
                $this->saved_payment_methods();
            }

            $this->element_form();

            if ( $this->saved_cards && ! empty( Helper::get_reusable_payment_methods() ) && is_user_logged_in() ) {
                $force_save_payment = is_add_payment_method_page() ||
                (
                    $display_tokenization &&
                    ! apply_filters( 'dokan_stripe_express_display_save_payment_method_checkbox', $display_tokenization )
                );

                $this->save_payment_method_checkbox( $force_save_payment );
            }
        } catch ( \Exception $e ) {
            // Output the error message.
            Helper::log( 'Error: ' . $e->getMessage() );
            /* translators: 1) opening div tag, 2) closing div tag */
            printf( esc_html__( '%1$sAn error was encountered when preparing the payment form. Please try again later.%2$s', 'dokan' ), '<div>', '</div>' );
        }
    }

    /**
     * Process the payment for a given order.
     *
     * @since 3.6.1
     *
     * @param int  $order_id Reference.
     * @param bool $retry Should we retry on fail.
     * @param bool $force_save_source Force save the payment source.
     * @param mix  $previous_error Any error message from previous request.
     * @param bool $use_order_source Whether to use the source, which should already be attached to the order.
     *
     * @return array|null An array with result of payment and redirect URL, or nothing.
     */
    public function process_payment( $order_id, $retry = true, $force_save_source = false, $previous_error = false, $use_order_source = false ) {
        $this->validate_minimum_order_amount( $order_id );
        if ( Helper::is_using_saved_payment_method() ) {
            return $this->process_payment_with_saved_payment_method( $order_id );
        }

        $payment_intent_id     = isset( $_POST['payment_intent_id'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_intent_id'] ) ) : '';  // phpcs:ignore WordPress.Security.NonceVerification.Missing
        $order                 = wc_get_order( $order_id );
        $payment_needed        = Helper::is_payment_needed( $order_id );
        $save_payment_method   = $this->has_subscription( $order_id ) || ! empty( $_POST[ 'wc-' . self::ID . '-new-payment-method' ] );         // phpcs:ignore WordPress.Security.NonceVerification.Missing
        $selected_payment_type = ! empty( $_POST['dokan_stripe_express_payment_type'] ) ? sanitize_text_field( wp_unslash( $_POST['dokan_stripe_express_payment_type'] ) ) : '';          // phpcs:ignore WordPress.Security.NonceVerification.Missing

        if ( $payment_intent_id ) {
            if ( $payment_needed ) {
                $request = [
                    'currency'    => strtolower( $order->get_currency() ),
                    'amount'      => Helper::get_stripe_amount( $order->get_total(), $order->get_currency() ),
                    'description' => sprintf(
                        /* translators: 1) blog name 2) order number */
                        __( '%1$s - Order %2$s', 'dokan' ),
                        wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
                        $order->get_order_number()
                    ),
                ];

                // Get user/customer for order.
                $customer_id = Order::get_stripe_customer_id_from_order( $order );
                if ( ! empty( $customer_id ) ) {
                    $request['customer'] = $customer_id;
                } else {
                    $user     = Order::get_user_from_order( $order );
                    $customer = Customer::set( $user->ID );
                    $customer = $customer->update_or_create();
                    if ( is_wp_error( $customer ) ) {
                        throw new \Exception( $customer->get_error_message() );
                    }
                    $request['customer'] = $customer;
                }

                if ( ! empty( $selected_payment_type ) ) {
                    // Only update the payment_method_types if we have a reference to the payment type the customer selected.
                    $request['payment_method_types'] = [ $selected_payment_type ];
                    Payment::set_method_title( $order, $selected_payment_type );

                    if ( ! $this->payment_methods[ $selected_payment_type ]->is_allowed_on_country( $order->get_billing_country() ) ) {
                        throw new \Exception( __( 'This payment method is not available on the selected country', 'dokan' ) );
                    }
                }

                if ( $save_payment_method ) {
                    $request['setup_future_usage'] = 'off_session';
                }

                $order->update_status( 'pending', __( 'Awaiting payment.', 'dokan' ) );
                OrderMeta::update_payment_type( $order, $selected_payment_type );
                OrderMeta::save( $order );
                PaymentIntent::update( $payment_intent_id, $request );
            }
        } else {
            return parent::process_payment( $order_id, $retry, $force_save_source, $previous_error, $use_order_source );
        }

        return [
            'result'         => 'success',
            'payment_needed' => $payment_needed,
            'order_id'       => $order_id,
            'redirect_url'   => wp_sanitize_redirect(
                esc_url_raw(
                    add_query_arg(
                        [
                            'order_id'            => $order_id,
                            'wc_payment_method'   => self::ID,
                            '_wpnonce'            => wp_create_nonce( 'dokan_stripe_express_process_redirect_order' ),
                            'save_payment_method' => $save_payment_method ? 'yes' : 'no',
                        ],
                        $this->get_return_url( $order )
                    )
                )
            ),
        ];
    }

    /**
     * Process payment using saved payment method.
     * This follows Stripe::process_payment,
     * but uses Payment Methods instead of Sources.
     *
     * @since 3.6.1
     *
     * @param int $order_id   The order ID being processed.
     * @param bool $can_retry Should we retry on fail.
     *
     * @return mixed
     */
    public function process_payment_with_saved_payment_method( $order_id, $can_retry = true ) {
        try {
            $order                   = wc_get_order( $order_id );
            $token                   = Token::get_from_request( $_POST );          // phpcs:ignore WordPress.Security.NonceVerification.Missing
            $payment_method          = PaymentMethod::get( $token->get_token() );
            $prepared_payment_method = PaymentMethod::prepare( $payment_method );

            Helper::maybe_disallow_prepaid_card( $payment_method );
            Payment::save_payment_method_data( $order, $prepared_payment_method );

            Helper::log( "Processing payment with saved payment method for order $order_id for the amount of {$order->get_total()}", 'Order', 'info' );

            // If we are retrying request, maybe intent has been saved to order.
            $intent                  = Payment::get_intent( $order );
            $enabled_payment_methods = Helper::get_reusable_payment_methods( $this->get_enabled_payment_methods_at_checkout() );
            $payment_needed          = Helper::is_payment_needed( $order_id );

            if ( $payment_needed ) {
                // This will throw exception if not valid.
                $this->validate_minimum_order_amount( $order );

                $request = [
                    'payment_method'       => $payment_method->id,
                    'payment_method_types' => array_values( $enabled_payment_methods ),
                    'customer'             => $payment_method->customer,
                ];

                if ( ! $intent ) {
                    $request['capture_method'] = Settings::is_manual_capture_enabled() ? 'manual' : 'automatic';
                    $request['confirm']        = 'true';
                }

                $intent = Payment::create_intent( $order, $request );
            } else {
                $request = [
                    'payment_method'       => $payment_method->id,
                    'payment_method_types' => array_values( $enabled_payment_methods ),
                    'customer'             => $payment_method->customer,
                ];
                if ( ! $intent ) {
                    $request['confirm'] = 'true';
                    // SEPA setup intents require mandate data.
                    if ( in_array( Helper::get_sepa_payment_method_type(), array_values( $enabled_payment_methods ), true ) ) {
                        $request['mandate_data'] = [
                            'customer_acceptance' => [
                                'type'   => 'online',
                                'online' => [
                                    'ip_address' => dokan_get_client_ip(),
                                    'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '', // @codingStandardsIgnoreLine
                                ],
                            ],
                        ];
                    }
                }

                $intent = Payment::create_intent( $order, $request, true );
            }

            if ( ! empty( $intent->error ) ) {
                $this->maybe_remove_non_existent_customer( $intent->error, $order );

                // We want to retry (apparently).
                if ( Helper::is_retryable_error( $intent->error ) ) {
                    return $this->retry_after_error( $intent, $order, $can_retry );
                }

                $this->throw_error_message( $intent, $order );
            }

            OrderMeta::update_payment_type( $order, $payment_method->type );
            OrderMeta::save( $order );

            if ( 'requires_action' === $intent->status || 'requires_confirmation' === $intent->status ) {
                if (
                    isset( $intent->next_action->type ) &&
                    'redirect_to_url' === $intent->next_action->type &&
                    ! empty( $intent->next_action->redirect_to_url->url )
                ) {
                    return [
                        'result'   => 'success',
                        'redirect' => $intent->next_action->redirect_to_url->url,
                    ];
                } else {
                    return [
                        'result'   => 'success',
                        // Include a new nonce for update_order_status to ensure the update order
                        // status call works when a guest user creates an account during checkout.
                        'redirect' => sprintf(
                            '#dokan-stripe-express-confirm-%s:%s:%s:%s',
                            $payment_needed ? 'pi' : 'si',
                            $order_id,
                            $intent->client_secret,
                            wp_create_nonce( 'dokan_stripe_express_update_order_status' )
                        ),
                    ];
                }
            }

            list( $payment_method_type, $payment_method_details ) = Payment::get_method_data_from_intent( $intent );

            if ( $payment_needed ) {
                // Use the last charge within the intent to proceed.
                Payment::process_response( end( $intent->charges->data ), $order );
            } else {
                $order->payment_complete();
                do_action( 'dokan_stripe_express_payment_completed', $order, $intent );
            }

            Payment::set_method_title( $order, $payment_method_type );

            // Remove cart.
            if ( isset( WC()->cart ) ) {
                WC()->cart->empty_cart();
            }

            // Return thank you page redirect.
            return [
                'result'   => 'success',
                'redirect' => $this->get_return_url( $order ),
            ];
        } catch ( DokanException $e ) {
            wc_add_notice( $e->get_message(), 'error' );
            Helper::log( 'Error: ' . $e->getMessage() );

            do_action( 'dokan_stripe_express_process_payment_error', $e, $order );

            /* translators: error message */
            $order->update_status( 'failed' );

            return [
                'result'   => 'fail',
                'redirect' => '',
            ];
        }
    }

    /**
     * Retries the payment process once an error occured.
     *
     * @param object   $intent            The Payment Intent response from the Stripe API.
     * @param WC_Order $order             An order that is being paid for.
     * @param bool     $retry             A flag that indicates whether another retry should be attempted.
     * @param bool     $force_save_source Force save the payment source.
     * @param mixed    $previous_error    Any error message from previous request.
     * @param bool     $use_order_source  Whether to use the source, which should already be attached to the order.
     *
     * @return array|void
     * @throws DokanException If the payment is not accepted.
     */
    public function retry_after_error( $intent, $order, $retry, $force_save_source = false, $previous_error = false, $use_order_source = false ) {
        if ( ! $retry ) {
            $localized_message = __( 'Sorry, we are unable to process your payment at this time. Please retry later.', 'dokan' );
            $order->add_order_note( $localized_message );
            throw new DokanException( print_r( $intent, true ), $localized_message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.
        }

        // Don't do anymore retries after this.
        if ( 5 <= $this->retry_interval ) {
            return $this->process_payment_with_saved_payment_method( $order->get_id(), false );
        }

        sleep( $this->retry_interval );
        $this->retry_interval++;

        return $this->process_payment_with_saved_payment_method( $order->get_id(), true );
    }

    /**
     * Processes intent status.
     *
     * @since 3.6.1
     *
     * @param object   $intent
     * @param object   $prepared_source
     * @param WC_Order $order
     *
     * @return void
     */
    public function process_intent_status( $intent, $prepared_source, $order ) {
        if ( 'requires_confirmation' === $intent->status ) {
            $intent = Payment::confirm_intent( $intent, $prepared_source );
        }

        if ( 'requires_action' === $intent->status ) {
            if ( is_wc_endpoint_url( 'order-pay' ) ) {
                $redirect_url = add_query_arg( 'dokan_stripe_express_confirmation', 1, $order->get_checkout_payment_url( false ) );

                return [
                    'result'   => 'success',
                    'redirect' => $redirect_url,
                ];
            } else {
                /**
                 * This URL contains only a hash, which will be sent to `checkout.js` where it will be set like this:
                 * `window.location = result.redirect`
                 * Once this redirect is sent to JS, the `onHashChange` function will execute `handleCardPayment`.
                 */
                return [
                    'result'                => 'success',
                    'redirect'              => $this->get_return_url( $order ),
                    'payment_intent_secret' => $intent->client_secret,
                ];
            }
        }

        if ( 'succeeded' === $intent->status ) {
            $order->payment_complete();
            do_action( 'dokan_stripe_express_payment_completed', $order, $intent );

            return [
                'result'   => 'success',
                'redirect' => $this->get_return_url( $order ),
            ];
        }

        return [
            'result'   => 'fail',
            'redirect' => '',
        ];
    }

    /**
     * Checks whether an order is refundable.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return boolean
     */
    public function can_refund_order( $order ) {
        // Check if the default refund method is enabled.
        if ( ! parent::can_refund_order( $order ) ) {
            return false;
        }

        // Check whether order is processed or completed
        if ( ! $order->has_status( [ 'processing', 'completed' ] ) ) {
            return false;
        }

        /**
         * We will not allow refund from the parent order.
         * The refund should always be given from the
         * sub orders if exists.
         * If it is a parent order, the refund button for
         * Stripe Express will not be shown.
         */
        if ( $order->get_meta( 'has_sub_order' ) ) {
            return false;
        }

        /*
         * We need to check if the payment method
         * used for this order supports refunds via Stripe.
         * To get the payment method, we need to get the
         * parent order if exists as the payment method
         * meta is stored on the parent order.
         */
        if ( $order->get_parent_id() ) {
            $order = wc_get_order( $order->get_parent_id() );
        }

        // Check if the payment method can refund via Stripe
        $payment_method = OrderMeta::get_payment_type( $order );
        if ( ! $this->payment_methods[ $payment_method ]->can_refund_via_stripe() ) {
            return false;
        }

        return true;
    }

    /**
     * Set formatted readable payment method title for order,
     * using payment method details from accompanying charge.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order WC Order being processed.
     * @param string   $payment_method_type Stripe payment method key.
     *
     * @return void
     */
    public function set_payment_method_title_for_order( $order, $payment_method_type ) {
        if ( ! isset( $this->payment_methods[ $payment_method_type ] ) ) {
            return;
        }

        $payment_method_title = $this->payment_methods[ $payment_method_type ]->get_label();

        $order->set_payment_method( self::ID );
        $order->set_payment_method_title( $payment_method_title );
        $order->save();
    }

    /**
     * Renders gateway description if available.
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function maybe_show_description() {
        $description = $this->get_description();
        if ( ! empty( $description ) ) {
            echo wp_kses(
                /* translators: 1) opening p tag, 2) gateway description, 3) closing p tag */
                sprintf( __( '%1$s%2$s%3$s', 'dokan' ), '<p>', $description, '</p>' ), // phpcs:ignore
                [
                    'p' => [],
                ]
            );
        }
    }
}
