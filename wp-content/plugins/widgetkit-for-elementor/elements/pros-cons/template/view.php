<?php
    $settings = $this->get_settings();
    $alignment = widgetkit_for_elementor_array_get($settings, 'layout_align');
    $title = widgetkit_for_elementor_array_get($settings, 'feature_title');
    $icon = widgetkit_for_elementor_array_get($settings, 'feature_icon');
    $lists = widgetkit_for_elementor_array_get($settings, 'feature_lists');
?>

    <div class="wkfe-feature-list row">
        <div class="feature-list-wrapper">

            <div class="col-md-12 column">
                <h2 class="title <?php echo 'layout-'.$alignment ?>">
                    <span class="icon">
                        <i class="<?php echo $icon; ?>"></i>
                    </span>
                    <span class="title-text"><?php echo esc_html($title); ?></span>
                </h2>
                <ul class="lists">
                    <?php foreach($lists as $list): ?>
                        <li>
                            <?php if ($list['single_feature_icon']):?>
                                <i class="<?php echo $list['single_feature_icon']; ?>"></i>
                             <?php endif;?>
                            <span>
                                <?php echo $list['single_feature_input']; ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

        </div>
    </div>

    <script type="text/javascript">
        jQuery(function($){
              if(!$('body').hasClass('wk-pros-cons')){
                  $('body').addClass('wk-pros-cons');
              }
        });

    </script>