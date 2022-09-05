<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package dokan
 * @package dokan - 2014 1.0
 */
 global $woocommerce
 //var_dump($woocommerce->session->get_session_cookie());

?>
</div>
<!-- .row -->
</div>
<!-- .container -->
<?php 
global $wp;
$current_url =  home_url($wp->request);
$dentist_account_status = get_user_meta(get_current_user_id(), 'dentist_account_status', true );
$plan_orderid = get_plan_orderid();
$plan_status = get_post_meta($plan_orderid,'_plan_status', true );
if(($dentist_account_status !='unsubscribe' && $dentist_account_status !='de-active') || strpos($current_url,"/checkout") > 0){?>
        <style type="text/css">
.sgpb-content-2721, .sgpb-popup-overlay-2721 {
	display:none;
}
.sgpb-overflow-hidden {
	width: 100%;
	height: 100%;
	overflow: auto !important;
}
</style>
        <?php }?>
        <?php /*if(strpos($current_url,"auction-activity/auction") > 0 || strpos($current_url,"shopadoc-auction-activity") > 0){?>
    <style type="text/css">
		@media only screen and (min-width: 801px) {
			#main.site-main {
				min-height: calc(100vh - 31.7333px - 45px - 242px);
			}
		}
	</style>
    <?php }*/?>
        <?php if(is_product() || strpos($current_url,"/auction-") > 0 || strpos($current_url,"shopadoc-auction-activity") > 0){?>
        <style type="text/css">
@media only screen and (min-width: 801px) {
#main.site-main {
	min-height: calc(100vh - 31.7333px - 45px - 242px);
}
}
</style>
        <?php }else{?>
        <style type="text/css">
#main.site-main {
	min-height: calc(100vh - 31.7333px - 45px);
}
</style>
        <?php }?>
        <?php if(is_product() || (strpos($current_url,"/auction-") > 0 && strpos($current_url,"/auction-activity") === false ) || strpos($current_url,"auction-activity/auction") > 0 || strpos($current_url,"shopadoc-auction-activity") > 0){?>
        <?php if($_GET['action'] != 'edit'){?>
<div class="container content-wrap ad_section_main">
  <?php do_shortcode("[ad_section]");?>
</div>
<?php }?>
<?php }?>
</div>
<!-- #main .site-main -->
<?php add_thickbox();?>
<footer id="colophon" class="site-footer" role="contentinfo">
  <div class="footer-widget-area">
    <div class="container">
      <div class="row">
        <div class="col-md-3">
          <?php dynamic_sidebar( 'footer-1' ); ?>
        </div>
        <div class="col-md-3">
          <?php dynamic_sidebar( 'footer-2' ); ?>
        </div>
        <div class="col-md-3">
          <?php dynamic_sidebar( 'footer-3' ); ?>
        </div>
        <div class="col-md-3">
          <?php dynamic_sidebar( 'footer-4' ); ?>
        </div>
      </div>
      <!-- .footer-widget-area --> 
    </div>
  </div>
  <div class="copy-container">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="footer-copy">
            <div class="col-md-8 site-info">
              <?php
                            $footer_text = get_theme_mod( 'footer_text' );

                            if ( empty( $footer_text ) ) {
                                printf( __( '&copy; 2018-%d, %s. All rights are reserved.', 'dokan-theme' ), date( 'Y' ), 'ShopADoc® The Dentist Marketplace, Inc');
                                printf( __( 'Powered by <a href="%s" target="_blank">GrossiWeb</a>', 'dokan-theme' ), esc_url( 'http://grossiweb.com' ));
                            } else {
                                echo str_replace("[year]",date('Y'),$footer_text);
                            }
                            ?>
            </div>
            <!-- .site-info -->
            
            <div class="col-md-4 footer-gateway">
              <?php
                                wp_nav_menu( array(
                                    'theme_location'  => 'footer',
                                    'depth'           => 1,
                                    'container_class' => 'footer-menu-container clearfix',
                                    'menu_class'      => 'menu list-inline pull-right',
                                ) );
                            ?>
            </div>
          </div>
        </div>
      </div>
      <!-- .row --> 
    </div>
    <!-- .container --> 
  </div>
  <!-- .copy-container --> 
