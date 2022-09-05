<label style="margin-right:2em;">
<input type="radio" name="<?php echo $this->plugin->options_slug; ?>[email-sched]" value="daily" <?php checked( 'daily', $sched ) ?> />
<?php _e( 'daily', 'advanced-ads-tracking' ); ?>
</label>
<label style="margin-right:2em;">
<input type="radio" name="<?php echo $this->plugin->options_slug; ?>[email-sched]" value="weekly" <?php checked( 'weekly', $sched ) ?> />
<?php _e( 'weekly', 'advanced-ads-tracking' ); ?>
</label>
<label style="margin-right:2em;">
<input type="radio" name="<?php echo $this->plugin->options_slug; ?>[email-sched]" value="monthly" <?php checked( 'monthly', $sched ) ?> />
<?php _e( 'monthly', 'advanced-ads-tracking' ); ?>
</label>
<p class="description"><?php _e( 'How often to send email reports', 'advanced-ads-tracking' ); ?></p>
<script type="text/template" id="advads-track-admin-spinner"><img alt="" class="ajax-spinner" src="<?php echo admin_url( 'images/spinner.gif' ); ?>" /></script>