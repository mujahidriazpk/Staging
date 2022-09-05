<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Remove all settings on uninstall
 */
function ccfwoo_uninstall() {
	if ( ccfwoo_get_option( 'delete_settings' ) === 'on' ) {
		delete_option( 'ccfwoo_general_section' );
	}
}
register_uninstall_hook( __FILE__, 'ccfwoo_uninstall' );

/**
 * Get the number of products in cart.
 */
function ccfwoo_get_cart_content() {

	if ( ! is_object( WC() ) ) {
		return 0;
	}

	if ( ! is_object( WC()->cart ) ) {
		return 0;
	}

	if ( empty( WC()->cart->get_cart_contents_count() ) ) {
		$content_count = 0;
	} else {
		$content_count = WC()->cart->get_cart_contents_count();
	}

	return $content_count;
}

/**
 * Upgrade sidebar on settings page.
 */
function ccfwoo_upgrade_to_pro_sidebar() {

	echo '<div class="banana-metabox">
			<h3>Optimize your sales with Pro</h3>
			  <p>
				<a href="https://puri.io/plugin/checkout-countdown-woocommerce/?utm_source=active_plugin&utm_medium=settings_sidebar&utm_campaign=checkout_countdown" target="_blank">Checkout Countdown Pro</a>
				comes with features that allow you to take sale optimization to the next level.
				</p>
				<ul style="list-style: initial; margin-left:18px;">
				<li>Reset count when a product is added to cart.</li>
		  		<li>Clear cart after countdown.</li>
          		<li>Redirect after countdown has finished.</li>
         		<li>Restart/loop the countdown.</li>
        		<li>Recalucate the cart totals with every loop.</li>
				<li>Start the timer without reloading the page when adding a product to cart (AJAX).</li>
		 		<li>and much more!</li>
				  </ul>
				You\'ll also directly support ongoing development. <br><br>
				<a class="button button-primary" href="https://puri.io/plugin/checkout-countdown-woocommerce/?utm_source=active_plugin&utm_medium=settings_sidebar&utm_campaign=checkout_countdown" target="_blank">Upgrade to Pro</a>
				<p>
				</p>
			  </div>';
}

 add_filter( 'ccfwoo_above_settings_sidebars', 'ccfwoo_upgrade_to_pro_sidebar', 10 );


 /**
  * Small upgrade notice at the bottom of general settings.
  */
function ccfwoo_upgrade_to_pro_settings_bottom() {
	echo '<div style="display:inline-block;width:100%; float:right; background-color:#ffffff;">';
	echo '<div style="padding:20px;">';
	echo '<a href="https://puri.io/plugin/checkout-countdown-woocommerce/?utm_source=active_plugin&utm_medium=settings_bottom&utm_campaign=checkout_countdown" target="_blank"><span style="font-weight:600;">Pro includes</span> <em>Restart count when adding to cart, Clear Cart, AJAX and more.</em></a>';
	echo '</div>';
	echo '</div>';
}
add_action( 'ccfwoo_form_bottom_ccfwoo_general_section', 'ccfwoo_upgrade_to_pro_settings_bottom', 10 );

/**
 * Creates a static preview of the countdown banner for the settings page.
 *
 * @return [type] [description]
 */
function ccfwoo_settings_preview() {

	$ccfwoo_bg   = ccfwoo_get_option( 'top_banner_background_color' ) ? ccfwoo_get_option( 'top_banner_background_color' ) : '#000000';
	$ccfwoo_font = ccfwoo_get_option( 'top_banner_font_color' ) ? ccfwoo_get_option( 'top_banner_font_color' ) : '#ffffff';

	$text = ccfwoo_get_option( 'countdown_text' ) ? ccfwoo_get_option( 'countdown_text' ) : 'We can only hold your item for {minutes} minutes and {seconds} seconds!';

	$text = str_replace( '{minutes}', '28', $text );
	$text = str_replace( '{seconds}', '42', $text );

	echo "<style>
              .cc-demo {
              color: $ccfwoo_font;
              max-width: 800px;
              text-align: center;
              background: $ccfwoo_bg;
              padding: 15px;
              margin: 0 auto;
              }
              .cc-woo-notice {
              max-width:600px;
              padding: 1em 1.618em;
              margin-bottom: 2.617924em;
              background-color: #e2401c;
              margin-left: 0;
              border-radius: 2px;
              color: #ffffff;
              clear: both;
              border-left: .6180469716em solid rgba(0,0,0,.15);
              }
          </style>";

	echo '<h4>Preview</h4>';
	echo '<div style="margin-bottom:20px;display:inline-block;width:100%; float:right; background-color:#ffffff;">';
	echo '<div style="padding:20px;">';

	echo "<div class='cc-demo'>$text</div>";

	echo '</div>';
	echo '</div>';
}
add_action( 'ccfwoo_form_top_ccfwoo_general_section', 'ccfwoo_settings_preview' );


