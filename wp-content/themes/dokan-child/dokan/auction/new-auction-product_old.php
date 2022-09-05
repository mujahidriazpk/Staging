<?php
$monday = date("Y-m-d",strtotime( "monday next week" ))." 10:00";
$thursday = date('Y-m-d', strtotime( 'thursday next week' ) )." 17:00";
$flash_cycle_start = date('Y-m-d', strtotime( 'friday next week' ) )." 07:30";
$flash_cycle_end = date('Y-m-d', strtotime( 'friday next week' ) )." 09:30";

?>
<style type="text/css">
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
._auction_start_priceformError{left:0px !important;}
.photo_inner_div {float:left;width:88%;}
.opt{float:left;width:12%;text-align:center;font-weight:bold;}
.example_link{margin-left:72px;}
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
					'#feat_image_id_panoramic': {
						'min': {
							'message': "Please upload Panoramic X-ray photo"
						}
					},
					'#feat_image_id_periapical': {
						'min': {
							'message': "Please upload Periapical X-ray photo"
						}
					},
					'#feat_image_id_bitewings': {
						'min': {
							'message': "Please upload Bitewings X-ray photo"
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
        ?>
        <header class="dokan-dashboard-header dokan-clearfix">
            <h1 class="entry-title">
                <?php _e( 'Offerings', 'dokan-auction' ); ?>
            </h1>
        </header><!-- .entry-header -->
		<h3>
        	Select 1 Service, upload the required x-ray(s) & photos then enter your Ask Fee.
        </h3>
        <div class="dokan-new-product-area">
            <?php if ( Dokan_Template_Auction::$errors ) { ?>
                <div class="dokan-alert dokan-alert-danger">
                    <a class="dokan-close" data-dismiss="alert">&times;</a>
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
						<input type="hidden" name="_flash_cycle_start" id="_flash_cycle_start" value="<?php echo $flash_cycle_start;?>" />
						<input type="hidden" name="_flash_cycle_end" id="_flash_cycle_end" value="<?php echo $flash_cycle_end;?>" />
                        <div class="product-edit-container dokan-clearfix">
                        	<?php /*?>
                            <div class="content-half-part featured-image">
                                <div class="featured-image">
                                    <div class="dokan-feat-image-upload">
                                        <div class="instruction-inside">
                                            <input type="hidden" name="feat_image_id" class="dokan-feat-image-id" value="0">
                                            <i class="fa fa-cloud-upload"></i>
                                            <a href="#" class="dokan-feat-image-btn dokan-btn"><?php _e( 'Upload Product Image', 'dokan-auction' ); ?></a>
                                        </div>

                                        <div class="image-wrap dokan-hide">
                                            <a class="close dokan-remove-feat-image">&times;</a>
                                                <img src="" alt="">
                                        </div>
                                    </div>
                                </div>
                                
                                       

                                <div class="dokan-product-gallery hide">
                                    <div class="dokan-side-body" id="dokan-product-images">
                                        <div id="product_images_container">
                                            <ul class="product_images dokan-clearfix">
                                                <li class="add-image add-product-images tips" data-title="<?php _e( 'Add gallery image', 'dokan-auction' ); ?>">
                                                    <a href="#" class="add-product-images"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                                </li>
                                            </ul>
                                            <input type="hidden" id="product_image_gallery" name="product_image_gallery" value="">
                                        </div>
                                        
                                    </div>
                                </div> <!-- .product-gallery -->
                            </div>
							<?php */?>
							<style type="text/css">
							.product_cat{ text-transform:capitalize;}
							</style>
                            <div class="content-half-part dokan-product-meta">
                            <div class="dokan-form-group dokan-auction-category">
                            <div class="content-half-part">
                            <?php 
								$categories = get_categories( array(
													'taxonomy' => 'product_cat',
    												'hide_empty' => false,
													'exclude'          => '15,46,47',
													'orderby' => 'term_id',
													'order'   => 'ASC'
												));
								echo '';
								$i = 0;
								list($array1, $array2) = array_chunk($categories, ceil(count($categories) / 2));
								echo '';
								foreach($array1 as $cat){			
							?>
                            	<input type="radio" class="validate[required]" <?php if($cat->term_id==dokan_posted_input( 'product_cat' )){?> checked="checked"<?php }else{}?> name="product_cat" id="<?php echo $cat->name;?>"  value="<?php echo $cat->term_id;?>"/>&nbsp;<?php echo $cat->name;?><br />
                            <?php $i++;}?>
                            </div>
                            <div class="content-half-part">
                            <?php echo '';
								foreach($array2 as $cat){			
							?>
                            	<input type="radio" class="validate[required]" <?php if($cat->term_id==dokan_posted_input( 'product_cat' )){?> checked="checked"<?php }else{}?> name="product_cat" id="<?php echo $cat->name;?>" value="<?php echo $cat->term_id;?>"/>&nbsp;<?php echo $cat->name;?><br />
                            <?php $i++;}?>
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
                                                    <input  id="feat_image_id_periapical" type="hidden" name="feat_image_id_periapical" class="dokan-feat-image-id_periapical" value="0">
                                                    <i class="fa fa-cloud-upload"></i>
                                                    <a href="#" class="dokan-feat-image-btn_periapical dokan-btn"><?php _e( 'Upload periapical X-ray', 'dokan-auction' ); ?></a><br /><a class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/periapical-l.jpg');?>"><span class="wp-caption-text hide">Periapical Film (pa)</span>See example</a>
                                                   
                                                </div>
        
                                                <div class="image-wrap dokan-hide">
                                                    <a class="close dokan-remove-feat-image_periapical">&times;</a>
                                                        <img src="" alt="">
                                                </div>
                                            </div>
                                        </div>
                                        <!---------------------------->
                                        <div class="opt" id="opt_1"></div>
                                    </div>
                                    <div class="photo_div dokan-form-group dokan-auction-panoramic" style="display:none;" id="photo2">
                                         <!---------------------------->
                                        <div class="photo_inner_div featured-image_panoramic">
                                            <div class="upload_section dokan-feat-image-upload_panoramic">
                                                <div class="instruction-inside">
                                                    <input id="feat_image_id_panoramic" type="hidden" name="feat_image_id_panoramic" class="dokan-feat-image-id_panoramic" value="0">
                                                    <i class="fa fa-cloud-upload"></i>
                                                    <a href="#" class="dokan-feat-image-btn_panoramic dokan-btn"><?php _e( 'Upload panoramic X-ray', 'dokan-auction' ); ?></a><br /><a class="example_link" href="#" caption="abc" data-featherlight="<?php echo home_url('/wp-content/uploads/panoramic-l.jpg');?>"><span class="wp-caption-text hide">Panoramic Film (panorex)</span>See example</a>
                                                </div>
        
                                                <div class="image-wrap dokan-hide">
                                                    <a class="close dokan-remove-feat-image_panoramic">&times;</a>
                                                        <img src="" alt="">
                                                </div>
                                            </div>
                                        </div>
                                        <!---------------------------->
                                        <div class="opt" id="opt_2"></div>
                                    </div>
                                    <div class="photo_div dokan-form-group dokan-auction-bitewings" style="display:none;" id="photo3">
                                         <!---------------------------->
                                        <div class="photo_inner_div featured-image_bitewings">
                                            <div class="upload_section dokan-feat-image-upload_bitewings">
                                                <div class="instruction-inside">
                                                    <input id="feat_image_id_bitewings" type="hidden" name="feat_image_id_bitewings" class="dokan-feat-image-id_bitewings" value="0">
                                                    <i class="fa fa-cloud-upload"></i>
                                                    <a href="#" class="dokan-feat-image-btn_bitewings dokan-btn"><?php _e( 'Upload bitewings X-ray', 'dokan-auction' ); ?></a><br /><a class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/bitewings-l.jpg');?>"><span class="wp-caption-text hide">Bitewing Films (bws)</span>See example</a>
                                                </div>
        
                                                <div class="image-wrap dokan-hide">
                                                    <a class="close dokan-remove-feat-image_bitewings">&times;</a>
                                                        <img src="" alt="">
                                                </div>
                                            </div>
                                        </div>
                                        <!---------------------------->
                                        <div class="opt" id="opt_3"></div>
                                    </div>
                                    <div class="photo_div dokan-form-group dokan-auction-full_mouth" style="display:none;" id="photo4">
                                         <!---------------------------->
                                        <div class="photo_inner_div featured-image_full_mouth">
                                            <div class="upload_section dokan-feat-image-upload_full_mouth">
                                                <div class="instruction-inside">
                                                   <!-- <input style="width:1px;height:1px;opacity:0;" id="feat_image_id_full_mouth" type="text" name="feat_image_id_full_mouth" class="dokan-feat-image-id_full_mouth validate[required,min[1]]" value="0">-->
                                                   <input id="feat_image_id_full_mouth" type="hidden" name="feat_image_id_full_mouth" class="dokan-feat-image-id_full_mouth" value="0">
                                                    <i class="fa fa-cloud-upload"></i>
                                                    <a href="#" class="dokan-feat-image-btn_full_mouth dokan-btn"><?php _e( 'Upload fullmouth X-ray', 'dokan-auction' ); ?></a><br /><a  class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/full-mouth-series-l.jpg');?>"><span class="wp-caption-text hide">Full Mouth Series (fmx)</span>See example</a>
                                                </div>
        
                                                <div class="image-wrap dokan-hide">
                                                    <a class="close dokan-remove-feat-image_full_mouth">&times;</a>
                                                        <img src="" alt="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="opt" id="opt_4"></div>
                                        <!---------------------------->
                                    </div>
                                    <div class="sample_image_container">
                                    <div class="photo_div dokan-form-group sample_photo_div dokan-auction-sample1" style="display:none;" id="photo5">
                                         <!---------------------------->
                                        <div class="photo_inner_div featured-image_sample1">
                                            <div class="upload_section dokan-feat-image-upload_sample1">
                                                <div class="instruction-inside">
                                                   <!-- <input style="width:1px;height:1px;opacity:0;" id="feat_image_id_sample1" type="text" name="feat_image_id_sample1" class="dokan-feat-image-id_sample1 validate[required,min[1]]" value="0">-->
                                                   <input id="feat_image_id_sample1" type="hidden" name="feat_image_id_sample1" class="dokan-feat-image-id_sample1" value="0">
                                                    <i class="fa fa-cloud-upload"></i>
                                                    <a href="#" class="dokan-feat-image-btn_sample1 dokan-btn"><?php _e( 'Upload Wide Smile image', 'dokan-auction' ); ?></a><br /><a  class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/1-Wide-Smile-new.jpg');?>"><span class="wp-caption-text hide">Wide Smile</span>See example</a>
                                                </div>
        
                                                <div class="image-wrap dokan-hide">
                                                    <a class="close dokan-remove-feat-image_sample1">&times;</a>
                                                        <img src="" alt="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="opt" id="opt_5"></div>
                                        <!---------------------------->
                                    </div>
                                    <div class="photo_div dokan-form-group sample_photo_div dokan-auction-sample2" style="display:none;" id="photo6">
                                         <!---------------------------->
                                        <div class="photo_inner_div featured-image_sample2">
                                            <div class="upload_section dokan-feat-image-upload_sample2">
                                                <div class="instruction-inside">
                                                   <!-- <input style="width:1px;height:1px;opacity:0;" id="feat_image_id_sample2" type="text" name="feat_image_id_sample2" class="dokan-feat-image-id_sample2 validate[required,min[1]]" value="0">-->
                                                   <input id="feat_image_id_sample2" type="hidden" name="feat_image_id_sample2" class="dokan-feat-image-id_sample2" value="0">
                                                    <i class="fa fa-cloud-upload"></i>
                                                    <a href="#" class="dokan-feat-image-btn_sample2 dokan-btn"><?php _e( 'Upload Upper Teeth image', 'dokan-auction' ); ?></a><br /><a  class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/2-Upper-Teeth-new.jpg');?>"><span class="wp-caption-text hide">Upper Teeth</span>See example</a>
                                                </div>
        
                                                <div class="image-wrap dokan-hide">
                                                    <a class="close dokan-remove-feat-image_sample2">&times;</a>
                                                        <img src="" alt="">
                                                </div>
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
                                                   <!-- <input style="width:1px;height:1px;opacity:0;" id="feat_image_id_sample3" type="text" name="feat_image_id_sample3" class="dokan-feat-image-id_sample3 validate[required,min[1]]" value="0">-->
                                                   <input id="feat_image_id_sample3" type="hidden" name="feat_image_id_sample3" class="dokan-feat-image-id_sample3" value="0">
                                                    <i class="fa fa-cloud-upload"></i>
                                                    <a href="#" class="dokan-feat-image-btn_sample3 dokan-btn"><?php _e( 'Upload Lower Teeth image', 'dokan-auction' ); ?></a><br /><a  class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/3-Lower-Teeth-new.jpg');?>"><span class="wp-caption-text hide">Lower Teeth</span>See example</a>
                                                </div>
        
                                                <div class="image-wrap dokan-hide">
                                                    <a class="close dokan-remove-feat-image_sample3">&times;</a>
                                                        <img src="" alt="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="opt" id="opt_7"></div>
                                        <!---------------------------->
                                    </div>
                                    <div class="photo_div dokan-form-group sample_photo_div dokan-auction-sample4" style="display:none;" id="photo8">
                                         <!---------------------------->
                                        <div class="photo_inner_div featured-image_sample4">
                                            <div class="upload_section dokan-feat-image-upload_sample4">
                                                <div class="instruction-inside">
                                                   <!-- <input style="width:1px;height:1px;opacity:0;" id="feat_image_id_sample4" type="text" name="feat_image_id_sample4" class="dokan-feat-image-id_sample4 validate[required,min[1]]" value="0">-->
                                                   <input id="feat_image_id_sample4" type="hidden" name="feat_image_id_sample4" class="dokan-feat-image-id_sample4" value="0">
                                                    <i class="fa fa-cloud-upload"></i>
                                                    <a href="#" class="dokan-feat-image-btn_sample4 dokan-btn"><?php _e( 'Upload Profile image', 'dokan-auction' ); ?></a><br /><a  class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/4-Profile-new.jpg');?>"><span class="wp-caption-text hide">Profile</span>See example</a>
                                                </div>
        
                                                <div class="image-wrap dokan-hide">
                                                    <a class="close dokan-remove-feat-image_sample4">&times;</a>
                                                        <img src="" alt="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="opt" id="opt_8"></div>
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
											width:25% !important;
										}
										.sample_photo_div .photo_inner_div{
											float:left;
											width:97% !important;
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
												if(val != '39' && val != '40' && val != '65' && val != '66'){
                                                	$("#photo_note").show();
												}else{
													$("#photo_note").hide();
												}
                                                //var val = $("#product_cat option:selected").val();
                                                var myArray1 = ['20','22','23','24','25','26','27','44','45'];
                                                if (myArray1.indexOf(val) === -1) {
                                                    $("#photo1").hide();
                                                }else{
                                                    $("#photo1").show();
                                                }
                                                var myArray2 = ['18','19','20','21','28','29','30','31','32','33','34','35','36','37','38','41','42','43','44','45','46','47','48','49','50','51','52','53','54'];
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
                                                var myArray2 = ['18','19','21','28','29','30','31','32','33','36','37','38','46','47','54'];
                                                if (myArray2.indexOf(val) === -1) {
                                                    $("#photo4").hide();
                                                }else{
                                                    $("#photo4").show();
                                                }
												var myArraySample = ['41','42','43'];
                                                if (myArraySample.indexOf(val) === -1) {
                                                    $("#photo5").hide();
                                                    $("#photo6").hide();
                                                    $("#photo7").hide();
                                                    $("#photo8").hide();
                                                }else{
                                                    $("#photo5").show();
                                                    $("#photo6").show();
                                                    $("#photo7").show();
                                                    $("#photo8").show();
                                                }
												
                                            }
                                        function showOpt(val){
											   if(val=='41' || val=='42'|| val=='43'){
												   $("#sample_photo_div").show();
												   $("#sample_photo_div_2").hide();
											   }else if(val=='28' || val=='29' || val=='30' || val=='31' || val=='32'){
												   $("#sample_photo_div").hide();
												   $("#sample_photo_div_2").show();
											   }else{
												   $("#sample_photo_div").hide();
												   $("#sample_photo_div_2").hide();
											   }
                                               if(val=='18' || val=='19'){
												   $("#opt_2").html('AND');
												   $("#opt_3").html('OR');
											   }else if(val == '44' || val == '45'){
												   $("#opt_1").html('AND');
												   $("#opt_2").html('');
												   $("#opt_3").html('');
											   }else{
												   if(val !='22' && val !='23' && val !='24' && val !='25' && val !='26' && val !='27' && val !='34' && val !='35'){
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
												   $(".photo_div").css('width',"33%");
											   }else if(val =='22' || val =='23' || val =='24' || val =='25' || val =='26' || val =='27' || val =='34' || val =='35'|| val =='41' || val =='42' || val=='43' || val=='48' || val=='49'|| val=='50'|| val=='51'|| val=='52'|| val=='53'){
												    $("#opt_1").html('');
													   $("#opt_2").html('');
													   $("#opt_3").html('');
												    $(".photo_div").css('width',"100%");
												   }else{
													   $(".photo_div").css('width',"50%");
												   }
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
                                        <label class="dokan-control-label" for="_auction_start_price"><?php _e( 'Start Price', 'dokan-auction' ); ?></label>
                                        <div class="dokan-form-group">
                                            <div class="dokan-input-group">
                                                <span class="dokan-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                                <input class="dokan-form-control validate[required]"  name="_auction_start_price" id="_auction_start_price" type="number" placeholder="" value="<?php if(dokan_posted_input( '_auction_start_price' )){ echo dokan_posted_input( '_auction_start_price' );}else{ echo '';}?>" step="any" min="1" title="This is a reverse auction so enter the maximum amount you are willing to pay and entice the dentists to compete for your business.">
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
                                                    <a href="#" class="dokan-feat-image-btn_plan dokan-btn"><?php _e( 'Upload your treatment plan', 'dokan-auction' ); ?></a>
                                                </div>
        
                                                <div class="image-wrap dokan-hide">
                                                    <a class="close dokan-remove-feat-image_plan">&times;</a>
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
        });
    })(jQuery)

</script>