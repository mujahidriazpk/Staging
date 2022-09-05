<?php
$settings = $this->get_settings();
$form_input_placeholder_text = widgetkit_for_elementor_array_get($settings, 'placeholder_text');
$form_button_text = widgetkit_for_elementor_array_get($settings, 'button_text');
?>
<div class="wkfe-mailchimp-wrapper">
    <div id="mailchimp-status"></div>
    <form class="wkfe-newsletter-form-element" action="#" id="wkfe-mailchimp" method="POST">
        <div class="email">
            <label for="email" style="display:none;">Email</label>
            <input 
            class="mailchimp_email_field"
            type="email" 
            name="email" 
            id="email" 
            placeholder="<?php echo esc_attr( $form_input_placeholder_text ? $form_input_placeholder_text : '' ); ?>"
            required 
            />
        </div>
        <div class="submit">
            <?php wp_nonce_field('wkfe-ajax-security-nonce'); ?>
            <input type="submit" name="submit" id="submit" value="<?php echo $form_button_text ? $form_button_text : 'Submit'; ?>"/>
        </div>
    </form>
</div>
