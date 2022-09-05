<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\Processors;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use WC_Order;
use WP_Error;
use Exception;
use WC_Payment_Tokens;
use WeDevs\Dokan\Exceptions\DokanException;
use WeDevs\DokanPro\Modules\StripeExpress\Api\Charge;
use WeDevs\DokanPro\Modules\StripeExpress\Api\Source;
use WeDevs\DokanPro\Modules\StripeExpress\Api\Transfer;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;
use WeDevs\DokanPro\Modules\StripeExpress\Api\SetupIntent;
use WeDevs\DokanPro\Modules\StripeExpress\Api\Transaction;
use WeDevs\DokanPro\Modules\StripeExpress\Processors\Order;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Settings;
use WeDevs\DokanPro\Modules\StripeExpress\Support\UserMeta;
use WeDevs\DokanPro\Modules\StripeExpress\Support\OrderMeta;
use WeDevs\DokanPro\Modules\StripeExpress\Api\PaymentIntent;
use WeDevs\DokanPro\Modules\StripeExpress\Api\PaymentMethod;
use WeDevs\DokanPro\Modules\StripeExpress\Processors\Customer;
use WeDevs\DokanPro\Modules\StripeExpress\Processors\Withdraw;

/**
 * Class for processing payments.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\Processors
 */
class Payment {

