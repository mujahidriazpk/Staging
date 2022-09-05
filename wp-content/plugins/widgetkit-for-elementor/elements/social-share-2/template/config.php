<?php

use Elementor\Group_Control_Border;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
Use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor WidgetKit social share 2
 *
 * Elementor widget for WidgetKit social share 2
 *
 * @since 1.0.0
 */
class wkfe_social_share_2 extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-social-share-2';
	} 

	public function get_title() {
		return esc_html__( 'Social Share Collapse', 'widgetkit-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-social-icons wk-icon';
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
            'ionsicon',
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
			'section_content_social_share_2',
			[
				'label' => esc_html__( 'Content', 'widgetkit-for-elementor' ),
			]
		);

		$this->add_control(
	       'social_share_2_image',
		        [
		          'label' => esc_html__( 'Upload Image', 'widgetkit-for-elementor' ),
		          'type'  => Controls_Manager::MEDIA,
		          'default' => [
						'url' => Utils::get_placeholder_image_src(),
				  	],
		        ]
	    );

	    $this->add_control(
		    'social_share_2_name',
		      	[
		          'label' => esc_html__( 'Name', 'widgetkit-for-elementor' ),
		          'type'  => Controls_Manager::TEXTAREA,
		          'default' => esc_html__( 'Kelly Chen', 'widgetkit-for-elementor' ),
		    	]
	    );


	   	$this->add_control(
		    'social_share_2_designation',
		      	[
		          'label' => esc_html__( 'Profession', 'widgetkit-for-elementor' ),
		          'type'  => Controls_Manager::TEXTAREA,
		          'default' => esc_html__( 'Front-end Developer', 'widgetkit-for-elementor' ),
		      	]
		);


			$repeater = new Repeater();

			    $repeater->add_control(
			      'social_share_2_title',
			      [
			          'label' => esc_html__( 'Social Name', 'widgetkit-for-elementor' ),
			          'type'  => Controls_Manager::TEXT,
			          'default' => esc_html__( 'Facebook', 'widgetkit-for-elementor' ),
			      ]
			    );


			$repeater->add_control(
		            'social_share_2_social_link',
		            [
		                'label' => esc_html__( 'Social Link', 'widgetkit-for-elementor' ),
		                'type'  => Controls_Manager::TEXT,
		                'default' => esc_html__( 'https://www.facebook.com/themexpert', 'widgetkit-for-elementor' ),
		            ]
		        );

		        $repeater->add_control(
		            'social_share_2_social_icon',
		            [
		                'label' => esc_html__( 'Social Icon', 'widgetkit-for-elementor' ),
		                'type'  => Controls_Manager::ICON,
		                'default' => 'fa fa-facebook',
		            ]
		        );


			$this->add_control(
			    'social_share_2_social_share',
			      [
			          'label'       => esc_html__( 'Social Share', 'widgetkit-for-elementor' ),
			          'type'        => Controls_Manager::REPEATER,
			          'show_label'  => true,
			          'default'     => [
			              [
			              	'social_share_2_title' => esc_html__( 'Facebook', 'widgetkit-for-elementor' ),
			                'social_share_2_social_link' => esc_html__( 'https://www.facebook.com/themesgrove', 'widgetkit-for-elementor' ),
			                'social_share_2_social_icon' => 'fa fa-facebook',
			 
			              ],
			              [
			              'social_share_2_title' => esc_html__( 'Twitter', 'widgetkit-for-elementor' ),
			               'social_share_2_social_link' => esc_html__( 'https://www.twitter.com/themesgrove', 'widgetkit-for-elementor' ),
			               'social_share_2_social_icon' => 'fa fa-twitter',
			 
			              ],
			              [
			              'social_share_2_title' => esc_html__( 'Linkedin', 'widgetkit-for-elementor' ),
			               'social_share_2_social_link' => esc_html__( 'https://www.linkedin.com/themesgrove', 'widgetkit-for-elementor' ),
			               'social_share_2_social_icon' => 'fa fa-linkedin',
			 
			              ],
			               [
			              'social_share_2_title' => esc_html__( 'Google', 'widgetkit-for-elementor' ),
			               'social_share_2_social_link' => esc_html__( 'https://www.google.com/themesgrove', 'widgetkit-for-elementor' ),
			               'social_share_2_social_icon' => 'fa fa-google',
			 
			              ],
			              [
			              'social_share_2_title' => esc_html__( 'Github', 'widgetkit-for-elementor' ),
			               'social_share_2_social_link' => esc_html__( 'https://www.github.com/themesgrove', 'widgetkit-for-elementor' ),
			               'social_share_2_social_icon' => 'fa fa-github',
			 
			              ]
			          ],
			          'fields'      => $repeater->get_controls(),
			          'title_field' => '{{{social_share_2_title}}}',
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
			'social_share_2_title_image_style',
			[
				'label' => esc_html__( 'Image', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'  => 'image_border',
				'label' => esc_html__( 'Border', 'widgetkit-for-elementor' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .tgx-social-share-2.profile .photo',
				'separator'   => 'before',
			]
		);



		$this->add_responsive_control(
			'social_share_2_image_raduis',
			[
				'label' => esc_html__( 'Image Radius', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'default'  => [
	                'size' => 100,
	            ],
				'range' => [
					'%' => [
						'min' =>0,
						'max' =>200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tgx-social-share-2.profile .photo' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);



		$this->add_control(
			'social_share_2_item_bg_color',
			[
				'label'     => esc_html__( 'Bg Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f1f1f1',
				'selectors' => [
					'{{WRAPPER}} .tgx-social-share-2.profile' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'social_share_2_item_raduis',
			[
				'label' => esc_html__( 'Item Radius', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
	                'size' => 5,
	            ],
				'range' => [
					'%' => [
						'min' =>0,
						'max' =>100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tgx-social-share-2.profile' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'    => 'profile_box_shadow',
                'label'   => esc_html__( 'Box Shadow', 'widgetkit-for-elementor' ),
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .tgx-social-share-2.profile',
            ]
        );



		$this->end_controls_section();

		$this->start_controls_section(
			'social_share_2_title_style',
			[
				'label' => esc_html__( 'Title', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'social_share_2_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333',
				'selectors' => [
					'{{WRAPPER}} .tgx-social-share-2.profile .profile-content .text .profile-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name'     => 'social_share_2_title_typography',
					'label'    => esc_html__( 'Title Typography', 'widgetkit-for-elementor' ),
					'scheme'   => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .tgx-social-share-2.profile .profile-content .text .profile-name',
				]
		);


		$this->add_control(
            'social_share_2_title_speacing',
            [
                'label' => esc_html__( 'Specing', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .tgx-social-share-2.profile .profile-content .text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->add_control(
			'social_share_2_title_bg_color',
			[
				'label'     => esc_html__( 'Bg Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e9f3e6',
				'selectors' => [
					'{{WRAPPER}} .tgx-social-share-2.profile .profile-content::before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
				'social_share_2_title_bg_transform',
				[
					'label' => esc_html__( 'Background Transform', 'widgetkit-for-elementor' ),
					'type'  => Controls_Manager::SLIDER,
					'default' => [
	                       'size' => -8,
	                ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .tgx-social-share-2.profile .profile-content::before' => 'transform: rotate({{SIZE}}deg);',
					],
				]
			);

			$this->add_responsive_control(
				'social_share_2_title_bg_transform_width',
				[
					'label' => esc_html__( 'Bg Transform Width', 'widgetkit-for-elementor' ),
					'type'  => Controls_Manager::SLIDER,
					'default' => [
	                       'size' => 230,
	                ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 500,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .tgx-social-share-2.profile .profile-content::before' => 'width:{{SIZE}}{{UNIT}};',
					],
				]
			);



	$this->end_controls_section();




	$this->start_controls_section(
			'social_share_2_designation_style',
			[
				'label' => esc_html__( 'Profession', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'social_share_2_designation_color',
			[
				'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333',
				'selectors' => [
					'{{WRAPPER}} .tgx-social-share-2.profile .profile-content .text .profile-profession' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name'     => 'social_share_2_designation_typography',
					'label'    => esc_html__( 'Designation Typography', 'widgetkit-for-elementor' ),
					'scheme'   => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .tgx-social-share-2.profile .profile-content .text .profile-profession',
				]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'social_share_2_button_style',
			[
				'label' => esc_html__( 'Button', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'social_share_2_button_color',
				[
					'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#fff',
					'selectors' => [
						'{{WRAPPER}} .tgx-social-share-2.profile .profile-content .btn-bar span::after,
						 {{WRAPPER}} .tgx-social-share-2.profile .profile-content .btn-bar span::before,
						 {{WRAPPER}} .tgx-social-share-2.profile .profile-content .btn-bar span' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'social_share_2_button_bg_color',
				[
					'label'     => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#ffbe44',
					'selectors' => [
						'{{WRAPPER}} .tgx-social-share-2.profile .profile-content .btn-bar' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'social_share_2_button_radius',
				[
					'label' => esc_html__( 'Radius', 'widgetkit-for-elementor' ),
					'type'  => Controls_Manager::SLIDER,
					'default' => [
	                       'size' => 50,
	                ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 200,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .tgx-social-share-2.profile .profile-content .btn-bar' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);



		$this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'    => 'share_button_box_shadow',
                'label'   => esc_html__( 'Box Shadow', 'widgetkit-for-elementor' ),
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .tgx-social-share-2.profile .profile-content .btn-bar:hover',
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'social_share_2_icon_style',
			[
				'label' => esc_html__( 'Social Icon', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
            'social_share_2_item_head',
            [
                'label' => esc_html__( 'Items', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
			'social_share_2_social_icon_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.7)',
				'selectors' => [
					'{{WRAPPER}} .tgx-social-share-2.profile .box' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
            'social_share_2_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .tgx-social-share-2.profile .box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


		$this->add_control(
            'social_share_2_icon_head',
            [
                'label' => esc_html__( 'Icon', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );



	$this->start_controls_tabs( 'social_share_2_style' );

        $this->start_controls_tab(
			'social_share_2_button_normal',
			[
				'label' => esc_html__( 'Normal', 'widgetkit-for-elementor' ),
			]
		);

		$this->add_responsive_control(
			'social_share_2_icon_font_size',
				[
					'label' => esc_html__( 'Size', 'widgetkit-for-elementor' ),
					'type'  => Controls_Manager::SLIDER,
					'default' => [
	                       'size' => 22,
	                ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .tgx-social-share-2.profile .box a' => 'font-size: {{SIZE}}{{UNIT}};',
					],
				]
			);


		$this->add_control(
			'social_share_2_button_text_color',
			[
				'label' => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default' => '#444',
				'selectors' => [
					'{{WRAPPER}} .tgx-social-share-2.profile .box a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'social_share_2_background_color',
			[
				'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-social-share-2.profile .box a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'social_share_2_icon_padding_size',
				[
					'label' => esc_html__( 'Padding', 'widgetkit-for-elementor' ),
					'type'  => Controls_Manager::SLIDER,
					'default' => [
	                       'size' => 50,
	                ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .tgx-social-share-2.profile .box a' => '
						width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					],
				]
			);


		$this->add_control(
            'social_share_2_social_icon_radius',
            [
                'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .tgx-social-share-2.profile .box a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


		$this->end_controls_tab();




		$this->start_controls_tab(
			'social_share_2_button_hover',
			[
				'label' => esc_html__( 'Hover', 'widgetkit-for-elementor' ),
			]
		);

		$this->add_control(
			'social_share_2_hover_color',
			[
				'label' => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-social-share-2.profile .box a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'social_share_2_button_background_hover_color',
			[
				'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default' => '#ffbe44',
				'selectors' => [
					'{{WRAPPER}} .tgx-social-share-2.profile .box a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'    => 'share_social_icon_box_shadow',
                'label'   => esc_html__( 'Box Shadow', 'widgetkit-for-elementor' ),
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .tgx-social-share-2.profile .box a:hover:hover',
            ]
        );

		$this->end_controls_tab();

		$this->end_controls_tabs();


	$this->end_controls_section();
	}

	protected function render() {
		require WK_PATH . '/elements/social-share-2/template/view.php';
	}


}
