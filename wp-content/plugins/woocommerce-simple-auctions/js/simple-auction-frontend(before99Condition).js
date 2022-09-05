jQuery(document).ready(function($){
	saajaxurl = SA_Ajax.ajaxurl;
	SA_last_activity = SA_Ajax.last_activity;
	running = false;
	var window_focus = true;
	var refreshIntervalId = '';
	if(data.interval){
		if(SA_Ajax.focus == 'yes'){
			$(window).on('focusin' , function() {
				window_focus = true;
			}).on('focusout', function() {
				window_focus = false;
			});
		}
	   refreshIntervalId =  setInterval(function(){
			//if(window_focus == true){
			   //getPriceAuction();
			   //Old Logic
 				updateAuction();
			//}
		}, data.interval*1000);
	}
	$( ".auction-time-countdown" ).each(function( index ) {
		var time 	= $(this).data('time');
		var format 	= $(this).data('format');
		if(format == ''){
			format = 'yowdHMS';
		}
		if(data.compact_counter == 'yes'){
			compact	 = true;
		} else{
			compact	 = false;
		}
		var etext ='';
		if($(this).hasClass('future') ){
			var etext = '<div class="started">'+data.started+'</div>';
		} else{
			var etext = '<div class="over">'+data.finished+'</div>';
		}
		/*****New Logic For time*************/
		var auction_id = jQuery(this).data('auctionid');
		if($(this).hasClass('future') && !$(this).hasClass('future_flash')){
			var future = 'yes';
		}else if($(this).hasClass('future_flash')){
			var future = 'future_flash';
		}else{
			var future = 'no';
		}
		if(future=='future_flash'){
			$( this).SAcountdown({
				//until:   $.SAcountdown.UTCDate(-(new Date().getTimezoneOffset()),new Date(time*1000)),
				until:   time,
				format: format,
				compact:  compact,
				onTick: watchCountdown,
				onExpiry: updateAuctionStatus,
				//expiryText: etext
			});
		}else{	
			jQuery.ajax({
			 type : "post",
			 url :'/getSec.php',
			 data : { 'product_id' : auction_id,'future' : future},
			 success: function(response) {
						$( ".auction-time-countdown").SAcountdown({
							//until:   $.SAcountdown.UTCDate(-(new Date().getTimezoneOffset()),new Date(time*1000)),
							until:   response,
							format: format,
							compact:  compact,
							onTick: watchCountdown,
							onExpiry: updateAuctionStatus,
							//expiryText: etext
						});
				}
			});
		}
		/*****END New Logic For time*************/
		/*****OLD Logic For time
		$( this).SAcountdown({
				until:   time,
				format: format,
				compact:  compact,
				onTick: watchCountdown,
				onExpiry: updateAuctionStatus,
			//expiryText: etext
		});
		**********/
	});
	function watchCountdown(periods) { 
		//console.log(periods);
		//alert('Just ' + periods[5] + ' minutes and ' + periods[6] + ' seconds to go'); 
		var auctionid = jQuery(this).data('auctionid');
		var status = jQuery(this).data('status');
		var future = jQuery(this).hasClass('future') ? 'true' : 'false';
		//console.log(future);
		var play_snipping = jQuery("#play_snipping").val();
		if(parseInt(periods[0]) == 0 && parseInt(periods[1]) == 0 && parseInt(periods[2]) == 0 && parseInt(periods[3]) == 0 && parseInt(periods[4]) == 0){
			if(parseInt(periods[5]) < 5 && play_snipping=='no'){
				//console.log("snipping");
				//playAudioLoop('http://51.79.16.47/~shopadoc/wp-content/uploads/sounds/087168277-helicopter-sound-effect.mp3');
				jQuery("#play_snipping").val("yes");
				//location.reload(true);
			}
			if(status=="win"){
			}else{
				if(parseInt(periods[5])==0 && parseInt(periods[6]) == 0){
					jQuery(".next_bid_txt .bid_amount_txt.amt").html('$<span class="underline_amt">0.00</span>');
					jQuery("div.biding_form .bid_amount_txt.amt").css('animation','none').css('-webkit-animation','none');
					//jQuery(".bid_now_img").hide();
					jQuery("#bid_anchor").removeAttr('href');
					jQuery(".bid_now_img").show();
					//updateAuctionStatus();
					//important when count to auction/flash end and auction going live
					if(jQuery(this).hasClass('future')){
						location.reload(true);
					}else{
						//updateAuctionStatus();
					}
					//location.reload(true);
				}
			}
		}
		//New Logic
		if(future =='false'){
			//updateAuction();
		}
	}
	$('form.cart').submit(function() {
		clearInterval(refreshIntervalId);
	});
	$( "input[name=bid_value]" ).on('changein', function( event ) {
		$(this).addClass('changein');
	});
	$( ".sealed-text a" ).on('click', function(e){
		e.preventDefault();
		$('.sealed-bid-desc').slideToggle('fast');
	});
	$( ".sa-watchlist-action" ).on('click', watchlist);
	function watchlist( event ) {
		var auction_id = jQuery(this).data('auction-id');
		var currentelement  =  $(this);
		jQuery.ajax({
		 type : "get",
		 url : SA_Ajax.ajaxurl,
		 data : { post_id : auction_id, 'wsa-ajax' : "watchlist"},
		 success: function(response) {
					 currentelement.parent().replaceWith(response);
					 $( ".sa-watchlist-action" ).on('click', watchlist);
					 jQuery( document.body).trigger('sa-wachlist-action',[response,auction_id] );
			}
		});}
	closeAuction();
});

