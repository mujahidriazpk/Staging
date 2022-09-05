<?php
/**
 * This is the email sent to the customer when his subscription is in overdue
 *
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
do_action( 'woocommerce_email_header', $email_heading, $email );
?>

<?php
$order_ids = $subscription->order_ids;
if( count( $order_ids )>1):
?>
<p><?php printf(__('Your subscription renewal order is now being processed. Your order details are shown below for your reference:', 'yith-woocommerce-subscription'), get_option('blogname')); ?></p>
<?php else:?>
    <p><?php printf(__('Your subscription order is now being processed. Your order details are shown below for your reference:', 'yith-woocommerce-subscription'), get_option('blogname')); ?></p>
<?php endif;?>


<h2><a class="link" href="<?php echo $subscription->get_view_subscription_url() ?>"><?php printf( __( 'Subscription #%s', 'yith-woocommerce-subscription'), $subscription->id ); ?></a> (<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', time()), date_i18n( wc_date_format(), time() )); ?>)</h2>

<?php
wc_get_template( 'emails/email-subscription-detail-table.php', array( 'subscription' => $subscription ) , '', YITH_YWSBS_TEMPLATE_PATH.'/' );
?>

<?php
wc_get_template( 'emails/email-subscription-customer-details.php', array( 'subscription' => $subscription ) , '', YITH_YWSBS_TEMPLATE_PATH.'/' );
?>

<?php
do_action( 'woocommerce_email_footer', $email );