    /**
     * Disburse payments on demand.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return void
     */
    public static function disburse( WC_Order $order ) {
        if ( Order::is_subscription_order( $order ) ) {
            return false;
        }

        // Charge id is stored in parent order, so we need to parse it from that order.
        $parent_order = $order->get_parent_id() ? wc_get_order( $order->get_parent_id() ) : $order;

        $charge_id = Order::get_charge_id( $parent_order );
        if ( ! $charge_id ) {
            throw new DokanException( 'dokan_charge_id_not_found', __( 'No charge id is found to process the order!', 'dokan' ) );
        }

        $all_orders = Order::get_all_orders_to_be_processed( $order );
        if ( ! $all_orders ) {
            throw new DokanException( 'dokan_no_order_found', __( 'No orders found to be processed!', 'dokan' ) );
        }

        $all_withdraws = [];
        $intent        = self::get_intent( $parent_order );
        $currency      = $order->get_currency();
        $order_total   = $order->get_total();
        $stripe_fee    = self::get_stripe_fee( $intent );

        OrderMeta::update_stripe_fee( $order, $stripe_fee );

        if ( $order->get_meta( 'has_sub_order' ) ) {
            OrderMeta::update_dokan_gateway_fee( $order, $stripe_fee );
            /* translators: 1) gateway title, 2) processing fee with currency */
            $order->add_order_note( sprintf( __( '[%1$s] Gateway processing fee %2$s', 'dokan' ), Helper::get_gateway_title(), wc_price( $stripe_fee, $order->get_currency() ) ) );
        }

        foreach ( $all_orders as $sub_order ) {
            //return if $sub_order not instance of WC_Order
            if ( ! $sub_order instanceof WC_Order ) {
                continue;
            }

            $sub_order_id        = $sub_order->get_id();
            $vendor_id           = dokan_get_seller_id_by_order( $sub_order_id );
            $connected_vendor_id = UserMeta::get_stripe_account_id( $vendor_id );

            if ( empty( $connected_vendor_id ) ) {
                $sub_order->add_order_note( __( 'Vendor is not connected to Stripe. The payment transfer has been terminated.', 'dokan' ) );
                continue;
            }

            $vendor_raw_earning    = dokan()->commission->get_earning_by_order( $sub_order, 'seller' );
            $stripe_fee_for_vendor = 0;
            $sub_order_total       = $sub_order->get_total();

            if ( floatval( $sub_order_total ) <= 0 ) {
                /* translators: order number */
                $sub_order->add_order_note( sprintf( __( 'Order %s payment completed', 'dokan' ), $sub_order->get_order_number() ) );
                continue;
            }

            if (
                Settings::sellers_pay_processing_fees() &&
                ! empty( $order_total ) &&
                ! empty( $sub_order_total ) &&
                ! empty( $stripe_fee )
            ) {
                $stripe_fee_for_vendor = Order::get_fee_for_suborder( $stripe_fee, $sub_order, $order );
                $vendor_raw_earning    = $vendor_raw_earning - $stripe_fee_for_vendor;

                OrderMeta::update_stripe_fee( $sub_order, $stripe_fee_for_vendor );
                OrderMeta::update_gateway_fee_paid_by( $sub_order, 'seller' );
            } else {
                OrderMeta::update_gateway_fee_paid_by( $sub_order, 'admin' );
            }

            OrderMeta::save( $sub_order );

            $vendor_earning = Helper::get_stripe_amount( $vendor_raw_earning );

            if ( $vendor_earning <= 0 ) {
                $sub_order->add_order_note(
                    sprintf(
                        /* translators: 1) balance amount 2) currency */
                        __( 'Transfer to the vendor stripe account has been terminated due to a negative balance: %1$s %2$s', 'dokan' ),
                        $vendor_raw_earning,
                        $currency
                    )
                );
                continue;
            }

            // get currency and symbol
            $currency        = $order->get_currency();
            $currency_symbol = html_entity_decode( get_woocommerce_currency_symbol( $order->get_currency() ) );

            // prepare extra metadata
            $application_fee = dokan()->commission->get_earning_by_order( $sub_order, 'admin' );
            $metadata        = [
                'stripe_processing_fee' => $currency_symbol . wc_format_decimal( $stripe_fee_for_vendor, 2 ),
                'application_fee'       => $currency_symbol . wc_format_decimal( $application_fee, 2 ),
            ];

            // get payment info
            $payment_info = self::generate_data( $order, $sub_order, $metadata, 'transfer' );

            // transfer amount to vendor's connected account
            if ( ! empty( OrderMeta::get_transfer_id( $sub_order ) ) ) {
                continue;
            }

            try {
                $payment_info['amount']      = $vendor_earning;
                $payment_info['currency']    = $currency;
                $payment_info['destination'] = $connected_vendor_id;
                $transfer                    = Transfer::create( $payment_info );

                OrderMeta::update_transfer_id( $sub_order, $transfer->id );
                OrderMeta::save( $sub_order );

                $withdraw_data = [
                    'user_id'  => $vendor_id,
                    'amount'   => wc_format_decimal( $vendor_raw_earning, 2 ),
                    'order_id' => $sub_order_id,
                ];

                $all_withdraws[] = $withdraw_data;
                Withdraw::process_data( $withdraw_data );

                // update vendor payment meta
                try {
                    $vendor_charge = Charge::update(
                        $transfer->destination_payment,
                        [
                            'description'    => $payment_info['description'],
                            'transfer_group' => $payment_info['transfer_group'],
                            'metadata'       => $payment_info['metadata'],
                        ],
                        [
                            'stripe_account' => $transfer->destination,
                        ]
                    );
                } catch ( Exception $e ) {
                    Helper::log( 'Could not update charge information: ' . $e->getMessage() );
                }

                if ( $order->get_id() !== $sub_order_id ) {
                    $sub_order->add_order_note(
                        sprintf(
                            /* translators: 1) order number, 2) gateway title, 3) charge id */
                            __( 'Order %1$s payment is completed via %2$s (Charge ID: %3$s)', 'dokan' ),
                            $sub_order->get_order_number(),
                            Helper::get_gateway_title(),
                            $charge_id
                        )
                    );
                }

                self::save_charge_data( $sub_order, $intent );
                OrderMeta::update_customer_id( $sub_order, $intent->customer );
                OrderMeta::update_source_id( $sub_order, $intent->source );
                OrderMeta::save( $sub_order );
            } catch ( Exception $e ) {
                Helper::log(
                    sprintf(
                        'Could not transfer amount to connected vendor account. Order ID: %1$s. Amount: %2$s %3$s',
                        $sub_order->get_id(),
                        $vendor_raw_earning,
                        $currency
                    )
                );

                $sub_order->add_order_note(
                    sprintf(
                        /* translators: 1) gateway title, 2) error message */
                        __( '[%1$s] Transfer failed to vendor. Reason: %2$s', 'dokan' ),
                        Helper::get_gateway_title(),
                        $e->getMessage()
                    )
                );

                continue;
            }
        }

        $order->add_order_note(
            sprintf(
                /* translators: 1) Gateway title, 2) Order number, 3) payment method type, 4) charge id */
                __( '[%1$s] Order %2$s payment is completed via %3$s. (Charge ID: %4$s)', 'dokan' ),
                Helper::get_gateway_title(),
                $order->get_order_number(),
                OrderMeta::get_payment_type( $order ),
                $charge_id
            )
        );

        OrderMeta::update_withdraw_data( $order, $all_withdraws );
        OrderMeta::save( $order );
        dokan()->commission->calculate_gateway_fee( $order->get_id() );
    }

