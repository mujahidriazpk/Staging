<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\Processors;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use WeDevs\DokanPro\Modules\StripeExpress\Support\Config;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Settings;

/**
 * Class for processing webhhoks.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\Processors
 */
class Webhook {

    /**
     * Constants to indicate different webhook statuses.
     *
     * @since 3.6.1
     *
     * @var string
     */
    const STATUS_VALIDATION_SUCCEEDED   = 'validation_succeeded';
    const STATUS_EMPTY_HEADERS          = 'empty_headers';
    const STATUS_EMPTY_BODY             = 'empty_body';
    const STATUS_USER_AGENT_INVALID     = 'user_agent_invalid';
    const STATUS_SIGNATURE_INVALID      = 'signature_invalid';
    const STATUS_SIGNATURE_MISMATCH     = 'signature_mismatch';
    const STATUS_TIMESTAMP_OUT_OF_RANGE = 'timestamp_out_of_range';

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

    /**
     * Retrieves option key for webhook.
     *
     * @since 3.6.1
     *
     * @return string
     */
    private static function option_key( $key = '' ) {
        return Helper::get_gateway_id() . "_webhook_$key";
    }

    /**
     * Removes webhook key from settings.
     *
     * @since 3.6.1
     *
     * @return boolean
     */
    public static function delete_key() {
        $settings                        = Settings::get();
        $webhook_key                     = 'webhook_key';
        $settings[ $webhook_key ]        = '';
        $settings[ "test_$webhook_key" ] = '';

        return Settings::update( $settings );
    }

    /**
     * Retrieves status messages regarding webhook.
     *
     * @since 3.6.1
     *
     * @return array
     */
    public static function get_status_messages() {
        return [
            self::STATUS_VALIDATION_SUCCEEDED   => __( 'No error', 'dokan' ),
            self::STATUS_EMPTY_HEADERS          => __( 'The webhook was missing expected headers', 'dokan' ),
            self::STATUS_EMPTY_BODY             => __( 'The webhook was missing expected body', 'dokan' ),
            self::STATUS_USER_AGENT_INVALID     => __( 'The webhook received did not come from Stripe', 'dokan' ),
            self::STATUS_SIGNATURE_INVALID      => __( 'The webhook signature was missing or was incorrectly formatted', 'dokan' ),
            self::STATUS_SIGNATURE_MISMATCH     => __( 'The webhook was not signed with the expected signing secret', 'dokan' ),
            self::STATUS_TIMESTAMP_OUT_OF_RANGE => __( 'The timestamp in the webhook differed more than five minutes from the site time', 'dokan' ),
        ];
    }

    /**
     * Returns the localized reason the last webhook failed.
     *
     * @since 3.6.1
     *
     * @return string Reason the last webhook failed.
     */
    public static function get_last_error() {
        $option          = static::config()->is_live_mode() ? self::option_key( 'last_error' ) : self::option_key( 'test_last_error' );
        $last_error      = get_option( $option, false );
        $status_messages = self::get_status_messages();

        if ( isset( $status_messages[ $last_error ] ) ) {
            return $status_messages[ $last_error ];
        }

        return( __( 'Unknown error.', 'dokan' ) );
    }

    /**
     * Sets the reason for the last failed webhook.
     *
     * @since 3.6.1
     *
     * @return boolean
     */
    public static function set_last_error( $reason ) {
        $option = static::config()->is_live_mode() ? self::option_key( 'last_error' ) : self::option_key( 'test_last_error' );
        return update_option( $option, $reason );
    }

    /**
     * Gets (and sets, if unset) the timestamp the plugin first
     * started tracking webhook failure and successes.
     *
     * @since 3.6.1
     *
     * @return integer UTC seconds since 1970.
     */
    public static function get_monitoring_began_time() {
        $option              = static::config()->is_live_mode() ? self::option_key( 'monitor_began_at' ) : self::option_key( 'test_monitor_began_at' );
        $monitoring_began_at = get_option( $option, 0 );

        if ( 0 === $monitoring_began_at ) {
            $monitoring_began_at = time();
            update_option( $option, $monitoring_began_at );

            /*
             * Enforce database consistency. This should only be needed if the user
             * has modified the database directly. We should not allow timestamps
             * before monitoring began.
             */
            self::set_last_success_time( 0 );
            self::set_last_failure_time( 0 );
            self::set_last_error( self::STATUS_VALIDATION_SUCCEEDED );
        }

        return $monitoring_began_at;
    }

    /**
     * Sets the timestamp of the last successfully processed webhook.
     *
     * @since 3.6.1
     *
     * @param integer UTC seconds since 1970.
     *
     * @return boolean
     */
    public static function set_last_success_time( $timestamp ) {
        $option = static::config()->is_live_mode() ? self::option_key( 'last_success_at' ) : self::option_key( 'test_last_success_at' );
        return update_option( $option, $timestamp );
    }

    /**
     * Gets the timestamp of the last successfully processed webhook,
     * or returns 0 if no webhook has ever been successfully processed.
     *
     * @since 3.6.1
     *
     * @return integer UTC seconds since 1970 | 0.
     */
    public static function get_last_success_time() {
        $option = static::config()->is_live_mode() ? self::option_key( 'last_success_at' ) : self::option_key( 'test_last_success_at' );
        return get_option( $option, 0 );
    }

