<?php
defined( 'WPINC' ) || exit;

class Advanced_Ads_Responsive_Amp_Admin {
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'wp_admin_plugins_loaded' ) );
	}

	/**
	 * Load actions and filters.
	 */
	public function wp_admin_plugins_loaded() {
		if ( ! class_exists( 'Advanced_Ads', false ) ) { return; }

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 9 );
		// Amp ad type.
		add_filter( 'advanced-ads-save-options', array( $this, 'save_ad_options' ) );
		// Adsense ad type.
		new Advanced_Ads_Responsive_Amp_Adsense_Admin;
	}

	/**
	 * Enqueue admin-specific JavaScript.
	 */
	public function enqueue_admin_scripts() {
		if ( Advanced_Ads_Admin::screen_belongs_to_advanced_ads() ) {
			$uriRelPath = plugin_dir_url( __FILE__ );
		    wp_enqueue_script( ADVADS_SLUG . '-amp-admin', $uriRelPath . 'assets/admin.js', array( 'jquery' ), AAR_VERSION );
			wp_localize_script( ADVADS_SLUG . '-amp-admin', 'advanced_ads_amp_admin', array(
				'supported_adsense_types' => Advanced_Ads_Responsive_Amp::$supported_adsense_types )
			);
		}
	}

	/**
	 * Sanitize and save ad options.
	 *
	 * @param arr $options
	 * @return arr $options
	 */
	public function save_ad_options( array $options ) {
		$attributes = isset( $_POST['advanced_ad']['amp']['attributes'] ) ? array_values( $_POST['advanced_ad']['amp']['attributes']  ) : array();
		$data = isset( $_POST['advanced_ad']['amp']['data'] ) ? array_values( $_POST['advanced_ad']['amp']['data'] ) : array();

		unset( $options['amp']['attributes'], $options['amp']['data'] );

		if ( is_array( $attributes ) && is_array( $data ) && count( $attributes ) === count( $data ) ) {
			foreach ( $attributes as $_i => $_attribute ) {
				$clear_attribute = sanitize_key( $_attribute );
				$clear_data = isset( $data[ $_i ] ) ? $data[ $_i ] : '';

				if ( $clear_attribute && $clear_data ) {
					$options['amp']['attributes'][ $clear_attribute ] = $clear_data;
				}
			}
		}

		if ( ! empty( $_POST['advanced_ad']['amp']['fallback'] ) ) {
			$options['amp']['fallback'] = wp_kses_post( $_POST['advanced_ad']['amp']['fallback'] );
		}

		return $options;
	}

	/**
	 * callback to display the AMP display condition
	 *
	 * @param arr $options options of the condition
	 * @param int $index index of the condition
	 */
	public static function metabox_amp( $options, $index = 0, $form_name = '' ) {
		if ( ! isset ( $options['type'] ) || '' === $options['type'] ) { return; }

		$type_options = Advanced_Ads_Display_Conditions::get_instance()->conditions;

		if ( ! isset( $type_options[ $options['type'] ] ) ) {
			return;
		}

		// form name basis
		if ( method_exists( 'Advanced_Ads_Display_Conditions', 'get_form_name_with_index' ) ) {
			$name = Advanced_Ads_Display_Conditions::get_form_name_with_index( $form_name, $index );
		} else {
			$name = Advanced_Ads_Display_Conditions::FORM_NAME . '[' . $index . ']';
		}

		// options
		$operator = isset( $options['operator'] ) ? $options['operator'] : 'is';

		?><input type="hidden" name="<?php echo $name; ?>[type]" value="<?php echo $options['type']; ?>"/>
		<select name="<?php echo $name; ?>[operator]">
			<option value="is" <?php selected( 'is', $operator ); ?>><?php _e( 'is', 'advanced-ads-responsive' ); ?></option>
			<option value="is_not" <?php selected( 'is_not', $operator ); ?>><?php _e( 'is not', 'advanced-ads-responsive' ); ?></option>
		</select>
		<p class="description"><?php echo $type_options[ $options['type'] ]['description']; ?></p><?php
	}

	/**
	 * Check if an amp plugin is enabled.
	 *
	 * @return bool
	 */
	public static function has_amp_plugin() {
		return function_exists( 'is_amp_endpoint' ) || function_exists( 'is_wp_amp' ) || function_exists( 'ampforwp_is_amp_endpoint' );
	}
}



