<?php
// -TODO should use a constant for option key as it is shared at multiple positions
class Advanced_Ads_Pro_Module_Cache_Busting {
    /** @#+
     * Cache-busting option values.
     *
     * @var string
     */
    const OPTION_ON = 'on';
    const OPTION_OFF = 'off';
    const OPTION_AUTO = 'auto';
    // Ignore any cache-busting, even for no placement.
    const OPTION_IGNORE = 'ignore';
    /** @#- */

	/**
	 * Instance of this class.
	 *
	 * @var Advanced_Ads_Pro_Module_Cache_Busting
	 */
	private static $instance = null;

    /**
     * Internal global ad block count.
     *
     * @var integer
     */
    protected static $adOffset = 0;

    /**
     * Module options
     *
     * @var array
     */
    protected $options = array();

    /**
     * Context-switch used for ad override.
     *
     * @var boolean
     */
    protected $isHead = true;

    /**
     * True if ajax, false otherwise.
     *
     * @var boolean
     */
    public $is_ajax;

    /**
     * Ads, Groups, Placements for JavaScript.
     *
     * @var arrays
     */
    protected $passive_cache_busting_ads = array();
    protected $passive_cache_busting_groups = array();
    protected $passive_cache_busting_placements = array();

    /**
     * Simple js items injected using js.
     * Their conditions are not checked for every visitor of a cached page.
     *
     * @var arrays
     */
    protected $js_items = array();

    /**
     * Whether we are collecting simple js items.
     *
     * @var bool
     */
    protected $collecting_js_items = false;

    /**
     * Info about simple items for tracking purpose.
     *
     * @var array
     */
    protected $has_js_items = array();

    /**
     * Ads loaded without cache-busting
     *
     * @var array
     */
    protected $has_ads = array();

    /**
     *  Queries for ads, that need to be loaded with AJAX
     *
     * @var array
     */
    protected static $ajax_queries = array();

	/**
	 * Each AJAX query is merged into this array. A query may replace but not remove an item in this array.
	 *
	 * @var array
	 */
	private $ajax_default_args = array (
		'lazy_load' => 'disabled',
		'cache-busting' => 'on',
		'ad_label' => 'default',
		'placement_position' => '',
		'item_adblocker' => '',
		'pro_minimum_length' => '0',
		'words_between_repeats' => '0',
		'previous_method' => null,
		'previous_id' => null,
		'wp_the_query' => array (
			'term_id' => '',
			'taxonomy' => '',
			'is_main_query' => true,
			'is_rest_api' => false,
			'page' => 1,
			'numpages' => 1,
			'is_archive' => false,
			'is_search' => false,
			'is_home' => false,
			'is_404' => false,
			'is_attachment' => false,
			'is_singular' => true,
			'is_front_page' => false,
			'is_feed' => false,
		),
		'global_output' => true,
	);

	/**
	 * One argument in this array may belong to several AJAX requests.
	 *
	 * @var array
	 */
	private $ajax_queries_args = array();

    private function __construct() {
        // Load options (and only execute when enabled).
        $options = Advanced_Ads_Pro::get_instance()->get_options();

        if ( isset( $options['cache-busting'] ) ) {
            $this->options = $options['cache-busting'];
        }

        $this->cache_busting_module_enabled = isset( $this->options['enabled'] ) && $this->options['enabled'];
		if ( ! $this->should_init_cb() ) {
			add_action('wp_enqueue_scripts', [$this, 'check_for_tcf_privacy']);
			return;
		}

		$this->lazy_load_module_enabled = ! empty( $options['lazy-load']['enabled'] );
		$this->lazy_load_module_offset = ! empty( $options['lazy-load']['offset'] ) ? absint( $options['lazy-load']['offset'] ) : 0;

        $this->is_ajax = ( defined( 'DOING_AJAX' ) && DOING_AJAX )
            // An AJAX request but not to `/admin-ajax.php`.
            || ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && 'XMLHttpRequest' === $_SERVER['HTTP_X_REQUESTED_WITH'] );


        if ( ! $this->is_ajax && ! is_admin() ) {
            add_action( 'wp', array( $this, 'init_fronend' ) );
            // load Advads Tracking header scripts
            add_filter( 'advanced-ads-tracking-load-header-scripts', array( $this, 'load_tracking_scripts' ), 10, 1 );
        } else {
            // only execute when enabled
            if ( $this->cache_busting_module_enabled ) {
                new Advanced_Ads_Pro_Module_Cache_Busting_Admin_UI();
            }
        }
        $this->fallback_method = ( ! isset( $this->options['default_fallback_method'] ) || $this->options['default_fallback_method'] === 'ajax' ) ? 'ajax' : 'off';
        if ( 'ajax' === $this->fallback_method ) {
            $this->server_info = new Advanced_Ads_Pro_Cache_Busting_Server_Info( $this, $this->options );
        }

