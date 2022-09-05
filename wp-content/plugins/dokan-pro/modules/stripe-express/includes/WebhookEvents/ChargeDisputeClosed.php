<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\WebhookEvents;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;
use WeDevs\DokanPro\Modules\StripeExpress\Processors\Order;
use WeDevs\DokanPro\Modules\StripeExpress\Support\OrderMeta;
use WeDevs\DokanPro\Modules\StripeExpress\Utilities\Abstracts\WebhookEvent;

/**
 * Class to handle `charge.disput.closed` webhook.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\WebhookEvents
 */
class ChargeDisputeClosed extends WebhookEvent {

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

        $order_status = OrderMeta::get_status_before_hold( $order );

        switch ( $disput->status ) {
            case 'won':
                $message = __( 'The dispute was resolved in your favor.', 'dokan' );
                break;

            case 'lost':
                $message      = __( 'The dispute was lost or accepted.', 'dokan' );
                $order_status = 'failed';
                break;

            case 'warning_closed':
                $message = __( 'The inquiry or retrieval was closed.', 'dokan' );
                break;

            default:
                return;
        }

        // Mark final so that order status is not overridden by out-of-sequence events.
        OrderMeta::make_status_final( $order );

        // Fail order if dispute is lost, or else revert to pre-dispute status.
        $order->update_status( $order_status, $message );
        OrderMeta::save( $order );
    }
}
