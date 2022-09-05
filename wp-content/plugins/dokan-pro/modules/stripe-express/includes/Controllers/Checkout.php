<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\Controllers;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use Exception;
use WC_Customer;
use WeDevs\Dokan\Exceptions\DokanException;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;
use WeDevs\DokanPro\Modules\StripeExpress\Api\SetupIntent;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Settings;
use WeDevs\DokanPro\Modules\StripeExpress\Api\PaymentIntent;
use WeDevs\DokanPro\Modules\StripeExpress\Support\OrderMeta;
use WeDevs\DokanPro\Modules\StripeExpress\Processors\Payment;
use WeDevs\DokanPro\Modules\StripeExpress\Processors\Customer;

/**
 * Ajax controller class for checkout.
 *
 * Handles in-checkout AJAX calls, related to Payment Intents.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\Controllers
 */
class Checkout {

    /**
     * Holds an instance of the gateway class.
     *
     * @since 3.6.1
     *
     * @var WeDevs\DokanPro\Modules\StripeExpress\Gateways\Stripe
     */
    protected $gateway;

    /**
     * Class constructor.
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function __construct() {
        if ( ! Helper::is_gateway_ready() ) {
            return;
        }

        $this->hooks();
    }

    /**
     * Registers all necessary hooks.
     *
     * @since 3.6.1
     *
     * @return void
     */
    protected function hooks() {
        add_action( 'wc_ajax_dokan_stripe_express_create_payment_intent', [ $this, 'create_payment_intent' ] );
        add_action( 'wc_ajax_dokan_stripe_express_update_payment_intent', [ $this, 'update_payment_intent' ] );
        add_action( 'wc_ajax_dokan_stripe_express_init_setup_intent', [ $this, 'init_setup_intent' ] );

        add_action( 'wc_ajax_dokan_stripe_express_update_order_status', [ $this, 'update_order_status' ] );
        add_action( 'wc_ajax_dokan_stripe_express_update_failed_order', [ $this, 'update_failed_order' ] );
    }

    /**
     * Returns an instantiated gateway.
     *
     * @since 3.6.1
     *
     * @return WeDevs\DokanPro\Modules\StripeExpress\Gateways\Stripe
     */
    protected function gateway() {
        if ( ! isset( $this->gateway ) ) {
            $gateways      = WC()->payment_gateways()->payment_gateways();
            $this->gateway = $gateways[ Helper::get_gateway_id() ];
        }

        return $this->gateway;
    }

    /**
     * Handle AJAX requests for creating a payment intent for Stripe.
     *
     * @since 3.6.1
     *
     * @return mixed
     */
    public function create_payment_intent() {
        try {
            if ( ! check_ajax_referer( 'dokan_stripe_express_checkout', false, false ) ) {
                throw new Exception( __( "We're not able to process this payment. Please refresh the page and try again.", 'dokan' ) );
            }

            // If paying from order, we need to get the total from the order instead of the cart.
            $order_id = isset( $_POST['order_id'] ) ? absint( wp_unslash( $_POST['order_id'] ) ) : null;
            $amount   = WC()->cart->get_total( false );
            $order    = wc_get_order( $order_id );
            if ( is_a( $order, 'WC_Order' ) ) {
                $amount = $order->get_total();
            }

            $currency       = get_woocommerce_currency();
            $payment_intent = PaymentIntent::create(
                [
                    'amount'               => Helper::get_stripe_amount( $amount, strtolower( $currency ) ),
                    'currency'             => strtolower( $currency ),
                    'payment_method_types' => $this->gateway()->get_enabled_payment_methods_at_checkout( $order_id ),
                    'capture_method'       => Settings::is_manual_capture_enabled() ? 'manual' : 'automatic',
                ]
            );

            if ( ! empty( $payment_intent->error ) ) {
                throw new Exception( $payment_intent->error->message );
            }

            wp_send_json_success(
                [
                    'id'            => $payment_intent->id,
                    'client_secret' => $payment_intent->client_secret,
                ],
                200
            );
        } catch ( DokanException $e ) {
            Helper::log( 'Create payment intent error: ' . $e->getMessage() );
            // Send back error so it can be displayed to the customer.
            wp_send_json_error(
                [
                    'error' => [
                        'message' => $e->getMessage(),
                    ],
                ]
            );
        }
    }

