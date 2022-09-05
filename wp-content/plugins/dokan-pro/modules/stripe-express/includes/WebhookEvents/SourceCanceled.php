<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\WebhookEvents;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;
use WeDevs\DokanPro\Modules\StripeExpress\Processors\Order;
use WeDevs\DokanPro\Modules\StripeExpress\Support\OrderMeta;
use WeDevs\DokanPro\Modules\StripeExpress\Utilities\Abstracts\WebhookEvent;

/**
 * Class to handle `source.canceled` webhook.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\WebhookEvents
 */
class SourceCanceled extends WebhookEvent {

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
        $order = Order::get_order_by_charge_id( $source->id );
        if ( ! $order ) {
            $order = Order::get_order_by_source_id( $source->id );
            if ( ! $order ) {
                Helper::log( 'Could not find order via charge/source ID: ' . $source->id );
                return;
            }
        }

        if ( Helper::get_gateway_id() !== $order->get_payment_method() ) {
            return;
        }

        /* translators: gateway title */
        $message = sprintf( __( '[%s] This payment was cancelled.', 'dokan' ), Helper::get_gateway_title() );
        if ( ! $order->has_status( 'cancelled' ) && ! OrderMeta::get_status_final( $order ) ) {
            $order->update_status( 'cancelled', $message );
        } else {
            $order->add_order_note( $message );
        }
    }
}
