<script type="text/html" id="tmpl-gam-ad-sizes-table-row">
<# _.each( data.rows, function( row, width ) { #>
<tr>
	<td class="advads-ad-parameters-option-list-min-width" data-th="<?php esc_html_e( 'min. screen width', 'advanced-ads-gam' ); ?>">
		<input type="number" min="0" max="3000" name="advanced_ad[output][ad-sizes][{{width}}][width]" value="{{width}}"/>px</td>
	<# _.each( row, function( checked, size ) { #>
	<td data-th="{{size}}"><input type="checkbox" name="advanced_ad[output][ad-sizes][{{width}}][sizes][]" value="{{size}}" <# if( checked ) { #> checked="checked"<# } #>/></td>
	<# }) #>
	<td class="advads-option-buttons">
		<span class="dashicons dashicons-plus advads-row-new" title="<?php esc_html_e( 'add', 'advanced-ads-gam' ); ?>"></span>
		<span class="dashicons dashicons-trash advads-tr-remove" title="<?php esc_html_e( 'delete', 'advanced-ads-gam' ); ?>"></span>
		<span class="advads-loader hidden"></span>
	</td>
</tr>
<# }) #>
</script>
