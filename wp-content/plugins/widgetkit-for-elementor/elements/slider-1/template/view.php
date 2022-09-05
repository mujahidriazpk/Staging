<?php
   // Silence pro 1

    $settings = $this->get_settings();
    $id = $this->get_id();

    $header_tag_arr_for_slider_1 = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'];
    $slider_1_header_tag = esc_html(wp_kses($settings['heading_tag'], $header_tag_arr_for_slider_1));

?>


    <div id="tgx-hero-unit" class="tgx-slider-1">
        <div id="<?php echo $id; ?>" class="carousel slide" data-ride="carousel" data-interval="
             <?php if ($settings['slider_interval']):
                 echo $settings['slider_interval'];
             endif; ?>" <?php echo ($settings['slider_data_pause'] == 'yes') ? '' : 'data-pause="false"'; ?>>
             <?php if ($settings['dot_enable_1']):?>
                <!-- Indicators -->
                <ol class="carousel-indicators">
                    <?php for($i = 0; $i < count($settings['slider_option']); $i++):
                        $active = $i == 0 ? 'active' : '';?>
                    <li data-target="#<?php echo $id; ?>" data-slide-to="<?php echo $i;?>" class="<?php echo $active;?>"></li>
                    <?php endfor; ?>
                </ol>
            <?php endif; ?>
            <!-- Wrapper for slides -->
            <div class="carousel-inner ">
                <?php  $i = 0;?>
                <?php foreach ( $settings['slider_option'] as $slider ) : ?>
                    <?php $active = $i == 0 ? 'active' : ''; ?>
                    <div class="item <?php echo $active;?> slider-<?php echo $i;?>" style="background-image: url('<?php echo $slider['slider_image']['url'];?>');">

                        <div class="carousel-caption ">
                            <?php if ($slider['title']):?>
                                <<?php echo $slider_1_header_tag;?> class="slider-title animated animate-delay-1 <?php echo $slider['title_animation']; ?>">
                                        <?php echo $slider['title']; ?>                    
                                </<?php echo $slider_1_header_tag;?>>
                            <?php endif; ?>


                            <?php if ($slider['slider_content']):?>
                                <p class="slider-description animated animate-delay-2
                                    <?php echo $slider['content_animation']; ?> ">
                                    <?php echo $slider['slider_content']; ?>
                                </p>
                                        <?php 
                             //$shortcode = $slider['slider_content'];
                             //echo do_shortcode($shortcode);

                             ?>
                            <?php endif; ?>
                            
                            <?php if ($slider['btn_text']):?>
                                <span class="slider-action">
                                    <a class="btn btn-slider animated animate-delay-3 <?php echo $slider['btn_animation']; ?>" href="<?php echo $slider['btn_link']['url']; ?>"> <?php echo $slider['btn_text']; ?>
                                    </a>
                                </span>
                            <?php endif; ?>
             
                        </div>
                    </div><!-- /.item -->

                <?php  $i++; endforeach; ?>

                </div> <!-- /.inner -->
                <?php if ($settings['arrow_enable_1']):?>
                    <a class="hidden-xs left carousel-control" href="#<?php echo $id; ?>" data-slide="prev">
                        <i class="fas fa-long-arrow-alt-left"></i>
                    </a>
                    <a class="hidden-xs right carousel-control" href="#<?php echo $id; ?>" data-slide="next">
                        <i class="fas fa-long-arrow-alt-right "></i>
                    </a>
                <?php endif; ?>
        </div><!-- /.id -->
    </div> <!-- /#hero-unit -->

        <script type="text/javascript">
            jQuery(function($){
                if(!$('body').hasClass('wk-slider-1')){
                    $('body').addClass('wk-slider-1');
                }
            });

        </script>

