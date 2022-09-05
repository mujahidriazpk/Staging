<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\WebhookEvents;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use WC_Payment_Tokens;
use WeDevs\Dokan\Exceptions\DokanException;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;
use WeDevs\DokanPro\Modules\StripeExpress\Processors\Order;
use WeDevs\DokanPro\Modules\StripeExpress\Processors\Payment;
use WeDevs\DokanPro\Modules\StripeExpress\Utilities\Traits\PaymentUtils;
use WeDevs\DokanPro\Modules\StripeExpress\Utilities\Abstracts\WebhookEvent;

/**
 * Class to handle `source.chargeable` webhook.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\WebhookEvents
 */
class SourceChargeable extends WebhookEvent {

    use PaymentUtils;

    /**
     * Class constructor.
     *
     * @since 3.6.1
     *
     * @param object $event
     */
    public function __construct( $event ) {
        $this->set( $event );
    }

    /**
     * Handles the event.
     *
     * @since 3.6.1
     *
     * @param object $source
     *
     * @return void
     */
    public function handle( $source ) {
        // The following 3 payment methods are synchronous so does not need to be handle via webhook.
        if ( 'card' === $source->type || 'sepa_debit' === $source->type || 'three_d_secure' === $source->type ) {
            return;
        }

        $order = Order::get_order_by_source_id( $source->id );
        if ( ! $order ) {
            Helper::log( 'Could not find order via source ID: ' . $source->id );
            return;
        }

        if ( $order->has_status( [ 'processing', 'completed' ] ) ) {
            return;
        }

        if ( $order->has_status( 'on-hold' ) && 'receiver' !== $source->flow ) {
            return;
        }

        try {
            // Result from Stripe API request.
            $response = null;

            // This will throw exception if not valid.
            $this->validate_minimum_order_amount( $order );

            Helper::log( "Info: (Webhook) Begin processing payment for order {$order->get_id()} for the amount of {$order->get_total()}" );

            $prepared_source = Order::prepare_source( $order );
            $response        = Payment::create_charge( $order, $prepared_source );

            if ( ! empty( $response->error ) ) {
                // Customer param wrong? The user may have been deleted on stripe's end. Remove customer_id. Can be retried without.
                if ( Helper::is_no_such_customer_error( $response->error ) ) {
                    delete_user_option( $order->get_customer_id(), '_stripe_customer_id' );
                    $order->delete_meta_data( '_stripe_customer_id' );
                    $order->save();
                }

                if ( Helper::is_no_such_token_error( $response->error ) && $prepared_source->token_id ) {
                    // Source param wrong? The CARD may have been deleted on stripe's end. Remove token and show message.
                    $wc_token = WC_Payment_Tokens::get( $prepared_source->token_id );
                    $wc_token->delete();
                    $localized_message = __( 'This card is no longer available and has been removed.', 'dokan' );
                    $order->add_order_note( $localized_message );
                    throw new DokanException( print_r( $response, true ), $localized_message );
                }

                $localized_messages = Helper::get_payment_message();

                if ( 'card_error' === $response->error->type ) {
                    $localized_message = isset( $localized_messages[ $response->error->code ] ) ? $localized_messages[ $response->error->code ] : $response->error->message;
                } else {
                    $localized_message = isset( $localized_messages[ $response->error->type ] ) ? $localized_messages[ $response->error->type ] : $response->error->message;
                }

                $order->add_order_note( $localized_message );

                throw new DokanException( print_r( $response, true ), $localized_message );
            }

            Payment::process_response( $response, $order );
        } catch ( DokanException $e ) {
            Helper::log( 'Error: ' . $e->getMessage() );
        }
    }
}
