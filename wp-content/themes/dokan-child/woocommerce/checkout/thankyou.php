<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;
$credit_card_number = get_post_meta($order->get_id(), '_credit_card_number',true);
if($credit_card_number !=""){
	$paid_text = 'Card ending in '.$credit_card_number;
}else{
	$paid_text = $order->get_payment_method_title();
}
$order_ref_no = get_post_meta($order->get_id(), 'order_ref_#' , TRUE);
if($order_ref_no==''){
	$order_ref_no = $order->get_order_number();
}
?>
<style type="text/css">
			.site{text-align:left;}
			.only_print{
				visibility:hidden;
				display:none;
			}
			@media print {
				body{
					background-repeat:no-repeat !important;
					background-attachment:scroll !important;
				}
				body,*{
                -moz-print-color-adjust: exact;
                -ms-print-color-adjust: exact;
                print-color-adjust: exact;
				-webkit-print-color-adjust: exact !important;   /* Chrome, Safari, Edge */
				color-adjust: exact !important;                 /*Firefox*/
            }
					#not_print,#colophon{
					visibility: hidden;
					display:none;
				  }
				  .only_print{
					visibility:visible;
					display:block;
					text-align:center;
					 width:100%;
				  }
				  table.only_print{
					  width:100%;
					  text-align:center;
					  display:block !important;
					  float:left;
				  }
				  ul.woocommerce-thankyou-order-details{
					  clear:both;
					  float:left;
					  width:100%;
					  padding:0 !important;
					  margin: 0 0 1em !important;
				  }
				  ul.woocommerce-thankyou-order-details li{
					  float:left;
					 /* width:20%;*/
					 padding:0 !important;
					 margin:0 !important;
					 overflow:hidden;
					 word-wrap: break-word;
					/* line-height:17px !important;*/
					 text-align:left;
					 width:100%;
					 font-weight:bold;
					 
					}
					.woocommerce ul.order_details li strong{
						/*line-height:17px;*/
						display:inline-block;
					}
				  /*ul.woocommerce-thankyou-order-details li.woocommerce-order-overview__order{
					  width:25%;
					  padding-right:6px !important;
				  }
				  ul.woocommerce-thankyou-order-details li.woocommerce-order-overview__date{
					  width:20%;
					  padding-right:3px !important;
				  }
				  ul.woocommerce-thankyou-order-details li.woocommerce-order-overview__email{
					  width:25%;
					  padding:6px !important;
				  }
				  ul.woocommerce-thankyou-order-details li.woocommerce-order-overview__total {
					  width:10%;
					  padding:6px !important;
				  }
				  ul.woocommerce-thankyou-order-details li.woocommerce-order-overview__payment-method{
					  width:20%;
					  padding-left:3px !important;
				  }*/
				  /*body * {
					visibility: hidden;
				  }
				  #section-to-print, #print * {
					visibility: visible;
				  }
				  #print{
					position: absolute;
					left: 0;
					top: 0;
				  }*/
				}
		</style>
<script>
jQuery(function($){
			$('body').toggleClass('compact');
		// Print page.
		$(document).on('click', '.print', function(e) {
			e.preventDefault();
			window.print();
		});
});
</script>
<div class="buttons" id="not_print">
    <a href="javascript:" class="button button-primary print"><?php esc_html_e( 'Printable Version', 'wpforms-lite' ); ?></a>
</div>
<div class="woocommerce-order">

	<?php if ( $order ) :

		do_action( 'woocommerce_before_thankyou', $order->get_id() ); ?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
				<?php endif; ?>
			</p>

		<?php else : ?>

			<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'woocommerce' ), $order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details only_print" >

				

				<li class="woocommerce-order-overview__date date">
					<?php esc_html_e( 'Date:', 'woocommerce' ); ?>
					<strong><?php echo wc_format_datetime( $order->get_date_created() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</li>
				<li class="woocommerce-order-overview__order order">
					<?php esc_html_e( 'Order number:', 'woocommerce' ); ?>
					<strong><?php echo $order_ref_no; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</li>
				

				<li class="woocommerce-order-overview__total total">
					<?php esc_html_e( 'Total:', 'woocommerce' ); ?>
					<strong><?php echo $order->get_formatted_order_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</li>

				<?php if ( $order->get_payment_method_title() ) : ?>
					<li class="woocommerce-order-overview__payment-method method">
						<?php esc_html_e( 'Payment method:', 'woocommerce' ); ?>
						<strong><?php echo wp_kses_post($paid_text); ?></strong>
					</li>
				<?php endif; ?>
                <?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
					<li class="woocommerce-order-overview__email email">
						<?php esc_html_e( 'Email:', 'woocommerce' ); ?>
						<strong><?php echo $order->get_billing_email(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
					</li>
				<?php endif; ?>

			</ul>
			<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details" id="not_print">

				<li class="woocommerce-order-overview__order order">
					<?php esc_html_e( 'Order number:', 'woocommerce' ); ?>
					<strong><?php echo $order_ref_no; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</li>

				<li class="woocommerce-order-overview__date date">
					<?php esc_html_e( 'Date:', 'woocommerce' ); ?>
					<strong><?php echo wc_format_datetime( $order->get_date_created() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</li>

				<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
					<li class="woocommerce-order-overview__email email">
						<?php esc_html_e( 'Email:', 'woocommerce' ); ?>
						<strong><?php echo $order->get_billing_email(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
					</li>
				<?php endif; ?>

				<li class="woocommerce-order-overview__total total">
					<?php esc_html_e( 'Total:', 'woocommerce' ); ?>
					<strong><?php echo $order->get_formatted_order_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</li>

				<?php if ( $order->get_payment_method_title() ) : ?>
					<li class="woocommerce-order-overview__payment-method method">
						<?php esc_html_e( 'Payment method:', 'woocommerce' ); ?>
						<strong><?php echo wp_kses_post($paid_text); ?></strong>
					</li>
				<?php endif; ?>

			</ul>

		<?php endif; ?>

		<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
		<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

	<?php else : ?>

		<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'woocommerce' ), null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>

	<?php endif; ?>

</div>
                <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received only_print" id="only_print" style="float:left;width:100%;text-align:center;">ShopADoc® The Dentist Marketplace</p>