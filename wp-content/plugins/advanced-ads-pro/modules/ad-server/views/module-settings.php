<?php
/**
 * Render options for the Ad Server module
 *
 * @var string $embedding_url URL where the ad should be loaded.
 * @var boolean $block_no_referrer Value of the block-no-referrer option.
 */
?>
<input name="<?php echo esc_attr( Advanced_Ads_Pro::OPTION_KEY ); ?>[ad-server][enabled]"
	   class="advads-has-sub-settings"
	   id="advanced-ads-pro-ad-server-enabled" type="checkbox" value="1" <?php checked( $module_enabled ); ?> />
<label for="advanced-ads-pro-ad-server-enabled"
	   class="description"><?php esc_html_e( 'Activate module.', 'advanced-ads-pro' );
	    ?> – <a href="<?php echo esc_url( ADVADS_URL ) . 'ad-server-wordpress/#utm_source=advanced-ads&utm_medium=link&utm_campaign=pro-ad-server-manual'; ?>" target="_blank"><?php esc_html_e( 'Manual', 'advanced-ads-pro' ); ?></a>
	   </label>

<div class="advads-sub-settings">
	<p class="description"><?php esc_html_e( 'Top level domains on which the ads will be loaded.', 'advanced-ads-pro' ); ?> <?php esc_html_e( 'Separate multiple values with a comma.', 'advanced-ads-pro' ); ?></p>
	<label>
		<input style="width: 90%" id="advanced-ads-pro-server-domains"
			   name="<?php echo esc_attr( Advanced_Ads_Pro::OPTION_KEY ); ?>[ad-server][embedding-url]" type="text"
			   value="<?php echo esc_html( $embedding_url ); ?>"/>
		<p id="advanced-ads-pro-server-domains-error"
		   class="advads-notice-inline advads-error hidden"><?php esc_html_e( 'Please don’t enter subdirectories.', 'advanced-ads-pro' ); ?></p>
	</label>
	<br/><br/>
	<label>
		<input name="<?php echo esc_attr( Advanced_Ads_Pro::OPTION_KEY ); ?>[ad-server][block-no-referrer]"
			   type="checkbox" value="1" <?php checked( $block_no_referrer ); ?> />
		<?php esc_html_e( 'Prevent direct access to the placement URL.', 'advanced-ads-pro' ); ?>
	</label>
</div>
<script>
	// check if input is valid URLs without subdirectories
	jQuery(document).ready(function () {
		jQuery( '#advanced-ads-pro-server-domains' ).on( 'change', function () {
// Sudirectories are not allowed so let’s just check for the / character
			advanced_ads_pro_server_check_target_urls( jQuery(this).val() );
		});
	});
	// run the check once on load.
    advanced_ads_pro_server_check_target_urls( jQuery( '#advanced-ads-pro-server-domains' ).val() );
	/**
	 * check if the URLs of the target sites are valid
	 * if not, show a warning
	 *
	 * @param string value of the target URL.
	 */
	function advanced_ads_pro_server_check_target_urls( value ) {
	    // is there a "/" with a preceding and following alphanumeric value then this might be a subdirectory
		if ( /[a-z0-9]\/[a-z0-9]/.test( value ) ) {
			jQuery('#advanced-ads-pro-server-domains-error').show();
		} else {
			jQuery('#advanced-ads-pro-server-domains-error').hide();
		}
	}
</script>
