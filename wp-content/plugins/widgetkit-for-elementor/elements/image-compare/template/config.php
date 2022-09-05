<?php
Use Elementor\Core\Schemes\Typography;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
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

class wkfe_image_compare extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-image-compare';
	}

	public function get_title() {
		return esc_html__( 'Image Compare', 'widgetkit-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-image-before-after';
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
            'event-move',
            'image-compare',
            'uikit-js',
            'uikit-icons',
		 ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'_section_images',
			[
				'label' => __( 'Images', 'widgetkit-for-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

        $this->start_controls_tabs( '_tab_images' );
        $this->start_controls_tab(
            '_tab_before_image',
            [
                'label' => __( 'Before', 'widgetkit-for-elementor' ),
            ]
        );

        $this->add_control(
            'before_image',
            [
                'label' => __( 'Image', 'widgetkit-for-elementor' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );



        $this->add_control(
            'before_label',
            [
                'label' => __( 'Label', 'widgetkit-for-elementor' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Before', 'widgetkit-for-elementor' ),
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            '_tab_after_image',
            [
                'label' => __( 'After', 'widgetkit-for-elementor' ),
            ]
        );

        $this->add_control(
            'after_image',
            [
                'label' => __( 'Image', 'widgetkit-for-elementor' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );

        $this->add_control(
            'after_label',
            [
                'label' => __( 'Label', 'widgetkit-for-elementor' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'After', 'widgetkit-for-elementor' ),
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail',
                'default' => 'full',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'image_border_radius',
            [
                'label' => __( 'Radius', 'widgetkit-for-elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wk-image-compare .image-compare-container img, {{WRAPPER}} .wk-image-compare .image-compare-overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_controls',
            [
                'label' => __( 'Controls', 'widgetkit-for-elementor' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );


        $this->add_control(
            'orientation',
            [
                'label' => __( 'Orientation', 'widgetkit-for-elementor' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'horizontal' => [
                        'title' => __( 'Horizontal', 'widgetkit-for-elementor' ),
                        'icon' => 'fa fa-arrows-h',
                    ],
                    'vertical' => [
                        'title' => __( 'Vertical', 'widgetkit-for-elementor' ),
                        'icon' => 'fa fa-arrows-v',
                    ],
                ],
                'default' => 'horizontal',
            ]
        );

        $this->add_control(
            'hide_overlay',
            [
                'label' => __( 'Display Overlay', 'widgetkit-for-elementor' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', 'widgetkit-for-elementor' ),
                'label_off' => __( 'No', 'widgetkit-for-elementor' ),
                'return_value' => 'yes',
                'description' => __( 'Hide overlay with before and after label', 'widgetkit-for-elementor' ),
                'style_transfer' => true,
            ]
        );


        $this->add_control(
            'click_enable',
            [
                'label' => __( 'Click to Move', 'widgetkit-for-elementor' ),
                'type' => Controls_Manager::SWITCHER,
                'yes' => __( 'Yes', 'widgetkit-for-elementor' ),
                'no' => __( 'No', 'widgetkit-for-elementor' ),
                'return_value' => 'no',
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_handle',
            [
                'label' => __( 'Handle', 'widgetkit-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'separator_handle_color',
            [
                'label' => __( 'Color', 'widgetkit-for-elementor' ),
                'type' => Controls_Manager::COLOR,
                 'selectors' => [
                    '{{WRAPPER}} .wk-image-compare  .image-compare-handle:before, {{WRAPPER}} .wk-image-compare .image-compare-handle:after' => 'background:{{VALUE}};',
                    '{{WRAPPER}} .wk-image-compare .image-compare-handle' => 'border-color:{{VALUE}} !important;',
                    '{{WRAPPER}} .wk-image-compare .image-compare-right-arrow' => 'border-left-color:{{VALUE}};',
                    '{{WRAPPER}} .wk-image-compare .image-compare-left-arrow' => 'border-right-color:{{VALUE}};',
                      '{{WRAPPER}} .wk-image-compare .image-compare-up-arrow' => 'border-bottom-color:{{VALUE}};',
                    '{{WRAPPER}} .wk-image-compare .image-compare-down-arrow' => 'border-top-color:{{VALUE}};',
                ],
            ]
        );


        $this->add_responsive_control(
            'arrow_box_width',
            [
                'label' => __( 'Border Width', 'widgetkit-for-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 2,
                        'max' => 4,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wk-image-compare .image-compare-horizontal .image-compare-handle:before, {{WRAPPER}} .wk-image-compare .image-compare-horizontal .image-compare-handle:after' => 'width:{{SIZE}}{{UNIT}};',
                     '{{WRAPPER}} .wk-image-compare .image-compare-vertical .image-compare-handle:before, 
                     {{WRAPPER}} .wk-image-compare .image-compare-vertical .image-compare-handle:after' => 'height:{{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .wk-image-compare .image-compare-handle' => 'border:{{SIZE}}{{UNIT}} solid;',
                ],
            ]
        );


        $this->add_responsive_control(
            'handler_border_radius',
            [
                'label' => __( 'Circle Radius', 'widgetkit-for-elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wk-image-compare .image-compare-handle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            '_section_style_label',
            [
                'label' => __( 'Label', 'widgetkit-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_control(
            'label_color',
            [
                'label' => __( 'Color', 'widgetkit-for-elementor' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wk-image-compare .image-compare-before-label:before, {{WRAPPER}} .wk-image-compare .image-compare-after-label:before' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'label_typography',
                'selector' => '{{WRAPPER}} .wk-image-compare .image-compare-before-label:before, {{WRAPPER}} .wk-image-compare .image-compare-after-label:before',
                'scheme' => Typography::TYPOGRAPHY_3,
            ]
        );



        $this->add_control(
            'label_bg_color',
            [
                'label' => __( 'Background Color', 'widgetkit-for-elementor' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wk-image-compare .image-compare-before-label:before, {{WRAPPER}} .wk-image-compare .image-compare-after-label:before' => 'background-color: {{VALUE}}',
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'label_box_shadow',
                'selector' => '{{WRAPPER}} .wk-image-compare .image-compare-before-label:before, {{WRAPPER}} .wk-image-compare .image-compare-after-label:before'
            ]
        );


        $this->add_responsive_control(
            'label_border_radius',
            [
                'label' => __( 'Border Radius', 'widgetkit-for-elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wk-image-compare .image-compare-before-label:before, {{WRAPPER}} .wk-image-compare .image-compare-after-label:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'label_padding',
            [
                'label' => __( 'Padding', 'widgetkit-for-elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wk-image-compare .image-compare-before-label:before, {{WRAPPER}} .wk-image-compare .image-compare-after-label:before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );





        $this->end_controls_section();
    }


	protected function render() {
        require WK_PATH . '/elements/image-compare/template/view.php';

    }


    public function _content_template() {
    }
}