        add_filter( 'advanced-ads-ad-output-debug-content', array( $this, 'add_debug_content' ), 10, 2 );
		add_filter( 'advanced-ads-ajax-ad-select-arguments', array( $this, 'add_default_ajax_arguments' ), 10, 2 );
    }

	/**
	 * Check if cache-busting should be initialized.
	 *
	 * Even when the module is disabled, we partially (i.e .conditions are not checked for every visitor of a cached page)
	 * use cache-busting functionality to deliver Custom Position placements so that they do not appear in the footer when
	 * selectors do not exist.
	 *
	 * @see self::add_simple_js_item
	 */
	private function should_init_cb() {
		if ( $this->cache_busting_module_enabled ) {
			return true;
		}
		$placements = Advanced_Ads::get_ad_placements_array();
		foreach ( $placements as $placement ) {
			if ( isset( $placement['type'] ) && $placement['type'] === 'custom_position' && ! empty( $placement['item'] ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @return obj A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

    /**
     *  Init cache-busting frontend after the `parse_query` hook.
     *  Not ajax, not admin.
     */
    public function init_fronend() {
        global $wp_the_query;

        if ( apply_filters( 'advanced-ads-pro-cb-frontend-disable', false )
            // Disable cache-busting on AMP pages.
            || ( function_exists( 'advads_is_amp' ) && advads_is_amp() )
            || $wp_the_query->is_feed()
        ) { return; }


        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_head', array( $this, 'watch_wp_head'), PHP_INT_MAX );
        add_filter( 'advanced-ads-ad-output', array( $this, 'watch_ad_output' ), 100, 2 );
        add_filter( 'advanced-ads-group-output', array( $this, 'watch_group_output' ), 100, 2 );
        // override output based on the Advanced_Ads_Ad object conditions
        add_filter( 'advanced-ads-ad-select-override-by-ad', array( $this, 'override_ad_select_by_ad' ), 10, 3 );
        // override output based on the Advanced_Ads_Group object conditions
        add_filter( 'advanced-ads-ad-select-override-by-group', array( $this, 'override_ad_select_by_group' ), 10, 4 );
        add_action( 'wp_footer', array( $this, 'passive_cache_busting_output' ), 21 );
        add_filter( 'advanced-ads-can-display', array( $this, 'can_display_by_display_limit' ), 10, 3 );

        if ( ! $this->cache_busting_module_enabled ) {
            return;
        }
        add_filter( 'advanced-ads-ad-select-args', array( $this, 'override_ad_select' ), 100 );
        add_filter( 'advanced-ads-ad-select-args', array( $this, 'disable_global_output' ), 101 );
        add_action( 'advanced-ads-can-display-placement', array( $this, 'placement_can_display' ), 12, 2 );
    }

    /**
     * Output passive cache-busting array
     */
    public function passive_cache_busting_output() {
        // if ( true === WP_DEBUG ) {
        //     echo '<pre>' . htmlentities( print_r( $this->passive_cache_busting_placements, true ) ) . '</pre>';
        // }

        $arrays = array(
            'window.advads_placement_tests' => Advanced_Ads_Pro_Placement_Tests::get_instance()->get_placement_tests_js( false ),
            'window.advads_passive_ads' => $this->passive_cache_busting_ads,
            'window.advads_passive_groups' => $this->passive_cache_busting_groups,
            'window.advads_passive_placements' => $this->passive_cache_busting_placements,
            'window.advads_ajax_queries' => self::$ajax_queries,
            'window.advads_has_ads' => $this->has_ads,
            'window.advads_js_items' => $this->js_items,
            'window.advads_ajax_queries_args' => $this->ajax_queries_args,
        );

        $content = '';
        foreach ( $arrays as $name => $array ) {
            if ( $array ) {
                $has_data = true;
                $content .= $name . ' = ' . json_encode( $array ) . ";\n";
            }
        }

        if ( ! $content ) {
            return;
        }

        $content = '<script>'
        . $content
        . '( window.advanced_ads_ready || jQuery( document ).ready ).call( null, function() {'
        .     'if ( !window.advanced_ads_pro ) {'
        .         'console.log("Advanced Ads Pro: cache-busting can not be initialized");'
        .     '} '
        . '});'
        . '</script>';

        if ( class_exists( 'Advanced_Ads_Utils' ) && method_exists( 'Advanced_Ads_Utils', 'get_inline_asset' ) ) {
            $content = Advanced_Ads_Utils::get_inline_asset( $content );
        }
        echo $content;
    }

    public function enqueue_scripts() {
	    // Include in footer to prevent conflict when Autoptimize and NextGen Gallery are used at the same time.
	    $uri_rel_path = AAP_BASE_URL . 'assets/js/';
	    $dependencies = array( 'jquery' );

	    // If the privacy module is active, add advanced-js as a dependency.
	    if ( ! empty( Advanced_Ads_Privacy::get_instance()->options()['enabled'] ) ) {
		    $dependencies[] = ADVADS_SLUG . '-advanced-js';
	    }

        if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
            wp_register_script( 'krux/postscribe', $uri_rel_path . 'postscribe.js', array(), '2.0.8', true );
            wp_register_script( 'advanced-ads-pro/cache_busting', $uri_rel_path . 'base.js', array_merge( $dependencies, array( 'krux/postscribe' ) ), AAP_VERSION, true );
        } else {
            // minified
            wp_register_script( 'advanced-ads-pro/cache_busting', $uri_rel_path . 'base.min.js', $dependencies, AAP_VERSION, true );
        }

		$info = array(
			'ajax_url'                 => admin_url( 'admin-ajax.php' ),
			'lazy_load_module_enabled' => $this->lazy_load_module_enabled,
			'lazy_load'                => array(
				'default_offset' => $this->lazy_load_module_offset,
				'offsets'        => apply_filters( 'advanced-ads-lazy-load-placement-offsets', array() ),
			),
			'moveintohidden'           => defined( 'ADVANCED_ADS_PRO_CUSTOM_POSITION_MOVE_INTO_HIDDEN' ),
			'wp_timezone_offset'       => Advanced_Ads_Utils::get_wp_timezone()->getOffset(date_create()),
		);
        if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
            $current_lang = apply_filters( 'wpml_current_language', null );
            $info['ajax_url'] = add_query_arg( 'wpml_lang', $current_lang, $info['ajax_url'] );
        }

        wp_localize_script( 'advanced-ads-pro/cache_busting', 'advanced_ads_pro_ajax_object', $info );

        wp_enqueue_script( 'advanced-ads-pro/cache_busting' );
    }

    /**
     * Provide current_ad propery to client.
     *
     * @param string          $content
     * @param Advanced_Ads_Ad $ad
     *
     * @return string
     */
    public function watch_ad_output( $content, $ad = null ) {
        if ( isset( $ad ) && $ad instanceof Advanced_Ads_Ad ) {
            // build content (arguments are: id, method, title)
            if ( ! empty( $ad->global_output ) ) {
                $this->has_ads[] = array( "$ad->id", 'ad', $ad->title, 'off' );
            }
            if ( $this->collecting_js_items ) {
                $this->has_js_items[] = array( 'id' => $ad->id, 'type' => 'ad', 'title' => $ad->title, 'blog_id' => get_current_blog_id() );
            }
        }

        return $content;
    }

    /**
     * Provide current group propery to client.
     *
     * @param string $content
     * @param Advanced_Ads_Group $group
     *
     * @return string
     */
    public function watch_group_output( $content, Advanced_Ads_Group $group ) {
        if ( $this->collecting_js_items ) {
            $this->has_js_items[] = array( 'id' => $group->id, 'type' => 'group', 'title' => $group->id );
        }

        return $content;
    }

    /**
     * Turn off head optimisation.
     */
    public function watch_wp_head() {
        $this->isHead = false;
    }

    /**
     * Replace ad content with placeholder.
     *
     * @param array $arguments
     *
     * @return array
     */
    public function override_ad_select( $arguments ) {
        // placements and not Feed only
        $not_feed = empty( $arguments['wp_the_query']['is_feed'] );
        if ( $arguments['method'] === Advanced_Ads_Select::PLACEMENT && $not_feed ) {
            $placements = Advanced_Ads::get_ad_placements_array();
            if ( empty( $placements[ $arguments['id'] ]['item'] ) || ! isset( $placements[ $arguments['id'] ]['type'] ) ) {
                // placement was created but no item was selected in dropdown
                unset( $arguments['override'] );
                return $arguments;
            }

            $arguments['placement_type'] = $placements[ $arguments['id'] ]['type'];
            $options =  isset( $placements[ $arguments['id'] ]['options'] ) ? (array) $placements[ $arguments['id'] ]['options'] : array();

            foreach ( $options as $_k => $_v ) {
                if ( ! isset( $arguments[ $_k ] ) ) {
                    $arguments[ $_k ] = $_v;
                }
            }

            $query = self::build_js_query( $arguments );

            // allow to disable feature
            if ( $this->can_override( $query ) ) {
                $arguments['override'] = $this->get_override_content( $query );
            }
        }

        return $arguments;
    }

    /**
     * Disable global output for cache-busting.
	 * We neither track ads nor show them in the "Ads" section of Admin Bar until we show them to the user.
     *
     * @param array $arguments
     * @return array $arguments
     */
    public function disable_global_output( $arguments ) {
		if ( isset( $arguments['global_output'] ) ) {
			return $arguments;
		}

		if (
			// Custom position.
			( isset( $arguments['placement_type'] ) && $arguments['placement_type'] === 'custom_position' )
			// Cache Busting "ajax" or "auto".
			|| ( isset( $arguments['placement_type'] ) && ( ! isset( $arguments['cache-busting'] ) || $arguments['cache-busting'] !== self::OPTION_OFF ) )
			// Force passive cache-busting.
			|| ( ! isset( $arguments['placement_type'] ) && ! empty( $this->options['passive_all'] ) )
		) {
			$arguments['global_output'] = false;
			return $arguments;
		}

		$arguments['global_output'] = true;
		return  $arguments;
    }

	/**
	 * return ad, prepared for js handler if the conditions are met
	 *
	 * @param string $overriden_ad ad content to override
	 * @param obj $ad Advanced_Ads_Ad
	 * @param array $args argument passed to the 'get_ad_by_id' function
	 * @return string ad content prepared for js handler if the conditions are met 
	 */
	public function override_ad_select_by_ad( $overriden_ad, Advanced_Ads_Ad $ad, $args ) {
        if ( ! $this->can_override_passive( $args ) ) {
            return $overriden_ad;
        }

        if ( $this->cache_busting_module_enabled ) {
            // Cache busting 'auto'.
            $overriden_ad = $this->cache_busting_auto_for_ad( $overriden_ad, $ad, $args );
        }

        if ( false === $overriden_ad ) {
            // The cache-busting module is disabled or the 'off' fallback has been aplied.
            $overriden_ad = $this->get_simple_js_ad( $overriden_ad, $ad, $args );
        }

        return $overriden_ad;
    }

    /**
     * return group, prepared for js handler if the conditions are met
     *
     * @param string $overriden_group group content to override
     * @param obj $adgroup Advanced_Ads_Group
     * @param array/null $ordered_ad_ids ordered ids of the ads that belong to the group
     * @param array $args argument passed to the 'get_ad_by_group' function
     * @return string/false group content prepared for js handler if the conditions are met
     */
    public function override_ad_select_by_group( $overriden_group, Advanced_Ads_Group $adgroup, $ordered_ad_ids, $args ) {
        if ( ! $this->can_override_passive( $args ) ) {
            return $overriden_group;
        }

        if ( $this->cache_busting_module_enabled ) {
            // Cache busting 'auto'.
            $overriden_group = $this->cache_busting_auto_for_group( $overriden_group, $adgroup, $ordered_ad_ids, $args );
        }

        if ( false === $overriden_group ) {
            // The cache-busting module is disabled or the 'off' fallback has been aplied.
            $overriden_group = $this->get_simple_js_group( $overriden_group, $adgroup, $ordered_ad_ids, $args );
        }

        return $overriden_group;
    }


    public function cache_busting_auto_for_ad( $overriden_ad, Advanced_Ads_Ad $ad, $args ) {
        //if it was requested by placement; if cache-busting option does not exists yet, or exist and = 'auto'
        $cache_busting_auto = isset( $args['placement_type'] ) && ( ! isset( $args['cache-busting'] ) || $args['cache-busting'] === self::OPTION_AUTO );
        $cache_busting_off = isset( $args['cache-busting'] ) && $args['cache-busting'] === self::OPTION_OFF;
        $prev_is_placement = isset( $args['previous_method'] ) && $args['previous_method'] === 'placement' && isset( $args['previous_id'] );
        $test_id = isset( $args['test_id'] ) ? $args['test_id'] : null;
        $is_passive_all = ! empty( $this->options['passive_all'] );

        if ( $cache_busting_auto  && ! $this->is_passive_method_used() ) { // ajax method
            // ad was requested by group `placement->group->ad` or `group->ad`
            if ( isset( $args['previous_method'] ) && $args['previous_method'] === 'group' && isset( $args['previous_id'] ) ) {
                return $ad;
            }
            $ad->args['cache-busting'] = self::OPTION_ON;
            $ad->args['cache-busting-orig'] = self::OPTION_AUTO;
            $overriden_ad = $this->get_overridden_ajax_ad( $ad, $args );

            if ( false === $overriden_ad ) {
                // static and not test
                if ( isset( $args['output']['placement_id'] ) ) {
                    if ( ! $this->placement_can_display_not_passive( $args['output']['placement_id'] ) ) { return ''; }
                    $this->add_placement_to_current_ads( $args['output']['placement_id'] );
                }
            }
            return $overriden_ad;
        }
        elseif ( ! $cache_busting_off && ( $cache_busting_auto || $is_passive_all ) ) { // passive method
            // ad was requested by group `placement->group->ad` or `group->ad`
            if ( isset( $args['previous_method'] ) && $args['previous_method'] === 'group' && isset( $args['previous_id'] ) ) {
                return $ad;
            }

            $needs_backend = $this->ad_needs_backend_request( $ad );

            // ad was requested by placement `placement->ad` or `ad`
            // check if ad can be delivered without any cache-busting
            if ( 'static' === $needs_backend && ! $is_passive_all && ! $test_id ) {
                $ad->args['cache-busting'] = self::OPTION_OFF;
                $ad->args['cache-busting-orig'] = self::OPTION_AUTO;
                $ad->global_output = true;
                if ( isset( $args['output']['placement_id'] ) ) {
                    if ( ! $this->placement_can_display_not_passive( $args['output']['placement_id'] ) ) { return ''; }
                    $this->add_placement_to_current_ads( $args['output']['placement_id'] );
                }

                return $overriden_ad;
            }
            // check if ad cannot be delivered with passive cache-busting
            if ( 'off' === $needs_backend || 'ajax' === $needs_backend ) {
                $is_ajax_fallbback = 'ajax' === $needs_backend;

                if ( isset( $args['output']['placement_id'] ) && ! $this->placement_can_display_not_passive( $args['output']['placement_id'] ) ) {
                    // prevent selection of this placement using JavaScript
                    if ( $test_id ){
                        Advanced_Ads_Pro_Placement_Tests::get_instance()->no_cb_fallbacks[] = $args['previous_id'];
                    }
                    return '';
                }

                if ( $is_ajax_fallbback && $cache_busting_auto ) {
                    $ad->args['cache-busting'] = self::OPTION_ON;
                    $ad->args['cache-busting-orig'] = self::OPTION_AUTO;
                    return $this->get_overridden_ajax_ad( $ad, $args );
                }

                // `No cache-busting` fallback
                if ( $test_id ) {
                    if ( in_array( $args['previous_id'], Advanced_Ads_Pro_Placement_Tests::get_instance()->get_random_placements() ) ) {
                        Advanced_Ads_Pro_Placement_Tests::get_instance()->delivered_tests[ $args['previous_id'] ] = $test_id;
                    } else {
                        // prevent selection of this placement using JavaScript
                        Advanced_Ads_Pro_Placement_Tests::get_instance()->no_cb_fallbacks[] = $args['previous_id'];
                        return '';
                    }
                }
                $ad->args['cache-busting'] = self::OPTION_OFF;
                $ad->args['cache-busting-orig'] = self::OPTION_AUTO;
                $ad->args['global_output'] = true;
                $ad->global_output = true;
                if ( isset( $args['output']['placement_id'] ) ) {
                    if ( ! $this->placement_can_display_not_passive( $args['output']['placement_id'] ) ) { return ''; }
                    $this->add_placement_to_current_ads( $args['output']['placement_id'] );
                }
                return $overriden_ad;
            }

            if ( ! $ad->can_display( array( 'passive_cache_busting' => true ) ) ) {
                if ( $test_id && array_key_exists( $args['previous_id'], Advanced_Ads_Pro_Placement_Tests::get_instance()->delivered_tests ) ) {
                    Advanced_Ads_Pro_Placement_Tests::get_instance()->delivered_tests[ $args['previous_id'] ] = $test_id;
                }

                return '';
            }

            // deliver ad using passive cache-busting
            // add new info to the passive cache-busting array
            $overriden_ad = $this->get_passive_overriden_ad( $ad, $args );
        }

        if ( $prev_is_placement && false === $overriden_ad && $test_id ) {
            Advanced_Ads_Pro_Placement_Tests::get_instance()->delivered_tests[ $args['previous_id'] ] = $test_id ;
        }

        return $overriden_ad;
    }

    public function cache_busting_auto_for_group( $overriden_group, Advanced_Ads_Group $adgroup, $ordered_ad_ids, $args ) {
        $prev_is_placement = isset( $args['previous_method'] ) && $args['previous_method'] === 'placement' && isset( $args['previous_id'] );
        $cache_busting_auto = isset( $args['placement_type'] ) && ( ! isset( $args['cache-busting'] ) || $args['cache-busting'] === self::OPTION_AUTO );
        $test_id = isset( $args['test_id'] ) ? $args['test_id'] : null;
        $is_passive_all = ! empty( $this->options['passive_all'] );
        $cache_busting_off = isset( $args['cache-busting'] ) && $args['cache-busting'] === self::OPTION_OFF;

        if ( $cache_busting_auto && ! $this->is_passive_method_used() ) { // ajax method
            $group_ads = $this->request_passive_ads_of_group( $adgroup, $ordered_ad_ids, $args );
            if ( $test_id || ! $this->group_ads_static( $group_ads, $adgroup ) ) {
                $adgroup->ad_args['cache-busting'] = self::OPTION_ON;
                $adgroup->ad_args['cache-busting-orig'] = self::OPTION_AUTO;
                $query = self::build_js_query( $args);
                $overriden_group = $this->get_override_content( $query );
            }

            if ( false === $overriden_group ) {
                // Static and does not belong to a test.
                $adgroup->ad_args['cache-busting'] = self::OPTION_OFF;
                $adgroup->ad_args['cache-busting-orig'] = self::OPTION_AUTO;
                unset( $adgroup->ad_args['cache_busting_elementid'], $args['cache_busting_elementid'] );
                $adgroup->ad_args['global_output'] = true;
                if ( isset( $args['output']['placement_id'] ) ) {
                    if ( ! $this->placement_can_display_not_passive( $args['output']['placement_id'] ) ) { return ''; }
                    $this->add_placement_to_current_ads( $args['output']['placement_id'] );
                }
            }
            return $overriden_group;
        }
        elseif ( ! $cache_busting_off && ( $cache_busting_auto || $is_passive_all ) ) { // passive method
            if ( is_array( $ordered_ad_ids ) && count( $ordered_ad_ids ) > 0 ) {
                // add info about the group to the passive cache-busting array
                $uniq_key = ++self::$adOffset;

                $group_ads = $this->request_passive_ads_of_group( $adgroup, $ordered_ad_ids, $args );

                foreach ( $group_ads as $ad ) {
                    $needs_backend = $this->ad_needs_backend_request( $ad );

                    if ( 'off' === $needs_backend || 'ajax' === $needs_backend ) {
                        $is_ajax_fallbback = 'ajax' === $needs_backend;

                        // delete info from the passive cache-busting array
                        $this->delete_passive_group( $adgroup, $args, $uniq_key );

                        if ( isset( $args['output']['placement_id'] ) && ! $this->placement_can_display_not_passive( $args['output']['placement_id'] ) ) {
                            // prevent selection of this placement using JavaScript
                            if ( $test_id ){
                                Advanced_Ads_Pro_Placement_Tests::get_instance()->no_cb_fallbacks[] = $args['previous_id'];
                            }
                            return '';
                        }

                        if ( $is_ajax_fallbback && $cache_busting_auto ) {
                            $adgroup->ad_args['cache-busting'] = self::OPTION_ON;
                            $adgroup->ad_args['cache-busting-orig'] = self::OPTION_AUTO;
                            $query = self::build_js_query( $args);
                            return $this->get_override_content( $query );
                        } else {
                            // `No cache-busting` fallback
                            if ( $test_id ) {
                                if ( in_array( $args['previous_id'], Advanced_Ads_Pro_Placement_Tests::get_instance()->get_random_placements() ) ) {
                                    Advanced_Ads_Pro_Placement_Tests::get_instance()->delivered_tests[ $args['previous_id'] ] = $test_id;
                                } else {
                                    // prevent selection of this placement using JavaScript
                                    Advanced_Ads_Pro_Placement_Tests::get_instance()->no_cb_fallbacks[] = $args['previous_id'];
                                    return '';
                                }
                            }

                            $adgroup->ad_args['cache-busting'] = self::OPTION_OFF;
                            $adgroup->ad_args['cache-busting-orig'] = self::OPTION_AUTO;
                            unset( $adgroup->ad_args['cache_busting_elementid'], $args['cache_busting_elementid'] );
                            $adgroup->ad_args['global_output'] = true;
                            if ( isset( $args['output']['placement_id'] ) ) {
                                if ( ! $this->placement_can_display_not_passive( $args['output']['placement_id'] ) ) { return ''; }
                                $this->add_placement_to_current_ads( $args['output']['placement_id'] );
                            }

                            return $overriden_group;
                        }
                    }
                }

                if ( $this->group_ads_static( $group_ads, $adgroup ) && ! $is_passive_all && ! $test_id ) {
                    $adgroup->ad_args['cache-busting'] = self::OPTION_OFF;
                    $adgroup->ad_args['cache-busting-orig'] = self::OPTION_AUTO;
                    unset( $adgroup->ad_args['cache_busting_elementid'], $args['cache_busting_elementid'] );
                    $adgroup->ad_args['global_output'] = true;
                    if ( isset( $args['output']['placement_id'] ) ) {
                        if ( ! $this->placement_can_display_not_passive( $args['output']['placement_id'] ) ) { return ''; }
                        $this->add_placement_to_current_ads( $args['output']['placement_id'] );
                    }
                    return $overriden_group;
                }

                $output_string = $this->get_passive_overriden_group( $adgroup, $ordered_ad_ids, $args, $uniq_key, $group_ads );
                $overriden_group = $output_string;
            }
        }

        if ( $prev_is_placement && false === $overriden_group && $test_id ) {
            Advanced_Ads_Pro_Placement_Tests::get_instance()->delivered_tests[ $args['previous_id'] ] = $test_id;
        }

        return $overriden_group;

    }

	/**
	 * Request passive ads of a group.
	 *
	 * @param obj $adgroup Advanced_Ads_Group
	 * @param array/null $ordered_ad_ids ordered ids of the ads that belong to the group
	 * @param array $args argument passed to the 'get_ad_by_group' function
	 */
	private function request_passive_ads_of_group( $adgroup, $ordered_ad_ids, $args ) {
		$args['global_output'] = false;
		$args['is_top_level'] = false;
		$args['ad_label'] = 'disabled';
		$args['group_info'] = array (
			'passive_cb' => true,
			'id' => $adgroup->id,
			'name' => $adgroup->name,
			'type' => $adgroup->type,
			'refresh_enabled' => Advanced_Ads_Pro_Group_Refresh::is_enabled( $adgroup ),
		);

		$ordered_ad_ids = is_array( $ordered_ad_ids ) ? $ordered_ad_ids : array();
		$group_ads = array();
		foreach ( $ordered_ad_ids as $_ad_id ) {
			// get result from the 'override_ad_select_by_ad' method
			$ad = Advanced_Ads_Select::get_instance()->get_ad_by_method( $_ad_id, Advanced_Ads_Select::AD, $args );

			// Ignore ads that are hidden for all users.
			if ( ! $ad instanceof Advanced_Ads_Ad || ! $ad->can_display( array( 'passive_cache_busting' => true ) ) ) {
				continue;
			}

			$group_ads[] = $ad;
		}
		return $group_ads;
	}

    /**
     * Get simple js ad.
     * Conditions are not checked for every visitor of a cached page.
     */
    public function get_simple_js_ad( $overriden_ad, Advanced_Ads_Ad $ad, $args ) {
        $cp_placement = isset( $args['placement_type'] ) && $args['placement_type'] === 'custom_position';

        if ( ! $cp_placement
            // Check if collecting of simple ads has been started.
            || $this->collecting_js_items ) {
            return $overriden_ad;
        }

        $this->collecting_js_items = true;
        $elementid = $this->generate_elementid();
        $args['cache_busting_elementid'] = $ad->args['cache_busting_elementid'] = $elementid;
        $overriden_ad = '';

        if ( $ad->can_display() ) {
            // Disable global output because the ads will be tracked using an AJAX request.
            $ad->args['global_output'] = false;
            $ad->global_output = false;

            $l = count( $this->has_js_items );
            $overriden_ad = $this->add_simple_js_item( $elementid, $ad->output(), $l, $args );

            $ad->args['global_output'] = true;
            $ad->global_output = true;
        }

        $this->collecting_js_items = false;
        return $overriden_ad;
    }

    /**
     * Get simple js group.
     * Conditions are not checked for every visitor of a cached page.
     */
    public function get_simple_js_group( $overriden_group, Advanced_Ads_Group $adgroup, $ordered_ad_ids, $args ) {
        $cp_placement = isset( $args['placement_type'] ) && $args['placement_type'] === 'custom_position';

        if ( ! $cp_placement
            || $this->collecting_js_items ) {
            // Check if collecting of simple ads has been started.
            return $overriden_group;
        }

        $this->collecting_js_items = true;
        $elementid = $this->generate_elementid();
        $args['cache_busting_elementid'] = $adgroup->ad_args['cache_busting_elementid'] = $elementid;

        // Disable global output because the ads will be tracked using an AJAX request.
        $adgroup->ad_args['global_output'] = false;

        $l = count( $this->has_js_items );
        $overriden_group = $this->add_simple_js_item( $elementid, $adgroup->output( $ordered_ad_ids ), $l, $args );

        $adgroup->ad_args['global_output'] = true;
        $this->collecting_js_items = false;

        return $overriden_group;
    }

    /**
     * Add simple js item.
     *
     * @param string $elementid Wrapper id.
     * @param string $output Ad/Group output.
     * @param int $l Number of existing simple js items.
     * @param array $args Placement options.
     * @return string Wrapper id.
     */
    function add_simple_js_item( $elementid, $output, $l, $args ) {
		if ( isset( $args['output']["placement_id"] ) ) {
			$placements = Advanced_Ads::get_ad_placements_array();
			if ( isset( $placements[ $args['output']["placement_id"] ] ) )
				$placement = $placements[ $args['output']["placement_id"] ];

			$this->has_js_items[] = array(
				'id' => $args['output']["placement_id"],
				'type' => 'placement',
				'title' => ! empty( $placement['name'] ) ? $placement['name'] : '',
				'blog_id' => get_current_blog_id()
			);
		}

        $js_item = array(
            'output' => $output,
            'elementid' => $elementid,
            'args' => $args,
            'has_js_items' => array_slice( $this->has_js_items, $l ),
        );

        $js_item = apply_filters(
            'advanced-ads-cache-busting-item',
            $js_item,
            array(
                'method' => 'placement',
                'args' => $args
            )
        );

        $this->js_items[] = $js_item;


        /**
         * Collect blog data before `restore_current_blog` is called
         */
        if ( class_exists( 'Advanced_Ads_Tracking_Util', false ) && method_exists( 'Advanced_Ads_Tracking_Util', 'collect_blog_data' ) ) {
            $tracking_utils = Advanced_Ads_Tracking_Util::get_instance();
            $tracking_utils->collect_blog_data();
        }

        $placement_id = ! empty( $args['output']['placement_id'] ) ? $args['output']['placement_id'] : '';

		return $this->create_wrapper( $elementid, $placement_id, $args );
    }

    /**
     * add data related to ad and ad placement to js array
     *
     * @param obj $ad Advanced_Ads_Ad
     * @param array $args argument passed to the 'get_ad_by_id' function
     * @return string
     */
    private function get_passive_overriden_ad( Advanced_Ads_Ad $ad, $args ) {
        $cache_busting_auto = isset( $args['placement_type'] ) && ( ! isset( $args['cache-busting'] ) || $args['cache-busting'] === self::OPTION_AUTO );

        if ( $cache_busting_auto ) {
            $js_array = & $this->passive_cache_busting_placements;
            $id = $args['previous_id'];
        } else {
            $js_array = & $this->passive_cache_busting_ads;
            $id = $args['id'];
        }
        $uniq_key = $id . '_' . ++self::$adOffset;

		$not_head                        = ! $this->isHead || ( isset( $args['placement_type'] ) && $args['placement_type'] !== 'header' );
		$elementid                       = $not_head ? $this->generate_elementid() : null;
		$args['cache_busting_elementid'] = $ad->args['cache_busting_elementid'] = $elementid;
		$placement_id                    = ! empty( $args['output']['placement_id'] ) ? $args['output']['placement_id'] : '';
		$output_string                   = $not_head ? $this->create_wrapper( $elementid, $placement_id, $args ) : '';

        $js_array[ $uniq_key ] = array(
            'elementid' => array( $elementid ),
            'ads' => array( $ad->id => $this->get_passive_cb_for_ad( $ad ) ), // only 1 ad
        );


        if ( $cache_busting_auto ) {
            $placements = Advanced_Ads::get_ad_placements_array();
            $test_id = isset( $args['test_id'] ) ? $args['test_id'] : null;

            $js_array[ $uniq_key ]['type'] = 'ad';
            $js_array[ $uniq_key ]['id'] = $ad->id;
            $js_array[ $uniq_key ]['placement_info']  = $this->get_placement_info( $id );
            $js_array[ $uniq_key ]['test_id'] = $test_id;

            if ( $ad_for_adblocker = Advanced_Ads_Pro_Module_Ads_For_Adblockers::get_ad_for_adblocker( $args ) ) {
                $js_array[ $uniq_key ]['ads_for_ab'] = array( $ad_for_adblocker->id => $this->get_passive_cb_for_ad( $ad_for_adblocker ) );
            }

            if ( 'ajax' === $this->fallback_method ) {
                $ajax_info = $this->server_info->get_ajax_for_passive_placement( $ad, $args, $elementid );
                if ( $ajax_info ) {
                    $js_array[ $uniq_key ] = array_merge( $js_array[ $uniq_key ], $ajax_info );
                }
            }
        }

        $js_array[ $uniq_key ] = apply_filters(
            'advanced-ads-cache-busting-item',
            $js_array[ $uniq_key ],
            array(
                'method' => $cache_busting_auto ? 'placement' : 'ad',
                'args' => $args
            )
        );

        return $output_string;
    }

    /**
     * add data related to group and group placement to js array
     *
     * @param obj $adgroup Advanced_Ads_Group
     * @param array/null $ordered_ad_ids ordered ids of the ads that belong to the group
     * @param array $args argument passed to the 'get_ad_by_group' function
     * @param str $uniq_key Property name in JS array.
     * @param array $group_ads Group ads.
     * @return string
     */
    private function get_passive_overriden_group( Advanced_Ads_Group $adgroup, $ordered_ad_ids, $args, $uniq_key, $group_ads ) {
        $cache_busting_auto = isset( $args['placement_type'] ) && ( ! isset( $args['cache-busting'] ) || $args['cache-busting'] === self::OPTION_AUTO );

        if ( $cache_busting_auto ) {
            $js_array = & $this->passive_cache_busting_placements;
            $id = $args['previous_id'];
        } else {
            $js_array = & $this->passive_cache_busting_groups;
            $id = $args['id'];
        }
        $uniq_key = $id . '_' . $uniq_key;

		$not_head                        = ! $this->isHead || ( isset( $args['placement_type'] ) && $args['placement_type'] !== 'header' );
		$elementid                       = $not_head ? $this->generate_elementid() : null;
		$args['cache_busting_elementid'] = $adgroup->ad_args['cache_busting_elementid'] = $elementid;
		$placement_id                    = ! empty( $args['output']['placement_id'] ) ? $args['output']['placement_id'] : '';
		$output_string                   = $not_head ? $this->create_wrapper( $elementid, $placement_id, $args ) : '';

        // remove ads with 0 ad weight
        $weights = $adgroup->get_ad_weights();
        foreach ( $weights as $_ad_id => $_ad_weight ){
            if ( $_ad_weight === 0 ){
                unset( $weights[ $_ad_id ] );
            }
        }

        if ( ( $ad_count = apply_filters( 'advanced-ads-group-ad-count', $adgroup->ad_count, $adgroup ) ) === 'all' ) {
            $ad_count = 999;
        }

        $passive_ads = array();
        foreach ( $group_ads as $group_ad ) {
            $passive_ads[ $group_ad->id ] = $this->get_passive_cb_for_ad( $group_ad );
        }

        $js_array[ $uniq_key ] = array (
            'type'=> 'group',
            'id' => $adgroup->id,
            'elementid' => array( $elementid ),
            'ads' => $passive_ads,
            'group_info' => array(
                'id' => $adgroup->id,
                'name' => $adgroup->name,
                'weights' => $weights,
                'type' => $adgroup->type,
                'ordered_ad_ids' => $ordered_ad_ids,
                'ad_count' => $ad_count,
            ),
        );

        // deprecated after Advaned Ads Slider > 1.3.1
        if ( 'slider' === $adgroup->type && defined( 'AAS_VERSION' ) && version_compare( AAS_VERSION, '1.3.1', '<=' ) ) {
            $slider_options = Advanced_Ads_Slider::get_slider_options( $adgroup );
            $js_array[ $uniq_key ]['group_info']['slider_options'] = $slider_options;
        }



        if ( Advanced_Ads_Pro_Group_Refresh::is_enabled( $adgroup ) ) {
            $js_array[ $uniq_key ]['group_info']['refresh_enabled'] = true;
            $js_array[ $uniq_key ]['group_info']['refresh_interval_for_ads'] = Advanced_Ads_Pro_Group_Refresh::get_ad_intervals( $adgroup );
        }

        $advads_plugin = Advanced_Ads::get_instance();
        $label = '';
        if ( method_exists( $advads_plugin, 'get_label' ) ) {
            $placement_state = isset( $args['ad_label'] ) ? $args['ad_label'] : 'default';
            $label = Advanced_Ads::get_instance()->get_label( $placement_state );
        }

        if ( $cache_busting_auto ) {
            $placements = Advanced_Ads::get_ad_placements_array();
            $js_array[ $uniq_key ]['placement_info']  = $this->get_placement_info( $id );
            $js_array[ $uniq_key ]['test_id'] = isset( $args['test_id'] ) ? $args['test_id'] : null;

            if ( $ad_for_adblocker = Advanced_Ads_Pro_Module_Ads_For_Adblockers::get_ad_for_adblocker(
                array_diff_key( $args, array( 'ad_label' => false ) )
            ) ) {
                $js_array[ $uniq_key ]['ads_for_ab'] = array( $ad_for_adblocker->id => $this->get_passive_cb_for_ad( $ad_for_adblocker ) );
            }

            if ( 'ajax' === $this->fallback_method ) {
                $ajax_info = $this->server_info->get_ajax_for_passive_placement( $group_ads, $args, $elementid );
                if ( $ajax_info ) {
                    $js_array[ $uniq_key ] = array_merge( $js_array[ $uniq_key ], $ajax_info );
                }
            }
        }

        $js_array[ $uniq_key ] = apply_filters( 'advanced-ads-pro-passive-cb-group-data', $js_array[ $uniq_key ], $adgroup, $elementid );

        // Add wrapper around group.
        if ( ( ! empty( $adgroup->wrapper ) || $label )
            && is_array( $adgroup->wrapper )
            && class_exists( 'Advanced_Ads_Utils' ) && method_exists( 'Advanced_Ads_Utils' , 'build_html_attributes' )
        ) {
			$before = '<div' . Advanced_Ads_Utils::build_html_attributes( $adgroup->wrapper ) . '>'
                . $label
                . apply_filters( 'advanced-ads-output-wrapper-before-content-group', '', $adgroup );

            $after = apply_filters( 'advanced-ads-output-wrapper-after-content-group', '', $adgroup )
                . '</div>';
            if ( ! empty( $adgroup->ad_args['placement_clearfix'] ) ) {
                $after .= '<br style="clear: both; display: block; float: none; "/>';
            }

            $js_array[ $uniq_key ]['group_wrap'][] = array(
                'before' => $before,
                'after' => $after,
            );

        }
        $js_array[ $uniq_key ] = apply_filters(
            'advanced-ads-cache-busting-item',
            $js_array[ $uniq_key ],
            array(
                'method' => $cache_busting_auto ? 'placement' : 'group',
                'args' => $args
            )
        );

        return $output_string;
    }

	/**
	 * Get placement information
	 *
	 * @param string $id Placement id.
	 * @param array $placement_info Placement information.
	 */
	private function get_placement_info( $id ) {
		// The information which passive cache-busting (`base.js`) can read.
		// When a new placement option is added and passive cache-busting needs to access it, it should be added to the array.
		$allowed_keys = array( 'id', 'lazy_load', 'test_id', 'layer_placement', 'close', 'inject_by', 'placement_position', 'pro_custom_element', 'container_id' );

		$placements = Advanced_Ads::get_ad_placements_array();
		$placement_info = $placements[ $id ];
		$placement_info['id'] = (string) $id;

		if ( ! empty( $placement_info['options'] ) && is_array( $placement_info['options'] ) ) {
			foreach ( $placement_info['options'] as $k => $option ) {
				if ( ! in_array( $k, $allowed_keys, true ) ) {
					unset( $placement_info['options'][ $k ] );
				}
			}
		}
		return $placement_info;
	}

    /**
     * add new passive ad to passive cb js array
     *
     * @param obj $ad Advanced_Ads_Ad
     * @param array $args argument passed to the 'get_ad_by_id' function
     * @param str $uniq_key Property name in JS array.
     */
    private function add_passive_ad_to_group( Advanced_Ads_Ad $ad, $args, $uniq_key ) {
        $cache_busting_auto = isset( $args['placement_type'] ) && ( ! isset( $args['cache-busting'] ) || $args['cache-busting'] === self::OPTION_AUTO );

        if ( $cache_busting_auto ) {
            $uniq_key = $args['previous_id'] . '_' . $uniq_key;
            $this->passive_cache_busting_placements[ $uniq_key ]['ads'][ $ad->id ] = $this->get_passive_cb_for_ad( $ad );
        } else {
            $uniq_key = $args['id'] . '_' . $uniq_key;
            $this->passive_cache_busting_groups[ $uniq_key ]['ads'][ $ad->id ] = $this->get_passive_cb_for_ad( $ad );
        }
    }

    /**
     * delete an ad from passive cb js array
     *
     * @param $adgroup Advanced_Ads_Group
     * @param array $args argument passed to the 'get_ad_by_id' function
     * @param str $uniq_key Property name in JS array.
     */
    private function delete_passive_group( Advanced_Ads_Group $adgroup, $args, $uniq_key ) {
        $cache_busting_auto = isset( $args['placement_type'] ) && ( ! isset( $args['cache-busting'] ) || $args['cache-busting'] === self::OPTION_AUTO );

        if ( $cache_busting_auto ) {
            $uniq_key = $args['previous_id'] . '_' . $uniq_key;
            unset( $this->passive_cache_busting_placements[ $uniq_key ] );
        } else {
            $uniq_key = $args['id'] . '_' . $uniq_key;
            unset( $this->passive_cache_busting_groups[ $uniq_key ] );
        }
    }

    /**
     * get ad info for passive cache-busting
     *
     * @param obj $ad Advanced_Ads_Ad
     * @return array
     */
    public function get_passive_cb_for_ad( Advanced_Ads_Ad $ad ) {
        $ad_options = $ad->options();
        $ad->args['cache-busting'] = self::OPTION_AUTO;

        $passive_cb_for_ad = apply_filters( 'advanced-ads-pro-passive-cb-for-ad', array(
            'id' => $ad->id,
            'title' => $ad->title,
            'expiry_date' => (int) $ad->expiry_date,
            'visitors' => ( ! empty( $ad_options['visitors'] ) && is_array( $ad_options['visitors'] ) ) ? array_values( $ad_options['visitors'] ) : array(),
            'content' => $ad->output( array( 'global_output' => false ) ),
            'once_per_page' => ( ! empty( $ad_options['output']['once_per_page'] ) ) ? 1 : 0,
            'debugmode' => isset( $ad->output['debugmode'] ),
			'blog_id' => get_current_blog_id(),
			'type' => $ad->type,
			'position' => isset( $ad->output['position'] ) ? $ad->output['position'] : '',
        ), $ad );

		// Consent overridden for this ad.
		$passive_cb_for_ad['privacy']['ignore'] = ! empty( $ad_options['privacy']['ignore-consent'] );
		// This ad has custom code and therefore needs consent (if not overridden above).
		$passive_cb_for_ad['privacy']['needs_consent'] = ! empty( Advanced_Ads_Pro::get_instance()->get_custom_code( $ad ) );
		
		/**
		 * Collect blog data before `restore_current_blog` is called 
		 */
		if ( class_exists( 'Advanced_Ads_Tracking_Util', false ) && method_exists( 'Advanced_Ads_Tracking_Util', 'collect_blog_data' ) ) {
			$tracking_utils = Advanced_Ads_Tracking_Util::get_instance();
			$tracking_utils->collect_blog_data();
		}
		
        return $passive_cb_for_ad;
    }

    /**
     * return wrapper and js code to load the ad
     *
     * @param obj $ad Advanced_Ads_Ad
     * @param array $args argument passed to the 'get_ad_by_id' function
     * @return string/bool $overridden_ad
     */
    public function get_overridden_ajax_ad( $ad, $args ) {
        $overridden_ad = false;
        $test_id = isset( $args['test_id'] ) ? $args['test_id'] : null;
        $needs_backend = $this->ad_needs_backend_request( $ad );

        if ( 'static' !== $needs_backend || $test_id ) {
            $query = self::build_js_query( $args);
            $overridden_ad = $this->get_override_content( $query );
        }

        return $overridden_ad;
    }

    /**
     * Determine if backend request is needed.
     *
     * @param obj $ad Advanced_Ads_Ad
     * @return string
     *     'static'   Do not use cache-busting. There are no dynamic conditions, all users will see the same.
     *     'off'      Do not use cache-busting (fallback).
     *     'ajax'     Use AJAX request (fallback).
     *     'passive'  Use passive cache-busting.
     */
    public function ad_needs_backend_request( Advanced_Ads_Ad $ad ) {
        $ad_options = $ad->options();

        // code is evaluated as php if setting was never saved or php is allowed
        $allow_php = ( 'plain' === $ad->type && ( ! isset( $ad->output['allow_php'] ) || $ad->output['allow_php'] ) );
        // if there is at least one visitor condition (check old "visitor" and new "visitors" conditions)
        $is_visitor_conditions = ( ( ! empty( $ad_options['visitors'] ) && is_array( $ad_options['visitors'] ) )
            || ( ! empty( $ad_options['visitor'] ) && is_array( $ad_options['visitor'] ) ) );
        $is_group = 'group' === $ad->type;
        $has_shortcode = ! empty( $ad_options['output']['has_shortcode'] )
            // The Rich Content ad type saved long time ago.
            || ( ! isset( $ad_options['output']['has_shortcode'] ) && $ad->type === 'content' );
        $is_lazy_load = $this->lazy_load_module_enabled && isset( $ad_options['lazy_load'] ) && 'enabled' === $ad_options['lazy_load'];
        // Check if there is conditions that need backend request.
        $has_not_js_conditions = false;
        if ( ! empty( $ad_options['visitors'] ) && is_array( $ad_options['visitors'] ) ) {
            $visitors = $ad_options['visitors'];
            // Conditions that can be checked using js.
            $js_visitor_conditions = array(
                'mobile',
                'referrer_url',
                'user_agent',
                'request_uri',
                'browser_lang',
                'cookie',
                'page_impressions',
                'ad_impressions',
                'new_visitor',
                'device_width',
                'tablet',
            );

			if ( $this->fallback_method === 'ajax'
				&& isset( $ad_options['placement_type'] )
			) {
                // Conditions that can be checked by passive cache-busting only if cookies exist.
                // If not, ajax cache-busting will not be used.
                $all_server_conditions = $this->server_info->get_all_server_conditions();
                $js_visitor_conditions = array_merge( $js_visitor_conditions, array_keys( $all_server_conditions ) );
            }


            $js_visitor_conditions = apply_filters( 'advanced-ads-js-visitor-conditions', $js_visitor_conditions );

            foreach ( $visitors as $visitor ) {
                if ( ! in_array( $visitor['type'], $js_visitor_conditions ) ) {
                    // Use AJAX cache-busting, or disable cache-busting.
                    $has_not_js_conditions = true;
                }
            }
        }

        $has_tracking = false;
        if ( class_exists( 'Advanced_Ads_Tracking', false ) &&
            ( ( isset( $ad_options['tracking']['impression_limit'] ) && $ad_options['tracking']['impression_limit'] ) ||
            ( isset( $ad_options['tracking']['click_limit'] ) && $ad_options['tracking']['click_limit'] ) ) 
        ) {
            // Use AJAX cache-busting, or disable cache-busting.
            $has_tracking = true;
        }

        $has_test = ! empty( $ad_options['test_id'] );

        $hidden_without_consent = false;
        if ( empty( $ad_options['privacy']['ignore-consent'] )
            && class_exists( 'Advanced_Ads_Privacy' ) ) {
            $privacy_options = Advanced_Ads_Privacy::get_instance()->options();
            $npa_adsense = $ad->type === 'adsense' &&  ! empty( $privacy_options['show-non-personalized-adsense'] );
            // Check if the ad is invisible until consent is given.
            if ( ! empty( $privacy_options['enabled'] )
                // If the content method is 'cookie'.
                && ! empty( $privacy_options['consent-method'] ) // && empty( $privacy_options['show-without-consent'] )
                // Non-personalized Adsense are visible even without consent.
                && ! $npa_adsense
            ) {
                $hidden_without_consent = true;
            }
        }
        $specific_days = ! empty( $ad_options['weekdays']['enabled'] );
        $cp_placement = isset( $ad_options['placement_type'] ) && $ad_options['placement_type'] === 'custom_position';
        $checks_placement_cookies = ( ! empty( $ad_options['layer_placement']['close']['enabled'] )
            && ! empty( $ad_options['layer_placement']['close']['timeout_enabled'] ) )
        || ( ! empty( $ad_options['close']['enabled'] )
            && ! empty( $ad_options['close']['timeout_enabled'] ) );

        if ( $allow_php || $is_group || $has_shortcode || $has_not_js_conditions || $has_tracking ) {
            // Use AJAX cache-busting, or disable cache-busting.
            $return = $this->fallback_method;
        } elseif ( $is_visitor_conditions || $is_lazy_load || $hidden_without_consent || $specific_days || $cp_placement || $checks_placement_cookies ) {
            // Passive cache-busting.
            $return = 'passive';
        } else {
            $return = 'static';
        }


        $return = apply_filters( 'advanced-ads-pro-ad-needs-backend-request', $return, $ad, $this->fallback_method );
        return $return;
    }

    /**
     * Determine if all ads of a group are static.
     *
     * @param Advanced_Ads_Ad[] $group_ads An array of ad objects.
     * @param Advanced_Ads_Group $adgroup Group object.
     * @return bool
     */
    private function group_ads_static( $group_ads, $adgroup ) {
        if ( 0 === count( $group_ads )  ) {
            return true;
        }
        if ( 1 === count( $group_ads ) ) {
            return 'static' === $this->ad_needs_backend_request( $group_ads[0] );
        }

        return false;
    }

	/**
	 * Prepare query for js handler
	 *
	 * @param array $arguments
	 * @return array query
	 */
	public static function build_js_query( $arguments ) {
		// base query (required keys)
		$query = array(
			'id' => (string) $arguments['id'],
			'method' => (string) $arguments['method'],
		);
		$arguments['global_output'] = true;

		// process further arguments (optional keys)
		$params = array_diff_key( $arguments, array( 'id' => false, 'method' => false ) );

		if ( $params !== array() ) {
			$query['params'] = $params;
		}
		return $query;
	}

    /**
     * Determine override option for query.
     *
     * @param array $query
     *
     * @return boolean
     */
    protected function can_override( $query ) {
        $params = isset( $query['params'] ) ? $query['params'] : array();

        // allow disable cache-busting according to placement settings
        if ( $query['method'] === 'placement' && ! isset( $params['cache-busting'] ) ) {
            $placement_options = Advanced_Ads::get_ad_placements_array();

            if ( isset( $placement_options[ $query['id'] ]['options']['cache-busting'] ) ) {
                $params['cache-busting'] = $placement_options[ $query['id'] ]['options']['cache-busting'];
            }
        }

        return isset( $params['cache-busting'] ) && $params['cache-busting'] === self::OPTION_ON;
    }

    /**
     * Check if passive cache-busting can be used.
     *
     * @param array $args argument passed to ads.
     * @return bool
     */
    private function can_override_passive( $args ) {
        if ( ! empty( $args['wp_the_query']['is_feed'] ) || ! array_key_exists( 'previous_method', $args ) || ! array_key_exists( 'previous_id', $args ) ) {
            return false;
        }

        // Prevent non-header placement from being collected through wp_head.
        if ( doing_action( 'wp_head' ) && isset( $args['placement_type'] ) && 'header' !== $args['placement_type']
            && ! $this->can_inject_during_wp_head() ) {
            return false;
        }

        if ( isset( $args['cache-busting'] ) && $args['cache-busting'] === self::OPTION_IGNORE ) {
            return false;
        }

        return true;
    }

    /**
     * Prepare ad for js handler.
     *
     * @param array $query
     * @return string
     */
    protected function get_override_content( $query ) {
        $content = '';

        // Prevent non-header placement from being collected through wp_head.
        if ( doing_action( 'wp_head' ) && isset( $query['params']['placement_type'] ) && 'header' !== $query['params']['placement_type']
            && ! $this->can_inject_during_wp_head() ) {
            return $content;
        }

        // <head> scripts require no wrapper
        if ( ! $this->isHead
            || ( isset( $query['params']['placement_type'] ) && $query['params']['placement_type'] !== 'header' )
        ) {
            $query['elementid'] = $this->generate_elementid();

            // Get placement id
            if ( ! empty( $query['method'] ) && 'placement' === $query['method'] && ! empty( $query['id'] )  ) {
                // Cache-busting: "ajax"
                $placement_id = $query['id'];
            } elseif( ! empty( $query['params']['output']['placement_id'] )  ) {
                // AJAX fallback
                $placement_id = $query['params']['output']['placement_id'];
            } else {
                $placement_id = '';
            }

			$content .= $this->create_wrapper( $query['elementid'], $placement_id, $query['params'] );
        }

        $query = $this->get_ajax_query( $query );
        self::$ajax_queries[] = $query;


        return $content;
    }

	/**
	 * Get ajax query.
	 *
	 * @param array $query
	 * @param bool $request_placement Whether or not to request top level placement.
	 * @return array
	 */
    public function get_ajax_query( $query, $request_placement = true ) {
        // Request placement.
        if ( $request_placement && isset( $query['params']['output']['placement_id'] ) ) {
            $query['method'] = 'placement';
            $query['id'] = $query['params']['output']['placement_id'];
        }
        $query['blog_id'] = get_current_blog_id();

        /**
         * Collect blog data before `restore_current_blog` is called
         */
        if ( class_exists( 'Advanced_Ads_Tracking_Util', false ) && method_exists( 'Advanced_Ads_Tracking_Util', 'collect_blog_data' ) ) {
            $tracking_utils = Advanced_Ads_Tracking_Util::get_instance();
            $tracking_utils->collect_blog_data();
        }

        // Check if the `advanced-ads-ajax-ad-select-arguments` filter exists.
        if ( ! empty( $query['params'] ) && version_compare( ADVADS_VERSION, '1.24.0', '>' ) ) {
			$query['params'] = $this->remove_default_ajax_args( $this->ajax_default_args, $query['params'] );
			$query['params'] = $this->extract_general_ajax_args( $query['params'] );

        }

		return $query;
    }

	/**
	 * Remove default AJAX arguments to reduce the size of the array printed in footer.
	 *
	 * @param array $default Default arguments.
	 * @param array $source A full list of arguments that we need to be minifed.
	 * @return array Minified array (source array that does not contain default arguments).
	 */
	private function remove_default_ajax_args( $default, $source ) {
		$result = array();

		foreach ( $source as $key => $f ) {
			if ( ! array_key_exists( $key, $default ) ) {
				$result[ $key ] = $source[ $key ];
				continue;
			}

			if ( $source[ $key ] === $default[ $key ] ) {
				continue;
			}

			if (
				! is_array( $default[ $key ] )
				|| ! is_array( $source[ $key ] )
			) {
				$result[ $key ] = $source[ $key ];
				continue;
			}

			$key_result = $this->remove_default_ajax_args( $default[ $key ], $source[ $key ] );
			if ( $key_result !== array() ) {
				$result[ $key ] = $key_result;
			}
		}

		return $result;
	}

	/**
	 * Extract general AJAX arguments into separate array to reduce the size of the array printed in footer.
	 *
	 * @param array $source A full list of arguments to extract general arguments from.
	 * @return array A list of arguments with general arguments removed.
	 */
	private function extract_general_ajax_args( $source ) {
		if ( wp_doing_ajax() ) {
			// Do nothing because we are not able to add data to the footer array.
			return $source;
		}
		if ( isset( $source['post'] ) ) {
			$ref = array_search( $source['post'], $this->ajax_queries_args, true );
			if ( $ref === false ) {
				$ref = 'r' . count( $this->ajax_queries_args );
				$this->ajax_queries_args[ $ref ] = $source['post'];
			}
			$source['post'] = $ref;
		}
		return $source;
	}

	/**
	 * Add default AJAX arguments that were removed to reduce the size of the array printed in footer.
	 *
	 * @see self::remove_default_ajax_args
	 *
	 * When the item in the default array is not an array, it will be replaced by the item in the minified array.
	 * When an item exists in either associative array, it will be added. Numeric keys are overridden.
	 *
	 * @param array $arguments Minified arguments.
	 * @param array $request Current ad request.
	 * @return array New arguments.
	 */
	public function add_default_ajax_arguments( $arguments, $request ) {
		if ( ! empty( $request['elementId'] ) ) {
			$arguments['cache_busting_elementid'] = $request['elementId'];
		}

		return array_replace_recursive( $this->ajax_default_args, $arguments );
	}

	/**
	 * Create wrapper for cache-busting.
	 *
	 * @param string $element_id   Id of the wrapper.
	 * @param string $placement_id Id of the placement.
	 * @param array  $args         Custom arguments of ad or group.
	 *
	 * @return string Cache-busting wrapper.
	 */
	private function create_wrapper( $element_id, $placement_id = '', $args = array() ) {
		$class = $element_id;
		if ( $placement_id ) {
			$prefix = Advanced_Ads_Plugin::get_instance()->get_frontend_prefix();
			$class .= ' ' . $prefix . $placement_id;
		}
		$style           = ! empty( $args['inline-css'] ) ? 'style="' . $args['inline-css'] . '"' : '';
		$wrapper_element = ! empty( $args['inline_wrapper_element'] ) ? 'span' : 'div';

		// TODO: `id` is deprecated.
		return '<' . $wrapper_element . ' ' . $style . ' class="' . $class . '" id="' . $element_id . '"></' . $wrapper_element . '>';
	}




    /**
     * Generate unique element id
     *
     * @return string
     */
    public function generate_elementid() {
        $prefix = Advanced_Ads_Plugin::get_instance()->get_frontend_prefix();
        return $prefix . md5( 'advanced-ads-pro-ad-' . uniqid( ++self::$adOffset, true ) );
    }

    /**
     * Check if placement can be displayed without passive cache-busting.
     *
     * @param string $id Placement id.
     * @see placement_can_display()
     * @return bool
     */
    private function placement_can_display_not_passive( $id ) {
        // We force this filter to return true when collecting placements for passive cache-busting.
        // For now revoke this behavior
        return apply_filters( 'advanced-ads-can-display-placement', true, $id );
    }

    /**
     * check if placement was closed before
     *
     * @param int $id placement id
     * @return bool whether placement can be displayed or not
     */
    public function placement_can_display( $return, $id = 0 ){
        static $checked_passive = array();

        if ( in_array( $id, $checked_passive ) ) {
            // Ignore current filter when the placement is delivered without passive cache-busting.
            return $return;
        }

        // get all placements
        $placements = Advanced_Ads::get_ad_placements_array();

        $cache_busting_auto = ! isset( $placements[ $id ]['options']['cache-busting'] ) || $placements[ $id ]['options']['cache-busting'] === self::OPTION_AUTO;

        if ( $cache_busting_auto && $this->is_passive_method_used() ) {
            $checked_passive[] = $id;
            return true;
        }

        return $return;
    }

    /**
     * determines, whether the "passive"  method is used or not
     *
     * @return bool true if the "passive" method is used, false otherwise
     */
    public function is_passive_method_used() {
        return isset( $this->options['default_auto_method'] ) && $this->options['default_auto_method'] === 'passive';
    }

    /**
     * determines, whether or not to load tracking scripts
     *
     * @param bool  $need_load_header_scripts
     * @return bool true if tracking scripts should be loaded, $need_load_header_scripts otherwise
     */
    public function load_tracking_scripts( $need_load_header_scripts ) {
        //the script is used by: passive cache-busting, 'group refresh' feature
        return true;
    }

    /**
     * Add ad debug content
     *
     * @param arr $content
     * @param obj $ad Advanced_Ads_Ad
     * @return arr $content
     */
    public function add_debug_content( $content, Advanced_Ads_Ad $ad ) {
        $needs_backend = $this->ad_needs_backend_request( $ad );
        if ( 'off' === $needs_backend || 'ajax' === $needs_backend ) {
            $info = __( 'The ad can not work with passive cache-busting', 'advanced-ads-pro' );
        } else {
            $info = __( 'The ad can work with passive cache-busting', 'advanced-ads-pro' );
        }

        if ( $this->is_ajax ) {
            $name = _x( 'ajax', 'setting label', 'advanced-ads-pro' );
        } elseif ( isset( $ad->args['cache-busting'] ) && $ad->args['cache-busting'] === self::OPTION_AUTO ) {
            $name =  __( 'passive', 'advanced-ads-pro' );
            $info .= '<br />##advanced_ads_passive_cb_debug##'
            . sprintf( '<div class="advads-passive-cb-debug" style="display:none;" data-displayed="%s" data-hidden="%s"></div>',
                __( 'The ad is displayed on the page', 'advanced-ads-pro' ),
                __( 'The ad is not displayed on the page', 'advanced-ads-pro' )
            );
        } else {
            $name = _x( 'off', 'setting label', 'advanced-ads-pro' );
        }

        $content[] = sprintf( '%s <strong>%s</strong><br />%s', _x( 'Cache-busting:', 'placement admin label', 'advanced-ads-pro' ), $name, $info );


        return $content;
    }

    /**
     * Add placement to current ads.
     *
     * @param string $id Placement id.
     */
    private function add_placement_to_current_ads( $id ) {
        $placements = Advanced_Ads::get_ad_placements_array();
        $name = ! empty( $placements[ $id ]['name'] ) ? $placements[ $id ]['name'] : $id;
        Advanced_Ads::get_instance()->current_ads[] = array('type' => 'placement', 'id' => $id, 'title' => $name );
    }

	public function get_visitors( Advanced_Ads_Ad $ad ) {
		$visitors = ( ! empty( $ad_options['visitors'] ) && is_array( $ad_options['visitors'] ) ) ? array_values( $ad_options['visitors'] ) : array();
	}

    /**
     * Check if the ad can be displayed based on display limit.
     * Handle "Custom position" placements that have cache-busting disabled.
     *
     * @param bool $can_display Existing value.
     * @param obj $ad Advanced_Ads_Ad object
     * @param array $check_options
     * @return bool true if limit is not reached, false otherwise
     */
    public function can_display_by_display_limit( $can_display, Advanced_Ads_Ad $ad, $check_options ) {
        if ( ! $can_display ) {
            return false;
        }

        if ( ! $this->collecting_js_items ) {
            return $can_display;
        }

        $output_options = $ad->options( 'output' );

        if ( ! empty( $output_options['once_per_page'] ) ) {

            foreach ( $this->has_js_items as $item ) {
                if ( $item['type'] === 'ad' && absint( $item['id'] ) === $ad->id ) {
                    return false;
                }
            }
        }
        return true;
    }

	/**
	 * Check whether the module is enabled.
	 *
	 * @return bool.
	 */
	public static function is_enabled() {
		$options = Advanced_Ads_Pro::get_instance()->get_options();
		return ! empty( $options['cache-busting']['enabled'] );
	}

    /**
     * Check if placements of type other than `header` can be injected during `wp_head` action.
     */
    private function can_inject_during_wp_head() {
        return class_exists( 'Advanced_Ads_Compatibility' )
            && method_exists( 'Advanced_Ads_Compatibility', 'can_inject_during_wp_head' )
            && Advanced_Ads_Compatibility::can_inject_during_wp_head();
    }

	/**
	 * Check if TCF privacy is active; only do this when cache-busting is turned off.
	 * If yes, add a script to handle decoded ads due to TCF privacy settings.
	 */
	public function check_for_tcf_privacy() {
		$options = Advanced_Ads_Privacy::get_instance()->options();
		if ( ! isset( $options['enabled'] ) || $options['consent-method'] !== 'iab_tcf_20' ) {
			return;
		}

		wp_enqueue_script(
			// we need the same handle as with cache-busting so tracking still works.
			'advanced-ads-pro/cache_busting',
			AAP_BASE_URL . 'assets/js/privacy' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' ) . '.js',
			[ ADVADS_SLUG . '-advanced-js', 'jquery'],
			AAP_VERSION,
			true
		);
	}
}
