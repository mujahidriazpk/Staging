<?php
	$options = isset( $placement['options']['layer_placement']['sticky'] ) ? $placement['options']['layer_placement']['sticky'] : array();

	$enabled   = isset( $options['enabled'] ) ? $options['enabled'] : false;
	$assistant = isset( $options['assistant'] ) ? $options['assistant'] : 'center';
	$type      = isset( $options['type'] ) ? $options['type'] : 'assistant';
	$width     = isset( $options['position']['width'] ) ? absint( $options['position']['width'] ) : 0;
	$height    = isset( $options['position']['height'] ) ? absint( $options['position']['height'] ) : 0;

	$option_name = "advads[placements][$placement_slug][options][layer_placement][sticky]";
	// echo "<pre>";
	// print_r($placement);
	// echo "</pre>";
	if (! isset($layer_class)){
		$layer_class = Advanced_Ads_Layer::get_layer_class();
	}
?>

<div>
	<div class="<?php echo $layer_class; ?>-aa-position">
		
		<div class="<?php echo $layer_class; ?>-assistant-wrapper">
			<div class="advads-sticky-assistant" id="<?php echo $layer_class; ?>-ads-type-assistant-inputs-<?php echo $placement_slug; ?>">
				<table>
					<tr>
						<td><input type="radio" name="<?php echo $option_name; ?>[assistant]" title="<?php _e( 'top left', 'advanced-ads-layer' ); ?>" value="topleft" <?php checked( $assistant, 'topleft' ); ?>/></td>
						<td><input type="radio" name="<?php echo $option_name; ?>[assistant]" title="<?php _e( 'top center', 'advanced-ads-layer' ); ?>" value="topcenter" <?php checked( $assistant, 'topcenter' ); ?>/></td>
						<td><input type="radio" name="<?php echo $option_name; ?>[assistant]" title="<?php _e( 'top right', 'advanced-ads-layer' ); ?>" value="topright" <?php checked( $assistant, 'topright' ); ?>/></td>
					</tr>
					<tr>
						<td><input type="radio" name="<?php echo $option_name; ?>[assistant]" title="<?php _e( 'center left', 'advanced-ads-layer' ); ?>" value="centerleft" <?php checked( $assistant, 'centerleft' ); ?>/></td>
						<td><input type="radio" name="<?php echo $option_name; ?>[assistant]" title="<?php _e( 'center', 'advanced-ads-layer' ); ?>" value="center" <?php checked( $assistant, 'center' ); ?>/></td>
						<td><input type="radio" name="<?php echo $option_name; ?>[assistant]" title="<?php _e( 'center right', 'advanced-ads-layer' ); ?>" value="centerright" <?php checked( $assistant, 'centerright' ); ?>/></td>
					</tr>
					<tr>
						<td><input type="radio" name="<?php echo $option_name; ?>[assistant]" title="<?php _e( 'bottom left', 'advanced-ads-layer' ); ?>" value="bottomleft" <?php checked( $assistant, 'bottomleft' ); ?>/></td>
						<td><input type="radio" name="<?php echo $option_name; ?>[assistant]" title="<?php _e( 'bottom center', 'advanced-ads-layer' ); ?>" value="bottomcenter" <?php checked( $assistant, 'bottomcenter' ); ?>/></td>
						<td><input type="radio" name="<?php echo $option_name; ?>[assistant]" title="<?php _e( 'bottom right', 'advanced-ads-layer' ); ?>" value="bottomright" <?php checked( $assistant, 'bottomright' ); ?>/></td>
					</tr>
				</table>

				
				<div <?php if ( $this->fancybox_is_enabled || ( ! $width && ! $height )  ): ?> style="display:none;" <?php endif; ?>>
					<br/>
					<p class="advads-error-message"><?php _e( 'These settings are deprecated. Please, set width and height for the ad itself.', 'advanced-ads-layer' ); ?></p>
					<p class="description"><?php _e( 'Enter banner width and height to correctly center the ad.', 'advanced-ads-layer' ); ?></p>
					<label><?php _e( 'banner width', 'advanced-ads-layer' ); ?>
						<input type="number" name="<?php echo $option_name; ?>[position][width]" title="<?php _e( 'banner width', 'advanced-ads-layer' ); ?>" value="<?php echo $width; ?>"/>px
					</label>, 
					<label><?php _e( 'banner height', 'advanced-ads-layer' ); ?>
						<input type="number" name="<?php echo $option_name; ?>[position][height]" title="<?php _e( 'banner height', 'advanced-ads-layer' ); ?>" value="<?php echo $height; ?>"/>px
					</label>
				</div>


			</div>
			<p class="description"><?php _e( 'Choose a position on the screen.', 'advanced-ads-layer' ); ?></p>

		</div>

		<div class='clear'></div>
	</div>
</div>
