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
do_action('ywsbs_my_subscriptions_view_before');
if(isset($_GET['mode'])&&$_GET['mode']=='test' || 1==1){
$subscriptions = YWSBS_Subscription_Helper()->get_subscriptions_by_user( get_current_user_id() );
$status = ywsbs_get_status();
?>
<h2><?php echo apply_filters( 'ywsbs_my_account_subscription_title', __( 'My Subscriptions', 'yith-woocommerce-subscription' ) ); ?></h2>
<?php if( empty( $subscriptions ) ) : ?>
    <p class="ywsbs-my-subscriptions"><?php _e( 'There is no active subscription for your account.', 'yith-woocommerce-subscription' ) ?></p>
    <a href="javascript:" class="example2 woocommerce-Button button bid_button bid_on" >Change Payment Plan</a>
<?php else: ?>
    <table class="shop_table ywsbs_subscription_table my_account_orders">
        <thead>
        <tr>
            <th class="ywsbs-subscription-product"><?php _e( 'Product', 'yith-woocommerce-subscription' ); ?></th>
            <th class="ywsbs-subscription-status"><?php _e( 'Status', 'yith-woocommerce-subscription' ); ?></th>
            <th class="ywsbs-subscription-recurring"><?php _e( 'Amount', 'yith-woocommerce-subscription' ); ?></th>
            <th class="ywsbs-subscription-start-date"><?php _e( 'Start date', 'yith-woocommerce-subscription' ); ?></th>
            <th class="ywsbs-subscription-payment-date"><?php _e( 'Next Payment Due Date', 'yith-woocommerce-subscription' ); ?></th>
            <th class="ywsbs-subscription-action-view"></th>
            <?php if( get_option('ywsbs_allow_customer_cancel_subscription') == 'yes' ): ?>
                <th class="ywsbs-subscription-action-delete"></th>
            <?php endif ?>
        </tr>
        </thead>
        <tbody>
        <?php $flag = false;foreach ( $subscriptions as $subscription_post ) :
            $subscription = ywsbs_get_subscription( $subscription_post->ID );
            $next_payment_due_date = ( !in_array( $subscription->status, array( 'paused', 'cancelled') )  && $subscription->payment_due_date ) ? date_i18n( wc_date_format(), $subscription->payment_due_date ) : '';
            $start_date = ( $subscription->start_date ) ? date_i18n( wc_date_format(), $subscription->start_date ) : '';
			//if(($status[$subscription->status]=='active' || $status[$subscription->status] == 'expired') && $subscription->product_id != 1141):
			if(($status[$subscription->status]=='active') && $subscription->product_id != 1141):
				$flag = true;
				$active_plan = $subscription->product_id;
            ?>


            <tr class="ywsbs-item">
                <td class="ywsbs-subscription-product">
                    <a href="<?php echo get_permalink( $subscription->product_id ) ?>"><?php echo $subscription->product_name ?></a><?php echo ' x '. $subscription->quantity  ?>
                </td>

                <td class="ywsbs-subscription-status">
                    <span class="status <?php echo $subscription->status ?>"><?php echo $status[$subscription->status] ?></span>
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
                    <?php
                    $actions = array(
                        'view'    => array(
                            'url'  => $subscription->get_view_subscription_url(),
                            'name' => __( 'View', 'yith-woocommerce-subscription' )
                        )
                    );

                    if ( $actions = apply_filters( 'woocommerce_my_account_my_subscriptions_actions', $actions, $subscription ) ) {
                        foreach ( $actions as $key => $action ) {
                            echo '<a href="' . esc_url( $action['url'] ) . '" class="' . apply_filters('ywsbs_button_class', 'btn btn-xs btn-ghost-blue') . ' ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
                        }
                    }
                    ?>
                </td>
                <?php if( get_option('ywsbs_allow_customer_cancel_subscription') == 'yes' ): ?>
                    <td class="ywsbs-subscription-action-delete">
                        <?php if(  $subscription->can_be_cancelled() ): ?>
                            <a href="#cancel-subscription-modal"  class="<?php echo apply_filters('ywsbs_button_class', 'button') ?> cancel-subscription-button" data-ywsbs-rel="prettyPhoto" data-id="<?php echo $subscription->id ?>" data-url="<?php echo esc_url( $subscription->get_change_status_link('cancelled') ) ?>" data-expired="<?php echo $next_payment_due_date ?>"><?php echo apply_filters('ywsbs_label_cancel_subscription_button',__( 'Cancel', 'yith-woocommerce-subscription' ) ) ?></a>
                        <?php endif ?>
                    </td>
                <?php endif ?>
            </tr>
            <?php endif;?>
        <?php endforeach; ?>
        </tbody>
    </table>
    
    <?php if($flag):?>
<?php
	if($active_plan == 942){
		$change_plan = 'monthly';
	}
	if($active_plan == 948){
		$change_plan = 'single';
	}
?>
<a href="javascript:" class="example2 woocommerce-Button button change_plan" >Change Payment Plan</a>
				
				<script type="text/javascript">
                                jQuery('.example2').on('click', function(){
                                    jQuery.confirm({
                                        title: 'Confirm!',
										columnClass: 'col-md-8 col-md-offset-3',
                                        content: 'Are you sure you want to change payment plan?<br />',
                                        buttons: {
											No: {
                                                text: 'No, keep my current payment plan',
                                                //btnClass: 'btn-blue',
                                                /*keys: [
                                                    'enter',
                                                    'shift'
                                                ],*/
                                                /*action: function(){
                                                    //this.jQuerycontent // reference to the content
                                                    jQuery.alert('No');
                                                }*/
                                            },
                                            Yes: {
                                                text: 'Yes please change my payment plan',
                                                btnClass: 'btn-blue',
                                                /*keys: [
                                                    'enter',
                                                    'shift'
                                                ],*/
                                                action: function(){
                                                   // this.jQuerycontent // reference to the content
                                                    //jQuery.alert('Yes');
													window.location.replace("<?php echo get_site_url().'/?action=add_to_cart&type='.$change_plan.'&auction_id=';?>");
                                                }
                                            }
                                        }
                                    });
                                });
                            </script>
<?php endif;?>

<!-- SUBSCRIPTION CANCEL POPUP OPENER -->
<div id="cancel-subscription-modal" class="hide-modal myaccount-modal-cancel" >
    <p><?php echo apply_filters( 'ywsbs_content_cancel_subscription_modal', __( 'Do you really want to cancel subscription?', 'yith-woocommerce-subscription' )) ?></p>
    <p>
        <a class="ywsbs-button button my-account-cancel-quote-modal-button " data-id="" href="#"><?php _e( 'Yes, I want to cancel the subscription', 'yith-woocommerce-subscription' ) ?></a>
        <a class="ywsbs-button button close-subscription-modal-button" href="#"><?php _e( 'Close', 'yith-woocommerce-subscription' ) ?></a>
    </p>
</div>
<?php endif;
	do_action('ywsbs_my_subscriptions_view_after');
?>


<?php }?>