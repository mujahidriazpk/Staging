<?php 
add_action('admin_notices','widgetkit_for_elemetor_admin_notice');
add_action('admin_init','widgetkit_for_elemetor_dismiss_admin_notice');


function widgetkit_for_elemetor_admin_notice(){
	//delete_option('notice_dissmissed');
	if (get_option("wk-thank-you-notice")) {
		return;
	}
	$notice_container = 
	<<<EOD
	<div class="wk-thank-you-notice notice is-dismissible %s uniqueclass">
	<p>%s</p>
	</div>
EOD;
	$notice = "Thanks for using WidgetKit";
	printf($notice_container, "notice-info", $notice);

}


function widgetkit_for_elemetor_dismiss_admin_notice(){
	if( isset($_GET['dismissed']) && $_GET['dismissed'] == 1 ){
		update_option('wk-thank-you-notice', 1);
	}
}
