<select name="<?php echo ADVADS_SLUG . '[' . AAGT_SLUG . '][locale]' ?>">
    <?php foreach ( Advanced_Ads_Geo_Api::$locales as $_key => $_value ){
	?><option value="<?php echo $_key; ?>" <?php selected( $_key, $locale ); ?>><?php echo $_value; ?></option><?php
    } 
?></select>
<p class="description"><?php _e( 'Choose the language of the state/region or city entered. If the language is not available in the geo location database, it will check against the English version.', 'advanced-ads-geo' ); ?></p>