</footer>
<!-- #colophon .site-footer -->
</div>
<!-- #page .hfeed .site -->

<style type="text/css">
.fa {
	font-size:2em;
}
ul.panel-icon li {
	padding:15px 5px;
}
.jsProfileType h3 {
	font-weight:normal !important;
}
.bg {
	background-image: linear-gradient(to bottom, #fefefe, #f6f6f6) !important;
	background-repeat: repeat-x !important;
	color:#818588;
	padding:1%;
	cursor:pointer;
	font-weight:bold;
	border:solid 1px #e9e9e9;
}
.bg h3 {
	color:#818588 !important;
	font-weight:bold !important;
	font-size:23px !important;
}
.bg_opacity {
	border: 6px solid #e9e9e9;
	border-radius: 10px;
	opacity: 0.4;
	padding:6px;
	cursor:default;
}
.gantry-width-20 {
	width:20%;
}
.gantry-width-3 {
	float: left;
	width: 4%;
	margin-top: -41px;
}
.blue {
	color:#e67902;
}
.gantry-width-30 {
	width:28.5%;
}
.gantry-width-49 {
	width:48%;
	padding:2%;
	float:left;
	margin-top:10px;
}
.hide {
	display:none;
}
.mainDiv {
	float:left;
	margin:0 auto;
	padding-bottom:10px;
	width:100%;
}
.childDiv {
	float:left;
	margin:0 auto;
	clear:both;
	padding-bottom:10px;
	width:100%;
}
.div100 {
	float:left;
	width:100%;
}
.rt-center {
	text-align:center;
}
/*unhide copuon code checkout*/
.checkout_coupon {
	display: block !important;
}
/*hide message have a coupon?*/
.woocommerce-info {
	display:none;
}
/*coupon code checkout style*/
.checkout_coupon button.button {
	background-color: #insert button color here;
}
#billing_full_name_field_2 {
	/*margin-top: -80px;*/
	float: left;
	width:100%;
	/*margin-left: 15px;*/
}
.woocommerce-checkout-payment#payment {
	padding-bottom:20px;
}
</style>
<?php 
//New Layout
$role = 'dentist';
if (is_user_logged_in()){
	$user = wp_get_current_user();
	if($user->roles[0]=='seller'){
		$role = 'seller';
	}else{
		$role = 'dentist';
	}
}
?>
<?php if(is_product()){?>
<?php if($role=='seller'){?>
<style type="text/css">
				@media only screen and (max-width: 800px) {
					.current_bid .bid_amount_txt,.auction-price.starting-bid,.ended_section1 {
						width: 100%;
						text-align: right !important;
					}
					.biding_form{
						padding:8% 0;
					}
					.priceBox{
						padding:1% 0 !important;
						margin:0 !important;
                    }
					.priceBoxPad{
						padding: 7px 0 !important;
						margin:0 auto !important;
					}
				}
            </style>
<?php }?>
<?php if($role=='dentist'){?>
<style type="text/css">
				.price.priceBox table tr td table tr{float:left;width:50%;}
				.price.priceBox{padding:0px 10px !important;}
				.priceBox{font-size:18px;}
				.cart-coupon{display:none !important;}	
				@media only screen and (max-width: 800px) {
					.desktop_miles_label{
						display:none;
					}
					.mobile_miles_label{
						display:block;
					}
					.mobile_price{
						display:block;
					}
					.desktop_price{
						display:none;
					}
					.biding_form{
						padding:0 0;
					}
					.priceBox{
						padding:0 !important;
						margin:0 !important;
                    }
					.woocommerce div.product .product_title{
						padding-bottom:10px;
					}
					.woocommerce div.product{
						padding-bottom:10%;
					}
					.buttons_added{
						padding-top:0 !important;
					}
					/*.end_img{
						width:auto !important;
						height:auto !important;
						margin-top:-25px !important;
					}*/
				}
            </style>
<?php }?>
<?php }?>
<script type="text/javascript">
<?php if(is_product()){?>
jQuery(document).ready(function() {

var windowsize = jQuery(window).width();
if(windowsize <= 800){
	jQuery(".site-info").text('<?php echo "© 2018-".date("Y")." ShopADoc Inc. All Rights Reserved"?>');
	 var gallery = jQuery(".woocommerce-product-gallery");
	 var abuse = jQuery(".abuse");
	 <?php if($role=='seller'){?>
		//Place the section on right position for mobile layout
		var priceBox = jQuery(".price.priceBox");
		priceBox.insertAfter('.product');
		jQuery(".price.priceBox").css('float','left').css('clear','both').css('width','100%');
		var biding_form = jQuery(".biding_form");
		biding_form.insertBefore('.price.priceBox');
		//jQuery(".accordion_div").show();
		//jQuery('.close_link').show();
		//var bidding_section_live = jQuery(".bidding_section_live");
		//bidding_section_live.insertAfter('.entry-title');
	  
		//Allocate height to black price box
		var main_height = jQuery(".content-area #main").height();
		var product_div = jQuery(".content-area  #main .product").height();
		var biding_form_height = jQuery(".biding_form").height();
		//alert(main_height+"=="+product_div+"=="+biding_form_height);
		var black_box_height = main_height - (product_div + biding_form_height) + 34;
		//jQuery(".price.priceBox,table.main_table").height(black_box_height+"px");
		
		//Allocate height to black price box for tablets
		if(windowsize <= 800 && (windowsize > 448)){
			var black_box_height = main_height - (product_div + biding_form_height) - 40;
			//jQuery(".price.priceBox,table.main_table").height(black_box_height+"px");
		}
		
		
			jQuery("#update_distance").css('width','115px');
			jQuery(".rotation_main").css('margin-top','5px');
			jQuery(".rotation_main").css('margin-bottom','5px');
			
			if((navigator.userAgent.match(/iPhone/i)) && windowsize <= 448) {
				//alert(main_height+"-"+product_div+"+"+biding_form_height);
				var full_window_height = jQuery(window).height();
				//jQuery(".biding_form").css('padding','4% 0');
				//New Changes
				jQuery(".price.priceBox table tr td.up_down").css('width','40%');
				jQuery(".biding_form").css('padding','12px 0 11px 0');
				jQuery(".auction-user,h1.product_title,p.auction-start").css('margin-bottom','0px');
				jQuery(".auction_detail").css('padding-bottom','0px');
				jQuery(".woocommerce div.product").css('margin-top','-12px').css('height','207px');
				jQuery(".flex-active-slide, .woocommerce-product-gallery__image, .woocommerce-product-gallery__wrapper div").css('height','195px');
				//var main_site_height = product_div + black_box_height + biding_form_height;
				//jQuery('#main.site-main').css('height',main_site_height+"px");
				jQuery('.single-product #content').css('margin-top','15px');
				jQuery(".upcomming_flash .swf_container").hide();
				//jQuery(".price.priceBox table tr td.up_down").css('width','43%');
				jQuery('.price.priceBox table tr td table tr').css('line-height','16px');
				
			}
			if(windowsize >= 448){
				var full_window_height = jQuery(window).height();
				//New Changes
				jQuery(".rotation_main").css('width','85%').css('float','none').css('margin','0 auto');
				jQuery(".rotation_ad").css('width','49.5%');
				jQuery(".ad_section_main").css('margin-bottom','5px');
				jQuery(".ad_section_main").css('margin-top','15px');
				
				jQuery(".biding_form").css('padding','1% 0');
				jQuery(".priceBox").addClass('priceBoxPad');
				jQuery(".biding_form,.priceBoxPad").css('width','85%').css('margin','0 auto').css('float','none');
				jQuery(".priceBoxPad").css('position','relative').css('top','10px');
				jQuery('.up_down img').css('width','47px');
				
				jQuery(".price.priceBox table tr td").css('font-size','20px').css('height','35px');
				jQuery(".bid_amount_txt").css('font-size','27px')
				jQuery(".price.priceBox table tr td table tr").css('float','none');
				jQuery('table tr td.up_down').css('text-align','center');
				jQuery('#update_distance').css('width','145px');
				jQuery(".priceBoxPad table tr td").css('width','30%');
				jQuery(".up_down").css('width','40%');
				jQuery(".woocommerce div.product").css('height','221px');
				//jQuery(".auction-price").css('float','right');
			}
		<?php }?>
	  <?php if($role=='dentist'){?>
		var biding_form = jQuery(".biding_form");
	  	biding_form.insertAfter('.product');
		//jQuery(".accordion_div").show();
		//jQuery('.close_link').show();
		
		jQuery(".biding_form .quantity").css('float','left');
		jQuery(".biding_form .quantity .bid_amount_txt").css('margin-top','20px');
		
		jQuery(".rotation_main").css('margin-top','5px');
		jQuery(".rotation_main").css('margin-bottom','5px');
		if((navigator.userAgent.match(/iPhone/i)) && windowsize <= 448) {
			jQuery(".rotation_main").css('margin-top','-15px');
			//New Changes
			jQuery(".biding_form").css('padding','0 0');
			jQuery(".biding_form").css('margin-top','-17px');
			jQuery(".ended_section").css('padding-top','4px');
			jQuery(".auction-user,h1.product_title,p.auction-start").css('margin-bottom','0px');
			jQuery(".auction_detail").css('padding-bottom','0px');
			jQuery(".woocommerce div.product .product_title").css('padding-bottom','0px').css('height','auto');
			jQuery(".woocommerce div.product").css('margin-top','-12px').css('height','270px');
			jQuery(".flex-active-slide, .woocommerce-product-gallery__image, .woocommerce-product-gallery__wrapper div").css('height','205px');
			jQuery(".biding_form .ended_section2").css('width','30%').css('margin-top','-22px');
			jQuery(".biding_form .ended_section2 img").css('width','100%');
			jQuery(".upcomming_flash .swf_container").hide();
		}
		if(windowsize >= 448){
			jQuery(".rotation_main").css('width','85%').css('float','none').css('margin','0 auto');
			jQuery(".rotation_ad").css('width','49.5%');
			jQuery(".ad_section_main").css('margin-top','5px').css('margin-bottom','5px');
			//New Changes
			jQuery(".biding_form").css('margin-top','0');
			jQuery(".biding_form").css('width','85%').css('margin','0 auto').css('float','none');
			jQuery(".ended_section2").css('float','left');
			jQuery(".ended_section2 img").css('float','right');
			jQuery(".flex-active-slide, .woocommerce-product-gallery__image, .woocommerce-product-gallery__wrapper div").css('height','208px');
			jQuery(".biding_form .ended_section2").css('width','39%');
			jQuery(".bid_now_img, .end_img").css('margin-top','-68px').css('position','relative').css('z-index','1');
			jQuery("#clear_space").css('height','1px');
			jQuery(".woocommerce div.product").css('height','258px');
			jQuery(".ended_section1").css('margin-top','-15px').css('width','55%');
			jQuery(".woocommerce div.product .product_title").css('height','30px');
			jQuery(".ended_section").css('padding','6px 0 0 0px');
			jQuery(".bid_amount_txt").css('font-size','34px').css('line-height','37px');
			
		};
		jQuery('.single-product #content').css('margin-top','15px');
	  <?php }?>
	 //jQuery(".auction_detail").prepend(abuse);
	 
	 
	 jQuery(".detail_link").unbind("mouseover");
}else{
	  var gallery = jQuery(".woocommerce-product-gallery");
      gallery.insertBefore('.summary');
	  var abuse = jQuery(".abuse");
	  jQuery(".woocommerce-product-gallery").append(abuse);
	 jQuery(".detail_link").unbind("click");
	 jQuery(".rotation_main").css('margin-top','20px');
}
jQuery(window).resize(function() {});
});
<?php }else{ ?>
jQuery(document).ready(function() {
	var height1 = jQuery(".elementor-section.elementor-section-height-full").height();
	var height2 = jQuery(".home_slide .text_section").height();
	var heightimg = height1 - height2;
	var windowsize = jQuery(window).width();
	
	//alert(windowsize);
	if(windowsize <= 800){	
		jQuery( ".status_col label span" ).each(function( index ) {
		   jQuery( this ).html(jQuery( this ).text().replace(/\ /g, '<br/>'));
		});
		jQuery( ".date_col label" ).each(function( index ) {
		   jQuery( this ).html(jQuery( this ).text().replace(/\ /g, '<br/>'));
		});
		jQuery( "th.ask_fee" ).html(jQuery( "th.ask_fee" ).text().replace(/\ /g, '<br/>'));
		jQuery(".site-info").text('<?php echo "© 2018-".date("Y")." ShopADoc Inc. All Rights Reserved"?>');
		
		var navbarHeight = jQuery('.navbar').height();
		
	<?php if($wp->request =='auction-activity/auction'){?>
		var footerHeight = 45;
		var rotation_mainHeight = jQuery('.rotation_main .rotation_set').height();
		var product_listing_topHeight = jQuery('.product-listing-top').height();
		if (product_listing_topHeight === null){
			product_listing_topHeight = 0;
		}
		//alert($("body").height()+"=="+$(window).height());
		var full_window_height = jQuery(window).height();
		var body_height = jQuery("body").height();
		
		var my_listing_customHeight = parseInt(full_window_height) - parseInt(navbarHeight) - parseInt(footerHeight) - parseInt(rotation_mainHeight) - parseInt(product_listing_topHeight) ;
		jQuery(".my-listing-custom").height(my_listing_customHeight+"px");
		jQuery(".rotation_main").css('margin-top','5px');
		jQuery(".rotation_main").css('margin-bottom','5px');
		
		
		if((navigator.userAgent.match(/iPhone/i)) && windowsize <= 448) {
			
			var my_listing_customHeight = parseInt(full_window_height) - parseInt(navbarHeight) - parseInt(rotation_mainHeight) - parseInt(product_listing_topHeight) + 15;
			//alert(my_listing_customHeight);
			//if(my_listing_customHeight > 270){
				my_listing_customHeight = '240'
			//}
			jQuery(".my-listing-custom").height(my_listing_customHeight+"px");
			jQuery(".my-listing-custom").css({
   												"overflow-y": "scroll",
    											"-webkit-overflow-scrolling": "touch"
											});
		}
		
		if(windowsize >= 448){
			jQuery(".rotation_main").css('width','85%').css('float','none').css('margin','0 auto');
			jQuery(".rotation_ad").css('width','49.5%');
			jQuery(".ad_section_main").css('margin-bottom','5px');
			var rotation_mainHeight = jQuery('.rotation_main .rotation_set').height();
			var full_window_height = jQuery(window).height();
			var my_listing_customHeight = parseInt(full_window_height) - parseInt(navbarHeight) - parseInt(footerHeight) - parseInt(rotation_mainHeight) - parseInt(product_listing_topHeight) + 120;
			/*if(my_listing_customHeight >= 230){
				my_listing_customHeight = '204';
			}*/
			my_listing_customHeight = '310';
			jQuery(".my-listing-custom").height(my_listing_customHeight+"px");
			jQuery(".dokan-product-listing .dokan-product-listing-area .product-listing-top").css('margin-bottom','0px');
			
			
		}
		<?php }?>
	<?php if($wp->request =='shopadoc-auction-activity'){?>
		var footerHeight = 30;
		var rotation_mainHeight = jQuery('.rotation_main .rotation_set').height();
		var product_listing_topHeight = jQuery('.product-listing-top').height();
		if (product_listing_topHeight === null){
			product_listing_topHeight = 0;
		}
		//alert($("body").height()+"=="+$(window).height());
		var full_window_height = jQuery(window).height();
		var body_height = jQuery("body").height();
		
		var my_listing_customHeight = parseInt(full_window_height) - parseInt(navbarHeight) - parseInt(footerHeight) - parseInt(rotation_mainHeight) - parseInt(product_listing_topHeight) ;
		jQuery(".my-listing-custom").height(my_listing_customHeight+"px");
		jQuery(".rotation_main").css('margin-top','5px');
		jQuery(".rotation_main").css('margin-bottom','5px');
		
		if((navigator.userAgent.match(/iPhone/i)) && windowsize <= 448) {
			var my_listing_customHeight = parseInt(full_window_height) - parseInt(navbarHeight) - parseInt(rotation_mainHeight) - parseInt(product_listing_topHeight) +20;
			//if(my_listing_customHeight > 270){
				my_listing_customHeight = '312'
			//}
			jQuery(".my-listing-custom").height(my_listing_customHeight+"px");
		}
		
		if(windowsize >= 448){
			jQuery(".rotation_main").css('width','85%').css('float','none').css('margin','0 auto');
			jQuery(".rotation_ad").css('width','49.5%');
			jQuery(".ad_section_main").css('margin-bottom','5px');
			var rotation_mainHeight = jQuery('.rotation_main .rotation_set').height();
			var full_window_height = jQuery(window).height();
			var my_listing_customHeight = parseInt(full_window_height) - parseInt(navbarHeight) - parseInt(footerHeight) - parseInt(rotation_mainHeight) - parseInt(product_listing_topHeight) + 108;
			jQuery(".my-listing-custom").height(my_listing_customHeight+"px");
		}
		<?php }?>
		jQuery( "#profileType_section1 button.buttonstyles" ).click(function() {
			jQuery('.sgpb-popup-dialog-main-div-theme-wrapper-1').css('top','60px');
		  
		});
		
	}else{
		var navbarHeight = jQuery('.navbar').height();
		//var footerHeight = jQuery('#colophon').height();
		<?php if($role=='seller'){?>
		var footerHeight = 25;
		<?php }else{?>
		var footerHeight = 10;
		<?php }?>
		var rotation_mainHeight = jQuery('.rotation_main').height();
		var product_listing_topHeight = jQuery('.product-listing-top').height();
		if (product_listing_topHeight === null){
			product_listing_topHeight = 0;
		}
		//alert($("body").height()+"=="+$(window).height());
		var full_window_height = jQuery(window).height();
		var body_height = jQuery("body").height();
		var my_listing_customHeight = parseInt(full_window_height) - parseInt(navbarHeight) - parseInt(footerHeight) - parseInt(rotation_mainHeight) - parseInt(product_listing_topHeight) - 50;
		jQuery(".my-listing-custom").height(my_listing_customHeight+"px");
	}
		
});
<?php }?>

