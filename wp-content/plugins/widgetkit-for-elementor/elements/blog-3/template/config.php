<?php


use Elementor\Widget_Base;
use Elementor\Controls_Manager;
Use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor WidgetKit Blog 3
 *
 * Elementor widget for WidgetKit blog 3
 *
 * @since 1.0.0
 */
class wkfe_blog_3 extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-blog-3';
	}

	public function get_title() {
		return esc_html__( 'Blog Revert', 'widgetkit-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-sidebar wk-icon';
	}

	public function get_categories() {
		return [ 'widgetkit_deprecated_elements' ];
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
			'blog_3_post_show',
				[
					'label'     => esc_html__( 'Post Show', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '3',
					'options'   => [
						'2'     => esc_html__( 'Show 2', 'widgetkit-for-elementor' ),
						'3'     => esc_html__( 'Show 3', 'widgetkit-for-elementor' ),
						'4'     => esc_html__( 'Show 4 ', 'widgetkit-for-elementor' ),
						'6'     => esc_html__( 'Show 6 ', 'widgetkit-for-elementor' ),
					],
				]
		);

	    $this->add_control(
			'blog_3_post_column',
				[
					'label'     => esc_html__( 'Post Column', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '4',
					'options'   => [
						'6'     => esc_html__( 'Column 2', 'widgetkit-for-elementor' ),
						'4'     => esc_html__( 'Column 3', 'widgetkit-for-elementor' ),
						'3'     => esc_html__( 'Column 4 ', 'widgetkit-for-elementor' ),
					],
				]
		);



		$this->add_control(
            'shadow_style',
            [
                'label' => esc_html__( 'Box Shadow Normal', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'    => 'image_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector'=> '{{WRAPPER}} .tgx-blog-3 .blog-info',
			]
		);

		$this->add_control(
            'shadow_hover_style',
            [
                'label' => esc_html__( 'Box Shadow Hover', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'    => 'image_box_shadow_hover',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .tgx-blog-3 .blog-wrapper:hover .blog-info',
			]
		);


		$this->add_control(
			'layout_bg',
			[
				'label'     => esc_html__( 'Layout Bg Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fafafa',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-3 .blog-info' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_postion',
				[
					'label'    => esc_html__( 'Image Position', 'widgetkit-for-elementor' ),
					'type'     => Controls_Manager::SLIDER,
					'default'  => [
                        'size' => 220,
                    ],
					'range'  => [
						'px' => [
							'min' => 20,
							'max' => 500,
						],
					],
					'selectors' => [
						'{{WRAPPER}}  .tgx-blog-3 .overlay-hover' => 'max-height: {{SIZE}}{{UNIT}};',
					],
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
				'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#404040',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-3 .blog-wrapper .title  a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .tgx-blog-3 .blog-wrapper .title',
                'scheme'   => Typography::TYPOGRAPHY_3,
            ]
        );

        $this->add_control(
			'title_hover_color',
			[
				'label'     => esc_html__( 'Hover Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#255cdc',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-3 .blog-wrapper .title a:hover'  => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_space',
			[
				'label' => esc_html__( 'Spacing', 'widgetkit-for-elementor' ),
			 	'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .tgx-blog-3 .blog-wrapper .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
			'content_color',
			[
				'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#444',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-3 .blog-wrapper .desc' => 'color: {{VALUE}};',
				],
			]
		);

			$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'description_typography',
                'selector' => '{{WRAPPER}} .tgx-blog-3 .blog-wrapper .desc',
                'scheme'   => Typography::TYPOGRAPHY_3,
            ]
        );

		$this->add_responsive_control(
			'content_space',
			[
				'label' => esc_html__( 'Spacing', 'widgetkit-for-elementor' ),
			 	'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .tgx-blog-3 .blog-wrapper .desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);



		$this->end_controls_section();

		$this->start_controls_section(
			'meta_style',
				[
					'label' => esc_html__( 'Author', 'widgetkit-for-elementor' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
		);



		$this->add_control(
			'author_position',
				[
					'label'     => esc_html__( 'Position', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'bottom',
					'options'   => [
						'top'		=> esc_html__('Top', 'widgetkit-for-elementor'),
						'middle'	=> esc_html__('Middle', 'widgetkit-for-elementor'),
						'bottom'    => esc_html__( 'Bottom', 'widgetkit-for-elementor' ),
					],
				]
		);
		$this->add_control(
			'author_text_color',
			[
				'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#444',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-3 .blog-info .author .author-info' => 'color: {{VALUE}};',
				],
			]
		);


		$this->add_group_control(
	           Group_Control_Typography::get_type(),
	            [
	                'name'     => 'meta_typography',
	                'selector' => '{{WRAPPER}} .tgx-blog-3 .blog-info .author .author-info',
	                'scheme'   => Typography::TYPOGRAPHY_3,
	            ]
        );

		$this->add_responsive_control(
			'author_space',
			[
				'label' => esc_html__( 'Spacing', 'widgetkit-for-elementor' ),
			 	'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .tgx-blog-3 .blog-info .author' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_responsive_control(
			'author_img_radius',
			[
				'label' => esc_html__( 'Image Radius', 'widgetkit-for-elementor' ),
			 	'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .tgx-blog-3 .blog-info .author .author-img img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		require WK_PATH . '/elements/blog-3/template/view.php';
	}


}
