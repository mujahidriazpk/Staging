<?php

class Advanced_Ads_Selling {

	/**
	 * Holds plugin base class
	 *
	 * @var Advanced_Ads_Selling_Plugin
	 * @since 1.0.0
	 */
	protected $plugin;

	/**
	 * Initialize the plugin
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {
		$this->plugin = Advanced_Ads_Selling_Plugin::get_instance();

		add_action( 'wp_loaded', array( $this, 'save_ad_content' ) );

		// register events when all plugins are loaded
		add_action( 'plugins_loaded', array( $this, 'wp_plugins_loaded' ) );
		// the hook needs to be 'wp' because we need access to the query
		add_action( 'wp', array( $this, 'load_ad_setup_page' ) );

		// Enqueue public scripts and style
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_scripts' ) );

		// Add action to add "Ad link" while mail is sent
		add_action( 'woocommerce_email_after_order_table', array( $this, 'display_ad_link' ), 10, 4 );

		// manipulate product price layout
		add_action( 'woocommerce_get_price_html', array( $this, 'manipulare_price_html' ), 10, 2 );

		// Show setup form in the content.
		// After the Selling add-on has injected the form into the content, the Beaver Builder replaces all the content,
		// including the Selling form. We inject the form at priority 11 after the Beaver Builder has already replaced
		// all the content at priority 10.
		add_action( 'the_content', array( $this, 'display_setup_form' ), 11 );
	}

	public function enqueue_public_scripts() {
		global $post;

		if ( ! class_exists( 'WooCommerce', false ) ) {
			return;
		}

		wp_register_script( 'advanced-ads-selling-single-product-script', AASA_BASE_URL . 'public/assets/js/ad-setup.js', array( 'jquery' ) );

		if ( is_product() ) {
			$woocommerce_price_decimal_sep = get_option( 'woocommerce_price_decimal_sep' );
			$woocommerce_currency_position = get_option( 'woocommerce_currency_pos', 'left' );
			$prices                        = Advanced_Ads_Selling_Plugin::get_prices( $post->ID );
			$reverse_prices                = array_reverse( $prices, true ); // Reverse array preserving keys
			$get_prices                    = array();

			foreach ( $reverse_prices as $reverse_price ) {
				$get_prices[] = $reverse_price;
			}

			// add js for check code in public
			wp_enqueue_script( 'advanced-ads-selling-single-product-script' );

			wp_localize_script( 'advanced-ads-selling-single-product-script', 'AdvancedAdSelling', array(
				'ajaxurl'                       => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
				'product_prices'                => $get_prices,
				'woocommerce_price_decimal_sep' => $woocommerce_price_decimal_sep,
				'woocommerce_currency_position' => $woocommerce_currency_position,
			) );
		}

		// load on setup page
		$options        = Advanced_Ads_Selling_Plugin::get_instance()->options();
		$public_page_id = ( isset( $options['setup-page-id'] ) && $options['setup-page-id'] ) ? absint( $options['setup-page-id'] ) : false;
		if ( $public_page_id && isset( $post->ID ) && $post->ID === $public_page_id ) {
			wp_enqueue_script( 'advanced-ads-selling-single-product-script' );
			wp_localize_script( 'advanced-ads-selling-single-product-script', 'AdvancedAdSelling', array(
				'maxFileSize' => apply_filters( 'advanced-ads-selling-upload-file-size', 1048576 ),
			) );
		}

		$options = Advanced_Ads_Selling_Plugin::get_instance()->options();
		if ( isset( $options['wc-fixes'] ) && $options['wc-fixes'] ) {
			wp_enqueue_style( 'advanced-ads-selling-wc-fixes', AASA_BASE_URL . 'public/assets/css/wc-fixes.css', array( 'woocommerce-layout' ), AASA_VERSION );
		}
	}

	/**
	 * Load actions and filters
	 */
	public function wp_plugins_loaded() {
		if ( ! class_exists( 'WooCommerce', false ) ) {
			return;
		}

		add_filter( 'advanced-ads-can-inject-into-content', array( $this, 'prevent_ads_inject_into_woo_content' ), 10, 2 );
		add_action( 'woocommerce_thankyou', array( $this, 'display_ads_setup_data' ), 10, 1 );
		add_action( 'woocommerce_view_order', array( $this, 'display_ads_setup_data' ), 10, 1 );
		add_action( 'woocommerce_advanced_ad_add_to_cart', array( $this, 'add_to_cart_template' ) );

		// apply WooCommerce fixes
		$options = Advanced_Ads_Selling_Plugin::get_instance()->options();
		if ( isset( $options['wc-fixes'] ) && $options['wc-fixes'] ) {
			// Remove image from product pages
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
			// Remove sale badge from product page
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
			// Remove product thumbnail from the cart page
			add_filter( 'woocommerce_cart_item_thumbnail', '__return_empty_string' );
			// Remove product images from the shop loop
			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
			// Remove sale badges from the shop loop
			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
		}
	}

