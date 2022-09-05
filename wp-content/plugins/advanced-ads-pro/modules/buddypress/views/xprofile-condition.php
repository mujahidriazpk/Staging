<?php
/**
 * Render xprofile visitor condition settions.
 *
 * @var string $name base name of the setting.
 * @var array $options condition options.
 * @var array $groups BuddyPress field groups.
 * @var string $field field option.
 * @var string $value value option.
 * @var array $type_options options for the condition type.
 */
?><input type="hidden" name="<?php echo $name; ?>[type]" value="<?php echo esc_attr( $options['type'] ); ?>"/>

<?php
if ( $groups ) :
	?>
	<div class="advads-conditions-select-wrap"><select name=<?php echo esc_attr( $name ); ?>[field]">
			<?php
			foreach ( $groups as $_group ) :
				?>
				<optgroup label="<?php echo esc_html( $_group->name ); ?>">
					<?php
					if ( $_group->fields ) {
						foreach ( $_group->fields as $_field ) :
							?>
							<option value="<?php echo esc_attr( $_field->id ); ?>" <?php selected( $field, $_field->id ); ?>><?php echo esc_html( $_field->name ); ?></option>
						<?php
						endforeach;
					};
					?>
				</optgroup>
			<?php
			endforeach;
			?>
		</select></div>
<?php
else :
	?>
	<p class="advads-notice-inline advads-error">
		<?php
		/* translators: "profile fields" relates to BuddyPress profile fields */
		esc_html_e( 'No profile fields found', 'advanced-ads-pro' );
		?>
	</p>
<?php
endif;

if ( 0 <= version_compare( ADVADS_VERSION, '1.9.1' ) ) {
	include ADVADS_BASE_PATH . 'admin/views/ad-conditions-string-operators.php';
}
?>
<input type="text" name="<?php echo esc_attr( $name ); ?>[value]" value="<?php echo esc_attr( $value ); ?>" />
<br class="clear" />
<br />
<p class="description"><?php echo esc_html( $type_options[ $options['type'] ]['description'] ); ?></p>
