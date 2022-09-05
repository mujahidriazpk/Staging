jQuery(document).ready(function($){

	$(document).on('click', '.swf_add_to_favourites', function(e){
		e.preventDefault();
		var prod_id = $(this).data().productid;
		if( isNaN(prod_id) ){
			return;
		}
		prod_id = parseInt(prod_id);
		data = {
			prod_id:prod_id,
			action:'simple_ajax_add_to_favourites',
			simple_favourites_nonce: swfAjax.nonce
		}
		var $this_button = $(this);
		$.post(swfAjax.ajaxurl, data, function(msg){
			var html = "<span class='swf_message'>"+msg+"</span><button class='swf_remove_from_favourites' data-product_id='"+prod_id+"'>Remove from Favorites</button>";
			$this_button.closest('.swf_container').html(html);
			var $this_messsage = $(".swf_remove_from_favourites").closest('.swf_container').find( '.swf_message' );
			$this_messsage.html(msg);
			$this_messsage.fadeIn();
			setTimeout(function(){ $this_messsage.fadeOut(); }, 4000);
		});
	});

	$(document).on( 'click', '.swf_remove_from_favourites', function(){
		var prod_id    = $(this).data().product_id;
		if( isNaN(prod_id) ){
			return;
		}
		prod_id = parseInt(prod_id);
		data = {
			prod_id:prod_id,
			action:'simple_ajax_remove_from_favourites',
			simple_favourites_nonce: swfAjax.nonce
		}
		$.post(swfAjax.ajaxurl, data, function(msg){
			location.reload();
		});
	});

	if( $( '#swf_favourites_display' ).length != 0 ){
		var max_height = 0;
		$('ul.products li.product').each(function(){
			max_height = $(this).height() > max_height ? $(this).height() : max_height;
		});
		$('ul.products li.product').height(max_height);
	}

});