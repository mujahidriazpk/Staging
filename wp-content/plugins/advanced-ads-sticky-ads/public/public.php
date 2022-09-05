<?php

class Advanced_Ads_Sticky {

		/**
		 * holds plugin base class
		 *
		 * @var Advanced_Ads_Sticky_Plugin
		 * @since 1.2.0
		 */
		protected $plugin;
		
		/**
		 * hold names of new placements
		 */
		protected $sticky_placements = array( 
		    'sticky_left_sidebar', 'sticky_right_sidebar',
		    'sticky_header', 'sticky_footer',
		    'sticky_left_window', 'sticky_right_window' );

	/**
	 * Collect the ids of placements.
	 *
	 * @var array
	 */
	private $placement_ids = array();

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	public function __construct( $is_admin, $is_ajax ) {

		$this->plugin = Advanced_Ads_Sticky_Plugin::get_instance();

		add_action( 'plugins_loaded', array( $this, 'wp_plugins_loaded_ad_actions' ), 20 );

		// handle special ajax events
		if ( $is_ajax ) {
		} elseif ( ! $is_admin ) {
			// no ajax, no admin
			add_action( 'plugins_loaded', array( $this, 'wp_plugins_loaded' ) );
		}

	}

	/**
	 * load actions and filters needed only for ad rendering
	 *  this will make sure options get loaded for ajax and non-ajax-calls
	 */
	public function wp_plugins_loaded_ad_actions(){
		// stop, if main plugin doesn’t exist
		if ( ! class_exists( 'Advanced_Ads', false ) ) {
			return ;
		}

		// add wrapper options, ad
		add_filter( 'advanced-ads-output-wrapper-options', array( $this, 'add_wrapper_options' ), 20, 2 );
		// add wrapper options, group
		add_filter( 'advanced-ads-output-wrapper-options-group', array( $this, 'add_wrapper_options_group' ), 10, 2 );
		// add wrapper options, group, passive cache-busting
		add_filter( 'advanced-ads-pro-passive-cb-group-data', array( $this, 'after_group_output_passive' ), 11, 3 );
		// action after ad output is created; used for js injection
		add_filter( 'advanced-ads-ad-output', array( $this, 'after_ad_output' ), 10, 2 );
		// action after group output is created; used for js injection
		add_filter( 'advanced-ads-group-output', array( $this, 'after_group_output' ), 10, 2 );

		// add button to wrapper content
		add_filter('advanced-ads-output-wrapper-before-content', array($this, 'add_button'), 20, 2);
		// add button to wrapper content (group)
		add_filter( 'advanced-ads-output-wrapper-before-content-group', array( $this, 'add_button_group' ), 20, 2 );

		// check if current placement can be displayed at all
		add_filter('advanced-ads-can-display-placement', array($this, 'placement_can_display'), 10, 2);
	}

	/**
	* load actions and filters
	*/
	public function wp_plugins_loaded() {
		// stop, if main plugin doesn’t exist
		if ( ! class_exists( 'Advanced_Ads', false ) ) {
			return ;
		}

		$this->collect_placements();
		// add options to the wrapper
		add_filter( 'advanced-ads-set-wrapper', array( $this, 'set_wrapper' ), 20, 2 );
		// append js file into footer
		add_action( 'wp_enqueue_scripts', array( $this, 'footer_scripts' ) );
		// register sticky ad array in header
		//add_action( 'wp_head', array( $this, 'register_js_array' ) );
		// inject ad content into footer
		add_action( 'wp_footer', array( $this, 'footer_injection' ), 10 );
	}

	/**
	 * register an array where all sticky ad ids are stored later
	 *
	 * @since 1.0.0
	 */
	/*
	public function register_js_array(){
		// print only, if js method is enabled
		$options = $this->plugin->options();
		if ( isset($options['sticky']['check-position-fixed']) && $options['sticky']['check-position-fixed'] ){
			echo '<script>advanced_ads_sticky_ads = new Array();</script>';
		}
	}
	*/

