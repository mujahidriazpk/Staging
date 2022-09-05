<?php
// Silence is golden.

    $settings = $this->get_settings();

?>
    <div class="tgx-team-2">
        <div class="team-container">
            <!-- First Team Block -->
            <div class="team-each-wrap">
                <div class="team-block">
                    <div class="team-image">            
                            <img src="<?php echo $settings['team_image']['url'];?>" alt="<?php echo $settings['team_name'];?>"> 
   
                    </div>
                    <?php if ( ! empty( $settings['social_share_2'] ) ) : ?>
                        <div class="team-social">
                            <?php foreach ( $settings['social_share_2'] as $social ) : ?>
                                <?php if ( ! empty( $social['social_link'] ) ) : ?>
                                    <a <?php if($social['social_link'] ['is_external'])
                                    { echo 'target="_blank"'; }else{ echo 'rel="nofollow"';}?>
                                    href="<?php  echo $social['social_link']['url'];?>" class="<?php  echo strtolower($social['title']);?>">
                                         <i class="<?php echo esc_attr( $social['social_icon']); ?>"></i>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>  
                    <?php endif; ?> 

                <div class="team-info">
                    <div class="name">

                        <?php if ( ! empty( $settings['team_name'] ) ) : ?>
                            <?php if ( ! empty( $settings['image_external_link'] ) ) : ?>        
                                <a <?php if( $settings['image_external_link'] ['is_external'])
                                        { echo 'target="_blank"'; }else{ echo 'rel="nofollow"';}?>  
                                        href="<?php  echo $settings['image_external_link']['url'];?>">
                                <h4 class="team-title"><?php echo $settings['team_name'];?></h4>
                                </a>
                            <?php endif; ?>  
                        <?php endif; ?>

                        <?php if ( ! empty( $settings['designation'] ) ) : ?>
                            <span class="team-designation"><?php echo $settings['designation'];?></span>
                        <?php endif; ?>
                    </div>
   
                    </div><!-- end .hover content -->           
                </div>

            </div><!-- end .team wrap -->
        </div><!-- end .tema container -->
    </div> <!-- end .section -->

    <script type="text/javascript">
        jQuery(function($){
            if(!$('body').hasClass('wk-team-vertical-icon')){
                $('body').addClass('wk-team-vertical-icon');
            }
        });

    </script>
