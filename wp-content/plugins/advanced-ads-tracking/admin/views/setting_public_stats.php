<?php echo site_url(); ?>/<input id="public-stat-base" name="<?php 
	echo $this->plugin->options_slug; ?>[public-stats-slug]" type="text" value="<?php 
	echo $public_stats_slug; ?>" autocomplete="advads-stats-slug"/>/<span id="public-stats-spinner32" style="display:inline-block;vertical-align:middle;margin-left:0.5em;"></span><br />
<p id="public-stat-notice" style="font-style:italic;"></p>
<script type="text/javascript">
var advadsTrackingAjaxNonce = '<?php echo $this->ajax_nonce; ?>';
</script>
