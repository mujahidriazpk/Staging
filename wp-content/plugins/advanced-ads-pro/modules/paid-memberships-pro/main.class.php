<?php

/**
 * Display and Visitor Condition for PaidMembershipsPro plugin
 * 
 * latest PMP version checked: 1.8.13.6
 */
class Advanced_Ads_Pro_Module_PaidMembershipsPro {

	protected $options = array();

	public function __construct() {
		
		add_filter( 'advanced-ads-display-conditions', array( $this, 'display_conditions' ) );
		add_filter( 'advanced-ads-visitor-conditions', array( $this, 'visitor_conditions' ) );
	}

	/**
	 * add PMP display condition
	 *
	 * @param arr $conditions display conditions
	 * @return arr $conditions new global display conditions
	 */
	public function display_conditions( $conditions ){
	    
		if( defined( 'PMPRO_VERSION' ) ){
			// language set with the WPML plugin
			$conditions['pmp_membership_level'] = array(
				'label' => __( 'PMP page level', 'advanced-ads-pro' ),
				'description' => __( 'Display ads only on pages that need a specific membership level set with PaidMembershipsPro.', 'advanced-ads-pro' ),
				'metabox' => array( 'Advanced_Ads_Pro_Module_PaidMembershipsPro', 'metabox_pmp_membership_level_display_condition' ), // callback to generate the metabox
				'check' => array( 'Advanced_Ads_Pro_Module_PaidMembershipsPro', 'check_pmp_membership_level_display_condition' ) // callback for frontend check
			);
		}
	    

		return $conditions;
	}
	
	/**
	 * check PMP membership level condition in frontend
	 *
	 * @since 1.14
	 * @param arr $options options of the condition
	 * @param obj $ad Advanced_Ads_Ad
	 * @return bool true if can be displayed
	 */
	static function check_pmp_membership_level_display_condition($options = array(), Advanced_Ads_Ad $ad) {

	    if (!isset($options['value']) || !is_array($options['value'])) {
		return false;
	    }

	    if (isset($options['operator']) && $options['operator'] === 'is_not') {
		$operator = 'is_not';
	    } else {
		$operator = 'is';
	    }
	    
	    global $wpdb;
	    $ad_options = $ad->options();
	    $post_id = isset($ad_options['post']['id']) ? $ad_options['post']['id'] : null;

	    if( $post_id && defined( 'PMPRO_VERSION' ) ){
		// get membership level of current page
		$page_levels = $wpdb->get_col("SELECT membership_id FROM {$wpdb->pmpro_memberships_pages} WHERE page_id = '{$post_id}'");
		
		if ( is_array( $page_levels ) ){
			switch( $operator ){
			    case 'is_not' :
				return ! count( array_intersect( $options['value'], $page_levels ) );
				break;
			    default :
				return count( array_intersect( $options['value'], $page_levels ) );
			}
		}
	    }

	    return true;
	}
	
	/**
	 * callback to display the PMP membership level condition
	 *
	 * @since 1.14
	 * @param arr $options options of the condition
	 * @param int $index index of the condition
	 */
	static function metabox_pmp_membership_level_display_condition( $options, $index = 0, $form_name = '' ){
	    
	    global $wpdb;

	    if ( ! isset ( $options['type'] ) || '' === $options['type'] ) { return; }
	    
	    $type_options = Advanced_Ads_Display_Conditions::get_instance()->conditions;

	    if (!isset($type_options[$options['type']])) {
		return;
	    }

	    // form name basis
		$name = Advanced_Ads_Display_Conditions::get_form_name_with_index( $form_name, $index );

	    // options
	    $values = ( isset($options['value']) && is_array($options['value']) ) ? $options['value'] : array();
	    $operator = ( isset($options['operator']) && $options['operator'] === 'is_not' ) ? 'is_not' : 'is';
	    
	    // get values and select operator based on previous settings

	    ?><input type="hidden" name="<?php echo $name; ?>[type]" value="<?php echo $options['type']; ?>"/>
	    <select name="<?php echo $name; ?>[operator]">
		<option value="is" <?php selected('is', $operator); ?>><?php _e('is', 'advanced-ads-pro'); ?></option>
		<option value="is_not" <?php selected('is_not', $operator); ?>><?php _e('is not', 'advanced-ads-pro'); ?></option>
	    </select><?php
	    
	    // get all levels
	    $levels = $wpdb->get_results( "SELECT * FROM {$wpdb->pmpro_membership_levels}", OBJECT );
	    ?><div class="advads-conditions-single advads-buttonset"><?php
		$rand = md5( $form_name );
	    if( is_array( $levels ) && count( $levels ) ){
		foreach( $levels as $_level ) {
		    $value = ( $values === array() || in_array($_level->id, $values) ) ? 1 : 0;
			$field_id = 'advads-visitor-conditions-' . sanitize_title( $_level->id ) . $rand;
			?><label class="button ui-button" for="<?php echo $field_id;
				?>"><?php echo $_level->name; ?></label><input type="checkbox" id="<?php echo $field_id; ?>" name="<?php echo $name; ?>[value][]" <?php checked($value, 1); ?> value="<?php echo $_level->id; ?>"><?php
		}
	    } else {
		_e( 'No membership levels set up yet.', 'advanced-ads-pro' );
	    }
	    ?></div>
	    
	    <p class="description"><?php echo $type_options[ $options['type'] ]['description']; ?></p><?php
	}	
	
