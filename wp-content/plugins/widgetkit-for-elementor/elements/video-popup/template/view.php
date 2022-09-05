<?php
    $settings = $this->get_settings_for_display();
    $id       = $this->get_id();
?>
    <div class="wk-video-popup-wrapper">

        <?php if ($settings['video_style'] == '1'): ?>

            <div class="video style-1">
                <a class="<?php echo $id; ?>bla-<?php if ($settings['yt_autoplay'] == 'yes'): echo '1';else:echo '2';endif;?>" href="<?php if ($settings['video_type'] == 'youtube'): ?><?php echo $settings['video_link']; ?>
                    <?php else:echo $settings['vimeo_link'];endif;?>">
                    <i class="fa fa-play"></i>
                </a>
            </div>

        <?php elseif ($settings['video_style'] == '2'): ?>

            <a href="<?php if ($settings['video_type'] == 'youtube'): ?><?php echo $settings['video_link']; ?><?php else:echo $settings['vimeo_link'];endif;?>" class="icon-video style-2 play-icon-text <?php echo $id; ?>bla-<?php if ($settings['yt_autoplay'] == 'yes'): echo '1';else:echo '2';endif;?>">
                <i class="fa fa-play-circle" aria-hidden="true">&nbsp;</i> 
                <?php echo $settings['popup_text']; ?>
            </a>

        <?php elseif ($settings['video_style'] == '3'): ?>

            <a class="play-btn style-3 <?php echo $id; ?>bla-<?php if ($settings['yt_autoplay'] == 'yes'): echo '1';else:echo '2';endif;?>" href="<?php if ($settings['video_type'] == 'youtube'): ?>
                <?php echo $settings['video_link']; ?><?php else:echo $settings['vimeo_link'];endif;?>">
              
            </a>

        <?php elseif ($settings['video_style'] == '4'): ?>
            <div class="video-popup-4 style-4">
                <a id="play-video" class="video-play-button <?php echo $id; ?>bla-<?php if ($settings['yt_autoplay'] == 'yes'): echo '1';else:echo '2';endif;?>" href="<?php if ($settings['video_type'] == 'youtube'): ?><?php echo $settings['video_link']; ?><?php else:echo $settings['vimeo_link'];endif;?>">
                  <span></span>
                </a>
            </div>
        <?php elseif ($settings['video_style'] == '5'): ?>
            <div class="video-popup-5 style-5">
                <a class="<?php echo $id; ?>bla-<?php if ($settings['yt_autoplay'] == 'yes'): echo '1';else:echo '2';endif;?>" href="<?php if ($settings['video_type'] == 'youtube'): ?><?php echo $settings['video_link']; ?><?php else:echo $settings['vimeo_link'];endif;?>">
                    <svg version="1.1" id="play" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" height="100px" width="100px"
                         viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
                      <path class="stroke-solid" fill="none" stroke="<?php if($settings['icon_color']) : echo $settings['icon_color']; else : echo 'white'; endif; ?>"  d="M49.9,2.5C23.6,2.8,2.1,24.4,2.5,50.4C2.9,76.5,24.7,98,50.3,97.5c26.4-0.6,47.4-21.8,47.2-47.7
                        C97.3,23.7,75.7,2.3,49.9,2.5"/>
                      <path class="stroke-dotted" fill="none" stroke="<?php if($settings['icon_color']) : echo $settings['icon_color']; else : echo 'white'; endif; ?>"  d="M49.9,2.5C23.6,2.8,2.1,24.4,2.5,50.4C2.9,76.5,24.7,98,50.3,97.5c26.4-0.6,47.4-21.8,47.2-47.7
                        C97.3,23.7,75.7,2.3,49.9,2.5"/>
                      <path class="icon" fill="<?php if($settings['icon_color']) : echo $settings['icon_color']; else : echo 'white'; endif; ?>" d="M38,69c-1,0.5-1.8,0-1.8-1.1V32.1c0-1.1,0.8-1.6,1.8-1.1l34,18c1,0.5,1,1.4,0,1.9L38,69z"/>
                    </svg>
                </a>
            </div>
        <?php else:?>
            <div class="video">
                <a class="<?php echo $id; ?>bla-<?php if ($settings['yt_autoplay'] == 'yes'): echo '1';else:echo '2';endif;?>" href="<?php if ($settings['video_type'] == 'youtube'): ?><?php echo $settings['video_link']; ?><?php else:echo $settings['vimeo_link'];endif;?>"><i class="fa fa-play"></i></a>
            </div>

        <?php endif;?>

    </div>
    <script>
        jQuery(document).ready(function () {
            jQuery(function(){
              jQuery("a.<?php echo $id; ?>bla-1").YouTubePopUp();
              jQuery("a.<?php echo $id; ?>bla-2").YouTubePopUp( { autoplay: 0 } ); // Disable autoplay
            });
        });
    </script>

    <script type="text/javascript">
        jQuery(function($){
            if(!$('body').hasClass('wk-video-popup')){
                $('body').addClass('wk-video-popup');
            }
        });

    </script>