<?php
/*$url = get_permalink(48);
wp_redirect($url);
exit;*/
global $post, $product;

$post_id        = $post->ID;
$seller_id      = dokan_get_current_user_id();

if ( isset( $_GET['product_id'] ) ) {
    $post_id        = intval( $_GET['product_id'] );
    $post           = get_post( $post_id );
    $post_status    = $post->post_status;
    $product        = dokan_wc_get_product( $post_id );
}

// bail out if not author
if ( $post->post_author != $seller_id ) {
    wp_die( __( 'Access Denied', 'dokan-auction' ) );
}
$product_cat = -1;
$term = array();
$term = wp_get_post_terms( $post_id, 'product_cat', array( 'fields' => 'ids') );

if ( $term ) {
	$product_cat = reset( $term );
}
$feat_image_id     = 0;

if ( has_post_thumbnail( $post_id ) ) {
	$wrap_class        = '';
	$instruction_class = ' dokan-hide';
	$feat_image_id     = get_post_thumbnail_id( $post_id );
}
$_regular_price                = get_post_meta( $post_id, '_regular_price', true );
$_featured                     = get_post_meta( $post_id, '_featured', true );
$_stock                        = get_post_meta( $post_id, '_stock', true  );
$_auction_item_condition       = get_post_meta( $post_id, '_auction_item_condition', true );
$_auction_type                 = get_post_meta( $post_id, '_auction_type', true );

$_auction_proxy                = get_post_meta( $post_id, '_auction_proxy', true );
$_auction_sealed              = get_post_meta( $post_id, '_auction_sealed', true );
$_auction_start_price          = get_post_meta( $post_id, '_auction_start_price', true );
$_auction_bid_increment        = get_post_meta( $post_id, '_auction_bid_increment', true );
$_auction_reserved_price       = get_post_meta( $post_id, '_auction_reserved_price', true );
$_auction_dates_from           = get_post_meta( $post_id, '_auction_dates_from', true );
$_auction_dates_to             = get_post_meta( $post_id, '_auction_dates_to', true );

$_auction_automatic_relist     = get_post_meta( $post_id, '_auction_automatic_relist', true );
$_auction_relist_fail_time     = get_post_meta( $post_id, '_auction_relist_fail_time', true );
$_auction_relist_not_paid_time = get_post_meta( $post_id, '_auction_relist_not_paid_time', true );
$_auction_relist_duration      = get_post_meta( $post_id, '_auction_relist_duration', true );

$feat_image_id_periapical      = get_post_meta( $post_id, 'feat_image_id_periapical', true );
$feat_image_id_periapical_2      = get_post_meta( $post_id, 'feat_image_id_periapical_2', true );
$feat_image_id_panorex      = get_post_meta( $post_id, 'feat_image_id_panorex', true );
$feat_image_id_bitewing      = get_post_meta( $post_id, 'feat_image_id_bitewing', true );
$feat_image_id_full_mouth      = get_post_meta( $post_id, 'feat_image_id_full_mouth', true );
$feat_image_id_sample1      = get_post_meta( $post_id, 'feat_image_id_sample1', true );
$feat_image_id_sample2      = get_post_meta( $post_id, 'feat_image_id_sample2', true );
$feat_image_id_sample3      = get_post_meta( $post_id, 'feat_image_id_sample3', true );
$feat_image_id_sample4      = get_post_meta( $post_id, 'feat_image_id_sample4', true );


