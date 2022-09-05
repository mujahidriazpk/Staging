<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\Support;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use WC_Order;

/**
 * Helper class for Stripe gateway.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\Support
 */
class OrderMeta {

    /**
     * Generates meta key with prefix.
     *
     * @since 3.6.1
     *
     * @param string $key
     *
     * @return string
     */
    public static function key( $key ) {
        return '_' . Helper::get_gateway_id() . '_' . $key;
    }

    /**
     * Saves the order.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return void
     */
    public static function save( WC_Order $order ) {
        if ( is_callable( [ $order, 'save' ] ) ) {
            $order->save();
        }
    }

    /**
     * Updates the status of charge captured.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param string   $is_captured
     *
     * @return void
     */
    public static function update_charge_captured( WC_Order $order, $is_captured = 'yes' ) {
        $order->update_meta_data( self::key( 'charge_captured' ), $is_captured );
    }

    /**
     * Checks whether stripe charge is captured or not.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return boolean
     */
    public static function is_charge_captured( WC_Order $order ) {
        return 'yes' === $order->get_meta( self::key( 'charge_captured' ), true );
    }

    /**
     * Retrieves transaction id key.
     *
     * @since 3.6.1
     *
     * @return string
     */
    public static function transaction_id_key() {
        return self::key( 'transaction_id' );
    }

    /**
     * Updates the transaction id of an order
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param string   $trn_id
     *
     * @return void
     */
    public static function update_transaction_id( WC_Order $order, $trn_id ) {
        $order->set_transaction_id( $trn_id );
        $order->update_meta_data( self::transaction_id_key(), $trn_id );
    }

    /**
     * Retrieves transaction id of an order.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return boolean
     */
    public static function get_transaction_id( WC_Order $order ) {
        return $order->get_meta( self::transaction_id_key(), true );
    }

    /**
     * Retrieves transfer id key.
     *
     * @since 3.6.1
     *
     * @return string
     */
    public static function transfer_id_key() {
        return self::key( 'transfer_id' );
    }

    /**
     * Updates the transfer id of an order.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param string   $trn_id
     *
     * @return void
     */
    public static function update_transfer_id( WC_Order $order, $trn_id ) {
        $order->update_meta_data( self::transfer_id_key(), $trn_id );
    }

    /**
     * Retrieves transfer id of an order.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return boolean
     */
    public static function get_transfer_id( WC_Order $order ) {
        return $order->get_meta( self::transfer_id_key(), true );
    }

    /**
     * Retrieves payment/setup intent id key.
     *
     * @since 3.6.1
     *
     * @param boolean $is_setup
     *
     * @return string
     */
    public static function intent_id_key( $is_setup = false ) {
        $intent_type = $is_setup ? 'setup' : 'payment';
        return self::key( "{$intent_type}_intent_id" );
    }

    /**
     * Adds payment intent id and order note to order if payment intent is not already saved.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param string   $payment_intent_id
     *
     * @return void
     */
    public static function add_payment_intent( WC_Order $order, $payment_intent_id ) {
        $intent_key    = self::intent_id_key();
        $old_intent_id = $order->get_meta( $intent_key, true );

        if ( $old_intent_id === $payment_intent_id ) {
            return;
        }

        $order->add_order_note(
            sprintf(
                /* translators: $1%s payment intent ID */
                __( '[%1$s] Payment Intent ID: %2$s', 'dokan' ),
                Helper::get_gateway_title(), $payment_intent_id
            )
        );

        $order->update_meta_data( $intent_key, $payment_intent_id );
    }

    /**
     * Retrieves payment intent id.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return mixed
     */
    public static function get_payment_intent( WC_Order $order ) {
        return $order->get_meta( self::intent_id_key(), true );
    }

    /**
     * Adds setup intent to order.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param string $intent_id
     *
     * @return void
     */
    public static function add_setup_intent( WC_Order $order, $intent_id ) {
        $order->update_meta_data( self::intent_id_key( true ), $intent_id );
    }

    /**
     * Retrieves setup intent id.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return string
     */
    public static function get_setup_intent( WC_Order $order ) {
        return $order->get_meta( self::intent_id_key( true ), true );
    }

