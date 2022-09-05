<?php

/**
 * Add the countdown as a shortcode [checkout_countdown]
 */
function ccfwoo_shortcode() {
	$shortcode = ccfwoo_countdown_html_content( 'shortcode' );

	return $shortcode;
}
add_shortcode( 'checkout_countdown', 'ccfwoo_shortcode' );

/**
 * Legacy shortcodes [cc_countdown] [cc-countdown]
 */
add_shortcode( 'cc_countdown', 'ccfwoo_shortcode' );
add_shortcode( 'cc-countdown', 'ccfwoo_shortcode' );


/**
 * Display the countdown as a checkout notice.
 */
function ccfwoo_checkout_notice() {

	$locations = is_array( ccfwoo_get_option( 'countdown_locations' ) ) ? ccfwoo_get_option( 'countdown_locations' ) :
	array();

	//Mujahid Code
	$product_id = 1642;
	$in_cart = false;
	
	foreach( WC()->cart->get_cart() as $cart_item ) {
		$product_in_cart = $cart_item['product_id'];
		if ( $product_in_cart === $product_id ) $in_cart = true;
	}
	if (!$in_cart ) {
		if ( ! in_array( 'checkout-notice', $locations, true ) || 1==1) {
			return;
		}
	}

	return ccfwoo_countdown_html_content( 'notice' );

};
add_action( 'woocommerce_before_checkout_form', 'ccfwoo_checkout_notice', 1 );

/**
 * Display the countdown as a cart noice.
 */
function ccfwoo_cart_notice() {

	$locations = is_array( ccfwoo_get_option( 'countdown_locations' ) ) ? ccfwoo_get_option( 'countdown_locations' ) :
	array();

	if ( ! in_array( 'cart-notice', $locations, true ) ) {
		return;
	}
	return ccfwoo_countdown_html_content( 'notice' );

};
add_action( 'woocommerce_before_cart', 'ccfwoo_cart_notice', 1 );

/**
 * Display the banner content on the HTML.
 */
function ccfwoo_display_bar() {
	$locations = is_array( ccfwoo_get_option( 'countdown_locations' ) ) ? ccfwoo_get_option( 'countdown_locations' ) :
	array();

	if ( ! in_array( 'bar', $locations, true ) ) {
		return;
	}

	echo ccfwoo_countdown_html_content( 'bar' );

}
add_action( 'wp_head', 'ccfwoo_display_bar', 100 );

/**
 * Loading dots as html.
 */
function ccfwoo_loading_html() {
	$loading_dots = '<div class="ccfwoo-loading-dots">
    <div class="ccfwoo-loading-dots--dot"></div>
    <div class="ccfwoo-loading-dots--dot"></div>
    <div class="ccfwoo-loading-dots--dot"></div>
	</div>';

	return $loading_dots;
}

/**
 * HTML Content for the countdown.
 * Can also return as WC notice.
 */
function ccfwoo_countdown_html_content( $type ) {

	$loading_dots = ccfwoo_loading_html();

	if ( $type === 'notice' ) {

		$notice_html  = '<div class="checkout-countdown-wrapper checkout-countdown-notice" id="cc-countdown-wrap">
    <div class="checkout-countdown-content" id="cc-countdown-timer">';
		$notice_html .= $loading_dots;
		$notice_html .= '</div></div>';
		// error, notice or success.
		return wc_print_notice( $notice_html, 'error' );
	}

	$add_classes = '';
	$add_id      = '';

	if ( $type === 'shortcode' ) {
		$add_classes .= 'checkout-countdown-shortcode cc-shortcode cc-countdown ';
		$add_id       = 'cc-countdown-timer';
	}

	if ( $type === 'bar' ) {
		$add_classes .= 'checkout-countdown-bar ';
	}

	$add_classes .= ccfwoo_get_option( 'enable_banner_message' ) !== 'on' && ccfwoo_get_cart_content() === 0 ?
	'ccfwoo-is-hidden' : '';

	$html = '<div class="checkout-countdown-wrapper ' . $add_classes . '" id="' . $add_id . '">';
	$html .= '<div class="checkout-countdown-content">';
	$html .= $loading_dots;
	$html .= '</div>';
	$html .= '</div>';

	return $html;
}
