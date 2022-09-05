<?php
/**
 * My Account Subscriptions Section of YITH WooCommerce Subscription
 *
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$order_id = yit_get_order_id( $order );
$subscriptions = yit_get_prop( $order, 'subscriptions');

if( empty( $subscriptions ) || 1==1) {
	return;
}

$status = ywsbs_get_status();
?>
<!--<small>* if necessary</small><br />-->
	<h2><?php echo apply_filters( 'ywsbs_my_account_subscription_title', __( 'Related Subscriptions', 'yith-woocommerce-subscription' ) ); ?></h2>

<?php if ( $subscriptions ) :

	?>
	<table class="shop_table ywsbs_subscription_table my_account_orders">
		<thead>
		<tr>
			<th class="ywsbs-subscription-product"><?php _e( 'Product', 'yith-woocommerce-subscription' ); ?></th>
			<th class="ywsbs-subscription-status"><?php _e( 'Status', 'yith-woocommerce-subscription' ); ?></th>
			<th class="ywsbs-subscription-recurring"><?php _e( 'Amount', 'yith-woocommerce-subscription' ); ?></th>
			<th class="ywsbs-subscription-start-date"><?php _e( 'Start date', 'yith-woocommerce-subscription' ); ?></th>
			<th class="ywsbs-subscription-payment-date"><?php _e( 'Next Payment Due Date', 'yith-woocommerce-subscription' ); ?></th>
			<th class="ywsbs-subscription-action-view"></th>
			<?php if( get_option('allow_customer_cancel_subscription') == 'yes' ): ?>
				<th class="ywsbs-subscription-action-delete"></th>
			<?php endif ?>
		</tr>
		</thead>
		<tbody>
		<?php foreach ( $subscriptions as $subscription_post ) :
			$subscription = ywsbs_get_subscription( $subscription_post );

			$next_payment_due_date = ( ! in_array( $subscription->get( 'status' ), array( 'paused', 'cancelled' ) ) && $subscription->get( 'payment_due_date' ) ) ? date_i18n( wc_date_format(), $subscription->get( 'payment_due_date' ) ) : '';
			$start_date = ( $subscription->get( 'start_date' ) ) ? date_i18n( wc_date_format(), $subscription->get( 'start_date' ) ) : '';
			?>


			<tr class="ywsbs-item">
				<td class="ywsbs-subscription-product">
					<a href="<?php echo get_permalink( $subscription->get('product_id') ) ?>"><?php echo $subscription->get('product_name') ?></a><?php echo ' x '. $subscription->get('quantity')  ?>
				</td>
				<?php 
					if($status[$subscription->get('status')]=='active'){
						if($subscription->get('product_id') == 1141){
							$user_id = get_current_user_id();
							update_user_meta ($user_id,'dentist_account_status','de-active');
						}else{
							$user_id = get_current_user_id();
							update_user_meta ($user_id,'dentist_account_status','active');
						}
					}
				?>
				<td class="ywsbs-subscription-status">
					<span class="status <?php echo $subscription->get('status') ?>"><?php echo $status[$subscription->get('status')] ?></span>
				</td>

				<td class="ywsbs-subscription-recurring">
					<?php echo str_replace("/ 30 days","",str_replace("/ 7 days","",$subscription->get_formatted_recurring())) ?>
				</td>

				<td class="ywsbs-subscription-start-date">
					<?php echo $start_date ?>
				</td>

				<td class="ywsbs-subscription-payment-date">
					<?php echo $next_payment_due_date ?>
				</td>

				<td class="ywsbs-subscription-action-view">
					<a href="<?php echo $subscription->get_view_subscription_url() ?>" class="<?php echo apply_filters('ywsbs_button_class', 'button') ?>"><?php echo __('View','yith-woocommerce-subscription') ?></a>
				</td>
				<?php if( get_option('ywsbs_allow_customer_cancel_subscription') == 'yes' ): ?>
					<td class="ywsbs-subscription-action-delete">
						<?php if(  $subscription->can_be_cancelled() ): ?>
							<a href="#cancel-subscription-modal"  class="button cancel-subscription-button" data-ywsbs-rel="prettyPhoto" data-id="<?php echo $subscription->id ?>" data-url="<?php echo esc_url( $subscription->get_change_status_link('cancelled') ) ?>"><?php _e( 'Cancel', 'yith-woocommerce-subscription' ) ?></a>
						<?php endif ?>
					</td>
				<?php endif ?>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php endif;?>

<?php if( get_option('allow_customer_cancel_subscription') == 'yes' ): ?>
<!-- SUBSCRIPTION CANCEL POPUP OPENER -->
<div id="cancel-subscription-modal" class="hide-modal myaccount-modal-cancel" >
	<p><?php _e( 'Do you really want to cancel subscription?', 'yith-woocommerce-subscription' ) ?></p>
	<p>
		<a class="ywsbs-button button my-account-cancel-quote-modal-button" data-id="" href="#"><?php _e( 'Yes, I want to cancel the subscription', 'yith-woocommerce-subscription' ) ?></a>
		<a class="ywsbs-button button close-subscription-modal-button" href="#"><?php _e( 'Close', 'yith-woocommerce-subscription' ) ?></a>
	</p>
</div>
<?php endif;?>
