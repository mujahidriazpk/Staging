<?php

use Elementor\Group_Control_Border;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
Use Elementor\Core\Schemes\Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor WidgetKit Testimonial 2
 *
 * Elementor widget for WidgetKit testimoinal 2
 *
 * @since 1.0.0
 */
class wkfe_testimonial_2 extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-testimonial-2';
	}

	public function get_title() {
		return esc_html__( 'Testimonial Single', 'widgetkit-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-testimonial wk-icon';
	}

	public function get_categories() {
		return [ 'widgetkit_deprecated_elements' ];
	}

	/**
	 * A list of style that the widgets is depended in
	 **/
	public function get_style_depends() {
        return [
            'widgetkit_bs',
			'owl-css',
            'animate-css',			
            'widgetkit_main',
        ];
    }
	/**
	 * A list of scripts that the widgets is depended in
	 **/
	public function get_script_depends() {
		return [ 
			'bootstarp-js',
			'owl-carousel',
			'widgetkit-main',
		 ];
	}

	protected function _register_controls() {

			// Content options Start
	$this->start_controls_section(
		'section_content',
			[
				'label' => esc_html__( 'Testimonials', 'widgetkit-for-elementor' ),
			]
		);


		$repeater = new Repeater();
	    $repeater->add_control(
		    'title_2',
		      	[
		          'label' => esc_html__( 'Title', 'widgetkit-for-elementor' ),
		          'type'  => Controls_Manager::TEXTAREA,
		          'default' => esc_html__( 'Jonathan Morgan', 'widgetkit-for-elementor' ),
		    	]
	    );

	   	$repeater->add_control(
		    'designation_2',
		      	[
		          'label' => esc_html__( 'Designation', 'widgetkit-for-elementor' ),
		          'type'  => Controls_Manager::TEXTAREA,
		          'default' => esc_html__( 'Jonathan Morgan', 'widgetkit-for-elementor' ),
		    	]
	    );


		$repeater->add_control(
	       'testimoni_image_2',
		        [
		          'label' => esc_html__( 'Testimonial Image', 'widgetkit-for-elementor' ),
		          'type'  => Controls_Manager::MEDIA,
		           'default' => [
						'url' => Utils::get_placeholder_image_src(),
				  	],
		        ]
	    );

		$repeater->add_control(
		    'testimoni_content_2',
		      	[
		          'label' => esc_html__( 'Description', 'widgetkit-for-elementor' ),
		          'type'  => Controls_Manager::TEXTAREA,
		          'default' => esc_html__( 'Corem ipsum dolor si amet consectetur adipisic ingelit sed do adipisicido executiv
						sunse pit lore kome.', 'widgetkit-for-elementor' ),
		      	]
		);

		$this->add_control(
		    'testimonial_option_2',
		      [
		          'label'       => esc_html__( 'Testimonials Options', 'widgetkit-for-elementor' ),
		          'type'        => Controls_Manager::REPEATER,
		          'show_label'  => true,
		          'default'     => [
		              [
		                'title_2'       => esc_html__( 'Harsul Hisham', 'widgetkit-for-elementor' ),
		                'designation_2' => esc_html__( 'Engineer', 'widgetkit-for-elementor' ),
		                'testimoni_content_2' => 'Enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo conse quat. Duis aute irure dolor in reprehenderit in voluptate.',
		                'testimoni_image_2' => '',
		 
		              ],
		              [
		                'title_2'       => esc_html__( 'Teem Southy', 'widgetkit-for-elementor' ),
		                'designation_2' => esc_html__( 'Developer', 'widgetkit-for-elementor' ),
		                'testimoni_content_2' => 'Enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo conse quat. Duis aute irure dolor in reprehenderit in voluptate.',
		                'testimoni_image_2' => '',
		 
		              ]
		          ],
		          'fields'      => $repeater->get_controls(),
		          'title_field' => '{{{title_2}}}',
		      ]
		  );

	$this->end_controls_section();
	// Content options End


	$this->start_controls_section(
		'section_title',
		[
			'label' => esc_html__( 'Title/Designation', 'widgetkit-for-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]
	);

     	$this->add_control(
			'name_color',
			[
				'label'     => esc_html__( 'Name Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#182432',
				'selectors' => [
					'{{WRAPPER}} .tgx-testimonial-2 .testimoni-wrapper .name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name'     => 'title_typography',
					'label'    => esc_html__( 'Title Typography', 'widgetkit-for-elementor' ),
					'scheme'   => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .tgx-testimonial-2 .testimoni-wrapper .name',
				]
		);

		$this->add_responsive_control(
			'title_position_2',
				[
					'label'  => esc_html__( 'Title Specing', 'widgetkit-for-elementor' ),
					'type'   => Controls_Manager::SLIDER,
					'default'  => [
                        'size' => 10,
                    ],
					'range'  => [
						'px' => [
							'min' => -10,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}}  .tgx-testimonial-2 .testimoni-wrapper .name' => 'margin: {{SIZE}}{{UNIT}};',
					],
				]
			);


		$this->add_control(
            'degination_style',
            [
                'label' => esc_html__( 'Designation', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );


		$this->add_control(
			'designation_color_2',
			[
				'label'     => esc_html__( 'Designation Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#989898',
				'selectors' => [
					'{{WRAPPER}} .tgx-testimonial-2 .testimoni-wrapper .designation' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name'     => 'designation_typography',
					'label'    => esc_html__( 'Designation Typography', 'widgetkit-for-elementor' ),
					'scheme'   => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .tgx-testimonial-2 .testimoni-wrapper .designation',
				]
		);
	$this->end_controls_section();


	$this->start_controls_section(
		'section_content_2',
		[
			'label' => esc_html__( 'Content', 'widgetkit-for-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]
	);


        $this->add_control(
			'testimoni_position',
				[
					'label'     => esc_html__( 'Position', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'top',
					'options'   => [
						'top'		=> esc_html__('Top', 'widgetkit-for-elementor'),
						'bottom'    => esc_html__( 'Bottom', 'widgetkit-for-elementor' ),
					],
				]
		);

		$this->add_responsive_control(
			'content_specing_2',
				[
					'label'   => esc_html__( 'Content Specing', 'widgetkit-for-elementor' ),
					'type'    => Controls_Manager::SLIDER,
					'default' => [
                        'size' => 20,
                    ],
					'range'  => [
						'px' => [
							'min' => -10,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}}  .tgx-testimonial-2 .testimoni-wrapper .testimony' => 'margin: {{SIZE}}{{UNIT}} auto;',
					],
				]
			);

		$this->add_control(
			'testimoni_color_2',
			[
				'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#676767',
				'selectors' => [
					'{{WRAPPER}} .tgx-testimonial-2 .testimoni-wrapper .testimony' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name'     => 'testimoni_typography_2',
					'label'    => esc_html__( 'Testimoni Typography', 'widgetkit-for-elementor' ),
					'scheme'   => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .tgx-testimonial-2 .testimoni-wrapper .testimony',
				]
		);


	$this->end_controls_section();

	$this->start_controls_section(
		'section_image_2',
		[
			'label' => esc_html__( 'Image', 'widgetkit-for-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]
	);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'  => 'img_border_2',
				'label' => esc_html__( 'Border', 'widgetkit-for-elementor' ),
				'placeholder' => '1px',
				'default'  => '1px',
				'selector' => '{{WRAPPER}} .tgx-testimonial-2 .testimonial-image',
				'separator' => 'before',
			]
		);


		$this->add_control(
			'img_2_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tgx-testimonial-2 .testimonial-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		'section_nav_2',
		[
			'label' => esc_html__( 'Nav', 'widgetkit-for-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]
	);

		$this->add_control(
			'nav_enable_2',
			[
				'label'     => esc_html__( 'Nav Enable/Disable', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'yes'    => esc_html__( 'True', 'widgetkit-for-elementor' ),
				'no'     => esc_html__( 'False', 'widgetkit-for-elementor' ),
			]
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'  => 'nav_border_2',
				'label' => esc_html__( 'Border', 'widgetkit-for-elementor' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '
				{{WRAPPER}} .tgx-testimonial-2 .owl-carousel-left, 
				{{WRAPPER}} .tgx-testimonial-2 .owl-carousel-right',
				'separator' => 'before',
				'condition' => [
					'nav_enable_2' => 'yes',
				],
			]
		);

		$this->add_control(
			'nav_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tgx-testimonial-2 .owl-carousel-left, 
					 {{WRAPPER}} .tgx-testimonial-2 .owl-carousel-right' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'nav_enable_2' => 'yes',
				],
			]
		);


		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#555',
				'selectors' => [
					'{{WRAPPER}} .tgx-testimonial-2 .owl-carousel-left i, 
					 {{WRAPPER}} .tgx-testimonial-2 .owl-carousel-right i' => 'color:{{VALUE}};',
				],
				'condition' => [
					'nav_enable_2' => 'yes',
				],
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label'     => esc_html__( 'Hover Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-testimonial-2 .owl-carousel-left:hover i, 
					{{WRAPPER}} .tgx-testimonial-2 .owl-carousel-right:hover i' => 'color: {{VALUE}};',

				],
				'condition' => [
					'nav_enable_2' => 'yes',
				],
			]
		);

		$this->add_control(
			'icon_hover_bg_color',
			[
				'label'     => esc_html__( 'Hover Background Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ed485f',
				'selectors' => [
					'{{WRAPPER}} .tgx-testimonial-2 .owl-carousel-left:hover, 
					{{WRAPPER}} .tgx-testimonial-2 .owl-carousel-right:hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'nav_enable_2' => 'yes',
				],
			]
		);

		$this->add_control(
			'autoplay_enable_2',
			[
				'label'     => esc_html__( 'Auto Play Enable/Disable', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'yes'    => esc_html__( 'True', 'widgetkit-for-elementor' ),
				'no'     => esc_html__( 'False', 'widgetkit-for-elementor' ),
			]
		);

	$this->end_controls_section();
	}

	protected function render() {
		require WK_PATH . '/elements/testimonial-2/template/view.php';
	}


}