    /**
     * Retrieves source id key.
     *
     * @since 3.6.1
     *
     * @return string
     */
    public static function source_id_key() {
        return self::key( 'source_id' );
    }

    /**
     * Retrieves stripe source id.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return mixed
     */
    public static function get_source_id( WC_Order $order ) {
        return $order->get_meta( self::source_id_key(), true );
    }

    /**
     * Updates stripe source id.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param string $source_id
     *
     * @return void
     */
    public static function update_source_id( WC_Order $order, $source_id ) {
        $order->update_meta_data( self::source_id_key(), $source_id );
    }

    /**
     * Retrieves stripe customer id.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return mixed
     */
    public static function get_customer_id( WC_Order $order ) {
        return $order->get_meta( self::key( 'customer_id' ), true );
    }

    /**
     * Updates stripe customer id.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param string   $customer_id
     *
     * @return void
     */
    public static function update_customer_id( WC_Order $order, $customer_id ) {
        $order->update_meta_data( self::key( 'customer_id' ), $customer_id );
    }

    /**
     * Deletes stripe customer id.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return void
     */
    public static function delete_customer_id( WC_Order $order ) {
        $order->delete_meta_data( self::key( 'customer_id' ) );
    }

    /**
     * Retrieves stripe card id.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return mixed
     */
    public static function get_card_id( WC_Order $order ) {
        return $order->get_meta( self::key( 'card_id' ), true );
    }

    /**
     * Updates stripe card id.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param string $card_id
     *
     * @return void
     */
    public static function update_card_id( WC_Order $order, $card_id ) {
        $order->update_meta_data( self::key( 'card_id' ), $card_id );
    }

    /**
     * Retrieves stripe payment type
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return mixed
     */
    public static function get_payment_type( WC_Order $order ) {
        $payment_type = $order->get_meta( self::key( 'payment_type' ), true );
        if ( ! empty( $payment_type ) ) {
            return $payment_type;
        }

        if ( $order->get_parent_id() ) {
            return self::get_payment_type( wc_get_order( $order->get_parent_id() ) );
        }

        return 'card';
    }

    /**
     * Updates stripe payment type.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param string   $payment_type
     *
     * @return void
     */
    public static function update_payment_type( WC_Order $order, $payment_type ) {
        $order->update_meta_data( self::key( 'payment_type' ), $payment_type );
    }

    /**
     * Check whether order redirect is processed.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return boolean
     */
    public static function is_redirect_processed( WC_Order $order ) {
        return $order->get_meta( self::key( 'redirect_processed' ), true );
    }

    /**
     * Updates the flag if order redirect is processed.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param string   $is_processed
     *
     * @return void
     */
    public static function update_redirect_processed( WC_Order $order, $is_processed = 'yes' ) {
        $order->update_meta_data( self::key( 'redirect_processed' ), $is_processed );
    }

    /**
     * Retrieves disbursement mode key.
     *
     * @since 3.6.1
     *
     * @return string
     */
    public static function disburse_mode_key() {
        return self::key( 'disburse_mode' );
    }

    /**
     * Updates the disbursement mode of an order.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param string   $disburse_mode
     *
     * @return void
     */
    public static function update_disburse_mode( WC_Order $order, $disburse_mode ) {
        $order->update_meta_data( self::disburse_mode_key(), $disburse_mode );
    }

    /**
     * Retrieves the disbursement mode of an order.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return void
     */
    public static function get_disburse_mode( WC_Order $order ) {
        return $order->get_meta( self::disburse_mode_key(), true );
    }

    /**
     * Gets the Stripe fee for order.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return string
     */
    public static function get_stripe_fee( WC_Order $order ) {
        return $order->get_meta( self::key( 'fee' ), true );
    }

    /**
     * Updates the Stripe fee for order.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param float    $amount
     *
     * @return void
     */
    public static function update_stripe_fee( WC_Order $order, $amount = 0.0 ) {
        $order->update_meta_data( self::key( 'fee' ), $amount );
    }

    /**
     * Retrieves withdraw data for a parent order.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return array
     */
    public static function get_withdraw_data( WC_Order $order ) {
        return $order->get_meta( self::key( 'withdraw_data' ), true );
    }

