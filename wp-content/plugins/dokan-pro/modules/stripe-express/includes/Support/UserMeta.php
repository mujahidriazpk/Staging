<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\Support;

defined( 'ABSPATH' ) || exit; // Exit if called directly

/**
 * Helper class for Stripe gateway.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\Support
 */
class UserMeta {

    /**
     * Generates meta key with prefix.
     *
     * @since 3.6.1
     *
     * @return string
     */
    public static function key( $key ) {
        return '_' . Helper::get_gateway_id() . '_' . $key;
    }

    /**
     * Generates meta key for stripe account id.
     *
     * @since 3.6.1
     *
     * @return string
     */
    public static function stripe_account_id_key() {
        $key = 'account_id';

        if ( Settings::is_test_mode() ) {
            $key = "test_$key";
        }

        return self::key( $key );
    }

    /**
     * Retrieves stripe account ID of a user.
     *
     * @since 3.6.1
     *
     * @param int|string $user_id
     *
     * @return string|false
     */
    public static function get_stripe_account_id( $user_id ) {
        return get_user_meta( $user_id, self::stripe_account_id_key(), true );
    }

    /**
     * Updates stripe account id for a user.
     *
     * @since 3.6.1
     *
     * @param int|string $user_id
     * @param string     $account_id
     *
     * @return int|boolean
     */
    public static function update_stripe_account_id( $user_id, $account_id ) {
        $meta_key = self::stripe_account_id_key();
        delete_user_meta( $user_id, "{$meta_key}_trash" );
        return update_user_meta( $user_id, $meta_key, $account_id );
    }

    /**
     * Deletes stripe account id of a user
     *
     * @since 3.6.1
     *
     * @param int|string $user_id ID of the user
     * @param boolean    $force   Default `false` and store the current id in trash, If `true`, no trash will be maintained
     *
     * @return boolean
     */
    public static function delete_stripe_account_id( $user_id, $force = false ) {
        $meta_key = self::stripe_account_id_key();

        if ( ! $force ) {
            $account_id = get_user_meta( $user_id, $meta_key, true );
            update_user_meta( $user_id, "{$meta_key}_trash", $account_id );
        } else {
            delete_user_meta( $user_id, "{$meta_key}_trash" );
        }

        return delete_user_meta( $user_id, $meta_key );
    }

    /**
     * Retrieves stripe account id that was previously trashed.
     *
     * @since 3.6.1
     *
     * @param int|string $user_id
     *
     * @return string|false
     */
    public static function get_trashed_stripe_account_id( $user_id ) {
        return get_user_meta( $user_id, self::stripe_account_id_key() . '_trash', true );
    }

    /**
     * Retrieves stripe customer id meta key.
     *
     * @since 3.6.1
     *
     * @return string
     */
    public static function stripe_customer_id_key() {
        return self::key( 'customer_id' );
    }

    /**
     * Retrieves stripe customer id.
     *
     * @since 3.6.1
     *
     * @param int|string $user_id
     *
     * @return string|false
     */
    public static function get_stripe_customer_id( $user_id ) {
        return get_user_option( self::stripe_customer_id_key(), $user_id );
    }

    /**
     * Updates stripe customer id.
     *
     * @since 3.6.1
     *
     * @param int|string $user_id
     * @param string     $stripe_id
     *
     * @return string|boolean
     */
    public static function update_stripe_customer_id( $user_id, $stripe_id ) {
        return update_user_option( $user_id, self::stripe_customer_id_key(), $stripe_id );
    }

    /**
     * Deletes stripe cutomer id.
     *
     * @since 3.6.1
     *
     * @param int|string $user_id
     *
     * @return boolean
     */
    public static function delete_stripe_customer_id( $user_id ) {
        return delete_user_option( $user_id, self::stripe_customer_id_key() );
    }
}
