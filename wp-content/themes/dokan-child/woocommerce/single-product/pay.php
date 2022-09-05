<?php
/**
 * Auction pay
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce, $product, $post;

if(!(method_exists( $product, 'get_type') && $product->get_type() == 'auction')){
	return;
}

$user_id = get_current_user_id();

if ( ($user_id == $product->get_auction_current_bider() && $product->get_auction_closed() == '2' && !$product->get_auction_payed() ) && 1==2) :
?>
<?php //the_title( '<h1 class="product_title entry-title">', '</h1>' );?>

    <!--<p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) );?> priceBox"><?php echo $product->get_price_html(); ?></p>-->

    <p><?php _e('Congratulations you have won this auction!', 'wc_simple_auctions') ?></p>
    
    <?php if(!($product->get_auction_type() == 'reverse' && get_option('simple_auctions_remove_pay_reverse') == 'yes')) { ?>
    	<p><a href="<?php echo apply_filters( 'woocommerce_simple_auction_pay_now_button',esc_attr(add_query_arg("pay-auction",$product->get_id(), simple_auction_get_checkout_url()))); ?>" class="button"><?php _e('Pay Now', 'wc_simple_auctions') ?></a></p>
    <?php } ?>	

<?php endif; ?>