    /**
     * Updates withdraw data for a parent order.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param string   $withdraw_data
     *
     * @return void
     */
    public static function update_withdraw_data( WC_Order $order, $withdraw_data ) {
        $order->update_meta_data( self::key( 'withdraw_data' ), $withdraw_data );
    }

    /**
     * Checks if withdraw balance added for an order.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return boolean
     */
    public static function is_withdraw_balance_added( WC_Order $order ) {
        return 'yes' === $order->get_meta( self::key( 'withdraw_balance_added' ), true );
    }

    /**
     * Updates withdraw balance added flag.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param string   $is_added
     *
     * @return void
     */
    public static function update_if_withdraw_balance_added( $order, $is_added = 'yes' ) {
        $order->update_meta_data( self::key( 'withdraw_balance_added' ), $is_added );
    }

    /**
     * Retrieves refund ids for an order.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return array
     */
    public static function get_refund_ids( WC_Order $order ) {
        return (array) $order->get_meta( self::key( 'refund_ids' ), true );
    }

    /**
     * Updates refund ids for an order.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param string   $refund_id
     *
     * @return void
     */
    public static function update_refund_id( WC_Order $order, $refund_id ) {
        $refund_ids = self::get_refund_ids( $order );

        if ( is_array( $refund_ids ) ) {
            $refund_ids[] = $refund_id;
        } else {
            $refund_ids = [ $refund_id ];
        }

        $order->update_meta_data( self::key( 'refund_ids' ), $refund_ids );
    }

    /**
     * Retrieves last refund id for an order.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return array
     */
    public static function get_last_refund_id( WC_Order $order ) {
        return (array) $order->get_meta( self::key( 'last_refund_id' ), true );
    }

    /**
     * Updates last refund id for an order.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param string   $refund_id
     *
     * @return void
     */
    public static function update_last_refund_id( WC_Order $order, $refund_id ) {
        $order->update_meta_data( self::key( 'last_refund_id' ), $refund_id );
    }

    /**
     * Updates gateway fee for dokan.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param float|string $fee
     *
     * @return void
     */
    public static function update_dokan_gateway_fee( WC_Order $order, $fee ) {
        $order->update_meta_data( 'dokan_gateway_fee', $fee );
    }

    /**
     * Updates gateway fee for dokan.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return string|false
     */
    public static function get_dokan_gateway_fee( WC_Order $order ) {
        return $order->get_meta( 'dokan_gateway_fee', true );
    }

    /**
     * Updates who paid the gateway fee for dokan.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param string   $paid_by 'seller' or 'admin'
     *
     * @return void
     */
    public static function update_gateway_fee_paid_by( WC_Order $order, $paid_by = 'seller' ) {
        $paid_by = 'seller' === $paid_by ? $paid_by : 'admin';
        $order->update_meta_data( 'dokan_gateway_fee_paid_by', $paid_by );
    }

    /**
     * Retrieves who paid the gateway fee for dokan.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return string|false
     */
    public static function get_gateway_fee_paid_by( WC_Order $order ) {
        return $order->get_meta( 'dokan_gateway_fee_paid_by', true );
    }

    /**
     * Updates status final.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param string   $status
     *
     * @return void
     */
    public static function make_status_final( WC_Order $order ) {
        $order->update_meta_data( self::key( 'status_final' ), true );
    }

    /**
     * Retrieves status final.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return string|false
     */
    public static function get_status_final( WC_Order $order ) {
        return $order->get_meta( self::key( 'status_final' ), true );
    }

    /**
     * Deletes status final.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param string   $status
     *
     * @return void
     */
    public static function undo_status_final( WC_Order $order ) {
        $order->delete_meta_data( self::key( 'status_final' ) );
    }

    /**
     * Updates status before hold.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     * @param string   $status
     *
     * @return void
     */
    public static function update_status_before_hold( WC_Order $order, $status ) {
        $order->update_meta_data( self::key( 'status_before_hold' ), $status );
    }

    /**
     * Retrieves status before hold.
     *
     * @since 3.6.1
     *
     * @param WC_Order $order
     *
     * @return string|false
     */
    public static function get_status_before_hold( WC_Order $order ) {
        return $order->get_meta( self::key( 'status_before_hold' ), true );
    }
}
