<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\Admin;

/**
 * Manager class for Admin.
 *
 * @since 3.6.1
 */
class Manager {

    /**
     * Class constructor
     *
     * @since 3.6.1
     */
    public function __construct() {
        if ( ! is_admin() ) {
            return;
        }

        $this->init_classes();
    }

    /**
     * Instantiates required classes.
     *
     * @since 3.6.1
     *
     * @return void
     */
    private function init_classes() {
        new Assets();
    }
}
