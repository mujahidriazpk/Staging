<?php

namespace WPFormsStripe;

/**
 * Stripe Form Builder related functionality.
 *
 * @package    WPFormsStripe
 * @author     WPForms
 * @since      2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2018, WPForms LLC
 */
class StripePayment extends \WPForms_Payment {

	/**
	 * Initialize.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		$this->version  = WPFORMS_STRIPE_VERSION;
		$this->name     = 'Stripe';
		$this->slug     = 'stripe';
		$this->priority = 10;
		$this->icon     = wpforms_stripe()->url . 'assets/images/addon-icon-stripe.png';
	}

	/**
	 * Display content inside the panel content area.
	 *
	 * @since 2.0.0
	 */
	public function builder_content() {

		Admin\Builder::content( $this->form_data );
	}
}
