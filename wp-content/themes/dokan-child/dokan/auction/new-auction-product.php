<?php
global $monday,$thursday,$auction_expired_date_time,$flash_cycle_start,$flash_cycle_end;
//$timezone = getTimeZone_Custom();
//date_default_timezone_set($timezone);
$today_date_time = date('Y-m-d H:i');
$monday_auction_by = date("Y-m-d",strtotime("monday this week"))." 08:00";
$this_monday = date("Y-m-d",strtotime("monday this week"))." 08:30";
$this_thursday = date('Y-m-d', strtotime( 'thursday this week' ) )." 13:00";
$flash_cycle_start_this = date('Y-m-d', strtotime( 'friday this week' ) )." 08:30";
$flash_cycle_end_this = date('Y-m-d', strtotime( 'friday this week' ) )." 10:30";
$this_saturday = date('Y-m-d', strtotime( 'saturday this week' ) )." 10:30";
if ($today_date_time < $monday_auction_by) {
	$monday = $this_monday;
	$thursday = $this_thursday;
	$flash_cycle_start = $flash_cycle_start_this;
	$flash_cycle_end = $flash_cycle_end_this;
	$auction_expired_date_time = $this_saturday;
}
//echo  $flash_cycle_start_this."==".$flash_cycle_end;
$seller_address = getDentistAddress();

$feat_image_id_periapical      = $_POST['feat_image_id_periapical'];
$feat_image_id_periapical_2      = $_POST['feat_image_id_periapical_2'];
$feat_image_id_panorex      = $_POST['feat_image_id_panorex'];
$feat_image_id_bitewing      = $_POST['feat_image_id_bitewing'];
$feat_image_id_full_mouth      = $_POST['feat_image_id_full_mouth'];
$feat_image_id_sample1      = $_POST['feat_image_id_sample1'];
$feat_image_id_sample2      = $_POST['feat_image_id_sample2'];
$feat_image_id_sample3      = $_POST['feat_image_id_sample3'];
$feat_image_id_sample4      = $_POST['feat_image_id_sample4'];

