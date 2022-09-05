<?php
class Advanced_Ads_Pro_Group_Refresh {
	/**
	 * Data related to each group.
	 *
	 * @var array
	 */
	private $state_groups = array();

	/**
	 * Caches shown group ids.
	 *
	 * @var array
	 */
	private $shown_group_ids = array();

	public function __construct() {
		$options = Advanced_Ads_Pro::get_instance()->get_options();

		if ( empty( $options['cache-busting']['enabled'] ) ) {
			return ;
		}

		$this->is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

		if ( $this->is_ajax ) {
			$this->init_group_refresh();
		}
	}

	/**
	* Init group refresh.
	*/
	private function init_group_refresh() {
		add_filter( 'advanced-ads-ad-select-args', array( $this, 'additional_ad_select_args' ), 10, 3 );
		add_filter( 'advanced-ads-group-output-ad-ids', array( $this, 'group_output_ad_ids' ), 10, 5 );
		add_filter( 'advanced-ads-group-output', array($this, 'group_output'), 10, 2 );
		add_filter( 'advanced-ads-can-display', array( $this, 'can_display' ), 10, 2 );
		add_action( 'advanced-ads-output', array($this, 'ad_output'), 10, 2 );

		// manipulate number of ads that should be displayed in a group
		add_filter( 'advanced-ads-group-ad-count', array($this, 'adjust_ad_group_number'), 10, 2 );
	}

	/**
	 * Save JS query that loads group using AJAX cache-busting.
	 *
	 * @param array $args
	 * @return array $args
	 */
	public function additional_ad_select_args( $args, $method, $id ) {
		if ( ! isset( $args['cache_busting_elementid'] ) || empty( $args['group_refresh'] ) ) {
			return $args;
		}

		if (
			// Allow to track each ad of a group with refresh interval enabled only once.
			(
				$method === Advanced_Ads_Select::AD
				&& ! empty( $args['group_refresh']['shown_ad_ids'][ $id ] )
				&& isset( $args['group_info']['id'] )
				&& isset( $args['group_refresh']['group_id'] )
				&& absint( $args['group_info']['id'] ) === absint( $args['group_refresh']['group_id'] )
			)
			// Display the same group only once in the "Ads" menu of Admin Bar.
			|| ( $method === Advanced_Ads_Select::GROUP && ! empty( $args['group_refresh']['shown_group_ids'][ $id ] ) )
		) {
			$args['global_output'] = false;
			return $args;
		}

		$args['global_output'] = true;
		return $args;
	}

	/**
	 * Change ordered ids of ads that belong to the group.
	 *
	 * @param array $ordered_ad_ids
	 * @param string $type
	 * @param array $ads
	 * @param array $weights
	 * @param obj $group Advanced_Ads_Group
	 * @return array $ordered_ad_ids
	 */
	public function group_output_ad_ids( $ordered_ad_ids, $type, $ads, $weights, Advanced_Ads_Group $group ) {
		if ( ! is_array( $ordered_ad_ids ) || count( $ordered_ad_ids ) < 2 ) {
			return $ordered_ad_ids;
		}

		if ( ! self::is_enabled( $group ) ) {
			return $ordered_ad_ids;
		}

		if ( ! isset( $group->ad_args['cache_busting_elementid'] ) ) {
			return $ordered_ad_ids;
		}

		// Ignore Refresh interval in nested groups.
		if ( isset( $group->ad_args['group_refresh']['group_id'] )
			&& isset( $group->ad_args['id'] )
			&& absint( $group->ad_args['group_refresh']['group_id'] ) !== absint( $group->ad_args['id'] )
		) {
			return $ordered_ad_ids;
		}

		$el_group_id = $group->ad_args['cache_busting_elementid'] . '_' . $group->id;

		$this->state_groups[ $el_group_id ]['shown_ad_ids'] = isset( $group->ad_args['group_refresh']['shown_ad_ids'] ) ? $group->ad_args['group_refresh']['shown_ad_ids'] : array();
		$this->state_groups[ $el_group_id ]['ad_label'] = isset( $group->ad_args['ad_label'] ) ? $group->ad_args['ad_label'] : 'default';


		if ( empty( $group->ad_args['group_refresh']['prev_ad_id'] ) ) {
			// Show the first ad.
			return $ordered_ad_ids;
		}
		$prev_ad_id = absint( $group->ad_args['group_refresh']['prev_ad_id'] );


		// Do not show previously visible ad.
		switch ( $type ) {
			case 'ordered' :
				// At this point ads with the same weight will not be shuffled anymore.
				// We support the order that was formed before the first ad was shown.
				arsort( $weights );
				$ordered_ad_ids = array_keys( $weights );
				if ( false === ( $pos = array_search( $prev_ad_id , $ordered_ad_ids ) ) ) {
					return $ordered_ad_ids;
				}

				$start = array_slice( $ordered_ad_ids, 0, $pos );
				$end = array_slice( $ordered_ad_ids, $pos + 1 );
				$ordered_ad_ids = array_merge( $end, $start );
				break;
			default :
				if ( false === ( $pos = array_search( $prev_ad_id , $ordered_ad_ids ) ) ) {
					return $ordered_ad_ids;
				}
				unset( $ordered_ad_ids[ $pos ] );
		}


		return $ordered_ad_ids;
	}

