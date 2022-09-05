<?php
foreach ( $post_types as $_type_id => $_type ) {
	$checked = in_array( $_type_id, $selected );

	if ( $type_label_counts[ $_type->label ] < 2 ) {
		$_label = $_type->label;
	} else {
		$_label = sprintf( '%s (%s)', $_type->label, $_type_id );
	}
	?>
	<label style="margin-right: 1em;"><input type="checkbox" name="<?php
	echo ADVADS_SLUG; ?>[pro][general][disable-by-post-types][]" <?php
		checked( $checked, true ); ?> value="<?php echo $_type_id; ?>"><?php esc_html_e( $_label ); ?></label><?php
}
?>
