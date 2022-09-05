<?php

/**
 * Advanced Ads
 *
 * @package   Advanced_Ads_Group
 * @author    Thomas Maier <support@wpadvancedads.com>
 * @license   GPL-2.0+
 * @link      https://wpadvancedads.com
 * @copyright 2014 Thomas Maier, Advanced Ads GmbH
 */

/**
 * An ad group object
 *
 * @package Advanced_Ads_Group
 * @author  Thomas Maier <support@wpadvancedads.com>
 */
class Advanced_Ads_Group {

	/**
	 * Default ad group weight
	 * previously called MAX_AD_GROUP_WEIGHT
	 */
	const MAX_AD_GROUP_DEFAULT_WEIGHT = 10;

	/**
	 * Term id of this ad group
	 */
	public $id = 0;

	/**
	 * Group type
	 *
	 * @since 1.4.8
	 */
	public $type = 'default';

	/**
	 * Name of the taxonomy
	 */
	public $taxonomy = '';

	/**
	 * Post type of the ads
	 */
	protected $post_type = '';

	/**
	 * The current loaded ad
	 */
	protected $current_ad = '';

	/**
	 * The name of the term
	 */
	public $name = '';

	/**
	 * The slug of the term
	 */
	public $slug = '';

	/**
	 * The description of the term
	 */
	public $description = '';

	/**
	 * Number of ads to display in the group block
	 */
	public $ad_count = 1;

	/**$slug
	 * contains other options
	 *
	 * @since 1.5.5
	 */
	public $options = array();

	/**
	 * Optional arguments passed to ads.
	 *
	 * @var array
	 */
	public $ad_args = array();

	/**
	 * Containing ad weights
	 */
	private $ad_weights;

	/**
	 * Array with post type objects (ads)
	 */
	private $ads = array();

	/**
	 * Multidimensional array contains information about the wrapper
	 *  each possible html attribute is an array with possible multiple elements.
	 *
	 * @since untagged
	 */
	public $wrapper = array();

	/**
	 * Displayed above the ad.
	 */
	public $label = '';

	/**
	 * Init ad group object
	 *
	 * @since 1.0.0
	 * @param int|obj $group   either id of the ad group (= taxonomy id) or term object
	 * @param array   $ad_args optional arguments passed to ads
	 */
	public function __construct( $group, $ad_args = array() ) {
		$this->taxonomy = Advanced_Ads::AD_GROUP_TAXONOMY;

		$group = get_term( $group, $this->taxonomy );
		if ( $group == null || is_wp_error( $group ) ) {
			return;
		}

		$this->load( $group, $ad_args );
	}

	/**
	 * Load additional ad group properties
	 *
	 * @since 1.4.8
	 * @param int   $id      group id
	 * @param obj   $group   wp term object
	 * @param array $ad_args optional arguments passed to ads
	 */
	private function load( $group, $ad_args ) {
		$this->id = $group->term_id;
		$this->name = $group->name;
		$this->slug = $group->slug;
		$this->description = $group->description;
		$this->post_type = Advanced_Ads::POST_TYPE_SLUG;
		$this->ad_args = $ad_args;
		$this->is_head_placement = isset( $this->ad_args['placement_type'] ) && $this->ad_args['placement_type'] === 'header';
		$this->ad_args['is_top_level'] = ! isset( $this->ad_args['is_top_level'] );

		$this->load_additional_attributes();

		if ( ! $this->is_head_placement ) {
			$this->create_wrapper();
		}
	}

	/**
	 * Load additional attributes for groups that are not part of the WP terms
	 *
	 * @since 1.4.8
	 */
	protected function load_additional_attributes() {
		// -TODO should abstract (i.e. only call once per request)
		$all_groups = get_option( 'advads-ad-groups', array() );

		if ( ! isset( $all_groups[ $this->id ] ) || ! is_array( $all_groups[ $this->id ] ) ) { return; }

		if ( isset( $this->ad_args['change-group'] ) ) {
			// some options was provided by the user
			$group_data = Advanced_Ads_Utils::merge_deep_array( array( $all_groups[ $this->id ], $this->ad_args['change-group'] ) ) ;
		} else {
			$group_data = $all_groups[ $this->id ];
		}

		if ( isset( $group_data['type'] ) ) {
			$this->type = $group_data['type'];
		}

		// get ad count; default is 1
		if ( isset( $group_data['ad_count'] ) ) {
			$this->ad_count = $group_data['ad_count'] === 'all' ? 'all' : (int) $group_data['ad_count'];
		}

		if ( isset( $group_data['options'] ) ) {
			$this->options = isset( $group_data['options'] ) ? $group_data['options'] : array();
		}
	}

