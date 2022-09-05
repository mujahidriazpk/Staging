<?php
/**
 * Payment methods
 *
 * Shows customer payment methods on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/payment-methods.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 */

defined( 'ABSPATH' ) || exit;

$saved_methods = wc_get_customer_saved_methods_list( get_current_user_id() );
$has_methods   = (bool) $saved_methods;
$types         = wc_get_account_payment_methods_types();

do_action( 'woocommerce_before_account_payment_methods', $has_methods ); ?>

<?php if ( $has_methods ) : ?>

	<table class="woocommerce-MyAccount-paymentMethods shop_table shop_table_responsive account-payment-methods-table">
		<thead>
			<tr>
				<?php foreach ( wc_get_account_payment_methods_columns() as $column_id => $column_name ) : ?>
					<th class="woocommerce-PaymentMethod woocommerce-PaymentMethod--<?php echo esc_attr( $column_id ); ?> payment-method-<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<?php foreach ( $saved_methods as $type => $methods ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
			<?php foreach ( $methods as $method ) : ?>
				<tr class="payment-method<?php echo ! empty( $method['is_default'] ) ? ' default-payment-method' : ''; ?>">
					<?php foreach ( wc_get_account_payment_methods_columns() as $column_id => $column_name ) : ?>
						<td class="woocommerce-PaymentMethod woocommerce-PaymentMethod--<?php echo esc_attr( $column_id ); ?> payment-method-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
							<?php
							if ( has_action( 'woocommerce_account_payment_methods_column_' . $column_id ) ) {
								do_action( 'woocommerce_account_payment_methods_column_' . $column_id, $method );
							} elseif ( 'method' === $column_id ) {
								if ( ! empty( $method['method']['last4'] ) ) {
									/* translators: 1: credit card type 2: last 4 digits */
									echo sprintf( esc_html__( '%1$s ending in %2$s', 'woocommerce' ), esc_html( wc_get_credit_card_type_label( $method['method']['brand'] ) ), esc_html( $method['method']['last4'] ) );
								} else {
									echo esc_html( wc_get_credit_card_type_label( $method['method']['brand'] ) );
								}
							} elseif ( 'expires' === $column_id ) {
								echo esc_html( $method['expires'] );
							} elseif ( 'actions' === $column_id ) {
								foreach ( $method['actions'] as $key => $action ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
									echo '<a href="' . esc_url( $action['url'] ) . '" class="button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>&nbsp;';
								}
							}
							?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</table>

<?php else : ?>

	<p class="woocommerce-Message woocommerce-Message--info woocommerce-info"><?php esc_html_e( 'No saved methods found.', 'woocommerce' ); ?></p>

<?php endif; ?>

<?php do_action( 'woocommerce_after_account_payment_methods', $has_methods ); ?>

<?php if ( WC()->payment_gateways->get_available_payment_gateways() ) : ?>
	<a class="button" href="<?php echo esc_url( wc_get_endpoint_url( 'add-payment-method' ) ); ?>"><?php esc_html_e( 'Add payment method', 'woocommerce' ); ?></a>