	/**
	 * add PMP visitor condition
	 *
	 * @param arr $conditions visitor conditions
	 * @return arr $conditions new global visitor conditions
	 */
	public function visitor_conditions( $conditions ){
	    
		if( defined( 'PMPRO_VERSION' ) ){
			// language set with the WPML plugin
			$conditions['pmp_user_level'] = array(
				'label' => __( 'PMP user level', 'advanced-ads-pro' ),
				'description' => __( 'Display ads only to users with a specific membership level set with PaidMembershipsPro.', 'advanced-ads-pro' ),
				'metabox' => array( 'Advanced_Ads_Pro_Module_PaidMembershipsPro', 'metabox_pmp_membership_level_visitor_condition' ), // callback to generate the metabox
				'check' => array( 'Advanced_Ads_Pro_Module_PaidMembershipsPro', 'check_pmp_membership_level_visitor_condition' ) // callback for frontend check
			);
		}
	    
		return $conditions;
	}
	
	/**
	 * check PMP membership level visitor condition in frontend
	 *
	 * @since 1.14
	 * @param arr $options options of the condition
	 * @param obj $ad Advanced_Ads_Ad
	 * @return bool true if can be displayed
	 */
	static function check_pmp_membership_level_visitor_condition($options = array(), Advanced_Ads_Ad $ad) {

	    if (!isset($options['value']) || ! is_array($options['value'])) {
		return false;
	    }

	    if (isset($options['operator']) && $options['operator'] === 'is_not') {
		$operator = 'is_not';
	    } else {
		$operator = 'is';
	    }
	    
	    if( defined( 'PMPRO_VERSION' ) && function_exists( 'pmpro_getMembershipLevelForUser' ) ){
		// get membership level of current user
		$current_user = wp_get_current_user();
		$membership_level = pmpro_getMembershipLevelForUser( $current_user->ID );
		
		if ( isset( $membership_level->ID ) ){
			switch( $operator ){
			    case 'is_not' :
				return ! in_array( $membership_level->ID, $options['value'] );
				break;
			    default :
				return in_array( $membership_level->ID, $options['value'] );
			}
		} elseif( ! isset( $membership_level->ID ) && count( $options['value'] ) ){ // return false, if ad needs a membership level, but user has none
			return $operator === 'is_not';
		}
	    }

	    return true;
	}
	
	/**
	 * callback to display the PMP membership level visitor condition
	 *
	 * @since 1.14
	 * @param arr $options options of the condition
	 * @param int $index index of the condition
	 */
	static function metabox_pmp_membership_level_visitor_condition( $options, $index = 0, $form_name = '' ){
	    
	    global $wpdb;

	    if ( ! isset ( $options['type'] ) || '' === $options['type'] ) { return; }
	    
	    $type_options = Advanced_Ads_Visitor_Conditions::get_instance()->conditions;

	    if (!isset($type_options[$options['type']])) {
		return;
	    }

	    // form name basis
		$name = Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions::get_form_name_with_index( $form_name, $index );

	    // options
	    $values = ( isset($options['value']) && is_array($options['value']) ) ? $options['value'] : array();
	    $operator = ( isset($options['operator']) && $options['operator'] === 'is_not' ) ? 'is_not' : 'is';
	    
	    // get values and select operator based on previous settings

	    ?><input type="hidden" name="<?php echo $name; ?>[type]" value="<?php echo $options['type']; ?>"/>
	    <select name="<?php echo $name; ?>[operator]">
		<option value="is" <?php selected('is', $operator); ?>><?php _e('is', 'advanced-ads-pro'); ?></option>
		<option value="is_not" <?php selected('is_not', $operator); ?>><?php _e('is not', 'advanced-ads-pro'); ?></option>
	    </select><?php
	    
	    // get all levels
	    $levels = $wpdb->get_results( "SELECT * FROM {$wpdb->pmpro_membership_levels}", OBJECT );
		$rand = md5( $form_name );
	    ?><div class="advads-conditions-single advads-buttonset"><?php
	    if( is_array( $levels ) && count( $levels ) ){
		foreach( $levels as $_level ) {
		    $value = ( $values === array() || in_array($_level->id, $values) ) ? 1 : 0;
			$field_id = 'advads-visitor-conditions-' . sanitize_title( $_level->id ) . $rand;
			?><label class="button ui-button" for="<?php echo $field_id
				?>"><?php echo $_level->name; ?></label><input type="checkbox" id="<?php echo $field_id; ?>" name="<?php echo $name; ?>[value][]" <?php checked($value, 1); ?> value="<?php echo $_level->id; ?>"><?php
		}
	    } else {
		_e( 'No membership levels set up yet.', 'advanced-ads-pro' );
	    }
	    ?></div>
	    
	    <p class="description"><?php echo $type_options[ $options['type'] ]['description']; ?></p><?php
	}		

}
