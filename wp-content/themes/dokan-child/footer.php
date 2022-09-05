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
<?php /*?>
<style type="text/css">
	.sgpb-content-1640,.sgpb-popup-overlay-1640{display:none !important;}
</style>
<?php
//Code For auction relist popup

global $today_date_time,$today_date_time_seconds;

$flash_cycle_end_this = date('Y-m-d', strtotime( 'friday this week' ) )." 10:30";
//$flash_cycle_end_this = date('Y-m-d', strtotime( 'monday this week' ) )." 07:53";
$expire_relist='yes';
if(strtotime($today_date_time) >= strtotime($flash_cycle_end_this)){
	$newtimestamp = strtotime($flash_cycle_end_this.' + 3 minute');
	$to_date = date('Y-m-d H:i:s', $newtimestamp);
	if(strtotime($today_date_time) >= strtotime($to_date)){
		$expire_relist='yes';
	}else{
		$expire_relist='no';
	}
}
$post_statuses = array( 'publish',);
$args = array('post_status'         => $post_statuses,
					'ignore_sticky_posts' => 1,
					'meta_key' => '_auction_dates_from',
					'orderby' => 'meta_value',
					'order'               => 'asc',
					'author'              => dokan_get_current_user_id(),
					'posts_per_page'      => -1,
					'tax_query'           => array( array( 'taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction' ) ),
					'auction_archive'     => TRUE,
					'show_past_auctions'  => TRUE,
				);
$product_query = new WP_Query( $args );
//_auction_dates_from
$count = $product_query->found_posts;
if ( $product_query->have_posts() ) {
	while ($product_query->have_posts()) {
		$product_query->the_post();
	if($expire_relist =='no'){
		$bid_count = $wpdb->get_var($wpdb->prepare("SELECT count(id) as count FROM wp_simple_auction_log WHERE auction_id = %d LIMIT 1",$post->ID));
		$_auction_dates_to = get_post_meta($post->ID, '_auction_dates_to', true );
		$_flash_cycle_start = get_post_meta( $post->ID, '_flash_cycle_start' , TRUE);
		$_flash_cycle_end = get_post_meta( $post->ID, '_flash_cycle_end' , TRUE);
		 if(strtotime($_auction_dates_to) < strtotime($today_date_time_seconds) && strtotime($today_date_time_seconds) > strtotime($_flash_cycle_end) && ($bid_count == '' || $bid_count == 0)){
                  	$newtimestamp = strtotime($_flash_cycle_end.' + 3 minute');
						$to_date = date('Y-m-d H:i:s', $newtimestamp);
						if(strtotime($today_date_time_seconds) > strtotime($to_date)){ ?>
                        		<!--Incase Relist time Expires -->
                                <style type="text/css">
								
									.sgpb-content-1640,.sgpb-popup-overlay-1640{display:none !important;}
								</style>
						<?php }else{?>
                       			<style type="text/css">
								
									.sgpb-content-1640,.sgpb-popup-overlay-1640{display:block !important;}
								</style>
                    <?php }?>
                    <?php }else{ ?>
                    				<style type="text/css">
									
										.sgpb-content-1640,.sgpb-popup-overlay-1640{display:none !important;}
									</style>
                    <?php }?>
                <?php }else{?>
                					<style type="text/css">
									
										.sgpb-content-1640,.sgpb-popup-overlay-1640{display:none !important;}
									</style>
				<?php }
	}
}
?>
<!-- .container -->
<?php
*/
$page = '';
if(isset($_GET['action']) && $_GET['action']=='edit'){
	$page = 'edit';
}
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
<?php 
if(preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo 
|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i" 
, $_SERVER["HTTP_USER_AGENT"]) || 1==1){?>

<div class="module__item bidding"></div>
<div class="module__item black"></div>
<?php }?>
<?php 
global $wp,$post,$today_date_time_seconds;
$current_url =  home_url($wp->request);
$dentist_account_status = get_user_meta(get_current_user_id(), 'dentist_account_status', true );
$register_sub_end_date = get_user_meta(get_current_user_id(), 'register_sub_end_date', true );
$deactivate_CD = get_user_meta(get_current_user_id(), 'deactivate_CD', true );
$plan_orderid = get_plan_orderid();
$plan_status = get_post_meta($plan_orderid,'_plan_status', true );
//&&  $dentist_account_status !='de-active-sub-reg'
//$register_sub_end_date = date('Y-m-d H:i:s',$register_sub_end_date);
//$register_sub_end_date = strtotime('2022-05-22 12:00:00');
if((($dentist_account_status !='unsubscribe' && $dentist_account_status !='de-active')) || strpos($current_url,"/checkout") > 0 || $post->ID == '1735' || $post->ID == '1325' || $post->ID == '342' || $post->ID == '56' || $post->ID == '3850' || $post->ID == '54' || $post->ID == '60'  || $post->ID == '62'  || $post->ID == '66' || $post->ID == '68' || $dentist_account_status =='de-active-sub-reg' || $dentist_account_status =='de-active-sub-reg-intial'){?>
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
<?php }
if(($dentist_account_status == 'de-active-sub-reg' && strtotime($today_date_time_seconds) > $register_sub_end_date) && (strpos($current_url,"/checkout") == false && $post->ID != '1735' && $post->ID != '1325' && $post->ID != '342' && $post->ID != '56' && $post->ID != '3850' && $post->ID != '54' && $post->ID != '60'  && $post->ID != '62'  && $post->ID != '66' && $post->ID != '68')){?>
<style type="text/css">
.sgpb-content-2721, .sgpb-popup-overlay-2721 {
	display:block;
}
.sgpb-overflow-hidden {
	width: 100%;
	height: 100%;
	overflow: hidden !important;
}
</style>
<?php }
if(($dentist_account_status == 'de-active-sub-reg-intial' && strtotime($today_date_time_seconds) > $register_sub_end_date) && (strpos($current_url,"/checkout") == false && $post->ID != '1735' && $post->ID != '1325' && $post->ID != '342' && $post->ID != '56' && $post->ID != '3850' && $post->ID != '54' && $post->ID != '60'  && $post->ID != '62'  && $post->ID != '66' && $post->ID != '68')){?>
<style type="text/css">
.sgpb-content-2721, .sgpb-popup-overlay-2721 {
	display:block;
}
.sgpb-overflow-hidden {
	width: 100%;
	height: 100%;
	overflow: hidden !important;
}
</style>
<?php }
if((( $deactivate_CD=='No' ||  $deactivate_CD=='')) || strpos($current_url,"/checkout") > 0 || $post->ID == '1735' || $post->ID == '1325' || $post->ID == '342' || $post->ID == '56' || $post->ID == '3850' || $post->ID == '54' || $post->ID == '60'  || $post->ID == '62'  || $post->ID == '66' || $post->ID == '68' ){?>
<style type="text/css">
.sgpb-content-6776, .sgpb-popup-overlay-6776 {
	display:none;
}
/*.sgpb-overflow-hidden {
	width: 100%;
	height: 100%;
	overflow: hidden !important;
}*/
</style>
<?php }
?>
<?php if(is_product() || (strpos($current_url,"/auction-") > 0 && strpos($current_url,"/auction-activity") === false ) || strpos($current_url,"auction-activity/auction") > 0 || strpos($current_url,"shopadoc-auction-activity") > 0){?>
<?php if($_GET['action'] != 'edit'){?>
<div class="container content-wrap ad_section_main">
<div class="playBtnContainer"></div>
  <?php do_shortcode("[ad_section]");?>
</div>
<?php }?>
<?php }?>
  
