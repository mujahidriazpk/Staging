<?php
if ( ! $enabled ): ?>
<input disabled="disabled" type="checkbox" <?php checked( $checked, true ); ?> />
<input type="hidden" name="<?php echo ADVADS_SLUG . '[' . AAR_SLUG . '][reload-ads-on-resize]' ?>" value="<?php echo $checked; ?>" />
<?php else: ?>
<input type="checkbox" name="<?php echo ADVADS_SLUG . '[' . AAR_SLUG . '][reload-ads-on-resize]' ?>" value="1" <?php checked( $checked, true ); ?> />
<?php endif; ?>

<p class="description"><?php _e( 'Reload ads that are loaded via cache-busting when screen resizes.', 'advanced-ads-responsive' );
if ( ! $enabled ) {
	echo ' ' . sprintf( __( 'You need <a href="%s" target="_blank">Advanced Ads Pro</a> and cache-busting in order to use this feature.', 'advanced-ads-responsive' ), ADVADS_URL . 'add-ons/advanced-ads-pro' );
}
?>
