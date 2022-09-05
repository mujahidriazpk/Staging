<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\Api;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use Exception;
use WeDevs\Dokan\Exceptions\DokanException;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Api;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;

/**
 * Customer API handler class.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\Api
 */
class Customer extends Api {

    /**
     * Creates stripe customer.
     *
     * @since 3.6.1
     *
     * @param array $data
     *
     * @return \Stripe\Customer The newly created customer object.
     * @throws DokanException
     */
    public static function create( $data ) {
        try {
            return static::api()->customers->create( $data );
        } catch ( Exception $e ) {
            Helper::log( sprintf( 'Could not create customer: %s', $e->getMessage() ) );
            Helper::log( 'Customer data:', print_r( $data, true ) );
            throw new DokanException( 'dokan-stripe-customer-create-error', $e->getMessage() );
        }
    }

    /**
     * Updates a Stripe customer.
     *
     * @since 3.6.1
     *
     * @param int|string $id
     * @param array      $data
     *
     * @return \Stripe\Customer The updated customer object.
     * @throws DokanException
     */
    public static function update( $id, $data ) {
        try {
            return static::api()->customers->update( $id, $data );
        } catch ( Exception $e ) {
            Helper::log( sprintf( 'Could not update customer: %s. Error: %s', $id, $e->getMessage() ) );
            Helper::log( 'Customer data:', print_r( $data, true ) );
            throw new DokanException( 'dokan-stripe-customer-update-error', $e->getMessage() );
        }
    }

    /**
     * Retrieves a source data for a Stripe customer.
     *
     * @since 3.6.1
     *
     * @param int|string $customer_id
     * @param int|string $source_id
     *
     * @return \Stripe\AlipayAccount|\Stripe\BankAccount|\Stripe\BitcoinReceiver|\Stripe\Card|\Stripe\Source
     * @throws DokanException
     */
    public static function get_source( $customer_id, $source_id ) {
        try {
            return static::api()->customers->retrieveSource( $customer_id, $source_id );
        } catch ( Exception $e ) {
            Helper::log( sprintf( 'Could not retrieve source (%s) for customer: %s. Error: %s', $source_id, $customer_id, $e->getMessage() ) );
            throw new DokanException( 'dokan-stripe-customer-source-retrieve-error', $e->getMessage() );
        }
    }

    /**
     * Retrieves all sources data for a Stripe customer.
     *
     * @since 3.6.1
     *
     * @param int|string $customer_id
     *
     * @return \Stripe\Collection<\Stripe\AlipayAccount|\Stripe\BankAccount|\Stripe\BitcoinReceiver|\Stripe\Card|\Stripe\Source>
     * @throws DokanException
     */
    public static function get_sources( $customer_id ) {
        try {
            return static::api()->customers->allSources( $customer_id );
        } catch ( Exception $e ) {
            Helper::log( sprintf( 'Could not retrieve sources for customer: %s. Error: %s', $customer_id, $e->getMessage() ) );
            throw new DokanException( 'dokan-stripe-customer-sources-retrieve-error', $e->getMessage() );
        }
    }

    /**
     * Creates source for a Stripe customer.
     *
     * @since 3.6.1
     *
     * @param int|string $id   The customer ID.
     * @param array      $args Source data.
     *
     * @return \Stripe\AlipayAccount|\Stripe\BankAccount|\Stripe\BitcoinReceiver|\Stripe\Card|\Stripe\Source
     * @throws DokanException
     */
    public static function create_source( $customer_id, $data ) {
        try {
            return static::api()->customers->createSource( $customer_id, $data );
        } catch ( Exception $e ) {
            Helper::log( sprintf( 'Could not create source for customer: %s. Error: %s', $customer_id, $e->getMessage() ) );
            throw new DokanException( 'dokan-stripe-customer-source-create-error', $e->getMessage() );
        }
    }

    /**
     * Creates source for a Stripe customer.
     *
     * @since 3.6.1
     *
     * @param int|string $customer_id
     * @param int|string $source_id
     *
     * @return \Stripe\AlipayAccount|\Stripe\BankAccount|\Stripe\BitcoinReceiver|\Stripe\Card|\Stripe\Source
     * @throws DokanException
     */
    public static function update_source( $customer_id, $source_id ) {
        try {
            return static::api()->customers->updateSource( $customer_id, $source_id );
        } catch ( Exception $e ) {
            Helper::log( sprintf( 'Could not update source (%s) for customer: %s. Error: %s', $source_id, $customer_id, $e->getMessage() ) );
            throw new DokanException( 'dokan-stripe-customer-source-update-error', $e->getMessage() );
        }
    }

    /**
     * Creates source for a Stripe customer.
     *
     * @since 3.6.1
     *
     * @param int|string $customer_id
     * @param int|string $source_id
     *
     * @return \Stripe\AlipayAccount|\Stripe\BankAccount|\Stripe\BitcoinReceiver|\Stripe\Card|\Stripe\Source
     * @throws DokanException
     */
    public static function delete_source( $customer_id, $source_id ) {
        try {
            return static::api()->customers->deleteSource( $customer_id, $source_id );
        } catch ( Exception $e ) {
            Helper::log( sprintf( 'Could not delete source (%s) for customer: %s. Error: %s', $source_id, $customer_id, $e->getMessage() ) );
            throw new DokanException( 'dokan-stripe-customer-source-delete-error', $e->getMessage() );
        }
    }
}
