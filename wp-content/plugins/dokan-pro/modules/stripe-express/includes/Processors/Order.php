<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\Processors;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use WC_Order;
use Exception;
use WC_Product;
use WeDevs\Dokan\Exceptions\DokanException;
use WeDevs\DokanPro\Modules\StripeExpress\Api\Source;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;
use WeDevs\DokanPro\Modules\StripeExpress\Support\UserMeta;
use WeDevs\DokanPro\Modules\StripeExpress\Support\OrderMeta;

/**
 * Class for processing orders.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\Processors
 */
class Order {

    /**
     * Saves source to order.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param object $source
     *
     * @return void
     */
    public static function save_source( WC_Order $order, $source ) {
        if ( $source->customer ) {
            OrderMeta::update_customer_id( $order, $source->customer );
        }

        if ( $source->source ) {
            OrderMeta::update_source_id( $order, $source->source );
        }

        OrderMeta::save( $order );
    }

    /**
     * Checks whether a source exists.
     *
     * @since 3.6.1
     *
     * @param object $prepared_source The source that should be verified.
     * @throws Exception
     */
    public static function validate_source( $prepared_source ) {
        if ( empty( $prepared_source->source ) ) {
            throw new DokanException(
                'invalid-source',
                __( 'Invalid Payment Source: Payment processing failed. Please retry.', 'dokan' )
            );
        }

        if ( ! empty( $prepared_source->source_object->status ) && 'consumed' === $prepared_source->source_object->status ) {
            throw new DokanException(
                'invalid-source',
                sprintf(
                    /* translators: payment method endpoint url */
                    __( 'Payment processing failed. Please try again with a different card. If it\'s a saved card, <a href="%s" target="_blank">remove it first</a> and try again.', 'dokan' ),
                    wc_get_account_endpoint_url( 'payment-methods' )
                )
            );
        }
    }

    /**
     * Get payment source from an order. This could be used in the future for
     * a subscription as an example, therefore using the current user ID would
     * not work - the customer won't be logged in :)
     *
     * Not using 2.6 tokens for this part since we need a customer AND a card
     * token, and not just one.
     *
     * @since 3.6.1
     *
     * @param object $order
     *
     * @return object
     * @throws Exception
     */
    public static function prepare_source( $order = null ) {
        $stripe_customer = Customer::set();
        $stripe_source   = false;
        $token_id        = false;
        $source_object   = false;

        if ( $order ) {
            $stripe_customer_id = self::get_stripe_customer_id_from_order( $order );

            if ( $stripe_customer_id ) {
                $stripe_customer->set_id( $stripe_customer_id );
            }

            $source_id = OrderMeta::get_source_id( $order );

            // Since 4.0.0, we changed card to source so we need to account for that.
            if ( empty( $source_id ) ) {
                $source_id = OrderMeta::get_card_id( $order );

                // Take this opportunity to update the key name.
                OrderMeta::update_source_id( $order, $source_id );
                OrderMeta::save( $order );
            }

            if ( $source_id ) {
                $stripe_source = $source_id;
                $source_object = Source::get( $source_id );
            } elseif ( apply_filters( 'dokan_stripe_express_use_default_customer_source', true ) ) {
                /*
                 * We can attempt to charge the customer's default source
                 * by sending empty source id.
                 */
                $stripe_source = '';
            }
        }

        return (object) [
            'token_id'      => $token_id,
            'customer'      => $stripe_customer ? $stripe_customer->get_id() : false,
            'source'        => $stripe_source,
            'source_object' => $source_object,
        ];
    }

    /**
     * Checks if a order is a subscription order.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return boolean
     */
    public static function is_subscription_order( WC_Order $order ) {
        if ( ! Helper::has_subscription_module() ) {
            return false;
        }

        $product = self::get_subscription_product_by_order( $order );

        return $product ? true : false;
    }

    /**
     * Get subscription product from an order.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return \WC_Product|null
     */
    public static function get_subscription_product_by_order( WC_Order $order ) {
        foreach ( $order->get_items() as $item ) {
            $product = wc_get_product( $item['product_id'] );

            if ( 'product_pack' === $product->get_type() ) {
                return $product;
            }
        }

        return null;
    }

    /**
     * Extracts an order to all its suborders if exists
     * and returns data containg all of those orders.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return array
     */
    public static function get_all_orders_to_be_processed( $order ) {
        $all_orders = [];

        if ( $order->get_meta( 'has_sub_order' ) ) {
            $sub_order_ids = get_children(
                [
                    'post_parent' => $order->get_id(),
                    'post_type'   => 'shop_order',
                    'fields'      => 'ids',
                ]
            );

            foreach ( $sub_order_ids as $sub_order_id ) {
                $sub_order    = wc_get_order( $sub_order_id );
                $all_orders[] = $sub_order;
            }
        } else {
            $all_orders[] = $order;
        }

        return apply_filters( 'dokan_get_all_orders_to_be_processed', $all_orders );
    }

