<?php

namespace WPFormsStripe;

/**
 * Stripe related helper methods.
 *
 * @package    WPFormsStripe
 * @author     WPForms
 * @since      2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2018, WPForms LLC
 */
class Helpers {

	/**
	 * Check if Stripe keys have been configured in the plugin settings.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public static function has_stripe_keys() {

		// Test mode, check for test keys.
		if (
			wpforms_setting( 'stripe-test-mode', false )
			&& wpforms_setting( 'stripe-test-secret-key', false )
			&& wpforms_setting( 'stripe-test-publishable-key', false )
		) {
			return true;
		}

		// Live mode, check for live keys.
		if (
			! wpforms_setting( 'stripe-test-mode', false )
			&& wpforms_setting( 'stripe-live-secret-key', false )
			&& wpforms_setting( 'stripe-live-publishable-key', false )
		) {
			return true;
		}

		return false;
	}
}
