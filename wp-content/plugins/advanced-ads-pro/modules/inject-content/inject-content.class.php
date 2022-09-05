<?php

class Advanced_Ads_Pro_Module_Inject_Content {

	public function __construct() {
		// add placement types
		add_filter( 'advanced-ads-placement-types', array( $this, 'add_placement_types' ) );
		// TODO load options
		add_filter( 'the_content', array( $this, 'inject_content' ), 100 );
		// action after ad output is created; used for js injection
		add_filter( 'advanced-ads-ad-output', array( $this, 'after_ad_output' ), 10, 2 );
		// action after group output is created; used for js injection
		add_filter( 'advanced-ads-group-output', array( $this, 'after_group_output' ), 10, 2 );
		// check if content injection is limited for longer texts only
		add_filter( 'advanced-ads-can-inject-into-content', array( $this, 'check_content_length' ), 10, 3 );
		// Allow to prevent injection inside `the_content`.
		add_action( 'advanced-ads-can-inject-into-content', array( $this, 'prevent_injection_the_content' ), 10, 3 );
		// inject ad into footer
		add_action( 'wp_footer', array( $this, 'inject_footer' ), 20 );
		// inject ads into archive pages
		add_action( 'the_post', array( $this, 'inject_loop_post' ), 20, 2 );
		// add ads into AMP archive pages created by the AMP for WP plugin.
		add_action( 'ampforwp_between_loop', array( $this, 'inject_loop_post_amp_for_wp') );

		// support custom hook for content injections
		if( defined( 'ADVANCED_ADS_PRO_CUSTOM_CONTENT_FILTER' ) ){
			// run Advanced Ads content filter
			add_filter( ADVANCED_ADS_PRO_CUSTOM_CONTENT_FILTER, array( Advanced_Ads::get_instance(), 'inject_content' ) );
			// run Advanced Ads Pro content filter
			add_filter( ADVANCED_ADS_PRO_CUSTOM_CONTENT_FILTER, array( $this, 'inject_content' ) );
		}

		add_filter( 'advanced-ads-cache-busting-item', array( $this, 'inject_js_before_cache_busting_output' ), 10, 2 );
		$this->add_skip_paragraph_filters();

		// Check if ads can be displayed by post type.
		add_filter( 'advanced-ads-can-display', array( $this, 'can_display_by_post_type' ), 10, 2 );
		// Check if Verification code & Auto ads ads can be displayed by post type.
		add_filter( 'advanced-ads-can-display-ads-in-header', array( $this, 'can_display_in_header_by_post_type' ), 10 );
		add_action( 'advanced-ads-body-classes', array( $this, 'body_class' ) );
	}

	/**
	 * add new placement types
	 *
	 * @since   1.0.0
	 * @param array $types
	 *
	 * @return array $types
	 */
	public function add_placement_types($types) {
		// ad injection on random position
		$types['post_content_random'] = array(
			'title' => __( 'Random Paragraph', 'advanced-ads-pro' ),
			'description' => __( 'After a random paragraph in the main content.', 'advanced-ads-pro' ),
			'image' => AAP_BASE_URL . 'modules/inject-content/assets/img/content-random.png',
			'order'       => 22,
			'options' => array( 'show_position' => true, 'uses_the_content' => true, 'amp' => true )
		);
		// ad injection above the post headline
		$types['post_above_headline'] = array(
			'title' => __( 'Above Headline', 'advanced-ads-pro' ),
			'description' => __( 'Above the main headline on the page (&lt;h1&gt;).', 'advanced-ads-pro' ),
			'image' => AAP_BASE_URL . 'modules/inject-content/assets/img/content-above-headline.png',
			'order'       => 7,
			'options' => array( 'show_position' => true, 'uses_the_content' => true )
		);
		// ad injection in the middle of a post
		$types['post_content_middle'] = array(
			'title' => __( 'Content Middle', 'advanced-ads-pro' ),
			'description' => __( 'In the middle of the main content based on the number of paragraphs.', 'advanced-ads-pro' ),
			'image' => AAP_BASE_URL . 'modules/inject-content/assets/img/content-middle.png',
			'order' => 23,
			'options' => array( 'show_position' => true, 'uses_the_content' => true, 'amp' => true ),
		);
		// ad injection at a hand selected element in the frontend
		$types['custom_position'] = array(
			'title' => __( 'Custom Position', 'advanced-ads-pro' ),
			'description' => __( 'Attach the ad to any element in the frontend.', 'advanced-ads-pro' ),
			'image' => AAP_BASE_URL . 'modules/inject-content/assets/img/custom-position.png',
			'order'       => 60,
			'options' => array( 'show_position' => true )
		);
		// ad injection at a hand selected element in the frontend
		$types['archive_pages'] = array(
			'title' => __( 'Post Lists', 'advanced-ads-pro' ),
			'description' => __( 'Display the ad between posts on post lists, e.g. home, archives, search etc.', 'advanced-ads-pro' ),
			'image' => AAP_BASE_URL . 'modules/inject-content/assets/img/post-list.png',
			'order'       => 40,
			'options' => array( 'show_position' => true, 'show_lazy_load' => true  )
		);
		return $types;
	}


