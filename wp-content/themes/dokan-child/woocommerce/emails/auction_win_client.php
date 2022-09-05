<?php
/**
 * Email auction won
 *
 */
if (!defined('ABSPATH')) exit ; // Exit if accessed directly
global $US_state;
$product_data = wc_get_product($product_id);
$post   = get_post($product_id);
$Client = get_user_by( 'id', $post->post_author );
$client_name = $Client->first_name.' '.$Client->last_name;
$client_email = $Client->user_email;
$client_cell_ph = get_user_meta($post->post_author, 'client_cell_ph', true );

$dentist = get_user_by('email',$user_email);
$designation = get_user_meta($dentist->ID, 'designation', true );
if($designation!=""){
	$winnner_name = $dentist->first_name.' '.$dentist->last_name.", ".$designation;
}else{
	$winnner_name = $dentist->first_name.' '.$dentist->last_name;
}
$winnner_id = $dentist->ID;
$user_id = $dentist->ID;
$dentist_street = get_user_meta( $user_id, 'dentist_office_street', true);
$dentist_apt_no = get_user_meta( $user_id, 'dentist_office_apt_no', true);
$dentist_city = get_user_meta( $user_id, 'dentist_office_city', true);
$dentist_state = get_user_meta( $user_id, 'dentist_office_state', true);
$dentist_zip_code = get_user_meta( $user_id, 'dentist_office_zip_code', true);
$dentist_cell_ph = get_user_meta( $user_id, 'dentist_personal_cell', true );
$dentist_state = $US_state[$dentist_state];
if($dentist_apt_no !=""){
	$dentist_apt_no ='#'.$dentist_apt_no;
}
$dentist_address = $dentist_street." ".$dentist_apt_no."<br /> ".$dentist_city.", ".$dentist_state." ".$dentist_zip_code;
$dentist_address = preg_replace('/[\s$@_*]+/', ' ', $dentist_address);

$client_id = $Client->ID;
$client_street = get_user_meta( $client_id, 'client_street', true);
$client_apt_no = get_user_meta( $client_id, 'client_apt_no', true);
$client_city = get_user_meta( $client_id, 'client_city', true);
$client_state = get_user_meta( $client_id, 'client_state', true);
$client_zip_code = get_user_meta( $client_id, 'client_zip_code', true);
$client_state = $US_state[$client_state];
if($client_apt_no !=""){
	$client_apt_no ='#'.$client_apt_no;
}
$client_address = $client_street." ".$client_apt_no."<br /> ".$client_city.", ".$client_state." ".$client_zip_code;
$client_address = preg_replace('/[\s$@_*]+/', ' ', $client_address);
$auction_no = get_post_meta($product_id, 'auction_#' , TRUE);
if($auction_no==''){
	$auction_no = $product_id;
}
$product_cats_ids = wc_get_product_term_ids($product_id, 'product_cat' );
$sub_title = '';
if(in_array(76,$product_cats_ids)){
	 $sub_title = '&nbsp;-&nbsp;<span style="font-style:italic">abutments & denture only</span>';
	 }
if(in_array(77,$product_cats_ids)){
	 $sub_title = '&nbsp;-&nbsp;<span style="font-style:italic">abutments & dentures only</span>';
 }
if(in_array(119,$product_cats_ids)){
	  $sub_title = '&nbsp;-&nbsp;<span style="font-style:italic">locators & retrofit service only</span>';
  }
?>
<style type="text/css">
#template_header {
	background-color: #000 !important;
}
</style>
<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<?php /*?>
<?php if( ( $product_data->get_auction_type() == 'reverse' ) && ( get_option('simple_auctions_remove_pay_reverse') == 'yes')  ) { ?>

<p><?php printf( __( "Congratulations. You have won the auction.<br /><br />Service: <a href='%s'>%s</a><br />Winning Bid: %s.", 'wc_simple_auctions' ), get_permalink( $product_id ), $product_data -> get_title(), wc_price( $current_bid ) ); ?></p>
<?php } else { ?>
<p><?php printf(__( "Congratulations. You have won the auction.<br /><br />Service: <a href='%s'>%s</a><br />Winning Bid: %s.<br /><br />Please click on this link to pay for your auction %s ", 'wc_simple_auctions' ), get_permalink( $product_id  ), $product_data -> get_title(), wc_price( $current_bid ), '<a href="' . esc_attr( add_query_arg( "pay-auction",$product_id, $checkout_url ) ). '">' . __( 'payment', 'wc_simple_auctions' ) . '</a>' ); ?></p>
<?php } ?>
<?php */?>
<div style="margin-bottom: 40px;margin-top: 0;">
  <div style="margin-bottom: 30px;margin-top: 20px;top: -30px;padding:0;">
    <table width="100%" border="0">
      <tr>
        <td align="center" style="padding:0;vertical-align:top;"><p style="text-align:center;font-style:italic;margin:0;padding:0;"><img src="<?php echo get_site_url().'/wp-content/themes/dokan-child/win-email-photo.jpg';?>" border="0" alt="" title="shopadoc" ></p></td>
      </tr>
    </table>
  </div>
  <table width="100%" border="0">
    <tr>
      <td width="100%" style="padding:0;vertical-align:top;color:#000;">Auction #:&nbsp;<?php echo $auction_no; ?></td>
    </tr>
    <tr>
      <td width="100%" style="padding:0;vertical-align:top;color:#000;">WINNING BID:&nbsp;<?php echo wc_price_mujahid( $current_bid ); ?></td>
    </tr>
    <tr>
      <td style="padding:0;vertical-align:top;color:#000;">Service:&nbsp;<a href='<?php echo get_permalink( $product_id  );?>' style="text-decoration:none;color:#000;"><?php echo str_replace("*","",$product_data -> get_title());?><?php echo $sub_title;?></a></td>
    </tr>
  </table>
  <table width="100%" border="1">
    <tr>
      <td width="50%"><strong style="color:#000;">Client:</strong></td>
      <td width="50%"><strong style="color:#000;">Dentist:</strong></td>
    </tr>
    <tr>
      <td style="color:#000;"><?php echo $client_name; ?></td>
      <td style="color:#000;"><?php echo $winnner_name; ?></td>
    </tr>
    <tr>
      <td style="color:#000;"><?php echo strip_tags($client_address,'<br>');?></td>
      <td style="color:#000;"><?php echo strip_tags($dentist_address,'<br>');?></td>
    </tr>
    <tr>
      <td style="color:#000;"><?php echo str_replace("(","",str_replace(") ","-",$client_cell_ph));?></td>
      <td><a href="tel:<?php echo str_replace("(","",str_replace(") ","-",$dentist_cell_ph));?>" style="text-decoration:none;"><?php echo str_replace("(","",str_replace(") ","-",$dentist_cell_ph));?></a></td>
    </tr>
    <tr>
      <td><a style="color:#000;text-decoration:none;"><?php echo $client_email;?></a></td>
      <td><a style="text-decoration:none;" href="mailto:<?php echo $user_email;?>"><?php echo $user_email;?></a></td>
    </tr>
  </table>
  <table width="100%" border="0">
    <tr>
      <td align="center"><p style="text-align:center;font-style:italic;color:#000;">We appreciate the opportunity to be of service<br />
          and extend our warmest regards to you both.</p>
        <p style="text-align:center;font-style:italic;color:#000;">Staff of ShopADoc® The Dentist Marketplace, Inc</p></td>
    </tr>
  </table>
</div>
<?php //do_action( 'woocommerce_email_footer', $email ); ?>
