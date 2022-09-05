<?php 

class WKFE_Elements{
    private static $instance;

    public static function init(){
        if(null === self::$instance ){
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct(){
        $this->load_admin_files();
        $this->load_addon_files();
    }
    public function load_admin_files(){
        require_once WK_PATH . '/elements/helper-functions.php';
        require_once WK_PATH . '/admin/admin-init.php';
        require_once WK_PATH . '/admin/notices/admin-notices.php';
        // require_once WK_PATH . '/admin/includes/system-info.php';
    }
    public function load_addon_files(){
        $widgetkit_elements_keys = [
            'widget-slider-animation', 
            'widget-slider-content-animation', 
            'widget-slider-box-animation',
            'widget-portfolio',
            'widget-gallery',
            'widget-pricing-single', 
            'widget-pricing-icon', 
            'widget-pricing-tab',
            'widget-testimonial',
            'widget-testimonial-single',
            'widget-testimonial-center',
            'widget-team',
            'widget-team-overlay',
            'widget-team-verticle-icon',
            'widget-team-round',
            'widget-team-animation',
            'widget-blog-carousel',
            'widget-blog-sidebar',
            'widget-blog-revert',
            'widget-blog-hover-animation',
            'widget-blog-image',
            'widget-countdown',
            'widget-animation-text',
            'widget-content-carousel',
            'widget-button',
            'widget-hover-image',
            'widget-feature-box',
            'widget-search',
            'widget-contact',
            'widget-site-social',
            'widget-social-share-animation',
            'widget-social-share-collapse',
            'widget-post-carousel',
            'widget-image-compare',
            'widget-tilt-box',
            'widget-contact-form',
            'widget-pros-cons',
            'widget-click-tweet',
            'widget-video-popup',
            'widget-lottie-animation',
            'widget-mailchimp',
        ];
        $widgetkit_default_settings = array_fill_keys( $widgetkit_elements_keys, true );
        
        /**
         * get active component key from db
         */
        $get_active_component_from_db = get_option( 'widgetkit_save_settings', $widgetkit_default_settings );
        /**
         * if any diff found from db
         * then merge with load key
         * and update the db
         */
        if(array_diff_key($get_active_component_from_db, $widgetkit_default_settings)){
            $merged_arr = array_merge($widgetkit_default_settings, $get_active_component_from_db);
            update_option('widgetkit_save_settings', $merged_arr);
        }
        /**
         * get the updated data from db
         */

        $check_component_active = get_option( 'widgetkit_save_settings', $widgetkit_default_settings );

        if( $check_component_active['widget-animation-text'] ) {
            require_once WK_PATH . '/elements/animation-text/widget.php';
        }
        if( $check_component_active['widget-button'] ) {
            require_once WK_PATH . '/elements/button-modal/widget.php';
        }
        if( $check_component_active['widget-blog-carousel'] ) {
            require_once WK_PATH . '/elements/blog-1/widget.php';
        }
        if( $check_component_active['widget-blog-sidebar'] ) {
            require_once WK_PATH . '/elements/blog-2/widget.php';
        }
        if( $check_component_active['widget-blog-revert'] ) {
            require_once WK_PATH . '/elements/blog-3/widget.php';
        }
        if( $check_component_active['widget-blog-hover-animation'] ) {
            require_once WK_PATH . '/elements/blog-4/widget.php';
        }
        if( $check_component_active['widget-blog-image'] ) {
            require_once WK_PATH . '/elements/blog-5/widget.php';
        }
        if( $check_component_active['widget-click-tweet'] ) {
            require_once WK_PATH . '/elements/click-tweet/widget.php';
        }
        if( $check_component_active['widget-contact'] ) {
            require_once WK_PATH . '/elements/contact/widget.php';
        }
        if( $check_component_active['widget-contact-form'] ) {
            require_once WK_PATH . '/elements/contact-form/widget.php';
        }
        if( $check_component_active['widget-content-carousel'] ) {
            require_once WK_PATH . '/elements/content-carousel/widget.php';
        }
        if( $check_component_active['widget-countdown'] ) {
            require_once WK_PATH . '/elements/countdown/widget.php';
        }
        if( $check_component_active['widget-post-carousel'] ) {
            require_once WK_PATH . '/elements/carousel/widget.php';
        }
        if( $check_component_active['widget-portfolio'] ) {
            require_once WK_PATH . '/elements/portfolio/widget.php'; 
        }
        if( $check_component_active['widget-gallery'] ) {
            require_once WK_PATH . '/elements/gallery/widget.php';
        }
        if( $check_component_active['widget-hover-image'] ) {
            require_once WK_PATH . '/elements/hover-image/widget.php';
        }
        if( $check_component_active['widget-feature-box'] ) {
            require_once WK_PATH . '/elements/image-feature/widget.php';
        }
        if( $check_component_active['widget-image-compare'] ) {
            require_once WK_PATH . '/elements/image-compare/widget.php';
        }
        if( $check_component_active['widget-lottie-animation'] ) {
            require_once WK_PATH . '/elements/lottie/widget.php';
        }
        if( $check_component_active['widget-mailchimp'] ) {
            require_once WK_PATH . '/elements/mailchimp/widget.php';
        }
        if( $check_component_active['widget-pricing-single'] ) {
            require_once WK_PATH . '/elements/pricing-1/widget.php';
        }
        if( $check_component_active['widget-pricing-icon'] ) {
            require_once WK_PATH . '/elements/pricing-2/widget.php';
        }
        if( $check_component_active['widget-pricing-tab'] ) {
            require_once WK_PATH . '/elements/pricing-tab/widget.php';
        }
        if( $check_component_active['widget-pros-cons'] ) {
            require_once WK_PATH . '/elements/pros-cons/widget.php';
        }
        if( $check_component_active['widget-search'] ) {
            require_once WK_PATH . '/elements/search/widget.php';
        }
        if( $check_component_active['widget-site-social'] ) {
            require_once WK_PATH . '/elements/site-social/widget.php';
        }
        if( $check_component_active['widget-slider-animation'] ) {
            require_once WK_PATH . '/elements/slider-1/widget.php';
        }
        if( $check_component_active['widget-slider-content-animation'] ) {
            require_once WK_PATH . '/elements/slider-2/widget.php';
        }
        if( $check_component_active['widget-slider-box-animation'] ) {
            require_once WK_PATH . '/elements/slider-3/widget.php';
        }
        if( $check_component_active['widget-social-share-animation'] ) {
            require_once WK_PATH . '/elements/social-share-1/widget.php';
        }
        if( $check_component_active['widget-social-share-collapse'] ) {
            require_once WK_PATH . '/elements/social-share-2/widget.php';
        }
        if( $check_component_active['widget-testimonial'] ) {
            require_once WK_PATH . '/elements/testimonial/widget.php';
        }
        if( $check_component_active['widget-testimonial-center'] ) {
            require_once WK_PATH . '/elements/testimonial-1/widget.php';
        }
        if( $check_component_active['widget-testimonial-single'] ) {
            require_once WK_PATH . '/elements/testimonial-2/widget.php';
        }
        if( $check_component_active['widget-team'] ) {
            require_once WK_PATH . '/elements/team/widget.php';
        }
        if( $check_component_active['widget-team-overlay'] ) {
            require_once WK_PATH . '/elements/team-1/widget.php';
        }
        if( $check_component_active['widget-team-verticle-icon'] ) {
            require_once WK_PATH . '/elements/team-2/widget.php';
        }
        if( $check_component_active['widget-team-round'] ) {
            require_once WK_PATH . '/elements/team-3/widget.php';
        }
        if( $check_component_active['widget-team-animation'] ) {
            require_once WK_PATH . '/elements/team-4/widget.php';
        }
        if( $check_component_active['widget-tilt-box'] ) {
            require_once WK_PATH . '/elements/tilt-box/widget.php';
        }
        if( $check_component_active['widget-video-popup'] ) {
            require_once WK_PATH . '/elements/video-popup/widget.php';
        }
    }

}

?>