	/**
	 * Add JS code that reloads the group using AJAX request.
	 *
	 * @param string $output_string
	 * @return obj $group Advanced_Ads_Group
	 */
	public function group_output( $output_string, Advanced_Ads_Group $group ) {
		if ( ! isset( $group->ad_args['cache_busting_elementid'] ) ) {
			return $output_string;
		}

		if ( ! empty( $group->ad_args['group_refresh'] ) ) {
			$this->shown_group_ids[ $group->id ] = true;
		}

		if ( ! self::is_enabled( $group ) ) {
			return $output_string;
		}

		$this->shown_group_ids[ $group->id ] = true;

		$element_id = $group->ad_args['cache_busting_elementid'];
		$el_group_id = $group->ad_args['cache_busting_elementid'] . '_' . $group->id;


		if ( ! isset( $this->state_groups[ $el_group_id ] ) ) {
			return $output_string;
		}

		// Ignore Refresh interval in nested groups.
		if ( isset( $group->ad_args['group_refresh']['group_id'] )
			&& isset( $group->ad_args['id'] )
			&& absint( $group->ad_args['group_refresh']['group_id'] ) !== absint( $group->ad_args['id'] )
		) {
			return $output_string;
		}

		if ( ! isset( $this->state_groups[ $el_group_id ]['query']['id'] ) ) {
			$is_first_impression = $this->is_first_impression( $element_id );

			if ( $is_first_impression ) {
				static $count = 0;
				$element_id .= '-' . ++$count . '-group-refresh';
			}

			$prev_ad_id = ! empty( $this->state_groups[ $el_group_id ]['prev_ad_id'] ) ? $this->state_groups[ $el_group_id ]['prev_ad_id'] : '';
			$shown_ad_ids = ! empty( $this->state_groups[ $el_group_id ]['shown_ad_ids'] ) ? $this->state_groups[ $el_group_id ]['shown_ad_ids'] : array();
			$shown_group_ids = ! empty( $this->state_groups[ $el_group_id ]['shown_group_ids'] ) ? array_merge( $this->state_groups[ $el_group_id ]['shown_group_ids'], $this->shown_group_ids ) : $this->shown_group_ids;
			$this->shown_group_ids = array();


			$query = Advanced_Ads_Pro_Module_Cache_Busting::build_js_query( $group->ad_args);
			$query = Advanced_Ads_Pro_Module_Cache_Busting::get_instance()->get_ajax_query( $query, false );

			$query['elementid'] = $element_id;
			$query['params']['group_refresh']['prev_ad_id'] = $prev_ad_id;
			$query['params']['group_refresh']['shown_ad_ids'] = $shown_ad_ids;
			$query['params']['group_refresh']['shown_group_ids'] = $shown_group_ids;
			$query['params']['group_refresh']['group_id'] = $group->id;

			if ( isset( $group->ad_args['group_refresh']['is_top_level'] ) ) {
				$is_top_level = $group->ad_args['group_refresh']['is_top_level'];
			} else {
				$is_top_level = $is_first_impression && ! empty( $group->ad_args['is_top_level'] );
			}
			$query['params']['group_refresh']['is_top_level'] = $is_top_level;

			// If it is top level, make it top level again for the next request.
			// This allows to deprecate `group_refresh > is_top_level` key from above in the future.
			if ( ! empty( $group->ad_args['is_top_level'] ) ) {
				unset( $query['params']['is_top_level'] );
			}

			if ( isset( $this->state_groups[ $el_group_id ]['ad_label'] ) ) {
				$query['params']['ad_label'] = $this->state_groups[ $el_group_id ]['ad_label'];
			}

			$this->state_groups[ $el_group_id ]['query'] = $query;


			// If the first ad was shown, do not use Lazy Load anymore.
			unset( $query['params']['lazy_load'] );
			$position = ! empty( $this->state_groups[ $el_group_id ]['position'] ) ? $this->state_groups[ $el_group_id ]['position'] : false;
			$intervals =  self::get_ad_intervals( $group );
			$interval = $intervals[ $prev_ad_id ];

			$js = '<script>(function() {';
			$js .= 'var query_id = ' . mt_rand() . ';'
				. 'if ( advanced_ads_group_refresh.element_ids[ "' . $element_id . '" ] === query_id ) {'
				. '    return;'
				. '}'
				. 'advanced_ads_group_refresh.element_ids[ "' . $element_id . '" ] = query_id;';
			$js .= sprintf( 'advanced_ads_group_refresh.prepare_wrapper( jQuery(".%s"), "%s", %d );', $element_id, $position, $is_first_impression );
			$js .= 'advanced_ads_group_refresh.add_query( ' . json_encode( $query ) . ', ' . $interval . ' );';
			$js .= '})()</script>';



			if ( $is_first_impression ) {
				$style = in_array( $position, array( 'left', 'right' ) ) ? 'float:' . $float . ';' : '';
				// Create wrapper around group. The following AJAX requests will insert group content into this wrapper.
				$output_string = $js . '<div style="' . $style . '" class="' . $element_id .  '" id="' . $element_id .  '">' . $output_string . '</div>';
			} else {
				$output_string = $js . $output_string;
			}
		}

		return $output_string;
	}

