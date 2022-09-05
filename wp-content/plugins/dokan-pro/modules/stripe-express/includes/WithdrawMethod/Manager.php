<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\WithdrawMethod;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use WeDevs\DokanPro\Admin\Announcement;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;
use WeDevs\DokanPro\Modules\StripeExpress\Processors\User;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Settings;

/**
 * Class to handle all hooks for Stripe Express as withdraw method
 *
 * @since 3.6.1
 */
class Manager {

    /**
     * Class constructor
     *
     * @since 3.6.1
     *
     * @package WeDevs\DokanPro\Modules\StripeExpress\WithdrawMethod
     */
    public function __construct() {
        if ( ! Helper::is_gateway_ready() ) {
            return;
        }

        $this->hooks();
        $this->init_classes();
    }

    /**
     * Registers all required hooks.
     *
     * @since 3.6.1
     *
     * @return void
     */
    private function hooks() {
        // Register withdraw method
        add_filter( 'dokan_withdraw_methods', [ $this, 'register_withdraw_method' ] );
        // Process data for payment method settings in vendor dashboard
        add_filter( 'dokan_withdraw_method_settings_title', [ $this, 'get_heading' ], 10, 2 );
        add_filter( 'dokan_withdraw_method_icon', [ $this, 'get_icon' ], 10, 2 );
        add_filter( 'dokan_is_seller_connected_to_payment_method', [ $this, 'check_if_seller_connected' ], 10, 3 );
        // Process vendor settings for Stripe Express
        add_filter( 'dokan_store_profile_settings_args', [ $this, 'process_vendor_settings' ] );
        // Send announcement
        add_action( 'dokan_dashboard_before_widgets', [ $this, 'send_announcement_to_non_connected_vendor' ] );
        // Display notice
        add_action( 'dokan_dashboard_content_inside_before', [ $this, 'display_notice_on_vendor_dashboard' ] );
        // Process scripts for seller setup page
        add_action( 'init', [ $this, 'register_scripts' ] );
        add_action( 'dokan_setup_wizard_enqueue_scripts', [ $this, 'enqueue_scripts_for_seller_setup_page' ] );
        // Process calculations of profile settings completion progress
        add_action( 'wp', [ $this, 'update_profile_progress_on_connect' ] );
        add_action( 'dokan_stripe_express_seller_deactivated', [ $this, 'update_profile_progress_on_disconnect' ] );
    }


    /**
     * Inistantiates required classes
     *
     * @since 3.6.1
     *
     * @return void
     */
    private function init_classes() {
        new Ajax();
    }

    /**
     * Register Stripe Express as withdraw method
     *
     * @since 3.6.1
     *
     * @param array $methods
     *
     * @return array
     */
    public function register_withdraw_method( $methods ) {
        if ( Helper::is_gateway_ready() ) {
            $methods[ Helper::get_gateway_id() ] = [
                'title'    => Helper::get_gateway_title(),
                'callback' => [ $this, 'vendor_gateway_settings' ],
            ];
        }

        return $methods;
    }

    /**
     * Get the Withdrawal method icon
     *
     * @since 3.6.1
     *
     * @param string $method_icon
     * @param string $method_key
     *
     * @return string
     */
    public function get_icon( $method_icon, $method_key ) {
        if ( Helper::get_gateway_id() === $method_key ) {
            $method_icon = DOKAN_STRIPE_EXPRESS_ASSETS . 'images/stripe-withdraw-method.svg';
        }

        return $method_icon;
    }

    /**
     * Get the heading for this payment's settings page
     *
     * @since 3.6.1
     *
     * @param string $heading
     * @param string $slug
     *
     * @return string
     */
    public function get_heading( $heading, $slug ) {
        if ( false !== strpos( $slug, Helper::get_gateway_id() ) ) {
            $heading = __( 'Stripe Express Settings', 'dokan' );
        }

        return $heading;
    }

    /**
     * Checks if seller is connected to Stripe Express.
     *
     * @since 3.6.1
     *
     * @param boolean    $is_connected
     * @param string     $method_key
     * @param int|string $seller_id
     *
     * @return boolean
     */
    public function check_if_seller_connected( $is_connected, $method_key, $seller_id ) {
        if ( Helper::get_gateway_id() === $method_key ) {
            return Helper::is_seller_connected( $seller_id );
        }

        return $is_connected;
    }

