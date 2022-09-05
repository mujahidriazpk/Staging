<?php

class WP_Analytify_Pro_Base extends Analytify_General {

	function __construct(){

		parent::__construct();
	}

	function mask_license( $key ) {

		$license_parts  = str_split( $key, 4 );
		$i              = count( $license_parts ) - 1;
		$masked_license = '';

		foreach ( $license_parts as $license_part ) {
			if ( $i == 0 ) {
				$masked_license .= $license_part;
				continue;
			}

			$masked_license .= '<span class="bull">';
			$masked_license .= str_repeat( '&bull;', strlen( $license_part ) ) . '</span>&ndash;';
			--$i;
		}

		return $masked_license;

	}

	function get_formatted_masked_license( $key ) {
		return sprintf(
			'<p class="masked-license">%s <a href="%s">%s</a></p>',
			$this->mask_license( $key ), network_admin_url( $this->plugin_settings_base . '&nonce=' . wp_create_nonce( 'wpanalytify-remove-license' ) . '&wpanalytify-remove-license=1' ), _x( 'Remove', 'Delete license', 'wp-analytify-pro' )
		);
	}

	function get_formatted_masked_woo_license( $key ) {
		return sprintf(
			'<p class="masked-license">%s <a href="%s">%s</a></p>',
			$this->mask_license( $key ), network_admin_url( $this->plugin_settings_base . '&nonce=' . wp_create_nonce( 'wpanalytify-remove-woo-license' ) . '&wpanalytify-remove-woo-license=1' ), _x( 'Remove', 'Delete license', 'wp-analytify-pro' )
		);
	}

	function get_formatted_masked_edd_license( $key ) {
		return sprintf(
			'<p class="masked-license">%s <a href="%s">%s</a></p>',
			$this->mask_license( $key ), network_admin_url( $this->plugin_settings_base . '&nonce=' . wp_create_nonce( 'wpanalytify-remove-edd-license' ) . '&wpanalytify-remove-edd-license=1' ), _x( 'Remove', 'Delete license', 'wp-analytify-pro' )
		);
	}

	function get_formatted_masked_email_license( $key ) {
		return sprintf(
			'<p class="masked-license">%s <a href="%s">%s</a></p>',
			$this->mask_license( $key ), network_admin_url( $this->plugin_settings_base . '&nonce=' . wp_create_nonce( 'wpanalytify-remove-email-license' ) . '&wpanalytify-remove-email-license=1' ), _x( 'Remove', 'Delete license', 'wp-analytify-pro' )
		);
	}

	function get_formatted_masked_campaigns_license( $key ) {

		return sprintf(
			'<p class="masked-license">%s <a href="%s">%s</a></p>',
			$this->mask_license( $key ), network_admin_url( $this->plugin_settings_base . '&nonce=' . wp_create_nonce( 'wpanalytify-remove-campaigns-license' ) . '&wpanalytify-remove-campaigns-license=1' ), _x( 'Remove', 'Delete license', 'wp-analytify-pro' )
		);
	}

	function get_formatted_masked_google_optimize_license( $key ) {

		return sprintf(
			'<p class="masked-license">%s <a href="%s">%s</a></p>',
			$this->mask_license( $key ), network_admin_url( $this->plugin_settings_base . '&nonce=' . wp_create_nonce( 'wpanalytify-remove-google-optimize-license' ) . '&wpanalytify-remove-google-optimize-license=1' ), _x( 'Remove', 'Delete license', 'wp-analytify-pro' )
		);
	}

	function get_formatted_masked_events_tracking_license( $key ) {

		return sprintf(
			'<p class="masked-license">%s <a href="%s">%s</a></p>',
			$this->mask_license( $key ), network_admin_url( $this->plugin_settings_base . '&nonce=' . wp_create_nonce( 'wpanalytify-remove-events-tracking-license' ) . '&wpanalytify-remove-events-tracking-license=1' ), _x( 'Remove', 'Delete license', 'wp-analytify-pro' )
		);
	}

	function get_formatted_masked_dimensions_license( $key ) {

		return sprintf(
			'<p class="masked-license">%s <a href="%s">%s</a></p>',
			$this->mask_license( $key ), network_admin_url( $this->plugin_settings_base . '&nonce=' . wp_create_nonce( 'wpanalytify-remove-dimensions-license' ) . '&wpanalytify-remove-dimensions-license=1' ), _x( 'Remove', 'Delete license', 'wp-analytify-pro' )
		);
	}

	function get_formatted_masked_authors_license( $key ) {

		return sprintf(
			'<p class="masked-license">%s <a href="%s">%s</a></p>',
			$this->mask_license( $key ), network_admin_url( $this->plugin_settings_base . '&nonce=' . wp_create_nonce( 'wpanalytify-remove-authors-license' ) . '&wpanalytify-remove-authors-license=1' ), _x( 'Remove', 'Delete license', 'wp-analytify-pro' )
		);
	}	

	function get_formatted_masked_forms_license( $key ) {

		return sprintf(
			'<p class="masked-license">%s <a href="%s">%s</a></p>',
			$this->mask_license( $key ), network_admin_url( $this->plugin_settings_base . '&nonce=' . wp_create_nonce( 'wpanalytify-remove-forms-license' ) . '&wpanalytify-remove-forms-license=1' ), _x( 'Remove', 'Delete license', 'wp-analytify-pro' )
		);
	}	

	/**
	 * Goals masked license
	 *
	 * @param  string $key license key to apply mask
	 * @return string      formatted string
	 *
	 * @since  2.0.16
	 */
	function get_formatted_masked_goals_license( $key ) {

		return sprintf(
			'<p class="masked-license">%s <a href="%s">%s</a></p>',
			$this->mask_license( $key ), network_admin_url( $this->plugin_settings_base . '&nonce=' . wp_create_nonce( 'wpanalytify-remove-goals-license' ) . '&wpanalytify-remove-goals-license=1' ), _x( 'Remove', 'Delete license', 'wp-analytify-pro' )
		);
	}


	function is_license_expired() {

		$license  = $this->get_license_key();
		if( ! $license )
			return false;

		$response = get_site_transient( 'wp_analytify_check_license_expiration' );

		if( false === $response || empty( $response ) ) {
			$response = $this->wp_analytify_api_call( 'check_license', $license, ANALYTIFY_PRO_ID, ANALYTIFY_STORE_URL );
			set_site_transient( 'wp_analytify_check_license_expiration', $response, 60 * 60 * 48 );
		}

		if( $response->license === 'expired' )
			return true;
	}


	function is_license_constant() {
		return defined( 'WPANALYTIFY_LICENSE' );
	}

	function is_woo_license_constant() {
		return defined( 'WPANALYTIFY_WOO_LICENSE' );
	}

