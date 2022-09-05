<?php 
    class WKFE_Dashboard_PRO_Integration{
        private static $instance; 
        private $pro_enable_status;
        private $pro_integration_data;
        private $widgetkit_get_woo_settings;
        private $widgetkit_get_woo_single_settings;
        private $widgetkit_get_ld_settings;
        private $widgetkit_get_lp_settings;
        private $widgetkit_get_sensei_settings;
        private $widgetkit_get_lifter_settings;
        private $widgetkit_get_tutor_settings;

        public static function init(){
            if(null === self::$instance){
                self::$instance = new self;
            }
            return self::$instance;
        }

        public function __construct($pro_integration_data){
            $this->widgetkit_get_woo_settings = $pro_integration_data['widgetkit_get_woo_settings'];
            $this->widgetkit_get_woo_single_settings = $pro_integration_data['widgetkit_get_woo_single_settings'];
            $this->widgetkit_get_ld_settings = $pro_integration_data['widgetkit_get_ld_settings'];
            $this->widgetkit_get_lp_settings = $pro_integration_data['widgetkit_get_lp_settings'];
            $this->widgetkit_get_sensei_settings = $pro_integration_data['widgetkit_get_sensei_settings'];
            $this->widgetkit_get_lifter_settings = $pro_integration_data['widgetkit_get_lifter_settings'];
            $this->widgetkit_get_tutor_settings = $pro_integration_data['widgetkit_get_tutor_settings'];
            $this->pro_enable_status = apply_filters('wkpro_enabled', false);
            $this->wkfe_dashboard_pro_integration_content();
        }
        public function wkfe_dashboard_pro_integration_content(){
            ?>
            <div class="wk-pro-integrated-plugin">
                <!-- WooCommerce -->
                <div class="wk-padding-small wk-background-muted">
                    <div class="" wk-grid>
                        <div class="wk-width-auto@m wk-card-media-left wk-cover-container">
                            <img src="<?php echo plugins_url('../assets/images/woocommerce-logo.svg', __FILE__)?>" width="100">
                        </div>
                        <div class="wk-width-expand@m">
                            <div class="wk-card-body wk-padding-remove">
                                <div class="wk-flex wk-flex-between wk-flex-middle wk-margin-small-bottom">
                                    <h3 class="wk-card-title wk-margin-remove-top wk-margin-remove-bottom"><?php esc_html_e('WooCommerce', 'widgetkit-for-elementor')?></h3>
                                    <a class="demo-button-for-pro wk-button-primary" href="https://widgetkit.themesgrove.com/#pro-element" target="_blank"><?php esc_html_e('Demo', 'widgetkit-for-elementor');?></a>
                                    <!-- <?php //if (!apply_filters('wkpro_enabled', false)): ?>
                                        <span class="wk-label">Pro</span>
                                    <?php //else:?>
                                        <label class="switch">
                                            <input type="checkbox" id="widgetkit-pro-woocommerce" name="widgetkit-pro-woocommerce" <?php //checked(1, 'widgetkit-pro-woocommerce', true) ?>>
                                            <span class="rectangle round"></span>
                                        </label>
                                    <?php //endif; ?> -->
                                </div>
                                <p><?php esc_html_e('Build your shop quickly with our powerful WooCommerce Elements.', 'widgetkit-for-elementor');?></p>
                                
                            </div>
                        </div>
                    </div>
                    <div class="woo-elements">
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('Woo Products', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?> <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-woo-product" name="wke-woo-product" <?php checked(1, $this->widgetkit_get_woo_settings['wke-woo-product'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('Woo Product Carousel', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-woo-product-carousel" name="wke-woo-product-carousel" <?php checked(1, $this->widgetkit_get_woo_settings['wke-woo-product-carousel'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('Woo Categories', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-woo-categories" name="wke-woo-categories" <?php checked(1, $this->widgetkit_get_woo_settings['wke-woo-categories'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('Woo Recent Product', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-woo-recent-product" name="wke-woo-recent-product" <?php checked(1, $this->widgetkit_get_woo_settings['wke-woo-recent-product'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <div class="wk-width-auto@m wk-card-media-left wk-cover-container">
                            <h2>Single Product Elements</h2>
                        </div>
                    </div>
                    <div class="woo-elements woo-single-elements">
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('Thumbnail', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?> ">
                                <input type="checkbox" id="wke-woo-single-product-thumbnail" name="wke-woo-single-product-thumbnail" <?php checked(1, $this->widgetkit_get_woo_single_settings['wke-woo-single-product-thumbnail'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('Title', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?> ">
                                <input type="checkbox" id="wke-woo-single-product-title" name="wke-woo-single-product-title" <?php checked(1, $this->widgetkit_get_woo_single_settings['wke-woo-single-product-title'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('Price', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-woo-single-product-price" name="wke-woo-single-product-price" <?php checked(1, $this->widgetkit_get_woo_single_settings['wke-woo-single-product-price'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('Short Description', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-woo-single-product-short-description" name="wke-woo-single-product-short-description" <?php checked(1, $this->widgetkit_get_woo_single_settings['wke-woo-single-product-short-description'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('Stock Status', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-woo-single-product-stock-status" name="wke-woo-single-product-stock-status" <?php checked(1, $this->widgetkit_get_woo_single_settings['wke-woo-single-product-stock-status'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('Rating', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-woo-single-product-rating" name="wke-woo-single-product-rating" <?php checked(1, $this->widgetkit_get_woo_single_settings['wke-woo-single-product-rating'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('Cart Button', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-woo-single-product-cart-button" name="wke-woo-single-product-cart-button" <?php checked(1, $this->widgetkit_get_woo_single_settings['wke-woo-single-product-cart-button'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('SKU', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-woo-single-product-sku" name="wke-woo-single-product-sku" <?php checked(1, $this->widgetkit_get_woo_single_settings['wke-woo-single-product-sku'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('Categories', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-woo-single-product-categories" name="wke-woo-single-product-categories" <?php checked(1, $this->widgetkit_get_woo_single_settings['wke-woo-single-product-categories'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('Additional Information', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-woo-single-product-additional-information" name="wke-woo-single-product-additional-information" <?php checked(1, $this->widgetkit_get_woo_single_settings['wke-woo-single-product-additional-information'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('Review', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?> ">
                                <input type="checkbox" id="wke-woo-single-product-review" name="wke-woo-single-product-review" <?php checked(1, $this->widgetkit_get_woo_single_settings['wke-woo-single-product-review'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('Related Product', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-woo-single-product-related-product" name="wke-woo-single-product-related-product" <?php checked(1, $this->widgetkit_get_woo_single_settings['wke-woo-single-product-related-product'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        
                    </div>
                </div>
                <!-- LearnDash -->
                <div class="wk-padding-small wk-background-muted">
                    <div class="" wk-grid>
                        <div class="wk-width-auto@m wk-card-media-left wk-cover-container">
                            <img src="<?php echo plugins_url('../assets/images/learndash-logo.png', __FILE__)?>" width="100">
                        </div>
                        <div class="wk-width-expand@m">
                            <div class="wk-card-body wk-padding-remove">
                                <div class="wk-flex wk-flex-between wk-flex-middle wk-margin-small-bottom">
                                    <h3 class="wk-card-title wk-margin-remove-top wk-margin-remove-bottom"><?php esc_html_e('LearnDash', 'widgetkit-for-elementor')?></h3>
                                    <a class="demo-button-for-pro wk-button-primary" href="https://widgetkit.themesgrove.com/#pro-element" target="_blank"><?php esc_html_e('Demo', 'widgetkit-for-elementor');?></a>
                                </div>
                                <p><?php esc_html_e('Our learndash elments helps you to create your course landing page more smoothly.', 'widgetkit-for-elementor');?></p>
                            </div>
                        </div>
                    </div>
                    <div class="woo-elements">
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('LearnDash Course List', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-ld-course-list" name="wke-ld-course-list" <?php checked(1, $this->widgetkit_get_ld_settings['wke-ld-course-list'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('LearnDash Course Tab', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-ld-course-tab" name="wke-ld-course-tab" <?php checked(1, $this->widgetkit_get_ld_settings['wke-ld-course-tab'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('LearnDash Course Banner', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-ld-course-banner" name="wke-ld-course-banner" <?php checked(1, $this->widgetkit_get_ld_settings['wke-ld-course-banner'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('LearnDash Course Certificate', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-ld-course-certificate" name="wke-ld-course-certificate" <?php checked(1, $this->widgetkit_get_ld_settings['wke-ld-course-certificate'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('LearnDash Course Enrollment', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-ld-course-enrollment" name="wke-ld-course-enrollment" <?php checked(1, $this->widgetkit_get_ld_settings['wke-ld-course-enrollment'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('LearnDash Course Meta Info', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-ld-course-meta-info" name="wke-ld-course-meta-info" <?php checked(1, $this->widgetkit_get_ld_settings['wke-ld-course-meta-info'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('LearnDash Course Progress', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-ld-course-progress" name="wke-ld-course-progress" <?php checked(1, $this->widgetkit_get_ld_settings['wke-ld-course-progress'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('LearnDash Course Resource', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-ld-course-resource" name="wke-ld-course-resource" <?php checked(1, $this->widgetkit_get_ld_settings['wke-ld-course-resource'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('LearnDash Course Tab Content', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-ld-course-tab-content" name="wke-ld-course-tab-content" <?php checked(1, $this->widgetkit_get_ld_settings['wke-ld-course-tab-content'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('LearnDash Related Course', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-ld-course-related-course" name="wke-ld-course-related-course" <?php checked(1, $this->widgetkit_get_ld_settings['wke-ld-course-related-course'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('LearnDash Course Content', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-ld-course-curriculum" name="wke-ld-course-curriculum" <?php checked(1, $this->widgetkit_get_ld_settings['wke-ld-course-curriculum'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('LearnDash Course Instructor', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-ld-course-instructor" name="wke-ld-course-instructor" <?php checked(1, $this->widgetkit_get_ld_settings['wke-ld-course-instructor'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('LearnDash Course Payments Button', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-ld-course-payments-button" name="wke-ld-course-payments-button" <?php checked(1, $this->widgetkit_get_ld_settings['wke-ld-course-payments-button'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('LearnDash Course Content', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-ld-course-content" name="wke-ld-course-content" <?php checked(1, $this->widgetkit_get_ld_settings['wke-ld-course-content'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <!-- LearnPress -->
                <div class="wk-padding-small wk-background-muted">
                    <div class="" wk-grid>
                        <div class="wk-width-auto@m wk-card-media-left wk-cover-container">
                            <img src="<?php echo plugins_url('../assets/images/learnpress-logo.png', __FILE__)?>" width="100">
                        </div>
                        <div class="wk-width-expand@m">
                            <div class="wk-card-body wk-padding-remove">
                                <div class="wk-flex wk-flex-between wk-flex-middle wk-margin-small-bottom">
                                    <h3 class="wk-card-title wk-margin-remove-top wk-margin-remove-bottom"><?php esc_html_e('LearnPress', 'widgetkit-for-elementor')?></h3>
                                    <a class="demo-button-for-pro wk-button-primary" href="https://widgetkit.themesgrove.com/#pro-element" target="_blank"><?php esc_html_e('Demo', 'widgetkit-for-elementor');?></a>
                                </div>
                                <p><?php esc_html_e('Our more customizable learnpress elements lets you to build your site more quickly.', 'widgetkit-for-elementor');?></p>
                            </div>
                        </div>
                    </div>
                    <div class="woo-elements">
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('LearnPress Course List', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-lp-course-list" name="wke-lp-course-list" <?php checked(1, $this->widgetkit_get_lp_settings['wke-lp-course-list'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('LearnPress Course Tab', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-lp-course-tab" name="wke-lp-course-tab" <?php checked(1, $this->widgetkit_get_lp_settings['wke-lp-course-tab'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('LearnPress Course Category', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-lp-course-category" name="wke-lp-course-category" <?php checked(1, $this->widgetkit_get_lp_settings['wke-lp-course-category'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <!-- Sensei -->
                <div class="wk-padding-small wk-background-muted">
                    <div class="" wk-grid>
                        <div class="wk-width-auto@m wk-card-media-left wk-cover-container">
                            <img src="<?php echo plugins_url('../assets/images/sensei-logo.png', __FILE__)?>" width="100">
                        </div>
                        <div class="wk-width-expand@m">
                            <div class="wk-card-body wk-padding-remove">
                                <div class="wk-flex wk-flex-between wk-flex-middle wk-margin-small-bottom">
                                    <h3 class="wk-card-title wk-margin-remove-top wk-margin-remove-bottom"><?php esc_html_e('Sensei', 'widgetkit-for-elementor')?></h3>
                                    <a class="demo-button-for-pro wk-button-primary" href="https://widgetkit.themesgrove.com/#pro-element" target="_blank"><?php esc_html_e('Demo', 'widgetkit-for-elementor');?></a>
                                </div>
                                <p><?php esc_html_e('We are working hard to bring some niche elements which are integrated with Sensei.', 'widgetkit-for-elementor');?></p>
                            </div>
                        </div>
                    </div>
                    <div class="woo-elements">
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('Sensei Course List', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-sensei-course-list" name="wke-sensei-course-list" <?php checked(1, $this->widgetkit_get_sensei_settings['wke-sensei-course-list'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            
                            <?php echo __('Sensei Course Tab', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-sensei-course-tab" name="wke-sensei-course-tab" <?php checked(1, $this->widgetkit_get_sensei_settings['wke-sensei-course-tab'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('Sensei Course Category', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-sensei-course-category" name="wke-sensei-course-category" <?php checked(1, $this->widgetkit_get_sensei_settings['wke-sensei-course-category'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                        
                    </div>
                </div>
                
                <!-- Lifter -->
                <div class="wk-padding-small wk-background-muted">
                    <div class="" wk-grid>
                        <div class="wk-width-auto@m wk-card-media-left wk-cover-container">
                            <img src="<?php echo plugins_url('../assets/images/lifter-logo.png', __FILE__)?>" width="100">
                        </div>
                        <div class="wk-width-expand@m">
                            <div class="wk-card-body wk-padding-remove">
                                <div class="wk-flex wk-flex-between wk-flex-middle wk-margin-small-bottom">
                                    <h3 class="wk-card-title wk-margin-remove-top wk-margin-remove-bottom"><?php esc_html_e('LifterLMS', 'widgetkit-for-elementor')?></h3>
                                    <a class="demo-button-for-pro wk-button-primary" href="https://widgetkit.themesgrove.com/#pro-element" target="_blank"><?php esc_html_e('Demo', 'widgetkit-for-elementor');?></a>
                                </div>
                                <p><?php esc_html_e('We are working hard to bring some niche elements which are integrated with Lifter.', 'widgetkit-for-elementor');?></p>
                            </div>
                        </div>
                    </div>
                    <div class="woo-elements">
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('Lifter Course List', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-lifter-course-list" name="wke-lifter-course-list" <?php checked(1, $this->widgetkit_get_lifter_settings['wke-lifter-course-list'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <!-- Tutor -->
                <div class="wk-padding-small wk-background-muted">
                    <div class="" wk-grid>
                        <div class="wk-width-auto@m wk-card-media-left wk-cover-container">
                            <img src="<?php echo plugins_url('../assets/images/tutor-logo.png', __FILE__)?>" width="100">
                        </div>
                        <div class="wk-width-expand@m">
                            <div class="wk-card-body wk-padding-remove">
                                <div class="wk-flex wk-flex-between wk-flex-middle wk-margin-small-bottom">
                                    <h3 class="wk-card-title wk-margin-remove-top wk-margin-remove-bottom"><?php esc_html_e('Tutor LMS', 'widgetkit-for-elementor')?></h3>
                                    <a class="demo-button-for-pro wk-button-primary" href="https://widgetkit.themesgrove.com/#pro-element" target="_blank"><?php esc_html_e('Demo', 'widgetkit-for-elementor');?></a>
                                </div>
                                <p><?php esc_html_e('We are working hard to bring some niche elements which are integrated with Lifter.', 'widgetkit-for-elementor');?></p>
                            </div>
                        </div>
                    </div>
                    <div class="woo-elements">
                        <div class="wk-card wk-background-default wk-card-body wk-card-small wk-flex wk-flex-between wk-flex-middle">
                            <?php echo __('Tutor Course List', 'widgetkit-for-elementor'); ?>
                            <label class="switch  <?php echo !$this->pro_enable_status ? 'disable' : ''; ?>">
                                <input type="checkbox" id="wke-tutor-course-list" name="wke-tutor-course-list" <?php checked(1, $this->widgetkit_get_tutor_settings['wke-tutor-course-list'], $this->pro_enable_status) ?>>
                                <span class="rectangle round"></span>
                            </label>
                        </div>
                    </div>
                </div>
                
            </div>
            <?php 
        }
    }
?>