function AjaxTest(already_bid) {
	var already_bid = jQuery('#already_bid').val();
	var bid_value = jQuery('#bid_value').val();
	if(bid_value < 0){
		jQuery('#winning_bid_msg').html('<div class="woocommerce-error green" role="alert">Winning Bid is $1</div>');
		jQuery('#winning_bid_msg').show();
		jQuery("#bid_anchor").css('position','relative').css("z-index",'1');
		setTimeout("jQuery('#winning_bid_msg').hide();",3000);
	}else if(already_bid=='yes'){
		jQuery('#no_bid_msg').html('<div class="woocommerce-error green" role="alert">Your bid is registered.</div>');
		jQuery('#no_bid_msg').show();
		jQuery("#bid_anchor").css('position','relative').css("z-index",'1');
		setTimeout("jQuery('#no_bid_msg').hide();",3000);
	}else{
		saajaxurl = SA_Ajax.ajaxurl;
		SA_last_activity = SA_Ajax.last_activity;
		var bid = jQuery('#bid').val();
		var bid_value = jQuery('#bid_value').val();
		var place_bid = jQuery('#place-bid').val();
		var product_id = jQuery('#product_id').val();
		var user_id = jQuery('#user_id').val();
		var currentelement  = jQuery(this);
		bid = bid.replace(/,/g, '');
		bid_value = bid_value.replace(/,/g, '');
		place_bid = place_bid.replace(/,/g, '');
			jQuery.ajax({
				type : "post",
				url : SA_Ajax.ajaxurl+"=place_auction_bid",
				data : { bid : bid,bid_value : bid_value,'place-bid' : place_bid,product_id : product_id,user_id : user_id, 'wsa-ajax' : "place_auction_bid", "last_activity" : SA_last_activity},
				beforeSend: function(){
					// Show image container
					//jQuery(".ajax_loader").show();
					//jQuery("#auction_form").hide();
					//jQuery(".current_bid").hide();
					//	jQuery("#bid_anchor").css('position','relative').css("z-index",'-1')
					playAudio('/wp-content/uploads/sounds/Money%20(MP3).mp3');
				},
				success: function(response){
					//$('.response').empty();
					//$('.response').append(response);
					//alert(response);
					if (typeof response.activity != 'undefined') {
						SA_last_activity = response.activity;
	
					}
					if (typeof response.curent_bid != 'undefined' ) {
						//jQuery(".current_bid .bid_amount_txt").html(response.curent_bid);
					}
					if (typeof response.bid_value != 'undefined' ) {
							//jQuery(".next_bid").html('$'+response.bid_value);
						}
					if (typeof response.msg != 'undefined' ) {	
						//jQuery('.woocommerce-notices-wrapper').html(response.msg);
						//jQuery("html, body").animate({ scrollTop: 0 }, "slow");
					}
					//return false;
				},
				complete:function(response){
					// Hide image container
					jQuery("#bid_anchor").css('position','relative').css("z-index",'1');
					jQuery(".ajax_loader").hide();
					jQuery("#auction_form,.current_bid").show();
					jQuery(".current_bid").show();
					var html_bid='<div class="woocommerce-notices-wrapper" id="no_bid_msg" style="display: none;"></div>';
					jQuery('#bid_msg').html(html_bid);
					//jQuery('#no_bid_msg').show();
					jQuery('#already_bid').val('yes');
					jQuery('#auction_with_bid').val('yes');
					//jQuery('#winner_screen').val('yes');
					
					//setTimeout("jQuery('#no_bid_msg').hide();",3000);
				}
			});
		}
	}
	function updateFlashStatus(){
		jQuery("#auction_status").html('countdown to Flash Bid Cycle<span class="TM_flash">®</span>');
		jQuery(".current_bid .woocommerce-Price-amount.amount").text('none');
		jQuery(".next_bid.bid_amount_txt.amt").html('$<span class="underline_amt">0.00</span>');
		jQuery("div.biding_form .bid_amount_txt.amt").css('animation','none').css('-webkit-animation','none');
		jQuery(".auction_detail").addClass('upcomming_flash');
		//var html = '<div class="main-auction auction-time-countdown" id="flash_countdown" data-time="'+jQuery('#flash_countdown_sec').val()+'" data-auctionid="'+jQuery(this).data('auctionid')+'" data-format="yowdHMS"></div>';
		//jQuery("#countdown").html(html);
		//jQuery("#countdown").addClass('future');
		//Important
		/*jQuery('#flash_countdown').SAcountdown({
			until:   jQuery.SAcountdown.UTCDate(-(new Date().getTimezoneOffset()),new Date(jQuery('#flash_countdown_sec').val()*1000)),
			format: 'yowdHMS',
			compact:  compact,
			onTick: watchCountdown,
			onExpiry: closeAuction,
			//expiryText: etext
		});*/
	}
	function updateAuctionStatus(){
		var auction_with_bid = jQuery('#auction_with_bid').val();
		if(auction_with_bid=='no'){
			var auction_live_type = jQuery('#auction_live_type').val();
			jQuery(".mejs-playpause-button button").click();
			jQuery("#auction_status").html('ended');
			jQuery(".next_bid.bid_amount_txt.amt").html('$<span class="underline_amt">0.00</span>');
			jQuery("div.biding_form .bid_amount_txt.amt").css('animation','none').css('-webkit-animation','none');
			jQuery(".next_bid.bid_amount_txt.amt").removeClass('next_bid');
			jQuery(".current_bid .woocommerce-Price-amount.amount").text('none');
			jQuery(".auction_detail.live").html('<span id="auction_status">ended</span>');
			jQuery(".auction_detail.live").removeClass('live');
			
			playAudio('/wp-content/uploads/sounds/auction_failure.mp3');
			if(auction_live_type=='normal'){
				setTimeout("location.reload();",61000);
			}
			
			if(auction_live_type=='flash'){
				var seller_screen = jQuery('#seller_screen').val();
				if(seller_screen=='yes'){
					//Important seller only
					var timeLeft = 180;
					var elem = document.getElementById('timer_div');
					var timerId = setInterval(countdown_flash, 1000);
					function countdown_flash() {
					  if (timeLeft == 0) {
						clearTimeout(timerId);
						elem.innerHTML = '0 seconds remaining';
						window.location.replace(jQuery('#product_url').val()+"?action=expire");
					  } else {
						elem.innerHTML = timeLeft + ' seconds remaining';
						timeLeft--;
					  }
					}
					//jQuery(".sgpb-content-1640,.sgpb-popup-overlay-1640").show();
					jQuery(".sgpb-popup-overlay-1640").attr('style', 'z-index: 9999; background-color: black; opacity: 0.8; position: fixed; left: 0px; top: 0px; width: 100%; height: 100%;display:block !important;');
					jQuery(".sgpb-content-1640").attr('style', 'box-sizing: content-box; min-width: 320px; max-height: 259px; max-width: 1342px; border-style: solid; border-color: rgb(255, 0, 0); border-width: 0px; padding: 7px; width: 299.504px; background-repeat: no-repeat; background-position: center center; background-color: rgb(255, 255, 255); box-shadow: rgb(204, 204, 204) 0px 0px 0px 14px; overflow: auto;display:block !important;');
				}
			}
		}
		if(auction_with_bid=='yes'){
			jQuery.ajax({	
			url:'/ajax.php',	
			type:'POST',
			cache : false,
			data:{'mode':'checkExtend','product_id':jQuery('#product_id').val()},
			beforeSend: function() {},
			complete: function() {},
			success:function (data){
					if(data=='yes'){
						window.location.replace(jQuery('#product_url').val()+"?action=extended");
					}else{
						var winner_screen = jQuery('#winner_screen').val();
						if(winner_screen=='yes'){
							jQuery("#auction_status").html('✓ Email (Spam)');
							jQuery("#auction_status").addClass('red');
							jQuery(".auction_detail.live,.auction_detail.extend").html('<span class="red" id="auction_status">✓ Email (Spam)</span>');
							jQuery(".auction_detail.live").removeClass('live');
							jQuery(".auction_detail.extend").removeClass('extend');
						}else{
							jQuery("#auction_status").html('ended');
							jQuery(".auction_detail.live,.auction_detail.extend").html('<span id="auction_status">ended</span>');
							jQuery(".auction_detail.live").removeClass('live');
							jQuery(".auction_detail.extend").removeClass('extend');
						}
						jQuery(".mejs-playpause-button button").click();
						//jQuery(".current_bid .woocommerce-Price-amount.amount").text('none');
						jQuery(".next_bid.bid_amount_txt.amt").html('$<span class="underline_amt">0.00</span>');
						jQuery("div.biding_form .bid_amount_txt.amt").css('animation','none').css('-webkit-animation','none');
						jQuery(".next_bid.bid_amount_txt.amt").removeClass('next_bid');
						playAudio('/wp-content/uploads/sounds/auction_sucess.mp3');
					}
				}
			});
			
		}
	}
	