    /**
     * Get charge id from from an order
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param object   $intent
     *
     * @return string|false on failure
     */
    public static function get_charge_id( WC_Order $order, $intent = false ) {
        if ( ! $intent || ! is_object( $intent ) ) {
            $intent = Payment::get_intent( $order );
        }

        if ( ! $intent || ! is_object( $intent ) ) {
            return false;
        }

        $charges    = ! empty( $intent->charges->data ) ? $intent->charges->data : [];
        $charge_ids = wp_list_pluck( $charges, 'id' );

        return is_array( $charge_ids ) && isset( $charge_ids[0] ) ? $charge_ids[0] : false;
    }

    /**
     * Retrieves the processing for suborder.
     *
     * @since 3.6.1
     *
     * @param float    $processing_fee
     * @param WC_Order $suborder
     * @param WC_Order $order
     *
     * @return float
     */
    public static function get_fee_for_suborder( $processing_fee, $suborder, $order ) {
        $stripe_fee_for_vendor = $processing_fee * ( $suborder->get_total() / $order->get_total() );
        return number_format( $stripe_fee_for_vendor, 10 );
    }

    /**
     * Prepares a refund.
     *
     * @since 3.6.1
     *
     * @param array $args
     *
     * @return \WeDevs\DokanPro\Refund\Refund|\WP_Error
     */
    public static function prepare_refund( $args = [] ) {
        global $wpdb;

        $default_args = [
            'order_id'        => 0,
            'seller_id'       => 0,
            'refund_amount'   => 0,
            'refund_reason'   => '',
            'item_qtys'       => null,
            'item_totals'     => null,
            'item_tax_totals' => null,
            'restock_items'   => null,
            'date'            => current_time( 'mysql' ),
            'status'          => 0,
            'method'          => 'false',
        ];

        $args = wp_parse_args( $args, $default_args );

        $inserted = $wpdb->insert(
            $wpdb->dokan_refund,
            $args,
            [
                '%d',
                '%d',
                '%f',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
            ]
        );

        if ( $inserted !== 1 ) {
            return new \WP_Error( 'dokan_refund_create_error', __( 'Could not create new refund', 'dokan' ) );
        }

        $refund = dokan_pro()->refund->get( $wpdb->insert_id );

        return $refund;
    }

    /**
     * Processes a refund.
     *
     * @since 3.6.1
     *
     * @param \WC_Order                      $order
     * @param \WeDevs\DokanPro\Refund\Refund $dokan_refund
     * @param \MangoPay\Refund               $mangopay_refund
     *
     * @return boolean
     */
    public static function refund( $order, $dokan_refund, $stripe_refund ) {
        $order->add_order_note(
            sprintf(
                /* translators: 1) gateway title, 2) refund amount, 3) refund id, 4) refund reason */
                __( '[%1$s]. Refunded %2$s. Refund ID: %3$s. Reason - %4$s', 'dokan' ),
                Helper::get_gateway_title(),
                wc_price( $dokan_refund->get_refund_amount(), [ 'currency' => $order->get_currency() ] ),
                $stripe_refund->id,
                $dokan_refund->get_refund_reason()
            )
        );

        $refund_args = [
            Helper::get_gateway_id() => true,
        ];

        $transfer_id = OrderMeta::get_transfer_id( $order );
        if ( ! empty( $transfer_id ) ) {
            $refund_args['transfer_id'] = $transfer_id;
        }

        // Get balance transaction for refund amount, we need to deduct gateway charge from vendor refund amount
        $gateway_fee_refunded                = abs( Helper::format_balance_fee( $stripe_refund->balance_transaction ) );
        $refund_args['gateway_fee_refunded'] = ! empty( $gateway_fee_refunded ) ? $gateway_fee_refunded : 0;

        OrderMeta::update_refund_id( $order, $stripe_refund->id );
        OrderMeta::update_last_refund_id( $order, $stripe_refund->id );
        OrderMeta::save( $order );

        // Now try to approve the refund.
        $refund = $dokan_refund->approve( $refund_args );
        if ( is_wp_error( $refund ) ) {
            Helper::log( $refund->get_error_message(), 'Refund', 'error' );
        }
    }

