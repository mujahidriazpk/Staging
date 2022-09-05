<div class="advads-option-list">
    <span class="label"><?php _e('tracking', 'advanced-ads-tracking'); ?></span>
    <div>
	<select name="advanced_ad[tracking][enabled]">
	<?php foreach ( $tracking_choices as $key => $value ) : ?>
		<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $enabled, $key ); ?>><?php echo $value; ?></option>
	<?php endforeach; ?>
	</select>
	<p class="description"><?php printf(__('Please visit the <a href="%s" target="_blank">manual</a> to learn more about click tracking.', 'advanced-ads-tracking'), Advanced_Ads_Tracking_Admin::PLUGIN_LINK); ?></p>
    </div>
    <hr/>
    <span class="label"><?php _e('url', 'advanced-ads-tracking'); ?></span>
    <div>
	<input name="advanced_ad[url]" style="width:60%;" id="advads-url" type="text" value="<?php echo $link; ?>"/>
	<p class="description"><?php _e( 'Don’t use this field on JavaScript ad tags (like from Google AdSense). If you are using your own <code>&lt;a&gt;</code> tag, use <code>href="%link%"</code> to insert the tracking link.', 'advanced-ads-tracking' ); ?></p>
	<?php $supported_placeholder_array = array( 
		'[POST_ID]'	=> 'post ID', 
		'[POST_SLUG]'	=> 'post slug',
		'[CAT_SLUG]'	=> 'a comma-separated list of category slugs', 
		'[AD_ID]'	=> 'ID of the ad' ); 
	$supported_placeholders = implode('</code>, <code>' , array_keys( $supported_placeholder_array ) );
	$supported_placeholder_texts = implode(', ' , $supported_placeholder_array );
	?>
	<p class="description"><?php printf( 
		/*
		 * translators: %1$s is a list of placeholder like [POST_ID] and %2$s the appropriate names like "post ID"
		 */
		esc_attr__( 'You can use %1$s in the url to insert %2$s into the url.', 'advanced-ads-tracking' ), '<code>' . $supported_placeholders . '</code>', $supported_placeholder_texts ); ?></p>
    </div>
    <hr/>
	<span class="label"><?php _e( 'target window', 'advanced-ads-tracking' ); ?></span>
	<div>
		<label><input name="advanced_ad[tracking][target]" type="radio" value="default" <?php checked($target, 'default'); ?>/><?php _e('default', 'advanced-ads-tracking'); ?></label>
		<label><input name="advanced_ad[tracking][target]" type="radio" value="same" <?php checked($target, 'same'); ?>/><?php _e('same window', 'advanced-ads-tracking'); ?></label>
		<label><input name="advanced_ad[tracking][target]" type="radio" value="new" <?php checked($target, 'new'); ?>/><?php _e('new window', 'advanced-ads-tracking'); ?></label>
		<p class="description"><?php _e( 'Where to open the link (if present).', 'advanced-ads-tracking' ); ?></p>
    </div>
	<hr />
	<span class="label"><?php _e( 'Add “nofollow”', 'advanced-ads-tracking' ); ?></span>
	<div>
		<label><input name="advanced_ad[tracking][nofollow]" type="radio" value="default" <?php checked($nofollow, 'default'); ?>/><?php _e( 'default', 'advanced-ads-tracking' ); ?></label>
		<label><input name="advanced_ad[tracking][nofollow]" type="radio" value="1" <?php checked($nofollow, 1); ?>/><?php _e( 'yes', 'advanced-ads-tracking' ); ?></label>
		<label><input name="advanced_ad[tracking][nofollow]" type="radio" value="0" <?php checked($nofollow, 0); ?>/><?php _e( 'no', 'advanced-ads-tracking' ); ?></label>
		<p class="description"><?php printf( __( 'Add %s to tracking links.', 'advanced-ads-tracking' ), '<code>rel="nofollow"</code>'); ?></p>
    </div>
	<hr />
</div>
