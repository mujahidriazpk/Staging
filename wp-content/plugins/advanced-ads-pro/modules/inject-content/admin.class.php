<?php
/**
 * Inject content admin
 */
class Advanced_Ads_Pro_Module_Inject_Content_Admin {

	public function __construct() {
		// options for custom position placement
		add_action( 'advanced-ads-placement-options-after', array( $this, 'custom_position_placement_options' ), 11, 2 );
		// load frontend picker script
		add_action( 'advanced-ads-placements-list-after', array( $this, 'frontend_picker_script' ) );
		// add minimum length setting for content injection placements
		add_action( 'advanced-ads-placement-options-after-advanced', array( $this, 'minimum_content_length_option' ), 10, 2 );
		add_action( 'advanced-ads-placement-options-after-advanced', array( $this, 'in_any_loop_archive_pages_option' ), 10, 2 );
		// Render setting that allow to prevent injection inside `the_content`
		add_action( 'advanced_ads_render_post_meta_box', array( $this, 'render_post_meta_box' ), 10, 2 );
		// Save setting that allow to prevent injection inside `the_content`.
		add_filter( 'advanced_ads_save_post_meta_box', array( $this, 'save_post_meta_box' ) );
		add_action( 'advanced-ads-placement-options-after-advanced', array( $this, 'render_option_to_skip_paragraph' ), 10, 2 );
	}

	/**
	 * render custom position placement options
	 *
	 * @since 1.1.2
	 * @param string $placement_slug Id of the placement.
	 * @param array  $placement Placement information.
	 *
	 */
	public function custom_position_placement_options( $placement_slug, $placement ){
	    switch ( $placement['type'] ){
		    case 'custom_position' :
			$positions = array(
			    'insertBefore' => __( 'above', 'advanced-ads-pro' ),
			    'prependTo' => __( 'inside, before other content', 'advanced-ads-pro' ),
			    'appendTo' => __( 'inside, after other content', 'advanced-ads-pro' ),
			    'insertAfter' => __( 'below', 'advanced-ads-pro' )
			);
			$curr_position = isset($placement['options']['pro_custom_position']) ? $placement['options']['pro_custom_position'] : '';
			$inject_by = isset( $placement['options']['inject_by'] ) ? $placement['options']['inject_by'] : 'pro_custom_element';
			$container_id = ! empty( $placement['options']['container_id'] ) ? $placement['options']['container_id'] :  '#c' . md5( $placement_slug );
			ob_start(); ?>
			<div id="advads-frontend-element-<?php echo $placement_slug; ?>">
				    <fieldset><legend>
					<label><input type="radio" name="advads[placements][<?php echo $placement_slug; ?>][options][inject_by]" value="pro_custom_element" <?php 
					checked( $inject_by, 'pro_custom_element' ); ?>><?php _e( 'by existing element', 'advanced-ads-pro' ); ?></label>
				</legend>
					<p class="description"><?php _e( 'Place ads in relation to an existing element in the frontend.', 'advanced-ads-pro' ); ?></p>
					<input class="advads-frontend-element" type="text" name="advads[placements][<?php echo $placement_slug; ?>][options][pro_custom_element]" value="<?php
					    echo ( isset( $placement['options']['pro_custom_element'] ) ) ? esc_attr( stripslashes( $placement['options']['pro_custom_element'] ) ) : ''; 
					    ?>" placeholder="<?php _e( 'or enter manually', 'advanced-ads-pro' ); ?>"/>
					<button style="display:none; color: red;" type="button" class="advads-deactivate-frontend-picker button"><?php echo esc_html_x( 'stop selection', 'frontend picker', 'advanced-ads-pro' ); ?></button>
					<button type="button" class="advads-activate-frontend-picker button" data-placementid="<?php echo esc_attr( $placement_slug ); ?>"><?php esc_html_e( 'select position', 'advanced-ads-pro' ); ?></button>
					<p class="description"><?php _e( 'Uses <a href="https://api.jquery.com/category/selectors/" target="_blank">jQuery selectors</a>, e.g. #container_id, .container_class', 'advanced-ads-pro' ); ?></p>
					<label><?php _e( 'Position', 'advanced-ads-pro' ); ?>
					    <select name="advads[placements][<?php echo $placement_slug; ?>][options][pro_custom_position]">
						<?php foreach( $positions as $_value => $_text ) : ?>
						    <option value="<?php echo $_value; ?>" <?php selected( $_value, $curr_position ); ?>><?php echo $_text; ?></option>
						<?php endforeach; ?>
					    </select>
					</label>
				</fieldset>
				<fieldset>
					<legend><label><input type="radio" name="advads[placements][<?php echo $placement_slug; ?>][options][inject_by]" value="container_id" <?php 
					checked( $inject_by, 'container_id' ); ?>><?php _e( 'by new element', 'advanced-ads-pro' ); ?></label></legend>
					<p class="description"><?php _e( 'Place the following element where the ad should be displayed.', 'advanced-ads-pro' ); ?></p>
					<input type="text" class="advads-wide-input" name="" value="<?php echo esc_attr( sprintf( '<div id="%s"></div>', substr( $container_id, 1 ) ) ); ?>">
					<input type="hidden" name="advads[placements][<?php echo $placement_slug; ?>][options][container_id]" value="<?php 
					echo esc_attr( $container_id ); ?>">
				</fieldset>
			</div><?php
			
			$option_content = ob_get_clean();
			
			if( class_exists( 'Advanced_Ads_Admin_Options' ) ){
				Advanced_Ads_Admin_Options::render_option( 
					'placement-custom-position', 
					__( 'position', 'advanced-ads-pro' ),
					$option_content );
			}
			
			break;
		    case 'archive_pages' :
			$index = ! empty( $placement['options']['pro_archive_pages_index'] ) ? Advanced_Ads_Pro_Utils::absint( $placement['options']['pro_archive_pages_index'], 1 ) : 1;
			$index_option = '<input type="number" required="required" min="1" name="advads[placements][' . $placement_slug . '][options][pro_archive_pages_index]" value="'
			    . $index . '" id="advads-placements-archive-pages-index' . $placement_slug . '"/>';
			$option_content = sprintf(__( 'Inject before %s. post', 'advanced-ads-pro' ), $index_option );
			
			$description = __( 'Before which post to inject the ad on post lists.', 'advanced-ads-pro' );
			if( class_exists( 'Advanced_Ads_Admin_Options' ) ){
				Advanced_Ads_Admin_Options::render_option( 
					'placement-infeed-position', 
					__( 'position', 'advanced-ads-pro' ),
					$option_content,
					$description );
			}
			
			/*$curr_type = isset($placement['options']['pro_archive_pages_type']) ? $placement['options']['pro_archive_pages_type'] : '';
			$types = array(
			    'container' => __( 'post container', 'advanced-ads-pro' ),
			    'content' => __( 'post content', 'advanced-ads-pro' ),
			); ?><select name="advads[placements][<?php echo $placement_slug; ?>][options][pro_archive_pages_type]">
				<?php foreach( $types as $_value => $_text ) : ?>
				    <option value="<?php echo $_value; ?>" <?php selected( $_value, $curr_type); ?>><?php echo $_text; ?></option>
				<?php endforeach; ?>
			    </select>*/
			break;
	    }
	}

