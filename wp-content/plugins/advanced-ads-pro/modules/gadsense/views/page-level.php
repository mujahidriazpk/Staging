<?php
/**
 * Output auto ads enabled code in head.
 *
 * This template is a drop-in replacement for the template of the base plugin.
 *
 * @var array $parameters {
 *    Parameters of the AdSense code.
 *
 *    @type bool   $privacy_enabled Whether to wait for user consent.
 *    @type bool   $npa_enabled     Whether to show non-personalized ads.
 *    @type string $client_id       The Google AdSense client ID.
 *    @type bool   $top_anchor      AdSense anchor ad on top of pages.
 *    @type string $top_anchor_code The code for top anchor ads.
 *    @type string $script_src      AdSense script url.
 * }
 *
 */
?><script>
(function () {
	var scriptDone = false;
	document.addEventListener( 'advanced_ads_privacy', function ( event ) {
		if (
			( event.detail.state !== 'accepted' && event.detail.state !== 'not_needed' && ! advads.privacy.is_adsense_npa_enabled() )
			|| scriptDone
			|| advads.get_cookie( 'advads_pro_cfp_ban' )
		) {
			return;
		}

		// Google adsense script can only be added once.
		scriptDone = true;

		var script = document.createElement( 'script' ),
			first = document.getElementsByTagName( 'script' )[0];

		script.async = true;
		script.src = '<?php echo esc_url( $parameters['script_src'] ); ?>';
		<?php
		if ( $parameters['top_anchor'] ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- relevant user input has already been escaped.
			echo $parameters['top_anchor_code'];
		} else {
			printf( 'script.dataset.adClient = "%s";', esc_attr( $parameters['client_id'] ) );
		}
		?>

		first.parentNode.insertBefore( script, first );
	} );
} )();
</script>
