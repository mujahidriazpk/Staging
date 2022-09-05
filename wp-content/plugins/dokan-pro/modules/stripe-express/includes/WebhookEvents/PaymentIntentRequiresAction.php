<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\WebhookEvents;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;
use WeDevs\DokanPro\Modules\StripeExpress\Processors\Order;
use WeDevs\DokanPro\Modules\StripeExpress\Support\OrderMeta;
use WeDevs\DokanPro\Modules\StripeExpress\Utilities\Abstracts\WebhookEvent;

/**
 * Class to handle `payment_intent.requires_action` webhook.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\WebhookEvents
 */
class PaymentIntentRequiresAction extends WebhookEvent {

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
        $order = Order::get_order_by_intent_id( $intent->id );

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

        if ( in_array( OrderMeta::get_payment_type( $order ), [ 'boleto', 'oxxo' ], true ) ) {
            $order->update_status( 'on-hold', __( 'Awaiting payment.', 'dokan' ) );
            wc_reduce_stock_levels( $order->get_id() );
        }

        Order::unlock_processing( $order );
    }
}
