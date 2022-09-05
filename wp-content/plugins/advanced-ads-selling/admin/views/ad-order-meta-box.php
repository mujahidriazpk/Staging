<?php
/**
 * List order information on the ad edit screen.
 *
 * @var int $order_id ID of the WooCommerce order.
 * @var string $hash hash value for the ad setup URL.
 * @var WC_Product|null|false $product WooCommerce product.
 * @var int $item_id cart item.
 */
?><ul>
	<li><a href="<?php echo esc_url( get_edit_post_link( $order_id ) ); ?>">
							<?php
							printf(
							// translators: %d is an order ID.
								esc_html__( 'Order #%d', 'advanced-ads-selling' ),
								absint( $order_id )
							);
							?>
					</a></li>
	<li><a  href="<?php echo esc_url( Advanced_Ads_Selling_Plugin::get_instance()->get_ad_setup_url( $hash ) ); ?>"><?php esc_html_e( 'Public Ad Setup URL', 'advanced-ads-selling' ); ?></a></li>
	<?php
	if ( $product && $product->get_id() ) :
		?>
		<li>
		<?php
		esc_html_e( 'Product', 'advanced-ads-selling' );
		echo ': ';
		?>
<a href="<?php echo esc_url( get_edit_post_link( $product->get_id() ) ); ?>"><?php echo esc_html( $product->get_title() ); ?></a>
		</li>
	<?php endif; ?>
	<li>
	<?php
	esc_html_e( 'Placement', 'advanced-ads-selling' );
	echo ': ' . esc_html( wc_get_order_item_meta( $item_id, '_ad_placement' ) );
	?>
	</li>
	<li>
	<?php
	esc_html_e( 'Sales Type', 'advanced-ads-selling' );
	echo ': ' . esc_html( wc_get_order_item_meta( $item_id, '_ad_sales_type' ) );
	?>
	</li>
	<li>
	<?php
	esc_html_e( 'Pricing Value', 'advanced-ads-selling' );
	echo ': ' . esc_html( wc_get_order_item_meta( $item_id, '_ad_pricing_option' ) );
	?>
	</li>
	<li>
	<?php
	esc_html_e( 'Pricing Label', 'advanced-ads-selling' );
	echo ': ' . esc_html( wc_get_order_item_meta( $item_id, '_ad_pricing_label' ) );
	?>
	</li>
</ul>
