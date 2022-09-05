
<td class="advads-placements-table-test-column">
<?php if ( $show ) : ?>
	<?php if ( ! empty( $_placement['options']['test_id'] ) ) : ?>
		<input type="hidden" name="advads[placements][<?php echo $_placement_slug; ?>][options][test_id]" value="<?php echo $_placement['options']['test_id']; ?>" />
		<?php _ex( 'Testing', 'placement tests', 'advanced-ads-pro'  );
	else: ?>
		<label><?php _e( 'Test weight', 'advanced-ads-pro' ); ?>
		<select class="advads-add-to-placement-test" data-slug="<?php echo $_placement_slug; ?>">
			<option value=""></option>
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			<option value="6">6</option>
			<option value="7">7</option>
			<option value="8">8</option>
			<option value="9">9</option>
			<option value="10">10</option>
		</select>
		</label>
	<?php endif; ?>
<?php endif; ?>
</td>
