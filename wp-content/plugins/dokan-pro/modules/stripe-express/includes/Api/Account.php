<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\Api;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use Exception;
use WeDevs\Dokan\Exceptions\DokanException;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Api;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;

/**
 * Client API handler class
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\Api
 */
class Account extends Api {

    /**
     * Error code if provided account id is invalid.
     *
     * @since 3.6.1
     */
    const ACCOUNT_INVALID = 'account_invalid';

    /**
     * Retrieves a client.
     *
     * @since 3.6.1
     *
     * @param int|string $account_id
     * @param array $args
     *
     * @return object
     * @throws DokanException
     */
    public static function get( $account_id, array $args = [] ) {
        try {
            return static::api()->accounts->retrieve( $account_id, $args );
        } catch ( Exception $e ) {
            Helper::log( sprintf( 'Could not retrieve account: %s', $e->getMessage() ) );
            $error_code = ! empty( $e->getError()->code ) ? $e->getError()->code : $e->getCode();
            throw new DokanException( $error_code, $e->getMessage() );
        }
    }

    /**
     * Retrieves all connected accounts.
     *
     * @since 3.6.1
     *
     * @param array $args
     *
     * @return array|false
     */
    public static function all( array $args = [] ) {
        try {
            return static::api()->accounts->all( $args );
        } catch ( Exception $e ) {
            Helper::log( sprintf( 'Could not retrieve all accounts.', $e->getMessage() ) );
            return false;
        }
    }

    /**
     * Creates an express client.
     *
     * @since 3.6.1
     *
     * @param array $args
     *
     * @return object
     * @throws DokanException
     */
    public static function create( array $args = [] ) {
        try {
            $defaults = [
                'type'         => 'express',
                'capabilities' => [
                    'card_payments'       => [
                        'requested' => true,
                    ],
                    'ideal_payments'      => [
                        'requested' => true,
                    ],
                    'sepa_debit_payments' => [
                        'requested' => true,
                    ],
                    'transfers'           => [
                        'requested' => true,
                    ],
                ],
            ];

            $args = wp_parse_args( $args, $defaults );
            return static::api()->accounts->create( $args );
        } catch ( Exception $e ) {
            Helper::log( sprintf( 'Could not create account: %s', $e->getMessage() ) );
            /* translators: error message */
            throw new DokanException( 'dokan-stripe-express-account-create-error', sprintf( __( '%s', 'dokan' ), $e->getMessage() ) ); // phpcs:ignore WordPress.WP.I18n.NoEmptyStrings
        }
    }

    /**
     * Updates an connected account.
     *
     * @since 3.6.1
     *
     * @param string $account_id
     * @param array  $data
     *
     * @return object
     * @throws DokanException
     */
    public static function update( $account_id, array $data = [] ) {
        try {
            return static::api()->accounts->update( $account_id, $data );
        } catch ( Exception $e ) {
            Helper::log( sprintf( 'Could not update account: %1$s. Error: %2$s', $account_id, $e->getMessage() ) );
            /* translators: error message */
            throw new DokanException( 'dokan-stripe-express-account-create-error', sprintf( __( '%s', 'dokan' ), $e->getMessage() ) ); // phpcs:ignore WordPress.WP.I18n.NoEmptyStrings
        }
    }

    /**
     * Creates link for client onboarding.
     *
     * @since 3.6.1
     *
     * @param int|string $account_id
     * @param array $args
     *
     * @return mixed
     * @throws DokanException
     */
    public static function create_onboarding_link( $account_id, array $args = [] ) {
        $defaults = [
            'account' => $account_id,
            'type'    => 'account_onboarding',
        ];

        $args = wp_parse_args( $args, $defaults );

        try {
            return static::api()->accountLinks->create( $args );
        } catch ( Exception $e ) {
            $message = sprintf( 'Could not create client account link: %s', $e->getMessage() );
            Helper::log( $message, 'Account', 'error' );
            /* translators: error message */
            throw new DokanException( 'dokan-stripe-express-account-onboarding-error', sprintf( __( '%s', 'dokan' ), $message ) ); // phpcs:ignore WordPress.WP.I18n.NoEmptyStrings
        }
    }

    /**
     * Creates login link for a connnected express account.
     *
     * @since 3.6.1
     *
     * @param string $account_id
     * @param array  $args
     *
     * @return object
     * @throws DokanException
     */
    public static function create_login_link( $account_id, $args = [] ) {
        try {
            return static::api()->accounts->createLoginLink( $account_id, $args );
        } catch ( Exception $e ) {
            $message = sprintf(
                'Could not create login link for account: %1$s. Error: %2$s',
                $account_id,
                $e->getMessage()
            );
            Helper::log( $message, 'Account', 'error' );
            /* translators: error message */
            throw new DokanException( 'dokan-stripe-express-login-link-create-error', sprintf( __( '%s', 'dokan' ), $message ) ); // phpcs:ignore WordPress.WP.I18n.NoEmptyStrings
        }
    }
}
