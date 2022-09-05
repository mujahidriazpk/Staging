<?php
/**
 * Customer completed order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-completed-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<?php 
	 $user_id = get_post_meta($order->get_order_number(), '_customer_user', true);
	 $designation = get_user_meta( $user_id, 'designation', true );
	 if($designation !=""){
		 $designation = " ".$designation;
	 }else{
		 $designation ="";
	 }
?>
<?php /* translators: %s: Customer first name */ ?>
<!--<p><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name().' '.$order->get_billing_last_name().$designation) ); ?></p>
<?php /* translators: %s: Site title */ ?>
<p><?php esc_html_e( 'We have finished processing your order.', 'woocommerce' ); ?></p>-->
<p><br /></p>
<?php
global $woocommerce;
$items = $order->get_items();
$subscription_product = array();
foreach ( $items as $item ) {
	array_push($subscription_product,$item['product_id']);
}
if(in_array('942',$subscription_product) || in_array('948',$subscription_product)){
	 echo '<p  class="email-greeting" style="color:#000;">Greetings!  Your payment has processed.</p>';
	 echo '<p style="color:#000;">You are now able to bid in all auctions within<br>your service area.</p>';
	 echo '<p style="color:#000;">We hope you enjoy the thrill of this interactive<br>auction experience and wish for you the best!</p>';
}
if(in_array('126',$subscription_product) || in_array('1642',$subscription_product) ){
	 echo '<p class="email-greeting" style="color:#000;">Greetings!  Your payment has processed.</p>';
	 echo '<p style="color:#000;">You are now in queue for the auction.<br>We hope you enjoy the thrill of this interactive<br>auction experience and wish for you the best!</p>';
}
if(in_array('1141',$subscription_product)){
	 echo '<p class="email-greeting" style="color:#000;">Greetings!  Your payment has processed.</p>';
	 echo '<p style="color:#000;">You now have access to engage weekly auction listings within your service area as they populate.<br>Your registration will auto-renew the last day of your annual cycle.</p>';
}


/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
//do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );
if(in_array('1141',$subscription_product)){
	$start = date("F j, Y");
	$end = date("F j, Y", strtotime('+1 years'));
	$end = date("F j, Y", strtotime('-1 days', strtotime($end)));
	echo '<p style="color:#000 !important;">Your registration is effective '.$start.' - '.$end.'.</p>';
	echo '<p style="color:#000 !important;">Renewal will process via auto-pay on your anniversary date.</p>';
}
if(in_array('942',$subscription_product) || in_array('948',$subscription_product) || in_array('126',$subscription_product) || in_array('1141',$subscription_product)){
	 echo '<p style="text-align:center;color:#000;">ShopADoc The Dentist Marketplace®</p>';
}
/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );