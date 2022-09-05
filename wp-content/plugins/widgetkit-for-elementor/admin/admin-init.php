<?php

class Widgetkit_Admin
{
    // Widgets keys
    public $widgetkit_elements_keys = [
        'widget-slider-animation',
        'widget-slider-content-animation',
        'widget-slider-box-animation',
        'widget-gallery',
        'widget-portfolio',
        'widget-pricing-single',
        'widget-pricing-icon',
        'widget-pricing-tab',
        'widget-search',
        'widget-site-social',
        'widget-contact',

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
        'widget-social-share-animation',
        'widget-social-share-collapse',
        'widget-post-carousel',
        'wkpro-post-tab',
        'wkpro-post-carousel',
        'wkpro-post-list',
        'wkpro-grid-slider',
        'wkpro-headline-slider',
        'wkpro-smart-toggle',
        'wkpro-event-list',
        'wkpro-mobile-menu-toggle',
        'widget-image-compare',
        'widget-tilt-box',
        'widget-contact-form',
        'widget-pros-cons',
        'widget-click-tweet',
        'widget-video-popup',
        'widget-lottie-animation',
        'widget-mailchimp'
    ];

    public $widgetkit_woo_keys = [
        'wke-woo-product',
        'wke-woo-product-carousel',
        'wke-woo-categories',
        'wke-woo-recent-product',
    ];

    public $widgetkit_woo_single_keys = [
        'wke-woo-single-product-title',
        'wke-woo-single-product-price',
        'wke-woo-single-product-short-description',
        'wke-woo-single-product-categories',
        'wke-woo-single-product-cart-button',
        'wke-woo-single-product-thumbnail',
        'wke-woo-single-product-additional-information',
        'wke-woo-single-product-review',
        'wke-woo-single-product-related-product',
        'wke-woo-single-product-upsell-product',
        'wke-woo-single-product-rating',
        'wke-woo-single-product-cross-sell-product',
        'wke-woo-single-product-sku',
        'wke-woo-single-product-stock-status',
    ];

    public $widgetkit_ld_keys = [
        'wke-ld-course-list',
        'wke-ld-course-tab',
        'wke-ld-course-banner',
        'wke-ld-course-certificate',
        'wke-ld-course-enrollment',
        'wke-ld-course-meta-info',
        'wke-ld-course-progress',
        'wke-ld-course-resource',
        'wke-ld-course-tab-content',
        'wke-ld-course-related-course',
        'wke-ld-course-curriculum',
        'wke-ld-course-instructor',
        'wke-ld-course-payments-button',
        'wke-ld-course-content'
    ];

    public $widgetkit_lp_keys = [
        'wke-lp-course-list',
        'wke-lp-course-tab',
        'wke-lp-course-category',
    ];

    public $widgetkit_sensei_keys = [
        'wke-sensei-course-list',
        'wke-sensei-course-tab',
        'wke-sensei-course-category',
    ];
    
    public $widgetkit_lifter_keys = [
        'wke-lifter-course-list',
    ];
    public $widgetkit_tutor_keys = [
        'wke-tutor-course-list',
    ];
        
    private $pro_enable_status;
    
    // Default settings
    private $widgetkit_default_settings;
    private $widgetkit_woo_settings;
    private $widgetkit_woo_single_settings;
    private $widgetkit_ld_settings;
    private $widgetkit_lp_settings;
    private $widgetkit_sensei_settings;
    private $widgetkit_lifter_settings;
    private $widgetkit_tutor_settings;
    // widgetkit settings
    private $widgetkit_settings;
    private $wk_woo_settings;
    private $wk_woo_single_settings;
    private $wk_ld_settings;
    private $wk_lp_settings;
    private $wk_sensei_settings;
    private $wk_lifter_settings;
    private $wk_tutor_settings;
    // widgetkit get settings
    private $widgetkit_get_settings;
    private $widgetkit_get_woo_settings;
    private $widgetkit_get_woo_single_settings;
    private $widgetkit_get_ld_settings;
    private $widgetkit_get_lp_settings;
    private $widgetkit_get_sensei_settings;
    private $widgetkit_get_lifter_settings;
    private $widgetkit_get_tutor_settings;