	/**
	 * append js file in footer
	 *
	 * @since 1.0.0
	 */
	public function footer_scripts(){
		if ( function_exists( 'advads_is_amp' ) && advads_is_amp() ) {
			return;
		}
		// include file only if js method is enabled
		$options = $this->plugin->options();

		wp_enqueue_script( 'advanced-ads-sticky-footer-js', AASADS_BASE_URL . 'public/assets/js/sticky.js', array(), AASADS_VERSION, true );
		wp_localize_script( 'advanced-ads-sticky-footer-js', 'advanced_ads_sticky_settings', array(
			'check_position_fixed' => isset( $options['sticky']['check-position-fixed'] ) && $options['sticky']['check-position-fixed'],
			'sticky_class'         => $this->get_sticky_class(),
			'placements'           => $this->placement_ids,
		) );
	}

	/**
	 * set the ad wrapper options
	 *
	 * @since 1.0.0
	 * @param arr $wrapper wrapper options
	 * @param obj $ad ad object
	 * @deprecated since 1.2.2  (16 Jul 2015)
	 */
	public function set_wrapper($wrapper = array(), $ad){
		$options = $ad->options();

		// define basic layer options
		if ( ! empty($options['sticky']['enabled']) && ! empty($options['sticky']['type']) ){

		    $wrapper['class'][] = $this->get_sticky_class();
			$wrapper['style']['position'] = 'fixed';
			$wrapper['style']['z-index'] = 9999;

			switch ( $options['sticky']['type'] ) {
				case 'absolute' :

					if ( isset($options['sticky']['position']['top']) ) { $wrapper['style']['top'] = $options['sticky']['position']['top'] . 'px'; }
					if ( isset($options['sticky']['position']['right']) ) { $wrapper['style']['right'] = $options['sticky']['position']['right'] . 'px'; }
					if ( isset($options['sticky']['position']['bottom']) ) { $wrapper['style']['bottom'] = $options['sticky']['position']['bottom'] . 'px'; }
					if ( isset($options['sticky']['position']['left']) ) { $wrapper['style']['left'] = $options['sticky']['position']['left'] . 'px'; }
					break;
				case 'assistant' :
					$width = absint( $options['sticky']['position']['width'] );

					switch ( $options['sticky']['assistant'] ){
						case 'topleft' :
							$wrapper['style']['top'] = 0;
							$wrapper['style']['left'] = 0;
						break;
						case 'topcenter' :
							$wrapper['style']['margin-left'] = '-' . $width / 2 . 'px';
							$wrapper['style']['top'] = 0;
							$wrapper['style']['left'] = '50%';
						break;
						case 'topright' :
							$wrapper['style']['top'] = 0;
							$wrapper['style']['right'] = 0;
						break;
						case 'centerleft' :
							$wrapper['style']['bottom'] = '50%';
							$wrapper['style']['left'] = 0;
						break;
						case 'center' :
							$wrapper['style']['margin-left'] = '-' . $width / 2 . 'px';
							$wrapper['style']['bottom'] = '50%';
							$wrapper['style']['left'] = '50%';
						break;
						case 'centerright' :
							$wrapper['style']['bottom'] = '50%';
							$wrapper['style']['right'] = 0;
						break;
						case 'bottomleft' :
							$wrapper['style']['bottom'] = 0;
							$wrapper['style']['left'] = 0;
						break;
						case 'bottomcenter' :
							$wrapper['style']['margin-left'] = '-' . $width / 2 . 'px';
							$wrapper['style']['bottom'] = 0;
							$wrapper['style']['left'] = '50%';
						break;
						case 'bottomright' :
							$wrapper['style']['bottom'] = 0;
							$wrapper['style']['right'] = 0;
						break;
					}
				break;
			}
		}

		return $wrapper;
	}

	/**
	 * render a normal fixed box that is not moving
	 *
	 * @since 1.0.0
	 * @param string $content normal (html) content
	 * @param array $position position of the element with optional keys top, left, right, bottom
	 */
	/* public function render_fixed( $content = '', $position = 0 ) {
		// get random id for the ad in case there is more than one sticky ads
		list($output, $id) = self::_wrapper( $content, $position );
		return $output;
	} */

