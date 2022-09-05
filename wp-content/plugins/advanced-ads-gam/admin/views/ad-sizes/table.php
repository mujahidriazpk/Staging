<script type="text/html" id="tmpl-gam-ad-sizes-table">
	<table class="widefat striped advads-option-table advads-option-table-responsive">
		<thead>
		<tr>
			<th><?php esc_html_e( 'min. screen width', 'advanced-ads-gam' ); ?></th>
			<# _.each( data.header, function( header_title, index ) { #>
			<th>{{header_title}}</th>
			<# }) #>
			<th></th>
		</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	<?php if ( Advanced_Ads_Checks::active_amp_plugin() ) : ?>
		<# if ( data.header && -1 != data.header.indexOf('fluid') ) { #>
		<p class="description">* <?php esc_html_e( 'Ad units with the fluid size selected on AMP pages only work when placed below the fold.', 'advanced-ads-gam' ); ?></p>
		<# } #>
	<?php endif; ?>
</script>
