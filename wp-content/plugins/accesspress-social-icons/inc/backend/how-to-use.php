<?php defined('ABSPATH') or die("No script kiddies please!"); ?>
<div class="wrap aps-clear">
    <div class="aps-add-set-wrapper">
        <div class="aps-panel">
            <?php include('panel-head.php'); ?>
            <div class="aps-panel-body">
                <h2><?php _e('How to use', 'accesspress-social-icons'); ?></h2>
                <p>For full <strong>documentation</strong> on the plugin, please visit <a href="https://accesspressthemes.com/documentation/accesspress-social-icons/" target="_blank">here</a></p>
                <p><?php esc_html_e('For displaying the icons in the frontend, you need to build the icon set first.You can build unlimited number of icons sets which will generate the shortcode for each icons set.And also the build icon sets will be available in the widget section too.', 'accesspress-social-icons'); ?>
                </p>
                <p><?php esc_html_e('For building icons sets, currently there are two methods.Either you build your own set or build the sets using pre available icon themes.', 'accesspress-social-icons'); ?>
                </p>
                <h3>Building Own Icon Sets</h3>
                <p><?php esc_html_e('For building own icons sets, you can add the icons individually in the sets by either choosing  from pre available icons or by uploading your own icons.The advantage of building own icon set is that if there are some icons missing in the icons themes that you want then , you can build your own sets by selecting the available icons and also uploading the unavailable icons and adding that icon to your icon set.', 'accesspress-social-icons'); ?></p>
                <p><?php esc_html_e('While building your own icon sets, you can choose various styling options such as width, height, border styling options, shadow styling options.Some fields are a bit technical, but you donot need to worry to use them because we have got the live preview of those styles so that it will be easier for you to understand and know how icons is going to be displayed in the frontend.', 'accesspress-social-icons'); ?></p>
                <h3>Building Icons Sets from Pre Available Themes</h3>
                <p><?php esc_html_e('You can choose this method if you want to build the icon sets quickly. You just need to select the desired icons theme, then add necessary data for desired icons from the list such as icon title, height, width , link , tooltip text  and link target.Though there will be 20 or more icons in the list but only those icons will show in the frontend in which you have placed the icon link. ', 'accesspress-social-icons'); ?></p>

                <h3>Other Options</h3>
                <p><?php esc_html_e('There are the options to set margin between each icons, animation for icons , set the icons display position as vertical or horizontal. If vertical then you can set number or columns and if horizontal then you can set number of rows to display the icons.', 'accesspress-social-icons'); ?></p>
                <p><?php esc_html_e('You can also enable or disable the tooltip option for each icon.If you have enabled the tooltip then you can set the tooltip text and background color too as per your theme.', 'accesspress-social-icons'); ?></p>
                <p><?php esc_html_e('You can also set the opacity of the non hovered icons i.e how much transparent when your icons is non hovered.', 'accesspress-social-icons'); ?></p>
            </div>
        </div>
    </div>
    <?php include_once('promobar.php'); ?>
</div>
