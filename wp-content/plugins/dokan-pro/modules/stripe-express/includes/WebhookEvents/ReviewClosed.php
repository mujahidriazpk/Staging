<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\WebhookEvents;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;
use WeDevs\DokanPro\Modules\StripeExpress\Processors\Order;
use WeDevs\DokanPro\Modules\StripeExpress\Support\OrderMeta;
use WeDevs\DokanPro\Modules\StripeExpress\Utilities\Abstracts\WebhookEvent;

/**
 * Class to handle `review.closed` webhook.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\WebhookEvents
 */
class ReviewClosed extends WebhookEvent {

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
     * @param object $payload
     *
     * @return void
     */
    public function handle( $payload ) {
        if ( isset( $payload->payment_intent ) ) {
            $order = Order::get_order_by_intent_id( $payload->payment_intent );

            if ( ! $order ) {
                Helper::log( 'Could not find order via intent ID: ' . $payload->payment_intent );
                return;
            }
        } else {
            $order = Order::get_order_by_charge_id( $payload->charge );

            if ( ! $order ) {
                Helper::log( 'Could not find order via charge ID: ' . $payload->charge );
                return;
            }
        }

        $message = sprintf(
            /* translators: 1) gateway title, 2) reason */
            __( '[%1$s] The opened review for this order is now closed. Reason: %2$s', 'dokan' ),
            Helper::get_gateway_title(),
            $payload->reason
        );

        if ( ! OrderMeta::get_status_final( $order ) ) {
            $before_hold_status = OrderMeta::get_status_before_hold( $order );
            $before_hold_status = $before_hold_status ? $before_hold_status : 'processing';
            $order->update_status( $before_hold_status, $message );
        } else {
            $order->add_order_note( $message );
        }
    }
}
