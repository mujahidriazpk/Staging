<?php
// Silence is golden.

	$settings = $this->get_settings();


?>

    <div class="project">
        <div class="tgx-project">

            <?php if ($settings['item_option'] == 'custom_post'): ?>

                <?php foreach ( $settings['custom_content'] as $project ) : ?>
                    <figure class="project-wrap">
                        <div class="project-image" >
                            <?php if($project['project_thumb_image']):?>
                                <a href="<?php echo $project['project_demo_link']; ?>">
                                    <img src="<?php echo $project['project_thumb_image']['url'];?>" alt="<?php echo $project['project_title']; ?>">  
                                    
                                </a>
                                  
                            <?php endif; ?> 
                        </div>
                        <figcaption class="text-center">
                            <?php if ($project['project_title']): ?>
                                <h5 class="title">
                                    <a target="_blank" href="<?php echo $project['project_demo_link']; ?>"><?php echo $project['project_title']; ?>
                                    </a> 
                                </h5>       
                            <?php endif ?>    
                        </figcaption>                                                          
                    </figure>
                <?php endforeach; ?>


            <?php elseif ($settings['item_option'] == 'sticky_post'): ?>

                <?php $sticky = array(
                    'post_type' => 'post',
                    'posts_per_page' => $settings['sticky_post_show'],
                    'post__in'  => get_option( 'sticky_posts' ),
                    'ignore_sticky_posts' => 1
                );
                $sticky_post = new WP_Query( $sticky );
                        if($sticky_post->have_posts()):
                            while($sticky_post->have_posts()): 
                                $sticky_post->the_post(); 
                ?>


                <?php if ( has_post_thumbnail() ): ?>
                    <figure class="project-wrap">
                        <div class="project-image" >
                            <a href="<?php the_permalink();?>">
                                <?php the_post_thumbnail();?> 
                            </a>
                            
                        </div>
                        <figcaption class="text-center">
                            <h5 class="title">
                                <a href="<?php the_permalink();?>"><?php echo wp_trim_words( get_the_title(), 3, ' ' );?></a>
                            </h5>
                            
                        </figcaption>                                                          
                    </figure>
                <?php endif; endwhile; endif; wp_reset_postdata(); ?>

            <?php else: ?>

                <?php
                       $project = array(
                            'post_type' => 'post',
                            'order' => $settings['items_order'],
                            'orderby' => $settings['items_orderby'],
                            'post_status'       => 'publish',
                            'posts_per_page'    =>$settings['standard_post_show'],
                            'ignore_sticky_posts' => 1
                        );
                        $project_query = new WP_Query( $project );
                        if($project_query->have_posts()):
                            while($project_query->have_posts()): 
                                $project_query->the_post(); 
                    ?>
                    <?php //if ( has_post_thumbnail() ): ?>
                    <figure class="project-wrap">
                        <div class="project-image" >
                            <a href="<?php the_permalink();?>">
                                <?php the_post_thumbnail();?> 
                            </a>
                        </div>
                        <figcaption class="text-center">
                            <h5 class="title">
                                <a href="<?php the_permalink();?>"><?php echo wp_trim_words( get_the_title(), $settings['title_word'], ' ' );?>    
                                </a>
                            </h5>
                            
                        </figcaption>                                                          
                    </figure>
                <?php  endwhile; endif; wp_reset_postdata(); ?>
            <?php endif ?>
        </div><!-- /.perfecto-testimonial -->              
    </div><!-- /.section -->

    <script type="text/javascript">
          jQuery(function($){
              if(!$('body').hasClass('wk-post-carousel')){
                  $('body').addClass('wk-post-carousel');
              }
          });

    </script>

