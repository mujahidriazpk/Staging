<?php
// Silence is golden.

    $settings = $this->get_settings();

    // JSON FILE
   
    $this->add_render_attribute('wrapper','class','lottie-animation-wrapper');


    if( $settings['choose_data_file_source'] == 'url' ) {
        $this->add_render_attribute('wrapper','data-animation-path',esc_url($settings['json_file_link']));
    }

    if( $settings['choose_data_file_source'] == 'upload' ) {
        $this->add_render_attribute('wrapper','data-animation-path',esc_url($settings['json_file']['url']));
    }

    // animation renderer type
    $this->add_render_attribute('wrapper','data-animation-renderer',esc_attr($settings['animation_renderer_type']));
    
    // Link
    $this->add_render_attribute('link','href',esc_url($settings['link']['url']));

    if( $settings['link']['is_external'] ) {
        $this->add_render_attribute('link','target','_blank');
    }

    if( $settings['link']['nofollow'] ) {
        $this->add_render_attribute('link','rel','nofollow');
    }

    // animation play type
    $this->add_render_attribute('wrapper','data-animation-play',esc_attr($settings['animation_play_type']));

    // animation speed
    $this->add_render_attribute('wrapper','data-animation-speed',esc_attr($settings['animation_speed']));

    // animation loop
    if( $settings['choose_loop'] == 'yes' ) {
        $this->add_render_attribute('wrapper','data-animation-loop','true');
    } else {
        $this->add_render_attribute('wrapper','data-animation-loop','false');
    }

    // animation reverse
    if( $settings['choose_reversed'] == 'yes' ) {
        $this->add_render_attribute('wrapper','data-animation-reverse','true');
    } else {
        $this->add_render_attribute('wrapper','data-animation-reverse','false');
    }
    
?>
<figure>
    <?php if( $settings['choose_link'] == 'yes' && $settings['link']['url'] ) : ?>
        <a <?php echo $this->get_render_attribute_string( 'link' ); ?>>
    <?php endif; ?>

        <div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>></div>

    <?php if( $settings['choose_link'] == 'yes' && $settings['link']['url'] ) : ?>
        </a>
    <?php endif; ?>
    
    <?php if( !empty( $settings['widget_caption'] ) ): ?>
        <figcaption><?php echo esc_html($settings['widget_caption']); ?></figcaption>
    <?php endif; ?>
</figure>