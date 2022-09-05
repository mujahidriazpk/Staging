<?php
define('WP_USE_THEMES', true);
require('/home/642855.cloudwaysapps.com/gddwwykpfm/public_html/wp-load.php');
global $wpdb,$demo_listing;
$args_user = array('role'    => 'advanced_ads_user','orderby' => 'user_nicename','order'   => 'ASC');
$users = get_users( $args_user );
foreach($users as $user){
$args = array(
				'post__not_in' => array($demo_listing),
				'post_status'         => array('publish'),
				'posts_per_page'      => -1,
				'post_type'     => 'advanced_ads',
				//Custom Field Parameters
				'meta_key'       => 'ad_user',
				'meta_value'     => $user->ID,
				'meta_compare'   => '=',
				//'tax_query'           => array( array( 'taxonomy' => 'advanced_ads', 'field' => 'slug', 'terms' => 'auction' ) ),
				//'meta_query' => array(array('key' => '_auction_closed','operator' => 'EXISTS',)),
				//'show_past_auctions'  => TRUE,
              );
			$query = new WP_Query($args );
			$posts = $query->posts;
			$ads_status = array();
			foreach($posts as $post){
				//print_r($post);
				$ad_start_date = $post->post_date_gmt;
				$advanced_ads_ad_options = maybe_unserialize(get_post_meta($post->ID,'advanced_ads_ad_options',TRUE));
				$expiry_date = date("Y-m-d H:i:s",$advanced_ads_ad_options['expiry_date']);
				if($advanced_ads_ad_options['expiry_date'] >0){
					if(strtotime(date('Y-m-d H:i:s')) > $advanced_ads_ad_options['expiry_date'] ){
						$date1=date_create($expiry_date);
						$date2=date_create(date('Y-m-d H:i:s'));
						$diff=date_diff($date1,$date2);
						if($diff->format("%a") > 1){
							array_push($ads_status,'expire');
							/*echo $diff->format("%a")."==";
							echo $user->ID."==".$ad_start_date."==".$expiry_date."<br />";*/
						}
					}else{
						/*$date1=date_create($expiry_date);
						$date2=date_create(date('Y-m-d H:i:s'));
						$diff=date_diff($date1,$date2);
						echo $diff->format("%a")."==";
						echo $user->ID."==".$ad_start_date."==".$expiry_date."<br />";*/
						array_push($ads_status,"active");
					}
				}
			}
			if(!in_array("active",$ads_status) && !empty($ads_status)){
				update_user_meta($user->ID, 'deactivate_advertiser','Yes');
			}
}
?>