?>
<style type="text/css">
.upload_link{color:#0a7be2 !important;}
.dokan-form-group {
	margin-bottom: 15px;
	float: left;
	width: 100%;
}
.dokan-new-product-area .dokan-product-meta {
	width: 100%;
	text-align: left;
}
.dokan-auction-category .content-half-part{
	line-height:30px;
}
/*._auction_start_priceformError,._auction_maximum_travel_distanceformError{right:0 !important; left:unset !important;}*/
.photo_inner_div {float:left;width:88%;}
.opt{float:left;width:12%;text-align:center;font-weight:bold;}
.example_link{margin-left:72px;}
.sample_image_container .photo_inner_div i{display:none !important;}
.sample_image_container .photo_inner_div i.fa-times-circle{
	display:block !important;
}

/*.sample_image_container .photo_inner_div a .fa-times-circle {
	top: 10px;
	position: relative;
	right: -10px;
}*/
@media (max-width: 448px) {
	input[type=text]:focus,input[type=text]:focus-visible {
outline: none !important;
border-right:none !important;
}
	/*._auction_start_priceformError,._auction_maximum_travel_distanceformError{top:20px !important;}*/
	#scrollUp{bottom:10px !important;}
}
</style>
<link rel="stylesheet" href="<?php echo get_template_directory_uri();?>-child/jQuery-Validation-Engine/css/validationEngine.jquery.css" type="text/css"/>

</script>
<script src="<?php echo get_template_directory_uri();?>-child/jQuery-Validation-Engine/js/languages/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8">
</script>
<script src="<?php echo get_template_directory_uri();?>-child/jQuery-Validation-Engine/js/jquery.validationEngine.js" type="text/javascript" charset="utf-8">
</script>
<script>
		jQuery(document).ready(function(){
			// binds form submission and fields to the validation engine
			jQuery("#auctionForm").validationEngine({
				'custom_error_messages': {
					'#feat_image_id_panorex': {
						'min': {
							'message': "Please upload Panorex X-ray photo"
						}
					},
					'#feat_image_id_periapical': {
						'min': {
							'message': "Please upload Periapical X-ray photo"
						}
					},
					'#feat_image_id_bitewing': {
						'min': {
							'message': "Please upload Bitewing X-ray photo"
						}
					},
					'#feat_image_id_full_mouth': {
						'min': {
							'message': "Please upload Full Mouth Series X-ray photo"
						}
					}
				}
			});
		});
		
	</script>
<div class="dokan-dashboard-wrap">
    <?php
    do_action( 'dokan_dashboard_content_before' );
    do_action( 'dokan_new_auction_product_content_before' );
    ?>

    <div class="dokan-dashboard-content">
        <?php

            /**
             *  dokan_auction_content_inside_before hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_auction_content_inside_before' );
			global $title_right;
        ?>
        <header class="dokan-dashboard-header dokan-clearfix">
            <h1 class="entry-title">
                <?php _e( 'Offerings', 'dokan-auction' ); ?><?php echo $title_right;?>
            </h1>
        </header><!-- .entry-header -->
		<h3>
        	Select 1 Service, upload the required x-ray(s) & photos then enter your Ask Fee & Maximum Travel Distance.
        </h3>
        <div class="dokan-new-product-area">
            <?php if ( Dokan_Template_Auction::$errors ) { ?>
                <div class="dokan-alert dokan-alert-danger">
                    <a class="dokan-close" data-dismiss="alert">&nbsp;</a>
                    <?php foreach ( Dokan_Template_Auction::$errors as $error) { ?>
                        <strong><?php _e( 'Error!', 'dokan-auction' ); ?></strong> <?php echo $error ?>.<br>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php

            $can_sell = apply_filters( 'dokan_can_post', true );

            if ( $can_sell ) {

                if ( dokan_is_seller_enabled( get_current_user_id() ) ) { ?>

                    <form id="auctionForm" class="dokan-form-container dokan-auction-product-form" method="post" enctype="multipart/form-data">
                    	<input type="hidden" name="_auction_expired_date_time" id="_auction_expired_date_time" value="<?php echo $auction_expired_date_time;?>" />
						<input type="hidden" name="_flash_cycle_start" id="_flash_cycle_start" value="<?php echo $flash_cycle_start;?>" />
						<input type="hidden" name="_flash_cycle_end" id="_flash_cycle_end" value="<?php echo $flash_cycle_end;?>" />
                        <div class="product-edit-container dokan-clearfix">
                        	
							<style type="text/css">
							.product_cat{ text-transform:capitalize;}
							</style>
                            <div class="content-half-part dokan-product-meta">
                            <div class="dokan-form-group dokan-auction-category">
                            <div class="content-half-part half-part-1">
                            <?php 
							/*$args = array(  
													'hide_empty'               => 0, 
													'parent'						=> 0,   
													'exclude'                  =>array(15,106,118),
													'taxonomy'				=>'product_cat',
													'orderby' =>				'term_id',
													'order'					=> 'asc'
												); 
												
												$categories = get_categories($args );*/
							$categories =  get_categories('parent=0&exclude=15&hide_empty=0&taxonomy=product_cat&orderby=term_id&order=asc');
							//print_r($categories);
								/*$categories = get_categories( array(
													'taxonomy' => 'product_cat',
    												'hide_empty' => false,
													'exclude'          => '15,46,47',
													'orderby' => 'term_id',
													'order'   => 'ASC'
												));*/
								echo '';
								$cat_array_new = array();
								$i = 0;
								list($array1, $array2) = array_chunk($categories, ceil(count($categories) / 2));
								echo '';
								foreach($array1 as $cat){
								$categories_level_2 =  get_categories('parent='.$cat->term_id.'&exclude=15&hide_empty=0&taxonomy=product_cat&orderby=term_order&order=asc');	?>
                                <ul><strong><?php echo $cat->name;?></strong>
                                <?php foreach($categories_level_2 as $cat_level_2){?>
                                	<?php if($cat_level_2->term_id != 106 && $cat_level_2->term_id != 118){?>
                                	<li><input type="radio" class="validate[required]" <?php if($cat_level_2->term_id==dokan_posted_input( 'product_cat' )){?> checked="checked"<?php }else{}?> name="product_cat" id="<?php echo $cat_level_2->name;?>"  value="<?php echo $cat_level_2->term_id;?>"/>&nbsp;<span class="cat_label"><?php echo str_replace('®', "<span class='TM_offer'>&reg;</span>",str_replace("*","",$cat_level_2->name));?><?php if($cat_level_2->term_id==76){?>&nbsp;-&nbsp;<span style="font-style:italic">abutments & denture only</span><?php }?><?php if($cat_level_2->term_id==77){?>&nbsp;-&nbsp;<span style="font-style:italic">abutments & dentures only</span><?php }?><?php if($cat_level_2->term_id==119){?>&nbsp;-&nbsp;<span style="font-style:italic">locators & retrofit service only</span><?php }?></span></li>
                                 <?php 
								 	array_push($cat_array_new,$cat_level_2->name);
									}
								 }?>
                                </ul>
                            <?php }?>
                            </div>
                            <div class="content-half-part half-part-2">
                            <?php echo '';
								foreach($array2 as $cat){
								$categories_level_2 =  get_categories('parent='.$cat->term_id.'&exclude=15&hide_empty=0&taxonomy=product_cat&orderby=term_order&order=asc');	?>
                                <ul><strong><?php echo $cat->name;?></strong>
                                <?php foreach($categories_level_2 as $cat_level_2){?>
                                <?php if($cat_level_2->term_id != 106 && $cat_level_2->term_id != 118){?>
                                	<li><input type="radio" class="validate[required]" <?php if($cat_level_2->term_id==dokan_posted_input( 'product_cat' )){?> checked="checked"<?php }else{}?> name="product_cat" id="<?php echo $cat_level_2->name;?>"  value="<?php echo $cat_level_2->term_id;?>"/>&nbsp;<span class="cat_label"><?php echo str_replace('®', "<span class='TM_offer'>&reg;</span>",str_replace("*","",$cat_level_2->name));?><?php if($cat_level_2->term_id==76){?>&nbsp;-&nbsp;<span style="font-style:italic">abutments & denture only</span><?php }?><?php if($cat_level_2->term_id==77){?>&nbsp;-&nbsp;<span style="font-style:italic">abutments & dentures only</span><?php }?><?php if($cat_level_2->term_id==119){?>&nbsp;-&nbsp;<span style="font-style:italic">locators & retrofit service only</span><?php }?></span></li>
                                 <?php 
								 	 array_push($cat_array_new,$cat_level_2->name);
								}
								 }?>
                                </ul>
                            <?php } ?>
                            
                            <ul class='custom_service'><li>Six Month Smiles<span class='TM_offer_2'>®</span>, Invisalign<span class='TM_offer_2'>®</span>, & NTI-TSS<span class='TM_offer_2'>®</span> are not registered trademarks of ShopADoc, Inc.</li></ul>
                            </div>
                            </div>
                            <?php if ( dokan_get_option( 'product_category_style', 'dokan_selling', 'single' ) == 'single' && 1==2): ?>
                                    <div class="dokan-form-group dokan-auction-category">

                                        <?php
                                        $category_args =  array(
                                            'show_option_none' => __( '- Select a category -', 'dokan-auction' ),
                                            'hierarchical'     => 1,
                                            'hide_empty'       => 0,
                                            'name'             => 'product_cat',
                                            'id'               => 'product_cat',
                                            'taxonomy'         => 'product_cat',
                                            'title_li'         => '',
                                            'class'            => 'product_cat dokan-form-control dokan-select2',
                                            'exclude'          => '15',
                                            'selected'         => dokan_posted_input( 'product_cat' ),
                                        );

                                        wp_dropdown_categories( apply_filters( 'dokan_product_cat_dropdown_args', $category_args ) );
                                        ?>
                                    </div>
                                <?php elseif ( dokan_get_option( 'product_category_style', 'dokan_selling', 'single' ) == 'multiple' && 1==2): ?>
                                    <div class="dokan-form-group dokan-auction-category">
                                        <?php
                                        $term = array();
                                        include_once DOKAN_LIB_DIR.'/class.taxonomy-walker.php';
                                        $drop_down_category = wp_dropdown_categories( array(
                                            'show_option_none' => __( '', 'dokan-auction' ),
                                            'hierarchical'     => 1,
                                            'hide_empty'       => 0,
                                            'name'             => 'product_cat[]',
                                            'id'               => 'product_cat',
                                            'taxonomy'         => 'product_cat',
                                            'title_li'         => '',
                                            'class'            => 'product_cat dokan-form-control dokan-select2',
                                            'exclude'          => '',
                                            'selected'         => $term,
                                            'echo'             => 0,
                                            'walker'           => new DokanTaxonomyWalker()
                                        ) );

                                        echo str_replace( '<select', '<select data-placeholder="'.__( 'Select product category', 'dokan-auction' ).'" multiple="multiple" ', $drop_down_category );
                                        ?>
                                    </div>
                                <?php endif; ?>
                                <div class="dokan-form-group dokan-auction-post-title hide">
                                    <input class="dokan-form-control" name="post_title" id="post-title" type="text" placeholder="<?php esc_attr_e( 'Product name..', 'dokan-auction' ); ?>" value="<?php echo dokan_posted_input( 'post_title' ); ?>">
                                </div>
                                <div class="dokan-form-group" style="display:none;color:red;font-size:16px;" id="photo_note">
                                    <span><strong>Note:</strong>&nbsp;X-rays must be no older than 30 days and must not include any personal information or identification. Please use the edit function on your device to comply with this requirement.</span>
                                </div>
                                <div class="photo_div dokan-form-group dokan-auction-periapical" style="display:none;" id="photo1">
                                         <!---------------------------->
                                        <div class="photo_inner_div featured-image_periapical">
                                            <div class="upload_section dokan-feat-image-upload_periapical">
                                                <div class="instruction-inside">
                                                    <input  id="feat_image_id_periapical" type="hidden" name="feat_image_id_periapical" class="dokan-feat-image-id_periapical" value="<?php if($feat_image_id_periapical > 0){ echo $feat_image_id_periapical;}else{ echo '0';}?>">
                                                    <i class="fa fa-cloud-upload"></i>
                                                    <a href="#" class="dokan-feat-image-btn_periapical dokan-btn"><?php _e( '<span class="upload_link">Upload</span> periapical X-ray', 'dokan-auction' ); ?></a><br /><a class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/periapical-l.jpg');?>"><span class="wp-caption-text hide">Periapical Film (pa)</span>See example</a>
                                                   
                                                </div>
        
                                                <div class="image-wrap <?php if($feat_image_id_periapical > 0){ echo '';}else{ echo 'dokan-hide';}?>"> <a class="remove dokan-remove-feat-image_periapical">&nbsp;</a> <img src="<?php if($feat_image_id_periapical > 0){ echo wp_get_attachment_url($feat_image_id_periapical);}else{ echo '';}?>" alt=""> </div>
                                            </div>
                                        </div>
                                        <!---------------------------->
                                        <div class="opt" id="opt_1"></div>
                                    </div>
                                    <div class="photo_div dokan-form-group dokan-auction-panorex" style="display:none;" id="photo2">
                                    <div class="opt" id="either" style="display:none;">EITHER</div>
                                         <!---------------------------->
                                        <div class="photo_inner_div featured-image_panorex">
                                            <div class="upload_section dokan-feat-image-upload_panorex">
                                                <div class="instruction-inside">
                                                    <input id="feat_image_id_panorex" type="hidden" name="feat_image_id_panorex" class="dokan-feat-image-id_panorex" value="<?php if($feat_image_id_panorex > 0){ echo $feat_image_id_panorex;}else{ echo '0';}?>">
                                                    <i class="fa fa-cloud-upload"></i>
                                                    <a href="#" class="dokan-feat-image-btn_panorex dokan-btn"><?php _e( '<span class="upload_link">Upload</span> panorex X-ray', 'dokan-auction' ); ?></a><br /><a class="example_link" href="#" caption="abc" data-featherlight="<?php echo home_url('/wp-content/uploads/panorex-l.jpg');?>"><span class="wp-caption-text hide">Panorex Film</span>See example</a>
                                                </div>
        
                                                <div class="image-wrap <?php if($feat_image_id_panorex > 0){ echo '';}else{ echo 'dokan-hide';}?>"> <a class="remove dokan-remove-feat-image_panorex">&nbsp;</a> <img src="<?php if($feat_image_id_panorex > 0){ echo wp_get_attachment_url($feat_image_id_panorex);}else{ echo '';}?>" alt=""> </div>
                                            </div>
                                        </div>
                                        <!---------------------------->
                                        <div class="opt" id="opt_2"></div>
                                    </div>
                                    <div class="photo_div dokan-form-group dokan-auction-bitewing" style="display:none;" id="photo3">
                                         <!---------------------------->
                                        <div class="photo_inner_div featured-image_bitewing">
                                            <div class="upload_section dokan-feat-image-upload_bitewing">
                                                <div class="instruction-inside">
                                                    <input id="feat_image_id_bitewing" type="hidden" name="feat_image_id_bitewing" class="dokan-feat-image-id_bitewing" value="<?php if($feat_image_id_bitewing > 0){ echo $feat_image_id_bitewing;}else{ echo '0';}?>">
                                                    <i class="fa fa-cloud-upload"></i>
                                                    <a href="#" class="dokan-feat-image-btn_bitewing dokan-btn"><?php _e( '<span class="upload_link">Upload</span> bitewing X-rays', 'dokan-auction' ); ?></a><br /><a class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/bitewing-l.jpg');?>"><span class="wp-caption-text hide">Bitewing Films (bws)</span>See example</a>
                                                </div>
        
                                                <div class="image-wrap <?php if($feat_image_id_bitewing > 0){ echo '';}else{ echo 'dokan-hide';}?>"> <a class="remove dokan-remove-feat-image_bitewing">&nbsp;</a> <img src="<?php if($feat_image_id_bitewing > 0){ echo wp_get_attachment_url($feat_image_id_bitewing);}else{ echo '';}?>" alt=""> </div>
                                            </div>
                                        </div>
                                        <!---------------------------->
                                        <div class="opt" id="opt_3"></div>
                                    </div>
                                    <div class="photo_div dokan-form-group dokan-auction-full_mouth" style="display:none;" id="photo4">
                                    <div class="opt" id="or" style="display:none;">OR</div>
                                         <!---------------------------->
                                        <div class="photo_inner_div featured-image_full_mouth">
                                            <div class="upload_section dokan-feat-image-upload_full_mouth">
                                                <div class="instruction-inside">
                                                   <input id="feat_image_id_full_mouth" type="hidden" name="feat_image_id_full_mouth" class="dokan-feat-image-id_full_mouth" value="<?php if($feat_image_id_full_mouth > 0){ echo $feat_image_id_full_mouth;}else{ echo '0';}?>">
                                                    <i class="fa fa-cloud-upload"></i>
                                                    <a href="#" class="dokan-feat-image-btn_full_mouth dokan-btn"><?php _e( '<span class="upload_link">Upload</span> full mouth series X-rays', 'dokan-auction' ); ?></a><br /><a  class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/full-mouth-series-l.jpg');?>"><span class="wp-caption-text hide">Full Mouth Series (fmx)</span>See example</a>
                                                </div>
        
                                                <div class="image-wrap <?php if($feat_image_id_full_mouth > 0){ echo '';}else{ echo 'dokan-hide';}?>"> <a class="remove dokan-remove-feat-image_full_mouth">&nbsp;</a> <img src="<?php if($feat_image_id_full_mouth > 0){ echo wp_get_attachment_url($feat_image_id_full_mouth);}else{ echo '';}?>" alt=""> </div>
                                            </div>
                                        </div>
                                        <div class="opt" id="opt_4"></div>
                                        <!---------------------------->
                                    </div>
                                    <div class="sample_image_container">
                                    <div class="photo_div dokan-form-group sample_photo_div dokan-auction-sample1" style="display:none;" id="photo5">
                                    <div class="opt" id="and" style="display:none;">AND</div>
                                         <!---------------------------->
                                        <div class="photo_inner_div featured-image_sample1">
                                            <div class="upload_section dokan-feat-image-upload_sample1">
                                                <div class="instruction-inside">
                                                   <input id="feat_image_id_sample1" type="hidden" name="feat_image_id_sample1" class="dokan-feat-image-id_sample1" value="<?php if($feat_image_id_sample1 > 0){ echo $feat_image_id_sample1;}else{ echo '0';}?>">
                                                    <i class="fa fa-cloud-upload"></i>
                                                    <a href="#" class="dokan-feat-image-btn_sample1 dokan-btn"><?php _e( '<span class="upload_link">Upload</span> Full Frontal Smile photo', 'dokan-auction' ); ?></a><br /><a  class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/1-Wide-Smile-new.jpg');?>"><span class="wp-caption-text hide">Full Frontal Smile</span>See example</a>
                                                </div>
        
                                                <div class="image-wrap <?php if($feat_image_id_sample1 > 0){ echo '';}else{ echo 'dokan-hide';}?>"> <a class="remove dokan-remove-feat-image_sample1">&nbsp;</a> <img src="<?php if($feat_image_id_sample1 > 0){ echo wp_get_attachment_url($feat_image_id_sample1);}else{ echo '';}?>" alt=""> </div>
                                            </div>
                                        </div>
                                        <div class="opt" id="opt_5"></div>
                                        <!---------------------------->
                                    </div>
                                    <div class="photo_div dokan-form-group sample_photo_div dokan-auction-sample1" style="display:none;" id="photo5_PA">
                                    <div class="opt" id="and" >AND</div>
                                         <!---------------------------->
                                        <div class="photo_inner_div featured-image_periapical_2">
                                            <div class="upload_section dokan-feat-image-upload_periapical_2">
                                                <div class="instruction-inside">
                                                    <input  id="feat_image_id_periapical_2" type="hidden" name="feat_image_id_periapical_2" class="dokan-feat-image-id_periapical_2" value="<?php if($feat_image_id_periapical_2 > 0){ echo $feat_image_id_periapical_2;}else{ echo '0';}?>">
                                                    <i class="fa fa-cloud-upload"></i>
                                                    <a href="#" class="dokan-feat-image-btn_periapical_2 dokan-btn"><?php _e( '<span class="upload_link">Upload</span> periapical X-ray', 'dokan-auction' ); ?></a><br /><a class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/periapical-l.jpg');?>"><span class="wp-caption-text hide">Periapical Film (pa)</span>See example</a>
                                                   
                                                </div>
        
                                                <div class="image-wrap <?php if($feat_image_id_periapical_2 > 0){ echo '';}else{ echo 'dokan-hide';}?>"> <a class="remove dokan-remove-feat-image_periapical_2">&nbsp;</a> <img src="<?php if($feat_image_id_periapical_2 > 0){ echo wp_get_attachment_url($feat_image_id_periapical_2);}else{ echo '';}?>" alt=""> </div>
                                            </div>
                                        </div>
                                        <!---------------------------->
                                    </div>
                                    <div class="photo_div dokan-form-group sample_photo_div dokan-auction-sample4" style="display:none;" id="photo8">
                                         <!---------------------------->
                                        <div class="photo_inner_div featured-image_sample4">
                                            <div class="upload_section dokan-feat-image-upload_sample4">
                                                <div class="instruction-inside">
                                                   <input id="feat_image_id_sample4" type="hidden" name="feat_image_id_sample4" class="dokan-feat-image-id_sample4" value="<?php if($feat_image_id_sample4 > 0){ echo $feat_image_id_sample4;}else{ echo '0';}?>">
                                                    <i class="fa fa-cloud-upload"></i>
                                                    <a href="#" class="dokan-feat-image-btn_sample4 dokan-btn"><?php _e( '<span class="upload_link">Upload</span> Profile photo', 'dokan-auction' ); ?></a><br /><a  class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/4-Profile-new.jpg');?>"><span class="wp-caption-text hide">Profile</span>See example</a>
                                                </div>
        
                                                <div class="image-wrap <?php if($feat_image_id_sample4 > 0){ echo '';}else{ echo 'dokan-hide';}?>"> <a class="remove dokan-remove-feat-image_sample4">&nbsp;</a> <img src="<?php if($feat_image_id_sample4 > 0){ echo wp_get_attachment_url($feat_image_id_sample4);}else{ echo '';}?>" alt=""> </div>
                                            </div>
                                        </div>
                                        <div class="opt" id="opt_8"></div>
                                        <!---------------------------->
                                    </div>
                                    <div class="photo_div dokan-form-group sample_photo_div dokan-auction-sample2" style="display:none;" id="photo6">
                                         <!---------------------------->
                                        <div class="photo_inner_div featured-image_sample2">
                                            <div class="upload_section dokan-feat-image-upload_sample2">
                                                <div class="instruction-inside">
                                                   <input id="feat_image_id_sample2" type="hidden" name="feat_image_id_sample2" class="dokan-feat-image-id_sample2" value="<?php if($feat_image_id_sample2 > 0){ echo $feat_image_id_sample2;}else{ echo '0';}?>">
                                                    <i class="fa fa-cloud-upload"></i>
                                                    <a href="#" class="dokan-feat-image-btn_sample2 dokan-btn"><?php _e( '<span class="upload_link">Upload</span> Maxillary Top Arch photo', 'dokan-auction' ); ?></a><br /><a  class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/2-Upper-Teeth-new.jpg');?>"><span class="wp-caption-text hide">Maxillary Top Arch</span>See example</a>
                                                </div>
        
                                                <div class="image-wrap <?php if($feat_image_id_sample2 > 0){ echo '';}else{ echo 'dokan-hide';}?>"> <a class="remove dokan-remove-feat-image_sample2">&nbsp;</a> <img src="<?php if($feat_image_id_sample2 > 0){ echo wp_get_attachment_url($feat_image_id_sample2);}else{ echo '';}?>" alt=""> </div>
                                            </div>
                                        </div>
                                        <div class="opt" id="opt_6"></div>
                                        <!---------------------------->
                                    </div>
                                    <div class="photo_div dokan-form-group sample_photo_div dokan-auction-sample3" style="display:none;" id="photo7">
                                         <!---------------------------->
                                        <div class="photo_inner_div featured-image_sample3">
                                            <div class="upload_section dokan-feat-image-upload_sample3">
                                                <div class="instruction-inside">
                                                   <input id="feat_image_id_sample3" type="hidden" name="feat_image_id_sample3" class="dokan-feat-image-id_sample3" value="<?php if($feat_image_id_sample3 > 0){ echo $feat_image_id_sample3;}else{ echo '0';}?>">
                                                    <i class="fa fa-cloud-upload"></i>
                                                    <a href="#" class="dokan-feat-image-btn_sample3 dokan-btn"><?php _e( '<span class="upload_link">Upload</span> Mandibular Bottom Arch photo', 'dokan-auction' ); ?></a><br /><a  class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/3-Lower-Teeth-new.jpg');?>"><span class="wp-caption-text hide">Mandibular Bottom Arch</span>See example</a>
                                                </div>
        
                                                <div class="image-wrap <?php if($feat_image_id_sample3 > 0){ echo '';}else{ echo 'dokan-hide';}?>"> <a class="remove dokan-remove-feat-image_sample3">&nbsp;</a> <img src="<?php if($feat_image_id_sample3 > 0){ echo wp_get_attachment_url($feat_image_id_sample3);}else{ echo '';}?>" alt=""> </div>
                                            </div>
                                        </div>
                                        <div class="opt" id="opt_7"></div>
                                        <!---------------------------->
                                    </div>
                                    
                                    </div>
                                    <style type="text/css">
										.sample_image_container{
											float:left;
											clear:both;
											width:100%;
										}
										.sample_photo_div{
											float:left;
											width:25%;
										}
										.sample_photo_div .photo_inner_div{
											float:left;
											width:97%;
										}
										/*.sample_photo_div .photo {
											float: left;
											width: 24%;
											margin-right: 1%;
										}
										.sample_photo_div .photo img{
											height:250px;
											float:left;
										}*/
									</style>
                                    <div class="sample_photo_div dokan-form-group" style="display:none;" id="sample_photo_div">
                                         <!---------------------------->
                                         <!--
                                        <div class="photo photo_1">
                                            <img src="<?php echo home_url('/wp-content/uploads/1-Wide-Smile-new.jpg');?>" title="" alt="" />
                                        </div>
                                        <div class="photo photo_2">
                                            <img src="<?php echo home_url('/wp-content/uploads/2-Upper-Teeth-new.jpg');?>" title="" alt="" />
                                        </div>
                                        <div class="photo photo_3">
                                            <img src="<?php echo home_url('/wp-content/uploads/3-Lower-Teeth-new.jpg');?>" title="" alt="" />
                                        </div>
                                        <div class="photo photo_4">
                                            <img src="<?php echo home_url('/wp-content/uploads/4-Profile-new.jpg');?>" title="" alt="" />
                                        </div>
                                        -->
                                        <!---------------------------->
                                    </div>
                                    <div class="sample_photo_div dokan-form-group" style="display:none;" id="sample_photo_div_2">
                                         <!---------------------------->
                                         <!--
                                        <div class="photo photo_1">
                                            <img src="<?php echo home_url('/wp-content/uploads/1-Wide-Smile-new.jpg');?>" title="" alt="" />
                                        </div>
                                        -->
                                        <!---------------------------->
                                    </div>
								<script>
                                    ;(function($){
                                        function showPhotos(val){
												/*if(val != '39' && val != '65' && val != '66'){
                                                	$("#photo_note").show();
												}else{
													$("#photo_note").hide();
												}*/
                                                //var val = $("#product_cat option:selected").val();
												$("#photo5_PA").hide();
                                                var myArray1 = ['22','87','23','24','25','26','27','20','21','45','96'];
                                                if (myArray1.indexOf(val) === -1) {
                                                    $("#photo1").hide();
                                                }else{
                                                    $("#photo1").show();
                                                }
                                                var myArray2 = ['18','19','28','29','120','121','30','31','32','33','44','76','77','78','79','80','81','82','98','99','117','83','84','85','86','89','92','93','94','119','40','39','96','97','66','88','106','34','35','36','37','38','90','75','41','42','43','45','46','47','48','49','50','51','52','53','54'];
                                                if (myArray2.indexOf(val) === -1) {
                                                    $("#photo2").hide();
                                                }else{
                                                    $("#photo2").show();
                                                }
                                                var myArray3 = ['18','19'];
                                                if (myArray3.indexOf(val) === -1) {
                                                    $("#photo3").hide();
                                                }else{
                                                    $("#photo3").show();
                                                }
                                                var myArray2 = ['18','19','28','29','120','121','30','31','32','36','37','38','90','75','46','47','54'];
                                                if (myArray2.indexOf(val) === -1) {
                                                    $("#photo4").hide();
                                                }else{
                                                    $("#photo4").show();
                                                }
												var myArraySample = ['28','84','29','120','121','30','31','32','41','42','43','54'];
												var myArraySample2 = ['28','84','29','120','121','30','31','32','54'];
                                                if (myArraySample.indexOf(val) === -1) {
                                                    $("#photo5").hide();
                                                    $("#photo6").hide();
                                                    $("#photo7").hide();
                                                    $("#photo8").hide();
                                                }else{
													if(val=='54'){
														$("#photo5").hide();
														$("#photo5_PA").show();
														$("#photo5_PA .photo_inner_div").css('width',"76%");
													}else{
                                                    	$("#photo5").show();
														$("#photo5_PA").hide();
													}
													if(val=='41' || val=='42' || val=='43'){
														$("#photo6").show();
														$("#photo7").show();
														$("#photo8").show();
													}else{															
														$("#photo6").hide();
														$("#photo7").hide();
														$("#photo8").hide();
													}
                                                }
												
                                            }
                                        function showOpt(val){
											if(val=='84'){
													$(".photo_div").css('width',"50%");
													$("#opt_2").text('AND');
													$(".sample_image_container").css('clear',"none");
													$(".sample_image_container").css('float',"none");
													$("#photo5").removeClass("sample_photo_div");
												}else{
													$(".photo_div").css('width',"50%");
													//$("#opt_2").text('');
													$(".sample_image_container").css('clear',"both");
													$(".sample_image_container").css('float',"left");
													$("#photo5").addClass("sample_photo_div");
													$(".sample_photo_div").css('width',"25%");
												}
												if(val=='28' || val=='29' || val=='120' || val=='121' || val=='30' || val=='31' || val=='32' || val=='54'){
													$("#either").css('display',"block");
													$("#and").css('display',"block");
													$("#photo2 .photo_inner_div,#photo5 .photo_inner_div").css('width',"76%");
													
												}else{
													$("#either").css('display',"none");
													$("#and").css('display',"none");
													$("#photo2 .photo_inner_div,#photo5 .photo_inner_div").css('width',"88%");
												}
												if(val=='18' || val=='19'){
													$("#or").css('display',"block");
													
												}else{
													$("#or").css('display',"none");
												}
											   if(val=='41' || val=='42'|| val=='43'){
												   $("#sample_photo_div").show();
												   $("#sample_photo_div_2").hide();
											   }else if(val=='28' || val=='84' ||  val=='29' || val=='120' || val=='121' || val=='30' || val=='31' || val=='32' || val=='54'){
												   $("#sample_photo_div").hide();
												   $("#sample_photo_div_2").show();
											   }else{
												   $("#sample_photo_div").hide();
												   $("#sample_photo_div_2").hide();
											   }
                                               if(val=='18' || val=='19'){
												   $("#opt_2").html('AND');
												   //$("#opt_3").html('OR');
											   }else if(val == '45'){
												   $("#opt_1").html('AND');
												   $("#opt_2").html('');
												   $("#opt_3").html('');
											   }else if(val == '96'){
												   $("#opt_1").html('OR');
												   $("#opt_2").html('');
												   $("#opt_3").html('');
											   }else{
												   if(val !='22' && val !='87' && val !='20' && val !='21' && val !='23' && val !='24' && val !='25' && val !='26' && val !='27' &&  val !='76' && val !='77' && val !='78' && val !='79' && val !='80' && val !='84' && val !='81' && val !='82' && val !='98' && val !='99' && val !='83'  && val !='44' && val !='117' && val !='34' && val !='35' && val !='106'){
													   $("#opt_1").html('OR');
													   $("#opt_2").html('OR');
													   $("#opt_3").html('OR');
												   }else{
													   $("#opt_1").html('');
													   $("#opt_2").html('');
													   $("#opt_3").html('');
												   }
											   }
                                               if(val=='18' || val=='19'){
												   $(".photo_div").css('width',"50%");
											   }else if(val =='22' || val =='87' || val =='23' || val =='24' || val =='25' || val =='26' || val =='20' || val =='21' || val =='27'  || val =='33' || val =='76' || val =='77' || val =='78' || val =='79' || val =='80' || val =='81' || val =='82' || val =='98' || val =='99' || val =='83' || val =='85' || val =='86' || val =='89' || val =='92' || val =='93' || val =='94' || val =='119' || val =='40' || val =='39'  || val =='97' || val =='66' || val =='88' || val =='44' || val =='117' || val =='106' || val =='34'|| val =='35' || val =='42' || val=='43' || val=='48' || val=='49'|| val=='50'|| val=='51'|| val=='52'|| val=='53'){
												    $("#opt_1").html('');
													   $("#opt_2").html('');
													   $("#opt_3").html('');
												    $(".photo_div").css('width',"100%");
												   }else{
													   $(".photo_div").css('width',"50%");
												   }
												   if(val=='84'){
													$("#opt_2").text('AND');
												}
												if(val == '41' || val == '42' || val == '43'){
													$("#photo2").css("width","100%");
													$("#opt_2").html('');
													$(".sample_photo_div").css('width',"25%");
												}
												/*if(val=='39' || val=='66' || val=='88'){
													$("#photo_note").hide();
												}else{
													$("#photo_note").show();
												}*/
												
                                            }
                                        <?php if(dokan_posted_input( 'product_cat' )){?>
                                                showPhotos('<?php echo dokan_posted_input( 'product_cat' );?>');
												showOpt('<?php echo dokan_posted_input( 'product_cat' );?>');
                                            <?php }?>	
                                        $(document).ready(function(){
                                            $( "#product_cat" ).change(function() {
                                                showPhotos();
												showOpt();
                                            });
                                            
                                            jQuery(document).on('change', "input[name='product_cat']", function() {
                                                if(this.checked) {
                                                    var val = jQuery(this).val(); // retrieve the value
                                                    showPhotos(val);
													showOpt(val);
                                                }
                                            });	
                                        });
                                    })(jQuery)
                                
                                </script>

                                <div class="dokan-form-group dokan-auction-start-price">
                                        <label class="dokan-control-label _auction_start_price_label" for="_auction_start_price"><?php _e( 'Ask Fee', 'dokan-auction' ); ?>&nbsp;<span class="tooltip_New"><span class="tooltips" title='Enter the maximum amount you are willing to pay for the service selected.<br /><br />You will have the ability to increase your "Ask Fee" on the auction screen.'>&nbsp;</span></span></label>
                                        <div class="dokan-form-group">
                                            <div class="dokan-input-group">
                                                <span class="dokan-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                                <input class="dokan-form-control validate[required] min[0]"  name="_auction_start_price" id="_auction_start_price" type="text" placeholder="" value="<?php if(dokan_posted_input( '_auction_start_price' )){ echo dokan_posted_input( '_auction_start_price' );}else{ echo '';}?>" step="any" min="0"  data-prompt-position="topLeft:50,0" autocomplete="off"/>
                                            </div>
                                        </div>
                                    </div>
                                    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.2.6/jquery.inputmask.bundle.min.js"></script>
									<script type="text/javascript">
                                    jQuery(document).ready(function() {
                                     /* jQuery("#_auction_start_price").inputmask({
                                        //mask: "99999[.99]",
                                        //greedy: true,
                                        definitions: {
                                          '*': {
                                            validator: "[0-9]"
                                          }
                                        },
                                       leftAlign: true
                                      });*/
                                      jQuery("#_auction_start_price").inputmask('currency', {
                                         alias:"numeric",
                                            prefix: '',
                                            integerDigits:5,
                                            digits:0,
                                            allowMinus:false,        
                                            digitsOptional: false,
                                            placeholder: "",
                                            rightAlign: false
                                      });
                                        jQuery('#_auction_maximum_travel_distance').keypress(function(e){ 
                                           if (this.value.length == 0 && e.which == 48){
                                              return false;
                                           }else{
                                               if(e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)){
                                                   return false;
                                                }else{
                                                    if(this.value.length > 1){
                                                        return false;
                                                    }
                                                }
                                          }
                                        });
                                       /* jQuery('#_auction_start_price').keypress(function(e){ 
                                            if (this.value.length == 1 && e.which == 48){
                                              this.value ='';
                                              return false;
                                           }
                                        });
										jQuery('#_auction_start_price').change(function(e) {
										 		if (this.value.length == 1 && this.value == 0){
												  this.value ='';
												  return false;
											   }
										});*/
                                    });
                                    </script>
                                 <div class="dokan-form-group dokan-auction-start-price">
                                        <label class="dokan-control-label _auction_maximum_travel_distance_label" for="_auction_maximum_travel_distance"><?php _e( 'Maximum Travel Distance', 'dokan-auction' ); ?>&nbsp;<span class="tooltip_New"><span class="tooltips" title='Enter the maximum miles you are willing to travel to a dental office for service.<br /><br />1 Mile = 20 City Blocks = 20 minutes walk time. You will have the ability to increase your "Maximum Travel Distance" on the auction screen.'>&nbsp;</span></span></label>
                                        <div class="dokan-form-group">
                                            <div class="dokan-input-group">
                                                <span class="dokan-input-group-addon"><?php echo 'Miles'; ?></span>
                                                <input class="dokan-form-control validate[required] min[1] max[35]"  name="_auction_maximum_travel_distance" id="_auction_maximum_travel_distance" type="text" placeholder="" value="<?php if(dokan_posted_input( '_auction_maximum_travel_distance' )){ echo dokan_posted_input( '_auction_maximum_travel_distance' );}else{ echo '';}?>" step="any" min="1" max="35" data-prompt-position="topLeft:130,0" autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>   
                                    
								<div class="dokan-form-group dokan-auction-plan hide"  id="plan">
                                         <!---------------------------->
                                        <div class="featured-image_plan">
                                            <div class="upload_section dokan-feat-image-upload_plan">
                                                <div class="instruction-inside">
                                                    <input type="hidden" name="feat_image_id_plan" class="dokan-feat-image-id_plan" value="0">
                                                    <i class="fa fa-cloud-upload"></i>
                                                    <a href="#" class="dokan-feat-image-btn_plan dokan-btn"><?php _e( '<span class="upload_link">Upload</span> your treatment plan', 'dokan-auction' ); ?></a>
                                                </div>
        
                                                <div class="image-wrap dokan-hide">
                                                    <a class="remove dokan-remove-feat-image_plan">&nbsp;</a>
                                                        <img src="" alt="">
                                                        <a id="document_link" href="#" target="_blank"><img src="" alt=""></a>
                                                </div>
                                            </div>
                                        </div>
                                        <!---------------------------->
                                    </div>
                                <div class="dokan-form-group dokan-auction-post-excerpt hide">
                                    <textarea name="post_excerpt" id="post-excerpt" rows="5" class="dokan-form-control" placeholder="<?php esc_attr_e( 'Short description about the product...', 'dokan-auction' ); ?>"><?php echo dokan_posted_textarea( 'post_excerpt' ); ?></textarea>
                                </div>
                                <div class="dokan-form-group dokan-auction-tags hide">
                                    <?php
                                    require_once DOKAN_LIB_DIR.'/class.taxonomy-walker.php';
                                    $drop_down_tags = wp_dropdown_categories( array(
                                        'show_option_none' => __( '', 'dokan-auction' ),
                                        'hierarchical'     => 1,
                                        'hide_empty'       => 0,
                                        'name'             => 'product_tag[]',
                                        'id'               => 'product_tag',
                                        'taxonomy'         => 'product_tag',
                                        'title_li'         => '',
                                        'class'            => 'product_tags dokan-form-control dokan-select2',
                                        'exclude'          => '',
                                        'selected'         => array(),
                                        'echo'             => 0,
                                        'walker'           => new DokanTaxonomyWalker()
                                    ) );

                                    echo str_replace( '<select', '<select data-placeholder="'.__( 'Select product tags', 'dokan-auction' ).'" multiple="multiple" ', $drop_down_tags );
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="product-edit-new-container hide">
                            <div class="dokan-edit-row dokan-auction-general-sections dokan-clearfix">

                                <div class="dokan-section-heading" data-togglehandler="dokan_product_inventory">
                                    <h2><i class="fa fa-cubes" aria-hidden="true"></i> <?php _e( 'General Options', 'dokan-auction' ) ?></h2>
                                    <p><?php _e( 'Manage your auction product data', 'dokan-auction' ); ?></p>
                                    <div class="dokan-clearfix"></div>
                                </div>

                                <div class="dokan-section-content">
                                    <div class="content-half-part dokan-auction-item-condition hide">
                                        <label class="dokan-control-label" for="_auction_item_condition"><?php _e( 'Item condition', 'dokan-auction' ); ?></label>
                                        <div class="dokan-form-group">
                                            <select name="_auction_item_condition" class="dokan-form-control" id="_auction_item_condition">
                                                <option value="new" selected="selected"><?php _e( 'New', 'dokan-auction' ) ?></option>
                                                <option value="used"><?php _e( 'Used', 'dokan-auction' ) ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="content-half-part dokan-auction-type hide">
                                        <label class="dokan-control-label" for="_auction_type"><?php _e( 'Auction type', 'dokan-auction' ); ?></label>
                                        <div class="dokan-form-group">
                                            <select name="_auction_type" class="dokan-form-control" id="_auction_type">
                                                <option value="normal"><?php _e( 'Normal', 'dokan-auction' ) ?></option>
                                                <option value="reverse" selected="selected"><?php _e( 'Reverse', 'dokan-auction' ) ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="dokan-clearfix"></div>

                                    <div class="dokan-form-group dokan-auction-proxy-bid hide">
                                        <div class="checkbox">
                                            <label for="_auction_proxy">
                                                <input type="checkbox" name="_auction_proxy" id="_auction_proxy" value="yes" checked="checked">
                                                <?php _e( 'Enable proxy bidding for this auction product', 'dokan-auction' );?>
                                            </label>
                                        </div>
                                    </div>

                                    <?php if( get_option( 'simple_auctions_sealed_on', 'no' ) == 'yes') : ?>
                                        <div class="dokan-form-group dokan-auction-sealed-bid">
                                            <div class="checkbox">
                                                <label for="_auction_sealed">
                                                    <input type="checkbox" name="_auction_sealed" value="yes" id="_auction_sealed">
                                                    <?php _e( 'Enable sealed bidding for this auction product', 'dokan-auction' );?>
                                                    <i class="fa fa-question-circle tips" data-title="<?php _e( 'In this type of auction all bidders simultaneously submit sealed bids so that no bidder knows the bid of any other participant. The highest bidder pays the price they submitted. If two bids with same value are placed for auction the one which was placed first wins the auction.', 'dokan-auction' ); ?>"></i>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    

                                    <div class="content-half-part dokan-auction-bid-increment ">
                                        <label class="dokan-control-label" for="_auction_bid_increment"><?php _e( 'Bid increment', 'dokan-auction' ); ?></label>
                                        <div class="dokan-form-group">
                                            <div class="dokan-input-group">
                                                <span class="dokan-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                               <input class="dokan-form-control" name="_auction_bid_increment" id="_auction_bid_increment" type="number" placeholder="9.99" value="" step="any" min="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="dokan-clearfix"></div>

                                    <div class="content-half-part dokan-auction-reserved-price">
                                        <label class="dokan-control-label" for="_auction_reserved_price"><?php _e( 'Reserved price', 'dokan-auction' ); ?></label>
                                        <div class="dokan-form-group">
                                            <div class="dokan-input-group">
                                                <span class="dokan-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                                <input class="dokan-form-control" name="_auction_reserved_price" id="_auction_reserved_price" type="number" placeholder="9.99" value="" step="any" min="0" style="width: 97%;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="content-half-part dokan-auction-regular-price">
                                        <label class="dokan-control-label" for="_regular_price"><?php _e( 'Buy it now price', 'dokan-auction' ); ?></label>
                                        <div class="dokan-form-group">
                                            <div class="dokan-input-group">
                                                <span class="dokan-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                                <input class="dokan-form-control" name="_regular_price" id="_regular_price" type="number" placeholder="9.99" value="" step="any" min="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="dokan-clearfix"></div>

                                    <div class="content-half-part dokan-auction-dates-from">
                                        <label class="dokan-control-label" for="_auction_dates_from"><?php _e( 'Auction Start date', 'dokan-auction' ); ?></label>
                                        <div class="dokan-form-group">
                                            <input class="dokan-form-control auction-datepicker" name="_auction_dates_from" id="_auction_dates_from" type="text" value="<?php echo $monday;?>" style="width: 97%;">
                                        </div>
                                    </div>

                                    <div class="content-half-part dokan-auction-dates-to">
                                        <label class="dokan-control-label" for="_auction_dates_to"><?php _e( 'Auction End date', 'dokan-auction' ); ?></label>
                                        <div class="dokan-form-group">
                                            <input class="dokan-form-control auction-datepicker" name="_auction_dates_to" id="_auction_dates_to" type="text" value="<?php echo $thursday;?>">
                                        </div>
                                    </div>

                                    <div class="dokan-clearfix"></div>

                                    <div class="auction_relist_section hide">
                                        <div class="dokan-form-group dokan-auction-automatic-relist">
                                            <div class="dokan-text-left">
                                                <div class="checkbox">
                                                    <label for="_auction_automatic_relist">
                                                        <input type="hidden" name="_auction_automatic_relist" value="no">
                                                        <input type="checkbox" name="_auction_automatic_relist" id="_auction_automatic_relist" value="yes">
                                                        <?php _e( 'Enable automatic relisting for this auction', 'dokan-auction' );?>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relist_options" style="display: none">
                                            <div class="dokan-w3 dokan-auction-relist-fail-time">
                                                <label class="dokan-control-label" for="_auction_relist_fail_time"><?php _e( 'Relist if fail after n hours', 'dokan-auction' ); ?></label>
                                                <div class="dokan-form-group">
                                                    <input class="dokan-form-control" name="_auction_relist_fail_time" id="_auction_relist_fail_time" type="number">
                                                </div>
                                            </div>
                                            <div class="dokan-w3 dokan-auction-relist-not-paid-time">
                                                <label class="dokan-control-label" for="_auction_relist_not_paid_time"><?php _e( 'Relist if not paid after n hours', 'dokan-auction' ); ?></label>
                                                <div class="dokan-form-group">
                                                    <input class="dokan-form-control" name="_auction_relist_not_paid_time" id="_auction_relist_not_paid_time" type="number">
                                                </div>
                                            </div>
                                            <div class="dokan-w3 dokan-auction-relist-duration">
                                                <label class="dokan-control-label" for="_auction_relist_duration"><?php _e( 'Relist auction duration in h', 'dokan-auction' ); ?></label>
                                                <div class="dokan-form-group">
                                                    <input class="dokan-form-control" name="_auction_relist_duration" id="_auction_relist_duration" type="number">
                                                </div>
                                            </div>
                                            <div class="dokan-clearfix"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="dokan-form-group dokan-auction-post-content hide">
                            <?php wp_editor( Dokan_Template_Auction::$post_content, 'post_content', array('editor_height' => 50, 'quicktags' => false, 'media_buttons' => false, 'teeny' => true, 'editor_class' => 'post_content') ); ?>
                        </div>

                        <?php do_action( 'dokan_new_auction_product_form' ); ?>
						<div class="product-edit-container dokan-clearfix">
                        <div class="content-half-part dokan-product-meta">
                        <div class="dokan-form-group">
                        	<!--<span style="float:left;"><em>The express lane is open with no line</em></span>-->
                            <input type="hidden" name="_auction_location" value="<?php echo $seller_address?>">
                            <input type="hidden" name="product-type" value="auction">
                            <?php wp_nonce_field( 'dokan_add_new_auction_product', 'dokan_add_new_auction_product_nonce' ); ?>
                            <input type="submit" name="add_auction_product" class="dokan-btn dokan-btn-theme dokan-btn-lg dokan-left" value="<?php esc_attr_e( 'Add auction Product', 'dokan-auction' ); ?>"/>
                        </div>
                        </div>
						</div>
                    </form>

                <?php } else { ?>

                    <?php dokan_seller_not_enabled_notice(); ?>

                <?php } ?>

            <?php } else { ?>

                <?php do_action( 'dokan_can_post_notice' ); ?>

            <?php } ?>
        <?php

            /**
             *  dokan_auction_content_inside_after hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_auction_content_inside_after' );
        ?>
    </div> <!-- #primary .content-area -->

     <?php
        /**
         *  dokan_dashboard_content_after hook
         *  dokan_withdraw_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
        do_action( 'dokan_new_auction_product_content_after' );
    ?>
</div><!-- .dokan-dashboard-wrap -->

<script>
    ;(function($){
        $(document).ready(function(){
			 //$(".content-half-part ul li").text().replace("®", "<span class='TM_offer'>&reg;</span>");
			 /*$("body").html(
   				$("body").html().replace(/&reg;/g, "<span class='TM_offer'>&reg;</span>").replace(/®/g, "<span class='TM_offer'>&reg;</span>")
			);*/
			/*$( "#product_cat" ).change(function() {
			   var title = $("#product_cat option:selected").text();
			    $("#post-title").val(title);
			});*/
			jQuery(document).on('change', "input[name='product_cat']", function() {
				jQuery('#auctionForm').validationEngine('hideAll');
				if(this.checked) {
				 	var title = jQuery(this).attr('id'); // retrieve the value
				 	$("#post-title").val(title);
				}
			});
            $('.auction-datepicker').datetimepicker({
                dateFormat : 'yy-mm-dd'
            });
            $('#_auction_automatic_relist').on( 'click', function(){
              if($(this).prop('checked')){
                  $('.relist_options').show();
              }else{
                  $('.relist_options').hide();
              }
            });

            $('.dokan-auction-proxy-bid').on('change', 'input#_auction_proxy', function() {
                if( $(this).prop('checked') ) {
                    $('.dokan-auction-sealed-bid').hide();
                } else {
                    $('.dokan-auction-sealed-bid').show();
                }
            });

            $('.dokan-auction-sealed-bid').on('change', 'input#_auction_sealed', function() {
                if ( $(this).prop('checked') ) {
                    $('.dokan-auction-proxy-bid').hide();
                } else {
                    $('.dokan-auction-proxy-bid').show();
                }
            });
            $('input#_auction_proxy').trigger('change');
            $('input#_auction_sealed').trigger('change');
		
		  window.history.pushState(null, "", window.location.href);        
		  window.onpopstate = function() {
			  window.history.pushState(null, "", window.location.href);
		  };
        });
    })(jQuery)

</script>
<?php 
	global $current_user;                     
	$args = array(
	  'author'        =>  $current_user->ID, 
	  'post_type'	=> 'attachment',
	  'posts_per_page' => -1 // no limit
	);
	
	
	$current_user_posts = get_posts( $args );
	$total = count($current_user_posts);
?>
<?php if($total > 30){?>
<style type="text/css">
.media-router .media-menu-item:first-child{
	display:none !important;
}
</style>
<script type="text/javascript">
/*;(function($){
      $(document).ready(function(){
			jQuery(".media-router .media-menu-item:first-child").hide();
	 });
    })(jQuery)*/
</script>
<?php }?>