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
	
	<?php if (!is_plugin_active('wp-terms-popup-age/wp-terms-popup-age.php')) : ?>
	<div id="col-container">
        <div class="col-wrap wptp-add-on">
            <h1><?php _e('WP Terms Popup Age Verification', $this->plugin_name); ?></h1>

            <img class="alignleft" src="<?php echo plugins_url('/img/age-1.png', dirname(__FILE__)); ?>">
            
            <h2><?php _e('Confirm a visitor\'s age before they can agree to your popup and view your site', $this->plugin_name); ?></h2>

            <p>
            <?php _e('The Age Verification add-on for WP Terms Popup presents your visitors with a simple age verification request. The popup’s agreement button can not be clicked until the age the user has selected matches whatever age requirement you have set.', $this->plugin_name); ?>
            </p>

            <div class="clear"></div>

            <h2><?php _e('What Settings Are Available for WP Terms Popup Age Verification?', $this->plugin_name); ?></h2>

            <p>
            <?php _e('The Age Verification add-on allows for unique settings on each of the popups you using on your site. Those settings allow you to:', $this->plugin_name); ?>
            </p>

            <ul>
                <li><?php _e('Enable on a Per Popup Basis', $this->plugin_name); ?></li>
                <li><?php _e('Choose Your Date Format', $this->plugin_name); ?></li>
                <li><?php _e('Set the Minimum Age Requirement', $this->plugin_name); ?></li>
            </ul>
            
            <img class="alignright" src="<?php echo plugins_url('/img/age-2.png', dirname(__FILE__)); ?>">

            <p>
            <?php _e('The settings for your age verification are handled on each WP Terms Popup individually. You can turn the age verification request on, arrange the order of the fields, and set the minimum age requirement.', $this->plugin_name); ?>
            </p>
            
            <p>
            <?php _e('WP Terms Popup Age Verification integrates directly into the free plugin. After purchase and installation, this tab will be replaced with the Age Verification plugin.', $this->plugin_name); ?>
            </p>
            
            <p>
            <a class="wptp-button" target="_blank" href="https://termsplugin.com/age-verification?utm_source=plugin&utm_content=add_on_tab_age"><?php _e('Learn About WP Terms Popup Age Verification', $this->plugin_name); ?></a>
            </p>
        </div>
	</div>
	<?php endif; ?>

    <?php include 'wp-terms-popup-admin-footer.php'; ?>
</div>