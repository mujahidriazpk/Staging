<h3><?php _e('Reset Stats', 'advanced-ads-tracking'); ?></h3>
<p><?php _e('Use this form to remove the stats for one or all ads.', 'advanced-ads-tracking'); ?></p>
<a onclick="advads_toggle('#advads-tracking-reset-form');" class="advads-toggle-link"><?php _e( 'show/hide reset form', 'advanced-ads-tracking' ); ?></a>
<form action="" method="post" id="advads-tracking-reset-form" onsubmit="return advads_tracking_reset_form_submit();" style="display: none; ">
    <select name="advads-stats-reset">
        <option value=""><?php _e('(please choose the ad)', 'advanced-ads-tracking'); ?></option>
        <option value="all-ads" <?php if(isset($ads[0]) && isset($remove_ad)) selected($remove_ad, 'all-ads'); ?>><?php _e('--all ads--', 'advanced-ads-tracking'); ?></option>
        <?php foreach($all_ads as $_ad) : ?>
        <option value="<?php echo $_ad->ID; ?>" <?php if(isset($remove_ad)) selected($remove_ad, $_ad->ID); ?>><?php echo $_ad->post_title; ?></option>
        <?php endforeach; ?>
    </select>
    <input type="submit" id="advads-tracking-reset" value="<?php _e('clear stats', 'advanced-ads-tracking'); ?>" class="button button-primary"/>
</form>
<script>
    // check form submit
    function advads_tracking_reset_form_submit(){
        // get the selected ad
        var adoption = jQuery('#advads-tracking-reset-form select :selected');

        // check if there was an ad selected
        if(adoption.val() === ''){
            alert('<?php _e('Please choose an ad', 'advanced-ads-tracking'); ?>');
            return false;
        }

        // ask user to confirm reset
        return confirm('<?php _e('Are you sure you want to reset the stats for', 'advanced-ads-tracking'); ?>' + ' "' + adoption.text() + '"?');

    };
</script>