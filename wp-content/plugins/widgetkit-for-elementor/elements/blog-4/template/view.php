<?php
// Silence is golden.

	$settings = $this->get_settings();
    $id = $this->get_id();
?>

<div id=" <?php echo $id; ?>" class="tgx-blog-4">
    <div class="row">

        <?php
            $blog_post_formats = array('audio', 'image', 'video', 'link', 'gallery');
            $blog_4 = array(
                'cat' => $settings['cat_name'],
                'post_type'         => 'post',
                'post_status'       => 'publish',
                'posts_per_page'    => $settings['blog_4_post_item_show'],
                'ignore_sticky_posts' => 1,
                 'tax_query' => array( array(
                    'taxonomy' => 'post_format',
                    'field' => 'slug',
                    'terms' => array('post-format-aside', 'post-format-gallery', 'post-format-link', 'post-format-image', 'post-format-quote', 'post-format-status', 'post-format-audio', 'post-format-chat', 'post-format-video'),
                    'operator' => 'NOT IN'
                   ) 
                ),
            );

        $blog_4_query = new WP_Query( $blog_4 );
            if($blog_4_query->have_posts()):
                $i=1; 
                while($blog_4_query->have_posts()): 
                    $blog_4_query->the_post(); 
                
        ?>


    <?php if (has_post_thumbnail()): ?> 
        <?php $backgroundImg = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' ); ?> 
            <div class="col-md-<?php echo esc_attr( $settings['blog_4_layout_item_show']);?>">
            <?php if ($settings['blog_4_hover_effect'] == 'background_color'): ?>
                
           
                <article id="post-<?php the_ID(); ?>" <?php post_class('hover-effect-1'); ?>> 
                    <div class="wrapper" style="
                        background: url('<?php echo $backgroundImg[0]; ?>');
                        background-size: cover;
                        background-repeat: no-repeat;
                        background-position: center;
                    ">

                    <?php if ($settings['blog_4_date_enable'] == 'yes'): ?>
                        <div class="date">
                            <span>
                                <a href="<?php the_permalink();?>">
                                    <?php esc_html_e(date_i18n((get_option('date_format'))), 'widgetkit-for-elementor'); ?>
                                </a>
                            </span>     
                        </div> <!-- date -->
                    <?php endif ?>


                        <div class="data">
                            <div class="content">
                                <?php if ($settings['blog_4_author_enable'] == 'yes'): ?>
                                    <span class="author-details">
                                        <a href="<?php the_author_link(); ?>"><?php the_author(); ?></a>
                                    </span><!-- author-details -->
                                <?php endif; ?>

                                 <header class="entry-header">
                                    <h4 class="entry-title">
                                        <a href="<?php the_permalink();?>">
                                            <?php esc_html_e(wp_trim_words( get_the_title(),6, ''), 'widgetkit-for-elementor'); ?>
                                            
                                        </a>
                                    </h4>       
                                </header><!-- .entry-header -->

                                <?php if (!in_array(get_post_format(), $blog_post_formats)):?>
                                    <div class="entry-content">
                                        <p><?php esc_html_e(wp_trim_words( get_the_content(),$settings['blog_4_show_content'], ''), 'widgetkit-for-elementor'); ?></p>
                                    </div><!-- .entry-content -->
                                <?php endif; ?>

                            <?php if ($settings['blog_4_meta_enable'] == 'yes'): ?>
                                
                          
                                <label for="show-menu-<?php echo $i;?>" class="menu-button">
                                <span></span>
                                </label>
                            </div> <!-- content -->


                            <input type="checkbox" id="show-menu-<?php echo $i;?>" />
                            <ul class="menu-content">
                                <li>
                                    <a href="<?php the_permalink();?>" class="fa fa-bookmark-o"></a>
                                </li>
                                <?php $count_posts = wp_count_posts();?>
                                <li><a href="<?php the_permalink();?>" class="fa fa-eye"><span><?php echo $count_posts->publish;?></span></a></li>
                                <li><a href="<?php the_permalink();?>" class="fa fa-comment-o"><span><?php comments_number( '0 ', '1', '% Comments' ); ?></span></a></li>
                            </ul>
                              <?php endif ?>
                        </div> <!-- data -->
                    </div> <!-- wrapper -->
                </article><!-- hover effect 1-->

            <?php elseif($settings['blog_4_hover_effect'] == 'transparent'): ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class('hover-effect-2'); ?>> 
                    <div class="wrapper" style="
                        background: url('<?php echo $backgroundImg[0]; ?>');
                        background-size: cover;
                        background-repeat: no-repeat;
                        background-position: center;
                    ">
                    <div class="header">
                        <?php if ($settings['blog_4_date_enable'] == 'yes'): ?>
                            <div class="date">
                                <span>
                                    <a href="<?php the_permalink();?>">
                                        <?php echo get_the_date(); ?>
                                    </a>
                                </span>     
                            </div> <!-- date -->
                        <?php endif ?>

                        <?php if ($settings['blog_4_meta_enable'] == 'yes'): ?>
                            <ul class="menu-content">
                                <li>
                                    <a href="<?php the_permalink();?>" class="fa fa-bookmark-o"></a>
                                </li>
                                <?php $count_posts = wp_count_posts();?>
                                <li><a href="<?php the_permalink();?>" class="fa fa-eye"><span><?php echo $count_posts->publish;?></span></a></li>
                                <li><a href="<?php the_permalink();?>" class="fa fa-comment-o"><span><?php comments_number( '0 ', '1', '% Comments' ); ?></span></a></li>
                            </ul>
                        <?php endif ?>
                    </div> <!-- Header -->

                        <div class="data">
                            <div class="content">
                                <?php if ($settings['blog_4_author_enable'] == 'yes'): ?>
                                    <span class="author-details">
                                        <a href="<?php the_author_link(); ?>"><?php the_author(); ?></a>
                                    </span><!-- author-details -->
                                <?php endif; ?>

                                 <header class="entry-header">
                                    <h4 class="entry-title">
                                        <a href="<?php the_permalink();?>">
                                            <?php esc_html_e(wp_trim_words( get_the_title(),6, ''), 'widgetkit-for-elementor'); ?>
                                            
                                        </a>
                                    </h4>       
                                </header><!-- .entry-header -->

                                <?php if (!in_array(get_post_format(), $blog_post_formats)):?>
                                    <div class="entry-content">
                                        <p><?php esc_html_e(wp_trim_words( get_the_content(), $settings['blog_4_show_content'], ''), 'widgetkit-for-elementor'); ?>
                                            
                                        </p>

                                    </div><!-- .entry-content -->
                                <?php endif; ?>

                                <?php if ($settings['blog_4_button_enable'] == 'yes'): ?>
                                    <a href="<?php the_permalink();?>" class="button">
                                        <?php if ($settings['blog_4_button_enable'] == 'yes'): ?>
                                            <?php echo esc_html($settings['blog_4_btn_text']);?>
                                        <?php endif; ?>     
                                    </a>
                                <?php endif ?>

                            </div> <!-- content -->
                        </div> <!-- data -->
                    </div> <!-- wrapper 2 -->
                </article> <!-- Hover effect 2 -->
            <?php else: ?>
                <div></div>
            <?php endif ?>
        </div>

        <?php endif; $i++;?>
        <?php endwhile; wp_reset_postdata(); endif; ?>
    </div> <!-- row -->
</div><!-- tgx-blog-4 -->

    <script type="text/javascript">
          jQuery(function($){
              if(!$('body').hasClass('wk-blog-hover')){
                  $('body').addClass('wk-blog-hover');
              }
          });

    </script>

