<?php

/**
 *
 * NOTE: can not rely actively on base plugin without prior test for existence (only after plugins_loaded hook)
 */
class Advanced_Ads_Pro_Admin {

	/**
	 * Link to plugin page
	 *
	 * @since 1.1
	 * @const
	 */
	const PLUGIN_LINK = 'https://wpadvancedads.com/add-ons/advanced-ads-pro/';

	/**
	 * Field name of the user role
	 *
	 * @since 1.2.5
	 * @const
	 */
	const ROLE_FIELD_NAME = 'advanced-ads-role';

	/**
	 * Advanced Ads user roles array.
	 *
	 * @var array
	 */
	private $roles;

	/**
	 * Initialize the plugin
	 *
	 * @since   1.0.0
	 */
	public function __construct() {
		$this->roles = array(
			'advanced_ads_admin'   => __( 'Ad Admin', 'advanced-ads-pro' ),
			'advanced_ads_manager' => __( 'Ad Manager', 'advanced-ads-pro' ),
			'advanced_ads_user'    => __( 'Ad User', 'advanced-ads-pro' ),
			''                     => __( '--no role--', 'advanced-ads-pro' ),
		);

		// Add add-on settings to plugin settings page.
		add_action( 'advanced-ads-settings-init', array( $this, 'settings_init' ), 9, 1 );
		add_filter( 'advanced-ads-setting-tabs', array( $this, 'setting_tabs' ) );

		// Add user role selection to users page.
		add_action( 'show_user_profile', array( $this, 'add_user_role_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'add_user_role_fields' ) );

		add_action( 'profile_update', array( $this, 'save_user_role' ) );

		// Display warning if advanced visitor conditions are not active.
		add_action( 'advanced-ads-visitor-conditions-after', array( $this, 'show_condition_notice' ), 10, 2 );
		// Display "once per page" field.
		add_action( 'advanced-ads-output-metabox-after', array( $this, 'render_ad_output_options' ) );
		// Load admin style sheet.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		// Render repeat option for Content placement.
		add_action( 'advanced-ads-placement-post-content-position', array( $this, 'render_placement_repeat_option' ), 10, 2 );
		add_filter( 'pre_update_option_advanced-ads', array( $this, 'pre_update_advanced_ads_options' ), 10, 2 );

		// Show/hide warnings for privacy module based on Pro state.
		add_filter( 'advanced-ads-privacy-custom-show-warning', array( $this, 'show_custom_privacy_warning' ) );
		add_filter( 'advanced-ads-privacy-tcf-show-warning', '__return_false' );
		add_filter( 'advanced-ads-privacy-custom-link-attributes', array( $this, 'privacy_link_attributes' ) );
		add_filter( 'advanced-ads-ad-privacy-hide-ignore-consent', array( $this, 'hide_ignore_consent_checkbox' ), 10, 3 );

		// Show a warning if cache-busting is enabled, but no placement is used for a widget.
		add_action( 'in_widget_form', array( $this, 'show_no_placement_in_widget_warning' ), 10, 3 );
		add_action( 'advanced-ads-export-options', array( $this, 'export_options' ) );

		// Suggest a text for the WP Privacy Policy
		add_action( 'admin_init', array( $this, 'add_privacy_policy_content' ) );
	}

	/**
	 * Add settings to settings page
	 *
	 * @param string $hook settings page hook.
	 * @since 1.0.0
	 */
	public function settings_init( $hook ) {
		register_setting( Advanced_Ads_Pro::OPTION_KEY, Advanced_Ads_Pro::OPTION_KEY );
		register_setting( Advanced_Ads_Pro::OPTION_KEY . '-license', Advanced_Ads_Pro::OPTION_KEY . '-license' );

		/**
		 * Allow Ad Admin to save pro options.
		 *
		 * @param array $settings Array with allowed options.
		 *
		 * @return array
		 */
		add_filter( 'advanced-ads-ad-admin-options', function( $options ) {
			$options[] = Advanced_Ads_Pro::OPTION_KEY;
			$options[] = Advanced_Ads_Pro::OPTION_KEY . '-license';

			return $options;
		} );

		// Add license key field to license section.
		add_settings_field(
			'pro-license',
			'Pro',
			array( $this, 'render_settings_license_callback' ),
			'advanced-ads-settings-license-page',
			'advanced_ads_settings_license_section'
		);

		// Add new section.
		add_settings_section(
			Advanced_Ads_Pro::OPTION_KEY . '_modules-enable',
			'',
			array( $this, 'render_modules_enable' ),
			Advanced_Ads_Pro::OPTION_KEY . '-settings'
		);

		// Add new section.
		add_settings_section(
			'advanced_ads_pro_settings_section',
			'',
			array( $this, 'render_other_settings' ),
			Advanced_Ads_Pro::OPTION_KEY . '-settings'
		);
		// Setting for Autoptimize support.
		$has_optimizer_installed = Advanced_Ads_Checks::active_autoptimize();
		if ( ! $has_optimizer_installed && method_exists( 'Advanced_Ads_Checks', 'active_wp_rocket' ) ) {
			$has_optimizer_installed = Advanced_Ads_Checks::active_wp_rocket();
		}
		if ( $has_optimizer_installed ) {
			add_settings_field(
				'autoptimize-support',
				__( 'Allow optimizers to modify ad codes', 'advanced-ads-pro' ),
				array( $this, 'render_settings_autoptimize' ),
				Advanced_Ads_Pro::OPTION_KEY . '-settings',
				'advanced_ads_pro_settings_section'
			);
		}

		add_settings_field(
			'disable-by-post-types',
			__( 'Disable ads for post types', 'advanced-ads-pro' ),
			array( $this, 'render_settings_disable_post_types' ),
			$hook,
			'advanced_ads_setting_section_disable_ads'
		);
	}

	/**
	 * Copy settings from `general` tab in order to prevent it from being cleaned
	 * when Pro is deactivated.
	 *
	 * @param mixed $options Advanced Ads options.
	 * @return mixed options
	 */
	public function pre_update_advanced_ads_options( $options ) {
		$pro = Advanced_Ads_Pro::get_instance()->get_options();

		if ( isset( $options['pro']['general']['disable-by-post-types'] ) && is_array( $options['pro']['general']['disable-by-post-types'] ) ) {
			$pro['general']['disable-by-post-types'] = $options['pro']['general']['disable-by-post-types'];
		} else {
			$pro['general']['disable-by-post-types'] = array();
		}
		Advanced_Ads_Pro::get_instance()->update_options( $pro );
		return $options;
	}

	/**
	 * Render content of module enable option
	 */
	public function render_modules_enable() {
	}

	/**
	 * Render additional pro settings
	 *
	 * @since 1.1
	 */
	public function render_other_settings() {
		// Save options when the user is on the "Pro" tab.
		$selected = $this->get_disable_by_post_type_options();
		foreach ( $selected as $item ) { ?>
			<input type="hidden" name="<?php echo esc_attr( AAP_SLUG ); ?>[general][disable-by-post-types][]" value="<?php echo esc_html( $item ); ?>">
			<?php
		}
	}

	/**
	 * Render tooltip_option settings field
	 *
	 * @since 1.2.3
	 */
	public function render_settings_autoptimize() {
		$options                      = Advanced_Ads_Pro::get_instance()->get_options();
		$autoptimize_support_disabled = isset( $options['autoptimize-support-disabled'] ) ? $options['autoptimize-support-disabled'] : false;
		require AAP_BASE_PATH . '/views/setting_autoptimize.php';
	}

	/**
	 * Render settings to disable ads by post types.
	 */
	public function render_settings_disable_post_types() {
		$selected = $this->get_disable_by_post_type_options();

		$post_types        = get_post_types(
			array(
				'public'             => true,
				'publicly_queryable' => true,
			),
			'objects',
			'or'
		);
		$type_label_counts = array_count_values( wp_list_pluck( $post_types, 'label' ) );

		require AAP_BASE_PATH . '/views/setting_disable_post_types.php';
	}

	/**
	 * Get "Disabled by post type" Pro options.
	 */
	private function get_disable_by_post_type_options() {
		$options = Advanced_Ads_Pro::get_instance()->get_options();
		if ( isset( $options['general']['disable-by-post-types'] ) && is_array( $options['general']['disable-by-post-types'] ) ) {
			$selected = $options['general']['disable-by-post-types'];
		} else {
			$selected = array();
		}
		return $selected;
	}

	/**
	 * Register license
	 */
	public function render_settings_license_callback() {
		$licenses       = get_option( ADVADS_SLUG . '-licenses', array() );
		$license_key    = isset( $licenses['pro'] ) ? $licenses['pro'] : '';
		$license_status = get_option( Advanced_Ads_Pro::OPTION_KEY . '-license-status', false );

		// Get license status for old key.
		if ( ! $license_status ) {
			$old_license_status = get_option( Advanced_Ads_Pro::OPTION_KEY . '-modules-license-status', false );
			if ( $old_license_status ) {
				update_option( Advanced_Ads_Pro::OPTION_KEY . '-license-status', $old_license_status );
				delete_option( Advanced_Ads_Pro::OPTION_KEY . '-modules-license-status', $old_license_status );
			}
		}

		$index        = 'pro';
		$plugin_name  = AAP_PLUGIN_NAME;
		$options_slug = Advanced_Ads_Pro::OPTION_KEY;
		$plugin_url   = self::PLUGIN_LINK;

		// Template in main plugin.
		include ADVADS_BASE_PATH . 'admin/views/setting-license.php';
	}

	/**
	 * Add tracking settings tab
	 *
	 * @since 1.2.0
	 * @param array $tabs existing setting tabs.
	 * @return array $tabs setting tabs with AdSense tab attached.
	 */
	public function setting_tabs( array $tabs ) {
		$tabs['pro'] = array(
			// TODO abstract string.
			'page'  => Advanced_Ads_Pro::OPTION_KEY . '-settings',
			'group' => Advanced_Ads_Pro::OPTION_KEY,
			'tabid' => 'pro',
			'title' => 'Pro',
		);

		return $tabs;
	}

	/**
	 * Form field for user role selection
	 *
	 * @param array $user user data.
	 */
	public function add_user_role_fields( $user ) {
		if ( ! current_user_can( 'edit_users' ) ) {
			return;
		}

		$role = get_user_meta( $user->ID, self::ROLE_FIELD_NAME, true );
		?>
	<h3><?php esc_html_e( 'Advanced Ads User Role', 'advanced-ads-pro' ); ?></h3>
	<table class="form-table">
		<tr>
		<th><label for="advads_pro_role"><?php esc_html_e( 'Ad User Role', 'advanced-ads-pro' ); ?></label></th>
		<td><select name="<?php echo esc_attr( self::ROLE_FIELD_NAME ); ?>" id="advads_pro_role">
			<?php
			foreach ( $this->roles as $_slug => $_name ) :
				?>
				<option value="<?php echo esc_attr( $_slug ); ?>" <?php selected( $role, $_slug ); ?>><?php echo esc_html( $_name ); ?></option>
				<?php
			endforeach;
			?>
		</select>
		<p class="description"><?php esc_html_e( 'Please note, with the last update, the “Ad Admin“ and “Ad Manager“ roles have the “upload_files“ and the “unfiltered_html“ capabilities.', 'advanced-ads-pro' ); ?></p>
		</td>
		</tr>
	</table>
		<?php
	}

	/**
	 * Update the user role
	 *
	 * @param int $user_id ID of the user.
	 */
	public function save_user_role( $user_id) {
		if (
			! array_key_exists( self::ROLE_FIELD_NAME, $_POST )
			|| ! current_user_can( 'edit_users' )
			|| ! wp_verify_nonce($_POST['_wpnonce'], 'update-user_' . $user_id)
		) {
			return;
		}

		// check if this is a valid user role.
		$user_role = sanitize_text_field( $_POST[ self::ROLE_FIELD_NAME ] );
		if ( ! array_key_exists( $user_role, $this->roles ) ) {
			return;
		}

		// Get user object.
		$user = new WP_User( $user_id );

		// Remove previous role.
		$prev_role = get_user_meta( $user_id, self::ROLE_FIELD_NAME, true );
		$user->remove_role( $prev_role );

		// Save new role as user meta.
		update_user_meta( $user_id, self::ROLE_FIELD_NAME, $user_role );

		if ( $user_role ) {
			// Add role.
			$user->add_role( $user_role );
		}
	}


	/**
	 * Show a notice if advanced visitor conditions are disabled. Maybe some users are looking for it
	 *
	 * @param Advanced_Ads_Ad $ad Advanced_Ads_Ad object.
	 */
	public function show_condition_notice( $ad ) {
		$options = Advanced_Ads_Pro::get_instance()->get_options();

		if ( ! isset( $options['advanced-visitor-conditions']['enabled'] ) ) {
			echo '<p>' . sprintf(
				wp_kses(
							// Translators: %s is a URL.
					__( 'Enable the Advanced Visitor Conditions <a href="%s" target="_blank">in the settings</a>.', 'advanced-ads-pro' ),
					array(
						'a' => array(
							'href'   => array(),
							'target' => array(),
						),
					)
				),
				esc_url( admin_url( 'admin.php?page=advanced-ads-settings#top#pro' ) )
			) . '</p>';
		}
	}

	/**
	 * Add output options to ad edit page
	 *
	 * @param Advanced_Ads_Ad $ad Advanced_Ads_Ad ad object.
	 */
	public function render_ad_output_options( Advanced_Ads_Ad $ad ) {
		$output_options = $ad->options( 'output' );
		$once_per_page  = ! empty( $output_options['once_per_page'] ) ? 1 : 0;

		require AAP_BASE_PATH . '/views/setting_output_once.php';

		// Get CodeMirror setting for Custom code textarea.
		$settings    = $this->get_code_editor_settings();
		$custom_code = ! empty( $output_options['custom-code'] ) ? esc_textarea( $output_options['custom-code'] ) : '';
		require AAP_BASE_PATH . '/views/setting_custom_code.php';
	}

	/**
	 * Render repeat option for Content placement.
	 *
	 * @param string $_placement_slug id of the placement.
	 * @param array  $_placement placement options.
	 */
	public function render_placement_repeat_option( $_placement_slug, $_placement ) {
		$words_between_repeats = ! empty( $_placement['options']['words_between_repeats'] ) ? absint( $_placement['options']['words_between_repeats'] ) : 0;
		require AAP_BASE_PATH . '/views/setting_repeat.php';
	}

	/**
	 * Get CodeMirror settings.
	 */
	public function get_code_editor_settings() {
		global $wp_version;
		if ( 'advanced_ads' !== get_current_screen()->id
			|| defined( 'ADVANCED_ADS_DISABLE_CODE_HIGHLIGHTING' )
			|| -1 === version_compare( $wp_version, '4.9' ) ) {
			return false;
		}

		// Enqueue code editor and settings for manipulating HTML.
		$settings = wp_enqueue_code_editor( array( 'type' => 'text/html' ) );

		if ( ! $settings ) {
			$settings = false;
		}

		return $settings;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 */
	public function enqueue_admin_styles() {
		wp_enqueue_style( AAP_SLUG . '-admin-styles', AAP_BASE_URL . 'assets/admin.css', array(), AAP_VERSION );
	}

	/**
	 * Show a note about a deprecated feature and link to the appropriate page in our manual
	 *
	 * @param string $feature simple string to indicate the deprecated feature. Will be added to the UTM campaign attribute.
	 */
	public static function show_deprecated_notice( $feature = '' ) {
		$url = esc_url( ADVADS_URL ) . 'manual/deprecated-features/';

		if ( '' !== $feature ) {
			$url .= '#utm_source=advanced-ads&utm_medium=link&utm_campaign=deprecated-' . sanitize_title_for_query( $feature );
		}

		echo '<span class="advads-notice-inline advads-error">';
		printf(
			// Translators: %1$s is the opening link tag, %2$s is closing link tag.
			esc_html__( 'This feature is deprecated. Please find the removal schedule %1$shere%2$s', 'advanced-ads-pro' ),
			'<a href="' . esc_url( $url ) . '" target="_blank">',
			'</a>'
		);
		echo '</span>';
	}

	/**
	 * Only show privacy warning if cache-busting module not enabled.
	 *
	 * @param bool $show Whether to show warning.
	 *
	 * @return bool
	 */
	public function show_custom_privacy_warning( $show ) {
		if ( ! $show ) {
			return $show;
		}

		$options = Advanced_Ads_Pro::get_instance()->get_options();

		return ! isset( $options['cache-busting']['enabled'] );
	}

	/**
	 * Update Link in Privacy settings ot settings page instead of external plugin page.
	 *
	 * @return array
	 */
	public function privacy_link_attributes() {
		return array(
			'href' => esc_url( admin_url( 'admin.php?page=advanced-ads-settings#top#pro' ) ),
		);
	}

	/**
	 * Show the ignore-consent checkbox if this ad has custom code and type is image or dummy.
	 * The filter is called `advanced-ads-ad-privacy-hide-ignore-consent`, so the return needs to be !$hide to show.
	 *
	 * @param bool            $hide Whether to show ignore-consent checkbox.
	 * @param Advanced_Ads_Ad $ad   The ad object
	 *
	 * @return bool
	 */
	public function hide_ignore_consent_checkbox( $hide, Advanced_Ads_Ad $ad ) {
		if ( ! $hide || ! in_array( $ad->type, array( 'image', 'dummy' ), true ) ) {
			return $hide;
		}

		return empty( Advanced_Ads_Pro::get_instance()->get_custom_code( $ad ) );
	}

	/**
	 * Show a warning below the form of Advanced Ads widgets if cache-busting is enabled
	 * but the widget does not use a placement or "Force passive cache-busting" is enabled
	 *
	 * Uses the in_widget_form action hook
	 *
	 * @param WP_Widget $widget   The widget instance (passed by reference).
	 * @param null      $return   Return null if new fields are added.
	 * @param array     $instance An array of the widget's settings.
	 */
	public function show_no_placement_in_widget_warning( $widget, $return, $instance ) {

		// bail if this is not the Advanced Ads widget
		if ( ! is_a( $widget, 'Advanced_Ads_Widget' ) ) {
			return;
		}

		// bail if cache-busting is not enabled or if Force passive cache-busting is enabled
		$options = Advanced_Ads_Pro::get_instance()->get_options();
		if ( empty( $options['cache-busting']['enabled'] ) || isset( $options['cache-busting']['passive_all'] ) ) {
			return;
		}

		// check item ID and show warning if it is given but does not contain a placement
		if ( ! empty( $instance['item_id'] ) && 0 !== strpos( $instance['item_id'], 'placement_' ) ) {
			?>
			<p class="advads-notice-inline advads-error">
			<?php esc_html_e( 'Select a Sidebar placement to enable cache-busting.', 'advanced-ads-pro' ); ?>
			<a href="<?php echo esc_url( ADVADS_URL ); ?>manual/cache-busting/#Cache-Busting_in_Widgets" target="_blank">
				<?php esc_html_e( 'Learn more', 'advanced-ads-pro' ); ?>
			</a>
			</p>
			<?php
		}
	}

	/**
	 * Add Pro options to the list of options to be exported.
	 *
	 * @param $options Array of option data keyed by option keys.
	 * @return $options Array of option data keyed by option keys.
	 */
	public function export_options( $options ) {
		$options[ Advanced_Ads_Pro::OPTION_KEY ] = get_option( Advanced_Ads_Pro::OPTION_KEY );
		return $options;
	}

	/**
	 * Adds a privacy policy statement under Settings > Privacy > Policy Guide
	 * which customers can use as a basic templace.
	 */
	public function add_privacy_policy_content() {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}

		ob_start();
		include AAP_BASE_PATH . 'views/privacy-policy-content.php';

		wp_add_privacy_policy_content( AAP_PLUGIN_NAME, wp_kses_post( wpautop( ob_get_clean(), false ) ) );
	}
}
