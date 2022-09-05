<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\Utilities\Traits;

use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;

defined( 'ABSPATH' ) || exit; // Exit if called directly

/**
 * Trait for subscription utility functions.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\Utilities\Traits
 */
trait SubscriptionUtils {

    /**
     * Checks if subscriptions are enabled on the site.
     *
     * @since 3.6.1
     *
     * @return bool Whether subscriptions is enabled or not.
     */
    public function is_subscriptions_enabled() {
        return class_exists( 'WC_Subscriptions' ) && version_compare( \WC_Subscriptions::$version, '2.2.0', '>=' );
    }

    /**
     * Checks if an order has subscription.
     *
     * @since 3.6.1
     *
     * @param int|string $order_id
     *
     * @return boolean
     */
    public function has_subscription( $order_id ) {
        return Helper::has_subscription( $order_id );
    }

    /**
     * Returns whether this user is changing the payment method for a subscription.
     *
     * @since 3.6.1
     *
     * @return bool
     */
    public function is_changing_payment_method_for_subscription() {
        if ( isset( $_GET['change_payment_method'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
            return wcs_is_subscription( wc_clean( wp_unslash( $_GET['change_payment_method'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
        }
        return false;
    }

    /**
     * Returns boolean value indicating whether payment for an order will be recurring,
     * as opposed to single.
     *
     * @since 3.6.1
     *
     * @param int $order_id ID for corresponding WC_Order in process.
     *
     * @return bool
     */
    public function is_payment_recurring( $order_id ) {
        if ( ! $this->is_subscriptions_enabled() ) {
            return false;
        }
        return $this->is_changing_payment_method_for_subscription() || $this->has_subscription( $order_id );
    }

    /**
     * Returns a boolean value indicating whether the save payment checkbox should be
     * displayed during checkout.
     *
     * Returns `false` if the cart currently has a subscriptions or if the request has a
     * `change_payment_method` GET parameter. Returns the value in `$display` otherwise.
     *
     * @since 3.6.1
     *
     * @param bool $display Bool indicating whether to show the save payment checkbox in the absence of subscriptions.
     *
     * @return bool Indicates whether the save payment method checkbox should be displayed or not.
     */
    public function display_save_payment_method_checkbox( $display ) {
        if ( \WC_Subscriptions_Cart::cart_contains_subscription() || $this->is_changing_payment_method_for_subscription() ) {
            return false;
        }
        // Only render the "Save payment method" checkbox if there are no subscription products in the cart.
        return $display;
    }

    /**
     * Returns boolean on whether current WC_Cart or WC_Subscriptions_Cart
     * contains a subscription or subscription renewal item
     *
     * @since 3.6.1
     *
     * @return bool
     */
    public function is_subscription_item_in_cart() {
        if ( $this->is_subscriptions_enabled() ) {
            return \WC_Subscriptions_Cart::cart_contains_subscription() || $this->cart_contains_renewal();
        }
        return false;
    }

    /**
     * Checks the cart to see if it contains a subscription product renewal.
     *
     * @since 3.6.1
     *
     * @return mixed The cart item containing the renewal as an array, else false.
     */
    public function cart_contains_renewal() {
        if ( ! function_exists( 'wcs_cart_contains_renewal' ) ) {
            return false;
        }
        return wcs_cart_contains_renewal();
    }
}
