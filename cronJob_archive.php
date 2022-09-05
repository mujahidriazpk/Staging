<?php
define('WP_USE_THEMES', true);
require('/home/642855.cloudwaysapps.com/gddwwykpfm/public_html/wp-load.php');
global $wpdb;
$args = array(
			'post_status'         => array('publish','pending'),
				'posts_per_page'      => -1,
				'tax_query'           => array( array( 'taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction' ) ),
				'meta_query' => array(array('key' => '_auction_closed','operator' => 'EXISTS',)),
				//'auction_archive'     => TRUE,
				//'show_past_auctions'  => TRUE,
              );
		$query = new WP_Query($args );
		$posts = $query->posts;
foreach($posts as $post) {
    // Do your stuff, e.g.
    //echo $post->ID."<br />";
	//_auction_dates_to
	/*$auction_dates_to = get_post_meta($post->ID, '_auction_dates_to', true );
	$date1=date_create(date('Y-m-d',strtotime($auction_dates_to)));
	$date2=date_create(date('Y-m-d'));
	$diff=date_diff($date1,$date2);
	//echo $post->ID."==".$diff->format("%R%a days")."<br />";
	if((int) $diff->format("%a") > 29){
		//echo $post->ID."==".$diff->format("%R%a days")."<br />";
	}*/
	//if($post->ID == 1192){
		$my_post = array('ID' =>$post->ID,'post_status'   => 'private',);
		wp_update_post( $my_post );
		/*$_thumbnail_id = get_post_meta($post->ID, '_thumbnail_id', true );
		$_product_image_gallery = get_post_meta($post->ID, '_product_image_gallery', true );
		$images = array();
		if($_product_image_gallery !=""){
			$images = explode(",",$_product_image_gallery);
		}
		array_push($images,$_thumbnail_id);
		foreach($images as $image){
			wp_delete_attachment($image,true);
		}
		wp_delete_post($post->ID, true);*/
	//}
}
?>