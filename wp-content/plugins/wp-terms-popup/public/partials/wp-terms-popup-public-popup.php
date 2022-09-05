<?php
/**
 * HTML for popup.
 *
 * @link       https://linksoftwarellc.com
 * @since      2.0.0
 *
 * @package    Wp_Terms_Popup
 * @subpackage Wp_Terms_Popup/public/partials
 */
?>

<?php if ($termspageid) : ?>
<div id="wp-terms-popup" data-wptp-popup-id="<?php echo esc_attr($termspageid); ?>">
    <div id="wptp-css"><?php echo(get_option('termsopt_javascript') <> 1 ? $this->popup_css() : ''); ?></div>
    <div id="wptp-popup"><?php echo(get_option('termsopt_javascript') <> 1 ? $this->popup_html($termspageid) : ''); ?></div>
    </div>
</div>
<?php endif; ?>