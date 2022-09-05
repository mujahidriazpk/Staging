<?php
/**
 * Class Advanced_Ads_Pro
 */
class Advanced_Ads_Pro {

	/**
	 * Pro options
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * Interal plugin options – set by the plugin
	 *
	 * @var     array (if loaded)
	 */
	protected $internal_options;

	/**
	 * Option name shared by child modules.
	 *
	 * @var string
	 */
	const OPTION_KEY = 'advanced-ads-pro';

	/**
	 * Instance of Advanced_Ads_Pro
	 *
	 * @var Advanced_Ads_Pro
	 */
	private static $instance;

	/**
	 * Advanced_Ads_Pro constructor.
	 */
	private function __construct() {
		// Setup plugin once base plugin that is initialized at priority `20` is available.
		add_action( 'plugins_loaded', array( $this, 'init' ), 30 );
	}

	/**
	 * Instance of Advanced_Ads_Pro
	 *
	 * @return Advanced_Ads_Pro
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Must not be called before `plugins_loaded` hook.
	 */
	public function init() {

		if ( ! class_exists( 'Advanced_Ads', false ) ) {
			add_action( 'admin_notices', array( $this, 'missing_plugin_notice' ) );
			return;
		}

		// Load gettext domain.
		$this->load_textdomain();

		// Load config and modules.
		$options = $this->get_options();
		Advanced_Ads_ModuleLoader::loadModules( AAP_PATH . '/modules/', isset( $options['modules'] ) ? $options['modules'] : array() );

		// Load admin on demand.
		if ( is_admin() ) {
			new Advanced_Ads_Pro_Admin();
			// Run after the internal Advanced Ads version has been updated by the `Advanced_Ads_Upgrades`, because.
			// The `Advanced_Ads_Admin_Notices` can update this version, and the `Advanced_Ads_Upgrades` will not be called.
			add_action( 'init', array( $this, 'maybe_update_capabilities' ) );

			add_filter( 'advanced-ads-notices', array( $this, 'add_notices' ) );
			add_filter( 'advanced-ads-add-ons', array( $this, 'register_auto_updater' ), 10 );
		} else {
			// Force advanced js file to be attached.
			add_filter( 'advanced-ads-activate-advanced-js', '__return_true' );
			// Check autoptimize.
			if ( method_exists( 'Advanced_Ads_Checks', 'requires_noptimize_wrapping' ) && Advanced_Ads_Checks::requires_noptimize_wrapping() && ! isset( $options['autoptimize-support-disabled'] ) ) {
				add_filter( 'advanced-ads-output-inside-wrapper', array( $this, 'autoptimize_support' ) );
			}
		}
		new Advanced_Ads_Pro_Compatibility();

		// Override shortcodes.
		remove_shortcode( 'the_ad' );
		remove_shortcode( 'the_ad_group' );
		remove_shortcode( 'the_ad_placement' );
		add_shortcode( 'the_ad', array( $this, 'shortcode_display_ad' ) );
		add_shortcode( 'the_ad_group', array( $this, 'shortcode_display_ad_group' ) );
		add_shortcode( 'the_ad_placement', array( $this, 'shortcode_display_ad_placement' ) );

		add_filter( 'advanced-ads-can-display', array( $this, 'can_display_by_display_limit' ), 10, 3 );
		add_filter( 'advanced-ads-ad-output', array( $this, 'add_custom_code' ), 30, 2 );
		add_filter( 'advanced-ads-output-final', array( $this, 'encode_ad_custom_code' ), 20, 2 );
		add_filter( 'advanced-ads-placement-content-offsets', array( $this, 'placement_content_offsets' ), 10, 6 );

		add_action( 'wp_head', array( $this, 'wp_head' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
	}

	/**
	 * Enqueue front end script.
	 */
	public function wp_enqueue_scripts() {

		// Do not enqueue on AMP pages.
		if ( function_exists( 'advads_is_amp' ) && advads_is_amp() ) {
			return;
		}

		wp_enqueue_script(
			'advanced-ads-pro/front',
			sprintf( '%sassets/js/advanced-ads-pro%s.js', AAP_BASE_URL, defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' ),
			array( 'jquery', ADVADS_SLUG . '-advanced-js' ),
			AAP_VERSION,
			true
		);
	}

	/**
	 * Front end header script.
	 */
	public function wp_head() {
		// Do not enqueue on AMP pages.
		if ( function_exists( 'advads_is_amp' ) && advads_is_amp() ) {
			return;
		}

		?><script type="text/javascript">
		var advadsCfpQueue = [];
		var advadsCfpAd = function( adID ){
			if ( 'undefined' == typeof advadsProCfp ) { advadsCfpQueue.push( adID ) } else { advadsProCfp.addElement( adID ) }
		};
		</script>
		<?php

	}

	/**
	 * Fired when the plugin is activated.boolean $network_wide True if WPMU superadmin uses"Network Activate" action, false ifWPMU is disabled or plugin isactivated on an individual blog.
	 *
	 * @param    boolean $network_wide    True if WPMU superadmin uses
	 *                                    "Network Activate" action, false if
	 *                                    WPMU is disabled or plugin is
	 *                                    activated on an individual blog.
	 *
	 * @since    1.2.5
	 */
	public static function activate( $network_wide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {
				// Get all blog ids.
				global $wpdb;
				$blog_ids         = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
				$original_blog_id = $wpdb->blogid;

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::single_activate();
				}

				switch_to_blog( $original_blog_id );
			} else {
				self::single_activate();
			}
		} else {
			self::single_activate();
		}
	}

	/**
	 * Fired when the plugin is deactivated.boolean $network_wide True if WPMU superadmin uses"Network Activate" action, false ifWPMU is disabled or plugin isactivated on an individual blog.
	 *
	 * @param    boolean $network_wide    True if WPMU superadmin uses
	 *                                    "Network Activate" action, false if
	 *                                    WPMU is disabled or plugin is
	 *                                    activated on an individual blog.
	 *
	 * @since    1.2.5
	 */
	public static function deactivate( $network_wide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {
				// Get all blog ids.
				global $wpdb;
				$blog_ids         = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
				$original_blog_id = $wpdb->blogid;

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::single_deactivate();
				}

				switch_to_blog( $original_blog_id );
			} else {
				self::single_deactivate();
			}
		} else {
			self::single_deactivate();
		}
	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @param int $blog_id ID of the new blog.
	 */
	public static function activate_new_site( $blog_id ) {
		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();
	}