	/**
	 * ad is moving until a specific position on the screen and is sticky there
	 *
	 * @since not yet implemented
	 * @param string $content normal (html) content
	 * @param array $position position of the element with optional keys top, left, right, bottom
	 */
	/* public function render_scroll_to($content = '', $position = 0) {
		if ( empty($position) ) {
			return $content; }
		// wrap the content without fixed position
		list($output, $id) = self::_wrapper( $content, 0 );

		// attach JS object with sticky information
		$js = '<script>jQuery(document).ready(function(){ wzSticky.init("' . $id . '", "", ' . $position['top'] . ');})</script>';
		$output .= $js;

		return $output;
	} */

	/**
	 * ad is moving when a specific position on the screen is reached
	 */
	/* public function render_scroll_from($content = '', $position = 0) {
		if ( empty($position) ) {
			return $content; }

		// STILL NEEDS TO BE WRITTEN
	} */

	/**
	 * Inject ad placement into footer.
	 *
	 * @since 1.2.3
	 */
	public function footer_injection() {
		foreach ( $this->placement_ids as $placement_id ) {
			echo Advanced_Ads_Select::get_instance()->get_ad_by_method( $placement_id, Advanced_Ads_Select::PLACEMENT );
		}
	}

	/**
	 * add sticky attributes to wrapper
	 *
	 * @since 1.2.3
	 * @param arr $options
	 * @param obj $ad ad object
	 * @return arr $options Modified options.
	 */
	public function add_wrapper_options( $options = array(), Advanced_Ads_Ad $ad ){
		$top_level = ! isset( $ad->args['previous_method'] ) || 'placement' === $ad->args['previous_method'];
		if ( ! $top_level ) { return $options; }

		// don’t use this if we are not in a sticky ad
		if ( ! isset( $ad->args['placement_type'] ) || ! $this->is_sticky_placement( $ad->args['placement_type'] ) ) {
			return $options;
		}

		if ( isset( $ad->args['previous_method'] ) && 'group' === $ad->args['previous_method'] ) {
			$ad_options = $ad->options();
			if ( isset( $ad_options['close']['enabled'] ) && $ad_options['close']['enabled'] ) {
				$options['style']['position'] = 'relative';
			}
			return $options;
		}

		$width = ! empty( $ad->args['placement_width'] ) ? $ad->args['placement_width'] : $ad->width;

	    return $this->get_wrapper_options( $options, $ad->args, $width );
	}

	/**
	 * Add sticky attributes to wrapper of the group.
	 *
	 * @param arr $options
	 * @param obj $group Advanced_Ads_Group.
	 * @param bool $add_width Whether to add width to the wrapper.
	 * @since untagged
	 */
	public function add_wrapper_options_group( $options = array(), Advanced_Ads_Group $group ) {
		$top_level = ! isset( $group->ad_args['previous_method'] ) || 'placement' === $group->ad_args['previous_method'];
		if ( ! $top_level ) { return $options; }

		// Don't use this if Refresh interval is enabled and the group is nested inside ad ad.
		$group_refresh_not_top = isset( $group->ad_args['group_refresh'] ) && ! $group->ad_args['group_refresh']['is_top_level'];
		if ( $group_refresh_not_top ) {
			return $options;
		}


		$width = ! empty( $group->ad_args['placement_width'] ) ? $group->ad_args['placement_width'] : 0;
		$add_width = $group->type === 'slider' && $width;

		return $this->get_wrapper_options( $options, $group->ad_args, $width, $add_width );
	}

