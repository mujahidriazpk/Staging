<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package dokan
 * @package dokan - 2014 1.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<style type="text/css">
.media-frame-menu, .media-frame-menu-heading {
    display: none!important;
}
.dokan-new-product-area .dokan-btn:hover, .dokan-new-product-area .dokan-btn:focus {
    color: #000 !important;
    border-color: transparent !important;
    background-color: transparent !important;
    outline: thin dotted #333 !important;
}
</style>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-166289038-1"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());

gtag('config', 'UA-166289038-1');
</script>
<!-- Google Tag Manager --> 
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-58LBM3N');</script> 
<!-- End Google Tag Manager -->
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<!--<meta name="viewport" content="width=device-width, initial-scale=1.0">-->
<meta name = "viewport" content ="width=device-width, minimum-scale=1.0, maximum-scale = 1.0, user-scalable = no">
<title>
<?php wp_title( '|', true, 'right' ); ?>
</title>
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='expires' content='0'>
<meta http-equiv='pragma' content='no-cache'>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico" />
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/html5.js" type="text/javascript"></script>
<![endif]-->
<?php wp_head(); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.css" rel="stylesheet"  type='text/css'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script> 
<script src="<?php echo get_template_directory_uri(); ?>-child/jquery.maskedinput.js"></script> 
<script type="text/javascript">
function playVideoIcon(){
		jQuery(".jconfirm-closeIcon").click();		
		jQuery(".elementor-custom-embed-image-overlay").click();
	}
