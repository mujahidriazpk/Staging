<?php


use Elementor\Widget_Base;
use Elementor\Controls_Manager;
Use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor WidgetKit Blog 2
 *
 * Elementor widget for WidgetKit blog 2
 *
 * @since 1.0.0
 */
class wkfe_blog_2 extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-blog-2';
	}

	public function get_title() {
		return esc_html__( 'Blog Sidebar', 'widgetkit-for-elementor' );
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


	$this->start_controls_tabs( 'caption_control' );

		$this->start_controls_tab(
	        'color_options',
	          [
	            'label' => esc_html__( 'Sticky Post', 'widgetkit-for-elementor' ),
	          ]
	    );

	    $this->add_control(
			'sticky_post_show',
				[
					'label'     => esc_html__( 'Post Shows', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '1',
					'options'   => [
						'1'     => esc_html__( 'Show 1', 'widgetkit-for-elementor' ),
						'2'     => esc_html__( 'Show 2 ', 'widgetkit-for-elementor' ),
						'3'     => esc_html__( 'Show 3', 'widgetkit-for-elementor' ),
					],
				]
		);


        $this->end_controls_tab();


        $this->start_controls_tab(
	        'standard_post',
	          [
	            'label' => esc_html__( 'Standard Post', 'widgetkit-for-elementor' ),
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
			'standard_post_show',
				[
					'label'     => esc_html__( 'Post Shows', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '2',
					'options'   => [
						'1'     => esc_html__( 'Show 1', 'widgetkit-for-elementor' ),
						'2'     => esc_html__( 'Show 2 ', 'widgetkit-for-elementor' ),
						'3'     => esc_html__( 'Show 3', 'widgetkit-for-elementor' ),
					],
				]
		);

		$this->add_control(
			'layout_position',
				[
					'label'     => esc_html__( 'Layout Position', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'left',
					'options'   => [
						'left'	   => esc_html__('Left', 'widgetkit-for-elementor'),
						'right'    => esc_html__( 'Right', 'widgetkit-for-elementor' ),
					],
				]
		);

		$this->add_control(
			'standard_post_bg_color',
			[
				'label'     => esc_html__( 'Post Background Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-2 .blog-details' => 'background-color: {{VALUE}};',
				],
			]
		);


    $this->end_controls_tab();

    $this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'    => 'image_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .tgx-blog-2 .blog-details',
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

		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .tgx-blog-2 .custom-sticky-post .entry-title',
                'scheme'   => Typography::TYPOGRAPHY_3,
            ]
        );

		$this->add_responsive_control(
			'title_space',
			[
				'label' => esc_html__( 'Title Spacing', 'widgetkit-for-elementor' ),
			 	'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .tgx-blog-2 .custom-sticky-post .entry-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Title Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#404040',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-2 .entry-header  a' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .tgx-blog-2 .entry-header a:hover'  => 'color: {{VALUE}};',
					'{{WRAPPER}} .tgx-blog-2 .custom-sticky-post .author-meta a:hover'  => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .tgx-blog-2 .entry-footer .content-btn a:hover'  => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'standard_posttitle_typography',
                'label'     => esc_html__( 'Standard Post Typography', 'widgetkit-for-elementor' ),
                'selector' => '{{WRAPPER}} .tgx-blog-2 .custom-standard-post .entry-title',
                'scheme'   => Typography::TYPOGRAPHY_3,
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
                'name'     => 'description_typography',
                'selector' => '{{WRAPPER}} .tgx-blog-2 .custom-sticky-post .entry-content',
                'scheme'   => Typography::TYPOGRAPHY_3,
            ]
        );

		$this->add_responsive_control(
			'content_space',
			[
				'label' => esc_html__( 'Content Spacing', 'widgetkit-for-elementor' ),
			 	'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .tgx-blog-2 .custom-sticky-post .entry-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .tgx-blog-2 .custom-sticky-post .entry-content' => 'color: {{VALUE}};',
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

		$this->add_control(
			'meta_position_2',
				[
					'label'     => esc_html__( 'Meta Position', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'bottom',
					'options'   => [
						'top'		=> esc_html__('Top', 'widgetkit-for-elementor'),
						'bottom'    => esc_html__( 'Bottom', 'widgetkit-for-elementor' ),
					],
				]
		);


		$this->add_group_control(
	           Group_Control_Typography::get_type(),
	            [
	                'name'     => 'meta_typography',
	                'selector' => '{{WRAPPER}} .tgx-blog-2 .custom-sticky-post .blog-details .author-meta',
	                'scheme'   => Typography::TYPOGRAPHY_3,
	            ]
        );

		$this->add_responsive_control(
			'meta_space',
			[
				'label' => esc_html__( 'Meta Spacing', 'widgetkit-for-elementor' ),
			 	'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .tgx-blog-2 .custom-sticky-post .blog-details .author-meta' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'date_enable',
				[
					'label'     => esc_html__( 'Date Enable/Disable', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'enable'    => esc_html__( 'Enable', 'widgetkit-for-elementor' ),
					'disable'   => esc_html__( 'Disable', 'widgetkit-for-elementor' ),
				]
		);


		$this->add_control(
			'date_color',
			[
				'label'     => esc_html__( 'Date Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-2 .custom-sticky-post .post-thumbnail .date-format' => 'color: {{VALUE}};',
				],

				'condition' => [
					'date_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'date_bg_color',
			[
				'label'     => esc_html__( 'Date Background Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ed485f',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-2 .custom-sticky-post .post-thumbnail .date-format' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'date_enable' => 'yes',
				],

			]
		);



		$this->add_control(
			'post_overlay_color',
			[
				'label'     => esc_html__( 'Post Overlay Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-2 .post-thumbnail:before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'meta_color',
			[
				'label'     => esc_html__( 'Meta Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ed485f',
				'selectors' => [
					'{{WRAPPER}} .tgx-blog-2 .custom-sticky-post .blog-details .author-meta a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tgx-blog-2 .entry-footer .content-btn a' => 'color: {{VALUE}};',
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
		require WK_PATH . '/elements/blog-2/template/view.php';
	}


}
