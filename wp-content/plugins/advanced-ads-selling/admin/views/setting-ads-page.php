<input name="<?php esc_attr_e( Advanced_Ads_Selling_Plugin::OPTION_KEY ); ?>[ads-page]" type="checkbox"
		value="1" <?php checked( 1, $ads_page_enabled ); ?>/>
<p class="description"><?php esc_attr_e( 'Show an ”Ads” link in the customer account in the frontend with a list of purchased ads and additional information.', 'advanced-ads-selling' ); ?></p>
<p class="description"><?php esc_attr_e( 'The page contains links to the public stats pages of ads when the Advanced Ads Tracking add-on is enabled.', 'advanced-ads-selling' ); ?></p>