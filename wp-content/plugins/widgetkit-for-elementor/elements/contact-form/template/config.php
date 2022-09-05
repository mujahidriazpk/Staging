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
 * Elementor WidgetKit Contact Form 
 *
 * Elementor widget for WidgetKit contact form
 *
 * @since 1.0.0
 */

class wkfe_contact_form extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-contact-form';
	}

	public function get_title() {
		return esc_html__( 'Contact Form', 'widgetkit-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal wk-icon';
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
            'uikit-js',
            'uikit-icons',
		 ];
	}

	protected function _register_controls() {

        $this->start_controls_section(
            'section_contact',
            [
                'label'                 => __( 'Contact Form', 'widgetkit-for-elementor' ),
            ]
        );

            $this->add_control(
                'contact_form_list',
                    [
                        'label'       => __( 'Contact Forms', 'widgetkit-for-elementor' ),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'contact-7',
                        'options' => [
                            'contact-7'   => __( 'Contact Form 7', 'widgetkit-for-elementor' ),
                            'weforms'     => __( 'Weforms', 'widgetkit-for-elementor' ),
                            'wpforms'     => __( 'Wpforms', 'widgetkit-for-elementor' ),
                        ],
                        
                    ]
            );

        
            $this->add_control(
                'choose_form_7',
                [
                    'label'                 => esc_html__( 'Choose Form', 'widgetkit-for-elementor' ),
                    'type'                  => Controls_Manager::SELECT,
                    'label_block'           => true,
                    'options'               => widgetkit_contact_form_7(),
                    'default'               => '0',
                    'condition' => [
                        'contact_form_list' => 'contact-7',
                    ],
                ]
            );
            $this->add_control(
                'choose_weforms',
                [
                    'label'                 => esc_html__( 'Choose Form', 'widgetkit-for-elementor' ),
                    'type'                  => Controls_Manager::SELECT,
                    'label_block'           => true,
                    'options'               => widgetkit_weform(),
                    'default'               => '0',
                    'condition' => [
                        'contact_form_list' => 'weforms',
                    ],
                ]
            );

            $this->add_control(
                'choose_wpforms',
                [
                    'label'                 => esc_html__( 'Choose Form', 'widgetkit-for-elementor' ),
                    'type'                  => Controls_Manager::SELECT,
                    'label_block'           => true,
                    'options'               => widgetkit_wpforms(),
                    'default'               => '0',
                    'condition' => [
                        'contact_form_list' => 'wpforms',
                    ],
                ]
            );


        $this->end_controls_section();




        $this->start_controls_section(
            'section_message',
            [
                'label' => __( 'Message', 'widgetkit-for-elementor' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

            $this->add_control(
                'error_messages',
                [
                    'label'                 => __( 'Error', 'widgetkit-for-elementor' ),
                    'type'                  => Controls_Manager::SELECT,
                    'default'               => 'show',
                    'options'               => [
                        'show'          => __( 'Show', 'widgetkit-for-elementor' ),
                        'hide'          => __( 'Hide', 'widgetkit-for-elementor' ),
                    ],
                    'selectors_dictionary'  => [
                        'show'          => 'block',
                        'hide'          => 'none',
                    ],
                    'selectors'             => [
                        '{{WRAPPER}} .wk-contact-form .wpcf7-not-valid-tip' => 'display: {{VALUE}} !important;',
                    ],
                ]
            );

            $this->add_control(
                'validation_errors',
                [
                    'label'                 => __( 'Validation', 'widgetkit-for-elementor' ),
                    'type'                  => Controls_Manager::SELECT,
                    'default'               => 'show',
                    'options'               => [
                        'show'          => __( 'Show', 'widgetkit-for-elementor' ),
                        'hide'          => __( 'Hide', 'widgetkit-for-elementor' ),
                    ],
                    'selectors_dictionary'  => [
                        'show'          => 'block',
                        'hide'          => 'none',
                    ],
                    'selectors'             => [
                        '{{WRAPPER}} .wk-contact-form .wpcf7-validation-errors' => 'display: {{VALUE}} !important;',
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

        /**
         * Style Tab: Input & Textarea
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_label_style',
            [
                'label'                 => __( 'Label', 'widgetkit-for-elementor' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );


            $this->add_control(
                'label_color',
                [
                    'label'                 => __( 'Color', 'widgetkit-for-elementor' ),
                    'type'                  => Controls_Manager::COLOR,
                    'default'               => '',
                    'selectors'             => [
                        '{{WRAPPER}} .wk-contact-form label' => 'color: {{VALUE}}',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name'                  => 'label_typography',
                    'label'                 => __( 'Typography', 'widgetkit-for-elementor' ),
                    'scheme'                => Typography::TYPOGRAPHY_4,
                    'selector'              => '{{WRAPPER}} .wk-contact-form label',
                ]
            );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_fields_style',
            [
                'label'                 => __( 'Input & Textarea', 'widgetkit-for-elementor' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'field_typography',
                'label'                 => __( 'Typography', 'widgetkit-for-elementor' ),
                'scheme'                => Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .wk-contact-form input[type="text"], {{WRAPPER}} .wk-contact-form input[type="email"], {{WRAPPER}} .wk-contact-form textarea',
            ]
        );

        $this->start_controls_tabs( 'tabs_fields_style' );

        $this->start_controls_tab(
            'tab_fields_normal',
            [
                'label'                 => __( 'Normal', 'widgetkit-for-elementor' ),
            ]
        );

        $this->add_control(
            'field_text_color',
            [
                'label'                 => __( 'Color', 'widgetkit-for-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .wk-contact-form input[type="text"], {{WRAPPER}} .wk-contact-form input[type="email"], {{WRAPPER}} .wk-contact-form textarea' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'field_bg',
            [
                'label'                 => __( 'Background Color', 'widgetkit-for-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .wk-contact-form input[type="text"], {{WRAPPER}} .wk-contact-form input[type="email"], {{WRAPPER}} .wk-contact-form textarea' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'                  => 'field_border',
                'label'                 => __( 'Border', 'widgetkit-for-elementor' ),
                'placeholder'           => '1px',
                'default'               => '1px',
                'selector'              => '{{WRAPPER}} .wk-contact-form input[type="text"], {{WRAPPER}} .wk-contact-form input[type="email"], {{WRAPPER}} .wk-contact-form textarea, {{WRAPPER}} .wk-contact-form .wpuf-form-add.wpuf-style .wpuf-form .wpuf-fields input[type=text], {{WRAPPER}} .wk-contact-form .wpuf-form-add.wpuf-style .wpuf-form .wpuf-fields input[type=email], {{WRAPPER}} .wk-contact-form .wpuf-form-add.wpuf-style .wpuf-form .wpuf-fields textarea',
               
            ]
        );

        $this->add_control(
            'field_radius',
            [
                'label'                 => __( 'Border Radius', 'widgetkit-for-elementor' ),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => [ 'px', 'em', '%' ],
                'selectors'             => [
                    '{{WRAPPER}} .wk-contact-form input[type="text"], {{WRAPPER}} .wk-contact-form input[type="email"], {{WRAPPER}} .wk-contact-form textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator'             => 'before',
            ]
        );

        
        $this->add_responsive_control(
            'input_spacing',
            [
                'label'                 => __( 'Spacing', 'widgetkit-for-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'      => '5',
                    'unit'      => 'px'
                ],
                'range'                 => [
                    'px'        => [
                        'min'   => 0,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', 'em', '%' ],
                'selectors'             => [
                    '{{WRAPPER}} .wk-contact-form input[type="text"], {{WRAPPER}} .wk-contact-form input[type="email"], {{WRAPPER}} .wk-contact-form textarea' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'field_padding',
            [
                'label'                 => __( 'Padding', 'widgetkit-for-elementor' ),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => [ 'px', 'em', '%' ],
                'selectors'             => [
                    '{{WRAPPER}} .wk-contact-form input[type="text"], {{WRAPPER}} .wk-contact-form input[type="date"], {{WRAPPER}} .wk-contact-form select,  {{WRAPPER}} .wk-contact-form input[type="email"], {{WRAPPER}} .wk-contact-form textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
    
        
        $this->add_responsive_control(
            'input_width',
            [
                'label'                 => __( 'Width', 'widgetkit-for-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px'        => [
                        'min'   => 0,
                        'max'   => 1200,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', 'em', '%' ],
                'selectors'             => [
                    '{{WRAPPER}} .wk-contact-form input[type="text"], {{WRAPPER}} .wk-contact-form input[type="email"], {{WRAPPER}} .wk-contact-form textarea' => 'width: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );
        



        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_fields_focus',
            [
                'label'                 => __( 'Focus', 'widgetkit-for-elementor' ),
            ]
        );
        
        $this->add_control(
            'field_bg_focus',
            [
                'label'                 => __( 'Background Color', 'widgetkit-for-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .wk-contact-form input[type="text"]:focus, {{WRAPPER}} .wk-contact-form input[type="email"]:focus, {{WRAPPER}} .wk-contact-form textarea:focus' => 'background-color: {{VALUE}} !important; outline:none;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'                  => 'input_border_focus',
                'label'                 => __( 'Border', 'widgetkit-for-elementor' ),
                'placeholder'           => '1px',
                'default'               => '1px',
                'selector'              => '{{WRAPPER}} .wk-contact-form input[type="text"]:focus, {{WRAPPER}} .wk-contact-form input[type="email"]:focus, {{WRAPPER}} .wk-contact-form textarea:focus, {{WRAPPER}} .wk-contact-form .wpuf-form-add.wpuf-style .wpuf-form .wpuf-fields input[type=text]:focus, {{WRAPPER}} .wk-contact-form .wpuf-form-add.wpuf-style .wpuf-form .wpuf-fields input[type=email]:focus, {{WRAPPER}} .wk-contact-form .wpuf-form-add.wpuf-style .wpuf-form .wpuf-fields textarea:focus',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'                  => 'focus_box_shadow',
                'selector'              => '{{WRAPPER}} .wk-contact-form input[type="text"]:focus, {{WRAPPER}} .wk-contact-form input[type="email"]:focus, {{WRAPPER}} .wk-contact-form textarea:focus',
                'separator'             => 'before',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();
        
        $this->end_controls_section();

        /**
         * Style Tab: Placeholder Section
         */
        $this->start_controls_section(
            'section_placeholder_style',
            [
                'label'                 => __( 'Placeholder', 'widgetkit-for-elementor' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'text_color_placeholder',
            [
                'label'                 => __( 'Color', 'widgetkit-for-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'selectors'             => [
                    '{{WRAPPER}} .wk-contact-form ::-webkit-placeholder, {{WRAPPER}} .wk-contact-form ::placeholder' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'typography_placeholder',
                'label'                 => __( 'Typography', 'widgetkit-for-elementor' ),
                'scheme'                => Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .wk-contact-form ::-webkit-placeholder, {{WRAPPER}} .wk-contact-form ::placeholder',
            ]
        );
        
        $this->end_controls_section();


                /**
         * Style Tab: Submit Button
         */
        $this->start_controls_section(
            'section_submit_button_style',
            [
                'label'                 => __( 'Submit Button', 'widgetkit-for-elementor' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'button_typography',
                'label'                 => __( 'Typography', 'widgetkit-for-elementor' ),
                'scheme'                => Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .wk-contact-form button, {{WRAPPER}} .wk-contact-form input[type="button"], {{WRAPPER}} .wk-contact-form input[type="reset"], {{WRAPPER}} .wk-contact-form input[type="submit"]',
                'separator'             => 'before',
            ]
        );
        

        $this->start_controls_tabs( 'tabs_button_style' );

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label'                 => __( 'Normal', 'widgetkit-for-elementor' ),
            ]
        );
        $this->add_control(
            'button_text_color_normal',
            [
                'label'                 => __( 'Color', 'widgetkit-for-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .wk-contact-form button, {{WRAPPER}} .wk-contact-form input[type="button"], {{WRAPPER}} .wk-contact-form input[type="reset"], {{WRAPPER}} .wk-contact-form input[type="submit"]' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'button_bg_color_normal',
            [
                'label'                 => __( 'Background Color', 'widgetkit-for-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .wk-contact-form button, {{WRAPPER}} .wk-contact-form input[type="button"], {{WRAPPER}} .wk-contact-form input[type="reset"], {{WRAPPER}} .wk-contact-form input[type="submit"]' => 'background-color: {{VALUE}};',
                     '{{WRAPPER}} .wk-contact-form .wpuf-form .wpuf-submit input[type=submit]' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );



        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'                  => 'button_border_normal',
                'label'                 => __( 'Border', 'widgetkit-for-elementor' ),
                'default'               => '1px',
                'selector'              => '{{WRAPPER}} .wk-contact-form button, {{WRAPPER}} .wk-contact-form input[type="button"], {{WRAPPER}} .wk-contact-form input[type="reset"], {{WRAPPER}} .wk-contact-form input[type="submit"],  {{WRAPPER}} .wk-contact-form .wpuf-form .wpuf-submit input[type=submit]',
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label'                 => __( 'Border Radius', 'widgetkit-for-elementor' ),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '{{WRAPPER}} .wk-contact-form button, {{WRAPPER}} .wk-contact-form input[type="button"], {{WRAPPER}} .wk-contact-form input[type="reset"], {{WRAPPER}} .wk-contact-form input[type="submit"], {{WRAPPER}} .wk-contact-form .wpuf-form .wpuf-submit input[type=submit]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );



        $this->add_control(
            'button_width_type',
            [
                'label'                 => __( 'Width', 'widgetkit-for-elementor' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'default',
                'options'               => [
                    'default'       => __( 'Default', 'widgetkit-for-elementor' ),
                    'full-width'    => __( 'Full Width', 'widgetkit-for-elementor' ),
                    'custom'        => __( 'Custom', 'widgetkit-for-elementor' ),
                ],
                'prefix_class'      => 'wk-form-button-',
            ]
        );
        
        $this->add_responsive_control(
            'button_width',
            [
                'label'                 => __( 'Set Width', 'widgetkit-for-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'      => '100',
                    'unit'      => 'px'
                ],
                'range'                 => [
                    'px'        => [
                        'min'   => 0,
                        'max'   => 1120,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '{{WRAPPER}} .wk-submit-button-custom.wk-contact-form button, {{WRAPPER}} .wk-submit-button-custom.wk-contact-form input[type="button"], {{WRAPPER}} .wk-submit-button-custom wk-contact-form input[type="reset"], {{WRAPPER}} .wk-submit-button-custom.wk-contact-form input[type="submit"], {{WRAPPER}} .wk-contact-form .wpuf-form .wpuf-submit input[type=submit]' => 'width: {{SIZE}}{{UNIT}} !important;',
                ],
                'condition'             => [
                    'button_width_type' => 'custom',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_align',
            [
                'label'                 => __( 'Alignment', 'widgetkit-for-elementor' ),
                'type'                  => Controls_Manager::CHOOSE,
                'default'               => 'left',
                'options'               => [
                    'left'        => [
                        'title'   => __( 'Left', 'widgetkit-for-elementor' ),
                        'icon'    => 'eicon-h-align-left',
                    ],
                    'center'      => [
                        'title'   => __( 'Center', 'widgetkit-for-elementor' ),
                        'icon'    => 'eicon-h-align-center',
                    ],
                    'right'       => [
                        'title'   => __( 'Right', 'widgetkit-for-elementor' ),
                        'icon'    => 'eicon-h-align-right',
                    ],
                ],
                'selectors'             => [
                    '{{WRAPPER}} .wk-contact-form.button-right button, .wk-contact-form.button-right input[type="button"], .wk-contact-form.button-right input[type="reset"], .wk-contact-form.button-right input[type="submit"]'   => 'float: {{VALUE}}; ',
                    '{{WRAPPER}} .wk-contact-form.button-center button, .wk-contact-form.button-right input[type="button"], .wk-contact-form.button-center input[type="reset"], .wk-contact-form.button-center input[type="submit"]' => 'display:flex;margin:20px auto;justify-content: center;',
                    '{{WRAPPER}} .wk-contact-form.button-left button, .wk-contact-form.button-left input[type="button"], .wk-contact-form.button-left input[type="reset"], .wk-contact-form.button-left input[type="submit"]' => 'float: {{VALUE}}; ',
                ],
                'condition'             => [
                    'button_width_type' => ['default', 'custom'],
                ],
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label'                 => __( 'Padding', 'widgetkit-for-elementor' ),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '{{WRAPPER}} .wk-contact-form button, {{WRAPPER}} .wk-contact-form input[type="button"], {{WRAPPER}} .wk-contact-form input[type="reset"], {{WRAPPER}} .wk-contact-form input[type="submit"], {{WRAPPER}} .wk-contact-form .wpuf-form .wpuf-submit input[type=submit]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'button_margin',
            [
                'label'                 => __( 'Margin Top', 'widgetkit-for-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px'        => [
                        'min'   => 0,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '{{WRAPPER}} .wk-contact-form button, {{WRAPPER}} .wk-contact-form input[type="button"], {{WRAPPER}} .wk-contact-form input[type="reset"], {{WRAPPER}} .wk-contact-form input[type="submit"], {{WRAPPER}} .wk-contact-form .wpuf-form .wpuf-submit input[type=submit]' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'                  => 'button_box_shadow',
                'selector'              => '{{WRAPPER}} .wk-contact-form button, {{WRAPPER}} .wk-contact-form input[type="button"], {{WRAPPER}} .wk-contact-form input[type="reset"], {{WRAPPER}} .wk-contact-form input[type="submit"], {{WRAPPER}} .wk-contact-form .wpuf-form .wpuf-submit input[type=submit]',
                'separator'             => 'before',
            ]
        );
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label'                 => __( 'Hover', 'widgetkit-for-elementor' ),
            ]
        );
        $this->add_control(
            'button_text_color_hover',
            [
                'label'                 => __( 'Color', 'widgetkit-for-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .wk-contact-form button:hover, {{WRAPPER}} .wk-contact-form input[type="button"]:hover, {{WRAPPER}} .wk-contact-form input[type="reset"]:hover, {{WRAPPER}} .wk-contact-form input[type="submit"]:hover, {{WRAPPER}} .wk-contact-form .wpuf-form .wpuf-submit input[type=submit]:hover' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'button_bg_color_hover',
            [
                'label'                 => __( 'Background Color', 'widgetkit-for-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .wk-contact-form button:hover, {{WRAPPER}} .wk-contact-form input[type="button"]:hover, {{WRAPPER}} .wk-contact-form input[type="reset"]:hover, {{WRAPPER}} .wk-contact-form input[type="submit"]:hover, {{WRAPPER}} .wk-contact-form .wpuf-form .wpuf-submit input[type=submit]:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );



        $this->add_control(
            'button_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'widgetkit-for-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .wk-contact-form button:hover, {{WRAPPER}} .wk-contact-form input[type="button"]:hover, {{WRAPPER}} .wk-contact-form input[type="reset"]:hover, {{WRAPPER}} .wk-contact-form input[type="submit"]:hover, {{WRAPPER}} .wk-contact-form .wpuf-form .wpuf-submit input[type=submit]:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->end_controls_section();

        /**
         * Style Tab: Message
         */
        $this->start_controls_section(
            'section_error_style',
            [
                'label' => __('Message', 'widgetkit-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'error_messages_heading',
            [
                'label' => __('Error Messages', 'widgetkit-for-elementor'),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'error_messages' => 'show',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_error_messages_style');

        $this->start_controls_tab(
            'tab_error_messages_alert',
            [
                'label' => __('Alert', 'widgetkit-for-elementor'),
                'condition' => [
                    'error_messages' => 'show',
                ],
            ]
        );

        $this->add_control(
            'error_alert_text_color',
            [
                'label' => __('Color', 'widgetkit-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .wk-contact-form .wpcf7-not-valid-tip, {{WRAPPER}} .wk-contact-form .wpforms-form label.wpforms-error' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'error_messages' => 'show',
                ],
            ]
        );

        $this->add_responsive_control(
            'error_alert_spacing',
            [
                'label' => __('Spacing', 'widgetkit-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wk-contact-form .wpcf7-not-valid-tip' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'error_messages' => 'show',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_error_messages_fields',
            [
                'label' => __('Fields', 'widgetkit-for-elementor'),
                'condition' => [
                    'error_messages' => 'show',
                ],
            ]
        );
        $this->add_control(
            'error_field_color',
            [
                'label' => __('Color', 'widgetkit-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .wk-contact-form .wpcf7-not-valid' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'error_messages' => 'show',
                ],
            ]
        );

        $this->add_control(
            'error_field_bg_color',
            [
                'label' => __('Background Color', 'widgetkit-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .wk-contact-form .wpcf7-not-valid' => 'background: {{VALUE}}',
                ],
                'condition' => [
                    'error_messages' => 'show',
                ],
            ]
        );



        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'error_field_border',
                'label' => __('Border', 'widgetkit-for-elementor'),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .wk-contact-form .wpcf7-not-valid',
                'separator' => 'after',
                'condition' => [
                    'error_messages' => 'show',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'validation_errors_heading',
            [
                'label' => __('Validation Errors', 'widgetkit-for-elementor'),
                'type' => Controls_Manager::HEADING,
                // 'separator' => 'before',
                'condition' => [
                    'validation_errors' => 'show',
                ],
            ]
        );

        $this->add_control(
            'validation_errors_color',
            [
                'label' => __('Color', 'widgetkit-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .wk-contact-form .wpcf7-validation-errors, {{WRAPPER}} .wk-contact-form .wpuf-form .wpuf-submit .wpuf-errors' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'validation_errors' => 'show',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'validation_errors_typography',
                'label' => __('Typography', 'widgetkit-for-elementor'),
                'scheme' => Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} .wk-contact-form .wpcf7-validation-errors, {{WRAPPER}} .wk-contact-form .wpuf-form .wpuf-submit .wpuf-errors',
                'condition' => [
                    'validation_errors' => 'show',
                ],
            ]
        );

        $this->add_control(
            'validation_errors_bg_color',
            [
                'label' => __('Background Color', 'widgetkit-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .wk-contact-form .wpcf7-validation-errors, {{WRAPPER}} .wk-contact-form .wpuf-form .wpuf-submit .wpuf-errors' => 'background: {{VALUE}};',
                ],
                'condition' => [
                    'validation_errors' => 'show',
                ],
            ]
        );




        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'validation_errors_border',
                'label' => __('Border', 'widgetkit-for-elementor'),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .wk-contact-form .wpcf7-validation-errors',
                'separator' => 'before',
                'condition' => [
                    'validation_errors' => 'show',
                ],
            ]
        );

        $this->add_responsive_control(
            'validation_errors_margin',
            [
                'label' => __('Margin', 'widgetkit-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .wk-contact-form .wpcf7-validation-errors, {{WRAPPER}} .wk-contact-form .wpuf-form .wpuf-submit .wpuf-errors' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'validation_errors' => 'show',
                ],
            ]
        );

        $this->end_controls_section();
    }


	protected function render() {
        require WK_PATH . '/elements/contact-form/template/view.php';

    }


    public function _content_template() {
    }
}
