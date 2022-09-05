<?php
if ( ! defined( 'WPINC' ) ) {
    die();
}

?>
<label id="advads-adsense-matched-content" class="label" style="<?php if ( ! $is_supported ) { echo 'display: none;'; } ?>"><?php _e( 'Layout', 'advanced-ads-responsive' ); ?></label>
<div id="advads-adsense-matched-content-controls" style="overflow:hidden; <?php if ( ! $is_supported ) { echo 'display: none;'; } ?>">
<p>
<label><input id="matched-content-customize-switcher" type="checkbox" onchange="advads_toggle_box( this, '#matched-content-customize' );" <?php 
checked( $settings['customize_enabled'], 1 ); ?> value="1"><?php _e( 'Customize', 'advanced-ads-responsive' ); ?></label>
    <a href="https://support.google.com/adsense/answer/7533385" target="_blank"><?php _e( 'manual', 'advanced-ads-responsive' ); ?></a>
</p>
<table id="matched-content-customize" <?php if ( ! $settings['customize_enabled'] ) { echo 'style="display:none;"'; } ?>>
<tr>
	<td><?php _e( 'on desktop', 'advanced-ads-responsive' ); ?></td><td><?php _e( 'Rows', 'advanced-ads-responsive' ); ?></td><td><?php _e( 'Columns', 'advanced-ads-responsive' ); ?></td>
</tr>
<tr>
<td>
	<select id="matched-content-ui-type">
	<?php foreach( $types as $_type ): ?>
	<option value="<?php echo $_type ?>" <?php selected( $_type, $settings['ui_type'] ); ?>><?php echo $_type ?></option>
	<?php endforeach; ?>
	</select>
</td>
<td>
	<input type="number" min="1" max="99999" id="matched-content-rows-num" value="<?php echo $settings['rows_num']; ?>" />
</td>
<td>
	<input type="number" min="1" max="99999" id="matched-content-columns-num" value="<?php echo $settings['columns_num']; ?>" />
</td>
</tr>
<tr><td colspan="3"><?php _e( 'on mobile', 'advanced-ads-responsive' ); ?></td>
<tr>
<td>
	<select id="matched-content-ui-type-m">
	<?php foreach( $types as $_type ): ?>
	<option value="<?php echo $_type ?>" <?php selected( $_type, $settings['ui_type_m'] ); ?>><?php echo $_type ?></option>
	<?php endforeach; ?>
	</select>
</td>
<td>
	<input type="number" min="1" max="99999" id="matched-content-rows-num-m" value="<?php echo $settings['rows_num_m']; ?>" />
</td>
<td>
	<input type="number" min="1" max="99999" id="matched-content-columns-num-m" value="<?php echo $settings['columns_num_m']; ?>" />
</td>
</tr>
</table>
</div>
<hr />
