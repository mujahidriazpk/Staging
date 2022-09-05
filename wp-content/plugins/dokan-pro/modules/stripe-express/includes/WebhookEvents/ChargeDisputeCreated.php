<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\WebhookEvents;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;
use WeDevs\DokanPro\Modules\StripeExpress\Processors\Order;
use WeDevs\DokanPro\Modules\StripeExpress\Support\OrderMeta;
use WeDevs\DokanPro\Modules\StripeExpress\Utilities\Abstracts\WebhookEvent;

/**
 * Class to handle `charge.disput.created` webhook.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\WebhookEvents
 */
class ChargeDisputeCreated extends WebhookEvent {

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
     * @param object $disput
     *
     * @return void
     */
    public function handle( $disput ) {
        $order = Order::get_order_by_charge_id( $disput->charge );

        if ( ! $order ) {
            Helper::log( 'Could not find order via charge ID: ' . $disput->charge );
            return;
        }

        OrderMeta::update_status_before_hold( $order, $order->get_status() );

        $message = sprintf(
            /* translators: 1) gateway title, 2) opening anchor tag with url, 3) closing anchor tag */
            __( '[%1$s] A dispute was created for this order. Response is needed. Please go to your %2$sStripe Dashboard%3$s to review this dispute.', 'dokan' ),
            Helper::get_gateway_title(),
            sprintf( '<a href="%s" title="Stripe Dashboard" target="_blank">', Order::get_transaction_url( $order ) ),
            '</a>'
        );

        if ( empty( OrderMeta::get_status_final( $order ) ) ) {
            $order->update_status( 'on-hold', $message );
        } else {
            $order->add_order_note( $message );
        }

        OrderMeta::save( $order );
    }
}
