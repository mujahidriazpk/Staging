<?php
/**
 * HTML for post type meta boxes.
 *
 * @link       https://linksoftwarellc.com
 * @since      2.0.0
 *
 * @package    Wp_Terms_Popup
 * @subpackage Wp_Terms_Popup/admin/partials
 */
?>
<?php wp_nonce_field('post-type-meta-boxes', 'terms_popupmeta_nonce'); ?>
<table class="wptp-meta-box">
    <tbody>
        <tr>
            <td><?php _e('Agree Button Text', $this->plugin_name); ?></td>
            <td><input type="text" name="terms_agreetxt" size="20" value="<?php echo $meta_terms_agreetxt; ?>"></td>
        </tr>
        
        <tr>
            <td><?php _e('Decline Button Text', $this->plugin_name); ?></td>
            <td><input type="text" name="terms_disagreetxt" size="20" value="<?php echo $meta_terms_disagreetxt; ?>"></td>
        </tr>
        
        <tr class="has-help">
            <td><?php _e('Decline URL Redirect', $this->plugin_name); ?></td>
            <td>
                <input type="text" name="terms_redirecturl" size="45" value="<?php echo $meta_terms_redirecturl; ?>"><br>
                <small><?php _e('This URL is the website users will be sent to if they click the Decline button.', $this->plugin_name); ?></small>
            </td>
        </tr>

        <tr class="has-help">
            <td><?php _e('Buttons Always Visible?', $this->plugin_name); ?></td>
            <td>
                <input type="checkbox" id="terms_buttons_always_visible" name="terms_buttons_always_visible" value="1" <?php checked('1', (isset($meta_terms_buttons_always_visible) ? $meta_terms_buttons_always_visible : 0)); ?>><br>
                <small><?php _e('Turning this option on will show the buttons without having to scroll.', $this->plugin_name); ?></small>
            </td>
        </tr>
    </tbody>
</table>