<?php
    $settings = $this->get_settings();
    $header_tag_arr_for_slider_3 = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'];
    $slider_3_header_tag = esc_html(wp_kses($settings['heading_tag'], $header_tag_arr_for_slider_3));
?>
        <div class="tgx-slider-3">
            <div class="slideshow">
                <div class="slides">

                    <?php $a = 0;?>
                    <?php foreach($settings['tgx_slider_3_sliders'] as $sliders): ?>
                        <div class="text-center slider <?php echo ($a == 0) ? 'slide--current': ''; ?>">
                            <div class="slide__img" style="background-image: url(<?php echo esc_url( $sliders['tgx_slider_3_image']['url']); ?>)"></div>

                                <?php if($sliders['tgx_slider_3_title']): ?>
                                    <<?php echo $slider_3_header_tag;?> class="slide__title"><?php echo  $sliders['tgx_slider_3_title']; ?></<?php echo $slider_3_header_tag;?>>
                                <?php endif; ?>

                                <?php if($sliders['tgx_slider_3_subtitle']): ?>
                                    <p class="slide__desc"><?php echo  $sliders['tgx_slider_3_subtitle']; ?></p>
                                <?php endif; ?>

                                <?php if($sliders['tgx_slider_3_button_text']): ?>
                                    <a class="slide__link" href="<?php echo $sliders['tgx_slider_3_button_link']['url']; ?>">
                                        <?php echo  $sliders['tgx_slider_3_button_text']; ?>
                                    </a>
                                <?php endif; ?>

                        </div> <!-- slider -->
                    <?php $a++; endforeach; ?>
                </div><!-- Slides --> 


                <nav class="slidenav">
                    <?php if ($settings['tgx_slider_3_nav_previous']): ?>
                        <button class="slidenav__item slidenav__item--prev"><?php echo $settings['tgx_slider_3_nav_previous']; ?></button>
                    <?php endif; ?>

                    <?php if ($settings['tgx_slider_3_nav_divider']): ?>
                        <span class="divider">
                            <?php echo $settings['tgx_slider_3_nav_divider']; ?>
                        </span>
                    <?php endif; ?>

                    <?php if ($settings['tgx_slider_3_nav_next']): ?>
                        <button class="slidenav__item slidenav__item--next"><?php echo $settings['tgx_slider_3_nav_next']; ?></button>
                    <?php endif; ?>

                </nav><!-- Slidenav -->
                    
            </div> <!-- Slideshow -->
        </div> <!-- Slideshow -->

        <script type="text/javascript">
            jQuery(function($){
                if(!$('body').hasClass('wk-slider-3')){
                    $('body').addClass('wk-slider-3');
                }
            });

        </script>
