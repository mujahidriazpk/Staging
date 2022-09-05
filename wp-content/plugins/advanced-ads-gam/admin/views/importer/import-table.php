<?php
/**
 * Importable ads table.
 *
 * @var array $importable_ads list of external ad units that can be imported.
 */

?>
<form id="gam-import-form">
	<table class="widefat stripped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Name', 'advanced-ads-gam' ); ?></th>
				<th><?php esc_html_e( 'Description', 'advanced-ads-gam' ); ?></th>
				<th><input type="checkbox" value="1" id="gam-import-check-all" checked /></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$alt = '';
			foreach ( $importable_ads as $ad ) :
				$alt = ' alternate' == $alt ? '' : ' alternate';
				?>
			<tr class="checked<?php echo esc_attr( $alt ); ?>">
				<td><?php echo esc_html( $ad['name'] ); ?></td>
				<td><?php echo esc_html( $ad['description'] ); ?></td>
				<td><input type="checkbox" name="gam-ad-id[]" value="<?php echo esc_attr( $ad['id'] ); ?>" checked /></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</form>
