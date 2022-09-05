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
 * Elementor Perfecto WidgetKit Portfolio
 *
 * Elementor widget for WidgetKit portfolio
 *
 * @since 1.0.0
 */
class wkfe_portfolio extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-portfolio';
	}

	public function get_title() {
		return esc_html__( 'Filterable Portfolio', 'widgetkit-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-apps wk-icon';
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
            'fontawesome',
            'widgetkit_main',
        ];
    }
	/**
	 * A list of scripts that the widgets is depended in
	 **/
	public function get_script_depends() {
		return [ 
			'hoverdir',
			'modernizr',
			'animate-text',
			'mixitup-js',
			'anime-js',
			'widgetkit-main',
		 ];
	}
	
	protected function _register_controls() {
			// Content options Start
	$this->start_controls_section(
		'section_content',
		[
			'label' => esc_html__( 'Portfolio Content', 'widgetkit-for-elementor' ),
		]
	);


	$repeater = new Repeater();
	    $repeater->add_control(
		    'portfolio_title',
		      	[
		          'label'   => esc_html__( 'Portfolio Title', 'widgetkit-for-elementor' ),
		          'type'    => Controls_Manager::TEXTAREA,
		          'default' => esc_html__( 'Switch Pro', 'widgetkit-for-elementor' ),
		    	]
	    );




		$repeater->add_control(
	       'portfolio_thumb_image',
		        [
		          'label' => esc_html__( 'Upload Thumb Image', 'widgetkit-for-elementor' ),
		          'type'  => Controls_Manager::MEDIA,
		           'default'  => [
						'url' => Utils::get_placeholder_image_src(),
					],
		        ]
	    );

	    $repeater->add_control(
	       'portfolio_full_image',
		        [
		          'label' => esc_html__( 'Upload Full Image', 'widgetkit-for-elementor' ),
		          'type'  => Controls_Manager::MEDIA,
		           'default'  => [
						'url' => Utils::get_placeholder_image_src(),
					],
		        ]
	    );

		$repeater->add_control(
		    'portfolio_desc',
		      	[
		          'label'   => esc_html__( 'Portfolio Description', 'widgetkit-for-elementor' ),
		          'type'    => Controls_Manager::TEXTAREA,
		          'default' => esc_html__( 'Multiple Business WordPress Theme', 'widgetkit-for-elementor' ),
		      	]
		);

		$repeater->add_control(
			'portfolio_filter',
			[
				'label'   => esc_html__( 'Filter', 'widgetkit-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Business', 'widgetkit-for-elementor' ),
			]
		);

		$repeater->add_control(
			'filter_tag',
			[
				'label'   => esc_html__( 'Filter Tag', 'widgetkit-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'business, onepager', 'widgetkit-for-elementor' ),
			]
		);


		$repeater->add_control(
			'portfolio_demo_link',
			[
				'label'   => esc_html__( 'Demo Link', 'widgetkit-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'https://www.themexpert.com/wordpress-themes/switch-pro', 'widgetkit-for-elementor' ),
			]
		);



		$this->add_control(
		    'portfolio_content',
		      [
		          'label'       => esc_html__( 'Portfolio Contents', 'widgetkit-for-elementor' ),
		          'type'        => Controls_Manager::REPEATER,
		          'show_label'  => true,
		          'default'     => [
		              [
		                'portfolio_title' => esc_html__( 'Switch Pro', 'widgetkit-for-elementor' ),
		                'portfolio_desc' => esc_html__( 'Multiple Business WordPress Theme', 'widgetkit-for-elementor'),
		                'portfolio_thumb_image' => '',
		                'portfolio_filter' => esc_html__('Business', 'widgetkit-for-elementor'),
		                'portfolio_full_image' => '',
		                'filter_tag' => esc_html__('business,onepage', 'widgetkit-for-elementor'),
		                'portfolio_demo_link' => 'https://themesgrove.com/product/switch-pro/',
		 
		              ],
		              [
		                'portfolio_title' => esc_html__( 'Exploore', 'widgetkit-for-elementor' ),
		                'portfolio_desc' => esc_html__( 'WordPress Bloging Theme', 'widgetkit-for-elementor'),
		                'portfolio_thumb_image' => '',
		                'portfolio_filter' => esc_html__('Blog', 'widgetkit-for-elementor'),
		                'portfolio_full_image' => '',
		                'filter_tag' => esc_html__('business,blog', 'widgetkit-for-elementor'),
		                'portfolio_demo_link' => 'https://themesgrove.com/product/exploore/',
		 
		              ],
		              [
		                'portfolio_title' => esc_html__( 'Universidad', 'widgetkit-for-elementor' ),
		                'portfolio_desc'  => esc_html__( 'Education WordPress Theme', 'widgetkit-for-elementor'),
		                'portfolio_thumb_image' => '',
		                'portfolio_filter' => esc_html__('Education', 'widgetkit-for-elementor'),
		                'portfolio_full_image' => '',
		                'filter_tag' => esc_html__('education,blog', 'widgetkit-for-elementor'),
		                'portfolio_demo_link' => 'https://themesgrove.com/product/universidad/',
		 
		              ]


		          ],
		          'fields'      => $repeater->get_controls(),
		          'title_field' => '{{{portfolio_title}}}',
		      ]
		  );

	$this->end_controls_section();
	// Content options End
	
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
			'item_layout',
			[
				'label' => esc_html__( 'Layout', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_control(
			'colmun_layout',
				[
					'label'     => esc_html__( 'Column Shows', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '4',
					'options'   => [
						'3'     => esc_html__( '4', 'widgetkit-for-elementor' ),
						'4'     => esc_html__( '3', 'widgetkit-for-elementor' ),
						'6'     => esc_html__( '2', 'widgetkit-for-elementor' ),
					],
				]
		);

		$this->add_responsive_control(
			'item_spacing',
				[
					'label'  => esc_html__( 'Item Padding', 'widgetkit-for-elementor' ),
					'type'   => Controls_Manager::SLIDER,
					'range'  => [
						'px' => [
							'min' => 0,
							'max' => 10,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .tgx-portfolio .portfolio-item' => 'padding: {{SIZE}}{{UNIT}};',
					],
				]
		);

		$this->add_control(
			'portfolio_hover_effect',
				[
					'label'     => esc_html__( 'Hover Effect', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'hover_1',
					'options'   => [
						'hover_1'     => esc_html__( 'Hover 1', 'widgetkit-for-elementor' ),
						'hover_2'     => esc_html__( 'Hover 2', 'widgetkit-for-elementor' ),
						'hover_3'     => esc_html__( 'Hover 3', 'widgetkit-for-elementor' ),
						'hover_4'     => esc_html__( 'Hover 4', 'widgetkit-for-elementor' ),
					],
				]
		);
		$this->add_control(
			'hover_4_action',
				[
					'label'     => esc_html__( 'On Click Action', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'image_popup',
					'options'   => [
						'image_popup'     => esc_html__( 'Image Popup', 'widgetkit-for-elementor' ),
						'demo_link'     => esc_html__( 'Demo Link', 'widgetkit-for-elementor' ),
					],
					'condition' => [
						'portfolio_hover_effect' => 'hover_4'
					]
				]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Filter', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_control(
			'filter_enable',
			[
				'label'     => esc_html__( 'Enable/Disable', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'enable'    => esc_html__( 'Enable', 'widgetkit-for-elementor' ),
				'disable'   => esc_html__( 'Disable', 'widgetkit-for-elementor' ),
			]
		);

		$this->add_control(
			'portfolio_filter_show_title',
			[
				'label'   => esc_html__( 'Show Title', 'widgetkit-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Show All', 'widgetkit-for-elementor' ),
				'condition' => [
					'filter_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'filter_hr',
			[
			'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);


		$this->add_control(
			'filter_color',
			[
				'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#404040',
				'selectors' => [
					'{{WRAPPER}} .portfolio-filter>li>a' => 'color: {{VALUE}};',
				],
				'condition' => [
	                'filter_enable' => 'yes',
	            ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name'     => 'filter_typography',
					'label'    => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
					'scheme'   => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .portfolio-filter>li>a, {{WRAPPER}} .portfolio-filter.slash > li .filter-slash',
					'condition'=> [
		                'filter_enable' => 'yes',
		            ],
				]
		);



		$this->add_control(
			'filter_layout_align',
			[
				'label' => esc_html__( 'Alignment', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::CHOOSE,
				'default'   => 'center',
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
				'condition' => [
					'filter_enable' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .tgx-portfolio .portfolio-filter' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'filter_spacing',
				[
					'label'  => esc_html__( 'Spacing', 'widgetkit-for-elementor' ),
					'type'   => Controls_Manager::SLIDER,
					'default'  => [
						'size' => 10,
					],
					'range'  => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .tgx-portfolio .portfolio-filter' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
		                'filter_enable' => 'yes',
		            ],
		            'separator' => 'after',
				]
		);

		$this->add_control(
			'filter_layout',
				[
					'label'     => esc_html__( 'Layout', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'round',
					'options'   => [
						'border'     => esc_html__( 'Border', 'widgetkit-for-elementor' ),
						'round'      => esc_html__( 'Round', 'widgetkit-for-elementor' ),
						'slash'      => esc_html__( 'Slash', 'widgetkit-for-elementor' ),
					],
					'condition' => [
	                    'filter_enable' => 'yes',
	                ],
				]

		);

		$this->add_control(
			'filter_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#056ddc',
				'selectors' => [
					'{{WRAPPER}} .portfolio-filter.border > li a.active:after, 
					 {{WRAPPER}}  .portfolio-filter.border > li a.active:before,
					 {{WRAPPER}}  .portfolio-filter.border > li a:hover:before,
					 {{WRAPPER}}  .portfolio-filter.border > li a:hover:after,
					 {{WRAPPER}} .portfolio-filter.border > li a.mixitup-control-active:before,
					 {{WRAPPER}} .portfolio-filter.border > li a.mixitup-control-active:after
					' => 'background-color: {{VALUE}};',
				],
				'condition' => [
	                    'filter_layout' => 'border',
	            ],
			]
		);


		// Filter round start
		$this->add_control(
            'filter_round_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .portfolio-filter.round > li a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'filter_layout' => 'round',
                ],
            ]
        );

        $this->add_control(
            'filter_round_padding',
            [
                'label' => esc_html__( 'Padding', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .portfolio-filter.round > li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'filter_layout' => 'round',
                ],
            ]
        );



		$this->add_control(
			'filter_round_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fafafa',
				'selectors' => [
					'{{WRAPPER}} .portfolio-filter.round>li>a' => '
						background-color: {{VALUE}};
						border: 1px solid {{VALUE}};',
				],

				'condition' => [
                    'filter_layout' => 'round',
                ],
			]
		);

		$this->add_control(
			'filter_round_active_color',
			[
				'label'     => esc_html__( 'Hover/Active Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .portfolio-filter.round > li a.mixitup-control-active,
					 {{WRAPPER}} .portfolio-filter.round > li a.active,
					 {{WRAPPER}} .portfolio-filter.point > li a.active,
					 {{WRAPPER}} .portfolio-filter.round > li a:hover' => 'color: {{VALUE}};',


				],

				'condition' => [
                    'filter_layout' => 'round',
                ],
			]
		);

		$this->add_control(
			'filter_round_active_bg_color',
			[
				'label'     => esc_html__( 'Active/Hove Background Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#51bbe5',
				'selectors' => [
					'{{WRAPPER}} .portfolio-filter.round > li a.mixitup-control-active,
					 {{WRAPPER}} .portfolio-filter.round > li a.active,
					 {{WRAPPER}} .portfolio-filter.point > li a.active,
					 {{WRAPPER}} .portfolio-filter.round > li a:hover' => 'background-color: {{VALUE}}; border: 1px solid {{VALUE}};',

				],
				'condition' => [
                    'filter_layout' => 'round',
                ],
			]
		);





		// filter slash start
		$this->add_control(
			'filter_slash_color',
			[
				'label'     => esc_html__( 'Slash Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#404040',
				'selectors' => [
					'{{WRAPPER}} .portfolio-filter.slash > li .filter-slash
					' => 'color: {{VALUE}};',
				],
				'condition' => [
	                    'filter_layout' => 'slash',
	            ],
			]
		);

		$this->add_control(
			'filter_slash_hover_color',
			[
				'label'     => esc_html__( 'Active/hover Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#056ddc',
				'selectors' => [
					'{{WRAPPER}} .portfolio-filter.slash > li a.mixitup-control-active, 
					 {{WRAPPER}} .portfolio-filter.slash > li a.active, 
					 {{WRAPPER}} .portfolio-filter.slash > li a:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
	                    'filter_layout' => 'slash',
	            ],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_overlay_style',
			[
				'label' => esc_html__( 'Overlay', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'overlay_color',
			[
				'label'     => esc_html__( 'Item Overlay', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#056ddce8',
				'selectors' => [
					'{{WRAPPER}} .tgx-portfolio .hover-1 .portfolio-item div, 
					{{WRAPPER}}  .tgx-portfolio .hover-2 .overlay .overlay-spin:before,
					{{WRAPPER}}  .tgx-portfolio .hover-3 .effect-3 .info,
					{{WRAPPER}}  .tgx-portfolio .hover-4 .overlay-spin:before
						'  => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
            'portfolio_overlay_title',
            [
                'label' => esc_html__( 'Title', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
			'overlay_title_color',
			[
				'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-portfolio .portfolio-item .title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tgx-portfolio .hover-4 .overlay .portfolio-content .title:before' => 'background: {{VALUE}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name'     => 'overlay_title_typography',
					'label'    => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
					'scheme'   => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .tgx-portfolio .portfolio-item .title',
				]
		);

		$this->add_responsive_control(
			'title_bottom_line_spacing',
				[
					'label'  => esc_html__( 'Line Specing', 'widgetkit-for-elementor' ),
					'type'   => Controls_Manager::SLIDER,
					'default'  => [
						'size' => 50,
					],
					'range'  => [
						'px' => [
							'min' => 50,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .tgx-portfolio .hover-4 .overlay:hover .portfolio-content .title:before' => 'top: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
		                'portfolio_hover_effect' => 'hover_4',
		            ],
				]
		);


		$this->add_control(
            'portfolio_desc_heading',
            [
                'label' => esc_html__( 'Desc', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

		$this->add_control(
			'overlay_desc_color',
			[
				'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-portfolio .portfolio-item .desc ' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name'     => 'overlay_desc_typography',
					'label'    => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
					'scheme'   => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .tgx-portfolio .portfolio-item .desc',
				]
		);


		$this->add_control(
            'portfolio_icon_heading',
            [
                'label' => esc_html__( 'Icon', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
		$this->add_control(
            'overlay_icon_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .tgx-portfolio #hover-1 .portfolio-item .portfolio-buttons a,
                    {{WRAPPER}} .tgx-portfolio .hover-2 .portfolio-btn a,
                    {{WRAPPER}} .tgx-portfolio .hover-3 .effect-3 .external-link li a
                    ' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->add_control(
			'overlay_icon_color',
			[
				'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#056ddc',
				'selectors' => [
					'{{WRAPPER}} .tgx-portfolio #hover-1 .portfolio-item .portfolio-buttons a,
					 {{WRAPPER}} .tgx-portfolio .hover-2 .portfolio-btn a,
					 {{WRAPPER}} .tgx-portfolio .hover-3 .effect-3 .external-link li a' => 'color: {{VALUE}};',
					],
			]
		);

		$this->add_control(
			'overlay_icon_bg__color',
			[
				'label'     => esc_html__( 'Bg Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-portfolio #hover-1 .portfolio-item .portfolio-buttons a,
					{{WRAPPER}} .tgx-portfolio .hover-2 .portfolio-btn a,
					 {{WRAPPER}} .tgx-portfolio .hover-3 .effect-3 .external-link li a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .tgx-portfolio #hover-1 .portfolio-item .portfolio-buttons a:hover,
					{{WRAPPER}} .tgx-portfolio .hover-2 .portfolio-btn a:hover,
					 {{WRAPPER}} .tgx-portfolio .hover-3 .effect-3 .external-link li a:hover' => 'color: {{VALUE}};',
					],
			]
		);

		$this->add_control(
			'overlay_icon_hover_bg__color',
			[
				'label'     => esc_html__( 'Hover Bg Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#056ddc',
				'selectors' => [
					'{{WRAPPER}} .tgx-portfolio #hover-1 .portfolio-item .portfolio-buttons a:hover,

					 {{WRAPPER}} .tgx-portfolio .hover-3 .effect-3 .external-link li a:hover,
					 {{WRAPPER}} .tgx-portfolio .hover-2 .portfolio-btn a:hover' => 'background-color: {{VALUE}};',
					 
					],
			]
		);


		$this->end_controls_section();
	}

	protected function render() {
		require WK_PATH . '/elements/portfolio/template/view.php';
	}


}
