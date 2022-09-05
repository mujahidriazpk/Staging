<?php
/**
 * Effective ad output.
 *
 * @var string $div_id random alphanumeric ID of the div container.
 * @var string $path ad unit path.
 * @var string $size definition of ad unit size.
 * @var string $size_mapping_object definition of size mapping object.
 * @var string $size_mapping string that adds the size mapping object to the ad call.
 * @var string $empty_div optional string to allow collapsing the ad units.
 * @var string $key_values optional key-values parameter.
 */
?>
<script async="async" src="https://securepubads.g.doubleclick.net/tag/js/gpt.js"></script>
<script> var googletag = googletag || {}; googletag.cmd = googletag.cmd || [];</script>
<div id="<?php echo esc_attr( $div_id ); ?>">
  <script>
	googletag.cmd.push(function() {
		<?php
		echo $size_mapping_object;
		?>
		googletag.defineSlot( '<?php echo esc_attr( $path ); ?>', <?php echo $size; ?>, '<?php echo esc_attr( $div_id ); ?>' )
		.addService(googletag.pubads())<?php echo $key_values; ?><?php echo $size_mapping; ?><?php echo $empty_div; //phpcs:ignore ?>;
		googletag.enableServices();
		googletag.display( '<?php echo esc_attr( $div_id ); ?>' );
	});
  </script>
</div>
