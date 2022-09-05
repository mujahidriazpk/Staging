<label class="label"><?php _e( 'Attributes', 'advanced-ads-responsive' ); ?></label>
<div style="float:none; overflow:auto;">
	<table id="advads-amp-props" class="widefat">
		<thead>
			<tr>
				<th><?php _e( 'Name', 'advanced-ads-responsive' ) ?></th>
				<th><?php _e( 'Value', 'advanced-ads-responsive' ) ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach ( $attributes as $_attrubute => $_data ): ?>
			<tr class="advads-amp-prop-row">
				<td><input class="large-text" type="text" name="advanced_ad[amp][attributes][]" value="<?php echo esc_attr( $_attrubute ); ?>" /></td>
				<td><textarea class="large-text" name="advanced_ad[amp][data][]"><?php echo esc_textarea( $_data ); ?></textarea></td>
				<td><button type="button" class="advads-amp-delete-prop button">x</button></td>
			</tr>
			<?php
		endforeach;
		?>
			<tr>
				<td colspan="3">
				<button type="button" class="button button-primary" id="advads-amp-add-prop">
					<i class="dashicons dashicons-plus-alt" style="vertical-align: middle;"></i>&nbsp;<?php _e( 'Add attribute', 'advanced-ads-responsive' ); ?>
				</button>
			</tr>
		</tbody>
	</table>
</div>
<hr />

<label class="label"><?php _e( 'Fallback', 'advanced-ads-responsive' ); ?></label>
<div style="float:none; overflow:auto;">
	<textarea class="large-text" name="advanced_ad[amp][fallback]"><?php echo esc_textarea( $fallback); ?></textarea>
	<p class="description"><?php _e( ' If supported by the ad network, this text is shown if no ad is available for the ad slot', 'advanced-ads-responsive' ); ?></p>
</div>
<hr />