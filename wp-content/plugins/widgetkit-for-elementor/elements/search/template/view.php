<?php
    use Elementor\Icons_Manager;
    $settings = $this->get_settings();
    $search_icon_alignment = widgetkit_for_elementor_array_get($settings, 'search_icon_alignment');
    $search_icon_for_handler = widgetkit_for_elementor_array_get($settings, 'search_icon_for_handler');
    $search_form_input_placeholder = widgetkit_for_elementor_array_get($settings, 'search_form_input_placeholder');
    $search_form_input_button_text = widgetkit_for_elementor_array_get($settings, 'search_form_input_button_text');
?>

    <div class="wkfe-search">
        <div id="wkfe-search-<?php echo $this->get_id(); ?>" class="wkfe-search-wrapper wkfe-search-<?php echo $this->get_id(); ?>">
            <div class="search-click-handler click-handler"> 
                <?php Icons_Manager::render_icon( $search_icon_for_handler, [ 'aria-hidden' => 'false', 'class' => 'search-handler-icon' ] ); ?>
            </div>
            <div class="<?php echo $search_icon_alignment; ?> wkfe-search-form-wrapper" style="display:none;">
                <form action="<?php echo home_url( '/' ); ?>" method="get">
                    <label class="screen-reader-text" for="search">Search in <?php echo home_url( '/' ); ?></label>
                    <input placeholder="<?php echo esc_attr__($search_form_input_placeholder); ?>" type="text" name="s" id="search" value="<?php the_search_query(); ?>" />
                    <input type="submit" id="searchsubmit" value="<?php echo esc_attr__( $search_form_input_button_text ) ?>" />
                </form>
            </div>
        </div>
    </div><!-- animation-text -->

    <script type="text/javascript">
        jQuery(function($){
            if(!$('body').hasClass('wkfe-search')){
                $('body').addClass('wkfe-search');
            }
        });

    </script>