jQuery(document).ready(function() {
	
		/*var txt = 'v=1&tid=UA-166289038-1&cid=1379762423.1647958985&t=event&ni=1&ec=Advanced%20Ads&ea=Impressions&dl=https%3A%2F%2Fshopadoc.com%2Fshopadoc-auction-activity%2F&dp=%2Fshopadoc-auction-activity%2F&el=%5B6047%5D%20D%20Client%2003%2F01%2F22%20-%2012%2F31%2F22'+'\n'+'v=1&tid=UA-166289038-1&cid=1379762423.1647958985&t=event&ni=1&ec=Advanced%20Ads&ea=Impressions&dl=https%3A%2F%2Fshopadoc.com%2Fshopadoc-auction-activity%2F&dp=%2Fshopadoc-auction-activity%2F&el=%5B6047%5D%20D%20Client%2003%2F01%2F22%20-%2012%2F31%2F22'+'\n'+'v=1&tid=UA-166289038-1&cid=1379762423.1647958985&t=event&ni=1&ec=Advanced%20Ads&ea=Impressions&dl=https%3A%2F%2Fshopadoc.com%2Fshopadoc-auction-activity%2F&dp=%2Fshopadoc-auction-activity%2F&el=%5B6047%5D%20D%20Client%2003%2F01%2F22%20-%2012%2F31%2F22';
		jQuery.post('https://www.google-analytics.com/batch',txt);*/
		//jQuery.post('https://www.google-analytics.com/collect','v=1&tid=UA-166289038-1&cid=1379762423.1647958985&t=event&ni=1&ec=Advanced%20Ads&ea=Impressions&dl=https%3A%2F%2Fshopadoc.com%2Fshopadoc-auction-activity%2F&dp=%2Fshopadoc-auction-activity%2F&el=%5B6047%5D%20D%20Client%2003%2F01%2F22%20-%2012%2F31%2F22');
	//document.cookie = "firstTimeTest=true; max-age=-1";
	 if (document.cookie.indexOf("firstTimeTest=true") == -1) {
			//document.cookie = "firstTimeTest=true; max-age=86400"; // 86400: seconds in a day
			//Custom_popup_new("WELCOME TO SHOPADOC®",'Please view <img loading="lazy" class="alignnone wp-image-1831" src="/wp-content/uploads/2019/11/Screen-Shot-2019-11-25-at-5.10.29-AM.png" alt="" width="113" height="33"> & <img src="/wp-content/uploads/2020/12/play.png" class="attachment-full size-full" alt="" loading="lazy" width="42" height="42" onclick="playVideoIcon();"><br />Thanks for visiting',"col-md-6 col-md-offset-3 no-button");
	  }
	/*jQuery(document).on('UploadProgress','#__wp-uploader-id-0,#__wp-uploader-id-1,#__wp-uploader-id-2,#__wp-uploader-id-3,#__wp-uploader-id-4',function(){
		console.log("test");
	});*/
	//jQuery("#__wp-uploader-id-0,#__wp-uploader-id-1,#__wp-uploader-id-2,#__wp-uploader-id-3,#__wp-uploader-id-4").on('all', function(e) { console.log(e); });
	// Hack for "Upload New Media" Page (old uploader)


if (typeof wp.Uploader !== 'undefined' && typeof wp.Uploader.queue !== 'undefined') {
    wp.Uploader.queue.on('reset', function() { 
      //  console.log('Upload Complete! 2');
		jQuery(".media-frame-router .media-router .spinner").remove();
    });
}
if (typeof wp.Uploader !== 'undefined' && typeof wp.Uploader.queue !== 'undefined') {
    wp.Uploader.queue.on('add', function() { 
      //  console.log('image uploaded!');
	 	 jQuery(".media-frame-router .media-router .spinner").remove();
		// jQuery(".attachment-details.uploading.save-ready .attachment-info .thumbnail.thumbnail-image")
		jQuery(".media-frame-router .media-router").append('<span class="spinner" style="visibility:visible;float:left;margin:5px 0 0 10px;"></span>')
    });
	
}
window.addEventListener( "pageshow", function ( event ) {
  var historyTraversal = event.persisted || ( typeof window.performance != "undefined" && window.performance.navigation.type === 2 );
  if ( historyTraversal ) {
    // Handle page restore.
    //alert('refresh');
    window.location.reload();
  }
});

window.addEventListener( "pageshow", function ( event ) {
  var historyTraversal = event.persisted || ( typeof window.performance != "undefined" && window.performance.navigation.type === 2 );
  if ( historyTraversal ) {
    // Handle page restore.
    //alert('refresh');
    window.location.reload();
  }
});
 
	var userAgent = window.navigator.userAgent;
	if (userAgent.match(/iPad/i) || userAgent.match(/iPhone/i)) {
	   jQuery(window).scroll(function() {
			 var scrolledY = jQuery(window).scrollTop();
			 //jQuery('body').css('background-position', 'center ' + ((scrolledY)) + 'px');
			  jQuery('body').attr('style', 'background-position: center '+ ((scrolledY)) + 'px !important');
			 // jQuery('body').attr('style', 'background-attachment:scroll !important;background-size:auto 100vh !important;');
			
			  
		});
	}
	/*if (userAgent.match(/AppleWebKit/i)) {
	   jQuery(window).scroll(function() {
			 var scrolledY = jQuery(window).scrollTop() - 200;
			 //jQuery('body').css('background-position', 'center ' + ((scrolledY)) + 'px');
			  jQuery('body').attr('style', 'background-position: center '+ ((scrolledY)) + 'px !important');
		});
	}*/
	
	<?php global $post;?>
	<?php if($post->ID == 99 && !empty($_POST['wpforms'])){?>
		jQuery('#wpforms-895-field_4').val('<?php echo $_POST['wpforms']['fields'][4];?>');
		jQuery('#wpforms-895-field_45_1').attr('checked','checked');
	<?php }?>
	<?php if(is_front_page()){?>
		<?php if(!get_current_user_id()){?>
			var windowsize = jQuery(window).width();
			if(windowsize <= 448){
				jQuery("#responsive-menu-button").css('margin-right',"50px").css('margin-top','50px');
				//jQuery(".responsive-menu-button-text").css({ 'color': 'white'});
			}else{
				//jQuery("#responsive-menu-button").css('margin-right',jQuery(".nav.navbar-nav.navbar-right").width()+55+"px");
				jQuery("#responsive-menu-button").css('top',"60px").css('right',"65px");
			}
		<?php }else{?>
			//jQuery("#responsive-menu-button").css('margin-right',jQuery(".nav.navbar-nav.navbar-right").width()+55+"px");
			jQuery("#responsive-menu-button").css('top',"60px").css('right',"65px");
		<?php }?>
	<?php }else{?>
		jQuery("#responsive-menu-button").css('margin-right',jQuery(".nav.navbar-nav.navbar-right").width()+25+"px").css('top',"5px");
	<?php }?>
	
	jQuery("#billing_phone").mask("999-999-9999");
	var card_saved = parseInt(jQuery("ul.wc-saved-payment-methods").data('count'));
	if(card_saved > 0){
		jQuery("#wc-yith-stripe-cc-form").hide();
	}
	jQuery('input[type=radio][name="wc-yith-stripe-payment-token"]').change(function() {
    if (this.value == 'new') {
       jQuery("#wc-yith-stripe-cc-form").show();
    }else{
		jQuery("#wc-yith-stripe-cc-form").hide();
    }
});
	jQuery(document).on('click','img.img_size_2',function(){
		jQuery(".termsagree").click();
	});
	
	jQuery(document).on('click','.Zebra_Tooltip_Message span',function(){
		window.location.replace("<?php echo get_site_url().'/contact/';?>");
	});
	jQuery(".sgpb-popup-dialog-main-div-wrapper,.sgpb-theme-1-overlay").css('display','none !important');
	jQuery(".woocommerce-product-gallery img").removeAttr('title');
	//jQuery('.media-toolbar-primary.search-form').prepend('<button type="button" class="button media-button button-primary button-large media-button-select">Delete image</button>');
	jQuery(".media-modal-close").html('Back');
	jQuery(".media-modal-close").addClass('button button-primary bk-btn-media');
	jQuery(document).on('click','ul.attachments li',function(){
		var attachment_id = jQuery(this).data('id');
		//alert(attachment_id);
		jQuery('.search-form .button-grey').remove();
		if (jQuery(".media-toolbar .media-toolbar-primary.search-form button").attr('disabled')) {
		  //jQuery('#delete_img'+attachment_id).remove(this);
		  jQuery('#delete_img'+attachment_id).attr('disabled','disabled');
		} else {
			//jQuery('#delete_img'+attachment_id).remove(this);
			//jQuery('.media-toolbar-primary.search-form #delete_img'+attachment_id).remove(this);
			jQuery('.media-toolbar-primary.search-form').prepend('<button type="button" id="delete_img'+attachment_id+'" class="button media-button button-grey button-large media-button-select" sty>Delete</button>');
			
		}
	});
	jQuery('.auction_no_link').bind("click",function(){
			var auction_no = jQuery('.accordion_div').text();;
           	Custom_popup("Auction #",auction_no,"col-md-12 col-md-offset-3 no-button");
     });
	jQuery('.ad-analytics-img').bind("click",function(){
			//var auction_no = '<img src="<?php echo home_url('/wp-content/themes/dokan-child/ad_analyticsNew1.jpg');?>" title="analytics" />';
           	//Custom_popup("",auction_no,"col-md-6 col-md-offset-3 no-button");
     });
	 jQuery('.ad-analytics-img').bind("click",function(){
			var auction_no = '<img src="<?php echo home_url('/wp-content/themes/dokan-child/ad_analyticsNew1.jpg');?>" title="analytics" style="width:100%;" />';
           	Custom_popup("",auction_no,"col-md-4 col-md-offset-5 no-button");
     });
	  jQuery('.ad-analytics-img_adver').bind("click",function(){
			var auction_no = '<iframe width="450" height="515" src="<?php echo home_url('ad-analytics/?period=last7days&mode=popup'); ?>" frameborder="0" allowfullscreen></iframe>';
           	Custom_popup_iframe("",auction_no,"col-md-5 col-md-offset-4 no-button",true);
     });
	 
	/* jQuery( ".rotation_main .my-ad" ).each(function( index ) {
		  jQuery(this).attr('data-link',jQuery(this).attr('href'));
		  jQuery(this).attr('href','javascript:');
	});*/
	 jQuery(document).on('click','.rotation_main .my-ad',function(){
		 var linkURL = jQuery(this).attr('data-href');
		 if(linkURL !='#'){
		 		jQuery.confirm({
								title: '',
								columnClass: 'col-md-4 col-md-offset-4',
								closeIcon: true, // hides the close icon.
								content: '<span style="font-size:14px;font-weight:bold;">LEAVING SHOPADOC®</span><br />',
								buttons: {
									Yes: {
										text: 'Confirm',
										btnClass: 'yes_btn',
										action: function(){
											openLink(linkURL);
										}
									},
									No: {
										text: 'Cancel',
										btnClass: 'btn-blue no_btn',
										action: function(){
											//jQuery("#plan_deactive").removeAttr('checked');
										}
									}
								}
							});
	 			}
				//trashAttachment();
			});
	jQuery(document).on('click','.search-form .button-grey',function(){
				//jQuery(".button-link.delete-attachment").click();
				jQuery(".button-link.delete-attachment").addClass('trash-attachment');
				jQuery(".button-link.trash-attachment").removeClass('delete-attachment');
				jQuery.confirm({
								title: 'Please Confirm',
								columnClass: 'col-md-4 col-md-offset-4',
								closeIcon: true, // hides the close icon.
								content: 'Delete selected item<br />',
								buttons: {
									Yes: {
										text: 'Yes',
										btnClass: 'yes_btn',
										action: function(){
											jQuery('.media-toolbar-primary.search-form .button-grey').remove();
											//jQuery('#delete_img.button-grey').off('click');
											jQuery(".button-link.trash-attachment").click();
										}
									},
									No: {
										text: 'No',
										btnClass: 'btn-blue no_btn',
										action: function(){
											//jQuery("#plan_deactive").removeAttr('checked');
										}
									}
								}
							});
				//trashAttachment();
			});
	jQuery(document).on('click','#responsive-menu-item-2305',function(){
		jQuery('html').removeClass('responsive-menu-open');
		jQuery('#responsive-menu-button').removeClass('is-active');
		//jQuery('a.register').click();
	});
	jQuery(document).on('click','.elementor-custom-embed-image-overlay',function(){
		setTimeout("jQuery('.elementor-custom-embed-image-overlay img').attr('src','<?php echo get_site_url();?>/wp-content/themes/dokan-child/replay.png');",2000);
		//jQuery('.elementor-custom-embed-image-overlay img').attr('src','<?php echo get_site_url();?>/wp-content/themes/dokan-child/replay.png');
		//jQuery('a.register').click();
	});
	
});
//Not in use
function startSlideNew(seconds){
	if(seconds==''){
		var seconds = parseInt(jQuery(".countdown_amount.Seconds").text());
	}
	var current_sec =60;
	var current = 1;
	if(seconds>=0 && seconds<=6){
		current_sec = 6;
		current = 10;
	}else if(seconds>=7 && seconds<=12){
		current_sec = 12;
		current = 9;
	}else if(seconds>=13 && seconds<=18){
		current_sec = 18;
		current = 8;
	}else if(seconds>=19 && seconds<=24){
		current_sec = 24;
		current =7;
	}else if(seconds>=25 && seconds<=30){
		current_sec = 30;
		current = 6;
	}else if(seconds>=31 && seconds<=36){
		current_sec = 36;
		current = 5;
	}else if(seconds>=37 && seconds<=42){
		current_sec = 42;
		current = 4;
	}else if(seconds>=43 && seconds<=48){
		current_sec = 48;
		current = 3;
	}else if(seconds>=49 && seconds<=54){
		current_sec = 54;
		current = 2;
	}else if(seconds>=55 && seconds<=60){
		current_sec = 60;
		current = 1;
	}
	
	 var sec_remain = current_sec - seconds;
	//console.log(current_sec+" - "+seconds+" = "+sec_remain);
	jQuery(".rotation_set").hide();
	jQuery("#rotation_set_"+current).show();
	
	setTimeout("trackSlide("+current+");",5000);
 	setTimeout(function(){
	 var seconds_new = parseInt(jQuery(".countdown_amount.Seconds").text());
 	 startSlideNew(seconds_new);
	 //console.log(seconds_new);
    }, sec_remain * 1000);
	//console.log(next_slide_time);
}
function worldClock(zone, region){
var dst = 0
var time = new Date()
var gmtMS = time.getTime() + (time.getTimezoneOffset() * 60000)
var gmtTime = new Date(gmtMS)
var day = gmtTime.getDate()
var month = gmtTime.getMonth()
var year = gmtTime.getYear()
if(year < 1000){
year += 1900
}
var monthArray = new Array("January", "February", "March", "April", "May", "June", "July", "August", 
				"September", "October", "November", "December")
var monthDays = new Array("31", "28", "31", "30", "31", "30", "31", "31", "30", "31", "30", "31")
if (year%4 == 0){
monthDays = new Array("31", "29", "31", "30", "31", "30", "31", "31", "30", "31", "30", "31")
}
if(year%100 == 0 && year%400 != 0){
monthDays = new Array("31", "28", "31", "30", "31", "30", "31", "31", "30", "31", "30", "31")
}

var hr = gmtTime.getHours() + zone
var min = gmtTime.getMinutes()
var sec = gmtTime.getSeconds()

if (hr >= 24){
hr = hr-24
day -= -1
}
if (hr < 0){
hr -= -24
day -= 1
}
if (hr < 10){
hr = " " + hr
}
if (min < 10){
min = "0" + min
}
if (sec < 10){
sec = "0" + sec
}
if (day <= 0){
if (month == 0){
	month = 11
	year -= 1
	}
	else{
	month = month -1
	}
day = monthDays[month]
}
if(day > monthDays[month]){
	day = 1
	if(month == 11){
	month = 0
	year -= -1
	}
	else{
	month -= -1
	}
}
if (region == "NAmerica"){
	var startDST = new Date()
	var endDST = new Date()
	startDST.setMonth(3)
	startDST.setHours(2)
	startDST.setDate(1)
	var dayDST = startDST.getDay()
	if (dayDST != 0){
		startDST.setDate(8-dayDST)
		}
		else{
		startDST.setDate(1)
		}
	endDST.setMonth(9)
	endDST.setHours(1)
	endDST.setDate(31)
	dayDST = endDST.getDay()
	endDST.setDate(31-dayDST)
	var currentTime = new Date()
	currentTime.setMonth(month)
	currentTime.setYear(year)
	currentTime.setDate(day)
	currentTime.setHours(hr)
	if(currentTime >= startDST && currentTime < endDST){
		dst = 1
		}
}



	
if (dst == 1){
	hr -= -1
	if (hr >= 24){
	hr = hr-24
	day -= -1
	}
	if (hr < 10){
	hr = " " + hr
	}
	if(day > monthDays[month]){
	day = 1
	if(month == 11){
	month = 0
	year -= -1
	}
	else{
	month -= -1
	}
	}
return  sec;
}
else{
return sec;
}
}
function startSlide_NEW(){
	var time = new Date()
	//var gmtMS = time.getTime() + (time.getTimezoneOffset() * 60000)
	//var gmtTime = new Date(gmtMS);
	var seconds = time.getSeconds();

	//var seconds = worldClock(-8, "NAmerica");
	var current = 1;
	var current_sec;
	if(seconds>=0 && seconds<=6){
		current_sec = 6 - seconds;
		current = 1;
	}else if(seconds>=7 && seconds<=12){
		current_sec = 12 - seconds;
		current = 2;
	}else if(seconds>=13 && seconds<=18){
		current_sec = 18 - seconds;
		current = 3;
	}else if(seconds>=19 && seconds<=24){
		current_sec = 24 - seconds;
		current =4;
	}else if(seconds>=25 && seconds<=30){
		current_sec = 30 - seconds;
		current = 5;
	}else if(seconds>=31 && seconds<=36){
		current_sec = 36 - seconds;
		current = 6;
	}else if(seconds>=37 && seconds<=42){
		current_sec = 42 - seconds;
		current = 7;
	}else if(seconds>=43 && seconds<=48){
		current_sec = 48 - seconds;
		current =8;
	}else if(seconds>=49 && seconds<=54){
		current_sec = 54 - seconds;
		current = 9;
	}else if(seconds>=55 && seconds<=60){
		current_sec = 60 - seconds;
		current = 10;
	}
	//console.log("RotationNumber == "+current+" and remain Sec == "+current_sec);
	 jQuery(".rotation_set").hide();
	jQuery("#rotation_set_"+current).show();
	trackSlide(current);
	//setTimeout("trackSlide("+current+");",current_sec * 1000);
	current++;
	setTimeout("startSlide("+current+");",current_sec * 1000);
}
function startSlide(current){
	 if(current > 10&& 1==2){
		 startSlide(1);
	 }else{
		
		// setTimeout("trackSlide("+current+");",5000);
		// current++;
		 var time = new Date();
		 var seconds = time.getSeconds();
		 if (navigator.userAgent.includes("Mac")) {
			  // Do something if the browser is running on iOS
			  //console.log("This browser is running on iOS");
				if(seconds >= 58){
					seconds = 0;
				}else{
					seconds = seconds + 2;
				}
				
			} else {
			  // Do something if the browser is not running on iOS
			  //console.log("This browser is not running on iOS");
			}
		 if(seconds>=0 && seconds <= 6){
			current_sec = 6 - seconds;
			current = 1;
		}else if(seconds>=7 && seconds<=12){
			current_sec = 12 - seconds;
			current = 2;
		}else if(seconds>=13 && seconds<=18){
			current_sec = 18 - seconds;
			current = 3;
		}else if(seconds>=19 && seconds<=24){
			current_sec = 24 - seconds;
			current =4;
		}else if(seconds>=25 && seconds<=30){
			current_sec = 30 - seconds;
			current = 5;
		}else if(seconds>=31 && seconds<=36){
			current_sec = 36 - seconds;
			current = 6;
		}else if(seconds>=37 && seconds<=42){
			current_sec = 42 - seconds;
			current = 7;
		}else if(seconds>=43 && seconds<=48){
			current_sec = 48 - seconds;
			current =8;
		}else if(seconds>=49 && seconds<=54){
			current_sec = 54 - seconds;
			current = 9;
		}else if(seconds>=55 && seconds<=58){
			current_sec = 58 - seconds;
			current = 10;
		}
		if(current_sec==0){
			current = current + 1; 
			current_sec = 6;
		}
		 if(current > 10 ){
			 current = 1;
		 }
		// console.log("Current Second=="+seconds+" Remaining=="+current_sec+" Current Slide=="+current);
		 jQuery(".rotation_set").hide();
		 jQuery("#rotation_set_"+current).show();
		 //console.log(current_sec * 1000);
		 setTimeout("trackSlide("+current+");",current_sec * 1000);
	 	 setTimeout("startSlide("+current+");",current_sec * 1000);
	 }
 }
 function getRndInteger(min, max) {
  return Math.floor(Math.random() * (max - min + 1) ) + min;
}
 
 function trackSlide(current){
if(window.location.href.indexOf('localhost') == -1) {
     if(window.location.href.indexOf('staging') == -1) {
		var UA_ID = 'UA-166289038-1';
	 }else{
		var UA_ID = 'UA-166289038-2'; 
	 }
	 var set_ads = jQuery("#set_ads_"+current).val();
	 var set_ads_ga = jQuery("#set_ads_ga_"+current).val();
	 var tmp = set_ads_ga.split(",");
	 var HOST_URL = '';
	 var trackBaseData = {
				v: 1,
				tid: UA_ID,
				cid: '1379762423.1647958985',
				t: 'event',
				ni: 1,
				ec: 'Advanced Ads',
				ea: 'Impressions',
				dl: document.location.origin + document.location.pathname,
				dp: document.location.pathname,
			};
	 var payload = "";
	 jQuery.each(tmp, function( index, value ) {
		 			var val = value.replace("–","-");
					//var adInfo = {el: val,z:getRndInteger(1000,1000000)};
					var adInfo = {el: val};
					//console.log(adInfo);
					var adParam = jQuery.extend( {}, trackBaseData, adInfo );
					//payload += jQuery.param( adParam ) + "\n";
					payload = jQuery.param(adParam);
					payload += "&z="+getRndInteger(1000,1000000);
					jQuery.get('https://www.google-analytics.com/collect',payload);
	});
	 if ( payload.length) {
		 //console.log(payload);
		//jQuery.post('https://www.google-analytics.com/batch',payload);
	}
	 /*var HOST_URL = '';
	 var dl = document.location.origin + document.location.pathname;
	 var dp = document.location.pathname;
	 var ec = 'Advanced Ads';
	 var trackBaseData = 'v=1&tid=UA-166289038-1&cid=1379762423.1647958985&t=event&ni=1&ec='+ec+'&ea=Impressions&dl='+dl+'&dp='+dp;
	 var payload = "";*/
	 
	/* jQuery.post(
					"https://www.google-analytics.com/batch",
					'v=1&tid=UA-166289038-1&cid=1379762423.1647958985&t=event&ni=1&ec=Advanced%20Ads&ea=Impressions&dl=https%3A%2F%2Fshopadoc.com%2Fshopadoc-auction-activity%2F&dp=%2Fshopadoc-auction-activity%2F&el=%5B6047%5D%20D%20ClientMYTEST%20B1%26nbsp%3B03%2F01%2F22%20-%2012%2F31%2F22'
				);*/
	 jQuery.ajax({	
				url:'<?php echo get_site_url();?>/ajax.php',	
				type:'POST',
				data:{'mode':'track','set_ads':set_ads},
				beforeSend: function() {},
				complete: function() {
				},
				success:function (data){}
		
				});
 }
 }
		function removecode(){
			var code = jQuery("#wpforms-895-field_26").val();
		  	var price = parseFloat(jQuery(".wpforms-single-item-price .wpforms-price").html().replace("$ ",""));
		    jQuery(".wpforms-payment-total").html("$ "+price);
			jQuery("#wpforms-895-field_29").val(price);
			jQuery("#wpforms-895-field_26").val('');
			jQuery("#wpforms-895-field_34").val(price);
		}
		function apply_code(){
		  var code = jQuery("#wpforms-895-field_26").val();
		  var price = parseFloat(jQuery(".wpforms-single-item-price .wpforms-price").html().replace("$ ",""));
		  if(code ==''){
			  //Custom_popup("Error!","Please enter a Promo Code","col-md-6 col-md-offset-3");
			  jQuery("#promo_code_error").text('Please enter a Promo Code');
			  return false;
		  }
		  jQuery("#promo_code_error").text('');
		  jQuery.ajax({	
					url:'<?php echo get_site_url();?>/apply_coupon.php',	
					type:'POST',
					data:{'code':code,'price':price},
					beforeSend: function() {
						//jQuery('#'+gallery).prepend('<div class="loading" style="text-align:center;"><img src="<?php echo WP_SITEURL;?>/page-loader.gif" title="loading" alt="loading" /></div>');
						//jQuery(".wpforms-payment-total").html('<div class="loading" style="text-align:left;"><img src="<?php echo get_site_url();?>/wp-content/themes/dokan-child/woo_loading_trans.gif" title="loading" alt="loading" /></div>');
						jQuery("#wpforms-895-field_27").append('<img class="loading" style="margin-left:10px" src="<?php echo get_site_url();?>/wp-content/themes/dokan-child/woo_loading_trans.gif" title="loading" alt="loading" />');
						
					},
        			complete: function() {
						jQuery('#wpforms-895-field_27 .loading').remove();	
					},
					success:function (data){
							if(data=='error'){
								jQuery(".wpforms-payment-total").html("$ "+price);
								jQuery("#wpforms-895-field_29").val(price);
								jQuery("#wpforms-895-field_26").val('');
								//alert('Coupon "'+code+'" does not exist!');
								//Custom_popup("Error!","Invalid Promo Code","col-md-6 col-md-offset-3");
								jQuery("#promo_code_error").text('Invalid Promo Code');
							}else{
								var new_price = data;
								//wpforms-payment-total
								//Coupon: code10 (remove)
								jQuery("#promo_code_error").text('');
								jQuery(".wpforms-payment-total").html("$ "+new_price+" (<a href='javascript:removecode();' title='remove coupon code'>remove</a>)");
								jQuery("#wpforms-895-field_34").val(new_price);
								jQuery("#wpforms-895-field_29").val(new_price);
							}
					}
			
					});
		}
