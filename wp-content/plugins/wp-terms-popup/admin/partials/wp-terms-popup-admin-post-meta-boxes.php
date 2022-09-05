<?php
/**
 * HTML for post meta boxes.
 *
 * @link       https://linksoftwarellc.com
 * @since      2.0.0
 *
 * @package    Wp_Terms_Popup
 * @subpackage Wp_Terms_Popup/admin/partials
 */
?>
<?php if (get_option('termsopt_sitewide') == 1) : ?>
<?php wp_nonce_field('post-meta-boxes', 'terms_disablepopup_nonce'); ?>
<table>
    <tbody>
        <tr>
            <td>
                <p>
                <?php _e('You are currently using a sitewide popup. Check the box below to disable that popup from showing on this page.', $this->plugin_name); ?>
                </p>
                <label for="terms_disablepop">
                    <input type="checkbox" id="terms_disablepop" name="terms_disablepop" value="1" <?php checked('1', get_post_meta($object->ID, 'terms_disablepop', true)); ?>>
                    <span><?php _e('Disable Popup?', $this->plugin_name); ?></span>
                </label>
            </td>
        </tr>
    </tbody>
</table>
<?php else : ?>
<?php wp_nonce_field('post-meta-boxes', 'terms_enablepopup_nonce'); ?>
<table>
    <tbody>
        <tr>
            <td>
                <label for="terms_enablepop">
                    <input type="checkbox" id="terms_enablepop" name="terms_enablepop" value="1" <?php checked('1', get_post_meta($object->ID, 'terms_enablepop', true)); ?>>
                    <span><?php _e('Enable Popup?', $this->plugin_name); ?></span>
                </label>
            </td>
        </tr>
        
        <tr>
            <td><?php _e('Terms Popup to Show on this Post:', $this->plugin_name); ?></td>
        </tr>
        
        <tr>
            <td>
                <?php
                    if ((wp_dropdown_pages('name=termsopt_page&post_type=termpopup&echo=0')) == '') {
                        printf(__('Please <a href="%s">create your first Terms Popup</a> to proceed.', $this->plugin_name), esc_url('post-new.php?post_type=termpopup'));
                    } else {
                        $isselected = get_post_meta($object->ID, 'terms_selectedterms', true);
                        wp_dropdown_pages('name=terms_selectedterms&post_type=termpopup&show_option_none='.__('- Select -').'&selected='.$isselected);
                    }
                ?>
            </td>
        </tr>
    </tbody>
</table>
<?php endif; ?>