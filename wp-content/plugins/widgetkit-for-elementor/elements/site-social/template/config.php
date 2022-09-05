<?php

use Elementor\Group_Control_Border;
use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
Use Elementor\Core\Schemes\Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor WidgetKit animation text
 *
 * Elementor widget for WidgetKit animation text
 *
 * @since 1.0.0
 */
class wkfe_site_social extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-site-social';
	}

	public function get_title() {
		return esc_html__( 'Site Social', 'widgetkit-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-share wk-icon';
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
            'animate-text',
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

#	region position start
	// Content options Start
	$this->start_controls_section(
		'section_text_content',
		[
			'label' => esc_html__( 'Position', 'widgetkit-for-elementor' ),
		]
	);

		$this->add_responsive_control(
			'site_social_icon_alignment',
			[
				'label' => esc_html__( 'Alignment', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options' => [
					'left'    => [
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
					'{{WRAPPER}} .wkfe-site-social .wkfe-site-social-wrapper' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'site_social_platform_position',
			[
				'label' => esc_html__( 'Site Social Platform Position', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'allowed_dimensions' => [ 'top', '', '', '' ],
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .wkfe-site-social .wkfe-site-social-wrapper .wkfe-site-social-platform-wrapper.active' => 'top: {{TOP}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
            'social_icon_picker_for_handler',
            [
                'label' => esc_html__( 'Icon', 'widgetkit-for-elementor' ),
				'type'              => Controls_Manager::ICONS,
				'default'    =>  [
					'value'     => 'fa fa-share-alt',
					'library'   => 'fa-regular',
                ],
                'label_block'   => true,
            ]
        );
		$repeater = new Repeater();

		$repeater->add_control(
            'social_icon_picker',
            [
                'label' => esc_html__( 'Icon', 'widgetkit-for-elementor' ),
				'type'              => Controls_Manager::ICON,
				'default' => esc_html__( 'fa fa-facebook', 'widgetkit-for-elementor' ),
            ]
        );
        $repeater->add_control(
            'social_link',
            [
                'label'   => esc_html__( 'Link', 'widgetkit-for-elementor' ),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__( '#', 'widgetkit-for-elementor' ),
            ]
		);
		$repeater->add_control(
			'site_social_platform_hover_color',
			[
				'label' => __( 'Hover Color', 'widgetkit-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wkfe-site-social .wkfe-site-social-platform-wrapper a:hover' => 'color: {{VALUE}}',
				],
			]
		);

        $this->add_control(
            'site_social_default_list',
            [
                'type'    => Controls_Manager::REPEATER,
                'fields'  => $repeater->get_controls(),
                'default' => [
                    [
                        'social_icon_picker' => esc_html__( 'fa fa-facebook', 'widgetkit-for-elementor' ),
                        'social_link' => esc_html__( '#', 'widgetkit-for-elementor' ),
                        'site_social_platform_hover_color' => esc_html__( '#3b5999', 'widgetkit-for-elementor' ),
                    ],
                    [
						'social_icon_picker' => esc_html__( 'fa fa-twitter', 'widgetkit-for-elementor' ),
                        'social_link' => esc_html__( '#', 'widgetkit-for-elementor' ),
						'site_social_platform_hover_color' => esc_html__( '#55acee', 'widgetkit-for-elementor' ),
                    ],
                    [
						'social_icon_picker' => esc_html__( 'fa fa-youtube', 'widgetkit-for-elementor' ),
                        'social_link' => esc_html__( '#', 'widgetkit-for-elementor' ),
						'site_social_platform_hover_color' => esc_html__( '#cd201f', 'widgetkit-for-elementor' ),
                    ],
                ],
                // 'title_field' => '{{{ social_icon_picker }}}',
            ]
        );
	$this->end_controls_section();
# 	end postion region 

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

#	start region site_social icon style
	// Content options End
		$this->start_controls_section(
			'section_site_social_icon_layout',
			[
				'label' => esc_html__( 'Icon', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
			$this->add_control(
				'site_social_handler_icon_size',
				[
					'label'   => esc_html__( 'Size', 'widgetkit-for-elementor' ),
					'type'    => Controls_Manager::SLIDER,
					'default' => [
						'size' =>16,
					],
					'range'  => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .wkfe-site-social .site-social-click-handler' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .wkfe-site-social .site-social-click-handler svg' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);
			/**
             * tabs normal
             */
            $this->start_controls_tabs( 'site_social_icon_style__tab' );
                /**
                 * Normal style
                 */
                $this->start_controls_tab(
                    'site_social_icon_style_normal',
                    [
                        'label' => __('Normal', 'widgetkit-pro'),
                    ]
                );
					$this->add_control(
						'site_social_icon_color',
						[
							'label' => __( 'Color', 'widgetkit-pro' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .wkfe-site-social .click-handler' => 'color: {{VALUE}}',
							],
						]
					);
					$this->add_control(
						'site_social_icon_background_color',
						[
							'label' => __( 'Background', 'widgetkit-pro' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .wkfe-site-social .click-handler' => 'background-color: {{VALUE}}',
							],
						]
					);
                $this->end_controls_tab();
                /**
                 * Hover Style
                 */
                $this->start_controls_tab(
                    'site_social_icon_style_hover',
                    [
                        'label' => __('Hover/Active', 'widgetkit-pro'),
                    ]
                );
					$this->add_control(
						'site_social_icon_hover_color',
						[
							'label' => __( 'Icon', 'widgetkit-pro' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .wkfe-site-social .click-handler:hover' => 'color: {{VALUE}}',
								'{{WRAPPER}} .wkfe-site-social .click-handler.active' => 'color: {{VALUE}}',
							],
						]
					);
					$this->add_control(
						'site_social_icon_hover_background_color',
						[
							'label' => __( 'Background', 'widgetkit-pro' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .wkfe-site-social .click-handler:hover' => 'background: {{VALUE}} !important',
								'{{WRAPPER}} .wkfe-site-social .click-handler.active' => 'background: {{VALUE}} !important',
							],
						]
					);
                $this->end_controls_tab();
        
            $this->end_controls_tabs();

			// icon border
			$this->add_group_control(
                Group_Control_Border::get_type(), 
                [
                    'name'          => 'site_social_icon_border',
					'selector'      => '{{WRAPPER}} .wkfe-site-social .click-handler',
					'separator'		=> 'before'
                ]
			);
			// icon padding 
			$this->add_responsive_control(
                'site_social_icon_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'widgetkit-pro' ),
                    'type'  => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors'  => [
                        '{{WRAPPER}} .wkfe-site-social .click-handler' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );
			// icon padding 
			$this->add_responsive_control(
                'site_social_icon_padding',
                [
                    'label' => esc_html__( 'Padding', 'widgetkit-pro' ),
                    'type'  => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors'  => [
                        '{{WRAPPER}} .wkfe-site-social .click-handler' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'separator' => 'before'
                ]
            );
			// icon margin 
			$this->add_responsive_control(
                'site_social_icon_margin',
                [
                    'label' => esc_html__( 'Margin', 'widgetkit-pro' ),
                    'type'  => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors'  => [
                        '{{WRAPPER}} .wkfe-site-social .click-handler' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );
		$this->end_controls_section();
#	end region site_social icon style

#	region site_social form style
		$this->start_controls_section(
			'site_social_form_style',
			[
				'label' => esc_html__( 'Social Platforms', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
			$this->add_control(
				'site_social_platforms_icon_size',
				[
					'label'   => esc_html__( 'Size', 'widgetkit-for-elementor' ),
					'type'    => Controls_Manager::SLIDER,
					'default' => [
						'size' =>16,
					],
					'range'  => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .wkfe-site-social .social-platforms a' => 'font-size: {{SIZE}}{{UNIT}};',
					],
				]
			);
			// color 
			$this->add_control(
				'site_social_platform_icon_color',
				[
					'label' => __( 'Color', 'widgetkit-pro' ),
					'type' => Controls_Manager::COLOR,
					'default' => "#131418",
					'selectors' => [
						'{{WRAPPER}} .wkfe-site-social .wkfe-site-social-platform-wrapper a' => 'color: {{VALUE}} !important',
					],
				]
			);
			// background
			$this->add_control(
				'site_social_platform_background_color',
				[
					'label' => __( 'Background', 'widgetkit-pro' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .wkfe-site-social .wkfe-site-social-platform-wrapper' => 'background-color: {{VALUE}}',
					],
				]
			);
			// icon margin between
			$this->add_responsive_control(
                'site_social_platform_icon_gap',
                [
                    'label' => esc_html__( 'Icon Gap', 'widgetkit-pro' ),
					'type'  => Controls_Manager::DIMENSIONS,
					'allowed_dimensions' => 'horizontal',
                    'size_units' => [ 'px', '%' ],
                    'selectors'  => [
                        '{{WRAPPER}} .wkfe-site-social .wkfe-site-social-platform-wrapper a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'separator' => 'before'
                ]
            );
			// padding
			$this->add_responsive_control(
                'site_social_platform_padding',
                [
                    'label' => esc_html__( 'Padding', 'widgetkit-pro' ),
                    'type'  => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors'  => [
                        '{{WRAPPER}} .wkfe-site-social .wkfe-site-social-platform-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'separator' => 'before'
                ]
            );
			// border 
			$this->add_group_control(
                Group_Control_Border::get_type(), 
                [
                    'name'          => 'site_social_platform_border',
                    'selector'      => '{{WRAPPER}} .wkfe-site-social .wkfe-site-social-platform-wrapper',
                ]
			);
			// optional
			$this->add_control(
                'site_social_form_platform_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'widgetkit-pro' ),
                    'type'  => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px' ],
                    'selectors'  => [
                        '{{WRAPPER}}  .wkfe-site-social .wkfe-site-social-platform-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
			);
		$this->end_controls_section();
#	end region site_social form block style

	}

	protected function render() {
		require WK_PATH . '/elements/site-social/template/view.php';
	}


}