	function is_edd_license_constant() {
		return defined( 'WPANALYTIFY_EDD_LICENSE' );
	}

	function is_email_license_constant() {
		return defined( 'WPANALYTIFY_EMAIL_LICENSE' );
	}

	function is_campaigns_license_constant() {
		return defined( 'WPANALYTIFY_CAMPAIGNS_LICENSE' );
	}

	function is_dimensions_license_constant() {
		return defined( 'WPANALYTIFY_DIMENSIONS_LICENSE' );
	}

	function is_authors_license_constant() {
		return defined( 'WPANALYTIFY_AUTHORS_LICENSE' );
	}	
	
	function is_forms_license_constant() {
		return defined( 'WPANALYTIFY_FORMS_LICENSE' );
	}	

	function is_google_optimize_license_constant() {
		return defined( 'WPANALYTIFY_GOOLE_OPTIMIZE_LICENSE' );
	}

	function is_events_tracking_license_constant() {
		return defined( 'WPANALYTIFY_EVENTS_TRACKING_LICENSE' );
	}

	/**
	 * Check if license is defined manually as a Constant in wp-config.php
	 *
	 * @since  2.0.16
	 * @return boolean license key if exists
	 */
	function is_goals_license_constant() {
		return defined( 'WPANALYTIFY_GOALS_LICENSE' );
	}


	function get_license_key() {

		$license = trim( get_option( 'analytify_license_key' ) );
		return $this->is_license_constant() ? WPANALYTIFY_LICENSE : $license;
	}

	function get_woo_license_key( $addon = false ) {

		$license = trim( get_option( 'analytify_woo_license_key' ) );
		return $this->is_woo_license_constant() ? WPANALYTIFY_WOO_LICENSE : $license;
	}

	function get_edd_license_key( $addon = false ) {

		$license = trim( get_option( 'analytify_edd_license_key' ) );
		return $this->is_edd_license_constant() ? WPANALYTIFY_EDD_LICENSE : $license;
	}

	function get_email_license_key( $addon = false ) {

		$license = trim( get_option( 'analytify_email_license_key' ) );
		return $this->is_email_license_constant() ? WPANALYTIFY_EMAIL_LICENSE : $license;
	}

	function get_campaigns_license_key( $addon = false ) {

		$license = trim( get_option( 'analytify_campaigns_license_key' ) );
		return $this->is_campaigns_license_constant() ? WPANALYTIFY_CAMPAIGNS_LICENSE : $license;
	}

	function get_dimensions_license_key( $addon = false ) {

		$license = trim( get_option( 'analytify_dimensions_license_key' ) );
		return $this->is_dimensions_license_constant() ? WPANALYTIFY_DIMENSIONS_LICENSE : $license;
	}

	function get_authors_license_key( $addon = false ) {

		$license = trim( get_option( 'analytify_authors_license_key' ) );
		return $this->is_authors_license_constant() ? WPANALYTIFY_AUTHORS_LICENSE : $license;
	}	

	function get_forms_license_key( $addon = false ) {

		$license = trim( get_option( 'analytify_forms_license_key' ) );
		return $this->is_forms_license_constant() ? WPANALYTIFY_FORMS_LICENSE : $license;
	}	


	function get_google_optimize_license_key( $addon = false ) {

		$license = trim( get_option( 'analytify_google_optimize_license_key' ) );
		return $this->is_google_optimize_license_constant() ? WPANALYTIFY_GOOGLE_OPTIMIZE_LICENSE : $license;
	}

	function get_events_tracking_license_key( $addon = false ) {

		$license = trim( get_option( 'analytify_events_tracking_license_key' ) );
		return $this->is_events_tracking_license_constant() ? WPANALYTIFY_EVENTS_TRACKING_LICENSE : $license;
	}

	/**
	 *  Get Goals license Key
	 *
	 * @param  string $addon addon name (optional)
	 * @return string        license key
	 *
	 * @since 2.0.16
	 */
	function get_goals_license_key( $addon = false ) {

		$license = trim( get_option( 'analytify_goals_license_key' ) );
		return $this->is_goals_license_constant() ? WPANALYTIFY_GOALS_LICENSE : $license;
	}


	function get_license_status() {

		$status = get_option( 'analytify_license_status' ) ? trim( get_option( 'analytify_license_status' )) : false;
		return $status;
	}


	/**
	 * Sets the license index in the $load_settings array class property and updates the wpanalytify_settings option.
	 *
	 * @param string $key
	 */
	function set_license_key( $key ) {
		$this->load_settings['license'] = $key;
		update_site_option( 'wpanalytify_settings', $this->load_settings );
	}

	/**
	 * Sets the license index in the $load_settings array class property and updates the wpanalytify_settings option.
	 *
	 * @param string $key
	 */
	function set_woo_license_key( $key ) {
		$this->load_settings['woo_license'] = $key;
		update_site_option( 'wpanalytify_settings', $this->load_settings );
	}

	/**
	 * Sets the license index in the $load_settings array class property and updates the wpanalytify_settings option.
	 *
	 * @param string $key
	 */
	function set_edd_license_key( $key ) {
		$this->load_settings['edd_license'] = $key;
		update_site_option( 'wpanalytify_settings', $this->load_settings );
	}

	/**
	 * Sets the license index in the $load_settings array class property and updates the wpanalytify_settings option.
	 *
	 * @param string $key
	 */
	function set_email_license_key( $key ) {
		$this->load_settings['email_license'] = $key;
		update_site_option( 'wpanalytify_settings', $this->load_settings );
	}

	/**
	 * Sets the license index in the $load_settings array class property and updates the wpanalytify_settings option.
	 *
	 * @param string $key
	 */
	function set_campaigns_license_key( $key ) {
		$this->load_settings['campaigns_license'] = $key;
		update_site_option( 'wpanalytify_settings', $this->load_settings );
	}


	/**
	 * Sets the license index in the $load_settings array class property and updates the wpanalytify_settings option.
	 *
	 * @param string $key
	 * @since 2.0.16
	 */
	function set_goals_license_key( $key ) {
		$this->load_settings['goals_license'] = $key;
		update_site_option( 'wpanalytify_settings', $this->load_settings );
	}

	/**
	 * Sets the license index in the $load_settings array class property and updates the wpanalytify_settings option.
	 *
	 * @param string $key
	 * @since 2.0.16
	 */
	function set_authors_license_key( $key ) {
		$this->load_settings['authors_license'] = $key;
		update_site_option( 'wpanalytify_settings', $this->load_settings );
	}	

	/**
	 * Sets the license index in the $load_settings array class property and updates the wpanalytify_settings option.
	 *
	 * @param string $key
	 * @since 2.0.16
	 */
	function set_forms_license_key( $key ) {
		$this->load_settings['forms_license'] = $key;
		update_site_option( 'wpanalytify_settings', $this->load_settings );
	}		

