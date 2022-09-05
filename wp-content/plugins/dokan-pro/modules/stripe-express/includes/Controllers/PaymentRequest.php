<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\Controllers;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use WC_AJAX;
use Exception;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Config;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;
use WeDevs\DokanPro\Modules\StripeExpress\Utilities\Traits\PaymentRequestUtils;

/**
 * Stripe Payment Request API controller
 * Adds support for Apple Pay and Chrome Payment Request API buttons.
 * Utilizes the Stripe Payment Request Button to support checkout from the product detail and cart pages.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\Controllers
 */
class PaymentRequest {

    use PaymentRequestUtils;

    /**
     * Total label.
     *
     * @since 3.6.1
     *
     * @var string
     */
    public $total_label;

    /**
     * Publishable key.
     *
     * @since 3.6.1
     *
     * @var string
     */
    public $publishable_key;

    /**
     * Secret key.
     *
     * @since 3.6.1
     *
     * @var string
     */
    public $secret_key;

    /**
     * Is test mode active?
     *
     * @since 3.6.1
     *
     * @var bool
     */
    public $testmode;

    /**
     * Is API ready?
     *
     * @since 3.6.1
     *
     * @var bool
     */
    protected $api_ready;

    /**
     * Configuration data.
     *
     * @since 3.6.1
     *
     * @var Config
     */
    protected $config;

    /**
     * Class constructor.
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function __construct() {
        $this->config          = Config::instance();
        $this->testmode        = ! $this->config->is_live_mode();
        $this->publishable_key = $this->config->get_publishable_key();
        $this->secret_key      = $this->config->get_secret_key();
        $this->api_ready       = $this->config->verify_api_keys();
        $this->total_label     = $this->get_option( 'statement_descriptor' );
        $this->total_label     = str_replace( "'", '', Helper::clean_statement_descriptor( $this->total_label ) ) . apply_filters( 'dokan_stripe_express_payment_request_total_label_suffix', ' (via Dokan)' );

        // Checks if Stripe Gateway is enabled.
        if ( 'yes' !== $this->get_option( 'enabled' ) ) {
            return;
        }

        // Checks if Payment Request is enabled.
        if ( 'yes' !== $this->get_option( 'payment_request' ) ) {
            return;
        }

        // Don't load for change payment method page.
        if ( isset( $_GET['change_payment_method'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return;
        }

        $this->hooks();
    }

    /**
     * Initialize hooks.
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function hooks() {
        add_action( 'template_redirect', [ $this, 'set_session' ] );
        add_action( 'template_redirect', [ $this, 'handle_payment_request_redirect' ] );

        add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ] );

        add_action( 'woocommerce_after_add_to_cart_quantity', [ $this, 'render_payment_request_button' ], 1 );
        add_action( 'woocommerce_after_add_to_cart_quantity', [ $this, 'render_payment_request_button_separator' ], 2 );

        add_action( 'woocommerce_proceed_to_checkout', [ $this, 'render_payment_request_button' ], 1 );
        add_action( 'woocommerce_proceed_to_checkout', [ $this, 'render_payment_request_button_separator' ], 2 );

        add_action( 'woocommerce_checkout_before_customer_details', [ $this, 'render_payment_request_button' ], 1 );
        add_action( 'woocommerce_checkout_before_customer_details', [ $this, 'render_payment_request_button_separator' ], 2 );

        add_action( 'wc_ajax_dokan_stripe_express_log_errors', [ $this, 'log_errors' ] );
        add_action( 'wc_ajax_dokan_stripe_express_clear_cart', [ $this, 'clear_cart' ] );
        add_action( 'wc_ajax_dokan_stripe_express_add_to_cart', [ $this, 'add_to_cart' ] );
        add_action( 'wc_ajax_dokan_stripe_express_create_order', [ $this, 'create_order' ] );
        add_action( 'wc_ajax_dokan_stripe_express_get_cart_details', [ $this, 'get_cart_details' ] );
        add_action( 'wc_ajax_dokan_stripe_express_get_shipping_options', [ $this, 'get_shipping_options' ] );
        add_action( 'wc_ajax_dokan_stripe_express_update_shipping_method', [ $this, 'update_shipping_method' ] );
        add_action( 'wc_ajax_dokan_stripe_express_get_selected_product_data', [ $this, 'get_selected_product_data' ] );

        add_filter( 'woocommerce_gateway_title', [ $this, 'filter_gateway_title' ], 10, 2 );
        add_filter( 'woocommerce_login_redirect', [ $this, 'get_login_redirect_url' ], 10, 3 );
        add_action( 'woocommerce_checkout_order_processed', [ $this, 'add_order_meta' ], 10, 2 );
        add_filter( 'woocommerce_registration_redirect', [ $this, 'get_login_redirect_url' ], 10, 3 );
    }

    /**
     * Retrieves option value.
     *
     * @since 3.6.1
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function get_option( $key ) {
        return $this->config->get_option( $key );
    }

    /**
     * Gets the button type.
     *
     * @since 3.6.1
     *
     * @return string
     */
    public function get_button_type() {
        return ! empty( $this->get_option( 'payment_request_button_type' ) )
            ? $this->get_option( 'payment_request_button_type' )
            : 'default';
    }

