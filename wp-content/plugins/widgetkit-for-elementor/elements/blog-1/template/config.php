<?php


use Elementor\Widget_Base;
use Elementor\Controls_Manager;
Use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor WidgetKit Blog 1
 *
 * Elementor widget for WidgetKit blog 1
 *
 * @since 1.0.0
 */
class wkfe_blog_1 extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-blog-1';
	}

	public function get_title() {
		return esc_html__( 'Blog Carousel', 'widgetkit-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-sidebar wk-icon';
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
            'widgetkit_main',
        ];
    }
	/**
	 * A list of scripts that the widgets is depended in
	 **/
	public function get_script_depends() {
		return [ 
			'owl-carousel',
			'widgetkit-main',
		 ];
	}

	protected function _register_controls() {

		$terms = get_terms( array(
            'taxonomy' => 'category',
            'hide_empty' => false,
        ) );
        $cat_names = array();
        foreach( $terms as $t ):
            $cat_names[$t->term_id] = $t->name;
        endforeach;

		
		$this->start_controls_section(
			'section_style',
				[
					'label' => esc_html__( 'Layout', 'widgetkit-for-elementor' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
	            'cat_name',
		            [
		                'label'       => __( 'From Category', 'widgetkit-for-elementor' ),
		                'type' => Controls_Manager::SELECT,
		                'default' => 'uncategorized',
		                'options' => $cat_names,
		            ]
        	);

			$this->add_control(
			'post_item_show',
				[
					'label'     => esc_html__( 'Post Shows', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '3',
					'options'   => [
						'1'     => esc_html__( 'Show 1', 'widgetkit-for-elementor' ),
						'2'     => esc_html__( 'Show 2', 'widgetkit-for-elementor' ),
						'3'     => esc_html__( 'Show 3', 'widgetkit-for-elementor' ),
					],
				]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .tgx-blog-1 .post-wrapper',
			]
		);


		$this->add_control(
			'details_bg_color',
			[
				'label'     => esc_html__( 'Details Bg Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-1 .post-wrapper .post-details' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'auto_play_enable',
				[
					'label'     => esc_html__( 'Auto Play Enable/Disable', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'disable',
					'enable'    => esc_html__( 'Enable', 'widgetkit-for-elementor' ),
					'disable'   => esc_html__( 'Disable', 'widgetkit-for-elementor' ),
				]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Title', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Title Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#404040',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-1 .post-wrapper .entry-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .tgx-blog-1 .post-wrapper .entry-title',
                'scheme' => Typography::TYPOGRAPHY_3,
            ]
        );

		$this->add_responsive_control(
			'title_space',
			[
				'label' => esc_html__( 'Title Spacing', 'widgetkit-for-elementor' ),
			 	'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-1 .post-wrapper .entry-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);



		$this->add_control(
			'title_hover_color',
			[
				'label'     => esc_html__( 'Title & Meta Hover Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ed485f',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-1 .post-wrapper .entry-title a:hover'  => 'color: {{VALUE}};',
					'{{WRAPPER}} .tgx-blog-1 .post-wrapper .entry-meta a:hover'  => 'color: {{VALUE}};',			
					'{{WRAPPER}} .tgx-blog-1 .post-wrapper .entry-footer .content-btn a:hover i'  => 'color: {{VALUE}};',
					'{{WRAPPER}} .tgx-blog-1 .post-wrapper .entry-footer .content-btn a:hover'  => 'color: {{VALUE}};',
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

			$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .tgx-blog-1 .post-wrapper .entry-content',
                'scheme' => Typography::TYPOGRAPHY_3,
            ]
        );

		$this->add_responsive_control(
			'content_space',
			[
				'label' => esc_html__( 'Content Spacing', 'widgetkit-for-elementor' ),
			 	'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-1 .post-wrapper .entry-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_color',
			[
				'label'     => esc_html__( 'Content Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#444',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-1 .post-wrapper .entry-content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'meta_style',
				[
					'label' => esc_html__( 'Meta', 'widgetkit-for-elementor' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
		);




		$this->add_group_control(
	           Group_Control_Typography::get_type(),
	            [
	                'name'     => 'meta_typography',
	                'selector' => '{{WRAPPER}} .tgx-blog-1 .post-wrapper .entry-meta a,
	                {{WRAPPER}} .tgx-blog-1 .entry-footer .btn-readmore',
	                'scheme'   => Typography::TYPOGRAPHY_3,
	            ]
        );

		$this->add_responsive_control(
			'meta_space',
			[
				'label' => esc_html__( 'Spacing', 'widgetkit-for-elementor' ),
			 	'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .tgx-blog-1 .post-wrapper .entry-meta,
					{{WRAPPER}} .tgx-blog-1 .entry-footer ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);




		$this->add_control(
			'choose_meta_option',
				[
					'label'     => esc_html__( 'Choose Meta', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'button',
					'options'   => [
						'button'   => esc_html__( 'Read More', 'widgetkit-for-elementor' ),
						'date'     => esc_html__( 'Date & Comment', 'widgetkit-for-elementor' ),
					],
				]
		);

		$this->add_control(
			'read_more_text',
			[
				'label' => esc_html__( 'Button Text', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Read More', 'widgetkit-for-elementor' ),
				'condition' => [
					'choose_meta_option' => 'button',
				],
			]
		);

	$this->start_controls_tabs( 'read_more_button_style' );

		$this->start_controls_tab(
			'read_more_button_normal',
			[
				'label'     => esc_html__( 'Normal', 'widgetkit-for-elementor' ),
				'condition' => [
					'choose_meta_option' => 'button',
				],
			]
		);

		$this->add_control(
			'read_more_button_text_color',
			[
				'label' => esc_html__( 'Text Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default'   => '#444',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-1 .entry-footer .btn-readmore' => 'color: {{VALUE}};',
				],
				'condition' => [
					'choose_meta_option' => 'button',
				],
			]
		);

		$this->add_control(
			'read_more_background_color',
			[
				'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-1 .entry-footer .btn-readmore' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'choose_meta_option' => 'button',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'  => 'read_more_border',
				'label' => esc_html__( 'Border', 'widgetkit-for-elementor' ),
				'placeholder' => '1px',
				'default'   => '1px',
				'selector'  => '{{WRAPPER}} .tgx-blog-1 .entry-footer .btn-readmore',
				'separator' => 'before',
				'condition' => [
					'choose_meta_option' => 'button',
				],
			]
		);


		$this->add_control(
			'read_more_text_padding',
			[
				'label' => esc_html__( 'Text Padding', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tgx-blog-1 .entry-footer .btn-readmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'choose_meta_option' => 'button',
				],
				'separator' => 'before',
			]
		);


		$this->add_control(
			'read_more_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tgx-blog-1 .entry-footer .btn-readmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'choose_meta_option' => 'button',
				],
			]
		);


		$this->end_controls_tab();




		$this->start_controls_tab(
			'read_more_button_hover',
			[
				'label'     => esc_html__( 'Hover', 'widgetkit-for-elementor' ),
				'condition' => [
					'choose_meta_option' => 'button',
				],
			]
		);

		$this->add_control(
			'read_more_hover_color',
			[
				'label' => esc_html__( 'Text Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-1 .entry-footer .btn-readmore:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'choose_meta_option' => 'button',
				],
			]
		);

		$this->add_control(
			'read_more_button_background_hover_color',
			[
				'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default'   => '#ed485f',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-1 .entry-footer .btn-readmore:hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'choose_meta_option' => 'button',
				],

			]
		);

		$this->add_control(
			'read_more_button_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default'   => '#ed485f',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-1 .entry-footer .btn-readmore:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'choose_meta_option' => 'button',
				],
			]
		);


		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->add_control(
			'meta_position',
				[
					'label'     => esc_html__( 'Meta Position', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'bottom',
					'options'   => [
						'top'		=> esc_html__('Top'),
						'bottom'    => esc_html__( 'Bottom', 'widgetkit-for-elementor' ),
					],
					'condition' => [
						'choose_meta_option' => 'date',
					],
				]
		);


		$this->add_control(
			'meta_color',
			[
				'label'     => esc_html__( 'Meta Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#404040',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-1 .post-wrapper .entry-meta a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tgx-blog-1 .post-wrapper .entry-meta i' => 'color: {{VALUE}};',
				],
				'condition' => [
					'choose_meta_option' => 'date',
				],

			]
		);


		$this->add_control(
			'comment_enable',
				[
					'label'     => esc_html__( 'Comment Enable/Disable', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'enable'    => esc_html__( 'Enable', 'widgetkit-for-elementor' ),
					'disable'   => esc_html__( 'Disable', 'widgetkit-for-elementor' ),
					'condition' => [
						'choose_meta_option' => 'date',
					],
				]
		);

		$this->end_controls_section();

		// Button options Start
		$this->start_controls_section(
			'blog_1_button_style',
				[
					'label' => esc_html__( 'Nav Style', 'widgetkit-for-elementor' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
		);

		$this->add_control(
			'blog_1_nav_enable',
				[
					'label'     => esc_html__( 'Enable/Disable', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'enable'    => esc_html__( 'Enable', 'widgetkit-for-elementor' ),
					'disable'   => esc_html__( 'Disable', 'widgetkit-for-elementor' ),
				]
		);



       	$this->add_responsive_control(
			'blog_1_btn_text_size',
			[
				'label'   => esc_html__( 'Icon Size', 'widgetkit-for-elementor' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' =>20,
				],
				'range'  => [
					'px' => [
						'min' => 16,
						'max' => 24,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-1 .owl-carousel-left i, 
					{{WRAPPER}} .tgx-blog-1 .owl-carousel-right i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'blog_1_nav_enable' => 'yes',
				],
			]
		);


	$this->start_controls_tabs( 'blog_1_tabs_button_style' );

    $this->start_controls_tab(
        'blog_1_tab_button_normal',
          [
            'label' => esc_html__( 'Normal', 'widgetkit-for-elementor' ),
            'condition' => [
				'blog_1_nav_enable' => 'yes',
			],
          ]
    );

    $this->add_control(
        'blog_1_button_text_color',
          [
            'label' => esc_html__( 'Icon Color', 'widgetkit-for-elementor' ),
            'type'  => Controls_Manager::COLOR,
            'default'   => '#fff',
            'selectors' => [
              '{{WRAPPER}} .tgx-blog-1 .owl-carousel-left i, 
               {{WRAPPER}} .tgx-blog-1 .owl-carousel-right i' => 'color: {{VALUE}};',
            ],
           'condition' => [
				'blog_1_nav_enable' => 'yes',
          	],
          ]

    );

    $this->add_control(
        'blog_1_background_color',
	        [
	            'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
	            'type'  => Controls_Manager::COLOR,
	            'default'   => '#ed485f',
	            'selectors' => [
	              '{{WRAPPER}} .tgx-blog-1 .owl-carousel-left, 
	               {{WRAPPER}} .tgx-blog-1 .owl-carousel-right' => 'background-color: {{VALUE}};',
	            ],
	            'condition' => [
					'blog_1_nav_enable' => 'yes',
	        	],
	       ]
    );


    	$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'  => 'blog_1_project_border',
				'label' => esc_html__( 'Border', 'widgetkit-for-elementor' ),
				'placeholder' => '1px',
				'default'  => '1px',
				'selector' => '
				    {{WRAPPER}} .tgx-blog-1 .owl-carousel-left, 
					{{WRAPPER}} .tgx-blog-1 .owl-carousel-right',
				'separator' => 'before',
				'condition' => [
					'blog_1_nav_enable' => 'yes',
					],
			]
		);

		$this->add_control(
			'blog_1_project_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tgx-blog-1 .owl-carousel-left, 
					 {{WRAPPER}} .tgx-blog-1 .owl-carousel-right' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'blog_1_nav_enable' => 'yes',
				],
			]
		);



    $this->end_controls_tab();

    $this->start_controls_tab(
        'blog_1_tab_button_hover',
	        [
	            'label' => esc_html__( 'Hover', 'widgetkit-for-elementor' ),
	            'condition' => [
					'blog_1_nav_enable' => 'yes',
				],
	        ]
    );

    $this->add_control(
        'blog_1_icon_hover_color',
		    [
		        'label' => esc_html__( 'Icon Color', 'widgetkit-for-elementor' ),
		        'type'  => Controls_Manager::COLOR,
		        'default'   => '#ed485f',
		        'selectors' => [
		          '{{WRAPPER}} .tgx-blog-1 .owl-carousel-left:hover i, 
		           {{WRAPPER}} .tgx-blog-1 .owl-carousel-right:hover i' => 'color: {{VALUE}};',
		        ],
		        'condition' => [
					'blog_1_nav_enable' => 'yes',
				],
		    ]
    );

    $this->add_control(
        'blog_1_button_background_hover_color',
		    [
		        'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
		        'type'  => Controls_Manager::COLOR,
		        'default'   => ' #fff',
		        'selectors' => [
		          '{{WRAPPER}} .tgx-blog-1 .owl-carousel-left:hover, 
		           {{WRAPPER}} .tgx-blog-1 .owl-carousel-right:hover' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
		        ],
		        'condition' => [
					'blog_1_nav_enable' => 'yes',
				],
		    ]
    );



    $this->end_controls_tab();


    $this->end_controls_tabs();

$this->end_controls_section();

	
	/**
	 * Pro control panel 
	 */
	if(!apply_filters('wkpro_enabled', false)):
		$this->start_controls_section(
			'section_widgetkit_pro_box',
			[
				'label' => esc_html__( 'Go Premium for more layout & feature', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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

	

	}

	protected function render() {
		require WK_PATH . '/elements/blog-1/template/view.php';
	}


}
