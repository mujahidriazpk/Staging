<?php
/**
 * HTML for popup buttons.
 *
 * @link       https://linksoftwarellc.com
 * @since      2.0.0
 *
 * @package    Wp_Terms_Popup
 * @subpackage Wp_Terms_Popup/public/partials
 */
?>
<form id="wptp-form" method="post">
    <?php if (get_option('termsopt_sitewide') <> 1 && isset($termspageid)) : ?>
    <input type="hidden" name="wptp_popup_id" value="<?php echo esc_html($termspageid); ?>" />
    <?php endif; ?>
    <div class="tthebutton">
        <input class="termsagree" name="wptp_agree" type="submit" value="<?php echo esc_html($terms_agreetxt); ?>" />
        <input class="termsdecline" type="button" onclick="window.location.replace('<?php echo esc_url($terms_redirecturl); ?>')" value="<?php echo esc_html($terms_disagreetxt); ?>" />
    </div>
</form>
