<?php
   // Silence is golden.

    $settings = $this->get_settings();
    $id = $this->get_id();

?>

    <div id="tgx-team-<?php echo $id; ?>" class="tgx-team-4 animation">
                <?php if ( ! empty( $settings['team_4_team_name'] ) ) : ?>
                    <?php if ( ! empty( $settings['image_external_link'] ) ) : ?>        
                        <a <?php if( $settings['image_external_link'] ['is_external'])
                                { echo 'target="_blank"'; }else{ echo 'rel="nofollow"';}?>  
                                href="<?php  echo $settings['image_external_link']['url'];?>">
                        <h2 class="title-wrapper">
                        <span class="team-title"><?php echo $settings['team_4_team_name'];?></span>
                            <strong>
                                <i class="ion-ios-star-half"></i>
                                <?php if ( ! empty( $settings['team_4_designation'] ) ) : ?>
                                    <?php echo $settings['team_4_designation'];?>
                                <?php endif; ?>
                            </strong>
                        </h2>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
        <div class="mc-content">
            <div class="img-container">
                 <img src="<?php echo $settings['team_4_team_image']['url'];?>" alt="<?php echo $settings['team_4_team_name'];?>"> 
            </div>
            <?php if ( ! empty( $settings['team_4_content'] ) ) : ?>
                <div class="mc-description">
                    <p><?php echo $settings['team_4_content'] ?></p>
                </div>
            <?php endif; ?> 
        </div> <!-- /mc-content -->


        <a class="mc-btn-action">
            <i class="fa fa-bars"></i>
        </a>
        <div class="mc-footer">
           <?php if ( ! empty( $settings['team_4_social_share'] ) ) : ?>
                <div class="team-social">
                    <?php foreach ( $settings['team_4_social_share'] as $social ) : ?>
                        <?php if ( ! empty( $social['team_4_social_link'] ) ) : ?>
                            <a <?php if($social['team_4_social_link'] ['is_external'])
                                    { echo 'target="_blank"'; }else{ echo 'rel="nofollow"';}?>
                            href="<?php  echo $social['team_4_social_link']['url'];?>" class="<?php  echo strtolower($social['team_4_title']);?>">
                                 <i class="<?php echo esc_attr( $social['team_4_social_icon']); ?>"></i>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>  <!-- /team-social -->
            <?php endif; ?>
        </div> <!-- /mc-footer -->
    </div> <!-- /tgx-team-4 -->

    <script type="text/javascript">
         jQuery(function($) {
            jQuery('#tgx-team-<?php echo $id; ?> > .mc-btn-action').click(function () {
                var card = $(this).parent('#tgx-team-<?php echo $id; ?>');
                var icon = $(this).children('i');
                icon.addClass('fa-spin-fast');

                if (card.hasClass('mc-active')) {
                    card.removeClass('mc-active');

                    window.setTimeout(function() {
                        icon
                            .removeClass('fa-times')
                            .removeClass('fa-spin-fast')
                            .addClass('fa-bars');

                    }, 800);
                } else {
                    card.addClass('mc-active');

                    window.setTimeout(function() {
                        icon
                            .removeClass('fa-bars')
                            .removeClass('fa-spin-fast')
                            .addClass('fa-times');

                    }, 800);
                }
            });
        },(jQuery));
    </script>

    <script type="text/javascript">
        jQuery(function($){
            if(!$('body').hasClass('wk-team-animtion')){
                $('body').addClass('wk-team-animtion');
            }
        });

    </script>