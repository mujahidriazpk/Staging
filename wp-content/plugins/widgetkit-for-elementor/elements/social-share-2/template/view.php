<?php
   // Silence is golden.

    $settings = $this->get_settings();
    $id = $this->get_id();

?>


    <div id="<?php echo $id;?>" class="tgx-social-share-2 profile">

        <?php if ($settings['social_share_2_image']):?> 
            <div class="photo">
                <img src="<?php echo $settings['social_share_2_image']['url'];?>" alt="<?php echo $settings['social_share_2_name'];?>">
            </div>
        <?php endif;?>

        <div class="profile-content">
            <div class="text">
                <h3 class="profile-name"><?php echo $settings['social_share_2_name'];?></h3>
                <h6 class="profile-profession"><?php echo $settings['social_share_2_designation'];?></h6>
            </div>
            <div class="btn-bar click-<?php echo $id;?>"><span></span></div>
        </div> <!-- profile-content -->

        <div class="box box-<?php echo $id;?>">

            <?php if ( ! empty( $settings['social_share_2_social_share'] ) ) : ?>
                <?php foreach ( $settings['social_share_2_social_share'] as $social ) : ?>
                    <?php if ( ! empty( $social['social_share_2_social_link'] ) ) : ?>
                        <a target="_blank" href="<?php  echo $social['social_share_2_social_link'];?>" class="<?php  echo strtolower($social['social_share_2_title']);?>">
                                <i class="<?php echo esc_attr( $social['social_share_2_social_icon']); ?>"></i>
                        </a><!-- social-wrapper -->
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div><!-- box -->

    </div><!-- profile -->

    <script type="text/javascript">
        jQuery(function($) {

            jQuery('.click-<?php echo $id;?>').click(function() {
                jQuery(this).toggleClass('active');
                return $('.box-<?php echo $id;?>').toggleClass('open');
              });

            },(jQuery));


    </script>

    <script type="text/javascript">
        jQuery(function($){
            if(!$('body').hasClass('wk-social-share-collapse')){
                $('body').addClass('wk-social-share-collapse');
            }
        });

    </script>