$_visibility                   = ( version_compare( WC_VERSION, '2.7', '>' ) ) ? $product->get_catalog_visibility() : get_post_meta( $post_id, '_visibility', true );
$visibility_options            = dokan_get_product_visibility_options();
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
/*._auction_start_priceformError,._auction_maximum_travel_distanceformError{left:0px !important;}*/
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
	/*._auction_start_priceformError,._auction_maximum_travel_distanceformError{left:200px !important;}*/
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
do_action( 'dokan_edit_auction_product_content_before' );
?>
  <!--  -->
  <div class="dokan-dashboard-content dokan-product-edit">
    <?php

    /**
     *  dokan_edit_auction_product_content_inside_before hook
     *
     *  @since 2.4
     */
    do_action( 'dokan_edit_auction_product_content_inside_before' );
    ?>
    <?php /*?>
    <header class="dokan-dashboard-header dokan-clearfix">
      <h1 class="entry-title">
        <?php _e( 'Edit Auction Products', 'dokan-auction' ); ?>
        <span class="dokan-label <?php echo dokan_get_post_status_label_class( $post->post_status ); ?> dokan-product-status-label"> <?php echo dokan_get_post_status( $post->post_status ); ?> </span>
        <?php if ( $_visibility == 'hidden' ) { ?>
        <span class="dokan-label dokan-label-default">
        <?php _e( 'Hidden', 'dokan-auction' ); ?>
        </span>
        <?php } ?>
        <?php if ( $post->post_status == 'publish' ) { ?>
        <span class="dokan-right"> <a class="view-product dokan-btn dokan-btn-sm" href="<?php echo get_permalink( $post->ID ); ?>" target="_blank">
        <?php _e( 'View Product', 'dokan-auction' ); ?>
        </a> </span>
        <?php } ?>
      </h1>
    </header>
    <div class="dokan-new-product-area">
      <?php if ( isset( $_GET['message'] ) && $_GET['message'] == 'success') { ?>
      <div class="dokan-message">
        <button type="button" class="dokan-close" data-dismiss="alert">&nbsp;</button>
        <strong>
        <?php _e( 'Success!', 'dokan-auction' ); ?>
        </strong>
        <?php _e( 'The product has been updated successfully.', 'dokan-auction' ); ?>
        <?php if ( $post->post_status == 'publish' ) { ?>
        <a href="<?php echo get_permalink( $post_id ); ?>" target="_blank">
        <?php _e( 'View Product &rarr;', 'dokan-auction' ); ?>
        </a>
        <?php } ?>
      </div>
      <?php } ?>
      <h3> Select 1 Service, upload the required x-ray(s) & photos then enter your Ask Fee & Travel Distance. </h3>
	  <?php */?>
      <?php global $title_right;?>
      <header class="dokan-dashboard-header dokan-clearfix">
            <h1 class="entry-title">
                <?php _e( 'Offerings', 'dokan-auction' ); ?><?php echo $title_right;?>
            </h1>
        </header><!-- .entry-header -->
		<h3>
        	Select 1 Service, upload the required x-ray(s) & photos then enter your Ask Fee & Maximum Travel Distance.
        </h3>
      <form class="dokan-form-container dokan-auction-product-form" role="form" method="post" id="auctionForm">
        <?php wp_nonce_field( 'dokan_edit_auction_product', 'dokan_edit_auction_product_nonce' ); ?>
        <div class="product-edit-container dokan-clearfix">
          <?php /*?>
                            <div class="content-half-part featured-image">
                                <div class="featured-image">
                                    <div class="dokan-feat-image-upload">
                                        <div class="instruction-inside">
                                            <input type="hidden" name="feat_image_id" class="dokan-feat-image-id" value="0">
                                            <i class="fa fa-cloud-upload"></i>
                                            <a href="#" class="dokan-feat-image-btn dokan-btn"><?php _e( '<span class="upload_link">Upload</span> Product Image', 'dokan-auction' ); ?></a>
                                        </div>

                                        <div class="image-wrap dokan-hide">
                                            <a class="remove dokan-remove-feat-image">&nbsp;</a>
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
							.content-half-part.dokan-product-meta{width:100% !important;float:left;}
							</style>
          <div class="content-half-part dokan-product-meta">
            <div class="dokan-form-group dokan-auction-category">
              <div class="content-half-part half-part-1">
                <?php 
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
								$i = 0;
								list($array1, $array2) = array_chunk($categories, ceil(count($categories) / 2));
								echo '';
								foreach($array1 as $cat){
								$categories_level_2 =  get_categories('parent='.$cat->term_id.'&exclude=15&hide_empty=0&taxonomy=product_cat&orderby=term_order&order=asc');	?>
                <ul>
                  <strong><?php echo $cat->name;?></strong>
                  <?php foreach($categories_level_2 as $cat_level_2){?>
                  <?php $checked=""; if($product_cat==$cat_level_2->term_id){ $checked = 'checked="checked"';}?>
                  <?php if($cat_level_2->term_id != 106 && $cat_level_2->term_id != 118){?>
                  <li>
                    <input type="radio" class="validate[required]" <?php if($cat_level_2->term_id==$product_cat){?> checked="checked"<?php }else{}?> name="product_cat" id="<?php echo $cat_level_2->name;?>"  value="<?php echo $cat_level_2->term_id;?>"/>
                    &nbsp;<span class="cat_label"><?php echo str_replace("*","",$cat_level_2->name);?><?php if($cat_level_2->term_id==76){?>&nbsp;-&nbsp;<span style="font-style:italic">abutments & denture only</span><?php }?><?php if($cat_level_2->term_id==77){?>&nbsp;-&nbsp;<span style="font-style:italic">abutments & dentures only</span><?php }?><?php if($cat_level_2->term_id==119){?>&nbsp;-&nbsp;<span style="font-style:italic">locators & retrofit service only</span><?php }?></span></li>
                    <?php }?>
                  <?php }?>
                </ul>
                <?php }?>
              </div>
              <div class="content-half-part  half-part-1"> <?php echo '';
								foreach($array2 as $cat){
								$categories_level_2 =  get_categories('parent='.$cat->term_id.'&exclude=15&hide_empty=0&taxonomy=product_cat&orderby=term_order&order=asc');	?>
                <ul>
                  <strong><?php echo $cat->name;?></strong>
                  <?php foreach($categories_level_2 as $cat_level_2){?>
                  <?php $checked=""; if($product_cat==$cat_level_2->term_id){ $checked = 'checked="checked"';}?>
                  <?php if($cat_level_2->term_id != 106 && $cat_level_2->term_id != 118){?>
                  <li>
                    <input type="radio" class="validate[required]" <?php if($cat_level_2->term_id==$product_cat){?> checked="checked"<?php }else{}?> name="product_cat" id="<?php echo $cat_level_2->name;?>" value="<?php echo $cat_level_2->term_id;?>"/>
                    &nbsp;<span class="cat_label"><?php echo str_replace("*","",$cat_level_2->name);?><?php if($cat_level_2->term_id==76){?>&nbsp;-&nbsp;<span style="font-style:italic">abutments & denture only</span><?php }?><?php if($cat_level_2->term_id==77){?>&nbsp;-&nbsp;<span style="font-style:italic">abutments & dentures only</span><?php }?><?php if($cat_level_2->term_id==119){?>&nbsp;-&nbsp;<span style="font-style:italic">locators & retrofit service only</span><?php }?></span></li>
                    <?php }?>
                  <?php }?>
                </ul>
                <?php }?>
                <ul class='custom_service'><li>Six Month Smiles<span class='TM_offer_2'>®</span>, Invisalign<span class='TM_offer_2'>®</span>, & NTI-TSS<span class='TM_offer_2'>®</span> are not registered trademarks of ShopADoc, Inc.</li></ul>
              </div>
            </div>
            <div class="dokan-form-group dokan-auction-post-title hide">
              <input class="dokan-form-control" name="post_title" id="post-title" type="text" placeholder="<?php esc_attr_e( 'Product name..', 'dokan-auction' ); ?>" value="<?php echo $post->post_title; ?>">
            </div>
            <div class="dokan-form-group" style="display:none;color:red;font-size:16px;" id="photo_note"> <span><strong>Note:</strong>&nbsp;X-rays must be no older than 30 days and must not include any personal information or identification. Please use the edit function on your device to comply with this requirement.</span> </div>
            <div class="photo_div dokan-form-group dokan-auction-periapical" style="display:none;" id="photo1"> 
              <!---------------------------->
              <div class="photo_inner_div featured-image_periapical">
                <div class="upload_section dokan-feat-image-upload_periapical">
                  <div class="instruction-inside <?php if($feat_image_id_periapical > 0){ echo 'dokan-hide';}else{ echo '';}?>">
                    <input  id="feat_image_id_periapical" type="hidden" name="feat_image_id_periapical" class="dokan-feat-image-id_periapical" value="<?php if($feat_image_id_periapical > 0){ echo $feat_image_id_periapical;}else{ echo '0';}?>">
                    <i class="fa fa-cloud-upload"></i> <a href="#" class="dokan-feat-image-btn_periapical dokan-btn">
                    <?php _e( '<span class="upload_link">Upload</span> periapical X-ray', 'dokan-auction' ); ?>
                    </a><br />
                    <a class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/periapical-l.jpg');?>"><span class="wp-caption-text hide">Periapical Film (pa)</span>See example</a> </div>
                  <div class="image-wrap <?php if($feat_image_id_periapical > 0){ echo '';}else{ echo 'dokan-hide';}?>"> <a class="remove dokan-remove-feat-image_periapical">&nbsp;</a> <img src="<?php if($feat_image_id_periapical > 0){ echo wp_get_attachment_url($feat_image_id_periapical);}else{ echo '';}?>" alt=""> </div>
                </div>
              </div>
              <!---------------------------->
              <div class="opt" id="opt_1"></div>
            </div>
            <div class="photo_div dokan-form-group dokan-auction-panorex" style="display:none;" id="photo2"> 
              <!---------------------------->
              <div class="photo_inner_div featured-image_panorex">
                <div class="upload_section dokan-feat-image-upload_panorex">
                  <div class="instruction-inside <?php if($feat_image_id_panorex > 0){ echo 'dokan-hide';}else{ echo '';}?>">
                    <input  id="feat_image_id_panorex" type="hidden" name="feat_image_id_panorex" class="dokan-feat-image-id_panorex" value="<?php if($feat_image_id_panorex > 0){ echo $feat_image_id_panorex;}else{ echo '0';}?>">
                    <i class="fa fa-cloud-upload"></i> <a href="#" class="dokan-feat-image-btn_panorex dokan-btn">
                    <?php _e( '<span class="upload_link">Upload</span> panorex X-ray', 'dokan-auction' ); ?>
                    </a><br />
                    <a class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/panorex-l.jpg');?>"><span class="wp-caption-text hide">panorex Film</span>See example</a> </div>
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
                  <div class="instruction-inside <?php if($feat_image_id_bitewing > 0){ echo 'dokan-hide';}else{ echo '';}?>">
                    <input  id="feat_image_id_bitewing" type="hidden" name="feat_image_id_bitewing" class="dokan-feat-image-id_bitewing" value="<?php if($feat_image_id_bitewing > 0){ echo $feat_image_id_bitewing;}else{ echo '0';}?>">
                    <i class="fa fa-cloud-upload"></i> <a href="#" class="dokan-feat-image-btn_bitewing dokan-btn">
                    <?php _e( '<span class="upload_link">Upload</span> Bitewing X-ray', 'dokan-auction' ); ?>
                    </a><br />
                    <a class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/bitewing-l.jpg');?>"><span class="wp-caption-text hide">Bitewing Films (bws)</span>See example</a> </div>
                  <div class="image-wrap <?php if($feat_image_id_bitewing > 0){ echo '';}else{ echo 'dokan-hide';}?>"> <a class="remove dokan-remove-feat-image_bitewing">&nbsp;</a> <img src="<?php if($feat_image_id_bitewing > 0){ echo wp_get_attachment_url($feat_image_id_bitewing);}else{ echo '';}?>" alt=""> </div>
                </div>
              </div>
              <!---------------------------->
              <div class="opt" id="opt_3"></div>
            </div>
            <div class="photo_div dokan-form-group dokan-auction-full_mouth" style="display:none;" id="photo4"> 
              <!---------------------------->
              <div class="photo_inner_div featured-image_full_mouth">
                <div class="upload_section dokan-feat-image-upload_full_mouth">
                  <div class="instruction-inside <?php if($feat_image_id_full_mouth > 0){ echo 'dokan-hide';}else{ echo '';}?>">
                    <input  id="feat_image_id_full_mouth" type="hidden" name="feat_image_id_full_mouth" class="dokan-feat-image-id_full_mouth" value="<?php if($feat_image_id_full_mouth > 0){ echo $feat_image_id_full_mouth;}else{ echo '0';}?>">
                    <i class="fa fa-cloud-upload"></i> <a href="#" class="dokan-feat-image-btn_full_mouth dokan-btn">
                    <?php _e( '<span class="upload_link">Upload</span> full mouth series X-rays', 'dokan-auction' ); ?>
                    </a><br />
                    <a class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/full-mouth-series-l.jpg');?>"><span class="wp-caption-text hide">Full Mouth Series (fmx)</span>See example</a> </div>
                  <div class="image-wrap <?php if($feat_image_id_full_mouth > 0){ echo '';}else{ echo 'dokan-hide';}?>"> <a class="remove dokan-remove-feat-image_full_mouth">&nbsp;</a> <img src="<?php if($feat_image_id_full_mouth > 0){ echo wp_get_attachment_url($feat_image_id_full_mouth);}else{ echo '';}?>" alt=""> </div>
                </div>
              </div>
              <!---------------------------->
              <div class="opt" id="opt_4"></div>
            </div>
            <div class="sample_image_container">
              <div class="photo_div dokan-form-group sample_photo_div dokan-auction-sample1" style="display:none;" id="photo5">
                <div class="opt" id="and" style="display:none;">AND</div>
                <!---------------------------->
                <div class="photo_inner_div featured-image_sample1">
                  <div class="upload_section dokan-feat-image-upload_sample1">
                    <div class="instruction-inside <?php if($feat_image_id_sample1 > 0){ echo 'dokan-hide';}else{ echo '';}?>">
                      <input id="feat_image_id_sample1" type="hidden" name="feat_image_id_sample1" class="dokan-feat-image-id_sample1" value="<?php if($feat_image_id_sample1 > 0){ echo $feat_image_id_sample1;}else{ echo '0';}?>">
                      <i class="fa fa-cloud-upload"></i> <a href="#" class="dokan-feat-image-btn_sample1 dokan-btn">
                      <?php _e( '<span class="upload_link">Upload</span> Full Frontal Smile photo', 'dokan-auction' ); ?>
                      </a><br />
                      <a  class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/1-Wide-Smile-new.jpg');?>"><span class="wp-caption-text hide">Full Frontal Smile</span>See example</a> </div>
                    <div class="image-wrap <?php if($feat_image_id_sample1 > 0){ echo '';}else{ echo 'dokan-hide';}?>"> <a class="remove dokan-remove-feat-image_sample1">&nbsp;</a> <img src="<?php if($feat_image_id_sample1 > 0){ echo wp_get_attachment_url($feat_image_id_sample1);}else{ echo '';}?>" alt=""> </div>
                  </div>
                </div>
                <div class="opt" id="opt_5"></div>
                <!----------------------------> 
              </div>
              <div class="photo_div dokan-form-group sample_photo_div dokan-auction-sample1" style="display:none;" id="photo5_PA">
                <div class="opt" id="and" style="display:none;">AND</div>
                <!---------------------------->
                <div class="photo_inner_div featured-image_periapical_2">
                  <div class="upload_section dokan-feat-image-upload_periapical_2">
                    <div class="instruction-inside <?php if($feat_image_id_periapical_2 > 0){ echo 'dokan-hide';}else{ echo '';}?>">
                      <input id="feat_image_id_periapical_2" type="hidden" name="feat_image_id_periapical_2" class="dokan-feat-image-id_periapical_2" value="<?php if($feat_image_id_periapical_2 > 0){ echo $feat_image_id_periapical_2;}else{ echo '0';}?>">
                      <i class="fa fa-cloud-upload"></i> <a href="#" class="dokan-feat-image-btn_periapical_2 dokan-btn">
                      <?php _e( '<span class="upload_link">Upload</span> periapical X-ray', 'dokan-auction' ); ?>
                      </a><br />
                      <a  class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/periapical-l.jpg');?>"><span class="wp-caption-text hide">Full Frontal Smile</span>See example</a> </div>
                    <div class="image-wrap <?php if($feat_image_id_periapical_2 > 0){ echo '';}else{ echo 'dokan-hide';}?>"> <a class="remove dokan-remove-feat-image_periapical_2">&nbsp;</a> <img src="<?php if($feat_image_id_periapical_2 > 0){ echo wp_get_attachment_url($feat_image_id_periapical_2);}else{ echo '';}?>" alt=""> </div>
                  </div>
                </div>
                <div class="opt" id="opt_5"></div>
                <!----------------------------> 
              </div>
              <div class="photo_div dokan-form-group sample_photo_div dokan-auction-sample4" style="display:none;" id="photo8">
                <div class="opt" id="and" style="display:none;">AND</div>
                <!---------------------------->
                <div class="photo_inner_div featured-image_sample4">
                  <div class="upload_section dokan-feat-image-upload_sample4">
                    <div class="instruction-inside <?php if($feat_image_id_sample4 > 0){ echo 'dokan-hide';}else{ echo '';}?>">
                      <input id="feat_image_id_sample4" type="hidden" name="feat_image_id_sample4" class="dokan-feat-image-id_sample4" value="<?php if($feat_image_id_sample4 > 0){ echo $feat_image_id_sample4;}else{ echo '0';}?>">
                      <i class="fa fa-cloud-upload"></i> <a href="#" class="dokan-feat-image-btn_sample4 dokan-btn">
                      <?php _e( '<span class="upload_link">Upload</span> Profile photo', 'dokan-auction' ); ?>
                      </a><br />
                      <a  class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/4-Profile-new.jpg');?>"><span class="wp-caption-text hide">Full Frontal Smile</span>See example</a> </div>
                    <div class="image-wrap <?php if($feat_image_id_sample4 > 0){ echo '';}else{ echo 'dokan-hide';}?>"> <a class="remove dokan-remove-feat-image_sample4">&nbsp;</a> <img src="<?php if($feat_image_id_sample4 > 0){ echo wp_get_attachment_url($feat_image_id_sample4);}else{ echo '';}?>" alt=""> </div>
                  </div>
                </div>
                <div class="opt" id="opt_8"></div>
                <!----------------------------> 
              </div>
              <div class="photo_div dokan-form-group sample_photo_div dokan-auction-sample2" style="display:none;" id="photo6">
                <div class="opt" id="and" style="display:none;">AND</div>
                <!---------------------------->
                <div class="photo_inner_div featured-image_sample2">
                  <div class="upload_section dokan-feat-image-upload_sample2">
                    <div class="instruction-inside <?php if($feat_image_id_sample2 > 0){ echo 'dokan-hide';}else{ echo '';}?>">
                      <input id="feat_image_id_sample2" type="hidden" name="feat_image_id_sample2" class="dokan-feat-image-id_sample2" value="<?php if($feat_image_id_sample2 > 0){ echo $feat_image_id_sample2;}else{ echo '0';}?>">
                      <i class="fa fa-cloud-upload"></i> <a href="#" class="dokan-feat-image-btn_sample2 dokan-btn">
                      <?php _e( '<span class="upload_link">Upload</span> Maxillary Top Arch photo', 'dokan-auction' ); ?>
                      </a><br />
                      <a  class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/2-Upper-Teeth-new.jpg');?>"><span class="wp-caption-text hide">Full Frontal Smile</span>See example</a> </div>
                    <div class="image-wrap <?php if($feat_image_id_sample2 > 0){ echo '';}else{ echo 'dokan-hide';}?>"> <a class="remove dokan-remove-feat-image_sample2">&nbsp;</a> <img src="<?php if($feat_image_id_sample2 > 0){ echo wp_get_attachment_url($feat_image_id_sample2);}else{ echo '';}?>" alt=""> </div>
                  </div>
                </div>
                <div class="opt" id="opt_6"></div>
                <!----------------------------> 
              </div>
              <div class="photo_div dokan-form-group sample_photo_div dokan-auction-sample3" style="display:none;" id="photo7">
                <div class="opt" id="and" style="display:none;">AND</div>
                <!---------------------------->
                <div class="photo_inner_div featured-image_sample3">
                  <div class="upload_section dokan-feat-image-upload_sample3">
                    <div class="instruction-inside <?php if($feat_image_id_sample3 > 0){ echo 'dokan-hide';}else{ echo '';}?>">
                      <input id="feat_image_id_sample3" type="hidden" name="feat_image_id_sample3" class="dokan-feat-image-id_sample3" value="<?php if($feat_image_id_sample3 > 0){ echo $feat_image_id_sample3;}else{ echo '0';}?>">
                      <i class="fa fa-cloud-upload"></i> <a href="#" class="dokan-feat-image-btn_sample3 dokan-btn">
                      <?php _e( '<span class="upload_link">Upload</span> Mandibular Bottom Arch photo', 'dokan-auction' ); ?>
                      </a><br />
                      <a  class="example_link" href="#" data-featherlight="<?php echo home_url('/wp-content/uploads/3-Lower-Teeth-new.jpg');?>"><span class="wp-caption-text hide">Full Frontal Smile</span>See example</a> </div>
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
            
            
            <script>
                                    ;(function($){
                                        function showPhotos(val,mode){
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
												   if(val !='22' && val !='87' && val !='20' && val !='21' && val !='23' && val !='24' && val !='25' && val !='26' && val !='27'  &&  val !='76' && val !='77' && val !='78' && val !='79' && val !='80' && val !='84' && val !='81' && val !='82' && val !='98' && val !='99' && val !='83'  && val !='44' && val !='117' && val !='34' && val !='35' && val !='106'){
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
										}
                                        <?php if($product_cat){?>
                                                showPhotos('<?php echo $product_cat;?>','load');
												showOpt('<?php echo $product_cat;?>');
                                            <?php }?>	
                                        $(document).ready(function(){
                                            $( "#product_cat" ).change(function() {
                                                showPhotos();
												showOpt();
                                            });
                                            
                                            jQuery(document).on('change', "input[name='product_cat']", function() {
                                                if(this.checked) {
                                                    var val = jQuery(this).val(); // retrieve the value
                                                    showPhotos(val,'check');
													showOpt(val);
                                                }
                                            });	
                                        });
                                    })(jQuery)
                                
                                </script>
            <?php $_auction_start_price          = get_post_meta( $post_id, '_auction_start_price', true );?>
            <div class="dokan-form-group dokan-auction-start-price">
                                        <label class="dokan-control-label _auction_start_price_label" for="_auction_start_price"><?php _e( 'Ask Fee', 'dokan-auction' ); ?>&nbsp;<span class="tooltip_New"><span class="tooltips" title='Enter the maximum amount you are willing to pay for the service selected.<br /><br />You will have the ability to increase your "Ask Fee" on the auction screen.'>&nbsp;</span></span></label>
                                        <div class="dokan-form-group">
                                            <div class="dokan-input-group">
                                                <span class="dokan-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                                <input class="dokan-form-control validate[required] min[1]"  name="_auction_start_price" id="_auction_start_price" type="text" placeholder="" value="<?php if($_auction_start_price){ echo $_auction_start_price;}else{ echo 0;}?>" step="any" min="1" data-prompt-position="topLeft:50,0" autocomplete="off" />
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
                                        jQuery('#_auction_start_price').keypress(function(e){ 
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
										});
                                    });
                                    </script>
            <?php $_auction_maximum_travel_distance          = get_post_meta( $post_id, '_auction_maximum_travel_distance', true );?>
            <div class="dokan-form-group dokan-auction-start-price">
                                        <label class="dokan-control-label _auction_maximum_travel_distance_label" for="_auction_maximum_travel_distance"><?php _e( 'Maximum Travel Distance', 'dokan-auction' ); ?>&nbsp;<span class="tooltip_New"><span class="tooltips" title='Enter the maximum miles you are willing to travel to a dental office for service.<br /><br />1 Mile = 20 City Blocks = 20 minutes walk time. You will have the ability to increase your "Maximum Travel Distance" on the auction screen.'>&nbsp;</span></span></label>
                                        <div class="dokan-form-group">
                                            <div class="dokan-input-group">
                                                <span class="dokan-input-group-addon"><?php echo 'Miles'; ?></span>
                                                <input class="dokan-form-control validate[required] min[1] max[35]"  name="_auction_maximum_travel_distance" id="_auction_maximum_travel_distance" type="text" placeholder="" value="<?php if($_auction_maximum_travel_distance){ echo $_auction_maximum_travel_distance;}else{ echo 0;}?>" step="any" min="1" max="35" data-prompt-position="topLeft:130,0" autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>
            
          </div>
        </div>
        <div class="product-edit-container dokan-clearfix" style="padding-bottom:15px;">
          <div id="edit-product">
            <?php do_action( 'dokan_product_edit_before_main' ); ?>
            <input type="hidden" name="dokan_product_id" id="dokan-edit-product-id" value="<?php echo $post_id; ?>"/>
            <input type="hidden" name="product-type" value="auction">
            <input type="submit" name="update_auction_product" class="dokan-btn dokan-btn-theme dokan-btn-lg dokan-left" value="<?php esc_attr_e( 'Proceed to Checkout', 'dokan-auction' ); ?>"/>
            <div class="dokan-clearfix"></div>
          </div>
        </div>
      </form>
    </div>
    <?php

    /**
     *  dokan_edit_auction_product_inside_after hook
     *
     *  @since 2.4
     */
    do_action( 'dokan_edit_auction_product_inside_after' );
    ?>
  </div>
  <?php
/**
 *  dokan_dashboard_content_after hook
 *  dokan_withdraw_content_after hook
 *
 *  @since 2.4
 */
do_action( 'dokan_dashboard_content_after' );
do_action( 'dokan_edit_auction_product_content_after' );
wp_reset_postdata();
wp_reset_query();
?>
</div>
<!-- .dokan-dashboard-wrap -->

<style>
    .show_if_variable {
        display: none !important;
    }
</style>
<script>
;(function($){
    $(document).ready(function(){
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

        if($('#_auction_automatic_relist').prop('checked')){
            $('.relist_options').show();
        }else{
            $('.relist_options').hide();
        }

        $('#_auction_automatic_relist').on( 'change', function(){
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