    /**
     * Update order and maybe save payment method for an order after an intent has been created and confirmed.
     *
     * @since 3.6.1
     *
     * @param \WC_Order $order               Order being processed.
     * @param string    $intent_id           Stripe setup/payment ID.
     * @param bool      $save_payment_method Boolean representing whether payment method for order should be saved.
     *
     * @return void
     */
    public static function process_confirmed_intent( $order, $intent_id, $save_payment_method ) {
        $payment_needed = Helper::is_payment_needed( $order->get_id() );

        // Get payment intent to confirm status.
        if ( $payment_needed ) {
            $intent = PaymentIntent::get(
                $intent_id,
                [
                    'expand' => [
                        'payment_method',
                        'charges.data',
                    ],
                ]
            );

            $error = isset( $intent->last_payment_error ) ? $intent->last_payment_error : false;
        } else {
            $intent = SetupIntent::get(
                $intent_id,
                [
                    'expand' => [
                        'payment_method',
                        'latest_attempt',
                    ],
                ]
            );

            $error = isset( $intent->last_setup_error ) ? $intent->last_setup_error : false;
        }

        if ( ! empty( $error ) ) {
            Helper::log( 'Error when processing payment: ' . $error->message );
            throw new DokanException(
                'dokan-stripe-express-payment-error',
                __( "We're not able to process this payment. Please try again later.", 'dokan' )
            );
        }

        list( $payment_method_type, $payment_method_details ) = self::get_method_data_from_intent( $intent );
        $payment_methods = Helper::get_available_method_instances();
        if ( ! isset( $payment_methods[ $payment_method_type ] ) ) {
            return;
        }

        $payment_method = $payment_methods[ $payment_method_type ];
        if ( $save_payment_method && $payment_method->is_reusable() ) {
            $payment_method_object = null;
            if ( $payment_method->get_id() !== $payment_method->get_retrievable_type() ) {
                $payment_method_id     = $payment_method_details[ $payment_method_type ]->generated_sepa_debit;
                $payment_method_object = PaymentMethod::get( $payment_method_id );
            } else {
                $payment_method_object = $intent->payment_method;
            }

            $user                    = Order::get_user_from_order( $order );
            $prepared_payment_method = PaymentMethod::prepare( $payment_method_object );

            Customer::set( $user->ID );
            self::save_payment_method_data( $order, $prepared_payment_method );

            do_action( 'dokan_stripe_express_add_payment_method', $user->get_id(), $payment_method_object );
        }

        self::save_intent_data( $order, $intent );
        self::save_charge_data( $order, $intent );
        self::set_method_title( $order, $payment_method_type );
        OrderMeta::update_redirect_processed( $order );
        OrderMeta::save( $order );

        if ( $payment_needed ) {
            // Use the last charge within the intent to proceed.
            self::process_response( end( $intent->charges->data ), $order );
        } else {
            $order->payment_complete();
            do_action( 'dokan_stripe_express_payment_completed', $order, $intent );
        }
    }

    /**
     * Extracts payment method data from intent.
     *
     * @since 3.6.1
     *
     * @param object $intent
     *
     * @return array
     */
    public static function get_method_data_from_intent( $intent ) {
        $payment_method_type    = '';
        $payment_method_details = false;

        if ( 'payment_intent' === $intent->object ) {
            if ( ! empty( $intent->charges ) && 0 < $intent->charges->total_count ) {
                $charge                 = end( $intent->charges->data );
                $payment_method_details = $charge->payment_method_details;
                $payment_method_type    = ! empty( $payment_method_details ) ? $payment_method_details->type : '';
            }
        } elseif ( 'setup_intent' === $intent->object ) {
            if ( ! empty( $intent->latest_attempt ) && ! empty( $intent->latest_attempt->payment_method_details ) ) {
                $payment_method_details = $intent->latest_attempt->payment_method_details;
                $payment_method_type    = $payment_method_details->type;
            } elseif ( ! empty( $intent->payment_method ) ) {
                $payment_method_details = $intent->payment_method;
                $payment_method_type    = $payment_method_details->type;
            }
        }

        return [ $payment_method_type, $payment_method_details ];
    }

