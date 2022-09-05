<?php
    $settings = $this->get_settings();
    $text = widgetkit_for_elementor_array_get($settings, 'tweet_text');
    $icon = widgetkit_for_elementor_array_get($settings, 'tweet_button_icon');
    $button_text = widgetkit_for_elementor_array_get($settings, 'tweet_button_text');
?>

    <div class="wkfe-click-to-tweet row">
        <div class="click-to-tweet-wrapper">

            <p class="tweet-text">
                <?php echo esc_html($text); ?>
            </p>
            
            <div class="button-wrapper">
                <button class="wkfe-tweet">
                    <span class="icon-wrapper">
                        <i class="<?php echo esc_html($icon); ?>"></i>
                    </span>
                    <?php echo esc_html($button_text); ?>
                </button>
            </div>

        </div>
    </div>

        <script type="text/javascript">
          jQuery(function($){
              if(!$('body').hasClass('wk-click-to-tweet')){
                  $('body').addClass('wk-click-to-tweet');
              }
          });

    </script>