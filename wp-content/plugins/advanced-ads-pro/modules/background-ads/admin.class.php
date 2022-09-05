<?php

class Advanced_Ads_Pro_Module_Background_Ads_Admin {
    
	public function __construct() {
	    // stop, if main plugin doesn’t exist
	    if ( ! class_exists( 'Advanced_Ads', false ) ) {
		return;
	    }

	    // add background ads placement
	    add_action( 'advanced-ads-placement-types', array( $this, 'add_placement' ) );
	    // content of background ads placement
	    add_action( 'advanced-ads-placement-options-after-advanced', array( $this, 'placement_options' ), 10, 2 );

	    add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	    add_action( 'advanced-ads-placements-list-after', array( $this, 'placements_list_after' ) );

	}

	public function add_placement($types){
	    //ad injection on a bbPress forum
	    $types['background'] = array(
		'title' => __( 'Background Ad', 'advanced-ads-pro' ),
		'description' => __( 'Background of the website behind the main wrapper.', 'advanced-ads-pro' ),
		'image' => AAP_BASE_URL . 'modules/background-ads/assets/img/background.png',
		'order' => 70,
	    );
	    return $types;
	}

	public function placement_options( $placement_slug = '', $placement = array() ){
	    if( 'background' === $placement['type'] ){
		    $bg_color = ( isset($placement['options']['bg_color']) ) ? $placement['options']['bg_color'] : '';
		    $option_content = '<input type="text" value="'. $bg_color .'" class="advads-bg-color-field" name="advads[placements]['. $placement_slug . '][options][bg_color]"/>';
		    $description = __( 'Select a background color in case the background image is not high enough to cover the whole screen.', 'advanced-ads-pro' );
		    if( class_exists( 'Advanced_Ads_Admin_Options' ) ){
			Advanced_Ads_Admin_Options::render_option( 
				'placement-background-color', 
				__( 'background', 'advanced-ads-pro' ),
				$option_content,
				$description );
		    }
	    }

	}
    
	/**
	 * add color picker script to placements page
	 *
	 * @since 1.8
	 */
	function admin_scripts( ) {

	    if( ! class_exists( 'Advanced_Ads_Admin' ) ) {
		    return;
	    };

	    $screen = get_current_screen();
	    if ( 'advanced-ads_page_advanced-ads-placements' === $screen->id ){
		    // add color picker script
		    wp_enqueue_style( 'wp-color-picker' );
		    wp_enqueue_script( 'wp-color-picker' );
	    }
	}    
    
	/**
	 * render content after the placements list
	 *  activate color picker fields
	 *
	 * @since 1.8
	 * @param type $placements array with placements
	 */
	public function placements_list_after( $placements = array() ){
		?><script>
		jQuery(document).ready(function($){
			jQuery('.advads-bg-color-field').wpColorPicker();
		});
		</script><?php
	}    
}