    private $all_option_data = [];
    private $pro_integration_data = [];

    /**
     * Register construct
     */
    
    public function __construct()
    {
        //$this->includes();        
        $this->init_hooks();
    }
    
    /**
     * Register a custom opitons.
     */
	public function widgetkit_for_elementor_admin_options(){
	    add_menu_page( 
	        'Admin Menu',
            __( 'WidgetKit', 'widgetkit-for-elementor' ),
	        'manage_options',
	        'widgetkit-settings',
	        array($this, 'display_settings_pages'),
	        plugins_url('/assets/images/wk-icon-white.svg', __FILE__ ), 55
        ); 
        if(!apply_filters('wkpro_enabled', false)):
        add_submenu_page( 
            'widgetkit-settings', 
            '', 
            '<span class="dashicons dashicons-star-filled" style="color:#f44336; font-size: 17px"></span> ' . __( 'Go Pro', 'widgetkit-for-elementor' ) ,
            'manage_options', 
            'widgetkit-gopro', 
            array($this, 'handle_external_redirects')
        );
        endif;
    }

    /**
     * Register all hooks
     */
    public function init_hooks()
    {
        // Build admin main menu
        add_action('admin_menu', [$this, 'widgetkit_for_elementor_admin_options']);
        // Build admin script
        add_action('admin_enqueue_scripts', [$this, 'widgetkit_for_elementor_admin_page_scripts']);
        // Param check
        add_action('admin_init', [$this, 'widgetkit_for_elementor_admin_get_param_check']);
        // Build admin view and save
        add_action('wp_ajax_widgetkit_save_admin_addons_settings', [$this, 'widgetkit_for_elementor_sections_with_ajax']);
    }

    
    /**
     * Register scripts
     */
    public function widgetkit_for_elementor_admin_page_scripts ($page_slug_hook) {
        wp_enqueue_style( 'widgetkit-admin',  WK_URL.'/dist/css/wk-dashboard.css', array(), WK_VERSION, '' );
        wp_enqueue_script('widgetkit-elementor-admin-js', plugins_url('/assets/js/admin.js', __FILE__) , array('jquery','jquery-ui-tabs'), '1.0' , true );
        // wp_enqueue_script( 'widgetkit-sweet-js',  plugins_url('/assets/js/core.js', __FILE__), array( 'jquery' ), '1.0', true );
       /**
        * Load uikit only inside widgetkit setting page
        */
        global $wp;  
        $current_url = add_query_arg(array($_GET), $wp->request);
        $current_url_slug = explode("=", $current_url);
        
        if(
            $current_url && 
            (
                'toplevel_page_widgetkit-settings' === $page_slug_hook || 
                ( 
                    'toplevel_page_widgetkit-template-library' === $page_slug_hook ||
                    'widgetkit_page_widgetkit-template-library' === $page_slug_hook ||
                    'toplevel_page_widgetkit-template-library&package' === $page_slug_hook 
                ) 
            ) 
        ){
            wp_enqueue_style( 'wkkit',  plugins_url('/dist/css/uikit.custom.min.css', dirname(__FILE__)  ));
            wp_enqueue_style( 'widgetkit-sweetalert2-css', plugins_url('/assets/css/sweetalert2.min.css', __FILE__ ));

            wp_enqueue_script( 'wkkit',  plugins_url('/dist/js/uikit.min.js', dirname(__FILE__)  ));
            wp_enqueue_script( 'wkkit-icon',  plugins_url('/dist/js/uikit-icons.min.js', dirname(__FILE__)  ));
            wp_enqueue_script( 'widgetkit-sweetalert2-js', plugins_url('/assets/js/sweetalert2.min.js', __FILE__), array( 'jquery' ), '1.0', true );
        }
    }

    public function widgetkit_for_elementor_admin_get_param_check()
    {
        if (isset($_GET['dismissed']) && $_GET['dismissed'] == 1) {
            update_option('notice_dissmissed', 1);
        }
        $this->handle_external_redirects();
    }

