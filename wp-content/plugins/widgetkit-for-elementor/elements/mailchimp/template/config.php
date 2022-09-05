<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
Use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Border;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Layout
 * =============
 */
class wkfe_mailchimp extends Widget_Base
{

    public function get_name()
    {
        return 'widgetkit-for-elementor-mailchimp';
    }

    public function get_title()
    {
        return __('MailChimp', 'widgetkit-for-elementor');
    }

    public function get_icon()
    {
        return 'eicon-email-field';
    }

    public function get_categories()
    {
        return ['widgetkit_elementor'];
    }
    /**
	 * A list of style that the widgets is depended in
	 **/
	public function get_style_depends() {
        return [
            'widgetkit_bs',
            'fontawesome',
            'widgetkit_main',
        ];
    }

    /**
     * A list of scripts that the widgets is depended in
     *
     * @since 1.3.0
     **/
    public function get_script_depends() {
		return [ 
			'widgetkit-main',
		 ];
	}

    protected function _register_controls()
    {

        /* slides content title subtitle button and button link */
        $this->start_controls_section(
            'section_tab',
            [
                'label' => __('Layout', 'widgetkit-for-elementor'),
            ]
        );
        $this->add_control(
            'placeholder_text',
            [
                'label'       => __( 'Placeholder Text', 'widgetkit-for-elementor' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => __( 'hello@widgetkit.com', 'widgetkit-for-elementor' ),
                'placeholder' => __( 'hello@widgetkit.com', 'widgetkit-for-elementor' ),
            ]
        );
        $this->add_control(
            'button_text',
            [
                'label'       => __( 'Button Text', 'widgetkit-for-elementor' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => __( 'Submit', 'widgetkit-for-elementor' ),
                'placeholder' => __( 'Type your button text here', 'widgetkit-for-elementor' ),
            ]
        );

        $this->end_controls_section();

        /**
         * Style tab
         * =============
         */
        $this->start_controls_section(
            'section_style',
            [
                'label' => __( 'Typography', 'widgetkit-for-elementor' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => __( 'Input', 'widgetkit-for-elementor' ),
                'name' => 'email_input_typography',
                'selector' => '{{WRAPPER}}  .wkfe-newsletter-form-element input[type="email"]',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => __( 'Button', 'widgetkit-for-elementor' ),
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}}  .wkfe-newsletter-form-element input[type="submit"]',
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'input_style',
            [
                'label' => __( 'Input', 'widgetkit-for-elementor' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
            $this->add_control(
                'wkfe_email_input_color',
                [
                    'label' => esc_html__( 'Color', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::COLOR,
                    'default' => '#000',
                    'selectors' => [
                        '{{WRAPPER}} .wkfe-newsletter-form-element input[type="email"]' => 'color: {{VALUE}};',
                    ],
                ]
            );
            $this->add_control(
                'wkfe_email_input_background_color',
                [
                    'label' => esc_html__( 'Background', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::COLOR,
                    'default' => '#fff',
                    'selectors' => [
                        '{{WRAPPER}} .wkfe-newsletter-form-element input[type="email"]' => 'background-color: {{VALUE}};',
                    ],
                ]
            );
            $this->add_responsive_control(
                'wkfe_input_margin',
                [
                    'label' => esc_html__( 'Margin', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px' ],
                    'default' => [
                        'top' => 0,
                        'right' => 0,
                        'bottom' => 0,
                        'left' => 0,
                        'unit' => 'px',
                        'isLinked' => false,
                    ],
                    'devices' => [ 'desktop', 'tablet', 'mobile' ],
                    'desktop_default' => [
                        'size' => 0,
                        'unit' => 'px',
                    ],
                    'tablet_default' => [
                        'size' => 20,
                        'unit' => 'px',
                    ],
                    'mobile_default' => [
                        'size' => 10,
                        'unit' => 'px',
                    ],
                    'selectors'  => [
                        '{{WRAPPER}} .wkfe-newsletter-form-element div.email' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );
            $this->add_responsive_control(
                'input_custom_padding',
                [
                    'label' => __( 'Padding', 'widgetkit-for-elementor' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'default' => [
                        'top' => '10',
                        'right' => '30',
                        'bottom' => '10',
                        'left' => '30',
                        'isLinked' => false,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .wkfe-newsletter-form-element input[type="email"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );
            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name'  => 'wkfe_input_border',
                    'label' => esc_html__( 'Border', 'widgetkit-for-elementor' ),
                    'placeholder' => '1px',
                    'default'   => '1px',
                    'fields_options' => [
                        'border' => [
                            'default' => 'solid',
                        ],
                        'width' => [
                            'default' => [
                                'top' => '2',
                                'right' => '2',
                                'bottom' => '2',
                                'left' => '2',
                                'isLinked' => true,
                            ],
                        ],
                        'color' => [
                            'default' => '#ecb101',
                        ],
                    ],
                    'selector'  => '{{WRAPPER}} .wkfe-newsletter-form-element input[type="email"]',
                    'separator' => 'before',
                ]
            );
            $this->add_control(
                'wkfe_input_email_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'default' => [
                        'top' => '4',
                        'right' => '0',
                        'bottom' => '0',
                        'left' => '4',
                        'isLinked' => true,
                    ],
                    'selectors'  => [
                        '{{WRAPPER}} .wkfe-newsletter-form-element input[type="email"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );
        
        $this->end_controls_section();


        $this->start_controls_section(
            'button_style',
            [
                'label' => __( 'Button', 'widgetkit-for-elementor' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_responsive_control(
            'button_custom_padding',
            [
                'label' => __( 'Button Padding', 'widgetkit-for-elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => '10',
                    'right' => '30',
                    'bottom' => '10',
                    'left' => '30',
                    'isLinked' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .wkfe-newsletter-form-element input[type="submit"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        

        $this->start_controls_tabs( 'tabs_button_style' );
            /**
             * Button normal style is below
             * Hover style will start whle end of this control tab
             * nl prefix stands for newsletter
             */
            $this->start_controls_tab(
                'newsletter_button_normal',
                [
                    'label' => esc_html__( 'Normal', 'widgetkit-for-elementor' ),
                ]
            );
            
            $this->add_control(
                'wkfe_button_text_color',
                [
                    'label'   => esc_html__( 'Text', 'widgetkit-for-elementor' ),
                    'type'    => Controls_Manager::COLOR,
                    'default' => '#fff',
                    'selectors' => [
                        '{{WRAPPER}}  .wkfe-newsletter-form-element input[type="submit"]' => 'color: {{VALUE}};',
                        '{{WRAPPER}}  .wkfe-newsletter-form-element div.loading-ring' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'wkfe_button_background_color',
                [
                    'label' => esc_html__( 'Background', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::COLOR,
                    'default' => '#ecb101',
                    'selectors' => [
                        '{{WRAPPER}} .wkfe-newsletter-form-element input[type="submit"]' => 'background-color: {{VALUE}};',
                        '{{WRAPPER}} .wkfe-newsletter-form-element div.loading-ring' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            /**
             * No need to define color and border field
             * this group will produce those fields
             */
            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name'  => 'wkfe_button_border',
                    'label' => esc_html__( 'Border', 'widgetkit-for-elementor' ),
                    'placeholder' => '1px',
                    'default'   => '1px',
                    'fields_options' => [
                        'border' => [
                            'default' => 'solid',
                        ],
                        'width' => [
                            'default' => [
                                'top' => '2',
                                'right' => '2',
                                'bottom' => '2',
                                'left' => '2',
                                'isLinked' => true,
                            ],
                        ],
                        'color' => [
                            'default' => '#ecb101',
                        ],
                    ],
                    'selector'  => '{{WRAPPER}} .wkfe-newsletter-form-element input[type="submit"]',
                ]
            );
            

            $this->end_controls_tab();
            
            /**
             * Button hover style tab starts here
             */
            $this->start_controls_tab(
                'newsletter_button_hover',
                [
                    'label' => esc_html__( 'Hover', 'widgetkit-for-elementor' ),
                ]
            );

                $this->add_control(
                    'wkfe_button_text_hover_color',
                    [
                        'label'   => esc_html__( 'Text', 'widgetkit-for-elementor' ),
                        'type'    => Controls_Manager::COLOR,
                        'default' => '#ecb101',
                        'selectors' => [
                            '{{WRAPPER}}  .wkfe-newsletter-form-element input[type="submit"]:hover' => 'color: {{VALUE}};',
                        ],
                    ]
                );

                $this->add_control(
                    'wkfe_button_background_hover_color',
                    [
                        'label' => esc_html__( 'Background', 'widgetkit-for-elementor' ),
                        'type'  => Controls_Manager::COLOR,
                        'default' => '#fff',
                        'selectors' => [
                            '{{WRAPPER}} .wkfe-newsletter-form-element input[type="submit"]:hover' => 'background-color: {{VALUE}};',
                        ],
                    ]
                );

                $this->add_group_control(
                    Group_Control_Border::get_type(),
                    [
                        'name'  => 'wkfe_button_hover_border',
                        'label' => esc_html__( 'Border', 'widgetkit-for-elementor' ),
                        'placeholder' => '1px',
                        'default'   => '1px',
                        'selector'  => '{{WRAPPER}} .wkfe-newsletter-form-element input[type="submit"]:hover',
                    ]
                );
                
            $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'wkfe_submit_button_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'default' => [
                    'top' => '0',
                    'right' => '4',
                    'bottom' => '4',
                    'left' => '0',
                    'isLinked' => true,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .wkfe-newsletter-form-element input[type="submit"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );
        
        $this->end_controls_section();

    }

    protected function render()
    {
    ?>
    <?php
        require WK_PATH . '/elements/mailchimp/template/view.php';
        ?>
    <?php
    }
    protected function _content_template()
    {
    }
}