	/**
	 * Control the output of the group by type and amount of ads
	 *
	 * @param array $ordered_ad_ids Ordered ids of the ads that belong to the group.
	 *
	 * @return string $output output of ad(s) by ad
	 * @since 1.4.8
	 */
	public function output( $ordered_ad_ids ) {
		if ( empty( $ordered_ad_ids ) ) {
			return '';
		}

		// load the ad output
		$output = array();
		$ads_displayed = 0;
		$ad_count = apply_filters( 'advanced-ads-group-ad-count', $this->ad_count, $this );

		$ad_select = Advanced_Ads_Select::get_instance();

		// the Advanced_Ads_Ad obj can access this info
		$this->ad_args['group_info'] = array(
			'id' => $this->id,
			'name' => $this->name,
			'type' => $this->type,
			'refresh_enabled' => ! empty( $this->options['refresh']['enabled'] ),
		);
		$this->ad_args['ad_label'] = 'disabled';

		if( is_array( $ordered_ad_ids ) ){
			foreach ( $ordered_ad_ids as $_ad_id ) {
				$this->ad_args['group_info']['ads_displayed'] = $ads_displayed;

				// load the ad object
				$ad = $ad_select->get_ad_by_method( $_ad_id, Advanced_Ads_Select::AD, $this->ad_args );

				if ( $ad !== null ) {
					$output[] = $ad;
					$ads_displayed++;
					// break the loop when maximum ads are reached
					if( $ads_displayed === $ad_count ) {
						break;
					}
				}
			}
		}

		$global_output = ! isset( $this->ad_args['global_output'] ) || $this->ad_args['global_output'];
		if ( $global_output ) {
			// add the group to the global output array
			$advads = Advanced_Ads::get_instance();
			$advads->current_ads[] = array('type' => 'group', 'id' => $this->id, 'title' => $this->name);
		}

		if ( $output === array() || ! is_array( $output ) ) {
			return '';
		}

		// filter grouped ads output
		$output_array = apply_filters( 'advanced-ads-group-output-array', $output, $this );

		// make sure the right format comes through the filter
		if ( $output_array === array() || ! is_array( $output_array ) ) {
			return '';
		}

		$output_string = implode( '', $output_array );

		// Adds inline css to the wrapper.
		if ( ! empty( $this->ad_args['inline-css'] ) && $this->ad_args['is_top_level'] ) {
			$inline_css    = new Advanced_Ads_Inline_Css();
			$this->wrapper = $inline_css->add_css( $this->wrapper, $this->ad_args['inline-css'], $global_output );
		}

		if ( ! $this->is_head_placement && $this->wrapper !== array() ) {
			$output_string = '<div' . Advanced_Ads_Utils::build_html_attributes( $this->wrapper ) . '>'
			. $this->label
			. apply_filters( 'advanced-ads-output-wrapper-before-content-group', '', $this )
			. $output_string
			. apply_filters( 'advanced-ads-output-wrapper-after-content-group', '', $this )
			. '</div>';
		}

		if ( ! empty( $this->ad_args['is_top_level'] ) && ! empty( $this->ad_args['placement_clearfix'] ) ) {
			$output_string .= '<br style="clear: both; display: block; float: none;"/>';
		}

		// filter final group output
		return apply_filters( 'advanced-ads-group-output', $output_string, $this );
	}

	/**
	 * Get ordered ids of the ads that belong to the group
	 *
	 * @return array
	 */
	public function get_ordered_ad_ids() {
		// load ads
		$ads = $this->load_all_ads();
		if ( ! is_array( $ads ) ) {
			return array();
		}

		// get ad weights serving as an order here
		$weights = $this->get_ad_weights( array_keys( $ads ) );
		$ad_ids  = wp_list_pluck( $ads, 'ID' );

		// remove ads with 0 ad weight and unavailable ads (e.g. drafts).
		foreach ( $weights as $ad_id => $ad_weight ) {
			if ( $ad_weight === 0 || ! in_array( $ad_id, $ad_ids, true ) ) {
				unset( $weights[ $ad_id ] );
			}
		}

		// order ads based on group type
		if ( $this->type === 'ordered' ) {
			$ordered_ad_ids = $this->shuffle_ordered_ads( $weights );
		} else { // default
			$ordered_ad_ids = $this->shuffle_ads( $ads, $weights );
		}

		return apply_filters( 'advanced-ads-group-output-ad-ids', $ordered_ad_ids, $this->type, $ads, $weights, $this );
	}

