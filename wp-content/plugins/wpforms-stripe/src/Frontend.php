<?php

namespace WPFormsStripe;

/**
 * Stripe form frontend related functionality.
 *
 * @package    WPFormsStripe
 * @author     WPForms
 * @since      2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2018, WPForms LLC
 */
class Frontend {

	/**
	 * URL to Stripe JS library.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	const STRIPE_JS_URL = 'https://js.stripe.com/v2/';

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->init();
	}

	/**
	 * Initialize.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		add_action( 'wpforms_frontend_container_class', array( $this, 'form_container_class' ), 10, 2 );
		add_action( 'wpforms_wp_footer', array( $this, 'enqueues' ) );
	}

	/**
	 * Add class to form container if Stripe is enabled.
	 *
	 * @since 2.0.0
	 *
	 * @param array $class     Array of form classes.
	 * @param array $form_data Form data of current form.
	 *
	 * @return array
	 */
	public function form_container_class( $class, $form_data ) {

		if ( false === wpforms_has_field_type( 'credit-card', $form_data ) ) {
			return $class;
		}

		if ( ! Helpers::has_stripe_keys() ) {
			return $class;
		}

		if ( ! empty( $form_data['payments']['stripe']['enable'] ) ) {
			$class[] = 'wpforms-stripe';
		}

		return $class;
	}

	/**
	 * Enqueue assets in the frontend if Stripe is in use on the page.
	 *
	 * @since 2.0.0
	 *
	 * @param array $forms Form data of forms on current page.
	 */
	public function enqueues( $forms ) {

		if ( false === wpforms_has_field_type( 'credit-card', $forms, true ) ) {
			return;
		}

		$stripe = false;

		foreach ( $forms as $form ) {
			if ( ! empty( $form['payments']['stripe']['enable'] ) ) {
				$stripe = true;
				break;
			}
		}

		if ( ! $stripe ) {
			return;
		}

		if ( ! Helpers::has_stripe_keys() ) {
			return;
		}

		if ( wpforms_setting( 'stripe-test-mode', false ) ) {
			$publishable_key = wpforms_setting( 'stripe-test-publishable-key', false );
		} else {
			$publishable_key = wpforms_setting( 'stripe-live-publishable-key', false );
		}

		wp_enqueue_script(
			'stripe-js',
			self::STRIPE_JS_URL,
			array( 'jquery' )
		);

		$min = wpforms_get_min_suffix();

		wp_enqueue_script(
			'wpforms-stripe',
			wpforms_stripe()->url . "assets/js/wpforms-stripe{$min}.js",
			array( 'jquery', 'stripe-js' ),
			WPFORMS_STRIPE_VERSION
		);

		wp_localize_script( 'wpforms-stripe', 'wpforms_stripe', array(
			'publishable_key' => sanitize_text_field( $publishable_key ),
		) );
	}
}
