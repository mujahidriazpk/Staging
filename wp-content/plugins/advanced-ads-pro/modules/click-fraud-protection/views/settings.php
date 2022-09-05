<?php
$options = Advanced_Ads_Pro::get_instance()->get_options();
$module_enabled = isset( $options['cfp']['enabled'] ) && $options['cfp']['enabled'];
$click_limit = isset( $options['cfp']['click_limit'] )? Advanced_Ads_Pro_Utils::absint( $options['cfp']['click_limit'], 1 ) : 3;
$cookie_exp = isset( $options['cfp']['cookie_expiration'] )? Advanced_Ads_Pro_Utils::absint( $options['cfp']['cookie_expiration'], 1 ) : 3;
$ban_duration = isset( $options['cfp']['ban_duration'] )? Advanced_Ads_Pro_Utils::absint( $options['cfp']['ban_duration'], 1 ) : 7;
?>
<input name="<?php echo Advanced_Ads_Pro::OPTION_KEY; ?>[cfp][enabled]" type="checkbox" value="1" <?php checked( $module_enabled ); ?> id="advanced-ads-pro-cfp-enabled" class="advads-has-sub-settings" />
<label for="advanced-ads-pro-cfp-enabled" class="description">
	<?php _e( 'Activate <em>click fraud protection</em> module', 'advanced-ads-pro' ); 
	?> – <a href="<?php echo ADVADS_URL . 'manual/click-fraud-protection/#utm_source=advanced-ads&utm_medium=link&utm_campaign=pro-cfp-manual'; ?>" target="_blank"><?php _e( 'Manual', 'advanced-ads-pro' ); ?></a>
</label>

<div class="advads-sub-settings">
	<br />
	<input type="number" min="1" name="<?php echo Advanced_Ads_Pro::OPTION_KEY; ?>[cfp][click_limit]" style="width:4em;" value="<?php echo $click_limit; ?>" />
	<span><?php _e( 'Allowed clicks on a single ad before it is removed', 'advanced-ads-pro' ); ?></span>
	<br/>
	<input type="number" min="1" name="<?php echo Advanced_Ads_Pro::OPTION_KEY; ?>[cfp][cookie_expiration]" style="width:4em;" value="<?php echo $cookie_exp; ?>" />
	<span><?php _e( 'Period in which the click limit should be reached ( in hours )', 'advanced-ads-pro' ); ?></span>
	<br/>
	<input type="number" min="1" name="<?php echo Advanced_Ads_Pro::OPTION_KEY; ?>[cfp][ban_duration]" style="width:4em;" value="<?php echo $ban_duration; ?>" />
	<span><?php _e( 'Period for which to hide the ad ( in days )', 'advanced-ads-pro' ); ?></span>
</div>