    /**
     * Create a new Payment Intent.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param array $data The source that is used for the payment
     * @param boolean $setup Flag to determine if is is a setup intent
     *
     * @return object
     * @throws Exception
     */
    public static function create_intent( WC_Order $order, $data, $setup = false ) {
        // get payment info
        $payment_info = self::generate_data( $order );

        $request = [
            'amount'               => Helper::get_stripe_amount( $order->get_total() ),
            'currency'             => strtolower( $order->get_currency() ),
            'description'          => $payment_info['description'],
            'metadata'             => $payment_info['metadata'],
            'capture_method'       => 'automatic',
            'payment_method_types' => [ 'card' ],
        ];

        $request = wp_parse_args( $data, $request );

        try {
            $intent = ! $setup ? PaymentIntent::create( $request ) : SetupIntent::create( $request );
        } catch ( DokanException $e ) {
            throw new DokanException( 'unable_to_create_intent', $e->get_message() );
        }

        self::save_intent_data( $order, $intent );

        return $intent;
    }

    /**
     * Updates payment intent to be able to save payment method.
     *
     * @since 3.6.1
     *
     * @param string  $payment_intent_id   The id of the payment intent to update.
     * @param int     $order_id            The id of the order if intent created from Order.
     * @param boolean $save_payment_method True if saving the payment method.
     * @param string  $payment_type        The name of the selected payment type or empty string.
     *
     * @return array|null An array with result of the update, or nothing
     * @throws DokanException  If the update intent call returns with an error.
     */
    public static function update_intent( $payment_intent_id = '', $order_id = null, $save_payment_method = false, $payment_type = '' ) {
        $order = wc_get_order( $order_id );

        if ( ! is_a( $order, 'WC_Order' ) ) {
            throw new DokanException( 'invalid_order', __( 'No valid order found!', 'dokan' ) );
        }

        $customer = Customer::set( get_current_user_id() );
        // get payment info
        $payment_info = self::generate_data( $order );

        if ( $payment_intent_id ) {
            $request = [
                'amount'               => Helper::get_stripe_amount( $order->get_total(), strtolower( $order->get_currency() ) ),
                'currency'             => strtolower( $order->get_currency() ),
                'description'          => $payment_info['description'],
                'metadata'             => $payment_info['metadata'],
                'payment_method_types' => [ 'card' ],
            ];

            if ( ! empty( $payment_type ) ) {
                // Only update the payment_method_types if we have a reference to the payment type the customer selected.
                $request['payment_method_types'] = [ $payment_type ];
                OrderMeta::update_payment_type( $order, $payment_type );
            }

            if ( ! empty( $customer ) && $customer->get_id() ) {
                $request['customer'] = $customer->get_id();
            }

            if ( $save_payment_method ) {
                $request['setup_future_usage'] = 'off_session';
            }

            PaymentIntent::update( $payment_intent_id, $request );
            $order->update_status( 'pending', __( 'Awaiting payment.', 'dokan' ) );
            OrderMeta::add_payment_intent( $order, $payment_intent_id );
            OrderMeta::save( $order );
        }

        return true;
    }

    /**
     * Creates source for an order.
     *
     * @since 3.6.1
     *
     * @param WC_Order     $order
     * @param array|object $data
     *
     * @return object
     */
    public static function create_charge( WC_Order $order, $data ) {
        // get payment info
        $payment_info = self::generate_data( $order );
        $data         = (array) $data;

        $request = [
            'amount'               => Helper::get_stripe_amount( $order->get_total() ),
            'currency'             => strtolower( $order->get_currency() ),
            'description'          => $payment_info['description'],
            'metadata'             => $payment_info['metadata'],
            'capture_method'       => 'automatic',
            'payment_method_types' => [ 'card' ],
            'expand'               => [ 'balance_transaction' ],
        ];

        if ( $data['customer'] ) {
            $request['customer'] = $data['customer'];
        }

        if ( ! empty( $data['source'] ) ) {
            $request['source'] = $data['source'];
        }

        if ( ! empty( $data['payment_method'] ) ) {
            $request['payment_method'] = $data['payment_method'];
        }

        $request = wp_parse_args( $data, $request );

        try {
            return Charge::create( $request );
        } catch ( DokanException $e ) {
            throw new DokanException( 'unable_to_create_intent', $e->get_message() );
        }
    }

