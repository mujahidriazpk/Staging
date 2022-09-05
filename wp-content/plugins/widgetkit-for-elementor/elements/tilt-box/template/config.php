<?php
Use Elementor\Core\Schemes\Typography;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor WidgetKit Image Compare
 *
 * Elementor widget for WidgetKit image compare
 *
 * @since 1.0.0
 */

class wkfe_tilt_box extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-tilt-box';
	}

	public function get_title() {
		return esc_html__( 'Tilt Box', 'widgetkit-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-flip-box wk-icon';
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
            'uikit',
        ];
    }
	/**
	 * A list of scripts that the widgets is depended in
	 **/
	public function get_script_depends() {
		return [ 
			'widgetkit-main',
            // 'vanilla-tilt',
            'uikit-js',
            'uikit-icons',
		 ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_image',
			[
				'label' => __( 'Image', 'widgetkit-for-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_group_control(
            Group_Control_Background::get_type(),
                [
                    'name' => 'background',
                    'label' => __( 'Background', 'widgetkit-for-elementor' ),
                    'types' => [ 'classic', 'gradient' ],
                    'selector' => '{{WRAPPER}} .wk-tilt-box .tilt-element',
                ]
        );



        $this->add_responsive_control(
            'content_height',
            [
                'label' => __( 'Height', 'widgetkit-for-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],

                'default' => [
                    'size' => 400,
                ],
                'range' => [
                    'px' => [
                        'min' => 400,
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wk-tilt-box .tilt-element' => 'height:{{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',

            ]
        );



        $this->end_controls_section();

        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'widgetkit-for-elementor' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

            $this->add_control(
                'content_icon',
                [
                    'label' => __( 'Icon', 'widgetkit-for-elementor' ),
                    'type' => Controls_Manager::ICONS,
                    'fa4compatibility' => 'icon',
                ]
            );

            $this->add_control(
                'content_title',
                [
                    'label' => __( 'Title', 'widgetkit-for-elementor' ),
                    'type' => Controls_Manager::TEXT,
                    'default' => __( '', 'widgetkit-for-elementor' ),
                ]
            );

            $this->add_control(
                'content_description',
                [
                    'label' => __( 'Description', 'widgetkit-for-elementor' ),
                    'type' => Controls_Manager::TEXTAREA,
                    'default' => __( '', 'widgetkit-for-elementor' ),
                ]
            );

            $this->add_control(
                'content_button',
                [
                    'label' => __( 'Button Text', 'widgetkit-for-elementor' ),
                    'type' => Controls_Manager::TEXT,
                    'default' => __( '', 'widgetkit-for-elementor' ),
                ]
            );

            $this->add_control(
                'content_link',
                [
                    'label' => __( 'Link', 'widgetkit-for-elementor' ),
                    'type' => Controls_Manager::URL,
                    'placeholder' => __( 'https://your-link.com', 'widgetkit-for-elementor' ),

                ]
            );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_layout',
            [
                'label' => __( 'Layout', 'widgetkit-for-elementor' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'effect_enable',
                [
                    'label' => __( 'Effect Enable', 'widgetkit-for-elementor' ),
                    'type' => Controls_Manager::SWITCHER,
                    'yes' => __( 'Yes', 'widgetkit-for-elementor' ),
                    'no' => __( 'No', 'widgetkit-for-elementor' ),
                    'return_value' => 'yes',
                    'default'   => 'yes',
                    'separator' => 'before',
                ]
            );


            $this->add_control(
                'select_effect',
                    [
                        'label'       => __( 'Choose Effect', 'widgetkit-for-elementor' ),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'default',
                        'options' => [
                            'default'   => __( 'Default', 'widgetkit-for-elementor' ),
                            'glare'     => __( 'Glare', 'widgetkit-for-elementor' ),
                            'reverse'   => __( 'Reverse', 'widgetkit-for-elementor' ),
                            'floating'  => __( 'Floating', 'widgetkit-for-elementor' ),
                            // 'listening'  => __( 'Listening', 'widgetkit-for-elementor' ),
                            'x'  => __( 'X axis', 'widgetkit-for-elementor' ),
                            'y'  => __( 'Y axis', 'widgetkit-for-elementor' ),
                        ],
                        
                        'condition' => [
                            'effect_enable' => 'yes',
                        ],
                    ]
            );

            $this->add_control(
                'item_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors'  => [
                        '{{WRAPPER}} .wk-tilt-box .tilt-element' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                    ],
                    
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name'                  => 'item_box_shadow',
                    'selector'              => '{{WRAPPER}} .wk-tilt-box .tilt-element',
                ]
            );



            $this->add_control(
                'content_heading',
                [
                    'label' => esc_html__( 'Content', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );


            $this->add_control(
                'content_position',
                    [
                        'label'       => __( 'Display', 'widgetkit-for-elementor' ),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'overlay',
                        'options' => [
                            'overlay'   => __( 'Overlay', 'widgetkit-for-elementor' ),
                            'bottom'    => __( 'Bottom', 'widgetkit-for-elementor' ),
                        ],
                        
                    ]
            );

            $this->add_control(
                'content_overlay_position',
                [
                    'label' => __( 'Position', 'widgetkit-for-elementor' ),
                    'type' => Controls_Manager::CHOOSE,
                    'label_block' => false,
                    'options' => [
                        'top' => [
                            'title' => __( 'Top', 'widgetkit-for-elementor' ),
                            'icon' => 'eicon-v-align-top',
                        ],
                        'middle' => [
                            'title' => __( 'Center', 'widgetkit-for-elementor' ),
                            'icon' => 'eicon-h-align-center',
                        ],
                        'bottom' => [
                            'title' => __( 'Bottom', 'widgetkit-for-elementor' ),
                            'icon' => 'eicon-v-align-bottom',
                        ],
                    ],
                    'default' => 'center',
                    'condition' => [
                        'content_position' => 'overlay',
                    ],
                ]
            );


            $this->add_control(
                'content_align',
                [
                    'label' => esc_html__( 'Alignment', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::CHOOSE,
                    'default'   => 'left',
                    'options' => [
                        'left'    => [
                            'title' => esc_html__( 'Left', 'widgetkit-for-elementor' ),
                            'icon'  => 'eicon-text-align-left',
                        ],
                        'center' => [
                            'title' => esc_html__( 'Center', 'widgetkit-for-elementor' ),
                            'icon'  => 'eicon-text-align-center',
                        ],
                        'right' => [
                            'title' => esc_html__( 'Right', 'widgetkit-for-elementor' ),
                            'icon'  => 'eicon-text-align-left',
                        ],
                    ],
                ]
            );



        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_content',
            [
                'label' => __( 'Content', 'widgetkit-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

            $this->add_control(
                'icon_heading',
                [
                    'label' => esc_html__( 'Icon', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::HEADING,
                ]
            );

            $this->add_control(
                'icon_color',
                [
                    'label' => __( 'Color', 'widgetkit-for-elementor' ),
                    'type' => Controls_Manager::COLOR,
                     'selectors' => [
                        '{{WRAPPER}} .wk-tilt-box .wk-tilt-card .wk-tilt-card-icon-top' => 'color:{{VALUE}};',
                        '{{WRAPPER}} .wk-tilt-box .wk-tilt-card .wk-tilt-card-icon-top svg' => 'fill:{{VALUE}};',

                    ],
                ]
            );

            $this->add_responsive_control(
                'icon_size',
                [
                    'label' => __( 'Size', 'widgetkit-for-elementor' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%' ],
                    'default' => [
                        'size' => 30,
                    ],
                    'range' => [
                        'px' => [
                            'min' => 20,
                            'max' => 60,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wk-tilt-box .wk-tilt-card .wk-tilt-card-icon-top' => 'font-size:{{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .wk-tilt-box .wk-tilt-card .wk-tilt-card-icon-top svg' => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};',
                    ],
                    'separator' => 'after',
                ]
            );


            $this->add_control(
                'title_heading',
                [
                    'label' => esc_html__( 'Title', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::HEADING,

                ]
            );

            $this->add_control(
                'title_color',
                [
                    'label' => __( 'Color', 'widgetkit-for-elementor' ),
                    'type' => Controls_Manager::COLOR,
                     'selectors' => ['{{WRAPPER}} .wk-tilt-box .wk-tilt-card .wk-tilt-card-body .wk-tilt-card-title a' => 'color:{{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                    [
                        'name'     => 'title_typography',
                        'label'    => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
                        'scheme'   => Typography::TYPOGRAPHY_4,
                        'selector' => '{{WRAPPER}} .wk-tilt-box .wk-tilt-card .wk-tilt-card-body .wk-tilt-card-title',
                    ]
            );

            $this->add_responsive_control(
                'title_spacing',
                [
                    'label' => esc_html__( 'Spacing', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'allowed_dimensions' => ['top', 'bottom' ],
                        'selectors'  => [
                        '{{WRAPPER}} .wk-tilt-box .wk-tilt-card .wk-tilt-card-body .wk-tilt-card-title' => 'margin: {{TOP}}{{UNIT}} 0 {{BOTTOM}}{{UNIT}};',
                        
                    ],
                    'separator' => 'after',
                ]
            );

            $this->add_control(
                'desc_heading',
                [
                    'label' => esc_html__( 'Description', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::HEADING,

                ]
            );

            $this->add_control(
                'desc_color',
                [
                    'label' => __( 'Color', 'widgetkit-for-elementor' ),
                    'type' => Controls_Manager::COLOR,
                     'selectors' => ['{{WRAPPER}} .wk-tilt-box .wk-tilt-card .wk-tilt-card-body .wk-tilt-card-desc' => 'color:{{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                    [
                        'name'     => 'desc_typography',
                        'label'    => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
                        'scheme'   => Typography::TYPOGRAPHY_4,
                        'selector' => '{{WRAPPER}} .wk-tilt-box .wk-tilt-card .wk-tilt-card-body .wk-tilt-card-desc',
                    ]
            );


            $this->add_responsive_control(
                'description_spacing',
                [
                    'label' => __( 'Bottom Spacing', 'widgetkit-for-elementor' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%' ],
                    'default' => [
                        'size' => 10,
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 50,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wk-tilt-box .wk-tilt-card .wk-tilt-card-body .wk-tilt-card-desc' => 'margin-bottom:{{SIZE}}{{UNIT}};',
                    ],
                ]
            );



            $this->add_control(
                'button_heading',
                [
                    'label' => esc_html__( 'Button', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::HEADING,
                    'separator' => 'before',

                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'button_typography',
                    'selector' => '{{WRAPPER}} .wk-tilt-box .wk-tilt-card .wk-tilt-card-body .wk-button',
                    'scheme' => Typography::TYPOGRAPHY_3,
                ]
            );

            $this->start_controls_tabs( 'tabs_button_style' );

            $this->start_controls_tab(
                'button_normal',
                [
                    'label' => esc_html__( 'Normal', 'widgetkit-for-elementor' ),
                ]
            );

            $this->add_control(
                'button_text_color',
                [
                    'label'   => esc_html__( 'Color', 'widgetkit-for-elementor' ),
                    'type'    => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .wk-tilt-box .wk-tilt-card .wk-tilt-card-body .wk-button' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .wk-tilt-box .wk-tilt-card .wk-tilt-card-body .wk-button:hover' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'button_background_color',
                [
                    'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .wk-tilt-box .wk-tilt-card .wk-tilt-card-body .wk-button' => 'background-color: {{VALUE}};',
                        '{{WRAPPER}} .wk-tilt-box .wk-tilt-card .wk-tilt-card-body .wk-button:hover' => 'color: {{VALUE}}; border-color:{{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name'  => 'button_border',
                    'label' => esc_html__( 'Border', 'widgetkit-for-elementor' ),
                    'placeholder' => '1px',
                    'default'   => '1px',
                    'selector'  => '{{WRAPPER}} .wk-tilt-box .wk-tilt-card .wk-tilt-card-body .wk-button',
                  
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'button_hover',
                [
                    'label' => esc_html__( 'Hover', 'widgetkit-for-elementor' ),
                ]
            );

            $this->add_control(
                'button_hover_color',
                [
                    'label' => esc_html__( 'Color', 'widgetkit-for-elementor' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .wk-tilt-box .wk-tilt-card .wk-tilt-card-body .wk-button:hover' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'button_background_hover_color',
                [
                    'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .wk-tilt-box .wk-tilt-card .wk-tilt-card-body .wk-button:hover' => 'background-color: {{VALUE}};',
                    ],

                ]
            );

            $this->add_control(
                'button_hover_border_color',
                [
                    'label' => esc_html__( 'Border Color', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::COLOR,
                    'default'   => '',
                    'selectors' => [
                        '{{WRAPPER}} .wk-tilt-box .wk-tilt-card .wk-tilt-card-body .wk-button:hover' => 'border-color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_responsive_control(
                'button_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors'  => [
                        '{{WRAPPER}} .wk-tilt-box .wk-tilt-card .wk-tilt-card-body .wk-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' => 'before',
                ]
            );

            $this->add_responsive_control(
                'button_text_padding',
                [
                    'label' => esc_html__( 'Padding', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors'  => [
                        '{{WRAPPER}} .wk-tilt-box .wk-tilt-card .wk-tilt-card-body .wk-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    
                ]
            );

            $this->add_control(
                'common_heading',
                [
                    'label' => esc_html__( 'Common', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::HEADING,
                    'separator' => 'before',

                ]
            );


            $this->add_control(
                'content_bg_color',
                [
                    'label' => __( 'Background Color', 'widgetkit-for-elementor' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .wk-tilt-box .wk-tilt-card' => 'background-color: {{VALUE}}',
                    ],
                ]
            );
            $this->add_responsive_control(
                'content_padding',
                [
                    'label' => esc_html__( 'Padding', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors'  => [
                        '{{WRAPPER}} .wk-tilt-box .wk-tilt-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    
                ]
            );


        $this->end_controls_section();
    }


	protected function render() {
        require WK_PATH . '/elements/tilt-box/template/view.php';

    }


    public function _content_template() {
    }
}
