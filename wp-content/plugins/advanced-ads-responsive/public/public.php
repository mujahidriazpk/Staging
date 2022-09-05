<?php

class Advanced_Ads_Responsive {
	/**
	 * value for 'show tooltip' option
	 *
	 * @var bool
	 */
	private $show_tooltip;

        /**
         * holds plugin base class
         *
         * @var Advanced_Ads_Responsive_Plugin
         * @since 1.2.0
         */
        protected $plugin;

	/**
	 * can current user edit ads? – = necessary user right to see frontend helper
	 */
	protected  $can_edit_ads = false;

        /**
         * Initialize the plugin
         * and styles.
         *
         * @since     1.0.0
         */
        public function __construct() {

                $this->plugin = Advanced_Ads_Responsive_Plugin::get_instance();

                // init action
                add_action( 'init', array( $this, 'init' ) );

		// register events when all plugins are loaded
		add_action( 'plugins_loaded', array( $this, 'wp_admin_plugins_loaded' ) );
        }

        /**
         * init
         *
         * @since 1.2.0
         */
    	public function init() {
                $options = $this->plugin->options();
		
		$cap = method_exists( 'Advanced_Ads_Plugin', 'user_cap' ) ?  Advanced_Ads_Plugin::user_cap( 'advanced_ads_edit_ads') : 'manage_options';
		
                if ( current_user_can( $cap ) ) {
		    $this->can_edit_ads = true;
		}

                if ( isset( $options[ AAR_SLUG ]['show-tooltip'] ) && '1' == $options[ AAR_SLUG ]['show-tooltip'] ) {
                        $this->show_tooltip = true;
                } else {
                        // If option not set, FASLE
                        $this->show_tooltip = false;
                }
		
		// adjust image sizes to responsive
		if ( isset( $options[ AAR_SLUG ]['force-responsive-images'] ) && $options[ AAR_SLUG ]['force-responsive-images'] ) {
                        add_filter( 'advanced-ads-ad-image-tag-style', array( $this, 'force_responsive_images' ) );
                }
	}

	/**
	 * load actions and filters
	 */
	public function wp_admin_plugins_loaded(){

				// Add js file to header.
				add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
				

                // add filter for condition check
                add_filter( 'advanced-ads-can-display', array( $this, 'can_display' ), 10, 2 );

                // use wrapper filter to create a wrapping <div /> ( used as height probe on the ad )
                add_filter( 'advanced-ads-set-wrapper', array( $this, 'wrapper_filter' ), 10, 2 );

                // use an output filter to create a wrapping <span /> ( used as width probe on the ad )
                add_filter( 'advanced-ads-output-inside-wrapper', array( $this, 'inside_wrapper' ), 10, 2 );
		
		// force advanced JS file
		add_filter( 'advanced-ads-activate-advanced-js', '__return_true' );

	}

	/**
	 * Add a wrapping span inside the classic wrapping div
	 *
	 * @since 1.2.0
         * @param arr $wrapper, the wrapper array
	 * @parav obj $ad, the ad object
	 */
	public function inside_wrapper( $output, $ad ) {
        if ( $this->can_edit_ads && $this->show_tooltip ) {
                $output = '<span class="advads-size-tooltip-w">' . $output . '</span>';
        }
		return $output;
	}

	/**
	 * Add a wrapper to the ad if necessary
	 *
         * @since 1.2.0
	 * @param arr $wrapper, the wrapper array
	 * @parav obj $ad, the ad object
	 */
	public function wrapper_filter( $wrapper, $ad ) {
        // add wrapper (the classic wrapping div)
        if ( $this->can_edit_ads && $this->show_tooltip ) {
                $wrapper['class'][] = 'advads-size-tooltip-h';
        }
		return $wrapper;
	}

    /**
     * append js file in footer
     *
     * @since 1.0.0
     */
    public function register_scripts(){
		if ( function_exists( 'advads_is_amp' ) && advads_is_amp() ) {
			return;
		}

	    if( defined( 'ADVADS_SLUG' ) ){
		if( ! defined( 'ADVANCED_ADS_RESPONSIVE_DISABLE_BROWSER_WIDTH' ) ){
			wp_enqueue_script( 'advanced-ads-responsive', AAR_BASE_URL . 'public/assets/js/script.js', array( 'jquery', ADVADS_SLUG . '-advanced-js' ), AAR_VERSION, true );

			$options = $this->plugin->options();
			wp_localize_script( 'advanced-ads-responsive', 'advanced_ads_responsive', array(
				'reload_on_resize' => ! empty( $options[ AAR_SLUG ]['reload-ads-on-resize'] ) ? 1 : 0
			) );
		}
	    }

	    if ( $this->can_edit_ads && $this->show_tooltip) {
                // Enqueue these files only if the user has admin capability
                wp_register_script( 'advads-responsive-tooltip-js', AAR_BASE_URL . 'public/assets/js/tooltip.js', array( 'jquery' ), null );
                $translations = array(
                    'windowWidth' => __( 'window width', 'advanced-ads-responsive' ),
                    'adSize' => __( 'ad size', 'advanced-ads-responsive' ),
                    'containerWidth' => __( 'container width', 'advanced-ads-responsive' ),
                );
                wp_localize_script( 'advads-responsive-tooltip-js', 'advadsRespLocalize', $translations );
                        wp_enqueue_script( 'advads-responsive-tooltip-js' );
                        wp_enqueue_style( 'advads-responsive-tooltip-css', AAR_BASE_URL . 'public/assets/css/tooltip.css', array(), null );
            }
    }

