<input type="number" min="0" name="advads[placements][<?php echo esc_attr( $placement_slug ); ?>][options][words_between_repeats]" value="<?php echo absint( $words_between_repeats ); ?>" />
<p class="description">
<?php
	esc_html_e( 'A minimum amount of words between automatically injected ads.', 'advanced-ads-pro' );
?>
	 <?php
		esc_html_e( 'Words are counted within paragraphs, headlines and any other element.', 'advanced-ads-pro' );
		?>
</p>
