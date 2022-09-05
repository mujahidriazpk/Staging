<?php
/**
 * HTML for Designer tab.
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
	
	<?php if (!is_plugin_active('wp-terms-popup-pro/index.php') && !is_plugin_active('wp-terms-popup-designer/index.php')) : ?>
	<div id="col-container">
        <div class="col-wrap wptp-add-on">
            <h1><?php _e('WP Terms Popup Designer', $this->plugin_name); ?></h1>

            <img class="alignleft" src="<?php echo plugins_url('/img/designer-1.png', dirname(__FILE__)); ?>">
            
            <h2><?php _e('Adjust the appearance of your popups without writing code or modifying your WordPress theme', $this->plugin_name); ?></h2>

            <p>
            <?php _e('The Designer add-on is the perfect solution for WordPress website owners who want to make adjustments to the appearance of their popups without any code.', $this->plugin_name); ?>

            <div class="clear"></div>

            <h2><?php _e('What Can I Change with WP Terms Popup Designer?', $this->plugin_name); ?></h2>

            <p>
            <?php _e('Purchase the WP Terms Popup Designer plugin and adjust these parts of your popups without writing any code:', $this->plugin_name); ?>
            </p>

            <img class="alignright" src="<?php echo plugins_url('/img/designer-2.png', dirname(__FILE__)); ?>">
					
            <ul>
                <li><?php _e('Header Color, Size, Spacing, and Alignment', $this->plugin_name); ?></li>
                <li><?php _e('Background Color, Transparency, and Blur', $this->plugin_name); ?></li>
                <li><?php _e('Body Color, Size, Spacing, and Alignment', $this->plugin_name); ?></li>
                <li><?php _e('Button Order, Color, Size, and Shape', $this->plugin_name); ?></li>
                <li><?php _e('Appearance with Triggers', $this->plugin_name); ?></li>
                <li><?php _e('... and more.', $this->plugin_name); ?></li>
            </ul>
            
            <p>
            <?php _e('WP Terms Popup Designer integrates directly into the free plugin. After purchase and installation, this tab will be replaced with the Designer plugin.', $this->plugin_name); ?>
            </p>
            
            <p>
            <a class="wptp-button" target="_blank" href="https://termsplugin.com/designer?utm_source=plugin&utm_content=add_on_tab_designer"><?php _e('Learn About WP Terms Popup Designer', $this->plugin_name); ?></a>
            </p>
        </div>
	</div>
	<?php endif; ?>
	
    <?php
        if (is_plugin_active('wp-terms-popup-designer/index.php')) {
            wptp_designer_settings();
        }

        if (is_plugin_active('wp-terms-popup-pro/index.php') && !is_plugin_active('wp-terms-popup-designer/index.php')) {
            wtp_popupProSettingsPage();
        }
    ?>

    <?php include 'wp-terms-popup-admin-footer.php'; ?>
</div>