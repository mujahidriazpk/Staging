<?php

namespace WPFormsStripe\Admin;

/**
 * Stripe addon settings.
 *
 * @package    WPFormsStripe
 * @author     WPForms
 * @since      2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2018, WPForms LLC
 */
class Settings {

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

		add_filter( 'wpforms_settings_defaults', array( $this, 'register' ) );
	}

	/**
	 * Register Settings fields.
	 *
	 * @since 2.0.0
	 *
	 * @param array $settings Array of current form settings.
	 *
	 * @return array
	 */
	public function register( $settings ) {

		$desc = sprintf(
			wp_kses(
				/* translators: %s - WPForms.com Stripe documentation article URL. */
				__( 'Keys can be found in your Stripe account dashboard. For more information see our <a href="%s" target="_blank" rel="noopener noreferrer">Stripe documentation</a>.', 'wpforms' ),
				array(
					'a' => array(
						'href'   => array(),
						'target' => array(),
						'rel'    => array(),
					),
				)
			),
			'https://wpforms.com/docs/how-to-install-and-use-the-stripe-addon-with-wpforms/'
		);

		$settings['payments']['stripe-heading']              = array(
			'id'       => 'strie-heading',
			'content'  => '<h4>' . esc_html__( 'Stripe', 'wpforms-stripe' ) . '</h4><p>' . $desc . '</p>',
			'type'     => 'content',
			'no_label' => true,
			'class'    => array( 'section-heading' ),
		);
		$settings['payments']['stripe-test-secret-key']      = array(
			'id'   => 'stripe-test-secret-key',
			'name' => esc_html__( 'Test Secret Key', 'wpforms-stripe' ),
			'type' => 'text',
		);
		$settings['payments']['stripe-test-publishable-key'] = array(
			'id'   => 'stripe-test-publishable-key',
			'name' => esc_html__( 'Test Publishable Key', 'wpforms-stripe' ),
			'type' => 'text',
		);
		$settings['payments']['stripe-live-secret-key']      = array(
			'id'   => 'stripe-live-secret-key',
			'name' => esc_html__( 'Live Secret Key', 'wpforms-stripe' ),
			'type' => 'text',
		);
		$settings['payments']['stripe-live-publishable-key'] = array(
			'id'   => 'stripe-live-publishable-key',
			'name' => esc_html__( 'Live Publishable Key', 'wpforms-stripe' ),
			'type' => 'text',
		);
		$settings['payments']['stripe-test-mode']            = array(
			'id'   => 'stripe-test-mode',
			'name' => esc_html__( 'Test Mode', 'wpforms-stripe' ),
			'desc' => esc_html__( 'In test mode and no live Stripe transactions are processed.', 'wpforms-stripe' ),
			'type' => 'checkbox',
		);

		return $settings;
	}
}