	/**
	 * Check if ad can be displayed.
	 *
	 * @param bool $return
	 * @return obj $ad Advanced_Ads_Ad
	 */
	public function can_display( $return, Advanced_Ads_Ad $ad ) {
		if ( empty( $ad->args['cache_busting_elementid'] ) || empty( $ad->args['group_info']['id'] ) ) {
			return $return;
		}

		// Check again if the placement should be displayed.
		if (
			! $this->is_first_impression( $ad->args['cache_busting_elementid'] )
			&& isset( $ad->args['output']['placement_id'] )
			&& ! apply_filters( 'advanced-ads-can-display-placement', true, $ad->args['output']['placement_id'] ) ) {
			return false;
		}

		$el_group_id = $ad->args['cache_busting_elementid'] . '_' . $ad->args['group_info']['id'];

		if ( ! empty( $this->state_groups[ $el_group_id ]['limit_exceeded'] ) ) {
			return false; 
		}

		return $return;

	}

	/**
	 * Does some actions when ad is shown.
	 *
	 * @param array $args
	 * @return array $args
	 */
	public function ad_output( Advanced_Ads_Ad $ad, $output ) {
		if ( empty( $ad->args['cache_busting_elementid'] ) || empty( $ad->args['group_info']['id'] ) ) {
			return $ad;
		}

		$el_group_id = $ad->args['cache_busting_elementid'] . '_' . $ad->args['group_info']['id'];

		if ( ! empty( $this->state_groups[ $el_group_id ] ) ) {
			// Save current ad id so that this ad will not be added to next AJAX response.
			$this->state_groups[ $el_group_id ]['prev_ad_id'] = $ad->id;
			// Do not track the same ad twice.
			$this->state_groups[ $el_group_id ]['shown_ad_ids'][ $ad->id ] = true;
			// Allow to show only 1 ad.
			$this->state_groups[ $el_group_id ]['limit_exceeded'] = 1;
		}

		// Get the position of the placement or the ad.
		if ( ! isset( $this->state_groups[ $el_group_id ]['position'] ) ) {
			if ( ! empty( $ad->args['placement_position'] ) ) {
				$this->state_groups[ $el_group_id ]['position'] = $ad->args['placement_position'];
			} elseif ( ! empty( $options['output']['position'] ) ) {
				$this->state_groups[ $el_group_id ]['position'] = $options['output']['position'];
			}
		}
	}	

	/**
	* adjust the ad group number for group refresh
	*
	* @param int $ad_count
	* @param obj $adgroup Advanced_Ads_Group
	* @return int $ad_count
	*/
	public function adjust_ad_group_number( $ad_count = 0, Advanced_Ads_Group $group ) {
		if ( self::is_enabled( $group ) ) {
			return 'all';
		}

		return $ad_count;
	}

	/**
	 * Check if group refresh is enabled.
	 *
	 * @param obj $ad Advanced_Ads_Group
	 * @return bool
	 */
	public static function is_enabled( Advanced_Ads_Group $group ) {
		$result = ! empty( $group->options['refresh']['enabled'] ) && in_array( $group->type , array( 'default', 'ordered' ) )
			&& empty( $group->ad_args['adblocker_active'] );
		return $result;
	}

	/**
	 * Get durations (in ms) of the ads that belong to the group.
	 *
	 * @param obj $ad Advanced_Ads_Group
	 * @return array
	 */
	public static function get_ad_intervals( Advanced_Ads_Group $group ) {
		$group_interval = ! empty( $group->options['refresh']['interval'] ) ? absint( $group->options['refresh']['interval'] ) : 2000;

		// An array with ad ids as keys, duration (in ms) as values.
		$ad_intervals = apply_filters( 'advanced-ads-group-refresh-intervals', array() );

		$group_ad_ids = $group->get_ordered_ad_ids();
		$group_ad_intervals = array();
		foreach( $group_ad_ids as $ad_id ) {
			$group_ad_intervals[ $ad_id] = ! empty( $ad_intervals[ $ad_id] ) ? absint( $ad_intervals[ $ad_id] ) : $group_interval;
		}
		return $group_ad_intervals;
	}

	/**
	 * Check if no ads of the group has been shown to the user yet.
	 *
	 * @param string $element_id Element Id.
	 * @return bool
	 */
	private function is_first_impression( $element_id ) {
		return '-group-refresh' !== substr( $element_id, -14 );
	}
}
