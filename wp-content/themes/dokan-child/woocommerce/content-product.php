<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;
global $product,$total_auction,$Distance;
$flag = 'show';
if (is_user_logged_in()){
	$user_id = dokan_get_current_user_id();
	$auction_street = get_post_meta($product->id , 'auction_street' , TRUE);
	$auction_apt_no = get_post_meta($product->id , 'auction_apt_no' , TRUE);
	$auction_city = get_post_meta($product->id , 'auction_city' , TRUE);
	$auction_state = get_post_meta($product->id , 'auction_state' , TRUE);
	$auction_zip_code = get_post_meta($product->id , 'auction_zip_code' , TRUE);
	$auction_address = $auction_street." ".$auction_apt_no." ".$auction_city." ".$auction_state." ".$auction_zip_code;
	
	$dentist_office_street = get_user_meta( $user_id, 'dentist_office_street', true);
	$dentist_office_apt_no = get_user_meta( $user_id, 'dentist_office_apt_no', true);
	$dentist_office_city = get_user_meta( $user_id, 'dentist_office_city', true);
	$dentist_office_state = get_user_meta( $user_id, 'dentist_office_state', true);
	$dentist_office_zip_code = get_user_meta( $user_id, 'dentist_office_zip_code', true);
	$dentist_office_address = $dentist_office_street." ".$dentist_office_apt_no." ".$dentist_office_city." ".$dentist_office_state." ".$dentist_office_zip_code;
	if(trim($dentist_office_address) !="" && trim($auction_address) !=""){
		$Distance = get_driving_information($dentist_office_address,$auction_address);
		if($Distance <= 50){
			$flag = 'show';
		}else{
			$flag = 'hide';
		}
	}else{
		$flag = 'show';
	}
}else{
	$auction_street = get_post_meta($product->id , 'auction_street' , TRUE);
	$auction_apt_no = get_post_meta($product->id , 'auction_apt_no' , TRUE);
	$auction_city = get_post_meta($product->id , 'auction_city' , TRUE);
	$auction_state = get_post_meta($product->id , 'auction_state' , TRUE);
	$auction_zip_code = get_post_meta($product->id , 'auction_zip_code' , TRUE);
	$auction_address = $auction_street." ".$auction_apt_no." ".$auction_city." ".$auction_state." ".$auction_zip_code;
	
	$user_ip = getenv('REMOTE_ADDR');
	$pageurl = "http://www.geoplugin.net/php.gp?ip=$user_ip";
	$cURL=curl_init();
	curl_setopt($cURL,CURLOPT_URL,$pageurl);
	curl_setopt($cURL,CURLOPT_RETURNTRANSFER, TRUE);
	$geo=unserialize(trim(curl_exec($cURL)));
	curl_close($cURL);
	$country = $geo["geoplugin_countryName"];
	$city = $geo["geoplugin_city"];
	$dentist_office_address = $city." ".$country;
	if(trim($dentist_office_address) !="" && trim($auction_address) !=""){
		$Distance = get_driving_information($dentist_office_address,$auction_address);
		if($Distance <= 50){
			$flag = 'show';
		}else{
			$flag = 'hide';
		}
	}else{
		$flag = 'show';
	}
}
// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible()  || $flag=='hide' ) {
	return;
}
$total_auction ++;
?>
<li <?php wc_product_class(); ?> >
	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item' );

	/**
	 * Hook: woocommerce_before_shop_loop_item_title.
	 *
	 * @hooked woocommerce_show_product_loop_sale_flash - 10
	 * @hooked woocommerce_template_loop_product_thumbnail - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_product_title - 10
	 */
	do_action( 'woocommerce_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_after_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_rating - 5
	 * @hooked woocommerce_template_loop_price - 10
	 */
	do_action( 'woocommerce_after_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_after_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_close - 5
	 * @hooked woocommerce_template_loop_add_to_cart - 10
	 */
	do_action( 'woocommerce_after_shop_loop_item' );
	?>
</li>
