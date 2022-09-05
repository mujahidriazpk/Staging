<?php
// Silence is golden.

	$settings = $this->get_settings();
    $id = $this->get_id();
?>

<div id=" <?php echo $id; ?>" class="tgx-blog-5">
    <div class="row"> 

        <?php
            $blog_5_post_formats = array('audio', 'image', 'video', 'link', 'gallery');
            $blog_5 = array(
                'cat' => $settings['cat_name'],
                'post_type'         => 'post',
                'post_status'       => 'publish',
                'posts_per_page'    => $settings['blog_5_post_item_show'],
                'ignore_sticky_posts' => 1,
                 'tax_query' => array( array(
                    'taxonomy' => 'post_format',
                    'field' => 'slug',
                    'terms' => array('post-format-aside', 'post-format-gallery', 'post-format-link', 'post-format-image', 'post-format-quote', 'post-format-status', 'post-format-audio', 'post-format-chat', 'post-format-video'),
                    'operator' => 'NOT IN'
                   ) 
                ),
            );

        $blog_5_query = new WP_Query( $blog_5 );
            if($blog_5_query->have_posts()):
                 $i=1; 
                while($blog_5_query->have_posts()): 
                    $blog_5_query->the_post(); 
                
        ?>


    <?php if (has_post_thumbnail()): ?> 
        <div class="col-md-<?php echo esc_attr( $settings['blog_5_layout_item_show']);?>">            
            <article id="post-<?php the_ID(); ?>">
                <div class="card-container">
                    <div class="card u-clearfix">
                    <?php if ($settings['blog_5_image_postion'] == 'left'): ?>
                        <div class="image pull-left
                            <?php if ($settings['blog_5_layout_item_show'] == '12'): ?>
                            <?php echo "col-md-5"; ?>
                            <?php else: ?>
                            <?php echo "col-md-6"; ?>
                            <?php endif; ?>">
                            
                        <a href="<?php the_permalink(); ?>" ><?php the_post_thumbnail('full', array('class' => 'card-media')); ?></a>
                        </div><!-- image -->
                    <?php endif ?>

                        <div class="card-body
                            <?php if ($settings['blog_5_layout_item_show'] == '12'): ?>
                            <?php echo "col-md-7"; ?>
                            <?php else: ?>
                            <?php echo "col-md-6"; ?>
                            <?php endif; ?>">

                            <?php if ($settings['blog_5_count_enable'] == 'yes'): ?>
                                <span class="card-number card-circle subtle"><?php echo "0$i";?></span>
                            <?php endif; ?>

                            <?php if ($settings['blog_5_author_enable'] == 'yes'): ?>          
                                <span class="card-author subtle author-details">
                                    <a href="<?php the_permalink();?>"><?php the_author(); ?></a>
                                </span><!-- author-details -->
                            <?php endif ?>
                            
                             <header class="entry-header">
                                <h4 class="entry-title cart-title">
                                    <a href="<?php the_permalink();?>">
                                        <?php esc_html_e(wp_trim_words( get_the_title(),6, ''), 'widgetkit-for-elementor'); ?>
                                        
                                    </a>
                                </h4>       
                            </header><!-- .entry-header -->

                            <?php if (!in_array(get_post_format(), $blog_5_post_formats)):?>
                                <div class="entry-content">
                                    <p class="card-description content"><?php esc_html_e(wp_trim_words( get_the_content(),20, ''), 'widgetkit-for-elementor'); ?></p>
                                </div><!-- .entry-content -->
                            <?php endif; ?>

                            <?php if ($settings['blog_5_button_enable'] == 'yes'): ?>
                                <div class="card-read">
                                    <a href="<?php the_permalink();?>" class="button"><?php esc_html_e('Read More', 'widgetkit-for-elementor');?>     
                                    </a>
                                </div><!-- card-read-->
                            <?php endif ?>

                            <?php if ($settings['blog_5_count_enable'] == 'yes'): ?>
                                <?php $count_posts = wp_count_posts();?>
                                <span class="card-tag card-circle subtle"><?php echo $count_posts->publish;?></span>
                            <?php endif; ?>
                        </div><!-- card-body -->

                        <?php if ($settings['blog_5_image_postion'] == 'right'): ?>
                            <div class="image pull-right text-right
                                <?php if ($settings['blog_5_layout_item_show'] == '12'): ?>
                                <?php echo "col-md-5"; ?>
                                <?php else: ?>
                                <?php echo "col-md-6"; ?>
                                <?php endif; ?>">
                                
                            <a href="<?php the_permalink(); ?>" ><?php the_post_thumbnail('full', array('class' => 'card-media')); ?></a>
                            </div><!-- image-->
                        <?php endif; ?>
                    </div><!-- card -->
                </div><!-- cart-container -->
            </article><!-- post -->
        </div><!-- col-md- -->

        <?php endif; $i++;?>
        <?php endwhile; wp_reset_postdata();  endif; ?>
    </div>  <!-- row -->
</div><!-- tgx-blog-5 -->

    <script type="text/javascript">
          jQuery(function($){
              if(!$('body').hasClass('wk-blog-image')){
                  $('body').addClass('wk-blog-image');
              }
          });

    </script>

