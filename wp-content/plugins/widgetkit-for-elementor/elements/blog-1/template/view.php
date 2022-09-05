<?php
// Silence is golden.

	$settings = $this->get_settings();
    $id = $this->get_id();

?>

	<div id="<?php echo $id;?>" class="tgx-blog-1">
            <?php
                $post_formats = array('audio', 'image', 'video', 'link', 'gallery');
                    $blog = array(
                        'cat' => $settings['cat_name'],
                        'post_type'         => 'post',
                        'post_status'       => 'publish',
                        'posts_per_page'    => -1,
                        'ignore_sticky_posts' => 1,
                         'tax_query' => array( array(
                            'taxonomy' => 'post_format',
                            'field' => 'slug',
                            'terms' => array('post-format-aside', 'post-format-gallery', 'post-format-link', 'post-format-image', 'post-format-quote', 'post-format-status', 'post-format-audio', 'post-format-chat', 'post-format-video'),
                            'operator' => 'NOT IN'
                           ) 
                        ),
                    );

                $blog_query = new WP_Query( $blog );
                    if($blog_query->have_posts()):
                        while($blog_query->have_posts()): 
                            $blog_query->the_post(); 
            ?>


                <?php if (has_post_thumbnail()): ?>             

                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-wrapper'); ?>>
                        <?php if ( has_post_thumbnail() ):?>
                            <figure class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php  the_post_thumbnail();?>
                                </a>
                            </figure> <!-- post thumbnail -->
                        <?php endif; ?>

                        <div class="post-details">                       
                            <header class="entry-header">
                                <h4 class="entry-title">
                                    <a href="<?php the_permalink();?>">
                                        <?php esc_html_e(wp_trim_words( get_the_title(),4, ''), 'widgetkit-for-elementor'); ?>
                                        
                                    </a>
                                </h4>       
                            </header><!-- .entry-header -->

                            <?php if ($settings['meta_position'] == 'top'):?>
                                <div class="entry-top">
                                    <?php if ( 'post' === get_post_type() ) : ?>
                                        <div class="entry-meta">
                                            <span>
                                                <a href="<?php the_permalink();?>">
                                                   <?php esc_html_e(date_i18n((get_option('date_format'))), 'widgetkit-for-elementor'); ?>                                            
                                                </a>
                                                
                                                <?php if ($settings['comment_enable']): ?>
                                                    
                                                    <?php esc_html_e('|', 'widgetkit-for-elementor');?>

                                                    <a href="<?php the_permalink();?>">
                                                        <?php comments_number( 'No comments ', '1 Comments', '% responses' ); ?>
                                                    </a>
                                                <?php endif ?>
                                            </span>                               
                                        </div><!-- .entry-meta -->
                                    <?php endif; ?>
                                </div> <!-- .entry-footer -->
                            <?php endif; ?>


                            <?php if (!in_array(get_post_format(), $post_formats)):?>
                                <div class="entry-content">
                                    <p><?php esc_html_e(wp_trim_words( get_the_content(),20, ''), 'widgetkit-for-elementor'); ?></p>
                                </div><!-- .entry-content -->
                            <?php endif; ?>


                            <?php if ($settings['meta_position'] == 'bottom'):?>
                                <footer class="entry-footer">
                                <?php if ($settings['choose_meta_option'] == 'button'):?>
                                  <a class="btn-readmore" href="<?php the_permalink();?>"><?php echo $settings['read_more_text'];?></a>
                                <?php else: ?>
                                     <?php if ( 'post' === get_post_type() ) : ?>
                                        <div class="entry-meta">
                                            <span>
                                                <a href="<?php the_permalink();?>">
                                                   <?php esc_html_e(date_i18n((get_option('date_format'))), 'widgetkit-for-elementor'); ?>                                            
                                                </a>

                                                    <?php if ($settings['comment_enable']): ?>
                                                    
                                                    <?php esc_html_e('|', 'widgetkit-for-elementor');?>

                                                    <a href="<?php the_permalink();?>">
                                                        <?php comments_number( 'No comments ', '1 Comments', '% responses' ); ?>
                                                    </a>
                                                <?php endif ?>
                                            </span>                               
                                        </div><!-- .entry-meta -->

                                <?php endif; ?>

                                    <?php endif; ?>
                                </footer> <!-- .entry-footer -->
                            <?php endif; ?>
                        </div><!-- .blog details -->
                    </article><!-- #post-## -->
            <?php endif; ?>
        <?php endwhile; wp_reset_postdata(); endif; ?>   
  </div> <!-- /.section -->


    <script type='text/javascript'>
          jQuery(document).ready(function($) {
            jQuery("#<?php echo $id; ?>").addClass("owl-carousel").owlCarousel({
                  pagination: false,
                  margin:10,
                  dots:false,
                  loop:true,

                <?php if ($settings['blog_1_nav_enable'] == 'yes'): ?>
                    nav: true,
                <?php else: ?>
                    nav:false,
                <?php endif; ?>
                  navClass: ['owl-carousel-left','owl-carousel-right'],
                  navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
                  autoHeight : true,
                <?php if ($settings['auto_play_enable'] == 'yes'): ?>
                      autoplay:true,
                <?php else: ?>
                       autoplay:false,
                <?php endif; ?>
                  responsive:{
                      0:{
                          items:1
                      },
                      600:{
                          items:1
                      },
                      1000:{
                          items:<?php echo $settings['post_item_show'];?>
                      }
                  }
               });
        });


    </script>
    <script type="text/javascript">
          jQuery(function($){
              if(!$('body').hasClass('wk-blog-carousel')){
                  $('body').addClass('wk-blog-carousel');
              }
          });

    </script>

