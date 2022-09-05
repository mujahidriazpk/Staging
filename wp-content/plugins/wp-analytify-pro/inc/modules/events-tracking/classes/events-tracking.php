<?php
class ANALYTIFY_TRACKING extends WP_Analytify_Pro_Base {

	private $google_measurement_url       = 'https://ssl.google-analytics.com/collect';
	private $google_debug_measurement_url = 'https://www.google-analytics.com/debug/collect';

	function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'front_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'wp_login', array( $this, 'signed_in' ), 10, 2 );
		add_action( 'wp_logout', array( $this, 'signed_out' ) );
		add_action( 'register_form', array( $this, 'viewed_signup' ) );
		add_action( 'user_register', array( $this, 'signed_up' ) );
		add_action( 'analytify_tracking_code_before_pageview', array( $this, 'add_link_attribution' ) );
		add_action( 'analytify_add_submenu', array( $this, 'add_menu' ), 60 );
		add_action( 'analytify_dashboad_dropdown_option' , array( $this, 'dashboad_dropdown_option' ) );
		add_action( 'wp_analytify_events_tracking_license_key', array( $this, 'display_license_form' ) , 11 );
		add_action( 'wp_ajax_events_tracking_activate_license', array( $this, 'events_tracking_activate_license' ) );
		add_action( 'analytify_settings_logs', array( $this, 'settings_logs' ) );