	/**
	 * render minimum content length option for content injection placements
	 *
	 * @since 1.2.3
	 * @param string $placement_slug id of the placement
	 *
	 */
	public function minimum_content_length_option( $placement_slug, $placement ){
	    switch ( $placement['type'] ){
		    case 'post_top' :
		    case 'post_bottom' :
		    case 'post_content' :
		    case 'post_content_random' :
		    case 'post_content_middle' :
			    $options = Advanced_Ads_Pro::get_instance()->get_options();
				$minimum_length = ! empty( $placement['options']['pro_minimum_length'] ) ? absint( $placement['options']['pro_minimum_length'] ) : 0;
			    $option_content = '<input type="number" name="advads[placements][' . $placement_slug . '][options][pro_minimum_length]" size="4" min="0" value="'. $minimum_length . '" id="advads-placement-minimum-content-length-'. $placement_slug .'"/>';
			    $description = __( 'Minimum length of content (in words) before automatically injected ads are allowed in them. Set to zero or leave empty to always display ads, regardless of how long the content is.', 'advanced-ads-pro' );
			    
			    if( class_exists( 'Advanced_Ads_Admin_Options' ) ){
				Advanced_Ads_Admin_Options::render_option( 
					'placement-content-minimum-length',
					__( 'minimum content length', 'advanced-ads-pro' ),
					$option_content,
					$description );
			    }			    
		    break;
	    }
	}

	/**
	 * Render inject in any loop option for post list placement.
	 *
	 * @param string $placement_slug Id of the placement
	 * @param array  $placement Placement params.
	 */
	public function in_any_loop_archive_pages_option( $placement_slug, $placement ) {
	    switch ( $placement['type'] ) {
			case 'archive_pages' :
				$options = advanced_ads_pro::get_instance()->get_options();
				$in_any_loop = ! empty( $placement['options']['in_any_loop'] );
				$option_content = '<input type="checkbox" name="advads[placements][' . $placement_slug . '][options][in_any_loop]" value="1" ' . checked( $in_any_loop, 1, false ) . ' />';
				$description = __( 'Allow injection into any custom and secondary queries.', 'advanced-ads-pro' );
				$description .= ' ' . __( 'Only enable this option if you are sure what you are doing!', 'advanced-ads-pro' );

				if ( class_exists( 'Advanced_Ads_Admin_Options' ) ) {
					Advanced_Ads_Admin_Options::render_option(
						'placement-infeed-any-loop',
						__( 'secondary loops', 'advanced-ads-pro' ),
						$option_content,
						$description
					);
				}
			break;
		}
	}

