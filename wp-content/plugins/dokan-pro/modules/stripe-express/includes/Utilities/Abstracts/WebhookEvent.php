<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\Utilities\Abstracts;

/**
 * Class WebhookEvent
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\Utilities\Abstracts
 */
abstract class WebhookEvent {

    /**
     * Event holder.
     *
     * @since 3.6.1
     */
    private $event;

    /**
     * Handles the event.
     *
     * @since 3.6.1
     *
     * @param object $payload
     *
     * @return void
     */
    abstract public function handle( $payload );

    /**
     * Sets the event.
     *
     * @since 3.6.1
     *
     * @param $event
     *
     * @return void
     */
    public function set( $event ) {
        $this->event = $event;
    }

    /**
     * Get event
     *
     * @since 3.6.1
     *
     * @return array
     */
    public function get() {
        return $this->event;
    }
}
