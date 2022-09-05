<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\Api;

use Exception;
use Stripe\Event;
use WeDevs\Dokan\Exceptions\DokanException;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Api;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Webhook Endpoint API handler class
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\Api
 */
class WebhookEndpoint extends Api {

    /**
     * Prefix for webhook.
     *
     * @since 3.6.1
     *
     * @var string
     */
    private static $prefix = 'dokan-stripe-express';

    /**
     * Retrieves prefix for webhook.
     *
     * @since 3.6.1
     *
     * @return string
     */
    public static function prefix() {
        return self::$prefix;
    }

    /**
     * Retrieves supported webhook events.
     *
     * @since 3.6.1
     *
     * @return array
     */
    public static function get_supported_events() {
        return apply_filters(
            'dokan_stripe_express_webhook_events',
            [
                Event::PAYMENT_INTENT_SUCCEEDED                 => 'PaymentIntentSucceeded',
                Event::PAYMENT_INTENT_REQUIRES_ACTION           => 'PaymentIntentRequiresAction',
                Event::PAYMENT_INTENT_AMOUNT_CAPTURABLE_UPDATED => 'PaymentIntentAmountCapturableUpdated',
                Event::SETUP_INTENT_SUCCEEDED                   => 'SetupIntentSucceeded',
                Event::SETUP_INTENT_SETUP_FAILED                => 'SetupIntentSetupFailed',
                Event::SOURCE_CHARGEABLE                        => 'SourceChargeable',
                Event::SOURCE_CANCELED                          => 'SourceCanceled',
                Event::CHARGE_SUCCEEDED                         => 'ChargeSucceeded',
                Event::CHARGE_CAPTURED                          => 'ChargeCaptured',
                Event::CHARGE_FAILED                            => 'ChargeFailed',
                Event::CHARGE_DISPUTE_CREATED                   => 'ChargeDisputeCreated',
                Event::CHARGE_DISPUTE_CLOSED                    => 'ChargeDisputeClosed',
                Event::REVIEW_OPENED                            => 'ReviewOpened',
                Event::REVIEW_CLOSED                            => 'ReviewClosed',
            ]
        );
    }

    /**
     * Retrieves a webhook endpoint.
     *
     * @since 3.6.1
     *
     * @param string $webhook_id
     *
     * @return object|false
     */
    public static function get( $webhook_id ) {
        try {
            return static::api()->webhookEndpoints->retrieve( $webhook_id );
        } catch ( Exception $e ) {
            Helper::log( sprintf( 'Could not retrieve webhook: %s. Message: %s', $webhook_id, $e->getMessage() ) );
            return false;
        }
    }

    /**
     * Lists all webhook endpoints.
     *
     * @since 3.6.1
     *
     * @param array $args (Optional)
     *
     * @return array|false
     */
    public static function all( $args = [] ) {
        $data = [
            'limit' => 100, // Maximum limit
        ];

        $args = wp_parse_args( $args, $data );

        try {
            $endpoints = static::api()->webhookEndpoints->all( $args );
            return $endpoints->data;
        } catch ( Exception $e ) {
            Helper::log( sprintf( 'Could not retrieve all webhook endpoints. Message: %s', $e->getMessage() ) );
            return false;
        }
    }

    /**
     * Creates webhook endpoint.
     *
     * @since 3.6.1
     *
     * @param array $args (Optional)
     *
     * @return object
     * @throws DokanException
     */
    public static function create( $args = [] ) {
        $args = wp_parse_args( $args, self::generate_data() );

        try {
            return static::api()->webhookEndpoints->create( $args );
        } catch ( Exception $e ) {
            Helper::log( sprintf( 'Could not create webhook: %s. Message: %s', implode( ', ', (array) $args['enabled_events'] ), $e->getMessage() ) );

            throw new DokanException(
                'dokan-stripe-express-webhook-create-error',
                sprintf(
                    /* translators: 1) webhook events, 2) error message */
                    __( 'Could not create webhook: %1$s. Message: %2$s', 'dokan' ),
                    implode( ', ', (array) $args['enabled_events'] ),
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * Updates a webhook endpoint.
     *
     * @since 3.6.1
     *
     * @param string $webhook_id
     * @param array  $args       (Optional)
     *
     * @return object
     * @throws DokanException
     */
    public static function update( $webhook_id, $args = [] ) {
        $args = wp_parse_args( $args, self::generate_data() );

        unset( $args['api_version'] );

        try {
            return static::api()->webhookEndpoints->update( $webhook_id, $args );
        } catch ( Exception $e ) {
            Helper::log( sprintf( 'Could not update webhook: %s. Message: %s', $webhook_id, $e->getMessage() ) );

            throw new DokanException(
                'dokan-stripe-express-webhook-update-error',
                sprintf(
                    /* translators: 1) webhook events, 2) error message */
                    __( 'Could not update webhook: %1$s. Message: %2$s', 'dokan' ),
                    $webhook_id,
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * Deletes a webhook endpoint.
     *
     * @since 3.6.1
     *
     * @param string $webhook_id
     *
     * @return object
     * @throws DokanException
     */
    public static function delete( $webhook_id ) {
        try {
            return static::api()->webhookEndpoints->delete( $webhook_id );
        } catch ( Exception $e ) {
            Helper::log( sprintf( 'Could not delete webhook: %s. Message: %s', $webhook_id, $e->getMessage() ) );

            throw new DokanException(
                'dokan-stripe-express-webhook-delete-error',
                /* translators: 1) webhook event, 2) error message */
                sprintf( __( 'Could not delete webhook: %1$s. Message: %2$s', 'dokan' ), $webhook_id, $e->getMessage() )
            );
        }
    }

    /**
     * Retrieves a webhook event object
     *
     * @since 3.6.1
     *
     * @param string $event_id
     * @param array  $args     (Optional)
     *
     * @return object|false
     */
    public static function get_event( $event_id, $args = [] ) {
        try {
            return static::api()->events->retrieve( $event_id, $args );
        } catch ( Exception $e ) {
            Helper::log( sprintf( 'Could not retrieve webhook: %s. Message: %s', $event_id, $e->getMessage() ) );
            return false;
        }
    }

    /**
     * Generates URL for webhook.
     *
     * @since 3.6.1
     *
     * @return string
     */
    public static function generate_url() {
        return home_url( 'wc-api/' . self::prefix(), 'https' );
    }

    /**
     * Generates default webhook data.
     *
     * @since 3.6.1
     *
     * @return array
     */
    public static function generate_data() {
        return [
            'url'            => self::generate_url(),
            'enabled_events' => array_keys( self::get_supported_events() ),
            'api_version'    => Helper::get_api_version(),
            'description'    => __( 'This webhook is created by Dokan Pro.', 'dokan' ),
        ];
    }
}
