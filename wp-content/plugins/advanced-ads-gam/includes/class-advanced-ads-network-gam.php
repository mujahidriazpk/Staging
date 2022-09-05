<?php
/**
 * Network class for GAM
 */

class Advanced_Ads_Network_Gam extends Advanced_Ads_Ad_Network {

	/**
	 * The unique instance of this class
	 *
	 * @var Advanced_Ads_Network_Gam
	 */
	private static $instance;

	/**
	 * Default plugin options
	 *
	 * @var array
	 */
	private static $default_option;

	/**
	 * Construct the unique instance
	 */
	public function __construct() {
		parent::__construct( 'gam', 'Google Ad Manager' );
		add_action( 'wp_ajax_aagam_reload_ads_list', array( $this, 'refresh_ads_table' ) );
		self::$default_option = array(
			'account'           => array(),
			'ad_units'          => array(),
			'units_update_time' => 0,
			'tokens'            => array(),
			'empty-div'         => 'default',
		);
	}

	/**
	 * Retrieve the plugin's option
	 */
	public static function get_option() {
		$options = get_option( AAGAM_OPTION, array() );
		if ( ! is_array( $options ) ) {
			$options = array();
		}
		return $options + self::$default_option;
	}

	/**
	 * Get settings (settings page)
	 */
	public static function get_setting() {
		$default = array(
			'empty-div' => 'default',
		);
		return get_option( AAGAM_SETTINGS, $default );
	}

	/**
	 * Update plugin option
	 *
	 * @param [array] $option The new option value.
	 */
	public static function update_option( $option = array() ) {
		if ( ! is_array( $option ) ) {
			return;
		}
		update_option( AAGAM_OPTION, $option + self::get_option() );
	}

	/**
	 * Output the actual ad list markup
	 *
	 * @param [bool] $hide_idle_ads Whether to show idle ads.
	 */
	public function print_external_ads_list( $hide_idle_ads = true ) {
		require AAGAM_BASE_PATH . 'admin/views/ads-list.php';
	}

	/**
	 * Register our setting into WordPress Setting API
	 *
	 * @param [string] $hook The settings page hook.
	 * @param [string] $section_id Section in which the settings are added.
	 */
	protected function register_settings( $hook, $section_id ) {
		// add setting account setting.
		add_settings_field(
			'gam-netid',
			esc_html__( 'Ad Manager account', 'advanced-ads-gam' ),
			array( $this, 'render_settings_gam_netid' ),
			$hook,
			$section_id
		);
		$connected = self::get_instance()->is_account_connected();

		if ( $connected ) {
			// add setting account setting.
			add_settings_field(
				'gam-empty-div',
				esc_html__( 'Collapse empty elements', 'advanced-ads-gam' ),
				array( $this, 'render_settings_empty_div' ),
				$hook,
				$section_id
			);
		}
	}

	/**
	 * The callback method for the filter "advanced-ads-ad-types"
	 *
	 * @param [array] $types The currently registerd ad types.
	 */
	public function register_ad_type_callback( $types ) {
		$types[ $this->identifier ] = $this->get_ad_type();
		return $types;
	}

	/**
	 * Markup for "empty div" setting
	 */
	public function render_settings_empty_div() {
		require_once AAGAM_BASE_PATH . 'admin/views/gam-empty-div.php';
	}

	/**
	 * Markup for "network id" setting
	 */
	public function render_settings_gam_netid() {
		require_once AAGAM_BASE_PATH . 'admin/views/gam-network-id.php';
	}

	/**
	 * Retrieves an instance of the ad type for this ad network
	 */
	public function get_ad_type() {
		return new Advanced_Ads_Gam_Ad_Type();
	}

