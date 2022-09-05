<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\Api;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use WeDevs\DokanPro\Modules\StripeExpress\Support\Api;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;

/**
 * Source API handler class.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\Api
 */
class Source extends Api {

    /**
     * Retrieves stripe source id.
     *
     * @since 3.6.1
     *
     * @param string $source_id
     * @param array  $args
     *
     * @return object|false
     */
    public static function get( $source_id, $args = [] ) {
        try {
            return static::api()->sources->retrieve( $source_id, $args );
        } catch ( \Exception $e ) {
            Helper::log( sprintf( 'Could not retrieve source for id: %1$s. Error: %2$s', $source_id, $e->getMessage() ), 'Source' );
            return false;
        }
    }
}