    /**
     * Sets the timestamp of the last failed webhook.
     *
     * @since 3.6.1
     *
     * @param integer UTC seconds since 1970.
     */
    public static function set_last_failure_time( $timestamp ) {
        $option = static::config()->is_live_mode() ? self::option_key( 'last_failure_at' ) : self::option_key( 'test_last_failure_at' );
        update_option( $option, $timestamp );
    }

    /**
     * Gets the timestamp of the last failed webhook,
     * or returns 0 if no webhook has ever failed to process.
     *
     * @since 3.6.1
     *
     * @return integer UTC seconds since 1970 | 0.
     */
    public static function get_last_failure_time() {
        $option = static::config()->is_live_mode() ? self::option_key( 'last_failure_at' ) : self::option_key( 'test_last_failure_at' );
        return get_option( $option, 0 );
    }

    /**
     * Gets the state of webhook processing in a human readable format.
     *
     * @since 3.6.1
     *
     * @return string Details on recent webhook successes and failures.
     */
    public static function get_status_notice() {
        $monitoring_began_at = self::get_monitoring_began_time();
        $last_success_at     = self::get_last_success_time();
        $last_failure_at     = self::get_last_failure_time();
        $last_error          = self::get_last_error();
        $test_mode           = ! static::config()->is_live_mode();
        $date_format         = 'Y-m-d H:i:s e';

        // Case 1 (Nominal case): Most recent = success
        if ( $last_success_at > $last_failure_at ) {
            $message = sprintf(
                $test_mode ?
                    /* translators: 1) date and time of last webhook received, e.g. 2020-06-28 10:30:50 UTC */
                    __( 'The most recent test webhook, timestamped %s, was processed successfully.', 'dokan' ) :
                    /* translators: 1) date and time of last webhook received, e.g. 2020-06-28 10:30:50 UTC */
                    __( 'The most recent live webhook, timestamped %s, was processed successfully.', 'dokan' ),
                dokan_current_datetime()->setTimestamp( $last_success_at )->format( $date_format )
            );
            return $message;
        }

        // Case 2: No webhooks received yet
        if ( ( 0 === $last_success_at ) && ( 0 === $last_failure_at ) ) {
            $message = sprintf(
                $test_mode ?
                    /* translators: 1) date and time webhook monitoring began, e.g. 2020-06-28 10:30:50 UTC */
                    __( 'No test webhooks have been received since monitoring began at %s.', 'dokan' ) :
                    /* translators: 1) date and time webhook monitoring began, e.g. 2020-06-28 10:30:50 UTC */
                    __( 'No live webhooks have been received since monitoring began at %s.', 'dokan' ),
                dokan_current_datetime()->setTimestamp( $monitoring_began_at )->format( $date_format )
            );
            return $message;
        }

        // Case 3: Failure after success
        if ( $last_success_at > 0 ) {
            $message = sprintf(
                $test_mode ?
                    /*
                     * translators: 1) date and time of last failed webhook e.g. 2020-06-28 10:30:50 UTC
                     * translators: 2) reason webhook failed
                     * translators: 3) date and time of last successful webhook e.g. 2020-05-28 10:30:50 UTC
                     */
                    __( 'Warning: The most recent test webhook, received at %1$s, could not be processed. Reason: %2$s. (The last test webhook to process successfully was timestamped %3$s.)', 'dokan' ) :
                    /*
                     * translators: 1) date and time of last failed webhook e.g. 2020-06-28 10:30:50 UTC
                     * translators: 2) reason webhook failed
                     * translators: 3) date and time of last successful webhook e.g. 2020-05-28 10:30:50 UTC
                     */
                    __( 'Warning: The most recent live webhook, received at %1$s, could not be processed. Reason: %2$s. (The last live webhook to process successfully was timestamped %3$s.)', 'dokan' ),
                dokan_current_datetime()->setTimestamp( $last_failure_at )->format( $date_format ),
                $last_error,
                dokan_current_datetime()->setTimestamp( $last_success_at )->format( $date_format )
            );
            return $message;
        }

        // Case 4: Failure with no prior success
        $message = sprintf(
            $test_mode ?
                /* translators: 1) date and time of last failed webhook e.g. 2020-06-28 10:30:50 UTC
                 * translators: 2) reason webhook failed
                 * translators: 3) date and time webhook monitoring began e.g. 2020-05-28 10:30:50 UTC
                 */
                __( 'Warning: The most recent test webhook, received at %1$s, could not be processed. Reason: %2$s. (No test webhooks have been processed successfully since monitoring began at %3$s.)', 'dokan' ) :
                /* translators: 1) date and time of last failed webhook e.g. 2020-06-28 10:30:50 UTC
                 * translators: 2) reason webhook failed
                 * translators: 3) date and time webhook monitoring began e.g. 2020-05-28 10:30:50 UTC
                 */
                __( 'Warning: The most recent live webhook, received at %1$s, could not be processed. Reason: %2$s. (No live webhooks have been processed successfully since monitoring began at %3$s.)', 'dokan' ),
            dokan_current_datetime()->setTimestamp( $last_failure_at )->format( $date_format ),
            $last_error,
            dokan_current_datetime()->setTimestamp( $monitoring_began_at )->format( $date_format )
        );
        return $message;
    }
}