	/**
	 * Get sticky attributes.
	 *
	 * @param array $options Existing options.
	 * @param array $args Optional arguments passed to ads.
	 * @param int $width Width of the ad of the placement.
	 * @param bool $add_width Whether to add width to the wrapper.
	 * @return array $options Modified Options.
	 * @since untagged
	 */
	private function get_wrapper_options( $options = array(), $args, $width, $add_width = false ) {
	    if ( isset ( $args['placement_type'] ) && $this->is_sticky_placement( $args['placement_type'] ) ){
		    $width = absint( $width );

		    if ( ! empty( $args['sticky_is_fixed'] ) ) {
		        $options['class'][] = $this->get_sticky_class();
		    }

		    switch ( $args['placement_type'] ){
			    case 'sticky_header' :
				    $options['style']['position'] = 'fixed';
				    $options['style']['top'] = '0';
				    if ( isset( $args['sticky_bg_color'] ) && '' !== $args['sticky_bg_color'] ){
					    $options['style']['left'] = '0';
					    $options['style']['right'] = '0';
					    $options['style']['background-color'] = $args['sticky_bg_color'];
				    }
				    $options['style']['z-index'] = '10000';
				    $options['class'][] = $this->get_sticky_class();
				break;
			    case 'sticky_footer' :
				    $options['style']['position'] = 'fixed';
				    $options['style']['bottom'] = '0';
				    if ( isset( $args['sticky_bg_color'] ) && '' !== $args['sticky_bg_color'] ){
					    $options['style']['left'] = '0';
					    $options['style']['right'] = '0';
					    $options['style']['background-color'] = $args['sticky_bg_color'];
				    }
				    $options['style']['z-index'] = '10000';
				    $options['class'][] = $this->get_sticky_class();
				break;
			    case 'sticky_left_sidebar' :
				    $options['style']['position'] = 'absolute';
				    $options['style']['display'] = 'inline-block';

				    if ( $width ) {
					    $options['style']['left'] = '-' . $width . 'px';
				    } else {
					    $options['style']['right'] = '100%';
				    }

				    $options['style']['top'] = '0';
				    $options['style']['z-index'] = '10000';
				break;
			    case 'sticky_right_sidebar' :
				    $options['style']['position'] = 'absolute';
				    $options['style']['display'] = 'inline-block';

				    if ( $width ) {
					    $options['style']['right'] = '-' . absint( $width ) . 'px';
				    } else {
					    $options['style']['left'] = '100%';
				    }

				    $options['style']['top'] = '0';
				    $options['style']['z-index'] = '10000';
				break;
			    case 'sticky_left_window' :
				    $options['style']['position'] = 'absolute';
				    $options['style']['display'] = 'inline-block';
				    $options['style']['left'] = '0px';
				    $options['style']['top'] = '0';
				    $options['style']['z-index'] = '10000';
				break;
			    case 'sticky_right_window' :
				    $options['style']['position'] = 'absolute';
				    $options['style']['display'] = 'inline-block';
				    $options['style']['right'] = '0px';
				    $options['style']['top'] = '0';
				    $options['style']['z-index'] = '10000';
				break;
		    }

		    // hide ad if sticky trigger is given
		    if ( isset( $args['sticky']['trigger'] ) && '' !== $args['sticky']['trigger'] ){
			    $options['style']['display'] = 'none';
		    }

		    if ( $add_width ) {
			    $options['style']['width'] = absint( $width ) . 'px';
		    }
	    }

	    return $options;
	}

	/**
	 * inject js code to move ad into another element
	 *
	 * @since 1.2.3
	 * @param str $content ad content
	 * @param obj $ad ad object
	 */
	public function after_ad_output( $content = '', Advanced_Ads_Ad $ad ){
	    // don’t use this if we are not in a sticky ad
	    if( ! isset( $ad->args['placement_type'] ) || ! $this->is_sticky_placement( $ad->args['placement_type'] ) ){
		    return $content;
	    }
	    
	    // don’t inject script on a per ad-basis if this is a group
	    if( isset( $ad->args['previous_method'] ) && 'group' === $ad->args['previous_method'] ){
		return $content;
	    }

	    if ( isset( $ad->wrapper['id'] ) ) {
		    $width = ! empty( $ad->args['placement_width'] ) ? $ad->args['placement_width'] : $ad->width;
		    $height = ! empty( $ad->args['placement_height'] ) ? $ad->args['placement_height'] : $ad->height;
		    return $this->build_sticky_output( $content, $ad->args['placement_type'], $ad->args, $ad->wrapper['id'], $width, $height );
	    }

	    return $content;
	}
	
