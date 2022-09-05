<?php
/**
 * View for GamiPress ranks and achievements visitor condition.
 *
 * @var string $type           The visitor condition type.
 * @var string $name           Unique input name.
 * @var string $operator       The comparison operator.
 * @var array  $condition      Details for the condition.
 * @var array  $values         GamiPress achievements or ranks.
 * @var array  $selected_value The currently selected value.
 */
?>
<input type="hidden" name="<?php echo esc_attr( $name ); ?>[type]" value="<?php echo esc_attr( $type ); ?>"/>
<div class="advads-conditions-single">
	<select name="<?php echo esc_attr( $name ); ?>[operator]">
		<option value="is_equal" <?php selected( 'is_equal', $operator ); ?>>
			<?php esc_html_e( 'is equal', 'advanced-ads' ); ?>
		</option>
		<option value="is_higher" <?php selected( 'is_higher', $operator ); ?>>
			<?php esc_html_e( 'is higher or equal', 'advanced-ads' ); ?>
		</option>
		<option value="is_lower" <?php selected( 'is_lower', $operator ); ?>>
			<?php esc_html_e( 'is lower or equal', 'advanced-ads' ); ?>
		</option>
	</select>
	<div class="advads-conditions-select-wrap">
		<select name="<?php echo esc_attr( $name ); ?>[value]">
			<option value="" disabled <?php selected( 0, $selected_value ); ?>><?php esc_attr_e( 'Choose rank', 'advanced-ads-pro' ); ?></option>
			<?php foreach ( $values as $label => $optgroup ) : ?>
				<optgroup label="<?php echo esc_attr( $label ); ?> ">
					<?php foreach ( $optgroup as $value => $option ) : ?>
						<option value="<?php echo (int) $value; ?>" <?php selected( $value, $selected_value ); ?>><?php echo esc_attr( $option ); ?></option>
					<?php endforeach; ?>
				</optgroup>
			<?php endforeach; ?>
		</select>
	</div>
	<p class="description"><?php echo esc_html( $condition['description'] ); ?></p>
</div>
