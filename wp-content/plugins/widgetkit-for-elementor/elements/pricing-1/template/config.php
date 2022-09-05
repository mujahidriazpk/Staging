<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor WidgetKit Pricing 1
 *
 * Elementor widget for WidgetKit pricing 1
 *
 * @since 1.0.0
 */
class wkfe_pricing_1 extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-pricing-1';
	}

	public function get_title() {
		return esc_html__( 'Pricing Single', 'widgetkit-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-price-table wk-icon';
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
            'section_header',
            [
                'label' => esc_html__( 'Header', 'widgetkit-for-elementor' ),
            ]
        );

        $this->add_control(
            'heading',
            [
                'label' => esc_html__( 'Title', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::TEXT,
                'default' => esc_html__( 'Agency Membership', 'widgetkit-for-elementor' ),
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'section_pricing',
            [
                'label' => esc_html__( 'Pricing', 'widgetkit-for-elementor' ),
            ]
        );

        $this->add_control(
            'currency_symbol',
            [
                'label' => esc_html__( 'Currency Symbol', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::SELECT,
                'options' => [
                    ''    => esc_html__( 'None', 'widgetkit-for-elementor' ),
                    'dollar' => '&#36; ' . _x( 'Dollar', 'Currency Symbol', 'widgetkit-for-elementor' ),
                    'euro'   => '&#128; ' . _x( 'Euro', 'Currency Symbol', 'widgetkit-for-elementor' ),
                    'baht'   => '&#3647; ' . _x( 'Baht', 'Currency Symbol', 'widgetkit-for-elementor' ),
                    'franc'  => '&#8355; ' . _x( 'Franc', 'Currency Symbol', 'widgetkit-for-elementor' ),
                    'guilder'=> '&fnof; ' . _x( 'Guilder', 'Currency Symbol', 'widgetkit-for-elementor' ),
                    'krona'  => 'kr ' . _x( 'Krona', 'Currency Symbol', 'widgetkit-for-elementor' ),
                    'lira'   => '&#8356; ' . _x( 'Lira', 'Currency Symbol', 'widgetkit-for-elementor' ),
                    'peseta' => '&#8359 ' . _x( 'Peseta', 'Currency Symbol', 'widgetkit-for-elementor' ),
                    'peso'   => '&#8369; ' . _x( 'Peso', 'Currency Symbol', 'widgetkit-for-elementor' ),
                    'pound'  => '&#163; ' . _x( 'Pound Sterling', 'Currency Symbol', 'widgetkit-for-elementor' ),
                    'real'   => 'R$ ' . _x( 'Real', 'Currency Symbol', 'widgetkit-for-elementor' ),
                    'ruble'  => '&#8381; ' . _x( 'Ruble', 'Currency Symbol', 'widgetkit-for-elementor' ),
                    'rupee'  => '&#8360; ' . _x( 'Rupee', 'Currency Symbol', 'widgetkit-for-elementor' ),
                    'shekel' => '&#8362; ' . _x( 'Shekel', 'Currency Symbol', 'widgetkit-for-elementor' ),
                    'yen'    => '&#165; ' . _x( 'Yen/Yuan', 'Currency Symbol', 'widgetkit-for-elementor' ),
                    'won'    => '&#8361; ' . _x( 'Won', 'Currency Symbol', 'widgetkit-for-elementor' ),
                    'custom' => esc_html__( 'Custom', 'widgetkit-for-elementor' ),
                ],
                'default' => 'dollar',
            ]
        );

        $this->add_control(
            'currency_symbol_custom',
            [
                'label' => esc_html__( 'Custom Symbol', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::TEXT,
                'condition' => [
                    'currency_symbol' => 'custom',
                ],
            ]
        );

        $this->add_control(
            'price',
            [
                'label' => esc_html__( 'Price', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::TEXT,
                'default' => '99',
            ]
        );


        $this->add_control(
            'period',
            [
                'label' => esc_html__( 'Period', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::TEXT,
                'default' => '/ 1 year',
            ]
        );


        $this->add_control(
            'discount_price_switcher',
                [
                    'label'     => esc_html__( 'Discount', 'widgetkit-for-elementor' ),
                    'type'      => Controls_Manager::SWITCHER,
                    'default'   => 'no',
                    'yes'  => esc_html__( 'Yes', 'widgetkit-for-elementor' ),
                    'no'   => esc_html__( 'No', 'widgetkit-for-elementor' ),
                    'separator' => 'before',
                ]
        );


        $this->add_control(
            'discount_price',
            [
                'label' => esc_html__( 'Original Price', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::TEXT,
                'default' => '199',
                'condition' => [
                    'discount_price_switcher' => 'yes',
                ],
            ]
        );


            $this->add_control(
                'currency_position',
                    [
                        'label' => __( 'Position', 'widgetkit-for-elementor' ),
                        'type' => Controls_Manager::CHOOSE,
                        'default' => 'before',
                        'options' => [
                            'before' => [
                                'title' => __( 'Before', 'widgetkit-for-elementor' ),
                                'icon' => 'eicon-h-align-left',
                            ],
                            'after' => [
                                'title' => __( 'After', 'widgetkit-for-elementor' ),
                               'icon' => 'eicon-h-align-right',
                            ],
                        ],
                        'toggle' => false,
                         'condition' => [
                            'discount_price_switcher' => 'yes',
                        ],
                    ]
                );



        $this->end_controls_section();

        $this->start_controls_section(
            'section_features',
            [
                'label' => esc_html__( 'Features', 'widgetkit-for-elementor' ),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'item_text',
            [
                'label'   => esc_html__( 'Text', 'widgetkit-for-elementor' ),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__( 'List Item', 'widgetkit-for-elementor' ),
            ]
        );

        $repeater->add_control(
            'item_icon',
            [
                'label'   => esc_html__( 'Icon', 'widgetkit-for-elementor' ),
                'type'    => Controls_Manager::ICON,
                'default' => 'fa fa-check-circle',
            ]
        );

        $repeater->add_control(
            'item_icon_color',
            [
                'label' => esc_html__( 'Icon Color', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} i' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'features_list',
            [
                'type'    => Controls_Manager::REPEATER,
                'fields'  => $repeater->get_controls(),
                'default' => [
                    [
                        'item_text' => esc_html__( 'Unlimited Domian Usage', 'widgetkit-for-elementor' ),
                        'item_icon' => 'fa fa-check',
                    ],
                    [
                        'item_text' => esc_html__( '3 Domain Tech Support', 'widgetkit-for-elementor' ),
                        'item_icon' => 'fa fa-check',
                    ],
                    [
                        'item_text' => esc_html__( 'Forum Support', 'widgetkit-for-elementor' ),
                        'item_icon' => 'fa fa-check',
                    ],
                    [
                        'item_text' => esc_html__( 'Demo Data Included', 'widgetkit-for-elementor' ),
                        'item_icon' => 'fa fa-check',
                    ],
                    [
                        'item_text' => esc_html__( 'Copyright Removal', 'widgetkit-for-elementor' ),
                        'item_icon' => 'fa fa-check',
                    ],
                ],
                'title_field' => '{{{ item_text }}}',
            ]
        );

        $this->end_controls_section();



        $this->start_controls_section(
            'section_button',
            [
                'label' => esc_html__( 'Button', 'widgetkit-for-elementor' ),
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label'   => esc_html__( 'Button Text', 'widgetkit-for-elementor' ),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__( 'Sign Up', 'widgetkit-for-elementor' ),
            ]
        );

        $this->add_control(
            'link',
            [
                'label' => esc_html__( 'Link', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::URL,
                'placeholder' => 'http://your-link.com',
                'default' => [
                    'url' => '#',
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

	

        $this->start_controls_section(
            'section_header_style',
            [
                'label' => esc_html__( 'Header', 'widgetkit-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'show_label' => false,
            ]
        );


        $this->add_control(
            'heading_color',
            [
                'label' => esc_html__( 'Color', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::COLOR,
                'default'   => '#404040',
                'selectors' => [
                    '{{WRAPPER}} .tgx-price-table__heading' => 'color: {{VALUE}};',
                ],
            ]
        );



        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'heading_typography',
                'selector' => '{{WRAPPER}} .tgx-price-table__heading',
            ]
        );

        
        $this->add_control(
            'title_position',
                [
                    'label'     => esc_html__( 'Position', 'widgetkit-for-elementor' ),
                    'type'      => Controls_Manager::SELECT,
                    'default'   => 'top',
                    'options'   => [
                        'top'     => esc_html__( 'Top', 'widgetkit-for-elementor' ),
                        'bottom'  => esc_html__( 'Bottom', 'widgetkit-for-elementor' ),
                    ],
                ]
        );

        $this->add_control(
            'heading_layout_style',
            [
                'label' => esc_html__( 'Layout', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'layout_position',
                [
                    'label'     => esc_html__( 'Alignment', 'widgetkit-for-elementor' ),
                    'type'      => Controls_Manager::SELECT,
                    'default'   => 'left',
                    'options'   => [
                        'left'     => esc_html__( 'Left', 'widgetkit-for-elementor' ),
                        'center'   => esc_html__( 'Center', 'widgetkit-for-elementor' ),
                        'right'    => esc_html__( 'Right', 'widgetkit-for-elementor' ),
                    ],
                ]
        );

        $this->add_control(
            'layout_bg_color',
            [
                'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::COLOR,
                'default'   => '#f9f9f9',
                'selectors' => [
                    '{{WRAPPER}} .tgx-price-table' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'    => 'layout_box_shadow',
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .tgx-price-table',
            ]
        );


        $this->add_control(
			'title_space',
			[
				'label' => esc_html__( 'Spacing', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' =>20,
				],
				'range'  => [
					'px' => [
						'min' => -15,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tgx-price-table .tgx-price-table__heading' => 'margin: {{SIZE}}{{UNIT}} 0;',
				],
			]
		);


        $this->end_controls_section();

        $this->start_controls_section(
            'section_pricing_element_style',
            [
                'label' => esc_html__( 'Pricing', 'widgetkit-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'show_label' => false,
            ]
        );


        $this->add_control(
            'price_color',
            [
                'label'   => esc_html__( 'Color', 'widgetkit-for-elementor' ),
                'type'    => Controls_Manager::COLOR,
                'default' => '#ed485f',
                'selectors' => [
                    '{{WRAPPER}} .tgx-price-table__currency,
                     {{WRAPPER}} .tgx-price-table__integer-part, 
                    {{WRAPPER}} .tgx-price-table__fractional-part' => 'color: {{VALUE}}',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'price_typography',
                'selector' => '{{WRAPPER}} .tgx-price-table__price',
            ]
        );



        $this->add_control(
            'heading_currency_style',
            [
                'label' => esc_html__( 'Currency', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'currency_symbol!' => '',
                ],
            ]
        );

        $this->add_control(
            'currency_size',
            [
                'label' => esc_html__( 'Size', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::SLIDER,
               'default' => [
                    'size' => 32,
                    'unit' => 'px',
                ],
                'range'  => [
                    'px' => [
                        'min' => 20,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .tgx-price-table__currency' => 'font-size:{{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'currency_symbol!' => '',
                ],
            ]
        );


        $this->add_control(
            'heading_period_style',
            [
                'label' => esc_html__( 'Period', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'currency_symbol!' => '',
                ],
            ]
        );

        $this->add_control(
            'period_color',
            [
                'label' => esc_html__( 'Color', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::COLOR,
                'default' => '#424c57',
                'selectors' => [
                    '{{WRAPPER}} .tgx-price-table__period' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'period_size',
            [
                'label' => esc_html__( 'Size', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::SLIDER,
               'default' => [
                    'size' => 20,
                    'unit' => 'px',
                ],
                'range'  => [
                    'px' => [
                        'min' => 20,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .tgx-price-table__period' => 'font-size:{{SIZE}}{{UNIT}};',
                ],
            ]
        );


        $this->add_control(
            'heading_discount',
            [
                'label' => esc_html__( 'Discount', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'discount_price_switcher' => 'yes',
                ],
            ]
        );



        $this->add_control(
            'discount_color',
            [
                'label' => esc_html__( 'Color', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::COLOR,
                'default' => '#424c57',
                'selectors' => [
                    '{{WRAPPER}} .tgx-price-table__price del' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'discount_price_switcher' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'dicount_size',
            [
                'label' => esc_html__( 'Size', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::SLIDER,
               'default' => [
                    'size' => 20,
                    'unit' => 'px',
                ],
                'range'  => [
                    'px' => [
                        'min' => 20,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .tgx-price-table__price del' => 'font-size:{{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'discount_price_switcher' => 'yes',
                ],
            ]
        );



        $this->add_control(
            'price_list_alignment',
            [
                'label' => esc_html__( 'Alignment', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::CHOOSE,
                'label_block' => false,
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
                    '{{WRAPPER}} .tgx-price-table__price' => 'text-align: {{VALUE}}',
                ],
                'separator' => 'before',
            ]
        );



        $this->add_responsive_control(
            'pricing_element_padding',
            [
                'label' => esc_html__( 'Margin', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .tgx-price-table__price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );



        $this->end_controls_section();

        $this->start_controls_section(
            'section_features_list_style',
            [
                'label' => esc_html__( 'Features', 'widgetkit-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'show_label' => false,
            ]
        );

        $this->add_control(
            'features_list_color',
            [
                'label' => esc_html__( 'Color', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tgx-price-table__features-list' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'features_list_typography',
                'selector' => '{{WRAPPER}} .tgx-price-table__features-list li',
            ]
        );

        $this->add_control(
            'border_color',
            [
                'label' => esc_html__( 'Border Color', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::COLOR,
                'default'   => '#ddd',
                'selectors' => [
                    '{{WRAPPER}} .tgx-price-table>.tgx-price-table__features-list > li:not(:last-child)' => 'border-bottom:1px solid {{VALUE}};',
                ],
            ]
        );


        $this->add_control(
            'features_list_alignment',
            [
                'label' => esc_html__( 'Alignment', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::CHOOSE,
                'label_block' => false,
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
                    '{{WRAPPER}} .tgx-price-table__features-list' => 'text-align: {{VALUE}}',
                ],
            ]
        );

       $this->add_control(
            'divider_gap',
            [
                'label' => esc_html__( 'Gap', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 15,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .tgx-price-table__feature-inner' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );


        $this->add_responsive_control(
            'features_list_padding',
            [
                'label' => esc_html__( 'Margin', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .tgx-price-table__features-list' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );





        $this->end_controls_section();

        $this->start_controls_section(
            'section_footer_style',
            [
                'label' => esc_html__( 'Button', 'widgetkit-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'show_label' => false,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'  => 'button_typography',
                'label' => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
                'selector'  => '{{WRAPPER}} .tgx-price-table__button',
                'condition' => [
                    'button_text!' => '',
                ],
            ]
        );

        $this->add_control(
            'button_width',
            [
                'label' => esc_html__( 'Button Width', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 50,
                    'unit' => '%',
                ],
                'range'  => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .tgx-price-table__button' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );




        $this->start_controls_tabs( 'tabs_button_style' );

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label'     => esc_html__( 'Normal', 'widgetkit-for-elementor' ),
                'condition' => [
                    'button_text!' => '',
                ],
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label'   => esc_html__( 'Text Color', 'widgetkit-for-elementor' ),
                'type'    => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .tgx-price-table__button' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'button_text!' => '',
                ],
            ]
        );



        $this->add_control(
            'button_background_color',
            [
                'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::COLOR,
                'default'   => '#ed485f',
                'selectors' => [
                    '{{WRAPPER}} .tgx-price-table__button' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'button_text!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(), [
                'name'  => 'button_border',
                'label' => esc_html__( 'Border', 'widgetkit-for-elementor' ),
                'placeholder' => '1px',
                'default'   => '1px',
                'selector'  => '{{WRAPPER}} .tgx-price-table__button',
                'condition' => [
                    'button_text!' => '',
                ],
            ]
        );


        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label'     => esc_html__( 'Hover', 'widgetkit-for-elementor' ),
                'condition' => [
                    'button_text!' => '',
                ],
            ]
        );

        $this->add_control(
            'button_hover_color',
            [
                'label' => esc_html__( 'Text Color', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::COLOR,
                'default'   => '#ed485f',
                'selectors' => [
                    '{{WRAPPER}} .tgx-price-table__button:hover' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'button_text!' => '',
                ],
            ]
        );

        $this->add_control(
            'button_background_hover_color',
            [
                'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::COLOR,
                'default'   => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .tgx-price-table__button:hover' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'button_text!' => '',
                ],
            ]
        );

        $this->add_control(
            'button_hover_border_color',
            [
                'label' => esc_html__( 'Border Color', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::COLOR,
                'default'   => '#ed485f',
                'selectors' => [
                    '{{WRAPPER}} .tgx-price-table__button:hover' => 'border: 1px solid  {{VALUE}};',
                ],
            ]
        );


        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'button_text_padding',
            [
                'label' => esc_html__( 'Text Padding', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .tgx-price-table__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'button_text!' => '',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .tgx-price-table__button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'button_text!' => '',
                ],
            ]
        );

        $this->add_control(
            'btn_margin',
            [
                'label' => esc_html__( 'Margin', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors'  => [
                    '{{WRAPPER}} .tgx-price-table__button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'button_list_alignment',
            [
                'label' => esc_html__( 'Alignment', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::CHOOSE,
                'label_block' => false,
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
                    '{{WRAPPER}} .tgx-price-table__footer' => 'text-align: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

    }

    private function get_currency_symbol( $symbol_name ) {
        $symbols = [
            'dollar' => '&#36;',
            'euro'   => '&#128;',
            'franc'  => '&#8355;',
            'pound'  => '&#163;',
            'ruble'  => '&#8381;',
            'shekel' => '&#8362;',
            'baht'   => '&#3647;',
            'yen'    => '&#165;',
            'won'    => '&#8361;',
            'guilder'=> '&fnof;',
            'peso'   => '&#8369;',
            'peseta' => '&#8359',
            'lira'   => '&#8356;',
            'rupee'  => '&#8360;',
            'real'   => 'R$',
            'krona'  => 'kr',
        ];
        return isset( $symbols[ $symbol_name ] ) ? $symbols[ $symbol_name ] : '';
    }

	protected function render() {
		require WK_PATH . '/elements/pricing-1/template/view.php';
	}


}