	/**
	 * Perform when Advanced Ads Pro is enabled on a single site
	 */
	public static function single_activate() {
		// Create new user roles.
		add_role(
			'advanced_ads_admin',
			__( 'Ad Admin', 'advanced-ads-pro' ),
			array(
				'read'                           => true,
				'advanced_ads_manage_options'    => true,
				'advanced_ads_see_interface'     => true,
				'advanced_ads_edit_ads'          => true,
				'advanced_ads_manage_placements' => true,
				'advanced_ads_place_ads'         => true,
				'upload_files'                   => true,
				'unfiltered_html'                => true,
			)
		);
		add_role(
			'advanced_ads_manager',
			__( 'Ad Manager', 'advanced-ads-pro' ),
			array(
				'read'                           => true,
				'advanced_ads_see_interface'     => true,
				'advanced_ads_edit_ads'          => true,
				'advanced_ads_manage_placements' => true,
				'advanced_ads_place_ads'         => true,
				'upload_files'                   => true,
				'unfiltered_html'                => true,
			)
		);
		add_role(
			'advanced_ads_user',
			__( 'Ad User', 'advanced-ads-pro' ),
			array(
				'read'                   => true,
				'advanced_ads_place_ads' => true,
			)
		);

		self::enable_placement_test_emails();
	}

	/**
	 * Perform when Advanced Ads Pro is deactivated on a single site.
	 */
	public static function single_deactivate() {
		// Remove user roles.
		remove_role( 'advanced_ads_admin' );
		remove_role( 'advanced_ads_manager' );
		remove_role( 'advanced_ads_user' );

		self::disable_placement_test_emails();
	}