	/**
	 * Load template for add-to-cart with the correct filter for the fields
	 */
	public function add_to_cart_template() {
		$post_id = get_the_ID();

		$prices     = Advanced_Ads_Selling_Plugin::get_prices( $post_id );
		$ad_types   = get_post_meta( $post_id, '_ad_types', true );
		$sales_type = get_post_meta( $post_id, '_ad_sales_type', true );
		// reset sales type, when Tracking is not enabled
		if ( in_array( $sales_type, array( 'impressions', 'clicks' ) ) && ! defined( 'AAT_VERSION' ) ) {
			$sales_type = 'flat';
		}

		$ad_types_raw = Advanced_Ads::get_instance()->ad_types;

		if ( $placements = get_post_meta( $post_id, '_ad_placements', true ) ) {
			// load all placements
			$model          = Advanced_Ads::get_instance()->get_model();
			$placements_raw = $model->get_ad_placements_array();
		} else {
			$placements = array();
		}

		require AASA_BASE_PATH . 'public/views/product-template.php';
	}

	/**
	 *  Load ad setup page, if we are on the right url
	 */
	public function load_ad_setup_page() {
		if ( is_admin() || ! class_exists( 'WooCommerce', false ) ) {
			return;
		}

		$page_hash = $this->get_page_hash_from_setup_url();

		// display the page if the hash is correct
		if ( $page_hash && ! $this->is_setup_page() ) {

			// check if there is another page selected as the ad setup page and forward to it
			$options        = Advanced_Ads_Selling_Plugin::get_instance()->options();
			$public_page_id = ( isset( $options['setup-page-id'] ) && $options['setup-page-id'] ) ? absint( $options['setup-page-id'] ) : false;

			if ( $public_page_id && get_post( $public_page_id ) ) {
				$setup_page_url = Advanced_Ads_Selling_Plugin::get_instance()->get_ad_setup_url( $page_hash );
				wp_redirect( $setup_page_url );
				die();
			}

			$order_id = $this->hash_to_order_id( $page_hash );

			// load the order
			$order = new WC_Order( $order_id );
			$items = $order->get_items();

			if ( false !== $order_id ) {
				require_once AASA_BASE_PATH . 'public/views/ad-setup-page.php';
				die;
			}
		}
	}

	/**
	 * Load ad setup form into custom page in the page content
	 */
	public function display_setup_form( $content ) {
		if ( ! class_exists( 'WooCommerce', false ) || doing_action( 'wp_head' ) ) {
			return $content;
		}

		$page_hash = $this->get_page_hash_from_setup_url();

		if ( $page_hash ) {
			$order_id = $this->hash_to_order_id( $page_hash );

			// load the order
			$order = new WC_Order( $order_id );
			$items = $order->get_items();

			if ( false !== $order_id ) {
				ob_start();

				require_once AASA_BASE_PATH . 'public/views/ad-setup-form.php';

				if ( defined( 'ADVANCED_ADS_SELLING_SHOW_SETUP_FORM_AFTER_CONTENT' ) ) {
					$content .= ob_get_clean();
				} else {
					$content = ob_get_clean() . $content;
				}
			}
		} elseif ( $this->is_setup_page() ) {
			$content .= '<p>' . __( 'The ad setup form is only visible through the link in the purchase confirmation email or account.', 'advanced-ads-selling' ) . '</p>';
		}

		return $content;
	}

	/**
	 * Get order for a setup page
	 *
	 * @return false|str
	 */
	public function get_page_hash_from_setup_url( $type = 'page' ) {

		// check if this page has the hash as a get parameter
		$page_hash = false;

		if ( isset( $_GET['h'] ) && $this->is_setup_page( get_the_ID() ) ) {
			return esc_attr( $_GET['h'] );
		} else {
			$protocol = 'http';
			if ( is_ssl() ) {
				$protocol .= 's';
			}
			$protocol .= '://';

			$full_url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

			// use $site_url if this causes problems with WP installations in subdirectories
			$site_url = home_url();

			$sub1 = substr( $full_url, strlen( $site_url ) );

			if ( 0 === strpos( $sub1, '/' . Advanced_Ads_Selling_Plugin::AD_STORE_SLUG . '/' ) ) {
				$expl      = explode( '/', $sub1 );
				$page_hash = $expl[2];
			}
		}

		return $page_hash;
	}

	/**
	 * Check if the current page is the ad setup page
	 * according to the Settings under Advanced Ads > Settings > Selling Ads
	 *
	 * @param integer $post_id WP_Post ID.
	 * @return bool true if the current post is the setup page
	 */
	public function is_setup_page( $post_id = 0 ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! $post_id ) {
			return false;
		}

