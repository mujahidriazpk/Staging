<?php
// Silence is golden.
    use Elementor\Icons_Manager;
    $team = $this->get_settings();
    $id = $this->get_id();
    $header_tag_arr_for_team = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'];
    $team_header_tag = esc_html(wp_kses($team['header_tag'], $header_tag_arr_for_team));
    use Elementor\Group_Control_Image_Size;

?>

    <div class="wk-team">
        <?php if ($team['item_styles'] == 'screen_1'):?>
            <div class="wk-card wk-card-default wk-style-1">
                <div class="wk-card-media-top wk-overflow-hidden">
                    <?php if( $team['single_content_link']):?>
                        <a class="wk-display-block wk-text-center" href="<?php echo $team['single_content_link']['url']; ?>" <?php echo $team['single_content_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                            <?php echo  Group_Control_Image_Size::get_attachment_image_html( $team, 'team_image', 'single_image') ;?>
                        </a>
                    <?php else: ?>
                        <?php echo  Group_Control_Image_Size::get_attachment_image_html( $team, 'team_image', 'single_image') ;?>
                    <?php endif; ?>
                </div> <!-- wk-card-image -->

                 <div class="wk-card-body">
                    <div class="wk-grid-small wk-flex-top" wk-grid>
                        <?php if( $team['single_title']):?>
                            <div class="wk-width-expand">
                                <<?php echo $team_header_tag;?> class="wk-card-title wk-margin-remove">
                                    <?php if( $team['single_content_link']):?>
                                        <a class="wk-display-block" href="<?php echo $team['single_content_link']['url']; ?>" <?php echo $team['single_content_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                                            <?php echo $team['single_title']; ?>
                                        </a>
                                    <?php else: ?>
                                        <?php echo $team['single_title']; ?>
                                    <?php endif; ?>
                                </<?php echo $team_header_tag;?>>
                            </div> <!-- wk-width-expand-->
                        <?php endif; ?>

                        <?php if( $team['social_share']):?>
                            <div class="wk-width-auto social-icons">
                                <?php foreach ( $team['social_share'] as $social ) : ?>
                                    <?php if ($social['social_link']['url']): ?>
                                        <a href="<?php echo $social['social_link']['url']; ?>"  
                                            <?php echo $social['social_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                                            
                                            <?php 
                                               Icons_Manager::render_icon( $social['social_icon'], [ 'aria-hidden' => 'true' ] );
                                            ?>
                                        </a>
                                    <?php endif ;?>
                               
                                <?php endforeach; ?>
                            </div> <!-- wk-width-auto-->
                        <?php endif; ?>
                    </div>
                
                    <?php if( $team['single_designation']):?>
                        <span class="wk-card-designation wk-inline-block"><?php echo $team['single_designation']; ?></span>
                    <?php endif; ?>
                    <?php if( $team['single_content']):?>
                        <p class="wk-text-normal wk-margin-small"><?php echo $team['single_content']; ?></p>
                    <?php endif; ?>
                </div> <!-- wk-card-body -->
            </div> <!-- wk-card-->


            <?php elseif($team['item_styles'] == 'screen_2'): ?>
                <div class="wk-card wk-card-default wk-style-2">
                    <div class="wk-card-media-top wk-overflow-hidden">
                        <?php if( $team['single_content_link']):?>
                            <a class="wk-display-block" href="<?php echo $team['single_content_link']['url']; ?>" <?php echo $team['single_content_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                                <?php echo  Group_Control_Image_Size::get_attachment_image_html( $team, 'team_image', 'single_image') ;?>
                            </a>
                        <?php else: ?>
                            <?php echo  Group_Control_Image_Size::get_attachment_image_html( $team, 'team_image', 'single_image') ;?>
                        <?php endif; ?>
                    </div> <!-- wk-card-image -->

                     <div class="wk-card-body">
                        <?php if( $team['single_title']):?>
                            <<?php echo $team_header_tag;?> class="wk-card-title wk-margin-remove">
                                <?php if( $team['single_content_link']):?>
                                    <a class="wk-display-block" href="<?php echo $team['single_content_link']['url']; ?>" <?php echo $team['single_content_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                                        <?php echo $team['single_title']; ?>
                                    </a>
                                <?php else: ?>
                                    <?php echo $team['single_title']; ?>
                                <?php endif; ?>
                            </<?php echo $team_header_tag;?>>
                        <?php endif; ?>

                        <?php if( $team['single_designation']):?>
                            <span class="wk-card-designation wk-inline-block"><?php echo $team['single_designation']; ?></span>
                        <?php endif; ?>

                        <?php if( $team['social_share']):?>
                            <div class="social-icons">
                                <?php foreach ( $team['social_share'] as $social ) : ?>
                                    <?php if ($social['social_link']['url']): ?>
                                        <a href="<?php echo $social['social_link']['url']; ?>"  
                                            <?php echo $social['social_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                                            
                                            <?php 
                                               Icons_Manager::render_icon( $social['social_icon'], [ 'aria-hidden' => 'true' ] );
                                            ?>
                                        </a>
                                    <?php endif ;?>
                                <?php endforeach; ?>
                            </div> <!-- wk-width-auto-->
                        <?php endif; ?>

                        <?php if( $team['single_content']):?>
                            <p class="wk-text-normal wk-margin-small"><?php echo $team['single_content']; ?></p>
                        <?php endif; ?>
                    </div> <!-- wk-card-body -->
                </div> <!-- wk-card-->



            <?php elseif($team['item_styles'] == 'screen_3'): ?>
                <div class="wk-card wk-card-default wk-grid-collapse wk-style-3 wk-flex-middle" wk-grid>
                    <?php if ($team['image_position'] == 'left'):?> 
                        <div class="wk-card-media-left wk-cover-container wk-width-1-2@m wk-position-relative wk-overflow-hidden">
                            <?php if( $team['single_content_link']):?>
                                <a class="wk-display-block" href="<?php echo $team['single_content_link']['url']; ?>" <?php echo $team['single_content_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                                    <?php echo  Group_Control_Image_Size::get_attachment_image_html( $team, 'team_image', 'single_image') ;?>
                                </a>
                            <?php else: ?>
                                <?php echo  Group_Control_Image_Size::get_attachment_image_html( $team, 'team_image', 'single_image') ;?>
                            <?php endif; ?>
                        </div> <!-- wk-card-image -->
                    <?php endif; ?>
                    <div class="wk-width-1-2@m">
                         <div class="wk-card-body">
                            <?php if( $team['single_title']):?>
                                <<?php echo $team_header_tag;?> class="wk-card-title wk-margin-remove">
                                    <?php if( $team['single_content_link']):?>
                                        <a class="wk-display-block" href="<?php echo $team['single_content_link']['url']; ?>" <?php echo $team['single_content_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                                            <?php echo $team['single_title']; ?>
                                        </a>
                                    <?php else: ?>
                                        <?php echo $team['single_title']; ?>
                                    <?php endif; ?>
                                </<?php echo $team_header_tag;?>>
                            <?php endif; ?>

                            <?php if( $team['single_designation']):?>
                                <span class="wk-card-designation wk-inline-block"><?php echo $team['single_designation']; ?></span>
                            <?php endif; ?>

                            <?php if( $team['single_content']):?>
                                <p class="wk-text-normal wk-margin-small"><?php echo $team['single_content']; ?></p>
                            <?php endif; ?>
                            <?php if( $team['social_share']):?>
                                <div class="social-icons">
                                    <?php foreach ( $team['social_share'] as $social ) : ?>
                                        <?php if ($social['social_link']['url']): ?>
                                            <a href="<?php echo $social['social_link']['url']; ?>"  
                                                <?php echo $social['social_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                                                
                                                <?php 
                                                   Icons_Manager::render_icon( $social['social_icon'], [ 'aria-hidden' => 'true' ] );
                                                ?>
                                            </a>
                                        <?php endif ;?>
                                    <?php endforeach; ?>
                                </div> <!-- wk-width-auto-->
                            <?php endif; ?>
                        </div> <!-- wk-card-body -->
                    </div>
                    <?php if ($team['image_position'] == 'right'):?> 
                        <div class="wk-card-media-right wk-cover-container wk-width-1-2@m wk-position-relative wk-overflow-hidden">
                            <?php if( $team['single_content_link']):?>
                                <a class="wk-display-block" href="<?php echo $team['single_content_link']['url']; ?>" <?php echo $team['single_content_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                                    <img src="<?php echo $team['single_image']['url']; ?>" alt="">
                                </a>
                            <?php else: ?>
                                <img src="<?php echo $team['single_image']['url']; ?>" alt="">
                            <?php endif; ?>
                        </div> <!-- wk-card-image -->
                    <?php endif; ?>
                </div> <!-- wk-card-->


            <?php elseif($team['item_styles'] == 'screen_4'): ?>
                <div class="wk-card wk-style-4 ">
                    <div class="wk-card-wrapper wk-position-relative wk-transition-toggle wk-overflow-hidden">
                        <?php if( $team['single_content_link']):?>
                            <a class="wk-display-block" href="<?php echo $team['single_content_link']['url']; ?>" <?php echo $team['single_content_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                                <?php echo  Group_Control_Image_Size::get_attachment_image_html( $team, 'team_image', 'single_image') ;?>
                            </a>
                        <?php else: ?>
                            <?php echo  Group_Control_Image_Size::get_attachment_image_html( $team, 'team_image', 'single_image') ;?>
                        <?php endif; ?>

                         <div class="wk-card-body wk-padding-remove wk-position-bottom wk-background-muted">
                            <div class="info-wrapper wk-position-relative wk-padding-small">
                                <?php if( $team['single_title']):?>
                                    <<?php echo $team_header_tag;?> class="wk-card-title wk-margin-remove">
                                        <?php if( $team['single_content_link']):?>
                                            <a class="wk-display-block" href="<?php echo $team['single_content_link']['url']; ?>" <?php echo $team['single_content_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                                                <?php echo $team['single_title']; ?>
                                            </a>
                                        <?php else: ?>
                                            <?php echo $team['single_title']; ?>
                                        <?php endif; ?>
                                    </<?php echo $team_header_tag;?>>
                                <?php endif; ?>

                                <?php if( $team['single_designation']):?>
                                    <span class="wk-card-designation wk-inline-block"><?php echo $team['single_designation']; ?></span>
                                <?php endif; ?>

                                <?php if( $team['social_share']):?>
                                    <div class="social-icons">
                                        <?php foreach ( $team['social_share'] as $social ) : ?>
                                            <?php if ($social['social_link']['url']): ?>
                                                <a href="<?php echo $social['social_link']['url']; ?>"  
                                                    <?php echo $social['social_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                                                    
                                                    <?php 
                                                       Icons_Manager::render_icon( $social['social_icon'], [ 'aria-hidden' => 'true' ] );
                                                    ?>
                                                </a>
                                            <?php endif ;?>
                                        <?php endforeach; ?>
                                    </div> <!-- wk-width-auto-->
                                <?php endif; ?>
                                <?php if( $team['single_content']):?>
                                    <p class="wk-text-normal wk-margin-small"><?php echo $team['single_content']; ?></p>
                                <?php endif; ?>
                                
                            </div>
                        </div> <!-- wk-card-body -->
                    </div>
                </div> <!-- wk-card-->

            <?php elseif($team['item_styles'] == 'screen_5'): ?>
                <div id="<?php echo $id; ?>" class="wk-card wk-style-5">
                    <div class="wk-card-wrapper wk-position-relative wk-transition-toggle">
                        <?php if( $team['single_content_link']):?>
                            <a class="wk-display-block" href="<?php echo $team['single_content_link']['url']; ?>" <?php echo $team['single_content_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                                <?php echo  Group_Control_Image_Size::get_attachment_image_html( $team, 'team_image', 'single_image') ;?>

                            </a>
                        <?php else: ?>
                            <?php echo  Group_Control_Image_Size::get_attachment_image_html( $team, 'team_image', 'single_image') ;?>

                        <?php endif; ?>

                         <div class="wk-card-body wk-padding-remove wk-position-bottom wk-background-muted">
                            <div class="info-wrapper wk-position-relative wk-padding-small">
                                <?php if( $team['single_title']):?>
                                    <<?php echo $team_header_tag;?> class="wk-card-title wk-margin-remove">
                                        <?php if( $team['single_content_link']):?>
                                            <a class="wk-display-block" href="<?php echo $team['single_content_link']['url']; ?>" <?php echo $team['single_content_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                                                <?php echo $team['single_title']; ?>
                                            </a>
                                        <?php else: ?>
                                            <?php echo $team['single_title']; ?>
                                        <?php endif; ?>
                                    </<?php echo $team_header_tag;?>>
                                <?php endif; ?>

                                <?php if( $team['single_designation']):?>
                                    <span class="wk-card-designation wk-inline-block"><?php echo $team['single_designation']; ?></span>
                                <?php endif; ?>
                                 <?php if( $team['single_content']):?>
                                    <p style="display: none;" class="wk-text-normal wk-margin-small wk-margin-remove-top"><?php echo $team['single_content']; ?></p>
                                <?php endif; ?>

                                <?php if( $team['social_share']):?>
                                    <div class="social-icons">
                                        <?php foreach ( $team['social_share'] as $social ) : ?>
                                            <?php if ($social['social_link']['url']): ?>
                                                <a href="<?php echo $social['social_link']['url']; ?>"  
                                                    <?php echo $social['social_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                                                    
                                                    <?php 
                                                       Icons_Manager::render_icon( $social['social_icon'], [ 'aria-hidden' => 'true' ] );
                                                    ?>
                                                </a>
                                            <?php endif ;?>
                                        <?php endforeach; ?>
                                    </div> <!-- wk-width-auto-->
                                <?php endif; ?>
                            </div>
                        </div> <!-- wk-card-body -->
                    </div>
                </div> <!-- wk-card-->

            <?php elseif($team['item_styles'] == 'screen_6'): ?>

                <div class="wk-card wk-style-6">
                    <div class="wk-card-wrapper">
                        <a class="wk-display-block wk-card-link" href="<?php echo $team['single_content_link']['url']; ?>" <?php echo $team['single_content_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                            <div class="wk-card-hover-bg" style="background-image:url(<?php echo $team['single_image']['url']; ?>);">
                                
                            </div>
                        </a>
                        <div class="wk-card-media"></div>
                        <div class="wk-card-body wk-position-relative">
                            <?php if( $team['single_title']):?>
                                <<?php echo $team_header_tag;?> class="wk-card-title wk-margin-remove">
                                    <?php if( $team['single_content_link']):?>
                                        <a class="wk-display-block" href="<?php echo $team['single_content_link']['url']; ?>" <?php echo $team['single_content_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                                            <?php echo $team['single_title']; ?>
                                        </a>
                                    <?php else: ?>
                                        <?php echo $team['single_title']; ?>
                                    <?php endif; ?>
                                </<?php echo $team_header_tag;?>>
                            <?php endif; ?>

                            <?php if( $team['single_designation']):?>
                                <span class="wk-card-designation wk-inline-block"><?php echo $team['single_designation']; ?></span>
                            <?php endif; ?>
                            
                            <?php if( $team['single_content']):?>
                                <p class="wk-text-normal wk-margin-small"><?php echo $team['single_content']; ?></p>
                            <?php endif; ?>

                            <?php if( $team['social_share']):?>
                                <div class="wk-width-auto social-icons">
                                    <?php foreach ( $team['social_share'] as $social ) : ?>
                                        <?php if ($social['social_link']['url']): ?>
                                            <a href="<?php echo $social['social_link']['url']; ?>"  
                                                <?php echo $social['social_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                                                
                                                <?php 
                                                   Icons_Manager::render_icon( $social['social_icon'], [ 'aria-hidden' => 'true' ] );
                                                ?>
                                            </a>
                                        <?php endif ;?>
                                    <?php endforeach; ?>
                                </div> <!-- wk-width-auto-->
                            <?php endif; ?>
                        </div> <!-- wk-card-body -->
                    </div>
                </div>

            <?php else: ?>
                <div class="wk-card wk-card-default wk-style-1">
                    <div class="wk-card-media-top wk-overflow-hidden">
                        <?php if( $team['single_content_link']):?>
                            <a class="wk-display-block" href="<?php echo $team['single_content_link']['url']; ?>" <?php echo $team['single_content_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                                <img src="<?php echo $team['single_image']['url']; ?>" alt="">
                            </a>
                        <?php else: ?>
                            <img src="<?php echo $team['single_image']['url']; ?>" alt="">
                        <?php endif; ?>
                    </div> <!-- wk-card-image -->

                     <div class="wk-card-body">
                        <div class="wk-grid-small wk-flex-middle" wk-grid>
                            <?php if( $team['single_title']):?>
                                <div class="wk-width-expand">
                                    <<?php echo $team_header_tag;?> class="wk-card-title wk-margin-remove">
                                        <?php if( $team['single_content_link']):?>
                                            <a class="wk-display-block" href="<?php echo $team['single_content_link']['url']; ?>" <?php echo $team['single_content_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                                                <?php echo $team['single_title']; ?>
                                            </a>
                                        <?php else: ?>
                                            <?php echo $team['single_title']; ?>
                                        <?php endif; ?>
                                    </<?php echo $team_header_tag;?>>
                                </div> <!-- wk-width-expand-->
                            <?php endif; ?>

                            <?php if( $team['social_share']):?>
                                <div class="wk-width-auto social-icons">
                                    <?php foreach ( $team['social_share'] as $social ) : ?>
                                        <?php if ($social['social_link']['url']): ?>
                                            <a href="<?php echo $social['social_link']['url']; ?>"  
                                                <?php echo $social['social_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>>
                                                
                                                <?php 
                                                   Icons_Manager::render_icon( $social['social_icon'], [ 'aria-hidden' => 'true' ] );
                                                ?>
                                            </a>
                                        <?php endif ;?>
                                    <?php endforeach; ?>
                                </div> <!-- wk-width-auto-->
                            <?php endif; ?>
                        </div>
                    
                        <?php if( $team['single_designation']):?>
                            <span class="wk-card-designation wk-inline-block"><?php echo $team['single_designation']; ?></span>
                        <?php endif; ?>
                        <?php if( $team['single_content']):?>
                            <p class="wk-text-normal wk-margin-small"><?php echo $team['single_content']; ?></p>
                        <?php endif; ?>
                    </div> <!-- wk-card-body -->
                </div> <!-- wk-card-->
        <?php endif; ?>
    </div><!-- wk-grid-->


        <script type="text/javascript">
            <?php if ($team['item_styles'] == 'screen_5'): ?>
                jQuery(document).ready(function(){
                    jQuery("#<?php echo $id; ?>.wk-style-5 .wk-card-wrapper").hover(function(){
                    jQuery("#<?php echo $id; ?>.wk-style-5 .wk-text-normal").slideToggle("medium");
                  });
                });
            <?php endif; ?>
            jQuery(function($){
                if(!$('body').hasClass('wk-team')){
                    $('body').addClass('wk-team');
                }
            });

        </script>
 


