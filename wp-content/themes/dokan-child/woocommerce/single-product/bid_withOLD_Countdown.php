<?php
/**
 * Auction bid
 *
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$dentist_account_status = get_user_meta(get_current_user_id(), 'dentist_account_status', true );
if($dentist_account_status =='de-active'){
	wp_redirect( home_url( '/my-account/edit-account/' ) );
	exit();
}
global $woocommerce,$demo_listing, $product, $post,$US_state,$today_date_time,$today_date_time_seconds;
setlocale(LC_MONETARY, 'en_US');
/*if($product->get_id()==3977){
	if(!isset($_GET['ref_key']) || $_GET['ref_key'] == "" || getKeyStatus($_GET['ref_key']) == 'expire'){
		wp_redirect(home_url("/"));
	}
}*/
if(!(method_exists( $product, 'get_type') && $product->get_type() == 'auction') ){
	return;
}
if($post->post_status=='private'){
	wp_redirect(home_url());
	exit();
}

$current_user = wp_get_current_user();
$product_id =  $product->get_id();
$_auction_current_bid = get_post_meta( $product_id, '_auction_current_bid', true );
$_auction_start_price = get_post_meta( $product_id, '_auction_start_price',TRUE);
if(isset($_GET['action']) && $_GET['action']=='expire'){
	if(is_user_logged_in() && $post->post_author == $current_user->ID){
		update_post_meta( $product_id, '_auction_relist_expire','yes');
	}
}
//update_post_meta($product_id, '_auction_extend_first_time', '' );
//update_post_meta($product_id, '_flash_status', 'no' );
//update_post_meta($product_id, '_auction_relist_expire', 'no' );
//update_post_meta($product_id, '_auction_relist_expire', 'no' );
$_auction_relist_expire = get_post_meta( $product_id, '_auction_relist_expire', true );
$_auction_dates_to = get_post_meta($product_id, '_auction_dates_to', true );
$_flash_cycle_start = get_post_meta($product_id, '_flash_cycle_start' , TRUE);
$_flash_cycle_end = get_post_meta($product_id, '_flash_cycle_end' , TRUE);
$_auction_maximum_travel_distance = get_post_meta($product_id, '_auction_maximum_travel_distance',true);
$_auction_relist_expire = get_post_meta( $product_id, '_auction_relist_expire', true );

$user_max_bid = $product->get_user_max_bid($product_id ,$current_user->ID );
$max_min_bid_text = $product->get_auction_type() == 'reverse' ? __( 'Your min bid is', 'wc_simple_auctions' ) : __( 'Your max bid is', 'wc_simple_auctions' );
$gmt_offset = get_option('gmt_offset') > 0 ? '+'.get_option('gmt_offset') : get_option('gmt_offset');
$auction_user = wp_get_current_user($post->post_author);
//print_r($auction_user);
$client_street = get_user_meta($post->post_author, 'client_street', true );
$client_apt_no = get_user_meta($post->post_author, 'client_apt_no', true );
$client_city = get_user_meta($post->post_author, 'client_city', true);
$client_state = get_user_meta($post->post_author, 'client_state', true);
$client_zip_code = get_user_meta($post->post_author, 'client_zip_code', true);
$address = '';

if($client_city){
	$address = $client_city;
}
if($client_state){
	$address .= ", ".$client_state;
}
if($client_zip_code){
	$address .= " ".$client_zip_code;
}
$user_role = $current_user->roles[0];

$user_id = $current_user->ID;	
$dentist_office_street = get_user_meta( $user_id, 'dentist_office_street', true );
$dentist_office_apt_no = get_user_meta( $user_id, 'dentist_office_apt_no', true );
$dentist_office_city = get_user_meta( $user_id, 'dentist_office_city', true );
$dentist_office_state = get_user_meta( $user_id, 'dentist_office_state', true );
$dentist_office_zip_code = get_user_meta( $user_id, 'dentist_office_zip_code', true );
$dentist_office_state = $US_state[$dentist_office_state];
$dentist_office_address = '';
if($dentist_office_street){
	$dentist_office_address .= $dentist_office_street;
}
if($dentist_office_apt_no){
	$dentist_office_address .= " ".$dentist_office_apt_no;
}
if($dentist_office_city){
	$dentist_office_address .= " ".$dentist_office_city;
}
if($dentist_office_state){
	$dentist_office_address .= " ".$dentist_office_state;
}
if($dentist_office_zip_code){
	$dentist_office_address .= ", ".$dentist_office_zip_code;
}
$distance = get_driving_information($dentist_office_address,trim($client_street." ".$client_apt_no." ".$address));
$distance = 30;
$client_ID = str_pad($post->post_author, 5, "0", STR_PAD_LEFT);
$auction_current_bid_price = wc_price_mujahid(get_post_meta($product_id, '_auction_current_bid', true ));
if($product_id==$demo_listing){
	$_auction_dates_from =  get_post_meta($product_id, '_auction_dates_from', true );
}else{
	$_auction_dates_from =  get_post_meta($product_id, '_auction_dates_from_org', true );
}
$_auction_expired_date_time =  get_post_meta($product_id, '_auction_expired_date_time', true );
$auction_no = get_post_meta($product_id, 'auction_#',true);
if($auction_no==""){
	$auction_no = $product_id;
}
$product_cats_ids = wc_get_product_term_ids($product_id, 'product_cat' );
$sub_title = '';
if(in_array(119,$product_cats_ids)){
	$sub_title = '<span class="sub_title">locators & retrofit service only</span>';
}
if(in_array(76,$product_cats_ids)){
	$sub_title = '<span class="sub_title">abutments & denture only</span>';
}
if(in_array(77,$product_cats_ids)){
	$sub_title = '<span class="sub_title">abutments & dentures only</span>';
}
$html_detail = '<div class="detail_section">
<p class="auction-user mobile_hide"><a href="javascript:"  class="detail_link1">Details</a><!--<span class="close_link" style="display:none;"><a href="javascript:" >X</a></span>--></p>
  <div class="accordion_div1">
    <p class="auction-start">'.apply_filters("time_left_text", __( "<strong>Auction Begins:&nbsp;</strong>", "wc_simple_auctions" ), $product).date_i18n( get_option( "date_format" ),  strtotime($_auction_dates_from)).'&nbsp;'.date_i18n( get_option( "time_format" ),  strtotime($_auction_dates_from)).' </p>
    <p class="auction-start">'.apply_filters("time_left_text", __( "<strong>Auction Ends:&nbsp;</strong>", "wc_simple_auctions" ), $product).date_i18n( get_option( "date_format" ),  strtotime( $product->get_auction_end_time() )).'&nbsp;'.date_i18n( get_option( "time_format" ),  strtotime( $product->get_auction_end_time() )).'</p>
    <p class="auction-user">'.apply_filters("time_left_text", __( "<strong class='no_translate'>*Flash Bid Cycle<span class=\"TM_flash\">®</span>:&nbsp;</strong>", "wc_simple_auctions" ), $product).date_i18n(get_option("date_format"),strtotime($_flash_cycle_start))." @&nbsp;<span class='mobile_hide'></span><span class='no_translate'>".date("g:i A",strtotime($_flash_cycle_start))."</span> <span class='no_translate'>to</span> ".date_i18n(str_replace("@ ","",get_option("time_format")),strtotime($_flash_cycle_end)).'<span style="float:right;font-style:italic;font-size:10px;">* if needed</span></p>
    <p class="auction-user desktop_hide"><a href="javascript:"  class="detail_link"><strong>Auction #</strong></a><span class="mobile_line accordion_div popup" style="display:none;"><strong>Auction #</strong><br />'.$auction_no.'<span class="close_link" style="display:none;"><a href="javascript:" >X</a></span></span></p>
	<p class="auction-user mobile_hide"><strong>Auction #:&nbsp;</strong><span class="mobile_line">'.$auction_no.'</span></p>
	
  </div></div>';
if($user_role =='seller' ) {?>
<style type="text/css">
	.current_bid_txt, .current.auction, .winned-for.auction {
		color:red;
	}
	.current_bid_txt .amount{color:black !important;}
</style>
<?php }?>
<style type="text/css">
@media (min-width: 801px) {
	.biding_form{opacity:0;}
}
</style>
<div class="popup_product_title" data-title="<?php echo $product->get_name();?>"> &nbsp;</div>
<!----------------------Show Expire Popup------------------------------------------>
<?php 
//echo $post->post_author."==".$current_user->ID."==".$_auction_relist_expire;
if(is_user_logged_in() && $post->post_author == $current_user->ID && ($_auction_relist_expire=='' ||$_auction_relist_expire=='no')){
	//echo $_auction_dates_to."==".$today_date_time_seconds."==".$_flash_cycle_end."==".$_auction_current_bid;
	if(strtotime($_auction_dates_to) < strtotime($today_date_time_seconds) && strtotime($today_date_time_seconds) >= strtotime($_flash_cycle_end) && ($_auction_current_bid == '' || $_auction_current_bid == 0)){?>
    	<?php
		
			$newtimestamp = strtotime($_flash_cycle_end.' + 3 minute');
			$to_date = date('Y-m-d H:i:s', $newtimestamp);
			if(strtotime($today_date_time_seconds) > strtotime($to_date)){
				wp_redirect(get_permalink( $product_id )."?action=expire");
			}else{
			$hiDate = new DateTime($today_date_time_seconds);
			$loDate = new DateTime($to_date);
			$diff = $hiDate->diff($loDate);
			//echo $today_date_time_seconds."==".$to_date."<br />";
			$secs = ((($diff->format("%a") * 24) + $diff->format("%H")) * 60 + $diff->format("%i")) * 60 + $diff->format("%s");
			?>
			<script type="text/javascript">
				//setTimeout(function() {window.location.replace("<?php echo get_permalink( $product_id );?>?action=expire");}, 30000);
				jQuery(document).ready(function() {
					var timeLeft = parseInt('<?php echo $secs;?>');;
					var elem = document.getElementById('timer_div');
					var timerId = setInterval(countdown, 1000);
					function countdown() {
					  if (timeLeft == 0) {
						clearTimeout(timerId);
						elem.innerHTML = '0 seconds remaining';
						window.location.replace("<?php echo get_permalink( $product_id );?>?action=expire");
					  } else {
						elem.innerHTML = timeLeft + ' seconds remaining';
						timeLeft--;
					  }
					}
				});
			</script>
			<style type="text/css">
			.sgpb-content-1640,.sgpb-popup-overlay-1640{display:block !important;}
			</style>
        <?php }?>
    <?php }else{?>
        <style type="text/css">
        .sgpb-content-1640,.sgpb-popup-overlay-1640{display:none !important;}
        </style>
    <?php }
}else{?>
	<style type="text/css">
    .sgpb-content-1640,.sgpb-popup-overlay-1640{display:none !important;}
    </style>
<?php }?>