	/**
	 * inject js code to move ad group into another element
	 *
	 * @since 1.4.6.1
	 * @param str $content ad content
	 * @param obj $ad ad object
	 */
	public function after_group_output( $content = '', Advanced_Ads_Group $group ){
	    // don’t use this if we are not in a sticky placement
	    if ( ! isset( $group->ad_args['placement_type'] ) || ! $this->is_sticky_placement( $group->ad_args['placement_type'] ) ) {
		    return $content;
	    }

		// Don't use this if Refresh interval is enabled and the group is nested inside ad ad.
		$group_refresh_not_top = isset( $group->ad_args['group_refresh'] ) && ! $group->ad_args['group_refresh']['is_top_level'];
		if ( $group_refresh_not_top ) {
			return $content;
		}

	    if ( isset( $group->wrapper['id'] ) ) {
		    $width = ! empty( $group->ad_args['placement_width'] ) ? $group->ad_args['placement_width'] : 0;
		    $height = ! empty( $group->ad_args['placement_height'] ) ? $group->ad_args['placement_height'] : 0;

			return $this->build_sticky_output( $content, $group->ad_args['placement_type'], $group->ad_args, $group->wrapper['id'], $width, $height );
	    }
	    return $content;
	}

	/**
	 * Inject js code to move ad group into another element (passive cache-busting).
	 *
	 * @param array $group_data Data to inject after the group.
	 * @param obj $group Advanced_Ads_Group.
	 * @param string $elementid
	 * @since untagged
	 */
	public function after_group_output_passive( $group_data, Advanced_Ads_Group $group, $elementid ) {

		// don’t use this if we are not in a sticky placement
		if ( ! isset( $group->ad_args['placement_type'] ) || ! $this->is_sticky_placement( $group->ad_args['placement_type'] ) ) {
			return $group_data;
		}

		if ( isset( $group->wrapper['id'] ) ) {
			$width = ! empty( $group->ad_args['placement_width'] ) ? $group->ad_args['placement_width'] : 0;
			$height = ! empty( $group->ad_args['placement_height'] ) ? $group->ad_args['placement_height'] : 0;

			$js_output = $this->build_sticky_output( '', $group->ad_args['placement_type'], $group->ad_args, $group->wrapper['id'], $width, $height );
			$group_data['group_wrap'][] = array( 'after' => $js_output );
		}

		return $group_data;
	}
	
