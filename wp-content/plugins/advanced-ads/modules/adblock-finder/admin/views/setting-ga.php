<?php
/**
 * Input for Google Analytics property ID.
 *
 * @var string $ga_uid Google Analytics property ID
 */
?>
<label>
	<input type="text" name="<?php echo esc_attr( ADVADS_SLUG ); ?>[ga-UID]" value="<?php echo esc_attr( $ga_uid ); ?>"/>
	<?php esc_html_e( 'Google Analytics Tracking ID', 'advanced-ads' ); ?>
</label>

<p class="description">
	<?php esc_html_e( 'Do you want to know how many of your visitors are using an ad blocker? Enter your Google Analytics property ID above to count them.', 'advanced-ads' ); ?>
	<br>
	<?php
	printf(
	/* translators: 1: is an example id for Universal Analytics <code>UA-123456-1</code>, 2: is an example id for GA4 '<code>G-A12BC3D456</code>' */
		esc_html__( '%1$s for Universal Analytics or %2$s for Google Analytics 4.', 'advanced-ads' ),
		'<code>UA-123456-1</code>',
		'<code>G-A12BC3D456</code>'
	);
	?>
</p>