    /**
     * Renders Stripe Express form for registration as withdraw method
     *
     * @since 3.6.1
     *
     * @param array $store_settings
     *
     * @return void
     */
    public function vendor_gateway_settings( $store_settings ) {
        global $current_user;
        $payment_settings = [];
        $user_id          = $current_user->ID;

        if ( ! empty( $store_settings['payment'] ) && ! empty( $store_settings['payment']['stripe_express'] ) ) {
            $payment_settings = $store_settings['payment']['stripe_express'];
        }

        if ( empty( $payment_settings['email'] ) ) {
            $payment_settings['email'] = $current_user->user_email;
        }

        wp_enqueue_style( 'dokan-stripe-express-vendor' );
        wp_enqueue_script( 'dokan-stripe-express-vendor' );

        Helper::get_template(
            'vendor-gateway-settings',
            [
                'user_id'             => $user_id,
                'is_seller_connected' => Helper::is_seller_connected( $user_id ),
                'stripe_account'      => User::get_data( $user_id ),
                'payment_settings'    => $payment_settings,
            ]
        );
    }

    /**
     * Processes Stripe Express payment settings for vendors
     *
     * @since 3.6.1
     *
     * @param array $settings
     *
     * @return array
     */
    public function process_vendor_settings( $settings ) {
        if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'dokan_payment_settings_nonce' ) ) {
            return $settings;
        }

        if ( ! empty( $_POST['settings']['stripe_express'] ) ) {
            $settings['payment']['stripe_express'] = wc_clean( wp_unslash( $_POST['settings']['stripe_express'] ) );
        }

        return $settings;
    }

    /**
     * Sends announcement to vendors if their account is not connected with MnagoPay
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function send_announcement_to_non_connected_vendor() {
        if ( ! is_user_logged_in() ) {
            return;
        }

        if ( ! Settings::is_send_announcement_to_sellers_enabled() ) {
            return;
        }

        // Check Stripe Express payment gateway is enabled
        $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
        if ( ! array_key_exists( Helper::get_gateway_id(), $available_gateways ) ) {
            return;
        }

        // Check if Stripe Express is ready
        if ( ! Helper::is_gateway_ready() ) {
            return;
        }

        // get current user id
        $seller_id = dokan_get_current_user_id();

        // check if current user is vendor
        if ( ! dokan_is_user_seller( $seller_id ) ) {
            return;
        }

        // check if vendor is already connected with Stripe Express
        if ( Helper::is_seller_connected( $seller_id ) ) {
            return;
        }

        // Check Stripe Express payment gateway is active as withdraw method
        if ( ! in_array( Helper::get_gateway_id(), dokan_withdraw_get_active_methods(), true ) ) {
            return;
        }

        if ( false === get_transient( "dokan_stripe_express_notice_intervals_$seller_id" ) ) {
            $announcement = new Announcement();
            // sent announcement message
            $args = [
                'title'         => $this->notice_to_connect(),
                'sender_type'   => 'selected_seller',
                'sender_ids'    => [ $seller_id ],
                'status'        => 'publish',
            ];

            $notice = $announcement->create_announcement( $args );

            if ( is_wp_error( $notice ) ) {
                return Helper::log(
                    sprintf(
                        'Error creating announcement for non-connected seller %1$s. Error Message: %2$s',
                        $seller_id,
                        $notice->get_error_message()
                    )
                );
            }

            // Notice is sent, now store transient
            set_transient( "dokan_stripe_express_notice_intervals_$seller_id", 'sent', DAY_IN_SECONDS * Settings::get_announcement_interval() );
        }
    }

    /**
     * Display notice to vendors if their account is not connected with Stripe Express
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function display_notice_on_vendor_dashboard() {
        if ( ! is_user_logged_in() ) {
            return;
        }

        // Geet current user id
        $seller_id = dokan_get_current_user_id();

        // Check if current user is vendor
        if ( ! dokan_is_user_seller( $seller_id ) ) {
            return;
        }

        // Check if notice on vendor dashboard is enabled
        if ( ! Settings::is_display_notice_on_vendor_dashboard_enabled() ) {
            return;
        }

        // Check Stripe Express payment gateway is active as withdraw method
        if ( ! in_array( Helper::get_gateway_id(), dokan_withdraw_get_active_methods(), true ) ) {
            return;
        }

        // Check if Stripe Express payment gateway is enabled
        $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
        if ( ! array_key_exists( Helper::get_gateway_id(), $available_gateways ) ) {
            return;
        }

        // Check if Stripe Express is ready
        if ( ! Helper::is_gateway_ready() ) {
            return;
        }

        // Check if vendor is already connected with Stripe Express
        if ( Helper::is_seller_connected( $seller_id ) ) {
            return;
        }

        if ( ! dokan_is_withdraw_method_enabled( Helper::get_gateway_id() ) ) {
            return;
        }

        echo '<div class="dokan-alert dokan-alert-danger dokan-panel-alert">' . $this->notice_to_connect() . '</div>';
    }

    /**
     * Retrieves notice for non-connected sellers.
     *
     * @since 3.6.1
     *
     * @return string
     */
    private function notice_to_connect() {
        return wp_kses(
            sprintf(
                /* translators: 1) opening <a> tag with link to the payment settings, 2) closing </a> tag  */
                __( 'Your account is not connected with Stripe Express. Sign up for a %1$sStripe Express%2$s account to receive automatic payouts.', 'dokan' ),
                sprintf( '<a href="%s">', esc_url_raw( Helper::get_payment_settings_url() ) ),
                '</a>'
            ),
            [
                'a' => [
                    'href'   => true,
                    'target' => true,
                ],
            ]
        );
    }

    /**
     * Enqueues necessary scripts.
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function enqueue_scripts_for_seller_setup_page() {
        // While we are enqueueing here, our scripts have not been registered yet.
        if ( empty( $_GET['page'] ) || 'dokan-seller-setup' !== $_GET['page'] || empty( $_GET['step'] ) || 'payment' !== $_GET['step'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return;
        }

        wp_enqueue_style( 'dokan-stripe-express-vendor-setup' );
        wp_enqueue_style( 'dokan-style' );
        wp_print_scripts( 'dokan-stripe-express-vendor-setup' );
    }

    /**
     * Register scripts.
     *
     * @since 3.7.4
     *
     * @return void
     */
    public function register_scripts() {
        list( $suffix, $version ) = dokan_get_script_suffix_and_version();

        wp_register_style(
            'dokan-stripe-express-vendor-setup',
            DOKAN_STRIPE_EXPRESS_ASSETS . "css/vendor{$suffix}.css",
            [],
            $version
        );

        wp_register_script(
            'dokan-stripe-express-vendor-setup',
            DOKAN_STRIPE_EXPRESS_ASSETS . "js/vendor{$suffix}.js",
            [ 'jquery' ],
            $version,
            true
        );

        wp_localize_script(
            'dokan-stripe-express-vendor-setup',
            'dokanStripeExpressData',
            [
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'dokan_stripe_express_vendor_payment_settings' ),
            ]
        );
    }

    /**
     * Calculate Dokan profile completeness value
     *
     * @since 3.7.1
     *
     * @param array $progress_track_value
     *
     * @return array
     */
    public function calculate_profile_progress( $progress_track_value ) {
        if (
            ! isset( $progress_track_value['progress'], $progress_track_value['current_payment_val'] ) ||
            $progress_track_value['current_payment_val'] <= 0
        ) {
            return $progress_track_value;
        }

        $progress_track_value['progress'] += $progress_track_value['current_payment_val'];
        $progress_track_value[ Helper::get_gateway_id() ] = $progress_track_value['current_payment_val'];
        $progress_track_value['current_payment_val'] = 0;

        return $progress_track_value;
    }

    /**
     * Update profile progress
     *
     * @since 3.7.1
     *
     * @return void
     */
    public function update_profile_progress_on_connect() {
        if (
            empty( $_REQUEST['seller_id'] ) ||
            ! isset( $_REQUEST['action'] ) ||
            'stripe_express_onboarding' !== sanitize_text_field( wp_unslash( $_REQUEST['action'] ) )
        ) {
            return;
        }

        $seller_id = intval( $_REQUEST['seller_id'] );

        if ( ! Helper::is_seller_connected( $seller_id ) ) {
            return;
        }

        /*
         * Calculate profile progress including
         * the seller activation for the Stripe Express gateway.
         */
        add_filter( 'dokan_profile_completion_progress_for_payment_methods', [ $this, 'calculate_profile_progress' ] );

        dokan_pro()->store_settings->save_store_data( $seller_id );

        // Remove the filter to avoid unnecessary recalculation.
        remove_filter( 'dokan_profile_completion_progress_for_payment_methods', [ $this, 'calculate_profile_progress' ] );
    }

    /**
     * Update profile progress
     *
     * @since 3.7.1
     *
     * @param int $seller_id
     */
    public function update_profile_progress_on_disconnect( $seller_id ) {
        dokan_pro()->store_settings->save_store_data( $seller_id );
    }
}