    /**
     * Handle AJAX request for updating a payment intent for Stripe.
     *
     * @since 3.6.1
     *
     * @return mixed
     */
    public function update_payment_intent() {
        try {
            if ( ! check_ajax_referer( 'dokan_stripe_express_checkout', false, false ) ) {
                throw new Exception( __( "We're not able to process this payment. Please refresh the page and try again.", 'dokan' ) );
            }

            $order_id            = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : null;
            $payment_intent_id   = isset( $_POST['payment_intent_id'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_intent_id'] ) ) : '';
            $save_payment_method = isset( $_POST['save_payment_method'] ) ? 'yes' === sanitize_text_field( wp_unslash( $_POST['save_payment_method'] ) ) : false;
            $payment_type        = ! empty( $_POST['payment_type'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_type'] ) ) : '';

            wp_send_json_success( Payment::update_intent( $payment_intent_id, $order_id, $save_payment_method, $payment_type ), 200 );
        } catch ( DokanException $e ) {
            // Send back error so it can be displayed to the customer.
            wp_send_json_error(
                [
                    'error' => [
                        'message' => $e->getMessage(),
                    ],
                ]
            );
        }
    }

    /**
     * Handle AJAX requests for creating a setup intent without confirmation for Stripe.
     *
     * @since 3.6.1
     *
     * @return mixed
     */
    public function init_setup_intent() {
        try {
            if ( ! check_ajax_referer( 'dokan_stripe_express_checkout', false, false ) ) {
                throw new Exception( __( "We're not able to add this payment method. Please refresh the page and try again.", 'dokan' ) );
            }

            // Determine the customer managing the payment methods, create one if we don't have one already.
            $user        = wp_get_current_user();
            $customer    = Customer::set( $user->ID );
            $customer_id = $customer->get_id();
            if ( empty( $customer_id ) ) {
                $customer_data = $customer->map_data( null, new WC_Customer( $user->ID ) );
                $customer_id   = $customer->create( $customer_data );

                if ( is_wp_error( $customer_id ) ) {
                    throw new Exception(
                        sprintf(
                            /* translators: error message */
                            __( 'We\'re not able to add this payment method. Error: %s', 'dokan' ),
                            $customer_id->get_error_message()
                        )
                    );
                }
            }

            $payment_method_types = Helper::get_reusable_payment_methods();
            $setup_intent         = SetupIntent::create(
                [
                    'customer'             => $customer_id,
                    'confirm'              => 'false',
                    'payment_method_types' => array_values( $payment_method_types ),
                ]
            );

            if ( ! empty( $setup_intent->error ) ) {
                throw new Exception( $setup_intent->error->message );
            }

            $intent = [
                'id'            => $setup_intent->id,
                'client_secret' => $setup_intent->client_secret,
            ];

            wp_send_json_success( $intent, 200 );
        } catch ( Exception $e ) {
            // Send back error, so it can be displayed to the customer.
            wp_send_json_error(
                [
                    'error' => [
                        'message' => $e->getMessage(),
                    ],
                ]
            );
        }
    }

    /**
     * Handle AJAX request after authenticating payment at checkout.
     *
     * This function is used to update the order status after the user has
     * been asked to authenticate their payment.
     *
     * This function is used for both:
     * - regular checkout
     * - Pay for Order page (in theory).
     *
     * @since 3.6.1
     *
     * @return mixed
     * @throws Exception
     */
    public function update_order_status() {
        try {
            if ( ! check_ajax_referer( 'dokan_stripe_express_update_order_status', false, false ) ) {
                throw new Exception( __( 'CSRF verification failed.', 'dokan' ) );
            }

            $order_id = isset( $_POST['order_id'] ) ? absint( wp_unslash( $_POST['order_id'] ) ) : false;
            $order    = wc_get_order( $order_id );
            if ( ! $order ) {
                throw new Exception( __( "We're not able to process this payment. Please try again later.", 'dokan' ) );
            }

            $intent_id          = OrderMeta::get_payment_intent( $order );
            $intent_id_received = isset( $_POST['intent_id'] ) ? sanitize_text_field( wp_unslash( $_POST['intent_id'] ) ) : null;
            if ( empty( $intent_id_received ) || $intent_id_received !== $intent_id ) {
                $note = sprintf(
                    /* translators: %1: transaction ID of the payment or a translated string indicating an unknown ID. */
                    esc_html__( 'A payment with ID %s was used in an attempt to pay for this order. This payment intent ID does not match any payments for this order, so it was ignored and the order was not updated.', 'dokan' ),
                    $intent_id_received
                );
                $order->add_order_note( $note );
                throw new Exception( __( "We're not able to process this payment. Please try again later.", 'dokan' ) );
            }
            $save_payment_method = ! empty( sanitize_text_field( wp_unslash( $_POST['payment_method_id'] ) ) );

            Payment::process_confirmed_intent( $order, $intent_id_received, $save_payment_method );
            wp_send_json_success(
                [
                    'return_url' => $this->gateway()->get_return_url( $order ),
                ],
                200
            );
        } catch ( DokanException $e ) {
            wc_add_notice( $e->getMessage(), 'error' );
            Helper::log( 'Error: ' . $e->getMessage() );

            /* translators: error message */
            if ( $order ) {
                $order->update_status( 'failed' );
            }

            // Send back error so it can be displayed to the customer.
            wp_send_json_error(
                [
                    'error' => [
                        'message' => $e->getMessage(),
                    ],
                ]
            );
        }
    }

    /**
     * Handle AJAX request if error occurs while confirming intent.
     * We will log the error and update the order.
     *
     * @since 3.6.1
     *
     * @return mixed
     * @throws Exception
     */
    public function update_failed_order() {
        try {
            if ( ! check_ajax_referer( 'dokan_stripe_express_checkout', false, false ) ) {
                throw new Exception( __( 'CSRF verification failed.', 'dokan' ) );
            }

            $order_id  = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : null;
            $intent_id = isset( $_POST['intent_id'] ) ? sanitize_text_field( wp_unslash( $_POST['intent_id'] ) ) : '';
            $order     = wc_get_order( $order_id );
            if ( ! empty( $order_id ) && ! empty( $intent_id ) && is_object( $order ) ) {
                $payment_needed = 0 < $order->get_total();
                if ( $payment_needed ) {
                    $intent = PaymentIntent::get(
                        $intent_id,
                        [
                            'expand' => [
                                'charges.data',
                            ],
                        ]
                    );
                } else {
                    $intent = SetupIntent::get( $intent_id );
                }
                $error = $intent->last_payment_error;

                if ( ! empty( $error ) ) {
                    Helper::log( 'Error when processing payment: ' . $error->message );
                    throw new Exception( __( "We're not able to process this payment. Please try again later.", 'dokan' ) );
                }

                // Use the last charge within the intent to proceed.
                if ( isset( $intent->charges ) && ! empty( $intent->charges->data ) ) {
                    $charge = end( $intent->charges->data );
                    Payment::process_response( $charge, $order );
                } else {
                    // TODO: Add implementation for setup intents.
                    Payment::process_response( $intent, $order );
                }

                Payment::save_intent_data( $order, $intent );
                Payment::save_charge_data( $order, $intent );
            }
        } catch ( DokanException $e ) {
            // We are expecting an exception to be thrown here.
            wc_add_notice( $e->getMessage(), 'error' );
            Helper::log( 'Error: ' . $e->getMessage() );

            do_action( 'dokan_stripe_express_process_payment_error', $e, $order );

            /* translators: error message */
            $order->update_status( 'failed' );
        }

        wp_send_json_success();
    }
}
