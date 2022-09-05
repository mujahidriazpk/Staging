<?php
/**
 * HTML for Collector tab.
 *
 * @link       https://linksoftwarellc.com
 * @since      2.0.0
 *
 * @package    Wp_Terms_Popup
 * @subpackage Wp_Terms_Popup/admin/partials
 */
?>

<div class="wrap wptp-wrap">
	<h2>WP Terms Popup</h2>
	
	<h2 class="nav-tab-wrapper">
        <?php do_action('wptp_settings_tabs'); ?>
	</h2>
	
	<?php if (!is_plugin_active('wp-terms-popup-collector/index.php')) : ?>
	<div id="col-container">
        <div class="col-wrap wptp-add-on">
            <h1><?php _e('WP Terms Popup Collector', $this->plugin_name); ?></h1>

            <img class="alignleft" src="<?php echo plugins_url('/img/collector-1.png', dirname(__FILE__)); ?>">
            
            <h2><?php _e('Store information about your website\'s visitors after they agree to your popups', $this->plugin_name); ?></h2>

            <p>
            <?php _e('Collector saves information about your visitors after they agree to popups created with the WP Terms Popup plugin. You can use Collector to keep track of when users accept your terms of service, privacy policy, or other important information on your WordPress site.', $this->plugin_name); ?>
            </p>

            <div class="clear"></div>

            <h2><?php _e('What Data Does WP Terms Popup Collector Store?', $this->plugin_name); ?></h2>

            <p>
            <?php _e('The add-on stores information about visitors after they have clicked a popup\'s agreement button. The details stored after each visitor agrees to your popup includes:', $this->plugin_name); ?>
            </p>
            
            <img class="alignright" src="<?php echo plugins_url('/img/collector-2.png', dirname(__FILE__)); ?>">

            <ul>
                <li><?php _e('Agreement Date', $this->plugin_name); ?></li>
                <li><?php _e('IP Address', $this->plugin_name); ?></li>
                <li><?php _e('User Agent/Browser Details', $this->plugin_name); ?></li>
                <li><?php _e('Page the Popup was Seen', $this->plugin_name); ?></li>
                <li><?php _e('WordPress Username (if applicable)', $this->plugin_name); ?></li>
                <li><?php _e('Agreement Expiration Date', $this->plugin_name); ?></li>
            </ul>
            
            <p>
            <?php _e('You can search through your saved results, copy them to your clipboard, and export the data to a CSV file with the click of a button.', $this->plugin_name); ?>
            </p>
            
            <p>
            <?php _e('WP Terms Popup Collector integrates directly into the free plugin. After purchase and installation, this tab will be replaced with the Collector plugin.', $this->plugin_name); ?>
            </p>
            
            <p>
            <a class="wptp-button" target="_blank" href="https://termsplugin.com/collector?utm_source=plugin&utm_content=add_on_tab_collector"><?php _e('Learn About WP Terms Popup Collector', $this->plugin_name); ?></a>
            </p>
        </div>
	</div>
	<?php endif; ?>
	
	<?php
        if (is_plugin_active('wp-terms-popup-collector/index.php')) {
            wptp_collector_settings();
        }
    ?>

    <?php include 'wp-terms-popup-admin-footer.php'; ?>
</div>