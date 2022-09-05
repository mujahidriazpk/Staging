<?php
$options = Advanced_Ads_Pro::get_instance()->get_options();
$check = isset($options['advanced-visitor-conditions']['enabled']) && $options['advanced-visitor-conditions']['enabled'];
?>
<input name="<?php echo Advanced_Ads_Pro::OPTION_KEY; ?>[advanced-visitor-conditions][enabled]" id="advanced-ads-pro-advanced-visitor-conditions-enabled" type="checkbox" value="1" <?php checked( $check ); ?> />
<label for="advanced-ads-pro-advanced-visitor-conditions-enabled" class="description"><?php _e('Activate <em>advanced visitor conditions</em> module.', 'advanced-ads-pro'); ?></label>
