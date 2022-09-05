<?php
// Silence is golden.
    $settings = $this->get_settings();
    $id = $this->get_id();
    $header_tag_arr_for_slider_2 = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'];
    $slider_2_header_tag = esc_html(wp_kses($settings['heading_tag'], $header_tag_arr_for_slider_2));
?>
    <!-- Animation Slider start -->
    <section id="tgx-hero-unit" class="tgx-slider-2">
        <div class="row">
            <div id="<?php echo $id; ?>" class="carousel slide" data-ride="carousel" data-interval="
                 <?php if ($settings['slider_interval']):
                     echo $settings['slider_interval'];
                 endif; ?>
                " <?php echo ($settings['slider_data_pause'] == 'yes') ? '' : 'data-pause="false"'; ?>>
                 <?php if ($settings['dot_enable']):?>
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
                        <div class="item <?php echo $active;?> slider-<?php echo $i;?>" style="background-image: url('<?php echo $slider['slider_image_2']['url'];?>');">

                        <?php if ($settings['layout_align'] == 'left'): ?>
                            <?php if ($slider['slider_animation_image']['url']): ?> 
                                <div class="col-md-8 col-xs-12 col-sm-12">
                            <?php else: ?>
                                <div class="col-md-12 col-xs-12 col-sm-12">
                            <?php endif; ?>
                                <div class="carousel-caption ">
                                    <?php if ($slider['title']):?>
                                        <<?php echo $slider_2_header_tag;?> class="slider-title animated animate-delay-1 <?php echo $slider['title_animation']; ?>">
                                            <?php echo $slider['title']; ?>
                                        </<?php echo $slider_2_header_tag;?>>
                                    <?php endif; ?>


                                    <?php if ($slider['slider_content']):?>
                                        <p class="slider-description animated animate-delay-2
                                           <?php echo $slider['content_animation']; ?> ">
                                            <?php echo $slider['slider_content']; ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <?php if($slider['btn_text']): ?>
                                    <span class="slider-action">
                                        <a class="btn btn-slider animated animate-delay-3 <?php echo $slider['btn_animation']; ?>" href="<?php echo $slider['btn_link']['url']; ?>"> <?php echo $slider['btn_text']; ?>
                                        </a>
                                    </span>
                                    <?php endif; ?>    
                                </div>
                            </div>

                            <?php if ($slider['slider_animation_image']['url']): ?>        
                                <div class="col-md-4 col-xs-12 col-sm-12">
                                    <div class="carousel-image animated animate-delay-4 <?php echo $slider['image_animation']; ?>">
                                        <img src="<?php echo $slider['slider_animation_image']['url'];?>" alt="animation-image" class="img-responsive">
                                    </div>
                                </div>
                            <?php endif ?>

                        <?php else: ?>

                            <?php if ($slider['slider_animation_image']['url']): ?>        
                                <div class="col-md-4 col-xs-12 col-sm-12">
                                    <div class="carousel-image animated animate-delay-4 <?php echo $slider['image_animation']; ?>">
                                        <img src="<?php echo $slider['slider_animation_image']['url'];?>" alt="animation-image" class="img-responsive">
                                    </div>
                                </div>
                            <?php endif ?>

                            <?php if ($slider['slider_animation_image']['url']): ?> 
                                <div class="col-md-8 col-xs-12 col-sm-12">
                            <?php else: ?>
                                <div class="col-md-12 col-xs-12 col-sm-12">
                            <?php endif; ?>
                                <div class="carousel-caption ">
                                    <?php if ($slider['title']):?>
                                        <h2 class="slider-title animated animate-delay-1 <?php echo $slider['title_animation']; ?>">
                                            <?php echo $slider['title']; ?>
                                        </h2>
                                    <?php endif; ?>

                                    <?php if ($slider['slider_content']):?>
                                        <p class="slider-description animated animate-delay-2
                                           <?php echo $slider['content_animation']; ?> ">
                                            <?php echo $slider['slider_content']; ?>
                                        </p>
                                    <?php endif; ?>

                                    <span class="slider-action">
                                        <a class="btn btn-slider animated animate-delay-3 <?php echo $slider['btn_animation']; ?>" href="<?php echo $slider['btn_link']['url']; ?>"> <?php echo $slider['btn_text']; ?>
                                        </a>
                                    </span>
                     
                                </div>
                            </div>                
                        <?php endif ?>                
                    </div>
                <?php $i++;  endforeach; ?>
            </div>


            <?php if ($settings['arrow_enable']):?>
                <!-- Left and right controls -->
                <a class="left carousel-control hidden-xs" href="#<?php echo $id; ?>" role="button" data-slide="prev">
                    <span class="fa fa-angle-left" aria-hidden="true"></span>
                    <!-- <span class="sr-only"><?php esc_html_e('Previous', 'widgetkit-for-elementor');?></span> -->
                </a>
                <a class="right carousel-control hidden-xs" href="#<?php echo $id; ?>" role="button" data-slide="next">
                    <span class="fa fa-angle-right" aria-hidden="true"></span>
                   <!--  <span class="sr-only"><?php esc_html_e('Next', 'widgetkit-for-elementor');?></span> -->
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>


<script type="text/javascript">
   jQuery(document).ready(function($) {
        // Hero area auto height adjustment
     jQuery('#tgx-slider .carousel-inner .item') .css({'height': (($(window).height()))+'px'});
     jQuery(window).resize(function(){
        $('#tgx-slider .carousel-inner .item') .css({'height': (($(window).height()))+'px'});
    });
            if(!$('body').hasClass('wk-slider-2')){
            $('body').addClass('wk-slider-2');
        };
       });
</script>


