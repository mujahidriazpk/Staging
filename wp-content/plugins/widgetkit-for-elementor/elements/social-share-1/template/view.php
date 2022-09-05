<?php
   // Silence is golden.

    $settings = $this->get_settings();
    $id = $this->get_id();

?>

    <div id="<?php echo $id;?>" class="social-share-1">
        <div class="contact">
            <div class="contact-wrapper">
                <div class="content">
                    <img src="<?php echo $settings['social_share_1_image']['url'];?>" alt="<?php echo $settings['social_share_1_name'];?>"> 

                    <aside>
                        <h2 class="person-name"><?php echo $settings['social_share_1_name'];?></h2>
                        <p><?php echo $settings['social_share_1_designation'];?></p>
                    </aside> <!-- aside -->
                      
                    <button class="button-<?php echo $id;?>">
                        <i class="ion-android-close"></i>
                        <span><?php echo $settings['social_share_1_button_text'];?></span> 
                    </button><!-- button -->
                </div><!-- /content -->

                <div class="title btn-<?php echo $id;?>"><p><?php echo $settings['social_share_1_button_text'];?></p></div><!-- title -->
            </div><!-- /content-wrapper -->

            <nav class="social-share social-<?php echo $id;?>">
                <?php if ( ! empty( $settings['social_share_1_social_share'] ) ) : ?>
                    <?php foreach ( $settings['social_share_1_social_share'] as $social ) : ?>
                        <?php if ( ! empty( $social['social_share_1_social_link'] ) ) : ?>
                            <a target="_blank" href="<?php  echo $social['social_share_1_social_link'];?>" class="<?php  echo strtolower($social['social_share_1_title']);?>">
                                <div class="social-content">
                                    <h2 class="social-name">
                                        <?php echo $social['social_share_1_title'];?>
                                    </h2>
                                    <span> 
                                        <?php echo esc_attr($social['social_share_1_social_email']);?>
                                    </span>
                                </div> <!-- social-content -->

                                <div class="icon-social">
                                    <i class="<?php echo esc_attr( $social['social_share_1_social_icon']); ?>"></i>
                                </div><!-- icon -->  
                            </a><!-- social-wrapper -->
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </nav><!-- social-share -->
        </div><!-- contact -->
    </div><!-- social-share-1 -->

    <script type="text/javascript">
        jQuery(function($) {
            jQuery('.button-<?php echo $id;?>').click(function(){
                jQuery('.button-<?php echo $id;?>').toggleClass('active ');
                jQuery('.btn-<?php echo $id;?>').toggleClass('active');
                jQuery('.social-<?php echo $id;?>').toggleClass('active');
            });
        },(jQuery));

    </script>
    <script type="text/javascript">
        jQuery(function($){
            if(!$('body').hasClass('wk-social-share-animation')){
                $('body').addClass('wk-social-share-animation');
            }
        });

    </script>