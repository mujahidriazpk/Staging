<div id="advanced-ads-weekdays" class="misc-pub-section">
<label onclick="advads_toggle_box( '#advanced-ads-weekdays-enable', '#advanced-ads-weekdays .inner' )">
<input type="checkbox" id="advanced-ads-weekdays-enable" name="advanced_ad[weekdays][enabled]" value="1" <?php
	checked( $enabled, 1 ); ?>/><?php _e( 'Set specific days', 'advanced-ads-pro' ); ?>
</label>
<div class="inner" <?php if ( ! $enabled ) : ?>style="display:none;"<?php endif; ?>>
	<select id="advads-pro-weekdays" name="advanced_ad[weekdays][day_indexes][]" multiple="multiple" size="7">
	<?php for ( $i = 1; $i <= 7; $i++ ) :
		$day_index = ( $i === 7 ) ? 0 : $i;
		$selected = in_array( $day_index , $day_indexes ) ? ' selected="selected"' : '';
		printf( '<option value="%s"%s>%s</option>', $day_index, $selected, esc_html( $wp_locale->get_weekday( $day_index ) ) );
	endfor; ?>
	</select>
    <p class="description">(<?php echo $TZ; ?>)</p>
</div>
</div>