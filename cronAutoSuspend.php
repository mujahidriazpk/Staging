<?php
define('WP_USE_THEMES', true);
require('/home/642855.cloudwaysapps.com/gddwwykpfm/public_html/wp-load.php');
global $wpdb,$demo_listing,$today_date_time_seconds;
//$today_date_time_seconds = '2025-07-20 07:28:10';
$args_user = array('role'    => 'customer','orderby' => 'user_nicename','order'   => 'ASC');
$users = get_users( $args_user );
foreach($users as $user){
	$data = get_user_meta($user->ID);
	if($data['register_sub_end_date'][0] !=''){
		$register_sub_end_date = date('Y-m-d H:i:s',$data['register_sub_end_date'][0]);
		//echo $user->ID.'=='.$register_sub_end_date.'<br />';
		if($data['deactivate_CD'][0]=='No' || $data['deactivate_CD'][0] ==''){
			if(strtotime($today_date_time_seconds) >= $data['register_sub_end_date'][0]){
				//
				update_user_meta($user->ID, 'deactivate_CD','Yes');
				$SuspendReason = get_user_meta($user->ID, 'SuspendReason', true ); 
				$NewSuspendReason  = date('m/d/y').' / exp reg.';
				if($SuspendReason!=''){
					update_user_meta($user->ID, 'SuspendReason',$SuspendReason.'&#13;&#10;'.$NewSuspendReason);
					
				}else{
					update_user_meta($user->ID, 'SuspendReason',$NewSuspendReason);
				}
				$data = array('user_id' =>$user->ID, 'log_data' =>$NewSuspendReason, 'status' =>1,);
				$format = array('%d','%s','%d');
				$wpdb->insert('wp_user_CD_log',$data,$format);
			}
		}
	}
}