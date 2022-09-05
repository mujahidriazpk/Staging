<?php


use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
Use Elementor\Core\Schemes\Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor WidgetKit social share 1
 *
 * Elementor widget for WidgetKit social share 1
 *
 * @since 1.0.0
 */
class wkfe_social_share_1 extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-social-share-1';
	} 

	public function get_title() {
		return esc_html__( 'Social Share Animation', 'widgetkit-for-elementor' );
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
			'section_content_social_share_1',
			[
				'label' => esc_html__( 'Content', 'widgetkit-for-elementor' ),
			]
		);

		$this->add_control(
	       'social_share_1_image',
		        [
		          'label' => esc_html__( 'Upload Image', 'widgetkit-for-elementor' ),
		          'type'  => Controls_Manager::MEDIA,
		          'default' => [
						'url' => Utils::get_placeholder_image_src(),
				  	],
		        ]
	    );

	    $this->add_control(
		    'social_share_1_name',
		      	[
		          'label' => esc_html__( 'Name', 'widgetkit-for-elementor' ),
		          'type'  => Controls_Manager::TEXTAREA,
		          'default' => esc_html__( 'Edward Norton', 'widgetkit-for-elementor' ),
		    	]
	    );


	   	$this->add_control(
		    'social_share_1_designation',
		      	[
		          'label' => esc_html__( 'Profession', 'widgetkit-for-elementor' ),
		          'type'  => Controls_Manager::TEXTAREA,
		          'default' => esc_html__( 'Freelancer', 'widgetkit-for-elementor' ),
		      	]
		);

		$this->add_control(
		    'social_share_1_button_text',
		      	[
		          'label' => esc_html__( 'Button Text', 'widgetkit-for-elementor' ),
		          'type'  => Controls_Manager::TEXT,
		          'default' => esc_html__( 'Click Me', 'widgetkit-for-elementor' ),
		    	]
	    );

			$repeater = new Repeater();

			    $repeater->add_control(
			      'social_share_1_title',
			      [
			          'label' => esc_html__( 'Social Name', 'widgetkit-for-elementor' ),
			          'type'  => Controls_Manager::TEXT,
			          'default' => esc_html__( 'Facebook', 'widgetkit-for-elementor' ),
			      ]
			    );


			$repeater->add_control(
		            'social_share_1_social_link',
		            [
		                'label' => esc_html__( 'Social Link', 'widgetkit-for-elementor' ),
		                'type'  => Controls_Manager::TEXT,
		                'default' => esc_html__( 'https://www.facebook.com/themexpert', 'widgetkit-for-elementor' ),
		            ]
		        );

				$repeater->add_control(
		            'social_share_1_social_email',
		            [
		                'label' => esc_html__( 'Email', 'widgetkit-for-elementor' ),
		                'type'  => Controls_Manager::TEXT,
		                'default' => 'riccavallo@gmail.com',
		            ]
		        );

		        $repeater->add_control(
		            'social_share_1_social_icon',
		            [
		                'label' => esc_html__( 'Social Icon', 'widgetkit-for-elementor' ),
		                'type'  => Controls_Manager::ICON,
		                'default' => 'fa fa-facebook',
		            ]
		        );


			$this->add_control(
			    'social_share_1_social_share',
			      [
			          'label'       => esc_html__( 'Social Share', 'widgetkit-for-elementor' ),
			          'type'        => Controls_Manager::REPEATER,
			          'show_label'  => true,
			          'default'     => [
			              [
			              	'social_share_1_title' => esc_html__( 'Facebook', 'widgetkit-for-elementor' ),
			                'social_share_1_social_link' => esc_html__( 'https://www.facebook.com/themesgrove', 'widgetkit-for-elementor' ),
			                'social_share_1_social_email' => 'riccavallo@gmail.com',
			                'social_share_1_social_icon' => 'fa fa-facebook',
			 
			              ],
			              [
			              'social_share_1_title' => esc_html__( 'Twitter', 'widgetkit-for-elementor' ),
			               'social_share_1_social_link' => esc_html__( 'https://www.twitter.com/themesgrove', 'widgetkit-for-elementor' ),
			                'social_share_1_social_email' => 'riccavallo@gmail.com',
			               'social_share_1_social_icon' => 'fa fa-twitter',
			 
			              ],
			              [
			              'social_share_1_title' => esc_html__( 'Linkedin', 'widgetkit-for-elementor' ),
			               'social_share_1_social_link' => esc_html__( 'https://www.linkedin.com/themesgrove', 'widgetkit-for-elementor' ),
			                'social_share_1_social_email' => 'riccavallo@gmail.com',
			               'social_share_1_social_icon' => 'fa fa-linkedin',
			 
			              ]
			          ],
			          'fields'      => $repeater->get_controls(),
			          'title_field' => '{{{social_share_1_title}}}',
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
			'social_share_1_title_image_style',
			[
				'label' => esc_html__( 'Image', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_control(
            'social_share_1_social_image_alignment',
            [
                'label' => esc_html__( 'Image Alignment', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::CHOOSE,
                'label_block' => false,
                'default' => esc_html__( 'left', 'widgetkit-for-elementor' ),
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'widgetkit-for-elementor' ),
                        'icon'  => 'fa fa-align-left',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'widgetkit-for-elementor' ),
                        'icon'  => 'fa fa-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}  .social-share-1 .contact .contact-wrapper .content img' => 'float: {{VALUE}}',
                ],
            ]
        );
		$this->add_responsive_control(
			'social_share_1_image_raduis',
			[
				'label' => esc_html__( 'Image Radius', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
	                'size' => 100,
	            ],
				'range' => [
					'%' => [
						'min' =>0,
						'max' =>200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .social-share-1 .contact .contact-wrapper .content img' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
            'social_share_1_image_postion',
            [
                'label' => esc_html__( 'Image Gap', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .social-share-1 .contact .contact-wrapper .content img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


		$this->add_control(
			'social_share_1_image_bg_color',
			[
				'label'     => esc_html__( 'Bg Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eb524a',
				'selectors' => [
					'{{WRAPPER}} .social-share-1 .contact .contact-wrapper' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'social_share_1_item_raduis',
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
					'{{WRAPPER}} .social-share-1 .contact .contact-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);



		$this->end_controls_section();

		$this->start_controls_section(
			'social_share_1_title_style',
			[
				'label' => esc_html__( 'Title', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'social_share_1_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .social-share-1 .contact .contact-wrapper .content aside .person-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name'     => 'social_share_1_title_typography',
					'label'    => esc_html__( 'Title Typography', 'widgetkit-for-elementor' ),
					'scheme'   => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .social-share-1 .contact .contact-wrapper .content aside .person-name',
				]
		);


		$this->add_responsive_control(
			'social_share_1_title_margin',
			[
				'label' => esc_html__( 'Margin', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' =>0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .social-share-1 .contact .contact-wrapper .content aside' => 'margin: {{SIZE}}{{UNIT}} 0;',
				],
			]
		);


	$this->end_controls_section();




	$this->start_controls_section(
			'social_share_1_designation_style',
			[
				'label' => esc_html__( 'Profession', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'social_share_1_designation_color',
			[
				'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .social-share-1 .contact .contact-wrapper .content aside p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name'     => 'social_share_1_designation_typography',
					'label'    => esc_html__( 'Designation Typography', 'widgetkit-for-elementor' ),
					'scheme'   => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .social-share-1 .contact .contact-wrapper .content aside p',
				]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'social_share_1_button_style',
			[
				'label' => esc_html__( 'Button', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'social_share_1_button_color',
				[
					'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#fff',
					'selectors' => [
						'{{WRAPPER}} .social-share-1 .contact .contact-wrapper button' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
					[
						'name'     => 'social_share_1_button_typography',
						'label'    => esc_html__( 'Description Typography', 'widgetkit-for-elementor' ),
						'scheme'   => Typography::TYPOGRAPHY_4,
						'selector' => '{{WRAPPER}} .social-share-1 .contact .contact-wrapper button',
					]
			);


			$this->add_control(
				'social_share_1_button_bg_color',
				[
					'label'     => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#ffbe44',
					'selectors' => [
						'{{WRAPPER}} .social-share-1 .contact .contact-wrapper button' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'social_share_1_button_radius',
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
						'{{WRAPPER}} .social-share-1 .contact .contact-wrapper button' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'social_share_1_button_active_bg_color',
				[
					'label'     => esc_html__( 'Active Bg Color', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#c63535',
					'selectors' => [
						'{{WRAPPER}} .social-share-1 .contact .contact-wrapper .title' => 'background-color: {{VALUE}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'social_share_1_icon_style',
			[
				'label' => esc_html__( 'Social Icon', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
            'social_share_1_item_head',
            [
                'label' => esc_html__( 'Items', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
			'social_share_1_social_icon_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f1f1f1',
				'selectors' => [
					'{{WRAPPER}} .social-share-1 .contact .social-share a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
            'social_share_1_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .social-share-1 .contact .social-share a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


		$this->add_control(
            'social_share_1_icon_title_heading',
            [
                'label' => esc_html__( 'Title', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );


		$this->add_control(
			'social_share_1_icon_color',
			[
				'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333',
				'selectors' => [
					'{{WRAPPER}} .social-share-1 .contact .social-share a .social-content .social-name' => 'color: {{VALUE}};',
				],
			]
		);



		$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name'     => 'social_share_1_social_title_typography',
					'label'    => esc_html__( 'Title Typography', 'widgetkit-for-elementor' ),
					'scheme'   => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .social-share-1 .contact .social-share a .social-content .social-name',
				]
		);






		$this->add_control(
            'social_share_1_hover_heading',
            [
                'label' => esc_html__( 'Email', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

		$this->add_control(
			'social_share_1_icon_email_color',
			[
				'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#b3b3b3',
				'selectors' => [
					'{{WRAPPER}} .social-share-1 .contact .social-share a .social-content span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
				Group_Control_Typography::get_type(),
					[
						'name'     => 'social_share_1_social_email_typography',
						'label'    => esc_html__( 'Email Typography', 'widgetkit-for-elementor' ),
						'scheme'   => Typography::TYPOGRAPHY_4,
						'selector' => '{{WRAPPER}} .social-share-1 .contact .social-share a .social-content span',
					]
			);

		$this->add_control(
            'social_share_1_icon_head',
            [
                'label' => esc_html__( 'Icon', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
			'social_share_1_icon_icon_color',
			[
				'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333',
				'selectors' => [
					'{{WRAPPER}} .social-share-1 .contact .social-share a .icon-social' => 'color: {{VALUE}};',
				],
			]
		);


	$this->end_controls_section();
	}

	protected function render() {
		require WK_PATH . '/elements/social-share-1/template/view.php';
	}


}