	/**
	 * Update the stored ad unit lists from Google
	 *
	 * This method will be called via wp AJAX.
	 * It has to retrieve the list of ads from the ad network and store it as an option.
	 * Does not return ad units - use "get_external_ad_units" if you're looking for an array of ad units.
	 */
	public function update_external_ad_units() {

		$response  = Advanced_Ads_Gam_Admin::get_instance()->ajax_get_ad_units();
		$raw_units = isset( $response['units'] ) ? json_decode( json_encode( $response['units'] ), true ) : array();
		$units     = array();

		$options = self::get_option();

		foreach ( $raw_units as $_unit ) {
			if ( 'ARCHIVED' != $_unit['status'] && 'SMART_BANNER' != $_unit['smartSizeMode'] ) {
				$units[ $_unit['id'] ] = array(
					'id'                    => $_unit['id'],
					'networkCode'           => $options['account']['networkCode'],
					'effectiveRootAdUnitId' => $options['account']['effectiveRootAdUnitId'],
					'name'                  => $_unit['name'],
					'parentPath'            => $_unit['parentPath'],
					'adUnitCode'            => $_unit['adUnitCode'],
					'description'           => $_unit['description'],
					'isFluid'               => 'true' === $_unit['isFluid'],
					'isNative'              => 'true' === $_unit['isNative'],
				);

				// not every ad has ad unit sizes
				if ( isset( $_unit['adUnitSizes'] ) ) {
					$units[ $_unit['id'] ]['adUnitSizes'] = $_unit['adUnitSizes'];
				}
			}
		}

		$units_copy = $units;

		$success = usort(
			$units_copy,
			function( $a, $b ) {
				if ( 0 > strcasecmp( $a['name'], $b['name'] ) ) {
					return -1;
				} else {
					return 1;
				}
			}
		);

		if ( $success ) {
			$units = $units_copy;
		}

		$options['ad_units']          = $units;
		$options['units_update_time'] = time();
		update_option( AAGAM_OPTION, $options );

	}

	/**
	 * Update external ad units and send the ad table body markup.
	 */
	public function refresh_ads_table() {

		if ( ! current_user_can( Advanced_Ads_Plugin::user_cap( 'advanced_ads_manage_options' ) ) ) {
			die;
		}
		$_post = wp_unslash( $_POST );

		if ( isset( $_post['nonce'] ) && false !== wp_verify_nonce( $_post['nonce'], 'gam-selector' ) ) {
			$this->update_external_ad_units();
			ob_start();
			require AAGAM_BASE_PATH . 'admin/views/ads-list-table-body.php';
			$markup = ob_get_clean();

			$update_age_string = $this->get_last_update_string();

			header( 'Content-Type: aaplication/json' );
			echo json_encode(
				array(
					'status'          => true,
					'markup'          => $markup,
					'updateAgeString' => $update_age_string,
				)
			);
		}
		die;
	}

	/**
	 * Sanitize the network specific options
	 *
	 * @param $options the options to sanitize.
	 * @return [mixed] the sanitized options.
	 */
	protected function sanitize_settings( $options ) {
		$options['empty-div'] = ( isset( $options['empty-div'] ) && in_array( $options['empty-div'], array( 'default', 'collapse', 'fill' ) ) ) ? $options['empty-div'] : 'default';
		return $options;
	}

	/**
	 * Sanitize the settings for this ad network
	 *
	 * @param $ad_settings_post.
	 *
	 * @return [mixed] the sanitized settings.
	 */
	public function sanitize_ad_settings( $ad_settings_post ) {
		return $ad_settings_post;
	}

	/**
	 * Get the ad units list
	 *
	 * @return [array] ad units list (Advanced_Ads_Ad_Network_Ad_Unit)
	 */
	public function get_external_ad_units() {
		$options = self::get_option();
		return $options['ad_units'];
	}

	/**
	 * Checks if the ad_unit is supported by advanced ads
	 *
	 * This determines wheter it can be imported or not.
	 *
	 * @param $ad_unit the ad unit.
	 *
	 * @return [bool]
	 */
	public function is_supported( $ad_unit ) {
		return true;
	}

