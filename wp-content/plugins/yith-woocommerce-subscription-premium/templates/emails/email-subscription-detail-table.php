<?php
/**
 * HTML Template for Subscription Detail
 *
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>
<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
		<thead>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Product', 'yith-woocommerce-subscription' ); ?></th>
<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Subtotal', 'yith-woocommerce-subscription' ); ?></th>
</tr>
</thead>
<tbody>
<tr>
    <td scope="col" style="text-align:left;">
        <a href="<?php echo get_permalink( $subscription->product_id ) ?>"><?php echo $subscription->product_name ?></a><?php echo ' x ' . $subscription->quantity ?>
    </td>

    <td scope="col" style="text-align:left;"><?php echo wc_price( $subscription->line_total, array('currency' => $subscription->order_currency )) ?></td>
</tr>

</tbody>
<tfoot>
<?php if ( $subscription->line_tax != 0 ): ?>
    <tr>
        <th scope="row"><?php _e( 'Item Tax:', 'yith-woocommerce-subscription' ) ?></th>
        <td><?php echo wc_price( $subscription->line_tax, array('currency' => $subscription->order_currency ) ) ?></td>
    </tr>
<?php endif ?>
<tr>
    <th scope="row"><?php _e( 'Subtotal:', 'yith-woocommerce-subscription' ) ?></th>
    <td><?php echo wc_price( $subscription->line_total + $subscription->line_tax, array('currency' => $subscription->order_currency ) ) ?></td>
</tr>

<?php
if ( !empty( $subscription->subscriptions_shippings ) ) :?>
    <tr>
        <th scope="row"><?php _e( 'Shipping:', 'yith-woocommerce-subscription' ) ?></th>
        <td><?php echo wc_price( $subscription->subscriptions_shippings['cost'], array('currency' => $subscription->order_currency ) ) . sprintf( __( '<small> via %s</small>', 'yith-woocommerce-subscription' ), $subscription->subscriptions_shippings['name'] ); ?></td>
    </tr>
    <?php
    if ( !empty( $subscription->order_shipping_tax ) ) :
        ?>
        <tr>
            <th scope="row"><?php _e( 'Shipping Tax:', 'yith-woocommerce-subscription' ) ?></th>
            <td colspan="2"><?php echo wc_price( $subscription->order_shipping_tax, array('currency' => $subscription->order_currency ) ); ?></td>
        </tr>
        <?php
    endif;
endif;
?>
<tr>
    <th scope="row"><?php _e( 'Total:', 'yith-woocommerce-subscription' ) ?></th>
    <td colspan="2"><?php echo wc_price( $subscription->subscription_total, array('currency' => $subscription->order_currency ) ); ?></td>
</tr>
</tfoot>
</table>