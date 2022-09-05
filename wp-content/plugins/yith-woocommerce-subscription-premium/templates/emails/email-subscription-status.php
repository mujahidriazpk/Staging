<?php
/**
 * This is the email sent to the administrator when the subscription changes status
 *
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

do_action( 'woocommerce_email_header', $email_heading, $email );
$status = ywsbs_get_status();
$sbs_status = isset( $status[ $subscription->status ]) ? $status[ $subscription->status ] : $subscription->status;
?>


	<p><?php printf( __( 'The status of subscription #%d has changed to <strong>%s</strong>', 'yith-woocommerce-subscription' ), $subscription->id, $sbs_status ); ?></p>

	<h2><a class="link" href="<?php echo admin_url( 'post.php?post=' . $subscription->id . '&action=edit' ); ?>"><?php printf( __( 'Subscription #%s', 'yith-woocommerce-subscription'), $subscription->id ); ?></a> (<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', time()), date_i18n( wc_date_format(), time() )); ?>)</h2>


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

			<td scope="col" style="text-align:left;"><?php echo wc_price( $subscription->line_total, array('currency' => $subscription->order_currency ) ) ?></td>
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
<?php if ( ! empty( $subscription->order_ids ) ): ?>
	<h3><?php _e( 'Related Orders', 'yith-woocommerce-subscription' ); ?></h3>
	<?php if ( $subscription->order_ids ): ?>
		<ul style="list-style-type: none;padding-left: 0px;margin-bottom: 35px;">
			<?php
			foreach ( $subscription->order_ids as $order_id ) :
				$order = wc_get_order( $order_id ); ?>
				<li>
				<?php
				if ( ! $order ) {
					printf( __( '<p>Order #%d</p>', 'yith-woocommerce-subscription' ), $order_id );
					continue;
				}
				if( function_exists('wc_format_datetime') ){
					$order_date           = $order->get_date_created();
					$order_date_formatted = wc_format_datetime( $order_date );
				}else{
					$order_date  = $order->order_date;
					$order_date_formatted = date_i18n( get_option( 'date_format' ), strtotime( $order_date ) );
				}
				?>
					<?php printf( '<time datetime="%s">%s</time>', date( 'Y-m-d', strtotime( $order_date ) ), $order_date_formatted ); ?> -
					<?php printf( __('order <a href="%s">#%d</a>', 'yith-woocommerce-subscription'), esc_url( admin_url( 'post.php?post=' . $order_id . '&action=edit' ) ), $order->get_order_number()  ) ?> -
					<?php echo wc_price( $order->get_total(), array('currency' => $order->get_currency() ) ) ?>
                </li>
			<?php endforeach ?>
		</ul>
	<?php endif ?>

<?php endif ?>

<?php
wc_get_template( 'emails/email-subscription-customer-details.php', array( 'subscription' => $subscription ) , '', YITH_YWSBS_TEMPLATE_PATH.'/' );
?>


<?php
	do_action( 'woocommerce_email_footer', $email );