	/**
	 * [get_latest_version description]
	 * @return [type] [description]
	 */
	function get_latest_version( $license ) {

		$response = $this->wp_analytify_api_call( 'get_version', $license, ANALYTIFY_PRODUCT_NAME, ANALYTIFY_STORE_URL );
		return $response->new_version;
	}

	/**
	 * Returns a formatted message dependant on the status of the license.
	 *
	 * @param bool $trans
	 * @param string $context
	 *
	 * @since  2.0
	 * @return array|string|void
	 */
	function get_license_status_message( $trans = false, $context = null ) {

		$license               = $this->get_license_key();
		$api_response_provided = true;

		if ( empty( $license ) && ! $trans ) {
			$message = sprintf( __( '<strong>Activate Your License</strong> &mdash; Please <a href="%s" class="">enter your license key</a> to enable priority support and plugin updates.', 'wp-analytify-pro' ), network_admin_url( $this->plugin_settings_base . '#settings' ) );

			return $message;
		}

		if ( ! $trans ) {
			$trans = get_site_transient( 'wpanalytify_license_response' );

			if ( false === $trans ) {
				$trans = $this->check_license( $license );
			}

			$trans                 = json_decode( $trans, true );
			$api_response_provided = false;
		}


		$errors = $trans->error;

		if ( 'expired' === $errors ) {

			$message_base = sprintf( '<strong>%s</strong> &mdash; ', __( 'Your License Has Expired', 'wp-analytify-pro' ) );
			$message_end  = sprintf( __( 'Login to <a href="%s">My Account</a> to renew. ', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );

			$contextual_messages = array(
				'default' => $message_base . $message_end,
				'update'  => $message_base . __( 'Updates are only available to those with an active license. ', 'wp-analytify-pro' ) . $message_end,
				'support' => $message_base . __( 'Only active licenses can submit support requests. ', 'wp-analytify-pro' ) . $message_end,
				'license' => $message_base . __( "All features will continue to work, but you won't be able to receive updates or email support. ", 'wp-analytify-pro' ) . $message_end,
			);

			if ( empty( $context ) ) {
				$context = 'default';
			}
			if ( ! empty( $contextual_messages[ $context ] ) ) {
				$message = $contextual_messages[ $context ];
			} elseif ( 'all' === $context ) {
				$message = $contextual_messages;
			}
		} elseif ( 'no_activations_left' === $errors ) {
			$message = sprintf( __( '<strong>No Activations Left</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to upgrade your license or deactivate a previous activation.', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );
		} elseif ( 'missing' === $errors ) {
			if ( ! $api_response_provided ) {
				$message = sprintf( __( '<strong>Your License Was Not Found</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to double check your license key.', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );
			} else {
				$error   = reset( $errors );
				$message = __( '<strong>Your License Was Not Found</strong> &mdash; ', 'wp-analytify-pro' );
				$message .= $error;
			}
		} else {
			$error   = reset( $errors );
			$message = sprintf( __( '<strong>An Unexpected Error Occurred</strong> &mdash; Please contact us at <a href="%1$s">%2$s</a> and quote the following:', 'wp-analytify-pro' ), 'mailto:support@analytify.io', 'support@analytify.io' );
			$message .= sprintf( '<p>%s</p>', $error );
		}

		return $message;
	}


	/**
	 * Returns a formatted message dependant on the status of the WooCommerce add-on license.
	 *
	 * @param bool $trans
	 * @param string $context
	 *
	 * @since  2.0
	 * @return array|string|void
	 */
	function get_woo_license_status_message( $trans = false, $context = null ) {

		$license               = $this->get_woo_license_key();
		$api_response_provided = true;

		if ( empty( $license ) && ! $trans ) {
			$message = sprintf( __( '<strong>Activate Your License</strong> &mdash; Please <a href="%s" class="">enter your license key</a> to enable priority support and plugin updates.', 'wp-analytify-pro' ), network_admin_url( $this->plugin_settings_base . '#settings' ) );

			return $message;
		}

		if ( ! $trans ) {
			$trans = get_site_transient( 'wpanalytify_woo_license_response' );

			if ( false === $trans ) {
				$trans = $this->check_woo_license( $license );
			}

			$trans                 = json_decode( $trans, true );
			$api_response_provided = false;
		}


		$errors = $trans->error;

		if ( 'expired' === $errors ) {

			$message_base = sprintf( '<strong>%s</strong> &mdash; ', __( 'Your License Has Expired', 'wp-analytify-pro' ) );
			$message_end  = sprintf( __( 'Login to <a href="%s">My Account</a> to renew. ', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );

			$contextual_messages = array(
				'default' => $message_base . $message_end,
				'update'  => $message_base . __( 'Updates are only available to those with an active license. ', 'wp-analytify-pro' ) . $message_end,
				'support' => $message_base . __( 'Only active licenses can submit support requests. ', 'wp-analytify-pro' ) . $message_end,
				'license' => $message_base . __( "All features will continue to work, but you won't be able to receive updates or email support. ", 'wp-analytify-pro' ) . $message_end,
			);

			if ( empty( $context ) ) {
				$context = 'default';
			}
			if ( ! empty( $contextual_messages[ $context ] ) ) {
				$message = $contextual_messages[ $context ];
			} elseif ( 'all' === $context ) {
				$message = $contextual_messages;
			}
		} elseif ( 'no_activations_left' === $errors ) {
			$message = sprintf( __( '<strong>No Activations Left</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to upgrade your license or deactivate a previous activation.', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );
		} elseif ( 'missing' === $errors ) {
			if ( ! $api_response_provided ) {
				$message = sprintf( __( '<strong>Your License Was Not Found</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to double check your license key.', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );
			} else {
				$error   = reset( $errors );
				$message = __( '<strong>Your License Was Not Found</strong> &mdash; ', 'wp-analytify-pro' );
				$message .= $error;
			}
		} else {
			$error   = reset( $errors );
			$message = sprintf( __( '<strong>An Unexpected Error Occurred</strong> &mdash; Please contact us at <a href="%1$s">%2$s</a> and quote the following:', 'wp-analytify-pro' ), 'mailto:support@analytify.io', 'support@analytify.io' );
			$message .= sprintf( '<p>%s</p>', $error );
		}

		return $message;
	}


	/**
	 * Returns a formatted message dependant on the status of the EDD Add-on license.
	 *
	 * @param bool $trans
	 * @param string $context
	 *
	 * @since  2.0
	 * @return array|string|void
	 */
	function get_edd_license_status_message( $trans = false, $context = null ) {

		$license               = $this->get_edd_license_key();
		$api_response_provided = true;

		if ( empty( $license ) && ! $trans ) {
			$message = sprintf( __( '<strong>Activate Your License</strong> &mdash; Please <a href="%s" class="">enter your license key</a> to enable priority support and plugin updates.', 'wp-analytify-pro' ), network_admin_url( $this->plugin_settings_base . '#settings' ) );

			return $message;
		}

		if ( ! $trans ) {
			$trans = get_site_transient( 'wpanalytify_edd_license_response' );

			if ( false === $trans ) {
				$trans = $this->check_edd_license( $license );
			}

			$trans                 = json_decode( $trans, true );
			$api_response_provided = false;
		}


		$errors = $trans->error;

		if ( 'expired' === $errors ) {

			$message_base = sprintf( '<strong>%s</strong> &mdash; ', __( 'Your License Has Expired', 'wp-analytify-pro' ) );
			$message_end  = sprintf( __( 'Login to <a href="%s">My Account</a> to renew. ', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );

			$contextual_messages = array(
				'default' => $message_base . $message_end,
				'update'  => $message_base . __( 'Updates are only available to those with an active license. ', 'wp-analytify-pro' ) . $message_end,
				'support' => $message_base . __( 'Only active licenses can submit support requests. ', 'wp-analytify-pro' ) . $message_end,
				'license' => $message_base . __( "All features will continue to work, but you won't be able to receive updates or email support. ", 'wp-analytify-pro' ) . $message_end,
			);

			if ( empty( $context ) ) {
				$context = 'default';
			}
			if ( ! empty( $contextual_messages[ $context ] ) ) {
				$message = $contextual_messages[ $context ];
			} elseif ( 'all' === $context ) {
				$message = $contextual_messages;
			}
		} elseif ( 'no_activations_left' === $errors ) {
			$message = sprintf( __( '<strong>No Activations Left</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to upgrade your license or deactivate a previous activation.', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );
		} elseif ( 'missing' === $errors ) {
			if ( ! $api_response_provided ) {
				$message = sprintf( __( '<strong>Your License Was Not Found</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to double check your license key.', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );
			} else {
				$error   = reset( $errors );
				$message = __( '<strong>Your License Was Not Found</strong> &mdash; ', 'wp-analytify-pro' );
				$message .= $error;
			}
		} else {
			$error   = reset( $errors );
			$message = sprintf( __( '<strong>An Unexpected Error Occurred</strong> &mdash; Please contact us at <a href="%1$s">%2$s</a> and quote the following:', 'wp-analytify-pro' ), 'mailto:support@analytify.io', 'support@analytify.io' );
			$message .= sprintf( '<p>%s</p>', $error );
		}

		return $message;
	}

	/**
	 * Returns a formatted message dependant on the status of the Email Add-on license.
	 *
	 * @param bool $trans
	 * @param string $context
	 *
	 * @since  2.0
	 * @return array|string|void
	 */
	function get_email_license_status_message( $trans = false, $context = null ) {

		$license               = $this->get_email_license_key();
		$api_response_provided = true;

		if ( empty( $license ) && ! $trans ) {
			$message = sprintf( __( '<strong>Activate Your License</strong> &mdash; Please <a href="%s" class="">enter your license key</a> to enable priority support and plugin updates.', 'wp-analytify-pro' ), network_admin_url( $this->plugin_settings_base . '#settings' ) );

			return $message;
		}

		if ( ! $trans ) {
			$trans = get_site_transient( 'wpanalytify_email_license_response' );

			if ( false === $trans ) {
				$trans = $this->check_email_license( $license );
			}

			$trans                 = json_decode( $trans, true );
			$api_response_provided = false;
		}


		$errors = $trans->error;

		if ( 'expired' === $errors ) {

			$message_base = sprintf( '<strong>%s</strong> &mdash; ', __( 'Your License Has Expired', 'wp-analytify-pro' ) );
			$message_end  = sprintf( __( 'Login to <a href="%s">My Account</a> to renew. ', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );

			$contextual_messages = array(
				'default' => $message_base . $message_end,
				'update'  => $message_base . __( 'Updates are only available to those with an active license. ', 'wp-analytify-pro' ) . $message_end,
				'support' => $message_base . __( 'Only active licenses can submit support requests. ', 'wp-analytify-pro' ) . $message_end,
				'license' => $message_base . __( "All features will continue to work, but you won't be able to receive updates or email support. ", 'wp-analytify-pro' ) . $message_end,
			);

			if ( empty( $context ) ) {
				$context = 'default';
			}
			if ( ! empty( $contextual_messages[ $context ] ) ) {
				$message = $contextual_messages[ $context ];
			} elseif ( 'all' === $context ) {
				$message = $contextual_messages;
			}
		} elseif ( 'no_activations_left' === $errors ) {
			$message = sprintf( __( '<strong>No Activations Left</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to upgrade your license or deactivate a previous activation.', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );
		} elseif ( 'missing' === $errors ) {
			if ( ! $api_response_provided ) {
				$message = sprintf( __( '<strong>Your License Was Not Found</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to double check your license key.', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );
			} else {
				$error   = reset( $errors );
				$message = __( '<strong>Your License Was Not Found</strong> &mdash; ', 'wp-analytify-pro' );
				$message .= $error;
			}
		} else {
			$error   = reset( $errors );
			$message = sprintf( __( '<strong>An Unexpected Error Occurred</strong> &mdash; Please contact us at <a href="%1$s">%2$s</a> and quote the following:', 'wp-analytify-pro' ), 'mailto:support@analytify.io', 'support@analytify.io' );
			$message .= sprintf( '<p>%s</p>', $error );
		}

		return $message;
	}

	/**
	 * Returns a formatted message dependant on the status of the Campaigns Add-on license.
	 *
	 * @param bool $trans
	 * @param string $context
	 *
	 * @since  2.0
	 * @return array|string|void
	 */
	function get_campaigns_license_status_message( $trans = false, $context = null ) {

		$license               = $this->get_campaigns_license_key();
		$api_response_provided = true;

		if ( empty( $license ) && ! $trans ) {
			$message = sprintf( __( '<strong>Activate Your License</strong> &mdash; Please <a href="%s" class="">enter your license key</a> to enable priority support and plugin updates.', 'wp-analytify-pro' ), network_admin_url( $this->plugin_settings_base . '#settings' ) );

			return $message;
		}

		if ( ! $trans ) {
			$trans = get_site_transient( 'wpanalytify_campaigns_license_response' );

			if ( false === $trans ) {
				$trans = $this->check_campaigns_license( $license );
			}

			$trans                 = json_decode( $trans, true );
			$api_response_provided = false;
		}


		$errors = $trans->error;

		if ( 'expired' === $errors ) {

			$message_base = sprintf( '<strong>%s</strong> &mdash; ', __( 'Your License Has Expired', 'wp-analytify-pro' ) );
			$message_end  = sprintf( __( 'Login to <a href="%s">My Account</a> to renew. ', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );

			$contextual_messages = array(
				'default' => $message_base . $message_end,
				'update'  => $message_base . __( 'Updates are only available to those with an active license. ', 'wp-analytify-pro' ) . $message_end,
				'support' => $message_base . __( 'Only active licenses can submit support requests. ', 'wp-analytify-pro' ) . $message_end,
				'license' => $message_base . __( "All features will continue to work, but you won't be able to receive updates or email support. ", 'wp-analytify-pro' ) . $message_end,
			);

			if ( empty( $context ) ) {
				$context = 'default';
			}
			if ( ! empty( $contextual_messages[ $context ] ) ) {
				$message = $contextual_messages[ $context ];
			} elseif ( 'all' === $context ) {
				$message = $contextual_messages;
			}
		} elseif ( 'no_activations_left' === $errors ) {
			$message = sprintf( __( '<strong>No Activations Left</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to upgrade your license or deactivate a previous activation.', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );
		} elseif ( 'missing' === $errors ) {
			if ( ! $api_response_provided ) {
				$message = sprintf( __( '<strong>Your License Was Not Found</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to double check your license key.', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );
			} else {
				$error   = reset( $errors );
				$message = __( '<strong>Your License Was Not Found</strong> &mdash; ', 'wp-analytify-pro' );
				$message .= $error;
			}
		} else {
			$error   = reset( $errors );
			$message = sprintf( __( '<strong>An Unexpected Error Occurred</strong> &mdash; Please contact us at <a href="%1$s">%2$s</a> and quote the following:', 'wp-analytify-pro' ), 'mailto:support@analytify.io', 'support@analytify.io' );
			$message .= sprintf( '<p>%s</p>', $error );
		}

		return $message;
	}


	/**
	 * Returns a formatted message dependant on the status of the Goals Add-on license.
	 *
	 * @param bool $trans
	 * @param string $context
	 *
	 * @since  2.0.16
	 * @return array|string|void
	 */
	function get_goals_license_status_message( $trans = false, $context = null ) {

		$license               = $this->get_goals_license_key();
		$api_response_provided = true;

		if ( empty( $license ) && ! $trans ) {
			$message = sprintf( __( '<strong>Activate Your License</strong> &mdash; Please <a href="%s" class="">enter your license key</a> to enable priority support and plugin updates.', 'wp-analytify-pro' ), network_admin_url( $this->plugin_settings_base . '#settings' ) );

			return $message;
		}

		if ( ! $trans ) {
			$trans = get_site_transient( 'wpanalytify_goals_license_response' );

			if ( false === $trans ) {
				$trans = $this->check_goals_license( $license );
			}

			$trans                 = json_decode( $trans, true );
			$api_response_provided = false;
		}


		$errors = $trans->error;

		if ( 'expired' === $errors ) {

			$message_base = sprintf( '<strong>%s</strong> &mdash; ', __( 'Your License Has Expired', 'wp-analytify-pro' ) );
			$message_end  = sprintf( __( 'Login to <a href="%s">My Account</a> to renew. ', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );

			$contextual_messages = array(
				'default' => $message_base . $message_end,
				'update'  => $message_base . __( 'Updates are only available to those with an active license. ', 'wp-analytify-pro' ) . $message_end,
				'support' => $message_base . __( 'Only active licenses can submit support requests. ', 'wp-analytify-pro' ) . $message_end,
				'license' => $message_base . __( "All features will continue to work, but you won't be able to receive updates or email support. ", 'wp-analytify-pro' ) . $message_end,
			);

			if ( empty( $context ) ) {
				$context = 'default';
			}
			if ( ! empty( $contextual_messages[ $context ] ) ) {
				$message = $contextual_messages[ $context ];
			} elseif ( 'all' === $context ) {
				$message = $contextual_messages;
			}
		} elseif ( 'no_activations_left' === $errors ) {
			$message = sprintf( __( '<strong>No Activations Left</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to upgrade your license or deactivate a previous activation.', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );
		} elseif ( 'missing' === $errors ) {
			if ( ! $api_response_provided ) {
				$message = sprintf( __( '<strong>Your License Was Not Found</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to double check your license key.', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );
			} else {
				$error   = reset( $errors );
				$message = __( '<strong>Your License Was Not Found</strong> &mdash; ', 'wp-analytify-pro' );
				$message .= $error;
			}
		} else {
			$error   = reset( $errors );
			$message = sprintf( __( '<strong>An Unexpected Error Occurred</strong> &mdash; Please contact us at <a href="%1$s">%2$s</a> and quote the following:', 'wp-analytify-pro' ), 'mailto:support@analytify.io', 'support@analytify.io' );
			$message .= sprintf( '<p>%s</p>', $error );
		}

		return $message;
	}

	/**
	 * Returns a formatted message dependant on the status of the Dimensions Add-on license.
	 *
	 * @param bool $trans
	 * @param string $context
	 *
	 * @since  2.0
	 * @return array|string|void
	 */
	function get_dimensions_license_status_message( $trans = false, $context = null ) {

		$license               = $this->get_dimensions_license_key();
		$api_response_provided = true;

		if ( empty( $license ) && ! $trans ) {
			$message = sprintf( __( '<strong>Activate Your License</strong> &mdash; Please <a href="%s" class="">enter your license key</a> to enable priority support and plugin updates.', 'wp-analytify-pro' ), network_admin_url( $this->plugin_settings_base . '#settings' ) );

			return $message;
		}

		if ( ! $trans ) {
			$trans = get_site_transient( 'wpanalytify_dimensions_license_response' );

			if ( false === $trans ) {
				$trans = $this->check_dimensions_license( $license );
			}

			$trans                 = json_decode( $trans, true );
			$api_response_provided = false;
		}


		$errors = $trans->error;

		if ( 'expired' === $errors ) {

			$message_base = sprintf( '<strong>%s</strong> &mdash; ', __( 'Your License Has Expired', 'wp-analytify-pro' ) );
			$message_end  = sprintf( __( 'Login to <a href="%s">My Account</a> to renew. ', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );

			$contextual_messages = array(
				'default' => $message_base . $message_end,
				'update'  => $message_base . __( 'Updates are only available to those with an active license. ', 'wp-analytify-pro' ) . $message_end,
				'support' => $message_base . __( 'Only active licenses can submit support requests. ', 'wp-analytify-pro' ) . $message_end,
				'license' => $message_base . __( "All features will continue to work, but you won't be able to receive updates or email support. ", 'wp-analytify-pro' ) . $message_end,
			);

			if ( empty( $context ) ) {
				$context = 'default';
			}
			if ( ! empty( $contextual_messages[ $context ] ) ) {
				$message = $contextual_messages[ $context ];
			} elseif ( 'all' === $context ) {
				$message = $contextual_messages;
			}
		} elseif ( 'no_activations_left' === $errors ) {
			$message = sprintf( __( '<strong>No Activations Left</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to upgrade your license or deactivate a previous activation.', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );
		} elseif ( 'missing' === $errors ) {
			if ( ! $api_response_provided ) {
				$message = sprintf( __( '<strong>Your License Was Not Found</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to double check your license key.', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );
			} else {
				$error   = reset( $errors );
				$message = __( '<strong>Your License Was Not Found</strong> &mdash; ', 'wp-analytify-pro' );
				$message .= $error;
			}
		} else {
			$error   = reset( $errors );
			$message = sprintf( __( '<strong>An Unexpected Error Occurred</strong> &mdash; Please contact us at <a href="%1$s">%2$s</a> and quote the following:', 'wp-analytify-pro' ), 'mailto:support@analytify.io', 'support@analytify.io' );
			$message .= sprintf( '<p>%s</p>', $error );
		}

		return $message;
	}

	/**
	 * Returns a formatted message dependant on the status of the Authors Add-on license.
	 *
	 * @param bool $trans
	 * @param string $context
	 *
	 * @since  2.0
	 * @return array|string|void
	 */
	function get_authors_license_status_message( $trans = false, $context = null ) {

		$license               = $this->get_authors_license_key();
		$api_response_provided = true;

		if ( empty( $license ) && ! $trans ) {
			$message = sprintf( __( '<strong>Activate Your License</strong> &mdash; Please <a href="%s" class="">enter your license key</a> to enable priority support and plugin updates.', 'wp-analytify-pro' ), network_admin_url( $this->plugin_settings_base . '#settings' ) );

			return $message;
		}

		if ( ! $trans ) {
			$trans = get_site_transient( 'wpanalytify_authors_license_response' );

			if ( false === $trans ) {
				$trans = $this->check_authors_license( $license );
			}

			$trans                 = json_decode( $trans, true );
			$api_response_provided = false;
		}


		$errors = $trans->error;

		if ( 'expired' === $errors ) {

			$message_base = sprintf( '<strong>%s</strong> &mdash; ', __( 'Your License Has Expired', 'wp-analytify-pro' ) );
			$message_end  = sprintf( __( 'Login to <a href="%s">My Account</a> to renew. ', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );

			$contextual_messages = array(
				'default' => $message_base . $message_end,
				'update'  => $message_base . __( 'Updates are only available to those with an active license. ', 'wp-analytify-pro' ) . $message_end,
				'support' => $message_base . __( 'Only active licenses can submit support requests. ', 'wp-analytify-pro' ) . $message_end,
				'license' => $message_base . __( "All features will continue to work, but you won't be able to receive updates or email support. ", 'wp-analytify-pro' ) . $message_end,
			);

			if ( empty( $context ) ) {
				$context = 'default';
			}
			if ( ! empty( $contextual_messages[ $context ] ) ) {
				$message = $contextual_messages[ $context ];
			} elseif ( 'all' === $context ) {
				$message = $contextual_messages;
			}
		} elseif ( 'no_activations_left' === $errors ) {
			$message = sprintf( __( '<strong>No Activations Left</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to upgrade your license or deactivate a previous activation.', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );
		} elseif ( 'missing' === $errors ) {
			if ( ! $api_response_provided ) {
				$message = sprintf( __( '<strong>Your License Was Not Found</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to double check your license key.', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );
			} else {
				$error   = reset( $errors );
				$message = __( '<strong>Your License Was Not Found</strong> &mdash; ', 'wp-analytify-pro' );
				$message .= $error;
			}
		} else {
			$error   = reset( $errors );
			$message = sprintf( __( '<strong>An Unexpected Error Occurred</strong> &mdash; Please contact us at <a href="%1$s">%2$s</a> and quote the following:', 'wp-analytify-pro' ), 'mailto:support@analytify.io', 'support@analytify.io' );
			$message .= sprintf( '<p>%s</p>', $error );
		}

		return $message;
	}


	/**
	 * Returns a formatted message dependant on the status of the Forms Add-on license.
	 *
	 * @param bool $trans
	 * @param string $context
	 *
	 * @since  2.0
	 * @return array|string|void
	 */
	function get_forms_license_status_message( $trans = false, $context = null ) {

		$license               = $this->get_forms_license_key();
		$api_response_provided = true;

		if ( empty( $license ) && ! $trans ) {
			$message = sprintf( __( '<strong>Activate Your License</strong> &mdash; Please <a href="%s" class="">enter your license key</a> to enable priority support and plugin updates.', 'wp-analytify-pro' ), network_admin_url( $this->plugin_settings_base . '#settings' ) );

			return $message;
		}

		if ( ! $trans ) {
			$trans = get_site_transient( 'wpanalytify_forms_license_response' );

			if ( false === $trans ) {
				$trans = $this->check_forms_license( $license );
			}

			$trans                 = json_decode( $trans, true );
			$api_response_provided = false;
		}


		$errors = $trans->error;

		if ( 'expired' === $errors ) {

			$message_base = sprintf( '<strong>%s</strong> &mdash; ', __( 'Your License Has Expired', 'wp-analytify-pro' ) );
			$message_end  = sprintf( __( 'Login to <a href="%s">My Account</a> to renew. ', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );

			$contextual_messages = array(
				'default' => $message_base . $message_end,
				'update'  => $message_base . __( 'Updates are only available to those with an active license. ', 'wp-analytify-pro' ) . $message_end,
				'support' => $message_base . __( 'Only active licenses can submit support requests. ', 'wp-analytify-pro' ) . $message_end,
				'license' => $message_base . __( "All features will continue to work, but you won't be able to receive updates or email support. ", 'wp-analytify-pro' ) . $message_end,
			);

			if ( empty( $context ) ) {
				$context = 'default';
			}
			if ( ! empty( $contextual_messages[ $context ] ) ) {
				$message = $contextual_messages[ $context ];
			} elseif ( 'all' === $context ) {
				$message = $contextual_messages;
			}
		} elseif ( 'no_activations_left' === $errors ) {
			$message = sprintf( __( '<strong>No Activations Left</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to upgrade your license or deactivate a previous activation.', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );
		} elseif ( 'missing' === $errors ) {
			if ( ! $api_response_provided ) {
				$message = sprintf( __( '<strong>Your License Was Not Found</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to double check your license key.', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );
			} else {
				$error   = reset( $errors );
				$message = __( '<strong>Your License Was Not Found</strong> &mdash; ', 'wp-analytify-pro' );
				$message .= $error;
			}
		} else {
			$error   = reset( $errors );
			$message = sprintf( __( '<strong>An Unexpected Error Occurred</strong> &mdash; Please contact us at <a href="%1$s">%2$s</a> and quote the following:', 'wp-analytify-pro' ), 'mailto:support@analytify.io', 'support@analytify.io' );
			$message .= sprintf( '<p>%s</p>', $error );
		}

		return $message;
	}


	/**
	 * Returns a formatted message dependant on the status of the Google Optimize Add-on license.
	 *
	 * @param bool $trans
	 * @param string $context
	 *
	 * @since  2.0
	 * @return array|string|void
	 */
	function get_google_optimize_license_status_message( $trans = false, $context = null ) {

		$license               = $this->get_google_optimize_license_key();
		$api_response_provided = true;

		if ( empty( $license ) && ! $trans ) {
			$message = sprintf( __( '<strong>Activate Your License</strong> &mdash; Please <a href="%s" class="">enter your license key</a> to enable priority support and plugin updates.', 'wp-analytify-pro' ), network_admin_url( $this->plugin_settings_base . '#settings' ) );

			return $message;
		}

		if ( ! $trans ) {
			$trans = get_site_transient( 'wpanalytify_google_optimize_license_response' );

			if ( false === $trans ) {
				$trans = $this->check_google_optimize_license( $license );
			}

			$trans                 = json_decode( $trans, true );
			$api_response_provided = false;
		}


		$errors = $trans->error;

		if ( 'expired' === $errors ) {

			$message_base = sprintf( '<strong>%s</strong> &mdash; ', __( 'Your License Has Expired', 'wp-analytify-pro' ) );
			$message_end  = sprintf( __( 'Login to <a href="%s">My Account</a> to renew. ', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );

			$contextual_messages = array(
				'default' => $message_base . $message_end,
				'update'  => $message_base . __( 'Updates are only available to those with an active license. ', 'wp-analytify-pro' ) . $message_end,
				'support' => $message_base . __( 'Only active licenses can submit support requests. ', 'wp-analytify-pro' ) . $message_end,
				'license' => $message_base . __( "All features will continue to work, but you won't be able to receive updates or email support. ", 'wp-analytify-pro' ) . $message_end,
			);

			if ( empty( $context ) ) {
				$context = 'default';
			}
			if ( ! empty( $contextual_messages[ $context ] ) ) {
				$message = $contextual_messages[ $context ];
			} elseif ( 'all' === $context ) {
				$message = $contextual_messages;
			}
		} elseif ( 'no_activations_left' === $errors ) {
			$message = sprintf( __( '<strong>No Activations Left</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to upgrade your license or deactivate a previous activation.', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );
		} elseif ( 'missing' === $errors ) {
			if ( ! $api_response_provided ) {
				$message = sprintf( __( '<strong>Your License Was Not Found</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to double check your license key.', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );
			} else {
				$error   = reset( $errors );
				$message = __( '<strong>Your License Was Not Found</strong> &mdash; ', 'wp-analytify-pro' );
				$message .= $error;
			}
		} else {
			$error   = reset( $errors );
			$message = sprintf( __( '<strong>An Unexpected Error Occurred</strong> &mdash; Please contact us at <a href="%1$s">%2$s</a> and quote the following:', 'wp-analytify-pro' ), 'mailto:support@analytify.io', 'support@analytify.io' );
			$message .= sprintf( '<p>%s</p>', $error );
		}

		return $message;
	}

	/**
	 * Returns a formatted message dependant on the status of the Google Optimize Add-on license.
	 *
	 * @param bool $trans
	 * @param string $context
	 *
	 * @since  2.0
	 * @return array|string|void
	 */
	function get_events_tracking_license_status_message( $trans = false, $context = null ) {

		$license               = $this->get_events_tracking_license_key();
		$api_response_provided = true;

		if ( empty( $license ) && ! $trans ) {
			$message = sprintf( __( '<strong>Activate Your License</strong> &mdash; Please <a href="%s" class="">enter your license key</a> to enable priority support and plugin updates.', 'wp-analytify-pro' ), network_admin_url( $this->plugin_settings_base . '#settings' ) );

			return $message;
		}

		if ( ! $trans ) {
			$trans = get_site_transient( 'wpanalytify_events_tracking_license_response' );

			if ( false === $trans ) {
				$trans = $this->check_events_tracking_license( $license );
			}

			$trans                 = json_decode( $trans, true );
			$api_response_provided = false;
		}


		$errors = $trans->error;

		if ( 'expired' === $errors ) {

			$message_base = sprintf( '<strong>%s</strong> &mdash; ', __( 'Your License Has Expired', 'wp-analytify-pro' ) );
			$message_end  = sprintf( __( 'Login to <a href="%s">My Account</a> to renew. ', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );

			$contextual_messages = array(
				'default' => $message_base . $message_end,
				'update'  => $message_base . __( 'Updates are only available to those with an active license. ', 'wp-analytify-pro' ) . $message_end,
				'support' => $message_base . __( 'Only active licenses can submit support requests. ', 'wp-analytify-pro' ) . $message_end,
				'license' => $message_base . __( "All features will continue to work, but you won't be able to receive updates or email support. ", 'wp-analytify-pro' ) . $message_end,
			);

			if ( empty( $context ) ) {
				$context = 'default';
			}
			if ( ! empty( $contextual_messages[ $context ] ) ) {
				$message = $contextual_messages[ $context ];
			} elseif ( 'all' === $context ) {
				$message = $contextual_messages;
			}
		} elseif ( 'no_activations_left' === $errors ) {
			$message = sprintf( __( '<strong>No Activations Left</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to upgrade your license or deactivate a previous activation.', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );
		} elseif ( 'missing' === $errors ) {
			if ( ! $api_response_provided ) {
				$message = sprintf( __( '<strong>Your License Was Not Found</strong> &mdash; Please visit <a href="%s" target="_blank">My Account</a> to double check your license key.', 'wp-analytify-pro' ), 'https://analytify.io/your-account/' );
			} else {
				$error   = reset( $errors );
				$message = __( '<strong>Your License Was Not Found</strong> &mdash; ', 'wp-analytify-pro' );
				$message .= $error;
			}
		} else {
			$error   = reset( $errors );
			$message = sprintf( __( '<strong>An Unexpected Error Occurred</strong> &mdash; Please contact us at <a href="%1$s">%2$s</a> and quote the following:', 'wp-analytify-pro' ), 'mailto:support@analytify.io', 'support@analytify.io' );
			$message .= sprintf( '<p>%s</p>', $error );
		}

		return $message;
	}

   	/**
	 * @param Key $license_key License key to be validated.
	 *
	 * @return mixed $return Value to be returned as response.
	 */
    function check_license( $license_key ) {
		if ( empty( $license_key ) ) {
			return false;
		}

		$response = $this->wp_analytify_api_call( 'check_license', $license_key, ANALYTIFY_PRO_ID, ANALYTIFY_STORE_URL );

		set_site_transient( 'wpanalytify_license_response', $response, $this->transient_timeout );

		return $response;
	}

   	/**
	 * @param Key $license_key License key to be validated.
	 *
	 * @return mixed $return Value to be returned as response.
	 */
    function check_woo_license( $license_key ) {
		if ( empty( $license_key ) ) {
			return false;
		}

		$response = $this->wp_analytify_api_call( 'check_license', $license_key, WC_ANALYTIFY_PRODUCT_ID, WC_ANALYTIFY_STORE_URL );

		set_site_transient( 'wpanalytify_woo_license_response', $response, $this->transient_timeout );

		return $response;
	}


   	/**
	 * @param Key $license_key License key to be validated.
	 *
	 * @return mixed $return Value to be returned as response.
	 * @since  2.0
	 */
    function check_edd_license( $license_key ) {
		if ( empty( $license_key ) ) {
			return false;
		}

		$response = $this->wp_analytify_api_call( 'check_license', $license_key, EDD_ANALYTIFY_PRODUCT_ID, EDD_ANALYTIFY_STORE_URL );

		set_site_transient( 'wpanalytify_edd_license_response', $response, $this->transient_timeout );

		return $response;
	}


   	/**
	 * @param Key $license_key License key to be validated.
	 *
	 * @return mixed $return Value to be returned as response.
	 * @since  2.0
	 */
    function check_email_license( $license_key ) {
		if ( empty( $license_key ) ) {
			return false;
		}

		$response = $this->wp_analytify_api_call( 'check_license', $license_key, EMAIL_ANALYTIFY_PRODUCT_ID, EMAIL_ANALYTIFY_STORE_URL );

		set_site_transient( 'wpanalytify_email_license_response', $response, $this->transient_timeout );

		return $response;
	}

   	/**
	 * @param Key $license_key License key to be validated.
	 *
	 * @return mixed $return Value to be returned as response.
	 * @since  2.0
	 */
    function check_campaigns_license( $license_key ) {
		if ( empty( $license_key ) ) {
			return false;
		}

		$response = $this->wp_analytify_api_call( 'check_license', $license_key, CAMPAIGNS_ANALYTIFY_PRODUCT_ID, CAMPAIGNS_ANALYTIFY_STORE_URL );

		set_site_transient( 'wpanalytify_campaigns_license_response', $response, $this->transient_timeout );

		return $response;
	}

	/**
	 * @param Key $license_key License key to be validated.
	 *
	 * @return mixed $return Value to be returned as response.
	 * @since  2.0
	 */
	function check_dimensions_license( $license_key ) {
		if ( empty( $license_key ) ) {
			return false;
		}

		$response = $this->wp_analytify_api_call( 'check_license', $license_key, DIMENSIONS_ANALYTIFY_PRODUCT_ID, DIMENSIONS_ANALYTIFY_STORE_URL );

		set_site_transient( 'wpanalytify_dimensions_license_response', $response, $this->transient_timeout );

		return $response;
	}

	/**
	 * @param Key $license_key License key to be validated.
	 *
	 * @return mixed $return Value to be returned as response.
	 * @since  2.0
	 */
	function check_authors_license( $license_key ) {
		if ( empty( $license_key ) ) {
			return false;
		}

		$response = $this->wp_analytify_api_call( 'check_license', $license_key, AUTHORS_ANALYTIFY_PRODUCT_ID, AUTHORS_ANALYTIFY_STORE_URL );

		//set_site_transient( 'wpanalytify_authors_license_response', $response, $this->transient_timeout );

		return $response;
	}	

	/**
	 * @param Key $license_key License key to be validated.
	 *
	 * @return mixed $return Value to be returned as response.
	 * @since  2.0
	 */
	function check_forms_license( $license_key ) {
		if ( empty( $license_key ) ) {
			return false;
		}

		$response = $this->wp_analytify_api_call( 'check_license', $license_key, FORMS_ANALYTIFY_PRODUCT_ID, FORMS_ANALYTIFY_STORE_URL );

		set_site_transient( 'wpanalytify_forms_license_response', $response, $this->transient_timeout );

		return $response;
	}	

   	/**
   	 * check license key for goals addon
   	 *
	 * @param string $license_key License key to be validated.
	 *
	 * @return mixed $return Value to be returned as response.
	 * @since  2.0.16
	 */
    function check_goals_license( $license_key ) {
		if ( empty( $license_key ) ) {
			return false;
		}

		$response = $this->wp_analytify_api_call( 'check_license', $license_key, GOALS_ANALYTIFY_PRODUCT_ID, GOALS_ANALYTIFY_STORE_URL );

		set_site_transient( 'wpanalytify_goals_license_response', $response, $this->transient_timeout );

		return $response;
	}

	function wp_analytify_api_call( $type, $license, $product_id, $store_url ) {

		$api_params = array(
			'edd_action' 	=> $type,
			'license'    	=> ! empty( $license ) ? $license : false,
			'item_id' 		=> $product_id,
			'url'       	=> home_url()
			);

		// Call the custom API.
		$response = wp_remote_get( esc_url_raw(add_query_arg( $api_params, $store_url )), array( 'timeout' => 15, 'sslverify' => false ) );

		if ( is_wp_error( $response ) )
			return false;


		return json_decode( wp_remote_retrieve_body( $response ) );

	}

	function is_update_available() {

		if ( ! ANALYTIFY_VERSION ) {
			$installed_version = '0';
		} else {
			$installed_version = ANALYTIFY_VERSION;
		}

		$latest_version = get_site_transient( 'wp_analytify_check_latest_version' );

		if( false === $latest_version ){
			$latest_version = $this->get_latest_version( '8a97ba2fd7460564b494427811ade113' );
			set_site_transient( 'wp_analytify_check_latest_version', $latest_version, 60 * 60 * 24 );
		}

		if ( version_compare( $installed_version, $latest_version, '<' ) ) {

			//$update_url = wp_nonce_url( network_admin_url( 'update.php?action=upgrade-plugin&plugin=' . urlencode( plugin_basename( __FILE__ ) ) ), 'upgrade-plugin_' . plugin_basename( __FILE__ ) );

			echo sprintf( esc_html__( '%1$s %2$s %3$s Update Available %4$s &mdash; %9$s %10$s is now available. You currently have %11$s installed.  %5$s Changelog %6$s  %7$s %8$s', 'wp-analytify-pro' ), '<div class="notice notice-warning">', '<p>', '<b>', '</b>', '<a style="text-decoration:none" target="_blank" href="https://analytify.io/changelog/?utm_campaign=WPAnalytifyPro+UpdateAvailable&utm_medium=link&utm_source=WPAnalytifyPro+UpdateAvailable&utm_content=update-available-notice">', '</a>','</p>', '</div>', 'WP Analytify Pro', $latest_version, $installed_version );
		}

	}
}
