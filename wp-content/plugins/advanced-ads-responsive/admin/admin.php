<?php
class Advanced_Ads_Responsive_Admin {

    /**
     * stores the settings page hook
     *
     * @since   1.0.0
     * @var     string
     */
    protected $settings_page_hook = '';

    /**
     * link to plugin page
     *
     * @since	1.3
     * @const
     */
    const PLUGIN_LINK = 'https://wpadvancedads.com/add-ons/responsive-ads/';

    /**
     * holds base class
     *
     * @var Advanced_Ads_Responsive_Plugin
     * @since 1.2.0
     */
    protected $plugin;

    /**
     * Initialize the plugin by loading admin scripts & styles and adding a
     * settings page and menu.
     *
     * @since     1.0.0
     */
    public function __construct() {

        $this->plugin = Advanced_Ads_Responsive_Plugin::get_instance();

	add_action( 'plugins_loaded', array( $this, 'wp_admin_plugins_loaded' ) );
    }

	/**
	 * load actions and filters
	 */
	public function wp_admin_plugins_loaded(){

		if( ! class_exists( 'Advanced_Ads_Admin', false ) ) {
			// show admin notice
			add_action( 'admin_notices', array( $this, 'missing_plugin_notice' ) );

			return;
		}

		// add options to ad parameters
		add_action( 'advanced-ads-visitor-conditions-after', array( $this, 'render_ad_parameters' ), 10, 2 );

		add_action('advanced-ads-settings-init', array($this, 'settings_init'), 10, 1);
		// add list page
		add_action('admin_menu', array($this, 'add_list_page'));
	}

