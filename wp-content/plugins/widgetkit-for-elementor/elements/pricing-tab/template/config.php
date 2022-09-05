<?php


use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
Use Elementor\Core\Schemes\Typography;
use Elementor\Utils;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor WidgetKit pricing tab
 *
 * Elementor widget for WidgetKit pricing tab
 *
 * @since 1.0.0
 */

	class wkfe_pricing_tab extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-pricing-tab';
	}

	public function get_title() {
		return esc_html__( 'Pricing Tab', 'widgetkit-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-tabs wk-icon';
	}

	public function get_categories() {
		return [ 'widgetkit_elementor' ];
	}

	/**
	 * A list of style that the widgets is depended in
	 **/
	public function get_style_depends() {
        return [
            'widgetkit_bs',
            'widgetkit_main',
        ];
    }
	/**
	 * A list of scripts that the widgets is depended in
	 **/
	public function get_script_depends() {
		return [ 
			'widgetkit-main',
		 ];
	}

	protected function _register_controls() {


		$this->start_controls_section(
			'section_tabs',
			[
				'label' => esc_html__( 'Pricing Tabs', 'widgetkit-for-elementor' ),
			]
		);


	// Ens single pricing table 1

		
		$repeater = new Repeater();
		$repeater->add_control(
			'tab_title',
			[
				'label' => esc_html__( 'Tab Title', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Share Hosting', 'widgetkit-for-elementor' ),
				'label_block' => true,

			]
		);

		$repeater->add_control(
			'tab_subtitle',
			[
				'label' => esc_html__( 'Tab Subtitle', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Low cost hosting plan', 'widgetkit-for-elementor' ),
				'label_block' => true,
			]
		);

		$repeater->start_controls_tabs( 'tabs_repeater' );




	// Pricing tab 1st pricing repeater start
		$repeater->start_controls_tab( 'pricing1', [ 'label' => esc_html__( 'Pricing #1', 'widgetkit-for-elementor' ) ] );

		$repeater->add_control(
			'pricing_tab_enable_1',
			[
				'label' => esc_html__( 'Enable Pricing', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'enable'    => esc_html__( 'Enable', 'widgetkit-for-elementor' ),
				'disable'   => esc_html__( 'Disable', 'widgetkit-for-elementor' ),
				'description'   =>esc_html__( 'Show Hide Pricing Table', 'widgetkit-for-elementor' ),

			]
		);

		$repeater->add_control(
			'currency_symbol',
			[
				'label'   => esc_html__( 'Currency Symbol', 'widgetkit-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'None', 'widgetkit-for-elementor' ),
					'&#36;'   => '&#36; ' . _x( 'Dollar', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#128;'  => '&#128; ' . _x( 'Euro', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#3647;' => '&#3647; ' . _x( 'Baht', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8355;' => '&#8355; ' . _x( 'Franc', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&fnof;'  => '&fnof; ' . _x( 'Guilder', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'kr;'     => 'kr ' . _x( 'Krona', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8356;' => '&#8356; ' . _x( 'Lira', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8359;' => '&#8359 ' . _x( 'Peseta', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8369;' => '&#8369; ' . _x( 'Peso', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#163;'  => '&#163; ' . _x( 'Pound Sterling', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'R$;'     => 'R$ ' . _x( 'Real', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8381;' => '&#8381; ' . _x( 'Ruble', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8360;' => '&#8360; ' . _x( 'Rupee', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8377;' => '&#8377; ' . _x( 'Rupee (Indian)', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8362;' => '&#8362; ' . _x( 'Shekel', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#165;'  => '&#165; ' . _x( 'Yen/Yuan', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8361;' => '&#8361; ' . _x( 'Won', 'Currency Symbol', 'widgetkit-for-elementor' ),
				],
				'default' => '&#36;',
				'conditions' => [
					'terms'  => [
						[
							'name'     => 'pricing_tab_enable_1',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'price',
			[
				'label' => esc_html__( 'Price', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default' => '290',
				'conditions' => [
					'terms'  => [
						[
							'name' => 'pricing_tab_enable_1',
							'operator' => '==',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'period',
			[
				'label' => esc_html__( 'Period', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default' => esc_html__( '/ mo', 'widgetkit-for-elementor' ),
				'conditions' => [
					'terms'  => [
						[
							'name' => 'pricing_tab_enable_1',
							'operator' => '==',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'tab_image',
			[
				'label' => _x( 'Image', 'Background Control', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .slick-slide-bg' => 'background-image: url({{URL}})',
				],
				'conditions' => [
					'terms'  => [
						[
							'name'     => 'pricing_tab_enable_1',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'pricing_title',
			[
				'label' => esc_html__( 'Pricing Plan', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default' => esc_html__( 'Basic Plan', 'widgetkit-for-elementor' ),
				'label_block' => true,
				'conditions'  => [
					'terms'   => [
						[
							'name' => 'pricing_tab_enable_1',
							'operator' => '==',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'pricing_desc_1',
			[
				'label' => esc_html__( 'Pricing Desc', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default' => esc_html__( 'I think your best bet would be to start or join a startup.', 'widgetkit-for-elementor' ),
				'label_block' => true,
				'conditions'  => [
					'terms'   => [
						[
							'name'     => 'pricing_tab_enable_1',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'pricing_details',
			[
				'label' => esc_html__( 'Pricing Details', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::WYSIWYG,
				'default' =>'<ul>
				 	<li><i class="fa fa-angellist"></i><strong>Multiple</strong> Website</li>
				 	<li><i class="fa fa-angellist"></i><strong>20GB</strong> Web Space</li>
				 	<li><i class="fa fa-angellist"></i><strong>Suitable for ~ 35,000</strong>  Visits Monthly</li>
				</ul>',
				'show_label' => false,
				'conditions' => [
					'terms'  => [
						[
							'name'     => 'pricing_tab_enable_1',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);



		$repeater->add_control(
			'button_text',
			[
				'label' => esc_html__( 'Button Text', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default' => esc_html__( 'Get Started Now', 'widgetkit-for-elementor' ),
				'conditions' => [
					'terms'  => [
						[
							'name'     => 'pricing_tab_enable_1',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => esc_html__( 'Link', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::URL,
				'placeholder' => esc_html__( 'http://your-link.com', 'widgetkit-for-elementor' ),
				'conditions'  => [
					'terms'   => [
						[
							'name'     => 'pricing_tab_enable_1',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$repeater->end_controls_tab(); // End Pricing Table Tab 1 Content 




	// Pricing tab 2nd pricing repeater start
		$repeater->start_controls_tab( 'pricing2', [ 'label' => esc_html__( 'Pricing #2', 'widgetkit-for-elementor' ) ] );


		$repeater->add_control(
			'pricing_tab_enable_2',
			[
				'label' => esc_html__( 'Enable Pricing', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'enable'    => esc_html__( 'Enable', 'widgetkit-for-elementor' ),
				'disable'   => esc_html__( 'Disable', 'widgetkit-for-elementor' ),
				'description'   => esc_html__( 'Show Hide Pricing Table', 'widgetkit-for-elementor' ),
			]
		);


		$repeater->add_control(
			'currency_symbol_2',
			[
				'label' => esc_html__( 'Currency Symbol', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'None', 'widgetkit-for-elementor' ),
					'&#36;'   => '&#36; ' . _x( 'Dollar', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#128;'  => '&#128; ' . _x( 'Euro', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#3647;' => '&#3647; ' . _x( 'Baht', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8355;' => '&#8355; ' . _x( 'Franc', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&fnof;'  => '&fnof; ' . _x( 'Guilder', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'kr;'     => 'kr ' . _x( 'Krona', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8356;' => '&#8356; ' . _x( 'Lira', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8359;' => '&#8359 ' . _x( 'Peseta', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8369;' => '&#8369; ' . _x( 'Peso', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#163;'  => '&#163; ' . _x( 'Pound Sterling', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'R$;'     => 'R$ ' . _x( 'Real', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8381;' => '&#8381; ' . _x( 'Ruble', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8360;' => '&#8360; ' . _x( 'Rupee', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8377;' => '&#8377; ' . _x( 'Rupee (Indian)', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8362;' => '&#8362; ' . _x( 'Shekel', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#165;'  => '&#165; ' . _x( 'Yen/Yuan', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8361;' => '&#8361; ' . _x( 'Won', 'Currency Symbol', 'widgetkit-for-elementor' ),
				],
				'default'    => '&#36;',
				'conditions' => [
					'terms'  => [
						[
							'name'     => 'pricing_tab_enable_2',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'price_2',
			[
				'label' => esc_html__( 'Price', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default'    => '399',
				'conditions' => [
					'terms'  => [
						[
							'name'     => 'pricing_tab_enable_2',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'period_2',
			[
				'label' => esc_html__( 'Period', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default' => esc_html__( '/ mo', 'widgetkit-for-elementor' ),
				'conditions' => [
					'terms'  => [
						[
							'name'     => 'pricing_tab_enable_2',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'tab_image_2',
			[
				'label' => _x( 'Image', 'Background Control', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .slick-slide-bg' => 'background-image: url({{URL}})',
				],
				'conditions' => [
					'terms'  => [
						[
							'name'     => 'pricing_tab_enable_2',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'pricing_title_2',
			[
				'label' => esc_html__( 'Pricing Plan', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default' => esc_html__( 'Entry Plan', 'widgetkit-for-elementor' ),
				'label_block' => true,
	
				'conditions' => [
					'terms'  => [
						[
							'name'     => 'pricing_tab_enable_2',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);


		$repeater->add_control(
			'pricing_desc_2',
			[
				'label' => esc_html__( 'Pricing Desc', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default' => esc_html__( 'I think your best bet would be to start or join a startup.', 'widgetkit-for-elementor' ),
				'label_block' => true,
				'conditions'  => [
					'terms'   => [
						[
							'name'     => 'pricing_tab_enable_2',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);


		$repeater->add_control(
			'pricing_details_2',
			[
				'label' => esc_html__( 'Feature Items', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::WYSIWYG,
				'default' =>'<ul>
				 	<li><i class="fa fa-angellist"></i><strong>Multiple</strong> Website</li>
				 	<li><i class="fa fa-angellist"></i><strong>20GB</strong> Web Space</li>
				 	<li><i class="fa fa-angellist"></i><strong>Suitable for ~ 35,000</strong>  Visits Monthly</li>
				</ul>',
				'show_label' => false,
				'conditions' => [
					'terms'  => [
						[
							'name'     => 'pricing_tab_enable_2',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		); 
 
		$repeater->add_control(
			'button_text_2',
			[
				'label' => esc_html__( 'Button Text', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default' => esc_html__( 'Get Started Now', 'widgetkit-for-elementor' ),
				'conditions' => [
					'terms'  => [
						[
							'name'     => 'pricing_tab_enable_2',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'link_2',
			[
				'label' => esc_html__( 'Link', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::URL,
				'placeholder' => esc_html__( 'http://your-link.com', 'widgetkit-for-elementor' ),
				'conditions'  => [
					'terms'   => [
						[
							'name'     => 'pricing_tab_enable_2',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$repeater->end_controls_tab(); // End Pricing Table Tab 2 Content 



	// Pricing tab 3rd pricing repeater start
		$repeater->start_controls_tab( 'pricing3', [ 'label' => esc_html__( 'Pricing #3', 'widgetkit-for-elementor' ) ] );

		$repeater->add_control(
			'pricing_tab_enable_3',
			[
				'label' => esc_html__( 'Enable Pricing', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'enable'    => esc_html__( 'Enable', 'widgetkit-for-elementor' ),
				'disable'   => esc_html__( 'Disable', 'widgetkit-for-elementor' ),
				'description'   => __( 'Show Hide Pricing Table', 'widgetkit-for-elementor' ),
			]
		);


		$repeater->add_control(
			'currency_symbol_3',
			[
				'label' => esc_html__( 'Currency Symbol', 'widgetkit-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'None', 'widgetkit-for-elementor' ),
					'&#36;'    => '&#36; ' . _x( 'Dollar', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#128;'   => '&#128; ' . _x( 'Euro', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#3647;'  => '&#3647; ' . _x( 'Baht', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8355;'  => '&#8355; ' . _x( 'Franc', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&fnof;'   => '&fnof; ' . _x( 'Guilder', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'kr;'      => 'kr ' . _x( 'Krona', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8356;'  => '&#8356; ' . _x( 'Lira', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8359;'  => '&#8359 ' . _x( 'Peseta', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8369;'  => '&#8369; ' . _x( 'Peso', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#163;'   => '&#163; ' . _x( 'Pound Sterling', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'R$;'      => 'R$ ' . _x( 'Real', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8381;'  => '&#8381; ' . _x( 'Ruble', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8360;'  => '&#8360; ' . _x( 'Rupee', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8377;'  => '&#8377; ' . _x( 'Rupee (Indian)', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8362;'  => '&#8362; ' . _x( 'Shekel', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#165;'   => '&#165; ' . _x( 'Yen/Yuan', 'Currency Symbol', 'widgetkit-for-elementor' ),
					'&#8361;'  => '&#8361; ' . _x( 'Won', 'Currency Symbol', 'widgetkit-for-elementor' ),
				],
				'default'    => '&#36;',
				'conditions' => [
					'terms'  => [
						[
							'name'     => 'pricing_tab_enable_3',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);


		$repeater->add_control(
			'price_3',
			[
				'label' => esc_html__( 'Price', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default'    => '550',
				'conditions' => [
					'terms'  => [
						[
							'name'     => 'pricing_tab_enable_3',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'period_3',
			[
				'label' => esc_html__( 'Period', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default' => esc_html__( '/ mo', 'widgetkit-for-elementor' ),
				'conditions' => [
					'terms'  => [
						[
							'name'     => 'pricing_tab_enable_3',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'tab_image_3',
			[
				'label' => _x( 'Image', 'Background Control', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .slick-slide-bg' => 'background-image: url({{URL}})',
				],
				'conditions' => [
					'terms' => [
						[
							'name'     => 'pricing_tab_enable_3',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);



		$repeater->add_control(
			'pricing_title_3',
			[
				'label' => esc_html__( 'Pricing Plan', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default' => esc_html__( 'Pro Plan', 'widgetkit-for-elementor' ),
				'label_block' => true,
				'conditions'  => [
					'terms'   => [
						[
							'name'     => 'pricing_tab_enable_3',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);


		$repeater->add_control(
			'pricing_desc_3',
			[
				'label' => esc_html__( 'Pricing Desc', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default' => esc_html__( 'I think your best bet would be to start or join a startup.', 'widgetkit-for-elementor' ),
				'label_block' => true,
				'conditions'  => [
					'terms'   => [
						[
							'name'     => 'pricing_tab_enable_3',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'pricing_details_3',
			[
				'label' => esc_html__( 'Pricing Details', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::WYSIWYG,
				'default' =>'<ul>
				 	<li><i class="fa fa-angellist"></i><strong>Multiple</strong> Website</li>
				 	<li><i class="fa fa-angellist"></i><strong>20GB</strong> Web Space</li>
				 	<li><i class="fa fa-angellist"></i><strong>Suitable for ~ 35,000</strong>  Visits Monthly</li>
				</ul>',
				'show_label' => false,
				'conditions' => [
					'terms'  => [
						[
							'name'     => 'pricing_tab_enable_3',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);

 
 
		$repeater->add_control(
			'button_text_3',
			[
				'label' => esc_html__( 'Button Text', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default' => esc_html__( 'Get Started Now', 'widgetkit-for-elementor' ),
				'conditions' => [
					'terms'  => [
						[
							'name'     => 'pricing_tab_enable_3',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'link_3',
			[
				'label' => esc_html__( 'Link', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::URL,
				'placeholder' => esc_html__( 'http://your-link.com', 'widgetkit-for-elementor' ),
				'conditions'  => [
					'terms'   => [
						[
							'name'     => 'pricing_tab_enable_3',
							'operator' => '==',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$repeater->end_controls_tab(); // End Pricing Table Tab 2 Content 

		$repeater->end_controls_tabs();




        $this->add_control(
            'pricing_tabs',
            [
                'type'    => Controls_Manager::REPEATER,
                'fields'  => $repeater->get_controls(),
                'default' => [
                    [
                        'tab_title'    => esc_html__( 'Shared Hosting', 'widgetkit-for-elementor' ),
						'tab_subtitle' => esc_html__( 'Low cost hosting plan', 'widgetkit-for-elementor' ),
                    ],
                    [
                        'tab_title'    => esc_html__( 'Cloud Hosting', 'widgetkit-for-elementor' ),
						'tab_subtitle' => esc_html__( 'High perfomance solution', 'widgetkit-for-elementor' ),
                    ],
                    [
                        'tab_title'    => esc_html__( 'Dedicated Servers', 'widgetkit-for-elementor' ),
						'tab_subtitle' => esc_html__( 'Premium manages servers', 'widgetkit-for-elementor' ),
                    ],
                ],
                'title_field' => '{{{ tab_title }}}',
            ]
        );
 
	$this->end_controls_section();

	$this->start_controls_section(
		'section_pricing_tab_filter_style',
			[
				'label' => esc_html__( 'Tab Filter', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
                'show_label' => false,
			]
	);

		$this->add_control(
			'pricing_tab_filter_color',
				[
					'label' => esc_html__( 'Color', 'widgetkit-for-elementor' ),
					'type'  => Controls_Manager::COLOR,
					'default'   => '#444',
					'selectors' => [
						'{{WRAPPER}} .pricing-tab>.pricing-btn' => 'color: {{VALUE}};',
					],
				]
		);

		$this->add_control(
			'pricing_tab_filter_hover_color',
				[
					'label' => esc_html__( 'Hover/Active Color', 'widgetkit-for-elementor' ),
					'type'  => Controls_Manager::COLOR,
					'default'   => '#ed485f',
					'selectors' => [
						'{{WRAPPER}} .pricing-tab>input:checked+label,
						{{WRAPPER}}  .pricing-tab>.pricing-btn:hover' => 'color: {{VALUE}}; border-color:{{VALUE}} !important;',
					],
				]
		);

		$this->add_control(
            'pricing_tab_title_style',
            [
                'label' => esc_html__( 'Title', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

		$this->add_group_control(
	        Group_Control_Typography::get_type(),
	            [
	                'name'     => 'pricing_tab_filter_title_typography',
	                'label'    => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
	                'selector' => '{{WRAPPER}} .pricing-tab .price-title',
	            ]
	    );

	    $this->add_control(
            'pricing_tab_stb_title_style',
            [
                'label' => esc_html__( 'Sub Title', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

		$this->add_group_control(
	        Group_Control_Typography::get_type(),
	            [
	                'name'  => 'pricing_tab_filter_subtitle_typography',
	                'label' => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
	                'selector' => '{{WRAPPER}} .pricing-tab .price-subtitle',
	            ]
	    );


		$this->add_control(
            'pricing_tab_filter_border',
            [
                'label' => esc_html__( 'Border', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
	    $this->add_group_control(
            Group_Control_Border::get_type(), [
                'name'  => 'pricing_tab_filter_border',
                'label' => esc_html__( 'Border', 'widgetkit-for-elementor' ),
                'placeholder' => '1px',
                'default'  => '1px',
                'selector' => '{{WRAPPER}} .pricing-tab>.pricing-btn,
                		{{WRAPPER}} .pricing-tab>input:checked+label,
						{{WRAPPER}}  .pricing-tab>.pricing-btn:hover',
            ]
        );

        $this->add_control(
            'pricing_tab_filter_alignment',
            [
                'label' => esc_html__( 'Alignment', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::CHOOSE,
                'label_block' => false,
                'default'  => esc_html__( 'center', 'widgetkit-for-elementor' ),
                'options'  => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'widgetkit-for-elementor' ),
                        'icon'  => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'widgetkit-for-elementor' ),
                        'icon'  => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'widgetkit-for-elementor' ),
                        'icon'  => 'fa fa-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}  .pricing-tab>.pricing-btn' => 'text-align: {{VALUE}}',
                ],
            ]
        );

       $this->add_control(
            'pricing_tab_filter_padding',
            [
                'label' => esc_html__( 'Padding', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .pricing-tab>.pricing-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

	$this->end_controls_section();

	
	/**
	 * Pro control panel 
	 */
	if(!apply_filters('wkpro_enabled', false)):
		$this->start_controls_section(
			'section_widgetkit_pro_box',
			[
				'label' => esc_html__( 'Go Premium for more layout & feature', 'widgetkit-for-elementor' ),
			]
		);
			$this->add_control(
				'wkfe_control_go_pro',
				[
					'label' => __('Unlock more possibilities', 'widgetkit-for-elementor'),
					'type' => Controls_Manager::CHOOSE,
					'default' => '1',
					'description' => '<div class="elementor-nerd-box">
					<div class="elementor-nerd-box-message"> Get the  <a href="https://themesgrove.com/widgetkit-for-elementor/" target="_blank">Pro version</a> of <a href="https://themesgrove.com/widgetkit-for-elementor/" target="_blank">WidgetKit</a> for more stunning elements and customization options.</div>
					<a class="widgetkit-go-pro elementor-nerd-box-link elementor-button elementor-button-default elementor-go-pro" href="https://themesgrove.com/widgetkit-for-elementor/" target="_blank">Go Pro</a>
					</div>',
				]
			);
		$this->end_controls_section();
	endif;

	

	// tab content 3 style

	$this->start_controls_section(
        'section_pricing_tab_1_style',
            [
                'label' => esc_html__( '1st Tab Content', 'widgetkit-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'show_label' => false,
            ]
    );


		$this->add_control(
            'pricing_tab_1_heading',
            [
                'label' => esc_html__( 'Header', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
			'pricing_tab_1_header_color',
			[
				'label' => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-heading .cost' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_control(
			'pricing_tab_1_header_bg',
			[
				'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default'   => '#0069ff',
				'selectors' => [
					'{{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-heading .cost' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
	        Group_Control_Typography::get_type(),
	            [
	                'name'  => 'pricing_tab_1_pricing_typography',
	                'label' => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
	                'selector' => '{{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-heading .cost .amount',
	            ]
	    );

	   $this->add_control(
			'pricing_tab_1_pricing_currency_size',
			[
				'label' => esc_html__( 'Currency & Period Font Size', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'default'  => [
					'size' =>16,
				],
				'range'  => [
					'px' => [
						'min' => 12,
						'max' => 30,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-heading .cost .curency,
					{{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-heading .cost .period' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

	   $this->add_control(
            'pricing_tab_1_image',
            [
                'label' => esc_html__( 'Icon Image', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

	   	$this->add_control(
            'pricing_tab_1_image_alignment',
            [
                'label' => esc_html__( 'Alignment', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::CHOOSE,
                'label_block' => false,
                'default' => esc_html__( 'center', 'widgetkit-for-elementor' ),
                'options' => [
                    'left'=> [
                        'title' => esc_html__( 'Left', 'widgetkit-for-elementor' ),
                        'icon'  => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'widgetkit-for-elementor' ),
                        'icon'  => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'widgetkit-for-elementor' ),
                        'icon'  => 'fa fa-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}  .tgx-pricing-tab-0  .tgx-pricing-tab-image' => 'text-align: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'pricing_tab_1_image_margin',
            [
                'label' => esc_html__( 'Margin', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors'  => [
                    '{{WRAPPER}} .tgx-pricing-tab-0  .tgx-pricing-tab-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'pricing_tab_1_about',
            [
                'label' => esc_html__( 'About', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );


        $this->add_control(
			'pricing_tab_1_plan_color',
			[
				'label' => esc_html__( 'Title Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default'   => '#0069ff',
				'selectors' => [
					'{{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
	        Group_Control_Typography::get_type(),
	            [
	                'name'  => 'pricing_tab_1_plan_typography',
	                'label' => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
	                'selector' => '{{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-title',
	            ]
	    );


	    $this->add_control(
			'pricing_tab_1_desc_color',
			[
				'label' => esc_html__( 'Desc Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default'   => '#444',
				'selectors' => [
					'{{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
	        Group_Control_Typography::get_type(),
	            [
	                'name'  => 'pricing_tab_1_desc_typography',
	                'label' => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
	                'selector' => '{{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-desc',
	            ]
	    );

	    $this->add_control(
            'pricing_tab_1_feature',
            [
                'label' => esc_html__( 'Features', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
			'pricing_tab_1_feature_item_color',
			[
				'label' => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default' => '#444',
				'selectors' => [
					' {{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-feature' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pricing_tab_1_feature_item_heighlight_color',
			[
				'label' => esc_html__( 'Heighlight Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default' => '#4a6385',
				'selectors' => [
					'{{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-feature li b,
					 {{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-feature li strong' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'  => 'pricing_tab_1_feature_typography',
                'label' => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
                'selector' => '{{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-feature',
            ]
        );

        $this->add_responsive_control(
            'pricing_tab_1_feature_list_margin',
            [
                'label' => esc_html__( 'Margin', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors'  => [
                    '{{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-feature ul' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
			'pricing_tab_1_feature_item_padding',
			[
				'label' => esc_html__( 'Item Gap', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'default'  => [
					'size' =>5,
				],
				'range'  => [
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-feature ul li' => 'padding: {{SIZE}}{{UNIT}} 0;',
				],
			]
		);

		$this->add_control(
            'pricing_tab_1_button',
            [
                'label' => esc_html__( 'Button', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->start_controls_tabs( 'pricing_tab_1_tabs_button' );

        $this->start_controls_tab(
            'pricing_tab_1_normal',
            [
                'label' => esc_html__( 'Normal', 'widgetkit-for-elementor' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'  => 'pricing_tab_1_button_typography',
                'label' => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
                'selector' => '{{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-footer .tgx-pricing-tab-btn',
            ]
        );

        $this->add_control(
            'pricing_tab_1_button_text_color',
            [
                'label' => esc_html__( 'Text Color', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-footer .tgx-pricing-tab-btn' => 'color: {{VALUE}};',
                ],
            ]
        );



        $this->add_control(
            'pricing_tab_1_button_background_color',
            [
                'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::COLOR,
                'default' => '#0069ff',
                'selectors' => [
                    '{{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-footer .tgx-pricing-tab-btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(), [
                'name'  => 'pricing_tab_1_button_border',
                'label' => esc_html__( 'Border', 'widgetkit-for-elementor' ),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-footer .tgx-pricing-tab-btn',
            ]
        );

        $this->add_control(
            'pricing_tab_1_button_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-footer .tgx-pricing-tab-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'pricing_tab_1_button_text_padding',
            [
                'label' => esc_html__( 'Text Padding', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-footer .tgx-pricing-tab-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

      	$this->start_controls_tab(
            'pricing_tab_1_button_hover',
            [
                'label' => esc_html__( 'Hover', 'widgetkit-for-elementor' ),
            ]
        );

	        $this->add_control(
	            'pricing_tab_1_button_hover_color',
	            [
	                'label' => esc_html__( 'Text Color', 'widgetkit-for-elementor' ),
	                'type'  => Controls_Manager::COLOR,
	                'default' => '#0069ff',
	                'selectors' => [
	                    '{{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-footer .tgx-pricing-tab-btn:hover' => 'color: {{VALUE}};',
	                ],
	            ]
	        );

	        $this->add_control(
	            'pricing_tab_1_button_background_hover_color',
	            [
	                'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
	                'type'  => Controls_Manager::COLOR,
	                'default'   => '#fff',
	                'selectors' => [
	                    '{{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-footer .tgx-pricing-tab-btn:hover' => 'background-color: {{VALUE}};',
	                ],
	            ]
	        );

	        $this->add_control(
	            'pricing_tab_1_button_hover_border_color',
	            [
	                'label' => esc_html__( 'Border Color', 'widgetkit-for-elementor' ),
	                'type'  => Controls_Manager::COLOR,
	                'default'   => '#0069ff',
	                'selectors' => [
	                    '{{WRAPPER}} .tgx-pricing-tab-0 .tgx-pricing-tab-footer .tgx-pricing-tab-btn:hover' => 'border-color:{{VALUE}};',
	                ],
	            ]
	        );

 		$this->end_controls_tab();

        $this->end_controls_tabs();


    $this->end_controls_section();





	// tab content 2 style

   	$this->start_controls_section(
        'section_pricing_tab_2_style',
            [
                'label' => esc_html__( '2nd Tab Content', 'widgetkit-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'show_label' => false,
            ]
    );


		$this->add_control(
            'pricing_tab_2_heading',
            [
                'label' => esc_html__( 'Header', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
			'pricing_tab_2_header_color',
			[
				'label' => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-heading .cost' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_control(
			'pricing_tab_2_header_bg',
			[
				'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default'   => '#e55',
				'selectors' => [
					'{{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-heading .cost' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
	        Group_Control_Typography::get_type(),
	            [
	                'name'  => 'pricing_tab_2_pricing_typography',
	                'label' => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
	                'selector' => '{{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-heading .cost .amount',
	            ]
	    );

	   $this->add_control(
			'pricing_tab_2_pricing_currency_size',
			[
				'label' => esc_html__( 'Currency & Period Font Size', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'default'  => [
					'size' =>16,
				],
				'range'  => [
					'px' => [
						'min' => 12,
						'max' => 30,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-heading .cost .curency,
					{{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-heading .cost .period' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

	   $this->add_control(
            'pricing_tab_2_image',
            [
                'label' => esc_html__( 'Icon Image', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

	   	$this->add_control(
            'pricing_tab_2_image_alignment',
            [
                'label' => esc_html__( 'Alignment', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::CHOOSE,
                'label_block' => false,
                'default'  => esc_html__( 'center', 'widgetkit-for-elementor' ),
                'options'  => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'widgetkit-for-elementor' ),
                        'icon'  => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'widgetkit-for-elementor' ),
                        'icon'  => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'widgetkit-for-elementor' ),
                        'icon'  => 'fa fa-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}  .tgx-pricing-tab-1  .tgx-pricing-tab-image' => 'text-align: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'pricing_tab_2_image_margin',
            [
                'label' => esc_html__( 'Margin', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors'  => [
                    '{{WRAPPER}} .tgx-pricing-tab-1  .tgx-pricing-tab-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'pricing_tab_2_about',
            [
                'label' => esc_html__( 'About', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );


        $this->add_control(
			'pricing_tab_2_plan_color',
			[
				'label' => esc_html__( 'Title Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default' => '#e55',
				'selectors' => [
					'{{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
	        Group_Control_Typography::get_type(),
	            [
	                'name'  => 'pricing_tab_2_plan_typography',
	                'label' => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
	                'selector' => '{{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-title',
	            ]
	    );


	    $this->add_control(
			'pricing_tab_2_desc_color',
			[
				'label'   => esc_html__( 'Desc Color', 'widgetkit-for-elementor' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#444',
				'selectors' => [
					'{{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
	        Group_Control_Typography::get_type(),
	            [
	                'name'     => 'pricing_tab_2_desc_typography',
	                'label'    => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
	                'selector' => '{{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-desc',
	            ]
	    );

	    $this->add_control(
            'pricing_tab_2_feature',
            [
                'label' => esc_html__( 'Features', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
			'pricing_tab_2_feature_item_color',
			[
				'label' => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default'   => '#444',
				'selectors' => [
					' {{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-feature' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pricing_tab_2_feature_item_heighlight_color',
			[
				'label' => esc_html__( 'Heighlight Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default' => '#4a6385',
				'selectors' => [
					'{{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-feature li b,
					 {{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-feature li strong' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'  => 'pricing_tab_2_feature_typography',
                'label' => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
                'selector' => '{{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-feature',
            ]
        );

        $this->add_responsive_control(
            'pricing_tab_2_feature_list_margin',
            [
                'label' => esc_html__( 'Margin', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors'  => [
                    '{{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-feature ul' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
			'pricing_tab_2_feature_item_padding',
			[
				'label' => esc_html__( 'Item Gap', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'default'  => [
					'size' =>5,
				],
				'range'  => [
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-feature ul li' => 'padding: {{SIZE}}{{UNIT}} 0;',
				],
			]
		);

		$this->add_control(
            'pricing_tab_2_button',
            [
                'label' => esc_html__( 'Button', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->start_controls_tabs( 'pricing_tab_2_tabs_button' );

        $this->start_controls_tab(
            'pricing_tab_2_normal',
            [
                'label' => esc_html__( 'Normal', 'widgetkit-for-elementor' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'  => 'pricing_tab_2_button_typography',
                'label' => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
                'selector' => '{{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-footer .tgx-pricing-tab-btn',
            ]
        );

        $this->add_control(
            'pricing_tab_2_button_text_color',
            [
                'label' => esc_html__( 'Text Color', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::COLOR,
                'default'   => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-footer .tgx-pricing-tab-btn' => 'color: {{VALUE}};',
                ],
            ]
        );



        $this->add_control(
            'pricing_tab_2_button_background_color',
            [
                'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::COLOR,
                'default'   => '#e55',
                'selectors' => [
                    '{{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-footer .tgx-pricing-tab-btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(), [
                'name'  => 'pricing_tab_2_button_border',
                'label' => esc_html__( 'Border', 'widgetkit-for-elementor' ),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-footer .tgx-pricing-tab-btn',
            ]
        );

        $this->add_control(
            'pricing_tab_2_button_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-footer .tgx-pricing-tab-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'pricing_tab_2_button_text_padding',
            [
                'label' => esc_html__( 'Text Padding', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-footer .tgx-pricing-tab-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

      	$this->start_controls_tab(
            'pricing_tab_2_button_hover',
            [
                'label' => esc_html__( 'Hover', 'widgetkit-for-elementor' ),
            ]
        );

	        $this->add_control(
	            'pricing_tab_2_button_hover_color',
	            [
	                'label' => esc_html__( 'Text Color', 'widgetkit-for-elementor' ),
	                'type'  => Controls_Manager::COLOR,
	                'default'   => '#e55',
	                'selectors' => [
	                    '{{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-footer .tgx-pricing-tab-btn:hover' => 'color: {{VALUE}};',
	                ],
	            ]
	        );

	        $this->add_control(
	            'pricing_tab_2_button_background_hover_color',
	            [
	                'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
	                'type'  => Controls_Manager::COLOR,
	                'default'   => '#fff',
	                'selectors' => [
	                    '{{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-footer .tgx-pricing-tab-btn:hover' => 'background-color: {{VALUE}};',
	                ],
	            ]
	        );

	        $this->add_control(
	            'pricing_tab_2_button_hover_border_color',
	            [
	                'label' => esc_html__( 'Border Color', 'widgetkit-for-elementor' ),
	                'type'  => Controls_Manager::COLOR,
	                'default'  => '#e55',
	                'selectors' => [
	                    '{{WRAPPER}} .tgx-pricing-tab-1 .tgx-pricing-tab-footer .tgx-pricing-tab-btn:hover' => 'border-color:{{VALUE}};',
	                ],
	            ]
	        );

 		$this->end_controls_tab();

        $this->end_controls_tabs();


    $this->end_controls_section();




// tab content 3 style

    $this->start_controls_section(
        'section_pricing_tab_3_style',
            [
                'label' => esc_html__( '3rd Tab Content', 'widgetkit-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'show_label' => false,
            ]
    );


		$this->add_control(
            'pricing_tab_3_heading',
            [
                'label' => esc_html__( 'Header', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
			'pricing_tab_3_header_color',
			[
				'label' => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-heading .cost' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_control(
			'pricing_tab_3_header_bg',
			[
				'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default'   => '#2ecc71',
				'selectors' => [
					'{{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-heading .cost' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
	        Group_Control_Typography::get_type(),
	            [
	                'name'  => 'pricing_tab_3_pricing_typography',
	                'label' => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
	                'selector' => '{{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-heading .cost .amount',
	            ]
	    );

	   $this->add_control(
			'pricing_tab_3_pricing_currency_size',
			[
				'label' => esc_html__( 'Currency & Period Font Size', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'default'  => [
					'size' =>16,
				],
				'range'  => [
					'px' => [
						'min' => 12,
						'max' => 30,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-heading .cost .curency,
					{{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-heading .cost .period' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

	   $this->add_control(
            'pricing_tab_3_image',
            [
                'label' => esc_html__( 'Icon Image', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

	   	$this->add_control(
            'pricing_tab_3_image_alignment',
            [
                'label' => esc_html__( 'Alignment', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::CHOOSE,
                'label_block' => false,
                'default' => esc_html__( 'center', 'widgetkit-for-elementor' ),
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'widgetkit-for-elementor' ),
                        'icon'  => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'widgetkit-for-elementor' ),
                        'icon'  => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'widgetkit-for-elementor' ),
                        'icon'  => 'fa fa-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}  .tgx-pricing-tab-2  .tgx-pricing-tab-image' => 'text-align: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'pricing_tab_3_image_margin',
            [
                'label' => esc_html__( 'Margin', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors'  => [
                    '{{WRAPPER}} .tgx-pricing-tab-2  .tgx-pricing-tab-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'pricing_tab_3_about',
            [
                'label' => esc_html__( 'About', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );


        $this->add_control(
			'pricing_tab_3_plan_color',
			[
				'label' => esc_html__( 'Title Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default'   => '#2ecc71',
				'selectors' => [
					'{{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
	        Group_Control_Typography::get_type(),
	            [
	                'name'  => 'pricing_tab_3_plan_typography',
	                'label' => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
	                'selector' => '{{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-title',
	            ]
	    );


	    $this->add_control(
			'pricing_tab_3_desc_color',
			[
				'label' => esc_html__( 'Desc Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default'   => '#444',
				'selectors' => [
					'{{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
	        Group_Control_Typography::get_type(),
	            [
	                'name'     => 'pricing_tab_3_desc_typography',
	                'label'    => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
	                'selector' => '{{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-desc',
	            ]
	    );

	    $this->add_control(
            'pricing_tab_3_feature',
            [
                'label' => esc_html__( 'Features', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
			'pricing_tab_3_feature_item_color',
			[
				'label' => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default'   => '#444',
				'selectors' => [
					' {{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-feature' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pricing_tab_3_feature_item_heighlight_color',
			[
				'label' => esc_html__( 'Heighlight Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default' => '#4a6385',
				'selectors' => [
					'{{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-feature li b,
					 {{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-feature li strong' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pricing_tab_3_feature_typography',
                'label' => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
                'selector' => '{{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-feature',
            ]
        );

        $this->add_responsive_control(
            'pricing_tab_3_feature_list_margin',
            [
                'label' => esc_html__( 'Margin', 'widgetkit-for-elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-feature ul' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
			'pricing_tab_3_feature_item_padding',
			[
				'label' => esc_html__( 'Item Gap', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'default'  => [
					'size' =>5,
				],
				'range'  => [
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-feature ul li' => 'padding: {{SIZE}}{{UNIT}} 0;',
				],
			]
		);

		$this->add_control(
            'pricing_tab_3_button',
            [
                'label' => esc_html__( 'Button', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->start_controls_tabs( 'pricing_tab_3_tabs_button' );

        $this->start_controls_tab(
            'pricing_tab_3_normal',
            [
                'label' => esc_html__( 'Normal', 'widgetkit-for-elementor' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'  => 'pricing_tab_3_button_typography',
                'label' => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
                'selector' => '{{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-footer .tgx-pricing-tab-btn',
            ]
        );

        $this->add_control(
            'pricing_tab_3_button_text_color',
            [
                'label' => esc_html__( 'Text Color', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::COLOR,
                'default'   => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-footer .tgx-pricing-tab-btn' => 'color: {{VALUE}};',
                ],
            ]
        );



        $this->add_control(
            'pricing_tab_3_button_background_color',
            [
                'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::COLOR,
                'default'   => '#2ecc71',
                'selectors' => [
                    '{{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-footer .tgx-pricing-tab-btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(), [
                'name'  => 'pricing_tab_3_button_border',
                'label' => esc_html__( 'Border', 'widgetkit-for-elementor' ),
                'placeholder' => '1px',
                'default'  => '1px',
                'selector' => '{{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-footer .tgx-pricing-tab-btn',
            ]
        );

        $this->add_control(
            'pricing_tab_3_button_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-footer .tgx-pricing-tab-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'pricing_tab_3_button_text_padding',
            [
                'label' => esc_html__( 'Text Padding', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-footer .tgx-pricing-tab-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

      	$this->start_controls_tab(
            'pricing_tab_3_button_hover',
            [
                'label' => esc_html__( 'Hover', 'widgetkit-for-elementor' ),
            ]
        );

	        $this->add_control(
	            'pricing_tab_3_button_hover_color',
	            [
	                'label' => esc_html__( 'Text Color', 'widgetkit-for-elementor' ),
	                'type'  => Controls_Manager::COLOR,
	                'default'   => '#2ecc71',
	                'selectors' => [
	                    '{{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-footer .tgx-pricing-tab-btn:hover' => 'color: {{VALUE}};',
	                ],
	            ]
	        );

	        $this->add_control(
	            'pricing_tab_3_button_background_hover_color',
	            [
	                'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
	                'type'  => Controls_Manager::COLOR,
	                'default'   => '#fff',
	                'selectors' => [
	                    '{{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-footer .tgx-pricing-tab-btn:hover' => 'background-color: {{VALUE}};',
	                ],
	            ]
	        );

	        $this->add_control(
	            'pricing_tab_3_button_hover_border_color',
	            [
	                'label' => esc_html__( 'Border Color', 'widgetkit-for-elementor' ),
	                'type'  => Controls_Manager::COLOR,
	                'default'   => '#2ecc71',
	                'selectors' => [
	                    '{{WRAPPER}} .tgx-pricing-tab-2 .tgx-pricing-tab-footer .tgx-pricing-tab-btn:hover' => 'border-color:{{VALUE}};',
	                ],
	            ]
	        );

 		$this->end_controls_tab();

        $this->end_controls_tabs();


    $this->end_controls_section();


	} // End Tab section

	protected function render() {
		require WK_PATH . '/elements/pricing-tab/template/view.php';
	}


}
