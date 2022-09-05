<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package dokan
 * @package dokan - 2014 1.0
 */
get_header();
global $wp,$post;
$current_url =  home_url($wp->request);
?>

<div id="primary" class="content-area col-md-12">
  <div id="content" class="site-content" role="main">
    <?php if(strpos($current_url,"/auction-") > 0){?>
    <style type="text/css">
	  @media only screen and (max-width: 448px){
.module {
    height: 100vh;
    height: calc(var(--vh, 1vh) * 100);
    margin: 0 auto;
    max-width: 100%;
}
.header {
    height: 4.4% !important;
}
.site-main {
    height: 46% !important;
    overflow: hidden;
}

.ad_section_main {
    height: 50% !important;
    padding: 0;
}
.rotation_ad{
	margin-bottom:5px;
}
.rotation_main{
	padding:0 5px;
}
	  }
.sgpb-content-1640,.sgpb-popup-overlay-1640{display:none !important;}
</style>
    <div id="product-4675" class="product type-product post-4675 status-publish first instock product_cat-in-office-teeth-whitening sold-individually shipping-taxable product-type-auction details" style="height: 100%;"> <span class="bid_amount_txt"> <span class="starting-bid" data-auction-id="4675" data-bid="" data-status="running"><span class="starting auction red">EXPIRED LISTING&nbsp;</span></span> </span> </div>
  </div>
  <script type="text/javascript">
  jQuery(document).ready(function() {
		//Place the section on right position for mobile layout
		jQuery(".navbar-top-area").addClass("module__item").addClass('header');
		jQuery("div#main.site-main").addClass("module__item").addClass('details');
		jQuery(".ad_section_main").addClass("module__item").addClass('ads');
		jQuery("#colophon").addClass("module__item").addClass('footer');
	 });
  </script>
  <?php }else{?>
  <article id="post-0" class="post error404 not-found">
    <header class="entry-header">
      <h1 class="entry-title">
        <?php _e( 'Oops! That page can&rsquo;t be found.', 'dokan-theme' ); ?>
      </h1>
    </header>
    <!-- .entry-header -->
    
    <div class="entry-content">
      <p>
        <?php _e( 'It looks like nothing was found at this location. Please return to <a href="'.home_url().'" title="home page">home page</a>.', 'dokan-theme' ); ?>
      </p>
      <?php //get_search_form(); ?>
    </div>
    <!-- .entry-content --> 
  </article>
  <!-- #post-0 .post .error404 .not-found -->
  <?php }?>
  <?php /*?>
        <div class="row">
            <div class="col-md-4">
                <?php the_widget( 'WP_Widget_Recent_Posts' ); ?>
            </div>
            <div class="col-md-4">
                <div class="widget">
                    <h2 class="widgettitle"><?php _e( 'Categories', 'dokan-theme' ); ?></h2>
                    <ul>
                        <?php wp_list_categories( array('orderby' => 'count', 'order' => 'DESC', 'show_count' => 1, 'title_li' => '', 'number' => 10) ); ?>
                    </ul>
                </div><!-- .widget -->
            </div>

            <div class="col-md-4">
                <?php the_widget( 'WP_Widget_Tag_Cloud' ); ?>

                <?php
                $archive_content = '<p>' . sprintf( __( 'Try looking in the monthly archives. %1$s', 'dokan-theme' ), convert_smilies( ':)' ) ) . '</p>';
                the_widget( 'WP_Widget_Archives', 'dropdown=1', "after_title=</h2>$archive_content" );
                ?>
            </div>
        </div>
		<?php */?>
</div>
<!-- #content .site-content -->
</div>
<!-- #primary .content-area -->

<?php get_footer(); ?>
