<?php
/**
 * Notice about new plugin - Flexible Wishlist.
 *
 * @var string $ajax_url    URL for Admin Ajax.
 * @var string $ajax_action Action for Admin Ajax.
 * @var string $image_url   .
 * @var string $install_url .
 * @package Flexible Checkout Fields
 */

?>
<div class="notice notice-success is-dismissible"
	data-notice="fcf-admin-notice"
	data-notice-url="<?php echo esc_attr( $ajax_url ); ?>"
	data-notice-action="<?php echo esc_attr( $ajax_action ); ?>"
>
	<img src="<?php echo esc_attr( $image_url ); ?>" alt="">
	<h2>
		<?php echo esc_html( __( 'New free plugin by WP Desk: Flexible Wishlist for WooCommerce', 'flexible-checkout-fields' ) ); ?>
	</h2>
	<p>
		<?php
		echo wp_kses_post(
			sprintf(
			/* translators: %1$s: open strong tag, %2$s: open strong tag, %3$s: heart icon, %4$s: open anchor tag, %5$s: open anchor tag */
				__( 'Introducing our new %1$sWooCommerce Wishlist plugin%2$s %3$s It\'s lightweight. It\'s free. Fits any theme. 100%% customizable and flexible. %4$sRead more%5$s and try it now. Uninstall any time with just one click.', 'flexible-checkout-fields' ),
				'<strong>',
				'</strong>',
				'<span class="dashicons dashicons-heart"></span>',
				'<a href="' . esc_url( apply_filters( 'flexible_checkout_fields/short_url', '#', 'fcf-settings-notice-fw-read-more' ) ) . '" target="_blank">',
				'</a>'
			)
		);
		?>
	</p>
	<div>
		<a href="<?php echo esc_url( $install_url ); ?>"
			class="button button-hero button-primary">
			<?php echo esc_html( __( 'Try for free', 'flexible-checkout-fields' ) ); ?>
		</a>
		<button type="button"
			class="button button-hero" data-notice-button>
			<?php echo esc_html( __( 'Do not show again', 'flexible-checkout-fields' ) ); ?>
		</button>
	</div>
</div>