	/**
	 * build sticky script output
	 * 
	 * @since 1.4.6.1
	 */
	public function build_sticky_output( $content = '', $type = '', $placement_options = array(), $wrapper_id = '', $width = 0, $height = 0 ) {
		$top_level = ! isset( $placement_options['previous_method'] ) || 'placement' === $placement_options['previous_method'];
		if ( ! $top_level ) { return $content; }

	    // don’t use this if we are not in a sticky ad
	    if( '' === $type || ! $this->is_sticky_placement( $type ) ){
		    return $content;
	    }
	    
	    $target = '';
	    $options = array();
	    $fixed = ( isset( $placement_options['sticky_is_fixed'] ) && $placement_options['sticky_is_fixed'] ) ? true : false;
	    $centered = false;
	    // Whether we can  convert 'fixed' position to 'absolute' in case 'fixed' is not supported.
	    $can_convert_to_abs = false;
	    $is_invisible = false;
	    $width_missing = false;
	    switch ( $type ){
		    case 'sticky_left_sidebar' :
				if ( isset( $placement_options['sticky_element'] ) && '' !== $placement_options['sticky_element'] ){
					$target = $placement_options['sticky_element'];
				} else {
					$options[] = 'target:"wrapper"';
					$options[] = 'offset:"left"';
				}
				$width_missing = empty( $width );
			break;
		    case 'sticky_right_sidebar' :
				if ( isset( $placement_options['sticky_element'] ) && '' !== $placement_options['sticky_element'] ){
					$target = $placement_options['sticky_element'];
				} else {
					$options[] = 'target:"wrapper"';
					$options[] = 'offset:"right"';
				}
				$width_missing = empty( $width );
		    break;
		    case 'sticky_header' :
		    case 'sticky_footer' :
			    $centered = true;
			    $can_convert_to_abs = true;
		    break;
		    case 'sticky_left_window' :
		    case 'sticky_right_window' :
			    $target = 'body';
			    $can_convert_to_abs = true;
		    break;
		    default : return $content; // dont add output on placements not added by this plugin
	    }
	    
	    // show warning, if width is missing
	    if( $width_missing ){
		    $content .= '<script>console.log("Advanced Ads Sticky: Can not place sticky ad due to missing width attribute of the ad.");</script>';
	    }	    

	    $content .= '<script>( window.advanced_ads_ready || jQuery( document ).ready ).call( null, function() {';
		$content .= 'var wrapper_id = "#' . $wrapper_id . '"; var $wrapper = jQuery( wrapper_id );';

	    if ( ! empty( $placement_options['cache_busting_elementid'] ) ) {
		    $content = '<script>advads.move("#'. $placement_options['cache_busting_elementid'] .'", "' . $target . '", { '. implode( ',', $options ) .' });</script>' . $content;
		    $use_grandparent = true;
	    } else {
		    $content .= 'advads.move( wrapper_id, "' . $target . '", { '. implode( ',', $options ) .' });';
		    $use_grandparent = false;
	    }


		$content .= 'window.advanced_ads_sticky_items = window.advanced_ads_sticky_items || {};'
			. 'advanced_ads_sticky_items[ "' . $wrapper_id . '" ] = { '
			. '"can_convert_to_abs": "' . $can_convert_to_abs . '", '
			. '"initial_css": $wrapper.attr( "style" ), '
			. '"modifying_func": function() { ';

	    if ( $fixed ){
			$options  = array(
				'use_grandparent' => $use_grandparent,
				'offset' => 'sticky_left_sidebar' === $type ? 'left' : 'right'
			);
		    // add is_invisible option, if trigger and duration are set
		    if( isset( $placement_options['sticky']['trigger'] ) 
			&& '' !== $placement_options['sticky']['trigger'] ) {
			$options['is_invisible'] = true;
		    }
			$options = json_encode( $options );
		    $content .= 'advads.fix_element( $wrapper, '.$options.' );';
		} elseif ( $type === 'sticky_left_sidebar' || $type === 'sticky_right_sidebar' ) {
			$options  = array( 'use_grandparent' => $use_grandparent );
			$options = json_encode( $options );
			$content .= 'advads.set_parent_relative( $wrapper, ' . $options . ' );';
		}

	    if ( $centered ){
		    // use width to center the ad
		    // might be resent, if background given
		    if ( $width ) {
			    $content .= '$wrapper.css("width", ' . absint( $width )  . ');';
		    }
		    // center element with text-align, if background is selected
		    if ( isset( $placement_options['sticky_bg_color'] ) && '' !== $placement_options['sticky_bg_color'] ){
			    // check if there is a display setting already (maybe due to timeout
			    if ( isset( $placement_options['sticky']['trigger'] ) && '' !== $placement_options['sticky']['trigger'] ){
				$display = 'none';
			    } else {
				$display = 'block';
			    }
			    $content .= '$wrapper.css({ textAlign: "center", display: "'. $display .'", width: "auto" });';
		    } elseif ( $width ) {
			    $content .= 'advads.center_fixed_element( $wrapper );';
		    } else {
			    $content .= '$wrapper.css({ "-webkit-transform": "translateX(-50%)", "-moz-transform": "translateX(-50%)", "transform": "translateX(-50%)", "left": "50%", "margin-right": "-50%" });';
		    }
	    }

	    // center ad container vertically
	    if ( isset( $placement_options['sticky_center_vertical'] ) ){
		    // use height to center the ad
		    if( $height ){
			    $content .= '$wrapper.css("height", ' . absint( $height )  . ');';

		    }
		    $content .= 'advads.center_vertically( $wrapper );';
	    }

	    // choose effect and duration
	    $effect = '';
	    if( isset( $placement_options['sticky']['trigger'] ) 
		    && '' !== $placement_options['sticky']['trigger'] 
		    && isset( $placement_options['sticky']['effect'] ) ) {

		    $duration = isset( $placement_options['sticky']['duration'] ) ? absint( $placement_options['sticky']['duration'] ) : 0;
		    switch( $placement_options['sticky']['effect'] ){
			    case 'fadein' :
				$effect = "fadeIn($duration).";
				break;
			    case 'slidedown' :
				$effect = "slideDown($duration).";
				break;
			    default : // show
				$effect = "show($duration).";
		    }
	    }

	    // use trigger
	    if( isset( $placement_options['sticky']['trigger'] )
	    	&& $placement_options['sticky']['trigger']
	    ) {
			$effect .= "css( 'display', 'block' );";

			switch( $placement_options['sticky']['trigger'] ){
			    case 'effect' :
				$content .= '$wrapper.' . $effect;
				break;
			    case 'timeout' :
				$delay = isset( $placement_options['sticky']['delay'] ) ? absint( $placement_options['sticky']['delay'] ) * 1000 : 0;
				$content .= 'setTimeout( function() { $wrapper.trigger( "advads-sticky-trigger" ).' . $effect . "}, $delay );";
				break;
		    }
	    }

	    $content = $this->close_script( $content, $placement_options, $wrapper_id );

		// End of modifying function declaration.
		$content .= "}};\n";

		// Check if the function for waiting until images are ready exists.
		$content .= 'if ( advads.wait_for_images ) { ' . "\n";
		$content .= '    var sticky_wait_for_images_time = new Date().getTime();' . "\n";
		$content .= '    $wrapper.data( "sticky_wait_for_images_time", sticky_wait_for_images_time );' . "\n";
		$content .= '    advads.wait_for_images( $wrapper, function() {' . "\n";
		$content .= '        // At the moment when this function is called, it is possible that ' . "\n";
		$content .= '        // the placement has been updated using "Reload ads on resize" feature of Responsive add-on ' . "\n";
		$content .= '        if ( $wrapper.data( "sticky_wait_for_images_time" ) === sticky_wait_for_images_time ) {' . "\n";
		$content .= '            advanced_ads_sticky_items[ "' . $wrapper_id . '" ]["modifying_func"]();' . "\n";
		$content .= '        } ' . "\n";
		$content .= '    } );' . "\n";
		$content .= '} else { ' . "\n";
		$content .= '    advanced_ads_sticky_items[ "' . $wrapper_id . '" ]["modifying_func"]();' . "\n";
		$content .= '};' . "\n";


		// End of document ready.
		$content .= '});</script>';

	    return $content;
	}

