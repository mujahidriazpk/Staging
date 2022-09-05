<?php
// Silence is golden.

    $settings = $this->get_settings();


?>


    <div class="animation-text">
        <div class="text-slide">
            <h2 class="cd-headline <?php if ($settings['choose_animation_text'] == 'rotate') {
                echo "rotate-1";
            }
                elseif($settings['choose_animation_text'] == 'clip'){
                    echo "clip";
                }
                elseif($settings['choose_animation_text'] == 'loading_bar'){
                    echo "loading-bar";
                }
                elseif($settings['choose_animation_text'] == 'push'){
                    echo "push";
                }
                else{
                    echo "clip";
                }?>">
                <?php if ($settings['prefix_title']): ?>  
                    <span><?php echo $settings['prefix_title']; ?></span>
                <?php endif ?>
                <span class="cd-words-wrapper">
                    <?php $text = 0; ?>
                    <?php foreach ( $settings['animate_text_list'] as $animation_text ) : ?>
                        <b class="<?php echo ($text == 0) ? 'is-visible': ''; ?>"><?php echo $animation_text['animate_text'];?></b>
                    <?php $text++; endforeach; ?>
                </span>
                 <?php if ($settings['suffix_title']): ?>  
                    <span><?php echo $settings['suffix_title']; ?></span>
                <?php endif ?>
            </h2> <!-- cd-headline -->
        </div> <!-- text-slide -->
    </div><!-- animation-text -->

        <script type="text/javascript">
            jQuery(function($){
                if(!$('body').hasClass('wk-animation-headline')){
                    $('body').addClass('wk-animation-headline');
                }
            });

        </script>