    /**
     * Gets the button theme.
     *
     * @since 3.6.1
     *
     * @return string
     */
    public function get_button_theme() {
        return ! empty( $this->get_option( 'payment_request_button_theme' ) )
            ? $this->get_option( 'payment_request_button_theme' )
            : 'dark';
    }

    /**
     * Gets the button height.
     *
     * @since 3.6.1
     *
     * @return string
     */
    public function get_button_height() {
        $height = ! empty( $this->get_option( 'payment_request_button_size' ) )
            ? $this->get_option( 'payment_request_button_size' )
            : 'default';

        if ( 'medium' === $height ) {
            return '48';
        }

        if ( 'large' === $height ) {
            return '56';
        }

        // for the "default" and "catch-all" scenarios.
        return '40';
    }

    /**
     * Retrieves value of button locations option.
     *
     * @since 3.6.1
     *
     * @return array
     */
    public function get_button_locations() {
        // If the locations have not been set return the default setting.
        if ( empty( $this->get_option( 'payment_request_button_locations' ) ) ) {
            return [ 'product' ];
        }

        /*
         * If all locations are removed through the settings UI the location config will be set to
         * an empty string "". If that's the case (and if the settings are not an array for any
         * other reason) we should return an empty array.
         */
        if ( ! is_array( $this->get_option( 'payment_request_button_locations' ) ) ) {
            return [];
        }

        return $this->get_option( 'payment_request_button_locations' );
    }

    /**
     * The settings for the `button` attribute - they depend on the "settings redesign" flag value.
     *
     * @since 3.6.1
     *
     * @return array
     */
    public function get_button_settings() {
        return [
            'type'   => $this->get_button_type(),
            'theme'  => $this->get_button_theme(),
            'height' => $this->get_button_height(),
            // Default format is en_US.
            'locale' => substr( get_locale(), 0, 2 ),
        ];
    }

