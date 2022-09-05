<?php
if (!defined('ABSPATH'))
{
    die('-1');
}
/**
 * @package Prevent Landscape Rotation
 * @version 2.0
 * @since 1.0
 */
if (!current_user_can('manage_options'))
{
    wp_die(__('You do not have sufficient permissions to access this page.'));
}
?>
<div class="wrap">
<h1><?php echo esc_html('Prevent Landscape Rotation Settings', 'prevent-landscape-rotation'); ?></h1>
<?php
$apj_plr_default_message = APJ_PLR_DEFAULT_MESSAGE;
$apj_plr_message         = APJ_PLR_OPT_MESSAGE;
$apj_plr_bg_clr_code     = APJ_PLR_OPT_BG_COLOR_CODE;
$apj_plr_txt_clr_code    = APJ_PLR_OPT_TXT_COLOR_CODE;
if (isset($_POST["submit"]) && $_POST['action'] == 'apj_update_general')
{
    $plr_message_show    = trim($_POST[$apj_plr_message]);
    $plr_message_show    = strip_tags( stripslashes($plr_message_show));
    $plr_message_show    = sanitize_text_field($plr_message_show);
    $plr_bg_clr          = trim($_POST[$apj_plr_bg_clr_code]);
    $plr_bg_clr          = strip_tags( stripslashes($plr_bg_clr));
    $plr_bg_clr          = sanitize_text_field($plr_bg_clr);
    $plr_txt_clr         = trim($_POST[$apj_plr_txt_clr_code]);
    $plr_txt_clr         = strip_tags( stripslashes($plr_txt_clr));
    $plr_txt_clr         = sanitize_text_field($plr_txt_clr);
    update_option($apj_plr_message, $plr_message_show);
    update_option($apj_plr_bg_clr_code, $plr_bg_clr);
    update_option($apj_plr_txt_clr_code, $plr_txt_clr);
    echo '<div id="message" class="updated fade"><p>Settings saved.</p></div>';
}
else
{
    $plr_message_show = get_option($apj_plr_message);
    $plr_bg_clr       = get_option($apj_plr_bg_clr_code);
    $plr_txt_clr      = get_option($apj_plr_txt_clr_code);
}
?>
<div>
    <fieldset>
        <form method="post" action=""> 
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><label for="<?php echo $apj_plr_message; ?>">Enter New Message :</label></th>
                        <td>
                            <textarea id="<?php echo $apj_plr_message; ?>" name="<?php echo $apj_plr_message; ?>" class="regular-text" rows="3" required><?php echo (empty($plr_message_show) ? esc_attr($apj_plr_default_message) : esc_attr($plr_message_show)); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="<?php echo $apj_plr_bg_clr_code; ?>">Enter Background Color Code :</label></th>
                        <td>
                            <input type="text" value="<?php echo (empty($plr_bg_clr) ? 'rgba(216, 216, 216, 0.94)' : esc_attr($plr_bg_clr)); ?>" id="<?php echo $apj_plr_bg_clr_code; ?>" name="<?php echo $apj_plr_bg_clr_code; ?>" class="my-color-field" data-alpha-enabled="true" data-default-color="rgba(216, 216, 216, 0.94)" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="<?php echo $apj_plr_txt_clr_code; ?>">Enter Text Color Code :</label></th>
                        <td>
                            <input type="text" value="<?php echo (empty($plr_txt_clr) ? '#000000' : esc_attr($plr_txt_clr)); ?>" id="<?php echo $apj_plr_txt_clr_code; ?>" name="<?php echo $apj_plr_txt_clr_code; ?>" class="my-color-field" data-default-color="#000000" />
                        </td>
                    </tr>
                </tbody></table>
                <p class="submit"><input type="hidden" name="action" value="apj_update_general" /><input type="submit" value="Save Changes" class="button button-primary" name="submit" /></p>
            </form>
        </fieldset>        
    </div>

    
</div>
<?php
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker-alpha', plugins_url( '../assets/js/wp-color-picker-alpha.min.js',  __FILE__ ), array( 'wp-color-picker' ), '3.0.0', true );
    wp_enqueue_script( 'wp-color-picker-init',  plugins_url( '../assets/js/wp-color-picker-script.js',  __FILE__ ), array( 'wp-color-picker-alpha' ), '3.0.0', true );
?>
