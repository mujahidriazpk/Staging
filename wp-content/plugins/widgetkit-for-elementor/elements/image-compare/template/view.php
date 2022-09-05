<?php
    $image_compare = $this->get_settings();
    $id = $this->get_id();
?>
    <div class="wk-image-compare">
         <div class="row">
            <div id="image-compare-<?php echo $id; ?>" class="image-compare-container">
                
                <img src="<?php echo $image_compare['before_image']['url']; ?>" alt="before">
                <img src="<?php echo $image_compare['after_image']['url']; ?>" alt="After">
            </div>
        </div>
    </div>

    <script>
        jQuery(document).ready(function(){
            jQuery("#image-compare-<?php echo $id;?>").image_compare({

                //  How much of the before image is visible when the page loads
                default_offset_pct: 0.5,

                // or vertical
                orientation: '<?php echo $image_compare['orientation'];?>',
                // orientation: 'vertical',

                // label text
                after_label: '<?php echo $image_compare['after_label']; ?>',
                before_label: '<?php echo $image_compare['before_label']; ?>',
               

                // enable/disable overlay
                <?php if ($image_compare['hide_overlay'] == 'yes'):?>
                    no_overlay: false,  
                <?php else: ?>
                    no_overlay: true,
                <?php endif; ?>

                // move with handle
                move_with_handle_only: false,
                
                <?php if($image_compare['click_enable'] == 'no'): ?>
                    // click to move
                    click_to_move: true
                <?php else: ?>
                    click_to_move: false
                <?php endif; ?>
              
            });       
        }); 
    </script>

    <script type="text/javascript">
          jQuery(function($){
              if(!$('body').hasClass('wk-image-compare')){
                  $('body').addClass('wk-image-compare');
              }
          });

    </script>