	/**
	* add the close button to the wrapper

	* @since 1.4.1
	* @param string $content additional content added
	* @param obj $ad ad object
	*/
	public function add_button( $content = '', $ad = '' ) {
	    $options = $ad->options();
	    $top_level = ! isset( $options['previous_method'] ) || 'placement' === $options['previous_method'];
	    if ( $top_level && isset($options['close']['enabled']) && $options['close']['enabled'] ) {
		    // build close button
		    $content .= $this->build_close_button($options['close']);
	    }

	    return $content;
	}

	/**
	* Add the close button to the group wrapper.
	*
	* @param string $content additional content added
	* @param obj $group Advanced_Ads_Group
	*/
	public function add_button_group( $content = '', Advanced_Ads_Group $group ) {
		$top_level = ! isset( $group->ad_args['previous_method'] ) || 'placement' === $group->ad_args['previous_method'];

		// Don't use this if Refresh interval is enabled and the group is nested inside ad ad.
		$group_refresh_not_top = isset( $group->ad_args['group_refresh'] ) && ! $group->ad_args['group_refresh']['is_top_level'];
		if ( $group_refresh_not_top ) {
			return $content;
		}


		if ( $top_level && isset( $group->ad_args['close']['enabled'] ) && $group->ad_args['close']['enabled'] ) {
			// build close button
			$content .= $this->build_close_button( $group->ad_args['close'] );
		}

		return $content;
	}

