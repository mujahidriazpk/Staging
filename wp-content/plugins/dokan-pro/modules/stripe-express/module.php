<?php

namespace WeDevs\DokanPro\Modules\StripeExpress;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use WeDevs\Dokan\Traits\ChainableContainer;

/**
 * Main class for Stripe Express module
 *
 * @since 3.6.1
 */
class Module {

    use ChainableContainer;

    /**
     * Class constructor
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function __construct() {
        $this->constants();
        $this->controllers();
        $this->hooks();
    }

    /**
     * Define module constants
     *
     * @since 3.6.1
     *
     * @return void
     */
    private function constants() {
        define( 'DOKAN_STRIPE_EXPRESS_FILE', __FILE__ );
        define( 'DOKAN_STRIPE_EXPRESS_PATH', dirname( DOKAN_STRIPE_EXPRESS_FILE ) );
        define( 'DOKAN_STRIPE_EXPRESS_ASSETS', plugin_dir_url( DOKAN_STRIPE_EXPRESS_FILE ) . 'assets/' );
        define( 'DOKAN_STRIPE_EXPRESS_TEMPLATE_PATH', dirname( DOKAN_STRIPE_EXPRESS_FILE ) . '/templates/' );
    }

    /**
     * Sets all controllers
     *
     * @since 3.6.1
     *
     * @return void
     */
    private function controllers() {
        $this->container['frontend']        = new Frontend\Manager();
        $this->container['admin']           = new Admin\Manager();
        $this->container['gateway']         = new Controllers\Gateway();
        $this->container['checkout']        = new Controllers\Checkout();
        $this->container['cart']            = new Controllers\Cart();
        $this->container['webhook']         = new Controllers\Webhook();
        $this->container['order']           = new Controllers\Order();
        $this->container['refund']          = new Controllers\Refund();
        $this->container['payment_tokens']  = new Controllers\Token();
        $this->container['payment_request'] = new Controllers\PaymentRequest();
        $this->container['withdraw_method'] = new WithdrawMethod\Manager();
        $this->container['delay_disburse']  = new Utilities\BackgroundProcesses\DelayedDisbursement();
    }

    /**
     * Registers required hooks.
     *
     * @since 3.6.1
     *
     * @return void
     */
    private function hooks() {
        // Activation and Deactivation hook
        add_action( 'dokan_activated_module_stripe_express', [ $this, 'activate' ] );
        add_action( 'dokan_deactivated_module_stripe_express', [ $this, 'deactivate' ] );
    }

    /**
     * Performs actions upon module activation
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function activate( $instance ) {
        $this->container['webhook']->register();

        if ( ! wp_next_scheduled( 'dokan_stripe_express_daily_schedule' ) ) {
            wp_schedule_event( time(), 'daily', 'dokan_stripe_express_daily_schedule' );
        }
    }

    /**
     * Performs actions upon module deactivation
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function deactivate( $instance ) {
        $this->container['webhook']->deregister();

        // clear scheduled task
        wp_clear_scheduled_hook( 'dokan_stripe_express_daily_schedule' );
    }
}
