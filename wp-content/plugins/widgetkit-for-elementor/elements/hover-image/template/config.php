<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
Use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor WidgetKit Image Box
 *
 * Elementor widget for WidgetKit image box
 *
 * @since 1.0.0
 */
class wkfe_hover_image extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-hover-image';
	}

	public function get_title() {
		return esc_html__( 'Hover Image', 'widgetkit-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-image-before-after wk-icon';
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
			'widgetkit-main',
		 ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_hover_image',
			[
				'label' => __( 'Image', 'widgetkit-for-elementor' ),
			]
		);

		$this->add_control(
			'hover_image',
			[
				'label' => __( 'Choose Image', 'widgetkit-for-elementor' ),
				'type' => Controls_Manager::MEDIA, 
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_responsive_control(
			'hover_image_size',
			[
				'label' => __( 'Image Size (%)', 'widgetkit-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tgx-hover-image' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'hover_image_align',
			[
				'label' => __( 'Alignment', 'widgetkit-for-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'widgetkit-for-elementor' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'widgetkit-for-elementor' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'widgetkit-for-elementor' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tgx-hover-image .hover-image' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_image_caption_title',
			[
				'label' => __( 'Title Caption', 'widgetkit-for-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => '',
				'placeholder' => __( 'Enter your image caption', 'widgetkit-for-elementor' ),
			]
		);


		$this->add_control(
			'hover_image_caption_content',
			[
				'label' => __( 'Content Caption', 'widgetkit-for-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => '',
				'placeholder' => __( 'Enter your image caption', 'widgetkit-for-elementor' ),
			]
		);

		$this->add_control(
			'select_link_to',
			[
				'label' => __( 'Choose Lightbox', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'url',
				'options' => [
					'url' => __( 'URL', 'elementor' ),
					'lightbox' => __( 'Lightbox', 'elementor' ),
				],
			]
		);

		$this->add_control(
			'hover_image_link',
			[
				'label' => __( 'Link to', 'widgetkit-for-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'https://themesgrove.com', 'widgetkit-for-elementor' ),
				'show_label' => true,
				'condition' => [
                   'hover_image_hover_animation' => ['default-effect', 'jazz-effect','bubba-effect', 'goliath-effect'],
                   'select_link_to' => ['url'],
               ],
			]
		);


		$this->add_control(
			'hover_image_lightbox',
			[
				'label' => __( 'Lightbox', 'widgetkit-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'no',
				'options' => [
					'yes' => __( 'Yes', 'widgetkit-for-elementor' ),
					'no' =>  __( 'No', 'widgetkit-for-elementor' ),
				],
				'condition' => [
                   'select_link_to' => ['lightbox'],
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
			'section_style_hover_image',
			[
				'label' => __( 'Image', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'hover_image_hover_animation',
			[
				'label' => __( 'Hover Effect', 'widgetkit-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'jazz-effect',
				'options' => [
					'default-effect' => __( 'Default', 'widgetkit-for-elementor' ),
					'jazz-effect' => __( 'Jazz', 'widgetkit-for-elementor' ),
					'goliath-effect' => __( 'Goliath', 'widgetkit-for-elementor' ),
					'sadie-effect' => __( 'Sadie', 'widgetkit-for-elementor' ),
					'bubba-effect' => __( 'Bubba', 'widgetkit-for-elementor' ),
				],
			]
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'hover_image_mage_border',
				'selector' => '{{WRAPPER}} .tgx-hover-image .jazz-effect figcaption::after',
				'separator' => 'before',
				'condition' => [
					'hover_image_hover_animation' => 'jazz-effect',
				],
			]
		);


		$this->start_controls_tabs( 
			'hover_image_shadow_style',
				[
					'label' => esc_html__( 'Box Shadow', 'widgetkit-for-elementor' ),
				]

		);

			$this->start_controls_tab(
				'hover_image_normal_shadow',
				[
					'label' => esc_html__( 'Normal', 'widgetkit-for-elementor' ),
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'hover_image_box_shadow',
					'label' => esc_html__( 'Normal Shadow', 'widgetkit-for-elementor' ),
					'exclude' => [
						'box_shadow_position',
					],
					'selector' => '{{WRAPPER}} .tgx-hover-image',
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'modal_tab_button_hover',
				[
					'label' => esc_html__( 'Hover', 'widgetkit-for-elementor' ),
				]
			);

			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'hover_image_hover_box_shadow',
				'label' => esc_html__( 'Hover Shadow', 'widgetkit-for-elementor' ),
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .tgx-hover-image:hover',
			]
		);

			$this->end_controls_tab();

		$this->end_controls_tabs();



		$this->end_controls_section();


		$this->start_controls_section(
				'section_style_hover_image_overlay',
				[
					'label' => __( 'Background Overlay', 'widgetkit-for-elementor' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
			);



		$this->add_control(
			'hover_image_over_color',
			[
				'label' => __( 'Overlay', 'widgetkit-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'gradient',
				'options' => [
					'gradient'   => __( 'Gradient', 'widgetkit-for-elementor' ),
					'background' => __( 'Background', 'widgetkit-for-elementor' ),
				],
			]
		);



		$this->add_control(
			'hover_image_overlay_gradient_bg_color',
			[
				'label' => __( 'Gradient Color', 'widgetkit-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(252,0,63,0.97)',
				'selectors' => [
					'{{WRAPPER}} .tgx-hover-image .jazz-effect .hover-image,
					{{WRAPPER}} .tgx-hover-image .default-effect .hover-image,
					{{WRAPPER}} .tgx-hover-image .sadie-effect figcaption::before,
					{{WRAPPER}} .tgx-hover-image .bubba-effect .hover-image,
					{{WRAPPER}} .tgx-hover-image .goliath-effect:hover .image-caption' => 'background: linear-gradient(to bottom, transparent 0%, {{VALUE}} 75%);',
				],

				'condition' => [
					'hover_image_over_color' => 'gradient',
				],
			]
		);

		$this->add_control(
			'hover_image_overlay_bg_color',
			[
				'label' => __( 'Overlay Color', 'widgetkit-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(252,0,63,0.97)',
				'selectors' => [
				'{{WRAPPER}} .tgx-hover-image .jazz-effect .hover-image,
				{{WRAPPER}} .tgx-hover-image .default-effect .hover-image,
				{{WRAPPER}} .tgx-hover-image .sadie-effect figcaption::before,
				{{WRAPPER}} .tgx-hover-image .bubba-effect .hover-image,
				{{WRAPPER}} .tgx-hover-image .goliath-effect:hover .image-caption' => 'background: {{VALUE}};',

				],

				'condition' => [
					'hover_image_over_color' => 'background',
				],
			]
		);

			$this->add_control(
				'hover_image_opacity',
				[
					'label' => __( 'Opacity (%)', 'widgetkit-for-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'max' => 1,
							'min' => 0.10,
							'step' => 0.01,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .tgx-hover-image .default-effect:hover img, 
						{{WRAPPER}} .tgx-hover-image .jazz-effect:hover img,
						{{WRAPPER}} .tgx-hover-image .goliath-effect:hover .image-caption,
						{{WRAPPER}} .tgx-hover-image .sadie-effect:hover figcaption::before' => 'opacity: {{SIZE}};',
					],
				]
			);


			$this->add_control(
				'hover_image_caption_border_before',
				[
					'label' => __( 'Border Top/Bottom', 'widgetkit-for-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'max' => 1,
						],
					],

				'selectors' => [
					'{{WRAPPER}} .tgx-hover-image .bubba-effect .hover-image::before' => 'border-top: {{SIZE}}{{UNIT}} solid; border-bottom: {{SIZE}}{{UNIT}} solid;',
				],
				'condition' => [
					'hover_image_hover_animation' => 'bubba-effect',
				],
				]
			);

			$this->add_control(
				'hover_image_caption_border_after',
				[
					'label' => __( 'Border Left/Right', 'widgetkit-for-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'max' => 1,
						],
					],

				'selectors' => [
					'{{WRAPPER}} .tgx-hover-image .bubba-effect .hover-image::after' => 'border-right: {{SIZE}}{{UNIT}} solid; border-left: {{SIZE}}{{UNIT}} solid;',
				],
				'condition' => [
					'hover_image_hover_animation' => 'bubba-effect',
				],
				]
			);

			$this->add_control(
			'hover_image_caption_border_before_after_color',
			[
				'label' => __( 'Border Color', 'widgetkit-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-hover-image .bubba-effect .hover-image::before' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .tgx-hover-image .bubba-effect .hover-image::after' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'hover_image_hover_animation' => 'bubba-effect',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_caption_hover_image',
			[
				'label' => __( 'Caption', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'hover_image_caption_title!' => '',
				],

			]
		);

		$this->add_control(
			'hover_image_caption_align',
			[
				'label' => __( 'Alignment', 'widgetkit-for-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'widgetkit-for-elementor' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'widgetkit-for-elementor' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'widgetkit-for-elementor' ),
						'icon' => 'fa fa-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'widgetkit-for-elementor' ),
						'icon' => 'fa fa-align-justify',
					],
				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .tgx-hover-image .image-caption' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_image_title_color',
			[
				'label' => __( 'Title Color', 'widgetkit-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-hover-image .image-caption .caption-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'hover_image_caption_title_typography',
				'selector' => '{{WRAPPER}} .tgx-hover-image .image-caption .caption-title',
				'scheme' => Typography::TYPOGRAPHY_3,
			]
		);

		$this->add_control(
			'hover_image_content_color',
			[
				'label' => __( 'Content Color', 'widgetkit-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-hover-image .image-caption .caption-content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'hover_image_caption_content_typography',
				'selector' => '{{WRAPPER}} .widget-image-caption',
				'scheme' => Typography::TYPOGRAPHY_3,
			]
		);


		$this->add_responsive_control(
			'hover_image_caption_space',
			[
				'label' => __( 'Spacing', 'widgetkit-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				// 'default' => [
				// 	'unit' => '100',
				// ],
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tgx-hover-image .jazz-effect .caption-title' => 'padding-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .tgx-hover-image .sadie-effect .caption-content' => 'margin: {{SIZE}}{{UNIT}} 0;',
					'{{WRAPPER}} .tgx-hover-image .bubba-effect .image-caption' => 'margin: {{SIZE}}{{UNIT}} 0;',
				],

				'condition' => [
                   'hover_image_hover_animation' => ['jazz-effect', 'sadie-effect', 'bubba-effect'],
               ],

			]
		);

				$this->add_responsive_control(
			'hover_image_default_padding',
			[
				'label' => __( 'Padding', 'widgetkit-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tgx-hover-image .default-effect .image-caption,
					 {{WRAPPER}} .tgx-hover-image .goliath-effect .image-caption .caption-title,
					 {{WRAPPER}} .tgx-hover-image .goliath-effect .image-caption .caption-content,
					 {{WRAPPER}} .tgx-hover-image .sadie-effect .image-caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],

				'condition' => [
                   'hover_image_hover_animation' => ['default-effect', 'goliath-effect', 'sadie-effect'],
               ],

			]
		);

		$this->add_control(
			'hover_image_caption_bg_color',
			[
				'label' => __( 'Background Color', 'widgetkit-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}  .tgx-hover-image .default-effect .image-caption,
					{{WRAPPER}}  .tgx-hover-image .goliath-effect .image-caption .caption-content' => 'background-color: {{VALUE}};',
				],

				'condition' => [
                   'hover_image_hover_animation' => ['default-effect'],
               ],
			]
		);



		$this->end_controls_section();

	}

	protected function render() {
		require WK_PATH . '/elements/hover-image/template/view.php';
	}


}