function chooseProfile(id){

	if(id==1){
		 jQuery(".childDiv1").show();
		 jQuery(".childDiv2").hide();
	 }if(id==2){
		 jQuery(".childDiv1").hide();
		 jQuery(".childDiv2").show();
	 }
	/* 
	 jQuery("#type-1").removeClass('bg_opacity');
	 jQuery("#type-2").removeClass('bg_opacity');
	 jQuery("#type-3").removeClass('bg_opacity');
	 jQuery("#type-4").removeClass('bg_opacity');
	 jQuery("#type-5").removeClass('bg_opacity');
	 jQuery("#type-6").removeClass('bg_opacity');
	 jQuery("#type-7").removeClass('bg_opacity');
	 jQuery("#type-8").removeClass('bg_opacity');
	 jQuery("#type-child").removeClass('bg_opacity');
	 jQuery("#type-"+id).addClass('bg_opacity');*/

	 /*jQuery('#btnSubmit').click();*/
 }
 function openLink(linkURL) {
        window.location.replace(linkURL);
    }
	function pageRedirect(page) {
	 	<?php
			global $post;
		?>
        window.location.replace("<?php echo get_site_url();?>/?action=add_to_cart&type="+page+"&auction_id=<?php echo $post->ID;?>");
    }
	 function pageRedirect_Relist() {
	 	<?php
			global $post;
		?>
        window.location.replace("<?php echo get_site_url();?>/?action=relist&mode=discount&product_id=<?php echo $post->ID;?>");
    }	
  jQuery(document).ready(function(){
	  	jQuery("#wpforms-895-field_31 li,#wpforms-895-field_45 li").addClass("container_my");
		jQuery("#wpforms-895-field_31_1,#wpforms-895-field_45_1").after('<span class="checkmark_my"></span>');
		jQuery("#wpforms-895-field_31 li .checkmark_my,#wpforms-895-field_45 li .checkmark_my").click(function() {
		  jQuery("#wpforms-895-field_31 label,#wpforms-895-field_45 label").click();
		});
		
	  	jQuery("#menu-item-browse").text("My Uploads");
	  	jQuery("#wpforms-185-field_4").after('<span toggle="#wpforms-185-field_4" class="fa fa-fw fa-eye field-icon toggle-password wpform-password"></span>');
		jQuery("#wpforms-895-field_4").after('<span toggle="#wpforms-895-field_4" class="fa fa-fw fa-eye field-icon toggle-password wpform-password"></span>');
		
	 	jQuery(".toggle-password").click(function() {
		  jQuery(this).toggleClass("fa-eye fa-eye-slash");
		  var input = jQuery(jQuery(this).attr("toggle"));
		  if (input.attr("type") == "password") {
			input.attr("type", "text");
		  } else {
			input.attr("type", "password");
		  }
		});
	  	jQuery(document).on('change', "input[name='plan']", function() {
			if(this.checked) {
				var val = jQuery(this).val(); 
				jQuery("#selected_plan").val(val);
				//setTimeout("pageRedirect('"+val+"')", 1000);
			}
		});
		jQuery( "#wpforms-895-field_44") .change(function () {    
			var val = jQuery(this).val(); 
			if(val=='Same as address listed above'){
				var street = jQuery("#wpforms-895-field_17").val();
				var street2 = jQuery("#wpforms-895-field_18").val();
				var city = jQuery("#wpforms-895-field_19").val();
				var state = jQuery("#wpforms-895-field_20").val();
				var zip = jQuery("#wpforms-895-field_22").val();
				jQuery("#wpforms-895-field_7-container").addClass('hide');
				jQuery("#wpforms-895-field_8-container").addClass('hide');
				jQuery("#wpforms-895-field_9-container").addClass('hide');
				jQuery("#wpforms-895-field_11-container").addClass('hide');
				jQuery("#wpforms-895-field_12-container").addClass('hide');
			}else{
				jQuery("#wpforms-895-field_7-container").removeClass('hide');
				jQuery("#wpforms-895-field_8-container").removeClass('hide');
				jQuery("#wpforms-895-field_9-container").removeClass('hide');
				jQuery("#wpforms-895-field_11-container").removeClass('hide');
				jQuery("#wpforms-895-field_12-container").removeClass('hide');
				var street = '';
				var street2 = '';
				var city = '';
				var state = '';
				var zip = '';
			}
			jQuery("#wpforms-895-field_7").val(street);
			jQuery("#wpforms-895-field_8").val(street2);
			jQuery("#wpforms-895-field_9").val(city);
			jQuery("#wpforms-895-field_11").val(state);
			jQuery("#wpforms-895-field_12").val(zip);
		});
		jQuery(document).on('change', "#wpforms-895-field_11", function() {
				var val = jQuery("#wpforms-895-field_11").val(); 
				var val_1 = jQuery("#wpforms-895-field_20").val();
				if(val === null || val == ""){
					jQuery("#wpforms-895-field_11").val(val_1);
					jQuery("#wpforms-submit-895").removeAttr('disabled');
					return true;
				}else{
					if(val=="" || val_1 == ""){
						jQuery("#wpforms-submit-895").removeAttr('disabled');
						return true;
					}
					if(val != val_1){
						jQuery("#wpforms-submit-895").attr('disabled','disabled');
						Custom_popup("Error!","Office where treatment is rendered must be same state as address on file with State Board of Dentistry.","col-md-6 col-md-offset-3 no-close-icon");
						jQuery("#wpforms-895-field_11").val(val_1);
						jQuery("#wpforms-submit-895").removeAttr('disabled');
					}else{
						jQuery("#wpforms-submit-895").removeAttr('disabled');
					}
				}
				
				
		});
		jQuery(document).on('click', "#bid_on,#pay_now_btn", function() {
				//var val = jQuery("input[name='plan']:checked").val(); // retrieve the value
				var val = jQuery("#selected_plan").val();
				if(val==''){
					//Custom_popup("Error!","Please select a payment option.","col-md-6 col-md-offset-3");
				}else{
					pageRedirect(val);
				}
				//setTimeout("pageRedirect('"+val+"')", 1000);
		});
		jQuery(document).on('click', "#pay_now_btn_relist", function() {
				var product_ids = jQuery.map(jQuery(':checkbox[name=relist_ids\\[\\]]:checked'), function(n, i){
									  return n.value;
								}).join(',');
				if(product_ids ==''){
					jQuery("#relist_error").text('Please select Relist Option.');
				}else{
					jQuery("#relist_error").text();					
					window.location.replace("<?php echo get_site_url();?>/?action=multi-relist&mode=discount&product_id="+product_ids);	
				}
			
				//var val = jQuery("input[name='plan']:checked").val(); // retrieve the value
				/*
				var val = jQuery("#selected_plan").val();
				if(val==''){
					//Custom_popup("Error!","Please select Re-list Option.","col-md-6 col-md-offset-3");
					jQuery("#relist_error").text('Please select Relist Option.');
				}else{
					jQuery("#relist_error").text();
					pageRedirect_Relist();
				}
				*/
				//setTimeout("pageRedirect('"+val+"')", 1000);
		});
		<?php if(is_front_page()) {?>
			//responsive-menu-label responsive-menu-label-left
			jQuery("#responsive-menu-button").append('<span class="responsive-menu-label responsive-menu-label-home responsive-menu-label-right"> <span class="responsive-menu-button-text">Menu</span> </span>');
		<?php }?>
		//jQuery(document).on('click', ".bid_on", function() {
						<?php 
							$subscriptions = YWSBS_Subscription_Helper()->get_subscriptions_by_user( get_current_user_id() );
							$status = ywsbs_get_status();
							$flag = false;
							foreach ( $subscriptions as $subscription_post ) :
								$subscription = ywsbs_get_subscription( $subscription_post->ID );
								//if(($status[$subscription->status]=='active' || $status[$subscription->status] == 'expired') && $subscription->product_id != 1141):
								if(($status[$subscription->status]=='active') && $subscription->product_id != 1141){
									$flag = true;
									$active_plan = $subscription->product_id;
								}
							endforeach;
						if($flag):
							if($active_plan == 942){
								$change_plan = 'monthly';?>
								jQuery("input[name=plan][value='single']").prop("checked", true);
								jQuery("input[name=plan][value='single']").hide();
								//jQuery("#plan0_span").prepend('<span class="tooltips popup_tooltip" title="This is your current subscription."><img src="<?php echo home_url('/wp-content/themes/dokan-child/checkbok.png');?>" alt="checkbox" /></span>');
								jQuery("#plan0_span").prepend('<span class="checkMark_span" ><img src="<?php echo home_url('/wp-content/themes/dokan-child/checkbok.png');?>" alt="checkbox" /></span>');
								jQuery("#pay_now_btn").hide();
								jQuery("#plan1_span").hide();
								jQuery(document).on('click', "input[name=plan]", function() {
									if(jQuery(this).val()=='single'){
										jQuery("#pay_now_btn").hide();
									}else{
										jQuery("#pay_now_btn").show();
									}
								});
							<?php }
							if($active_plan == 948){
								
								$plan_orderid =get_plan_orderid();
								$plan_status = get_post_meta($plan_orderid,'_plan_status',true);
								$status = get_post_meta($plan_orderid,'status',true);
								$change_plan = 'single';?>
								jQuery("input[name=plan][value='monthly']").prop("checked", true);
								jQuery("input[name=plan][value='monthly'],#plan1_span").hide();
								jQuery(".tooltips.popup_tooltip").html('<img src="<?php echo home_url('/wp-content/themes/dokan-child/checkbok.png');?>" alt="checkbox" />');
								jQuery("#pay_now_btn").hide();
								jQuery("#plan0_span").hide();
								jQuery("#deactive_span,#deactive_span2").show();
								<?php if($plan_status =="active_cancelled"){?>
									jQuery("#deactive_span").hide();
									jQuery("#deactive_span2").html('Cancellation is effective 4 weeks from your request per the <a href="/user-agreement/" title="Terms of Use.">Terms of Use</a>.<div class="woocommerce-error green" role="alert">Your request is in process and will not auto renew.</div>');
								<?php }?>
								jQuery(document).on('click', "input[name=plan]", function() {
									if(jQuery(this).val()=='single'){
										jQuery("#pay_now_btn").show();
										jQuery("#deactive_span,#deactive_span2").hide();
									}else{
										jQuery("#pay_now_btn").hide();
										jQuery("#deactive_span,#deactive_span2").show();
									}
								});
								jQuery('input[name=plan_deactive]').on('click', function(){
									jQuery(".sgpb-popup-dialog-main-div-wrapper .sgpb-popup-close-button-1,.sgpb-popup-overlay-955").click();
                                    jQuery.confirm({
                                        title: 'Please Confirm',
										columnClass: 'col-md-10 col-md-offset-1',
										closeIcon: true, // hides the close icon.
                                        content: '<br />',
                                        buttons: {
											Yes: {
                                                text: 'YES, CANCEL MY SUBSCRIPTION',
                                               // btnClass: 'btn-blue',
                                                /*keys: [
                                                    'enter',
                                                    'shift'
                                                ],*/
                                                action: function(){
                                                   // this.jQuerycontent // reference to the content
                                                    //jQuery.alert('Yes');
													window.location.replace("<?php echo get_site_url().'/?mode=de-active-sub';?>");
                                                }
                                            },
											No: {
                                                text: 'No, keep my current payment plan',
                                                btnClass: 'btn-blue',
                                                /*keys: [
                                                    'enter',
                                                    'shift'
                                                ],*/
                                                action: function(){
                                                    //this.jQuerycontent // reference to the content
                                                    //jQuery.alert('No');
													jQuery("#plan_deactive").removeAttr('checked');
                                                }
                                            }
                                        }
                                    });
                                });
							<?php }
						endif;
						?>
				//});
				
	});
	//Custom_popup("Error!","","col-md-6 col-md-offset-3");
	function Custom_popup_iframe(title,message,class_name,closeIconflag){
				jQuery.confirm({
					title: '',
					columnClass: class_name,
					closeIcon: closeIconflag, // hides the close icon.
					onContentReady: function () {
							
							//jQuery(".jconfirm-content").html("text");	
						
					},
					content: message,
					buttons: {
						Yes: {
							text: "OK",
						   	//btnClass: 'btn-blue',
							keys: ['enter'],
							action: function(){
							}
						}
					}
				});
				
                                
	}
	function Custom_popup(title,message,class_name){
				jQuery.confirm({
					title: '',
					columnClass: class_name,
					closeIcon: true, // hides the close icon.
					content: message,
					buttons: {
						Yes: {
							text: "OK",
						   	//btnClass: 'btn-blue',
							keys: ['enter'],
							action: function(){
							}
						}
					}
				});
                                
	}
	function Custom_popup_new(title,message,class_name){
				jQuery.confirm({
					title: title,
					columnClass: class_name,
					closeIcon: true, // hides the close icon.
					content: message,
					buttons: {
						Yes: {
							text: "OK",
						   	//btnClass: 'btn-blue',
							keys: ['enter'],
							action: function(){
							}
						}
					}
				});
                                
	}
