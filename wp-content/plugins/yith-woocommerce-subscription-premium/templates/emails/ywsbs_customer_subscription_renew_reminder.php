<?php
/**
 * This is the email sent to the customer when his subscription is expired
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

<p><?php printf( __( 'Subscription #%d is going to renew on %s', 'yith-woocommerce-subscription' ), $subscription->id, date_i18n( wc_date_format(), $next_activity_date ) ); ?></p>

<h2><a class="link" href="<?php echo $subscription->get_view_subscription_url() ?>"><?php printf( __( 'Subscription #%s', 'yith-woocommerce-subscription'), $subscription->id ); ?></a></h2>


<?php
wc_get_template( 'emails/email-subscription-detail-table.php', array( 'subscription' => $subscription ), '', YITH_YWSBS_TEMPLATE_PATH.'/'  );
?>

<?php
wc_get_template( 'emails/email-subscription-customer-details.php', array( 'subscription' => $subscription ), '', YITH_YWSBS_TEMPLATE_PATH.'/'  );
?>

<?php
do_action( 'woocommerce_email_footer', $email );