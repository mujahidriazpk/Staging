<?php
// Silence is golden.

    $settings = $this->get_settings();

?>

    <div class="tgx-blog-3">
       <div class="row">
            <?php
                $post_formats = array('audio', 'image', 'video', 'link', 'gallery');
                $post = array(
                            'cat' => $settings['cat_name'],
                            'post_type'         => 'post',
                            'post_status'       => 'publish',
                            'ignore_sticky_posts' => 1,
                            'posts_per_page'    => $settings['blog_3_post_show'],
                             'tax_query' => array( array(
                                'taxonomy' => 'post_format',
                                'field' => 'slug',
                                'terms' => array('post-format-aside', 'post-format-gallery', 'post-format-link', 'post-format-image', 'post-format-quote', 'post-format-status', 'post-format-audio', 'post-format-chat', 'post-format-video'),
                                'operator' => 'NOT IN'
                               ) 
                            ),
                        );

                $post_query = new WP_Query( $post );
                    if($post_query->have_posts()):
                        while($post_query->have_posts()): 
                            $post_query->the_post(); 
            ?>

                     


            <?php if ( has_post_thumbnail() ): ?>
                <div class="items">
                    <div class="col-md-<?php echo $settings['blog_3_post_column'];?> col-<?php echo $settings['blog_3_post_show'];?>">
                        <div class="blog-wrapper ">
                            <figure class="overlay overlay-hover">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail(); ?>
                                </a>
                            </figure> <!-- .overlay -->

                            <div class="blog-info">

                            <?php if ($settings['author_position'] == 'top'): ?>
                                <div class="author">
                                    <span class="author-img">
                                        <?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?>
                                    </span>
                                    <span class="author-info"><?php esc_html_e('by - ', 'widgetkit-for-elementor');?><?php the_author();?></span>
                                </div> <!-- .author -->
                            <?php endif; ?>

                            <h3 class="title ">
                                <a href="<?php the_permalink();?>"><?php echo wp_trim_words( get_the_title(),4, ''); ?></a>
                            </h3>

                            <?php if ($settings['author_position'] == 'middle'): ?>
                                <div class="author">
                                    <span class="author-img">
                                        <?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?>
                                    </span>
                                    <span class="author-info"><?php esc_html_e('by - ', 'widgetkit-for-elementor');?><?php the_author();?></span>
                                </div> <!-- .author -->
                            <?php endif; ?>
                            

                                <p class="desc">
                                     <?php echo wp_trim_words( get_the_content(),10, ''); ?>
                                </p>

                            <?php if ($settings['author_position'] == 'bottom'): ?>
                                <div class="author">
                                    <span class="author-img">
                                        <?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?>
                                    </span>
                                    <span class="author-info"><?php esc_html_e('by - ', 'widgetkit-for-elementor');?><?php the_author();?></span>
                                </div> <!-- .author -->
                            <?php endif; ?>
                            
                            </div> <!-- .blog-info -->
                        </div> <!-- .blog wrapper -->
                    </div> <!-- .itmes -->
                </div> <!-- col-md-3/4/6 -->
            <?php endif; endwhile; wp_reset_postdata(); endif; ?>
        </div><!-- /.row -->    
    </div> <!-- /.section -->

    <script type="text/javascript">
          jQuery(function($){
              if(!$('body').hasClass('wk-blog-revert')){
                  $('body').addClass('wk-blog-revert');
              }
          });

    </script>