    /**
     * Returns true if Payment Request Buttons are supported on the current page, false
     * otherwise.
     *
     * @since 3.6.1
     *
     * @return  boolean  True if PRBs are supported on current page, false otherwise
     */
    public function should_render_payment_request_button() {
        if ( ! $this->is_page_supported() ) {
            return;
        }

        // If keys are not set bail.
        if ( ! $this->api_ready ) {
            Helper::log( 'Keys are not set correctly.' );
            return false;
        }

        // If no SSL bail.
        if ( ! $this->testmode && ! is_ssl() ) {
            Helper::log( 'Stripe Payment Request live mode requires SSL.' );
            return false;
        }

        /*
         * Don't show if on the cart or checkout page, or if page contains the cart or checkout
         * shortcodes, with items in the cart that aren't supported.
         */
        if ( Helper::has_cart_or_checkout_on_current_page() && ! $this->allowed_items_in_cart() ) {
            return false;
        }

        // Don't show on cart if disabled.
        if ( is_cart() && ( ! $this->is_payment_request_button_enabled( 'cart' ) || ! Helper::validate_cart_items() ) ) {
            return false;
        }

        // Don't show on checkout if disabled.
        if ( is_checkout() && ! $this->is_payment_request_button_enabled( 'checkout' ) ) {
            return false;
        }

        // Check requirements for product page.
        if ( $this->is_product() ) {
            // Don't show if product page PRB is disabled.
            if ( ! $this->is_payment_request_button_enabled( 'product' ) ) {
                return false;
            }

            // Don't show if product on current page is not supported.
            if ( ! $this->is_product_supported( $this->get_product() ) ) {
                return false;
            }

            if ( in_array( $this->get_product()->get_type(), [ 'variable' ], true ) ) {
                $stock_availability = array_column( $this->get_product()->get_available_variations(), 'is_in_stock' );
                // Don't show if all product variations are out-of-stock.
                if ( ! in_array( true, $stock_availability, true ) ) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Checks whether or not payment request button is enabled on certain page.
     *
     * @since 3.6.1
     *
     * @param string $page Indicates the page to check. Expected values are 'cart', 'product', and 'checkout'.
     *
     * @return boolean
     */
    public function is_payment_request_button_enabled( $page ) {
        return in_array( $page, $this->get_button_locations(), true );
    }

    /**
     * Sets the WC customer session if one is not set.
     * This is needed so nonces can be verified by AJAX Request.
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function set_session() {
        if ( ! $this->is_product() || ( isset( WC()->session ) && WC()->session->has_session() ) ) {
            return;
        }

        WC()->session->set_customer_session_cookie( true );
    }

    /**
     * Handles payment request redirect when the redirect dialog "Continue" button is clicked.
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function handle_payment_request_redirect() {
        if (
            isset( $_GET['dokan_stripe_express_payment_request_redirect_url'], $_GET['_wpnonce'] ) &&
            wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'dokan-stripe-express-set-redirect-url' )
        ) {
            $url = rawurldecode( esc_url_raw( wp_unslash( $_GET['dokan_stripe_express_payment_request_redirect_url'] ) ) );

            /*
             * Sets a redirect URL cookie for 10 minutes, which we will redirect to after authentication.
             * Users will have a 10 minute timeout to login/create account, otherwise redirect URL expires.
             */
            wc_setcookie( 'dokan_stripe_express_payment_request_redirect_url', $url, time() + MINUTE_IN_SECONDS * 10 );
            // Redirects to "my-account" page.
            wp_safe_redirect( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) );
            exit;
        }
    }