	/**
	 * Return all ads from this group
	 *
	 * @since 1.0.0
	 */
	public function get_all_ads() {
		if ( count( $this->ads ) > 0 ) {
			return $this->ads; }
		else {
			return $this->load_all_ads(); }
	}

	/**
	 * Load all public ads for this group
	 *
	 * @since 1.0.0
	 * @update 1.1.0 load only public ads
	 * @update allow to cache groups for few minutes
	 *
	 * @return WP_Post[] $ads array with ad (post) objects
	 */
	private function load_all_ads() {

		if ( ! $this->id ) {
			return array();
		}

		// reset
		$this->ads = array();

		// much more complex than needed: one of the three queries is not needed and the last query gets slow quiet fast
		$args = array(
			'post_type' => $this->post_type,
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'taxonomy' => $this->taxonomy,
			'term' => $this->slug,
			'orderby' => 'id' // might want to avoid sorting as not needed for most calls and fast in PHP; slight I/O blocking concern
		);

		$found = false;
		$key = 'ad_group_all_ads_' . $this->post_type . '_' . $this->taxonomy . '_' . $this->slug;
		$ads = wp_cache_get( $key, Advanced_Ads_Model::OBJECT_CACHE_GROUP, false, $found );
		if ( $found ) {
			$this->ads = $ads;
		} else {
			$ads = new WP_Query( $args );

			if ( $ads->have_posts() ) {
				$this->ads = $this->add_post_ids( $ads->posts );
			}
			wp_cache_set( $key, $this->ads, Advanced_Ads_Model::OBJECT_CACHE_GROUP, Advanced_Ads_Model::OBJECT_CACHE_TTL);
		}

		return $this->ads;
	}

	/**
	 * Use post ids as keys for ad array
	 *
	 * @since 1.0.0
	 * @param arr $ads array with post objects
	 * @return arr $ads array with post objects with post id as their key
	 * @todo check, if there isn’t a WP function for this already
	 */
	private function add_post_ids(array $ads){

		$ads_with_id = array();
		foreach ( $ads as $_ad ){
			$ads_with_id[$_ad->ID] = $_ad;
		}

		return $ads_with_id;
	}

	/**
	 * Shuffle ads based on ad weight.
	 *
	 * @param array $ads     ad objects.
	 * @param array $weights ad weights, indexed by ad id.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function shuffle_ads( $ads, $weights ) {
		// get a random ad for every ad there is
		$shuffled_ads = array();
		// while non-zero weights are set select random next
		// phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition -- prevents code duplication.
		while ( null !== ( $random_ad_id = $this->get_random_ad_by_weight( $weights ) ) ) {
			// remove chosen ad from weights array
			unset( $weights[ $random_ad_id ] );
			// put random ad into shuffled array
			if ( ! empty( $ads[ $random_ad_id ] ) ) {
				$shuffled_ads[] = $random_ad_id;
			}
		}

		return $shuffled_ads;
	}

	/**
	 * Shuffle ads that have the same width.
	 *
	 * @since untagged
	 * @param array $weights Array of $ad_id => weight pairs.
	 * @return array $ordered_ad_ids Ordered ad ids.
	 */
	public function shuffle_ordered_ads( array $weights ) {
		// order to highest weight first
		arsort( $weights );
		$ordered_ad_ids = array_keys( $weights );

		$weights = array_values( $weights );
		$count = count( $weights );
		$pos = 0;
		for ( $i = 1; $i <= $count; $i++ ) {
			if ( $i == $count || $weights[ $i ] !== $weights[ $i - 1] ) {
				$slice_len = $i - $pos;
				if ( $slice_len !== 1 ) {
					$shuffled = array_slice( $ordered_ad_ids, $pos, $slice_len );
					shuffle ( $shuffled );
					// Replace the unshuffled chunk with the shuffled one.
					array_splice( $ordered_ad_ids, $pos, $slice_len, $shuffled );
				}
				$pos = $i;
			}
		}
		return $ordered_ad_ids;
	}

	/**
	 * Get random ad by ad weight.
	 *
	 * @since 1.0.0
	 * @param array $ad_weights Indexed by ad_id [int $ad_id => int $weight].
	 * @source applied with fix for order http://stackoverflow.com/a/11872928/904614
	 *
	 * @return null|int
	 */
	private function get_random_ad_by_weight(array $ad_weights) {

		// use maximum ad weight for ads without this
		// ads might have a weight of zero (0); to avoid mt_rand fail assume that at least 1 is set.
		$max = array_sum( $ad_weights );
		if ( $max < 1 ) {
			return null;
		}

		$rand = mt_rand( 1, $max );
		foreach ( $ad_weights as $ad_id => $weight ) {
			$rand -= $weight;
			if ( $rand <= 0 ) {
				return $ad_id;
			}
		}

		return null;
	}

