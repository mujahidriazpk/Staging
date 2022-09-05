<?php

namespace WPFormsStripe;

/**
 * Stripe payment processing.
 *
 * @package    WPFormsStripe
 * @author     WPForms
 * @since      2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2018, WPForms LLC
 */
class Process {

	/**
	 * Payment amount.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $amount = '';

	/**
	 * Payment token.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $token = '';

	/**
	 * Payment error message.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $error = false;

	/**
	 * Payment mode.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $mode = '';

	/**
	 * Form ID.
	 *
	 * @since 2.0.0
	 *
	 * @var int
	 */
	public $form_id = 0;

	/**
	 * Form Stripe payment settings.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	public $settings = array();

	/**
	 * Sanitized submitted field values and data.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	public $fields = array();

	/**
	 * Form data.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	public $form_data = array();

	/**
	 * Entry meta data with payment details.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	public $entry_data = array();

	/**
	 * Stripe charge payment object.
	 *
	 * @since 2.0.0
	 *
	 * @var \Stripe\StripeObject
	 */
	public $charge = false;

	/**
	 * Stripe subscription object.
	 *
	 * @since 2.0.0
	 *
	 * @var \Stripe\StripeObject
	 */
	public $subscription = false;

	/**
	 * Stripe customer object.
	 *
	 * @since 2.0.0
	 *
	 * @var \Stripe\StripeObject
	 */
	public $customer = false;

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

