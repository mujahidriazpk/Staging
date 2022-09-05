<?php
/**
 * HTML Template for Customer Detail
 *
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

$billing_address = $subscription->get_address_fields( 'billing', true );

?>
<h3><?php _e( 'Customer\'s details', 'yith-woocommerce-subscription' ); ?></h3>

<?php if ( !empty( $billing_address ) ) : ?>
    <p>
        <strong><?php _e( 'Address:', 'yith-woocommerce-subscription' ); ?></strong><br>
        <?php echo WC()->countries->get_formatted_address( $billing_address );   ?>
    </p>
<?php endif; ?>

<?php if ( $billing_email = $subscription->get_billing_email() ) : ?>
    <p>
        <strong><?php _e( 'Email:', 'yith-woocommerce-subscription' ); ?></strong> <?php echo esc_html( $billing_email ); ?>
    </p>
<?php endif; ?>

<?php if ( $billing_phone = $subscription->get_billing_phone() ) : ?>
    <p>
        <strong><?php _e( 'Telephone:', 'yith-woocommerce-subscription' ); ?></strong> <?php echo esc_html( $billing_phone ); ?>
    </p>
<?php endif; ?>
