<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\WebhookEvents;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;
use WeDevs\DokanPro\Modules\StripeExpress\Processors\Order;
use WeDevs\DokanPro\Modules\StripeExpress\Support\OrderMeta;
use WeDevs\DokanPro\Modules\StripeExpress\Utilities\Abstracts\WebhookEvent;

/**
 * Class to handle `setup_intent.setup_failed` webhook.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\WebhookEvents
 */
class SetupIntentSetupFailed extends WebhookEvent {

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
     * @param object $intent
     *
     * @return void
     */
    public function handle( $intent ) {
        $order = Order::get_order_by_intent_id( $intent->id, true );

        if ( ! $order ) {
            Helper::log( 'Could not find order via intent ID: ' . $intent->id );
            return;
        }

        if ( ! $order->has_status( [ 'pending', 'failed' ] ) ) {
            return;
        }

        if ( Order::lock_processing( $order, $intent ) ) {
            return;
        }

        $error_message = $intent->last_setup_error ? $intent->last_setup_error->message : '';

        $message = sprintf(
            /* translators: 1) gateway title, 2) The error message that was received from Stripe. */
            __( '[%1$s] SCA authentication failed. Reason: %2$s', 'dokan' ),
            Helper::get_gateway_title(),
            $error_message
        );

        if ( empty( OrderMeta::get_status_final( $order ) ) ) {
            $order->update_status( 'failed', $message );
        } else {
            $order->add_order_note( $message );
        }

        Order::unlock_processing( $order );
    }
}
