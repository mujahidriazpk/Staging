<?php
class Advanced_Ads_Pro_Weekdays {

	public function __construct() {
		$is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

		if ( ! is_admin() ) {
			add_filter( 'advanced-ads-pro-passive-cb-for-ad', array( $this, 'add_passive_cb_for_ad' ), 10, 2 );
		} elseif ( ! $is_ajax ) {
			add_action( 'post_submitbox_misc_actions', array( $this, 'add_weekday_options' ) );
			add_filter( 'advanced-ads-save-options', array( $this, 'save_weekday_options' ), 10, 2 );
			add_filter( 'advanced-ads-ad-list-column-filter', array( $this, 'ad_list_column_filter' ), 10, 3 );
			add_filter( 'advanced-ads-ad-list-filter', array( $this, 'ad_list_filter' ), 10, 2 );
			add_action( 'advanced-ads-ad-list-timing-column-after', array( $this, 'render_ad_planning_column' ), 10, 2 );
		}

		add_filter( 'advanced-ads-can-display', array( $this, 'can_display_by_weekday' ), 10, 3 );
	}

	/**
	 * Pass day indexes to passive cache-busting.
	 *
	 * @param arr $passive_cb_for_ad
	 * @param obj $ad Advanced_Ads_Ad
	 */
	public function add_passive_cb_for_ad( $passive_cb_for_ad, Advanced_Ads_Ad $ad  ) {
		$ad_options = $ad->options();

		if ( ! empty( $ad_options['weekdays']['enabled'] ) ) {
			if ( ! empty( $ad_options['weekdays']['day_indexes'] ) ) {
				$passive_cb_for_ad['day_indexes'] = self::sanitize_day_indexes( $ad_options['weekdays']['day_indexes'] );
			} else {
				$passive_cb_for_ad['day_indexes'] = array();
			}
		}

		return $passive_cb_for_ad;
	}

	/**
	 * Add options above the 'Publish' button.
	 */
	public function add_weekday_options() {
		global $post, $wp_locale;

		if ( $post->post_type !== Advanced_Ads::POST_TYPE_SLUG ) { return; }

		$ad = new Advanced_Ads_Ad( $post->ID );
		$options = $ad->options();

		$enabled = ! empty( $options['weekdays']['enabled'] );
		$day_indexes = ! empty( $options['weekdays']['day_indexes'] ) ? self::sanitize_day_indexes( $options['weekdays']['day_indexes'] ) : array();

		$TZ = Advanced_Ads_Admin::timezone_get_name( Advanced_Ads_Admin::get_wp_timezone() );

		include dirname( __FILE__ ) . '/views/ad-submitbox-meta.php';
	}

	/**
	 * Save options above the 'Publish' button.
	 * @param arr $options
	 * @param obj $ad Advanced_Ads_Ad
	 */
	public function save_weekday_options( $options = array(), Advanced_Ads_Ad $ad ) {
		$options['weekdays']['enabled'] = ! empty( $_POST['advanced_ad']['weekdays']['enabled'] );

		if ( isset( $_POST['advanced_ad']['weekdays']['day_indexes'] ) ) {
			$options['weekdays']['day_indexes'] = self::sanitize_day_indexes( $_POST['advanced_ad']['weekdays']['day_indexes'] );
		} else {
			$options['weekdays']['day_indexes'] = array();
		}

		return $options;
	}

	/**
	 * Add new item to the filter above the ad list.
	 * @param arr $timing_filter
	 * @return arr $timing_filter
	 */
	public function add_item_to_frontend_filter( $timing_filter ) {
		$timing_filter['advads-pro-filter-specific-days'] = __( 'specific days', 'advanced-ads-pro' );
		return $timing_filter;
	}

