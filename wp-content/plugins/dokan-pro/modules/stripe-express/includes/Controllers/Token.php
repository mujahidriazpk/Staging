<?php

namespace WeDevs\DokanPro\Modules\StripeExpress\Controllers;

defined( 'ABSPATH' ) || exit; // Exit if called directly

use WC_Payment_Tokens;
use WeDevs\DokanPro\Modules\StripeExpress\Support\Helper;
use WeDevs\DokanPro\Modules\StripeExpress\Processors\Customer;

/**
 * Handles and process WC payment tokens API.
 * Seen in checkout page and my account->add payment method page.
 *
 * @since 3.6.1
 *
 * @package WeDevs\DokanPro\Modules\StripeExpress\Controllers
 */
class Token {

    /**
     * Class constructor.
     *
     * @since 3.6.1
     */
    public function __construct() {
        $this->hooks();
    }

    /**
     * Registers all necessary hooks.
     *
     * @since 3.6.1
     *
     * @return void
     */
    private function hooks() {
        add_filter( 'woocommerce_get_customer_payment_tokens', [ $this, 'get_customer_payment_tokens' ], 10, 3 );
        add_filter( 'woocommerce_payment_methods_list_item', [ $this, 'get_account_saved_payment_methods_list_item_sepa' ], 10, 2 );
        add_filter( 'woocommerce_get_credit_card_type_label', [ $this, 'normalize_sepa_label' ] );
        add_action( 'woocommerce_payment_token_deleted', [ $this, 'payment_token_deleted' ], 10, 2 );
        add_action( 'woocommerce_payment_token_set_default', [ $this, 'payment_token_set_default' ] );
    }

    /**
     * Normalizes the SEPA IBAN label on My Account page.
     *
     * @since 3.6.1
     *
     * @param string $label
     *
     * @return string
     */
    public function normalize_sepa_label( $label ) {
        if ( 'sepa iban' === strtolower( $label ) ) {
            return 'SEPA IBAN';
        }

        return $label;
    }

    /**
     * Extract the payment token from the provided request.
     *
     * @todo Once php requirement is bumped to >= 7.1.0 set return type to ?\WC_Payment_Token
     * since the return type is nullable, as per
     * https://www.php.net/manual/en/functions.returning-values.php#functions.returning-values.type-declaration
     *
     * @since 3.6.1
     *
     * @param array $request Associative array containing payment request information.
     *
     * @return WC_Payment_Token|NULL
     */
    public static function get_from_request( array $request ) {
        $payment_method    = ! is_null( $request['payment_method'] ) ? $request['payment_method'] : null;
        $token_request_key = "wc-$payment_method-payment-token";

        if (
            ! isset( $request[ $token_request_key ] ) ||
            'new' === $request[ $token_request_key ]
        ) {
            return null;
        }

        //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        $token = WC_Payment_Tokens::get( sanitize_text_field( $request[ $token_request_key ] ) );

        // If the token doesn't belong to this gateway or the current user it's invalid.
        if ( ! $token || $payment_method !== $token->get_gateway_id() || $token->get_user_id() !== get_current_user_id() ) {
            return null;
        }

        return $token;
    }

    /**
     * Checks if customer has saved payment methods.
     *
     * @since 3.6.1
     *
     * @param int $customer_id
     *
     * @return bool
     */
    public static function customer_has_saved_methods( $customer_id ) {
        $gateways = [ 'dokan_stripe_express', 'dokan_stripe_express_sepa' ];

        if ( empty( $customer_id ) ) {
            return false;
        }

        $has_token = false;

        foreach ( $gateways as $gateway ) {
            $tokens = WC_Payment_Tokens::get_customer_tokens( $customer_id, $gateway );

            if ( ! empty( $tokens ) ) {
                $has_token = true;
                break;
            }
        }

        return $has_token;
    }

