<?php
	$favourites = $args[ 'favourites' ];
?>

<div id='swf_favourites_display'>

	<h2>My Favorites</h2>
	
	<?php 
		if( !empty( $favourites ) ) {
			echo do_shortcode('[products ids="' . implode(',', $favourites) . '" columns="3"]'); 
		}
		else{
	?>

		<h2>There are no items in your favorites!</h2>
		<p>To add some now, visit the shop</p>
		<a href='<?php echo get_permalink( wc_get_page_id( 'shop' ) ); ?>'>Shop Now</a>

		<?php }
	?>

</div>