<!---------------------End Show Expire Popup---------------------------------------------------------------->
<!---------------------Auction Live Section----------------------------------------------------------------->
<?php if(($product->is_closed() === FALSE ) && ($product->is_started() === TRUE )){
	$_auction_dates_extend = get_post_meta($product_id, '_auction_dates_extend', true );
	$_auction_extend_counter = get_post_meta($product_id, '_auction_extend_counter', true );
	$_extend_time_start = get_post_meta($product_id, '_extend_time_start', true );
	 $auctionend = new DateTime($product->get_auction_dates_to());
	 //echo $_extend_time_start."==". $auctionend->format('Y-m-d H:i:s');
	 $remaining_second = $product->get_seconds_remaining(); 
	?>
    
    <!------------------------Auction Extended-------------------------->
    <?php if($_auction_dates_extend == 'yes' && $_auction_extend_counter == 'no'){
			/***************************Auction Extended**********************************/
			$auction_status = '<span class="extend_text">Extended</span>';
			$play_sound = home_url('wp-content/uploads/sounds/087168277-helicopter-sound-effect.mp3');
			$play_snipping = 'yes';
			$auction_detail_class = ' extend';
			/***************************End Auction Extended**********************************/
		}else{
			/***************************Check Whether Live Or Flash Cycle Live**********************************/
			$_flash_status = get_post_meta($product_id, '_flash_status', true );
			$play_sound = home_url('wp-content/uploads/sounds/auctionLive.mp3');
			$auction_detail_class = ' live';
			$play_snipping = 'no';
			if($_auction_dates_extend == 'yes'){
				//$remaining_second = $remaining_second - 5;
			}
			if($_flash_status == 'yes'){
				$right_position = '125px';
				$auction_status = '<style>@media only screen and (min-width: 800px) {.mejs-pause > button,.mejs-play > button {top: -10px;right: 100px;}.mejs-horizontal-volume-slider {margin-top: -10px !important;margin-right: 35px;}}@media only screen and (max-width: 448px) {.mejs-pause > button,.mejs-play > button {top: -12px;right: 12px;}}</style><span id="auction_status"><span class="no_translate">Flash Bid Cycle<span class="TM_flash">®</span></span> live</span>';
			}else{
				$right_position = '100px';
				$auction_status = '<span id="auction_status">auction live</span>';
			}
			/***************************END Whether Live Or Flash Cycle Live**********************************/
		?>
        	<?php if($_flash_status == 'yes'){?>
            	<input type="hidden" id="auction_live_type" value="flash"  />
            <?php }else{?>
            	<input type="hidden" id="auction_live_type" value="normal"  />
            <?php }?>
            
    	<?php }?>
        <!----------------------CountDown For Live-------------------------------->
    <div class="auction-time" id="countdown">
      <div class="main-auction auction-time-countdown" data-time="<?php echo $product->get_seconds_remaining();?>" data-auctionid="<?php echo $product_id ?>" data-format="<?php echo get_option( 'simple_auctions_countdown_format' ) ?>"></div>
    </div>
    <script type="text/javascript">
	CountDown();
	</script>
			<style type="text/css"> 
                 <?php if($user_role !='seller' ){?>
                    #mep_0{height:18px !important;}
                <?php }?>
				#mep_0{right:<?php echo $right_position;?> !important;}
                div.biding_form .bid_amount_txt.amt {
                  -webkit-animation: mymove 3s infinite; /* Safari 4.0 - 8.0 */
                  animation: mymove 3s infinite;
                }
                /* Safari 4.0 - 8.0 */
                @-webkit-keyframes mymove {
                    from {
                    -webkit-transform: scale3d(1, 1, 1);
                    transform: scale3d(1, 1, 1);
                  }
                
                  50% {
                    -webkit-transform: scale3d(1.05, 1.05, 1.05);
                    transform: scale3d(1.05, 1.05, 1.05);
                  }
                
                  to {
                    -webkit-transform: scale3d(1, 1, 1);
                    transform: scale3d(1, 1, 1);
                  }
                }
                @keyframes mymove {
                  from {
                    -webkit-transform: scale3d(1, 1, 1);
                    transform: scale3d(1, 1, 1);
                  }
                
                  50% {
                    -webkit-transform: scale3d(1.2, 1.2, 1.2);
                    transform: scale3d(1.2, 1.2, 1.2);
                  }
                
                  to {
                    -webkit-transform: scale3d(1, 1, 1);
                    transform: scale3d(1, 1, 1);
                  }
                }
            </style>
      	<div class="auction_detail <?php echo $auction_detail_class?>">
            <?php 
            if($user_role=='customer' || !is_user_logged_in()){ 
                echo do_shortcode("[simple_favourites_button]");
            }
            $auctionend = new DateTime($product->get_auction_dates_to());
            $auctionendformat = $auctionend->format('Y-m-d H:i:s');
            $time = current_time( 'timestamp' );
            $timeplus5 = date('Y-m-d H:i:s', strtotime('+5 minutes', $time));
            echo do_shortcode('[sc_embed_player_template1 autoplay="true" loops="true" volume="10" fileurl="'.$play_sound.'"]');
            echo '<input type="hidden" id="play_snipping" value="'.$play_snipping.'" />';
			
			//echo $_SERVER['HTTP_REFERER'];
			if(strpos($_SERVER['HTTP_REFERER'],'auction-activity/auction') > 0 || strpos($_SERVER['HTTP_REFERER'],'shopadoc-auction-activity') || strpos($_SERVER['HTTP_REFERER'],'report-abuse')){

			// facebook refered user
			//echo "internal";
		
			}
			else{
				//echo "external";
			// else part
			}
            ?>
            <style type="text/css">
			/*.jconfirm.jconfirm-white .jconfirm-bg, .jconfirm.jconfirm-light .jconfirm-bg {
				background-color: #fff;
				opacity: 0.9;
			}*/
			/*.jconfirm.jconfirm-white .jconfirm-bg, .jconfirm.jconfirm-light .jconfirm-bg{
			  background: rgba(255, 255, 255, 0.1); 
			  backdrop-filter: blur(5px); 
			  opacity: 1;
			}*/
			.jconfirm.jconfirm-white .jconfirm-bg, .jconfirm.jconfirm-light .jconfirm-bg{
  background-color: rgba(255, 255, 255, 0.1);
 -webkit-backdrop-filter: blur(8px);
  backdrop-filter: blur(8px);
  opacity:1;
}
				.play_btn_black{width:100%;}
				.sound-popup {
					margin-top: -10%;
					float: none;
					display: inline-block;
					margin-top:-10%;
					margin-left:0;
					/* text-align: center; */
				}
				.jc-bs3-container.container{text-align:center;}
				@media only screen and (max-width: 800px) {
					.jconfirm .jconfirm-cell{vertical-align:top;}
					.play_btn_black{width:45%;}
					.sound-popup{margin-top:0 !important;}
				}
			</style>
            <script type="text/javascript">
                jQuery( document ).ready(function() {
					//audio-5486-1_html5
					<?php /*if(trim($auction_detail_class) =='live'){*/?>
					setTimeout(function(){
						var timetext = jQuery(".mejs-playpause-button button").attr("title");
						//var time = parseFloat(timetext.replace(":","."));
						if (timetext == 'Pause') {
						
							//Its playing...do your job
							console.log('played');
						
						} else {
							//.rotation_main 100000001
							jQuery.confirm({
								title: '',
								columnClass: 'col-md-3 col-md-offset-5 no-title sound-popup',
								closeIcon: false, // hides the close icon.
								onContentReady: function () {
										//jQuery(".jconfirm.jconfirm-white .jconfirm-bg, .jconfirm.jconfirm-light .jconfirm-bg").css('background-color','#fff').css('opacity','.6');
										jQuery( ".jconfirm-bg,.jconfirm-cell" ).click(function() {
											jQuery(".mejs-play").click();
											jQuery(".rotation_main").css('position',"inherit").css('z-index','auto');
											jQuery(".jconfirm.jconfirm-light.jconfirm-open").remove();
										});													
										jQuery(".rotation_main").css('position',"inherit").css('z-index','100000001');
										//jQuery(".jconfirm .jconfirm-cell").css("vertical-align","top");width="300px" 
								},
								content: '<img src="<?php echo home_url('/wp-content/themes/dokan-child/play_btn_black.png');?>" title="ENTER" class="play_btn_black" align="center" style="float:none;display:block;margin:0 auto;" onclick="jQuery(\'.jconfirm-buttons button\').click();"/>',
								buttons: {
									Yes: {
										text: "ENTER",
										btnClass: 'btn-blue hide',
										keys: ['enter'],
										action: function(){
											jQuery(".mejs-play").click();
											jQuery(".rotation_main").css('position',"inherit").css('z-index','auto');
										}
									}
								}
							});
						}
					}, 1000);
					<?php /*}*/ ?>
                    setTimeout('jQuery(".mejs-play button").click();',1000); 
						
                });
            </script>
            <?php echo $auction_status;?>
        </div>
        <!---------------------------Title Section------------------------------------>
        <?php echo str_replace("*","",the_title( '<h1 class="product_title entry-title">', '</h1>',false)); echo $sub_title;?>
        <?php if(is_user_logged_in() && $post->post_author == $current_user->ID){?>
        	  <!-------------------------Update Distance and Price Section For Seller------------------------------------>
        	  <div class="price priceBox">
                <form action="<?php echo get_permalink()?>" method="post" id="price_form" >
                <input type="hidden" name="product_id" value="<?php echo $product_id;?>" />
                <input type="hidden" name="mode" value="update" />
                <input type="hidden" value="<?php echo $_auction_start_price;?>" id="_new_price" name="_new_price" />
                <input type="hidden" value="<?php echo $_auction_maximum_travel_distance;?>" id="_auction_maximum_travel_distance" name="_auction_maximum_travel_distance" />
                <style type="text/css">
					table tr td {
						height: 25px;
						vertical-align: middle;
					}
					@media only screen and (max-width: 448px) {
						table tr td {
							height: auto;
							width:50%;
						}
							table.pricing_table tr td {
                                height: auto;
                                width:100%;
                               float:left;
                            }
						/*.priceBox{
							padding:0 2px !important;
						}*/
					}
                </style>
                <input type="hidden" id="_auction_maximum_travel_distance_field" value="<?php echo $_auction_maximum_travel_distance;?>" />
                <table class="main_table" width="100%" border="0" >
                <tr>
                <td class="td_1"><table class="pricing_table" width="100%" border="0" >
                    <tr>
                      <td>Ask Fee:&nbsp;<span id="price_txt" class="desktop_price"><?php echo wc_price_ask_mujahid($_auction_start_price)?></span><span id="price_txt_mobile" class="mobile_price"><?php echo wc_price_ask_mujahid($_auction_start_price);?></span></td>
                    </tr>
                    <tr>
                      <td><span class="mobile_miles_label">Travel Miles:&nbsp;</span><span class="desktop_miles_label">Travel Distance:&nbsp;</span><span id="distance_txt"><?php echo $_auction_maximum_travel_distance;?> <span class="desktop_miles_label">Miles</span></span> </td>
                    </tr>
                  </table></td>
                <td  class="up_down"><table width="100%" border="0" >
                    <tr>
                      <td>
					  	<?php if($_auction_current_bid == '' || $_auction_current_bid == 0){
								$up_click = ' onclick="updatePrice(\'up\');"';
								$down_click = ' onclick="updatePrice(\'down\');"';
							  }else{
								  $up_click = '';
								  $down_click = '';
							  }
						?>
                        <span><img src="<?php echo home_url('wp-content/themes/dokan-child/Up.png');?>" <?php echo $up_click;?> alt="up" class="up_img" border="0" style="width:30px;"/><img id="down_price_btn" src="<?php echo home_url('wp-content/themes/dokan-child/Down.png');?>" <?php echo $down_click;?> alt="down" class="down_img" border="0" style="width:30px;"/></span>
                        </td>
                    </tr>
                    <tr>
                      <td><?php if($_auction_maximum_travel_distance < 36){?>
                        <span ><img src="<?php echo home_url('wp-content/themes/dokan-child/Up.png');?>" class="up_img" onclick="updateDistance('up','live');" alt="up" border="0" style="width:30px;"/><img src="<?php echo home_url('wp-content/themes/dokan-child/Down.png');?>" class="down_img" onclick="updateDistance('down','live');" alt="Down" border="0" style="width:30px;" id="down_btn"/></span>
                        <?php }?></td>
                    </tr>
                  </table></td>
                <td class="td_3"><table width="100%" border="0" >
                    <tr>
                      <td colspan="2" rowspan="2"><input type="image" src="<?php echo home_url('wp-content/themes/dokan-child/Update.png');?>"  name="update_distance" id="update_distance" style="width:100px;padding:0;border:0;background:transparent;vertical-align:top;" /></td>
                    </tr>
                  </table></td>
                </tr>
                </table>
                </form>
            </div>
            
    		<!-------------------------End Distance and Price Section For Seller------------------------------------>
        <?php }else{?>
        	<!-------------------------Update Distance and Price Section For Dentist------------------------------------>
            <style type="text/css">
				.price.priceBox table tr td table tr{float:left;width:50%;}
				.price.priceBox{padding:0px 10px !important;}
				.priceBox{font-size:18px;}	
				.price_box_left{
					width:50%;
					float:left;
				}
				.price_box_right{
					width:50%;
					float:left;
				}
				.priceBox p{
					margin: 5px 0;
					line-height: 14px;
				}
				@media only screen and (max-width: 448px) {
					.priceBox{font-size:15px;}
					.priceBox p .label_price1{float:left;width:51%;}
                            .priceBox p .label_price2{float:left;width:80%;}
					.price.priceBox{padding:0px 5px !important;}
					.priceBox p{
						font-size:10px !important;
					}
				}
				</style>
			  <div class="price priceBox">
				  <div class="price_box_left">
					<p><span class="label_price1">Ask Fee:&nbsp;</span><span class="label_text1"><span id="price_txt" class="desktop_price"><?php echo wc_price_ask_mujahid($_auction_start_price)?></span><span id="price_txt_mobile" class="mobile_price"><?php echo wc_price_ask_mujahid($_auction_start_price);?></span></span></p>
				  </div>
				  <div class="price_box_right">
					<p><span class="label_price2"><span class="mobile_miles_label">Travel Miles:&nbsp;</span><span class="desktop_miles_label">Travel Distance:&nbsp;</span></span><span class="label_text2"><span id="distance_txt"><?php echo $_auction_maximum_travel_distance;?> </span> <span class="desktop_miles_label">Miles</span></span></p>
				  </div>
			  </div>
            <!-------------------------End Distance and Price Section For Dentist------------------------------------>
        <?php }?>
        <!-----------------------------Auction Detail,Price,Bid Form Live auction Section------------------------------------>
        <div class='bidding_section_live auction-ajax-change' >
        <!-----------------------------Auction Detail Section Live auction Section------------------------------------>
        	<?php echo $html_detail;?>
        <!-----------------------------End Auction Detail Section Live auction Section--------------------------------->
        <!-----------------------------Auction Prices Section Live----------------------------------------------------->
        
           </div>
        <div class="biding_form">
        <div class="ended_section">
        <div class="ended_section1">
        <?php if($user_role=='seller' || $user_role=='customer'){?>
		  <?php if($_auction_current_bid == '' || $_auction_current_bid == 0){?>
          
                  <div class="buttons_added current_bid"> 
                    <span class="bid_amount_txt">
                        <span class="auction-price starting-bid" data-auction-id="<?php echo esc_attr( $product_id ); ?>" data-bid="" data-status="running"><span class="starting auction current_bid_txt">Current Bid:&nbsp;</span><span class="woocommerce-Price-amount_new amount"><?php if($_auction_start_price <=1 ){?><span class="woocommerce-Price-currencySymbol"></span><span class="underline_amt underline_new">NONE</span><?php }else{?><span class="underline_new">NONE</span><?php }?></span></span>
                    </span>
                  </div>
          <?php }else{?>
                <div class="buttons_added current_bid"> 
                    <span class="bid_amount_txt current_bid_txt"> <?php  if($_auction_current_bid==.99){ echo '<span class="auction-price current-bid" data-auction-id="'.$product_id.'" data-bid="0.99" data-status="running"><span class="current auction">Current Bid:</span> <span class="woocommerce-Price-amount amount">$<span class="underline_amt underline">0</span></span></span>';}else{ echo str_replace("Ask Price","Current Bid",$product->get_price_html());}?></span>
                </div>
          <?php }?>
          <?php if((int) $distance <= (int) get_option('mile_radius') && ($user_role=='customer' || !is_user_logged_in())){?>
          		<div class="buttons_added"> 
                	<!-----------------NEXT BID Text And Value And bid success Msg----------------------->
                	<span class="next_bid_txt bid_amount_txt red">NEXT BID:</span>
                    <?php if($_auction_start_price <=1 && $_auction_current_bid !=.99){?>
                 		<span class="next_bid bid_amount_txt amt1">&nbsp;<?php echo '$<span class="underline_amt underline">0</span>'; ?></span>
                    <?php }else{?>
             				<span class="next_bid bid_amount_txt <?php if($_auction_current_bid!=.99){?>amt<?php }?>"><?php  if($_auction_current_bid==.99){echo '&nbsp;<span class="underline_new">NONE</span>';}else{echo '<span class="woocommerce-Price-currencySymbol">$</span><span class="underline_amt">'.$product->bid_value().'</span>'; }?>
                        </span>
                    <?php }?>
                    <?php //echo '<span class="woocommerce-Price-currencySymbol">$</span><span class="underline_amt">'.money_format('%i', (float)$product->bid_value())."</span>";?>
                    
                  	<span id="bid_msg"></span>
                    <!-----------------End NEXT BID Text And Value And bid success Msg Section----------------------->
                </div>
                
                <div id="winning_bid_msg" style="display:none;">
                	<div class="woocommerce-error green" role="alert">Winning Bid is $1</div>      	
                </div>
                <div id="no_bid_msg" style="display:none;">
                	<!--<div class="woocommerce-error green" role="alert">Your bid is registered.</div> -->     	
                </div>
          <?php }?>
      <?php }?>
      </div>
           <!-----------------------------End Auction Prices Section Live-------------------------------------------------->
           <!-----------------------------Bidding Form------------------------------------------------------------------------->
      <div class="ended_section2">
           <?php do_action('woocommerce_before_bid_form'); ?>
           <?php if((int) $distance <= (int) get_option('mile_radius') && ($user_role=='customer' || !is_user_logged_in())){?>
           		<div class="ajax_loader"><img src="<?php echo home_url('/wp-content/themes/dokan-child/ajax_loader.gif');?>" alt="ajax request" title="ajax loader" /></div>
               <form id="auction_form" class="auction_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo $product_id; ?>" >
                <?php do_action('woocommerce_before_bid_button'); ?>
                <input type="hidden" id="bid" name="bid" value="<?php echo esc_attr( $product_id ); ?>" />
                <div class="quantity buttons_added"> 
                	<?php 
						$already_bid = 'no';
						$message_bid = woocommerce__simple_auctions_winning_bid_message_custom($product_id);
						echo $message_bid;
						if($message_bid!=""){
							$already_bid = 'yes';
						}
                  	?>
                  <input type="button" value="+" class="plus" />
                  <?php if( $_auction_start_price <=1&& 1==2){?>
                  	<input type="hidden" id="bid_value" name="bid_value" data-auction-id="<?php echo esc_attr( $product_id ); ?>"  <?php if ($product->get_auction_sealed() != 'yes'){ ?> value="<?php if($_auction_current_bid==1 || $_auction_start_price <=1){ echo '-1';}else{echo $product->bid_value();} ?>" max="<?php if($_auction_current_bid==1 || $_auction_start_price <=1){ echo '-1';}else{echo $product->bid_value();}  ?>"  <?php } ?> step="any" size="<?php echo strlen($product->get_curent_bid())+2 ?>" title="bid"  class="input-text  qty bid text left" readonly="readonly">
				<?php }else{?>
                  <input type="hidden" id="bid_value" name="bid_value" data-auction-id="<?php echo esc_attr( $product_id ); ?>"  <?php if ($product->get_auction_sealed() != 'yes'){ ?> value="<?php if($_auction_current_bid==1 || $_auction_start_price <=1){ echo .99;}else{echo $product->bid_value();} ?>" max="<?php if($_auction_current_bid==1 || $_auction_start_price <=1){ echo .99;}else{echo $product->bid_value();}  ?>"  <?php } ?> step="any" size="<?php echo strlen($product->get_curent_bid())+2 ?>" title="bid"  class="input-text  qty bid text left" readonly="readonly">
                  <?php }?>
                  <input type="button" value="-" class="minus" />
                  <input type="hidden" id="already_bid" value="<?php echo $already_bid;?>" />
                </div>
				<?php //$subscriptions_users = YWSBS_Subscription_Helper()->get_subscriptions_by_user($current_user->ID);?>
                <?php if (!is_user_logged_in() ) { ?>
                    <input type="image" class="bid_now_img bid_button" name="submit" src="<?php echo home_url().'/bid_now_btn.png';?>" border="0" alt="Submit" />
                <?php }else{
						$plan_status = get_plan_status();
						if($plan_status=='active'){?>
							<a id="bid_anchor" href="javascript:AjaxTest('<?php echo $already_bid;?>');"  onclick='	jQuery("#bid_anchor").css("position","relative").css("z-index","-1");'><img class="bid_now_img bid_button" src="<?php echo home_url().'/bid_now_btn.png';?>" border="0" alt="With each bid the amount is reduced by 3%" style="" /></a>
						<?php }else{
							$dentist_account_status = get_user_meta(get_current_user_id(), 'dentist_account_status', true );
							if($dentist_account_status =='unsubscribe'){?>
								<img class="bid_now_img bid_button reactive_btn" src="<?php echo home_url().'/bid_now_btn.png';?>" border="0" alt="With each bid the amount is reduced by 3%" style="" />
							<?php }else{?>
								<img class="bid_now_img bid_button bid_on" src="<?php echo home_url().'/bid_now_btn.png';?>" border="0" alt="With each bid the amount is reduced by 3%" style="" /> 
							<?php }?>
						<?php }?>
                <?php }?>
                <input type="hidden" id="place-bid" name="place-bid" value="<?php echo $product_id; ?>" />
                <input type="hidden" id="product_id" name="product_id" value="<?php echo esc_attr( $product_id ); ?>" />
                <?php if ( is_user_logged_in() ) { ?>
                <input type="hidden" id="user_id" name="user_id" value="<?php echo  get_current_user_id(); ?>" />
                <?php  } ?>
                <?php do_action('woocommerce_after_bid_button'); ?>
              </form>
           <?php }?>
       </div>
       </div>
       </div>
           <!-----------------------------End Bid Form---------------------------------------------------------------------->
           <!----------------------------End Auction Detail,Price,Bid Form Live auction Section----------------------------->
<?php }elseif(($product->is_closed() === FALSE ) && ($product->is_started() === FALSE )){?>
		<!----------------------Countdown to auction Section----------------------------------------------------------------->
		<!----------------------CountDown For Upcoming----------------------------------------------------------------------->
		<div class="auction-time future" id="countdown">
      		<div class="auction-time-countdown future" data-time="<?php echo $product->get_seconds_to_auction() ?>" data-auctionid="<?php echo $product_id ?>" data-format="<?php echo get_option( 'simple_auctions_countdown_format' ) ?>"></div>
    	</div>
        <div class="auction_detail upcoming">
			<?php if($user_role=='customer' || !is_user_logged_in()){ echo do_shortcode("[simple_favourites_button]");}?>
            <span id="auction_status">countdown to auction</span>
    	</div>
        <?php echo str_replace("*","",the_title( '<h1 class="product_title entry-title">', '</h1>',false)); echo $sub_title;?>
        <?php if(is_user_logged_in() && $post->post_author == $current_user->ID){?>
        		<!-------------------------Update Distance and Price Section For Seller--------------------------------->
        		<style type="text/css">.fa {font-size: 20px !important;}</style>
        		<div class="price priceBox">
          <form action="<?php echo get_permalink()?>" method="post" id="price_form" >
            <input type="hidden" name="mode" value="update" />
            <input type="hidden" name="product_id" value="<?php echo $product_id;?>" />
            <input type="hidden" value="<?php echo $_auction_start_price;?>" id="_new_price" name="_new_price" />
            <input type="hidden" value="<?php echo $_auction_maximum_travel_distance;?>" id="_auction_maximum_travel_distance" name="_auction_maximum_travel_distance" />
            <style type="text/css">
                table tr td {
                    height: 25px;
                    vertical-align: middle;
                }
				@media only screen and (max-width: 448px) {
					table tr td {
						 height: auto;
                    	width:50%;
					}
					table.pricing_table tr td {
						height: auto;
						width:100%;
					   float:left;
					}
					/*.priceBox{
						padding:0 2px !important;
					}*/
				}
            </style>
            <input type="hidden" id="_auction_maximum_travel_distance_field" value="<?php echo $_auction_maximum_travel_distance;?>" />
            <table class="main_table" width="100%" border="0" >
              <tr>
                <td class="td_1"><table class="pricing_table" width="100%" border="0" >
                    <tr>
                      <td>Ask Fee:&nbsp;<span id="price_txt" class="desktop_price"><?php echo wc_price_ask_mujahid($_auction_start_price)?></span><span id="price_txt_mobile" class="mobile_price"><?php echo wc_price_ask_mujahid($_auction_start_price);?></span></td>
                    </tr>
                    <tr>
                      <td><span class="mobile_miles_label">Travel Miles:&nbsp;</span><span class="desktop_miles_label">Travel Distance:&nbsp;</span><span id="distance_txt"><?php echo $_auction_maximum_travel_distance;?> <span class="desktop_miles_label">Miles</span></span></td>
                    </tr>
                  </table></td>
                <td  class="up_down"><table width="100%" border="0" >
                    <tr>
                      <td>
					  	<?php if($_auction_current_bid == '' || $_auction_current_bid == 0){
								$up_click = ' onclick="updatePrice(\'up\');"';
								$down_click = ' onclick="updatePrice(\'down\');"';
							  }else{
								  $up_click = '';
								  $down_click = '';
							  }
						?>
                        <span><img src="<?php echo home_url('wp-content/themes/dokan-child/Up.png');?>" <?php echo $up_click;?> alt="up" class="up_img" border="0" style="width:30px;"/><img id="down_price_btn" src="<?php echo home_url('wp-content/themes/dokan-child/Down.png');?>" <?php echo $down_click;?> alt="down" class="down_img" border="0" style="width:30px;"/></span>
                        </td>
                    </tr>
                    <tr>
                      <td><?php if($_auction_maximum_travel_distance < 36){?>
                        <span ><img src="<?php echo home_url('wp-content/themes/dokan-child/Up.png');?>" class="up_img" onclick="updateDistance('up','countdown');" alt="up" border="0" style="width:30px;"/><img src="<?php echo home_url('wp-content/themes/dokan-child/Down.png');?>" class="down_img" onclick="updateDistance('down','countdown');" alt="Down" border="0" style="width:30px;" id="down_btn"/></span>
                        <?php }?></td>
                    </tr>
                  </table></td>
                <td class="td_3"><table width="100%" border="0" >
                    <tr>
                      <td colspan="2" rowspan="2"><input type="image" src="<?php echo home_url('wp-content/themes/dokan-child/Update.png');?>"  name="update_distance" id="update_distance" style="width:100px;padding:0;border:0;background:transparent;vertical-align:top;" /></td>
                    </tr>
                  </table></td>
              </tr>
            </table>
          </form>
        </div>
		
    			<!-------------------------End Distance and Price Section For Seller------------------------------------>
        <?php }else{?>
        		<!-------------------------Update Distance and Price Section For Dentist-------------------------------->
        		<style type="text/css">
				.price.priceBox table tr td table tr{float:left;width:50%;}
				.price.priceBox{padding:0px 10px !important;}
				.priceBox{font-size:18px;}	
				.price_box_left{
					width:50%;
					float:left;
				}
				.price_box_right{
					width:50%;
					float:left;
				}
				.priceBox p{
					margin: 5px 0;
					line-height: 14px;
				}
				@media only screen and (max-width: 448px) {
					.price.priceBox{padding:0px 5px !important;}
					.priceBox{font-size:15px;}
					.priceBox p .label_price1{float:left;width:51%;}
                            .priceBox p .label_price2{float:left;width:80%;}
					.priceBox p{
						font-size:10px !important;
					}
				}
				</style>
				<div class="price priceBox">
				  <div class="price_box_left">
					<p><span class="label_price1">Ask Fee:&nbsp;</span><span class="label_text1"><span id="price_txt" class="desktop_price"><?php echo wc_price_ask_mujahid($_auction_start_price)?></span><span id="price_txt_mobile" class="mobile_price"><?php echo wc_price_ask_mujahid($_auction_start_price);?></span></span></p>
				  </div>
				  <div class="price_box_right">
					<p><span class="label_price2"><span class="mobile_miles_label">Travel Miles:&nbsp;</span><span class="desktop_miles_label">Travel Distance:&nbsp;</span></span><span class="label_text2"><span id="distance_txt"><?php echo $_auction_maximum_travel_distance;?> <span class="desktop_miles_label">Miles</span></span></span></p>
				  </div>
				</div>
                <!-------------------------End Distance and Price Section For Dentist------------------------------->
		<?php }?>
        <?php echo $html_detail;?>
        <div class="biding_form">
            <div class="ended_section">
                <div class="ended_section1" <?php if($user_role =='seller'){?> style="width:100%;"<?php }?>>
                <?php if($user_role=='seller' || $user_role=='customer'){?>
                      <div class="buttons_added current_bid"> 
                        <span class="bid_amount_txt">
                            <span class="auction-price starting-bid" data-auction-id="<?php echo esc_attr( $product_id ); ?>" data-bid="" data-status="running"><span class="starting auction current_bid_txt">Current Bid:&nbsp;</span><span class="woocommerce-Price-amount_new amount underline"><?php if($_auction_start_price <=1 && 1==2){?>NONE<?php }else{?>NONE<?php }?></span></span>
                        </span>
                      </div>
                      <?php if($user_role !='seller'){?>
                        <div class="buttons_added"> 
                        <span class="bid_amount_txt" > 
                            <span class="starting-bid" data-auction-id="<?php echo esc_attr( $product_id ); ?>" data-bid="" data-status="running"><span class="starting auction red">NEXT BID:&nbsp;</span><span class="woocommerce-Price-amount amount countdown_price"><?php if($_auction_start_price <=1){?><span class="underline">0</span><?php }else{?><?php echo wc_price_ask_mujahid($_auction_start_price)?><?php }?></span></span>
                        </span>
                    </div>
                    <div class="too_early" style="width:100%;float:left;display:none;">
                      <ul class="woocommerce-error green" role="alert">
                        <li>Too early...starts soon</li>
                      </ul>
                    </div>

                    <?php }?>
                <?php }?>
                </div>
                <?php if($user_role !='seller'){?>
            	<div class="ended_section2"> <img class="bid_button end_img" src="<?php echo home_url().'/bid_now_btn.png';?>" border="0" alt="With each bid the amount is reduced by 3%" style="" id="countdown_img"/> </div>
                <?php }?>
            </div>
         </div>
<?php }elseif($product->is_closed() === TRUE){?>
	<!----------------------Auction Closed Section------------------------------------------------------------------>
    <?php if($_auction_current_bid){?>
    	<!-------------------Auction Ended With no Bid------------------------------------------------------------>
        <div class="auction-time" id="countdown">
      		<div class="main-auction auction-time-countdown" data-status="win" data-time="<?php echo $product->get_seconds_to_auction() ?>" data-auctionid="<?php echo $product_id ?>" data-format="<?php echo get_option( 'simple_auctions_countdown_format' ) ?>"></div>
    	</div>
        <div class="auction_detail ended">
		  <?php if($user_role=='customer' || !is_user_logged_in()){ echo do_shortcode("[simple_favourites_button]");}?>
          <?php $customer_winner = get_post_meta($product_id,'_auction_current_bider', true ) ;
         		if((is_user_logged_in() && $post->post_author == $current_user->ID) || ($customer_winner ==$current_user->ID)){?>
                	 <span class="red" id="auction_status">✓ Email (Spam)</span>
                 <?php }else{?>
                 	<span id="auction_status">ended</span>
                    
          		<?php }?>
                <?php if(strtotime($today_date_time) >  strtotime($_auction_expired_date_time)  && $product_id != $demo_listing){?>
                    <script type="text/javascript">
							jQuery('.product.product-type-auction').html('<style type="text/css">.sgpb-content-1640,.sgpb-popup-overlay-1640{display:none !important;}</style><span class="bid_amount_txt" > <span class="starting-bid" data-auction-id="<?php echo esc_attr( $product_id ); ?>" data-bid="" data-status="running"><span class="starting auction red">EXPIRED LISTING&nbsp;</span></span> </span>');
							jQuery(document).ready(function() {
							//jQuery(".details").attr("style","height:45.6% !important");
							//jQuery(".bidding").attr("style","display:none !important");
							});
						</script> 
                        <?php }?>
        </div>
        <?php echo str_replace("*","",the_title( '<h1 class="product_title entry-title">', '</h1>',false)); echo $sub_title;?>
        <!-------------------------Distance and Price Section For Dentist------------------------------------------->
        <style type="text/css">
			.swf_container{display:none;}
			.price.priceBox table tr td table tr{float:left;width:50%;}
			.price.priceBox{padding:0px 10px !important;}
			.priceBox{font-size:18px;}	
			.price_box_left{
				width:50%;
				float:left;
			}
			.price_box_right{
				width:50%;
				float:left;
			}
			.priceBox p{
				margin: 5px 0;
				line-height: 14px;
			}
			@media only screen and (max-width: 448px) {
				.priceBox{font-size:15px;}
				.priceBox p .label_price1{float:left;width:51%;}
                            .priceBox p .label_price2{float:left;width:80%;}
				.price.priceBox{padding:0px 5px !important;}
				.priceBox p{
					font-size:10px !important;
				}
			}
		</style>
		<div class="price priceBox">
		  <div class="price_box_left">
			<p><span class="label_price1">Ask Fee:&nbsp;</span><span class="label_text1"><span id="price_txt" class="desktop_price"><?php echo wc_price_ask_mujahid($_auction_start_price)?></span><span id="price_txt_mobile" class="mobile_price"><?php echo wc_price_ask_mujahid($_auction_start_price);?></span></span></p>
		  </div>
		  <div class="price_box_right">
			<p><span class="label_price2"><span class="mobile_miles_label">Travel Miles:&nbsp;</span><span class="desktop_miles_label">Travel Distance:&nbsp;</span></span><span class="label_text2"><span id="distance_txt"><?php echo $_auction_maximum_travel_distance;?></span> <span class="desktop_miles_label">Miles</span></span></p>
		  </div>
		</div>
        <!-------------------------End Distance and Price Section For Dentist--------------------------------------->
        <div class='auction-ajax-change clear' >
        <?php echo $html_detail;?>
        <!-----------------------------Auction Prices Section Ended------------------------------------------------->
        <div class="biding_form">
        <div class="ended_section">
           <div class="ended_section1">
        <div class="buttons_added current_bid" style="width:100%;float:left;padding-bottom:0;"> <span class="bid_amount_txt current_bid_txt"> <?php echo str_replace("Winning Bid","Current Bid",$product->get_price_html()); ?></span></div>
		<?php if($user_role !='seller'){?>
    		<div class="buttons_added" > 
        	<span class="bid_amount_txt" > 
            	<span class="starting-bid" data-auction-id="<?php echo esc_attr( $product_id ); ?>" data-bid="" data-status="running"><span class="starting auction red">NEXT BID:&nbsp;</span>
                <?php if($_auction_start_price <=1){?><span class="woocommerce-Price-amount amount">$<span class="underline_amt_must">0</span></span><?php }else{?><span class="woocommerce-Price-amount amount"><span class="underline_amt_must">NONE</span></span><?php }?>
                </span>
            </span>
        </div>
    	<?php }?>
        <div id="no_bid_msg" style="display:none;">
                	<div class="woocommerce-error green" role="alert">Your bid is registered.</div>     	
                </div>
        </div>
          <div class="ended_section2"> <img class="bid_button end_img" src="<?php echo home_url().'/bid_now_btn.png';?>" border="0" alt="With each bid the amount is reduced by 3%" style="" /> </div>
        </div>
        </div>
     </div>
    	<!-----------------------------End Auction Prices Section Ended--------------------------------------------->
    <?php }else{?>
    	<!-------------------Auction Ended Without no Bid----------------------------------------------------------->
        
        <!-------------------Auction Ended With Flash to Come ------------------------------------------------------>
        	<?php if(strtotime($today_date_time) <= strtotime($_flash_cycle_start) && strtotime($_flash_cycle_end) > strtotime($today_date_time)){?>
            	<?php //$second = apply_filters('woocommerce_simple_auctions_get_seconds_remaining', strtotime($_flash_cycle_start)  -  (get_option( 'gmt_offset' )*3600) ,1);
						$second = apply_filters('woocommerce_simple_auctions_get_seconds_remaining', strtotime($_flash_cycle_start)  -  (get_option( 'gmt_offset' )*3600) - time() ,  1 );
				?>
                <div class="auction-time future future_flash" id="countdown">
  					<div class="main-auction auction-time-countdown future future_flash" <?php if($_auction_relist_expire=='yes' || $_GET['action']=='expire'){?> data-status="win"<?php }else{}?> data-time="<?php echo $second; ?>" data-auctionid="<?php echo $product_id ?>" data-format="<?php echo get_option( 'simple_auctions_countdown_format' ) ?>"></div>
				</div>
                <div class="auction_detail upcomming_flash">
					<?php if($user_role=='customer' || !is_user_logged_in()){ echo do_shortcode("[simple_favourites_button]");}?>
                    <span id="auction_status"><span class="countdown_txt">countdown to </span><span class="no_translate">Flash Bid Cycle<span class="TM_flash">®</span></span></span>
                </div>
                <?php echo str_replace("*","",the_title( '<h1 class="product_title entry-title">', '</h1>',false)); echo $sub_title;?>
                <?php if(is_user_logged_in() && $post->post_author == $current_user->ID){?>
                        <!-------------------------Update Distance and Price Section For Seller--------------------------------->
                        <style type="text/css">.fa {font-size: 20px !important;}</style>
                        <div class="price priceBox">
                  <form action="<?php echo get_permalink()?>" method="post" id="price_form" onsubmit="playAudio('<?php echo home_url('wp-content/uploads/sounds/Money%20(MP3).mp3')?>')">
                    <input type="hidden" name="mode" value="update" />
                    <input type="hidden" name="product_id" value="<?php echo $product_id;?>" />
                    <input type="hidden" value="<?php echo $_auction_start_price;?>" id="_new_price" name="_new_price" />
                    <input type="hidden" value="<?php echo $_auction_maximum_travel_distance;?>" id="_auction_maximum_travel_distance" name="_auction_maximum_travel_distance" />
                    <style type="text/css">
                        table tr td {
                            height: 25px;
                            vertical-align: middle;
                        }
                        @media only screen and (max-width: 448px) {
                            table tr td {
                                 height: auto;
                                width:50%;
                            }
							table.pricing_table tr td {
                                height: auto;
                                width:100%;
                               float:left;
                            }
                            /*.priceBox{
                                padding:0 2px !important;
                            }*/
                        }
                    </style>
                    <input type="hidden" id="_auction_maximum_travel_distance_field" value="<?php echo $_auction_maximum_travel_distance;?>" />
                    <table class="main_table" width="100%" border="0" >
                      <tr>
                        <td class="td_1"><table class="pricing_table" width="100%" border="0" >
                            <tr>
                              <td>Ask Fee:&nbsp;<span id="price_txt" class="desktop_price"><?php echo wc_price_ask_mujahid($_auction_start_price)?></span><span id="price_txt_mobile" class="mobile_price"><?php echo wc_price_ask_mujahid($_auction_start_price);?></span></td>
                            </tr>
                            <tr>
                              <td><span class="mobile_miles_label">Travel Miles:&nbsp;</span><span class="desktop_miles_label">Travel Distance:&nbsp;</span><span id="distance_txt"><?php echo $_auction_maximum_travel_distance;?> <span class="desktop_miles_label">Miles</span></span></td>
                            </tr>
                          </table></td>
                        <td  class="up_down"><table width="100%" border="0" >
                            <tr>
                              <td>
					  	<?php if($_auction_current_bid == '' || $_auction_current_bid == 0){
								$up_click = ' onclick="updatePrice(\'up\');"';
								$down_click = ' onclick="updatePrice(\'down\');"';
							  }else{
								  $up_click = '';
								  $down_click = '';
							  }
						?>
                        <span><img src="<?php echo home_url('wp-content/themes/dokan-child/Up.png');?>" <?php echo $up_click;?> alt="up" class="up_img" border="0" style="width:30px;"/><img id="down_price_btn" src="<?php echo home_url('wp-content/themes/dokan-child/Down.png');?>" <?php echo $down_click;?> alt="down" class="down_img" border="0" style="width:30px;"/></span>
                        </td>
                            </tr>
                            <tr>
                              <td><?php if($_auction_maximum_travel_distance < 36){?>
                                <span ><img src="<?php echo home_url('wp-content/themes/dokan-child/Up.png');?>" class="up_img" onclick="updateDistance('up','countdown_flash');" alt="up" border="0" style="width:30px;"/><img src="<?php echo home_url('wp-content/themes/dokan-child/Down.png');?>" class="down_img" onclick="updateDistance('down','countdown_flash');" alt="Down" border="0" style="width:30px;" id="down_btn"/></span>
                                <?php }?></td>
                            </tr>
                          </table></td>
                        <td class="td_3"><table width="100%" border="0" >
                            <tr>
                              <td colspan="2" rowspan="2"><input type="image" src="<?php echo home_url('wp-content/themes/dokan-child/Update.png');?>"  name="update_distance" id="update_distance" style="width:100px;padding:0;border:0;background:transparent;vertical-align:top;" /></td>
                            </tr>
                          </table></td>
                      </tr>
                    </table>
                  </form>
                </div>
                <!-------------------------End Distance and Price Section For Seller------------------------------------>
				<?php }else{?>
                        <!-------------------------Update Distance and Price Section For Dentist-------------------------------->
                        <style type="text/css">
                        .price.priceBox table tr td table tr{float:left;width:50%;}
                        .price.priceBox{padding:0px 10px !important;}
                        .priceBox{font-size:18px;}	
                        .price_box_left{
                            width:50%;
                            float:left;
                        }
                        .price_box_right{
                            width:50%;
                            float:left;
                        }
                        .priceBox p{
                            margin: 5px 0;
                            line-height: 14px;
                        }
                        @media only screen and (max-width: 448px) {
                            .price.priceBox{padding:0px 5px !important;}
                            .priceBox{font-size:15px;}
                            .priceBox p .label_price1{float:left;width:51%;}
                            .priceBox p .label_price2{float:left;width:80%;}
                            .priceBox p{
                                font-size:10px !important;
                            }
                        }
                        </style>
                        <div class="price priceBox">
                          <div class="price_box_left">
                            <p><span class="label_price1">Ask Fee:&nbsp;</span><span class="label_text1"><span id="price_txt" class="desktop_price"><?php echo wc_price_ask_mujahid($_auction_start_price)?></span><span id="price_txt_mobile" class="mobile_price"><?php echo wc_price_ask_mujahid($_auction_start_price);?></span></span></p>
                          </div>
                          <div class="price_box_right">
                            <p><span class="label_price2"><span class="mobile_miles_label">Travel Miles:&nbsp;</span><span class="desktop_miles_label">Travel Distance:&nbsp;</span></span><span class="label_text2"><span id="distance_txt"><?php echo $_auction_maximum_travel_distance;?> <span class="desktop_miles_label">Miles</span></span></span></p>
                          </div>
                        </div>
                        <!-------------------------End Distance and Price Section For Dentist------------------------------->
                <?php }?>
                <?php echo $html_detail;?>
                <div class="biding_form">
                    <div class="ended_section">
                        <div class="ended_section1" <?php if($user_role =='seller'){?> style="width:100%;"<?php }?>>
                        <?php if($user_role=='seller' || $user_role=='customer'){?>
                              <div class="buttons_added current_bid"> 
                                <span class="bid_amount_txt">
                                    <span class="auction-price starting-bid" data-auction-id="<?php echo esc_attr( $product_id ); ?>" data-bid="" data-status="running"><span class="starting auction current_bid_txt">Current Bid:&nbsp;</span><span class="woocommerce-Price-amount_new amount underline"><?php if($_auction_start_price <=1){?>NONE<?php }else{?>NONE<?php }?></span></span>
                                </span>
                              </div>
                              <?php if($user_role !='seller'){?>
                                <div class="buttons_added"> 
                                <span class="bid_amount_txt" > 
                                    <span class="starting-bid" data-auction-id="<?php echo esc_attr( $product_id ); ?>" data-bid="" data-status="running"><span class="starting auction red">NEXT BID:&nbsp;</span><span class="woocommerce-Price-amount amount countdown_price"><?php if($_auction_start_price <=1){?><span class="underline">$0</span><?php }else{?><?php echo wc_price_ask_mujahid($_auction_start_price)?><?php }?></span></span>
                                </span>
                            </div>
                            <div class="too_early" style="width:100%;float:left;display:none;">
                              <ul class="woocommerce-error green" role="alert">
                                <li>Too early...starts soon</li>
                              </ul>
                            </div>
        
                            <?php }?>
                        <?php }?>
                        </div>
                        <?php if($user_role !='seller'){?>
                        <div class="ended_section2"> <img class="bid_button end_img" src="<?php echo home_url().'/bid_now_btn.png';?>" border="0" alt="With each bid the amount is reduced by 3%" style="" id="countdown_img"/> </div>
                        <?php }?>
                    </div>
                 </div>
        <!-------------------End Auction Ended With Flash to Come ------------------------------------------------------>
            <?php }else{?>
        <!-------------------Auction Ended With Flash to Expire------------------------------------------------------>
        <div class="auction-time" id="countdown">
      		<div class="main-auction auction-time-countdown" data-status="win" data-time="<?php echo $product->get_seconds_to_auction() ?>" data-auctionid="<?php echo $product_id ?>" data-format="<?php echo get_option( 'simple_auctions_countdown_format' ) ?>"></div>
    	</div>
                    <div class="auction_detail ended">
                      <?php if($user_role=='customer' || !is_user_logged_in()){ echo do_shortcode("[simple_favourites_button]");}?>
                      <?php /*$customer_winner = get_post_meta($product_id,'_auction_current_bider', true ) ;
                            if((is_user_logged_in() && $post->post_author == $current_user->ID) || ($customer_winner ==$current_user->ID)){?>
                                 <span class="red">✓ Email (Spam)</span>
                             <?php }else{?>
                                <span>ended</span>
                            <?php }*/?>
                        <span id="auction_status">ended</span> 
                        <?php 
							if(strtotime($today_date_time) >  strtotime($_auction_expired_date_time) && $product_id != $demo_listing){?>
                        <script type="text/javascript">
							
							jQuery('.product.product-type-auction').html('<style type="text/css">.sgpb-content-1640,.sgpb-popup-overlay-1640{display:none !important;}</style><span class="bid_amount_txt" > <span class="starting-bid" data-auction-id="<?php echo esc_attr( $product_id ); ?>" data-bid="" data-status="running"><span class="starting auction red">EXPIRED LISTING&nbsp;</span></span> </span>');
							//jQuery(".site-main.module__item.details").css("height","45.6%");
							//jQuery(".module__item.bidding").hide();
						</script> 
                        <?php }?>
                    </div>
                    <?php echo str_replace("*","",the_title( '<h1 class="product_title entry-title">', '</h1>',false)); echo $sub_title;?>
                    <?php if(is_user_logged_in() && $post->post_author == $current_user->ID){?>
                        <!-------------------------Update Distance and Price Section For Seller--------------------------------->
                        <style type="text/css">.fa {font-size: 20px !important;}</style>
                        <div class="price priceBox">
                  <form action="<?php echo get_permalink()?>" method="post" id="price_form" onsubmit="playAudio('<?php echo home_url('wp-content/uploads/sounds/Money%20(MP3).mp3')?>')">
                    <input type="hidden" name="mode" value="update" />
                    <input type="hidden" name="product_id" value="<?php echo $product_id;?>" />
                    <input type="hidden" value="<?php echo $_auction_start_price;?>" id="_new_price" name="_new_price" />
                    <input type="hidden" value="<?php echo $_auction_maximum_travel_distance;?>" id="_auction_maximum_travel_distance" name="_auction_maximum_travel_distance" />
                    <style type="text/css">
                        table tr td {
                            height: 25px;
                            vertical-align: middle;
                        }
                        @media only screen and (max-width: 448px) {
                            table tr td {
                                 height: auto;
                                width:50%;
                            }
							table.pricing_table tr td {
                                height: auto;
                                width:100%;
                               float:left;
                            }
                           /* .priceBox{
                                padding:0 2px !important;
                            }*/
                        }
                    </style>
                    <input type="hidden" id="_auction_maximum_travel_distance_field" value="<?php echo $_auction_maximum_travel_distance;?>" />
                    <table class="main_table" width="100%" border="0" >
                      <tr>
                        <td class="td_1"><table class="pricing_table" width="100%" border="0" >
                            <tr>
                              <td>Ask Fee:&nbsp;<span id="price_txt" class="desktop_price"><?php echo wc_price_ask_mujahid($_auction_start_price)?></span><span id="price_txt_mobile" class="mobile_price"><?php echo wc_price_ask_mujahid($_auction_start_price);?></span></td>
                            </tr>
                            <tr>
                              <td><span class="mobile_miles_label">Travel Miles:&nbsp;</span><span class="desktop_miles_label">Travel Distance:&nbsp;</span><span id="distance_txt"><?php echo $_auction_maximum_travel_distance;?> <span class="desktop_miles_label">Miles</span></span></td>
                            </tr>
                          </table></td>
                        <td  class="up_down"><table width="100%" border="0" >
                            <tr>
                              <td>
					  	<?php if($_auction_current_bid == '' || $_auction_current_bid == 0){
								$up_click = ' onclick="updatePrice(\'up\');"';
								$down_click = ' onclick="updatePrice(\'down\');"';
							  }else{
								  $up_click = '';
								  $down_click = '';
							  }
						?>
                        <span><img src="<?php echo home_url('wp-content/themes/dokan-child/Up.png');?>" <?php echo $up_click;?> alt="up" class="up_img" border="0" style="width:30px;"/><img id="down_price_btn" src="<?php echo home_url('wp-content/themes/dokan-child/Down.png');?>" <?php echo $down_click;?> alt="down" class="down_img" border="0" style="width:30px;"/></span>
                        </td>
                            </tr>
                            <tr>
                              <td><?php if($_auction_maximum_travel_distance < 36){?>
                                <span ><img src="<?php echo home_url('wp-content/themes/dokan-child/Up.png');?>" class="up_img" onclick="updateDistance('up','ended_flash');" alt="up" border="0" style="width:30px;"/><img src="<?php echo home_url('wp-content/themes/dokan-child/Down.png');?>" class="down_img" onclick="updateDistance('down','ended_flash');" alt="Down" border="0" style="width:30px;" id="down_btn"/></span>
                                <?php }?></td>
                            </tr>
                          </table></td>
                        <td class="td_3"><table width="100%" border="0" >
                            <tr>
                              <td colspan="2" rowspan="2"><input type="image" src="<?php echo home_url('wp-content/themes/dokan-child/Update.png');?>"  name="update_distance" id="update_distance" style="width:100px;padding:0;border:0;background:transparent;vertical-align:top;" /></td>
                            </tr>
                          </table></td>
                      </tr>
                    </table>
                  </form>
                </div>
                <!-------------------------End Distance and Price Section For Seller------------------------------------>
				<?php }else{?>
                        <!-------------------------Update Distance and Price Section For Dentist-------------------------------->
                        <style type="text/css">
                        .price.priceBox table tr td table tr{float:left;width:50%;}
                        .price.priceBox{padding:0px 10px !important;}
                        .priceBox{font-size:18px;}	
                        .price_box_left{
                            width:50%;
                            float:left;
                        }
                        .price_box_right{
                            width:50%;
                            float:left;
                        }
                        .priceBox p{
                            margin: 5px 0;
                            line-height: 14px;
                        }
                        @media only screen and (max-width: 448px) {
                            .price.priceBox{padding:0px 5px !important;}
                            .priceBox{font-size:15px;}
                            .priceBox p .label_price1{float:left;width:51%;}
                            .priceBox p .label_price2{float:left;width:80%;}
                            .priceBox p{
                                font-size:10px !important;
                            }
                        }
                        </style>
                        <div class="price priceBox">
                          <div class="price_box_left">
                            <p><span class="label_price1">Ask Fee:&nbsp;</span><span class="label_text1"><span id="price_txt" class="desktop_price"><?php echo wc_price_ask_mujahid($_auction_start_price)?></span><span id="price_txt_mobile" class="mobile_price"><?php echo wc_price_ask_mujahid($_auction_start_price);?></span></span></p>
                          </div>
                          <div class="price_box_right">
                            <p><span class="label_price2"><span class="mobile_miles_label">Travel Miles:&nbsp;</span><span class="desktop_miles_label">Travel Distance:&nbsp;</span></span><span class="label_text2"><span id="distance_txt"><?php echo $_auction_maximum_travel_distance;?> <span class="desktop_miles_label">Miles</span></span></span></p>
                          </div>
                        </div>
                        <!-------------------------End Distance and Price Section For Dentist------------------------------->
                <?php }?>
                    <div class='auction-ajax-change clear' >
						<?php echo $html_detail;?>
                        <!-----------------------------Auction Prices Section Ended------------------------------------------------->
                        <div class="biding_form">
                        <div class="ended_section">
                          <div class="ended_section1">
                            <div class="buttons_added current_bid" style="width:100%;float:left;padding-bottom:0;"> <span class="bid_amount_txt current_bid_txt"> <?php echo str_replace("Winning Bid","Current Bid",$product->get_price_html()); ?></span></div>
                            <?php if($user_role !='seller'){?>
                            <div class="buttons_added" > <span class="bid_amount_txt" > <span class="starting-bid" data-auction-id="<?php echo esc_attr( $product_id ); ?>" data-bid="" data-status="running"><span class="starting auction red">NEXT BID:&nbsp;</span><span class="woocommerce-Price-amount amount"><span class="underline_amt_must">NONE</span></span></span> </span> </div>
                            <?php }?>
                          </div>
                          <?php if($user_role !='seller'){?>
                          <div class="ended_section2"> <img class="bid_button end_img" src="<?php echo home_url().'/bid_now_btn.png';?>" border="0" alt="With each bid the amount is reduced by 3%" style="" /> </div>
                          <?php }?>
                        </div>
                        </div>
                     </div>
                    <!-----------------------------End Auction Prices Section Ended--------------------------------------------->
        <!-------------------End Auction Ended With Flash to Expire------------------------------------------------------>
        	<?php }?>
	<?php }?>
<?php }?>
<!--<audio id="myAudio" controls style="display:none;">
  <source src="<?php echo home_url('wp-content/uploads/sounds/keyboard.mp3')?>" type="audio/mpeg">
  Your browser does not support the audio element.