function closeAuction(){

		var auctionid = jQuery(this).data('auctionid');
		var future = jQuery(this).hasClass('future') ? 'true' : 'false';
		var ajaxcontainer = jQuery(this).parent().next('.auction-ajax-change');

		ajaxcontainer.empty().prepend('<div class="ajax-working"></div>');
		ajaxcontainer.parent().children('form.buy-now').remove();

		var ajaxurl = saajaxurl+'=finish_auction';

		jQuery( document.body).trigger('sa-close-auction',[auctionid]);
		request =  jQuery.ajax({
		 type : "post",
		 url : ajaxurl,
		 cache : false,
		 data : {action: "finish_auction", post_id : auctionid, ret: ajaxcontainer.length, future: future},
		 success: function(response) {
					if (response.length  != 0){
						ajaxcontainer.children('.ajax-working').remove();
						ajaxcontainer.prepend(response);
						jQuery( document.body).trigger('sa-action-closed',[auctionid]);
						//Mujahid Code
						//location.reload();
						//updateAuctionStatus();
					}
				}
		});
}

function updateAuction(){
	// var auction_ids={};

	// jQuery(".auction-price").each(function(){
	//     	var auction_id = jQuery(this).data('auction-id');
	//     	var auction_bid = jQuery(this).data('bid');
	//     	var auction_status= jQuery(this).data('status');
	// 		auction_ids [auction_id]= {'price': auction_bid , 'status': auction_status};
	// });
	// if(jQuery.isEmptyObject(auction_ids)){
	// 	return;
	// }
	var product_id = jQuery("#product_id").val();
	var current_user_ID = jQuery("#current_user_ID").val();
	var seller_screen = jQuery("#seller_screen").val();
	var current_bid = jQuery('.auction-price.current-bid').data('bid');
	var price = jQuery(".desktop_price .underline_amt").text();
	price = parseInt(price.replace(/,/g, ""));
	var distance = parseInt(jQuery(".price_box_right #distance_txt").text());
	
	running = true;
	var ajaxurl = saajaxurl+'=get_update_price_for_auctions';
	jQuery.ajax({
		type : "post",
		encoding:"UTF-8",
		url : '/getAuctionData.php',
		dataType: 'json',
		data : {action: "get_update_price_for_auctions",mode:'checkBidUpdate', "product_id" : product_id,'current_bid':current_bid,"last_activity" : SA_last_activity,'seller_screen':seller_screen,'current_user_ID':current_user_ID,'price':price,'distance':distance},
		success: function(response) {
			if(response != null ) {
				//bid_value==value==max
				//next_bid.bid_amount_txt.amt
				if(response.add_to_cart_text!='Auction finished'){
					if(response.response_mode=='update_distance'){
						jQuery(".price_box_right #distance_txt").text(response.distance);
					}else if(response.response_mode=='update_price'){
						jQuery("#bid_value").val(response.price);
						jQuery("#bid_value").attr('max',response.price);
						jQuery(".next_bid,.countdown_price").html('<span class="woocommerce-Price-currencySymbol">$</span><span class="underline_amt">'+response.price+"</span>");
						jQuery(".desktop_price .underline_amt,.mobile_price .underline_amt").text(response.price);
					}else{
						if(response.curent_bider != response.curent_id){
							jQuery("#already_bid").val('no');
						}
						if(response.bid_value==0){}
						jQuery("#bid_value").val(response.bid_value);
						jQuery("#bid_value").attr('max',response.bid_value);
						jQuery(".next_bid").html('<span class="woocommerce-Price-currencySymbol">$</span><span class="underline_amt">'+response.bid_value+"</span>");
						if(response.curent_bider !=""){
							jQuery('.woocommerce-Price-amount_new').remove();
							jQuery(".current_bid_txt").html(response.curent_bid);
						}
						jQuery('#winner_screen').val(response.winner_screen);
					}
				}
			//console.log(response);

		 }

		 jQuery( document.body).trigger('sa-action-price-respons', response);
		 running = false;
	 },
	 error: function() {
		running = false;
	 }
	});
}

