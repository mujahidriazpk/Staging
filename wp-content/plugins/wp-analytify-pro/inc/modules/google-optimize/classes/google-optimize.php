<?php
class Analytify_Google_Optimize extends WP_Analytify_Pro_Base {

	public function __construct() {

		$this->admin_hooks();
		$this->public_hooks();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function admin_hooks() {
		
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'analytify_settings_logs', array( $this, 'settings_logs' ) );

		add_filter( 'wp_analytify_pro_setting_tabs', array( $this, 'settings_tab' ), 20, 1 );
		add_filter( 'wp_analytify_pro_setting_fields', array( $this, 'setting_fields' ), 20, 1 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function public_hooks() {

		add_action( 'wp_head', array( $this, 'add_optimize_page_head_scripts' ), 2 );
		add_action( 'analytify_tracking_code_before_pageview', array( $this, 'add_optimize_code' ) );
	}

	public function settings_tab( $old_tabs ) {

		$pro_tabs = array(
			array(
				'id'       => 'wp-analytify-google-optimize',
				'title'    => __( 'Google Optimize', 'wp-analytify-pro' ),
				'desc'     => __( 'Learn and Setup Google Optimize.', 'wp-analytify-pro' ),
				'priority' => '0',
			),
		);

		return array_merge( $old_tabs, $pro_tabs );
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @since 1.0.0
	*/
	public function admin_scripts( $page ) {

	}

	/**
	 *
	 * Adds settings fields
	 *
	 * @since 1.0.0
	 */
	public function setting_fields( $old_fields ) {

		$pro_fields = array(
			'wp-analytify-google-optimize' => array(
				array(
					'name'  => 'google-optimize-container-id',
					'label' => __( 'Google Optimize Container ID', 'wp-analytify-pro' ),
					'desc'  => __( 'Add Google Optimize Container ID.', 'wp-analytify-pro' ),
					'type'  => 'text',
				),
				array(
					'name'  => 'google-optimize-async-hide',
					'label' => __( 'Enable Google Optimize Async Hide', 'wp-analytify-pro' ),
					'desc'  => __( 'Enable Google Optimize Async Hide', 'wp-analytify-pro' ),
					'type'  => 'checkbox',
				),
				array(
					'name'    => 'google-optimize-async-hide-time',
					'label'   => __( 'Google Optimize Async Hide Time', 'wp-analytify-pro' ),
					'desc'    => __( 'Google Optimize Async Hide Time', 'wp-analytify-pro' ),
					'type'    => 'text',
					'default' => '4000',
				),
				// array(
				// 	'name'    => 'google-optimize-container-condition',
				// 	'label'   => __( 'Conditionally Load Google Optimize Container Id', 'wp-analytify-pro' ),
				// 	'desc'    => __( 'By default google optimize container ID load in all pages on the frontend. If you enable & configure conditions then the container ID will load conditionally on the frontend.', 'wp-analytify-pro' ),
				// 	'type'    => 'optimize_multi_options',
				// ),
			),
		);
		
		return array_merge( $old_fields, $pro_fields );
	}

	/**
	 * Add Google Optimize Code.
	 *
	 * @since 1.0.0
	 */
	public function add_optimize_code() {

		$optimize_code = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'google-optimize-container-id', 'wp-analytify-google-optimize', '' );
		$tracking_code = WP_ANALYTIFY_FUNCTIONS::get_UA_code();

		if ( ! empty( $optimize_code )  ) {
			if ( 'gtag' === ANALYTIFY_TRACKING_MODE ) {
				echo 'gtag("config", "'.$tracking_code.'", { "optimize_id": "'.$optimize_code.'"});';
			} else {
				echo 'ga("require", "'.$optimize_code.'");';
			}
		}
	}

	/**
	 * Add Optimze Head Scripts.
	 *
	 * @since 1.0.0
	 */
	public function add_optimize_page_head_scripts() {

		$async_hide      = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'google-optimize-async-hide', 'wp-analytify-google-optimize', 'off' );
		$async_hide_time = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'google-optimize-async-hide-time', 'wp-analytify-google-optimize', '4000' );
		$optimize_code = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'google-optimize-container-id', 'wp-analytify-google-optimize', '' );

		// Add anti-flicker snippet.
		if ( 'off' != $async_hide ) {
			echo "<style>.async-hide { opacity: 0 !important} </style>
			<script>(function(a,s,y,n,c,h,i,d,e){s.className+=' '+y;h.start=1*new Date;
			h.end=i=function(){s.className=s.className.replace(RegExp(' ?'+y),'')};
			(a[n]=a[n]||[]).hide=h;setTimeout(function(){i();h.end=null},c);h.timeout=c;
			})(window,document.documentElement,'async-hide','dataLayer',{$async_hide_time},
			{'{$optimize_code}':true});</script>";
		}

		// Add Google Optimize script.
		if ( '' != $optimize_code ) {
			echo '<script src="https://www.googleoptimize.com/optimize.js?id='.$optimize_code.'"></script>';
		}
	}

	/**
	 * Add events tracking  settings in diagnostic information.
	 *
	 */
	function settings_logs() {

		echo "-- Google Optimize Setting --\r\n \r\n";
		
		$options = get_option( 'wp-analytify-google-optimize' );

		if ( method_exists( 'WPANALYTIFY_Utils', 'print_settings_array' ) ) {
			WPANALYTIFY_Utils::print_settings_array( $options );
		}
	}

}

new Analytify_Google_Optimize();