</audio>-->
<script type="text/javascript">
	var audio_type = new Audio("<?php echo home_url('wp-content/uploads/sounds/gun-guns-shotgun-int-close-loa.wav')?>");
	var audio_end = new Audio("<?php echo home_url('wp-content/uploads/sounds/auction_failure.mp3')?>");
	function showHide(id){
		jQuery("."+id).slideToggle('slow');
	}
	function showHover(id){
		jQuery("."+id).show('slow');
	}
	function playAudio(url) {
	  var a = new Audio(url);
	  a.play();
	}
	function playAudioLoop_old(url) {
	  var audio = new Audio()
	  audio.src = url;
	  audio.loop = true;
	  audio.load();
	  audio.play();
	}
	
	var audio;
	function playAudioLoop(url) {
		audio = document.getElementById("myAudio");
		document.getElementById("myAudio").style.display="block";
		//audio = new Audio(url);
		audio.src = url;
		audio.loop = true;
		audio.volume = 0.3; 
		audio.load();
		audio.play();
	}
	
	function play(){
		audio.play();
		jQuery("#play_btn").hide();
		jQuery("#pause_btn").show();
	}
	
	function pause(){
		audio.pause();
		jQuery("#play_btn").show();
		jQuery("#pause_btn").hide();
	}
	jQuery("#price_form").submit(function(e) {
			e.preventDefault(); // avoid to execute the actual submit of the form.			
			if (jQuery("#update_distance").hasClass("btn_animate")) {
			 		jQuery("#update_distance").removeClass('btn_animate');
					playAudio('<?php echo home_url('wp-content/uploads/sounds/Money%20(MP3).mp3')?>')
					var form = jQuery(this);
					var url = '<?php echo get_site_url();?>/ajax.php';
					jQuery.ajax({
						   type: "POST",
						   url: url,
						   data: form.serialize(), // serializes the form's elements.
						   success: function(data)
						   {
							   var tmp = data.split("##");
							   jQuery("#price_txt").html(tmp[0]);
							   jQuery("#distance_txt").html(tmp[1]+' <span class="desktop_miles_label">Miles</span>');
							   jQuery("#_auction_maximum_travel_distance").val(tmp[1]);
							   jQuery("#_auction_maximum_travel_distance_field").val(tmp[1]);
						   }
						 });
			}else{
				//alert("no updated");
			}
		
		});
		function updateDistance(dir,status){
			//audio_type.play();
			var _auction_maximum_travel_distance = parseInt(jQuery("#_auction_maximum_travel_distance").val());
			var _auction_maximum_travel_distance_field = parseInt(jQuery("#_auction_maximum_travel_distance_field").val());
			if(dir == 'up'){
				_auction_maximum_travel_distance++;
				if(_auction_maximum_travel_distance > 35){
					//Custom_popup("Error!","Travel Miles must be within "+parseInt('<?php echo $_auction_maximum_travel_distance;?>')+" - 35 mile range","col-md-4 col-md-offset-4");
					Custom_popup("Error!","Travel Miles may not be increased","col-md-4 col-md-offset-4");
				}else if(_auction_maximum_travel_distance < 1){
					Custom_popup("Error!","Travel Miles may not be decreased","col-md-4 col-md-offset-4");
				}else{
					playAudio('<?php echo home_url('wp-content/uploads/sounds/gun-guns-shotgun-int-close-loa.wav')?>');
					jQuery("#update_distance").addClass('btn_animate');
					jQuery("#distance_txt").html(_auction_maximum_travel_distance+' <span class="desktop_miles_label">Miles</span>');
					jQuery("#_auction_maximum_travel_distance").val(_auction_maximum_travel_distance);
					jQuery("#update_distance").show();
					jQuery("#down_btn").show();
				}
			}
			if(dir == 'down'){
				_auction_maximum_travel_distance--;
				//if(_auction_maximum_travel_distance < parseInt('<?php echo $_auction_maximum_travel_distance;?>')){
				if(status =='countdown'){
					 if(_auction_maximum_travel_distance < 1){
						Custom_popup("Error!","Travel Miles may not be decreased","col-md-4 col-md-offset-4");
					}else{
						playAudio('<?php echo home_url('wp-content/uploads/sounds/gun-guns-shotgun-int-close-loa.wav')?>');
						jQuery("#update_distance").addClass('btn_animate');
						jQuery("#distance_txt").html(_auction_maximum_travel_distance+' <span class="desktop_miles_label">Miles</span>');
						jQuery("#_auction_maximum_travel_distance").val(_auction_maximum_travel_distance);
						jQuery("#update_distance").show();
					}
				}else{
					if(_auction_maximum_travel_distance < parseInt(_auction_maximum_travel_distance_field)){
						Custom_popup("Error!","Travel Miles may not be decreased","col-md-4 col-md-offset-4");
						//Custom_popup("Error!","Travel Miles must be within "+parseInt('<?php echo $_auction_maximum_travel_distance;?>')+" - 35 mile range","col-md-4 col-md-offset-4");
					}else if(_auction_maximum_travel_distance < 1){
						Custom_popup("Error!","Travel Miles may not be decreased","col-md-4 col-md-offset-4");
					}else{
						playAudio('<?php echo home_url('wp-content/uploads/sounds/gun-guns-shotgun-int-close-loa.wav')?>');
						jQuery("#update_distance").addClass('btn_animate');
						jQuery("#distance_txt").html(_auction_maximum_travel_distance+' <span class="desktop_miles_label">Miles</span>');
						jQuery("#_auction_maximum_travel_distance").val(_auction_maximum_travel_distance);
						jQuery("#update_distance").show();
					}
				}
			}
		}
		function updatePrice(dir){
			//audio_type.play();
			var _new_price = parseInt(jQuery("#_new_price").val());
			if(dir == 'up'){
				playAudio('<?php echo home_url('wp-content/uploads/sounds/gun-guns-shotgun-int-close-loa.wav')?>');
				_new_price++;
				jQuery("#update_distance").addClass('btn_animate');
				jQuery("#_new_price").val(_new_price);
				_new_price = _new_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
				jQuery(".woocommerce-Price-amount.amount").html('<span class="woocommerce-Price-currencySymbol">$</span><span class="underline_amt">'+_new_price+'</span>');
				jQuery("#update_price").show();
				jQuery("#down_price_btn").show();
			}
			if(dir == 'down'){
				_new_price--;
				if(_new_price < parseInt('<?php echo $_auction_start_price;?>')){
					Custom_popup("Error!","<br />Ask Fee must be greater than $"+'<?php echo custom_number_format($_auction_start_price);?>',"col-md-3 col-md-offset-4");
				}else{
				playAudio('<?php echo home_url('wp-content/uploads/sounds/gun-guns-shotgun-int-close-loa.wav')?>');
					jQuery("#update_distance").addClass('btn_animate');
					jQuery("#_new_price").val(_new_price);
					_new_price = _new_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
					jQuery(".woocommerce-Price-amount.amount").html('<span class="woocommerce-Price-currencySymbol">$</span><span class="underline_amt">'+_new_price+'</span>');
					jQuery("#update_price").show();
				}
			}
		}
