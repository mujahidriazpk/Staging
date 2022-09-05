<?php


use Elementor\Widget_Base;
use Elementor\Controls_Manager;
Use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor WidgetKit Blog 5
 *
 * Elementor widget for WidgetKit blog 5
 *
 * @since 1.0.0
 */
class wkfe_blog_5 extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-blog-5';
	}

	public function get_title() {
		return esc_html__( 'Blog Image', 'widgetkit-for-elementor' );
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
			'blog_5_section_style',
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
			'blog_5_post_item_show',
				[
					'label'     => esc_html__( 'Post Shows', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '3',
					'options'   => [
						'2'     => esc_html__( 'Show 2', 'widgetkit-for-elementor' ),
						'3'     => esc_html__( 'Show 3', 'widgetkit-for-elementor' ),
						'4'     => esc_html__( 'Show 4 ', 'widgetkit-for-elementor' ),
						'6'     => esc_html__( 'Show 6', 'widgetkit-for-elementor' ),
					],
				]
		);


		$this->add_control(
			'blog_5_layout_item_show',
				[
					'label'     => esc_html__( 'Column', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '12',
					'options'   => [
						'6'     => esc_html__( 'Column 2', 'widgetkit-for-elementor' ),
						'12'    => esc_html__( 'Column 1', 'widgetkit-for-elementor' ),
					],
				]
		);

		$this->add_control(
            'blog_5_hover_effect_heading',
            [
                'label' => esc_html__( 'Item Positions', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

		$this->add_control(
			'blog_5_image_postion',
				[
					'label'     => esc_html__( 'Image Position', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'right',
					'options'   => [
						'left'     => esc_html__( 'Left', 'widgetkit-for-elementor' ),
						'right'    => esc_html__( 'Right', 'widgetkit-for-elementor' ),
					],
				]
		);



		$this->add_control(
			'blog_5_details_bg_color',
			[
				'label'     => esc_html__( 'Item Bg Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-5 .card' => 'background-color: {{VALUE}};',
				],
			]
		);


		$this->add_responsive_control(
			'blog_5_details_padding',
			[
				'label' => esc_html__( 'Details Padding', 'widgetkit-for-elementor' ),
			 	'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .tgx-blog-5 .card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'    => 'image_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector'=> '{{WRAPPER}} .tgx-blog-5 .card',
			]
		);


		$this->end_controls_section();


		$this->start_controls_section(
			'blog_5_count_style',
				[
					'label' => esc_html__( 'Count', 'widgetkit-for-elementor' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
		);


		$this->add_control(
			'blog_5_count_enable',
				[
					'label'     => esc_html__( 'Enable/Disable', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => '',
					'enable'    => esc_html__( 'Enable', 'widgetkit-for-elementor' ),
					'disable'   => esc_html__( 'Disable', 'widgetkit-for-elementor' ),
				]
		);

		$this->add_control(
			'blog_5_count_color',
			[
				'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-5 .card-circle' => 'color: {{VALUE}}; border:1px solid {{VALUE}};',
				],
				'condition' => [
					'blog_5_count_enable' => 'yes',
				],
			]
		);


		$this->add_responsive_control(
			'blog_5_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
			 	'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .tgx-blog-5 .card-circle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],

				'condition' => [
					'blog_5_count_enable' => 'yes',
				],
			]
		);



		$this->end_controls_section();

		$this->start_controls_section(
			'blog_5_section_title',
			[
				'label' => esc_html__( 'Title', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'blog_5_author_enable',
				[
					'label'     => esc_html__( 'Author Enable/Disable', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'enable'    => esc_html__( 'Enable', 'widgetkit-for-elementor' ),
					'disable'   => esc_html__( 'Disable', 'widgetkit-for-elementor' ),
				]
		);

		$this->add_control(
			'blog_5_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#404040',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-5 .entry-title a,
					 {{WRAPPER}} .tgx-blog-5 .author-details a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .tgx-blog-5 .entry-title',
                'scheme'   => Typography::TYPOGRAPHY_3,
            ]
        );

		$this->add_responsive_control(
			'blog_5_title_space',
			[
				'label' => esc_html__( 'Title Spacing', 'widgetkit-for-elementor' ),
			 	'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .tgx-blog-5 .entry-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);



		$this->add_control(
			'blog_5_title_hover_color',
			[
				'label'     => esc_html__( 'Title & Meta Hover Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#255cdc',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-5 .entry-title a:hover'  => 'color: {{VALUE}};',
					'{{WRAPPER}} .tgx-blog-5 .author-details a:hover'  => 'color: {{VALUE}};',
				],
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'blog_5_content_style',
				[
					'label' => esc_html__( 'Content', 'widgetkit-for-elementor' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
			'blog_5_content_color',
			[
				'label'     => esc_html__( 'Content Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#444',
				'selectors' => [
					'{{WRAPPER}}  .tgx-blog-5 .entry-content .content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'description_typography',
                'selector' => '
	                {{WRAPPER}} .tgx-blog-5 .entry-content .content',
                'scheme'   => Typography::TYPOGRAPHY_3,
            ]
        );

		$this->add_responsive_control(
			'blog_5_content_space',
			[
				'label' => esc_html__( 'Content Spacing', 'widgetkit-for-elementor' ),
			 	'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .tgx-blog-5 .entry-content .content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);



		$this->end_controls_section();



		$this->start_controls_section(
			'blog_5_button_style',
				[
					'label' => esc_html__( 'Button', 'widgetkit-for-elementor' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
		);



		$this->add_control(
			'blog_5_button_enable',
				[
					'label'     => esc_html__( 'Enable/Disable', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'enable'    => esc_html__( 'Enable', 'widgetkit-for-elementor' ),
					'disable'   => esc_html__( 'Disable', 'widgetkit-for-elementor' ),
				]
		);

		$this->add_control(
			'blog_5_button_color',
			[
				'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#404040',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-5 .card-read a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'blog_5_button_enable' => 'yes',
				],
			]
		);


		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'button_typography',
                'selector' => '
	                {{WRAPPER}} .tgx-blog-5 .card-read',
                'scheme'    => Typography::TYPOGRAPHY_3,
                'condition' => [
					'blog_5_button_enable' => 'yes',
				],
            ]
        );




		$this->add_control(
            'blog_5_button_alignment',
            [
                'label' => esc_html__( 'Alignment', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::CHOOSE,
                'label_block' => false,
                'default'  => esc_html__( 'right', 'widgetkit-for-elementor' ),
                'options'  => [
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
                    '{{WRAPPER}}  .tgx-blog-5 .card-read' => 'text-align: {{VALUE}}',
                ],

                'condition' => [
					'blog_5_button_enable' => 'yes',
				],
            ]
        );


		$this->add_responsive_control(
			'blog_5_button_left_position',
				[
					'label'   => esc_html__( 'Line Right Position', 'widgetkit-for-elementor' ),
					'type'    => Controls_Manager::SLIDER,
					'default' => [
						'size'=> 0,
					],
					'range'  => [
						'%'  => [
							'min' => 0,
							'max' => 10,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .tgx-blog-5 .card-read:after' => 'right: {{SIZE}}; margin:auto;',
					],
					'condition' => [
					'blog_5_button_alignment' => 'left',
				],
			]
		);


        $this->add_control(
			'blog_5_button_hover_color',
			[
				'label'     => esc_html__( 'Hover Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#77d7b9',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-5 .card-read a:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'blog_5_button_enable' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'blog_5_button_line_position',
				[
					'label'   => esc_html__( 'Line Width', 'widgetkit-for-elementor' ),
					'type'    => Controls_Manager::SLIDER,
					'default' => [
						'size' => 50,
					],
					'range'  => [
						'%'  => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .tgx-blog-5 .card-read:after' => 'width: {{SIZE}}%;',
					],
					'condition' => [
					'blog_5_button_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'blog_5_button_line_color',
			[
				'label'     => esc_html__( 'Line Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#77d7b9',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-5 .card-read:after' => 'color: {{VALUE}};',
				],
				'condition' => [
					'blog_5_button_enable' => 'yes',
				],
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
		require WK_PATH . '/elements/blog-5/template/view.php';
	}


}