	/**
	 * show warning if Advanced Ads js is not activated
	 */
	public function missing_plugin_notice(){
		$plugins = get_plugins();
		if( isset( $plugins['advanced-ads/advanced-ads.php'] ) ){ // is installed, but not active
			$link = '<a class="button button-primary" href="' . wp_nonce_url( 'plugins.php?action=activate&amp;plugin=advanced-ads/advanced-ads.php&amp', 'activate-plugin_advanced-ads/advanced-ads.php' ) . '">'. __('Activate Now', 'advanced-ads-responsive') .'</a>';
		} else {
			$link = '<a class="button button-primary" href="' . wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . 'advanced-ads'), 'install-plugin_' . 'advanced-ads') . '">'. __('Install Now', 'advanced-ads-responsive') .'</a>';
		}
		echo '
		<div class="error">
		  <p>'.sprintf(__('<strong>%s</strong> requires the <strong><a href="https://wpadvancedads.com" target="_blank">Advanced Ads</a></strong> plugin to be installed and activated on your site.', 'advanced-ads-responsive'), 'Advanced Ads Responsive') .
		     '&nbsp;' . $link . '</p></div>';
	}

    /**
     * render license key section
     *
     * @since 1.2.0
     */
    public function render_settings_license_callback(){
		$licenses = get_option(ADVADS_SLUG . '-licenses', array());
		$license_key = isset($licenses['responsive']) ? $licenses['responsive'] : '';
		$license_status = get_option($this->plugin->options_slug . '-license-status', false);
		$index = 'responsive';
		$plugin_name = AAR_PLUGIN_NAME;
		$options_slug = $this->plugin->options_slug;
		$plugin_url = self::PLUGIN_LINK;

		// template in main plugin
		include ADVADS_BASE_PATH . 'admin/views/setting-license.php';
    }

    /**
     * add settings to settings page
     *
     * @param string $hook settings page hook
     */
    public function settings_init( $hook ) {

        // don’t initiate if main plugin not loaded
        if ( ! class_exists( 'Advanced_Ads_Admin' ) ) return;

        // add license key field to license section
        add_settings_field(
            'responsive-license',
            __('Responsive', 'advanced-ads-responsive'),
            array($this, 'render_settings_license_callback'),
            'advanced-ads-settings-license-page',
            'advanced_ads_settings_license_section'
        );

        // add new section
         add_settings_section(
            'advanced_ads_responsive_setting_section',
            __( 'Responsive Ads', 'advanced-ads-responsive' ),
            array( $this, 'render_settings_section_callback' ),
            $hook
         );

        // add assistant setting field
         add_settings_field(
            'tooltip-everyad',
            __( 'Activate size assistant', 'advanced-ads-responsive' ),
            array( $this, 'render_settings_tooltip_option_callback' ),
            $hook,
            'advanced_ads_responsive_setting_section'
         );
	 
        // add responsive images setting field
         add_settings_field(
            'responsive-images',
            __( 'Responsive Image Ads', 'advanced-ads-responsive' ),
            array( $this, 'render_settings_responsive_image_ads_option_callback' ),
            $hook,
            'advanced_ads_responsive_setting_section'
         );

		// add reload ads on resize setting field
		add_settings_field(
			'reload-ads-on-resize',
			__( 'Reload ads on resize', 'advanced-ads-responsive' ),
			array( $this, 'render_settings_reload_ads_on_resize_option_callback' ),
			$hook,
			'advanced_ads_responsive_setting_section'
		);
		 add_settings_field(
			 'fallback-width',
			 __( 'Fallback width', 'advanced-ads-responsive' ),
			 array( $this, 'render_settings_fallback_width_option_callback' ),
			 $hook,
			 'advanced_ads_responsive_setting_section'
		 );
    }

	/**
	 * Render settings section
	 */
	public function render_settings_section_callback() {
		return;
	}

	/**
	 * Render tooltip_option settings field
	 */
	public function render_settings_tooltip_option_callback() {
		$options = $this->plugin->options();
		$show_tooltip = ( isset( $options[ AAR_SLUG ]['show-tooltip'] ) && '1' == $options[ AAR_SLUG ]['show-tooltip'] )? true : false;
		require AAR_BASE_PATH . 'admin/views/setting_tooltip.php';
	}

	/**
	 * Render responsive image settings field
	 */
	public function render_settings_responsive_image_ads_option_callback() {
		$options = $this->plugin->options();
		$force_responsive = ( isset( $options[ AAR_SLUG ]['force-responsive-images'] ) ) ? true : false;
		require AAR_BASE_PATH . 'admin/views/setting_responsive_images.php';
	}

	/**
	 * Render setting to reload ads when screen resizes.
	 */
	public function render_settings_reload_ads_on_resize_option_callback() {
		$options = $this->plugin->options();
		$enabled = defined( 'AAP_VERSION' );
		$checked = ! empty( $options[ AAR_SLUG ]['reload-ads-on-resize'] );
		require AAR_BASE_PATH . 'admin/views/setting_reload_ads.php';
	}

	/**
	 * Render setting to set fallback width.
	 */
	public function render_settings_fallback_width_option_callback() {
		$options = $this->plugin->options();
		$width = ! empty( $options[ AAR_SLUG ]['fallback-width'] ) ? absint( $options[ AAR_SLUG ]['fallback-width'] ) : 768;
		require AAR_BASE_PATH . 'admin/views/setting_fallback_width.php';
	}

    /**
     * render options for ad parameters
     *
     * @since 1.0.0
     * @deprecated since version 1.2.1 replaced with visitor conditions api
     */
    public function render_ad_parameters( $ad = 0, $types = 0 ){


        $by_size_options = $ad->options( 'visitor' );
        $by_size_enable = 0;
        $by_size_from = 0;
        $by_size_to = 0;
        $by_size_fallback = 'display';

        if ( isset( $by_size_options['by-size'] ) ) {
            $by_size_enable = isset( $by_size_options['by-size']['enable'] ) ? absint( $by_size_options['by-size']['enable'] ) : 0;
            $by_size_from = absint( $by_size_options['by-size']['from'] );
            $by_size_to = absint( $by_size_options['by-size']['to'] );
        }
        if ( isset( $by_size_options['by-size']['fallback'] ) ) {
            $by_size_fallback = $by_size_options['by-size']['fallback'];
        }

	if( ! $by_size_enable ) {
	    return;
	}

        require_once( 'views/metabox.php' );
    }

    /**
     * add ads list page
     *
     * @since 1.1.1
     */
    public function add_list_page(){
	
	$cap = method_exists( 'Advanced_Ads_Plugin', 'user_cap' ) ?  Advanced_Ads_Plugin::user_cap( 'advanced_ads_edit_ads') : 'manage_options';
	
        add_submenu_page(
            null, __( 'Responsive Ads', 'advanced-ads-responsive' ), __( 'Responsive Ads', 'advanced-ads-responsive' ), $cap, AAR_SLUG . '-list', array( $this, 'display_responsive_ads_list' )
         );
    }

    /**
     * Render the responsive ads list page
     *
     * @since    1.0.0
     */
    public function display_responsive_ads_list() {
        if( ! class_exists( 'Advanced_Ads' ) ) return array();
        // get all ads with responsive settings
        $advads = Advanced_Ads::get_instance();

        // initiate variables
        $sorted_ads = array();
        $widths = array( 0 );
        $groups = array();

        // order ads by group and with ad id
        $ads = $advads->get_ads();

        // iterate through ads and get the responsive settings
        foreach( $ads as $_key => $_ad ){
            // get ad options
            $_ad->ad_options = $_ad->advanced_ads_ad_options;

            // put responsive options into widths array
            if( isset( $_ad->ad_options['visitors'] ) ){
		// iterate through visitor conditions
		foreach( $_ad->ad_options['visitors'] as $_condition ){
		    if( 'device_width' === $_condition['type'] ) {
			switch( $_condition['operator'] ){
			    case 'is_higher' :
				$widths[] = absint( $_condition['value'] );
				break;
			    case 'is_lower' :
				$widths[] = absint( $_condition['value'] ) + 1;
				break;
			    default :
				$widths[] = absint( $_condition['value'] );
			}
		    }
		}
	    }

	    // get categories
            $ad_groups = get_the_terms( $_ad->ID, Advanced_Ads::AD_GROUP_TAXONOMY );

	    $unsorted_ads = array();
            if( ! $ad_groups ){
                $unsorted_ads[$_ad->ID] = $_ad;
            } else {
                foreach ( $ad_groups as $_group ) {
                    $sorted_ads[$_group->term_id][$_ad->ID] = $_ad;
                    $groups[$_group->term_id] = $_group;
                }
            }
        }

        $sorted_ads['unsorted'] = $unsorted_ads;

        // order values
        sort( $widths );
        // remove duplicates, rebase keys and exchange keys with values
        $widths = array_flip( array_values( array_unique( $widths ) ) );
        $max_columns = count( $widths );

        include_once( 'views/list.php' );
    }
}
