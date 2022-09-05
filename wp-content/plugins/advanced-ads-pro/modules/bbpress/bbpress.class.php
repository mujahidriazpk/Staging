<?php

class Advanced_Ads_Pro_Module_bbPress {
    
    public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'wp_plugins_loaded_ad_actions' ), 31 );
    }
    
    public function wp_plugins_loaded_ad_actions(){
	// stop, if main plugin doesn’t exist
	if ( ! class_exists( 'Advanced_Ads', false ) ) {
            return;
	}
        
        // stop if bbPress isn't activated
        if ( ! class_exists( 'bbPress', false ) ){
            return;
        }
    
	// load bbPress hooks
	// get placements
	$placements = get_option( 'advads-ads-placements', array() );
	if( is_array( $placements ) ){
		foreach ( $placements as $_placement_id => $_placement ){
			if ( isset($_placement['type']) && 'bbPress comment' == $_placement['type'] && isset( $_placement['options']['bbPress_comment_hook'] ) ){
			    $hook = str_replace( ' ', '_', 'bbp_' . $_placement['options']['bbPress_comment_hook'] );
			    add_action( $hook, array($this, 'execute_hook') );
			}
			if ( isset($_placement['type']) && 'bbPress static' == $_placement['type'] && isset( $_placement['options']['bbPress_static_hook'] ) ){
			    $hook = str_replace( ' ', '_', 'bbp_' . $_placement['options']['bbPress_static_hook'] );
			    add_action( $hook, array($this, 'execute_hook') );
			}
		}
	}
    }
    
    public function execute_hook(){
        // get placements
	$placements = get_option( 'advads-ads-placements', array() );
        // look for the current hook in the placements
	$hook = current_filter();
	if( is_array( $placements ) ){
	    foreach ( $placements as $_placement_id => $_placement ){
		if ( isset($_placement['type'] ) && 'bbPress comment' == $_placement['type']
		&& isset( $_placement['options']['pro_bbPress_comment_pages_index'] )
		&& isset( $_placement['options']['bbPress_comment_hook'] )
		&& $hook === str_replace( ' ', '_', 'bbp_' . $_placement['options']['bbPress_comment_hook'] ) ){
		    if( did_action( $hook ) == $_placement['options']['pro_bbPress_comment_pages_index'] ){
		       the_ad_placement( $_placement_id ); 
		    }
		} elseif ( isset($_placement['type'] ) && 'bbPress static' == $_placement['type']
		&& isset( $_placement['options']['bbPress_static_hook'] )
		&& $hook === str_replace( ' ', '_', 'bbp_' . $_placement['options']['bbPress_static_hook'] ) ) {
		    the_ad_placement( $_placement_id ); 
		}
	    }
	}
    }
    
}

