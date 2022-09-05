<?php
// Silence is golden.

    $settings = $this->get_settings();

    // Global Options
    global $post;

?>

    <div class="tgx-blog-2">
         <div class="row">
            <?php if ($settings['layout_position'] == 'left'): ?>
            <div class="col-md-4">
                <?php
                    $post_formats = array('audio', 'image', 'video', 'link', 'gallery');
                        $blog = array(
                            'cat' => $settings['cat_name'],
                            'post_type'         => 'post',
                            'post_status'       => 'publish',
                            'posts_per_page'    => $settings['standard_post_show'],
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
                    <?php if ( has_post_thumbnail() ):?>
                        <aside id="post-<?php the_ID(); ?>" <?php post_class('custom-standard-post standard-post clearfix'); ?>>
                            
                            <figure class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php  the_post_thumbnail();?>
                                </a>
                            </figure><!-- .post thubmanail -->

                            <div class="blog-details">
                                <header class="entry-header">
                                    <h4 class="entry-title">
                                        <a href="<?php the_permalink();?>">
                                            <?php esc_html_e(wp_trim_words( get_the_title(),4, ''), 'widgetkit-for-elementor'); ?>
                                        </a>
                                    </h4>       
                                </header><!-- .entry-header -->
                                <footer class="entry-footer">
                                    <div class="content-btn">
                                        <a href="<?php the_permalink(); ?>" class="btn-readmore"><?php esc_html_e('Read More', 'widgetkit-for-elementor');?>
                                        </a>                    
                                    </div>
                                </footer> <!-- .entry-footer -->
                            </div><!-- .blog-details-->
                        </aside><!-- #post-## -->
                    <?php endif; ?>
                <?php endwhile; wp_reset_postdata(); endif; ?>
            </div> <!-- /.col-md-4 -->
            <?php endif; ?>


            <div class="col-md-8">
                <?php 
                    $args = array( 
                         $post_formats = array('audio', 'image', 'video', 'link', 'gallery'),
                        'numberposts' => $settings['sticky_post_show'], 
                        'post__in'  => get_option( 'sticky_posts' ),
                        'ignore_sticky_posts' => 1,
                        'post_status'=>"publish",
                        'post_type'=>"post",
                        'orderby'=>"post_date",
                               'tax_query' => array( array(
                                        'taxonomy' => 'post_format',
                                        'field' => 'slug',
                                        'terms' => array('post-format-aside', 'post-format-gallery', 'post-format-link', 'post-format-image', 'post-format-quote', 'post-format-status', 'post-format-audio', 'post-format-chat', 'post-format-video'),
                                        'operator' => 'NOT IN'
                                       ) 
                                    ),
                        );
                    $postslist = get_posts( $args ); ?>

                <?php foreach ($postslist as $post) :  setup_postdata($post); ?>
                    <article class="custom-sticky-post">

                        <?php if ( has_post_thumbnail() ):?>
                            <figure class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php  the_post_thumbnail();?>
                                </a>
                                <?php if ($settings['date_enable']): ?>
                                    
                                <span class="date-format">
                                   <?php esc_html_e(date_i18n((get_option('date_format'))), 'widgetkit-for-elementor'); ?>
                                </span>
                                <?php endif ?>

                            </figure>
                        <?php endif; ?> 

                        <div class="blog-details">
                            <?php if ($settings['meta_position_2'] == 'top'): ?>
                                <span class="author-meta">
                                    <?php esc_html_e('By :', 'widgetkit-for-elementor');?>
                                     <a class="author" href="<?php the_permalink(); ?>"> <?php the_author(); ?></a>
                                </span>
                            <?php endif ?>
                             <h2 class="entry-header">
                                 <a class="entry-title" href="<?php the_permalink();?>"><?php esc_html_e(wp_trim_words( get_the_title(),8, ''), 'widgetkit-for-elementor'); ?></a>
                            </h2>
                            <?php if ($settings['meta_position_2'] == 'bottom'):?>
                                <span class="author-meta">
                                    <?php esc_html_e('By :', 'widgetkit-for-elementor');?>
                                     <a class="author" href="<?php the_permalink(); ?>"> <?php the_author(); ?></a>
                                </span>
                           <?php endif; ?>

                            <?php if (!in_array(get_post_format(), $post_formats)):?>
                                <div class="entry-content">
                                    <p><?php esc_html_e(wp_trim_words( get_the_content(),35, ''), 'widgetkit-for-elementor'); ?></p>
                                </div><!-- .entry-content -->
                            <?php endif; ?>
                            
                            <footer class="entry-footer">
                                <div class="content-btn">
                                    <a href="<?php the_permalink(); ?>" class="btn-readmore"><?php esc_html_e('Read More', 'widgetkit-for-elementor');?>
                                    </a>                    
                                </div>
                            </footer> <!-- .entry-footer -->
                        </div>

                    </article><!-- .sticky-post -->
                <?php endforeach; ?>
           </div><!-- .col-md-8 -->

            <?php if ($settings['layout_position'] == 'right'): ?>
                <div class="col-md-4">
                <?php
                    $post_formats = array('audio', 'image', 'video', 'link', 'gallery');
                        $blog = array(
                            'post_type'         => 'post',
                            'post_status'       => 'publish',
                            'posts_per_page'    => $settings['standard_post_show'],
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
                    <?php if ( has_post_thumbnail() ):?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('standard-post clearfix'); ?>>
                            <figure class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php  the_post_thumbnail();?>
                                </a>
                            </figure><!-- .post thubmanail -->
                            

                            <div class="blog-details">
                                <header class="entry-header">
                                    <h4 class="entry-title">
                                        <a href="<?php the_permalink();?>">
                                            <?php esc_html_e(wp_trim_words( get_the_title(),4, ''), 'widgetkit-for-elementor'); ?>
                                        </a>
                                    </h4>       
                                </header><!-- .entry-header -->
                                <footer class="entry-footer">
                                    <div class="content-btn">
                                        <a href="<?php the_permalink(); ?>" class="btn-readmore"><?php esc_html_e('Read More', 'widgetkit-for-elementor');?>
                                        </a>                    
                                    </div>
                                </footer> <!-- .entry-footer -->
                            </div><!-- .blog-details-->
                        </article><!-- #post-## -->
                    <?php endif; ?>
                <?php endwhile; wp_reset_postdata(); endif; ?>
            </div> <!-- /.col-md-4 -->
        <?php endif ?>
    </div><!-- /.row -->     
</div> <!-- /.section -->

    <script type="text/javascript">
          jQuery(function($){
              if(!$('body').hasClass('wk-blog-sidebar')){
                  $('body').addClass('wk-blog-sidebar');
              }
          });

    </script>
