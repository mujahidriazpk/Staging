<?php
/**
 * HTML for settings.
 *
 * @link       https://linksoftwarellc.com
 * @since      2.0.0
 *
 * @package    Wp_Terms_Popup
 * @subpackage Wp_Terms_Popup/admin/partials
 */
?>
<?php include_once ABSPATH.'wp-admin/includes/plugin.php'; ?>
<div class="wrap wptp-wrap">
	<h2>WP Terms Popup</h2>
	
	<h2 class="nav-tab-wrapper">
        <?php do_action('wptp_settings_tabs'); ?>
	</h2>

    <div class="wrap wptp-wrap" style="margin-top:30px;">
        <div class="postbox-container" style="float:right; width:30%;">
            <div class="meta-box-sortables">
                <div class="postbox">
                    <div class="inside">
                        <h2><?php _e('WP Terms Popup Help', $this->plugin_name); ?></h2>

                        <div class="wptp-alert">
                            <h3><?php _e('Caching', $this->plugin_name); ?></h3>

                            <p><?php _e('We recommend resetting or flushing your site\'s cache <b>each time</b> you change a WP Terms Popup setting.', $this->plugin_name); ?></p>
                        </div>

                        <h3><?php _e('Popups', $this->plugin_name); ?></h3>

                        <h4><?php _e('Load Popups with JavaScript', $this->plugin_name); ?></h4>
                        <p>Enable this setting if you have site caching in place. This setting is also recommended when your popups contain large amounts of text or images.</p>
                        <p><?php printf(__('<a target="new" href="%s">Learn more about how this settings works and when it is best to enable it on your website</a>.', $this->plugin_name), esc_url('https://termsplugin.com/support/load-popups-with-javascript/')); ?></p>

                        <h4><?php _e('Agreement Expiration', $this->plugin_name); ?></h4>
                        <p><?php _e('This setting controls how much time passes before a user has to agree to your popup again.', $this->plugin_name); ?></p>
                        <p><?php _e('Leaving this blank will make popups reappear after 72 hours.', $this->plugin_name); ?></p>
                        <p><?php _e('Setting this to 0 will force the popup to appear every time a page is loaded.', $this->plugin_name); ?></p>

                        <hr>

                        <h3><?php _e('Buttons', $this->plugin_name); ?></h3>

                        <h4><?php _e('Decline URL Redirect', $this->plugin_name); ?></h4>
                        <p><?php _e('This URL is the website users will be sent to if they click the Decline button.', $this->plugin_name); ?></p>

                        <h4><?php _e('Button Always Visible?', $this->plugin_name); ?></h4>
                        <p><?php _e('Turning this option on will show the buttons without having to scroll.', $this->plugin_name); ?></p>

                        <hr>

                        <?php if (!is_plugin_active('wp-terms-popup-designer/index.php')) : ?>
                        <h3><?php _e('Background', $this->plugin_name); ?></h3>

                        <h4><?php _e('Transparency', $this->plugin_name); ?></h4>
                        <p><?php _e('This setting controls the darkness of content behind the popup.', $this->plugin_name); ?></p>

                        <hr>
                        <?php endif; ?>

                        <h3><?php _e('Additional Support', $this->plugin_name); ?></h3>
                        <p>
                        <?php printf(__('Do you need more help or have a specific question about a WP Terms Popup feature? You can reach us through our <a target="_blank" href="%s">support forum</a> at WordPress.org.', $this->plugin_name), esc_url('https://wordpress.org/support/plugin/wp-terms-popup')); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div style="float: left; width:60%;">
            <form method="post">
                <?php wp_nonce_field('wptpd_settings') ?>
                
                <!-- Popups -->
                <div class="wptp-setting" style="margin-top:0;">
                    <h3><?php _e('Popups', $this->plugin_name); ?></h3>
                    
                    <table>
                        <tbody>
                            <tr>
                                <td colspan="2">
                                    <label for="termsopt_javascript">
                                        <input type="checkbox" id="termsopt_javascript" name="termsopt_javascript" value="1" <?php checked('1', esc_attr(get_option('termsopt_javascript'))); ?>>
                                        <span><?php _e('Load popups with JavaScript?', $this->plugin_name); ?></span>
                                    </label>
                                </td>
                            </tr>
                            
                            <tr>
                                <td colspan="2">
                                    <label for="termsopt_sitewide">
                                        <input type="checkbox" id="termsopt_sitewide" name="termsopt_sitewide" value="1" <?php checked('1', esc_attr(get_option('termsopt_sitewide'))); ?> onclick="if (this.checked == true) { document.getElementById('wptp_termsopt_page').style.display = 'table-row'; } else { document.getElementById('wptp_termsopt_page').style.display = 'none'; }">
                                        <span><?php _e('Show only one popup site wide?', $this->plugin_name); ?></span>
                                    </label>
                                </td>
                            </tr>
                            
                            <tr id="wptp_termsopt_page" style="<?php echo esc_attr(get_option('termsopt_sitewide')) == 1 ? 'display:table-row;' : 'display:none;' ?>">
                                <th><?php _e('Site Wide Popup', $this->plugin_name); ?></th>
                                <td>
                                    <?php if ((wp_dropdown_pages('name=termsopt_page&post_type=termpopup&echo=0')) == '') : ?>
                                    <?php printf(__('Please <a href="%s">create your first popup</a> to proceed.', $this->plugin_name), esc_url('post-new.php?post_type=termpopup')); ?>
                                    <?php else : ?>
                                    <?php wp_dropdown_pages('name=termsopt_page&post_type=termpopup&show_option_none='.__('- Select -', $this->plugin_name).'&selected='.esc_attr(get_option('termsopt_page'))); ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><?php _e('Agreement Expiration', $this->plugin_name); ?></th>
                                <td><input class="small-text" type="number" name="termsopt_expiry" min="0" max="99999" value="<?php echo esc_attr(get_option('termsopt_expiry')); ?>"> <?php _e('Hours', $this->plugin_name); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- User Visibility -->
                <div class="wptp-setting">
                    <h3><?php _e('User Visibility', $this->plugin_name); ?></h3>
                    <p>
                    <?php _e('Define who will see your popups based on their logged in status or user role.', $this->plugin_name); ?>
                    </p>

                    <p>
                    <strong><?php _e('It is highly recommended that sites using a caching plugin or service clear the cache completely after making any changes to the User Visibility setting.', $this->plugin_name); ?></strong>
                    </p>

                    <?php $termsopt_user_visiblity = get_option('termsopt_user_visiblity'); ?>
                    
                    <table>
                        <tbody>
                            <tr>
                                <td colspan="2">
                                    <label for="role-guest">
                                        <input type="checkbox" id="role-guest" name="termsopt_user_visiblity[guest]" value="1" <?php checked('1', ($termsopt_user_visiblity === false || isset($termsopt_user_visiblity['guest']) ? 1 : 0)); ?>>
                                        <span><?php _e('Guests & Logged Out Users', $this->plugin_name); ?></span>
                                    </label>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <label for="role-all-logged-in">
                                        <input type="checkbox" id="role-all-logged-in" name="termsopt_user_visiblity[all-logged-in]" value="1" <?php checked('1', (get_option('termsopt_adminenabled') == 1 || isset($termsopt_user_visiblity['all-logged-in']) ? 1 : 0)); ?>>
                                        <span><?php _e('All Logged In Users (Ignore Role)', $this->plugin_name); ?></span>
                                    </label>
                                </td>
                            </tr>

                            <?php
                                global $wp_roles;
                                $roles = apply_filters('editable_roles', $wp_roles->roles);
                            ?>
                            <?php foreach ($roles as $role_type => $role) : ?>
                            <tr>
                                <td colspan="2">
                                    <label for="role-<?php echo $role_type; ?>">
                                        <input type="checkbox" id="role-<?php echo $role_type; ?>" name="termsopt_user_visiblity[<?php echo $role_type; ?>]" value="1" <?php checked('1', (isset($termsopt_user_visiblity[$role_type]) ? 1 : 0)); ?>>
                                        <span><?php echo translate_user_role($role['name']); ?></span>
                                    </label>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Buttons -->
                <div class="wptp-setting">
                    <h3><?php _e('Buttons', $this->plugin_name); ?></h3>
                    
                    <p><?php _e('These values will be the default options for all new popups you create.', $this->plugin_name); ?></p>
                    
                    <table>
                        <tbody>
                            <tr>
                                <th><?php _e('Agree Button Text', $this->plugin_name); ?></th>
                                <td><input type="text" name="termsopt_agreetxt" size="20" value="<?php echo esc_attr(get_option('termsopt_agreetxt')); ?>"></td>
                            </tr>
                            
                            <tr>
                                <th><?php _e('Decline Button Text', $this->plugin_name); ?></th>
                                <td><input type="text" name="termsopt_disagreetxt" size="20" value="<?php echo esc_attr(get_option('termsopt_disagreetxt')); ?>"></td>
                            </tr>
                            
                            <tr>
                                <th><?php _e('Decline URL Redirect', $this->plugin_name); ?></th>
                                <td><input type="text" name="termsopt_redirecturl" size="45" value="<?php echo esc_attr(get_option('termsopt_redirecturl')); ?>"></td>
                            </tr>

                            <tr>
                                <th><?php _e('Buttons Always Visible?', $this->plugin_name); ?></th>
                                <td>
                                    <input type="checkbox" id="termsopt_buttons_always_visible" name="termsopt_buttons_always_visible" value="1" <?php checked('1', esc_attr(get_option('termsopt_buttons_always_visible', 0))); ?>><br>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php if (!is_plugin_active('wp-terms-popup-designer/index.php')) : ?>
                <!-- Background -->
                <div class="wptp-setting">
                    <h3><?php _e('Background', $this->plugin_name); ?></h3>
                    
                    <table>
                        <tbody>
                            <tr class="has-help">
                                <th><?php _e('Transparency', $this->plugin_name); ?></th>
                                <td><input class="small-text" type="number" name="termsopt_opac" min="1" max="10" value="<?php echo esc_attr(get_option('termsopt_opac')); ?>"></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><small><?php _e('1 = Light, 10 = Dark.', $this->plugin_name); ?></small></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php else : ?>
                <input type="hidden" name="termsopt_opac" value="<?php echo esc_attr(get_option('termsopt_opac')); ?>">
                <?php endif; ?>
                
                <p><input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Settings', $this->plugin_name); ?>" /></p>
            </form>
        </div>
    </div>

	<?php include 'wp-terms-popup-admin-footer.php'; ?>
</div>