    /**
     * Load public scripts and styles.
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function scripts() {
        if ( ! $this->should_render_payment_request_button() ) {
            return;
        }

        wp_localize_script(
            'dokan-stripe-express-payment-request',
            'dokanStripeExpressPRData',
            apply_filters(
                'dokan_stripe_express_payment_request_script_params',
                $this->localized_data()
            )
        );

        wp_enqueue_script( 'dokan-stripe-express-payment-request' );

        $gateways = WC()->payment_gateways->get_available_payment_gateways();
        if ( isset( $gateways[ Helper::get_gateway_id() ] ) ) {
            $gateways[ Helper::get_gateway_id() ]->payment_scripts();
        }
    }

    /**
     * Returns the JavaScript configuration object used for any pages with a payment request button.
     *
     * @since 3.6.1
     *
     * @return array  The settings used for the payment request button in JavaScript.
     */
    public function localized_data() {
        $needs_shipping = 'no';
        if ( ! is_null( WC()->cart ) && WC()->cart->needs_shipping() ) {
            $needs_shipping = 'yes';
        }

        return [
            'ajaxUrl'             => WC_AJAX::get_endpoint( '%%endpoint%%' ),
            'stripe'              => [
                'key'              => $this->publishable_key,
                'allowPrepaidCard' => apply_filters( 'dokan_stripe_express_allow_prepaid_card', true ) ? 'yes' : 'no',
                'paymentMethod'    => Helper::get_gateway_id(),
                'apiVersion'       => Helper::get_api_version(),
            ],
            'customer'            => $this->get_customer_data(),
            'nonce'               => [
                'payment'                => wp_create_nonce( 'dokan-stripe-express-payment-request' ),
                'shipping'               => wp_create_nonce( 'dokan-stripe-express-payment-request-shipping' ),
                'updateShipping'         => wp_create_nonce( 'dokan-stripe-express-update-shipping-method' ),
                'checkout'               => wp_create_nonce( 'woocommerce-process_checkout' ),
                'addToCart'              => wp_create_nonce( 'dokan-stripe-express-add-to-cart' ),
                'getSelectedProductData' => wp_create_nonce( 'dokan-stripe-express-get-selected-product-data' ),
                'logErrors'              => wp_create_nonce( 'dokan-stripe-express-log-errors' ),
                'clearCart'              => wp_create_nonce( 'dokan-stripe-express-clear-cart' ),
            ],
            'i18n'                => [
                'error'              => [
                    'noPrepaidCard'   => __( 'Sorry, we\'re not accepting prepaid cards at this time.', 'dokan' ),
                    /* translators: Do not translate the [option] placeholder */
                    'unknownShipping' => __( 'Unknown shipping option "[option]".', 'dokan' ),
                ],
                'applePay'           => __( 'Apple Pay', 'dokan' ),
                'googlePay'          => __( 'Google Pay', 'dokan' ),
                'login'              => __( 'Log In', 'dokan' ),
                'cancel'             => __( 'Cancel', 'dokan' ),
                'makeSelection'      => esc_attr__( 'Please select some product options before adding this product to your cart.', 'dokan' ),
                'productUnavailable' => esc_attr__( 'Sorry, this product is unavailable. Please choose a different combination.', 'dokan' ),
            ],
            'checkout'            => [
                'url'              => wc_get_checkout_url(),
                'currencyCode'     => strtolower( get_woocommerce_currency() ),
                'countryCode'      => substr( get_option( 'woocommerce_default_country' ), 0, 2 ),
                'shippingNeeded'   => $needs_shipping,
                // Defaults to 'required' to match how core initializes this option.
                'payerPhoneNeeded' => 'required' === get_option( 'woocommerce_checkout_phone_field', 'required' ),
            ],
            'button'              => $this->get_button_settings(),
            'loginStatus'         => $this->get_login_confirmation_settings(),
            'isProductPage'       => $this->is_product(),
            'product'             => $this->get_product_data(),
        ];
    }

    /**
     * Renders the payment request button.
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function render_payment_request_button() {
        $gateways = WC()->payment_gateways->get_available_payment_gateways();

        if ( ! isset( $gateways[ Helper::get_gateway_id() ] ) ) {
            return;
        }

        if ( ! $this->is_page_supported() ) {
            return;
        }

        if ( ! $this->should_render_payment_request_button() ) {
            return;
        }

        ?>
        <div id="dokan-stripe-express-payment-request-wrapper" style="clear:both;padding-top:1.5em;display:none;">
            <div id="dokan-stripe-express-payment-request-button">
                <!-- A Stripe Element will be inserted here. -->
            </div>
        </div>
        <?php
    }

    /**
     * Display payment request button separator.
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function render_payment_request_button_separator() {
        $gateways = WC()->payment_gateways->get_available_payment_gateways();

        if ( ! isset( $gateways[ Helper::get_gateway_id() ] ) ) {
            return;
        }

        if ( ! is_cart() && ! is_checkout() && ! $this->is_product() && ! isset( $_GET['pay_for_order'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return;
        }

        if ( is_checkout() && ! in_array( 'checkout', $this->get_button_locations(), true ) ) {
            return;
        }
        ?>
        <p id="dokan-stripe-express-payment-request-button-separator" style="margin-top:1.5em;text-align:center;display:none;">
            &mdash; <?php esc_html_e( 'OR', 'dokan' ); ?> &mdash;
        </p>
        <?php
    }

    /**
     * Get cart details.
     *
     * @since 3.6.1
     *
     * @return mixed
     */
    public function get_cart_details() {
        check_ajax_referer( 'dokan-stripe-express-payment-request', 'security' );

        if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
            define( 'WOOCOMMERCE_CART', true );
        }