</div>
<?php if(isset($_GET['mode'])&& $_GET['mode']=='popup'){?>

        <div class="col-md-12">
          <img src="/wp-content/themes/dokan-child/google_by_logo.png" alt="google_data_logo" style="width:195px;margin-left:-8px;"/>
        </div>
    <?php }?>
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
<?php if(is_product()){?>
<?php if($role=='seller'){?>
<style type="text/css">
	@media only screen and (max-width: 850px) {
		.current_bid .bid_amount_txt,.auction-price.starting-bid,.ended_section1 {
			width: 100%;
			text-align: right !important;
		}
		.ended_section2 {
			display:none !important;
		}
		/*.biding_form{
			padding:8% 0;
		}
		.priceBox{
			padding:1% 0 !important;
			margin:0 !important;
		}
		.priceBoxPad{
			padding: 7px 0 !important;
			margin:0 auto !important;
		}*/
	}
</style>
<?php }?>
<?php if($role=='dentist'){?>
<style type="text/css">
				.price.priceBox table tr td table tr{float:left;width:50%;}
				.price.priceBox{padding:0px 10px !important;}
				.priceBox{font-size:18px;}
				.cart-coupon{display:none !important;}	
				@media only screen and (max-width: 850px) {
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
					.sub_title{margin-top:-17px;}
					.ads{/*margin-top:5px;*/}
					.ended_section1{width:75% !important;}
					/*.woocommerce div.product{
						padding-bottom:10%;
					}
					.buttons_added{
						padding-top:0 !important;
					}*/
					/*.end_img{
						width:auto !important;
						height:auto !important;
						margin-top:-25px !important;
					}*/
				}
				@media only screen and (max-width: 448px) {
					.lang_es .ended_section1{width:72% !important;}
					.ended_section1{width:65% !important;}
				}
            </style>
<?php }?>
<?php }?>
<?php if(is_product() ){?>
<style type="text/css">	
@media only screen  and (min-width: 448px) and (max-width:850px) {
	.module {
	  height: 100vh; /* Use vh as a fallback for browsers that do not support Custom Properties */
	  height: calc(var(--vh, 1vh) * 100);
	  margin: 0 auto;
	  max-width: 100%;
	}
	.module__item{
		float:left;
		width:100%;
		overflow:hidden;
	}
	.module__item.header{
		overflow:visible;
	}
	<?php if($role=='seller'){?>
			.header{
				 height: 2.8% !important;
			}
			.details{
				 height: 23.7% !important;
			}
			.bidding{
				 height:6% !important;
				 display:flex;
			}
			.black{
				 height: 12% !important;
			}
			.ads{
				 height: 55.5% !important;
			}
	<?php }?>
	<?php if($role=='dentist'){?>
			.header{
				 height: 2.8% !important;
			}
			.details{
				 height: 26.2% !important;
			}
			.bidding{
				 height: 16% !important;
				 display:flex;
			}
			.ads{
				 height: 55% !important;
			}
	<?php }?>
	/*.footer{
		 height: 5% !important;
	}*/
	

}
@media only screen and (max-width: 448px) {
	.module {
	  height: 100vh; /* Use vh as a fallback for browsers that do not support Custom Properties */
	  height: calc(var(--vh, 1vh) * 100);
	  margin: 0 auto;
	  max-width: 100%;
	}
	.module__item{
		float:left;
		width:100%;
		/*overflow:scroll;*/
	}
	.module__item.header{
		overflow:visible;
	}
	<?php 
			global $today_date_time,$demo_listing,$product,$demo_listing;
			$product_id =  $product->get_id();
			$_auction_expired_date_time =  get_post_meta($product_id, '_auction_expired_date_time', true );
			if(strtotime($today_date_time) >  strtotime($_auction_expired_date_time) && $product_id != $demo_listing){?>
				.header{
					 height: 4.4% !important;
				}
				.details{
					 height: 45.1% !important;
					 overflow:hidden;
				}
				.bidding{
					 display:none;
				}
				.ads{
					 height: 50.5% !important;
					 padding:0;
				}
				.my-ad {
					float: none;
					width: 100%;
				}
				.rotation_ad{margin-right:2px !important}
	<?php }else{?>
		<?php if($role=='seller'){?>
				.header{
					 height: 4.4% !important;
				}
				.details{
					 height: 28.8% !important;
					 overflow:hidden;
				}
				.bidding{
					 height:4.3% !important;
					 display:flex;
				}
				.black{
					 height: 12% !important;
				}
				.ads{
					 height: 50.5% !important;
					 padding:0;
				}
				.priceBox{
					margin-left:5px !important;
					width:97% !important;
				}
				.up_img, .down_img {
					width: 45% !important;
				}
				.woocommerce #content div.product div.images, .woocommerce div.product div.images, .woocommerce-page #content div.product div.images, .woocommerce-page div.product div.images{
					height:75% !important; 
				}
				#update_distance {
					/*width: 90% !important;*/
				}
		<?php }?>
		<?php if($role=='dentist'){?>
					.header{
						 height: 4.4% !important;
					}
					.details{
						 height: 32.6% !important;
						 overflow:hidden;
					}
					.bidding{
						 height: 13% !important;
						 display:flex;
					}
					.ads{
						 height: 50% !important;
						 padding:0;
					}
		<?php }?>
	<?php }?>
	/*.footer{
		 height: 5% !important;
	}*/
}
@media only screen and (min-width: 851px) {
	.module {
	  height: 100vh; /* Use vh as a fallback for browsers that do not support Custom Properties */
	  height: calc(var(--vh, 1vh) * 100);
	  margin: 0 auto;
	  max-width: 100%;
	}
	.module__item{
		float:left;
		width:100%;
		overflow:hidden;
	}
	.module__item.header{
		overflow:visible;
	}
	.header{
	 height: 4.6% !important;
	}
	.details{
	 height: 58.4% !important;
	}
	.rotation_main{
		display: flex;
		align-self: flex-end;
	}
	.ads{
	 height: 35.5% !important;
	 float:none;
	 display:flex;
	}
	<?php if(my_phone_or_tablet() !="tab"){?>
	.rotation_main,.rotation_set,.rotation_ad.div[id^="advads-"],.rotation_set img{height:100% !important;}
	<?php }?>
}
</style>
<?php global $demo_listing,$current_user,$post; 
	if($post->ID==$demo_listing){?>
    <?php if($current_user->roles[0]=='advanced_ads_user'){?>  
	<style type="text/css">
	@media only screen and (min-width:448px) and (max-width:850px) { 
			.details{
				height:40.6% !important;
			}
			
			.bidding{height:0 !important;}
			.ads{
				height:56% !important;
			}
		}
		@media only screen and (max-width: 448px) {
			.details{
				height:46% !important;
			}
			.bidding{height:0 !important;}
			.ads{
				height:49% !important;
			}
		}
	</style>
    
    <?php }else{?> 
    <style type="text/css">
		@media only screen and (min-width:448px) and (max-width:850px) { 
		.details{
				height:28% !important;
			}
			.ads{
				height:55.5% !important;
			}
			.bidding{height:13.2% !important;}
		}
		@media only screen and (max-width: 448px) {
			.details{
				height:32% !important;
			}
			.bidding{height:15.6% !important;}
			.ads{
				height:48% !important;
			}
		}
	</style>
    <?php }?>
<?php }?>
<?php }elseif((strpos($current_url,"/auction-") > 0 || strpos($current_url,"shopadoc-auction-activity") > 0)  && $page !="edit"){?>
<style type="text/css">	
@media only screen  and (min-width: 448px) and (max-width:850px) {
	.module {
	  height: 100vh; /* Use vh as a fallback for browsers that do not support Custom Properties */
	  height: calc(var(--vh, 1vh) * 100);
	  margin: 0 auto;
	  max-width: 100%;
	}
	.module__item{
		float:left;
		width:100%;
		/*overflow:hidden;*/
	}
	<?php if($role=='seller'){?>
			.header{
				 height: 2.8% !important;
			}
			.details{
				 height: 42.2% !important;
			}
			.ads{
				 height: 55% !important;
				 z-index:1;
				 position:relative;
			}
			.rotation_main {
				display: flex;
				align-self: center;
			}
	<?php }?>
	<?php if($role=='dentist'){?>
			.header{
				 height: 2.8% !important;
			}
			.details{
				 height: 42.2% !important;
			}
			.ads{
				 height: 55% !important;
			}
			.rotation_main {
				display: flex;
				align-self: center;
			}
	<?php }?>
	/*.footer{
		 height: 5% !important;
	}*/

}
@media only screen and (max-width: 448px) {
	.module {
	  height: 100vh; /* Use vh as a fallback for browsers that do not support Custom Properties */
	  height: calc(var(--vh, 1vh) * 100);
	  margin: 0 auto;
	  max-width: 100%;
	}
	.module__item{
		float:left;
		width:100%;
		/*overflow:hidden;*/
	}
	<?php if($role=='seller'){?>
			.header{
				 height: 4.4% !important;
			}
			.details{
				 height: 45.6% !important;
			}
			.ads{
				 height: 50% !important;
				 padding:0;
			}
	<?php }?>
	<?php if($role=='dentist'){?>
			.header{
				 height:4.4% !important;
			}
			.details{
				 height: 45.6% !important;
			}
			.ads{
				 height: 50% !important;
				 padding:0;
			}
	<?php }?>
	/*.footer{
		 height: 5% !important;
	}*/
}
@media only screen and (min-width: 851px) {
	.module {
	  height: 100vh; /* Use vh as a fallback for browsers that do not support Custom Properties */
	  height: calc(var(--vh, 1vh) * 100);
	  margin: 0 auto;
	  max-width: 100%;
	}
	.module__item{
		float:left;
		width:100%;
		/*overflow:hidden;*/
	}
	.module__item.header{
		overflow:visible;
	}
	.rotation_main{
		display: flex;
		align-self: flex-end;
	}
<?php if($role=='seller'){?>
		.header{
			 height: 4.6% !important;
		}
		.details{
			 height: 57% !important;
		}
		.ads{
			 height: 38.4% !important;
			 float:none;
			 display:flex;
			 z-index:1;
			 position:relative;
		}
<?php }?>
<?php if($role=='dentist'){?>
		.header{
			 height: 4.6% !important;
		}
		.details{
			 height: 57% !important;
		}
		.ads{
			 height: 38.4% !important;
			 float:none;
			 display:flex;
			 z-index:1;
			 position:relative;
		}
<?php }?>
}
</style>
<?php }else{?>
<style type="text/css">
#main.site-main {
	min-height: calc(100vh - 31.7333px - 45px);
}
</style>
<?php }?>
<script type="text/javascript">
<?php if(is_product()){?>
function FontChangeTitle(firstFont,source){
		var i = firstFont;
		for (i = i - .7; i > 0; ) {
			  jQuery('.product_title.entry-title').css('font-size',i+'px');
			  var heightOfDiv =jQuery('.product_title.entry-title').height();
				if(heightOfDiv > 22){
					  FontChangeTitle(i,source);
					  break;
				  }else{
					   break;
				  }
			}
}

jQuery(document).ready(function() {
var windowsize = jQuery(window).width();
if(windowsize <= 850){
	var orientation = (screen.orientation || {}).type || screen.mozOrientation || screen.msOrientation;
	//alert(orientation);
	jQuery(".site-info").text('<?php echo "© 2018-".date("Y")." ShopADoc Inc. All Rights Reserved"?>');
	 var gallery = jQuery(".woocommerce-product-gallery");
	 var abuse = jQuery(".abuse");
	 jQuery(".auction-user,h1.product_title,p.auction-start").css('margin-bottom','0px');
	jQuery(".auction_detail").css('padding-bottom','0px');
	//jQuery(".woocommerce div.product").css('margin-top','-12px').css('height','207px');
	 <?php if($role=='seller'){?>
		//Place the section on right position for mobile layout
		jQuery(".navbar-top-area").addClass("module__item").addClass('header');
		jQuery("div#main.site-main").addClass("module__item").addClass('details');
		jQuery(".ad_section_main").addClass("module__item").addClass('ads');
		jQuery("#colophon").addClass("module__item").addClass('footer');
		var priceBox = jQuery(".price.priceBox");
		priceBox.appendTo('.black');
		jQuery(".price.priceBox").css('float','left').css('clear','both').css('width','100%');
		var biding_form = jQuery(".biding_form");
		biding_form.appendTo('.bidding');
		
		//jQuery(".accordion_div").show();
		//jQuery('.close_link').show();
		
		var vh = window.innerHeight * 0.01;
		// Then we set the value in the --vh custom property to the root of the document
		document.documentElement.style.setProperty('--vh', vh+'px');
		//alert(jQuery(".module__item.ads").height());
		jQuery(".rotation_main").css('width','auto').css('float','none').css('margin','0 auto');
		jQuery(".rotation_ad").css('width','48%');
		var height = jQuery(".ad_section_main").height();
		var ad_height =  height / 2 ;
		ratio = ad_height / 250;
		width = 300 * ratio - 10;
		var height = ad_height - 15;
		var ad_section_width = width * 2 + 5;

		jQuery(".price.priceBox table tr td table tr").css('line-height','17px');
		jQuery(".module__item.black .price.priceBox").css('height','100%').css('margin','0px');
		//jQuery(".rotation_ad").css('width',parseInt(width)+'px');
		//jQuery(".rotation_ad").css('height',parseInt(height)+'px');
		if(windowsize <= 448){
			//jQuery(".rotation_main").css('width','100%').css('float','left').css('margin','0 auto').css('text-align','center').css('margin-top','8px');
			jQuery(".rotation_main").css('width','100%').css('float','left').css('margin','0 auto').css('text-align','center');
			jQuery(".rotation_set").css('width','auto').css('float','none').css('margin','0 auto').css('text-align','center');
			jQuery(".rotation_ad").css('float','none').css('display','inline-block');
		}else if(windowsize > 448 && windowsize <= 850){
			jQuery(".ad_section_main").css('padding-left','0px').css('padding-right','0px');
			jQuery(".rotation_main").css('width','80%').css('float','none').css('margin','0 auto');
			jQuery(".rotation_ad").css('width','49.5%');
			//jQuery(".ad_section_main").css('margin-bottom','5px');
			
			jQuery(".biding_form").css('margin-top','0');
			jQuery(".biding_form,.black").css('width','80%').css('margin','0 auto').css('float','none');
			jQuery(".ended_section2").css('float','left');
		}else{
			jQuery(".ad_section_main").css('padding-left','0px').css('padding-right','0px');
			jQuery(".rotation_main").css('width','80%').css('float','none').css('margin','0 auto');
			jQuery(".rotation_ad").css('width','49.5%');
			//jQuery(".ad_section_main").css('margin-bottom','5px');
			
			jQuery(".biding_form").css('margin-top','0');
			jQuery(".biding_form,.black").css('width','80%').css('margin','0 auto').css('float','none');
			jQuery(".ended_section2").css('float','left');
			jQuery(".ended_section2 img").css('float','right');
		}
		
		
		//jQuery(".bidding").css('width',ad_section_width+'px').css('float','none').css('margin','0 auto');
		//jQuery(".black").css('width',ad_section_width+'px').css('float','none').css('margin','0 auto');
	  <?php }?>
	  <?php if($role=='dentist'){?>
		jQuery(".navbar-top-area").addClass("module__item").addClass('header');
		jQuery("div#main.site-main").addClass("module__item").addClass('details');
		jQuery(".ad_section_main").addClass("module__item").addClass('ads');
		jQuery("#colophon").addClass("module__item").addClass('footer');
		var biding_form = jQuery(".biding_form");
		biding_form.appendTo('.bidding');
		//jQuery(".accordion_div").show();
		//jQuery('.close_link').show();
		
		var vh = window.innerHeight * 0.01;
		document.documentElement.style.setProperty('--vh', vh+'px');
		jQuery(".rotation_main").css('width','auto').css('float','none').css('margin','0 auto');
		jQuery(".rotation_ad").css('width','48%');
		var height = jQuery(".ad_section_main").height();
		var ad_height =  height / 2 ;
		ratio = ad_height / 250;
		width = 300 * ratio - 10;
		var height = ad_height - 15;
		var ad_section_width = width * 2 + 5;
		
		//jQuery(".rotation_ad").css('width',parseInt(width)+'px');
		//jQuery(".rotation_ad").css('height',parseInt(height)+'px');
		if(windowsize <= 448){
			//jQuery(".rotation_main").css('width','100%').css('float','left').css('margin','0 auto').css('text-align','center').css('margin-top','8px');
			jQuery(".rotation_main").css('width','100%').css('float','left').css('margin','0 auto').css('text-align','center');
			jQuery(".rotation_set").css('width','auto').css('float','none').css('margin','0 auto').css('text-align','center');
			jQuery(".rotation_ad").css('float','none').css('display','inline-block');
		}else if(windowsize > 448 && windowsize <= 850){
			jQuery(".ad_section_main").css('padding-left','0px').css('padding-right','0px');
			jQuery(".rotation_main").css('width','80%').css('float','none').css('margin','0 auto');
			jQuery(".rotation_ad").css('width','49.5%');
			//jQuery(".ad_section_main").css('margin-bottom','5px');
			jQuery(".biding_form").css('margin-top','0');
			
			jQuery(".biding_form").css('width','80%').css('margin','0 auto').css('float','none');
			jQuery(".ended_section2").css('float','left');
		}else{
			jQuery(".ad_section_main").css('padding-left','0px').css('padding-right','0px');
			jQuery(".rotation_main").css('width','80%').css('float','none').css('margin','0 auto');
			jQuery(".rotation_ad").css('width','49.5%');
			//jQuery(".ad_section_main").css('margin-bottom','5px');
			jQuery(".biding_form").css('margin-top','0');
			
			jQuery(".biding_form").css('width','80%').css('margin','0 auto').css('float','none');
			jQuery(".ended_section2").css('float','left');
			jQuery(".ended_section2 img").css('float','right');
		}
		
		//jQuery(".bidding").css('width',ad_section_width+'px').css('float','none').css('margin','0 auto');
		//jQuery(".priceBox").css('width',ad_section_width+'px').css('float','none').css('margin','0 auto');
	  <?php }?>
	 //jQuery(".auction_detail").prepend(abuse);
	 jQuery(".details .content-wrap,.details .row,.details .content-area,.details .site-content,.details #main.site-main,.details div.product,.details .woocommerce-product-gallery__wrapper").css('height','100%');
	 jQuery('.details .woocommerce-product-gallery').css('height','87%');
	 jQuery(".details .flex-active-slide,.details .woocommerce-product-gallery__image,.details .woocommerce-product-gallery__wrapper div").css('height','100%');
	 
}else{
	  var gallery = jQuery(".woocommerce-product-gallery");
      gallery.insertBefore('.summary');
	  var abuse = jQuery(".abuse");
	  jQuery(".woocommerce-product-gallery").append(abuse);
		//Place the section on right position for mobile layout
		jQuery(".navbar-top-area").addClass("module__item").addClass('header');
		jQuery("div#main.site-main").addClass("module__item").addClass('details');
		jQuery(".ad_section_main").addClass("module__item").addClass('ads');
		jQuery("#colophon").addClass("module__item").addClass('footer');
		
		jQuery(".details .container.content-wrap,.details .row,.details #primary,.details #content,.details #main,.details .product,.details .summary").css('height','100%');
		setTimeout(function(){
			var margin = 12 + parseInt(jQuery('.product_title').css('margin-bottom')) + parseInt(jQuery('.priceBox').css('margin-top')) + parseInt(jQuery('.priceBox').css('margin-bottom')) + parseInt(jQuery('.auction-user.mobile_hide').css('margin-bottom'));
		if(typeof  jQuery('.sub_title').height() !=='undefined'){
			var height_sub_title =  jQuery('.sub_title').height();
		}else{
			var height_sub_title =  0;
		}
		var ad_section_main_height = jQuery(".ad_section_main").height();
		var rotation_main_height = jQuery(".rotation_main").height();
		if(rotation_main_height < 250){
			rotation_main_height = ad_section_main_height;
		}
		var ad_height = ad_section_main_height - rotation_main_height;
		//console.log(ad_section_main_height +"-"+rotation_main_height);
		if(ad_height < 0){
			var ad_height = 0;
		}
		var auction_time_height = jQuery('.auction-time').height();
		if(auction_time_height == 0){
			var auction_time_height = 69;
		}
		//console.log(auction_time_height +"="+jQuery('.auction_detail').height()+"+"+jQuery('.product_title').height()+"+"+jQuery('.priceBox').height()+"+"+jQuery('.detail_section').height()+"+"+margin);
		var sum_height =auction_time_height + jQuery('.auction_detail').height() + jQuery('.product_title').height() + height_sub_title + jQuery('.priceBox').height()+ jQuery('.detail_section').height() + margin ;
		var summary_height =jQuery('.summary.entry-summary').height();
		//console.log(summary_height +"-"+sum_height+"+"+ad_height);
		var biding_height = summary_height - (sum_height + ad_height);
		//console.log(sum_height);
		jQuery(".biding_form").css('height',biding_height+'px').css('display','table');
		if(biding_height < 110){
			jQuery('.ended_section1').css('margin-top','-2px');
			jQuery(".bid_now_img, .end_img").css('margin-top','-20px').css('width','65%');
		}
		if(biding_height > 210){
			jQuery(".bid_now_img, .end_img").css('width','100%').css('margin-top','-50px');
		}
		jQuery(".biding_form .ended_section").css('display','table-cell').css('vertical-align','middle').css('float','none');
		jQuery(".biding_form").css('opacity','1');	
		}, 1000);
		
		
		var heightOfDiv =jQuery( '.product_title.entry-title' ).height();
		var windowsize = jQuery(window).width();
		if(heightOfDiv > 22){
			FontChangeTitle('20','mobile');
		}		
		//alert(biding_height);
	 	/*jQuery(".auction-time").css('height','15%');
		jQuery(".auction_detail").css('height','5%');
		jQuery(".product_title").css('height','10%');
		jQuery(".priceBox").css('height','7%');
		jQuery(".accordion_div1").css('height','25%');
		jQuery(".biding_form").css('height','30%');*/
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
	if(windowsize <= 850){	
		jQuery( ".status_col label span" ).each(function( index ) {
		   jQuery( this ).html(jQuery( this ).text().replace(/\ /g, '<br/>'));
		});
		jQuery( ".date_col label" ).each(function( index ) {
		   jQuery( this ).html(jQuery( this ).text().replace(/\ /g, '<br/>'));
		});
		jQuery( "th.ask_fee" ).html(jQuery( "th.ask_fee" ).text().replace(/\ /g, '<br/>'));
		jQuery(".site-info").text('<?php echo "© 2018-".date("Y")." ShopADoc Inc. All Rights Reserved"?>');
		
		var navbarHeight = jQuery('.navbar').height();
		
	<?php if($wp->request =='auction-activity/auction' && $page !='edit'){?>
		jQuery(".navbar-top-area").addClass("module__item").addClass('header');
		jQuery("div#main.site-main").addClass("module__item").addClass('details');
		jQuery(".ad_section_main").addClass("module__item").addClass('ads');
		jQuery("#colophon").addClass("module__item").addClass('footer');
		jQuery(".details div,.details article").css('height','100%');
		
		jQuery("#content").css('height','calc(100% - 15px)');
		jQuery(".details .product-listing-top").css('height','15%');
		jQuery(".details .nano").css('height','85%');
		jQuery(".details .my-listing-custom").css('height','88%');
		var vh = window.innerHeight * 0.01;
		document.documentElement.style.setProperty('--vh', vh+'px');
		jQuery(".rotation_main").css('width','auto').css('float','none').css('margin','0 auto');
		jQuery(".rotation_ad").css('width','48%');
		var height = jQuery(".ad_section_main").height();
		var ad_height =  height / 2 ;
		ratio = ad_height / 250;
		width = 300 * ratio - 10;
		var height = ad_height - 15;
		
		//jQuery(".rotation_ad").css('width',parseInt(width)+'px');
		//jQuery(".rotation_ad").css('height',parseInt(height)+'px');
		if(windowsize <= 448){
			//jQuery(".rotation_main").css('width','100%').css('float','left').css('margin','0 auto').css('text-align','center').css('margin-top','8px');
			jQuery(".rotation_main").css('width','100%').css('float','left').css('margin','0 auto').css('text-align','center');
			jQuery(".rotation_set").css('width','auto').css('float','none').css('margin','0 auto').css('text-align','center');
			jQuery(".rotation_ad").css('float','none').css('display','inline-block');
		}else{
			jQuery(".ad_section_main").css('padding-left','0px').css('padding-right','0px');
			jQuery(".rotation_main").css('width','80%').css('float','none').css('margin','0 auto');
			jQuery(".rotation_ad").css('width','49.5%');
			//jQuery(".ad_section_main").css('margin-bottom','5px');
		}
		<?php }?>
	<?php if($wp->request =='shopadoc-auction-activity'){?>
		jQuery(".navbar-top-area").addClass("module__item").addClass('header');
		jQuery("div#main.site-main").addClass("module__item").addClass('details');
		jQuery(".ad_section_main").addClass("module__item").addClass('ads');
		jQuery("#colophon").addClass("module__item").addClass('footer');
		jQuery(".details div,.details article").css('height','100%');
		jQuery(".details #no_auction_div").css('height','auto');
		jQuery(".details .nano").css('height','100%');
		jQuery(".details .my-listing-custom").css('height','84%');
		
		var vh = window.innerHeight * 0.01;
		document.documentElement.style.setProperty('--vh', vh+'px');
		jQuery(".rotation_main").css('width','auto').css('float','none').css('margin','0 auto');
		jQuery(".rotation_ad").css('width','48%');
		var height = jQuery(".ad_section_main").height();
		var ad_height =  height / 2 ;
		ratio = ad_height / 250;
		width = 300 * ratio - 10;
		var height = ad_height - 15;
		
		//jQuery(".rotation_ad").css('width',parseInt(width)+'px');
		//jQuery(".rotation_ad").css('height',parseInt(height)+'px');
		if(windowsize <= 448){
			jQuery(".rotation_main").css('width','100%').css('float','left').css('margin','0 auto').css('text-align','center');
			jQuery(".rotation_set").css('width','auto').css('float','none').css('margin','0 auto').css('text-align','center');
			jQuery(".rotation_ad").css('float','none').css('display','inline-block');
		}else{
			jQuery(".ad_section_main").css('padding-left','0px').css('padding-right','0px');
			jQuery(".rotation_main").css('width','80%').css('float','none').css('margin','0 auto');
			jQuery(".rotation_ad").css('width','49.5%');
			//jQuery(".ad_section_main").css('margin-bottom','5px');
		}
		<?php }?>
		jQuery( "#profileType_section1 button.buttonstyles" ).click(function() {
			jQuery('.sgpb-popup-dialog-main-div-theme-wrapper-1').css('top','60px');
		  
		});
		
	}else{
		
		<?php if($wp->request =='auction-activity/auction' && $page !='edit'){?>
		jQuery(".navbar-top-area").addClass("module__item").addClass('header');
		jQuery("div#main.site-main").addClass("module__item").addClass('details');
		jQuery(".ad_section_main").addClass("module__item").addClass('ads');
		jQuery("#colophon").addClass("module__item").addClass('footer');
		//Change For nEw scroller
		jQuery(".details div,.details article").css('height','100%');
		
		jQuery("#content").css('height','calc(100% - 15px)');
		jQuery(".details .product-listing-top").css('height','22%').css('margin-top',"0px");
		//jQuery(".details .nano").css('height','calc(78% - 15px)');
		jQuery(".details .my-listing-custom").css('height','94%');
		<?php }?>
		<?php if($wp->request =='shopadoc-auction-activity'){?>
		jQuery(".navbar-top-area").addClass("module__item").addClass('header');
		jQuery("div#main.site-main").addClass("module__item").addClass('details');
		jQuery(".ad_section_main").addClass("module__item").addClass('ads');
		jQuery("#colophon").addClass("module__item").addClass('footer');
		jQuery(".details div,.details article").css('height','100%');
		jQuery("#content").css('height','calc(100% - 30px)');
		jQuery(".details #no_auction_div").css('height','auto');
		//jQuery(".details .nano").css('height','100%');
		jQuery(".details .my-listing-custom").css('height','calc(100% - 15px)');
		<?php }?>
	}
	function fixAdheight(){
		var height_ads = jQuery(".module__item.ads").height();
		var height_rotation_main = jQuery(".rotation_main ").height();
		var height_diff = parseFloat(height_ads) - parseFloat(height_rotation_main);
		//console.log(height_ads+"=="+height_rotation_main+"=="+height_diff);
		if(height_diff > 150){
			 jQuery(".rotation_main").css('margin-bottom','20px');
		}
	}
	setTimeout(fixAdheight,1500);
});
<?php }?>

<?php /************New layout********/?>

(function($) {
    $(document).ready(function() { 
	
	$(function() {
		
	var headerHeight = $('#page .navbar-top-area').height();
	jQuery(".navbar-top-area .navbar-nav > li > a.menu-login").attr('style','height:'+headerHeight+'px;padding:0 10px !important;display:table-cell;vertical-align:middle;');
	jQuery(".navbar-top-area .navbar-nav > li > a.menu-login-deactive").attr('style','height:'+headerHeight+'px;padding:0 10px !important;display:table-cell;vertical-align:middle;color:#fff;text-decoration:none !important;');
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
		/*var html = '<td colspan="2" ><div class="woocommerce-form-coupon-toggle"><div class="woocommerce-info">Have a coupon? <a href="#" class="showcoupon">Click here to enter your code</a></div></div><form class="checkout_coupon woocommerce-form-coupon" method="post" style="display:none"><!--<p>If you have a coupon code, please apply it below.</p>--><p class="form-row form-row-first"><input type="text" name="coupon_code" class="input-text" placeholder="Coupon code" id="coupon_code" value=""/></p><p class="form-row form-row-last"><button type="submit" class="button" name="apply_coupon" value="Apply">Apply</button></p><div class="clear"></div></form></td>';
		$(".cart-coupon").html(html);*/
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
			<?php if(isset($_GET['lang']) && $_GET['lang']=='es'){?>
				 $("#place_order").text('Realizar pedido');
				 $("#checkout_tooltip").html('<span class="tooltip_New checkout"><span class="tooltips" title="Por favor, revise la pestaña Bandeja de entrada, correo no deseado, correo no deseado y promociones para ver los recibos y la correspondencia de ShopADoc.">&nbsp;</span></span>');
				//jQuery("#billing_full_name_field_2 label").append('<span id="checkout_tooltip_new">Por favor, revise la pestaña Bandeja de entrada, correo no deseado, correo no deseado y promociones para ver los recibos y la correspondencia de ShopADoc.</span>');
			<?php }else{?>
			//jQuery("#billing_full_name_field_2 label").append('<span id="checkout_tooltip_new">Please check your Inbox, Spam, Junk, &amp; Promotions tab for receipts &amp; correspondence from ShopADoc.</span>');
		  $("#checkout_tooltip").html('<span class="tooltip_New checkout"><span class="tooltips" title="Please check your Inbox, Spam, Junk, & Promotions tab for receipts & correspondence from ShopADoc.">&nbsp;</span></span>');
		<?php	}?>
		
		/*$('#billing_full_name').focusin(  
		  function(){  
			$("#checkout_tooltip_new").fadeIn("slow");
		  }).focusout(  
		  function(){  
			$("#checkout_tooltip_new").fadeOut("slow");
		  });*/
		  
		  $(".tooltips img").closest(".tooltips").css("display", "inline-block");
                    
			new $.Zebra_Tooltips($('.tooltips').not('.custom_m_bubble'), {
				'background_color':     '#c9c9c9',
				'color':				'#000000',
				'max_width':  500,
				'opacity':    .95, 
				'position':    'center'
			});
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