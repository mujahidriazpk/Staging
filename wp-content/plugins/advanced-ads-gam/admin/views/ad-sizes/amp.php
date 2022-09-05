<?php
/**
 * Ad sizes on AMP.
 */

?>
<script type="text/html" id="tmpl-gam-amp-ad-sizes">
	<tfoot>
	<tr>
		<th data-th="">
			<?php esc_html_e( 'AMP pages', 'advanced-ads-gam' ); ?>
			<input type="hidden" name="advanced_ad[output][amp-has-sizes]" value="1"/>
		</th>
		<# _.each( data.sizes, function( size ) { #>
		<th data-th="{{size}}">
			<input type="checkbox" name="advanced_ad[output][amp-ad-sizes][]"
			<# if ('undefined' != typeof data.checked[size]) { #>
			checked="checked"
			<# } #>
			value="{{size}}" />
			<?php if ( Advanced_Ads_Checks::active_amp_plugin() ) : ?>
				<# if ('fluid'== size) { #> <strong>*</strong> <# } #>
			<?php endif; ?>
		</th>
		<# }) #>
		<th></th>
	</tr>
	</tfoot>
</script>
