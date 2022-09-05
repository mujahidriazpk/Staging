<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\Support;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use WeDevs\DokanPro\Modules\StripeExpress\Support\Config;

/**
 * API handler class
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\Support
 */
class Api {

    /**
     * Retrieves the desired API object.
     *
     * @since 3.6.1
     *
     * @return \Stripe\StripeClient
     */
    protected static function api() {
        return self::config()->client;
    }

    /**
     * Returns instance of configuration.
     *
     * @since 3.6.1
     *
     * @return Config
     */
    protected static function config() {
        return Config::instance();
    }
}
