<?php
// Silence is golden.

	$settings = $this->get_settings();
  $id = $this->get_id();

?>

    <div class="tgx-testimonial-1 <?php echo $id; ?>">
          <?php foreach ( $settings['testimonial_option_1'] as $testimonial_1 ) : ?>
            <div class="testimoni-wrapper">
                <div class="testimony"> <p> <?php echo $testimonial_1['testimoni_content_1'];?></p></div>
                <div class="author">
                 <?php if ($testimonial_1['testimoni_image_1']['url']):?>

                    <?php
                        if ($settings['testimonial_items'] == '1'):?>
                          <div class="col-md-1">
                    <?php elseif ($settings['testimonial_items'] == '2'):?>
                        <div class="col-md-3">
                    <?php else:?>
                       <div class="col-md-4">
                    <?php endif;?>

                       <?php if ($testimonial_1['testimoni_image_1']['url']):?>
                              <span>
                                  <img class="testimonial-image" src="<?php echo $testimonial_1['testimoni_image_1']['url']; ?>">
                              </span>
                      <?php endif;?>
                        
                    </div>
                  <?php endif; ?>
                    <?php
                        if ($settings['testimonial_items'] == '1'):?>
                            <div class="col-md-11">
                        <?php elseif ($settings['testimonial_items'] == '2'):?>
                            <div class="col-md-9">
                        <?php else:?>
                           <div class="col-md-8">
                        <?php endif;?>
                            <?php if ($testimonial_1['title_1']):?>
                              <h4 class="name"><?php echo $testimonial_1['title_1'];  ?></h4>
                            <?php endif; ?>

                            <?php if ($testimonial_1['designation_1']):?>
                              <p class="designation"><?php echo $testimonial_1['designation_1'];  ?></p>
                            <?php endif; ?>
                     </div>
                </div>
            </div>
          <?php endforeach; ?>            
    </div><!-- /.section -->

    <script type='text/javascript'>
          jQuery(document).ready(function($) {
            jQuery(".<?php echo $id; ?>").addClass("owl-carousel").owlCarousel({
                  pagination: false,
                  margin:10,
                  dots:false,
                  
                  <?php if ($settings['testimonial_items'] == '2'):?>
                      center:false,
                  <?php else: ?>
                      center:true,
                  <?php endif; ?>
                  loop:true,
                  nav: false,
                  navClass: ['owl-carousel-left','owl-carousel-right'],
                  navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
                  autoHeight : true,
               <?php if ($settings['autoplay_enable'] == 'yes'): ?>
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
                          items:<?php echo $settings['testimonial_items']; ?>
                      }
                  }
               });
        });


    </script>

    <script type="text/javascript">
        jQuery(function($){
            if(!$('body').hasClass('wk-testimonial-single')){
                $('body').addClass('wk-testimonial-single');
            }
        });

    </script>
