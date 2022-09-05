<?php

class Advanced_Ads_Layer {

	/**
	 * holds plugin base class
	 *
	 * @var Advanced_Ads_Layer_Plugin
	 * @since 1.2.4
	 */
	protected $plugin;

	/**
	 * @var bool
	 * @since 1.3
	 */
	protected $fancybox_is_enabled;

	/**
	 * Array to hold placement ids.
	 *
	 * @var array
	 */
	private $placement_ids = array();

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $is_admin, $is_ajax ) {

		$this->plugin = Advanced_Ads_Layer_Plugin::get_instance();

		add_action( 'plugins_loaded', array( $this, 'wp_plugins_loaded_ad_actions' ), 20 );

		if ( ! $is_admin ) {
			add_action( 'plugins_loaded', array( $this, 'wp_plugins_loaded' ) );
		}

	}

	/**
	 * load actions and filters needed only for ad rendering
	 * this will make sure options get loaded for ajax and non-ajax-calls
	 */
	public function wp_plugins_loaded_ad_actions() {
		// stop, if main plugin doesn’t exist
		if ( ! class_exists( 'Advanced_Ads', false ) ) {
			return ;
		}

		$advads_options = $this->plugin->options();
		$this->fancybox_is_enabled = isset( $advads_options['layer']['use-fancybox'] ) ? $advads_options['layer']['use-fancybox'] : 0; 

		// add layer placement
		add_action( 'advanced-ads-placement-types', array( $this, 'add_layer_placement' ) );
		// add options to the wrapper
		add_filter( 'advanced-ads-set-wrapper', array( $this, 'set_wrapper' ), 21, 2 );
		// add wrapper options. Load after Sticky ad plugin
		add_filter( 'advanced-ads-output-wrapper-options', array( $this, 'add_wrapper_options' ), 21, 2 );
		// add wrapper options, group
		add_filter( 'advanced-ads-output-wrapper-options-group', array( $this, 'add_wrapper_options_group' ), 10, 2 );
		// action after ad output is created; used for js injection
		add_filter( 'advanced-ads-ad-output', array( $this, 'add_content_after' ), 20, 2 );
		// Action after group output is created; used for js injection.
		add_filter( 'advanced-ads-group-output', array( $this, 'add_content_after_group' ), 10, 2 );
		// Action after group output (passive cache-busting) is created; used for js injection.
		add_filter( 'advanced-ads-pro-passive-cb-group-data', array( $this, 'after_group_output_passive' ), 11, 2 );

		// // add button to wrapper content
		if ( ! $this->fancybox_is_enabled ) {
			add_filter( 'advanced-ads-output-wrapper-before-content', array( $this, 'add_button' ), 20, 2 );
			add_filter( 'advanced-ads-output-wrapper-before-content-group', array( $this, 'add_button_group' ), 20, 2 );
		}
		// // add close js to wrapper content
		// check if current placement can be displayed at all (after Sticky Ad plugin)
		add_filter( 'advanced-ads-can-display-placement', array( $this, 'placement_can_display' ), 11, 2 ); 
		// check if current ad can be displayed at all
		add_filter( 'advanced-ads-can-display', array( $this, 'can_display' ), 11, 2 );
	}

	/**
	* load actions and filters
	*/
	public function wp_plugins_loaded() {
		// stop, if main plugin doesn’t exist
		if ( ! class_exists( 'Advanced_Ads', false ) ) {
			return;
		}

		$this->collect_placements();

		// append js file into footer
		add_action( 'wp_enqueue_scripts', array( $this, 'footer_scripts' ) );
		// frontend output
		add_action( 'wp_head', array( $this, 'header_output' ) );
		// inject ad content into footer
		add_action( 'wp_footer', array( $this, 'footer_injection' ), 10 );
	}

	/**
	 * add layer placement to list of placements (on placement page, but also for all AXAX calls)
	 *
	 * @since 1.2.4
	 * @param arr $types existing placements
	 * @return arr $types
	 */
	public function add_layer_placement( $types ) {

		// fixed header bar
		$types['layer'] = array(
			'title'       => __( 'PopUp and Layer Ads', 'advanced-ads-layer' ),
			'description' => __( 'Create PopUp or Layer Ad', 'advanced-ads-layer' ),
			'image'       => AAPLDS_BASE_URL . 'admin/assets/img/layer.png',
			'order'       => 71,
		);

		return $types;
	}	
	
	/**
	 * Inject ad placement into footer.
	 *
	 * @since 1.2.4
	 */
	public function footer_injection() {
		foreach ( $this->placement_ids as $placement_id ) {
			echo Advanced_Ads_Select::get_instance()->get_ad_by_method( $placement_id, Advanced_Ads_Select::PLACEMENT );
		}
	}

	/**
	 * Collect pop-up placement ids.
	 */
	private function collect_placements() {
		$placements = Advanced_Ads::get_instance()->get_model()->get_ad_placements_array();
		foreach ( $placements as $placement_id => $placement ) {
			if ( isset( $placement['type'] ) && $placement['type'] === 'layer' ) {
				$this->placement_ids[] = $placement_id;
			}
		}
	}

	/**
	 * add sticky attributes to wrapper
	 *
	 * @since 1.2.4
	 * @param arr $options
	 * @param obj $ad ad object
	 */
	public function add_wrapper_options( $options = array(), Advanced_Ads_Ad $ad ) {
		$top_level = ! isset( $ad->args['previous_method'] ) || 'placement' === $ad->args['previous_method'];
		if ( ! $top_level ) { return $options; }

		// new settings from the ad itself
		$width = ( isset( $ad->width ) ) ? $ad->width : 0;
		$height = ( isset( $ad->height ) ) ? $ad->height : 0;

		if ( ! $width) {
			// obsolete settings from layer placement
			$width = ( ! empty( $args['layer_placement']['sticky']['position']['width'] ) ) ? absint( $args['layer_placement']['sticky']['position']['width']) : 0;
		}
		if ( ! $height) {
			// obsolete settings from layer placement
			$height = ( ! empty( $args['layer_placement']['sticky']['position']['height'] ) ) ? absint( $args['layer_placement']['sticky']['position']['height']) : 0;
		}


		return $this->get_wrapper_options( $options, $ad->args, $width, $height );
	}


	/**
	 * Add sticky attributes to group wrapper.
	 *
	 * @since untagged
	 * @param arr $options Existing attributes.
	 * @param obj $group Advanced_Ads_Group.
	 */
	public function add_wrapper_options_group( $options = array(), Advanced_Ads_Group $group ) {
		$top_level = ! isset( $group->ad_args['previous_method'] ) || 'placement' === $group->ad_args['previous_method'];
		if ( ! $top_level ) { return $options; }

		$width = ! empty( $group->ad_args['placement_width'] ) ? absint( $group->ad_args['placement_width'] ) : 0;
		$height = ! empty( $group->ad_args['placement_height'] ) ? absint( $group->ad_args['placement_height'] ) : 0;
		$add_width = $group->type === 'slider' && $width;

		return $this->get_wrapper_options( $options, $group->ad_args, $width, $height, $add_width );
	}

	/**
	 * Get wrapper attributes.
	 *
	 * @since untagged
	 * @param arr $options Existing attributes.
	 * @param arr $args Arguments passed to ads.
	 * @param int $width Width of the wrapper.
	 * @param int $height Height of the wrapper.
	 * @param bool $add_width Whether to add width to the wrapper.
	 * @return arr $options Modified attributes.
	 */
	private function get_wrapper_options( $options = array(), $args, $width, $height, $add_width = false ) {
		if ( isset ( $args['placement_type'] ) && $args['placement_type'] == 'layer' ) {
		    $layer_class = $this->get_layer_class();
			$options['class'][] = $layer_class;

			$options['data-width'][] = $width;
			$options['data-height'][] = $height;
			
			if ( ! empty( $args['layer_placement']['effect'] ) && ! empty( $args['layer_placement']['duration'] ) ) {
				$options['class'][] = 'advads-effect';
				$options['class'][] = 'advads-effect-' . $args['layer_placement']['effect'];
				$options['class'][] = 'advads-duration-' . absint( $args['layer_placement']['duration'] );
			}
            
			if ( isset( $args['layer_placement']['trigger'] ) ) {
				// add trigger options depending on trigger
				switch ( $args['layer_placement']['trigger'] ) {
					case '' :
						$options['class'][] = $layer_class . '-onload'; 
					break;
					case 'stop' :
						$options['class'][] = $layer_class . '-stop'; 
					break;
					case 'half' : 
						$options['class'][] = $layer_class . '-half'; 
					break;
					case 'custom' :
						$options['class'][] = $layer_class . '-offset';
						if ( isset( $args['layer_placement']['offset'] ) && $args['layer_placement']['offset'] > 0 ) {
							$options['class'][] = $layer_class . '-offset-' . absint( $args['layer_placement']['offset'] );
						}
					break;
					case 'exit' : 
						$options['class'][] = $layer_class . '-exit'; 
					break; 
					case 'delay' :
						$options['class'][] = $layer_class . '-delay';
						$options['data-advads-layer-delay'][] = isset( $args['layer_placement']['delay_sec'] ) ? absint( $args['layer_placement']['delay_sec'] ) * 1000 : 0;
					break;
				} 
			} else {
				$options['class'][] = $layer_class . '-onload';
			}

			// set background arguments (in form of a class)
			if ( ! empty( $args['layer_placement']['background'] ) ) {
				$options['class'][] = 'advads-has-background';
				if ( ! empty( $args['layer_placement']['background_click_close'] ) ) {
					$options['class'][] = 'advads-background-click-close';
				}
			}


			if ( isset( $args['layer_placement']['close']['enabled'] ) && $args['layer_placement']['close']['enabled'] ) {
				$options['class'][] = 'advads-close';
			}

			if ( ! empty( $args['layer_placement']['auto_close']['trigger'] ) ) {
				$auto_close_delay = isset( $args['layer_placement']['auto_close']['delay'] ) ? absint ( $args['layer_placement']['auto_close']['delay'] ) * 1000 : 0;
				if ( $auto_close_delay )  {
					$options['data-auto-close-delay'] = $auto_close_delay;
				}
			}

			$is_assistant = ! empty( $args['layer_placement']['sticky']['assistant'] );
			if ( $is_assistant ) {
				$options['class'][] = 'is-sticky';
				$options['data-position'][] = $args['layer_placement']['sticky']['assistant'];
			}

			$options['style']['display'] = 'none';

			if ( $add_width ) {
				$options['style']['width'] = $width . 'px';
			}

			if ( $this->fancybox_is_enabled ) {
				$options['class'][] = 'use-fancybox';
				return $options;
			}

			$options['style']['z-index'] = '9999';
			$options['style']['position'] = 'fixed';

			if ( $is_assistant ) {
				switch ( $args['layer_placement']['sticky']['assistant'] ) {
					case 'topleft' :
						$options['style']['top'] = 0;
						$options['style']['left'] = 0;
					break;
					case 'topcenter' :
						$options['style']['margin-left'] = '-' . $width / 2 . 'px';
						$options['style']['top'] = 0;
						$options['style']['left'] = '50%';
					break;
					case 'topright' :
						$options['style']['top'] = 0;
						$options['style']['right'] = 0;
					break;
					case 'centerleft' :
						$options['style']['margin-bottom'] = '-' . $height / 2 . 'px';
						$options['style']['bottom'] = '50%';
						$options['style']['left'] = 0;
					break;
					case 'center' :
						$options['style']['margin-left'] = '-' . $width / 2 . 'px';
						$options['style']['margin-bottom'] = '-' . $height / 2 . 'px';
						$options['style']['bottom'] = '50%';
						$options['style']['left'] = '50%';
					break;
					case 'centerright' :
						$options['style']['margin-bottom'] = '-' . $height / 2 . 'px';
						$options['style']['bottom'] = '50%';
						$options['style']['right'] = 0;
					break;
					case 'bottomleft' :
						$options['style']['bottom'] = 0;
						$options['style']['left'] = 0;
					break;
					case 'bottomcenter' :
						$options['style']['margin-left'] = '-' . $width / 2 . 'px';
						$options['style']['bottom'] = 0;
						$options['style']['left'] = '50%';
					break;
					case 'bottomright' :
						$options['style']['bottom'] = 0;
						$options['style']['right'] = 0;
					break;
				}
				
			} else {
				$options['style']['margin-left'] = '-' . $width / 2 . 'px';
				$options['style']['margin-bottom'] = '-' . $height / 2 . 'px';
				$options['style']['bottom'] = '50%';
				$options['style']['left'] = '50%';				
			}


		}

		return $options;
	}

	/**
	 * append js file in footer
	 *
	 * @since 1.0.0
	 */
	public function footer_scripts() {
		$options = $this->plugin->options();

		$deps = array( 'jquery' );

		if ( class_exists( 'Advanced_Ads_Pro' ) ) {
			$pro_options = Advanced_Ads_Pro::get_instance()->get_options();
			if ( ! empty( $pro_options['cache-busting']['enabled'] ) ) {
				$deps[] = 'advanced-ads-pro/cache_busting';
			}
		}

		if ( $this->fancybox_is_enabled ) {
			// Add a patched version of Fancybox that works with new versions of jQuery.
			wp_enqueue_script( 'advanced-ads-layer-fancybox-js', AAPLDS_BASE_URL . 'public/assets/fancybox/jquery.fancybox-1.3.4-patched.js', array( 'jquery' ), '1.3.4', true );
			wp_enqueue_style( 'advanced-ads-layer-fancybox-css', AAPLDS_BASE_URL . 'public/assets/fancybox/jquery.fancybox-1.3.4.css', array(), '1.3.4' );
			$deps[] = 'advanced-ads-layer-fancybox-js';
		}

		wp_enqueue_script( 'advanced-ads-layer-footer-js', AAPLDS_BASE_URL . 'public/assets/js/layer.js', $deps, AAPLDS_VERSION, true );
		wp_localize_script( 'advanced-ads-layer-footer-js', 'advanced_ads_layer_settings', array(
			'layer_class' => $this->get_layer_class(),
			'placements'  => $this->placement_ids,
		) );
	}

	/**
	 * content output in the header
	 */
	public function header_output() {
		// inject js array for banner conditions
		echo '<script>advads_items = { conditions: {}, display_callbacks: {}, display_effect_callbacks: {}, hide_callbacks: {}, backgrounds: {}, effect_durations: {}, close_functions: {}, showed: [] };</script>';
		echo '<style type="text/css" id="' . self::get_layer_class() . '-custom-css"></style>';
	}

	/**
	 * set the ad wrapper options
	 *
	 * @since 1.0.0
	 * @param arr $wrapper wrapper options
	 * @param obj $ad ad object
	 */
	public function set_wrapper( $wrapper = array(), $ad ) {
		return $this->add_css_to_wrapper( $wrapper, $ad );
	}

	/**
	 * set the ad wrapper options
	 *
	 * @since 1.2.4
	 * @param arr $wrapper wrapper options
	 * @param obj $ad ad object
	 * @return arr $wrapper with css classes/styles
	 * @deprecated since 1.3 (Oct 13 2015)
	 */
	public function add_css_to_wrapper( $wrapper = array(), $ad ) {
		$options = $ad->options();

		// define basic layer options
		if ( isset( $options['layer']['enabled'] ) && $options['layer']['enabled'] ) {
		    $layer_class = $this->get_layer_class();
			$wrapper['class'][] = $layer_class;
			$wrapper['style']['display'] = 'none';
			$wrapper['style']['z-index'] = '9999';

			$width = ( isset( $ad->width ) ) ? $ad->width : 0;
			$height = ( isset( $ad->height ) ) ? $ad->height : 0;
			$wrapper['data-width'][] = $width;
			$wrapper['data-height'][] = $height;		

			if ( $this->fancybox_is_enabled ) {
				$wrapper['class'][] = 'use-fancybox';
			}
			
			if ( ! empty( $options['layer']['effect'] ) && ! empty( $options['layer']['duration'] ) ) {
				$wrapper['class'][] = 'advads-effect';
				$wrapper['class'][] = 'advads-effect-' . $options['layer']['effect'];

				if ( ! empty( $options['layer']['duration'] ) ) {
					$wrapper['class'][] = 'advads-duration-' . absint( $options['layer']['duration'] );
				}
			}

			// center the ad if position is not set by sticky plugin
			if ( empty( $options['sticky']['enabled'] ) || empty( $options['sticky']['type'] ) ) {
				$wrapper['style']['position'] = 'fixed';
				$wrapper['style']['margin-left'] = '-' . $width / 2 . 'px';
				$wrapper['style']['margin-bottom'] = '-' . $height / 2 . 'px';				
				$wrapper['style']['bottom'] = '50%';
				$wrapper['style']['left'] = '50%';
			}

			// add trigger options depending on trigger
			switch( $options['layer']['trigger'] ) {
				case '' :
					$wrapper['class'][] = $layer_class . '-onload';
				break;
				case 'stop' :
					$wrapper['class'][] = $layer_class . '-stop';
				break;
				case 'half' :
					$wrapper['class'][] = $layer_class . '-half';
				break;
				case 'custom' :
					$wrapper['class'][] = $layer_class . '-offset';
					if ( isset( $options['layer']['offset'] ) && $options['layer']['offset'] > 0 ) {
						$wrapper['class'][] = $layer_class . '-offset-' . absint( $options['layer']['offset'] );
					}
				break;
				case 'exit' : 
					$wrapper['class'][] = $layer_class . '-exit'; 
				break; 				
			}
			// set background arguments (in form of a class)
			if ( ! empty( $options['layer']['background'] ) ) {
				$wrapper['class'][] = 'advads-has-background';
			}
		}
		// set close button options
		if ( isset( $options['layer']['close']['enabled'] ) && $options['layer']['close']['enabled'] ) {
			$wrapper['class'][] = 'advads-close';
			/*
			// $wrapper['style']['position'] = 'relative';
			// add unique close id to check timeout
			if ( ! empty($options['layer']['close']['timeout_enabled'] ) ) {
				$wrapper['class'][] = 'advads-timeout';
				$wrapper['data-id'][] = $ad->id;
				$wrapper['data-timeout'][] = absint($options['layer']['close']['timeout']);
			}
			*/
		}

		return $wrapper;
	}

	/**
	* add the close button to the wrapper

	* @since 1.0.0
	* @param string $content additional content added
	* @param obj $ad ad object
	*/
	public function add_button( $content = '', $ad = '' ) {
		$options = $ad->options();
		$top_level = ! isset( $options['previous_method'] ) || 'placement' === $options['previous_method'];

		// for button, enabled in layer placement
		if ( isset( $options['layer_placement']['close']['enabled'] ) && $options['layer_placement']['close']['enabled'] 
			&& $top_level
		) {
			// build close button
			$content .= $this->build_close_button( $options['layer_placement']['close'] );
		}
		// for button, enabled in ad settings
		else if ( isset( $options['layer']['close']['enabled'] ) && $options['layer']['close']['enabled'] ) {
			// build close button
			$content .= $this->build_close_button( $options['layer']['close'] );
		}

		return $content;
	}


	/**
	* add the close button to the group wrapper
	*
	* @param string $content additional content added
	* @param obj $group Advanced_Ads_Group
	*/
	public function add_button_group( $content = '', Advanced_Ads_Group $group ) {
		$top_level = ! isset( $group->ad_args['previous_method'] ) || 'placement' === $group->ad_args['previous_method'];

		// for button, enabled in layer placement
		if ( ! empty( $group->ad_args['layer_placement']['close']['enabled'] ) && $top_level ) {
			// build close button
			$content .= $this->build_close_button( $group->ad_args['layer_placement']['close'] );
		}

		return $content;
	}

	/**
	 * build the close button
	 *
	 * @since 1.0.0
	 * @param arr $options original [close] part of the ad options array
	 */
	public function build_close_button( $options ) {
		$closebutton = '';
		if ( ! empty( $options['where'] ) && ! empty( $options['side'] ) ) {
			switch( $options['where'] ) {
				case 'inside' :
					$offset = '0';
				break;
				default : $offset = '-15px';
			}
			switch( $options['side'] ) {
				case 'left' :
					$side = 'left';
					$opposite = 'right';
				break;
				default :
					$side = 'right';
					$opposite = 'left';
			}
			$prefix      = Advanced_Ads_Plugin::get_instance()->get_frontend_prefix();
			$closebutton = '<span class="' . esc_attr( $prefix ) . 'close-button" title="' . __( 'close', 'advanced-ads-layer' )
				.'" style="width: 15px; height: 15px; background: #fff; position: relative; line-height: 15px;'
				.' text-align: center; cursor: pointer; z-index: 10000; '
				. $side . ':' . $offset . ';float: ' . $side . '; margin-' . $opposite . ': -15px;">×</span>';
		}

		return $closebutton;
	}


	/**
	 * add content after the ad wrapper
	 *
	 * @since 1.0.0
	 * @param string $content Existing content.
	 * @param obj $ad ad object
	 */
	public function add_content_after( $content = '', Advanced_Ads_Ad $ad ) {
		if ( ! isset( $ad->wrapper['id'] ) ) { return $content; }

		$options = $ad->options();
		$content .= $this->close_script( $ad->wrapper['id'], $options, $ad->id );
        
		return $content;
	}

	/**
	 * Add content after the group wrapper.
	 *
	 * @since untagged
	 * @param str $content Existing content.
	 * @param obj $group Advanced_Ads_Group.
	 * @return str $content Modified content.
	 */
	public function add_content_after_group( $content = '', Advanced_Ads_Group $group ) {
		if ( isset( $group->wrapper['id'] ) ) {
			$content .= $this->close_script( $group->wrapper['id'], $group->ad_args );
		}

	    return $content;
	}

	/**
	 * Add content after the group wrapper (passive cache-busting).
	 *
	 * @since untagged
	 * @param arr $group_data Data to inject after the group.
	 * @param obj $group Advanced_Ads_Group.
	 * @return arr $group_data Modified data to inject after the group.
	 */
	public function after_group_output_passive( $group_data, Advanced_Ads_Group $group ) {
		if ( isset( $group->wrapper['id'] ) ) {

			$close_script = $this->close_script( $group->wrapper['id'], $group->ad_args );

			if ( $close_script) {
				$group_data['group_wrap'][] = array( 'after' => $close_script );
			}
		}

		return $group_data;
	}


	/**
	 * add the javascript for close and timeout feature
	 *
	 * @since 1.2.4
	 * @param str $wrapper_id Id of the wrapper.
	 * @param arr $options Arguments passed to ads.
	 * @param int $ad_id Id of the ad.
	 */
	public function close_script( $wrapper_id, $options, $ad_id = '' ) {

		// close button enabled for layer placement
		$content = '';
		$top_level = ! isset( $options['previous_method'] ) || 'placement' === $options['previous_method'];
		$is_layer_placement = isset ( $options['placement_type'] ) && $options['placement_type'] === 'layer';

		if ( $top_level && $is_layer_placement ) {
			$close_button_enabled = isset( $options['layer_placement']['close']['enabled'] ) && $options['layer_placement']['close']['enabled'];
			$set_cookie_string = '';
			// check if value exists; also 0 works, since it sets the cookie for the current session
			if ( isset( $options['layer_placement']['close']['timeout_enabled'] ) ) {
				$timeout = isset( $options['layer_placement']['close']['timeout'] ) ? absint( $options['layer_placement']['close']['timeout'] ) : 0;
				if ( ! $timeout ) {
					// Session cookie;
					$timeout = 'null';
				}

				$set_cookie_string .= 'advads.set_cookie("timeout_placement_' . $options['output']['placement_id'] . '", 1, '. $timeout .'); ';
			} 

			$content .= $this->build_close_popup_js( $set_cookie_string, $wrapper_id, $close_button_enabled );
		}
		// close button for ad itself
		else if ( isset( $options['layer']['close']['enabled'] ) && $options['layer']['close']['enabled'] && $ad_id ) {
			$close_button_enabled = isset( $options['layer_placement']['close']['enabled'] ) && $options['layer_placement']['close']['enabled'];
			$set_cookie_string = '';
			if ( isset( $options['layer']['close']['timeout_enabled'] ) ) {
				$timeout = isset( $options['layer']['close']['timeout'] ) ? absint( $options['layer']['close']['timeout'] ) : 0;
				if ( ! $timeout ) {
					// Session cookie;
					$timeout = 'null';
				}
				$set_cookie_string .= 'advads.set_cookie("timeout_' . $ad_id . '", 1, '. $timeout .'); ';
			}

			$content .= $this->build_close_popup_js( $set_cookie_string, $wrapper_id, true );
		}

		return $content;
	}

	/**
	 * build js for popup close handling
	 *
	 * @param str $set_cookie_string for setup timeout cookie
	 * @param str $wrapper_id Id of the wrapper.
	 * @param bool $close_button_enabled Whether the close button is enabled.
	 * @return str js for popup close handling
	 */
	private function build_close_popup_js( $set_cookie_string, $wrapper_id, $close_button_enabled ) {
		$script = '<script>( window.advanced_ads_ready || jQuery( document ).ready ).call( null, function() {';

		if ( $this->fancybox_is_enabled ) {
			$script .= "advads_items.close_functions[ '{$wrapper_id}' ] = function() {"
			. "advads.close( '#{$wrapper_id}' );";

			if ( $set_cookie_string ) {
				$script .= $set_cookie_string;
			}

			$script .= '};';
		} else {
			$script .= "advads_items.close_functions[ '{$wrapper_id}' ] = function() {"
				. "advads.close( '#{$wrapper_id}' ); "
				// remove background if there is not visible ads with background
				. "if ( can_remove_background ( '{$wrapper_id}' ) ) { "
				.     'jQuery( ".advads-background" ).remove(); '
				. '}; ';

				if ( $set_cookie_string ) {
					$script .= $set_cookie_string;
				}
			$script .= '};';

			if ( $close_button_enabled ) {
				$prefix  = Advanced_Ads_Plugin::get_instance()->get_frontend_prefix();
				$script .= "jQuery( '#{$wrapper_id}' ).on( 'click', '.${prefix}close-button', function() { "
					. "var close_function = advads_items.close_functions[ '{$wrapper_id}' ];"
					. "if ( typeof close_function === 'function' ) {"
					.     "close_function(); "
					. "}";
				$script .= '});';
			}
		}
		$script .= '});</script>';
		return $script;
	}

	/**
	 * check if placement was closed with a cookie before
	 *
	 * @since 1.2.4
	 * @param int $id placement id
	 * @return bool whether placement can be displayed or not
	 * @return bool false if placement was closed for this user
	 */
	public function placement_can_display( $return, $id = 0 ) {

		// get all placements
		$placements = Advanced_Ads::get_ad_placements_array();

		if ( ! isset( $placements[ $id ]['options']['layer_placement']['close']['enabled'] ) || ! $placements[ $id ]['options']['layer_placement']['close']['enabled'] ) {
			return $return;
		}

		if ( isset( $placements[ $id ]['options']['layer_placement']['close']['timeout_enabled'] ) && $placements[ $id ]['options']['layer_placement']['close']['timeout_enabled'] ) {
			$slug = sanitize_title( $placements[ $id ]['name'] );
			if ( isset( $_COOKIE[ 'timeout_placement_' . $slug ] ) ) {
				return false;
			}
		}

		return $return;
	}

	/**
	 * check if the current ad can be displayed based on minimal and maximum browser width
	 *
	 * @since 1.2.4
	 * @param bool $can_display value as set so far
	 * @param obj $ad the ad object
	 * @return bool false if can’t be displayed, else return $can_display
	 */
	public function can_display( $can_display, $ad = 0 ) {

		$ad_options = $ad->options();

		if ( ! isset( $ad_options['layer']['close']['enabled'] ) || ! $ad_options['layer']['close']['enabled'] ) {
			return $can_display;
		}

		if ( isset( $ad_options['layer']['close']['timeout_enabled'] ) && $ad_options['layer']['close']['timeout_enabled'] ) {
			if ( isset( $_COOKIE[ 'timeout_' . $ad->id ] ) ) {
				return false;
			}
		}

		return $can_display;
	}

	/**
	 * returns the (css) class name for layer ads
	 */
	public static final function get_layer_class(){
	    return Advanced_Ads_Plugin::get_instance()->get_frontend_prefix() . "layer";
	}
}