/**
 * Convert plugin options to use Banana Framework instead.
 */
function ccfwoo_convert_options_for_banana() {

	$new_options  = get_option( 'ccfwoo_general_section', 'none' );
	$check_enable = get_option( 'ccfwoo_enable_countdown', 'none' );

	// No old settings to convert - return.
	if ( $check_enable === 'none' ) {
		return;
	}

	 // New settings already exsits - return
	if ( is_array( $new_options ) ) {

		// Handle Backwards compatability for 3.0.
		$new_location = array();

		if ( array_key_exists( 'display_type', $new_options ) ) {

			if ( $new_options['display_type'] === 'site-banner' ) {
				$new_location['bar'] = 'bar';
			}
			if ( $new_options['display_type'] === 'woo-notice' ) {
				$new_location['cart-notice']     = 'cart-notice';
				$new_location['checkout-notice'] = 'checkout-notice';
			}

			// Delete our old display_type.
			unset( $new_options['display_type'] );

			// Update to new countdown_locations.
			$new_options['countdown_locations'] = $new_location;

			update_option( 'ccfwoo_general_section', $new_options, false );

		}

		return;
	}

	$temp = array();

	$enable = ! empty( get_option( 'ccfwoo_enable_countdown' ) ) ? get_option( 'ccfwoo_enable_countdown' ) : null;

	$temp['enable'] = $enable;

	/*$countdown_time = ! empty( get_option( 'ccfwoo_minutes' ) ) ? get_option( 'ccfwoo_minutes' ) : null;

	$temp['countdown_time'] = $countdown_time;*/
	
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
		$temp['countdown_time'] = 5;
	}else{
		$countdown_time = ! empty( get_option( 'ccfwoo_minutes' ) ) ? get_option( 'ccfwoo_minutes' ) : null;
		$temp['countdown_time'] = $countdown_time;
	}
	//////////////////////////////////////////

	$display_type = ! empty( get_option( 'ccfwoo_countdown_style' ) ) ? get_option( 'ccfwoo_countdown_style' ) : null;

	$temp['display_type'] = $display_type;

	$before_text    = ! empty( get_option( 'ccfwoo_before_countdown' ) ) ? get_option( 'ccfwoo_before_countdown' ) : '';
	$inbetween_text = ! empty( get_option( 'ccfwoo_inbetween_countdown' ) ) ? get_option( 'ccfwoo_inbetween_countdown' ) : '';
	$after_text     = ! empty( get_option( 'ccfwoo_after_countdown' ) ) ? get_option( 'ccfwoo_after_countdown' ) : '';

	if ( ! empty( $before_text ) || ! empty( $before_text ) || ! empty( $before_text ) ) {
		$countdown_text = "$before_text {minutes} $inbetween_text {seconds} $after_text";
	} else {
		$countdown_text = false;
	}

	$temp['countdown_text'] = $countdown_text;

	$expired_text = ! empty( get_option( 'ccfwoo_expired_text' ) ) ? get_option( 'ccfwoo_expired_text' ) : false;

	$temp['expired_text'] = $expired_text;

	$top_banner_background_color = ! empty( get_option( 'ccfwoo_style_bg_color' ) ) ? get_option( 'ccfwoo_style_bg_color' ) : false;

	$temp['top_banner_background_color'] = $top_banner_background_color;

	$top_banner_font_color = ! empty( get_option( 'ccfwoo_style_font_color' ) ) ? get_option( 'ccfwoo_style_font_color' ) : false;

	$temp['top_banner_font_color'] = $top_banner_font_color;

	$enable_banner_message = ! empty( get_option( 'ccfwoo_enable_banner_message' ) ) ? get_option( 'ccfwoo_enable_banner_message' ) : false;

	$temp['enable_banner_message'] = $enable_banner_message;

	$banner_message_text = ! empty( get_option( 'ccfwoo_banner_message' ) ) ? get_option( 'ccfwoo_banner_message' ) : false;

	$temp['banner_message_text'] = $banner_message_text;

	$new_array = array();

	foreach ( $temp as $key => $value ) {

		if ( empty( $value ) ) {
			// Skip if empty or false.
			continue;
		}

		if ( is_serialized( $value ) ) {
			// unserialize
			$value = unserialize( $value );
		}

		if ( is_array( $value ) ) {
			$value = $value[0];
		}

		if ( $value === 'yes' ) {
			$value = 'on';
		}

		// Set it back to the orginal key
		$new_array[ $key ] = $value;
	}

	update_option( 'ccfwoo_general_section', $new_array, false );

}
add_action( 'wp_loaded', 'ccfwoo_convert_options_for_banana', 50 );
