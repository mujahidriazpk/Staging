<?php

use Elementor\Group_Control_Box_Shadow;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
Use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Controls_Stack;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor WidgetKit Team
 *
 * Elementor widget for WidgetKit team
 *
 * @since 1.0.0
 */
class wkfe_team extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-team';
	} 

	public function get_title() {
		return esc_html__( 'Team', 'widgetkit-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-person wk-icon';
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
			'fontawesome',
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

	protected function register_controls() {


#	start layout section 
		$this->start_controls_section(
			'section_layout',
				[
					'label' => esc_html__( 'Layout', 'widgetkit-for-elementor' ),
				]
		);
			$this->add_control(
				'item_styles',
					[
						'label'       => __( 'Choose Screen', 'widgetkit-for-elementor' ),
						'type' => Controls_Manager::SELECT,
						'default' => 'screen_1',
						'options' => [
							'screen_1'   => __( 'Screen 1', 'widgetkit-for-elementor' ),
							'screen_2'   => __( 'Screen 2', 'widgetkit-for-elementor' ),
							'screen_3'   => __( 'Screen 3', 'widgetkit-for-elementor' ),
							'screen_4'   => __( 'Screen 4', 'widgetkit-for-elementor' ),
							'screen_5'   => __( 'Screen 5', 'widgetkit-for-elementor' ),
							'screen_6'   => __( 'Screen 6', 'widgetkit-for-elementor' ),
						],
					]
			);
			$this->add_control(
				'image_position',
				[
					'label' => __( 'Image Position', 'widgetkit-for-elementor' ),
					'description' => __( 'You must have a single colum', 'widgetkit-for-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'default' => 'left',
					'options' => [
						'left' => [
							'title' => __( 'Left', 'widgetkit-for-elementor' ),
							'icon' => 'eicon-h-align-left',
						],
						'right' => [
							'title' => __( 'Right', 'widgetkit-for-elementor' ),
							'icon' => 'eicon-h-align-right',
						],
					],
					'toggle' => false,
					'condition'   => [
						'item_styles' => 'screen_3',
					],
				]
			);
			
		$this->end_controls_section();
#	start content section 


# 	start content section

		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'widgetkit-for-elementor' ),
			]
		);
			$this->add_control(
			'single_image',
					[
						'label' => esc_html__( 'Image', 'widgetkit-for-elementor' ),
						'type'  => Controls_Manager::MEDIA,
						'default' => [
							'url' => Utils::get_placeholder_image_src(),
						],
				]
			);
			$this->add_group_control(
                Group_Control_Image_Size::get_type(),
                [
                    'label' => esc_html__('Image size', 'widgetkit-for-elementor'),
                    'name' => 'team_image',
                    'default' => 'large',
                    'separator' => 'none',
                ]
            );
			$this->add_control(
				'single_title',
					[
					'label' => esc_html__( 'Title', 'widgetkit-for-elementor' ),
					'type'  => Controls_Manager::TEXT,
					'default' => esc_html__( 'Alex Prokopenko', 'widgetkit-for-elementor' ),
					]
			);
			$this->add_control(
				'single_designation',
					[
					'label' => esc_html__( 'Designation', 'widgetkit-for-elementor' ),
					'type'  => Controls_Manager::TEXT,
					'default' => esc_html__( 'Graphics Designer', 'widgetkit-for-elementor' ),
					]
			);
			$this->add_control(
				'single_content',
					[
						'label' => esc_html__( 'Description', 'widgetkit-for-elementor' ),
						'type'  => Controls_Manager::TEXTAREA,
						'default' => esc_html__( 'This is the place to present the bio of Jason. He is a cat and dog loving person, who loves sunday ice creams, Lady Gaga and everything Apple related.', 'widgetkit-for-elementor' ),
					]
			);
			$this->add_control(
				'single_content_link',
					[
						'label' => __( 'Link', 'widgetkit-for-elementor' ),
						'type'  => Controls_Manager::URL,
						// 'dynamic' => [
						// 	'active'  => true,
						// ],
						'placeholder' => __( 'https://your-link.com', 'widgetkit-for-elementor' ),
						'separator'   => 'after',
						// 'condition'   => [
		//                    'item_option' => 'single_content',
		//                ],
						'default' => [
							'url' => 'https://www.themesgrove.com', 
						],
					]
				);
				$repeater = new Repeater();

					$repeater->add_control(
					'title',
					[
						'label'   => esc_html__( 'Social Name', 'widgetkit-for-elementor' ),
						'type'    => Controls_Manager::TEXT,
						'default' => esc_html__( 'Facebook', 'widgetkit-for-elementor' ),
					]
					);

					$repeater->add_control(
						'social_icon',
						[
							'label' => esc_html__( 'Social Icon', 'widgetkit-for-elementor' ), 
							'type'              => Controls_Manager::ICONS,
							'fa4compatibility'  => 'social',
							'default'    =>  [
								'value'     => 'fa fa-facebook',
								'library'   => 'fa-solid',
							],
							'recommended' => [
								'fa-brands' => [
									'android',
									'apple',
									'behance',
									'bitbucket',
									'codepen',
									'delicious',
									'deviantart',
									'digg',
									'dribbble',
									'facebook',
									'flickr',
									'foursquare',
									'free-code-camp',
									'github',
									'gitlab',
									'globe',
									'google-plus',
									'houzz',
									'instagram',
									'jsfiddle',
									'linkedin',
									'medium',
									'meetup',
									'mixcloud',
									'odnoklassniki',
									'pinterest',
									'product-hunt',
									'reddit',
									'shopping-cart',
									'skype',
									'slideshare',
									'snapchat',
									'soundcloud',
									'spotify',
									'stack-overflow',
									'steam',
									'stumbleupon',
									'telegram',
									'thumb-tack',
									'tripadvisor',
									'tumblr',
									'twitch',
									'twitter',
									'viber',
									'vimeo',
									'vk',
									'weibo',
									'weixin',
									'whatsapp',
									'wordpress',
									'xing',
									'yelp',
									'youtube',
									'500px',
								],
								'fa-solid' => [
									'envelope',
									'link',
									'rss',
								],
							],
							'label_block'   => true,
						]
					);
					$repeater->add_control(
						'social_link',
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
				$this->add_control(
					'social_share',
					[
						'label'       => esc_html__( 'Social Links', 'widgetkit-for-elementor' ),
						'type'        => Controls_Manager::REPEATER,
						'fields'      =>  $repeater->get_controls() ,
						'show_label'  => true,
						'default'     => [
							[
								'title'       => esc_html__( 'Facebook', 'widgetkit-for-elementor' ),
								'social_icon' => [
									'value' => 'fa fa-facebook',
									'library' => 'fa-solid',
								],
								'social_link' => esc_html__( 'https://www.facebook.com/themesgrove', 'widgetkit-for-elementor' ),
							],
							[
								'title'       => esc_html__( 'Twitter', 'widgetkit-for-elementor' ),
								'social_icon' => [
									'value' => 'fa fa-twitter',
									'library' => 'fa-solid',
								],
								'social_link' => esc_html__( 'https://www.twitter.com/themesgrove', 'widgetkit-for-elementor' ),
							],
							[
								'title'       => esc_html__( 'Linkedin', 'widgetkit-for-elementor' ),
								'social_icon' => [
									'value' => 'fa fa-linkedin',
									'library' => 'fa-solid',
								],
								'social_link' => esc_html__( 'https://www.linkedin.com/themesgrove', 'widgetkit-for-elementor' ),
							]
						],
						'title_field' => '{{{title}}}',
					]
				);
				$this->add_control(
					'header_tag',
					[
						'label' => __( 'Title Heading Tag', 'widgetkit-for-elementor' ),
						'type' => Controls_Manager::SELECT,
						'options' => [
							'h1' => 'H1',
							'h2' => 'H2',
							'h3' => 'H3',
							'h4' => 'H4',
							'h5' => 'H5',
							'h6' => 'H6',
							'div' => 'div',
							'span' => 'span',
							'p' => 'p',
						],
						'default' => 'h3',
					]
				);
		$this->end_controls_section();

#	end content section

			
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
            'section_style_shape',
            [
                'label' => esc_html__( 'Shape', 'widgetkit-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition'   => [
                    'item_styles' => 'screen_6',
                ],
            ]
        );

	            $this->add_control(
					'image_shape_bg_color',
					[
						'label'     => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '',
						'selectors' => [
							'{{WRAPPER}} .wk-team .wk-style-6 .wk-card-wrapper .wk-card-link' => 'background-color: {{VALUE}};',
						],
						'condition'   => [
                        	'item_styles' => 'screen_6',
                    	],
					]
				);

				$this->add_control(
		            'image_shape_radius',
		            [
		                'label' => esc_html__( 'Radius', 'widgetkit-for-elementor' ),
		                'type'  => Controls_Manager::DIMENSIONS,
		                'size_units' => [ 'px', '%' ],
		                'placeholder' => [
								'top' => '60',
								'right' => '60',
								'bottom' => '40',
								'left' => '40',
							],
		                'selectors'  => [
		                    '{{WRAPPER}} .wk-team .wk-style-6 .wk-card-wrapper .wk-card-link' => 'border-radius:50% 50% 50% 50%/{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		                ],
		                'condition'   => [
                        	'item_styles' =>'screen_6',
                    	],
		            ]
		        );


				$this->add_control(
	                'image_bg_position',
	                [
	                    'label'       => __( 'Background Size', 'widgetkit-for-elementor' ),
	                    'type' => Controls_Manager::SELECT,
	                    'default' => 'cover',
	                    'options' => [
	                        'auto'     => __( 'Auto', 'widgetkit-for-elementor' ),
	                        'contain'  => __( 'Contain', 'widgetkit-for-elementor' ),
	                        'cover'    => __( 'Cover', 'widgetkit-for-elementor' ),
	                    ],
	                    'condition'   => [
                        	'item_styles' => 'screen_6',
                    	],
                    	'selectors' => [
							'{{WRAPPER}} .wk-team .wk-style-6 .wk-card-wrapper .wk-card-hover-bg' => 'background-size: {{VALUE}};',
						],
                    	
	                ]
	            );

        $this->end_controls_section();



		$this->start_controls_section(
            'section_style_image',
            [
                'label' => esc_html__( 'Image', 'widgetkit-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );



	        $this->add_responsive_control(
	            'image_size',
	                [
	                    'label'   => esc_html__( 'Size(%)', 'widgetkit-for-elementor' ),
	                    'type'    => Controls_Manager::SLIDER,
	                    'default' => [
	                    'size'    => 100,
	                    ],
	                    'range'   => [
	                        '%'   => [
	                            'min' => 10,
	                            'max' => 100,
	                        ],
	                    ],
	                    'selectors' => [
	                        '{{WRAPPER}} .wk-team .wk-card.wk-style-1 .wk-card-media-top img' => 'width: {{SIZE}}%;',
	                        '{{WRAPPER}} .wk-team .wk-card.wk-style-5 .wk-card-wrapper img'  => 'width: {{SIZE}}%;',
	                        '{{WRAPPER}} .wk-team .wk-card.wk-style-2 .wk-card-media-top a, {{WRAPPER}} .wk-team .wk-card.wk-style-2 .wk-card-media-top img'  => 'width: {{SIZE}}%;',
	                    ],
	                    'condition'   => [
                        	'item_styles' => ['screen_1', 'screen_2', 'screen_5'],
                    	],
	                ]
	            );


	            $this->add_responsive_control(
	                'image_height_custom',
	                    [
	                        'label'   => esc_html__( 'Height(px)', 'widgetkit-for-elementor' ),
	                        'type'    => Controls_Manager::SLIDER,
	                        'default' => [
	                           'size' =>'',
	                        ],
	                        'range'  => [
	                            'px' => [
	                                'min' => 10,
	                                'max' => 1000,
	                            ],
	                        ],
	                        'selectors' => [
	                            '{{WRAPPER}} .wk-team .wk-card .wk-card-media-top' => 'max-height: {{SIZE}}{{UNIT}};',
	                             '{{WRAPPER}} .wk-team .wk-card .wk-card-media-left' => 'max-height: {{SIZE}}{{UNIT}};',
	                             '{{WRAPPER}} .wk-team .wk-card .wk-card-media-right' => 'max-height: {{SIZE}}{{UNIT}};',
	                        ],
	                        'condition'   => [
                        		'item_styles' => ['screen_1', 'screen_3'],
                    		],
	                    ]
	            );




		       //  $this->add_responsive_control(
	        //         'image_shape_radius',
	        //             [
	        //                 'label'   => esc_html__( 'Shape Radius', 'widgetkit-for-elementor' ),
	        //                 'type'    => Controls_Manager::SLIDER,
	        //                 'default' => [
	        //                 'size'    => 0,
	        //                 ],
	        //                 'range'  => [
	        //                     '%' => [
	        //                         'min' => 0,
	        //                         'max' => 100,
	        //                     ],
	        //                 ],
  							// 'selectors'  => [
		       //              	'{{WRAPPER}} .wk-team .wk-style-6 .wk-card-wrapper .wk-card-link' => 'border-radius:50% 50% 50% 50%/{{SIZE}}{{UNIT}} 50% calc({{SIZE}}{{UNIT}} - 20%) calc({{SIZE}}{{UNIT}} - 30%);',
		       //          	],

	        //                 'condition'   => [
         //                		'item_styles' =>'screen_6',
         //            		],
	        //             ]
	        //     );

        		$this->add_control(
					'image_hover_overlay_color',
					[
						'label'     => esc_html__( 'Hover Overlay Color', 'widgetkit-for-elementor' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '',
						'selectors' => [
							'{{WRAPPER}} .wk-team .wk-style-6 .wk-card-wrapper:not(.nohover):hover' => 'background-color: {{VALUE}};',
						],
						'condition'   => [
                        	'item_styles' => 'screen_6',
                    	],
					]
				);


	        	$this->add_control(
					'image_border_color',
					[
						'label'     => esc_html__( 'Border Color', 'widgetkit-for-elementor' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '',
						'selectors' => [
							'{{WRAPPER}} .wk-team .wk-style-3 .wk-card-media-left:before' => 'background: {{VALUE}};',
							'{{WRAPPER}} .wk-team .wk-style-3 .wk-card-media-right:before' => 'background: {{VALUE}};',
						],
						'condition'   => [
                        	'item_styles' => 'screen_3',
                    	],
					]
				);
		        $this->add_control(
		            'image_border_radius',
		            [
		                'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
		                'type'  => Controls_Manager::DIMENSIONS,
		                'size_units' => [ 'px', '%' ],
		                'selectors'  => [
		                    '{{WRAPPER}} .wk-team .wk-card.wk-style-2 .wk-card-media-top a, {{WRAPPER}} .wk-team .wk-card.wk-style-2 .wk-card-media-top img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		                    '{{WRAPPER}} .wk-team .wk-style-3 .wk-card-media-left:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		                    '{{WRAPPER}} .wk-team .wk-style-3 .wk-card-media-right:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		                    '{{WRAPPER}} .wk-team .wk-style-4 .wk-card-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

		                     '{{WRAPPER}} .wk-team .wk-style-4 .wk-card-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		                    '{{WRAPPER}} .wk-team .wk-style-5 .wk-card-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		                ],
		                'condition'   => [
                        	'item_styles' => ['screen_2', 'screen_3', 'screen_4' , 'screen_5'],
                    	],
		            ]
		        );


         $this->end_controls_section();


		$this->start_controls_section(
			'content_style',
			[
				'label' => esc_html__( 'Content', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
	       		$this->add_control(
	                'title_heading',
	                [
	                    'label' => __( 'Title', 'widgetkit-for-elementor' ),
	                    'type'  => Controls_Manager::HEADING,
	                    'separator' => 'before',
	                ]
	            );

				$this->add_control(
					'title_color',
					[
						'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '#404040',
						'selectors' => [
							'{{WRAPPER}} .wk-team .wk-card .wk-card-body  .wk-card-title a' => 'color: {{VALUE}};',
						],
					]
				);
				$this->add_group_control(
					Group_Control_Typography::get_type(),
						[
							'name'     => 'title_typography',
							'label'    => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
							'scheme'   => Typography::TYPOGRAPHY_4,
							'selector' => '{{WRAPPER}} .wk-team .wk-card .wk-card-body .wk-card-title',
						]
				);
				$this->add_control(
	                'title_hover_color',
	                [
	                    'label'     => esc_html__( 'Hover Color', 'widgetkit-for-elementor' ),
	                    'type'      => Controls_Manager::COLOR,
	                    'default'   => '#0073aa',
	                    'selectors' => [
	                        '{{WRAPPER}} .wk-team .wk-card .wk-card-body .wk-card-title a:hover' => 'color: {{VALUE}}; text-decoration:none;',
	                        '{{WRAPPER}} .wk-team .wk-style-6 .wk-card-wrapper:hover .wk-card-title a' => 'color: {{VALUE}} !important;',
	                    ],
	                ]
	            );


	            $this->add_responsive_control(
	                'title_spacing',
	                    [
	                        'label'   => esc_html__( 'Spacing', 'widgetkit-for-elementor' ),
	                        'type'    => Controls_Manager::SLIDER,
	                        'default' => [
	                        'size'    => 0,
	                        ],
	                        'range'  => [
	                            'px' => [
	                                'min' => 0,
	                                'max' => 100,
	                            ],
	                        ],
	                        'selectors' => [
	                            // '{{WRAPPER}} .wk-team .wk-card .wk-card-body .wk-grid-small' => 'padding: {{SIZE}}{{UNIT}} 0 0;',
	                            '{{WRAPPER}} .wk-team .wk-card .wk-card-body .wk-card-title' => 'padding: 0 0 {{SIZE}}{{UNIT}};',
	                        ],
	                    ]
	            );


		        $this->add_control(
	                'designation_heading',
	                [
	                    'label' => __( 'Designation', 'widgetkit-for-elementor' ),
	                    'type'  => Controls_Manager::HEADING,
	                    'separator' => 'before',
	                ]
	            );

				$this->add_control(
					'designation_color',
					[
						'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '',
						'selectors' => [
							'{{WRAPPER}} .wk-team .wk-card .wk-card-body .wk-card-designation' => 'color: {{VALUE}};',
						],
					]
				);
				$this->add_group_control(
					Group_Control_Typography::get_type(),
						[
							'name'     => 'designation_typography',
							'label'    => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
							'scheme'   => Typography::TYPOGRAPHY_4,
							'selector' => '{{WRAPPER}} .wk-team .wk-card .wk-card-body .wk-card-designation',
						]
				);

				$this->add_control(
	                'designation_hover_color',
	                [
	                    'label'     => esc_html__( 'Hover Color', 'widgetkit-for-elementor' ),
	                    'type'      => Controls_Manager::COLOR,
	                    'default'   => '#fff',
	                    'selectors' => [
	                        '{{WRAPPER}} .wk-team .wk-style-6 .wk-card-wrapper:hover .wk-card-designation' => 'color: {{VALUE}} !important;',
	                    ],
	                    'condition'   => [
                        	'item_styles' => 'screen_6',
                    	],
	                ]
	            );


	            $this->add_responsive_control(
	                'designation_spacing',
	                    [
	                        'label'   => esc_html__( 'Spacing', 'widgetkit-for-elementor' ),
	                        'type'    => Controls_Manager::SLIDER,
	                        'default' => [
	                        'size'    => 0,
	                        ],
	                        'range'  => [
	                            'px' => [
	                                'min' => 0,
	                                'max' => 100,
	                            ],
	                        ],
	                        'selectors' => [
	                            '{{WRAPPER}} .wk-team .wk-card .wk-card-body .wk-card-designation' => 'margin-bottom: {{SIZE}}{{UNIT}};',
	                        ],
	                    ]
	            );


		        $this->add_control(
	                'description_heading',
	                [
	                    'label' => __( 'Description', 'widgetkit-for-elementor' ),
	                    'type'  => Controls_Manager::HEADING,
	                    'separator' => 'before',
	                ]
	            );

				$this->add_control(
					'description_color',
					[
						'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '',
						'selectors' => [
							'{{WRAPPER}} .wk-team .wk-card .wk-card-body .wk-text-normal' => 'color: {{VALUE}};',
						],
					]
				);
				$this->add_group_control(
					Group_Control_Typography::get_type(),
						[
							'name'     => 'description_typography',
							'label'    => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
							'scheme'   => Typography::TYPOGRAPHY_4,
							'selector' => '{{WRAPPER}} .wk-team .wk-card .wk-card-body .wk-text-normal',
						]
				);
				$this->add_control(
	                'description_hover_color',
	                [
	                    'label'     => esc_html__( 'Hover Color', 'widgetkit-for-elementor' ),
	                    'type'      => Controls_Manager::COLOR,
	                    'default'   => '#fff',
	                    'selectors' => [
	                        '{{WRAPPER}} .wk-team .wk-style-6 .wk-card-wrapper:hover .wk-text-normal' => 'color: {{VALUE}} !important;',
	                    ],
	                    'condition'   => [
                        	'item_styles' => 'screen_6',
                    	],
	                ]
	            );


 				$this->add_control(
	                'common_heading',
	                [
	                    'label' => __( 'Common', 'widgetkit-for-elementor' ),
	                    'type'  => Controls_Manager::HEADING,
	                    'separator' => 'before',
	                ]
	            );

	            $this->add_control(
	                'content_bg_color',
	                [
	                    'label'     => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
	                    'type'      => Controls_Manager::COLOR,
	                    'default'   => '',
	                    'selectors' => [
	                        '{{WRAPPER}} .wk-team .wk-card.wk-style-1 .wk-card-body' => 'background: {{VALUE}};',
	                        '{{WRAPPER}} .wk-team .wk-card.wk-style-4 .wk-card-body' => 'background: {{VALUE}};',
	                        '{{WRAPPER}} .wk-team .wk-card.wk-style-5 .wk-card-body' => 'background: {{VALUE}};',
	                    ],
	                     'condition'   => [
                        	'item_styles' => ['screen_1', 'screen_4', 'screen_5'],
                    	],
	                ]
	            );



	            $this->add_group_control(
	                Group_Control_Box_Shadow::get_type(),
	                [
	                    'name' => 'content_box_shadow',
	                    'exclude' => [
	                        'box_shadow_position',
	                    ],
	                    'selector' => '{{WRAPPER}} .wk-team .wk-card',
	                ]
	            );

			    $this->add_responsive_control(
					'content_layout_align',
					[
						'label' => esc_html__( 'Alignment', 'widgetkit-for-elementor' ),
						'type'  => Controls_Manager::CHOOSE,
						'default'   =>'' ,
						'options'   => [
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
						'selectors' => [
							'{{WRAPPER}} .wk-team .wk-card' => 'text-align: {{VALUE}};',
						],
						'condition'   => [
                        	'item_styles' => ['screen_2', 'screen_3', 'screen_4', 'screen_5'],
                    	],
					]
				);

				$this->add_responsive_control(
		            'content_padding',
		            [
		                'label' => esc_html__( 'Padding', 'widgetkit-for-elementor' ),
		                'type'  => Controls_Manager::DIMENSIONS,
		                'size_units' => [ 'px', '%' ],
		                'selectors'  => [
		                    '{{WRAPPER}} .wk-team .wk-card .wk-card-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		                    '{{WRAPPER}} .wk-team .wk-card .wk-card-body .info-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		                ],
		            ]
		        );

		       	$this->add_responsive_control(
		            'content_position',
		            [
		                'label' => esc_html__( 'Position', 'widgetkit-for-elementor' ),
		                'type'  => Controls_Manager::DIMENSIONS,
		                'size_units' => [ 'px', '%' ],
		                'allowed_dimensions' => ['right', 'bottom', 'left' ],
		                	'placeholder' => [
								'top' => '',
								'right' => '60',
								'bottom' => '17',
								'left' => '5',
							],
					        'selectors'  => [
		                    '{{WRAPPER}} .wk-team .wk-style-4 .wk-card-body' => 'right: {{RIGHT}}{{UNIT}}; bottom: {{BOTTOM}}{{UNIT}};
		                    left: {{LEFT}}{{UNIT}};',
		                    
		                ],
		                'condition'   => [
                        	'item_styles' => 'screen_4',
                    	],
		            ]
		        );

		        $this->add_control(
		            'content_border_radius',
		            [
		                'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
		                'type'  => Controls_Manager::DIMENSIONS,
		                'size_units' => [ 'px', '%' ],
		                'placeholder' => [
							'top' => '0',
							'right' => '10',
							'bottom' => '0',
							'left' => '10',
						],
		                'selectors'  => [
		                    '{{WRAPPER}} .wk-team .wk-style-4 .wk-card-body' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		                    '{{WRAPPER}} .wk-team .wk-style-5 .wk-card-body' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		                    '{{WRAPPER}} .wk-team .wk-style-6, {{WRAPPER}}  .wk-team .wk-style-6 .wk-card-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',


		                   
		                ],
		                'condition'   => [
                        	'item_styles' => ['screen_4', 'screen_5', 'screen_6'],
                    	],
		            ]
		        );
		        $this->add_control(
	                'global_heading',
	                [
	                    'label' => __( 'Global', 'widgetkit-for-elementor' ),
	                    'type'  => Controls_Manager::HEADING,
	                    'separator' => 'before',
	                ]
	            );

		        $this->add_control(
	                'content_item_bg_color',
	                [
	                    'label'     => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
	                    'type'      => Controls_Manager::COLOR,
	                    'default'   => '',
	                    'selectors' => [
	                        '{{WRAPPER}} .wk-team .wk-card.wk-style-1' => 'background: {{VALUE}};',
	                        '{{WRAPPER}} .wk-team .wk-card.wk-style-2' => 'background: {{VALUE}};',
	                        '{{WRAPPER}} .wk-team .wk-card.wk-style-3' => 'background: {{VALUE}};',
	                        '{{WRAPPER}} .wk-team .wk-card.wk-style-4' => 'background: {{VALUE}};',
	                        '{{WRAPPER}} .wk-team .wk-card.wk-style-5' => 'background: {{VALUE}};',
	                        '{{WRAPPER}} .wk-team .wk-card.wk-style-6' => 'background: {{VALUE}};',
	                    ],
	                ]
	            );

		$this->end_controls_section();

	

		$this->start_controls_section(
			'icon_style',
			[
				'label' => esc_html__( 'Social Links', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
			$this->add_control(
				'icon_size',
				[
					'label' => esc_html__( 'Font Size', 'widgetkit-for-elementor' ),
					'type'  => Controls_Manager::SLIDER,
					'default'  => [
						'size' => 12,
					],
					'range' => [
						'px' => [
							'min' => 1,
							'max' => 30,
						],
					],
					'selectors' => [
						'{{WRAPPER}}  .wk-team .wk-card .wk-card-body .social-icons a' => 'font-size: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
	            'border_radius',
	            [
	                'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
	                'type'  => Controls_Manager::DIMENSIONS,
	                'size_units' => [ 'px', '%' ],
	                'selectors'  => [
	                    '{{WRAPPER}} .wk-team .wk-card .wk-card-body .social-icons a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );

	       	$this->add_responsive_control(
                'social_width_height',
                    [
                        'label'   => esc_html__( 'Width & Height', 'widgetkit-for-elementor' ),
                        'type'    => Controls_Manager::SLIDER,
                        'default' => [
                        'size'    => 35,
                        ],
                        'range'  => [
                            'px' => [
                                'min' => 0,
                                'max' => 100,
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .wk-team .wk-card .wk-card-body .social-icons a' => 'width: {{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}}; line-height: calc({{SIZE}}{{UNIT}} + 2{{UNIT}});',
                        ],
                    ]
            );


		$this->start_controls_tabs( 'tabs_social_style' );

			    $this->start_controls_tab(
			        'tab_social_normal',
			          [
			            'label' => esc_html__( 'Normal', 'widgetkit-for-elementor' ),
			          ]
			    );

			    		$this->add_control(
							'icon_color',
							[
								'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
								'type'      => Controls_Manager::COLOR,
								'default'   => '#fff',
								'selectors' => [
									'{{WRAPPER}} .wk-team .wk-card .wk-card-body .social-icons a' => 'color: {{VALUE}};',
								],
							]
						);

						$this->add_control(
							'icon_bg_color',
							[
								'label'     => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
								'type'      => Controls_Manager::COLOR,
								'default'   => '#EEB90E',
								'selectors' => [
									'{{WRAPPER}} .wk-team .wk-card .wk-card-body .social-icons a' => 'background-color: {{VALUE}};',
								],
							]
						);

			        $this->add_group_control(
			            Group_Control_Border::get_type(),
			            [
			                'name'  => 'icon_border',
			                'label' => esc_html__( 'Border', 'widgetkit-for-elementor' ),
			                'placeholder' => '1px',
			                'default'  => '1px',
			                'selector' => '
			                    {{WRAPPER}} .wk-team .wk-card .wk-card-body .social-icons a',
			                'separator' => 'before',
			            ]
			        );

				$this->end_controls_tab();

				$this->start_controls_tab(
			        'tab_social_hover',
			          [
			            'label' => esc_html__( 'Hover', 'widgetkit-for-elementor' ),
			          ]
			    );

			    		$this->add_control(
							'icon_hover_color',
							[
								'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
								'type'      => Controls_Manager::COLOR,
								'default'   => '',
								'selectors' => [
									'{{WRAPPER}} .wk-team .wk-card .wk-card-body .social-icons a:hover i' => 'color: {{VALUE}};',
								],
							]
						);

						$this->add_control(
							'icon_hover_bg_color',
							[
								'label'     => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
								'type'      => Controls_Manager::COLOR,
								'default'   => '',
								'selectors' => [
									'{{WRAPPER}} .wk-team .wk-card .wk-card-body .social-icons a:hover' => 'background-color: {{VALUE}}; transition:all 0.3s ease;',
								],
							]
						);

						$this->add_control(
							'icon_hover_border_color',
							[
								'label'     => esc_html__( 'Border Color', 'widgetkit-for-elementor' ),
								'type'      => Controls_Manager::COLOR,
								'default'   => '',
								'selectors' => [
									'{{WRAPPER}} .wk-team .wk-card .wk-card-body .social-icons a:hover' => 'border-color: {{VALUE}}; transition:all 0.3s ease;',
								],
							]
						);
				$this->end_controls_tab();

			$this->end_controls_tabs();

	$this->end_controls_section();
	}

	protected function render() {
		require WK_PATH . '/elements/team/template/view.php';
		?>
		<!-- <h3>hello world</h3> -->
		<?php 
	}
	protected function _content_template()
    {
    }


}
