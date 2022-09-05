<?php
/**
 * This is the email sent to the customer when his subscription has been suspended
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


<p><?php printf( __( 'Subscription #%d has been suspended', 'yith-woocommerce-subscription' ), $subscription->id ); ?></p>
<p><?php printf(__('If you do not pay it by <strong>%s</strong>, your subscription #%d will be <strong>%s</strong>.', 'yith-woocommerce-subscription'), date_i18n( wc_date_format(), $next_activity_date ) , $subscription->id, $next_activity); ?></p>
<p><?php _e('To pay for this order please use the following link:', 'yith-woocommerce-subscription'); ?></p>
<p style="padding:10px 0;"><a class="link" style="background-color:#eee;padding:8px 15px;color:#111;text-decoration:none;" href="<?php echo esc_url($order->get_checkout_payment_url()); ?>"><?php _e('pay now', 'yith-woocommerce-subscription'); ?></a></p>

<h2><a class="link" href="<?php echo $subscription->get_view_subscription_url() ?>"><?php printf( __( 'Subscription #%s', 'yith-woocommerce-subscription'), $subscription->id ); ?></a> (<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', time()), date_i18n( wc_date_format(), time() )); ?>)</h2>

<?php
wc_get_template( 'emails/email-subscription-detail-table.php', array( 'subscription' => $subscription ), '', YITH_YWSBS_TEMPLATE_PATH.'/'  );
?>

<?php
wc_get_template( 'emails/email-subscription-customer-details.php', array( 'subscription' => $subscription ), '', YITH_YWSBS_TEMPLATE_PATH.'/' );
?>

<?php
do_action( 'woocommerce_email_footer', $email );
