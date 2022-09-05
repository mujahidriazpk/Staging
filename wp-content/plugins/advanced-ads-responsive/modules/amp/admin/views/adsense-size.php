<label class="label" <?php  if ( ! $is_supported ) { echo 'style="display: none;"'; } ?>>AMP</label>
<div id="advads-adsense-responsive-amp-inputs" style="overflow:hidden; <?php if ( ! $is_supported ) { echo 'display: none;'; } ?>">
	<ul <?php if ( ! Advanced_Ads_Responsive_Amp_Admin::has_amp_plugin() ) { echo 'style="display: none;"'; }; ?>>
	<li>
		<label><input type="radio" name="<?php echo $option_name; ?>[layout]" value="default" <?php
			checked( $layout, 'default' ); ?> /><?php _e( 'automatically convert to AMP', 'advanced-ads-responsive' ); ?></label>
		<a class="advads-manual-icon" href="<?php echo ADVADS_URL . 'manual/amp-adsense-wordpress/#utm_source=advanced-ads&utm_medium=link&utm_campaign=settings-adsense-amp'; ?>" target="_blank">
		    <span class="dashicons dashicons-welcome-learn-more"></span>
		</a>
	</li>
	<li>
		<label><input type="radio" name="<?php echo $option_name; ?>[layout]" value="responsive" <?php checked( $layout, 'responsive' ); ?>/><?php
		printf( __( 'use dynamic size with ratio %s x %s', 'advanced-ads-responsive' ),
			'</label><label><input type="number" min="1" max="99999" name="' . $option_name . '[width]" value="' . $width . '"/>',
			'</label><label><input type="number" min="1" max="99999" name="' . $option_name . '[height]" value="' . $height . '"/>'
		); ?></label>
	</li>
	<li>
		<label><input type="radio" name="<?php echo $option_name; ?>[layout]" value="fixed_height" <?php checked( $layout, 'fixed_height' ); ?>/><?php
		printf( __( 'use responsive width and static height of %s px', 'advanced-ads-responsive' ),
			'</label><label><input type="number" min="1" max="99999" name="' . $option_name . '[fixed_height]" value="' . $fixed_height . '"/>'
		); ?></label>
	</li>
	<li>
		<label><input type="radio" name="<?php echo $option_name; ?>[layout]" value="hide" <?php
			checked( $layout, 'hide' ); ?> /><?php _e( 'hide', 'advanced-ads-responsive' ); ?></label>
	</li>
	</ul>
	<?php if ( ! Advanced_Ads_Responsive_Amp_Admin::has_amp_plugin() ):
		_e( 'no AMP plugin found', 'advanced-ads-responsive' );
	endif; ?>
</div>
<hr />