	/**
	* build the close button
	*
	* @since 1.4.1
	* @param arr $options original [close] part of the ad options array
	*/
	public function build_close_button( $options ){
	    $closebutton = '';
	    if(!empty($options['where']) && !empty($options['side'])) {
		switch($options['where']){
		    case 'inside' :
			$offset = '0';
			break;
		    default : $offset = '-15px';
		}
		switch($options['side']){
		    case 'left' :
			$side = 'left';
			$opposite = 'right';
			break;
			default :
				$side = 'right';
				$opposite = 'left';
		}
		$prefix      = Advanced_Ads_Plugin::get_instance()->get_frontend_prefix();
		// Add a dummy `onclick` attribute so that the `click` event gets fired in all browsers.
		$closebutton = '<span class="' . $prefix . 'close-button" onclick="void(0)" title="'.__('close', 'advanced-ads-sticky')
			.'" style="width: 15px; height: 15px; background: #fff; position: relative; line-height: 15px;'
			. ' text-align: center; cursor: pointer; z-index: 10000;'
			. $side . ':' . $offset . ';float: ' . $side . '; margin-' . $opposite . ': -15px;">×</span>';
	    }

	    return $closebutton;
	}

	/**
	 * add the javascript for close and timeout feature
	 *
	 * @since 1.4.1
	 * @param string $content Existing content.
	 * @param array $placement_options
	 * @param string $wrapper_id
	 * @return string $content Modified content.
	 */
	public function close_script( $content, $placement_options = array(), $wrapper_id = '' ) {
		if ( isset( $placement_options['close']['enabled']) && $placement_options['close']['enabled'] ) {
			$script = 'jQuery( "#' . $wrapper_id . '" ).on( "click", "span", function() { advads.close( "#'. $wrapper_id .'" ); ';
			// set time cookie
			if ( ! empty( $placement_options['close']['timeout_enabled'])){
				$timeout = absint( $placement_options['close']['timeout'] ) ? absint( $placement_options['close']['timeout'] ) : null;
				$script .= 'advads.set_cookie( "timeout_placement_' . $placement_options['output']['placement_id'] . '", 1, '. $timeout .');';
			}
			$content .= $script .'});';
		}

		return $content;
	}

	/**
	 * check if placement was closed with a cookie before
	 *
	 * @since 1.4.1
	 * @param int $id placement id
	 * @return bool whether placement can be displayed or not
	 * @return bool false if placement was closed for this user
	 */
	public function placement_can_display( $return, $id = 0 ){

		// get all placements
		$placements = Advanced_Ads::get_ad_placements_array();

		if( ! isset( $placements[ $id ]['options']['close']['enabled'] ) || ! $placements[ $id ]['options']['close']['enabled'] ){
			return $return;
		}

		if( isset( $placements[ $id ]['options']['close']['timeout_enabled'] ) && $placements[ $id ]['options']['close']['timeout_enabled'] ){
			$slug = sanitize_title( $placements[ $id ]['name'] );
			if( isset( $_COOKIE[ 'timeout_placement_' . $slug ] ) ){
				return false;
			}
		}

		return $return;
	}
	
	/**
	 * add check if a specific placement belongs to Advanced Ads Sticky
	 * 
	 * @since 1.4.7
	 * @param str $placement string with placement
	 * @return bool true, if placement belongs to this add-on
	 */
	private function is_sticky_placement( $placement = '' ){
	    
		if( !$placement ){
		    return false;
		}
		
		return in_array( $placement, $this->sticky_placements );
		
	}
	
	/**
	 * returns the (css) class name that is used for wrapping sticky ads etc. 
	 */
	public function get_sticky_class(){
	    return Advanced_Ads_Plugin::get_instance()->get_frontend_prefix() . "sticky";
	}

	/**
	 * Collect sticky placement ids.
	 */
	private function collect_placements() {
		$placements = Advanced_Ads::get_instance()->get_model()->get_ad_placements_array();

		foreach ( $placements as $placement_id => $_placement ) {
			if ( isset( $_placement['type'] ) && $this->is_sticky_placement( $_placement['type'] ) ) {
				$this->placement_ids[] = $placement_id;
			}
		}
	}
}
