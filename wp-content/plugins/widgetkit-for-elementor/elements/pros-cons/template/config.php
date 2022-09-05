<?php

use Elementor\Group_Control_Border;
use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
Use Elementor\Core\Schemes\Typography;

if ( ! defined( 'ABSPATH' ) ) exit;

class WKFE_Feature_List_Config extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-pros-cons';
	}

	public function get_title() {
		return esc_html__( 'Pros & Cons', 'widgetkit-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-editor-list-ul wk-icon';
	}

	public function get_categories() {
		return [ 'widgetkit_elementor' ];
	}

	/**
	 * A list of stylesheet that 
	 * theis widget depends
	 **/
	public function get_style_depends() {
        return [
            'widgetkit_bs',
            'widgetkit_main',
            'animate-css',
        ];
    }
	/**
	 * A list of scripts that 
	 * this widget depends
	 **/
	public function get_script_depends() {
		return [ 
			'widgetkit-main',
		 ];
	}

	protected function _register_controls() {

	$this->start_controls_section(
		'heading_layout', 
		[
			'label' => esc_html__('Layout', 'widgetkit-for-elementor')
		]
	);
		$this->add_control(
			'layout_align',
			[
				'label' => esc_html__( 'Alignment', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options' => [
					'left'    => [
						'title' => esc_html__( 'Left', 'widgetkit-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'widgetkit-for-elementor' ),
						'icon'  => 'eicon-v-align-bottom',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'widgetkit-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
			]
		);
		$this->add_responsive_control(
			'title_icon_width',
			[
				'label'   => esc_html__( 'Icon Width', 'widgetkit-for-elementor' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' =>50,
				],
				'range'  => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .wkfe-feature-list h2.title span.icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'layout_align' => ['center','right'],
				],
			]
		);
		$this->add_responsive_control(
			'title_icon_radius',
			[
				'label'   => esc_html__( 'Icon Width', 'widgetkit-for-elementor' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' =>50,
				],
				'range'  => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .wkfe-feature-list h2.title span.icon' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'layout_align' => ['center','right'],
				],
			]
		);
	$this->end_controls_section();
	
	$this->start_controls_section(
		'section_feature_content',
		[
			'label' => esc_html__( 'Feature', 'widgetkit-for-elementor' ),
		]
	);

		$this->add_control(
		    'feature_title',
		      	[
		          'label' => esc_html__( 'Title', 'widgetkit-for-elementor' ),
		          'type'  => Controls_Manager::TEXT,
		          'default' => esc_html__( 'Pros', 'widgetkit-for-elementor' ),
		    	]
		);
		$this->add_control(
		    'feature_icon',
		      	[
		          'label' => esc_html__( 'Icon', 'widgetkit-for-elementor' ),
		          'type'  => Controls_Manager::ICON,
		          'default' => esc_html__( 'fa fa-thumbs-up', 'widgetkit-for-elementor' ),
		    	]
	    );
		/**
		 * single feature repeater
		 */
        $repeater = new Repeater();
        $repeater->add_control(
            'single_feature_input',
            [
                'label'   => esc_html__( 'Feature', 'widgetkit-for-elementor' ),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__( 'Feature', 'widgetkit-for-elementor' ),
            ]
		);
		$repeater->add_control(
            'single_feature_icon',
            [
                'label'   => esc_html__( 'Icon', 'widgetkit-for-elementor' ),
                'type'    => Controls_Manager::ICON,
                'default' => esc_html__( 'fa fa-check', 'widgetkit-for-elementor' ),
            ]
        );
        $this->add_control(
            'feature_lists',
            [
                'type'    => Controls_Manager::REPEATER,
                'fields'  => $repeater->get_controls(),
                'default' => [
                    [
                        'single_feature_input' => esc_html__( 'This is my awesome feature.', 'widgetkit-for-elementor' ),
                        'single_feature_icon' => esc_html__( 'fa fa-check', 'widgetkit-for-elementor' ),
                    ],
                    [
                        'single_feature_input' => esc_html__( 'My product feature is beautiful.', 'widgetkit-for-elementor' ),
                        'single_feature_icon' => esc_html__( 'fa fa-check', 'widgetkit-for-elementor' ),
					],
					[
                        'single_feature_input' => esc_html__( 'It will serve you whatever you want.', 'widgetkit-for-elementor' ),
                        'single_feature_icon' => esc_html__( 'fa fa-check', 'widgetkit-for-elementor' ),
					],
					[
                        'single_feature_input' => esc_html__( 'My product is developed based on market niche.', 'widgetkit-for-elementor' ),
                        'single_feature_icon' => esc_html__( 'fa fa-check', 'widgetkit-for-elementor' ),
					],
					[
                        'single_feature_input' => esc_html__( 'I am very proud of my product.', 'widgetkit-for-elementor' ),
                        'single_feature_icon' => esc_html__( 'fa fa-check', 'widgetkit-for-elementor' ),
                    ],
                    
                ],
                'title_field' => '{{{ single_feature_input }}}',
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

	
	/**
	 * Style tab
	 * =============
	 */


	/**
	 * Feature title style
	 * -------------------
	 */
	$this->start_controls_section(
		'style_title', 
		[
			'label' => esc_html__('Title', 'widgetkit-for-elementor'),
			'tab' => Controls_Manager::TAB_STYLE,
		]
	);
	$this->add_group_control(
		Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Font', 'widgetkit-for-elementor' ),
				'scheme'   => Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .wkfe-feature-list h2.title span',
			]
	);
	$this->add_control(
		'title_color',
		[
			'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#333',
			'selectors' => [
				'{{WRAPPER}} .wkfe-feature-list h2.title span' => 'color: {{VALUE}};',
			],
		]
	);
	
	$this->add_control(
		'title_background',
		[
			'label'     => esc_html__( 'Background', 'widgetkit-for-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#f5f5f5',
			'selectors' => [
				'{{WRAPPER}} .wkfe-feature-list h2.title' => 'background-color: {{VALUE}};',
			],
			'condition' => [
				'layout_align' => ['left', 'right'],
			]
		]
	);

	$this->add_control(
		'title_background_center',
		[
			'label'     => esc_html__( 'Background', 'widgetkit-for-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#f5f5f5',
			'selectors' => [
				'{{WRAPPER}} .wkfe-feature-list h2.title span.title-text' => 'display:block; background-color: {{VALUE}};',
			],
			'condition' => [
				'layout_align' => ['center'],
			]
		]
	);
	$this->add_control(
		'title_box_bg',
		[
			'label'     => esc_html__( 'Box Background', 'widgetkit-for-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#f5f5f5',
			'selectors' => [
				'{{WRAPPER}} .wkfe-feature-list h2.title' => 'background-color: {{VALUE}};',
			],
			'condition' => [
				'layout_align' => ['center'],
			]
		]
	);

	$this->add_responsive_control(
		'title_padding',
		[
			'label' => esc_html__( 'Title Padding', 'widgetkit-for-elementor' ),
			'type'  => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				'{{WRAPPER}} .wkfe-feature-list h2.title span.title-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'condition' => [
				'layout_align' => ['left', 'right'],
			]
		]
	);
	$this->add_responsive_control(
		'title_center_padding',
		[
			'label' => esc_html__( 'Title Padding', 'widgetkit-for-elementor' ),
			'type'  => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				'{{WRAPPER}} .wkfe-feature-list h2.title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'default' => [
				'top' => '15',
				'right' => '15',
				'bottom' => '0',
				'left' => '15',
				'unit' => 'px',
				'isLinked' => false,
			],
			'condition' => [
				'layout_align' => ['center'],
			]
		]
	);
	
	$this->add_control(
		'title_border_color',
		[
			'label'     => esc_html__( 'Border Color', 'widgetkit-for-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ddd',
			'selectors' => [
				'{{WRAPPER}} .wkfe-feature-list h2.title' => 'border-color: {{VALUE}};',
			],
		]
	);
	$this->add_responsive_control(
		'title_border_size',
		[
			'label'   => esc_html__( 'Border Size', 'widgetkit-for-elementor' ),
			'type'    => Controls_Manager::SLIDER,
			'default' => [
				'size' =>1,
			],
			'range'  => [
				'px' => [
					'min' => 0,
					'max' => 50,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .wkfe-feature-list h2.title' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
			],
		]
	);
	$this->add_control(
		'hr',
		[
			'type' => \Elementor\Controls_Manager::DIVIDER,
		]
	);
	$this->add_responsive_control(
		'title_icon_size',
		[
			'label'   => esc_html__( 'Icon Size', 'widgetkit-for-elementor' ),
			'type'    => Controls_Manager::SLIDER,
			'default' => [
				'size' =>20,
			],
			'range'  => [
				'px' => [
					'min' => 10,
					'max' => 200,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .wkfe-feature-list h2.title i' => 'font-size: {{SIZE}}{{UNIT}};',
			],
		]
	);
	$this->add_control(
		'title_icon_color',
		[
			'label'     => esc_html__( 'Icon Color', 'widgetkit-for-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#fff',
			'selectors' => [
				'{{WRAPPER}} .wkfe-feature-list h2.title span.icon' => 'color: {{VALUE}};',
			],
		]
	);
	$this->add_control(
		'title_icon_bg_color',
		[
			'label'     => esc_html__( 'Icon Background', 'widgetkit-for-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#23a455',
			'selectors' => [
				'{{WRAPPER}} .wkfe-feature-list h2.title span.icon' => 'background-color: {{VALUE}};',
				'{{WRAPPER}} .wkfe-feature-list h2.title span.icon:after' => 'border-left-color: {{VALUE}};',
			],
		]
	);
	$this->add_responsive_control(
		'title_icon_spacing',
		[
			'label'   => esc_html__( 'Icon Spacing', 'widgetkit-for-elementor' ),
			'type'    => Controls_Manager::SLIDER,
			'default' => [
				'size' =>10,
			],
			'range'  => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .wkfe-feature-list h2.title span.icon' => 'margin-right: {{SIZE}}{{UNIT}};',
			],
			'condition' => [
				'layout_align' => ['left', 'right'],
			]
		]
	);
	
	$this->add_responsive_control(
		'title_icon_center_spacing',
		[
			'label'   => esc_html__( 'Icon Spacing', 'widgetkit-for-elementor' ),
			'type'    => Controls_Manager::SLIDER,
			'default' => [
				'size' => 0,
			],
			'range'  => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .wkfe-feature-list h2.title span.icon' => 'margin-bottom: {{SIZE}}{{UNIT}};',
			],
			'condition' => [
				'layout_align' => ['center'],
			]
		]
	);

	$this->add_responsive_control(
		'title_icon_padding',
		[
			'label' => esc_html__( 'Icon Padding', 'widgetkit-for-elementor' ),
			'type'  => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				'{{WRAPPER}} .wkfe-feature-list h2.title span.icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'separator' => 'before',
		]
	);
	$this->end_controls_section();

	/**
	 * Feature list style
	 * ------------------
	 */
	$this->start_controls_section(
		'style_feature', 
		[
			'label' => esc_html__('Feature', 'widgetkit-for-elementor'),
			'tab' => Controls_Manager::TAB_STYLE,
		]
	);
	$this->add_group_control(
		Group_Control_Typography::get_type(),
			[
				'name'     => 'feature_typography',
				'label'    => esc_html__( 'Font', 'widgetkit-for-elementor' ),
				'scheme'   => Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .wkfe-feature-list li span',
			]
	);
	$this->add_control(
		'feature_color',
		[
			'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#333',
			'selectors' => [
				'{{WRAPPER}} .wkfe-feature-list li span' => 'color: {{VALUE}};',
			],
		]
	);
	$this->add_control(
		'feature_background_color',
		[
			'label'     => esc_html__( 'Background', 'widgetkit-for-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#fff',
			'selectors' => [
				'{{WRAPPER}} .wkfe-feature-list .lists' => 'background-color: {{VALUE}};',
			],
		]
	);
	$this->add_responsive_control(
		'feature_padding',
		[
			'label' => esc_html__( 'Padding', 'widgetkit-for-elementor' ),
			'type'  => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				'{{WRAPPER}} .wkfe-feature-list .lists' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'separator' => 'before',
		]
	);
	$this->add_responsive_control(
		'feature_icon_size',
		[
			'label'   => esc_html__( 'Icon Size', 'widgetkit-for-elementor' ),
			'type'    => Controls_Manager::SLIDER,
			'default' => [
				'size' =>16,
			],
			'range'  => [
				'px' => [
					'min' => 10,
					'max' => 50,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .wkfe-feature-list li i' => 'font-size: {{SIZE}}{{UNIT}};',
			],
		]
	);
	$this->add_control(
		'feature_icon_color',
		[
			'label'     => esc_html__( 'Icon Color', 'widgetkit-for-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#23a455',
			'selectors' => [
				'{{WRAPPER}} .wkfe-feature-list li i' => 'color: {{VALUE}};',
			],
		]
	);
	$this->add_responsive_control(
		'feature_icon_spacing',
		[
			'label'   => esc_html__( 'Icon Spacing', 'widgetkit-for-elementor' ),
			'type'    => Controls_Manager::SLIDER,
			'default' => [
				'size' =>5,
			],
			'range'  => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .wkfe-feature-list li i' => 'margin-right: {{SIZE}}{{UNIT}};',
			],
		]
	);
	$this->end_controls_section();

	/**
	 * box style
	 */
	$this->start_controls_section(
		'box', 
		[
			'label' => esc_html__('Box', 'widgetkit-for-elementor'),
			'tab' => Controls_Manager::TAB_STYLE,
		]
	);
	$this->add_group_control(
		Group_Control_Border::get_type(),
		[
			'name' => 'border',
			'label' => __( 'Border', 'widgetkit-for-elementor' ),
			'selector' => '{{WRAPPER}} .wkfe-feature-list .column'
		]
	);
	$this->end_controls_section();

	}

	protected function render() {
		require WK_PATH . '/elements/pros-cons/template/view.php';
	}


}