	/**
	 * injected ad randomly into post content
	 *
	 * @since 1.0.0
	 * @param str $content post content
	 */
	public function inject_content( $content = '' ) {
		global $post;

		$options = Advanced_Ads::get_instance()->options();

		// do not inject in content when on a BuddyPress profile upload page (avatar & cover image).
		if ( ( function_exists( 'bp_is_user_change_avatar' ) && bp_is_user_change_avatar() ) || ( function_exists( 'bp_is_user_change_cover_image' ) && bp_is_user_change_cover_image() ) ) {
			return $content;
		}

		if ( $this->has_many_the_content() ) {
			return $content;
		}

		// Check if ads are disabled in secondary queries.
		if ( ! empty( $options['disabled-ads']['secondary'] ) ) {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				// This function was called by ajax (in secondary query).
				return $content;
			}
			// get out of wp_router_page post type if ads are disabled in secondary queries.
			if ( 'wp_router_page' === get_post_type() ) {
				return $content;
			}
		}

		// No need to inject ads because all tags are stripped from excepts.
		if ( doing_filter( 'get_the_excerpt' ) ) {
			return $content;
		}

		// run only within the loop on single pages of public post types
		$public_post_types = get_post_types( array( 'public' => true, 'publicly_queryable' => true ), 'names', 'or' );

		// make sure that no ad is injected into another ad
		if ( get_post_type() == Advanced_Ads::POST_TYPE_SLUG ){
			return $content;
		}

