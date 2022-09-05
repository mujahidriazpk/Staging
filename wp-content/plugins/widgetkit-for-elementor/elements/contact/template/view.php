<?php
    use Elementor\Icons_Manager;
    $settings = $this->get_settings();
    $contact_icon_handler = widgetkit_for_elementor_array_get($settings, 'contact_icon_handler');
    $contact_icon_alignment = widgetkit_for_elementor_array_get($settings, 'contact_icon_alignment');
    $contact_header = widgetkit_for_elementor_array_get($settings, 'contact_header');
    $contact_title = widgetkit_for_elementor_array_get($settings, 'contact_title');
    $contact_content = widgetkit_for_elementor_array_get($settings, 'contact_content');
    $contact_box_position = widgetkit_for_elementor_array_get($settings, 'contact_box_position');
    $dynamic_field_settings = $this->get_settings_for_display();
?>

    <div class="wkfe-contact">
        <div id="wkfe-contact-<?php echo $this->get_id(); ?>" class="wkfe-contact-wrapper wkfe-contact-<?php echo $this->get_id(); ?>">
            <div class="contact-click-handler"> 
                <div data-handler="<?php echo $this->get_id(); ?>" class="icon-svg-wrapper">
                    <?php Icons_Manager::render_icon( $contact_icon_handler, [ 'aria-hidden' => 'false', 'class' => 'contact-handler-icon' ] ); ?>
                </div>
            </div>
            <div class="<?php echo $contact_icon_alignment; ?> wkfe-contact-content-wrapper <?php echo $contact_box_position; ?>" style="display:none;">
                <div class=" <?php echo $contact_icon_alignment; ?> arrow-up"></div>
                <div class="content-header">
                    <?php echo $dynamic_field_settings['contact_header']; ?>
                </div>
                <div class="content-title">
                    <?php echo $dynamic_field_settings['contact_title']; ?>
                </div>
                <div class="contact-content"><?php echo $contact_content; ?></div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        jQuery(function($){
            if(!$('body').hasClass('wkfe-contact')){
                $('body').addClass('wkfe-contact');
            }
        });
    </script>