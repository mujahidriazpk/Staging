<?php
/***
 * Public Stats endpoint class
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/***
 * Class Advanced_Ads_Selling_Ads_Page_Endpoint
 */
class Advanced_Ads_Selling_Ads_Page_Endpoint {

	/**
	 * Public Stats endpoint.
	 *
	 * @var string
	 */
	public static $endpoint = 'ads';

	/**
	 * User id.
	 *
	 * @var integer
	 */
	private $user_id;

	/**
	 * Plugin actions.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'wp_plugins_loaded' ) );
	}

	/**
	 * Add endpoint only on plugins_loaded with various checks
	 */
	public function wp_plugins_loaded() {
		$this->user_id = get_current_user_id();
		if ( ! class_exists( 'Advanced_Ads', false ) ) {
			return;
		}

		if ( ! class_exists( 'WooCommerce', false ) ) {
			return;
		}

		if ( defined( 'AAT_VERSION' ) && version_compare( AAT_VERSION, '1.8.18', '>=' ) ) {
			// Actions used to insert a new endpoint in the WordPress.
			add_action( 'init', array( $this, 'add_endpoints' ) );
			add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );

			// Change the My Account page title.
			add_filter( 'the_title', array( $this, 'endpoint_title' ) );
			add_action( 'wp_loaded', array( $this, 'on_wp_loaded' ) );
		}
	}

	/**
	 * Register hook functions that need WP to be loaded.
	 */
	public function on_wp_loaded() {
		if ( Advanced_Ads_Selling_Order::get_instance()->customer_has_ads( $this->user_id ) ) {
			// Inserting your new tab/page into the My Account page.
			add_filter( 'woocommerce_account_menu_items', array( $this, 'new_menu_items' ) );
			add_action( 'woocommerce_account_ads_endpoint', array( $this, 'endpoint_content' ) );
		}
	}

	/**
	 * Register new endpoint to use inside My Account page.
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
	 */
	public function add_endpoints() {
		add_rewrite_endpoint( 'ads', EP_ROOT | EP_PAGES );
	}

	/**
	 * Add new query var.
	 *
	 * @param array $vars
	 *
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'ads';

		return $vars;
	}

	/**
	 * Set endpoint title.
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	public function endpoint_title( $title ) {
		global $wp_query;

		if ( ! $wp_query ) {
			return false;
		}
		$is_endpoint = isset( $wp_query->query_vars[ self::$endpoint ] );

		if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
			// New page title.
			$title = __( 'Ads', 'advanced-ads-selling' );

			remove_filter( 'the_title', array( $this, 'endpoint_title' ) );
		}

		return $title;
	}

	/**
	 * Insert the new endpoint into the My Account menu.
	 *
	 * @param array $items
	 *
	 * @return array
	 */
	public function new_menu_items( $items ) {
		// Remove the logout menu item.
		$logout = $items['customer-logout'];
		unset( $items['customer-logout'] );

		// Insert your Active Ads endpoint.
		$items[ self::$endpoint ] = __( 'Ads', 'advanced-ads-selling' );

		// Insert back the logout item.
		$items['customer-logout'] = $logout;

		return $items;
	}

	/**
	 * Endpoint HTML content.
	 */
	public function endpoint_content() {
		include AASA_BASE_PATH . 'public/views/woocommerce/myaccount/ads.php';
	}

	/**
	 * Plugin install action.
	 * Flush rewrite rules to make our active ads endpoint available.
	 */
	public function install() {
		flush_rewrite_rules();
	}
}

// Flush rewrite rules on plugin activation.
register_activation_hook( __FILE__, array( 'Advanced_Ads_Selling_Ads_Page_Endpoint', 'install' ) );
