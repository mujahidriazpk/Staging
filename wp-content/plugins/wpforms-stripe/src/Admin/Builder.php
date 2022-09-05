<?php

namespace WPFormsStripe\Admin;

use WPFormsStripe\Helpers;

/**
 * Stripe Form Builder related functionality.
 *
 * @package    WPFormsStripe
 * @author     WPForms
 * @since      2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2018, WPForms LLC
 */
class Builder {

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

		add_filter( 'wpforms_field_credit_card_enable', '__return_true' );
		add_filter( 'wpforms_builder_strings', array( $this, 'javascript_strings' ) );
		add_action( 'wpforms_builder_enqueues', array( $this, 'enqueues' ) );
	}

	/**
	 * Add our localized strings to be available in the form builder.
	 *
	 * @since 2.0.0
	 *
	 * @param array $strings Form builder JS strings.
	 *
	 * @return array
	 */
	public function javascript_strings( $strings ) {

		$strings['stripe_recurring_email'] = esc_html__( 'When recurring subscription payments are enabled, the Customer Email is required. Please go to the Stripe payment settings and select a Customer Email.', 'wpforms-stripe' );

		return $strings;
	}

	/**
	 * Enqueue assets for the builder.
	 *
	 * @since 2.0.0
	 */
	public function enqueues() {

		$min = wpforms_get_min_suffix();

		wp_enqueue_script(
			'wpforms-builder-stripe',
			wpforms_stripe()->url . "assets/js/admin-builder-stripe{$min}.js",
			array( 'jquery' ),
			WPFORMS_STRIPE_VERSION,
			false
		);
	}

	/**
	 * Outputs form builder settings panel content.
	 *
	 * @since 2.0.0
	 *
	 * @param array $form_data Form data.
	 */
	public static function content( $form_data ) {

		if ( ! Helpers::has_stripe_keys() ) {
			echo '<p class="wpforms-alert wpforms-alert-info">';
			printf(
				wp_kses(
					/* translators: %s - Admin area Payments settings page URL. */
					__( 'Before enabling Stripe payments you need to setup your Stripe API keys (both Live and Test) on the <a href="%s">Settings page</a>.', 'wpforms-stripe' ),
					array(
						'a' => array(
							'href' => array(),
						),
					)
				),
				esc_url( admin_url( 'admin.php?page=wpforms-settings&view=payments' ) )
			);
			echo '</p>';
			return;
		}

		echo '<p class="wpforms-alert wpforms-alert-info" id="stripe-credit-card-alert">';
		esc_html_e( 'To use Stripe payments you need to add a Credit Card field to the form', 'wpforms-stripe' );
		echo '</p>';

		wpforms_panel_field(
			'checkbox',
			'stripe',
			'enable',
			$form_data,
			esc_html__( 'Enable Stripe payments', 'wpforms-stripe' ),
			array(
				'parent'  => 'payments',
				'default' => '0',
			)
		);

		wpforms_panel_field(
			'text',
			'stripe',
			'payment_description',
			$form_data,
			esc_html__( 'Payment Description', 'wpforms-stripe' ),
			array(
				'parent'  => 'payments',
				'tooltip' => esc_html__( 'Enter your payment description. Eg: Donation for the soccer team. Only used for standard one-time payments.', 'wpforms-stripe' ),
			)
		);

		wpforms_panel_field(
			'select',
			'stripe',
			'receipt_email',
			$form_data,
			esc_html__( 'Stripe Payment Receipt', 'wpforms-stripe' ),
			array(
				'parent'      => 'payments',
				'field_map'   => array( 'email' ),
				'placeholder' => esc_html__( '--- Select Email ---', 'wpforms-stripe' ),
				'tooltip'     => esc_html__( 'If you would like to have Stripe send a receipt after payment, select the email field to use. This is optional but recommended. Only used for standard one-time payments.', 'wpforms-stripe' ),
			)
		);

		wpforms_conditional_logic()->builder_block(
			array(
				'form'        => $form_data,
				'type'        => 'panel',
				'panel'       => 'stripe',
				'parent'      => 'payments',
				'actions'     => array(
					'go'   => esc_html__( 'Process', 'wpforms-stripe' ),
					'stop' => esc_html__( 'Don\'t process', 'wpforms-stripe' ),
				),
				'action_desc' => esc_html__( 'this charge if', 'wpforms-stripe' ),
			)
		);

		echo '<h2>Subscriptions</h2>';

		wpforms_panel_field(
			'checkbox',
			'stripe',
			'enable',
			$form_data,
			esc_html__( 'Enable recurring subscription payments', 'wpforms-stripe' ),
			array(
				'parent'     => 'payments',
				'subsection' => 'recurring',
				'default'    => '0',
			)
		);

		wpforms_panel_field(
			'text',
			'stripe',
			'name',
			$form_data,
			esc_html__( 'Plan Name', 'wpforms-stripe' ),
			array(
				'parent'     => 'payments',
				'subsection' => 'recurring',
				'tooltip'    => esc_html__( 'Enter the subscription name. Eg: Email Newsletter. Subscription period and price are automatically appended. If left empty the form name will be used.', 'wpforms-stripe' ),
			)
		);

		wpforms_panel_field(
			'select',
			'stripe',
			'period',
			$form_data,
			esc_html__( 'Recurring Period', 'wpforms-stripe' ),
			array(
				'parent'     => 'payments',
				'subsection' => 'recurring',
				'default'    => 'yearly',
				'options'    => array(
					'daily'      => esc_html__( 'Daily', 'wpforms-stripe' ),
					'weekly'     => esc_html__( 'Weekly', 'wpforms-stripe' ),
					'monthly'    => esc_html__( 'Monthly', 'wpforms-stripe' ),
					'quarterly'  => esc_html__( 'Quarterly', 'wpforms-stripe' ),
					'semiyearly' => esc_html__( 'Semi-Yearly', 'wpforms-stripe' ),
					'yearly'     => esc_html__( 'Yearly', 'wpforms-stripe' ),
				),
				'tooltip'    => esc_html__( 'How often you would like the charge to recur.', 'wpforms-stripe' ),
			)
		);

		wpforms_panel_field(
			'select',
			'stripe',
			'email',
			$form_data,
			esc_html__( 'Customer Email', 'wpforms-stripe' ),
			array(
				'parent'      => 'payments',
				'subsection'  => 'recurring',
				'field_map'   => array( 'email' ),
				'placeholder' => esc_html__( '--- Select Email ---', 'wpforms-stripe' ),
				'tooltip'     => esc_html__( 'Select the field that contains the customers email address. This field is required.', 'wpforms-stripe' ),
			)
		);

		wpforms_conditional_logic()->builder_block(
			array(
				'form'        => $form_data,
				'type'        => 'panel',
				'panel'       => 'stripe',
				'parent'      => 'payments',
				'subsection'  => 'recurring',
				'actions'     => array(
					'go'   => esc_html__( 'Process', 'wpforms-stripe' ),
					'stop' => esc_html__( 'Don\'t process', 'wpforms-stripe' ),
				),
				'action_desc' => esc_html__( 'payment as recurring if', 'wpforms-stripe' ),
			)
		);
	}
}
