<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\WebhookEvents;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;
use WeDevs\DokanPro\Modules\StripeExpress\Processors\Order;
use WeDevs\DokanPro\Modules\StripeExpress\Processors\Payment;
use WeDevs\DokanPro\Modules\StripeExpress\Utilities\Abstracts\WebhookEvent;

/**
 * Class to handle `payment_intent.succeeded` webhook.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\WebhookEvents
 */
class PaymentIntentSucceeded extends WebhookEvent {

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

        $charge = end( $intent->charges->data );

        do_action( 'dokan_stripe_express_process_payment', $charge, $order );

        // Process valid response.
        Payment::process_response( $charge, $order );

        Order::unlock_processing( $order );
    }
}
