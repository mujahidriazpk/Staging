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
class wkfe_search extends Widget_Base {

	public function get_name() {
		return 'widgetkit-pro-search';
	}

	public function get_title() {
		return esc_html__( 'Search', 'widgetkit-pro' );
	}

	public function get_icon() {
		return 'eicon-search wk-icon';
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
			'label' => esc_html__( 'Icon', 'widgetkit-pro' ),
		]
	);
		$this->add_control(
			'search_icon_for_handler',
			[
				'label' => esc_html__( 'Icon', 'widgetkit-pro' ),
				'type'              => Controls_Manager::ICONS,
				'default'    =>  [
					'value'     => 'fa fa-search',
					'library'   => 'fa-regular',
				],
				'label_block'   => true,
			]
		);

		$this->add_responsive_control(
			'search_icon_alignment',
			[
				'label' => esc_html__( 'Alignment', 'widgetkit-pro' ),
				'type'  => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options' => [
					'left'    => [
						'title' => esc_html__( 'Left', 'widgetkit-pro' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'widgetkit-pro' ),
						'icon'  => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'widgetkit-pro' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .wkfe-search .wkfe-search-wrapper' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'search_form_position',
			[
				'label' => esc_html__( 'Search Form Position', 'widgetkit-pro' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'allowed_dimensions' => 'vertical',
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .wkfe-search .wkfe-search-wrapper .wkfe-search-form-wrapper.active' => 'top: {{TOP}}{{UNIT}}; bottom:{{BOTTOM}}{{UNIT}}; ',
					'{{WRAPPER}} .wkfe-search .wkfe-search-wrapper .wkfe-search-form-wrapper' => 'top: 20px; bottom:{{BOTTOM}}{{UNIT}};',
				],
			]
		);
	$this->end_controls_section();
# 	end postion region 

# 	start region search form config
	$this->start_controls_section(
		'section_text_search_form',
		[
			'label' => esc_html__( 'Search Form', 'widgetkit-pro' ),
		]
	);
		$this->add_control(
			'search_form_input_placeholder',
				[
					'label' => esc_html__( 'Input Placeholder', 'widgetkit-pro' ),
					'type'  => Controls_Manager::TEXT,
					'default' => esc_html__( 'Search here', 'widgetkit-pro' ),
				]
		);
		$this->add_control(
			'search_form_input_button_text',
				[
					'label' => esc_html__( 'Button', 'widgetkit-pro' ),
					'type'  => Controls_Manager::TEXT,
					'default' => esc_html__( 'Search', 'widgetkit-pro' ),
				]
		);
	$this->end_controls_section();

#	end region search form config

#	start pro config for free user
	/**
	 * Pro control panel 
	 */
	if(!apply_filters('wkpro_enabled', false)):
		$this->start_controls_section(
			'section_widgetkit_pro_box',
			[
				'label' => esc_html__( 'Go Premium for more layout & feature', 'widgetkit-pro' ),
			]
		);
			$this->add_control(
				'wkfe_control_go_pro',
				[
					'label' => __('Unlock more possibilities', 'widgetkit-pro'),
					'type' => Controls_Manager::CHOOSE,
					'default' => '1',
					'description' => '<div class="elementor-nerd-box">
					<div class="elementor-nerd-box-message"> Get the  <a href="https://themesgrove.com/widgetkit-pro/" target="_blank">Pro version</a> of <a href="https://themesgrove.com/widgetkit-pro/" target="_blank">WidgetKit</a> for more stunning elements and customization options.</div>
					<a class="widgetkit-go-pro elementor-nerd-box-link elementor-button elementor-button-default elementor-go-pro" href="https://themesgrove.com/widgetkit-pro/" target="_blank">Go Pro</a>
					</div>',
				]
			);
		$this->end_controls_section();
	endif;
#	end region for pro config

#	start region search icon style
	// Content options End
		$this->start_controls_section(
			'section_search_icon_layout',
			[
				'label' => esc_html__( 'Icon', 'widgetkit-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
			$this->add_control(
				'search_handler_icon_size',
				[
					'label'   => esc_html__( 'Size', 'widgetkit-pro' ),
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
						'{{WRAPPER}} .wkfe-search .search-click-handler' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .wkfe-search .search-click-handler svg' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);
			/**
             * tabs normal
             */
            $this->start_controls_tabs( 'search_icon_style__tab' );
                /**
                 * Normal style
                 */
                $this->start_controls_tab(
                    'search_icon_style_normal',
                    [
                        'label' => __('Normal', 'widgetkit-pro'),
                    ]
                );
					$this->add_control(
						'search_icon_color',
						[
							'label' => __( 'Color', 'widgetkit-pro' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .wkfe-search .click-handler' => 'color: {{VALUE}}',
							],
						]
					);
					$this->add_control(
						'search_icon_background_color',
						[
							'label' => __( 'Background', 'widgetkit-pro' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .wkfe-search .click-handler' => 'background-color: {{VALUE}}',
							],
						]
					);
                $this->end_controls_tab();
                /**
                 * Hover Style
                 */
                $this->start_controls_tab(
                    'search_icon_style_hover',
                    [
                        'label' => __('Hover/Active', 'widgetkit-pro'),
                    ]
                );
					$this->add_control(
						'search_icon_hover_color',
						[
							'label' => __( 'Icon', 'widgetkit-pro' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .wkfe-search .click-handler:hover' => 'color: {{VALUE}}',
								'{{WRAPPER}} .wkfe-search .click-handler.active' => 'color: {{VALUE}}',
							],
						]
					);
					$this->add_control(
						'search_icon_hover_background_color',
						[
							'label' => __( 'Background', 'widgetkit-pro' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .wkfe-search .click-handler:hover' => 'background: {{VALUE}} !important',
								'{{WRAPPER}} .wkfe-search .click-handler.active' => 'background: {{VALUE}} !important',
							],
						]
					);
                $this->end_controls_tab();
        
            $this->end_controls_tabs();

			// icon border
			$this->add_group_control(
                Group_Control_Border::get_type(), 
                [
                    'name'          => 'search_icon_border',
					'selector'      => '{{WRAPPER}} .wkfe-search .click-handler',
					'separator'		=> 'before'
                ]
			);
			// icon padding 
			$this->add_responsive_control(
                'search_icon_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'widgetkit-pro' ),
                    'type'  => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors'  => [
                        '{{WRAPPER}} .wkfe-search .click-handler' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );
			// icon padding 
			$this->add_responsive_control(
                'search_icon_padding',
                [
                    'label' => esc_html__( 'Padding', 'widgetkit-pro' ),
                    'type'  => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors'  => [
                        '{{WRAPPER}} .wkfe-search .click-handler' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'separator' => 'before'
                ]
            );
			// icon margin 
			$this->add_responsive_control(
                'search_icon_margin',
                [
                    'label' => esc_html__( 'Margin', 'widgetkit-pro' ),
                    'type'  => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors'  => [
                        '{{WRAPPER}} .wkfe-search .click-handler' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );
		$this->end_controls_section();
#	end region search icon style

#	region search form style
		$this->start_controls_section(
			'search_form_style',
			[
				'label' => esc_html__( 'Search Form', 'widgetkit-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
			$this->add_control(
				'search_box_hover_background_color',
				[
					'label' => __( 'Background', 'widgetkit-pro' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .wkfe-search .wkfe-search-form-wrapper' => 'background: {{VALUE}} !important',
					],
				]
			);
			$this->add_group_control(
                Group_Control_Border::get_type(), 
                [
                    'name'          => 'search_box_border',
                    'selector'      => '{{WRAPPER}} .wkfe-search .wkfe-search-form-wrapper',
                ]
			);
			// icon padding 
			$this->add_responsive_control(
                'search_box_padding',
                [
                    'label' => esc_html__( 'Padding', 'widgetkit-pro' ),
                    'type'  => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors'  => [
                        '{{WRAPPER}} .wkfe-search .wkfe-search-form-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
                ]
            );
			/**
             * Input
             */
            $this->add_control(
                'search_form_input_style_header',
                [
                'label' => __( 'Input', 'widgetkit-pro' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                ]
			);
				$this->add_control(
					'search_form_input_color',
					[
						'label' => __( 'Color', 'widgetkit-pro' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .wkfe-search .wkfe-search-form-wrapper input[type="text"]' => 'color: {{VALUE}}',
						],
					]
				);
				$this->add_control(
					'search_form_input_background_color',
					[
						'label' => __( 'Background', 'widgetkit-pro' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .wkfe-search .wkfe-search-form-wrapper input[type="text"]' => 'background: {{VALUE}}',
						],
					]
				);
				$this->add_group_control(
					Group_Control_Border::get_type(), 
					[
						'name'          => 'search_input_border',
						'selector'      => '{{WRAPPER}} .wkfe-search .wkfe-search-form-wrapper input[type="text"]',
					]
				);
				$this->add_control(
					'search_form_input_border_radius',
					[
						'label' => esc_html__( 'Border Radius', 'widgetkit-pro' ),
						'type'  => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px' ],
						'selectors'  => [
							'{{WRAPPER}}  .wkfe-search .wkfe-search-form-wrapper input[type="text"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
				$this->add_responsive_control(
					'search_input_padding',
					[
						'label' => esc_html__( 'Padding', 'widgetkit-pro' ),
						'type'  => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .wkfe-search .wkfe-search-form-wrapper input[type="text"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'separator' => 'before'
					]
				);
			/**
             * button
             */
            $this->add_control(
                'search_form_button_style_heading',
                [
                'label' => __( 'Button', 'widgetkit-pro' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                ]
            );
			// button typography
			$this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'label' => __( 'Typography', 'widgetkit-pro' ),
                    'name' => 'search_button_typography',
                    'selector' => '{{WRAPPER}} .wkfe-search .wkfe-search-form-wrapper input[type="submit"]',
                ]
            );
			/**
             * tabs normal
             */
			$this->start_controls_tabs( 'search_button_style__tab' );
				/**
                 * Normal style
                 */
                $this->start_controls_tab(
                    'search_button_style_normal',
                    [
                        'label' => __('Normal', 'widgetkit-pro'),
                    ]
                );
					$this->add_control(
						'search_button_color',
						[
							'label' => __( 'Button', 'widgetkit-pro' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .wkfe-search .wkfe-search-form-wrapper input[type="submit"]' => 'color: {{VALUE}}',
							],
						]
					);
					$this->add_control(
						'search_button_background_color',
						[
							'label' => __( 'Background', 'widgetkit-pro' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .wkfe-search .wkfe-search-form-wrapper input[type="submit"]' => 'background-color: {{VALUE}}',
							],
						]
					);
				$this->end_controls_tab();
				/**
				 * Hover Style
				 */
				$this->start_controls_tab(
					'search_button_style_hover',
					[
						'label' => __('Hover', 'widgetkit-pro'),
					]
				);
					$this->add_control(
						'search_button_hover_color',
						[
							'label' => __( 'button', 'widgetkit-pro' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .wkfe-search .wkfe-search-form-wrapper input[type="submit"]:hover' => 'color: {{VALUE}}',
							],
						]
					);
					$this->add_control(
						'search_button_hover_background_color',
						[
							'label' => __( 'Background', 'widgetkit-pro' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .wkfe-search .wkfe-search-form-wrapper input[type="submit"]:hover' => 'background: {{VALUE}} !important',
							],
						]
					);
				$this->end_controls_tab();

			$this->end_controls_tabs(); 
			
			// button border 
			$this->add_group_control(
                Group_Control_Border::get_type(), 
                [
                    'name'          => 'search_form_button_border',
					'selector'      => '{{WRAPPER}} .wkfe-search .wkfe-search-form-wrapper input[type="submit"]',
					'separator'		=> 'before'
                ]
			);
			$this->add_control(
                'search_form_button_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'widgetkit-pro' ),
                    'type'  => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px' ],
                    'selectors'  => [
                        '{{WRAPPER}} .wkfe-search .wkfe-search-form-wrapper input[type="submit"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
			);
			// button padding 
			$this->add_responsive_control(
                'search_form_button_padding',
                [
                    'label' => esc_html__( 'Padding', 'widgetkit-pro' ),
                    'type'  => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors'  => [
                        '{{WRAPPER}} .wkfe-search .wkfe-search-form-wrapper input[type="submit"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'separator' => 'before'
                ]
			);
		$this->end_controls_section();
#	end region search form block style

	}

	protected function render() {
		require WK_PATH . '/elements/search/template/view.php';
	}


}