	/**
	 * Get weights of ads in this group
	 *
	 * @param array $ad_ids Ids of ads assigned to this group.
	 *
	 * @return array
	 */
	public function get_ad_weights( $ad_ids = array() ) {
		if ( is_array( $this->ad_weights ) ) {
			return $this->ad_weights;
		}

		$weights          = get_option( 'advads-ad-weights', array() );
		$this->ad_weights = array();
		if ( array_key_exists( $this->id, $weights ) ) {
			$this->ad_weights = $weights[ $this->id ];
		}
		asort( $this->ad_weights );

		// add ads whose weight has not yet been saved with the default value.
		foreach ( $ad_ids as $ad_id ) {
			if ( ! array_key_exists( $ad_id, $this->ad_weights ) ) {
				$this->ad_weights[ $ad_id ] = self::MAX_AD_GROUP_DEFAULT_WEIGHT;
			}
		}

		return $this->ad_weights;
	}

	/**
	 * Save ad group information that are not included in terms or ad weight
	 *
	 * @since 1.4.8
	 * @param arr $args group arguments
	 */
	public function save($args = array()) {

		$defaults = array( 'type' => 'default', 'ad_count' => 1, 'options' => array() );
		$args = wp_parse_args($args, $defaults);

		// get global ad group option
		$groups = get_option( 'advads-ad-groups', array() );

		$groups[$this->id] = $args;

		update_option( 'advads-ad-groups', $groups );
	}

	/**
	 * Delete all the ad weights for a group by id
	 *
	 * @since 1.0.0
	 */
	public static function delete_ad_weights($group_id){
	    $all_weights = get_option( 'advads-ad-weights', array() );
	    if ($all_weights && isset($all_weights[$group_id])){
	        unset($all_weights[$group_id]);
	        update_option( 'advads-ad-weights', $all_weights );
	    }
	}

	/**
	 * Create a wrapper to place around the group.
	 */
	private function create_wrapper() {
		$this->wrapper = array();

		if ( $this->ad_args['is_top_level'] ) {
			// Add label.
			$placement_state = isset( $this->ad_args['ad_label'] ) ? $this->ad_args['ad_label'] : 'default';
			$this->label = Advanced_Ads::get_instance()->get_label( $placement_state );

			// Add placement class.
			if ( isset( $this->ad_args['output']['class'] ) && is_array( $this->ad_args['output']['class'] ) ) {
				$this->wrapper['class'] = $this->ad_args['output']['class'];
			}

			if ( isset( $this->ad_args['output']['wrapper_attrs'] ) && is_array( $this->ad_args['output']['wrapper_attrs'] ) ) {
				foreach ( $this->ad_args['output']['wrapper_attrs'] as $key => $value ) {
					$this->wrapper[$key] = $value;
				}
			}

			if ( ! empty( $this->ad_args['placement_position'] ) ) {
				switch ( $this->ad_args['placement_position'] ) {
					case 'left' :
						$this->wrapper['style']['float'] = 'left';
						break;
					case 'right' :
						$this->wrapper['style']['float'] = 'right';
						break;
					case 'center' :
						// We don't know whether the 'add_wrapper_sizes' option exists and width is set.
						$this->wrapper['style']['text-align'] = 'center';
						break;
				}
			}
		}

		$this->wrapper = (array) apply_filters( 'advanced-ads-output-wrapper-options-group', $this->wrapper, $this );

		if ( ( $this->wrapper !== array() || $this->label ) && ! isset( $this->wrapper['id'] ) ) {
			$prefix = Advanced_Ads_Plugin::get_instance()->get_frontend_prefix();
			$this->wrapper['id'] = $prefix . mt_rand();
		}
	}

	/**
	 * Calculate the number of available weights for a group depending on
	 * number of ads and default value.
	 *
	 * @param int $num_ads Number of ads in the group.
	 * @since 1.8.22
	 *
	 * @return  max weight used in group settings
	 */
	public static function get_max_ad_weight( $num_ads = 1 ){

		// use default if lower than default.
		$num_ads = absint( $num_ads );

		// use number of ads or max ad weight value, whatever is higher
		$max_weight = $num_ads < self::MAX_AD_GROUP_DEFAULT_WEIGHT ? self::MAX_AD_GROUP_DEFAULT_WEIGHT : $num_ads;

		// allow users to manipulate max ad weight
		return apply_filters( 'advanced-ads-max-ad-weight', $max_weight, $num_ads );
	}


}