function getPriceAuction(){
	if(jQuery('.auction-price').length<1){
		return;
	}
	if (running == true){
		return;
	}
	// var auction_ids={};
	// jQuery(".auction-price").each(function(){
	//     	var auction_id = jQuery(this).data('auction-id');
	//     	var auction_bid = jQuery(this).data('bid');
	//     	var auction_status= jQuery(this).data('status');
	// 		auction_ids [auction_id]= {'price': auction_bid , 'status': auction_status};
	// });
	// if(jQuery.isEmptyObject(auction_ids)){
	// 	return;
	// }
	running = true;
	var ajaxurl = saajaxurl+'=get_price_for_auctions';
	var product_id = jQuery("#product_id").val();
	jQuery.ajax({
		type : "post",
		encoding:"UTF-8",
		url : ajaxurl,
		dataType: 'json',
		data : {action: "get_price_for_auctions", "last_activity" : SA_last_activity},
		success: function(response) {
			if(response != null ) {
				if (typeof response.last_activity != 'undefined') {
					SA_last_activity = response.last_activity;
				}
				jQuery.each( response, function( key, value ) {
					auction = jQuery("body").find(".auction-price[data-auction-id='" + key + "']");
					auction.replaceWith(value.curent_bid);
					/*
					if(value.add_to_cart_text != 'Auction finished' && key == product_id){
						if(value.curent_bider != value.curent_id){
							jQuery("#already_bid").val('no');
						}
						jQuery("#bid_value").val(value.bid_value);
						jQuery("#bid_value").attr('max',value.bid_value);
						jQuery(".next_bid").html('<span class="woocommerce-Price-currencySymbol">$</span><span class="underline_amt">'+value.bid_value+"</span>");
						if(value.curent_bider !=""){
							jQuery(".current_bid_txt").html(value.curent_bid);
						}
						jQuery('#winner_screen').val(value.winner_screen);
					}
					*/
					//Mujahid Code
					//alert(value.bid_value);
					if (typeof value.bid_value != 'undefined' ) {
						//jQuery(".next_bid").html('$'+value.bid_value);
					}

					//jQuery("body").find("[data-auction-id='" + key + "']").addClass('changed blink').fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100, function(){jQuery(this).removeClass('blink');});
					if (typeof value.timer != 'undefined') {
						var curenttimer = jQuery("body").find(".auction-time-countdown[data-auctionid='" + key + "']");
						if(curenttimer.attr('data-time') != value.timer){
							curenttimer.attr('data-time',value.timer );
							//location.reload();
							//curenttimer.SAcountdown('option',  'until',  jQuery.SAcountdown.UTCDate(-(new Date().getTimezoneOffset()),new Date(value.timer*1000)) );

							//curenttimer.addClass('changed blink').fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100, function(){jQuery(this).removeClass('blink');});
						}
					}
					if (typeof value.curent_bider != 'undefined' ) {
						var curentuser = jQuery("input[name=user_id]");
						var mainauction = jQuery("input[name=place-bid]").val();
						if (curentuser.length){
							if(value.curent_bider != curentuser.val() && mainauction == key ){
								jQuery('.woocommerce-message:contains("'+  data.no_need  +'")').replaceWith(data.outbid_message );
							}
						}
						if(jQuery( "span.winning[data-auction_id='"+key+"']" ).attr('data-user_id') != value.curent_bider){
							jQuery( "span.winning[data-auction_id='"+key+"']" ).remove()
						}
					}
					if (typeof value.bid_value != 'undefined' ) {
						if(!jQuery( "input[name=bid_value][data-auction-id='"+key+"']" ).hasClass('changedin')){
							jQuery( "input[name=bid_value][data-auction-id='"+key+"']" ).val(value.bid_value).removeClass('changedin');
						}
					}
					if (typeof value.reserve != 'undefined' ) {
						jQuery( ".auction-ajax-change .reserve[data-auction-id='"+key+"']" ).text(value.reserve);
					}
					if (typeof value.activity != 'undefined' ) {
						jQuery("#auction-history-table-" + key +" tbody > tr:first" ).before(value.activity);
						jQuery("#auction-history-table-" + key +" tbody > tr:first" ).addClass('changed blink').fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100, function(){jQuery(this).removeClass('blink');})
					}
					if (typeof value.add_to_cart_text != 'undefined' ) {
						jQuery( "a.button.product_type_auction[data-product_id='"+key+"']" ).text(value.add_to_cart_text);
					}
					jQuery( document.body).trigger('sa-action-price-changed',[key, value]);
				});
			//console.log(response);
		 }
		 jQuery( document.body).trigger('sa-action-price-respons', response);
		 running = false;
	 },
	 error: function() {
		running = false;
	 }
	});
}
jQuery(function($){$(".auction_form div.quantity:not(.buttons_added),.auction_form td.quantity:not(.buttons_added)").addClass("buttons_added").append('<input type="button" value="+" class="plus" />').prepend('<input type="button" value="-" class="minus" />'),$(document).on("click",".auction_form .plus,.auction_form .minus",function(){var t=$(this).closest(".quantity").find("input[name=bid_value]"),a=parseFloat(t.val()),n=parseFloat(t.attr("max")),s=parseFloat(t.attr("min")),e=t.attr("step");a&&""!==a&&"NaN"!==a||(a=0),(""===n||"NaN"===n)&&(n=""),(""===s||"NaN"===s)&&(s=0),("any"===e||""===e||void 0===e||"NaN"===parseFloat(e))&&(e=1),$(this).is(".plus")?t.val(n&&(n==a||a>n)?n:a+parseFloat(e)):s&&(s==a||s>a)?t.val(s):a>0&&t.val(a-parseFloat(e)),t.trigger("change")})});