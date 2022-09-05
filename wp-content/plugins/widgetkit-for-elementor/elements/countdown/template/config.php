<?php


use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
Use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor WidgetKit social share 1
 *
 * Elementor widget for WidgetKit social share 1
 *
 * @since 1.0.0
 */
class wkfe_countdown extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-countdown';
	} 

	public function get_title() {
		return esc_html__( 'Countdown', 'widgetkit-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-countdown wk-icon';
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
        ];
    }
	/**
	 * A list of scripts that the widgets is depended in
	 **/
	public function get_script_depends() {
		return [ 
			'countdown',
			'widgetkit-main',
		 ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'widgetkit_countdown_global_settings',
			[
				'label'		=> esc_html__( 'Content', 'widgetkit-for-elementor' )
			]
		);

		$this->add_control(
			'widgetkit_countdown_style',
		  	[
		     	'label'			=> esc_html__( 'Style', 'widgetkit-for-elementor' ),
		     	'type' 			=> Controls_Manager::SELECT,
		     	'options' 		=> [
		     		'd-u-s' => esc_html__( 'Horizontal', 'widgetkit-for-elementor' ),
		     		'd-u-u' => esc_html__( 'Verticle', 'widgetkit-for-elementor' ),
		     	],
		     	'default'		=> 'd-u-s'
		  	]
		);

		$this->add_control(
			'widgetkit_countdown_date_time',
		  	[
		     	'label'			=> esc_html__( 'Set Your Date', 'widgetkit-for-elementor' ),
		     	'type' 			=> Controls_Manager::DATE_TIME,
		     	'picker_options'	=> [
		     		'format' => 'Ym/d H:m:s'
		     	],
		     	'default' => date( "Y/m/d H:m:s", strtotime("+ 1 Day") ),
				'description' => esc_html__( 'Date format is (yyyy/mm/dd). Time format is (hh:mm:ss). Example: 2020-01-01 09:30.', 'widgetkit-for-elementor' )
		  	]
		);

		$this->add_control(
			'widgetkit_countdown_s_u_time',
			[
				'label' 		=> esc_html__( 'Timer Depends On', 'widgetkit-for-elementor' ),
				'type' 			=> Controls_Manager::SELECT,
				'options' 		=> [
					'wp-time'			=> esc_html__('Default', 'widgetkit-for-elementor' ),
					'user-time'			=> esc_html__('Local', 'widgetkit-for-elementor' )
				],
				'default'		=> 'wp-time',
				'description'	=> esc_html__('This will set the current time of the option that you will choose.', 'widgetkit-for-elementor')
			]
		);

		$this->add_control(
			'widgetkit_countdown_units',
		  	[
		     	'label'			=> esc_html__( 'Add Units', 'widgetkit-for-elementor' ),
		     	'type' 			=> Controls_Manager::SELECT2,
				'description' => esc_html__('Select the time units that you want to display in countdown timer.', 'widgetkit-for-elementor' ),
				'options'		=> [
					'Y'     => esc_html__( 'Years', 'widgetkit-for-elementor' ),
					'O'     => esc_html__( 'Month', 'widgetkit-for-elementor' ),
					'W'     => esc_html__( 'Week', 'widgetkit-for-elementor' ),
					'D'     => esc_html__( 'Day', 'widgetkit-for-elementor' ),
					'H'     => esc_html__( 'Hours', 'widgetkit-for-elementor' ),
					'M'     => esc_html__( 'Minutes', 'widgetkit-for-elementor' ),
					'S' 	=> esc_html__( 'Second', 'widgetkit-for-elementor' ),
				],
				'default' 		=> [
					'D',
					'H',
					'M',
					'S'
				],
				'multiple'		=> true,
				'separator'		=> 'after'
		  	]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'widgetkit_countdown_on_expire_settings',
			[
				'label' => esc_html__( 'Expire Text' , 'widgetkit-for-elementor' )
			]
		);

		$this->add_control(
			'widgetkit_countdown_expiry_text_',
			[
				'label'			=> esc_html__('On expiry Text', 'widgetkit-for-elementor'),
				'type'			=> Controls_Manager::WYSIWYG,
				'default'		=> esc_html__('Date is over', 'widgetkit-for-elementor'),
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'widgetkit_countdown_transaltion',
			[
				'label' => esc_html__( 'Formate' , 'widgetkit-for-elementor' )
			]
		);

		$this->add_control(
			'widgetkit_countdown_day_singular',
		  	[
		     	'label'			=> esc_html__( 'Day (Singular)', 'widgetkit-for-elementor' ),
		     	'type' 			=> Controls_Manager::TEXT,
		     	'default'		=> 'Day'
		  	]
		);

		$this->add_control(
			'widgetkit_countdown_day_plural',
		  	[
		     	'label'			=> esc_html__( 'Day (Plural)', 'widgetkit-for-elementor' ),
		     	'type' 			=> Controls_Manager::TEXT,
		     	'default'		=> 'Days'
		  	]
		);

		$this->add_control(
			'widgetkit_countdown_week_singular',
		  	[
		     	'label'			=> esc_html__( 'Week (Singular)', 'widgetkit-for-elementor' ),
		     	'type' 			=> Controls_Manager::TEXT,
		     	'default'		=> 'Week'
		  	]
		);

		$this->add_control(
			'widgetkit_countdown_week_plural',
		  	[
		     	'label'			=> esc_html__( 'Weeks (Plural)', 'widgetkit-for-elementor' ),
		     	'type' 			=> Controls_Manager::TEXT,
		     	'default'		=> 'Weeks'
		  	]
		);


		$this->add_control(
			'widgetkit_countdown_month_singular',
		  	[
		     	'label'			=> esc_html__( 'Month (Singular)', 'widgetkit-for-elementor' ),
		     	'type' 			=> Controls_Manager::TEXT,
		     	'default'		=> 'Month'
		  	]
		);


		$this->add_control(
			'widgetkit_countdown_month_plural',
		  	[
		     	'label'			=> esc_html__( 'Months (Plural)', 'widgetkit-for-elementor' ),
		     	'type' 			=> Controls_Manager::TEXT,
		     	'default'		=> 'Months'
		  	]
		);


		$this->add_control(
			'widgetkit_countdown_year_singular',
		  	[
		     	'label'			=> esc_html__( 'Year (Singular)', 'widgetkit-for-elementor' ),
		     	'type' 			=> Controls_Manager::TEXT,
		     	'default'		=> 'Year'
		  	]
		);


		$this->add_control(
			'widgetkit_countdown_year_plural',
		  	[
		     	'label'			=> esc_html__( 'Years (Plural)', 'widgetkit-for-elementor' ),
		     	'type' 			=> Controls_Manager::TEXT,
		     	'default'		=> 'Years'
		  	]
		);


		$this->add_control(
			'widgetkit_countdown_hour_singular',
		  	[
		     	'label'			=> esc_html__( 'Hour (Singular)', 'widgetkit-for-elementor' ),
		     	'type' 			=> Controls_Manager::TEXT,
		     	'default'		=> 'Hour'
		  	]
		);


		$this->add_control(
			'widgetkit_countdown_hour_plural',
		  	[
		     	'label'			=> esc_html__( 'Hours (Plural)', 'widgetkit-for-elementor' ),
		     	'type' 			=> Controls_Manager::TEXT,
		     	'default'		=> 'Hours'
		  	]
		);


		$this->add_control(
			'widgetkit_countdown_minute_singular',
		  	[
		     	'label'			=> esc_html__( 'Minute (Singular)', 'widgetkit-for-elementor' ),
		     	'type' 			=> Controls_Manager::TEXT,
		     	'default'		=> 'Minute'
		  	]
		);

		$this->add_control(
			'widgetkit_countdown_minute_plural',
		  	[
		     	'label'			=> esc_html__( 'Minutes (Plural)', 'widgetkit-for-elementor' ),
		     	'type' 			=> Controls_Manager::TEXT,
		     	'default'		=> 'Minutes'
		  	]
		);

        $this->add_control(
			'widgetkit_countdown_second_singular',
		  	[
		     	'label'			=> esc_html__( 'Second (Singular)', 'widgetkit-for-elementor' ),
		     	'type' 			=> Controls_Manager::TEXT,
		     	'default'		=> 'Second',
		  	]
		);
        
		$this->add_control(
			'widgetkit_countdown_second_plural',
		  	[
		     	'label'			=> esc_html__( 'Seconds (Plural)', 'widgetkit-for-elementor' ),
		     	'type' 			=> Controls_Manager::TEXT,
		     	'default'		=> 'Seconds'
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
        	'widgetkit_countdown_layout_style', 
            [
                'label'         => esc_html__('Layout', 'widgetkit-for-elementor'),
                'tab'           => Controls_Manager::TAB_STYLE,
            ]
          );
        $this->add_control(
			'widgetkit_countdown_item_bg_color',
			[
				'label' 		=> esc_html__( 'Item Bg Color', 'widgetkit-for-elementor' ),
				'type' 			=> Controls_Manager::COLOR,
				'selectors'		=> [
					'{{WRAPPER}} .widgetkit-countdown .widgetkit_countdown-section' => 'background-color: {{VALUE}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'    => 'image_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .widgetkit-countdown .widgetkit_countdown-section',
			]
		);


        $this->add_group_control(
            Group_Control_Border::get_type(),
                [
                    'name'          => 'widgetkit_countdown_digits_border',
                    'selector'      => '{{WRAPPER}} .widgetkit-countdown .widgetkit_countdown-section',
                ]
        );


        $this->add_control('widgetkit_countdown_digit_border_radius',
                [
                    'label'         => esc_html__('Border Radius', 'widgetkit-for-elementor'),
                    'type'          => Controls_Manager::SLIDER,
                    'size_units'    => ['px', '%', 'em'],
                    'selectors'     => [
                        '{{WRAPPER}} .widgetkit-countdown .widgetkit_countdown-section' => 'border-radius: {{SIZE}}{{UNIT}};'
                        ]
                    ]
                );


		$this->add_control(
            'widgetkit_countdown_separator_width',
            [
                'label' => esc_html__( 'Separator Spacing', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .widgetkit-countdown .widgetkit_countdown-section' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->add_responsive_control(
            'widgetkit_countdown_item_alignment',
            [
                'label' => esc_html__( 'Alignment', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::CHOOSE,
                'label_block' => false,
                'default' => esc_html__( 'center', 'widgetkit-for-elementor' ),
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
                   	'justify' => [
						'title' => __( 'Justified', 'widgetkit-for-elementor' ),
						'icon' => 'fa fa-align-justify',
					],
                ],
                'selectors' => [
                    '{{WRAPPER}}  .widgetkit_countdown-row' => 'text-align: {{VALUE}}',
                ],
            ]
        );
        

		$this->end_controls_section();

		$this->start_controls_section(
			'widgetkit_countdown_typhography',
			[
				'label' => esc_html__( 'Number' , 'widgetkit-for-elementor' ),
				'tab' 			=> Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'widgetkit_countdown_digit_color',
			[
				'label' 		=> esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type' 			=> Controls_Manager::COLOR,
				'selectors'		=> [
					'{{WRAPPER}} .widgetkit-countdown .widgetkit_countdown-section .widgetkit_countdown-amount' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'widgetkit_countdown_digit_typo',
				'scheme'   => Typography::TYPOGRAPHY_3,
				'label'    => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
				'selector' => '{{WRAPPER}} .widgetkit-countdown .widgetkit_countdown-section .widgetkit_countdown-amount',
				'separator'		=> 'after'
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'number_text_shadow',
				'selector' => '{{WRAPPER}} .widgetkit-countdown .widgetkit_countdown-section .widgetkit_countdown-amount',
			]
		);
        
        
        $this->add_control(
			'widgetkit_countdown_timer_digit_bg_color',
			[
				'label' 		=> esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
				'type' 			=> Controls_Manager::COLOR,
				'selectors'		=> [
					'{{WRAPPER}} .widgetkit-countdown .widgetkit_countdown-section .widgetkit_countdown-amount' => 'background-color: {{VALUE}};'
				]
			]
		);
        

			$this->add_control(
	            'widgetkit_countdown_digit_bg_size',
	            [
	                'label' => esc_html__( 'Padding', 'widgetkit-for-elementor' ),
	                'type'  => Controls_Manager::DIMENSIONS,
	                'size_units' => [ 'px', '%' ],
	                'selectors'  => [
	                    '{{WRAPPER}} .widgetkit-countdown .widgetkit_countdown-section .widgetkit_countdown-amount' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );


        
        $this->end_controls_section();
        
        $this->start_controls_section('widgetkit_countdown_unit_style', 
            [
                'label'         => esc_html__('Title', 'widgetkit-for-elementor'),
                'tab'           => Controls_Manager::TAB_STYLE,
            ]
            );

        $this->add_control(
			'widgetkit_countdown_unit_color',
			[
				'label' 		=> esc_html__( ' Color', 'widgetkit-for-elementor' ),
				'type' 			=> Controls_Manager::COLOR,
				'selectors'		=> [
					'{{WRAPPER}} .widgetkit-countdown .widgetkit_countdown-section .widgetkit_countdown-period' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'widgetkit_countdown_unit_typo',
				'scheme' => Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} .widgetkit-countdown .widgetkit_countdown-section .widgetkit_countdown-period',
				'separator'		=> 'after'
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow',
				'selector' => '{{WRAPPER}} .widgetkit-countdown .widgetkit_countdown-section .widgetkit_countdown-period',
			]
		);

		$this->add_control(
			'widgetkit_countdown_unit_bg_color',
			[
				'label' 		=> esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
				'type' 			=> Controls_Manager::COLOR,
				'selectors'		=> [
					'{{WRAPPER}} .widgetkit-countdown .widgetkit_countdown-section .widgetkit_countdown-period' => 'background-color: {{VALUE}};'
				]
			]
		);

		$this->add_control('widgetkit_countdown_title_position',
                [
                    'label'         => esc_html__('Position', 'widgetkit-for-elementor'),
                    'type'          => Controls_Manager::SLIDER,
                    'default' 		=> [
					'size' => 36,
					],
					'range' 		=> [
						'px' 	=> [
							'min' => 0,
							'max' => 200,
						]
					],
                    'selectors'     => [
                        '{{WRAPPER}} .widgetkit-countdown .widgetkit_countdown-section .widgetkit_countdown-period' => 'margin: {{SIZE}}{{UNIT}} 0;'
                        ]
                    ]
                );


			$this->add_control(
	            'widgetkit_countdown_title_padding',
	            [
	                'label' => esc_html__( 'Padding', 'widgetkit-for-elementor' ),
	                'type'  => Controls_Manager::DIMENSIONS,
	                'size_units' => [ 'px', '%' ],
	                'selectors'  => [
	                    '{{WRAPPER}} .widgetkit-countdown .widgetkit_countdown-section .widgetkit_countdown-period' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );


		$this->end_controls_section();
	}

	protected function render() {
		require WK_PATH . '/elements/countdown/template/view.php';
	}


}
