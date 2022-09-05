<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://linksoftwarellc.com
 * @since      2.0.0
 *
 * @package    Wp_Terms_Popup
 * @subpackage Wp_Terms_Popup/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wp_Terms_Popup
 * @subpackage Wp_Terms_Popup/public
 * @author     Link Software LLC <support@linksoftwarellc.com>
 */
class Wp_Terms_Popup_Public
{
    /**
     * The ID of this plugin.
     *
     * @since    2.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    2.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    2.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    2.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__).'css/wp-terms-popup-public.css', [], $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    2.0.0
     */
    public function enqueue_scripts()
    {
        if (get_option('termsopt_javascript') == 1) {
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__).'js/wp-terms-popup-ajaxhandler.js', ['jquery'], $this->version, false);
            wp_localize_script($this->plugin_name, 'wptp_ajax_object', ['ajaxurl' => admin_url('admin-ajax.php'), 'ajax_nonce' => wp_create_nonce('wptp-ajaxhandler-nonce')]);
        }
    }

    /**
     * Set WP Terms Popup cookie.
     *
     * @since    2.0.0
     */
    public function set_cookie()
    {
        if (isset($_POST['wptp_agree'])) {
            include_once ABSPATH.'wp-admin/includes/plugin.php';

            if (isset($_POST['wptp_popup_id']) && is_numeric($_POST['wptp_popup_id'])) {
                $termspageid = sanitize_text_field($_POST['wptp_popup_id']);
            } else {
                $currentpostid = get_the_ID();

                if (get_option('termsopt_sitewide') == 1) {
                    $termspageid = get_option('termsopt_page');
                } elseif (get_option('termsopt_sitewide') <> 1) {
                    $enabled = get_post_meta($currentpostid, 'terms_enablepop', true);

                    if ($enabled == 1) {
                        $termspageid = get_post_meta($currentpostid, 'terms_selectedterms', true);
                    }
                } else {
                    return;
                }
            }

            $wptp_cookie = 'wptp_terms_'.$termspageid;

            if (get_option('termsopt_expiry') && get_option('termsopt_expiry') != '' && get_option('termsopt_expiry') > 0) {
                $sesslifetime = (get_option('termsopt_expiry')) * 60 * 60;
            } else {
                if (get_option('termsopt_expiry') != '' && get_option('termsopt_expiry') == 0) {
                    $sesslifetime = 0;
                } else {
                    $sesslifetime = 3 * 24 * 60 * 60;
                }
            }

            setcookie($wptp_cookie, 'accepted', time() + $sesslifetime, '/');

            if (is_plugin_active('wp-terms-popup-collector/index.php')) {
                wptp_collector_update_logs($termspageid);
            }
        }
    }

    /**
     * Handle CSS AJAX.
     *
     * @since    2.0.0
     */
    public function ajaxhandler_css()
    {
        // check_ajax_referer('wptp-ajaxhandler-nonce', 'wptp_nonce');

        if (!isset($_POST['wptp_nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['wptp_nonce']), 'wptp-ajaxhandler-nonce')) {
            exit();
        }

        $wptp_content['css'] = $this->popup_css();

        die(json_encode($wptp_content));
    }

    /**
     * Handle HTML AJAX.
     *
     * @since    2.0.0
     */
    public function ajaxhandler_popup()
    {
        // check_ajax_referer('wptp-ajaxhandler-nonce', 'wptp_nonce');

        if (!isset($_POST['wptp_nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['wptp_nonce']), 'wptp-ajaxhandler-nonce')) {
            exit();
        }

        $wptp_content['popup'] = $this->popup_html(sanitize_text_field($_POST['termspageid']));

        die(json_encode($wptp_content));
    }

    /**
     * Popup display check.
     *
     * @since    2.0.0
     */
    public function display_check($shortcode = null)
    {
        global $wp_customize;

        // Never show the popup on the admin-side (in Appearance, for example, under Customize or Widgets)
        if (is_admin() || isset($wp_customize)) {
            return false;
        }

        $termsopt_user_visiblity = get_option('termsopt_user_visiblity');
        $wptp_display_popup = false;

        // Handle Guests & Logged Out Users
        if (!is_user_logged_in() && ($termsopt_user_visiblity === false || (isset($termsopt_user_visiblity['guest']) && $termsopt_user_visiblity['guest'] == 1))) {
            $wptp_display_popup = true;
        }

        // Handle Logged In Users
        if (is_user_logged_in()) {
            if (get_option('termsopt_adminenabled') == 1 || isset($termsopt_user_visiblity['all-logged-in']) && $termsopt_user_visiblity['all-logged-in'] == 1) {
                $wptp_display_popup = true;
            } else {
                $user = wp_get_current_user();
                $user_roles = (array) $user->roles;

                if (!empty($termsopt_user_visiblity)) {
                    foreach ($termsopt_user_visiblity as $user_visibility_role => $user_visibility_value) {
                        if (in_array($user_visibility_role, $user_roles)) {
                            $wptp_display_popup = true;
                            continue;
                        }
                    }
                }
            }
        }

        if ($wptp_display_popup === true) {
            $this->popup($shortcode);
        } else {
            return false;
        }
    }

    /**
     * After Content.
     *
     * @since    2.0.0
     */
    public function after_content($termspageid)
    {
        // Agree Button Text
        if ((get_post_meta($termspageid, 'terms_agreetxt', true)) != '') {
            $terms_agreetxt = get_post_meta($termspageid, 'terms_agreetxt', true);
        } elseif (get_option('termsopt_agreetxt') != '') {
            $terms_agreetxt = get_option('termsopt_agreetxt');
        } else {
            $terms_agreetxt = __('I Agree', $this->plugin_name);
        }

        // Decline Button Text
        if ((get_post_meta($termspageid, 'terms_disagreetxt', true)) != '') {
            $terms_disagreetxt = get_post_meta($termspageid, 'terms_disagreetxt', true);
        } elseif (get_option('termsopt_disagreetxt') != '') {
            $terms_disagreetxt = get_option('termsopt_disagreetxt');
        } else {
            $terms_disagreetxt = __('I Do Not Agree', $this->plugin_name);
        }

        // Decline URL Redirect
        if ((get_post_meta($termspageid, 'terms_redirecturl', true)) != '') {
            $terms_redirecturl = get_post_meta($termspageid, 'terms_redirecturl', true);
        } elseif (get_option('termsopt_redirecturl') && get_option('termsopt_redirecturl') != '') {
            $terms_redirecturl = get_option('termsopt_redirecturl');
        } else {
            $terms_redirecturl = 'https://www.google.com/';
        }

        // Buttons Always Visible?
        if ((get_post_meta($termspageid, 'terms_buttons_always_visible', true)) != '') {
            $terms_buttons_always_visible = get_post_meta($termspageid, 'terms_buttons_always_visible', true);
        } elseif (get_option('termsopt_buttons_always_visible') && get_option('termsopt_buttons_always_visible') != '') {
            $terms_buttons_always_visible = get_option('termsopt_buttons_always_visible');
        } else {
            $terms_buttons_always_visible = 0;
        }

        echo '<div id="wp-terms-popup-after-content"'.($terms_buttons_always_visible == 1 ? ' class="sticky"' : '').'>';
        include 'partials/wp-terms-popup-public-popup-buttons.php';
        echo '</div>';
    }

    /**
     * Shortcode.
     *
     * @since    2.0.1
     */
    public function shortcode($atts)
    {
        extract(shortcode_atts(['id' => 0], $atts));
        $termspageid = $atts['id'];

        $this->display_check($termspageid);
    }

    /**
     * Popup CSS.
     *
     * @since    2.0.0
     */
    private function popup_css()
    {
        include_once ABSPATH.'wp-admin/includes/plugin.php';

        $wptp_css = '';

        if (is_plugin_active('wp-terms-popup-designer/index.php')) {
            ob_start();
            include str_replace('wp-terms-popup', 'wp-terms-popup-designer', plugin_dir_path(__DIR__)).'inc/wptp-designer-css.php';
            $wptp_css = ob_get_contents();
            ob_end_clean();
        } else {
            ob_start();
            include 'partials/wp-terms-popup-public-css.php';
            $wptp_css = ob_get_contents();
            ob_end_clean();
        }

        return $wptp_css;
    }

    /**
     * Popup HTML.
     *
     * @since    2.5.0
     */
    private function popup_html($terms_id)
    {
        $wptp_html = '';

        $wptp_html .= '<div id="tfade" class="tdarkoverlay"></div>';
        $wptp_html .= '<div id="tlight" id="wptp-content" class="tbrightcontent">';
        $wptp_html .= '<div id="wptp-container" class="termspopupcontainer">';
        $wptp_html .= '<h3 class="termstitle">';
        $wptp_html .= $this->title($terms_id);
        $wptp_html .= '</h3>';

        $wptp_html .= '<div class="termscontentwrapper">';
        $wptp_html .= '<div id="wp-terms-popup-content">'.$this->content($terms_id).'</div>';

        ob_start();
        do_action('wptp_popup_after_content', $terms_id);
        $wptp_html .= ob_get_contents();
        ob_end_clean();

        $wptp_html .= '</div>';
        $wptp_html .= '</div>';
        $wptp_html .= '</div>';

        return $wptp_html;
    }

    /**
     * Popup.
     *
     * @since    2.0.0
     */
    private function popup($shortcode = null)
    {
        if (isset($_POST) && !empty($_POST)) {
            return;
        }

        if (get_option('termsopt_sitewide') == 1) {
            $termspageid = get_option('termsopt_page');
            $currentpostid = get_the_ID();
            $disabled = get_post_meta($currentpostid, 'terms_disablepop', true);

            if (!$termspageid || $termspageid == '' || (!is_home() && !is_archive() && $disabled == 1)) {
                return;
            }
        } elseif (get_option('termsopt_sitewide') <> 1) {
            if ($shortcode != null) {
                $termspageid = $shortcode;
            } else {
                $currentpostid = get_the_ID();
                $enabled = get_post_meta($currentpostid, 'terms_enablepop', true);

                if ($enabled == 1) {
                    $termspageid = get_post_meta($currentpostid, 'terms_selectedterms', true);
                } elseif ($enabled <> 1) {
                    return;
                }
            }
        } else {
            return;
        }

        $wptp_cookie = 'wptp_terms_'.$termspageid;

        $wptp_collector_match_found = false;
        if (function_exists('wptp_collector_check')) {
            $wptp_collector_match_found = wptp_collector_check($termspageid);
        }
       //Mujahid code
        if (get_option('termsopt_javascript') != 1 && isset($_POST['wptp_agree']) || (isset($_COOKIE[$wptp_cookie]) && $_COOKIE[$wptp_cookie] == 'accepted') || $wptp_collector_match_found == true && 1==2) {
            // DO NOT DISPLAY POPUP, USE THIS FOR FUTURE FEATURES
        } else {
            include_once ABSPATH.'wp-admin/includes/plugin.php';

            if (is_plugin_active('wp-terms-popup-pro/index.php')) {
                include ABSPATH.'wp-content/plugins/wp-terms-popup-pro/terms-pro.php';
            } else {
              	//Mujahid code
                include ABSPATH.'wp-content/themes/dokan-child/wp-terms-popup/partials/wp-terms-popup-public-popup.php';
            }
        }
    }

    /**
     * Popup title.
     *
     * @since    2.0.0
     */
    private function title($popup_id)
    {
        $popup_title = get_the_title($popup_id);

        return $popup_title;
    }

    /**
     * Popup content.
     *
     * @since    2.0.0
     */
    private function content($popup_id)
    {
        include_once ABSPATH.'wp-admin/includes/plugin.php';

        $wptp_popup = get_post($popup_id);
        $popup_content = '';

        if ($popup_id && is_plugin_active('wp-terms-popup-collector/index.php')) {
            wptp_collector_update_results($popup_id);
        }

        if (get_option('termsopt_javascript') <> 1) {
            $popup_content = $wptp_popup->post_content;

            $popup_content = wptexturize($popup_content);
            $popup_content = convert_smilies($popup_content);
            $popup_content = convert_chars($popup_content);
            $popup_content = prepend_attachment($popup_content);
            $popup_content = do_shortcode($popup_content);
            $popup_content = shortcode_unautop($popup_content);
            $popup_content = wpautop($popup_content);
        } else {
            $popup_content = apply_filters('the_content', $wptp_popup->post_content);
        }

        return $popup_content;
    }
}
