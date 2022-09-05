<div id="advanced-ads-expiry-date-<?php echo $slug; ?>">
	<label>
		<input onclick="advads_toggle_box( this, '#advanced-ads-expiry-date-<?php echo $slug; ?> .inner' )" type="checkbox" id="advanced-ads-expiry-date-enable" name="advads[placement_tests][<?php echo $slug; ?>][expiry_date][enabled]"
			value="1" <?php checked( $enabled, 1 ); ?>/><?php _ex( 'Send email after', 'placement tests', 'advanced-ads-pro' ); ?>&nbsp;(<?php echo $TZ; ?>)
	</label>
	<br/>

	<div class="inner" <?php if ( ! $enabled ) : ?>style="display:none;"<?php endif; ?>>
		<?php
			$month = '<label><span class="screen-reader-text">' . __( 'Month', 'advanced-ads-pro' ) . '</span><select class="advads-mm" name="advads[placement_tests][' . $slug . '][expiry_date][month]"' . ">\n";
			for ( $i = 1; $i < 13; $i = $i + 1 ) {
				$monthnum = zeroise( $i, 2 );
				$month .= "\t\t\t" . '<option value="' . $monthnum . '" ' . selected( $curr_month, $monthnum, false ) . '>';
				$month .= sprintf( _x( '%1$s-%2$s', '1: month number (01, 02, etc.), 2: month abbreviation', 'advanced-ads-pro' ),
				$monthnum, $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) ) . "</option>\n";
			}
			$month .= '</select></label>';

			$day = '<label><span class="screen-reader-text">' . __( 'Day', 'advanced-ads-pro' ) . '</span><input type="text" class="advads-jj" name="advads[placement_tests][' . $slug . '][expiry_date][day]" value="' . $curr_day . '" size="2" maxlength="2" autocomplete="off" /></label>';
			$year = '<label><span class="screen-reader-text">' . __( 'Year', 'advanced-ads-pro' ) . '</span><input type="text" class="advads-aa" name="advads[placement_tests][' . $slug . '][expiry_date][year]" value="' . $curr_year . '" size="4" maxlength="4" autocomplete="off" /></label>';
			$hour = '<label><span class="screen-reader-text">' . __( 'Hour', 'advanced-ads-pro' ) . '</span><input type="text" class="advads-hh" name="advads[placement_tests][' . $slug . '][expiry_date][hour]" value="' . $curr_hour . '" size="2" maxlength="2" autocomplete="off" /></label>';
			$minute = '<label><span class="screen-reader-text">' . __( 'Minute', 'advanced-ads-pro' ) . '</span><input type="text" class="advads-mn" name="advads[placement_tests][' . $slug . '][expiry_date][minute]" value="' . $curr_minute . '" size="2" maxlength="2" autocomplete="off" /></label>';

		?>
		<fieldset class="advads-timestamp">
			<?php printf( _x( '%1$s %2$s, %3$s @ %4$s %5$s', 'order of expiry date fields 1: month, 2: day, 3: year, 4: hour, 5: minute', 'advanced-ads-pro' ), $month, $day, $year, $hour, $minute ); ?>
		</fieldset>
	</div>
</div>