	/**
	 * Show warning if Advanced Ads js is not activated
	 */
	public function missing_plugin_notice() {
		echo '
		<div class="error">
		  <p>' . sprintf(
			wp_kses(
				// Translators: %s is the plugin’s name.
				__( '<strong>%s</strong> requires the <strong><a href="https://wpadvancedads.com" target="_blank">Advanced Ads</a></strong> plugin to be installed and activated on your site.', 'advanced-ads-pro' ),
				array(
					'strong' => array(),
					'a'      => array(
						'href'   => array(),
						'target' => array(),
					),
				)
			),
			'Advanced Ads Pro'
		) . '&nbsp;';
		$plugins = get_plugins();
		if ( isset( $plugins['advanced-ads/advanced-ads.php'] ) ) { // Is installed, but not active.
			echo '<a class="button button-primary" href="' . esc_url( wp_nonce_url( 'plugins.php?action=activate&amp;plugin=advanced-ads/advanced-ads.php&amp', 'activate-plugin_advanced-ads/advanced-ads.php' ) ) . '">' . esc_html__( 'Activate Now', 'advanced-ads-pro' ) . '</a>';
		} else {
			echo '<a class="button button-primary" href="' . esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=advanced-ads' ), 'install-plugin_advanced-ads' ) ) . '">' . esc_html__( 'Install Now', 'advanced-ads-pro' ) . '</a>';
		}
		echo '</p></div>';
	}

	/**
	 * Return Advanced Ads Pro options
	 *
	 * @return array
	 */
	public function get_options() {
		if ( ! isset( $this->options ) ) {
			$default_options = array();
			$this->options   = get_option( self::OPTION_KEY, $default_options );
			// Handle previous option key.
			if ( $this->options === array() ) {
				$old_options = get_option( self::OPTION_KEY . '-modules', false );
				if ( $old_options ) {
					// Update old options.
					$this->update_options( $old_options );
					delete_option( self::OPTION_KEY . '-modules' );
				}
			}
		}

		return $this->options;
	}

	/**
	 * Set a specific option.
	 *
	 * @param string $key key to identify the option.
	 * @param mixed  $value value of the option.
	 */
	public function set_option( $key, $value ) {
		$options = $this->get_options();

		$options[ $key ] = $value;
		$this->update_options( $options );
	}

	/**
	 * Update all Advanced Ads Pro options.
	 *
	 * @param array $options
	 */
	public function update_options( array $options ) {
		$updated = update_option( self::OPTION_KEY, $options );

		if ( $updated ) {
			$this->options = $options;
		}
	}

	/**
	 * Load the plugin’s text domain.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( AAP_SLUG, false, AAP_BASE_DIR . '/languages' );
	}

	/**
	 * Register plugin for the auto updater in the base plugin
	 *
	 * @param array $plugins plugin that are already registered for auto updates.
	 * @return array $plugins
	 */
	public function register_auto_updater( array $plugins = array() ) {

		$plugins['pro'] = array(
			'name'         => AAP_PLUGIN_NAME,
			'version'      => AAP_VERSION,
			'path'         => AAP_BASE_PATH . 'advanced-ads-pro.php',
			'options_slug' => self::OPTION_KEY,
		);
		return $plugins;
	}

	/**
	 * Add autoptimize support
	 *
	 * @param string          $ad_content ad content.
     * @return string output that should not be changed by Autoptimize.
	 */
	public function autoptimize_support( $ad_content = '' ) {
		return '<!--noptimize-->' . $ad_content . '<!--/noptimize-->';
	}

	/**
	 * Return internal plugin options, these are options set by the plugin
	 *
	 * @param bool $set_defaults true if we set default options.
	 * @return array $options
	 */
	public function internal_options( $set_defaults = true ) {
		if ( ! $set_defaults ) {
			return get_option( AAP_PLUGIN_NAME . '-internal', array() );
		}

		if ( ! isset( $this->internal_options ) ) {
			$defaults               = array(
				'version' => AAP_VERSION,
			);
			$this->internal_options = get_option( AAP_PLUGIN_NAME . '-internal', array() );
			// Save defaults.
			if ( $this->internal_options === array() ) {
				$this->internal_options = $defaults;
				$this->update_internal_options( $this->internal_options );
			}
		}

		return $this->internal_options;
	}

	/**
	 * Update internal plugin options
	 *
	 * @param array $options new internal options.
	 */
	public function update_internal_options( array $options ) {
		$this->internal_options = $options;
		update_option( AAP_PLUGIN_NAME . '-internal', $options );
	}

	/**
	 * Update capabilities and warn user if needed
	 */
	public function maybe_update_capabilities() {
		$internal_options = $this->internal_options( false );
		if ( ! isset( $internal_options['version'] ) ) {
			$roles = array( 'advanced_ads_admin', 'advanced_ads_manager' );
			// Add notice if there is at least 1 user with that role.
			foreach ( $roles as $role ) {
				$users_query = new WP_User_Query(
					array(
						'fields' => 'ID',
						'number' => 1,
						'role'   => $role,
					)
				);
				if ( count( $users_query->get_results() ) ) {
					Advanced_Ads_Admin_Notices::get_instance()->add_to_queue( 'pro_changed_caps' );
					break;
				}
			}

			$admin_role = get_role( 'advanced_ads_admin' );
			if ( $admin_role ) {
				$admin_role->add_cap( 'upload_files' );
				$admin_role->add_cap( 'unfiltered_html' );
			}
			$manager_role = get_role( 'advanced_ads_manager' );
			if ( $manager_role ) {
				$manager_role->add_cap( 'upload_files' );
				$manager_role->add_cap( 'unfiltered_html' );
			}

			// Save new version.
			$this->internal_options();
		}
	}

	/**
	 * Add potential warning to global array of notices.
	 *
	 * @param array $notices existing notices.
	 *
	 * @return mixed
	 */
	public function add_notices( $notices ) {
		$notices['pro_changed_caps'] = array(
			'type'   => 'update',
			'text'   => __( 'Please note, the “Ad Admin“ and the “Ad Manager“ roles have the “upload_files“ and the “unfiltered_html“ capabilities', 'advanced-ads-pro' ),
			'global' => true,
		);
		return $notices;
	}

	/**
	 * Check if the ad can be displayed based on display limit
	 *
	 * @param bool            $can_display existing value.
	 * @param Advanced_Ads_Ad $ad Advanced_Ads_Ad object.
	 * @param array           $check_options tbd.
	 * @return bool true if limit is not reached, false otherwise
	 */
	public function can_display_by_display_limit( $can_display, Advanced_Ads_Ad $ad, $check_options ) {
		if ( ! $can_display ) {
			return false;
		}

		$output_options = $ad->options( 'output' );

		if ( empty( $check_options['passive_cache_busting'] ) && ! empty( $output_options['once_per_page'] ) ) {
			$current_ads = Advanced_Ads::get_instance()->current_ads;

			foreach ( $current_ads as $item ) {
				if ( $item['type'] === 'ad' && absint( $item['id'] ) === $ad->id ) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Get offsets for Content placement.
	 *
	 * @param array  $offsets Existing Offsets.
	 * @param array  $options Injection options.
	 * @param array  $placement_opts Placement options.
	 * @param object $xpath DOMXpath object.
	 * @param array  $items Selected items.
	 * @param object $dom DOMDocument object.
	 * @return array $offsets New offsets.
	 */
	public function placement_content_offsets( $offsets, $options, $placement_opts, $xpath = null, $items = null, $dom = null ) {
		if ( ! isset( $options['paragraph_count'] ) ) {
			return $offsets;
		}

		if ( isset( $placement_opts['placement']['type'] ) ) {
			if ( 'post_content_random' === $placement_opts['placement']['type'] ) {
				$max = absint( $options['paragraph_count'] - 1 );
				// Skip if have only one paragraph since `wp_rand( 0, 0)` generates large number.
				if ( $max > 0 ) {
					$rand    = wp_rand( 0, $max );
					$offsets = array( $rand );
				}
			}

			if ( 'post_content_middle' === $placement_opts['placement']['type'] ) {
				$middle  = absint( ( $options['paragraph_count'] - 1 ) / 2 );
				$offsets = array( $middle );
			}
		}

		// "Content" placement, repeat position.
		if ( ! empty( $placement_opts['repeat'] )
			&& isset( $options['paragraph_id'] )
			&& isset( $options['paragraph_select_from_bottom'] ) ) {

			$offsets = array();
			for ( $i = $options['paragraph_id'] - 1; $i < $options['paragraph_count']; $i++ ) {
				// Select every X number.
				if ( ( $i + 1 ) % $options['paragraph_id'] === 0 ) {
					$offsets[] = $options['paragraph_select_from_bottom'] ? $options['paragraph_count'] - 1 - $i : $i;
				}
			}
		}

		if ( ! empty( $placement_opts['words_between_repeats'] )
			&& $xpath && $items && $dom ) {
			$options['words_between_repeats'] = absint( $placement_opts['words_between_repeats'] );

			$offset_shifter = new Advanced_Ads_Pro_Offset_Shifter( $dom, $xpath, $options );
			$offsets        = $offset_shifter->calc_offsets( $offsets, $items );
		}

		return $offsets;
	}

	/**
	 * Add custom code after the ad.
	 *
	 * Note: this won’t work for the Background ad placement. There is a custom solution for that in Advanced_Ads_Pro_Module_Background_Ads:ad_output
	 *
	 * @param string          $ad_content Ad content.
	 * @param Advanced_Ads_Ad $ad ad object.
	 * @return string $ad_content Ad content.
	 */
	public function add_custom_code( $ad_content, Advanced_Ads_Ad $ad ) {
		$custom_code = $this->get_custom_code($ad);

		if ( ! empty( $custom_code ) ) {
			return $ad_content . $custom_code;
		}
		return $ad_content;
	}

	/**
	 * If this ad has custom code, encode the output.
	 *
	 * @param string          $output The output string.
	 * @param Advanced_Ads_Ad $ad     The ad object.
	 *
	 * @return string
	 */
	public function encode_ad_custom_code( $output, Advanced_Ads_Ad $ad ) {
		$privacy = Advanced_Ads_Privacy::get_instance();
		if (
			// don't encode if AMP.
			( function_exists( 'advads_is_amp' ) && advads_is_amp() )
			// privacy module is either not enabled, or shows all ads without consent.
			|| ( empty( $privacy->options()['enabled'] ) )
			// Ad is already encoded.
			|| ( ! method_exists( $privacy, 'is_ad_output_encoded' ) || $privacy->is_ad_output_encoded( $output ) )
			// Consent is overridden, and this is not an AdSense ad, don't encode it.
			|| ( $ad->type !== 'adsense' && isset( $ad->options()['privacy']['ignore-consent'] ) )
		) {
			return $output;
		}

		// If we have custom code, encode the ad.
		if ( ! empty( $this->get_custom_code( $ad ) ) ) {
			$output = $privacy->encode_ad( $output, $ad );
		}

		return $output;
	}

	/**
	 * Get the custom code for this ad.
	 *
	 * @param Advanced_Ads_Ad $ad The ad object.
	 *
	 * @return string
	 */
	public function get_custom_code( Advanced_Ads_Ad $ad ) {
		$options     = $ad->options( 'output' );
		$custom_code = isset( $options['custom-code'] ) ? $options['custom-code'] : '';

		return (string) apply_filters( 'advanced_ads_pro_output_custom_code', $custom_code, $ad );
	}

	/**
	 * Enable placement test emails
	 */
	public static function enable_placement_test_emails() {
		// Only schedule if not yet scheduled.
		if ( ! wp_next_scheduled( 'advanced-ads-placement-tests-emails' ) ) {
			wp_schedule_event( time(), 'daily', 'advanced-ads-placement-tests-emails' );
		}
	}

	/**
	 * Disable placement test emails
	 */
	public static function disable_placement_test_emails() {
		wp_clear_scheduled_hook( 'advanced-ads-placement-tests-emails' );
	}

	/**
	 * Shortcode to include ad in frontend
	 *
	 * @param array $atts shortcode attributes.
	 * @return string content as generated by the shortcode.
	 */
	public function shortcode_display_ad( $atts ) {
		return $this->do_shortcode( $atts, 'shortcode_display_ad' );
	}

	/**
	 * Shortcode to include ad from an ad group in frontend
	 *
	 * @param array $atts shortcode attributes.
	 * @return string content as generated by the shortcode.
	 */
	public function shortcode_display_ad_group( $atts ) {
		return $this->do_shortcode( $atts, 'shortcode_display_ad_group' );
	}

	/**
	 * Shortcode to display content of an ad placement in frontend
	 *
	 * @param array $atts shortcode attributes.
	 * @return string content as generated by the shortcode.
	 */
	public function shortcode_display_ad_placement( $atts ) {
		return $this->do_shortcode( $atts, 'shortcode_display_ad_placement' );
	}

	/**
	 * Create shortcode output.
	 *
	 * @param array  $atts shortcode attributes.
	 * @param string $function_name function to be executed by the shortcode.
	 *
	 * @return string content as generated by the shortcode.
	 */
	private function do_shortcode( $atts, $function_name ) {
		$blog_id = isset( $atts['blog_id'] ) ? absint( $atts['blog_id'] ) : 0;

		if ( $blog_id && $blog_id !== get_current_blog_id() && is_multisite() ) {
			// Prevent database error.
			if ( ! Advanced_Ads_Pro_Utils::blog_exists( $blog_id ) ) {
				return ''; }

			if ( method_exists( Advanced_Ads::get_instance(), 'switch_to_blog' ) ) {
				Advanced_Ads::get_instance()->switch_to_blog( $blog_id );
			}

			// Use the public available function here.
			$result = call_user_func( array( Advanced_Ads_Plugin::get_instance(), $function_name ), $atts );

			if ( method_exists( Advanced_Ads::get_instance(), 'restore_current_blog' ) ) {
				Advanced_Ads::get_instance()->restore_current_blog();
			}
			return $result;
		}

		// Use the public available function here.
		return call_user_func( array( Advanced_Ads_Plugin::get_instance(), $function_name ), $atts );
	}

}
