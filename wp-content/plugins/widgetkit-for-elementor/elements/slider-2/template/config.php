<?php


use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
Use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor WidgetKit Slider 2
 *
 * Elementor widget for WidgetKit slider 2
 *
 * @since 1.0.0
 */
class wkfe_slider_2 extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-slider-2';
	} 

	public function get_title() {
		return esc_html__( 'Slider Content', 'widgetkit-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-post-slider wk-icon';
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
            'owl-css',
            'animate-css',
            'fontawesome',
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
			'label' => esc_html__( 'Content', 'widgetkit-for-elementor' ),
		]
	);


	$repeater = new Repeater();
	    $repeater->add_control(
		    'title',
		      	[
		          'label' => esc_html__( 'Title', 'widgetkit-for-elementor' ),
		          'type'  => Controls_Manager::TEXTAREA,
		          'default' => esc_html__( 'Jonathan Morgan', 'widgetkit-for-elementor' ),
		    	]
	    );

	    $repeater->add_control(
			'title_animation',
				[
					'label'     => esc_html__( 'Title Animation', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'fadeInDown',
					'options'   => [
						'none'			   => esc_html__('None'),
						'fadeInLeft'       => esc_html__( 'fadeInLeft', 'widgetkit-for-elementor' ),
						'slideInRight'     => esc_html__( 'slideInRight', 'widgetkit-for-elementor' ),
						'bounceIn'         => esc_html__( 'bounceIn', 'widgetkit-for-elementor' ),
						'fadeInUp'         => esc_html__( 'fadeInUp', 'widgetkit-for-elementor' ),
						'fadeInRightBig'   => esc_html__( 'fadeInRightBig', 'widgetkit-for-elementor' ),
						'fadeInDown'       => esc_html__( 'fadeInDown', 'widgetkit-for-elementor' ),
						'rotateIn'         => esc_html__( 'rotateIn', 'widgetkit-for-elementor' ),
						'lightSpeedIn'     => esc_html__( 'lightSpeedIn', 'widgetkit-for-elementor' ),
						'zoomIn'           => esc_html__( 'zoomIn', 'widgetkit-for-elementor' ),
						'zoomInDown'       => esc_html__( 'zoomInDown', 'widgetkit-for-elementor' ),
					],
				]
		);



		$repeater->add_control(
	       'slider_image_2',
		        [
		          'label' => esc_html__( 'Upload Slider Image', 'widgetkit-for-elementor' ),
		          'type'  => Controls_Manager::MEDIA,
		           'default'  => [
						'url' => Utils::get_placeholder_image_src(),
					],
		        ]
	    );

		$repeater->add_control(
		    'slider_content',
		      	[
		          'label' => esc_html__( 'Description', 'widgetkit-for-elementor' ),
		          'type'  => Controls_Manager::TEXTAREA,
		          'default' => esc_html__( 'Corem ipsum dolor si amet consectetur adipisic ingelit sed do adipisicido executiv
						sunse pit lore kome.', 'widgetkit-for-elementor' ),
		      	]
		);


	    $repeater->add_control(
			'content_animation',
				[
					'label'     => esc_html__( 'Content Animation', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'fadeInDown',
					'options'   => [
						'none'			   => esc_html__('None'),
						'fadeInLeft'       => esc_html__( 'fadeInLeft', 'widgetkit-for-elementor' ),
						'slideInRight'     => esc_html__( 'slideInRight', 'widgetkit-for-elementor' ),
						'bounceIn'         => esc_html__( 'bounceIn', 'widgetkit-for-elementor' ),
						'fadeInUp'         => esc_html__( 'fadeInUp', 'widgetkit-for-elementor' ),
						'fadeInRightBig'   => esc_html__( 'fadeInRightBig', 'widgetkit-for-elementor' ),
						'fadeInDown'       => esc_html__( 'fadeInDown', 'widgetkit-for-elementor' ),
						'rotateIn'         => esc_html__( 'rotateIn', 'widgetkit-for-elementor' ),
						'lightSpeedIn'     => esc_html__( 'lightSpeedIn', 'widgetkit-for-elementor' ),
						'zoomIn'           => esc_html__( 'zoomIn', 'widgetkit-for-elementor' ),
						'zoomInDown'       => esc_html__( 'zoomInDown', 'widgetkit-for-elementor' ),
					],
				]
		);

		$repeater->add_control(
			'btn_text',
			[
				'label' => esc_html__( 'Button Text', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Get Started', 'widgetkit-for-elementor' ),
				'placeholder' => esc_html__( 'Get Started', 'widgetkit-for-elementor' ),
			]
		);

		$repeater->add_control(
			'btn_link',
			[
				'label' => esc_html__( 'Button Link', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::URL,
				'placeholder' => 'http://your-link.com',
				'default' => [
					'url' => '#',
				],
			]
		);

	    $repeater->add_control(
			'btn_animation',
				[
					'label'     => esc_html__( 'Button Animation', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'fadeInDown',
					'options'   => [
						'none'			   => esc_html__( 'None'),
						'fadeInLeft'       => esc_html__( 'fadeInLeft', 'widgetkit-for-elementor' ),
						'slideInRight'     => esc_html__( 'slideInRight', 'widgetkit-for-elementor' ),
						'bounceIn'         => esc_html__( 'bounceIn', 'widgetkit-for-elementor' ),
						'fadeInUp'         => esc_html__( 'fadeInUp', 'widgetkit-for-elementor' ),
						'fadeInRightBig'   => esc_html__( 'fadeInRightBig', 'widgetkit-for-elementor' ),
						'fadeInDown'       => esc_html__( 'fadeInDown', 'widgetkit-for-elementor' ),
						'rotateIn'         => esc_html__( 'rotateIn', 'widgetkit-for-elementor' ),
						'lightSpeedIn'     => esc_html__( 'lightSpeedIn', 'widgetkit-for-elementor' ),
						'zoomIn'           => esc_html__( 'zoomIn', 'widgetkit-for-elementor' ),
						'zoomInDown'       => esc_html__( 'zoomInDown', 'widgetkit-for-elementor' ),
					],
				]
		);

		$repeater->add_control(
	       'slider_animation_image',
		        [
		          'label' => esc_html__( 'Upload Slider Animation Image', 'widgetkit-for-elementor' ),
		          'type'  => Controls_Manager::MEDIA,
		           'default'  => [
						'url' => Utils::get_placeholder_image_src(),
					],
		        ]
	    );


	    $repeater->add_control(
			'image_animation',
				[
					'label'     => esc_html__( 'Image Animation', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'fadeInUp',
					'options'   => [
						'none'			   => esc_html__( 'None'),
						'fadeInLeft'       => esc_html__( 'fadeInLeft', 'widgetkit-for-elementor' ),
						'slideInRight'     => esc_html__( 'slideInRight', 'widgetkit-for-elementor' ),
						'bounceIn'         => esc_html__( 'bounceIn', 'widgetkit-for-elementor' ),
						'fadeInUp'         => esc_html__( 'fadeInUp', 'widgetkit-for-elementor' ),
						'fadeInRightBig'   => esc_html__( 'fadeInRightBig', 'widgetkit-for-elementor' ),
						'fadeInDown'       => esc_html__( 'fadeInDown', 'widgetkit-for-elementor' ),
						'rotateIn'         => esc_html__( 'rotateIn', 'widgetkit-for-elementor' ),
						'lightSpeedIn'     => esc_html__( 'lightSpeedIn', 'widgetkit-for-elementor' ),
						'zoomIn'           => esc_html__( 'zoomIn', 'widgetkit-for-elementor' ),
						'zoomInDown'       => esc_html__( 'zoomInDown', 'widgetkit-for-elementor' ),
					],
				]
		);

		$this->add_control(
		    'slider_option',
		      [
		          'label'       => esc_html__( 'Slider Options', 'widgetkit-for-elementor' ),
		          'type'        => Controls_Manager::REPEATER,
		          'show_label'  => true,
		          'default'     => [
		              [
		                'title' => esc_html__( 'We Manage Your Business', 'widgetkit-for-elementor' ),
		                'slider_content' => 'Enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo conse quat. Duis aute irure dolor in reprehenderit in voluptate.',
		                'slider_image' => '',
		                'btn-text' => 'Get Started',
		                'btn-link' => 'https://www.themesgrove.com',
		                'title_animation' => 'fadeInDown',
		                'content_animation' => 'fadeInDown',
		                'btn_animation' => 'fadeInDown',
		                'image_animation' => 'fadeInDown',
		                'slider_animation_image' => '',
		 
		              ],
		               [
		                'title' => esc_html__( 'We are professional', 'widgetkit-for-elementor' ),
		                'slider_content' => 'Enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo conse quat. Duis aute irure dolor in reprehenderit in voluptate.',
		                'slider_image' => '',
		                'btn-text' => 'Get Started',
		                'btn-link' => 'https://www.themesgrove.com',
		                'title_animation' => 'fadeInDown',
		                'content_animation' => 'fadeInDown',
		                'btn_animation' => 'fadeInDown',
		                'image_animation' => 'fadeInDown',
		                'slider_animation_image' => '',
		 
		              ]
		          ],
		          'fields'      => $repeater->get_controls(),
		          'title_field' => '{{{title}}}',
		      ]
		  );

		$this->add_control(
			'heading_tag',
				[
					'label'     => esc_html__( 'Heading Tag', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'h2',
					'options'   => [
						'h1'			   => esc_html__('H1'),
						'h2'			   => esc_html__('H2'),
						'h3'			   => esc_html__('H3'),
						'h4'			   => esc_html__('H4'),
						'h5'			   => esc_html__('H5'),
						'h6'			   => esc_html__('H6'),
						'p'			       => esc_html__('P'),
						'span'			   => esc_html__('Span'),
					],
				]
		);

	$this->end_controls_section();
	// Content options End




	// Content options Start
	$this->start_controls_section(
		'section_layout',
		[
			'label' => esc_html__( 'Content Layout', 'widgetkit-for-elementor' ),
		]
	);


	    $this->add_control(
			'layout_align',
			[
				'label'     => esc_html__( 'Layout Align', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'left',
				'options'   => [
					'left'    => esc_html__( 'Left', 'widgetkit-for-elementor' ),
					'right'   => esc_html__( 'Right', 'widgetkit-for-elementor' ),
				],
			]
		);


		$this->add_responsive_control(
			'image_left_position',
				[
					'label'  => esc_html__( 'Left Position', 'widgetkit-for-elementor' ),
					'type'   => Controls_Manager::SLIDER,
					'default'  => [
                        'size' => -50,
                    ],
					'range'  => [
						'px' => [
							'min' => -20,
							'max' => 500,
						],
					],
					'selectors' => [
						'{{WRAPPER}}  .tgx-slider-2 .carousel .carousel-inner .item .carousel-image' => 'left: {{SIZE}}{{UNIT}};',
					],

					'condition' => [
						'layout_align' => 'left',
					],

				]
			);

		$this->add_responsive_control(
			'image_right_position',
				[
					'label'  => esc_html__( 'Right Position', 'widgetkit-for-elementor' ),
					'type'   => Controls_Manager::SLIDER,
					'default' => [
                        'size' => 120,
                    ],
					'range'  => [
						'px' => [
							'min' => 10,
							'max' => 500,
						],
					],
					'selectors' => [
						'{{WRAPPER}}  .tgx-slider-2 .carousel .carousel-inner .item .carousel-image' => 'left: {{SIZE}}{{UNIT}};',
					],

					'condition' => [
						'layout_align' => 'right',
					],

				]
			);

		$this->add_responsive_control(
			'image_position',
				[
					'label'  => esc_html__( 'Top Position', 'widgetkit-for-elementor' ),
					'type'   => Controls_Manager::SLIDER,
					'default'  => [
                        'size' => 220,
                    ],
					'range'  => [
						'px' => [
							'min' => -20,
							'max' => 1500,
						],
					],
					'selectors' => [
						'{{WRAPPER}}  .tgx-slider-2 .carousel .carousel-inner .item .carousel-image' => 'top: {{SIZE}}{{UNIT}};',
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

	


	// Color options Start
	$this->start_controls_section(
		'section_option',
			[
				'label' => esc_html__( 'Content', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
	);


	$this->start_controls_tabs( 'caption_control' );

	$this->start_controls_tab(
	        'typo_option',
	          [
	            'label' => esc_html__( 'Ttitle Option', 'widgetkit-for-elementor' ),
	          ]
        );


        $this->add_control(
			'title_color',
				[
					'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#fff',
					'selectors' => [
						'{{WRAPPER}} .tgx-slider-2 .carousel-inner .carousel-caption .slider-title' => 'color: {{VALUE}};',
					],
				]
		);

		$this->add_control(
			'title_highlight',
				[
					'label'     => esc_html__( 'Highlight Color', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#ed485f',
					'selectors' => [
						'{{WRAPPER}} .tgx-slider-2 .carousel .carousel-inner .carousel-caption b,
						 {{WRAPPER}} .tgx-slider-2 .carousel .carousel-inner .carousel-caption span,
						 {{WRAPPER}} .tgx-slider-2 .carousel .carousel-inner .carousel-caption strong' => 'color: {{VALUE}};',
					],
				]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name'     => 'title_typography',
					'label'    => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
					'scheme'   => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .tgx-slider-2 .carousel-inner .item .carousel-caption .slider-title',
				]
		);

        $this->add_responsive_control(
			'caption_position',
				[
					'label'  => esc_html__( 'Caption Position', 'widgetkit-for-elementor' ),
					'type'   => Controls_Manager::SLIDER,
					'default'  => [
                        'size' => 150,
                    ],
					'range'  => [
						'px' => [
							'min' => 100,
							'max' => 500,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .tgx-slider-2 .carousel-inner .item .carousel-caption' => 'top: {{SIZE}}{{UNIT}};',
					],
				]
			);

        $this->add_responsive_control(
			'caption_width',
				[
					'label'  => esc_html__( 'Caption Width', 'widgetkit-for-elementor' ),
					'type'   => Controls_Manager::SLIDER,
					'default'  => [
                        'size' => 700,
                    ],
					'range'  => [
						'px' => [
							'min' =>150,
							'max' => 1100,
						],
					],
					'selectors' => [
						'{{WRAPPER}}  .tgx-slider-2 .carousel-inner .item .carousel-caption' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);

		$this->add_responsive_control(
	         'caption_align',
		         [
		           'label' => esc_html__( 'Alignment', 'widgetkit-for-elementor' ),
		           'type'  => Controls_Manager::CHOOSE,
		           'default'   => 'left',
		           'options' => [
		             'left'  => [
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
		             'justify' => [
		               'title' => esc_html__( 'Justified', 'widgetkit-for-elementor' ),
		               'icon'  => 'fa fa-align-justify',
		             ],
		           ],
		           'selectors' => [
		             '{{WRAPPER}} .tgx-slider-2 .carousel-inner .item .carousel-caption' => 'text-align: {{VALUE}};',
		           ],
	         ]
       );


    $this->end_controls_tab();

		$this->start_controls_tab(
	        'color_options',
		          [
		            'label' => esc_html__( 'Content Option', 'widgetkit-for-elementor' ),
		          ]
	    );



		$this->add_control(
			'content_color',
				[
					'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#fff',
					'selectors' => [
						'{{WRAPPER}} .tgx-slider-2 .carousel-inner .carousel-caption .slider-description' => 'color: {{VALUE}};',
					],
				]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name'     => 'content_typography',
					'label'    => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
					'scheme'   => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .tgx-slider-2 .carousel-inner .carousel-caption .slider-description',
				]
		);

		$this->add_control(
			'item_overlay_color',
			[
				'label'     => esc_html__( 'Item Overlay Bg Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(7,16,38,0.83)',
				'selectors' => [
					'{{WRAPPER}} .tgx-slider-2 .carousel-inner .item:before' => 'background-color: {{VALUE}};',
				],
			]
		);

        $this->add_responsive_control(
			'desc_width',
				[
					'label'  => esc_html__( 'Description Width', 'widgetkit-for-elementor' ),
					'type'   => Controls_Manager::SLIDER,
					'default'  => [
                        'size' => 500,
                    ],
					'range'  => [
						'px' => [
							'min' =>250,
							'max' => 1200,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .tgx-slider-2 .carousel-inner .item .carousel-caption .slider-description' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);

        $this->add_control(
			'content_margin',
				[
					'label'     => esc_html__( 'Margin', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '0',
					'options'   => [
						'none'	  => esc_html__( 'None'),
						'0'       => esc_html__( '0', 'widgetkit-for-elementor' ),
						'auto'	  => esc_html__( 'Auto', 'widgetkit-for-elementor' ),

					],
					'selectors' => [
						'{{WRAPPER}} .tgx-slider-2 .carousel-inner .carousel-caption .slider-description' => 'margin: {{VALUE}};',
					],
				]
		);

		$this->add_responsive_control(
			'content_spacing',
				[
					'label'  => esc_html__( 'Spacing', 'widgetkit-for-elementor' ),
					'type'   => Controls_Manager::SLIDER,
					'default'  => [
                        'size' => 20,
                    ],
					'range'  => [
						'px' => [
							'min' => -20,
							'max' => 50,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .tgx-slider-2 .carousel-inner .carousel-caption .slider-description' => 'padding-top: {{SIZE}}{{UNIT}};',
					],
				]
			);


        $this->end_controls_tab();

        

    $this->end_controls_tabs();

$this->end_controls_section();
// caption options End


		// Button options Start
		$this->start_controls_section(
			'button_style',
				[
					'label' => esc_html__( 'Button', 'widgetkit-for-elementor' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name'     => 'btn_text_size',
					'label'    => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
					'scheme'   => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .tgx-slider-2 .carousel-inner .carousel-caption .btn-slider',
				]
		);



	$this->start_controls_tabs( 'tabs_button_style' );

    $this->start_controls_tab(
        'tab_button_normal',
          [
            'label' => esc_html__( 'Normal', 'widgetkit-for-elementor' ),
          ]
    );

    $this->add_control(
        'button_text_color',
          [
            'label' => esc_html__( 'Color', 'widgetkit-for-elementor' ),
            'type'  => Controls_Manager::COLOR,
            'default'   => '#fff',
            'selectors' => [
              '{{WRAPPER}} .tgx-slider-2 .carousel-inner .carousel-caption .btn-slider' => 'color: {{VALUE}};',
            ],
          ]
    );

    $this->add_control(
        'background_color',
	        [
	            'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
	            'type'  => Controls_Manager::COLOR,
	            'default' => 'transparent',
	            'selectors' => [
	              '{{WRAPPER}} .tgx-slider-2 .carousel-inner .carousel-caption .btn-slider' => 'background-color: {{VALUE}};',
	            ],
	        ]
    );

     $this->add_control(
        'button_border_color',
	        [
	            'label' => esc_html__( 'Border Color', 'widgetkit-for-elementor' ),
	            'type'  => Controls_Manager::COLOR,
	            'default' => ' #ed485f',
	           
	            'selectors' => [
	              '{{WRAPPER}} .tgx-slider-2 .carousel-inner .carousel-caption .btn-slider' => 'border: 1px solid {{VALUE}};',
	            ],
	        ]
        );

    $this->end_controls_tab();

    $this->start_controls_tab(
        'tab_button_hover',
	        [
	            'label' => esc_html__( 'Hover', 'widgetkit-for-elementor' ),
	        ]
    );

    $this->add_control(
        'hover_color',
		    [
		        'label' => esc_html__( 'Color', 'widgetkit-for-elementor' ),
		        'type'  => Controls_Manager::COLOR,
		        'default'   => ' #fff',
		        'selectors' => [
		          '{{WRAPPER}} .tgx-slider-2 .carousel-inner .carousel-caption .btn-slider:hover' => 'color: {{VALUE}};',
		        ],
		    ]
    );

    $this->add_control(
        'button_background_hover_color',
		    [
		        'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
		        'type'  => Controls_Manager::COLOR,
		        'default'   => ' #ed485f',
		        'selectors' => [
		          '{{WRAPPER}} .tgx-slider-2 .carousel-inner .carousel-caption .btn-slider:hover' => 'background-color: {{VALUE}};',
		        ],
		    ]
    );

    $this->add_control(
        'button_hover_border_color',
	        [
	            'label' => esc_html__( 'Border Color', 'widgetkit-for-elementor' ),
	            'type'  => Controls_Manager::COLOR,
	            'default'   => ' #ed485f',
	            'selectors' => [
	              '{{WRAPPER}} .tgx-slider-2 .carousel-inner .carousel-caption .btn-slider:hover' => 'border-color: {{VALUE}};',
	            ],
	        ]
    );

    $this->end_controls_tab();


    $this->end_controls_tabs();

        $this->add_control(
            'border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .tgx-slider-2 .carousel-inner .carousel-caption .btn-slider' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'text_padding',
            [
                'label' => esc_html__( 'Padding', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .tgx-slider-2 .carousel-inner .carousel-caption .btn-slider' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                
            ]
        );

		$this->add_responsive_control(
			'button_spacing',
				[
					'label'  => esc_html__( 'Spacing', 'widgetkit-for-elementor' ),
					'type'   => Controls_Manager::SLIDER,
					'default' => [
						'size' => 30,
					],
					'range'  => [
						'px' => [
							'min' => 10,
							'max' => 50,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .tgx-slider-2 .carousel-inner .carousel-caption .btn-slider' => 'margin: {{SIZE}}{{UNIT}} 0;',
					],
				]
			);


$this->end_controls_section();
// Button options End




// Control options Start
$this->start_controls_section(
	'control_style',
		[
			'label' => esc_html__( 'Control', 'widgetkit-for-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]
	);


	$this->start_controls_tabs( 'slider_control' );

		$this->start_controls_tab(
	          'Background Options',
	          [
	            'label' => esc_html__( 'Background', 'widgetkit-for-elementor' ),
	          ]
	    );

	    	$this->add_responsive_control(
				'slider_2_height',
					[
						'label'  => esc_html__( 'Slider Height', 'widgetkit-for-elementor' ),
						'type'   => Controls_Manager::SLIDER,
						'range'  => [
							'px' => [
								'min' => 10,
								'max' => 1000,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .tgx-slider-2 .carousel .carousel-inner .item' => 'height: {{SIZE}}{{UNIT}} !important;',
						],
					]
			);

		    $this->add_control(
				'slider_interval',
				[
					'label' => esc_html__( 'Slider Interval', 'widgetkit-for-elementor' ),
					'type'  => Controls_Manager::TEXT,
					'default' => '10000',
				]
			);
			$this->add_control(
				'slider_data_pause',
					[
						'label'     => esc_html__( 'Data Pause', 'widgetkit-for-elementor' ),
						'type'      => Controls_Manager::SWITCHER,
						'default'   => 'yes',
						'true'    => esc_html__( 'True', 'widgetkit-for-elementor' ),
						'false'   => esc_html__( 'False', 'widgetkit-for-elementor' ),
					]
			);


			$this->add_control(
				'bg_repeat',
				[
					'label'     => esc_html__( 'Background Repeat', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'no-repeat',
					'options'   => [
						'no-repeat'  => esc_html__( 'No Repeat', 'widgetkit-for-elementor' ),
						'repeat'     => esc_html__( 'Repeat', 'widgetkit-for-elementor' ),
						'repeat-x'   => esc_html__( 'Repeat X', 'widgetkit-for-elementor' ),
						'repeat-y'   => esc_html__( 'Repeat Y', 'widgetkit-for-elementor' ),
						'inherit'    => esc_html__( 'Inherit', 'widgetkit-for-elementor' ),
					],
					'selectors' => [
						'{{WRAPPER}} .tgx-slider-2 .carousel-inner .item' => 'background-repeat: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'bg_size',
				[
					'label'     => esc_html__( 'Background Size', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'cover',
					'options'   => [
						'cover'     => esc_html__( 'Cover', 'widgetkit-for-elementor' ),
						'contain'   => esc_html__( 'Contain', 'widgetkit-for-elementor' ),
						'inherit'   => esc_html__( 'Inherit', 'widgetkit-for-elementor' ),
					],
					'selectors' => [
						'{{WRAPPER}} .tgx-slider-2 .carousel-inner .item' => 'background-size: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'bg_attachment',
				[
					'label'     => esc_html__( 'Background Attachment', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'fixed',
					'options'   => [
						'scroll'  => esc_html__( 'Scroll', 'widgetkit-for-elementor' ),
						'fixed'    => esc_html__( 'Fixed', 'widgetkit-for-elementor' ),
						'inherit'  => esc_html__( 'Inherit', 'widgetkit-for-elementor' ),
					],
					'selectors' => [
						'{{WRAPPER}} .tgx-slider-2 .carousel-inner .item' => 'background-attachment: {{VALUE}};',
					],
				]
			);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'slider_nav',
	          [
	            'label' => esc_html__( 'Nav Options', 'widgetkit-for-elementor' ),
	          ]
	    );

		$this->add_control(
			'dot_enable',
				[
					'label'     => esc_html__( 'Dot Enable/Disable', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'enable'    => esc_html__( 'Enable', 'widgetkit-for-elementor' ),
					'disable'   => esc_html__( 'Disable', 'widgetkit-for-elementor' ),
				]
		);


		$this->add_control(
            'dot_border_radius',
            [
                'label' => esc_html__( 'Dot Border Radius', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .tgx-slider-2 .carousel-indicators li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

                'condition' => [
					'dot_enable' => 'yes',
				],

            ]
        );

		$this->add_control(
			'arrow_enable',
				[
					'label'     => esc_html__( 'Arrow Enable/Disable', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'enable'    => esc_html__( 'Enable', 'widgetkit-for-elementor' ),
					'disable'   => esc_html__( 'Disable', 'widgetkit-for-elementor' ),

				]
		);

		$this->add_control(
            'arrow_border_radius',
            [
                'label' => esc_html__( 'Arrow Border Radius', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .tgx-slider-2 .carousel .carousel-control' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
					'arrow_enable' => 'yes',
				],

            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'  => 'border',
				'label' => esc_html__( 'Border', 'widgetkit-for-elementor' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .tgx-slider-2  .carousel-indicators li, 
				{{WRAPPER}} .tgx-slider-2 .carousel .carousel-control',
				'separator'   => 'before',
			]
		);


		$this->add_control(
			'nav_bg_color',
				[
					'label'     => esc_html__( 'Dot & Arrow Hover Bg Color', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#ed485f',
					'selectors' => [
						'{{WRAPPER}} .tgx-slider-2 .carousel .carousel-indicators li.active' => 'border-color:{{VALUE}};',
						'{{WRAPPER}} .tgx-slider-2  .carousel-control:hover' => 'border-color: {{VALUE}} !important;',
						'{{WRAPPER}} .tgx-slider-2 .carousel .carousel-control:hover' => 'background-color:{{VALUE}};',
					],
				]
		);

    $this->end_controls_tab();

    $this->end_controls_tabs();

$this->end_controls_section();
// Control options End

	}

	protected function render() {
		require WK_PATH . '/elements/slider-2/template/view.php';
	}


}
