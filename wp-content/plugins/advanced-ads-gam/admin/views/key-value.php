<?php
/**
 * Key/value pairs targeting.
 */

$kvs = Advanced_Ads_Gam_Admin::get_instance()->get_key_values_types();
global $post;
$ad_options = false;
if ( $post ) {
	$the_ad     = new Advanced_Ads_Ad( $post->ID );
	$ad_options = $the_ad->options();
}

$used_types = array();

$sorted_keyval = array();

$keyval_by_types = array();

$keyval_by_name = array();

if ( ! isset( $ad_options['gam-keyval'] ) || ! is_array( $ad_options['gam-keyval'] ) ) {
	$ad_options['gam-keyval'] = array();
}
foreach ( $ad_options['gam-keyval'] as $entry ) {
	if ( ! isset( $keyval_by_types[ $entry['type'] ] ) ) {
		$keyval_by_types[ $entry['type'] ]                = array();
		$keyval_by_name[ $kvs[ $entry['type'] ]['name'] ] = $entry['type'];
	}
	$keyval_by_types[ $entry['type'] ][] = $entry;
}
ksort( $keyval_by_name );

$alpha_sort = function( $a, $b ) {
	if ( 0 > strcasecmp( $a['key'], $b['key'] ) ) {
		return -1;
	} elseif ( 0 < strcasecmp( $a['key'], $b['key'] ) ) {
		return 1;
	} else {
		return 0;
	}
};
foreach ( $keyval_by_name as $name => $type ) {
	usort( $keyval_by_types[ $type ], $alpha_sort );
	foreach ( $keyval_by_types[ $type ] as $val ) {
		$sorted_keyval[] = $val;
	}
}
?>

<hr />
<label class="label"><?php esc_html_e( 'Key-value targeting', 'advanced-ads-gam' ); ?></label>
<div id="advads-gam-keyvalue-div">
	<table id="advads-gam-keyvalue-table" class="widefat striped advads-option-table advads-option-table-responsive">
		<thead>
			<th><?php esc_html_e( 'Type', 'advanced-ads-gam' ); ?></th>
			<th><?php esc_html_e( 'Key', 'advanced-ads-gam' ); ?></th>
			<th colspan="2"><?php esc_html_e( 'Value', 'advanced-ads-gam' ); ?></th>
		</thead>
		<tbody>
			<?php
			if ( ! empty( $ad_options ) && isset( $ad_options['gam-keyval'] ) ) :
				?>
				<?php
				foreach ( $sorted_keyval as $keyval ) :
					if ( ! in_array( $keyval['type'], array( 'custom', 'postmeta', 'usermeta' ) ) ) {
						$used_types[] = $keyval['type'];
					}
					?>
			<tr>
				<td data-th="<?php echo esc_attr( esc_html__( 'Type', 'advanced-ads-gam' ) ); ?>">
					<input name="advanced_ad[gam][type][]" type="hidden" value="<?php echo esc_attr( $keyval['type'] ); ?>" />
					<?php echo esc_html( $kvs[ $keyval['type'] ]['name'] ); ?>
				</td>
				<td data-th="<?php echo esc_attr( esc_html__( 'Key', 'advanced-ads-gam' ) ); ?>">
					<input name="advanced_ad[gam][key][]" type="hidden" value="<?php echo esc_attr( $keyval['key'] ); ?>" />
					<code><?php echo esc_html( $keyval['key'] ); ?></code>
				</td>
				<td data-th="<?php echo esc_attr( esc_html__( 'Value', 'advanced-ads-gam' ) ); ?>">
					<?php if ( in_array( $keyval['type'], array( 'custom', 'postmeta', 'usermeta' ) ) ) : ?>
					<input name="advanced_ad[gam][value][]" type="hidden" value="<?php echo esc_attr( $keyval['value'] ); ?>" />
					<code><?php echo esc_html( $keyval['value'] ); ?></code>
						<?php
						else :

							$output = $kvs[ $keyval['type'] ]['html'];
							if ( false !== strpos( $output, 'class="onarchives"' ) ) {
								if ( '1' == $keyval['onarchives'] ) {
									$output = '<input type="hidden" name="advanced_ad[gam][value][]" value="" /><span class="description">' .
												esc_html__( 'Any terms, including categories and tags. Enabled on single and archive pages.', 'advanced-ads-gam' ) . '</span>' .
												'<input type="hidden" name="advanced_ad[gam][onarchives][]" value="1" />';
								} else {
									$output = '<input type="hidden" name="advanced_ad[gam][value][]" value="" /><span class="description">' .
												esc_html__( 'Any terms, including categories and tags. Enabled on single pages.', 'advanced-ads-gam' ) . '</span>' .
												'<input type="hidden" name="advanced_ad[gam][onarchives][]" value="0" />';
								}
							} else {
								echo '<input type="hidden" name="advanced_ad[gam][onarchives][]" value="0" />';
							}

							echo wp_kses(
								$output,
								array(
									'p'     => array( 'class' => true ),
									'span'  => array( 'class' => true ),
									'code'  => true,
									'input' => array(
										'type'    => true,
										'value'   => true,
										'name'    => true,
										'checked' => true,
										'onclick' => true,
										'class'   => true,
									),
								)
							);
							?>
						<?php endif; ?>
				</td>
				<td><i class="dashicons dashicons-dismiss" title="<?php esc_attr_e( 'Remove', 'advanced-ads-gam' ); ?>"></i></td>
			</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
	<br />
	<table id="advads-gam-keyvalue-inputs" class="widefat">
		<tbody>
			<tr>
				<td data-th="<?php echo esc_attr( esc_html__( 'Type', 'advanced-ads-gam' ) ); ?>">
					<select id="advads-gam-kv-type">
						<?php foreach ( $kvs as $slug => $type ) : ?>
							<?php
							if ( ! in_array( $slug, array( 'custom', 'postmeta', 'usermeta' ) ) && in_array( $slug, $used_types ) ) {
								continue;
							}
							?>
						<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( 'custom', $slug ); ?>><?php echo esc_html( $type['name'] ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td id="advads-gam-kv-key-td" data-th="<?php echo esc_attr( esc_html__( 'Key', 'advanced-ads-gam' ) ); ?>">
					<input type="text" id="advads-gam-kv-key-input" />
				</td>
				<td id="advads-gam-kv-value-td" data-th="<?php echo esc_attr( esc_html__( 'Value', 'advanced-ads-gam' ) ); ?>">
					<input type="text" id="advads-gam-kv-value-input" />
				</td>
				<td><button class="button-secondary" disabled id="advads-gam-add-kvpair"><?php esc_html_e( 'Add', 'advanced-ads-gam' ); ?></button></td>
			</tr>
		</tbody>
	</table>
	
</div>