</script>
<?php if(is_front_page()) {?>
<style type="text/css">
.responsive-menu-label.responsive-menu-label-home {
    display: block !important;
    right: -37px;
    top: 22px;
}
#responsive-menu-button.is-active .responsive-menu-label.responsive-menu-label-home {
    display: none !important;
}
.responsive-menu-label.responsive-menu-label-home .responsive-menu-button-text {
    color: #fff;
}
</style>
<?php }?>
<script>
function myFunction() {
  var x = document.getElementById("sub-active");
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}
</script> 
<script type="text/javascript">
 jQuery(document).ready(function(){
	 var post_id = jQuery(".variation dd.variation-Auctionid").text();
	 	if(post_id=='46'){
				jQuery("#coupon_code").attr('placeholder','Promo code');
				jQuery(".woocommerce-terms-and-conditions-checkbox-text").html('I authorize ShopADoc The Dentist Marketplace charge to my credit/debit card, listed below, an annual registration fee in the amount of $29.99.<br />The annual registration fee will be a recurring charge to this credit/debit card on your anniversary date of registration.<br /><br /> Under penalty of law, I certify I hold an active unrestricted license to practice dentistry and I am without pending investigation for disciplinary/ administrative action(s) against me. Should my status change, I agree to notify ShopADoc The Dentist Marketplace immediately by email to <a href="<?php echo home_url('/contact/');?>" title="Contact" >ShopADoc1@gmail.com</a> and refrain from further participation on this site until reinstatement by the State Board of Dentistry. I accept the <a href="<?php echo home_url('/user-agreement/');?>" title="User Agreement" >User Agreement</a>, <a href="<?php echo home_url('/privacy-policy/');?>" title="Privacy Policy" >Privacy Policy</a>, and <a href="<?php echo home_url('/house-rules/');?>" title="House Rules" >House Rules</a>.');
		}
		
	 });
	 jQuery('.media-frame-menu').attr('style','display:none !important;');
