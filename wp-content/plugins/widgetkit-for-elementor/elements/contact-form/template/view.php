<?php
    $contact_form = $this->get_settings();
    $id = $this->get_id();
    use Elementor\Icons_Manager;
?>

        <div class="wk-contact-form <?php echo ($contact_form['button_align'] == 'center')? 'button-center':'';?> <?php echo ($contact_form['button_align'] == 'right')? 'button-right':'';?> <?php echo ($contact_form['button_width_type'] == 'custom')? 'wk-submit-button-custom':'';?>">
            <?php if ($contact_form['contact_form_list'] == 'contact-7'):?>
                <?php if (function_exists( 'wpcf7' ) ):?>
                    <?php echo do_shortcode( '[contact-form-7 id="' . $contact_form['choose_form_7'] . '" ]' ); ?>
                <?php else: ?>
                    <p>Contact Form 7</strong> is not installed/activated on your site. Please install and activate <strong>Contact Form 7</strong> first.</p>
                <?php endif; ?>

            <?php elseif($contact_form['contact_form_list'] == 'weforms'): ?>
                <?php if (class_exists( 'WeForms' ) ):?>
                    <p><?php echo do_shortcode( '[weforms id="' . $contact_form['choose_weforms'] . '" ]' ); ?></p>
                <?php else: ?>
                    <p>WeForms</strong> is not installed/activated on your site. Please install and activate <strong>WeForms</strong> first.</p>
                <?php endif; ?>

            <?php elseif($contact_form['contact_form_list'] == 'wpforms'): ?>
                <?php if (class_exists( '\WPForms\WPForms') ):?>
                    <?php echo do_shortcode( '[wpforms id="' . $contact_form['choose_wpforms'] . '" ]' ); ?>
                <?php else: ?>
                    <p>Wpforms</strong> is not installed/activated on your site. Please install and activate <strong>Wpforms</strong> first.</p>
                <?php endif; ?>
            <?php else: ?>
                <?php echo do_shortcode( '[contact-form-7 id="' . $contact_form['choose_form_7'] . '" ]' ); ?>
            <?php endif;?>
        </div>

        <script type="text/javascript">
            jQuery(function($){
                if(!$('body').hasClass('wk-contact-form')){
                    $('body').addClass('wk-contact-form');
                }
            });

        </script>



