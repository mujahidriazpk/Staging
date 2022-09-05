<script>
/**
 * Init Custom code UI.
 */
jQuery( document ).ready( function() {
	var $textarea = jQuery( '#advads-custom-code-textarea' );

	function init() {
		var settings = <?php echo wp_json_encode( $settings ); ?>;
		if ( settings ) {
			wp.codeEditor.initialize( $textarea , settings );
		}
	}

	if ( ! $textarea.is( ':hidden' ) ) {
		init();
	} else {
		jQuery( '#advanced-ads-toggle-custom-code-editor' ).on( 'click', function() {
			jQuery( this ).hide();
			jQuery( $textarea ).slideToggle( 400, init );
		});
	}

} );
</script>
<hr class="advads-hide-in-wizard"/>
<label class='label advads-hide-in-wizard' for="advads-custom-code-textarea"><?php _e( 'custom code', 'advanced-ads-pro' ); ?></label>
<div id="advads-custom-code-wrap" class="advads-hide-in-wizard">
<?php if ( ! $custom_code ) : ?>
<a id="advanced-ads-toggle-custom-code-editor" href="javascript:;"><?php _e( 'place your own code below the ad', 'advanced-ads-pro' ); ?></a>
<?php endif; ?>
<textarea <?php if ( ! $custom_code ) { echo 'style="display:none;"';} ?> id="advads-custom-code-textarea" name="advanced_ad[output][custom-code]"><?php
echo $custom_code; ?></textarea>
<p <?php if ( ! $custom_code ) { echo 'style="display:none;"';} ?> class="description"><?php _e( 'Displayed after the ad content', 'advanced-ads-pro' ); ?></p>
</div>
