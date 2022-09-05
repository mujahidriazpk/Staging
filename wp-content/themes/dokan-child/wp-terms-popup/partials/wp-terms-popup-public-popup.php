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
<?php
//Mujahid Code
global $wpdb;
$post_date1 = $wpdb->get_var("SELECT post_modified_gmt FROM wp_posts WHERE ID = 56 LIMIT 1");
$post_date2 = $wpdb->get_var("SELECT post_modified_gmt FROM wp_posts WHERE ID = 60 LIMIT 1");
$post_date3 = $wpdb->get_var("SELECT post_modified_gmt FROM wp_posts WHERE ID = 62 LIMIT 1");

$current_user = wp_get_current_user();
$user_date = date("Y-m-d H:i:s",strtotime($current_user->user_registered));
//echo "i am here==".$user_date."==".$post_date3."<br />";
//echo 'mujahidtermpopup_'.$current_user->ID;
$termpopup = get_option('termpopup_'.$current_user->ID);
if ($termpopup=='' && $current_user->roles[0] !='advanced_ads_user' && $current_user->roles[0] !='ad_demo' && !is_super_admin($current_user->ID) && $current_user->ID !='' && ($user_date < $post_date1 || $user_date < $post_date2 || $user_date < $post_date3)) : ?>
<div id="wptp-css"><?php echo(get_option('termsopt_javascript') <> 1 ? $this->popup_css() : ''); ?></div>
<div id="tfade" class="tdarkoverlay"></div>
<div id="tlight" class="tbrightcontent">
	<div id="wptp-container" class="termspopupcontainer">
        <h3 class="termstitle"><?php echo(get_option('termsopt_javascript') <> 1 ? $this->title($termspageid) : '&nbsp;'); ?></h3>
		<div class="termscontentwrapper">
            <div id="wp-terms-popup-content"><?php echo(get_option('termsopt_javascript') <> 1 ? $this->content($termspageid) : ''); ?></div>
            <div id="wp-terms-popup-after-content"><?php do_action('wptp_popup_after_content', $termspageid); ?></div>
        </div>
	</div>
</div>
<?php endif; ?>