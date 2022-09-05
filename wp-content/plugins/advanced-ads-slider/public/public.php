<?php

class Advanced_Ads_Slider {

        /**
         * holds plugin base class
         *
         * @var Advanced_Ads_Slider_Plugin
         * @since 1.0.0
         */
        protected $plugin;

        /**
         * Initialize the plugin
         * and styles.
         *
         * @since     1.0.0
         */
        public function __construct() {

                $this->plugin = Advanced_Ads_Slider_Plugin::get_instance();

                // add js file to header
                add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );

		add_filter( 'advanced-ads-group-output-ad-ids', array( $this, 'output_ad_ids' ), 10, 5 );

		add_filter( 'advanced-ads-group-output-array', array( $this, 'output_slider_markup'), 10, 2 );

		// manipulate number of ads that should be displayed in a group
		add_filter( 'advanced-ads-group-ad-count', array($this, 'adjust_ad_group_number'), 10, 2 );

		// add slider markup to passive cache-busting.
		add_filter( 'advanced-ads-pro-passive-cb-group-data', array( $this, 'add_slider_markup_passive' ), 10, 3 );
        }

	/**
	 * append js file in footer
	 *
	 * @since 1.0.0
	 */
	public function register_scripts(){
		if( ! defined( 'ADVANCED_ADS_SLIDER_USE_CDN') ) {
		    wp_enqueue_script( 'unslider-js', AAS_BASE_URL . 'public/assets/js/unslider.min.js', array('jquery'), AAS_VERSION );
		    wp_enqueue_style( 'unslider-css', AAS_BASE_URL . 'public/assets/css/unslider.css', array(), AAS_VERSION );
		} else {
		    // Using a CDN to prevend encoding issues in certain cases.
		    wp_enqueue_script( 'unslider-js', 'https://cdnjs.cloudflare.com/ajax/libs/unslider/2.0.3/js/unslider-min.js', array('jquery'), AAS_VERSION );
		    wp_enqueue_style( 'unslider-css', 'https://cdnjs.cloudflare.com/ajax/libs/unslider/2.0.3/css/unslider.css', array(), AAS_VERSION );
		}
                wp_enqueue_style( 'slider-css', AAS_BASE_URL . 'public/assets/css/slider.css', array(), AAS_VERSION);
		// scripts for swipe feature
		if( ! defined( 'ADVANCED_ADS_NO_SWIPE') ) {
		    wp_enqueue_script( 'unslider-move-js', AAS_BASE_URL . 'public/assets/js/jquery.event.move.js', array('jquery'), AAS_VERSION );
		    wp_enqueue_script( 'unslider-swipe-js', AAS_BASE_URL . 'public/assets/js/jquery.event.swipe.js', array('jquery'), AAS_VERSION );
		}
		
	}
	
	/**
	 * get ids from ads in the order they should be displayed
	 *
	 * @param arr $ordered_ad_ids ad ids in the order from the main plugin
	 * @param str $type group type
	 * @param arr $ads array with ad objects
	 * @param arr $weights array with ad weights
	 * @param arr $group Advanced_Ads_Group Object
	 * @return arr $ad_ids
	 */
	public function output_ad_ids( $ordered_ad_ids, $type, $ads, $weights, Advanced_Ads_Group $group ){
	    // return order by weights if this is a slider
	    if( $type === 'slider' ){
			if(isset($group->options['slider']['random'])){
				return $group->shuffle_ads($ads, $weights);
			} else {
				return array_keys($weights);
			}
	    }

	    // return default
	    return $ordered_ad_ids;
	}

	/**
	 * adjust the ad group number if the ad type is a slider
	 *
	 * @param int $ad_count
	 * @param obj $group Advanced_Ads_Group
	 * @return int $ad_count
	 */
	public function adjust_ad_group_number( $ad_count = 0, $group ){

	    if( $group->type === 'slider' ){
		    return 'all';
	    }

	    return $ad_count;
	}

	/**
	 * add extra output markup for slider group
	 *
	 * @param arr $ad_content array with ad contents
	 * @param obj $group Advanced_Ads_Group
	 * @return arr $ad_content with extra markup
	 */
	public function output_slider_markup( array $ad_content, Advanced_Ads_Group $group ){

		if( count( $ad_content ) <= 1 || 'slider' !== $group->type ) {
		    return $ad_content;
		}

		$markup = $this->get_slider_markup( $group );

		foreach( $ad_content as $_key => $_content ){
		    $ad_content[$_key] = sprintf( $markup['each'], $_content );
		}

		$markup = $this->get_slider_markup( $group );
		array_unshift( $ad_content, $markup['before'] );
		array_push( $ad_content, $markup['after'] );

		return $ad_content;
	}

	/**
	 * Get markup to inject around each slide and around set of slides.
	 *
	 * @param arr $ad_content array with ad contents
	 * @param obj $group Advanced_Ads_Group
	 * @return arr
	 */
	public function get_slider_markup( Advanced_Ads_Group $group ) {
		$slider_options = self::get_slider_options( $group );

		/* custom css file was added with version 1.1. Deactivate the following lines if there are issues with your layout
		 * $css = "<style>.advads-slider { position: relative; width: 100% !important; overflow: hidden; } "
			. ".advads-slider ul, .advads-slider li { list-style: none; margin: 0 !important; padding: 0 !important; } "
			. ".advads-slider ul li { }</style>";*/
		$slider_var = '$' . preg_replace( '/[^\da-z]/i', '', $slider_options['init_class'] );
		
		$script = '<script>( window.advanced_ads_ready || jQuery( document ).ready ).call( null, function() {'
		. 'var ' . $slider_var . ' = jQuery( ".' . $slider_options['init_class'] . '" );'
		// display all ads after slider is loaded to avoid all ads being displayed as a list'
		. $slider_var . '.on( "unslider.ready", function() { jQuery( "div.custom-slider ul li" ).css( "display", "block" ); });'
		. $slider_var . '.unslider({ ' . $slider_options['settings'] . ' });'
		. $slider_var . '.on("mouseover", function(){'.$slider_var.'.unslider("stop");}).on("mouseout", function() {'.$slider_var.'.unslider("start");});});</script>';	

		$result = array(
			'before' => '<div id="'. $slider_options['slider_id'].'" class="'.'custom-slider '. $slider_options['init_class'] .' ' . $slider_options['prefix'] .'slider"><ul>',
			'after' => '</ul></div>' . $script,
			'each' => '<li>%s</li>',
			'min_ads' => 2,
		);
		//$result['after'] .= $css;

		return $result;
	}

	/**
	 * Add slider markup to passive cache-busting.
	 *
	 * @param arr $group_data
	 * @param obj $group Advanced_Ads_Group
	 * @param string $element_id
	 */
	public function add_slider_markup_passive( $group_data, Advanced_Ads_Group $group, $element_id ) {
		if ( $element_id && 'slider' === $group->type  ) {
			$group_data['group_wrap'][] = $this->get_slider_markup( $group );
		}

		return $group_data;
	}

    /**
     * return slider options
     *
     * @param obj $group Advanced_Ads_Group
     * @return array that contains slider options
     */
    public static function get_slider_options( Advanced_Ads_Group $group ) {
        $settings = array();
        if ( isset( $group->options['slider']['delay'] ) ) {
            $settings['delay'] = absint( $group->options['slider']['delay'] );
            $settings['autoplay'] = 'true';
            $settings['nav'] = 'false';
            $settings['arrows'] = 'false';
            $settings['infinite'] = 'true';
        }

        $settings = apply_filters( 'advanced-ads-slider-settings', $settings );

        // merge option keys and values in preparation for the option string
	$setting_attributes = array_map( array( 'Advanced_Ads_Slider', 'map_settings' ), array_values($settings), array_keys($settings));

        $settings = implode( ', ', $setting_attributes );

        $prefix = Advanced_Ads_Plugin::get_instance()->get_frontend_prefix();
        $slider_id = $prefix . 'slider-' . $group->id;
        $slider_init_class = $prefix . 'slider-' . mt_rand();

        return array(
            'prefix' => $prefix,
            'slider_id' => $slider_id,
            'init_class' => $slider_init_class,
            'settings' => $settings // slider init options
        );
    }
    
    /**
     * helper function for array_map, see above
     * needed for php prior 5.3
     */
    public static function map_settings( $value, $key ){
	
	return $key.':'.$value.'';
	
    }

}