	/**
	 * load frontend picker javascript
	 *
	 * @since 1.1.2
	 * @param arr $placements active placements
	 */
	public function frontend_picker_script( $placements ){
		// Check if the following code is included in the basic plugin.
		if ( 0 <= version_compare( ADVADS_VERSION, '1.19' ) ) {
			return;
		}

		?><script>jQuery( document ).ready( function(){
				// set element from frontend into placement input field
				if( localStorage.getItem( 'advads_frontend_element' )){
					var placement = localStorage.getItem( 'advads_frontend_picker' );
					var id = 'advads-frontend-element-' + placement;
					jQuery( '[id="' + id + '"]' ).find( '.advads-frontend-element' ).val( localStorage.getItem( 'advads_frontend_element' ) );

					var action = localStorage.getItem( 'advads_frontend_action' );
					if (typeof(action) !== 'undefined'){
						var show_all_link = jQuery( 'a[data-placement="' + placement + '"]');
						Advanced_Ads_Admin.toggle_placements_visibility( show_all_link );
					}
					localStorage.removeItem( 'advads_frontend_action' );
					localStorage.removeItem( 'advads_frontend_element' );
					localStorage.removeItem( 'advads_frontend_picker' );
					localStorage.removeItem( 'advads_prev_url' );
				}
				jQuery('.advads-activate-frontend-picker').on( 'click', function( e ){
					localStorage.setItem( 'advads_frontend_picker', jQuery( this ).data('placementid') );
					localStorage.setItem( 'advads_frontend_action', jQuery( this ).data('action') );
					localStorage.setItem( 'advads_prev_url', window.location );
					window.location = "<?php echo home_url(); ?>";
				});
				// allow to deactivate frontend picker
				if ( localStorage.getItem( 'advads_frontend_picker' ) ) {
					var id = 'advads-frontend-element-' + localStorage.getItem( 'advads_frontend_picker' );
					jQuery( '[id="' + id + '"]' ).find( '.advads-deactivate-frontend-picker' ).show();
				}
				jQuery( '.advads-deactivate-frontend-picker' ).on( 'click',  function( e ) {
					localStorage.removeItem( 'advads_frontend_action' );
					localStorage.removeItem( 'advads_frontend_element' );
					localStorage.removeItem( 'advads_frontend_picker' );
					localStorage.removeItem( 'advads_prev_url' );
					jQuery('.advads-deactivate-frontend-picker').hide();
				});
			});
		</script><?php
	}

	/**
	* Render setting that allow to prevent injection inside `the_content`.
	*
	* @param WP_Post $post The post object.
	* @param mixed $values existing values from database
	*/
	public function render_post_meta_box( $post, $values ) {
		require plugin_dir_path(__FILE__) . '/views/setting_post_meta_box.php';
	}

	/**
	* Sanitize and save setting that allow to prevent injection inside `the_content`.
	*
	* @param array $_data data sent by user
	* @return $_data sanitized data
	*/
	public function save_post_meta_box( $_data = array() ) {
		$_data['disable_the_content'] = isset( $_POST['advanced_ads']['disable_the_content'] ) ? absint( $_POST['advanced_ads']['disable_the_content'] ) : 0;

		return $_data;
	}

	/**
	 * Render the option to skip paragraph.
	 *
	 * @param string $placement_slug Slug of the placement.
	 * @param array  $placement Placement params.
	 */
	public function render_option_to_skip_paragraph( $placement_slug, $placement ) {
		if ( ! in_array( $placement['type'], array( 'post_content_middle', 'post_top', 'post_bottom', 'post_content' ), true ) ) {
			return;
		}

		if ( ! class_exists( 'Advanced_Ads_Admin_Options' ) ) {
			return;
		}

		$words_between_repeats = ! empty( $placement['options']['words_between_repeats'] ) ? absint( $placement['options']['words_between_repeats'] ) : 0;
		ob_start();
		require AAP_BASE_PATH . '/views/setting_words_between_ads.php';
		$setting = ob_Get_clean();

		Advanced_Ads_Admin_Options::render_option(
			'advanced-ads-pro-skip-paragraph',
			__( 'Words between ads', 'advanced-ads-pro' ),
			$setting
		);
	}

}