    /**
     * check if the current ad can be displayed based on minimal and maximum browser width
     *
     * @since 1.0.0
     * @param bool $can_display value as set so far
     * @param obj $ad the ad object
     * @return bool false if can’t be displayed, else return $can_display
     * @deprecated since version 1.2.1, use check_browser_width() instead
     */
    public function can_display( $can_display, $ad = 0 ){
	if ( ! $can_display ) {
		return false;
	}

        $ad_options = $ad->options();

        // check if by-size option is provided and option enabled
        if( ! isset( $ad_options['visitor']['by-size']['enable'] ) || !$ad_options['visitor']['by-size']['enable'] ) return $can_display;

        $browser_width = 0;
        if ( ! empty( $_COOKIE['advanced_ads_browser_width'] ) ) {
            $browser_width = absint( $_COOKIE['advanced_ads_browser_width'] );
        }

        // check default method if browser width is still empty
        if ( empty( $browser_width ) ){
            if( ! empty( $ad_options['visitor']['by-size']['fallback'] ) ){
                switch ( $ad_options['visitor']['by-size']['fallback'] ){
                    case 'display' : // display ad anyway
                        return $can_display;
                        break;
                    case 'hide' : // don’t display the ad
                        return false;
                        break;
                    case 'mobile' :
                        if( ! wp_is_mobile() ) return false;
                        break;
                    case 'desktop' :
                        if( wp_is_mobile() ) return false;
                        break;
                }
            }
        }

        // check browser width against minimum/maximum settings
        $from = absint( $ad_options['visitor']['by-size']['from'] );
        $to = absint( $ad_options['visitor']['by-size']['to'] );

        if( $from > 0 && $from > $browser_width ) return false;
        if( $to > 0 && $to < $browser_width ) return false;

        return $can_display;
    }

	/**
	 * check browser width in frontend
	 *
	 * @since 1.2.1
	 * @param arr $options options of the condition
	 * @return bool true if can be displayed
	 */
	static function check_browser_width( $options = array() ){

	    // return true if feature is disabled
	    if( defined( 'ADVANCED_ADS_RESPONSIVE_DISABLE_BROWSER_WIDTH' ) ){
		    return true;
	    }
	    
	    if ( ! isset( $options['operator'] ) || ! isset( $options['value'] ) ) {
			return true;
	    }


	    $browser_width = 0;
	    if ( ! empty( $_COOKIE['advanced_ads_browser_width'] ) ) {
		$browser_width = absint( $_COOKIE['advanced_ads_browser_width'] );
	    } else {
		$responsive_options = Advanced_Ads_Responsive_Plugin::get_instance()->options();
		$browser_width = ! empty( $responsive_options['responsive-ads']['fallback-width'] ) ? absint( $responsive_options['responsive-ads']['fallback-width'] ) : 768;
	    }

	    $value = absint( $options['value'] );

	    switch ( $options['operator'] ){
		    case 'is_equal' :
			    if ( $value !== $browser_width ) { return false; }
			    break;
		    case 'is_higher' :
			    if ( $value > $browser_width ) { return false; }
			    break;
		    case 'is_lower' :
			    if ( $value < $browser_width ) { return false; }
			    break;
	    }

	    return true;
	}
	
	/**
	 * check for tablet devices
	 * 
	 * @since 1.3
	 * @param arr $options options of the condition
	 * @return bool true if can be displayed
	 */
	static function check_tablet( $options = array() ){

	    global $advads_mobile_detect;
	    
	    if ( ! isset( $options['operator'] ) ) {
			return true;
	    }

	    switch ( $options['operator'] ){
		    case 'is' :
			    if ( ! $advads_mobile_detect->isTablet() ) { return false; }
			    break;
		    case 'is_not' :
			    if ( $advads_mobile_detect->isTablet() ) { return false; }
			    break;
	    }

	    return true;
	}
	
	/**
	 * force responsive images
	 * 
	 * @since 1.3
	 * @param str $image_styles existing image attributes
	 * $return str $image_styles new image attributes
	 */
	public function force_responsive_images( $image_styles ){
	    
	    return $image_styles . ' max-width: 100%; height: auto;';
	    
	}
}
