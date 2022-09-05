<?php
namespace apjPLR;
if (!defined('ABSPATH'))
{
    die('-1');
}
/**
 * @package Prevent Landscape Rotation
 * @version 2.0
 * @since 1.0
 */
class PreventLandscapeRotation
{
    /**
     * Plugin activation
     * @return void
     */
    public static function activate()
    {
        self::checkRequirements();
    }
    /**
     * Plugin uninstall
     * @return void
     */
    public static function uninstall()
    {
        self::apjuninstallplugin();
    }
    /**
     * Check plugin requirements
     * @return void
     */
    private static function checkRequirements()
    {

    }
    /**
     * Uninstall plugin
     * @return void
     */
    private static function apjuninstallplugin()
    {
        delete_option(APJ_PLR_OPT_ERR_NAME);
        delete_option(APJ_PLR_OPT_MESSAGE);
        delete_option(APJ_PLR_OPT_BG_COLOR_CODE);
        delete_option(APJ_PLR_OPT_TXT_COLOR_CODE);
    }
    /**
     * Initialize WordPress hooks
     * @return void
     */
    public static function initHooks()
    {

        //Admin notices
        add_action('admin_notices', array(
            'apjPLR\PreventLandscapeRotation',
            'adminNotices'
        ));
        //Admin menu
        add_action('admin_menu', array(
            'apjPLR\PreventLandscapeRotation',
            'adminMenu'
        ));

        add_action('wp_footer', array(
            'apjPLR\PreventLandscapeRotation',
            'APJPreventLandscapeRotationPublish'
        ));

        add_action('wp_head', array(
            'apjPLR\PreventLandscapeRotation',
            'APJStyles'
        ));

        add_action('admin_head', array(
            'apjPLR\PreventLandscapeRotation',
            'APJAdminStyles'
        ));

        add_filter("plugin_action_links", array(
            'apjPLR\PreventLandscapeRotation',
            'PluginActionLinks'
        ) , 1, 2);

        add_filter("plugin_row_meta", array(
            'apjPLR\PreventLandscapeRotation',
            'PluginRowMeta'
        ) , 1, 2);
        //Admin page
        $page = filter_input(INPUT_GET, 'page');
        if (!empty($page) && $page == APJ_PLR_MENU_SLUG)
        {
            add_filter('admin_footer_text', array(
                'apjPLR\PreventLandscapeRotation',
                'adminFooter'
            ));
        }
    }
    /**
     * Admin notices
     * @return void
     */
    public static function adminNotices()
    {
        if (get_option(APJ_PLR_OPT_ERR_NAME))
        {
            $class   = 'notice notice-error';
            $message = stripslashes_deep(esc_attr(get_option(APJ_PLR_OPT_ERR_NAME)));
            printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
        }
    }
    /**
     * APJPreventLandscapeRotationPublish
     * @return string
     */
    public static function APJPreventLandscapeRotationPublish()
    {
        $apj_plr_default_message = esc_html(APJ_PLR_DEFAULT_MESSAGE, 'prevent-landscape-rotation');
        $plr_message_show        = stripslashes_deep(esc_attr(get_option(APJ_PLR_OPT_MESSAGE)));
        $plr_bg_clr              = stripslashes_deep(esc_attr(get_option(APJ_PLR_OPT_BG_COLOR_CODE)));
        $plr_txt_clr             = stripslashes_deep(esc_attr(get_option(APJ_PLR_OPT_TXT_COLOR_CODE)));
        global $c_message;
        wp_enqueue_script( 'prevent-landscape-rotation', APJ_PLR_PLUGIN_JS_PATH . 'sweetalert.min.js', array('jquery'), false, true );
        if ( ! wp_script_is( 'jquery', 'done' ) ) {
         wp_enqueue_script( 'jquery' );
       }
	   //Mujahid code to disable for tabs
       wp_add_inline_script( 'prevent-landscape-rotation', 'const userAgent = navigator.userAgent.toLowerCase();
const isTablet = /(ipad|tablet|(android(?!.*mobile))|(windows(?!.*phone)(.*touch))|kindle|playbook|silk|(puffin(?!.*(IP|AP|WP))))/.test(userAgent);
function isLandscape() {return (window.orientation === 90 || window.orientation === -90);}jQuery(document).ready(function() {APJorientationChangeFnc();});jQuery(window).on(\'orientationchange\', function(event) {if (isLandscape() && !isTablet) {APJorientationChangeFnc();}else{swal.close();}});function APJorientationChangeFnc() {if (isLandscape() && !isTablet) {const wrapper = document.createElement(\'div\'); wrapper.innerHTML = "<img class=\'swal2-image apjnolandscapeimage\' src=\''.APJ_PLR_PLUGIN_IMAGES_PATH.'landscape-pic.png\' style=\'width: 30%;margin-left: auto;margin-right: auto;height: 84px;margin-bottom: 8px;display: block;\'><p style=\'color:' . (empty($plr_txt_clr) ? '#000000' : $plr_txt_clr) . '\'><br>' . (empty($plr_message_show) ? $apj_plr_default_message : $plr_message_show) . '</p>";swal({content: wrapper,allowOutsideClick: false,closeOnClickOutside: false,closeOnEsc: false,});jQuery(".swal-button--confirm").css("display","none");jQuery(\'.swal-overlay\').css(\'background-color\', \'' . (empty($plr_bg_clr) ? 'rgba(216, 216, 216, 0.94)' : $plr_bg_clr) . '\');jQuery(\'.swal-modal\').css(\'background-color\', \'rgba(0, 174, 83, 0)\');}else{swal({timer: 2,className: "apjtempswal",});swal.close();}}' );
     }
    /**
     * Css rules for temp swal
     * @return void
     */
    public static function APJStyles()
    {
        echo "<style>.apjtempswal{display:none!important;}</style>";
    }
    /**
     * Css rules for admin page
     * @return void
     */
    public static function APJAdminStyles()
    {
        echo "<style>.apjotherplugins ul{display: flex;}.apjotherplugins li{margin-right: 20px;}</style>";
    }
    /**
     * Admin menu
     * @return void
     */
    public static function adminMenu()
    {
        add_options_page('Prevent Landscape Rotation Settings', 'Prevent Landscape Rotation', 'manage_options', plugin_dir_path(__FILE__) . 'admin/adminpage.php');
    }
    /**
     * Admin footer
     * @return void
     */
    public static function adminFooter()
    {
?>
        <div class="apjotherplugins"><h4>Check out my other plugins</h4><ul><li><a href="https://wordpress.org/plugins/wp-php-version-display/" target="_blank">WP PHP Version Display</a></li><li><a href="https://wordpress.org/plugins/hide-front-end-wp-admin-bar/" target="_blank">Hide Front End WP Admin Bar</a></li><li><a href="https://wordpress.org/plugins/wp-quick-post-duplicator/" target="_blank">WP Quick Post Duplicator</a></li><li><a href="https://wordpress.org/plugins/wp-version-tag-remover/" target="_blank">WP Version Tag Remover</a></li></ul></div><p><a href="https://wordpress.org/support/plugin/prevent-landscape-rotation/reviews/#new-post" class="apj-review-link" target="_blank"><?php echo sprintf(__('If you like <strong> %s </strong> please leave us a &#9733;&#9733;&#9733;&#9733;&#9733; rating.', 'apjMC') , APJ_PLR_PLUGIN_NAME); ?></a>  <?php _e('Thank you.', 'apjMC'); ?></p>
<?php
    }
    /**
     * Plugin row meta/action links
     * @return void
     */
    public static function PluginRowMeta($links_array, $plugin_file_name)
    {
        if (strpos($plugin_file_name, APJ_PLR_PLUGIN_PATH)) $links_array = array_merge($links_array, array(
            '<a target="_blank" href="https://paypal.me/arulprasadj?locale.x=en_GB"><span style="font-size: 20px; height: 20px; width: 20px;" class="dashicons dashicons-heart"></span>Donate</a>'
        ));
        return $links_array;
    }
    public static function PluginActionLinks($links_array, $plugin_file_name)
    {
        if (strpos($plugin_file_name, APJ_PLR_PLUGIN_PATH)) $links_array = array_merge(array(
            '<a href="' . admin_url('admin.php?page='.APJ_PLR_MENU_SLUG.'') . '">Settings</a>'
        ) , $links_array);
        return $links_array;
    }
}