    public function handle_external_redirects()
    {
        if (empty($_GET['page'])) {
            return;
        }
        if ('widgetkit-gopro' === $_GET['page']) {
            wp_redirect('https://themesgrove.com/widgetkit-for-elementor/?utm_source=wp-menu&utm_campaign=widgetkit_gopro&utm_medium=wp-dash');
            exit;
        }
    }

    /**
     * Register display view
     */
    public function display_settings_pages()
    {
        $js_info = [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'security_nonce' => wp_create_nonce('ajax-security-nonce')
        ];
        wp_localize_script('widgetkit-elementor-admin-js', 'settings', $js_info);

        $this->pro_enable_status = apply_filters('wkpro_enabled', false);
       
	    $this->widgetkit_default_settings = array_fill_keys( $this->widgetkit_elements_keys, true );
        $this->widgetkit_woo_settings = array_fill_keys( $this->widgetkit_woo_keys, true );
        $this->widgetkit_woo_single_settings = array_fill_keys( $this->widgetkit_woo_single_keys, true );
        $this->widgetkit_ld_settings = array_fill_keys( $this->widgetkit_ld_keys, true );
        $this->widgetkit_lp_settings = array_fill_keys( $this->widgetkit_lp_keys, true );
        $this->widgetkit_sensei_settings = array_fill_keys( $this->widgetkit_sensei_keys, true );
        $this->widgetkit_lifter_settings = array_fill_keys( $this->widgetkit_lifter_keys, true );
        $this->widgetkit_tutor_settings = array_fill_keys( $this->widgetkit_tutor_keys, true );
       
	    $this->widgetkit_get_settings = get_option( 'widgetkit_save_settings', $this->widgetkit_default_settings );
        $this->widgetkit_get_woo_settings = get_option( 'widgetkit_save_woo_settings', $this->widgetkit_woo_settings );
        $this->widgetkit_get_woo_single_settings = get_option( 'widgetkit_save_woo_single_settings', $this->widgetkit_woo_single_settings );
        $this->widgetkit_get_ld_settings = get_option( 'widgetkit_save_ld_settings', $this->widgetkit_ld_settings );
        $this->widgetkit_get_lp_settings = get_option( 'widgetkit_save_lp_settings', $this->widgetkit_lp_settings );
        $this->widgetkit_get_sensei_settings = get_option( 'widgetkit_save_sensei_settings', $this->widgetkit_sensei_settings );
        $this->widgetkit_get_lifter_settings = get_option( 'widgetkit_save_lifter_settings', $this->widgetkit_lifter_settings );
        $this->widgetkit_get_tutor_settings = get_option( 'widgetkit_save_tutor_settings', $this->widgetkit_tutor_settings );
        
        /**
         * Check if found any difference between db and local key
         */
	    $widgetkit_new_settings = array_diff_key( $this->widgetkit_default_settings, $this->widgetkit_get_settings );
	    $widgetkit_new_woo_settings = array_diff_key( $this->widgetkit_woo_settings, $this->widgetkit_get_woo_settings );
	    $widgetkit_new_woo_single_settings = array_diff_key( $this->widgetkit_woo_single_settings, $this->widgetkit_get_woo_single_settings );
	    $widgetkit_new_ld_settings = array_diff_key( $this->widgetkit_ld_settings, $this->widgetkit_get_ld_settings );
	    $widgetkit_new_lp_settings = array_diff_key( $this->widgetkit_lp_settings, $this->widgetkit_get_lp_settings );
        $widgetkit_new_sensei_settings = array_diff_key( $this->widgetkit_sensei_settings, $this->widgetkit_get_sensei_settings );
        $widgetkit_new_lifter_settings = array_diff_key( $this->widgetkit_lifter_settings, $this->widgetkit_get_lifter_settings );
        $widgetkit_new_tutor_settings = array_diff_key( $this->widgetkit_tutor_settings, $this->widgetkit_get_tutor_settings );
        
        /**
         * If any difference found then update the db
         */
        if( ! empty( $widgetkit_new_settings ) ) {
            $widgetkit_updated_settings = array_merge( $this->widgetkit_get_settings, $widgetkit_new_settings );
            update_option( 'widgetkit_save_settings', $widgetkit_updated_settings );
        }
        if( ! empty( $widgetkit_new_woo_settings ) ) {
            $widgetkit_updated_woo_settings = array_merge( $this->widgetkit_get_woo_settings, $widgetkit_new_woo_settings );
            update_option( 'widgetkit_save_woo_settings', $widgetkit_updated_woo_settings );
        }
        if( ! empty( $widgetkit_new_woo_single_settings ) ) {
            $widgetkit_updated_woo_single_settings = array_merge( $this->widgetkit_get_woo_single_settings, $widgetkit_new_woo_single_settings );
            update_option( 'widgetkit_save_woo_single_settings', $widgetkit_updated_woo_single_settings );
        }
        if( ! empty( $widgetkit_new_ld_settings ) ) {
            $widgetkit_updated_ld_settings = array_merge( $this->widgetkit_get_ld_settings, $widgetkit_new_ld_settings );
            update_option( 'widgetkit_save_ld_settings', $widgetkit_updated_ld_settings );
        }
        if( ! empty( $widgetkit_new_lp_settings ) ) {
            $widgetkit_updated_lp_settings = array_merge( $this->widgetkit_get_lp_settings, $widgetkit_new_lp_settings );
            update_option( 'widgetkit_save_lp_settings', $widgetkit_updated_lp_settings );
        }
        if( ! empty( $widgetkit_new_sensei_settings ) ) {
            $widgetkit_updated_sensei_settings = array_merge( $this->widgetkit_get_sensei_settings, $widgetkit_new_sensei_settings );
            update_option( 'widgetkit_save_sensei_settings', $widgetkit_updated_sensei_settings );
        }
        if( ! empty( $widgetkit_new_lifter_settings ) ) {
            $widgetkit_updated_lifter_settings = array_merge( $this->widgetkit_get_lifter_settings, $widgetkit_new_lifter_settings );
            update_option( 'widgetkit_save_lifter_settings', $widgetkit_updated_lifter_settings );
        }
        if( ! empty( $widgetkit_new_tutor_settings ) ) {
            $widgetkit_updated_tutor_settings = array_merge( $this->widgetkit_get_tutor_settings, $widgetkit_new_tutor_settings );
            update_option( 'widgetkit_save_tutor_settings', $widgetkit_updated_tutor_settings );
        }

        $this->widgetkit_get_settings = get_option( 'widgetkit_save_settings', $this->widgetkit_default_settings );
        $this->widgetkit_get_woo_settings = get_option( 'widgetkit_save_woo_settings', $this->widgetkit_woo_settings );
        $this->widgetkit_get_woo_single_settings = get_option( 'widgetkit_save_woo_single_settings', $this->widgetkit_woo_single_settings );
        $this->widgetkit_get_ld_settings = get_option( 'widgetkit_save_ld_settings', $this->widgetkit_ld_settings );
        $this->widgetkit_get_lp_settings = get_option( 'widgetkit_save_lp_settings', $this->widgetkit_lp_settings );
        $this->widgetkit_get_sensei_settings = get_option( 'widgetkit_save_sensei_settings', $this->widgetkit_sensei_settings );
        $this->widgetkit_get_lifter_settings = get_option( 'widgetkit_save_lifter_settings', $this->widgetkit_lifter_settings );
        $this->widgetkit_get_tutor_settings = get_option( 'widgetkit_save_tutor_settings', $this->widgetkit_tutor_settings );

        $this->all_option_data = [
            'widgetkit_get_settings' => $this->widgetkit_get_settings, 
            'pro_integration_data' => [
                'widgetkit_get_woo_settings' => $this->widgetkit_get_woo_settings, 
                'widgetkit_get_woo_single_settings' => $this->widgetkit_get_woo_single_settings,
                'widgetkit_get_ld_settings' => $this->widgetkit_get_ld_settings, 
                'widgetkit_get_lp_settings' => $this->widgetkit_get_lp_settings, 
                'widgetkit_get_sensei_settings' => $this->widgetkit_get_sensei_settings, 
                'widgetkit_get_lifter_settings' => $this->widgetkit_get_lifter_settings,
                'widgetkit_get_tutor_settings' => $this->widgetkit_get_tutor_settings
            ]
            
        ]

?>


    <div class="wrap wk-dashboard-wrapper">
        <div class="response-wrap"></div>
        <form action="" method="POST" id="widgetkit-settings" name="widgetkit-settings">
            <div class="wk-container">
                <!-- header -->
                <?php 
                    require WK_PATH . '/admin/includes/header.php';
                    WKFE_Dashboard_Header::init();
                ?>
                <!-- content -->
                <?php 
                    require WK_PATH . '/admin/includes/content.php';
                    new WKFE_Dashboard_Content($this->all_option_data);
                ?>
            </div>
        </form>
    </div>

    <script type="text/javascript">!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});</script>
    <script type="text/javascript">window.Beacon('init', '940f4d8a-7f6f-432c-ae31-0ed5819fdbe4')</script>
