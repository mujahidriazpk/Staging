<?php

/**
 * Manage backend-facing logic for GamiPress integration
 */
class Advanced_Ads_Pro_Module_GamiPress_Admin {
	/**
	 * Array of GamiPress visitor conditions.
	 *
	 * @var array
	 */
	private $conditions;

	/**
	 * Advanced_Ads_Pro_Module_BuddyPress_Admin constructor.
	 */
	public function __construct() {
		add_filter( 'advanced-ads-visitor-conditions', array( $this, 'visitor_conditions' ) );
		$this->conditions = array(
			'gamipress_points'      => array(
				'label'       => __( 'GamiPress Points', 'advanced-ads-pro' ),
				'description' => __( 'Display ads based on GamiPress user points', 'advanced-ads-pro' ),
				'metabox'     => array( $this, 'points_metabox' ),
				'check'       => array( 'Advanced_Ads_Pro_Module_GamiPress', 'check_points_visitor_condition' ),
			),
			'gamipress_rank'        => array(
				'label'       => __( 'GamiPress Rank', 'advanced-ads-pro' ),
				'description' => __( 'Display ads based on GamiPress user rank', 'advanced-ads-pro' ),
				'metabox'     => array( $this, 'achievement_rank_metabox' ),
				'check'       => array( 'Advanced_Ads_Pro_Module_GamiPress', 'check_rank_visitor_condition' ),
			),
			'gamipress_achievement' => array(
				'label'       => __( 'GamiPress Achievement', 'advanced-ads-pro' ),
				'description' => __( 'Display ads based on GamiPress user achievement', 'advanced-ads-pro' ),
				'metabox'     => array( $this, 'achievement_rank_metabox' ),
				'check'       => array( 'Advanced_Ads_Pro_Module_GamiPress', 'check_achievement_visitor_condition' ),
			),
		);
	}

	/**
	 * Add visitor condition for GamiPress fields
	 *
	 * @param array $conditions array of registered visitor conditions.
	 *
	 * @return array
	 */
	public function visitor_conditions( $conditions ) {
		return array_merge( $conditions, $this->conditions );
	}

	/**
	 * Render visitor conditions for achievements and ranks.
	 *
	 * @param array  $options   condition options.
	 * @param int    $index     index of the option.
	 * @param string $form_name name of the form.
	 */
	public function achievement_rank_metabox( $options, $index, $form_name ) {
		$type           = $options['type'];
		$condition      = $this->conditions[ $type ];
		$name           = Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions::get_form_name_with_index( $form_name, $index );
		$operator       = isset( $options['operator'] ) ? $options['operator'] : 'is';
		$selected_value = isset( $options['value'] ) ? $options['value'] : 0;
		$values         = $this->get_possible_values( $this->get_possible_types( $options['type'] ) );

		if ( $type === 'gamipress_achievement' ) {
			include __DIR__ . '/views/visitor-condition-achievement.php';
		} else {
			$values = $this->order_ranks_by_priority( $values );
			include __DIR__ . '/views/visitor-condition-rank.php';
		}
	}

	/**
	 * Render visitor conditions for points.
	 *
	 * @param array  $options   condition options.
	 * @param int    $index     index of the option.
	 * @param string $form_name name of the form.
	 */
	public function points_metabox( $options, $index, $form_name ) {
		$type                 = $options['type'];
		$condition            = $this->conditions[ $type ];
		$name                 = Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions::get_form_name_with_index( $form_name, $index );
		$operator             = isset( $options['operator'] ) ? $options['operator'] : 'is';
		$point_ids            = $this->get_posts( 'points-type' );
		$points               = array_combine( $point_ids, array_map( static function( $post_id ) {
			return get_post_meta( $post_id, '_gamipress_plural_name', true );
		}, $point_ids ) );
		$selected_points_type = isset( $options['points'] ) ? $options['points'] : 0;
		$value                = isset( $options['value'] ) ? $options['value'] : 0;

		include __DIR__ . '/views/visitor-condition-points.php';
	}

	/**
	 * Get an array of achievements/rank for each achievement/rank type.
	 *
	 * @param int[] $types Post ids for type post types.
	 *
	 * @return array
	 */
	private function get_possible_values( $types ) {
		return array_filter( array_combine(
		// get the type name as optgroup title
			array_map( static function( $post_id ) {
				return get_the_title( $post_id );
			}, $types ),
			// get the actual values as $post_id => name
			array_map( function( $post_id ) {
				$children = $this->get_posts( get_post_field( 'post_name', $post_id ) );

				return array_combine( $children, array_map( static function( $post_id ) {
					return get_the_title( $post_id );
				}, $children ) );
			}, $types )
		) );
	}

	/**
	 * Get array of post ids for $post_type.
	 *
	 * @param string $post_type The post type to search for.
	 *
	 * @return int[]
	 */
	private function get_posts( $post_type ) {
		return ( new WP_Query( array(
			'post_type'      => $post_type,
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'fields'         => 'ids',
		) ) )->posts;
	}

	/**
	 * Get an array of post ids for achievement/rank types.
	 *
	 * @param string $type Post type name.
	 *
	 * @return int[]
	 */
	private function get_possible_types( $type ) {
		if ( $type === 'gamipress_achievement' ) {
			return $this->get_posts( 'achievement-type' );
		}

		if ( $type === 'gamipress_rank' ) {
			return $this->get_posts( 'rank-type' );
		}

		return array();
	}

	/**
	 * As ranks have priorities, we want to sort them based on it.
	 *
	 * @param array $ranks Array of ranks in form [int $post_id => string $post_title].
	 *
	 * @return array
	 */
	private function order_ranks_by_priority( array $ranks ) {
		foreach ( $ranks as &$sub_ranks ) {
			uksort( $sub_ranks, static function( $a, $b ) {
				$a_priority = gamipress_get_rank_priority( $a );
				$b_priority = gamipress_get_rank_priority( $b );

				if ( $a_priority === $b_priority ) {
					return 0;
				}

				return ( $a_priority < $b_priority ) ? -1 : 1;
			} );
		}

		return $ranks;
	}
}
