<?php

class Advanced_Ads_Pro_Module_bbPress_Admin {
    
    public function __construct() {
        // stop, if main plugin doesn’t exist
	if ( ! class_exists( 'Advanced_Ads', false ) ) {
            return;
	}
        
        // stop if bbPress isn't activated
        if ( ! class_exists( 'bbPress', false ) ){
            return;
        }
        
        // add sticky placement
	add_action( 'advanced-ads-placement-types', array( $this, 'add_placement' ) );
        // content of sticky placement
	add_action( 'advanced-ads-placement-options-after', array( $this, 'placement_options' ), 10, 2 );
        
    }
    
    public function add_placement($types){
        //ad injection on a bbPress forum
        $types['bbPress static'] = array(
            'title' => __( 'bbPress Static Content', 'advanced-ads-pro' ),
            'description' => __( 'Display ads on bbPress related pages.', 'advanced-ads-pro' ),
            'image' => AAP_BASE_URL . 'modules/bbpress/assets/img/bbpress-static.png',
            'order' => 32,
        );
        $types['bbPress comment'] = array(
            'title' => __( 'bbPress Reply Content', 'advanced-ads-pro' ),
            'description' => __( 'Display ads in bbPress replies.', 'advanced-ads-pro' ),
            'image' => AAP_BASE_URL . 'modules/bbpress/assets/img/bbpress-reply.png',
            'order' => 33,
        );
        return $types;
    }
    
    public function placement_options( $placement_slug = '', $placement = array() ){
	if( 'bbPress static' === $placement['type'] ){
            $bbPress_static_positions = $this->get_bbPress_static_hooks();
            $current = isset($placement['options']['bbPress_static_hook']) ? $placement['options']['bbPress_static_hook'] : '';
            ?><label><?php _e( 'position', 'advanced-ads-pro' ); ?></label>
            <select name="advads[placements][<?php echo $placement_slug; ?>][options][bbPress_static_hook]">
                <option>---</option>
                <?php foreach( $bbPress_static_positions as $_group => $_positions ) : ?>
                    <optgroup label="<?php echo $_group; ?>">
                <?php foreach( $_positions as $_position ) : ?>
                    <option <?php selected( $_position, $current ); ?>><?php echo $_position; ?></option>
                <?php endforeach; ?>
                    </optgroup>
                <?php endforeach; ?>
            </select>
            <?php
	}
        if ('bbPress comment' === $placement['type']) {
            $bbPress_comment_positions = $this->get_bbPress_comment_hooks();
            $current = isset($placement['options']['bbPress_comment_hook']) ? $placement['options']['bbPress_comment_hook'] : '';
            ?><label><?php _e('position', 'advanced-ads-pro'); ?></label>
                        <select name="advads[placements][<?php echo $placement_slug; ?>][options][bbPress_comment_hook]">
                            <option>---</option>
            <?php foreach ($bbPress_comment_positions as $_group => $_positions) : ?>
                                    <optgroup label="<?php echo $_group; ?>">
                <?php foreach ($_positions as $_position) : ?>
                                        <option <?php selected($_position, $current); ?>><?php echo $_position; ?></option>
                <?php endforeach; ?>
                                    </optgroup>
            <?php endforeach; ?>
                        </select>
            <?php
            $index = (isset($placement['options']['pro_bbPress_comment_pages_index'])) ? Advanced_Ads_Pro_Utils::absint( $placement['options']['pro_bbPress_comment_pages_index'], 1 ) : 1;
            ?><br><?php
            $index_option = '<input type="number" required="required" min="1" name="advads[placements][' . $placement_slug . '][options][pro_bbPress_comment_pages_index]" value="'
                    . $index . '"/>';
            printf(__('Inject at %s. post', 'advanced-ads-pro'), $index_option);
        }
    }

    public function get_bbPress_static_hooks(){
        return array(
            __( 'forum topic page', 'advanced-ads-pro' ) => array(
                'template after replies loop',
                'template before replies loop',
            ),
            __( 'single forum page', 'advanced-ads-pro' ) => array(
                'template after single forum',
                'template before single forum'
            ),
            __( 'forums page', 'advanced-ads-pro' ) => array(
                'template after forums loop',
                'template before forums loop'      
            )
        );
    }
    
    public function get_bbPress_comment_hooks(){
        return array(
            __( 'forum topic page', 'advanced-ads-pro' ) => array(
                'theme after reply content',
                'theme before reply content',
                'theme after reply author admin details'
            )
        );
    }
    
}

