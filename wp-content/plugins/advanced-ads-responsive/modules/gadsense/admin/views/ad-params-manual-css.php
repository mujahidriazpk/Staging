<?php
if (!defined('WPINC')) {
    die();
}
if (!isset($extra_params['default_hidden'])) $extra_params['default_hidden'] = false;
?>
<div id="gadsense-css-div" <?php if (!$is_responsive || !$use_manual_css) echo 'style="display: none;"'; ?>>
    <p><?php printf(__('Need help? Take a look at the <a href="%s" target="_blank">tutorial</a>.', 'advanced-ads-responsive'), 'http://wpadvancedads.com/adsense-responsive-custom-sizes/'); ?></p>
	<table class="widefat fixed">
		<thead>
			<tr>
				<th><?php _e('min. browser width', 'advanced-ads-responsive'); ?></th>
				<th><?php _e('ad width', 'advanced-ads-responsive'); ?></th>
				<th><?php _e('ad height', 'advanced-ads-responsive'); ?></th>
				<th colspan="2"><?php _e('hidden', 'advanced-ads-responsive'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php _e('Default', 'advanced-ads-responsive'); ?></td>
				<td><input type="number" min="0" <?php if ( isset( $extra_params['default_hidden'] ) && $extra_params['default_hidden'] ) echo 'disabled="disabled"';  ?> value="<?php echo $extra_params['default_width']; ?>" name="default-width" style="width: 5em;"></td>
				<td><input type="number" min="0" <?php if ( isset( $extra_params['default_hidden'] ) && $extra_params['default_hidden'] ) echo 'disabled="disabled"';  ?> value="<?php echo $extra_params['default_height']; ?>" name="default-height" style="width: 5em;"></td>
				<td colspan="2"><input type="checkbox" id="default-hidden" value="1" title="<?php esc_attr_e('Hide for this size ?', 'advanced-ads-responsive'); ?>" <?php checked($extra_params['default_hidden']); ?> /></td>
			</tr>
			<tr class="alt">
				<td><input type="number" min="0" value="" id="new-ad-min-width" style="width: 5em;"></td>
				<td><input type="number" min="0" value="" id="new-ad-width" style="width: 5em;"></td>
				<td><input type="number" min="0" value="" id="new-ad-height" style="width: 5em;"></td>
				<td><input type="checkbox" value="1" id="new-ad-hidden" title="<?php esc_attr_e('Hide for this size ?', 'advanced-ads-responsive'); ?>" /></td>
				<td>
					<button class="button button-primary" id="new-rule-btn">
						<i class="dashicons dashicons-plus-alt" style="vertical-align: middle;"></i>&nbsp;<?php _e('Add rule', 'advanced-ads-responsive'); ?>
					</button>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="widefat fixed">
		<tbody id="gadsense-css-tbody">
			<?php if (!empty($extra_params['at_media'])) : ?>
			<?php foreach ($extra_params['at_media'] as $row) : ?>
				<tr data-minwidth="<?php echo esc_attr($row['minw']); ?>">
					<td><b><span class="row-minw"><?php echo $row['minw']; ?></span></b>&nbsp;px</td>
					<?php if ($row['hidden']) : ?>
						<td colspan="2">
							<?php _e('Not displayed', 'advanced-ads-responsive'); ?>
							<span class="row-w" style="display:none"><?php echo $row['w']; ?></span>
							<span class="row-h" style="display:none"><?php echo $row['h']; ?></span>
							<input type="hidden" class="row-hidden" value="1" />
						</td>
					<?php else : ?>
						<td><b><span class="row-w"><?php echo $row['w']; ?></span></b>&nbsp;px</td>
						<td><b><span class="row-h"><?php echo $row['h']; ?></span></b>&nbsp;px<input type="hidden" class="row-hidden" value="0" /></td>
					<?php endif; ?>
					<td colspan="2">
						<button class="button button-secondary row-remove">
							<i class="dashicons dashicons-dismiss" title="<?php esc_attr_e('Remove this rule', 'advanced-ads-responsive'); ?>" style="vertical-align: middle;"></i>
							&nbsp;<?php _e('remove', 'advanced-ads-responsive'); ?>
						</button>
					</td>
				</tr>
			<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div><!-- #gadsense-css-div-->
