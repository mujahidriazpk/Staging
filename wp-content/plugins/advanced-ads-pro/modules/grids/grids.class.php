<?php

class Advanced_Ads_Pro_Module_Grids {

	public function __construct() {
		// add group type
		add_filter( 'advanced-ads-group-types', array( $this, 'add_group_type' ) );
		
		add_filter( 'advanced-ads-group-output-ad-ids', array( $this, 'output_ad_ids' ), 10, 5 );
		// manipulate number of ads that should be displayed in a group
		add_filter( 'advanced-ads-group-ad-count', array($this, 'adjust_ad_group_number'), 10, 2 );
		// frontend output
		add_filter( 'advanced-ads-group-output-array', array( $this, 'output_markup'), 10, 2 );
		// add grid markup to passive cache-busting.
		add_filter( 'advanced-ads-pro-passive-cb-group-data', array( $this, 'add_grid_markup_passive' ), 10, 3 );

	}

	/**
	 * add grid group type
	 *
	 * @param arr $group_types existing group types
	 * @return arr $group_types group types with the new grid group
	 */
	public function add_group_type( array $group_types ){

	    $group_types['grid'] = array(
		    'title' => __( 'Grid', 'advanced-ads-pro' ),
		    'description' => __( 'Display multiple ads in a grid', 'advanced-ads-pro' ),
	    );
	    return $group_types;
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
	public function output_ad_ids( $ordered_ad_ids, $type, $ads, $weights, $group ){
	    
	    // for some reason a client had an issue with $group not being the correct class
	    if( ! $group instanceof Advanced_Ads_Group ){
		    return $ordered_ad_ids;
	    }
	    
	    if( $type === 'grid' ){
			if(isset($group->options['grid']['random'])){
				return $group->shuffle_ads($ads, $weights);
			} else {
				return array_keys($weights);
			}
	    }

	    // return default
	    return $ordered_ad_ids;
	}

	/**
	 * adjust the ad group number if the ad type is a grid
	 *
	 * @param int $ad_count
	 * @param obj $group Advanced_Ads_Group
	 * @return int $ad_count
	 */
	public function adjust_ad_group_number( $ad_count = 0, $group ){
	    
	    if( $group->type === 'grid' ){
		    // calculate number from column and rows settings
		    return absint( $group->options['grid']['columns'] ) * absint( $group->options['grid']['rows'] );
	    }

	    return $ad_count;
	}
	
	/**
	 * add extra output markup for grid
	 *
	 * @param arr $ad_content array with ad contents
	 * @param obj $group Advanced_Ads_Group
	 * @return arr $ad_content with extra markup
	 */
	public function output_markup( array $ad_content, Advanced_Ads_Group $group ){

		if( count( $ad_content ) <= 1 || 'grid' !== $group->type ) {
		    return $ad_content;
		}

		$markup = $this->get_grid_markup( $group );
		// generate grid html
		$i = 1;
		foreach ( $ad_content as $_key => $_content ) {

			foreach ( $markup['each'] as $_column_index => $_format ) {
				if ( $_column_index === 'all' || $i % $_column_index == 0 ) {
					$ad_content[ $_key ] = sprintf( $_format, $_content );
					break;
				}
			}
			$i++;
		}

		array_unshift( $ad_content, $markup['before'] );
		array_push( $ad_content, $markup['after'] );


		return $ad_content;
	}

	/**
	 * Add grid markup to passive cache-busting.
	 *
	 * @param arr $group_data
	 * @param obj $group Advanced_Ads_Group
	 * @param string $element_id
	 */
	public function add_grid_markup_passive( $group_data, Advanced_Ads_Group $group, $element_id ) {
		if ( $element_id && 'grid' === $group->type  ) {
			$group_data['random'] = isset( $group->options['grid']['random'] );
			$group_data['group_wrap'][] = $this->get_grid_markup( $group );
		}

		return $group_data;
	}


	/**
	 * Get markup to inject around each ad and around entire set of ads.
	 *
	 * @param arr $ad_content array with ad contents
	 * @param obj $group Advanced_Ads_Group
	 * @return arr
	 */
	public function get_grid_markup( Advanced_Ads_Group $group ) {
		$columns =  absint( $group->options['grid']['columns'] );
		// $rows =	    absint( $group->options['grid']['rows'] );
		
		$prefix = Advanced_Ads_Plugin::get_instance()->get_frontend_prefix();
		$grid_id = $prefix . 'grid-' . $group->id;

		// get min width of an item
		$min_width =	absint( $group->options['grid']['min_width'] );

		// get breakpoint at which the full width idems are displayed
		$full_width_breakpoint = isset( $group->options['grid']['full_width_breakpoint'] ) ? absint( $group->options['grid']['full_width_breakpoint'] ) : false;
		
		// get column margin in percent
		$inner_margin =	absint( $group->options['grid']['inner_margin'] );
		
		// calculate column width
		$width = absint( ( 100 - ( $columns - 1 ) * $inner_margin ) / $columns );
		
		// generate styles
		$css = "<style>#$grid_id{list-style:none;margin:0;padding:0;overflow:hidden;}"
			. "#$grid_id>li{float:left;width:$width%;min-width:{$min_width}px;list-style:none;margin:0 $inner_margin% $inner_margin% 0;;padding:0;overflow:hidden;}"
			. "#$grid_id>li.last{margin-right:0;}"
			. "#$grid_id>li.last+li{clear:both;}";

		// add media query if there was a full width breakpoint set
		if( !empty( $full_width_breakpoint ) ){
		    $css .= "@media only screen and (max-width:{$full_width_breakpoint}px){#$grid_id>li{width:100%;}}";
		}
			
		$css .= "</style>";

		$result = array(
			'before' => '<ul id="' . $grid_id . '">',
			'after' => '</ul>' . $css,
			'each' => array(
				$columns => '<li class="last">%s</li>',
				'all' => '<li>%s</li>',
			),
			'min_ads' => 2,
		);

		return $result;
	}



}
