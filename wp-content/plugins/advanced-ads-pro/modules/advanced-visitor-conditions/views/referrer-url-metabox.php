<h4><?php _e( 'Display by referrer url', 'advanced-ads-pro' ); ?></h4>
    <p><label><input type="checkbox" name="advanced_ad[visitor][referrer-url][enable]" value="1" <?php
	checked( $ref_url_enable, 1 ); ?> onclick="advads_toggle_box(this, '#advads-referer-url');"/><?php _e( 'Display ad depending on the external url the user comes from.', 'advanced-ads-pro' ); ?></label></p>
    <div id="advads-referer-url"<?php if ( ! $ref_url_enable ) : ?> style="display:none;"<?php endif; ?>>
        <?php _e( 'URL', 'advanced-ads-pro' ); ?>
        <select name="advanced_ad[visitor][referrer-url][bool]"><?php
		foreach ( $referrer_url_bools as $_bool_key => $_bool ) : ?>
                <option value="<?php echo $_bool_key; ?>" <?php selected( $_bool_key, $ref_url_bool ); ?>><?php echo $_bool; ?></option>
            <?php endforeach; ?></select>
        <select name="advanced_ad[visitor][referrer-url][mode]"><?php
		foreach ( $referrer_url_modi as $_mod_key => $_modus ) : ?>
                <option value="<?php echo $_mod_key; ?>" <?php selected( $_mod_key, $ref_url_mode ); ?>><?php echo $_modus; ?></option>
            <?php endforeach; ?></select>
        <input type="text" name="advanced_ad[visitor][referrer-url][url]" value="<?php
			echo $ref_url_url; ?>" placeholder="<?php _e( 'url of the referrer', 'advanced-ads-pro' ); ?>"/>
    </div>