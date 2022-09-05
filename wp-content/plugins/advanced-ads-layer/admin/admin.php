<?php
class Advanced_Ads_Layer_Admin {

	/**
	 * stores the settings page hook
	 *
	 * @var     string
	 */
	protected $settings_page_hook = '';

	const PLUGIN_LINK = 'https://wpadvancedads.com/add-ons/popup-and-layer-ads/';

	/**
	 * holds base class
	 *
	 * @var Advanced_Ads_Layer_Plugin
	 * @since 1.2.0
	 */
	protected $plugin;

	/**
	 * @var bool
	 * @since 1.3
	 */
	protected $fancybox_is_enabled;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->plugin = Advanced_Ads_Layer_Plugin::get_instance();

		add_action( 'plugins_loaded', array( $this, 'wp_admin_plugins_loaded' ) );
	}

	/**
	 * load actions and filters
	 */
	public function wp_admin_plugins_loaded() {
		if ( ! class_exists( 'Advanced_Ads_Admin', false ) ) {
			// show admin notice
			add_action( 'admin_notices', array( $this, 'missing_plugin_notice' ) );

			return;
		}

		$advads_options = $this->plugin->options();
		$this->fancybox_is_enabled = isset( $advads_options['layer']['use-fancybox'] ) ? $advads_options['layer']['use-fancybox'] : 0;

		// add notice to Ad Health notices in backend.
		add_filter( 'advanced-ads-ad-health-notices', array( $this, 'add_ad_health_notices' ) );
		// add notice if more than one fancybox is used.
		add_action( 'advanced-ads-admin-notices', array( $this, 'more_than_one_fancyboxes_notice' ) );
		// add metabox.
		add_action( 'admin_init', array( $this, 'add_meta_box' ) );
		// register settings.
		add_action( 'advanced-ads-settings-init', array( $this, 'settings_init' ) );
		// add our new options using the options filter before saving.
		add_filter( 'advanced-ads-save-options', array( $this, 'save_options' ), 10, 2 );
		// add admin scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		// content of layer placement.
		add_action( 'advanced-ads-placement-options-after-advanced', array( $this, 'layer_placement_content' ), 10, 2 );
		// add AdSense warning.
		add_action( 'advanced-ads-placement-options-after', array( $this, 'add_adsense_warning' ), 5, 2 );
	}

	/**
	 * show warning if Advanced Ads js is not activated
	 */
	public function missing_plugin_notice() {
		$plugins = get_plugins();
		if( isset( $plugins['advanced-ads/advanced-ads.php'] ) ){ // is installed, but not active
			$link = '<a class="button button-primary" href="' . wp_nonce_url( 'plugins.php?action=activate&amp;plugin=advanced-ads/advanced-ads.php&amp', 'activate-plugin_advanced-ads/advanced-ads.php' ) . '">'. __('Activate Now', 'advanced-ads-layer') .'</a>';
		} else {
			$link = '<a class="button button-primary" href="' . wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . 'advanced-ads'), 'install-plugin_' . 'advanced-ads') . '">'. __('Install Now', 'advanced-ads-layer') .'</a>';
		}
		echo '<div class="error"><p>' . sprintf(__('<strong>%s</strong> requires the <strong><a href="https://wpadvancedads.com/#utm_source=advanced-ads&utm_medium=link&utm_campaign=activate-genesis" target="_blank">Advanced Ads</a></strong> plugin to be installed and activated on your site.', 'advanced-ads-layer'), 'Advanced Ads – PopUp and Layer Ads') 
			. '&nbsp;' . $link . '</p></div>';
	}

	/**
	 * Register warning to Ad Health notices in the backend
	 *
	 * @param	array 	$notices
	 * @return	array	extended notices
	 */
	public function add_ad_health_notices( $notices ) {
		$notices[ 'popup_multiple_fancyboxes' ] = array(
				'text' => __( 'You shouldn’t have more than one Fancybox on the same page. Please, make sure that you set up the ads for the different layers with display conditions to prevent them from showing up on the same page', 'advanced-ads-layer' )
		);

		return $notices;
	}

	/**
	 * show warning if Fancybox is enabled and more than one layer placement exists
	 */
	public function more_than_one_fancyboxes_notice() {
		$placements = Advanced_Ads::get_ad_placements_array();
		$layers_count = 0;
		foreach ( $placements as $placement ) {
			if ( $placement['type'] == 'layer' ) {
				$layers_count++;
			}
		}

		/**
		 * With Advanced Ads 1.12 we register a notice instead of showing it on all pages
		 * registering this with the new `advanced-ads-admin-notices` hooks makes sure that Advanced_Ads_Ad_Health_Notices is also available
		 */
		if ( $this->fancybox_is_enabled && $layers_count > 1 ) {
			Advanced_Ads_Ad_Health_Notices::get_instance()->add( 'popup_multiple_fancyboxes' );
		} else {
			Advanced_Ads_Ad_Health_Notices::get_instance()->remove( 'popup_multiple_fancyboxes' );
		}
	}

	/**
	 * add layer placement styles
	 *
	 * @since 1.2.4
	 * @param type $hook_suffix
	 */
	function admin_scripts( $hook_suffix ) {
		if ( ! class_exists( 'Advanced_Ads_Admin' ) ) {
			return;
		};

		if ( Advanced_Ads_Admin::screen_belongs_to_advanced_ads() ) {
			wp_enqueue_style( 'advanced-ads-layer-admin-css', AAPLDS_BASE_URL . 'admin/assets/css/admin.css', array(), AAPLDS_VERSION );
			wp_add_inline_style( 'advanced-ads-layer-admin-css', self::get_custom_css() );
		}
	}
	
	/**
	 * creates the css containing the layer placement styles
	 *
	 * @since 1.6.3
	 */
	static final function get_custom_css(){
		$layer_class = Advanced_Ads_Layer::get_layer_class();
		$css = ".$layer_class-aa-position div.clear { content: ' '; display: block; float: none; clear: both; }\n";
		$css.= ".advads-placements-table .$layer_class-aa-position .advads-sticky-assistant table tbody tr td { width: 3em; height: 2em; text-align: center; vertical-align: middle; padding: 0; }\n";
		return $css;
	}

	/**
	 * add settings to settings page
	 *
	 * @since 1.2.0
	 */
	public function settings_init() {

		// don’t initiate if main plugin not loaded
		if ( ! class_exists( 'Advanced_Ads_Admin' ) ) {
			return;
		}

		// get settings page hook
		$admin = Advanced_Ads_Admin::get_instance();
		$hook = $admin->plugin_screen_hook_suffix;
		$this->settings_page_hook = $hook;

		// add license key field to license section
		add_settings_field(
			'layer-license',
			__( 'PopUp and Layer Ads', 'advanced-ads-layer' ),
			array( $this, 'render_settings_license_callback' ),
			'advanced-ads-settings-license-page',
			'advanced_ads_settings_license_section'
		);

		// add new section
		add_settings_section(
			'advanced_ads_layer_setting_section',
			'PopUp and Layer Ads',
			array( $this, 'render_settings_section_callback' ),
			$hook
		);

		// add setting fields
		add_settings_field(
			'use-fancybox',
			__( 'Use Fancybox plugin', 'advanced-ads-layer' ),
			array( $this, 'render_settings_fancybox_callback' ),
			$hook,
			'advanced_ads_layer_setting_section'
		);

	}

	/**
	 * render license key section
	 *
	 * @since 1.2.0
	 */
	public function render_settings_license_callback() {
		$licenses = get_option(ADVADS_SLUG . '-licenses', array());
		$license_key = isset( $licenses['layer'] ) ? $licenses['layer'] : '';
		$license_status = get_option( $this->plugin->options_slug . '-license-status', false );
		$index = 'layer';
		$plugin_name = AAPLDS_PLUGIN_NAME;
		$options_slug = $this->plugin->options_slug;
		$plugin_url = self::PLUGIN_LINK;

		// template in main plugin
		include ADVADS_BASE_PATH . 'admin/views/setting-license.php';
	}

	/**
	 * render fancybox setting
	 *
	 */
	public function render_settings_section_callback() {
	}

	/**
	 * render fancybox setting
	 *
	 */
	public function render_settings_fancybox_callback() {
		echo '<input name="' . ADVADS_SLUG . '[layer][use-fancybox]" id="advanced-ads-layer-use-fancybox" type="checkbox" value="1" ' . checked( 1, $this->fancybox_is_enabled, false ) . ' />';
		echo '<p class="description">'. __( 'Activate this if you want to use Fancybox plugin for popup windows', 'advanced-ads-layer' ) .'</p>';
	}

	/**
	 * add own meta box for the ad parameters
	 *
	 * @since 1.0.0
	 */
	public function add_meta_box() {
		if ( ! class_exists( 'Advanced_Ads' ) ) {
			return;
		}

		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
		if ( ! $post_id ) {
			return;
		}
		$ad = new Advanced_Ads_Ad( $post_id );
		$options = $ad->options();
		$enabled = isset( $options['layer']['enabled'] ) ? $options['layer']['enabled'] : false;

		// return if not enabled, because this is deprecated
		if ( ! $enabled ) {
			return;
		}

		if ( ! class_exists( 'Advanced_Ads' ) ) return;
		add_meta_box(
			'ad-layer-ads-box', __( 'Layer and PopUp effects', 'advanced-ads-layer' ), array( $this, 'render_metabox' ), Advanced_Ads::POST_TYPE_SLUG, 'normal', 'low'
		);
	}

	/**
	 * render options for ad parameters
	 *
	 * @since 1.0.0
	 */
	public function render_metabox() {
		global $post;
		$ad = new Advanced_Ads_Ad( $post->ID );
		$options = $ad->options();
		// set options
		$enabled               = isset( $options['layer']['enabled'] ) ? $options['layer']['enabled'] : false;
		$trigger               = isset( $options['layer']['trigger'] ) ? $options['layer']['trigger'] : '';
		$offset                = isset( $options['layer']['offset'] ) ? absint( $options['layer']['offset'] ) : 0;
		$background            = isset( $options['layer']['background'] ) ? absint( $options['layer']['background'] ) : 0;
		$close_enabled         = isset( $options['layer']['close']['enabled'] ) ? $options['layer']['close']['enabled'] : 0;
		$close_where           = isset( $options['layer']['close']['where'] ) ? $options['layer']['close']['where'] : 'inside';
		$close_side            = isset( $options['layer']['close']['side'] ) ? $options['layer']['close']['side'] : 'right';
		$close_timeout_enabled = isset( $options['layer']['close']['timeout_enabled'] ) ? $options['layer']['close']['timeout_enabled'] : false;
		$close_timeout         = isset( $options['layer']['close']['timeout'] ) ? absint( $options['layer']['close']['timeout'] ) : 0;
		$effect                = isset( $options['layer']['effect'] ) ? $options['layer']['effect'] : 'show';
		$duration              = isset( $options['layer']['duration'] ) ? absint( $options['layer']['duration'] ) : 0;

		$layer_class = Advanced_Ads_Layer::get_layer_class();
		require_once( 'views/metabox.php' );
	}

	/**
	 * save options
	 *
	 * @since 1.0.0
	 */
	public function save_options( $options = array(), $ad = 0 ) {
		// sanitize sticky options
		$positions = array();

		$options['layer']['enabled']                  = ( ! empty( $_POST['advanced_ad']['layer']['enabled'] ) ) ? absint( $_POST['advanced_ad']['layer']['enabled'] ) : 0;
		$options['layer']['trigger']                  = ( ! empty( $_POST['advanced_ad']['layer']['trigger'] ) ) ? $_POST['advanced_ad']['layer']['trigger'] : '';
		$options['layer']['offset']                   = ( ! empty( $_POST['advanced_ad']['layer']['offset'] ) ) ? absint( $_POST['advanced_ad']['layer']['offset'] ) : '';
		$options['layer']['background']               = ( ! empty( $_POST['advanced_ad']['layer']['background'] ) ) ? absint( $_POST['advanced_ad']['layer']['background']) : '';
		$options['layer']['close']['enabled']         = ( ! empty( $_POST['advanced_ad']['layer']['close']['enabled'] ) ) ? absint( $_POST['advanced_ad']['layer']['close']['enabled'] ) : '';
		$options['layer']['close']['where']           = ( ! empty( $_POST['advanced_ad']['layer']['close']['where'] ) ) ? $_POST['advanced_ad']['layer']['close']['where'] : '';
		$options['layer']['close']['side']            = ( ! empty( $_POST['advanced_ad']['layer']['close']['side'] ) ) ? $_POST['advanced_ad']['layer']['close']['side'] : '';
		$options['layer']['close']['timeout_enabled'] = ( ! empty( $_POST['advanced_ad']['layer']['close']['timeout_enabled'] ) ) ? $_POST['advanced_ad']['layer']['close']['timeout_enabled'] : false;
		$options['layer']['close']['timeout']         = ( ! empty( $_POST['advanced_ad']['layer']['close']['timeout'] ) ) ? absint( $_POST['advanced_ad']['layer']['close']['timeout'] ) : 0;
		$options['layer']['effect']                   = ( ! empty( $_POST['advanced_ad']['layer']['effect'] ) ) ? $_POST['advanced_ad']['layer']['effect'] : 'show';
		$options['layer']['duration']                 = ( ! empty( $_POST['advanced_ad']['layer']['duration'] ) ) ? absint( $_POST['advanced_ad']['layer']['duration'] ) : 0;

		return $options;
	}

	/**
	 * render layer placement content
	 *
	 * @since 1.2.4
	 * @param string $placement_slug id of the placement
	 *
	 */
	public function layer_placement_content( $placement_slug, $placement ) {
		switch ( $placement['type'] ) {
			case 'layer' :
			    
				if( ! class_exists( 'Advanced_Ads_Admin_Options' ) ){
					echo 'Please update to Advanced Ads 1.8';
					return;
				}
			    
				$options = isset( $placement['options']['layer_placement'] ) ? $placement['options']['layer_placement'] : array();
				$option_name = "advads[placements][$placement_slug][options][layer_placement]";
			    
				// trigger
				$trigger    = isset( $options['trigger'] ) ? $options['trigger'] : '';
				$offset     = isset( $options['offset'] ) ? absint( $options['offset'] ) : 0;
				$delay_sec  = isset( $options['delay_sec'] ) ? absint( $options['delay_sec'] ) : 0;

				ob_start();
				include AAPLDS_BASE_PATH . '/admin/views/trigger.php'; 
				$option_content = ob_get_clean();
				
				Advanced_Ads_Admin_Options::render_option( 
					'placement-layer-trigger', 
					__( 'show the ad', 'advanced-ads-layer' ),
					$option_content );
				
				// effect
				$effect     = isset( $options['effect'] ) ? $options['effect'] : 'show';
				$duration   = isset( $options['duration'] ) ? absint( $options['duration'] ) : 0;

				ob_start();
				include AAPLDS_BASE_PATH . '/admin/views/effects.php'; 
				$option_content = ob_get_clean();
				
				Advanced_Ads_Admin_Options::render_option( 
					'placement-layer-effect', 
					__( 'effect', 'advanced-ads-layer' ),
					$option_content );				
				
				// background
				$background = isset( $options['background'] ) ? absint( $options['background'] ) : 0;
				$background_click_close = $background && ! empty( $options['background_click_close'] );

				ob_start();
				include AAPLDS_BASE_PATH . '/admin/views/background.php';
				$option_content = ob_get_clean();
				
				Advanced_Ads_Admin_Options::render_option( 
					'placement-layer-background', 
					__( 'background', 'advanced-ads-layer' ),
					$option_content );

				// auto close
				ob_start();
				include AAPLDS_BASE_PATH . '/admin/views/auto_close.php';
				$option_content = ob_get_clean();

				Advanced_Ads_Admin_Options::render_option(
					'placement-layer-auto-close',
					__( 'auto close', 'advanced-ads-layer' ),
					$option_content );

				// close button
				ob_start();
				include AAPLDS_BASE_PATH . '/admin/views/close-button.php'; 
				$option_content = ob_get_clean();
				
				Advanced_Ads_Admin_Options::render_option( 
					'placement-layer-trigger', 
					__( 'close button', 'advanced-ads-layer' ),
					$option_content );
				
				// position on the screen
				ob_start();
				include AAPLDS_BASE_PATH . '/admin/views/position.php'; 
				$option_content = ob_get_clean();
				
				Advanced_Ads_Admin_Options::render_option( 
					'placement-layer-trigger', 
					__( 'Position', 'advanced-ads-layer' ),
					$option_content );
				
				// dimension of the layer
				$width = isset( $placement['options']['placement_width'] ) ? absint( $placement['options']['placement_width'] ) : 0;
				$height = isset( $placement['options']['placement_height'] ) ? absint( $placement['options']['placement_height'] ) : 0;

				ob_start();
				include AAPLDS_BASE_PATH . '/admin/views/size.php';
				$option_content = ob_get_clean();
				
				Advanced_Ads_Admin_Options::render_option( 
					'placement-layer-dimensions', 
					__( 'size', 'advanced-ads-layer' ),
					$option_content );
			break;
		}
	}

	/**
	 * Add a warning when an AdSense ad is assigned to the layer placement.
	 *
	 * @param string $_placement_slug
	 * @param array $_placement
	 */
	public function add_adsense_warning( $_placement_slug, $_placement ) {
		if ( 'layer' !== $_placement['type'] || empty( $_placement['item'] ) ) {
			return;
		}

		if ( ! class_exists( 'Advanced_Ads_Utils' ) || ! method_exists( 'Advanced_Ads_Utils', 'get_nested_ads' ) ) {
			return;
		}

		foreach ( Advanced_Ads_Utils::get_nested_ads( $_placement_slug, 'placement' ) as $ad ) {
			if ( $ad->type === 'adsense' ) { ?>
				<p class="advads-error-message"><?php
				_e( 'It is against the AdSense policy to use their ads in popups.', 'advanced-ads-layer' ); ?></p>
				<?php return;
			}
		}
	}
}
