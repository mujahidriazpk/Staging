<div id="advads-cache-busting-check-wrap">
    <span id="advads-cache-busting-error-result" class="advads-notice-inline advads-error" style="display:none;">
        <?php printf(__( 'The code of this ad might not work properly with activated cache-busting. <a href="%s" target="_blank">Manual</a>', 'advanced-ads-pro' ), ADVADS_URL . 'manual/cache-busting/#advads-passive-compatibility-warning' ); ?>
    </span>
	<input type="hidden" id="advads-cache-busting-possibility" name="advanced_ad[cache-busting][possible]" value="true" />
</div>

<?php $type = ( isset( $types[$ad->type] ) ) ? $types[$ad->type] : current( $types ); ?>

<?php if ( $ad->type !== 'adsense' ): ?>
<script>
jQuery( document ).ready(function() {
	if ( typeof advads_cb_check_ad_markup !== 'undefined' ){
	    var ad_content = <?php echo json_encode( $type->prepare_output( $ad ) ); ?>;
	    advads_cb_check_ad_markup( ad_content );
	}
});
</script>
<?php endif; ?>
