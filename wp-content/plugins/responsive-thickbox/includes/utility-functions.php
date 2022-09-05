<?php
/**
 * Utility Functions
 *
 * @package     responsive-thickbox
 * @subpackage  Includes
 * @copyright   Copyright (c) 2016, Lyquidity Solutions Limited
 * @License:	Lyquidity Commercial
 * @since       1.0
 */

namespace lyquidity\responsive_thickbox;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/*
	endsWith
	errorHandler
	getBaseDomain
	getHomeUrlBaseDomain
 */

/** 
 * Presents settings within a table that shows a column for the settings and one for an adverts page
 * @param string $product The slug of the current product
 * @param function $callback A callback function that will render the settings.
 * @return void
 */ 
function advert( $product, $version, $callback )
{
	ob_start();
	$gif = admin_url() . "images/xit.gif";
?>
	<table style="height: 100%; width: 100%;"> <!-- This style is needed so cells will respect the height directive in FireFox -->
		<tr>
			<td>
				<?php echo $callback(); ?>
			</td>
			<td style="width: 242px; vertical-align:top; height: 100%; position: relative;">
				<style>
					#product-list-close button:hover {
						background-position: -10px !important;
					}
				</style>
				<div id="product-list-close" style="position: absolute; top: 26px; right: 22px;" >
					<button style="float: right; background: url(<?php echo $gif; ?>) no-repeat; border: none; cursor: pointer; display: inline-block; padding: 0; overflow: hidden; margin: 8px 0 0 0; text-indent: -9999px; width: 10px; height: 10px" >
						<span class="screen-reader-text">Remove Product List</span>
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div id="product-list-wrap" style="width: 100%; height: 100%; display: inline-block; background-color: white; border: 1px solid #ccc;" ></div>
				<script>
					function receiveMessage()
					{
						if (event.origin !== "https://www.wproute.com")
							return;
						
						// The data should be the outerheight of the iframe
						var height = event.data + 0;
						
						var iframe = jQuery('#product-list-frame');
						// Set a minimum height on the outer table but only if its not already there
						var table = iframe.parents('table');
						if ( table.height() > height + 30 ) return;
						table.css( 'min-height', ( height + 30 ) + 'px' );
					}
					window.addEventListener("message", receiveMessage, false);

					function iframeLoad(e) 
					{
						var iframe = jQuery('#product-list-frame');
						iframe[0].contentWindow.postMessage( "height", "https://www.wproute.com/" );

						// This is only for IE
						if ( window.navigator.userAgent.indexOf("MSIE ") == -1 && window.navigator.userAgent.indexOf("Trident/") == -1 ) return;
						// May need to do this for Opera as well
						iframe.closest('div').height( iframe.closest('td').height() - 2 );
						jQuery(window).on( 'resize', function(e) 
						{
							// Resize down to begin with.  This is because a maximize after a minimize results in the maximized div having the height of the minimized div
							iframe.closest('div').height( 10 ); 
							iframe.closest('div').height( iframe.closest('td').height() - 2 ); 
						} )
					}
					jQuery(document).ready(function ($) {
						var target = $('#product-list-wrap');
						target.html( '<iframe onload="iframeLoad();" id="product-list-frame" src="https://www.wproute.com/?action=product_list&product=<?php echo $product; ?>&version=<?php echo $version; ?>" height="100%" width="100%">' );
						$( '#product-list-close button' ).on( 'click', function(e) {
							e.preventDefault();
							$( '#product-list-close').closest('td').hide();
						} );
					} );
				</script>
			</td>
		</tr>
	</table>
<?php

	echo ob_get_clean();
}

/**
 * Find out if $haystack ends with $needle
 * @param string $haystack
 * @param string $needle
 * @return boolean
 */
function endsWith( $haystack, $needle )
{
	$strlen = strlen( $haystack );
	$testlen = strlen( $needle );
	if ( $testlen > $strlen ) return false;
	return substr_compare( $haystack, $needle, $strlen - $testlen, $testlen ) === 0;
}

/**
 * PHP error handler instance used to suppress errors when checking and validating regular expression queries.
 */
function errorHandler($errno, $errstr, $errfile, $errline)
{
	global $responsive_thickbox_error;
	$responsive_thickbox_error = true;

	/* Don't execute PHP internal error handler */
	return true;
}

/**
 * Return the base domain of a given domain.  If www.xxx.com is given it will return xxx.com.
 * @param string $domain The domain for which to find the base domain
 */
function getBaseDomain( $domain )
{
	$parts = explode( ".", $domain );
	$baseDomain = array_pop( $parts );
	$parts = array_reverse( $parts );

	// Test the components of the domain until the first one reports a valid domain.
	foreach ( $parts as $part )
	{
		if ( ! empty( $baseDomain ) ) $baseDomain = "." . $baseDomain;
		$baseDomain = "$part" . $baseDomain;
		$result = dns_get_record( $baseDomain, DNS_A );
		if ( $result ) break;
	}

	return $baseDomain;
}

/**
 * Return the base domain of the WP home_url()
 */
function getHomeUrlBaseDomain()
{
	return getBaseDomain( parse_url( home_url(), PHP_URL_HOST ) );
}