	/**
	 * Whether there is an account connected
	 *
	 * There is no common way to connect to an external account. You will have to implement it somehow, just
	 * like the whole setup process (usually done in the settings tab of this network).
	 * This method provides a way to return this account connection.
	 *
	 * @return [bool] true, when an account was successfully connected
	 */
	public function is_account_connected() {
		return ! empty( self::get_option()['tokens'] );
	}

	/**
	 * Get the JavaScript file that handle this network in the dashboard
	 *
	 * External ad networks rely on the same JavaScript base code. however you still have to provide
	 * a JavaScript class that inherits from the AdvancedAdsAdNetwork js class
	 * this has to point to that file, or return false,
	 * if you don't have to include it in another way (NOT RECOMMENDED!)
	 *
	 * @return string path to the JavaScript file containing the JavaScript class for this ad type
	 */
	public function get_javascript_base_path() {
		return AAGAM_BASE_URL . 'admin/js/gam.js';
	}

	/**
	 * Inline JavaScript variable appendded to the main JavaScript file
	 *
	 * Our script might need translations or other variables (like a nonce, which is included automatically).
	 * Add anything you need in this method and return the array.
	 *
	 * @param $data array holding the data
	 * @return array the data, that will be passed to the base javascript file containing the AdvancedAdsAdNetwork class
	 */
	public function append_javascript_data( &$data ) {

		$kvs = Advanced_Ads_Gam_Admin::get_instance()->get_key_values_types();
		return array(
			'hasGamLicense' => Advanced_Ads_Gam_Admin::has_valid_license() ? 'yes' : 'no',
			'kvTypes'       => $kvs,
			'i18n'          => array(
				'remove'          => esc_html__( 'Remove', 'advanced-ads-gam' ),
				'willBeCreatedAs' => esc_html( 'Will be created as', 'advanced-ads-gam' ),
			),
		);
	}

	/**
	 * Return age of the ad units list in seconds
	 *
	 * @return bool|int seconds since last update or FALSE if the age field does not exist.
	 */
	public function get_list_update_age() {
		$options = self::get_option();
		if ( empty( $options['units_update_time'] ) ) {
			return false;
		} else {
			return time() - absint( $options['units_update_time'] );
		}
	}

	/**
	 * Return the last units list update message
	 *
	 * @return [string] $msg
	 */
	public function get_last_update_string() {
		$options = self::get_option();
		$msg     = __( 'Last updated:', 'advanced-ads-gam' );
		if ( empty( $options['units_update_time'] ) ) {
			$msg .= ' ' . __( 'unknown', 'advanced-ads-gam' );
		} else {
			$age = time() - absint( $options['units_update_time'] );
			if ( $age < 60 ) {
				$msg .= ' ' . __( 'just now', 'advanced-ads-gam' );
			} elseif ( $age < 3600 ) {
				$msg .= ' ' . __( 'less than an hour ago', 'advanced-ads-gam' );
			} elseif ( $age < 86400 ) {
				$msg .= ' ' . __( 'less than 24 hours ago', 'advanced-ads-gam' );
			} else {
				$days = ( $age - ( $age % 86400 ) ) / 86400;
				$msg .= ' ' . sprintf( _n( 'one day ago', '%s days ago', $days, 'advanced-ads-gam' ), number_format_i18n( $days ) );
			}
		}
		return $msg;
	}

	/**
	 * Decode post content to get working ad unit data
	 *
	 * @param [string] $content The post content.
	 *
	 * @return [array] The ad unit data.
	 */
	public static function post_content_to_adunit( $content ) {
		$result = false;
		$un64   = base64_decode( $content );
		if ( false !== $un64 ) {
			$jdecode = json_decode( $un64, true );
			if ( null !== $jdecode ) {
				$result = $jdecode;
			}
		}
		return $result;
	}

	/**
	 * Returns the unique instance of this class
	 *
	 * @return Advanced_Ads_Network_Gam
	 */
	final public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new Advanced_Ads_Network_Gam();
		}
		return self::$instance;
	}

}
