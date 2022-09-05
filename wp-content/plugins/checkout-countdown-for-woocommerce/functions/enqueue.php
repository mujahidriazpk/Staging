<?php

/**
 * Load the CCFWOO Javascript.
 */
function ccfwoo_core_enqueue_scripts() {

	wp_enqueue_style( 'ccfwoo-style', plugin_dir_url( __FILE__ ) . '../assets/checkout-countdown.min.css', array(), '3.1.6' );

	if ( ccfwoo_get_option( 'bar_position', false, 'top' ) === 'top' ) {
		$position = 'position: relative;';
	} else {
		$position  = 'position: fixed;';
		$position .= 'bottom: 0;';
	}

	$inline_css = sprintf(
		'.checkout-countdown-wrapper.checkout-countdown-bar {
	color: %s;
	background-color: %s;
	  %s
}',
		ccfwoo_get_option( 'top_banner_font_color' ),
		ccfwoo_get_option( 'top_banner_background_color' ),
		$position
	);

	 wp_add_inline_style( 'ccfwoo-style', $inline_css );

	// Load Javascript and Access settings as variables.
	wp_enqueue_script( 'ccfwoo-countdown', plugin_dir_url( __FILE__ ) . '../assets/checkout-countdown.min.js', array(), '3.1.6', true );

	$countdown_text = sprintf(
		/* translators: %s: is the countdown text. */
		_x( '%s', 'Frontend: Counting text.', 'checkout-countdown-for-woocommerce' ), // phpcs:ignore WordPress.WP.I18n.NoEmptyStrings
		ccfwoo_get_option( 'countdown_text', false, false )
	);

	$expired_text = sprintf(
		/* translators: %s: is the expired countdown text. */
		_x( '%s', 'Frontend: Expired Countdown text', 'checkout-countdown-for-woocommerce' ), // phpcs:ignore WordPress.WP.I18n.NoEmptyStrings
		ccfwoo_get_option( 'expired_text', false, false )
	);

	$banner_message_text = sprintf(
		/* translators: %s: is banner text. */
		_x( '%s', 'Frontend: Banner text before counting', 'checkout-countdown-for-woocommerce' ), // phpcs:ignore WordPress.WP.I18n.NoEmptyStrings
		ccfwoo_get_option( 'banner_message_text', false, false )
	);
	//Mujahid Code
	$flag = '';
	foreach(WC()->cart->get_cart() as $key => $val ) {
		$_product = $val['data'];
		if($_product->get_id()==1642) {
			$flag = 'relist';
			break;
		}
	}
	if($flag == 'relist'){
		$countdown_time = 5;
	}else{
		$countdown_time = ccfwoo_get_option( 'countdown_time' );
	}
	////////////////line change below//////////////////////////////
	$data = array(
		'ccfwoo_minutes'              => $countdown_time,
		'top_banner_font_color'       => ccfwoo_get_option( 'top_banner_font_color' ),
		'top_banner_background_color' => ccfwoo_get_option( 'top_banner_background_color' ),
		'countdown_text'              => $countdown_text,
		'expired_text'                => $expired_text,
		'banner_message_text'         => $banner_message_text,
		'enable_banner_message'       => ccfwoo_get_option( 'enable_banner_message' ),
		'leading_zero'                => ccfwoo_get_option( 'leading_zero', false, false ),
		'cart_count'                  => ccfwoo_get_cart_content(), // Check the number of products in cart.
		'countdown_locations'         => is_array( ccfwoo_get_option( 'countdown_locations' ) ) ? ccfwoo_get_option( 'countdown_locations' ) : array(),
		'loading_html'                => ccfwoo_loading_html(),
		'expired_message_seconds'     => apply_filters( 'ccfwoo_expired_message_seconds', 6 ),
	);

	wp_localize_script(
		'ccfwoo-countdown',
		'ccfwooLocal',
		$data
	);

	do_action( 'ccfwoo_enqueue_scripts' );
}

add_action( 'wp_enqueue_scripts', 'ccfwoo_core_enqueue_scripts', 20 );
