<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\Processors;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use WP_Error;
use WeDevs\Dokan\Exceptions\DokanException;
use WeDevs\DokanPro\Modules\StripeExpress\Api\Account;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;
use WeDevs\DokanPro\Modules\StripeExpress\Support\UserMeta;

/**
 * Class for processing orders.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\Processors
 */
class User {

    /**
     * Onboards user for a stripe express account.
     *
     * @since 3.6.1
     *
     * @param int|string $user_id
     * @param array      $args
     *
     * @return object|WP_Error
     */
    public static function onboard( $user_id, $args = [] ) {
        $user = get_userdata( $user_id );
        if ( ! $user ) {
            return new WP_Error( 'dokan-stripe-express-invalid-user', __( 'No valid user found', 'dokan' ) );
        }

        $account_link_data = [];

        // Check if the vendor was previously registered.
        $trashed_account_id = UserMeta::get_trashed_stripe_account_id( $user->ID );
        if ( ! empty( $trashed_account_id ) ) {
            UserMeta::update_stripe_account_id( $user_id, $trashed_account_id );
        }

        // Check if an account id already exists.
        $account_id = UserMeta::get_stripe_account_id( $user->ID );

        try {
            if ( empty( $account_id ) ) {
                $account_data = [
                    'email' => $user->user_email,
                ];

                $response = Account::create( $account_data );

                UserMeta::update_stripe_account_id( $user_id, $response->id );
            } else {
                $response = Account::get( $account_id );
            }

            $account_id   = $response->id;
            $redirect_url = Helper::get_payment_settings_url();
            if ( ! empty( $args['url_args'] ) && false !== strpos( $args['url_args'], 'page=dokan-seller-setup' ) ) {
                $redirect_url = add_query_arg(
                    [
                        'page' => 'dokan-seller-setup',
                        'step' => 'payment',
                    ],
                    home_url( '/' )
                );
            }

            $account_link_data['refresh_url'] = $redirect_url;
            $account_link_data['return_url']  = add_query_arg(
                [
                    'action'    => 'stripe_express_onboarding',
                    'seller_id' => $user->ID,
                ],
                $redirect_url
            );

            return Account::create_onboarding_link( $account_id, $account_link_data );
        } catch ( DokanException $e ) {
            if ( Account::ACCOUNT_INVALID === $e->get_error_code() ) {
                UserMeta::delete_stripe_account_id( $user_id, true );
                return self::onboard( $user_id );
            }

            return new WP_Error(
                'dokan-stripe-express-onboard-error',
                __( 'Something went wrong! Please try again later.', 'dokan' )
            );
        }
    }

    /**
     * Gets atripe account data of an user.
     *
     * @since 3.6.1
     *
     * @param int|string $user_id
     * @param array      $args
     *
     * @return object|false
     */
    public static function get_data( $user_id, $args = [] ) {
        try {
            $account_id = UserMeta::get_stripe_account_id( $user_id );
            if ( empty( $account_id ) ) {
                return false;
            }

            return Account::get( $account_id, $args );
        } catch ( DokanException $e ) {
            return false;
        }
    }

    /**
     * Retrieves stripe login url.
     *
     * @since 3.6.1
     *
     * @param int|string $user_id
     * @param array      $args
     *
     * @return string|false Login url for stripe express, false in case of error
     */
    public static function get_stripe_login_url( $user_id, $args = [] ) {
        $account_id = UserMeta::get_stripe_account_id( $user_id );
        if ( empty( $account_id ) ) {
            return false;
        }

        try {
            $defaults = [
                'redirect_url' => dokan_get_page_url( 'dashboard', 'dokan', 'settings/payment' ),
            ];

            $args         = wp_parse_args( $args, $defaults );
            $stripe_login = Account::create_login_link( $account_id, $args );
            return $stripe_login->url;
        } catch ( DokanException $e ) {
            return false;
        }
    }

    /**
     * Checks if user is connected to stripe.
     *
     * @since 3.6.1
     *
     * @param int|string $user_id
     *
     * @return boolean
     */
    public static function is_connected( $user_id ) {
        $account = self::get_data( $user_id );
        if ( empty( $account ) ) {
            return false;
        }

        return $account->charges_enabled;
    }

    /**
     * Checks if an user has completed onboarding.
     *
     * @since 3.6.1
     *
     * @param int|string $user_id
     *
     * @return boolean
     */
    public static function is_onboarded( $user_id ) {
        $account = self::get_data( $user_id );
        if ( empty( $account ) ) {
            return false;
        }

        return $account->details_submitted;
    }

    /**
     * Checks if an user is enabled for payout.
     *
     * @since 3.6.1
     *
     * @param int|string $user_id
     *
     * @return boolean
     */
    public static function is_payout_enabled( $user_id ) {
        $account = self::get_data( $user_id );
        if ( empty( $account ) ) {
            return false;
        }

        return $account->payouts_enabled;
    }
}