    /**
     * Create the level 3 data array to send to Stripe when making a purchase.
     *
     * @param WC_Order $order The order that is being paid for.
     * @return array          The level 3 data to send to Stripe.
     */
    public static function generate_level3_data( WC_Order $order ) {
        // Get the order items. Don't need their keys, only their values.
        // Order item IDs are used as keys in the original order items array.
        $order_items = array_values( $order->get_items( [ 'line_item', 'fee' ] ) );
        $currency    = $order->get_currency();

        $stripe_line_items = array_map(
            function( $item ) use ( $currency ) {
                if ( is_a( $item, 'WC_Order_Item_Product' ) ) {
                    $product_id = $item->get_variation_id()
                        ? $item->get_variation_id()
                        : $item->get_product_id();
                    $subtotal   = $item->get_subtotal();
                } else {
                    $product_id = substr( sanitize_title( $item->get_name() ), 0, 12 );
                    $subtotal   = $item->get_total();
                }
                $product_description = substr( $item->get_name(), 0, 26 );
                $quantity            = $item->get_quantity();
                $unit_cost           = Helper::get_stripe_amount( ( $subtotal / $quantity ), $currency );
                $tax_amount          = Helper::get_stripe_amount( $item->get_total_tax(), $currency );
                $discount_amount     = Helper::get_stripe_amount( $subtotal - $item->get_total(), $currency );

                return (object) [
                    'product_code'        => (string) $product_id, // Up to 12 characters that uniquely identify the product.
                    'product_description' => $product_description, // Up to 26 characters long describing the product.
                    'unit_cost'           => $unit_cost, // Cost of the product, in cents, as a non-negative integer.
                    'quantity'            => $quantity, // The number of items of this type sold, as a non-negative integer.
                    'tax_amount'          => $tax_amount, // The amount of tax this item had added to it, in cents, as a non-negative integer.
                    'discount_amount'     => $discount_amount, // The amount an item was discounted—if there was a sale,for example, as a non-negative integer.
                ];
            },
            $order_items
        );

        $level3_data = [
            'merchant_reference' => $order->get_id(), // An alphanumeric string of up to  characters in length. This unique value is assigned by the merchant to identify the order. Also known as an “Order ID”.
            'shipping_amount'    => Helper::get_stripe_amount( (float) $order->get_shipping_total() + (float) $order->get_shipping_tax(), $currency ), // The shipping cost, in cents, as a non-negative integer.
            'line_items'         => $stripe_line_items,
        ];

        // The customer’s U.S. shipping ZIP code.
        $shipping_address_zip = $order->get_shipping_postcode();
        if ( Helper::is_valid_zip_code( $shipping_address_zip ) ) {
            $level3_data['shipping_address_zip'] = $shipping_address_zip;
        }

        // The merchant’s U.S. shipping ZIP code.
        $store_postcode = get_option( 'woocommerce_store_postcode' );
        if ( Helper::is_valid_zip_code( $store_postcode ) ) {
            $level3_data['shipping_from_zip'] = $store_postcode;
        }

        return $level3_data;
    }