    /**
     * Gets saved tokens from Stripe, if they don't already exist in WooCommerce.
     *
     * @param array  $tokens     Array of tokens
     * @param string $user_id    WC User ID
     * @param string $gateway_id WC Gateway ID
     *
     * @return array
     */
    public function get_customer_payment_tokens( $tokens, $user_id, $gateway_id ) {
        if ( ! is_user_logged_in() || ( ! empty( $gateway_id ) && Helper::get_gateway_id() !== $gateway_id ) ) {
            return $tokens;
        }

        if ( count( $tokens ) >= get_option( 'posts_per_page' ) ) {
            /*
             * The tokens data store is not paginated and
             * only the first "post_per_page" (defaults to 10) tokens are retrieved.
             * Having 10 saved credit cards is considered an unsupported edge case,
             * new ones that have been stored in Stripe won't be added.
             */
            return $tokens;
        }

        $reusable_payment_methods = Helper::get_reusable_payment_methods();
        $customer                 = Customer::set( $user_id );
        $remaining_tokens         = [];

        foreach ( $tokens as $token ) {
            if ( Helper::get_gateway_id() !== $token->get_gateway_id() ) {
                continue;
            }

            $payment_method_type = $this->get_payment_method_type_from_token( $token );
            if ( ! in_array( $payment_method_type, $reusable_payment_methods, true ) ) {
                // Remove saved token from list, if payment method is not enabled.
                unset( $tokens[ $token->get_id() ] );
            } else {
                /*
                 * Store relevant existing tokens here.
                 * We will use this list to check
                 * whether these methods still exist on Stripe's end.
                 */
                $remaining_tokens[ $token->get_token() ] = $token;
            }
        }

        $retrievable_payment_method_types = [];
        $payment_methods                  = Helper::get_available_method_instances();
        foreach ( $reusable_payment_methods as $payment_method_id ) {
            $payment_method = $payment_methods[ $payment_method_id ];
            if ( ! in_array( $payment_method->get_retrievable_type(), $retrievable_payment_method_types, true ) ) {
                $retrievable_payment_method_types[] = $payment_method->get_retrievable_type();
            }
        }

        foreach ( $retrievable_payment_method_types as $payment_method_id ) {
            $customers_payment_methods = $customer->get_payment_methods( $payment_method_id );

            // Prevent unnecessary recursion, WC_Payment_Token::save() ends up calling 'get_customer_payment_tokens' in some cases.
            remove_action( 'woocommerce_get_customer_payment_tokens', [ $this, 'get_customer_payment_tokens' ], 10, 3 );
            foreach ( $customers_payment_methods as $method ) {
                if ( ! isset( $remaining_tokens[ $method->id ] ) ) {
                    $payment_method_type = $this->get_original_payment_method_type( $method );
                    if ( ! in_array( $payment_method_type, $reusable_payment_methods, true ) ) {
                        continue;
                    }
                    // Create new token for new payment method and add to list.
                    $payment_method             = $payment_methods[ $payment_method_type ];
                    $token                      = $payment_method->create_payment_token_for_user( $user_id, $method );
                    $tokens[ $token->get_id() ] = $token;
                } else {
                    /*
                     * Count that existing token for payment method is still present on Stripe.
                     * Remaining IDs in $remaining_tokens no longer exist with Stripe and will be eliminated.
                     */
                    unset( $remaining_tokens[ $method->id ] );
                }
            }
            add_action( 'woocommerce_get_customer_payment_tokens', [ $this, 'get_customer_payment_tokens' ], 10, 3 );
        }

        /*
         * Eliminate remaining payment methods no longer known by Stripe.
         * Prevent unnecessary recursion, when deleting tokens.
         */
        remove_action( 'woocommerce_payment_token_deleted', [ $this, 'payment_token_deleted' ], 10, 2 );
        foreach ( $remaining_tokens as $token ) {
            unset( $tokens[ $token->get_id() ] );
            $token->delete();
        }
        add_action( 'woocommerce_payment_token_deleted', [ $this, 'payment_token_deleted' ], 10, 2 );

        return $tokens;
    }

    /**
     * Returns original type of payment method from Stripe payment method response,
     * after checking whether payment method is SEPA method generated from another type.
     *
     * @param object $payment_method Stripe payment method JSON object.
     *
     * @return string Payment method type/ID
     */
    private function get_original_payment_method_type( $payment_method ) {
        if ( Helper::get_sepa_payment_method_type() === $payment_method->type ) {
            if ( ! is_null( $payment_method->sepa_debit->generated_from->charge ) ) {
                return $payment_method->sepa_debit->generated_from->charge->payment_method_details->type;
            }
            if ( ! is_null( $payment_method->sepa_debit->generated_from->setup_attempt ) ) {
                return $payment_method->sepa_debit->generated_from->setup_attempt->payment_method_details->type;
            }
        }
        return $payment_method->type;
    }

    /**
     * Returns original Stripe payment method type from payment token
     *
     * @param object $payment_token WC Payment Token (CC or SEPA)
     *
     * @return string
     */
    private function get_payment_method_type_from_token( $payment_token ) {
        $type = $payment_token->get_type();
        if ( 'CC' === $type ) {
            return 'card';
        } elseif ( 'sepa' === $type ) {
            return $payment_token->get_payment_method_type();
        } else {
            return $type;
        }
    }

    /**
     * Controls the output for SEPA on the my account page.
     *
     * @since 3.6.1
     *
     * @param  array                $item          Individual list item from woocommerce_saved_payment_methods_list
     * @param  \WC_Payment_Token_CC $payment_token The payment token associated with this method entry
     *
     * @return array                           Filtered item
     */
    public function get_account_saved_payment_methods_list_item_sepa( $item, $payment_token ) {
        if ( 'sepa' === strtolower( $payment_token->get_type() ) ) {
            $item['method']['last4'] = $payment_token->get_last4();
            $item['method']['brand'] = esc_html__( 'SEPA IBAN', 'dokan' );
        }

        return $item;
    }

    /**
     * Delete token from Stripe.
     *
     * @since 3.6.1
     *
     * @param string            $token_id
     * @param \WC_Payment_Token $token
     *
     * @return void
     */
    public function payment_token_deleted( $token_id, $token ) {
        $customer = Customer::set( get_current_user_id() );
        if ( Helper::get_gateway_id() === $token->get_gateway_id() ) {
            $customer->detach_payment_method( $token->get_token() );
        }
    }

    /**
     * Set as default in Stripe.
     *
     * @since 3.6.1
     *
     * @param string $token_id
     *
     * @return void
     */
    public function payment_token_set_default( $token_id ) {
        $token    = WC_Payment_Tokens::get( $token_id );
        $customer = Customer::set( get_current_user_id() );

        if ( Helper::get_gateway_id() === $token->get_gateway_id() ) {
            $customer->set_default_payment_method( $token->get_token() );
        }
    }
}