<?php /************New layout********/?>

(function($) {
    $(document).ready(function() { 
	
	$(function() {
	<?php 
	global $wp;
	$current_url =  home_url($wp->request);
	if(!is_product() && strpos($current_url,"checkout/order-received")==false){?>
	
	var footerHeight = $('#colophon').height();
	if (parseInt($("body").height()) - parseInt(footerHeight) > $(window).height()) {
       // $(".ads_div").css('position','inherit').css('width','100%').css("bottom",footerHeight+"px");
    }else{
		var footerHeight = $('#colophon').height();
        //$(".ads_div").css('position','fixed').css('width','86%').css("bottom",footerHeight+"px");
	}
	<?php }?>
  $(".table-wrap").each(function() {
    var nmtTable = $(this);
    var nmtHeadRow = nmtTable.find("thead tr");
    nmtTable.find("tbody tr").each(function() {
      var curRow = $(this);
      for (var i = 0; i < curRow.find("td").length; i++) {
        var rowSelector = "td:eq(" + i + ")";
        var headSelector = "th:eq(" + i + ")";
        curRow.find(rowSelector).attr('data-title', nmtHeadRow.find(headSelector).text());
      }
    });
  });
});
		$(window).on("scroll", function() {
			var footerHeight = $('#colophon').height();
			
		});
		//var coupon2 = $(".checkout_coupon.woocommerce-form-coupon");
        //coupon2.insertAfter('.shop_table.woocommerce-checkout-review-order-table');
		var coupon2 = $(".checkout_coupon.woocommerce-form-coupon").remove();
		$(".checkout_coupon.woocommerce-form-coupon").hide();
		var billing_full_name_field = $("#billing_full_name_field").remove();
		var html = '<td colspan="2" ><div class="woocommerce-form-coupon-toggle"><div class="woocommerce-info">Have a coupon? <a href="#" class="showcoupon">Click here to enter your code</a></div></div><form class="checkout_coupon woocommerce-form-coupon" method="post" style="display:none"><!--<p>If you have a coupon code, please apply it below.</p>--><p class="form-row form-row-first"><input type="text" name="coupon_code" class="input-text" placeholder="Coupon code" id="coupon_code" value=""/></p><p class="form-row form-row-last"><button type="submit" class="button" name="apply_coupon" value="Apply">Apply</button></p><div class="clear"></div></form></td>';
		$(".cart-coupon").html(html);
       // billing_full_name_field.insertAfter('.woocommerce-checkout-payment#payment');
	   // billing_full_name_field.insertAfter('.test');
		
		 var sos_help = $("#sos_help");
        sos_help.insertAfter('#wpforms-185-field_43-container .wpforms-required-label');
		$( document.body ).on( 'update_order_review', function(){
			var html = '<td colspan="2" ><div class="woocommerce-form-coupon-toggle"><div class="woocommerce-info">Have a coupon? <a href="#" class="showcoupon">Click here to enter your code</a></div></div><form class="checkout_coupon woocommerce-form-coupon" method="post" style="display:none"><!--<p>If you have a coupon code, please apply it below.</p>--><p class="form-row form-row-first"><input type="text" name="coupon_code" class="input-text" placeholder="Coupon code" id="coupon_code" value=""/></p><p class="form-row form-row-last"><button type="button" onclick="jQuery(\'.checkout_coupon\').submit();" class="button" name="apply_coupon" value="Apply">Apply</button></p><div class="clear"></div></form></td>';
		$(".cart-coupon").html(html);
		});
		
		$("#es_link").click(function() {
			$("#login_txt").text('Iniciar Sesión');
		});
		$("#en_link").click(function() {
			$("#login_txt").text('Auction Sign-in');
		});
		//
		$( 'body' ).on( 'updated_checkout', function() {
		  $("#checkout_tooltip").html('<span class="tooltip_New checkout"><span class="tooltips" title="Please check your Inbox, Spam, Junk, & Promotions tab for receipts & correspondence from ShopADoc.">&nbsp;</span></span>');
		  $(".tooltips img").closest(".tooltips").css("display", "inline-block");
                    
			new $.Zebra_Tooltips($('.tooltips').not('.custom_m_bubble'), {
				'background_color':     '#c9c9c9',
				'color':				'#000000',
				'max_width':  500,
				'opacity':    .95, 
				'position':    'center'
			});
		});
		document.addEventListener('FilesAdded', function(e) {
			alert("test");
		});
		$( 'body' ).on( 'FilesAdded', function() {
		  alert("test");
		});
    })
	
})
(jQuery);