jQuery(document).ready(function() {
	//jQuery(".woocommerce-product-gallery__image a").removeAttr('href');
	<?php
		$product = new WC_product($product_id);
		$attachment_ids = $product->get_gallery_image_ids();
		if(count($attachment_ids)==0){
	?>
		//jQuery(".woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image").removeClass('woocommerce-product-gallery__image');
	<?php }?>
	jQuery(document).on('click', "#countdown_img", function() {
				//Custom_popup("Error!","<h2 class='early_msg'>Too early...starts soon</h2>","col-md-6 col-md-offset-3");
				jQuery('.too_early').show();
				setTimeout("jQuery('.too_early').hide();",2000);
		});
});
</script>
<!--CHECK FOR AJAX LOADING------------->
<?php //$second = apply_filters('woocommerce_simple_auctions_get_seconds_remaining', strtotime($_flash_cycle_start)  -  (get_option( 'gmt_offset' )*3600) ,1);
			$second = apply_filters('woocommerce_simple_auctions_get_seconds_remaining', strtotime($_flash_cycle_start)  -  (get_option( 'gmt_offset' )*3600) - time() ,  1 );
?>
<input type="hidden" id="flash_countdown_sec" value="<?php echo $second?>" />
<?php $customer_winner = get_post_meta($product_id,'_auction_current_bider', true ) ;
if((is_user_logged_in() && $post->post_author == $current_user->ID) || ($customer_winner ==$current_user->ID)){?>
<input type="hidden" id="winner_screen" value="yes" />
<?php }else{?>
<input type="hidden" id="winner_screen" value="no" />
<?php }?>
<?php if($_auction_current_bid){?>
<input type="hidden" id="auction_with_bid" value="yes" />
<?php }else{?>
<?php if((is_user_logged_in() && $post->post_author == $current_user->ID)){?>
<input type="hidden" id="seller_screen" value="yes" />
<?php }else{?>
<input type="hidden" id="seller_screen" value="no" />
<?php }?>
<input type="hidden" id="auction_with_bid" value="no" />
<?php }?>
<input type="hidden" id="current_user_ID" value="<?php echo $current_user->ID;?>"  />
<input type="hidden" id="product_url" value="<?php echo get_permalink( $product_id );?>"  />
<input type="hidden" id="product_id" value="<?php echo $product_id;?>"  />

