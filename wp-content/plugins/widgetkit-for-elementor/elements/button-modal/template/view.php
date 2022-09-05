<?php

    $settings = $this->get_settings();
    $id = $this->get_id();

?>




<div id="<?php echo $id;?>" class="modal-container">
    <div class="
    <?php if ($settings['button_modal_hover_effect'] == 'border'){
        echo "btn-border-modal";
        }else{
            echo "click-btn ";
        }
        ?>">

    <?php if ($settings['button_options_select'] == 'modal'):?>

        <button onclick="document.getElementById('modal-button-<?php echo $id;?>').style.display='block'" class="btn-hover-<?php echo $settings['button_modal_hover_effect'];?>  booking-button">


        <?php if ($settings['button_modal_hover_effect'] == 'border'): ?>
            <div class="btn-line btn-normal btn-hover-<?php echo $settings['button_modal_hover_effect'];?>">
                <a href="#">
                    <p> <?php echo $settings['modal_text'];?>
                            <?php if ($settings['modal_icon']):?>
                        <i style="float:<?php echo $settings['modal_icon_align'];?>;" class="<?php echo $settings['modal_icon'];?>"></i>
                    <?php endif; ?>
                        
                    </p>
                    <span class="top"></span>
                    <span class="right"></span>
                    <span class="bottom"></span>
                    <span class="left"></span>
                </a>
            </div>

        <?php elseif($settings['button_modal_hover_effect'] == 'bfm' || $settings['button_modal_hover_effect'] == 'fourcorner'): ?>
            <span>
                <?php echo $settings['modal_text'];?>
                <?php if ($settings['modal_icon']):?>
                    <i style="float:<?php echo $settings['modal_icon_align'];?>;" class="<?php echo $settings['modal_icon'];?>"></i>
                <?php endif; ?>
            </span>

        
        <?php else: ?>
            <?php echo $settings['modal_text'];?>
                <?php if ($settings['modal_icon']):?>
                    <i style="float:<?php echo $settings['modal_icon_align'];?>;" class="<?php echo $settings['modal_icon'];?>"></i>
                <?php endif; ?>
            <?php endif; ?>
        </button> <!-- /button -->






    <?php else: ?>

        <?php if ($settings['button_modal_hover_effect'] == 'border'): ?>
            <div class="btn-line btn-hover-<?php echo $settings['button_modal_hover_effect'];?>">
                <a 
                href="<?php echo $settings['normal_btn_link']['url'];?>" 
                target="<?php echo $settings['normal_btn_link']['is_external'] ? '_blank' : '';  ?>"
                rel="<?php echo $settings['normal_btn_link']['nofollow'] ? 'nofollow' : ''; ?>"
                >
                    <p> <?php echo $settings['modal_text'];?>
                            <?php if ($settings['modal_icon']):?>
                        <i style="float:<?php echo $settings['modal_icon_align'];?>;" class="<?php echo $settings['modal_icon'];?>"></i>
                    <?php endif; ?>
                        
                    </p>
                    <span class="top"></span>
                    <span class="right"></span>
                    <span class="bottom"></span>
                    <span class="left"></span>
                </a>
            </div>
        <?php else: ?>
        <a  
        href="<?php echo $settings['normal_btn_link']['url'];?>" 
        class="btn-hover-<?php echo $settings['button_modal_hover_effect'];?>  button-normal"
        target="<?php echo $settings['normal_btn_link']['is_external'] ? '_blank' : '';  ?>"
        rel="<?php echo $settings['normal_btn_link']['nofollow'] ? 'nofollow' : ''; ?>"
        >
            <?php if ($settings['button_modal_hover_effect'] == 'bfm' || $settings['button_modal_hover_effect'] == 'fourcorner' ): ?>
                <span>
                    <?php echo $settings['modal_text'];?>
                    <?php if ($settings['modal_icon']):?>
                        <i style="float:<?php echo $settings['modal_icon_align'];?>;" class="<?php echo $settings['modal_icon'];?>"></i>
                    <?php endif; ?>
                </span>
        
            <?php else: ?>
                <?php echo $settings['modal_text'];?>
                    <?php if ($settings['modal_icon']):?>
                        <i style="float:<?php echo $settings['modal_icon_align'];?>;" class="<?php echo $settings['modal_icon'];?>"></i>
                    <?php endif; ?>
                <?php endif; ?>
            </a> <!-- /button -->
        <?php endif; ?>


    <?php endif;?>


    </div> <!-- /click btn -->

    <?php if ($settings['button_options_select'] == 'modal'):?>
        <div id="modal-button-<?php echo $id;?>" class="tgx-modal">
            <div class="tgx-modal-content tgx-<?php echo $settings['modal_effect'];?>">

                <header class="tgx-container tgx-teal"> 
                    <span onclick="document.getElementById('modal-button-<?php echo $id;?>').style.display='none'" 
                    class="tgx-button tgx-display-topright">&times;</span>
                    <h2><?php echo $settings['modal_tile'];?></h2>
                </header><!-- /header -->

                <div class="tgx-container">
                    <?php if ($settings['modal_content'] == 'modal_shortcode'): ?>    
                        <?php echo do_shortcode($settings['modal_shortcode']);?>
                    <?php elseif($settings['modal_content'] == 'modal_video'): ?>
                        <?php echo $settings['modal_video'];?>
                    <?php else: ?>
                        <?php echo do_shortcode($settings['modal_shortcode']);?>
                    <?php endif ?>
                </div><!-- /tgx-container -->

            </div><!-- /tgx-content -->
        </div><!-- /modal-button -->
    <?php endif; ?>
</div><!-- /modal-container -->

    <script type="text/javascript">
          jQuery(function($){
              if(!$('body').hasClass('wk-button-modal')){
                  $('body').addClass('wk-button-modal');
              }
          });

    </script>

