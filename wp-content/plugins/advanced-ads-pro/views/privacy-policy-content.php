<?php
/**
 * Suggested content for the privacy policy visible under Settings > Privacy > Policy Guide
 */
?>
	<p class="privacy-policy-tutorial">
		<?php esc_html_e( 'Depending on the setup, Advanced Ads Pro and other add-ons use cookies to control which user sees which ad. They also help to reduce expensive server requests.', 'advanced-ads-pro' ); ?>
	</p>
	<p class="privacy-policy-tutorial">
		<?php esc_html_e( 'You can use the text below as a template for your own privacy policy.', 'advanced-ads-pro' ); ?>
	</p>
	<strong class="privacy-policy-tutorial"><?php esc_html_e( 'Suggested Text:', 'advanced-ads-pro' ); ?></strong>
<?php esc_html_e( 'This website uses Advanced Ads Pro to place advertisements. The WordPress plugin may use multiple first-party cookies to ensure the correct integration of ads. These cookies store technical information but not IP addresses. Their use is linked to specific features and options when embedding ads.', 'advanced-ads-pro' ); ?>
	<br>
<?php
printf(
	wp_kses_post(
	/* translators: %1$s is an opening a tag, %2$s is the corresponding closing one */
		__( 'Please, see the %1$sAdvanced Ads cookie information%2$s for more details.', 'advanced-ads-pro' )
	),
	'<a href="https://wpadvancedads.com/cookie-information/" target="_blank" rel="noopener">',
	'</a>'
);
