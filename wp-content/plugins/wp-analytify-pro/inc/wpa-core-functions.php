<?php

/**
 * Queue some JavaScript code to be output in the footer.
 *
 * @param string $code
 */
function wpa_pro_enqueue_js( $code ) {
    global $wpa_queued_js;

    if ( empty( $wpa_queued_js ) ) {
        $wpa_queued_js = '';
    }

    $wpa_queued_js .= "\n" . $code . "\n";
}

/**
 * Output any queued javascript code in the footer.
 */
function wpa_pro_print_js() {
    global $wpa_queued_js;

    if ( ! empty( $wpa_queued_js ) ) {

        echo "<!-- Analytify footer JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) {";

        // Sanitize
        $wpa_queued_js = wp_check_invalid_utf8( $wpa_queued_js );
        $wpa_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $wpa_queued_js );
        $wpa_queued_js = str_replace( "\r", '', $wpa_queued_js );

        echo $wpa_queued_js . "});\n</script>\n";

        unset( $wpa_queued_js );
    }
}

/**
 * Helper function for translation.
 */
if ( ! function_exists( 'analytify__' ) ) {
	/**
	 * Wrapper for __() gettext function.
	 * @param  string $string     Translatable text string
	 * @param  string $textdomain Text domain, default: wp-analytify
	 * @return void
	 */
	function analytify__( $string, $textdomain = 'wp-analytify' ) {
		return __( $string, $textdomain );
	}
}

if ( ! function_exists( 'analytify_e' ) ) {
	/**
	 * Wrapper for _e() gettext function.
	 * @param  string $string     Translatable text string
	 * @param  string $textdomain Text domain, default: wp-analytify
	 * @return void
	 */
	function analytify_e( $string, $textdomain = 'wp-analytify' ) {
		echo __( $string, $textdomain );
	}
}