		// check if this is the ad setup page
		$options        = Advanced_Ads_Selling_Plugin::get_instance()->options();
		$public_page_id = (int) ( isset( $options['setup-page-id'] ) && $options['setup-page-id'] ) ? absint( $options['setup-page-id'] ) : false;

		return $public_page_id && $public_page_id === $post_id;
	}

	/**
	 * Prevent Ads from woocommerce content
	 */
	public function prevent_ads_inject_into_woo_content( $flag, $content ) {
		if ( ! defined( 'ADVANCED_ADS_SELLING_ALLOW_WC_PAGE_INJECTIONS' ) && $this->is_woocommerce_page() ) {
			$flag = false;
		}
		return $flag;
	}

	/**
	 * Prevent Ads from woocommerce pages
	 */
	public function prevent_ads_inject_into_woo_pages( $flag, $options ) {
		if ( ! defined( 'ADVANCED_ADS_SELLING_ALLOW_WC_PAGE_INJECTIONS' ) && $this->is_woocommerce_page() ) {
			$flag = false;
		}

		return $flag;
	}

	/**
	 * Display ads setup data
	 */
	public function display_ads_setup_data( $orderid ) {

		// check, if the link should be displayed to the client at all
		if ( Advanced_Ads_Selling_Plugin::hide_ad_setup() ) {
			return;
		}

		$hash = get_post_meta( $orderid, 'advanced_ads_selling_setup_hash', true );

		if ( $hash ) {
			$setup_url = Advanced_Ads_Selling_Plugin::get_instance()->get_ad_setup_url( $hash );
			printf( __( 'You can manage the content of your ads on the <a href="%s">ad setup page</a>.', 'advanced-ads-selling' ), $setup_url );
		}
	}

	/**
	 * Check is woocommerce page
	 */
	public function is_woocommerce_page() {
		$flag = false;
		if ( is_shop() || is_product_taxonomy() || is_product() || is_cart() || is_checkout() || is_account_page() || $this->is_setup_page() ) {
			$flag = true;
		}
		return $flag;
	}

	/**
	 * Display ads setup data in order confirmation email
	 */
	public function display_ad_link( $order, $sent_to_admin, $plain_text, $email ) {

		// Display Add link in email

		global $woocommerce;
		if ( version_compare( $woocommerce->version, '3.0', '>=' ) ) {
			$order_id = $order->get_id();
		} else {
			$order_id = $order->id;
		}

		$this->display_ads_setup_data( $order_id );
	}

	/**
	 * Manipulate the price html output adding a unique class if in the loop
	 *
	 * @param type $price_html
	 * @param type $product
	 */
	public function manipulare_price_html( $price_html, $product ) {
		global $wp_query;

		// change price only on single pages of the current product
		$product_type = $product->get_type();
		if ( isset( $product_type ) && 'advanced_ad' === $product_type && in_the_loop() && is_single()
			&& $wp_query->queried_object_id === $product->get_id() ) {

			// add another class
			$pattern = '/woocommerce-Price-amount/';
			return preg_replace( $pattern, 'woocommerce-Price-amount woocommerce-ad-price', $price_html );
		} else {
			return $price_html;
		}
	}

	/**
	 *  Get order ID from the page hash
	 */
	protected function hash_to_order_id( $hash ) {
		$args = array(
			'post_type'      => 'shop_order',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'   => 'advanced_ads_selling_setup_hash',
					'value' => $hash,
				),
			),
		);

		$query = new WP_Query( $args );

		return isset( $query->posts[0] ) ? $query->posts[0] : false;
	}

	/**
	 * Save ad content sent from backend
	 */
	public function save_ad_content() {

		// If $_POST['aas_upload_ad'] is set
		if ( ! empty( $_POST['advanced-ads-selling-upload-ad'] ) && $_POST['advanced-ads-selling-upload-ad'] == 'advanced-ads-selling-upload-ad' ) {
			$data = array(); // Declare variables
			switch ( $_POST['advads_selling_ad_type'] ) { // Switch case on ad_type selected
				case 'image':
					$errors     = ''; // Declare variables
					$file_name  = $_FILES['advads_selling_ad_image']['name']; // Get file name
					$file_size  = $_FILES['advads_selling_ad_image']['size']; // Get file size
					$file_tmp   = $_FILES['advads_selling_ad_image']['tmp_name']; // Get file's temporary name
					$file_type  = $_FILES['advads_selling_ad_image']['type']; // Get file type
					$target_url = ! empty( $_POST['advads_selling_ad_url'] ) ? $_POST['advads_selling_ad_url'] : '';

					$file_datas = explode( '.', $file_name ); // Explode file name to retrieve extension
					foreach ( $file_datas as $file_data ) {
						$file_data_arr[] = $file_data; // Convert associative array to normal array
					}

					$file_ext   = strtolower( end( $file_data_arr ) ); // Get file extension
					$expensions = array( 'jpeg', 'jpg', 'png', 'gif' ); // Declare variables for allowed extension types

					if ( in_array( $file_ext, $expensions ) === false ) { // if file extensions is within allowed extensions
						$errors = esc_html__( 'Extension not allowed, please choose a JPEG, PNG or GIF file.', 'advanced-ads-selling' );
					}

					$max_file_size = apply_filters( 'advanced-ads-selling-upload-file-size', 1048576 );
					if ( $file_size > $max_file_size ) {
						$errors = sprintf(
							// translators: %s is a file size with one decimal
							__( 'The allowed file size is %s MB', 'advanced-ads-selling' ),
							number_format_i18n( $max_file_size / 1000000, 1 )
						);
					}

					if ( empty( $errors ) ) {

						// Get the path to the upload directory.
						$wp_upload_dir = wp_upload_dir();

						// These files need to be included as dependencies when on the front end.
						require_once ABSPATH . 'wp-admin/includes/image.php';
						require_once ABSPATH . 'wp-admin/includes/file.php';
						require_once ABSPATH . 'wp-admin/includes/media.php';

						// Insert the attachment.
						$attach_id = media_handle_upload( 'advads_selling_ad_image', 0 );
						$post_guid = get_the_guid( $attach_id );

						$ad_id           = Advanced_Ads_Selling_Order::order_item_id_to_ad_id( $_POST['advads_selling_order_item'] );
						$ad_post_content = '<img src="' . $post_guid . '" alt="' . $file_name . '" />';
						/*
						if( !empty( $target_url ) ) {//check when target url then wrap link with them
							$ad_post_content	= sprintf( '<a href="%s" target="_blank">%s</a>', $target_url, $ad_post_content );
						}*/

						// get ad object
						$ad = new Advanced_Ads_Ad( $ad_id );

						$ad->type = 'image';

						$output['image_id'] = $attach_id;

						$ad->set_option( 'output', $output );

						$ad->url = esc_url( $_POST['advads_selling_ad_url'] );

						// double check if we can use fopen
						/*
						if ( !empty ( $post_guid ) && ini_get('allow_url_fopen') && function_exists( 'getimagesize' ) ) {
							$image_size = getimagesize( $post_guid );

							$ad->width  = $image_size[0];
							$ad->height = $image_size[1];
						} else {
							$ad->width  = 0;
							$ad->height = 0;
						}*/

						$ad->content = $ad_post_content;
						$ad->status  = 'pending';

						if ( $attach_id ) {
							$attachment_meta = wp_get_attachment_metadata( $attach_id );
							$all_posts_id    = get_post_meta( $attach_id, '_advanced-ads_parent_id' );

							if ( ! in_array( $ad_id, $all_posts_id ) ) {
								add_post_meta( $attach_id, '_advanced-ads_parent_id', $ad_id, false );
							}
						}

						$ad->width  = isset( $attachment_meta['width'] ) ? absint( $attachment_meta['width'] ) : 0;
						$ad->height = isset( $attachment_meta['height'] ) ? absint( $attachment_meta['height'] ) : 0;

						// update the ad post
						$new_ad_content = array(
							'ID'          => $ad_id,
							'post_status' => 'pending',
						);

						$return = wp_update_post( $new_ad_content );

						$ad->save();

						$_POST['success'] = 'success';
					} else {
						$_POST['errors'] = $errors;
					}

					break;

				case 'plain':
					if ( ! isset( $_POST['advads_selling_ad_content'] ) || ! trim( $_POST['advads_selling_ad_content'] ) ) {
						$errors = esc_html__( 'Ad content missing.', 'advanced-ads-selling' );
					}

					if ( empty( $errors ) ) {
					$ad_id = Advanced_Ads_Selling_Order::order_item_id_to_ad_id( $_POST['advads_selling_order_item'] );

					// update the ad post
					$new_ad_content = array(
						'ID'           => $ad_id,
						'post_content' => trim( $_POST['advads_selling_ad_content'] ),
						'post_status'  => 'pending',
					);

					$return = wp_update_post( $new_ad_content );
					if ( is_wp_error( $return ) ) {
							die( esc_html__( 'Error when submitting the ad. Please contact the site admin.', 'advanced-ads-selling' ) );
						}

						$_POST['success'] = 'success';
					} else {
						$_POST['errors'] = $errors;
					}
			}

			/**
			 * Allow add-ons to add their own logic for another custom ad type
			 */
			do_action( 'advanced-ads-selling-save-ad-content-after', $_POST );
		}
	}
}