<?php global $current_user; 
	if($product_id==$demo_listing){?>
    <?php if($current_user->roles[0]=='advanced_ads_user'){
		$company = get_user_meta( $current_user->ID, 'advertiser_name', true);
		if($company==''){
			$company = 'AD DEMO';
		}
	?>
    <?php if(isset($_GET['screen']) && $_GET['screen']=='client'){
			$ad_text = 'CLIENT ADS';
		}else{
			$ad_text = 'DENTIST ADS';
		}
	?>
	<style type="text/css">
		.woocommerce-product-gallery.woocommerce-product-gallery--with-images.woocommerce-product-gallery--columns-4.images{height:100% !important;}
		.woocommerce-product-gallery__image img.zoomImg, .woocommerce div.product div.images img{float:left;}
		.biding_form .ended_section1{opacity:0 !important;}
		.product_title.entry-title,.biding_form .ended_section2,.swf_container,.abuse{display:none !important;}
		.realtime{color:#fff;margin-top:10px;animation: blinker 1s linear infinite;text-align:center;width:100%;}
		.ad_demo{color:#fff;margin-top:10px;font-size:340%;}
		.demo_ad_txt{float:left;width:100%;text-align:left;margin-top:0;height:auto !important;}
		.accordion_div1 .auction-user.mobile_hide{display:block !important;}
		.accordion_div1 .auction-user.desktop_hide{display:none !important;}
		@media only screen and (min-width: 800px) {
			.woocommerce-product-gallery__image img.zoomImg, .woocommerce div.product div.images img{float:left;}
			.demo_ad_txt{text-align:left;}
		}
		.demo_txt{float:left;width:100%;text-align:center;margin-top:0;position:absolute;bottom:0;}
		.demo_txt .priceBox{display:inline-block;width:auto;margin:0 auto !important;}
		.site-main.details .content-wrap,.site-main.details .content-wrap div.row,#primary{
			height:100%;
		}
		@media only screen and (min-width:1600px) and (max-width:3500px) { 
			.demo_txt{position:absolute;bottom:0;}
			/*.ad_demo{font-size:180px;}*/
		}
		@media only screen and (max-width: 800px) {
			.demo_txt{position:inherit;}
			.flex-active-slide a, .woocommerce-product-gallery__image a, .woocommerce-product-gallery__wrapper div a,.flex-active-slide, .woocommerce-product-gallery__image, .woocommerce-product-gallery__wrapper div{display:block !important;}
			.demo_ad_txt .demo_ad_title{width:100% !important;}
			.demo_txt .priceBox{display:inline-block;width:100%;margin:0 auto !important;margin-bottom:15px !important;}
			.woocommerce div.product{height:230px !important;}
			
			.auction-ajax-change p, .product .summary p, .woocommerce div.product .product_title, .auction_detail {
				line-height: 20px;
			}
			.woocommerce div.product{height:70% !important;}
			.demo_txt {position: inherit;height: 30%;}
			.bidding{width:97% !important;}
			.ad_demo{margin:0 !important;}
			.demo_txt .priceBox{margin-bottom:0 !important;display:flex;height:100%;}
			.demo_txt .priceBox .ad_demo {display: grid;align-items: center;width:100%;}
		}
		@media only screen and (max-width: 448px) {
			.woocommerce-product-gallery__image img.zoomImg, .woocommerce div.product div.images img, .flex-viewport {
				max-height: 100% !important;
			}
			.flex-active-slide, .woocommerce-product-gallery__image, .woocommerce-product-gallery__wrapper div{
				display:table !important;
			}
			.woocommerce div.product{height:70% !important;}
			.demo_txt {position: inherit;height: 30%;}
			.bidding{width:97% !important;}
			.ad_demo{margin:0 !important;}
			.demo_txt .priceBox{margin-bottom:0 !important;display:flex;height:100%;}
			.demo_txt .priceBox .ad_demo {display: grid;align-items: center;width:100%;}
			.auction-ajax-change p, .product .summary p, .woocommerce div.product .product_title, .auction_detail {
				line-height: 13px;
			}
		}
	</style>
    <script type="text/javascript">
		var img_width = jQuery('img.wp-post-image').attr('width');
		jQuery(".price.priceBox").html('<h1 class="center realtime">LIVE&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;REAL&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TIME</h1>');
		jQuery( '<div class="demo_txt"><div class="price priceBox"><h1 class="center ad_demo" id="company_name_txt"><?php echo $company;?></h1></div></div>' ).insertAfter( ".product" );
		jQuery( '<div class="demo_ad_txt"><h3 class="center demo_ad_title"><?php echo $ad_text;?></h3></div>' ).insertAfter( ".woocommerce-product-gallery__image a img.wp-post-image" );
		//jQuery(".demo_ad_txt .demo_ad_title").css('width',img_width+'px');
		var img_width = jQuery('img.wp-post-image').width();
		if(img_width==0){
			var img_width = jQuery('img.wp-post-image').attr('width');
		}
		jQuery(".demo_ad_txt .demo_ad_title").attr('style','width:'+img_width+'px !important');
		var windowsize = jQuery(window).width();
		if(windowsize <= 800 && windowsize > 448){
		}else if(windowsize <= 448){
			jQuery(".demo_ad_txt .demo_ad_title").attr('style','width:100% !important');
		}else{
		}
		//jQuery(".biding_form").html('<div class="price priceBox"><h1 class="center realtime">AD DEMO</h1></div>');
		function FontChange(firstFont,source,remain_height){
			var i = firstFont;
			for (i = i - 15; i > 0; ) {
				  jQuery(".ad_demo").css('font-size',i+'%');
				  var heightOfDiv =jQuery( '#company_name_txt' ).height();
				  if(source=='mobile'){
					  if(heightOfDiv > 35){
						  FontChange(i,source,remain_height);
						  break;
					  }else{
						   break;
					  }
				  }else if(source=='tab'){
					  if(heightOfDiv > 67){
						  FontChange(i,source,remain_height);
						  break;
					  }else{
						   break;
					  }
				  }else{
					    if(heightOfDiv > remain_height){
							//console.log(heightOfDiv+"=="+remain_height);
						  FontChange(i,source,remain_height);
						  break;
					  }else{
						   break;
					  }
				 }
			}
		}
		
		  jQuery(document).ready(function(){
			  var heightOfDiv = jQuery( '#company_name_txt' ).height();
			  var heightOfDiv_page= jQuery("#page").height();
			  var heightOfDiv_header = jQuery(".header").height();
			  var heightOfDiv_woocommerce_product_gallery = jQuery(".woocommerce-product-gallery").height();
				var heightOfDiv_rotation_main = jQuery(".rotation_main").height();
				var total_height = 27 + heightOfDiv_woocommerce_product_gallery + heightOfDiv_rotation_main;
				var remain_height = parseInt(heightOfDiv_page - total_height);
				//jQuery( '#company_name_txt' ).height(remain_height+"px");
			  var windowsize = jQuery(window).width();
			 if(windowsize <= 448){
			  	if(heightOfDiv > 40){
					FontChange('250','mobile',remain_height);
				}
			 }else if(windowsize > 448 && windowsize <= 800){
			  	if(heightOfDiv > 50){
					FontChange('360','tab',remain_height);
				}
			 }else{
				 FontChange('360','desktop',remain_height);
			 }
	});
	</script>
    <?php }else{?>
    <?php if(isset($_GET['screen']) && $_GET['screen']=='client'){
			$ad_text = 'CLIENT ADS';
		}else{
			$ad_text = 'DENTIST ADS';
		}
	?>
    <style type="text/css">
		.woocommerce-product-gallery.woocommerce-product-gallery--with-images.woocommerce-product-gallery--columns-4.images{height:100% !important;}
		.woocommerce-product-gallery__image img.zoomImg, .woocommerce div.product div.images img{float:left;}
		.biding_form .ended_section1{opacity:0 !important;}
		.product_title.entry-title,.biding_form .ended_section2,.swf_container,.abuse{display:none !important;}
		.realtime{color:#fff;margin-top:10px;animation: blinker 1s linear infinite;text-align:center;width:100%;}
		.ad_demo{color:#fff;margin-top:10px;font-size:340%;}
		.demo_ad_txt{float:left;width:100%;text-align:center;margin-top:0;height:auto !important;}
		.accordion_div1 .auction-user.mobile_hide{display:block !important;}
		.accordion_div1 .auction-user.desktop_hide{display:none !important;}
		@media only screen and (min-width: 800px) {
			.woocommerce-product-gallery__image img.zoomImg, .woocommerce div.product div.images img{float:left;}
			.demo_ad_txt{text-align:left;}
		}
		.demo_txt{float:left;width:100%;text-align:center;margin-top:0;position:absolute;bottom:0;}
		.demo_txt .priceBox{display:inline-block;width:auto;margin:0 auto !important;}
		.site-main.details .content-wrap,.site-main.details .content-wrap div.row,#primary{
			height:100%;
		}
		@media only screen and (min-width:1600px) and (max-width:3500px) { 
			.demo_txt{position:absolute;bottom:0;}
			.ad_demo{font-size:180px;}
		}
		@media only screen and (max-width: 800px) {
			.demo_txt{position:inherit;}
			.flex-active-slide a, .woocommerce-product-gallery__image a, .woocommerce-product-gallery__wrapper div a,.flex-active-slide, .woocommerce-product-gallery__image, .woocommerce-product-gallery__wrapper div{display:block !important;}
			.demo_ad_txt .demo_ad_title{width:100% !important;}
			.demo_txt .priceBox{display:inline-block;width:100%;margin:0 auto !important;margin-bottom:15px !important;}
			.woocommerce div.product{height:230px !important;}
			.auction-ajax-change p, .product .summary p, .woocommerce div.product .product_title, .auction_detail {
				line-height: 20px;
			}
			.demo_txt .priceBox{margin-bottom:0 !important;display:flex;}
			.demo_txt .priceBox .ad_demo {display: grid;align-items: center;width:100%;}
		}
		@media only screen and (max-width: 448px) {
			.woocommerce-product-gallery__image img.zoomImg, .woocommerce div.product div.images img, .flex-viewport {
				max-height: 100% !important;
			}
			.flex-active-slide, .woocommerce-product-gallery__image, .woocommerce-product-gallery__wrapper div{
				display:table !important;
			}
			.bidding{width:97% !important;}
			.demo_txt .priceBox{margin-bottom:0 !important;display:flex;}
			.demo_txt .priceBox .ad_demo {display: grid;align-items: center;width:100%;}
			.auction-ajax-change p, .product .summary p, .woocommerce div.product .product_title, .auction_detail {
				line-height: 13px;
			}
		}
	</style>
    <script type="text/javascript">
		//var img_width = jQuery('img.wp-post-image').attr('width');
		
		jQuery(".price.priceBox").html('<h1 class="center realtime" >LIVE&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;REAL&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TIME</h1>');
		
		//jQuery(".module__item.bidding").html('<div class="demo_txt "><div class="price priceBox"><h1 class="center ad_demo">AD DEMO</h1></div></div>' );
		jQuery( '<div class="demo_ad_txt"><h3 class="center demo_ad_title"><?php echo $ad_text;?></h3></div>' ).insertAfter( ".woocommerce-product-gallery__image a img.wp-post-image" );
		//jQuery(".demo_ad_txt .demo_ad_title").css('width',img_width+'px');
		var img_width = jQuery('img.wp-post-image').width();
		if(img_width==0){
			var img_width = jQuery('img.wp-post-image').attr('width');
		}
		jQuery(".demo_ad_txt .demo_ad_title").attr('style','width:'+img_width+'px !important');
		var windowsize = jQuery(window).width();
		if(windowsize <= 800 && windowsize > 448){
			jQuery(".biding_form").html('<div class="demo_txt "><div class="price priceBox"><h1 class="center ad_demo">AD DEMO</h1></div></div>');
			jQuery(".biding_form,.demo_txt,.price.priceBox").css('height','100%');
			jquery(".price.priceBox").attr('style','margin-bottom:0px !important');
		}else if(windowsize <= 448){
			jQuery(".demo_ad_txt .demo_ad_title").attr('style','width:100% !important');
			//jQuery( '<div class="demo_txt "><div class="price priceBox"><h1 class="center ad_demo">AD DEMO</h1></div></div>' ).insertAfter( ".product" );
			jQuery(".biding_form").html('<div class="demo_txt "><div class="price priceBox"><h1 class="center ad_demo">AD DEMO</h1></div></div>');
			jQuery(".biding_form,.demo_txt,.price.priceBox").css('height','100%');
			jquery(".price.priceBox").attr('style','margin-bottom:0px !important');
		}else{
			jQuery( '<div class="demo_txt "><div class="price priceBox"><h1 class="center ad_demo">AD DEMO</h1></div></div>' ).insertAfter( ".product" );
		}
		//jQuery(".biding_form").html('<div class="price priceBox"><h1 class="center realtime">AD DEMO</h1></div>');
	</script>
    <?php }?>
<?php }?>
<video style="opacity:0;position:inherit;z-index:-1;" width="50" height="50" loop autoplay muted >
  <source src="/blank.mp4" type="video/mp4">
  <source src="/blank.ogg" type="video/ogg">
Your browser does not support the video tag.
</video>
<script type="application/javascript">
//setTimeout(updateAuctionStatus, 5000);
setInterval(function(){
			//if(window_focus == true){
			   //getPriceAuction();
			   //Old Logic
 				//alertFunc();
			//}
		}, 5000);
function alertFunc(){
	var url = '<?php echo get_site_url();?>/ajax.php';
					jQuery.ajax({
						   type: "POST",
						   url: url,
						   data:{'mode':'mail'},
						   success: function(data)
						   {
							  
						   }
						 });
}
</script>