		add_filter( 'wp_analytify_pro_setting_tabs', array( $this, 'settings_tab' ), 20, 1 );
		add_filter( 'wp_analytify_pro_setting_fields', array( $this, 'setting_fields' ), 20, 1 );
	}

	/**
	 * Adds the "Events Tracking" page in the wp menu
	 *
	 * @since 1.0.0
	 */
	public function add_menu() {
		add_submenu_page( 'analytify-dashboard', esc_html__( 'Track every Event with Analytify.', 'wp-analytify-events-tracking' ), __( 'Events Tracking', 'wp-analytify-pro' ), 'edit_posts', 'analytify-events', array(
		$this,
		'analytify_page_file_path'
		), 45 );
	}

	/**
	 * Add dropdown option for tracking dashboard.
	 *
	 * @since 1.0.0
	 */
	public function dashboad_dropdown_option() {
		echo '<li><a href="'. admin_url( 'admin.php?page=analytify-events' ) .'">Events</a></li>';
	}


	public function analytify_page_file_path () {
		$screen = get_current_screen();
		if( strpos( $screen->base, 'analytify-events' ) !== false ) {
			include( ANALYTIFY_PRO_ROOT_PATH . '/inc/modules/events-tracking/view/event-tracking-dashboard.php' );
		}
	}

	public function front_scripts() {

		// Make sure that the GA ttracking code is being included
		if ( 'on' === $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'install_ga_code', 'wp-analytify-profile', 'off' ) ) {

			global $current_user;

			$roles = $current_user->roles;

			if ( !isset( $roles[0] ) OR ! in_array( $roles[0], $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'exclude_users_tracking', 'wp-analytify-profile', array() ) ) ) {

				wp_enqueue_script( 'analytify-events-tracking', plugins_url( 'assets/js/analytify-events-tracking.js', dirname( __FILE__ ) ), array( 'jquery' ) );

				wp_localize_script(
					'analytify-events-tracking', 'Analytify_Event', array(
						'ajaxurl'					=> admin_url( 'admin-ajax.php' ),
						'tracking_mode'				=> ANALYTIFY_TRACKING_MODE,
						'tracking_code'				=> WP_ANALYTIFY_FUNCTIONS::get_UA_code(),
						'is_track_user'				=> analytify_is_track_user(),
						'root_domain'				=> $this->get_root_domain(),
						'affiliate_link'			=> $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'affiliate_link_path', 'wp-analytify-events-tracking', '' ),
						'download_extension'		=> $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'file_extension', 'wp-analytify-events-tracking', 'zip|mp3*|mpe*g|pdf|docx*|pptx*|xlsx*|rar*' ),
						'anchor_tracking'			=> $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'anchor_tracking', 'wp-analytify-events-tracking' )
					)
				);

			}
		}

	}

	public function admin_scripts( $screen ) {
		if ( 'analytify_page_analytify-settings' === $screen ) {
			wp_enqueue_style( 'analytify-events-style', plugins_url( 'assets/css/analytify-events-tracking.css', dirname( __FILE__ ) ) );
			wp_enqueue_script( 'analytify-events-admin', plugins_url( 'assets/js/analytify-events-admin.js', dirname( __FILE__ ) ), array( 'jquery' ), false );
		}
	}

	/**
	 * License activation key fields/inputs
	 *
	 * @since 1.0.0
	 */
	function display_license_form() {
		$license_events_tracking  = get_option( 'analytify_events_tracking_license_key' );
		$status_events_tracking   = get_option( 'analytify_events_tracking_license_status' ); ?>

		<tr valign="top">
			<th scope="row" valign="top">
				<?php _e( 'Analytify for Events Tracking (License Key):', 'wp-analytify-pro' ); ?>
			</th>
			<?php  if( 'valid' === $status_events_tracking ){ ?>
				<td class="events-tracking-row">
				<?php echo $this->get_formatted_masked_events_tracking_license( $license_events_tracking ); ?>
				</td>
			<?php }else{ ?>
			<td class="events-tracking-row">
				<input id="analytify_events_tracking_license_key" name="analytify_events_tracking_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license_events_tracking ); ?>" />
				<input type="submit" class="button-secondary" id="analytify_events_tracking_license_activate" name="analytify_license_activate" value="<?php _e( 'Activate License', 'wp-analytify-pro' ); ?>"/>
				<br />
				<p id="google-events-tracking-status"><?php if ( $status_events_tracking ) { echo $status_events_tracking; } ?></p>
			</td>
			<?php } ?>
		</tr>
	
	<?php
	}

	public function get_root_domain() {
		$url  = site_url();
		$root = explode( '/', $url );
		preg_match( '/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', str_ireplace( 'www', '', isset( $root[2] ) ? $root[2] : $url ), $root );
		if ( isset( $root['domain'] ) ) {
			return $root['domain'];
		} else {
			return '';
		}
	}

	public function signed_up() {
		$attr = array(
			't'  => 'event',
			'ec' => 'Tracking by Analytify', // category
			'ea' => 'Sign Up',
		);

		$this->hit( $attr );
	}

	public function signed_out() {
		$attr = array(
			't'  => 'event',
			'ec' => 'Tracking by Analytify', // category
			'ea' => 'Logout',
		);

		$this->hit( $attr );
	}

	public function signed_in( $user_login, $user ) {
		$attr = array(
			't'   => 'event',
			'ec'  => 'Tracking by Analytify', // category
			'ea'  => 'Login',
			'el'  => $user_login, // Evnet Label
			'uid' => $user->ID,
		);

		// logged in at checkout
		$this->hit( $attr );
	}

	public function viewed_signup() {
		$attr = array(
			't'  => 'event',
			'ec' => 'Tracking by Analytify', // category
			'ea' => 'View Signup',
		);

		$this->hit( $attr );

	}

	public function get_cid() {
		if ( ! empty( $_COOKIE['_ga'] ) ) {
			list( $version, $domainDepth, $cid1, $cid2 ) = preg_split( '[\.]', $_COOKIE['_ga'], 4 );
			$contents                                    = array(
				'version'     => $version,
				'domainDepth' => $domainDepth,
				'cid'         => $cid1 . '.' . $cid2,
			);
			$cid = $contents['cid'];

			return $cid;
		} else {
			return $this->generate_uuid();
		}

	}

	/**
	 * Generate UUID v4 function - needed to generate a CID when one isn't available
	 *
	 * @link http://www.stumiller.me/implementing-google-analytics-measurement-protocol-in-php-and-wordpress/
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function generate_uuid() {
		return sprintf(
			'%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
			// 16 bits for "time_mid"
			mt_rand( 0, 0xffff ),
			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand( 0, 0x0fff ) | 0x4000,
			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand( 0, 0x3fff ) | 0x8000,
			// 48 bits for "node"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
	}

	public function hit( $attr, $debug = false ) {
		$default_args = array(
			't'  => 'event', // Required - Hit type
			'ec' => '', // Event category
			'ea' => '', // Event Action
			'el' => '', // Event Label
			'ev' => null, // Event Value
		);

		$body = array_merge( $default_args, $attr );

		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		$user_language = isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) : array();
		$user_language = reset( $user_language );

		$default_body = array(
			'v'   => '1', // Required - Version
			'tid' => WP_ANALYTIFY_FUNCTIONS::get_UA_code(), // Required - UA code
			'cid' => $this->get_cid(), // Required - Unique (anonymous) visitor ID
			't'   => 'pageview', // Required - Hit type
			'ni'  => true, // Non interaction

			'dh'  => str_replace( array( 'http://', 'https://' ), '', site_url() ),
			'dp'  => $_SERVER['REQUEST_URI'],
			'dt'  => get_the_title(),

			// Hits that usually also go with JS
			'ul'  => $user_language, // Optional - User language

			'uip' => $ip, // Optional - User IP, to make sure its not the servers'
			'ua'  => $_SERVER['HTTP_USER_AGENT'], // Optional - User Agent

		);

		$body = wp_parse_args( $body, $default_body );

		// Requests without ID are ignored by GA
		if ( false == $body['cid'] ) {
			return false;
		}

		if ( $debug ) {
			$response = wp_remote_post(
				$this->google_debug_measurement_url, array(
					'method'   => 'POST',
					'blocking' => true,
					'body'     => array_merge( $body, array( 'z' => time() ) ),
				)
			);

			print '<pre>';
			print_r( $response );
			print '</pre>';
		} else {
			$response = wp_remote_post(
				$this->google_measurement_url, array(
					'method'   => 'POST',
					'timeout'  => '5',
					'blocking' => false,
					'body'     => array_merge( $body, array( 'z' => time() ) ),
				)
			);
		}
	}

	public function settings_tab( $old_tabs ) {
		$pro_tabs = array(
			array(
				'id'       => 'wp-analytify-events-tracking',
				'title'    => __( 'Events Tracking', 'wp-analytify-pro' ),
				'desc'     => __( 'Track all events including clicks, links, files, extensions, affiliates.', 'wp-analytify-pro' ),
				'priority' => '0',
			)
		);

		return array_merge( $old_tabs, $pro_tabs );
	}

	public function setting_fields( $old_fields ) {

		$pro_fields = array();
		
		$pro_fields['wp-analytify-events-tracking'][] = array(
			'name'    => 'file_extension',
			'label'   => __( 'Extensions of files to track as downloads:', 'wp-analytify-pro' ),
			'desc'    => __( 'Add regex for file type.', 'wp-analytify-pro' ),
			'type'    => 'text',
			'default' => 'zip|mp3*|mpe*g|pdf|docx*|pptx*|xlsx*|rar*',
			'sanitize_callback' => 'sanitize_text_field',
		);
		
		if ( method_exists( 'WP_Analytify_Settings', 'callback_affiliates_repeater' ) ) {
			
			$pro_fields['wp-analytify-events-tracking'][] = array(
				'name'  => 'affiliate_link_path',
				'label' => __( 'Affiliate Links', 'wp-analytify-pro' ),
				'desc'  => __( 'Setup links of the affiliate you have partnered with for your website to make extra bucks.', 'wp-analytify-pro' ), 
				'type'  => 'affiliates_repeater',
				'default' => array(),
			);
		}
			
		$pro_fields['wp-analytify-events-tracking'][] = array(
			'name'  => 'enhanced_link_attribution',
			'label' => __( 'Enable enhanced link attribution', 'wp-analytify-pro' ),
			'desc'  => __( 'Add <a href=\'https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-link-attribution\' target=\'_blank\' rel=\'noopener noreferrer\'>Enhanced Link Attribution </a> to your tracking code.', 'wp-analytify-pro' ),
			'type'  => 'checkbox',
		);
		
		$pro_fields['wp-analytify-events-tracking'][] = array(
			'name'  => 'anchor_tracking',
			'label' => __( 'Turn on anchor tracking', 'wp-analytify-pro' ),
			'desc'  => __( 'Many WordPress \'1-page\' style themes rely on anchor tags for navigation to show virtual pages. The problem is that to Google Analytics, these are all just a single page, and it makes it hard to get meaningful statistics about pages viewed. This feature allows proper tracking in those themes.', 'wp-analytify-pro' ),
			'type'  => 'checkbox',
		);

		return array_merge( $old_fields, $pro_fields );
	}

	public function add_link_attribution() {

		$tracking_code = WP_ANALYTIFY_FUNCTIONS::get_UA_code();

		if ( 'on' === $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'enhanced_link_attribution', 'wp-analytify-events-tracking', 'off' ) ) {
			if ( 'gtag' === ANALYTIFY_TRACKING_MODE ) {
				echo "gtag('config', '".$tracking_code."', {
					'link_attribution': true
				});";
			} else {
				echo "ga('require', 'linkid');";
			}
		}
	}

	/**
	 * Add events tracking  settings in diagnostic information.
	 *
	 */
	function settings_logs() {

		echo "-- Events Tracking Setting --\r\n \r\n";
		
		$options = get_option( 'wp-analytify-events-tracking' );

		if ( method_exists( 'WPANALYTIFY_Utils', 'print_settings_array' ) ) {
			WPANALYTIFY_Utils::print_settings_array( $options );
		}
	}

}

new ANALYTIFY_TRACKING();
