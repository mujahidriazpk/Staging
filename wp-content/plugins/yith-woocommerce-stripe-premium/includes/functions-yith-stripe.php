<?php
/**
 * Function file
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Stripe
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCSTRIPE' ) ) {
	exit;
} // Exit if accessed directly

if( ! function_exists( 'yith_wcstripe_return_10' ) ){
	/**
	 * Just returns 10
	 *
	 * @return int 10
	 */
	function yith_wcstripe_return_10(){
		return 10;
	}
}

if( ! function_exists( 'yith_wcstripe_error_message_call' ) ){
    /**
     *
     * @return error
     */
    function yith_wcstripe_error_message_call( $errors, $err, $message ){

        return apply_filters( 'yith_wcstripe_error_message', $errors[ $err['code'] ], $message, $err['code'], $err );

    }
}

if( ! function_exists( 'yith_wcstripe_error_message_order_note_call' ) ){
    /**
     *
     * @return error
     */
    function yith_wcstripe_error_message_order_note_call( $e, $err ){

        return apply_filters( 'yith_wcstripe_error_message_order_note', 'Stripe Error: ' . $e->getHttpStatus() . ' - ' . $e->getMessage(), $e, $err );

    }
}

if( ! function_exists( 'yith_wcstripe_locate_template' ) ){
	/**
	 * Locate template for Stripe plugin
	 *
	 * @param $filename string Template name (with or without extension)
	 * @param $section string Subdirectory where to search
	 * @return string Found template
	 */
	function yith_wcstripe_locate_template( $filename, $section = '' ){
		$ext = preg_match( '/^.*\.[^\.]+$/', $filename ) ? '' : '.php';

		$template_name      = $section . '/' . $filename . $ext;
		$template_path      = WC()->template_path() . 'yith-wcstripe/';
		$default_path       = YITH_WCSTRIPE_DIR . 'templates/';

		return wc_locate_template( $template_name, $template_path, $default_path );
	}
}

if( ! function_exists( 'yith_wcstripe_get_template' ) ){
	/**
	 * Get template for Stripe plugin
	 *
	 * @param $filename string Template name (with or without extension)
	 * @param $args mixed Array of params to use in the template
	 * @param $section string Subdirectory where to search
	 */
	function yith_wcstripe_get_template( $filename, $args = array(), $section = '' ){
		$ext = preg_match( '/^.*\.[^\.]+$/', $filename ) ? '' : '.php';

		$template_name      = $section . '/' . $filename . $ext;
		$template_path      = WC()->template_path() . 'yith-wcstripe/';
		$default_path       = YITH_WCSTRIPE_DIR . 'templates/';

		wc_get_template( $template_name, $args, $template_path, $default_path );
	}
}
