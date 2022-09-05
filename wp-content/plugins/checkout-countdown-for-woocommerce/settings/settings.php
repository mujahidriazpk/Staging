<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Admin screen and settings for Checkout_Countdown_Main
 *
 * @author Morgan H
 * @version 1.0.0
 */

if ( ! class_exists( 'Checkout_Countdown_Main' ) ) :

	class Checkout_Countdown_Main {

		private $settings_api;

		protected $setup;

		function __construct( $setup ) {

			$this->setup = $setup;

			// Adds our settings API.
			require_once plugin_dir_path( __FILE__ ) . '/banana-framework/class-settings-api.php';

			add_action( 'wp_loaded', array( $this, 'load_classes' ) );

			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

			do_action( $this->setup['prefix'] . '_after_class_loaded' );

		}


		/**
		 * Load plugin classes.
		 *
		 * @since 1.0.0
		 */
		public function load_classes() {

			do_action( $this->setup['prefix'] . '_before_main_class_loaded' );

			if ( class_exists( 'Checkout_Countdown_Settings_API' ) ) {
				// Use this if plugin is free.
				$this->settings_api = new Checkout_Countdown_Settings_API( $this->setup );
			}

		}

		/**
		 * Load plugin textdomain.
		 *
		 * @since 1.0.0
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'checkout-countdown-for-woocommerce', false, basename( dirname( $this->setup['path'] ) ) . '/languages' );
		}

		public function admin_init() {
			// set the sections.
			$this->settings_api->set_sections( $this->get_settings_sections() );
			$this->settings_api->set_fields( $this->get_settings_fields() );
			$this->settings_api->set_sidebar( $this->get_settings_sidebar() );
			// initialize settings.
			$this->settings_api->admin_init();
		}

		public function admin_menu() {

			$page_title = 'Checkout Countdown for WooCommerce';
			$menu_title = 'Countdown';
			$capability = 'manage_options';
			$slug       = $this->setup['admin_page'];
			$callback   = array( $this, 'plugin_page' );
			$icon       = 'dashicons-clock';
			$position   = 100;
			add_menu_page( $page_title, $menu_title, $capability, $slug, $callback, $icon, $position );

		}

		/**
		 * Add a new section to the settings page
		 *
		 * @since 1.0.0
		 */

		public function get_settings_sections() {
			$sections = array(
				array(
					'id'    => 'ccfwoo_general_section',
					'title' => __( 'General', 'checkout-countdown-for-woocommerce' ),
				),
			);

			$sections = apply_filters( 'ccfwoo_extend_settings_sections', $sections );

			return $sections;
		}

		/**
		 * Returns all the settings fields
		 *
		 * @return array settings fields
		 */

		public function get_settings_fields() {

			$settings_fields = array();

			$settings_fields['ccfwoo_general_section'] = array(
				array(
					'name'  => 'quick_settings',
					'label' => __( 'Quick Settings', 'checkout-countdown-for-woocommerce' ),
					'desc'  => __( 'Quickly switch the Checkout Countdown on or off to get started.' ),
					'type'  => 'subheading',
					'class' => 'subheading',
				),
				array(
					'name'  => 'enable',
					'label' => __( 'Enable', 'checkout-countdown-for-woocommerce' ),
					'desc'  => __( 'Start', 'checkout-countdown-for-woocommerce' ),
					'type'  => 'checkbox',
				),
				array(
					'name'        => 'countdown_time',
					'label'       => __( 'Countdown from', 'checkout-countdown-for-woocommerce' ),
					'desc'        => __( 'Minutes (Use decimal for testing).', 'checkout-countdown-for-woocommerce' ),
					'placeholder' => __( '30', 'checkout-countdown-for-woocommerce' ),
					'min'         => 0.1,
					'step'        => 'any',
					'max'         => 100000,
					'type'        => 'number',
					'default'     => 30,
				),
				array(
					'name'    => 'countdown_locations',
					'label'   => __( 'Countdown Locations', 'checkout-countdown-for-woocommerce' ),
					'desc'    => __( 'Choose where you would like to display the countdown.', 'checkout-countdown-for-woocommerce' ),
					'type'    => 'multicheck',
					'options' => array(
						'bar'             => 'Countdown Bar - Whole Site',
						'cart-notice'     => 'Countdown in Cart page notice',
						'checkout-notice' => 'Countdown in Checkout page notice',
					),
					'default' => array(
						'bar' => 'Countdown bar - whole site',
					),
				),
				array(
					'name'  => 'section_display',
					'label' => __( 'Countdown Content', 'checkout-countdown-for-woocommerce' ),
					'desc'  => __( 'Choose how the countdown should be displayed.' ),
					'type'  => 'subheading',
					'class' => 'subheading',
				),
				array(
					'name'    => 'countdown_locations',
					'label'   => __( 'Countdown Locations', 'checkout-countdown-for-woocommerce' ),
					'desc'    => __( 'Choose where you would like to display the countdown.', 'checkout-countdown-for-woocommerce' ),
					'type'    => 'multicheck',
					'options' => array(
						'bar'             => 'Countdown Bar - Whole Site',
						'cart-notice'     => 'Countdown in Cart page notice',
						'checkout-notice' => 'Countdown in Checkout page notice',
					),
					'default' => array(
						'bar' => 'Countdown bar - whole site',
					),
				),
				array(
					'name'  => 'enable_banner_message',
					'label' => __( 'Enable Default Message', 'checkout-countdown-for-woocommerce' ),
					'desc'  => __( 'Display a default message before the customer has added a product to their cart.', 'checkout-countdown-for-woocommerce' ),
					'type'  => 'checkbox',
				),
				array(
					'name' => 'default_message_html',
					'desc' => __( 'Disabling the default message will hide the Countdown Bar until the customer has added product to their cart.', 'checkout-countdown-for-woocommerce' ),
					'type' => 'html',
				),
				array(
					'name'        => 'banner_message_text',
					'label'       => __( 'Default Message Text', 'checkout-countdown-for-woocommerce' ),
					'desc'        => __( 'Show a message in the banner before the countdown has started.', 'checkout-countdown-for-woocommerce' ),
					'placeholder' => __( '10% with a coupon for 24 hours only', 'checkout-countdown-for-woocommerce' ),
					'type'        => 'wysiwyg',
				),
				array(
					'name'        => 'countdown_text',
					'label'       => __( 'Counting Down Text', 'checkout-countdown-for-woocommerce' ),
					'desc'        => __(
						'<p>
					<b>Available Tags</b><br /><code>{days}</code> <code>{hours}</code> <code>{minutes}</code> <code>{seconds}</code><br />',
						'checkout-countdown-for-woocommerce'
					),
					'placeholder' => __( 'We can only hold your item for {minutes} minutes and {seconds} seconds!', 'checkout-countdown-for-woocommerce' ),
					'type'        => 'wysiwyg',
					'default'     => 'We can only hold your item for {minutes} minutes and {seconds} seconds!',
				),
				array(
					'name' => 'html',
					'desc' => __( 'Use the above tags to insert the time where needed.<br />You can also use basic html.', 'checkout-countdown-for-woocommerce' ),
					'type' => 'html',
				),
				array(
					'name'        => 'expired_text',
					'label'       => __( 'Countdown Expired Text', 'checkout-countdown-for-woocommerce' ),
					'desc'        => __( 'Displayed when the countdown has finished', 'checkout-countdown-for-woocommerce' ),
					'placeholder' => __( 'We can only hold on to items for so long. Please try again', 'checkout-countdown-for-woocommerce' ),
					'type'        => 'wysiwyg',
					'default'     => 'We can only hold on to items for so long. Please try again',
				),
				array(
					'name'  => 'section_top_banner',
					'label' => __( 'Countdown Bar', 'checkout-countdown-for-woocommerce' ),
					'desc'  => __( 'These settings only apply to the Countdown Bar option' ),
					'type'  => 'subheading',
					'class' => 'subheading',
				),
				array(
					'name'    => 'bar_position',
					'label'   => __( 'Bar Position', 'checkout-countdown-for-woocommerce' ),
					'desc'    => __( 'Select the position of the Countdown Bar on your store.', 'checkout-countdown-for-woocommerce' ),
					'type'    => 'radio',
					'default' => 'top',
					'options' => array(
						'top'    => 'Top Bar',
						'bottom' => 'Bottom Bar',
					),

				),
				array(
					'name'    => 'top_banner_background_color',
					'label'   => __( 'Background Color', 'checkout-countdown-for-woocommerce' ),
					'desc'    => __( 'Choose a background color for the banner.', 'checkout-countdown-for-woocommerce' ),
					'type'    => 'color',
					'default' => '#000000',
				),
				array(
					'name'    => 'top_banner_font_color',
					'label'   => __( 'Font Color', 'checkout-countdown-for-woocommerce' ),
					'desc'    => __( 'Choose a font color for the bannner.', 'checkout-countdown-for-woocommerce' ),
					'type'    => 'color',
					'default' => '#ffffff',
				),
				array(
					'name'  => 'miscellaneous_settings',
					'label' => __( 'Miscellaneous', 'checkout-countdown-for-woocommerce' ),
					'desc'  => __( 'Miscellaneous tweaks to personalize the countdown.' ),
					'type'  => 'subheading',
					'class' => 'subheading',
				),
				array(
					'name'  => 'leading_zero',
					'label' => __( 'Leading Zero', 'checkout-countdown-for-woocommerce' ),
					'desc'  => __( 'Leading zeros for minutes & seconds allow countdowns with this style: <code>03:02</code>', 'checkout-countdown-for-woocommerce' ),
					'type'  => 'checkbox',
				),
				array(
					'name'  => 'advanced',
					'label' => __( 'Advanced', 'checkout-countdown-for-woocommerce' ),
					'desc'  => __( '' ),
					'type'  => 'subheading',
					'class' => 'subheading',
				),
				array(
					'name'  => 'delete_settings',
					'label' => __( 'Delete settings on uninstallation ', 'checkout-countdown-for-woocommerce' ),
					'desc'  => __( 'Delete all settings from the database when uninstalling the plugin.', 'checkout-countdown-for-woocommerce' ),
					'type'  => 'checkbox',
				),
			);

			$settings_fields = apply_filters( 'ccfwoo_extend_setting_fields', $settings_fields );

			return $settings_fields;
		}

		/**
		 * Add sidebars to the setting page
		 *
		 * @since 1.0.0
		 */

		function get_settings_sidebar() {
			$sidebar = array(
				array(
					'id'      => 'sidebar-1',
					'title'   => __( 'Shortcode', 'checkout-countdown-for-woocommerce' ),
					'content' => __(
						'<p>
					        Use the <strong>[checkout_countdown]</strong> shortcode anywhere in your theme.
					        </p>
					        <p>
					        The shortcode is unstyled by default. You can apply your style via CSS. Use the example CSS to get started.
					        </p>
							<h4>Example CSS</h4>
							<div style="background-color: #373737;padding: 5px;color: #ffffff;font-size: 11px;">
							
							.checkout-countdown-shortcode {<br>
					        text-align: center;<br>
					        background: black;<br>
					        color: white;<br>
					        padding: 10px;<br>
					        width: 100%;<br>
					        display: inline-block;<br>
					        }
							
							</div>
					    ',
						'checkout-countdown-for-woocommerce'
					),
				),
				array(
					'id'      => 'sidebar-2',
					'title'   => __( 'Help us with a review', 'checkout-countdown-for-woocommerce' ),
					'content' => __( '<p>We have spent a long time developing Checkout Countdown,  <a href="https://wordpress.org/plugins/checkout-countdown-for-woocommerce/#reviews" target="_blank">leaving a review</a> give us motivation and help support the plugin!</p> ', 'checkout-countdown-for-woocommerce' ),
				),
				array(
					'id'      => 'sidebar-3',
					'title'   => __( 'Docs & Support', 'checkout-countdown-for-woocommerce' ),
					'content' => __( 'See our <a href="https://puri.io/docs" target="_blank">documentation</a> if you need more information, if you can\'t find what you are looking for just reach out to <a href="https://puri.io/support" target="_blank">support</a>. ', 'checkout-countdown-for-woocommerce' ),
				),

			);

			$sidebar = apply_filters( 'ccfwoo_extend_extend_settings_sidebar', $sidebar );

			return $sidebar;
		}


		/**
		 * Display the plugin page
		 */
		function plugin_page() {
			echo '<div class="wrap ' . $this->setup['prefix'] . '-admin">';
			echo '<h2>' . apply_filters( 'ccfwoo_filter_settings_title', $this->setup['name'] ) . '</h2>';
			$this->settings_api->show_navigation();
			$this->settings_api->show_sidebar();
			$this->settings_api->show_forms();
			echo '</div>';
		}

		/**
		 * Get all the pages
		 *
		 * @return array page names with key value pairs
		 */
		function get_pages() {
			$pages         = get_pages();
			$pages_options = array();
			if ( $pages ) {
				foreach ( $pages as $page ) {
					$pages_options[ $page->ID ] = $page->post_title;
				}
			}
			return $pages_options;
		}

		/**
		 * Get the value of a settings field
		 *
		 * @param string $option  settings field name
		 * @param string $section the section name this field belongs to
		 * @param string $default default text if it's not found
		 * @return string
		 */
		public function get_option( $option, $section, $default = '' ) {

			$options = get_option( $section );

			if ( isset( $options[ $option ] ) ) {
					return $options[ $option ];
			}
			return $default;
		}
	}
endif;
