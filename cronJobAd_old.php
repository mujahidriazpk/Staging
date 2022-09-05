<?php
define('WP_USE_THEMES', true);
require('/home/shopadoc/public_html/wp-load.php');
/*$args = array(
				'post_type'             => 'advanced_ads',
				'post_status'         => array('publish','pending'),
				'posts_per_page'      => -1,
				//'auction_archive'     => TRUE,
				//'show_past_auctions'  => TRUE,
              );
$query = new WP_Query($args );
$posts = $query->posts;*/
function change_weight(){
		
		global $wpdb;
		$weights = get_option('advads-ad-weights', array() );
		$weights_array = $weights;
		foreach($weights as $key=>$val){
			ksort($val);
			foreach($val as $post_id=> $weight){
				$weights_array[$key][$post_id] = 0;
			}
		}
		foreach($weights as $key=>$val){
			$flag = 0;
			$foundPost = array();
			ksort($val);
			foreach($val as $post_id=> $weight){
				$my_part_ID = $wpdb->get_var($wpdb->prepare("SELECT ID FROM wp_posts WHERE ID = %d AND display = %d LIMIT 1",$post_id,1));
				if ( $my_part_ID > 0 ){
					array_push($foundPost,$post_id);
				}else{
					$flag = 1;
					$weights_array[$key][$post_id] = 10;
					$wpdb->query($wpdb->prepare("UPDATE wp_posts SET display = 1 WHERE ID=".$post_id));
					break;
				}
			}
			if($flag==0){
				$wpdb->query($wpdb->prepare("UPDATE wp_posts SET display = 0 WHERE ID in ('".implode("','",$foundPost)."')"));
				ksort($val);
				foreach($val as $post_id=> $weight){
					$my_part_ID = $wpdb->get_var($wpdb->prepare("SELECT ID FROM wp_posts WHERE ID = %d AND display = %d LIMIT 1",$post_id,1));
					if ( $my_part_ID > 0 ){
						array_push($foundPost,$post_id);
					}else{
						$flag = 1;
						$weights_array[$key][$post_id] = 10;
						$wpdb->query($wpdb->prepare("UPDATE wp_posts SET display = 1 WHERE ID=".$post_id));
						break;
					}
					
					$i++;
				}
			}
		}
		//print_r($weights_array);
		update_option('advads-ad-weights', $weights_array );
}
//change_weight();
for($i=0; $i <= 10 ; $i++){
	change_weight();
	sleep(6);
}
?>