<?php
    $contents = $settings = $this->get_settings(); 
    $id = $this->get_id();
    $header_tag_arr_for_content_carousel = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'];
    $content_carousel_custom_header_tag = esc_html(wp_kses($contents['custom_header_tag'], $header_tag_arr_for_content_carousel));
    $content_carousel_post_header_tag = esc_html(wp_kses($contents['post_header_tag'], $header_tag_arr_for_content_carousel));
    use Elementor\Group_Control_Image_Size;
    ?>

        <div class="content-carousel" wk-slider="center:<?php echo $contents['center_mode_enable'] == 'yes'? 'true' :'false'; ?>; sets:<?php echo $contents['set_mode_enable'] == 'yes'? 'true' :'false'; ?>; autoplay:<?php echo $contents['autoplay_mode_enable'] == 'yes'? 'true' :'false'; ?>; autoplay-interval:<?php echo $contents['content_interval_option'];?>;">
            <div class="wk-visible-toggle wk-light <?php echo $contents['arrow_position'] == 'in'? 'wk-position-relative' : ' '; ?> " tabindex="-1">
                <?php if ($contents['center_mode_enable'] == 'yes'): ?>
                    <ul class="wk-grid-<?php echo $contents['column_gap']?> wk-slider-items wk-child-width-1-2@s" wk-grid>
                <?php else: ?>
                      <ul class="wk-grid-<?php echo $contents['column_gap']?> 
                      wk-slider-items 
                      wk-child-width-1-<?php echo $contents['item_column'];?>@l
                      wk-child-width-1-<?php echo is_int($contents['item_column_tablet']) ? $contents['item_column_tablet'] : 2 ;?>@m 
                      wk-child-width-1-<?php echo is_int($contents['item_column_mobile']) ? $contents['item_column_mobile'] : 1 ;?>@s" 
                      wk-grid>
                <?php endif; ?>
                    <?php if ($contents['item_option'] == 'custom_post'): ?>
                        <?php foreach ( $contents['custom_content'] as $content ) : ?>
                             <li class="wk-flex wk-flex-center wk-grid-match">
                                <div class="wk-card <?php echo $content['content_meta'] || $content['content_title'] || $content['content_content']? 'wk-card-default' : '';?> wk-margin-small-bottom">
                                    <?php if($content['content_thumb_image']['url']):?>
                                        <?php if($contents['thumbnail_position'] == 'top'): ;?>
                                            <div class="wk-card-media-top wk-overflow-hidden">
                                                <a class="wk-display-block" href="<?php echo $content['content_demo_link']['url']; ?>" <?php echo $content['content_demo_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                                                    <?php if($content['content_thumb_image']['id']): ?>
                                                        <img src="<?php echo Group_Control_Image_Size::get_attachment_image_src($content['content_thumb_image']['id'], 'cc_image', $contents ); ?>" alt="<?php echo $content['content_title']; ?>">
                                                    <?php else:?>
                                                        <img src="<?php echo $content['content_thumb_image']['url'];?>" alt="<?php echo $content['content_title']; ?>">  
                                                    <?php endif;?> 
                                                </a> 
                                            </div>
                                        <?php endif; ?> 
                                    <?php endif; ?> 
                                    <?php if( $content['content_meta'] || $content['content_title'] || $content['content_content']):?>
                                    <div class="wk-card-body">
                                        <?php if ($content['content_meta']): ?>
                                            <span class="wk-text-meta wk-inline-block"><?php echo $content['content_meta']; ?></span>
                                        <?php endif; ?>
                                        <?php if ($content['content_title']): ?>
                                            
                                            <<?php echo $content_carousel_custom_header_tag;?>  class="wk-card-title wk-margin-remove">
                                                <?php if ($content['content_demo_link']): ?>
                                                     <a href="<?php echo $content['content_demo_link']['url']; ?>" <?php echo $content['content_demo_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>><?php echo $content['content_title']; ?></a>
                                                <?php else: ?>
                                                        <?php echo $content['content_title']; ?>
                                                <?php endif; ?>  
                                            </<?php echo $content_carousel_custom_header_tag;?>>  
                                        <?php endif; ?>
                                        <?php if ($content['content_content']): ?>
                                            <p class=" wk-margin-small-bottom"><?php echo $content['content_content']; ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?> 
                                    <?php if($contents['thumbnail_position'] == 'bottom'):?>
                                        <div class="wk-card-media-bottom wk-overflow-hidden">
                                            <a class="wk-display-block" href="<?php echo $content['content_demo_link']['url']; ?>" <?php echo $content['content_demo_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                                                <?php if($content['content_thumb_image']['id']): ?>
                                                    <img src="<?php echo Group_Control_Image_Size::get_attachment_image_src($content['content_thumb_image']['id'], 'cc_image', $contents ); ?>" alt="<?php echo $content['content_title']; ?>">
                                                <?php else:?>
                                                    <img src="<?php echo $content['content_thumb_image']['url'];?>" alt="<?php echo $content['content_title']; ?>">  
                                                <?php endif;?> 
                                            </a> 
                                        </div>
                                    <?php endif; ?> 
                                
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                            <?php
                                $content = array(
                                    'post_type' => 'post',
                                    'category__in' => $contents['cat_multiple_id'],
                                    'order' => $contents['items_order'],
                                    'orderby' => $contents['items_orderby'],
                                    'post_status'       => 'publish',
                                    'posts_per_page'    =>$contents['post_show'],
                                    'ignore_sticky_posts' => 1
                                );
                                $content_query = new WP_Query( $content );
                                if($content_query->have_posts()):
                                    while($content_query->have_posts()): 
                                        $content_query->the_post(); 
                            ?>
                            <li class="wk-flex wk-flex-center wk-grid-match">
                                <div class="wk-card wk-card-default wk-margin-small-bottom">
                                  <?php if ( has_post_thumbnail() ): ?>
                                        <?php if($contents['thumbnail_position'] == 'top'): ;?>
                                            <div class="wk-card-media-top wk-overflow-hidden">
                                                <a class="wk-display-block" href="<?php the_permalink();?>">
                                                    <?php //the_post_thumbnail($contents['thumbnail_size']);?> 
                                                    <img src="<?php echo Group_Control_Image_Size::get_attachment_image_src(get_post_thumbnail_id(), 'cc_image', $contents );?>" alt="<?php the_title(); ?>">
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <div class="wk-card-body">
                                        <?php if ($contents['meta_enable'] == 'yes'): ?>
                                            <?php if (has_category()):?>
                                                <span class="wk-text-meta wk-inline-block"><?php the_category(', ') ?></span>
                                            <?php endif; ?>
                                            
                                        <?php endif; ?>
                                       
                                        <<?php echo $content_carousel_post_header_tag;?>  class="wk-card-title wk-margin-remove">    
                                            <a href="<?php the_permalink();?>"><?php echo wp_trim_words( get_the_title(), $contents['title_word'], ' ' );?>    
                                            </a>

                                        </<?php echo $content_carousel_post_header_tag;?>>
                                        <?php if ( $contents['content_enable'] == 'yes' ): ?>
                                            <p class="wk-margin-small-bottom">
                                                <?php echo wp_trim_words( get_the_content(), $contents['content_word'], ' ' );?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <?php if($contents['thumbnail_position'] == 'bottom'): ;?>
                                        <div class="wk-card-media-bottom wk-overflow-hidden">
                                            <a class="wk-display-block" href="<?php the_permalink();?>">
                                                <?php //the_post_thumbnail($contents['thumbnail_size']);?> 
                                                <img src="<?php echo Group_Control_Image_Size::get_attachment_image_src(get_post_thumbnail_id(), 'cc_image', $contents );?>" alt="<?php the_title(); ?>">
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </li>
                     <?php  endwhile; endif; wp_reset_postdata(); ?>
                    <?php endif; ?>
                </ul>
                <?php if ($contents['arrow_enable'] == 'yes'):?>
                    <a class=" <?php echo $contents['arrow_position'] == 'out'? 'wk-position-center-left-out' : 'wk-position-center-left'; ?> wk-position-medium wk-slidenav-small <?php echo $contents['arrow_on_hover'] == 'yes'? 'wk-hidden-hover' : ''; ?> " href="#" wk-slidenav-previous wk-slider-item="previous"></a>
                    <a class="<?php echo $contents['arrow_position'] == 'out'? 'wk-position-center-right-out' : 'wk-position-center-right'; ?> wk-position-medium  wk-slidenav-small <?php echo $contents['arrow_on_hover'] == 'yes'? 'wk-hidden-hover' : ''; ?>  " href="#" wk-slidenav-next wk-slider-item="next"></a>
                <?php endif; ?>

            </div>
                <?php if ($contents['dot_enable'] == 'yes'):?>
                    <ul class="wk-slider-nav wk-dotnav wk-flex-<?php echo $contents['dot_nav_align'];?> wk-margin-medium-top"></ul>
                <?php endif; ?>

        </div>

        <script>
            jQuery(function($){
                if(!$('body').hasClass('wk-content-carousel')){
                    $('body').addClass('wk-content-carousel');
                }
            });
        </script>