</script>
<?php /*?>
// checks if the the device is a mobile or not by testing for regex in the navigator.userAgent

var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
var isMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i) || navigator.userAgent.match(/WPDesktop/i);
    },
    any: function() {
        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
};
/*
// after testing, we can simply rotate the html body element how many degrees we want and also set other things like width/height parameters that will be enforced later in the code. in the example below the original orientaion is set to fit to mobile horizontal display (rotated 90 degrees in css), so if the code runs on a PC i will apply 0 degrees orientation to reset the css rotation

if(isMobile.any()){
        widthRatio = 0.95;
        heightRatio = 0.65;
        var deg = 90;
        document.body.style.webkitTransform = 'rotate('+deg+'deg)'; 
        document.body.style.mozTransform    = 'rotate('+deg+'deg)'; 
        document.body.style.msTransform     = 'rotate('+deg+'deg)'; 
        document.body.style.oTransform      = 'rotate('+deg+'deg)'; 
        document.body.style.transform       = 'rotate('+deg+'deg)'; 
}
<?php */?>
<?php wp_footer(); ?>
<?php
	global $post,$wp;
	$current_url =  home_url( $wp->request );
	if(strpos($current_url,"/contact") > 0 || strpos($current_url,"/checkout") > 0 || strpos($current_url,"/my-account/payment-methods") > 0){?>
<style type="text/css">
			.responsive-menu-label{
				display:block !important;
			}
		</style>
<?php }elseif(strpos($current_url,"/shopadoc-auction-activity") > 0){?>
<style type="text/css">
			.entry-title{display:none !important;}
		</style>
<?php }else{?>
<style type="text/css">
			
		</style>
<?php }?>
<?php if(is_front_page()){?>
<?php if(!get_current_user_id()){?>
<style type="text/css">
				@media only screen and (max-width: 448px) {
					.responsive-menu-button-text{color: #fff !important;}
				}
			</style>
<?php }?>
<?php } ?>
<div id="yith-wcwl-popup-message" style="display:none;">
  <div id="yith-wcwl-message"></div>
</div>
</body></html>