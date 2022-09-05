<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\Api;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use WeDevs\Dokan\Exceptions\DokanException;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Api;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;

/**
 * API handler class for charges.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\Api
 */
class Charge extends Api {

    /**
     * Creates a charge.
     *
     * @since 3.6.1
     *
     * @param array $data
     *
     * @return object
     * @throws DokanException
     */
    public static function create( $data ) {
        try {
            return static::api()->charges->create( $data );
        } catch ( \Exception $e ) {
            Helper::log( sprintf( 'Could not create charge. Error: %s', $e->getMessage() ), 'Charge' );
            Helper::log( 'Data: ' . print_r( $data, true ) );
            throw new DokanException( 'dokan-stripe-express-charge-error', $e->getMessage() );
        }
    }

    /**
     * Updates a charge.
     *
     * @since 3.6.1
     *
     * @param string $charge_id
     * @param array  $data
     * @param array  $args
     *
     * @return object
     * @throws DokanException
     */
    public static function update( $charge_id, $data, $extra = [] ) {
        try {
            return static::api()->charges->update( $charge_id, $data, $extra );
        } catch ( \Exception $e ) {
            Helper::log( sprintf( 'Could not update charge: %1$s. Error: %2$s', $charge_id, $e->getMessage() ), 'Charge' );
            Helper::log( 'Data: ' . print_r( $data, true ) );
            throw new DokanException( 'dokan-stripe-express-charge-error', $e->getMessage() );
        }
    }
}