	/**
	 * Add new item to the filter above the ad list.
	 *
	 * @param array $all_filters Existing filters.
	 * @param object $post WP_Post
	 * @param array $options Ad options.
	 * @return array $all_filters New filters.
	 */
	public function ad_list_column_filter( $all_filters, $post, $options ) {
		if ( ! empty( $options['weekdays']['enabled'] ) ) {
			if ( ! array_key_exists( 'advads-pro-filter-specific-days', $all_filters['all_dates'] ) ) {
				$all_filters['all_dates']['advads-pro-filter-specific-days'] = __( 'specific days', 'advanced-ads-pro' );
			}
		}
		return $all_filters;
	}

	/**
	 * Filter the ad list.
	 *
	 * @param array $posts Post list.
	 * @param array $all_ads_options Ad options.
	 * @return array $posts Post list.
	 */
	public function ad_list_filter( $posts, $all_ads_options ) {
		if ( isset( $_REQUEST['addate'] ) && 'advads-pro-filter-specific-days' ==  urldecode( $_REQUEST['addate'] ) ) {
			$new_posts = array();
			foreach ( $posts as $post ) {
				if ( ! empty( $all_ads_options[ $post->ID ]['weekdays']['enabled'] ) ) {
					$new_posts[] = $post;
				}
			}
			$posts = $new_posts;
		}

		return $posts;
	}


	/**
	 * Show weekdays on the ad list page.
	 *
	 * @param obj $ad Advanced_Ads_Ad
	 * @param str $html_classes existing html classes
	 */
	public function render_ad_planning_column( Advanced_Ads_Ad $ad, &$html_classes = '' ) {
		$options = $ad->options();
		$enabled = ! empty( $options['weekdays']['enabled'] );

		if ( $enabled ) {
			global $wp_locale;

			$html_classes .= ' advads-pro-filter-specific-days';

			$day_indexes = ! empty( $options['weekdays']['day_indexes'] ) ? self::sanitize_day_indexes( $options['weekdays']['day_indexes'] ) : array();
			$day_names = array_map( array( $wp_locale, 'get_weekday' ), $day_indexes ); ?>
			<p><?php
			if ( $day_names ) :
				printf( __( 'Shows up on: %s', 'advanced-ads-pro' ), implode( ', ', $day_names ) );
			else :
				_e( 'Never shows up', 'advanced-ads-pro' );
			endif;
			?></p>
			<?php
		}
	}

	/**
	 * Sanitize day indexes.
	 *
	 * @param arr $day_indexes
	 * @return arr with allowed day indexes
	 */
	public static function sanitize_day_indexes( $day_indexes ) {
		if ( ! is_array( $day_indexes ) ) { return array(); }

		foreach ( $day_indexes as $_key => &$_index ) {
			$_index = absint( $_index );

			if ( $_index > 6 ) {
				unset( $day_indexes[ $_key ] );
			}
		}

		return array_unique( array_values( $day_indexes) );
	}

	/**
	 * Determine if ad can be displayed today based on weekday
	 *
	 * @param bool            $can_display   value as set so far
	 * @param Advanced_Ads_Ad $ad            Advanced_Ads_Ad
	 * @param array           $check_options Check options.
	 *
	 * @return bool false if can’t be displayed, else return $can_display
	 */
	public function can_display_by_weekday( $can_display, Advanced_Ads_Ad $ad, $check_options ) {
		if ( ! $can_display ) {
			return false;
		}

		if ( ! empty( $check_options['passive_cache_busting'] ) ) {
			return $can_display;
		}

		$options = $ad->options();
		if ( empty( $options['weekdays']['enabled'] ) || empty( $options['weekdays']['day_indexes'] ) ) {
			return $can_display;
		}

		// current_datetime is available since WP 5.3.0
		$date = function_exists( 'current_datetime' ) ? current_datetime() : date_create_immutable( 'now', Advanced_Ads_Utils::get_wp_timezone() );

		return in_array(
			(int) $date->format( 'w' ),
			self::sanitize_day_indexes( $options['weekdays']['day_indexes'] ),
			true
		);
	}
}