<?php
    }
        
    /**
     * Register sections
     */
    public function widgetkit_for_elementor_sections_with_ajax()
    {
        if ( ! wp_verify_nonce( $_REQUEST['security'], 'ajax-security-nonce' ) ) // security_nonce
        {
            wp_send_json_error("No dirty business please", 400);
            return false;
            die ();
        }

        if (isset($_POST['fields'])) {
            parse_str($_POST['fields'], $settings);
        } else {
            return;
        }

        $this->widgetkit_settings = [
            'widget-slider-animation' => intval($settings['widget-slider-animation'] ? 1 : 0),
            'widget-search' => intval($settings['widget-search'] ? 1 : 0),
            'widget-site-social' => intval($settings['widget-site-social'] ? 1 : 0),
            'widget-contact' => intval($settings['widget-contact'] ? 1 : 0),
            'widget-slider-content-animation' => intval($settings['widget-slider-content-animation'] ? 1 : 0),
            'widget-slider-box-animation' => intval($settings['widget-slider-box-animation'] ? 1 : 0),
            'widget-gallery' => intval($settings['widget-gallery'] ? 1 : 0),            
            'widget-portfolio' => intval($settings['widget-portfolio'] ? 1 : 0),
            'widget-feature-box' => intval($settings['widget-feature-box'] ? 1 : 0),
            'widget-animation-text' => intval($settings['widget-animation-text'] ? 1 : 0),
            'widget-countdown' => intval($settings['widget-countdown'] ? 1 : 0),
            'widget-pricing-single' => intval($settings['widget-pricing-single'] ? 1 : 0),
            'widget-pricing-icon' => intval($settings['widget-pricing-icon'] ? 1 : 0),
            'widget-pricing-tab' => intval($settings['widget-pricing-tab'] ? 1 : 0),
            'widget-team' => intval($settings['widget-team'] ? 1 : 0),
            'widget-team-round' => intval($settings['widget-team-round'] ? 1 : 0),
            'widget-team-animation' => intval($settings['widget-team-animation'] ? 1 : 0),
            'widget-team-verticle-icon' => intval($settings['widget-team-verticle-icon'] ? 1 : 0),
            'widget-team-overlay' => intval($settings['widget-team-overlay'] ? 1 : 0),
            'widget-button' => intval($settings['widget-button'] ? 1 : 0),
            'widget-hover-image' => intval($settings['widget-hover-image'] ? 1 : 0),
            'widget-content-carousel' => intval($settings['widget-content-carousel'] ? 1 : 0),
            'widget-blog-revert' => intval($settings['widget-blog-revert'] ? 1 : 0),
            'widget-blog-hover-animation' => intval($settings['widget-blog-hover-animation'] ? 1 : 0),
            'widget-blog-image' => intval($settings['widget-blog-image'] ? 1 : 0),
            'widget-blog-carousel' => intval($settings['widget-blog-carousel'] ? 1 : 0),
            'widget-blog-sidebar' => intval($settings['widget-blog-sidebar'] ? 1 : 0),
            'widget-testimonial' => intval($settings['widget-testimonial'] ? 1 : 0),
            'widget-testimonial-single' => intval($settings['widget-testimonial-single'] ? 1 : 0),
            'widget-testimonial-center' => intval($settings['widget-testimonial-center'] ? 1 : 0),
            'widget-social-share-animation' => intval($settings['widget-social-share-animation'] ? 1 : 0),
            'widget-social-share-collapse' => intval($settings['widget-social-share-collapse'] ? 1 : 0),
            'widget-post-carousel' => intval($settings['widget-post-carousel'] ? 1 : 0),
            'widget-image-compare' => intval($settings['widget-image-compare'] ? 1 : 0),
            'widget-tilt-box' => intval($settings['widget-tilt-box'] ? 1 : 0),
            'widget-contact-form' => intval($settings['widget-contact-form'] ? 1 : 0),
            'widget-click-tweet' => intval($settings['widget-click-tweet'] ? 1 : 0),
            'widget-pros-cons' => intval($settings['widget-pros-cons'] ? 1 : 0),
            'widget-video-popup' => intval($settings['widget-video-popup'] ? 1 : 0),
            'widget-lottie-animation' => intval($settings['widget-lottie-animation'] ? 1 : 0),
            'widget-mailchimp' => intval($settings['widget-mailchimp'] ? 1 : 0),
            /**
             * pro
             */
            'wkpro-post-tab' => intval($settings['wkpro-post-tab'] ? 1 : 0),
            'wkpro-post-carousel' => intval($settings['wkpro-post-carousel'] ? 1 : 0),
            'wkpro-post-list' => intval($settings['wkpro-post-list'] ? 1 : 0),
            'wkpro-grid-slider' => intval($settings['wkpro-grid-slider'] ? 1 : 0),
            'wkpro-headline-slider' => intval($settings['wkpro-headline-slider'] ? 1 : 0),
            'wkpro-smart-toggle' => intval($settings['wkpro-smart-toggle'] ? 1 : 0),
            'wkpro-mobile-menu-toggle' => intval($settings['wkpro-mobile-menu-toggle'] ? 1 : 0),
            'wkpro-event-list' => intval($settings['wkpro-event-list'] ? 1 : 0),
        ];
        $this->wk_woo_settings = [
            'wke-woo-product' => intval($settings['wke-woo-product'] ? 1 : 0),
            'wke-woo-product-carousel' => intval($settings['wke-woo-product-carousel'] ? 1 : 0),
            'wke-woo-categories' => intval($settings['wke-woo-categories'] ? 1 : 0),
            'wke-woo-recent-product' => intval($settings['wke-woo-recent-product'] ? 1 : 0),
        ];
        $this->wk_woo_single_settings = [
            'wke-woo-single-product-title' => intval($settings['wke-woo-single-product-title'] ? 1 : 0),
            'wke-woo-single-product-price' => intval($settings['wke-woo-single-product-price'] ? 1 : 0),
            'wke-woo-single-product-short-description' => intval($settings['wke-woo-single-product-short-description'] ? 1 : 0),
            'wke-woo-single-product-categories' => intval($settings['wke-woo-single-product-categories'] ? 1 : 0),
            'wke-woo-single-product-cart-button' => intval($settings['wke-woo-single-product-cart-button'] ? 1 : 0),
            'wke-woo-single-product-thumbnail' => intval($settings['wke-woo-single-product-thumbnail'] ? 1 : 0),
            'wke-woo-single-product-additional-information' => intval($settings['wke-woo-single-product-additional-information'] ? 1 : 0),
            'wke-woo-single-product-review' => intval($settings['wke-woo-single-product-review'] ? 1 : 0),
            'wke-woo-single-product-related-product' => intval($settings['wke-woo-single-product-related-product'] ? 1 : 0),
            'wke-woo-single-product-upsell-product' => intval($settings['wke-woo-single-product-upsell-product'] ? 1 : 0),
            'wke-woo-single-product-rating' => intval($settings['wke-woo-single-product-rating'] ? 1 : 0),
            'wke-woo-single-product-cross-sell-product' => intval($settings['wke-woo-single-product-cross-sell-product'] ? 1 : 0),
            'wke-woo-single-product-sku' => intval($settings['wke-woo-single-product-sku'] ? 1 : 0),
            'wke-woo-single-product-stock-status' => intval($settings['wke-woo-single-product-stock-status'] ? 1 : 0),
        ];

        $this->wk_ld_settings = [
            'wke-ld-course-list' => intval($settings['wke-ld-course-list'] ? 1 : 0),
            'wke-ld-course-tab' => intval($settings['wke-ld-course-tab'] ? 1 : 0),
            'wke-ld-course-banner' => intval($settings['wke-ld-course-banner'] ? 1 : 0),
            'wke-ld-course-certificate' => intval($settings['wke-ld-course-certificate'] ? 1 : 0),
            'wke-ld-course-enrollment' => intval($settings['wke-ld-course-enrollment'] ? 1 : 0),
            'wke-ld-course-meta-info' => intval($settings['wke-ld-course-meta-info'] ? 1 : 0),
            'wke-ld-course-progress' => intval($settings['wke-ld-course-progress'] ? 1 : 0),
            'wke-ld-course-resource' => intval($settings['wke-ld-course-resource'] ? 1 : 0),
            'wke-ld-course-tab-content' => intval($settings['wke-ld-course-tab-content'] ? 1 : 0),
            'wke-ld-course-related-course' => intval($settings['wke-ld-course-related-course'] ? 1 : 0),
            'wke-ld-course-curriculum' => intval($settings['wke-ld-course-curriculum'] ? 1 : 0),
            'wke-ld-course-instructor' => intval($settings['wke-ld-course-instructor'] ? 1 : 0),
            'wke-ld-course-payments-button' => intval($settings['wke-ld-course-payments-button'] ? 1 : 0),
            'wke-ld-course-content' => intval($settings['wke-ld-course-content'] ? 1 : 0)

        ];
        $this->wk_lp_settings = [
            'wke-lp-course-list' => intval($settings['wke-lp-course-list'] ? 1 : 0),
            'wke-lp-course-tab' => intval($settings['wke-lp-course-tab'] ? 1 : 0),
            'wke-lp-course-category' => intval($settings['wke-lp-course-category'] ? 1 : 0),
        ];
        $this->wk_sensei_settings = [
            'wke-sensei-course-list' => intval($settings['wke-sensei-course-list'] ? 1 : 0),
            'wke-sensei-course-tab' => intval($settings['wke-sensei-course-tab'] ? 1 : 0),
            'wke-sensei-course-category' => intval($settings['wke-sensei-course-category'] ? 1 : 0),
        ];
        $this->wk_lifter_settings = [
            'wke-lifter-course-list' => intval($settings['wke-lifter-course-list'] ? 1 : 0),
        ];
        $this->wk_tutor_settings = [
            'wke-tutor-course-list' => intval($settings['wke-tutor-course-list'] ? 1 : 0),
        ];
        update_option('widgetkit_save_settings', $this->widgetkit_settings);
        update_option('widgetkit_save_woo_settings', $this->wk_woo_settings);
        update_option('widgetkit_save_woo_single_settings', $this->wk_woo_single_settings);
        update_option('widgetkit_save_ld_settings', $this->wk_ld_settings);
        update_option('widgetkit_save_lp_settings', $this->wk_lp_settings);
        update_option('widgetkit_save_sensei_settings', $this->wk_sensei_settings);
        update_option('widgetkit_save_lifter_settings', $this->wk_lifter_settings);
        update_option('widgetkit_save_tutor_settings', $this->wk_tutor_settings);

        return true;
        die();
    }

}

new Widgetkit_Admin;
