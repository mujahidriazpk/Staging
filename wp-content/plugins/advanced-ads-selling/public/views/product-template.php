<?php
/**
 * Advanced Ad product add to cart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

if ( ! $product->is_purchasable() || $product->get_type() !== 'advanced_ad' ) {
	return;
}

// get product_id
$product_id = $product->get_id();

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="cart" method="post" enctype='multipart/form-data'>
	
	<?php if( is_array( $prices ) && count( $prices ) ) :
	    switch( $sales_type ) : 
		default :
			?><ul id="advads_selling_option_ad_price"><?php
			$first = true;
			foreach( $prices as $_price ){
			    ?><li><label><input type="radio" name="option_ad_price" value="<?php echo $_price['value']; ?>"<?php if( $first) : echo ' checked="checked"'; endif; ?>>&nbsp;<?php echo $_price['label']; ?>, <?php echo wc_price( $_price['price'] ); ?></label></li><?php
			    $first = false;
			}
			?></ul><?php
	    endswitch;
	endif;

	if( is_array( $placements ) && count( $placements ) ) :
	    if( 1 < count( $placements ) ) :
	?><p id="advads_selling_option_headline"><?php _e( 'Select a placement', 'advanced-ads-selling' ); ?></p>
	    <ul id="advads_selling_option_placements"><?php
	    $first = true;
	    foreach( $placements as $_placement ){
		if( !isset( $placements_raw[ $_placement ] ) ){
		    continue;
		}
		?><li><label><input type="radio" name="option_ad_placement" value="<?php echo $_placement; ?>"<?php if( $first) : echo ' checked="checked"'; endif; ?>>&nbsp;<?php echo $placements_raw[$_placement]['name']; ?></label></li><?php
		$first = false;
	    }
	    ?></ul><?php
	    elseif( 1 === count( $placements ) ) :
		?><input type="hidden" name="option_ad_placement" value="<?php echo $placements[0]; ?>"><?php
	    endif;
	endif;
	
	?><p><?php _e( 'Available ad types', 'advanced-ads-selling' ); ?></p>
	
	<ul>
	    <?php foreach( $ad_types as $_type ){
		if( isset( $ad_types_raw[ $_type ] )){
		    echo '<li>' . $ad_types_raw[ $_type ]->title . '</li>';
		}
	    } ?>
	</ul>
	
	<p><?php _e( 'You will be able to submit the ad content after the purchase.', 'advanced-ads-selling' ); ?></p>
	
	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" />

	<button type="submit" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' );