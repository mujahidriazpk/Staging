<?php
use Elementor\Group_Control_Image_Size;
?>
<div class="wk-flex wk-flex-center wk-grid-match">
    <div class="wk-card wk-card-default wk-grid-collapse wk-margin wk-testimonial-3 wk-flex-middle" wk-grid>
    <?php if($testimonials['thumbnail_position_horizontal'] == 'left' && $testimonial['testimonial_thumb_image']['url']): ;?>
        <div class="wk-cover-container wk-width-1-2@m">
            <?php if($testimonial['testimonial_thumb_image']['url']):?>
        
                <div class="wk-card-media-left wk-overflow-hidden wk-padding-bottom">
                <?php if($testimonial['testimonial_thumb_image']['id']): ?>
                    <img src="<?php echo Group_Control_Image_Size::get_attachment_image_src($testimonial['testimonial_thumb_image']['id'], 'testimonial_image', $testimonials ); ?>" alt="<?php echo $testimonial['testimonial_title']; ?>">
                <?php else:?>
                    <img src="<?php echo $testimonial['testimonial_thumb_image']['url'];?>" alt="<?php echo $testimonial['testimonial_title']; ?>">  
                <?php endif;?>  
                </div>
            
            <?php endif; ?>
        </div>
    <?php endif; ?> 

    <div class="wk-width-expand">
        <div class="wk-card-body">
            <?php if ($testimonial['testimonial_content']): ?>
            <div class="wk-text-normal"><?php echo $testimonial['testimonial_content']; ?></div>
            <?php endif; ?>
            <?php if ($testimonial['testimonial_designation']): ?>
                <?php if ($testimonials['designation_position'] == 'vertical_top'): ?>
                    <span class="wk-text-meta wk-inline-block top"><?php echo $testimonial['testimonial_designation']; ?></span>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($testimonial['testimonial_title']): ?>
            <<?php echo $testimonial_header_tag;?>  class="wk-card-title wk-margin-remove">
                <?php if ($testimonial['content_demo_link']): ?>
                        <a href="<?php echo $testimonial['content_demo_link']['url']; ?>" <?php echo $testimonial['content_demo_link']['is_external']? 'target="_blank"' : '"rel="nofollow"'; ?>><?php echo $testimonial['testimonial_title']; ?></a>
                <?php else: ?>
                        <?php echo $testimonial['testimonial_title']; ?>
                <?php endif; ?> 
                <?php if ($testimonial['testimonial_designation']): ?>
                    <?php if ($testimonials['designation_position'] == 'horizontal_right'): ?>
                        <span class="wk-text-meta wk-inline-block right"><?php echo $testimonial['testimonial_designation']; ?></span>
                    <?php endif; ?>
                <?php endif; ?>
            </<?php echo $testimonial_header_tag;?>>  
        <?php endif; ?>

            <?php if ($testimonial['testimonial_designation']): ?>
                <?php if ($testimonials['designation_position'] == 'vertical_bottom'): ?>
                    <span class="wk-text-meta wk-inline-block bottom"><?php echo $testimonial['testimonial_designation']; ?></span>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>
        <?php if($testimonials['thumbnail_position_horizontal'] == 'right' && $testimonial['testimonial_thumb_image']['url']): ;?>
        <div class="wk-cover-container wk-width-1-2@m">
            <?php if($testimonial['testimonial_thumb_image']['url']):?>
                
                <div class="wk-card-media-right wk-overflow-hidden wk-padding-bottom">
                <?php if($testimonial['testimonial_thumb_image']['id']): ?>
                    <img src="<?php echo Group_Control_Image_Size::get_attachment_image_src($testimonial['testimonial_thumb_image']['id'], 'testimonial_image', $testimonials ); ?>" alt="<?php echo $testimonial['testimonial_title']; ?>">
                <?php else:?>
                    <img src="<?php echo $testimonial['testimonial_thumb_image']['url'];?>" alt="<?php echo $testimonial['testimonial_title']; ?>">  
                <?php endif;?>  
                </div>
            
            <?php endif; ?>
        </div>
    <?php endif; ?> 
</div>
</div>