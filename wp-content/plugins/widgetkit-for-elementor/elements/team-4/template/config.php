<?php


use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
Use Elementor\Core\Schemes\Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor WidgetKit Team 4
 *
 * Elementor widget for WidgetKit team 4
 *
 * @since 1.0.0
 */
class wkfe_team_4 extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-team-4';
	} 

	public function get_title() {
		return esc_html__( 'Team Animation', 'widgetkit-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-person wk-icon';
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
			'section_content_team_4',
			[
				'label' => esc_html__( 'Team Content', 'widgetkit-for-elementor' ),
			]
		);

		$this->add_control(
	       'team_4_team_image',
		        [
		          'label' => esc_html__( 'Upload Team Image', 'widgetkit-for-elementor' ),
		          'type'  => Controls_Manager::MEDIA,
		          'default' => [
						'url' => Utils::get_placeholder_image_src(),
				  	],
		        ]
	    );


	    $this->add_control(
		    'team_4_team_name',
		      	[
		          'label' => esc_html__( 'Team Name', 'widgetkit-for-elementor' ),
		          'type'  => Controls_Manager::TEXTAREA,
		          'default' => esc_html__( 'Edward Norton', 'widgetkit-for-elementor' ),
		    	]
	    );

	    $this->add_control(
            'image_external_link',
            [
                'label' => esc_html__( 'Link To', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::URL,
                'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'https://themesgrove.com', 'widgetkit-for-elementor' ),
				'default' => [
					'url' => 'https://themesgrove.com', 
				],
            ]
        );



	   	$this->add_control(
		    'team_4_designation',
		      	[
		          'label' => esc_html__( 'Team Designation', 'widgetkit-for-elementor' ),
		          'type'  => Controls_Manager::TEXTAREA,
		          'default' => esc_html__( 'CEO, Ag Express', 'widgetkit-for-elementor' ),
		      	]
		);

		$this->add_control(
		    'team_4_content',
		      	[
		          'label' => esc_html__( 'Team Description', 'widgetkit-for-elementor' ),
		          'type'  => Controls_Manager::TEXTAREA,
		          'default' => esc_html__( 'He has appeared in more than 100 films and television shows.', 'widgetkit-for-elementor' ),
		      	]
		);

			$repeater = new Repeater();

			    $repeater->add_control(
			      'team_4_title',
			      [
			          'label' => esc_html__( 'Social Name', 'widgetkit-for-elementor' ),
			          'type'  => Controls_Manager::TEXT,
			          'default' => esc_html__( 'Facebook', 'widgetkit-for-elementor' ),
			      ]
			    );


			$repeater->add_control(
		            'team_4_social_link',
		            [
		                'label' => esc_html__( 'Social Link', 'widgetkit-for-elementor' ),
		                'type'  => Controls_Manager::URL,
		                'dynamic' => [
							'active' => true,
						],
						'placeholder' => __( 'https://www.facebook.com/themesgrove', 'widgetkit-for-elementor' ),
						'default' => [
							'url' => 'https://www.facebook.com/themesgrove',
						],
		            ]
		        );

		        $repeater->add_control(
		            'team_4_social_icon',
		            [
		                'label' => esc_html__( 'Social Icon', 'widgetkit-for-elementor' ),
		                'type' => Controls_Manager::ICON,
		                'default' => 'fa fa-facebook',
		            ]
		        );


			$this->add_control(
			    'team_4_social_share',
			      [
			          'label'       => esc_html__( 'Social Share', 'widgetkit-for-elementor' ),
			          'type'        => Controls_Manager::REPEATER,
			          'show_label'  => true,
			          'default'     => [
			              [
			              	'team_4_title'       => esc_html__( 'Facebook', 'widgetkit-for-elementor' ),
			                'team_4_social_link' => esc_html__( 'https://www.facebook.com/themesgrove', 'widgetkit-for-elementor' ),
			                'team_4_social_icon' => 'fa fa-facebook',
			 
			              ],
			              [
			              'team_4_title'         => esc_html__( 'Twitter', 'widgetkit-for-elementor' ),
			               'team_4_social_link'  => esc_html__( 'https://www.twitter.com/themesgrove', 'widgetkit-for-elementor' ),
			               'team_4_social_icon'  => 'fa fa-twitter',
			 
			              ],
			              [
			              'team_4_title'         => esc_html__( 'Linkedin', 'widgetkit-for-elementor' ),
			               'team_4_social_link'  => esc_html__( 'https://www.linkedin.com/themesgrove', 'widgetkit-for-elementor' ),
			               'team_4_social_icon'  => 'fa fa-linkedin',
			 
			              ]
			          ],
			          'fields'      => $repeater->get_controls(),
			          'title_field' => '{{{team_4_title}}}',
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
			'team_4_title_icon_style',
			[
				'label' => esc_html__( 'Nav Icon', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'team_4_nav_icon_color',
			[
				'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-team-4.animation .mc-btn-action' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tgx-team-4.animation.mc-active .mc-btn-action' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'team_4_nav_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
	                'size' => 18,
	            ],
				'range' => [
					'px' => [
						'min' =>0,
						'max' =>50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tgx-team-4.animation .mc-btn-action' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'team_4_nav_icon_bg_color',
			[
				'label'     => esc_html__( 'Bg Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3F51B5',
				'selectors' => [
					'{{WRAPPER}} .tgx-team-4.animation .mc-btn-action' => 'background-color: {{VALUE}};',
				],
			]
		);


		$this->add_responsive_control(
			'team_4_nav_icon_radius',
			[
				'label' => esc_html__( 'Radius', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
	                'size' => 50,
	            ],
				'range' => [
					'px' => [
						'min' =>0,
						'max' =>100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tgx-team-4.animation .mc-btn-action,
					{{WRAPPER}} .tgx-team-4.mc-active .img-container' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'team_4_title_style',
			[
				'label' => esc_html__( 'Title', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'team_4_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-team-4.animation .title-wrapper span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name'     => 'team_4_title_typography',
					'label'    => esc_html__( 'Title Typography', 'widgetkit-for-elementor' ),
					'scheme'   => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .tgx-team-4.animation .title-wrapper span',
				]
		);


		$this->add_control(
			'team_4_item_bg_color',
			[
				'label'     => esc_html__( 'Backgroound Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3F51B5',
				'selectors' => [
					'{{WRAPPER}} .tgx-team-4.animation .title-wrapper' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .tgx-team-4.animation .title-wrapper:after' => 'border-top-color: {{VALUE}}; border-right-color: {{VALUE}};',

					'{{WRAPPER}} .tgx-team-4.animation .title-wrapper:before' => 'border-right-color: {{VALUE}}; border-bottom-color: {{VALUE}};',
					
				],
			]
		);


		$this->add_responsive_control(
			'team_4_title_padding',
			[
				'label' => esc_html__( 'Padding', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' =>0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tgx-team-4.animation .title-wrapper' => 'padding: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
            'team_4_title_alignment',
            [
                'label' => esc_html__( 'Alignment', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::CHOOSE,
                'label_block' => false,
                'default' => esc_html__( 'left', 'widgetkit-for-elementor' ),
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
                    '{{WRAPPER}}  .tgx-team-4.animation .title-wrapper' => 'text-align: {{VALUE}}',
                ],
            ]
        );




	$this->end_controls_section();

	$this->start_controls_section(
			'team_4_designation_style',
			[
				'label' => esc_html__( 'Designation', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'team_4_designation_color',
			[
				'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-team-4 .title-wrapper strong' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name'     => 'team_4_designation_typography',
					'label'    => esc_html__( 'Designation Typography', 'widgetkit-for-elementor' ),
					'scheme'   => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .tgx-team-4 .title-wrapper strong',
				]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'team_4_escription_style',
			[
				'label' => esc_html__( 'Description', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'team_4_desc_color',
				[
					'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#444',
					'selectors' => [
						'{{WRAPPER}} .tgx-team-4 .mc-description' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
					[
						'name'     => 'team_4_description_typography',
						'label'    => esc_html__( 'Description Typography', 'widgetkit-for-elementor' ),
						'scheme'   => Typography::TYPOGRAPHY_4,
						'selector' => '{{WRAPPER}} .tgx-team-4 .mc-description',
					]
			);


			$this->add_control(
				'team_4_desc_bg_color',
				[
					'label'     => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#E8EAF6',
					'selectors' => [
						'{{WRAPPER}} .tgx-team-4.animation.mc-active .mc-content' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'team_4_description_space',
				[
					'label' => esc_html__( 'Spacing', 'widgetkit-for-elementor' ),
					'type'  => Controls_Manager::SLIDER,
					'default' => [
	                       'size' => 60,
	                ],
					'range' => [
						'px' => [
							'min' => 60,
							'max' => 200,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .tgx-team-4 .mc-description' => 'padding: {{SIZE}}{{UNIT}} 20px;',
					],
				]
			);

		$this->add_control(
            'team_4_description_alignment',
            [
                'label' => esc_html__( 'Alignment', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::CHOOSE,
                'label_block' => false,
                'default' => esc_html__( 'left', 'widgetkit-for-elementor' ),
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
                    '{{WRAPPER}}  .tgx-team-4 .mc-description' => 'text-align: {{VALUE}}',
                ],
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'team_4_icon_style',
			[
				'label' => esc_html__( 'Social Icon', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_control(
			'team_4_icon_color',
			[
				'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-team-4 .mc-footer a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'team_4_icon_size',
			[
				'label' => esc_html__( 'Font Size', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 12,
						'max' => 30,
					],
				],
				'selectors' => [
					'{{WRAPPER}}  .tgx-team-4 .mc-footer a' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
            'team_4_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .tgx-team-4 .mc-footer a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


		$this->add_control(
            'team_4_hover_heading',
            [
                'label' => esc_html__( 'Hover', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

		$this->add_control(
			'team_4_icon_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1A237E',
				'selectors' => [
					'{{WRAPPER}} .tgx-team-4 .mc-footer a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'team_4_icon_hover_color',
			[
				'label'     => esc_html__( 'Hover Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1A237E',
				'selectors' => [
					'{{WRAPPER}} .tgx-team-4 .mc-footer a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'team_4_icon_hover_bg_color',
			[
				'label'     => esc_html__( 'Hover Background Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-team-4 .mc-footer a:hover' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
            'team_4_social_alignment',
            [
                'label' => esc_html__( 'Alignment', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::CHOOSE,
                'label_block' => false,
                'default' => esc_html__( 'left', 'widgetkit-for-elementor' ),
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
                    '{{WRAPPER}}  .tgx-team-4.mc-active .mc-footer' => 'text-align: {{VALUE}}',
                ],
            ]
        );

		$this->add_control(
			'team_4_icon_area_bg_color',
			[
				'label'     => esc_html__( 'Area Bg Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#C5CAE9',
				'selectors' => [
					'{{WRAPPER}} .tgx-team-4.animation.mc-active .mc-footer' => 'background-color: {{VALUE}};',
				],
			]
		);

	$this->end_controls_section();
	}

	protected function render() {
		require WK_PATH . '/elements/team-4/template/view.php';
	}


}
