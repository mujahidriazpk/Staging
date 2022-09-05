<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
Use Elementor\Core\Schemes\Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor WidgetKit Lottie Animation
 *
 * Elementor widget for WidgetKit Lottie Animation
 *
 * @since 1.0.0
 */
class wkfe_lottie_animation extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-lottie-animation';
	}

	public function get_title() {
		return esc_html__( 'Lottie Animation', 'widgetkit-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-animation wk-icon';
	}

	public function get_categories() {
		return [ 'widgetkit_elementor' ];
	}

	/**
	 * A list of scripts that the widgets is depended in
	 **/
	public function get_script_depends() {
		return [ 
			'lottie-js',
			'widgetkit-main',
		 ];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Animation', 'widgetkit-for-elementor' ),
			]
		);


		$this->add_control(
			'choose_data_file_source',
				[
					'label'     => esc_html__( 'JSON File Source', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
                    'default'   => 'upload',
					'options'   => [
						'upload'   => esc_html__('Upload File', 'widgetkit-for-elementor'),
						'url'      => esc_html__( 'URL', 'widgetkit-for-elementor' ),
					],
				]
		);


		$this->add_control(
	       'json_file',
            [
                'label' => esc_html__( 'JSON File', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::MEDIA,
                'media_type'  => 'application/json',
                'condition' => [
                    'choose_data_file_source' => 'upload',
                ]
		    ]
        );
        
        $this->add_control(
			'json_file_link',
			[
				'label' => __( 'JSON Link', 'widgetkit-for-elementor' ),
				'type' => Controls_Manager::TEXT,
                'placeholder' => __( 'https://your-link.com', 'widgetkit-for-elementor' ),
                'input_type'    => 'url',
                'label_block'   => true,
                'condition' => [
                    'choose_data_file_source' => 'url',
                ]
			]
        );
        
        $this->add_control(
            'choose_link',
            array(
              'label'     => __( 'Link', 'widgetkit-for-elementor' ),
              'type'      => Controls_Manager::SWITCHER,
              'label_on'  => __( 'Yes', 'widgetkit-for-elementor' ),
              'label_off' => __( 'No', 'widgetkit-for-elementor' )
            )
        );

		$this->add_control(
			'link',
			[
				'label' => __( 'Link', 'widgetkit-for-elementor' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'widgetkit-for-elementor' ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
                ],
                'condition' => [
                    'choose_link'   => 'yes'
                ],
                'show_label'    => false
			]
        );
        
        $this->add_control(
			'widget_caption',
			[
				'label' => __( 'Caption', 'widgetkit-pro'),
				'type' => Controls_Manager::TEXT,
                'label_block'   => true
			]
		);

        $this->end_controls_section();
        

        $this->start_controls_section(
			'animation_options',
			[
				'label' => esc_html__( 'Animation Options', 'widgetkit-for-elementor' ),
			]
        );
        
        $this->add_control(
			'animation_play_type',
				[
					'label'     => esc_html__( 'Play Type', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
                    'default'   => 'autoplay',
					'options'   => [
						'autoplay'  => esc_html__('Auto Play', 'widgetkit-for-elementor' ),
						'onhover'   => esc_html__('ON Hover', 'widgetkit-for-elementor'),
						'onclick'   => esc_html__('ON Click', 'widgetkit-for-elementor' ),
						'viewport'  => esc_html__('View Port Based', 'widgetkit-for-elementor' ),
					],
				]
        );

        $this->add_control(
			'animation_speed',
			[
				'label' => __( 'Speed', 'widgetkit-for-elementor'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 2.5,
				'step' => 0.5,
				'default' => 1,
			]
		);
        
        $this->add_control(
            'choose_loop',
            array(
              'label'     => __( 'Loop', 'widgetkit-for-elementor' ),
              'type'      => Controls_Manager::SWITCHER,
              'label_on'  => __( 'Yes', 'widgetkit-for-elementor' ),
              'label_off' => __( 'No', 'widgetkit-for-elementor' )
            )
        );

        $this->add_control(
            'choose_reversed',
            array(
              'label'     => __( 'Reverse', 'widgetkit-for-elementor' ),
              'type'      => Controls_Manager::SWITCHER,
              'label_on'  => __( 'Yes', 'widgetkit-for-elementor' ),
              'label_off' => __( 'No', 'widgetkit-for-elementor' )
            )
        );

        $this->add_control(
			'animation_renderer_type',
				[
					'label'     => esc_html__( 'Animation Renderer Type', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
                    'default'   => 'svg',
					'options'   => [
						'svg'       => esc_html__('SVG', 'widgetkit-for-elementor'),
						'canvas'    => esc_html__( 'Canvas', 'widgetkit-for-elementor' )
					],
				]
		);
	
        $this->end_controls_section();
        
		$this->start_controls_section(
			'lottie_animation_style',
			[
				'label' => esc_html__( 'Lottie', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		
		// Dimensions
        $this->add_responsive_control(
            'width',
            array(
                'label' => __( 'Width', 'widgetkit-for-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => array( '%' ),
                'default' => [
					'unit' => '%',
				],
                'range' => array(
                    '%' => array(
                        'min' => 1,
                        'max' => 100,
                    )
                ),
                'selectors' => array(
                    '{{WRAPPER}} figure' => 'width: {{SIZE}}{{UNIT}};',
                ),
            )
        );
        
        $this->add_responsive_control(
            'height',
            array(
                'label' => __( 'Height', 'widgetkit-for-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => array( '%' ),
                'range' => array(
                    '%' => array(
                        'min' => 1,
                        'max' => 100,
                    ),
                ),
                'default' => [
                    'unit' => '%',
                ],
                'selectors' => array(
                    '{{WRAPPER}} figure' => 'height: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        // Border and box shadow
        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'      => 'lottie_border',
                'selector'  => '{{WRAPPER}} figure',
                'separator' => 'before',
            )
        );

        $this->add_responsive_control(
            'lottie_border_radius',
            array(
                'label'       => __( 'Border Radius', 'widgetkit-for-elementor' ),
                'type'        => Controls_Manager::DIMENSIONS,
                'size_units'  => array( 'px', '%' ),
                'selectors'   => array(
                    '{{WRAPPER}} figure' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'      => 'lottie_box_shadow',
                'exclude'   => array( 'box_shadow_position' ),
                'selector'  => '{{WRAPPER}} figure',
            )
        );

        $this->add_responsive_control(
            'align',
            array(
                'label' => __( 'Alignment', 'widgetkit-for-elementor' ),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => array(
                    'flex-start' => array(
                        'title' => __( 'Left', 'widgetkit-for-elementor' ),
                        'icon' => 'eicon-text-align-left',
                    ),
                    'center' => array(
                        'title' => __( 'Center', 'widgetkit-for-elementor' ),
                        'icon' => 'eicon-text-align-center',
                    ),
                    'flex-end' => array(
                        'title' => __( 'Right', 'widgetkit-for-elementor' ),
                        'icon' => 'eicon-text-align-right',
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} .elementor-widget-container'   => 'display: flex; justify-content: {{VALUE}};',
                    '{{WRAPPER}} .elementor-widget-container a' => 'display: flex; justify-content: {{VALUE}};'
                ),
            )
        );
        
        $this->add_control(
            'caption_heading',
            [
                'label' => __( 'Caption', 'widgetkit-pro' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'caption_color',
            [
                'label'     => esc_html__( 'Color', 'widgetkit-pro' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} figure figcaption' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
                [
                    'name'     => 'caption_typography',
                    'label'    => esc_html__( 'Typography', 'widgetkit-pro' ),
                    'selector' => '{{WRAPPER}} figure figcaption',
                    'scheme' => Typography::TYPOGRAPHY_3,
                ]
        );

        $this->add_control(
			'margin',
			[
				'label' => __( 'Margin', 'widgetkit-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} figure figcaption' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'padding',
			[
				'label' => __( 'Padding', 'widgetkit-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} figure figcaption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

        $this->add_responsive_control(
            'caption_align',
            array(
                'label' => __( 'Alignment', 'widgetkit-for-elementor' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => array(
                    'left' => array(
                        'title' => __( 'Left', 'widgetkit-for-elementor' ),
                        'icon' => 'eicon-text-align-left',
                    ),
                    'center' => array(
                        'title' => __( 'Center', 'widgetkit-for-elementor' ),
                        'icon' => 'eicon-text-align-center',
                    ),
                    'right' => array(
                        'title' => __( 'Right', 'widgetkit-for-elementor' ),
                        'icon' => 'eicon-text-align-right',
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} figure figcaption'   => 'text-align: {{VALUE}};',
                ),
            )
        );
        
        $this->end_controls_section();

	}

	protected function render() {
		require WK_PATH . '/elements/lottie/template/view.php';
	}

}