		add_action( 'wpforms_process', array( $this, 'process_entry' ), 10, 4 );
		add_action( 'wpforms_process_complete', array( $this, 'process_entry_meta' ), 10, 4 );
	}

	/**
	 * Checks if a payment exists with an entry, if so validate and process.
	 *
	 * @since 2.0.0
	 *
	 * @param array $fields    Final/sanitized submitted field data.
	 * @param array $entry     Copy of original $_POST.
	 * @param array $form_data Form data.
	 */
	public function process_entry( $fields, $entry, $form_data ) {

		// Check if payment method exists and is enabled.
		if ( empty( $form_data['payments']['stripe']['enable'] ) ) {
			return;
		}

		$this->form_id   = (int) $form_data['id'];
		$this->fields    = $fields;
		$this->form_data = $form_data;
		$this->settings  = $form_data['payments']['stripe'];
		$this->mode      = wpforms_setting( 'stripe-test-mode', false ) ? 'test' : 'live';

		// Check for conditional logic.
		if (
			! empty( $this->settings['conditional_logic'] ) &&
			! empty( $this->settings['conditional_type'] ) &&
			! empty( $this->settings['conditionals'] )
		) {
			// All conditional logic checks passed, continue with processing.
			$process = wpforms_conditional_logic()->process( $this->fields, $this->form_data, $this->settings['conditionals'] );

			if ( 'stop' === $this->settings['conditional_type'] ) {
				$process = ! $process;
			}

			if ( ! $process ) {
				wpforms_log(
					esc_html__( 'Stripe payment stopped by conditional logic.', 'wpforms-stripe' ),
					$this->fields,
					array(
						'type'    => array( 'payment', 'conditional_logic' ),
						'form_id' => $this->form_id,
					)
				);

				return;
			}
		}

		// Check for Stripe token.
		if ( empty( $entry['stripeToken'] ) ) {
			$this->error = esc_html__( 'Stripe payment stopped, missing token.', 'wpforms-stripe' );
		} else {
			$this->token = $entry['stripeToken'];
		}

		// Check for Stripe keys.
		if ( ! Helpers::has_stripe_keys() ) {
			$this->error = esc_html__( 'Stripe payment stopped, missing keys.', 'wpforms-stripe' );
		}

		// Check that, despite how the form is configured, the form and
		// entry actually contain payment fields, otherwise no need to proceed.
		if ( ! wpforms_has_payment( 'form', $this->form_data ) || ! wpforms_has_payment( 'entry', $this->fields ) ) {
			$this->error = esc_html__( 'Stripe payment stopped, missing payment fields.', 'wpforms-stripe' );
		}

		// Check total charge amount.
		$this->amount = wpforms_get_total_payment( $this->fields );
		if ( empty( $this->amount ) || wpforms_sanitize_amount( 0 ) == $this->amount ) {
			$this->error = esc_html__( 'Stripe payment stopped, invalid/empty amount.', 'wpforms-stripe' );
		}

		// Before proceeding, check if any basic errors were detected.
		if ( $this->error ) {

			// Log error if we have one and stop.
			wpforms_log(
				$this->error,
				'',
				array(
					'type'    => array( 'error', 'payment' ),
					'form_id' => $this->form_id,
				)
			);

			// Check if the form contains a required credit card. If it does
			// and there was an error, return the error to the user and prevent
			// the form from being submitted. This should not occur under normal
			// circumstances.
			foreach ( $this->form_data['fields'] as $field ) {
				if ( 'credit-card' === $field['type'] && ! empty( $field['required'] ) ) {
					wpforms()->process->errors[ $this->form_id ]['footer'] = $this->error;
				}
			}

			return;
		}

		// Load Stripe PHP library.
		if ( ! class_exists( 'Stripe\Stripe', false ) ) {
			require_once wpforms_stripe()->path . 'vendor/init.php';
		}

		\Stripe\Stripe::setApiKey( wpforms_setting( 'stripe-' . $this->mode . '-secret-key' ) );

		\Stripe\Stripe::setAppInfo(
			'WPForms Stripe WordPress Plugin',
			WPFORMS_STRIPE_VERSION,
			'https://wpforms.com/addons/stripe-addon/'
		);

		// Proceed to executing the purchase.
		if ( ! empty( $this->settings['recurring']['enable'] ) ) {
			$this->process_payment_subscription();
		} else {
			$this->process_payment_single();
			
			//Mujahid Code To apply Subscription
			$this->process_payment_subscription();
		}
	}

	/**
	 * Update entry details and add meta for a successful payment.
	 *
	 * @since 2.0.0
	 *
	 * @param array  $fields    Final/sanitized submitted field data.
	 * @param array  $entry     Copy of original $_POST.
	 * @param array  $form_data Form data.
	 * @param string $entry_id  Entry ID.
	 */
	public function process_entry_meta( $fields, $entry, $form_data, $entry_id ) {

		if ( $this->charge && ! empty( $entry_id ) ) {

			wpforms()->entry->update(
				$entry_id,
				array(
					'status' => 'completed',
					'type'   => 'payment',
					'meta'   => wp_json_encode(
						array(
							'payment_type'         => 'stripe',
							'payment_total'        => $this->amount,
							'payment_currency'     => wpforms_setting( 'currency', 'USD' ),
							'payment_transaction'  => sanitize_text_field( $this->charge->id ),
							'payment_mode'         => 'live' === $this->mode ? 'production' : 'test',
							'payment_subscription' => ! empty( $this->subscription->id ) ? sanitize_text_field( $this->subscription->id ) : '',
							'payment_customer'     => ! empty( $this->customer->id ) ? sanitize_text_field( $this->customer->id ) : '',
							'payment_period'       => ! empty( $this->subscription->id ) ? sanitize_text_field( $this->settings['recurring']['period'] ) : '',
						)
					),
				)
			);

			// Update the Stripe charge meta data to include the Entry ID.
			$charge                        = \Stripe\Charge::retrieve( $this->charge->id );
			$charge->metadata['entry_id']  = $entry_id;
			$charge->metadata['entry_url'] = esc_url_raw( admin_url( 'admin.php?page=wpforms-entries&view=details&entry_id=' . $entry_id ) );
			$charge->save();

			// Update the Stripe subscription meta data to include the Entry ID.
			if ( ! empty( $this->subscription->id ) ) {
				$subscription                        = \Stripe\Subscription::retrieve( $this->subscription->id );
				$subscription->metadata['entry_id']  = $entry_id;
				$subscription->metadata['entry_url'] = esc_url_raw( admin_url( 'admin.php?page=wpforms-entries&view=details&entry_id=' . $entry_id ) );
				$subscription->save();
			}

			// Processing complete.
			do_action( 'wpforms_stripe_process_complete', $fields, $form_data, $entry_id, $this->charge, $this->subscription, $this->customer );
		}

		// @todo add Entry ID to charge meta.
	}

	/**
	 * Process a single payment.
	 *
	 * @since 2.0.0
	 */
	public function process_payment_single() {

		try {
			$this->customer = $this->get_customer();
			// Define the basic payment details.
			$args = array(
				'amount'   => $this->amount * 100,
				'currency' => strtolower( wpforms_setting( 'currency', 'USD' ) ),
				"customer" => $this->customer->id,
				'metadata' => array(
					'form_name' => sanitize_text_field( $this->form_data['settings']['form_title'] ),
					'form_id'   => $this->form_id,
				),
			);

			// Payment description.
			if ( ! empty( $this->settings['payment_description'] ) ) {
				$args['description'] = html_entity_decode( $this->settings['payment_description'], ENT_COMPAT, 'UTF-8' );
			}

			// Receipt email.
			if ( ! empty( $this->settings['receipt_email'] ) && ! empty( $this->fields[ $this->settings['receipt_email'] ]['value'] ) ) {
				$args['receipt_email'] = sanitize_email( $this->fields[ $this->settings['receipt_email'] ]['value'] );
			}

			// Charge the payment.
			$this->charge = \Stripe\Charge::create( $args );

			// Update the credit card field value to contain basic details.
			$this->update_credit_card_field_value();

		} catch ( \Stripe\Error\Card $e ) {

			// Since it's a decline, \Stripe\Error\Card will be caught.
			$body        = $e->getJsonBody();
			$this->error = $body['error']['message'];

		} catch ( \Stripe\Error\RateLimit $e ) {

			$this->error = esc_html__( 'Too many requests made to the API too quickly.', 'wpforms-stripe' );

		} catch ( \Stripe\Error\InvalidRequest $e ) {
			/*$body = $e->getJsonBody();
			$err  = $body['error'];
			print('Status is:' . $e->getHttpStatus() . "\n");
			print('Type is:' . $err['type'] . "\n");
			print('Code is:' . $err['code'] . "\n");
			// param is '' in this case
			print('Param is:' . $err['param'] . "\n");
			print('Message is:' . $err['message'] . "\n");
			die;*/
			$this->error = esc_html__( 'Invalid parameters were supplied to Stripe API.', 'wpforms-stripe' );

		} catch ( \Stripe\Error\Authentication $e ) {

			$this->error = esc_html__( 'Authentication with Stripe API failed.', 'wpforms-stripe' );

		} catch ( \Stripe\Error\ApiConnection $e ) {

			$this->error = esc_html__( 'Network communication with Stripe failed.', 'wpforms-stripe' );

		} catch ( \Stripe\Error\Base $e ) {

			$this->error = esc_html__( 'Unable to process Stripe payment.', 'wpforms-stripe' );

		} catch ( \Exception $e ) {

			// Something else happened, completely unrelated to Stripe.
			$this->error = esc_html__( 'Unable to process payment.', 'wpforms-stripe' );
		}

		if ( ! empty( $this->error ) ) {

			// Save error to display to user on frontend.
			wpforms()->process->errors[ $this->form_id ]['footer'] = sprintf(
				/* translators: %s - error message. */
				esc_html__( 'Credit Card Payment Error: %s', 'wpforms-stripe' ),
				$this->error
			);

			// Log error.
			wpforms_log(
				esc_html__( 'Stripe payment stopped by error', 'wpforms-stripe' ),
				$this->error,
				array(
					'type'    => array( 'payment', 'error' ),
					'form_id' => $this->form_id,
				)
			);
		}
	}

	/**
	 * Process a subscription payment.
	 *
	 * @since 2.0.0
	 */
	public function process_payment_subscription() {

		// Check subscription settings are provided.
		if ( empty( $this->settings['recurring']['period'] ) || empty( $this->settings['recurring']['email'] ) ) {
			$this->error = esc_html__( 'Stripe subscription payment stopped, missing form settings.', 'wpforms-stripe' );
		}

		// Check for required customer email.
		if ( empty( $this->fields[ $this->settings['recurring']['email'] ]['value'] ) ) {
			$this->error = esc_html__( 'Stripe subscription payment stopped, customer email not found.', 'wpforms-stripe' );
		}

		// Before proceeding, check if any basic errors were detected.
		if ( $this->error ) {

			// Log error if we have one and stop.
			wpforms_log(
				$this->error,
				'',
				array(
					'type'    => array( 'error', 'payment' ),
					'form_id' => $this->form_id,
				)
			);

			return;
		}

		// Check for conditional logic.
		if (
			! empty( $this->settings['recurring']['conditional_logic'] ) &&
			! empty( $this->settings['recurring']['conditional_type'] ) &&
			! empty( $this->settings['recurring']['conditionals'] )
		) {
			// All conditional logic checks passed, continue with processing.
			$process = wpforms_conditional_logic()->process( $this->fields, $this->form_data, $this->settings['recurring']['conditionals'] );

			if ( 'stop' === $this->settings['recurring']['conditional_type'] ) {
				$process = ! $process;
			}

			if ( ! $process ) {
				$this->process_payment_single();
				return;
			}
		}

		try {

			$this->customer = $this->get_customer();

			$plan_id = $this->get_plan();

			// Create the subscription.
			$this->subscription = \Stripe\Subscription::create( array(
				'customer' => $this->customer->id,
				'items'    => array(
					array(
						'plan' => $plan_id,
					),
				),
				'metadata' => array(
					'form_name' => sanitize_text_field( $this->form_data['settings']['form_title'] ),
					'form_id'   => $this->form_id,
				),
			) );

			// Reference invoice to get the charge object.
			$invoice = \Stripe\Invoice::all( array(
				'limit'        => 1,
				'subscription' => $this->subscription->id,
				'expand'       => array( 'data.charge' ),
			) );

			$this->charge = $invoice->data[0]->charge;

			// Update the credit card field value to contain basic details.
			$this->update_credit_card_field_value();

		} catch ( \Stripe\Error\Card $e ) {

			// Since it's a decline, \Stripe\Error\Card will be caught.
			$body  = $e->getJsonBody();
			$error = $body['error']['message'];

		} catch ( \Stripe\Error\RateLimit $e ) {

			$error = esc_html__( 'Too many requests made to the API too quickly.', 'wpforms-stripe' );

		} catch ( \Stripe\Error\InvalidRequest $e ) {
			/*$body = $e->getJsonBody();
			$err  = $body['error'];
			print('Status is:' . $e->getHttpStatus() . "\n");
			print('Type is:' . $err['type'] . "\n");
			print('Code is:' . $err['code'] . "\n");
			// param is '' in this case
			print('Param is:' . $err['param'] . "\n");
			print('Message is:' . $err['message'] . "\n");
			die;
*/			$error = esc_html__( 'Invalid parameters were supplied to Stripe API.', 'wpforms-stripe' );

		} catch ( \Stripe\Error\Authentication $e ) {

			$error = esc_html__( 'Authentication with Stripe API failed.', 'wpforms-stripe' );

		} catch ( \Stripe\Error\ApiConnection $e ) {

			$error = esc_html__( 'Network communication with Stripe failed.', 'wpforms-stripe' );

		} catch ( \Stripe\Error\Base $e ) {

			$error = esc_html__( 'Unable to process Stripe payment.', 'wpforms-stripe' );

		} catch ( \Exception $e ) {

			// Something else happened, completely unrelated to Stripe.
			$error = esc_html__( 'Unable to process payment.', 'wpforms-stripe' );
		}

		if ( ! empty( $error ) ) {

			// Save error to display to user on frontend.
			wpforms()->process->errors[ $this->form_id ]['footer'] = sprintf(
				/* translators: %s - error message. */
				esc_html__( 'Credit Card Payment Error: %s', 'wpforms-stripe' ),
				$error
			);

			// Log error.
			wpforms_log(
				esc_html__( 'Stripe subscription payment stopped by error', 'wpforms-stripe' ),
				$error,
				array(
					'type'    => array( 'payment', 'error' ),
					'form_id' => $this->form_id,
				)
			);
		}
	}

	/**
	 * Returns Stripe customer object.
	 *
	 * Checks if a customer exists in Stripe, if not creates one.
	 *
	 * @since 2.0.0
	 *
	 * @return \Stripe\Customer
	 */
	public function get_customer() {

		$email = sanitize_email( $this->fields[ $this->settings['recurring']['email'] ]['value'] );

		$customer = \Stripe\Customer::all( array(
			'email' => $email,
		) );

		if ( ! empty( $customer->data ) ) {
			return $customer->data[0];
		}

		return \Stripe\Customer::create( array(
			'email'  => $email,
			'source' => $this->token,
		) );
	}

	/**
	 * Returns Stripe plan ID.
	 *
	 * Checks if a plan exists in Stripe, if not creates one.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_plan() {

		switch ( $this->settings['recurring']['period'] ) {
			case 'daily':
				$period = array(
					'name'     => 'daily',
					'interval' => 'day',
					'count'    => 1,
					'desc'     => esc_html__( 'Daily', 'wpforms-stripe' ),
				);
				break;
			case 'weekly':
				$period = array(
					'name'     => 'weekly',
					'interval' => 'week',
					'count'    => 1,
					'desc'     => esc_html__( 'Weekly', 'wpforms-stripe' ),
				);
				break;
			case 'monthly':
				$period = array(
					'name'     => 'monthly',
					'interval' => 'month',
					'count'    => 1,
					'desc'     => esc_html__( 'Monthly', 'wpforms-stripe' ),
				);
				break;
			case 'quarterly':
				$period = array(
					'name'     => 'quarterly',
					'interval' => 'month',
					'count'    => 3,
					'desc'     => esc_html__( 'Quarterly', 'wpforms-stripe' ),
				);
				break;
			case 'semiyearly':
				$period = array(
					'name'     => 'semiyearly',
					'interval' => 'month',
					'count'    => 6,
					'desc'     => esc_html__( 'Semi-Yearly', 'wpforms-stripe' ),
				);
				break;
			case 'yearly':
			default:
				$period = array(
					'name'     => 'yearly',
					'interval' => 'year',
					'count'    => 1,
					'desc'     => esc_html__( 'Yearly', 'wpforms-stripe' ),
				);
				break;
		}
		$amount = 24.99;
		if ( ! empty( $this->settings['recurring']['name'] ) ) {
			$slug = preg_replace( '/[^a-z0-9\-]/', '', strtolower( str_replace( ' ', '-', $this->settings['recurring']['name'] ) ) );
		} else {
			$slug = 'form' . $this->form_id;
		}

		$plan_id = sprintf(
			'%s_%s_%s',
			$slug,
			$amount * 100,
			$period['name']
		);

		$name = sprintf( '%s (%s %s)',
			! empty( $this->settings['recurring']['name'] ) ? $this->settings['recurring']['name'] : $this->form_data['settings']['form_title'],
			$amount,
			$period['desc']
		);

		// Check if subscription plan already exists, otherwise create it.
		try {
			\Stripe\Plan::retrieve( $plan_id );

		} catch ( \Stripe\Error\InvalidRequest $e ) {
			//Mujahid Code To apply Subscription
			\Stripe\Plan::create( array(
				'amount'         => $amount * 100,
				'interval'       => $period['interval'],
				'interval_count' => $period['count'],
				'product'        => array(
					'name' => sanitize_text_field( $name ),
				),
				'nickname'       => sanitize_text_field( $name ),
				'currency'       => strtolower( wpforms_setting( 'currency', 'USD' ) ),
				'id'             => $plan_id,
				'metadata'       => array(
					'form_name' => sanitize_text_field( $this->form_data['settings']['form_title'] ),
					'form_id'   => $this->form_id,
				),
			) );
		}

		return $plan_id;
	}

	/**
	 * Update the credit card field value to contain basic details.
	 *
	 * @since 2.0.0
	 */
	public function update_credit_card_field_value() {

		foreach ( $this->fields as $field_id => $field ) {
			if ( 'credit-card' === $field['type'] ) {
				wpforms()->process->fields[ $field_id ]['value'] = apply_filters( 'wpforms_stripe_creditcard_value',
					'XXXXXXXXXXXX' . $this->charge->source->last4 . "\n" . $this->charge->source->brand,
					$this->charge
				);
			}
		}
	}
}
