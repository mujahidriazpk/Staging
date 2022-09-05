<?php 
    class WKFE_Dashboard_Elements{
        private static $instance; 
        private $pro_enable_status;
        private $widgetkit_get_settings;

        public static function init(){
            if(null === self::$instance){
                self::$instance = new self;
            }
            return self::$instance;
        }

        public function __construct($widgetkit_save_settings){
            $this->widgetkit_get_settings = $widgetkit_save_settings;
            $this->pro_enable_status = apply_filters('wkpro_enabled', false);
            $this->wkfe_dashboard_elements_content();
        }
        public function wkfe_dashboard_elements_content(){
            ?>
            <div class="wk-grid wk-grid-small wk-child-width-1-3" wk-grid>

                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Animated Headline', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-animation-text" name="widget-animation-text" <?php checked(1, $this->widgetkit_get_settings['widget-animation-text'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Blog Carousel', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-blog-carousel" name="widget-blog-carousel" <?php checked(1, $this->widgetkit_get_settings['widget-blog-carousel'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Blog Image', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-blog-image" name="widget-blog-image" <?php checked(1, $this->widgetkit_get_settings['widget-blog-image'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Button + Modal', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-button" name="widget-button" <?php checked(1, $this->widgetkit_get_settings['widget-button'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Blog Hover Animation', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-blog-hover-animation" name="widget-blog-hover-animation" <?php checked(1, $this->widgetkit_get_settings['widget-blog-hover-animation'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Click Tweet', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-click-tweet" name="widget-click-tweet" <?php checked(1, $this->widgetkit_get_settings['widget-click-tweet'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Contact', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-contact" name="widget-contact" <?php checked(1, $this->widgetkit_get_settings['widget-contact'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Contact Form', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-contact-form" name="widget-contact-form" <?php checked(1, $this->widgetkit_get_settings['widget-contact-form'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Content Carousel', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-content-carousel" name="widget-content-carousel" <?php checked(1, $this->widgetkit_get_settings['widget-content-carousel'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Countdown', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-countdown" name="widget-countdown" <?php checked(1, $this->widgetkit_get_settings['widget-countdown'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <!-- pro -->
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <span>
                            <?php echo esc_html__('Event List', 'widgetkit-for-elementor'); ?>    
                            <span class="pro-text">
                                <?php echo esc_html__('Pro', 'widgetkit-for-elementor'); ?>
                            </span>
                        </span>
                        
                        <label class="switch <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                            <input type="checkbox" id="wkpro-event-list" name="wkpro-event-list" <?php checked(1, $this->widgetkit_get_settings['wkpro-event-list'], $this->pro_enable_status) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div> 

                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Filterable Portfolio', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-portfolio" name="widget-portfolio" <?php checked(1, $this->widgetkit_get_settings['widget-portfolio'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>

                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Gallery', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-gallery" name="widget-gallery" <?php checked(1, $this->widgetkit_get_settings['widget-gallery'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>                                            
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Hover Image', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-hover-image" name="widget-hover-image" <?php checked(1, $this->widgetkit_get_settings['widget-hover-image'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Info Box', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-feature-box" name="widget-feature-box" <?php checked(1, $this->widgetkit_get_settings['widget-feature-box'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Image Compare', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-image-compare" name="widget-image-compare" <?php checked(1, $this->widgetkit_get_settings['widget-image-compare'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Lottie Animation', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-lottie-animation" name="widget-lottie-animation" <?php checked(1, $this->widgetkit_get_settings['widget-lottie-animation'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Mailchimp', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-mailchimp" name="widget-mailchimp" <?php checked(1, $this->widgetkit_get_settings['widget-mailchimp'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <!-- pro -->
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <span>
                            <?php echo esc_html__('Mobile Menu Toggle', 'widgetkit-for-elementor'); ?>    
                            <span class="pro-text">
                                <?php echo esc_html__('Pro', 'widgetkit-for-elementor'); ?>
                            </span>
                        </span>
                        
                        <label class="switch <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                            <input type="checkbox" id="wkpro-mobile-menu-toggle" name="wkpro-mobile-menu-toggle" <?php checked(1, $this->widgetkit_get_settings['wkpro-mobile-menu-toggle'], $this->pro_enable_status) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div> 
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo __('Pricing Single', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-pricing-single" name="widget-pricing-single" <?php checked(1, $this->widgetkit_get_settings['widget-pricing-single'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Pricing Icon', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-pricing-icon" name="widget-pricing-icon" <?php checked(1, $this->widgetkit_get_settings['widget-pricing-icon'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Pricing Tabs', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-pricing-tab" name="widget-pricing-tab" <?php checked(1, $this->widgetkit_get_settings['widget-pricing-tab'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Video Popup', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-video-popup" name="widget-video-popup" <?php checked(1, $this->widgetkit_get_settings['widget-video-popup'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <!-- pro -->
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <span>
                            <?php echo esc_html__('Post Tab', 'widgetkit-for-elementor'); ?>
                            <span class="pro-text">
                                <?php echo esc_html__('Pro', 'widgetkit-for-elementor'); ?>
                            </span>
                        </span>
                        
                        <label class="switch <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                            <input type="checkbox" id="wkpro-post-tab" name="wkpro-post-tab" <?php checked(1, $this->widgetkit_get_settings['wkpro-post-tab'], $this->pro_enable_status) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <!-- pro -->
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <span>
                            <?php echo esc_html__('Post Carousel', 'widgetkit-for-elementor'); ?>
                            <span class="pro-text">
                                <?php echo esc_html__('Pro', 'widgetkit-for-elementor'); ?>
                            </span>
                        </span>
                        
                        <label class="switch <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                            <input type="checkbox" id="wkpro-post-carousel" name="wkpro-post-carousel" <?php checked(1, $this->widgetkit_get_settings['wkpro-post-carousel'], $this->pro_enable_status) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <!-- pro -->
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <span>
                            <?php echo esc_html__('Post List', 'widgetkit-for-elementor'); ?>
                            <span class="pro-text">
                                <?php echo esc_html__('Pro', 'widgetkit-for-elementor'); ?>
                            </span>
                        </span>
                        
                        <label class="switch <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                            <input type="checkbox" id="wkpro-post-list" name="wkpro-post-list" <?php checked(1, $this->widgetkit_get_settings['wkpro-post-list'], $this->pro_enable_status) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <!-- pro -->
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <span>
                            <?php echo esc_html__('Post Grid Slider', 'widgetkit-for-elementor'); ?>
                            <span class="pro-text">
                                <?php echo esc_html__('Pro', 'widgetkit-for-elementor'); ?>
                            </span>
                        </span>
                    
                        <label class="switch <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                            <input type="checkbox" id="wkpro-grid-slider" name="wkpro-grid-slider" <?php checked(1, $this->widgetkit_get_settings['wkpro-grid-slider'], $this->pro_enable_status) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <!-- pro -->
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <span>
                            <?php echo esc_html__('Post Headline Slider', 'widgetkit-for-elementor'); ?>    
                            <span class="pro-text">
                                <?php echo esc_html__('Pro', 'widgetkit-for-elementor'); ?>
                            </span>
                        </span>
                        
                        <label class="switch <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                            <input type="checkbox" id="wkpro-headline-slider" name="wkpro-headline-slider" <?php checked(1, $this->widgetkit_get_settings['wkpro-headline-slider'], $this->pro_enable_status) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div> 
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Pros & Cons', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-pros-cons" name="widget-pros-cons" <?php checked(1, $this->widgetkit_get_settings['widget-pros-cons'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>

                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo __('Search', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-search" name="widget-search" <?php checked(1, $this->widgetkit_get_settings['widget-search'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                        <!-- <div class="wk-position-top-left wk-label">Pro</div> -->
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo __('Site Social', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-site-social" name="widget-site-social" <?php checked(1, $this->widgetkit_get_settings['widget-site-social'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                        <!-- <div class="wk-position-top-left wk-label">Pro</div> -->
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo __('Slider Animation', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-slider-animation" name="widget-slider-animation" <?php checked(1, $this->widgetkit_get_settings['widget-slider-animation'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                        <!-- <div class="wk-position-top-left wk-label">Pro</div> -->
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Slider Content Animation', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-slider-content-animation" name="widget-slider-content-animation" <?php checked(1, $this->widgetkit_get_settings['widget-slider-content-animation'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Slider Box Animation', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-slider-box-animation" name="widget-slider-box-animation" <?php checked(1, $this->widgetkit_get_settings['widget-slider-box-animation'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <!-- pro -->
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <span>
                            <?php echo esc_html__('Smart Toggle', 'widgetkit-for-elementor'); ?>    
                            <span class="pro-text">
                                <?php echo esc_html__('Pro', 'widgetkit-for-elementor'); ?>
                            </span>
                        </span>
                        
                        <label class="switch <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                            <input type="checkbox" id="wkpro-smart-toggle" name="wkpro-smart-toggle" <?php checked(1, $this->widgetkit_get_settings['wkpro-smart-toggle'], $this->pro_enable_status) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div> 
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Social Share Animation', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-social-share-animation" name="widget-social-share-animation" <?php checked(1, $this->widgetkit_get_settings['widget-social-share-animation'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Social Share Collapse', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-social-share-collapse" name="widget-social-share-collapse" <?php checked(1, $this->widgetkit_get_settings['widget-social-share-collapse'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Testimonial', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-testimonial" name="widget-testimonial" <?php checked(1, $this->widgetkit_get_settings['widget-testimonial'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Team', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-team" name="widget-team" <?php checked(1, $this->widgetkit_get_settings['widget-team'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>

                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Tilt Box', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-tilt-box" name="widget-tilt-box" <?php checked(1, $this->widgetkit_get_settings['widget-tilt-box'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>









                <div class="wk-width-1-1">
                    <h3>Legacy Widgets</h3>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Blog Sidebar', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-blog-sidebar" name="widget-blog-sidebar" <?php checked(1, $this->widgetkit_get_settings['widget-blog-sidebar'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Blog Revert', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-blog-revert" name="widget-blog-revert" <?php checked(1, $this->widgetkit_get_settings['widget-blog-revert'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>

                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Post Carousel', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-post-carousel" name="widget-post-carousel" <?php checked(1, $this->widgetkit_get_settings['widget-post-carousel'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Team Overlay', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-team-overlay" name="widget-team-overlay" <?php checked(1, $this->widgetkit_get_settings['widget-team-overlay'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Team Animation', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-team-animation" name="widget-team-animation" <?php checked(1, $this->widgetkit_get_settings['widget-team-animation'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Team Round', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-team-round" name="widget-team-round" <?php checked(1, $this->widgetkit_get_settings['widget-team-round'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Team Verticle Icon', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-team-verticle-icon" name="widget-team-verticle-icon" <?php checked(1, $this->widgetkit_get_settings['widget-team-verticle-icon'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>

                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Testimonial Single', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-testimonial-single" name="widget-testimonial-single" <?php checked(1, $this->widgetkit_get_settings['widget-testimonial-single'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>

                <div>
                    <div class="wk-card wk-card-default wk-card-hover wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                        <?php echo esc_html__('Testimonial Center', 'widgetkit-for-elementor'); ?>
                        <label class="switch">
                            <input type="checkbox" id="widget-testimonial-center" name="widget-testimonial-center" <?php checked(1, $this->widgetkit_get_settings['widget-testimonial-center'], true) ?>>
                            <span class="rectangle round"></span>
                        </label>
                    </div>
                </div>

                </div>
            <?php 
        }
    }
?>