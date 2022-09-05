<?php


use Elementor\Widget_Base;
use Elementor\Controls_Manager;
Use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor WidgetKit Blog 4
 *
 * Elementor widget for WidgetKit blog 4
 *
 * @since 1.0.0
 */
class wkfe_blog_4 extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-effect-1';
	}

	public function get_title() {
		return esc_html__( 'Blog Hover Animation', 'widgetkit-for-elementor' );
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
			'blog_4_section_style',
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
			'blog_4_post_item_show',
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
			'blog_4_layout_item_show',
				[
					'label'     => esc_html__( 'Column', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '4',
					'options'   => [
						'4'     => esc_html__( 'Column 3', 'widgetkit-for-elementor' ),
						'3'     => esc_html__( 'Column 4 ', 'widgetkit-for-elementor' ),
						'6'     => esc_html__( 'Column 2', 'widgetkit-for-elementor' ),
					],
				]
		);

		$this->add_control(
            'blog_4_hover_effect_heading',
            [
                'label' => esc_html__( 'Hover Effect', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

		$this->add_control(
			'blog_4_hover_effect',
				[
					'label'     => esc_html__( 'Effect', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'background_color',
					'options'   => [
						'background_color'     => esc_html__( 'Background', 'widgetkit-for-elementor' ),
						'transparent'          => esc_html__( 'Transparent ', 'widgetkit-for-elementor' ),
					],
				]
		);

		$this->add_control(
			'blog_4_wrapper_overlay_color',
			[
				'label'     => esc_html__( 'Overlay', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,114,114,0.99))',
				'selectors' => [
					'{{WRAPPER}} .hover-effect-2 .wrapper:before' => 'background: linear-gradient(to bottom, rgba(255,0,0,0), {{VALUE}});',
				],
				'condition' => [
					'blog_4_hover_effect' => 'transparent',
				],
			]
		);




		$this->add_control(
			'blog_4_details_bg_color',
			[
				'label'     => esc_html__( 'Details Bg Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .hover-effect-1 .content' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'blog_4_hover_effect' => 'background_color',
				],
			]
		);


		$this->add_responsive_control(
			'blog_4_details_padding',
			[
				'label' => esc_html__( 'Details Padding', 'widgetkit-for-elementor' ),
			 	'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .hover-effect-1 .data .content,
					 {{WRAPPER}} .hover-effect-2 .data .content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .hover-effect-1 .wrapper, {{WRAPPER}} .hover-effect-2 .wrapper',
			]
		);


		$this->end_controls_section();


		$this->start_controls_section(
			'blog_4_date_style',
				[
					'label' => esc_html__( 'Date', 'widgetkit-for-elementor' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
		);


		$this->add_control(
			'blog_4_date_enable',
				[
					'label'     => esc_html__( 'Enable/Disable', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'enable'    => esc_html__( 'Enable', 'widgetkit-for-elementor' ),
					'disable'   => esc_html__( 'Disable', 'widgetkit-for-elementor' ),
				]
		);

		$this->add_control(
			'blog_4_date_color',
			[
				'label'     => esc_html__( 'Date Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .hover-effect-1 .date span a,
					 {{WRAPPER}} .hover-effect-2 .date span a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'blog_4_date_enable' => 'yes',
				],
			]
		);


		$this->add_group_control(
	           Group_Control_Typography::get_type(),
	            [
	                'name'     => 'date_typography',
	                'selector' => '
	                {{WRAPPER}} .hover-effect-1 .date span a,
	                {{WRAPPER}} .hover-effect-2 .date span a',
	                'scheme'   => Typography::TYPOGRAPHY_3,
		           'condition' => [
						'blog_4_date_enable' => 'yes',
					],
	            ]
        );

        $this->add_control(
			'blog_4_date_bg_color',
			[
				'label'     => esc_html__( 'Date Bg Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#77d7b9',
				'selectors' => [
					'{{WRAPPER}} .hover-effect-1 .date' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'blog_4_hover_effect' => 'background_color',
				],
			]
		);

		$this->add_responsive_control(
			'blog_4_date_padding',
			[
				'label' => esc_html__( 'Padding', 'widgetkit-for-elementor' ),
			 	'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .hover-effect-1 .date,
					 {{WRAPPER}} .hover-effect-2 .date' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],

				'condition' => [
					'blog_4_date_enable' => 'yes',
				],
			]
		);



		$this->end_controls_section();

		$this->start_controls_section(
			'blog_4_section_title',
			[
				'label' => esc_html__( 'Title', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'blog_4_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#404040',
				'selectors' => [
					'{{WRAPPER}} .hover-effect-1 .wrapper .entry-title a,
					{{WRAPPER}} .hover-effect-2 .wrapper .entry-title a,
					 {{WRAPPER}} .hover-effect-1 .author-details a,
					 {{WRAPPER}} .hover-effect-2 .author-details a' => 'color: {{VALUE}};',

					 
					'{{WRAPPER}} .hover-effect-1 .menu-button span::after,
					 {{WRAPPER}} .hover-effect-1 .menu-button span::before,
					 {{WRAPPER}} .hover-effect-1 .menu-button span' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .hover-effect-1 .wrapper .entry-title,
                {{WRAPPER}} .hover-effect-2 .wrapper .entry-title',
                'scheme'   => Typography::TYPOGRAPHY_3,
            ]
        );

		$this->add_responsive_control(
			'blog_4_title_space',
			[
				'label' => esc_html__( 'Title Spacing', 'widgetkit-for-elementor' ),
			 	'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .hover-effect-1 .wrapper .entry-title,
					 {{WRAPPER}} .hover-effect-2 .wrapper .entry-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);



		$this->add_control(
			'blog_4_title_hover_color',
			[
				'label'     => esc_html__( 'Title & Meta Hover Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#77d7b9',
				'selectors' => [
					'{{WRAPPER}} .hover-effect-1 .wrapper .entry-title a:hover,
					 {{WRAPPER}} .hover-effect-2 .wrapper .entry-title a:hover'  => 'color: {{VALUE}};',
					'{{WRAPPER}} .hover-effect-1 .author-details a:hover,
					 {{WRAPPER}} .hover-effect-2 .author-details a:hover'  => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
            'blog_4_hover_autho_heading',
            [
                'label' => esc_html__( 'Author', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
			'blog_4_author_enable',
				[
					'label'     => esc_html__( 'Enable/Disable', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'enable'    => esc_html__( 'Enable', 'widgetkit-for-elementor' ),
					'disable'   => esc_html__( 'Disable', 'widgetkit-for-elementor' ),
				]
		);



		$this->end_controls_section();

		$this->start_controls_section(
			'blog_4_content_style',
				[
					'label' => esc_html__( 'Content', 'widgetkit-for-elementor' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
			'blog_4_content_color',
			[
				'label'     => esc_html__( 'Content Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#444',
				'selectors' => [
					'{{WRAPPER}} .hover-effect-1 .wrapper .entry-content,
					 {{WRAPPER}} .hover-effect-2 .wrapper .entry-content' => 'color: {{VALUE}};',
				],
			]
		);

			$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'description_typography',
                'selector' => '
	                {{WRAPPER}} .hover-effect-1 .wrapper .entry-content,
	                {{WRAPPER}} .hover-effect-2 .wrapper .entry-content',
                'scheme'  => Typography::TYPOGRAPHY_3,
            ]
        );

		$this->add_control(
			'blog_4_show_content',
			[
				'label' => esc_html__( 'Content Word', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default'     => esc_html__( '20', 'widgetkit-for-elementor' ),
				'placeholder' => esc_html__( '20', 'widgetkit-for-elementor' ),
			]
		);

		$this->add_responsive_control(
			'blog_4_content_space',
			[
				'label' => esc_html__( 'Content Spacing', 'widgetkit-for-elementor' ),
			 	'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .hover-effect-1 .wrapper .entry-content,
					 {{WRAPPER}} .hover-effect-2 .wrapper .entry-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);



		$this->end_controls_section();


		$this->start_controls_section(
			'blog_4_meta_style',
				[
					'label' => esc_html__( 'Meta', 'widgetkit-for-elementor' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
		);



		$this->add_control(
			'blog_4_meta_enable',
				[
					'label'     => esc_html__( 'Enable/Disable', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'enable'    => esc_html__( 'Enable', 'widgetkit-for-elementor' ),
					'disable'   => esc_html__( 'Disable', 'widgetkit-for-elementor' ),
				]
		);

		$this->add_control(
			'blog_4_meta_color',
			[
				'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .hover-effect-1 .menu-content a,
					 {{WRAPPER}} .hover-effect-2 .menu-content a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'blog_4_meta_enable' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'blog_4_meta_font_size',
				[
					'label'  => esc_html__( 'Size', 'widgetkit-for-elementor' ),
					'type'   => Controls_Manager::SLIDER,
					'range'  => [
						'px' => [
							'min' => 0,
							'max' => 10,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .hover-effect-1 .menu-content a,
						 {{WRAPPER}} .hover-effect-2 .menu-content a' => 'font-size: {{SIZE}}{{UNIT}};',
					],
				]
		);


        $this->add_control(
			'blog_4_meta_bg_color',
			[
				'label'     => esc_html__( 'Meta Bg Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#77d7b9',
				'selectors' => [
					'{{WRAPPER}} .hover-effect-1 .menu-content' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'blog_4_hover_effect' => 'background_color',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'blog_4_button_style',
				[
					'label' => esc_html__( 'Button', 'widgetkit-for-elementor' ),
					'tab'   => Controls_Manager::TAB_STYLE,
					'condition' => [
						'blog_4_hover_effect' => 'transparent',
					],
				]
		);



		$this->add_control(
			'blog_4_button_enable',
				[
					'label'     => esc_html__( 'Enable/Disable', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'enable'    => esc_html__( 'Enable', 'widgetkit-for-elementor' ),
					'disable'   => esc_html__( 'Disable', 'widgetkit-for-elementor' ),
				]
		);

		$this->add_control(
			'blog_4_btn_text',
			[
				'label' => esc_html__( 'Button Text', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Read More', 'widgetkit-for-elementor' ),
				'placeholder' => esc_html__( 'Read More', 'widgetkit-for-elementor' ),
				 'condition' => [
					'blog_4_button_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'blog_4_button_color',
			[
				'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .hover-effect-2 .button,
					 {{WRAPPER}} .hover-effect-2 .button::after' => 'color: {{VALUE}};',
				],
				'condition' => [
					'blog_4_button_enable' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'blog_4_button_font_size',
				[
					'label'  => esc_html__( 'Size', 'widgetkit-for-elementor' ),
					'type'   => Controls_Manager::SLIDER,
					'range'  => [
						'px' => [
							'min' => 0,
							'max' => 16,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .hover-effect-2 .button,
						 {{WRAPPER}} .hover-effect-2 .button::after' => 'font-size: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
					'blog_4_button_enable' => 'yes',
				],
				]
		);


		$this->add_responsive_control(
			'blog_4_button_icon_position',
				[
					'label'  => esc_html__( 'Icon Position', 'widgetkit-for-elementor' ),
					'type'   => Controls_Manager::SLIDER,
					'range'  => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .hover-effect-2 .button::after' => 'left: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
					'blog_4_button_enable' => 'yes',
				],
				]
		);

		$this->add_control(
            'blog_4_button_alignment',
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
                ],
                'selectors' => [
                    '{{WRAPPER}}  .hover-effect-2 .button' => 'text-align: {{VALUE}}',
                ],
                'condition' => [
					'blog_4_button_enable' => 'yes',
				],
            ]
        );


        $this->add_control(
			'blog_4_button_hover_color',
			[
				'label'     => esc_html__( 'Hover Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#77d7b9',
				'selectors' => [
					'{{WRAPPER}} .hover-effect-2 .button:hover,
					 {{WRAPPER}} .hover-effect-2 .button:hover::after' => 'color: {{VALUE}};',
				],
				'condition' => [
					'blog_4_button_enable' => 'yes',
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
		require WK_PATH . '/elements/blog-4/template/view.php';
	}


}
