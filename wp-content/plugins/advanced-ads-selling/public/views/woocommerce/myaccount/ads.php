<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$customer_id     = get_current_user_id();
$customer_orders = Advanced_Ads_Selling_Order::get_customer_orders( $customer_id );
?>

<table>
	<tr>
		<td>
			<strong>
				<?php esc_html_e( 'Ad Title - Product Name', 'advanced-ads-selling' ); ?>
			</strong>
		</td>
		<td>
			<strong>
				<?php esc_html_e( 'Options', 'advanced-ads-selling' ); ?>
			</strong>
		</td>
	</tr>
	<?php
	if ( empty( $customer_orders ) ) {
		esc_html_e( 'No orders found', 'advanced-ads-selling' );
	} else {
		foreach ( $customer_orders as $order_key => $order_values ) {
			$order = wc_get_order( $order_values->ID );

			if ( Advanced_Ads_Selling_Order::has_ads( $order_values->ID ) ) {
				foreach ( $order->get_items() as $item_id => $item ) {
					$item_id      = $item->get_id();
					$product_name = $item->get_name();
					$product_id   = $item['product_id'];

					$ad_id = Advanced_Ads_Selling_Order::order_item_id_to_ad_id( $item_id );

					if ( class_exists( 'Advanced_Ads_Tracking_Admin' ) && defined( 'AAT_VERSION' ) && version_compare( AAT_VERSION, '1.8.18', '>=' ) &&
					     method_exists( Advanced_Ads_Tracking_Admin::get_instance(), 'is_stats_option_saved' ) &&
					     method_exists( Advanced_Ads_Tracking_Admin::get_instance(), 'get_public_link' ) ) {
						?>
						<tr>
							<td>
								<?php echo esc_html( get_post( $ad_id )->post_title ) . ' - ' . esc_html( $product_name ); ?>
							</td>
							<td>
								<?php
								if ( Advanced_Ads_Tracking_Admin::get_instance()->is_stats_option_saved( $ad_id ) ) :
									?>
									<a style="text-decoration: none; border-bottom: 0;" href="<?php echo esc_url( Advanced_Ads_Tracking_Admin::get_instance()->get_public_link( $ad_id ) ); ?>">
										<span class="dashicons dashicons-chart-line" style="vertical-align: initial;"></span>
									</a>
								<?php endif; ?>
							</td>
						</tr>
						<?php
					}
				}
			}
		}
	}
	?>
</table>