</script>
<link href="https://fonts.googleapis.com/css?family=Cinzel" rel="stylesheet">
<?php
global $class_lang;
if ( isset( $_GET[ 'lang' ] ) && $_GET[ 'lang' ] == 'es' ) {
  $class_lang = "lang_es";
} else {
  $class_lang = "lang_en";
}
?>
<style type="text/css">
.media-frame-menu, .media-frame-menu-heading {
    display: none !important;
}
</style>
<?php
global $post;
if ( $post->ID == 6797 ) {
  ?>
<style type="text/css">
.page-id-6797 .navbar-top-area.navbar {
    z-index: 100000000;
}
#post-6797 .entry-header {
    display: none;
}
</style>
<script type="application/javascript">
			var auction_no = '<iframe width="100%" height="515" src="<?php echo home_url('ad-analytics/?period=last7days&mode=popup'); ?>" frameborder="0" allowfullscreen></iframe>';
           	Custom_popup_iframe("",auction_no,"col-md-8 col-md-offset-2 no-button",false);
    </script>
<?php }?>
<script type="text/javascript">
	function redirectTolist(){
		jQuery('.container_loader').show();
		//alert(jQuery('a#exitBtn').attr('data-href'));
		//jQuery('a#exitBtn').attr('data-href');
		window.setTimeout(function(){
			// Move to a new location or you can do something else
			window.location.href = jQuery('a#exitBtn').attr('data-href');
		}, 3000);
	}