<?php endif; ?>
<?php
$user_id = get_current_user_id();
$user = get_userdata( $user_id );
if($user->roles[0]=='customer'){
		if(isset($_GET['mode']) && ($_GET['mode']=='active' || $_GET['mode']=='de-active' || $_GET['mode']== 'unsubscribe' || $_GET['mode']== 'de-active-sub-reg' || $_GET['mode']== 'de-active-sub-reg-intial')){
			//get_user_meta( $user_id, 'dentist_account_status', true );
			//if(!add_user_meta($user_id,'dentist_account_status',$_GET['mode'])) {
				update_user_meta ($user_id,'dentist_account_status',$_GET['mode']);
				if($_GET['mode']=='de-active' &&1==2){
					/* global $current_user;
					$subscriptions_users = YWSBS_Subscription_Helper()->get_subscriptions_by_user($current_user->ID);
					foreach($subscriptions_users as $row){
						$plan_id = get_post_meta($row->ID,'product_id',true);
						if($plan_id != 1141){
							update_post_meta ($row->ID,'status','cancelled');
							update_post_meta ($row->ID,'cancelled_date',strtotime(date("Y-m-d")));
							update_post_meta ($row->ID,'end_date',strtotime(date("Y-m-d")));
						}
					}*/
				}
				
				if($_GET['mode']=='unsubscribe'){
					 global $current_user;
					$subscriptions_users = YWSBS_Subscription_Helper()->get_subscriptions_by_user($current_user->ID);
					foreach($subscriptions_users as $row){
						$plan_id = get_post_meta($row->ID,'product_id',true);
						if($plan_id == 1141 || 1==1){
							update_post_meta ($row->ID,'status','cancelled');
							update_post_meta ($row->ID,'cancelled_date',strtotime(date("Y-m-d")));
							update_post_meta ($row->ID,'end_date',strtotime(date("Y-m-d")));
						}
					}
				}
				if($_GET['mode']=='de-active-sub-reg-intial'){
					global $current_user,$wpdb;
					$entry_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_wpforms_entries WHERE user_id = %d and form_id='895' and status = 'completed' and type='payment' LIMIT 1",$current_user->ID));
					$stripe_subscription_id = json_decode($entry_row->meta)->payment_subscription;
					$payment_due_date = date("Y-m-d H:i:s", strtotime('+1 years', strtotime($entry_row->date)));
					update_user_meta ($current_user->ID,'register_sub_end_date',strtotime($payment_due_date));
					$path = "https://api.stripe.com/v1/subscriptions/".$stripe_subscription_id;
					$data = array("at_period_end"=>"true");
					curl_del($path,$data);
					wp_redirect(home_url('/my-account/payment-methods/?mode=msg'));
					exit;
				}
				if($_GET['mode']=='de-active-sub-reg'){
					global $current_user;
					$subscriptions_users = YWSBS_Subscription_Helper()->get_subscriptions_by_user($current_user->ID);
					foreach($subscriptions_users as $row){
						//print_r($row);
						$plan_id = get_post_meta($row->ID,'product_id',true);
						$status = get_post_meta($row->ID,'status',true);
						if($plan_id == 1141 && $status =='active'){
							$stripe_subscription_id = get_post_meta($row->ID,'stripe_subscription_id',true);
							$payment_due_date = get_post_meta($row->ID,'payment_due_date',true);
							//update_post_meta ($row->ID,'register_sub_end_date',$payment_due_date);
							update_post_meta ($row->ID,'status','cancelled');
							update_post_meta ($row->ID,'cancelled_date',strtotime(date("Y-m-d")));
							update_post_meta ($row->ID,'expired_date',$payment_due_date);
							update_post_meta ($row->ID,'end_date',$payment_due_date);
							update_user_meta ($user_id,'register_sub_end_date',$payment_due_date);
							//echo $row->ID."==".$stripe_subscription_id."<br />";
							$path = "https://api.stripe.com/v1/subscriptions/".$stripe_subscription_id;
 							$data = array("at_period_end"=>"true");
							curl_del($path,$data);
							wp_redirect(home_url('/my-account/payment-methods/?mode=msg'));
							exit;
						}
					}
				}
			//}
		}
		
	}
?>

<?php $dentist_account_status = get_user_meta(get_current_user_id(), 'dentist_account_status', true );
if($dentist_account_status =='unsubscribe' || $dentist_account_status=='de-active' || $dentist_account_status =='de-active-sub-reg' || $dentist_account_status =='de-active-sub-reg-intial'){
	//echo '<a href="'.get_site_url().'/?action=add_to_cart&type=register&auction_id=" class="" style="float:right;">Reactivate account</a>';
	echo '<a href="'.get_site_url().'/?action=add_to_cart&type=register&auction_id=" class="" style="float:right;">&nbsp;</a>';
}elseif(($dentist_account_status =='active' || $dentist_account_status =="")){
	$dentist_auction_status = get_dentist_active_auction();
	global $current_user;
	$intial_register = 'yes';
	$de_active_url = '/my-account/payment-methods/?mode=de-active-sub-reg-intial';
	$subscriptions_users = YWSBS_Subscription_Helper()->get_subscriptions_by_user($current_user->ID);
	foreach($subscriptions_users as $row){
		//print_r($row);
		$plan_id = get_post_meta($row->ID,'product_id',true);
		$status = get_post_meta($row->ID,'status',true);
		if($plan_id == 1141 && $status =='active'){
			$intial_register = 'no';
			$de_active_url = '/my-account/payment-methods/?mode=de-active-sub-reg';
			break;
		}
	}
	echo '<a href="javascript:" class="example2" style="float:right;">Deactivate auto-renewal</a>';?>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
	<script type="text/javascript">
			<?php if($dentist_auction_status=='active'){?>
		   jQuery('.example2').on('click', function(){
				jQuery.alert({
					title: 'Alert!',
					columnClass: 'col-md-6 col-md-offset-3',
					content: 'Auction(s) in progress, you cannot deactivate at this time.',
				});
			});
			<?php }else{?>
			jQuery('.example2').on('click', function(){
				jQuery.confirm({
					title: 'Please Confirm',
					columnClass: 'col-md-8 col-md-offset-3',
					content: 'Are you sure you want to deactivate your registration auto renewal feature?<br />Deactivation will require you to make a manual registration payment to continue to have access after your anniversary date.',
					buttons: {
						Yes: {
							text: 'Yes',
							action: function(){
								window.location.replace("<?php echo home_url($de_active_url);?>");
							}
						},
						No: {
							text: 'No',
							btnClass: 'btn-blue',
						},
						
					}
				});
			});
			<?php }?>
		</script>
<?php }?>