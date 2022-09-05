<?php
class Advanced_Ads_Sticky_Admin {

	/**
	 * stores the settings page hook
	 *
	 * @since   1.0.0
	 * @var     string
	 */
	protected $settings_page_hook = '';

	const PLUGIN_LINK = 'https://wpadvancedads.com/add-ons/sticky-ads/';

	/**
	 * holds base class
	 *
	 * @var Advanced_Ads_Sticky_Plugin
	 * @since 1.2.0
	 */
	protected $plugin;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

		$this->plugin = Advanced_Ads_Sticky_Plugin::get_instance();

		add_action( 'plugins_loaded', array( $this, 'wp_admin_plugins_loaded' ) );
	}

	/**
	 * load actions and filters
	 */
	public function wp_admin_plugins_loaded(){


		if( ! class_exists( 'Advanced_Ads_Admin', false ) ) {
			// show admin notice
			add_action( 'admin_notices', array( $this, 'missing_plugin_notice' ) );

			return;
		}

		// add metabox
		add_action( 'admin_init', array( $this, 'add_meta_box' ) );

		// add our new options using the options filter before saving
		add_filter( 'advanced-ads-save-options', array( $this, 'save_options' ), 10, 2 );

		// register settings
		add_action( 'advanced-ads-settings-init', array( $this, 'settings_init' ) );

		// add sticky placement
		add_action( 'advanced-ads-placement-types', array( $this, 'add_sticky_placement' ) );

		// content of sticky placement
		add_action( 'advanced-ads-placement-options-after-advanced', array( $this, 'sticky_placement_content' ), 10, 2 );

		// add admin scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		// content after placement list
		add_action( 'advanced-ads-placements-list-after', array( $this, 'placements_list_after' ) );
	}

	/**
	* show warning if Advanced Ads js is not activated
	*/
	public function missing_plugin_notice(){
		$plugins = get_plugins();
		if( isset( $plugins['advanced-ads/advanced-ads.php'] ) ){ // is installed, but not active
			$link = '<a class="button button-primary" href="' . wp_nonce_url( 'plugins.php?action=activate&amp;plugin=advanced-ads/advanced-ads.php&amp', 'activate-plugin_advanced-ads/advanced-ads.php' ) . '">'. __('Activate Now', 'advanced-ads-sticky') .'</a>';
		} else {
			$link = '<a class="button button-primary" href="' . wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . 'advanced-ads'), 'install-plugin_' . 'advanced-ads') . '">'. __('Install Now', 'advanced-ads-sticky') .'</a>';
		}
		echo '
		<div class="error">
		  <p>'.sprintf(__('<strong>%s</strong> requires the <strong><a href="https://wpadvancedads.com" target="_blank">Advanced Ads</a></strong> plugin to be installed and activated on your site.', 'advanced-ads-sticky'), 'Advanced Ads – Sticky Ads') .
		     '&nbsp;' . $link . '</p></div>';
	}

	/**
	 * add color picker script
	 *
	 * @since 1.3
	 * @param type $hook_suffix
	 */
	function admin_scripts( $hook_suffix ) {

	    if( ! class_exists( 'Advanced_Ads_Admin' ) ) {
		    return;
	    };

	    if ( Advanced_Ads_Admin::screen_belongs_to_advanced_ads() ){
		    // add color picker script
		    wp_enqueue_style( 'wp-color-picker' );
		    wp_enqueue_script( 'wp-color-picker' );
	    }
	}

	/**
	 * add own meta box for the ad parameters
	 *
	 * @since 1.0.1
	 */
	public function add_meta_box() {
		if ( ! class_exists( 'Advanced_Ads' ) ) {
			return;
		}

		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
		if ( ! $post_id ){
			return;
		}
		$ad = new Advanced_Ads_Ad( $post_id );
		$options = $ad->options();
		$enabled = isset($options['sticky']['enabled']) ? $options['sticky']['enabled'] : false;

		// return if not enabled, because this is deprecated
		if ( ! $enabled ) {
			return;
		}

		add_meta_box(
			'ad-sticky-ads-box', 'Sticky Ads', array( $this, 'render_metabox' ), Advanced_Ads::POST_TYPE_SLUG, 'normal', 'low'
		);
	}

	/**
	 * render options for ad parameters
	 *
	 * @since 1.0.0
	 */
	public function render_metabox(){
		global $post;
		$ad = new Advanced_Ads_Ad( $post->ID );
		$options = $ad->options();
		// set options
		$enabled = isset($options['sticky']['enabled']) ? $options['sticky']['enabled'] : false;
		$assistant = isset($options['sticky']['assistant']) ? $options['sticky']['assistant'] : false;
		$type = isset($options['sticky']['type']) ? $options['sticky']['type'] : '';
		$top = isset($options['sticky']['position']['top']) ? $options['sticky']['position']['top'] : '';
		$right = isset($options['sticky']['position']['right']) ? $options['sticky']['position']['right'] : '';
		$bottom = isset($options['sticky']['position']['bottom']) ? $options['sticky']['position']['bottom'] : '';
		$left = isset($options['sticky']['position']['left']) ? $options['sticky']['position']['left'] : '';
		$width = isset($options['sticky']['position']['width']) ? $options['sticky']['position']['width'] : 0;

		require_once( 'views/metabox.php' );
	}

	/**
	 * save sticky options
	 *
	 * @since 1.0.0
	 */
	public function save_options($options = array(), $ad = 0){

		// sanitize sticky options
		$positions = array();

		$options['sticky']['enabled'] = ( ! empty( $_POST['advanced_ad']['sticky']['enabled']) ) ? absint( $_POST['advanced_ad']['sticky']['enabled'] ) : 0;
		$options['sticky']['type'] = ( ! empty( $_POST['advanced_ad']['sticky']['type']) ) ? $_POST['advanced_ad']['sticky']['type'] : '';
		$options['sticky']['assistant'] = ( ! empty( $_POST['advanced_ad']['sticky']['assistant']) ) ? $_POST['advanced_ad']['sticky']['assistant'] : '';

		// sanitize positions
		if ( ! empty($_POST['advanced_ad']['sticky']['position']) ) {
			foreach ( $_POST['advanced_ad']['sticky']['position'] as $_position => $_value ){
				if ( $_value != '' ) {
					$positions[ $_position ] = intval( $_value ); }
			}
		}

		$options['sticky']['position'] = $positions;

		return $options;
	}

	/**
	 * add settings to settings page
	 *
	 * @since 1.0.0
	 */
	public function settings_init(){

		// don’t initiate if main plugin not loaded
		if ( ! class_exists( 'Advanced_Ads_Admin' ) ) { return; }

		// get settings page hook
		$admin = Advanced_Ads_Admin::get_instance();
		$hook = $admin->plugin_screen_hook_suffix;
		$this->settings_page_hook = $hook;

		// add license key field to license section
		add_settings_field(
			'sticky-license',
			'Sticky Ads',
			array( $this, 'render_settings_license_callback' ),
			'advanced-ads-settings-license-page',
			'advanced_ads_settings_license_section'
		);

		// add new section
		add_settings_section(
			'advanced_ads_sticky_setting_section',
			'Sticky Ads',
			array( $this, 'render_settings_section_callback' ),
			$hook
		);

		// add setting fields
		add_settings_field(
			'use-js-lib',
			__( 'Check browser capability', 'advanced-ads-sticky' ),
			array( $this, 'render_settings_scroll_callback' ),
			$hook,
			'advanced_ads_sticky_setting_section'
		);
	}

	/**
	 * render license key section
	 *
	 * @since 1.2.0
	 */
	public function render_settings_license_callback(){
	    $licenses = get_option( ADVADS_SLUG . '-licenses', array() );
	    $license_key = isset($licenses['sticky']) ? $licenses['sticky'] : '';
	    $license_status = get_option( $this->plugin->options_slug . '-license-status', false );
	    $index = 'sticky';
	    $plugin_name = AASADS_PLUGIN_NAME;
	    $options_slug = $this->plugin->options_slug;
	    $plugin_url = self::PLUGIN_LINK;

	    // template in main plugin
	    include ADVADS_BASE_PATH . 'admin/views/setting-license.php';
	}

	/**
	 * render advanced scroll method setting
	 *
	 * @since 1.0.0
	 */
	public function render_settings_section_callback(){
		_e( 'Settings for the Sticky Ads add-on', 'advanced-ads-sticky' );
	}

	/**
	 * render advanced scroll method setting
	 *
	 * @since 1.0.0
	 */
	public function render_settings_scroll_callback(){
		$options = $this->plugin->options();
		$check_position_fixed = isset($options['sticky']['check-position-fixed']) ? $options['sticky']['check-position-fixed'] : 0;
		echo '<input name="'.ADVADS_SLUG.'[sticky][check-position-fixed]" id="advanced-ads-sticky-check-position-fixed" type="checkbox" value="1" ' . checked( 1, $check_position_fixed, false ) . ' />';
		echo '<p class="description">'. __( 'Activate this if you experience problems with sticky ads and/or a lot of your visitors use old mobile devices. It will check browser capability and position the ad inline after scrolling. Technically speaking: removes <em>position: fixed</em>, if not supported.', 'advanced-ads-sticky' ) .'</p>';
	}

	/**
	 * add sticky placement to list of placements
	 *
	 * @since 1.2.3
	 * @param arr $types existing placements
	 * @return arr $types
	 */
	public function add_sticky_placement( $types ){

		// fixed header bar
		$types['sticky_header'] = array(
		'title' => __( 'Header Bar', 'advanced-ads-sticky' ),
		'description' => __( 'Fixed header bar.', 'advanced-ads-sticky' ),
		'image' => AASADS_BASE_URL . 'admin/assets/img/sticky-header.png',
		'order'       => 5,
		);
		// fixed footer bar
		$types['sticky_footer'] = array(
		'title' => __( 'Footer Bar', 'advanced-ads-sticky' ),
		'description' => __( 'Fixed footer bar.', 'advanced-ads-sticky' ),
		'image' => AASADS_BASE_URL . 'admin/assets/img/sticky-footer.png',
		'order'       => 90,
		);
		// fixed left sidebar
		$types['sticky_left_sidebar'] = array(
		'title' => __( 'Left Sidebar', 'advanced-ads-sticky' ),
		'description' => __( 'Sidebar on the left side of the content wrapper.', 'advanced-ads-sticky' ),
		'image' => AASADS_BASE_URL . 'admin/assets/img/sticky-sidebar-left.png',
		'order'       => 52,
		);
		// fixed right sidebar
		$types['sticky_right_sidebar'] = array(
		'title' => __( 'Right Sidebar', 'advanced-ads-sticky' ),
		'description' => __( 'Sidebar on the right side of the content wrapper.', 'advanced-ads-sticky' ),
		'image' => AASADS_BASE_URL . 'admin/assets/img/sticky-sidebar-right.png',
		'order'       => 53,
		);
		// fixed left browser bar
		$types['sticky_left_window'] = array(
		'title' => __( 'Left Bar', 'advanced-ads-sticky' ),
		'description' => __( 'Bar on the left side of the window.', 'advanced-ads-sticky' ),
		'image' => AASADS_BASE_URL . 'admin/assets/img/sticky-left.png',
		'order'       => 51,
		);
		// fixed right browser bar
		$types['sticky_right_window'] = array(
		'title' => __( 'Right Bar', 'advanced-ads-sticky' ),
		'description' => __( 'Bar on the right side of the window.', 'advanced-ads-sticky' ),
		'image' => AASADS_BASE_URL . 'admin/assets/img/sticky-right.png',
		'order'       => 54,
		);

		return $types;
	}

	/**
	 * render sticky placement content
	 *
	 * @since 1.2.3
	 * @param string $placement_slug id of the placement
	 *
	 */
	public function sticky_placement_content( $placement_slug, $placement ){
	    
	    if( ! class_exists( 'Advanced_Ads_Admin_Options' ) ){
		    echo 'Please update to Advanced Ads 1.8';
		    return;
	    }
	    
	    switch ( $placement['type'] ){
		    case 'sticky_header' :
		    case 'sticky_footer' :
			
				$options = isset( $placement['options']['sticky'] ) ? $placement['options']['sticky'] : array();
				$option_name = "advads[placements][$placement_slug][options][sticky]";
			
				// position
				ob_start();
				?><input type="text" value="<?php if ( isset($placement['options']['sticky_bg_color']) ) { echo $placement['options']['sticky_bg_color']; } ?>" class="advads-sticky-bg-color-field" name="advads[placements][<?php echo $placement_slug; ?>][options][sticky_bg_color]"/>
				    <p class="description"><?php _e( 'When selecting a background color, the sticky bar will cover the whole screen width.', 'advanced-ads-sticky' ); ?></p><?php
				$option_content = ob_get_clean();

				Advanced_Ads_Admin_Options::render_option( 
					'placement-sticky-background', 
					__( 'background', 'advanced-ads-sticky' ),
					$option_content );
				
				// trigger
				ob_start();
				include AASADS_BASE_PATH . '/admin/views/trigger.php';
				$option_content = ob_get_clean();

				Advanced_Ads_Admin_Options::render_option( 
					'placement-sticky-trigger', 
					__( 'show the ad', 'advanced-ads-sticky' ),
					$option_content );
				
				// effect
				ob_start();
				include AASADS_BASE_PATH . '/admin/views/effects.php';
				$option_content = ob_get_clean();

				Advanced_Ads_Admin_Options::render_option( 
					'placement-sticky-effect', 
					__( 'effect', 'advanced-ads-sticky' ),
					$option_content );
				
				// close button
				ob_start();
				include AASADS_BASE_PATH . '/admin/views/close-button.php';
				$option_content = ob_get_clean();

				Advanced_Ads_Admin_Options::render_option( 
					'placement-sticky-close-button', 
					__( 'close button', 'advanced-ads-sticky' ),
					$option_content );

				// dimensions
				$width = isset( $placement['options']['placement_width'] ) ? absint( $placement['options']['placement_width'] ) : 0;
				$height = false;

				ob_start();
				include AASADS_BASE_PATH . '/admin/views/size.php';
				$option_content = ob_get_clean();

				Advanced_Ads_Admin_Options::render_option( 
					'placement-sticky-dimension', 
					__( 'size', 'advanced-ads-sticky' ),
					$option_content );
			break;
		    case 'sticky_left_sidebar' :
		    case 'sticky_right_sidebar' :
			
				$options = isset( $placement['options']['sticky'] ) ? $placement['options']['sticky'] : array();
				$option_name = "advads[placements][$placement_slug][options][sticky]";

				// trigger
				ob_start();
				include AASADS_BASE_PATH . '/admin/views/trigger.php';
				$option_content = ob_get_clean();

				Advanced_Ads_Admin_Options::render_option(
					'placement-sticky-trigger',
					__( 'show the ad', 'advanced-ads-sticky' ),
					$option_content );

				// effect
				ob_start();
				include AASADS_BASE_PATH . '/admin/views/effects.php';
				$option_content = ob_get_clean();

				Advanced_Ads_Admin_Options::render_option(
					'placement-sticky-effect',
					__( 'effect', 'advanced-ads-sticky' ),
					$option_content );
			
				// position
				ob_start();
				?><label><input type="checkbox" name="advads[placements][<?php echo $placement_slug; ?>][options][sticky_is_fixed]" value="1" <?php
				    if ( isset($placement['options']['sticky_is_fixed']) ) { checked( $placement['options']['sticky_is_fixed'], 1 ); }
				?>/><?php _e( 'fix to window position', 'advanced-ads-sticky' ); ?></label><?php
				include AASADS_BASE_PATH . '/admin/views/vertical-center.php';
				$option_content = ob_get_clean();

				Advanced_Ads_Admin_Options::render_option( 
					'placement-sticky-position', 
					__( 'Position', 'advanced-ads-sticky' ),
					$option_content );
				
				// element selector
				ob_start();
				?><div id="advads-frontend-element-<?php echo $placement_slug; ?>"><input type="text" name="advads[placements][<?php echo $placement_slug; ?>][options][sticky_element]" value="<?php
				    echo (isset($placement['options']['sticky_element'])) ? $placement['options']['sticky_element'] : '';
				    ?>" class="advads-frontend-element"/>
					<button style="display:none; color: red;" type="button" class="advads-deactivate-frontend-picker button"><?php _ex( 'stop selection', 'frontend picker',  'advanced-ads-sticky' ); ?></button>
					<button type="button" class="advads-activate-frontend-picker button" data-placementid="<?php echo esc_attr( $placement_slug ); ?>" data-action="edit-placement"><?php _e( 'select position', 'advanced-ads-sticky' ); ?></button>
				</div>
				<p class="description"><?php _e( 'Use <a href="https://api.jquery.com/category/selectors/" target="_blank">jQuery selectors</a> to select a custom parent element or if automatic wrapper detection doesn’t work, e.g. #container_id, .container_class', 'advanced-ads-sticky' ); ?></p>
				<?php $option_content = ob_get_clean();

				Advanced_Ads_Admin_Options::render_option( 
					'placement-sticky-element', 
					__( 'parent element', 'advanced-ads-sticky' ),
					$option_content );
				
				// close button
				ob_start();
				include AASADS_BASE_PATH . '/admin/views/close-button.php';
				$option_content = ob_get_clean();

				Advanced_Ads_Admin_Options::render_option( 
					'placement-sticky-close-button', 
					__( 'close button', 'advanced-ads-sticky' ),
					$option_content );
				
				// dimensions
				$width = isset( $placement['options']['placement_width'] ) ? absint( $placement['options']['placement_width'] ) : 0;
				$height = isset( $placement['options']['placement_height'] ) ? absint( $placement['options']['placement_height'] ) : 0;

				ob_start();
				include AASADS_BASE_PATH . '/admin/views/size.php';
				$option_content = ob_get_clean();

				Advanced_Ads_Admin_Options::render_option( 
					'placement-sticky-dimension', 
					__( 'size', 'advanced-ads-sticky' ),
					$option_content );
				
		    break;
		    case 'sticky_left_window' :
		    case 'sticky_right_window' :
			
				$options = isset( $placement['options']['sticky'] ) ? $placement['options']['sticky'] : array();
				$option_name = "advads[placements][$placement_slug][options][sticky]";

				// trigger
				ob_start();
				include AASADS_BASE_PATH . '/admin/views/trigger.php';
				$option_content = ob_get_clean();

				Advanced_Ads_Admin_Options::render_option( 
					'placement-sticky-trigger', 
					__( 'show the ad', 'advanced-ads-sticky' ),
					$option_content );

				// effect
				ob_start();
				include AASADS_BASE_PATH . '/admin/views/effects.php';
				$option_content = ob_get_clean();

				Advanced_Ads_Admin_Options::render_option( 
					'placement-sticky-effect', 
					__( 'effect', 'advanced-ads-sticky' ),
					$option_content );			

				// position
				ob_start();
				?><label><input type="checkbox" name="advads[placements][<?php echo $placement_slug; ?>][options][sticky_is_fixed]" value="1" <?php
					if ( isset($placement['options']['sticky_is_fixed']) ) { checked( $placement['options']['sticky_is_fixed'], 1 ); }
				    ?>/><?php _e( 'fix to window position', 'advanced-ads-sticky' ); ?></label><?php
				include AASADS_BASE_PATH . '/admin/views/vertical-center.php';
				$option_content = ob_get_clean();

				Advanced_Ads_Admin_Options::render_option( 
					'placement-sticky-position', 
					__( 'Position', 'advanced-ads-sticky' ),
					$option_content );

				// close button
				ob_start();
				include AASADS_BASE_PATH . '/admin/views/close-button.php';
				$option_content = ob_get_clean();

				Advanced_Ads_Admin_Options::render_option( 
					'placement-sticky-close-button', 
					__( 'close button', 'advanced-ads-sticky' ),
					$option_content );

				// dimensions
				$width = isset( $placement['options']['placement_width'] ) ? absint( $placement['options']['placement_width'] ) : 0;
				$height = isset( $placement['options']['placement_height'] ) ? absint( $placement['options']['placement_height'] ) : 0;

				ob_start();
				include AASADS_BASE_PATH . '/admin/views/size.php';
				$option_content = ob_get_clean();

				Advanced_Ads_Admin_Options::render_option( 
					'placement-sticky-dimension', 
					__( 'size', 'advanced-ads-sticky' ),
					$option_content );
			
		    break;
	    }
	}

	/**
	 * render content after the placements list
	 *  activate color picker fields
	 *
	 * @since 1.3
	 * @param type $placements array with placements
	 */
	public function placements_list_after( $placements = array() ){
		?><script>
		jQuery(document).ready(function($){
			jQuery('.advads-sticky-bg-color-field').wpColorPicker();
		});
		</script>
		<?php
		// Check if the following code is included in the basic plugin.
		if ( 0 <= version_compare( ADVADS_VERSION, '1.19' ) ) {
			return;
		}

		// set element from frontend into placement input field
		// use the same code from Pro, if Pro enabled
		if ( ! defined( 'AAP_VERSION') ) : ?>
			<script>
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
			jQuery('.advads-activate-frontend-picker').click(function( e ){
				localStorage.setItem( 'advads_frontend_picker', jQuery( this ).data('placementid') );
				localStorage.setItem( 'advads_frontend_action', jQuery( this ).data('action') );
				localStorage.setItem( 'advads_prev_url', window.location );
				window.location = "<?php echo home_url(); ?>";
			});
			</script>
		<?php endif;
	}
}