</script>
</head>

<body id="element" <?php body_class( 'woocommerce' ); ?>>
<style type="text/css">
		 /*body.fullscreen .topExit{
			 display: block !important;
			 float:left;width:100%;height:60px;
			 position: absolute;top: 0px;
			 color: #fff;
			text-align: center;
			 z-index: 10001;
		 }*/
		 body.fullscreen .topExitInner{
			width: 28px;
			text-align: center;
			 position: absolute;
			 top: 20px;
			 z-index: 101;
			left: 50%;
			transform: translate(-50%, -50%);
		 }
	 </style>
<div class="topExitInner" style="display: none;"><a href="javascript:redirectTolist()" id="exitBtn" title="click Here to exit full screen" ><img src="<?php echo home_url('wp-content/themes/dokan-child/exit.png'); ?>" alt="exit" title="exit" width="28px"/></a></div>
<div id="element-inner">
<!-- Google Tag Manager (noscript) -->
<noscript>
<iframe src="https://www.googletagmanager.com/ns.html?id=GTM-58LBM3N"
height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->
<?php if((isset($_GET['mode'])&& $_GET['mode']=='popup')){?>
<div class="se-pre-con"></div>
<style>
.navbar{ display: none !important;  }
.no-js #loader { display: none;  }
.js #loader { display: block; position: absolute; left: 100px; top: 0; }
.se-pre-con {
	position: fixed;
	left: 0px;
	top: 0px;
	width: 100%;
	height: 100%;
	z-index: 9999;
	background: url(/wp-content/themes/dokan-child/ajax_loader.gif) center no-repeat #fff;
}
</style>
<script src="http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.2/modernizr.js"></script> 
<script>
jQuery(window).load(function() {
	jQuery(".se-pre-con").fadeOut("slow");;
});
</script>
<?php }?>
<div id="page" class="hfeed site module <?php echo $class_lang;?>">
<?php do_action( 'before' ); ?>
<nav class="navbar navbar-inverse navbar-top-area">
  <div class="container">
    <div class="row">
      <div class="col-md-6 col-sm-5">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-top-collapse"> <span class="sr-only">
          <?php _e( 'Toggle navigation', 'dokan-theme' ); ?>
          </span> <i class="fa fa-bars"></i> </button>
          <div class="translation_div">
            <?php dynamic_sidebar('smartslider_area_1'); ?>
          </div>
          <?php
          if ( is_front_page() ) {
            //echo do_shortcode('[aps-social id="1"]');
            ?>
          <div class="aps-social-icon-wrapper">
            <div class="aps-group-horizontal">
              <div class="aps-each-icon icon-1-1" style="margin:px;" data-aps-tooltip="Facebook" data-aps-tooltip-enabled="0" data-aps-tooltip-bg="#000" data-aps-tooltip-color="#fff"> <a href="https://www.facebook.com/2073258272773518/photos/2073258336106845/" target="&quot;_blank&quot;" class="aps-icon-link animated " data-animation-class=""> <img src="https://staging.shopadoc.com/wp-content/uploads/2019/06/facebook.png" alt="Facebook"> </a> <span class="aps-icon-tooltip aps-icon-tooltip-bottom" style="display: none;"></span>
                <style class="aps-icon-front-style">
.icon-1-1 img{height:20px;width:20px;opacity:1;-moz-box-shadow:0px 0px 0px 0 ;-webkit-box-shadow:0px 0px 0px 0 ;box-shadow:0px 0px 0px 0 ;padding:px;}.icon-1-1 .aps-icon-tooltip:before{border-color:#000}
</style>
              </div>
              <div class="aps-each-icon icon-1-2" style="margin:px;" data-aps-tooltip="instagram" data-aps-tooltip-enabled="0" data-aps-tooltip-bg="#000" data-aps-tooltip-color="#fff"> <a href="http://instagram.com/ShopADoc" target="&quot;_blank&quot;" class="aps-icon-link animated " data-animation-class=""> <img src="https://staging.shopadoc.com/wp-content/uploads/2019/05/serveimage-1.png" alt="instagram"> </a> <span class="aps-icon-tooltip aps-icon-tooltip-bottom" style="display: none;"></span>
                <style class="aps-icon-front-style">
.icon-1-2 img{height:20px;width:20px;opacity:1;-moz-box-shadow:0px 0px 0px 0 ;-webkit-box-shadow:0px 0px 0px 0 ;box-shadow:0px 0px 0px 0 ;padding:px;}.icon-1-2 .aps-icon-tooltip:before{border-color:#000}
</style>
              </div>
              <div class="aps-each-icon icon-1-3" style="margin:px;" data-aps-tooltip="Twitter" data-aps-tooltip-enabled="0" data-aps-tooltip-bg="#000" data-aps-tooltip-color="#fff"> <a href="https://twitter.com/realShopADoc" target="&quot;_blank&quot;" class="aps-icon-link animated " data-animation-class=""> <img src="https://staging.shopadoc.com/wp-content/uploads/2019/06/twitter.png" alt="Twitter"> </a> <span class="aps-icon-tooltip aps-icon-tooltip-bottom" style="display: none;"></span>
                <style class="aps-icon-front-style">
.icon-1-3 img{height:20px;width:20px;opacity:1;-moz-box-shadow:0px 0px 0px 0 ;-webkit-box-shadow:0px 0px 0px 0 ;box-shadow:0px 0px 0px 0 ;padding:px;}.icon-1-3 .aps-icon-tooltip:before{border-color:#000}
</style>
              </div>
              <div class="aps-each-icon icon-1-4" style="margin:px;" data-aps-tooltip="Youtube" data-aps-tooltip-enabled="0" data-aps-tooltip-bg="#000" data-aps-tooltip-color="#fff"> <a href="https://youtube.com" target="&quot;_blank&quot;" class="aps-icon-link animated " data-animation-class=""> <img src="https://staging.shopadoc.com/wp-content/uploads/2022/11/youtube.png" alt="Youtube"> </a> <span class="aps-icon-tooltip aps-icon-tooltip-bottom" style="display: none;"></span>
                <style class="aps-icon-front-style">
.icon-1-4 img{height:20px;width:px;opacity:1;-moz-box-shadow:0px 0px 0px 0 ;-webkit-box-shadow:0px 0px 0px 0 ;box-shadow:0px 0px 0px 0 ;padding:px;}.icon-1-4 .aps-icon-tooltip:before{border-color:#000}
</style>
              </div>
            </div>
          </div>
          <div class="app_div pull-left only_shown_desktop" style="margin-top:0;">
            <div class="andriod_app pull-left" style="margin:0 5px;"> <a href="#" title="Google-play"><img src="/wp-content/themes/dokan-child/Google-play.png" alt="Google-play" border="0" width="100px"/></a> </div>
            <div class="ios_app pull-left"> <a href="" title="IOS"><img src="/wp-content/themes/dokan-child/App-Store.png" alt="IOS" border="0" width="100px"/></a> </div>
          </div>
          <?php
          }
          ?>
          <?php
          $user = wp_get_current_user();
          if ( $user->roles[ 0 ] == 'shopadoc_admin' && is_product() ) {
            echo '<a href="' . home_url( '/wp-admin/admin.php?page=home_performance' ) . '" style="position:relative;z-index:555;font-size:13px !important;margin-left:5px;" class="dokan-btn dokan-btn-theme btn-primary" title="Back">Back</a>';
          }
          ?>
        </div>
        <?php
        wp_nav_menu( array(
          'theme_location' => 'top-left',
          'depth' => 0,
          'container' => 'div',
          'container_class' => 'collapse navbar-collapse navbar-top-collapse',
          'menu_class' => 'nav navbar-nav',
          'fallback_cb' => 'wp_bootstrap_navwalker::fallback',
          'walker' => new wp_bootstrap_navwalker() ) );
        ?>
      </div>
      <div class="col-md-6 col-sm-7 menu-div">
        <div class="collapse navbar-collapse navbar-top-collapse"> <?php echo do_shortcode('[responsive_menu]');?>
          <?php dokan_header_user_menu_custom(); ?>
        </div>
      </div>
    </div>
    <!-- .row --> 
  </div>
  <!-- .container --> 
</nav>
<header id="masthead" class="site-header" role="banner">
  <div class="container">
    <div class="row">
      <div class="col-md-4 col-sm-5">
        <hgroup>
          <h1 class="site-title"><a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
            <?php bloginfo( 'name' ); ?>
            <small> -
            <?php bloginfo( 'description' ); ?>
            </small></a></h1>
        </hgroup>
      </div>
      <!-- .col-md-6 -->
      
      <div class="col-md-8 col-sm-7 clearfix">
        <?php dynamic_sidebar( 'sidebar-header' ) ?>
      </div>
    </div>
    <!-- .row --> 
  </div>
  <!-- .container -->
  
  <div class="menu-container">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <nav role="navigation" class="site-navigation main-navigation clearfix">
            <h1 class="assistive-text"><i class="icon-reorder"></i>
              <?php _e( 'Menu', 'dokan-theme' ); ?>
            </h1>
            <div class="assistive-text skip-link"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'dokan-theme' ); ?>">
              <?php _e( 'Skip to content', 'dokan-theme' ); ?>
              </a></div>
            <nav class="navbar navbar-default" role="navigation">
              <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-main-collapse"> <span class="sr-only">
                <?php _e( 'Toggle navigation', 'dokan-theme' ); ?>
                </span> <i class="fa fa-bars"></i> </button>
                <a class="navbar-brand" href="<?php echo home_url(); ?>"><i class="fa fa-home"></i>
                <?php _e( 'Home', 'dokan-theme' ); ?>
                </a> </div>
              <div class="collapse navbar-collapse navbar-main-collapse">
                <?php
                wp_nav_menu( array(
                  'theme_location' => 'primary',
                  'container' => 'div',
                  'container_class' => 'collapse navbar-collapse navbar-main-collapse',
                  'menu_class' => 'nav navbar-nav',
                  'fallback_cb' => 'wp_bootstrap_navwalker::fallback',
                  'walker' => new wp_bootstrap_navwalker() ) );
                ?>
              </div>
            </nav>
          </nav>
          <!-- .site-navigation .main-navigation --> 
        </div>
        <!-- .span12 --> 
      </div>
      <!-- .row --> 
    </div>
    <!-- .container --> 
  </div>
  <!-- .menu-container --> 
</header>
<!-- #masthead .site-header -->
<style type="text/css">
.container_loader {
    position: absolute;
    height: 300px;
    width: 300px;
    z-index: 1;
    margin-left: auto;
    margin-right: auto;
    left: 0;
    right: 0;
    text-align: center;
	display:none;
}
 .loader{position:absolute;left:0;right:0;top:0;bottom:0;margin:auto}
 #let_go_link .elementor-widget-html{float:left;height:100%;}
 #let_go_link .elementor-widget-container{float:left;height:100%;width:100%;}
 #let_go_link a{    width: 100%;
    float: left;
    height: 105%;
    display: flex;
    justify-content: center;
    align-items: center;
}
.aps-each-icon {
    display: inline-block;
    position: relative;
}
</style>
<div class="container_loader"> <img src="<?php echo home_url('/wp-content/themes/dokan-child/ajax_loader.gif');?>" class="loader"> </div>
<div id="main" class="site-main">
<div class="container content-wrap">
<div class="row">
