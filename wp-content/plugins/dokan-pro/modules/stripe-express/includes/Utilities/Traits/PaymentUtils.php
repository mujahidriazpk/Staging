<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\Utilities\Traits;

use WeDevs\Dokan\Exceptions\DokanException;

/**
 * Trait to manage payment utilities.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\Utilities\Traits
 */
trait PaymentUtils {

    /**
     * Validates that the order meets the minimum order amount
     * set by Stripe.
     *
     * @since 3.6.1
     *
     * @param int|string $order_id
     *
     * @return void
     * @throws DokanException
     */
    public function validate_minimum_order_amount( $order_id ) {
        $order = wc_get_order( $order_id );
        if ( ! $order ) {
            throw new DokanException(
                'dokan-not-valid-order',
                /* translators: order id */
                sprintf( __( 'The order %s is not valid', 'dokan' ), $order_id )
            );
        }

        $minimum_amount = $this->get_minimum_amount();
        if ( $order->get_total() < $minimum_amount ) {
            throw new DokanException(
                'dokan-minimum-order-validation-failed',
                sprintf(
                    /* translators: order id */
                    __( 'Sorry, the minimum allowed order total is %1$s to use this payment method.', 'dokan' ),
                    wc_price( $minimum_amount )
                )
            );
        }
    }

    /**
     * Retrieves minimum amount for an order based on the currency.
     *
     * @since 3.6.1
     *
     * @param string $currency
     *
     * @return float
     */
    public function get_minimum_amount( $currency = '' ) {
        if ( empty( $currency ) ) {
            $currency = get_woocommerce_currency();
        }

        switch ( $currency ) {
            case 'GBP':
                $minimum_amount = 0.30;
                break;
            case 'AUD':
            case 'BRL':
            case 'CAD':
            case 'CHF':
            case 'EUR':
            case 'INR':
            case 'NZD':
            case 'SGD':
            case 'USD':
                $minimum_amount = 0.50;
                break;
            case 'BGN':
                $minimum_amount = 1.00;
                break;
            case 'AED':
            case 'MYR':
            case 'PLN':
            case 'RON':
                $minimum_amount = 2.00;
                break;
            case 'DKK':
                $minimum_amount = 2.50;
                break;
            case 'NOK':
            case 'SEK':
                $minimum_amount = 3.00;
                break;
            case 'HKD':
                $minimum_amount = 4.00;
                break;
            case 'MXN':
                $minimum_amount = 10.00;
                break;
            case 'CZK':
                $minimum_amount = 15.00;
                break;
            case 'JPY':
                $minimum_amount = 50.00;
                break;
            case 'HUF':
                $minimum_amount = 175.00;
                break;
            default:
                $minimum_amount = 0.50;
        }

        return apply_filters( 'dokan_stripe_express_minimum_order_amount', $minimum_amount, $currency );
    }

    /**
     * Checks if request is the original to prevent double processing
     * on WC side. The original-request header and request-id header
     * needs to be the same to mean its the original request.
     *
     * @since 3.6.1
     *
     * @param array $headers
     *
     * @return boolean
     */
    public function is_original_request( $headers ) {
        if ( $headers['original-request'] === $headers['request-id'] ) {
            return true;
        }

        return false;
    }
}