        WC()->cart->calculate_totals();

        $currency = get_woocommerce_currency();

        // Set mandatory payment details.
        $data = [
            'shipping_required' => WC()->cart->needs_shipping(),
            'order_data'        => [
                'currency'     => strtolower( $currency ),
                'country_code' => substr( get_option( 'woocommerce_default_country' ), 0, 2 ),
            ],
        ];

        $data['order_data'] += $this->build_display_items();

        wp_send_json( $data );
    }

    /**
     * Log errors coming from Payment Request
     *
     * @since 3.6.1
     *
     * @requires void
     */
    public function log_errors() {
        check_ajax_referer( 'dokan-stripe-express-log-errors', 'security' );

        $errors = isset( $_POST['errors'] ) ? wc_clean( wp_unslash( $_POST['errors'] ) ) : '';

        Helper::log( $errors );

        exit;
    }

    /**
     * Clears cart.
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function clear_cart() {
        check_ajax_referer( 'dokan-stripe-express-clear-cart', 'security' );

        WC()->cart->empty_cart();
        exit;
    }

    /**
     * Get shipping options.
     *
     * @see WC_Cart::get_shipping_packages().
     * @see WC_Shipping::calculate_shipping().
     * @see WC_Shipping::get_packages().
     *
     * @since 3.6.1
     *
     * @return void
     */
    public function get_shipping_options() {
        check_ajax_referer( 'dokan-stripe-express-payment-request-shipping', 'security' );

        $shipping_address = filter_input_array(
            INPUT_POST,
            [
                'country'   => FILTER_SANITIZE_STRING,
                'state'     => FILTER_SANITIZE_STRING,
                'postcode'  => FILTER_SANITIZE_STRING,
                'city'      => FILTER_SANITIZE_STRING,
                'address'   => FILTER_SANITIZE_STRING,
                'address_2' => FILTER_SANITIZE_STRING,
            ]
        );
        $product_view_options      = filter_input_array( INPUT_POST, [ 'is_product_page' => FILTER_SANITIZE_STRING ] );
        $should_show_itemized_view = ! isset( $product_view_options['is_product_page'] ) ? true : filter_var( $product_view_options['is_product_page'], FILTER_VALIDATE_BOOLEAN );

        $data = $this->_get_shipping_options( $shipping_address, $should_show_itemized_view );
        wp_send_json( $data );
    }

    /**
     * Gets shipping options available for specified shipping address.
     *
     * @since 3.6.1
     *
     * @param array   $shipping_address       Shipping address.
     * @param boolean $itemized_display_items Indicates whether to show subtotals or itemized views.
     *
     * @return array Shipping options data.
     * @throws Exception
     */
    protected function _get_shipping_options( $shipping_address, $itemized_display_items = false ) { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
        try {
            // Set the shipping options.
            $data = [];

            // Remember current shipping method before resetting.
            $chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
            $this->calculate_shipping( apply_filters( 'dokan_stripe_express_payment_request_shipping_posted_values', $shipping_address ) );

            $packages          = WC()->shipping->get_packages();
            $shipping_rate_ids = [];

            if ( ! empty( $packages ) && WC()->customer->has_calculated_shipping() ) {
                foreach ( $packages as $package_key => $package ) {
                    if ( empty( $package['rates'] ) ) {
                        throw new Exception( __( 'Unable to find shipping method for address.', 'dokan' ) );
                    }

                    foreach ( $package['rates'] as $key => $rate ) {
                        if ( in_array( $rate->id, $shipping_rate_ids, true ) ) {
                            // The Payment Requests will try to load indefinitely if there are duplicate shipping
                            // option IDs.
                            throw new Exception( __( 'Unable to provide shipping options for Payment Requests.', 'dokan' ) );
                        }
                        $shipping_rate_ids[]        = $rate->id;
                        $data['shipping_options'][] = [
                            'id'     => $rate->id,
                            'label'  => $rate->label,
                            'detail' => '',
                            'amount' => Helper::get_stripe_amount( $rate->cost ),
                        ];
                    }
                }
            } else {
                // Attach free shipping just to allow customer to order
                $data['shipping_options'][] = [
                    'id'     => 'free_shipping',
                    'label'  => esc_html__( 'Free Shipping', 'dokan' ),
                    'detail' => '',
                    'amount' => 0,
                ];
            }

            /*
             * The first shipping option is automatically applied on the client.
             * Keep chosen shipping method by sorting shipping options
             * if the method still available for new address.
             * Fallback to the first available shipping method.
             */
            if ( isset( $data['shipping_options'][0] ) ) {
                if ( isset( $chosen_shipping_methods[0] ) ) {
                    $chosen_method_id = $chosen_shipping_methods[0];
                    usort(
                        $data['shipping_options'],
                        function ( $option_1, $option_2 ) use ( $chosen_method_id ) {
                            if ( $option_1['id'] === $chosen_method_id ) {
                                return -1;
                            }

                            if ( $option_2['id'] === $chosen_method_id ) {
                                return 1;
                            }

                            return 0;
                        }
                    );
                }

                $first_shipping_method_id = $data['shipping_options'][0]['id'];
                $this->_update_shipping_method( [ $first_shipping_method_id ] );
            }

            WC()->cart->calculate_totals();

            $data          += $this->build_display_items( $itemized_display_items );
            $data['result'] = 'success';
        } catch ( Exception $e ) {
            $data          += $this->build_display_items( $itemized_display_items );
            $data['result'] = 'invalid_shipping_address';
        }

        return $data;
    }

    /**
     * Update shipping method.
     *
     * @since 3.6.1
     *
     * @return mixed
     */
    public function update_shipping_method() {
        check_ajax_referer( 'dokan-stripe-express-update-shipping-method', 'security' );

        if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
            define( 'WOOCOMMERCE_CART', true );
        }

        $shipping_methods = filter_input( INPUT_POST, 'shipping_method', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
        $this->_update_shipping_method( $shipping_methods );

        WC()->cart->calculate_totals();

        $product_view_options      = filter_input_array( INPUT_POST, [ 'is_product_page' => FILTER_SANITIZE_STRING ] );
        $should_show_itemized_view = ! isset( $product_view_options['is_product_page'] ) ? true : filter_var( $product_view_options['is_product_page'], FILTER_VALIDATE_BOOLEAN );

        $data           = [];
        $data          += $this->build_display_items( $should_show_itemized_view );
        $data['result'] = 'success';

        wp_send_json( $data );
    }

    /**
     * Updates shipping method in WC session.
     *
     * @since 3.6.1
     *
     * @param array $shipping_methods Array of selected shipping methods ids.
     *
     * @return void
     */
    public function _update_shipping_method( $shipping_methods ) { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
        $chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );

        if ( is_array( $shipping_methods ) ) {
            foreach ( $shipping_methods as $i => $value ) {
                $chosen_shipping_methods[ $i ] = wc_clean( $value );
            }
        }

        WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );
    }

    /**
     * Gets the selected product data.
     *
     * @since 3.6.1
     *
     * @return mixed
     */
    public function get_selected_product_data() {
        check_ajax_referer( 'dokan-stripe-express-get-selected-product-data', 'security' );

        try {
            $product_id   = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
            $qty          = ! isset( $_POST['qty'] ) ? 1 : apply_filters( 'woocommerce_add_to_cart_quantity', absint( $_POST['qty'] ), $product_id );
            $addon_value  = isset( $_POST['addon_value'] ) ? max( floatval( $_POST['addon_value'] ), 0 ) : 0;
            $product      = wc_get_product( $product_id );
            $variation_id = null;

            if ( ! is_a( $product, 'WC_Product' ) ) {
                /* translators: %d is the product Id */
                throw new Exception( sprintf( __( 'Product with the ID (%d) cannot be found.', 'dokan' ), $product_id ) );
            }

            if ( 'variable' === $product->get_type() && isset( $_POST['attributes'] ) ) {
                $attributes = wc_clean( wp_unslash( $_POST['attributes'] ) );

                $data_store   = \WC_Data_Store::load( 'product' );
                $variation_id = $data_store->find_matching_product_variation( $product, $attributes );

                if ( ! empty( $variation_id ) ) {
                    $product = wc_get_product( $variation_id );
                }
            }

            // Force quantity to 1 if sold individually and check for existing item in cart.
            if ( $product->is_sold_individually() ) {
                $qty = apply_filters( 'dokan_stripe_express_payment_request_add_to_cart_sold_individually_quantity', 1, $qty, $product_id, $variation_id );
            }

            if ( ! $product->has_enough_stock( $qty ) ) {
                /* translators: 1: product name 2: quantity in stock */
                throw new Exception( sprintf( __( 'You cannot add that amount of "%1$s"; to the cart because there is not enough stock (%2$s remaining).', 'dokan' ), $product->get_name(), wc_format_stock_quantity_for_display( $product->get_stock_quantity(), $product ) ) );
            }

            $total = $qty * $this->get_product_price( $product ) + $addon_value;

            $quantity_label = 1 < $qty ? ' (x' . $qty . ')' : '';

            $data  = [];
            $items = [];

            $items[] = [
                'label'  => $product->get_name() . $quantity_label,
                'amount' => Helper::get_stripe_amount( $total ),
            ];

            if ( wc_tax_enabled() ) {
                $items[] = [
                    'label'   => __( 'Tax', 'dokan' ),
                    'amount'  => 0,
                    'pending' => true,
                ];
            }

            if ( wc_shipping_enabled() && $product->needs_shipping() ) {
                $items[] = [
                    'label'   => __( 'Shipping', 'dokan' ),
                    'amount'  => 0,
                    'pending' => true,
                ];

                $data['shippingOptions'] = [
                    'id'     => 'pending',
                    'label'  => __( 'Pending', 'dokan' ),
                    'detail' => '',
                    'amount' => 0,
                ];
            }

            $data['displayItems'] = $items;
            $data['total']        = [
                'label'   => $this->total_label,
                'amount'  => Helper::get_stripe_amount( $total ),
                'pending' => true,
            ];

            $data['requestShipping'] = ( wc_shipping_enabled() && $product->needs_shipping() );
            $data['currency']        = strtolower( get_woocommerce_currency() );
            $data['country_code']    = substr( get_option( 'woocommerce_default_country' ), 0, 2 );

            wp_send_json( $data );
        } catch ( Exception $e ) {
            wp_send_json( [ 'error' => wp_strip_all_tags( $e->getMessage() ) ] );
        }
    }

    /**
     * Adds the current product to the cart. Used on product detail page.
     *
     * @since 3.6.1
     *
     * @return mixed
     */
    public function add_to_cart() {
        check_ajax_referer( 'dokan-stripe-express-add-to-cart', 'security' );

        if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
            define( 'WOOCOMMERCE_CART', true );
        }

        WC()->shipping->reset_shipping();

        $product_id   = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
        $qty          = ! isset( $_POST['qty'] ) ? 1 : absint( $_POST['qty'] );
        $product      = wc_get_product( $product_id );
        $product_type = $product->get_type();

        // First empty the cart to prevent wrong calculation.
        WC()->cart->empty_cart();

        if ( ( 'variable' === $product_type || 'variable-subscription' === $product_type ) && isset( $_POST['attributes'] ) ) {
            $attributes = wc_clean( wp_unslash( $_POST['attributes'] ) );

            $data_store   = \WC_Data_Store::load( 'product' );
            $variation_id = $data_store->find_matching_product_variation( $product, $attributes );

            WC()->cart->add_to_cart( $product->get_id(), $qty, $variation_id, $attributes );
        }

        if ( 'simple' === $product_type || 'subscription' === $product_type ) {
            WC()->cart->add_to_cart( $product->get_id(), $qty );
        }

        WC()->cart->calculate_totals();

        $data           = [];
        $data          += $this->build_display_items();
        $data['result'] = 'success';

        wp_send_json( $data );
    }

    /**
     * Settings array for the user authentication dialog and redirection.
     *
     * @since 3.6.1
     *
     * @return array
     */
    public function get_login_confirmation_settings() {
        if ( is_user_logged_in() || ! $this->is_authentication_required() ) {
            return false;
        }

        /* translators: The text encapsulated in `**` can be replaced with "Apple Pay" or "Google Pay". Please translate this text, but don't remove the `**`. */
        $message      = __( 'To complete your transaction with **the selected payment method**, you must log in or create an account with our site.', 'dokan' );
        $redirect_url = add_query_arg(
            [
                '_wpnonce'                                          => wp_create_nonce( 'dokan-stripe-express-set-redirect-url' ),
                'dokan_stripe_express_payment_request_redirect_url' => rawurlencode( home_url( add_query_arg( [] ) ) ),              // Current URL to redirect to after login.
            ],
            home_url()
        );

        return [
            'message'      => $message,
            'redirect_url' => $redirect_url,
        ];
    }

    /**
     * Filters the gateway title to reflect Payment Request type.
     *
     * @since 3.6.1
     *
     * @return string
     */
    public function filter_gateway_title( $title, $id ) {
        global $post;

        if ( ! is_object( $post ) ) {
            return $title;
        }

        $order        = wc_get_order( $post->ID );
        $method_title = is_object( $order ) ? $order->get_payment_method_title() : '';

        if ( Helper::get_gateway_id() === $id && ! empty( $method_title ) ) {
            return $method_title;
        }

        return $title;
    }

    /**
     * Add needed order meta.
     *
     * @since 3.6.1
     *
     * @param integer $order_id    The order ID.
     * @param array   $posted_data The posted data from checkout form.
     *
     * @return  void
     */
    public function add_order_meta( $order_id, $posted_data ) {
        if ( empty( $_POST['payment_request_type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
            return;
        }

        $order = wc_get_order( $order_id );

        $payment_request_type = sanitize_text_field( wp_unslash( $_POST['payment_request_type'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

        if ( 'apple_pay' === $payment_request_type ) {
            $order->set_payment_method_title( 'Apple Pay (Stripe)' );
            $order->save();
        } elseif ( 'google_pay' === $payment_request_type ) {
            $order->set_payment_method_title( 'Google Pay (Stripe)' );
            $order->save();
        } elseif ( 'payment_request_api' === $payment_request_type ) {
            $order->set_payment_method_title( 'Payment Request (Stripe)' );
            $order->save();
        }
    }

    /**
     * Returns the login redirect URL.
     *
     * @since 3.6.1
     *
     * @param string $redirect Default redirect URL.
     *
     * @return string Redirect URL.
     */
    public function get_login_redirect_url( $redirect ) {
        $url = isset( $_COOKIE['dokan_stripe_express_payment_request_redirect_url'] )
            ? esc_url_raw( wp_unslash( $_COOKIE['dokan_stripe_express_payment_request_redirect_url'] ) )
            : '';

        if ( empty( $url ) ) {
            return $redirect;
        }

        wc_setcookie( 'dokan_stripe_express_payment_request_redirect_url', null );

        return $url;
    }
}