    /**
     * Confirms an intent if it is the `requires_confirmation` state.
     *
     * @since 3.6.1
     *
     * @param object $intent The intent to confirm.
     * @param object $prepared_source The source that is being charged.
     *
     * @return \Stripe\Paymentintent|WP_Error
     */
    public static function confirm_intent( $intent, $prepared_source ) {
        if ( 'requires_confirmation' !== $intent->status ) {
            return $intent;
        }

        // Try to confirm the intent (if 3DS is not required).
        $request = [
            'source' => $prepared_source->source,
        ];

        try {
            $confirmed_intent = $intent->confirm( $request );
        } catch ( \Exception $e ) {
            return new WP_Error( 'unable_to_confirm_intent', $e->getMessage() );
        }

        return $confirmed_intent;
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
    public static function set_method_title( $order, $payment_method_type ) {
        $payment_methods = Helper::get_available_method_instances();
        if ( ! isset( $payment_methods[ $payment_method_type ] ) ) {
            return;
        }

        $payment_method_title = $payment_methods[ $payment_method_type ]->get_label();
        $order->set_payment_method( Helper::get_gateway_id() );
        $order->set_payment_method_title( $payment_method_title );
        $order->save();
    }

    /**
     * Stores extra meta data for an order from a Stripe Response.
     *
     * @since 3.6.1
     *
     * @param object $response
     * @param WC_Order $order
     *
     * @return object
     * @throws DokanException
     */
    public static function process_response( $response, $order ) {
        Helper::log( 'Processing response: ' . print_r( $response, true ) );

        $order_id = $order->get_id();
        $captured = ! empty( $response->captured ) ? 'yes' : 'no';

        // Store charge data.
        OrderMeta::update_charge_captured( $order, $captured );

        if ( 'yes' === $captured ) {
            switch ( $response->status ) {
                case 'succeeded':
                    $order->payment_complete( $response->id );

                    OrderMeta::update_transaction_id( $order, $response->id );

                    $order->add_order_note(
                        /* translators: 1) gateway title, 2) transaction id */
                        sprintf( __( '[%1$s] Charge complete (Charge ID: %2$s)', 'dokan' ), Helper::get_gateway_title(), $response->id )
                    );
                    break;

                /*
                 * Charge can be captured but in a pending state. Payment methods
                 * that are asynchronous may take couple days to clear. Webhook will
                 * take care of the status changes.
                 */
                case 'pending':
                    $order_stock_reduced = $order->get_meta( '_order_stock_reduced', true );

                    if ( ! $order_stock_reduced ) {
                        wc_reduce_stock_levels( $order_id );
                    }

                    OrderMeta::update_transaction_id( $order, $response->id );
                    /* translators: 1) gateway title, 2) transaction id */
                    $order->update_status( 'on-hold', sprintf( __( '[%1$s] Stripe charge awaiting payment: %2$ss.', 'dokan' ), $response->id ) );
                    break;

                case 'failed':
                    $localized_message = __( 'Payment processing failed. Please retry.', 'dokan' );
                    $order->add_order_note( $localized_message );
                    throw new DokanException( print_r( $response, true ), $localized_message );            }
        } else {
            OrderMeta::update_transaction_id( $order, $response->id );

            if ( $order->has_status( [ 'pending', 'failed' ] ) ) {
                wc_reduce_stock_levels( $order_id );
            }

            /* translators: transaction id */
            $order->update_status(
                'on-hold',
                sprintf(
                    /* translators: 1) gateway title, 2) charge id */
                    __( '[%1$s] Charge authorized (Charge ID: %2$s). Process order to take payment, or cancel to remove the pre-authorization. Attempting to refund the order in part or in full will release the authorization and cancel the payment.', 'dokan' ),
                    Helper::get_gateway_title(),
                    $response->id
                )
            );
        }

        OrderMeta::save( $order );

        do_action( 'dokan_stripe_express_process_response', $response, $order );

        return $response;
    }

    /**
     * Get payment intent id of an order.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return object|false
     */
    public static function get_intent( WC_Order $order ) {
        $intent_id = OrderMeta::get_payment_intent( $order );
        if ( empty( $intent_id ) ) {
            return false;
        }

        return PaymentIntent::get(
            $intent_id,
            [
                'expand' => [
                    'charges.data',
                ],
            ]
        );
    }

    /**
     * Saves payment method data top order meta
     *
     * @since 3.6.1
     *
     * @param \WC_Order $order
     * @param object    $payment_method
     *
     * @return void
     */
    public static function save_payment_method_data( $order, $payment_method ) {
        if ( $payment_method->customer ) {
            OrderMeta::update_customer_id( $order, $payment_method->customer );
        }

        OrderMeta::update_source_id( $order, $payment_method->payment_method );
        OrderMeta::save( $order );
    }

    /**
     * Saves payment/setup intent to order meta.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param object   $intent
     *
     * @return void
     */
    public static function save_intent_data( WC_Order $order, $intent ) {
        if ( 'payment_intent' === $intent->object ) {
            OrderMeta::add_payment_intent( $order, $intent->id );
        } elseif ( 'setup_intent' === $intent->object ) {
            OrderMeta::add_setup_intent( $order, $intent->id );
        }

        OrderMeta::save( $order );
    }

    /**
     * Saves charge data for order.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param object   $intent
     *
     * @return void
     */
    public static function save_charge_data( WC_Order $order, $intent ) {
        Ordermeta::update_charge_captured( $order );

        $charge_id = Order::get_charge_id( $order, $intent );

        if ( $charge_id ) {
            OrderMeta::update_transaction_id( $order, $charge_id );
        }

        OrderMeta::save( $order );
    }

    /**
     * Retrieves stripe fee from intent.
     *
     * @since 3.6.1
     *
     * @param object  $intent The stripe intent object
     * @param boolean $raw    (Optional) Whether or not to format the value. By default, formatted value will be returned.
     *
     * @return string|float
     */
    public static function get_stripe_fee( $intent, $raw = false ) {
        $latest_charge_data  = end( $intent->charges->data );
        $balance_transaction = Transaction::get( $latest_charge_data->balance_transaction );

        return ! $raw
            ? Helper::format_balance_fee( $balance_transaction )
            : $balance_transaction->fee;
    }

    /**
     * Get payment source. This can be a new token/source or existing WC token.
     * If user is logged in and/or has WC account, create an account on Stripe.
     * This way we can attribute the payment to the user to better fight fraud.
     *
     * @since 3.6.1
     *
     * @param int      $user_id              ID of the WP user.
     * @param bool     $force_save_source    Should we force save payment source.
     * @param int|null $existing_customer_id ID of customer if already exists.
     *
     * @return object
     * @throws Exception
     */
    public static function prepare_source( $user_id, $force_save_source = false, $existing_customer_id = null ) {
        $customer = Customer::set( $user_id );

        if ( ! empty( $existing_customer_id ) ) {
            $customer->set_id( $existing_customer_id );
        }

        $source_object      = '';
        $source_id          = '';
        $wc_token_id        = '';
        $token_saved        = false;
        $is_token           = false;
        $setup_future_usage = false;
        $payment_method     = isset( $_POST['payment_method'] ) // phpcs:ignore WordPress.Security.NonceVerification.Missing
            ? sanitize_text_field( wp_unslash( $_POST['payment_method'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Missing
            : Helper::get_gateway_id();

        // New CC info was entered and we have a new source to process.
        if ( ! empty( $_POST['stripe_source'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
            $source_object    = Source::get( sanitize_text_field( wp_unslash( $_POST['stripe_source'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
            $source_id        = $source_object->id;
            $maybe_saved_card = Helper::is_saved_card( $payment_method );

            /*
             * This is true if the user wants to store the card to their account.
             * Criteria to save to file is they are logged in, they opted
             * to save or product requirements and the source is actually reusable.
             * Either that or force_save_source is true.
             */
            if (
                $force_save_source ||
                ( $user_id && Settings::is_saved_cards_enabled() && $maybe_saved_card && 'reusable' === $source_object->usage )
            ) {
                $response = $customer->add_source( $source_object->id );
                if ( ! empty( $response->error ) ) {
                    throw new Exception( print_r( $response, true ), Helper::get_error_message_from_response( $response ) );
                }
                $setup_future_usage = true;
                $token_saved = true;
            }
        } elseif ( Helper::is_using_saved_payment_method() ) {
            $wc_token_id = sanitize_text_field( wp_unslash( $_POST[ 'wc-' . $payment_method . '-payment-token' ] ) ); // phpcs:ignore
            $wc_token    = WC_Payment_Tokens::get( $wc_token_id );

            if ( ! $wc_token || $wc_token->get_user_id() !== get_current_user_id() ) {
                WC()->session->set( 'refresh_totals', true );
                throw new Exception( __( 'Invalid payment method. Please input a new card number.', 'dokan' ) );
            }

            $source_id = $wc_token->get_token();

            if ( Helper::is_type_legacy_card( $source_id ) ) {
                $is_token = true;
            }
        } elseif ( ! empty( $_POST['stripe_token'] ) && 'new' !== $_POST['stripe_token'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
            $stripe_token     = sanitize_text_field( wp_unslash( $_POST['stripe_token'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
            $is_token         = true;
            $maybe_saved_card = Helper::is_saved_card( $payment_method );

            /**
             * This is true if the user wants to store the card to their account.
             * Criteria to save to file is they are logged in, they opted to save or product requirements and the source is
             * actually reusable. Either that or force_save_source is true.
             */
            if ( $force_save_source || ( $user_id && Settings::is_saved_cards_enabled() && $maybe_saved_card ) ) {
                $response = $customer->add_source( $stripe_token );
                if ( ! empty( $response->error ) ) {
                    throw new Exception( print_r( $response, true ), Helper::get_error_message_from_response( $response ) );
                }
                $source_id    = $response;
                $setup_future_usage = true;
                $token_saved = true;
            } else {
                $source_id    = $stripe_token;
                $is_token     = true;
            }
        }

        $customer_id = $customer->get_id();
        if ( ! $customer_id ) {
            $created = $customer->create();
            if ( is_wp_error( $created ) ) {
                throw new Exception( $created->get_error_message() );
            }

            $customer->set_id( $created );
            $customer_id = $customer->get_id();
        } else {
            $updated = $customer->update();
            if ( is_wp_error( $updated ) ) {
                throw new Exception( $updated->get_error_message() );
            }
        }

        if ( empty( $source_object ) && ! $is_token ) {
            $source_object = Source::get( $source_id );
        }

        return (object) [
            'customer'           => $customer_id,
            'token_id'           => $wc_token_id,
            'source'             => $source_id,
            'source_object'      => $source_object,
            'setup_future_usage' => $setup_future_usage ? 'off_session' : 'on_session',
            'token_saved'        => $token_saved,
        ];
    }

    /**
     * Generate extra information for orders to send with stripe.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order                     The Order object.
     * @param WC_Order $sub_order      (Optional) The Sub Order object if available.
     * @param array    $extra_metadata (Optional) Extra metadata to attach.
     * @param string   $api_type       (Optional) The Stripe API type to use. Ex: 'payment_intent', 'setup_intent', 'transfer', 'charge', etc.
     *
     * @return array
     */
    public static function generate_data( WC_Order $order, WC_Order $sub_order = null, array $extra_metadata = [], $api_type = 'payment_intent' ) {
        $post_data = [
            'transfer_group' => apply_filters(
                'dokan_stripe_express_transfer_group',
                sprintf( 'Dokan Order#%d', $order->get_id() ),
                $order,
                $sub_order
            ),
        ];

        if ( 'transfer' !== $api_type ) {
            $statement_descriptor = Settings::get_statement_descriptor();
            if ( ! empty( $statement_descriptor ) ) {
                $post_data['statement_descriptor'] = $statement_descriptor;
            }

            if ( method_exists( $order, 'get_shipping_postcode' ) && ! empty( $order->get_shipping_postcode() ) ) {
                $post_data['shipping'] = [
                    'name'    => trim( $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name() ),
                    'address' => [
                        'line1'       => $order->get_shipping_address_1(),
                        'line2'       => $order->get_shipping_address_2(),
                        'city'        => $order->get_shipping_city(),
                        'country'     => $order->get_shipping_country(),
                        'postal_code' => $order->get_shipping_postcode(),
                        'state'       => $order->get_shipping_state(),
                    ],
                ];
            }
        }

        $metadata = [
            'customer_name'  => sanitize_text_field( $order->get_billing_first_name() ) . ' ' . sanitize_text_field( $order->get_billing_last_name() ),
            'customer_email' => sanitize_email( $order->get_billing_email() ),
            'order_id'       => $order->get_id(),
            'site_url'       => esc_url( get_site_url() ),
            'payment_type'   => 'single',
        ];

        if ( Helper::has_subscription( $order->get_id() ) ) {
            $metadata['payment_type'] = 'recurring';
        }

        if ( is_array( $extra_metadata ) && ! empty( $extra_metadata ) ) {
            $metadata += $extra_metadata;
        }

        if ( ! is_null( $sub_order ) && $sub_order->get_id() !== $order->get_id() ) {
            $post_data['description'] = sprintf(
                /* translators: 1) blog name 2) order number 3) sub order number */
                __( '%1$1s - Order %2$2s, suborder of %3$3s', 'dokan' ),
                wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
                $sub_order->get_order_number(),
                $order->get_order_number()
            );

            // Fix sub order metadata
            $metadata['order_id']        = $sub_order->get_id();
            $metadata['parent_order_id'] = $order->get_id();
        } else {
            $post_data['description'] = sprintf(
                /* translators: 1) blog name 2) order number */
                __( '%1$s - Order %2$s', 'dokan' ),
                wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
                $order->get_order_number()
            );
        }

        $post_data['metadata'] = apply_filters( 'dokan_stripe_express_payment_metadata', $metadata, $order, $sub_order );

        return apply_filters( 'dokan_stripe_express_generate_payment_info', $post_data, $order, $sub_order );
    }
}