    /**
     * Locks an order for payment intent processing for 5 minutes.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order  The order that is being paid.
     * @param object   $intent The intent that is being processed.
     *
     * @return bool            A flag that indicates whether the order is already locked.
     */
    public static function lock_processing( $order, $intent = null ) {
        $order_id   = $order->get_id();
        $transient  = 'dokan_stripe_express_processing_intent_' . $order_id;
        $processing = get_transient( $transient );

        // Block the process if the same intent is already being handled.
        if ( '-1' === $processing || ( isset( $intent->id ) && $processing === $intent->id ) ) {
            return true;
        }

        // Save the new intent as a transient, eventually overwriting another one.
        set_transient( $transient, empty( $intent ) ? '-1' : $intent->id, 5 * MINUTE_IN_SECONDS );

        return false;
    }

    /**
     * Unlocks an order for processing by payment intents.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order The order that is being unlocked.
     *
     * @return void
     */
    public static function unlock_processing( $order ) {
        $order_id = $order->get_id();
        delete_transient( 'dokan_stripe_express_processing_intent_' . $order_id );
    }

    /**
     * Retrieves transaction url of an order.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return string
     */
    public static function get_transaction_url( $order ) {
        $gateways = WC()->payment_gateways()->payment_gateways();
        $gateway  = $gateways[ Helper::get_gateway_id() ];

        if ( $gateway->testmode ) {
            $gateway->view_transaction_url = 'https://dashboard.stripe.com/test/payments/%s';
        } else {
            $gateway->view_transaction_url = 'https://dashboard.stripe.com/payments/%s';
        }

        return $gateway->get_transaction_url( $order );
    }

    /**
     * Retrieves order from charge id.
     *
     * @since 3.6.1
     *
     * @param string $charge_id
     *
     * @return WC_Order|false
     */
    public static function get_order_by_charge_id( $charge_id ) {
        global $wpdb;

        if ( empty( $charge_id ) ) {
            return false;
        }

        $order_id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT posts.ID
                FROM $wpdb->posts AS posts
                LEFT JOIN $wpdb->postmeta AS meta
                ON posts.ID = meta.post_id
                WHERE meta.meta_value = %s
                AND meta.meta_key = %s
                AND posts.ID IN (
                    SELECT DISTINCT post_id
                    FROM $wpdb->postmeta
                    WHERE meta_key = 'has_sub_order'
                    AND meta_value = '1'
                )",
                $charge_id,
                OrderMeta::transaction_id_key()
            )
        );

        if ( empty( $order_id ) ) {
            return false;
        }

        $order = wc_get_order( $order_id );
    }

    /**
     * Retrieves order by intent id.
     *
     * @since 3.6.1
     *
     * @param string  $intent_id
     * @param boolean $is_setup
     *
     * @return WC_Order|false
     */
    public static function get_order_by_intent_id( $intent_id, $is_setup = false ) {
        global $wpdb;

        $order_id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT DISTINCT ID
                FROM $wpdb->posts as posts
                LEFT JOIN $wpdb->postmeta as meta
                ON posts.ID = meta.post_id
                WHERE meta.meta_value = %s
                AND meta.meta_key = %s",
                $intent_id,
                OrderMeta::intent_id_key( $is_setup )
            )
        );

        if ( ! empty( $order_id ) ) {
            return wc_get_order( $order_id );
        }

        return false;
    }

    /**
     * Retrieves order by source id.
     *
     * @since 3.6.1
     *
     * @param string  $source_id
     *
     * @return WC_Order|false
     */
    public static function get_order_by_source_id( $source_id ) {
        global $wpdb;

        $order_id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT DISTINCT ID
                FROM $wpdb->posts as posts
                LEFT JOIN $wpdb->postmeta as meta
                ON posts.ID = meta.post_id
                WHERE meta.meta_value = %s
                AND meta.meta_key = %s",
                $source_id,
                OrderMeta::source_id_key()
            )
        );

        if ( ! empty( $order_id ) ) {
            return wc_get_order( $order_id );
        }

        return false;
    }

    /**
     * Retrieves stripe customer id from order.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return string
     */
    public static function get_stripe_customer_id_from_order( WC_Order $order ) {
        // Try to get it via the order first.
        $customer = OrderMeta::get_customer_id( $order );

        if ( empty( $customer ) ) {
            $customer = UserMeta::get_stripe_customer_id( $order->get_customer_id() );
        }

        return $customer;
    }

    /**
     * Retrieves user from order.
     *
     * @since 3.6.1
     *
     * @param \WC_Order $order
     *
     * @return WP_User
     */
    public static function get_user_from_order( \WC_Order $order ) {
        $user = $order->get_user();
        if ( false === $user ) {
            $user = wp_get_current_user();
        }
        return $user;
    }
}