		// Do not inject on admin pages.
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return $content;
		}

		// check if admin allows injection in all places
		if( ! isset( $options['content-injection-everywhere'] ) ){
		    // check if this is a singular page within the loop or an amp page
		    $is_amp = function_exists( 'advads_is_amp' ) && advads_is_amp();
                    if ( ( ! is_singular( $public_post_types ) && ! is_feed() ) || ( ! $is_amp && ! in_the_loop() ) ) { return $content; }
		}

		$placements = get_option( 'advads-ads-placements', array() );

		if( ! apply_filters( 'advanced-ads-can-inject-into-content', true, $content, $placements )){
			return $content;
		}

		if( is_array( $placements ) ){
			foreach ( $placements as $_placement_id => $_placement ){
				if ( empty( $_placement['item'] ) ) {
				    continue;
				}

				if ( isset($_placement['type'])
					&& in_array( $_placement['type'],
						array('post_content_random',
						    'post_above_headline',
						    'post_content_middle')) ){

					// don’t inject above headline on non-singular pages
					if( 'post_above_headline' === $_placement['type'] && ! is_singular( $public_post_types ) ){
						continue;
					}

					// check if injection is ok for a specific placement id
					if( ! apply_filters( 'advanced-ads-can-inject-into-content-' . $_placement_id, true, $content, $_placement_id )){
						continue;
					}

					$_options = isset( $_placement['options'] ) ? $_placement['options'] : array();
					$_options['placement']['type'] = $_placement['type'];

					switch ( $_placement['type'] ) {
						case 'post_above_headline' :
							$content .= Advanced_Ads_Select::get_instance()->get_ad_by_method( $_placement_id, Advanced_Ads_Select::PLACEMENT, $_options );
						break;
						case 'post_content_middle' :
							$content = Advanced_Ads_Placements::inject_in_content( $_placement_id, $_options, $content );
						break;
						case 'post_content_random' :
							if ( $this->content_random_use_js( $_options ) ) {
								$content .= Advanced_Ads_Select::get_instance()->get_ad_by_method( $_placement_id, Advanced_Ads_Select::PLACEMENT, $_options );
							} else {
								$content = Advanced_Ads_Placements::inject_in_content( $_placement_id, $_options, $content );
							}
						break;
					}
				}
			}
		}

		return $content;
	}

	/**
	 * inject ad into footer
	 *
	 * @since 1.1.2
	 */
	public function inject_footer(){
		$placements = get_option( 'advads-ads-placements', array() );
		if( is_array( $placements ) ){
			foreach ( $placements as $_placement_id => $_placement ){
				if ( isset($_placement['type']) && 'custom_position' == $_placement['type'] ){
					// Do not inject on AMP pages.
					if ( function_exists( 'advads_is_amp' ) && advads_is_amp() ) { continue; }

					$_options = isset( $_placement['options'] ) ? $_placement['options'] : array();
					$_options['placement']['type'] = $_placement['type'];
					echo Advanced_Ads_Select::get_instance()->get_ad_by_method( $_placement_id, Advanced_Ads_Select::PLACEMENT, $_options );
				}
			}
		}
	}

	/**
	 * inject ad output and js code
	 *
	 * @since 1.1
	 * @param str $content ad content
	 * @param obj $ad ad object
	 */
	public function after_ad_output( $content = '', Advanced_Ads_Ad $ad ) {
		if ( isset( $ad->args['previous_method'] ) && Advanced_Ads_Select::GROUP === $ad->args['previous_method'] ) {
			return $content;
		}

		if ( ! isset( $ad->args['cache_busting_elementid'] ) && isset( $ad->wrapper['id'] ) ) {
			$content .= $this->get_output_js( $ad->wrapper['id'], $ad->args );
		}

		return $content;
	}

	/**
	 * inject js code after group output
	 *
	 * @param str $output_string final group output
	 * @param obj $group Advanced_Ads_Group
	 */
	public function after_group_output( $output_string, Advanced_Ads_Group $group ) {
		if ( $output_string ) {

			if ( ! isset( $group->ad_args['cache_busting_elementid'] ) ) {
				$wrapper_id = Advanced_Ads_Pro_Utils::generate_wrapper_id();

				if ( $js_output = $this->get_output_js( $wrapper_id, $group->ad_args ) ) {
					$output_string = '<div id="' . $wrapper_id . '">' . $output_string . '</div>' . $js_output;
				}
			}
		}

		return $output_string;
	}

	/**
	 * get js to append after ad/group output
	 *
	 * @return string
	 */
	private function get_output_js( $wrapper_id, $args ) {
		$content = '';
		// Do not inject js on AMP pages.
		if ( function_exists( 'advads_is_amp' ) && advads_is_amp() ) { return $content; }

		// Group refresh: do not move if the top level wrapper was moved earlier.
		if ( isset( $args['group_refresh'] ) && ! $args['group_refresh']['is_top_level'] ) {
			return $content;
		}

		// Move only the most outer group wrapper.
		$top_level = ! isset( $args['previous_method'] ) || 'placement' === $args['previous_method'];
		if ( ! $top_level ) {
			return $content;
		}

		if ( isset ( $args['placement']['type'] ) ) {
			switch( $args['placement']['type'] ){
				case 'post_content_random' :
					if ( ! $this->content_random_use_js( $args ) ) {
						return '';
					}
					$paragraphs_selector = $this->get_paragraph_selector( $args );
					$content .= 'var advads_content_p = jQuery("#'. $wrapper_id .'")' . $paragraphs_selector . ';'
						. 'var advads_content_random_p = advads_content_p.eq( Math.round(Math.random() * ( advads_content_p.length - 1) ) );'
						. 'if( advads_content_random_p.length ) { advads.move("#'. $wrapper_id .'", advads_content_random_p, { method: "insertAfter" }); }';
				break;
				case 'post_above_headline' :
					$content .= 'advads.move("#'. $wrapper_id .'", "h1", { method: "insertBefore" });';
				break;
				case 'custom_position' :
					// By element Selector.
					if ( ! isset( $args['inject_by'] ) || $args['inject_by'] === 'pro_custom_element'  ) {
						$target = isset( $args['pro_custom_element'] ) ? $args['pro_custom_element'] : '';
						$position = isset( $args['pro_custom_position'] ) ? $args['pro_custom_position'] : 'insertBefore';
					// By HTML container.
					} else {
						$target = isset( $args['container_id'] ) ? $args['container_id'] : '';
						$position = 'appendTo';
					}
					$options[] = 'method: "'. $position . '"';
					// check if can be moved into hidden elements
					if( defined( 'ADVANCED_ADS_PRO_CUSTOM_POSITION_MOVE_INTO_HIDDEN') ){
						$options[] = 'moveintohidden: "true"';
					}
					$content .= 'advads.move("#'. $wrapper_id .'", "'. $target .'", { '. implode( ', ', $options ) .' });';
				break;
			}

			if ( $content ) {
				if ( ! empty( $args['cache_busting_elementid'] ) ) {
					// Document is ready. Do not use another 'ready' block so that the wrapper is moved before executing js in ad content.
					$content = '<script>' . $content . '</script>';
				} else  {
					$content = '<script>( window.advanced_ads_ready || jQuery( document ).ready ).call( null, function() {' . $content . '});</script>';
				}
			}

		}

		return $content;
	}

	/**
	 * get paragraph selector for js depending on cache busting settings
	 *
	 * @return str $paragraph_selector
	 * @since 1.2.3
	 */
	private function get_paragraph_selector( $args ) {

		// check if level limitation is disabled
		$plugin_options = Advanced_Ads_Plugin::get_instance()->options();
		$content_injection_level_disabled = isset( $plugin_options['content-injection-level-disabled'] );

		/**
		 * find paragraphs
		 *  which are not within tables
		 *  which are not within blockquotes
		 *  which are not empty
		 *  which are not within an image caption
		 *
		 * depending on "Disable injection limitation" setting,
		 *  either inject into all p tags, including subordinated
		 *  or only direct and preceding siblings
		 */
		if( $content_injection_level_disabled ){
			$paragraphs_selector = '.parent().find("p:not(table p):not(blockquote p):not(div.wp-caption p)").filter(function(){return jQuery.trim(this.innerHTML)!==""})';
		} else {
			// Do not use 'prevAll' because it returns elements in reverse order.
			$paragraphs_selector = '.parent().children("p:not(table p):not(blockquote p):not(div.wp-caption p)").filter(function(){return jQuery.trim(this.innerHTML)!==""})';
		}

		// TODO: Deprecated.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && ! isset( $args['cache_busting_elementid'] ) ) {
			$paragraphs_selector = '.parent()' . $paragraphs_selector;
		}

		return apply_filters( 'advanced-ads-pro-inject-content-selector', $paragraphs_selector );
	}

	/**
	 * Check content length for injecting ads into the post content
	 *
	 * @param bool   $inject     whether to inject or not
	 * @param string $content    post content
	 * @param array  $placements array with all placements
	 *
	 * @return bool true, if injection is ok
	 */
	public function check_content_length( $inject = true, $content = '', $placements = array() ){
		if ( ! $inject ) {
			return false;
		}

		if ( defined( 'ADVADS_CURRENT_CONTENT_LENGTH' ) ) {
			return $inject;
		}

	    // content injection placements
	    $cj_placements = array( 'post_top', 'post_bottom', 'post_content', 'post_content_random', 'post_content_middle' );

	    // find out of content injection placements are defined at all
	    $has_content_placements = false;
	    foreach( $placements as $_placement_id => $_placement ){
		    if( isset( $_placement['type'] ) && in_array( $_placement['type'], $cj_placements ) ){
			    $has_content_placements = true;

			    // register filter for placement specific length check
			    add_filter( 'advanced-ads-can-inject-into-content-' . $_placement_id, array( $this, 'check_placement_minimum_length' ), 10, 3 );
			}
		}

		if ( $has_content_placements ) {
			// Remove all HTML tags and comments and count spaces in content.
			$length = (int) preg_match_all( '/\s+/', wp_strip_all_tags( $content ) );
			define( 'ADVADS_CURRENT_CONTENT_LENGTH', $length );
		}

	    return $inject;
	}

	/**
	 * Allow to prevent injections inside `the_content`.
	 *
	 * @param bool $inject whether to inject or not
	 * @param str $content post content
	 * @param arr $placements array with all placements
	 * @return bool true, if injection is ok
	 */
	public function prevent_injection_the_content( $inject = true, $content = '', $placements = array() ) {
		if ( ! $inject ) {
			return false;
		}

		global $post;
		if( empty( $post->ID ) ){
		    return true;
		}

		$post_ad_options = get_post_meta( $post->ID, '_advads_ad_settings', true );

		return empty( $post_ad_options['disable_the_content'] );
	}

	/**
	 * check content length setting of content injection placements
	 *
	 * @since 1.2.3
	 * @param bol $return whether to inject or not
	 * @param str $content post content
	 * @param str $placement_id id of the placement
	 * @return bool false if placement should not show up in current article
	 */
	public function check_placement_minimum_length( $return, $content = '', $placement_id ){
		if ( ! $return ) {
			return false;
		}

	    // get all placements
	    $placements = Advanced_Ads::get_ad_placements_array();

	    if( ! isset( $placements[ $placement_id ]['options']['pro_minimum_length'] ) || ! $placements[ $placement_id ]['options']['pro_minimum_length'] ){
		    return $return;
	    }

	    if( defined('ADVADS_CURRENT_CONTENT_LENGTH') && ADVADS_CURRENT_CONTENT_LENGTH < absint( $placements[ $placement_id ]['options']['pro_minimum_length'] ) ){
		    return false;
	    }

	    return $return;
	}

	/**
	 * echo ad before/after posts in loops on archive pages
	 *
	 * @since 1.2.1
	 * @param arr $post post object
	 * @param WP_Query $wp_query query object
	 */
	public function inject_loop_post( $post, $wp_query = null ) {

		$is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

		if ( ! $wp_query instanceof WP_Query || is_feed() || ( is_admin() && ! $is_ajax ) ) {
			return;
		}

		$plugin_options = Advanced_Ads_Plugin::get_instance()->options();
		// only inject on AJAX requests when Secondary Query option is enabled
		if ( ! empty( $plugin_options['disabled-ads']['secondary'] ) && $is_ajax ) {
			return;
		}

		if( ! isset( $wp_query->current_post )) {
			return;
		};

		// don’t inject into main query on single pages.
		if( $wp_query->is_main_query() && is_single() ){
			return;
		}

		$curr_index = $wp_query->current_post + 1; // normalize index

		// 'wp_reset_postdata()' does 'the_post' action.
		// handle the situation when wp_reset_postdata() is used after secondary query inside main query.
		static $handled_indexes = array();
		if ( $wp_query->is_main_query() ) {
			if ( in_array( $curr_index, $handled_indexes ) ) {
				return;
			}
			$handled_indexes[] = $curr_index;
		}

		$placements = get_option( 'advads-ads-placements', array() );
		if( is_array( $placements ) ){
			foreach ( $placements as $_placement_id => $_placement ){
				if ( empty($_placement['item']) ) {
					continue;
				}

				if ( isset($_placement['type']) && 'archive_pages' === $_placement['type'] ){
					$_options = isset( $_placement['options'] ) ? $_placement['options'] : array();

					if ( empty( $_options['in_any_loop'] )
						&& ( $wp_query->is_singular() || ! $wp_query->in_the_loop || ! $wp_query->is_main_query() ) ) {
						continue;
					}

					// check if the loop is outside of wp_head, but only on non-AJAX calls.
					if  ( ! is_admin() && ! did_action( 'wp_head' ) ) {
						continue;
					}

					// don’t attach if not container attachment selected
					/*if( ! isset( $_options['pro_archive_pages_type'] ) || 'container' !== $_options['pro_archive_pages_type'] ){
						continue;
					}*/

					if( isset( $_options['pro_archive_pages_index'] ) ){
						$ad_index = absint( $_options['pro_archive_pages_index'] );
						if( $ad_index === $curr_index ){
							// todo: leave a comment about the use of the next line. Might be needed to submit placement information to options.
							$_options['placement']['type'] = $_placement['type'];
							echo Advanced_Ads_Select::get_instance()->get_ad_by_method( $_placement_id, Advanced_Ads_Select::PLACEMENT, $_options );
						}
					}
				}
			}
		}
	}


	/**
	 * Insert an ad in the loop for archive pages created by AMP for WP (https://wordpress.org/plugins/accelerated-mobile-pages/)
	 *
	 * We can ommit the checks in inject_loop_post() here because the ampforwp_between_loop hook should provide the right position.
	 *
	 * @param int $count index of the current position in the loop.
	 */
	public function inject_loop_post_amp_for_wp( $count ) {

		$placements = get_option( 'advads-ads-placements', array() );
		if ( is_array( $placements ) ) {
			foreach ( $placements as $_placement_id => $_placement ) {
				if ( empty( $_placement['item'] ) ) {
					continue;
				}

				if ( isset( $_placement['type'] ) && 'archive_pages' === $_placement['type'] ) {
					$_options = isset( $_placement['options'] ) ? $_placement['options'] : array();

					if ( isset( $_options['pro_archive_pages_index'] ) ) {
						$ad_index = absint( $_options['pro_archive_pages_index'] );
						// We need to reduce our index by one to match how AMP for WP counts the index.
						if ( ( $ad_index - 1 ) === $count ) {
							// Todo: leave a comment about the use of the next line. Might be needed to submit placement information to options.
							$_options['placement']['type'] = $_placement['type'];
							// phpcs:ignore
							echo Advanced_Ads_Select::get_instance()->get_ad_by_method( $_placement_id, Advanced_Ads_Select::PLACEMENT, $_options );
						}
					}
				}
			}
		}
	}

	/**
	 * Find the calls to `the_content` inside functions hooked to `the_content`.
	 *
	 * @return bool
	 */
	public function has_many_the_content() {
		global $wp_current_filter;
		if ( count( array_keys( $wp_current_filter, 'the_content', true ) ) > 1 ) {
			// More then one `the_content` in the stack.
			return true;
		}
		return false;
	}

	/**
	 * Check whether or not to use JS to position the placement.
	 *
	 * @return bool
	 */
	private function content_random_use_js( $placement_options ) {
		if ( function_exists( 'advads_is_amp' ) && advads_is_amp() ) {
			return false;
		}

		if ( ! Advanced_Ads_Pro_Module_Cache_Busting::is_enabled() ) {
			return false;
		}

		if ( isset( $placement_options['cache-busting'] ) && 'off' === $placement_options['cache-busting']
			// Check if we did not switch to `off` from `auto`.
			&& ! isset( $placement_options['cache-busting-orig'] ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Inject a script to output before cache busting output.
	 * The script moves the empty wrapper because some ad networks do not allow to move ads inserted to the DOM.
	 *
	 * @param array $r Cache busting item.
	 * @param array $request Request info.
	 * @return array $r Cache busting item.
	 */
	function inject_js_before_cache_busting_output( $r, $request ) {
		if ( ! isset( $request['method'] ) || 'placement' !== $request['method']
			|| empty( $request['args']['cache_busting_elementid'] ) ) {
			return $r;
		}

		$el_id = $request['args']['cache_busting_elementid'];
		$r['inject_before'][] = $this->get_output_js( $el_id, $request['args'] );
		return $r;
	}

	/**
	 * Add filters to skip paragraph.
	 */
	private function add_skip_paragraph_filters() {
		$placements = Advanced_Ads::get_ad_placements_array();

		foreach ( $placements as $placement_id => $placement ) {
			if ( ! empty( $placement['options']['words_between_repeats'] ) ) {
				add_filter( 'advanced-ads-can-inject-into-content-' . $placement_id, array( $this, 'maybe_skip_content_placement' ), 10, 3 );
			}
		}
	}

	/**
	 * Check if the "Before/After Content" placement has enough words before.
	 *
	 * @param bool $return Whether to inject or not.
	 * @param str  $content Post content.
	 * @param str  $placement_id Placement id.
	 * @return bool
	 */
	public function maybe_skip_content_placement( $return, $content = '', $placement_id ) {
		if ( ! $return ) {
			return false;
		}

		$placements = Advanced_Ads::get_ad_placements_array();

		if ( empty( $placements[ $placement_id ]['type'] )
			|| ! in_array( $placements[ $placement_id ]['type'], array( 'post_top', 'post_bottom' ) ) ) {
			return $return;
		}

		if ( ! empty( $placements[ $placement_id ]['options']['words_between_repeats'] ) ) {
			$options['words_between_repeats'] = absint( $placements[ $placement_id ]['options']['words_between_repeats'] );

			$offset_shifter = Advanced_Ads_Pro_Offset_Shifter::from_html( $content, $options );

			if ( 'post_top' === $placements[ $placement_id ]['type'] ) {
				return $offset_shifter->can_inject_before_content_placement();
			} else {
				return $offset_shifter->can_inject_after_content_placement();
			}
		}
		return $return;
	}

	/**
	 * Check if the ad should be displayed based on post type.
	 *
	 * @param bool            $can_display True if the ad should be displayed, false otherwise.
	 * @param Advanced_Ads_Ad $ad Advanced_Ads_Ad object.
	 * @return bool True if the ad should be displayed, false otherwise.
	 */
	public function can_display_by_post_type( $can_display, Advanced_Ads_Ad $ad ) {
		if ( ! $can_display ) {
			return false;
		}

		$ad_options = $ad->options();

		if ( ! empty( $ad_options['post']['post_type'] )
			&& $this->post_type_disabled( $ad_options['post']['post_type'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Return true if the ad can be displayed in the header of the page depending on the current post type.
	 *
	 * @param bool $can_display if the ad can already be displayed.
	 *
	 * @return bool
	 */
	public function can_display_in_header_by_post_type( $can_display ) {
		if ( ! $can_display ) {
			return false;
		}


		$post_type = $this->get_current_post_type();
		if ( $this->post_type_disabled( $post_type ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get current post type.
	 *
	 * @return bool|string False on failure or post type on success.
	 */
	private function get_current_post_type() {
		global $wp_the_query, $post;
		// If currently on a single site, use the main query information just in case a custom query is broken.
		if ( isset( $wp_the_query->post->post_type ) && $wp_the_query->is_single() ) {
			return $wp_the_query->post->post_type;
		} elseif ( isset( $post->post_type ) ) {
			return $post->post_type;
		}
		return false;
	}

	/**
	 * Check if post type disabled.
	 *
	 * @param string $post_type Post type.
	 * @return bool
	 */
	private function post_type_disabled( $post_type ) {
		$options = Advanced_Ads_Pro::get_instance()->get_options();

		if ( ! empty( $options['general']['disable-by-post-types'] )
			&& is_array( $options['general']['disable-by-post-types'] )
			&& in_array( $post_type, $options['general']['disable-by-post-types'], true ) ) {
			return true;
		}

		return false;
	}


	/**
	 * Add classes to the `body` tag.
	 *
	 * @param string[] $aa_classes Array of existing class names.
	 * @return string[] $aa_classes Array of existing and new class names.
	 */
	public function body_class( $aa_classes ) {
		$post_type = $this->get_current_post_type();

		if ( $this->post_type_disabled( $post_type ) ) {
			$aa_classes[] = 'aa-disabled-post-type';
		}

		return $aa_classes;
	}
}
