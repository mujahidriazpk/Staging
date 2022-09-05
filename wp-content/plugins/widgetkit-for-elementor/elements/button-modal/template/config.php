<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
Use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

/**
 * Elementor WidgetKit Button Modal
 *
 * Elementor widget for WidgetKit button modal
 *
 * @since 1.0.0
 */
class wkfe_modal_button extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-button';
	}

	public function get_title() {
		return esc_html__( 'Button + Modal', 'widgetkit-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-button wk-icon';
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
			'modal_section_button',
			[
				'label' => esc_html__( 'Button', 'widgetkit-for-elementor' ),
			]
		);

		$this->add_control(
			'button_options_select',
				[
					'label'     => esc_html__( 'Choose Button', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'btn',
					'options'   => [
						'btn'      => esc_html__( 'Normal ', 'widgetkit-for-elementor' ),
						'modal'    => esc_html__( 'Modal ', 'widgetkit-for-elementor' ),
					],
				]
		);


		$this->add_control(
			'button_modal_hover_effect',
				[
					'label'     => esc_html__( 'Choose Hover Effect', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'lfr',
					'options'   => [
						'default'    => esc_html__( 'Default', 'widgetkit-for-elementor' ),
						'border'     => esc_html__( 'Border Square', 'widgetkit-for-elementor' ),
						'lfr'        => esc_html__( 'Left From Right', 'widgetkit-for-elementor' ),
						'afl'        => esc_html__( 'Angle From Left', 'widgetkit-for-elementor' ),
						'bfm'        => esc_html__( 'Both From Middle', 'widgetkit-for-elementor' ),
						'piramid'    => esc_html__( 'Pyramid', 'widgetkit-for-elementor' ),
						'door'    	 => esc_html__( 'Door', 'widgetkit-for-elementor' ),
						'ctm'    	 => esc_html__( 'Corner to Middle', 'widgetkit-for-elementor' ),
						'fourcorner' => esc_html__( 'Four Corner', 'widgetkit-for-elementor' ),
						'slice'      => esc_html__( 'Slice', 'widgetkit-for-elementor' ),
					],
				]
		);



		$this->add_control(
			'modal_text',
			[
				'label' => esc_html__( 'Button Text', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Click Here', 'widgetkit-for-elementor' ),
				'placeholder' => esc_html__( 'Click Here', 'widgetkit-for-elementor' ),
			]
		);

		$this->add_control(
			'normal_btn_link',
			[
				'label' => esc_html__( 'Button Link', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::URL,
				'placeholder' => 'http://your-link.com',
				'default' => [
					'url' => 'http://themesgrove.com',
				],
				'condition' => [
					'button_options_select' => 'btn',
				],
			]
		);


		$this->add_control(
			'modal_tile',
			[
				'label' => esc_html__( 'Modal Top Title', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Book Your Meeting', 'widgetkit-for-elementor' ),
				'condition' => [
					'button_options_select' => 'modal',
				],
			]
		);

		$this->add_control(
			'modal_content',
				[
					'label'     => esc_html__( 'Modal Content', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'modal_shortcode',
					'options'   => [
						'modal_video'        => esc_html__( 'Video', 'widgetkit-for-elementor' ),
						'modal_shortcode'    => esc_html__( 'Shortcode', 'widgetkit-for-elementor' ),
					],
					'condition' => [
					'button_options_select' => 'modal',
				],
				]
		);
		$this->add_control(
			'modal_shortcode',
			[
				'label' => esc_html__( 'Shortcode', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXTAREA,
				'default'   => esc_html__( '[contact-form-7 id="4" title="Corporate"]', 'widgetkit-for-elementor' ),
				'condition' => [
					'modal_content' => 'modal_shortcode',
					'button_options_select' => 'modal',
				],
			]
		);

		$this->add_control(
			'modal_video',
			[
				'label' => esc_html__( 'Embed Video', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXTAREA,
				'default' => esc_html__( '<iframe width="560" height="315" src="https://www.youtube.com/embed/GqiST256XPA" frameborder="0" gesture="media" allow="encrypted-media" allowfullscreen></iframe>', 'widgetkit-for-elementor' ),
				'condition' => [
					'modal_content' => 'modal_video',
					'button_options_select' => 'modal',
				],
			]
		);


		$this->add_control(
			'modal_icon',
			[
				'label' => esc_html__( 'Icon', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::ICON,
				'label_block' => true,
				'default' => '',
			]
		);

		$this->add_control(
			'modal_icon_align',
			[
				'label' => esc_html__( 'Icon Position', 'widgetkit-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'  => esc_html__( 'Before', 'widgetkit-for-elementor' ),
					'right' => esc_html__( 'After', 'widgetkit-for-elementor' ),
				],
				'condition' => [
					'modal_icon!' => '',
				],
			]
		);

		$this->add_control(
			'modal_icon_font_size',
			[
				'label' => esc_html__( 'Icon Size', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
                       'size' => 16,
                ],
				'range'  => [
					'px' => [
						'max' => 50,
					],
				],
				'condition' => [
					'modal_icon!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .modal-container .click-btn button i,
					{{WRAPPER}} .modal-container .click-btn .button-normal i' => 'font-size:{{SIZE}}{{UNIT}};',
				],
			]
		);




		$this->add_control(
			'modal_icon_speacing_modal',
			[
				'label' => esc_html__( 'Icon Spacing', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .modal-container .click-btn button i,
					 {{WRAPPER}} .modal-container .click-btn .button-normal i,
					 {{WRAPPER}} .modal-container .btn-line i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'modal_icon!' => '',
				],
			]
		);



		$this->add_responsive_control(
			'modal_align',
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
					'justify' => [
						'title' => esc_html__( 'Justified', 'widgetkit-for-elementor' ),
						'icon'  => 'fa fa-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .modal-container .click-btn,
					{{WRAPPER}} .modal-container .btn-border-modal,
					{{WRAPPER}} .modal-container .click-btn .button-normal' => 'text-align: {{VALUE}};',
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
			'modal_section_style',
			[
				'label' => esc_html__( 'Button Style', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'modal_typography',
				'label'    => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
				'scheme'   => Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .modal-container .click-btn button,
				{{WRAPPER}} .modal-container .click-btn .button-normal,
				{{WRAPPER}} .modal-container .btn-line a p',
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'modal_tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'widgetkit-for-elementor' ),
			]
		);

		$this->add_control(
			'modal_button_text_color',
			[
				'label'   => esc_html__( 'Text Color', 'widgetkit-for-elementor' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#444',
				'selectors' => [
					'{{WRAPPER}} .modal-container .click-btn button,
					 {{WRAPPER}} .modal-container .click-btn .button-normal,
					 {{WRAPPER}} .modal-container .btn-line p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'modal_background_color',
			[
				'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .modal-container .click-btn button,
					 {{WRAPPER}} .modal-container .click-btn .button-normal,
					 {{WRAPPER}} .modal-container .btn-line a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'  => 'modal_border',
				'label' => esc_html__( 'Border', 'widgetkit-for-elementor' ),
				'placeholder' => '1px',
				'default'   => '1px',
				'selector'  => '{{WRAPPER}} .modal-container .click-btn button, 
				{{WRAPPER}} .modal-container .click-btn .button-normal,
				{{WRAPPER}} .modal-container .btn-line a',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'modal_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .modal-container .click-btn button,
					{{WRAPPER}} .modal-container .click-btn .button-normal,
					{{WRAPPER}} .modal-container .btn-line a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'modal_text_padding',
			[
				'label' => esc_html__( 'Text Padding', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .modal-container .click-btn button,
					{{WRAPPER}} .modal-container .click-btn .button-normal,
					{{WRAPPER}} .modal-container .btn-line a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'modal_tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'widgetkit-for-elementor' ),
			]
		);

		$this->add_control(
			'modal_hover_color',
			[
				'label' => esc_html__( 'Text Color', 'widgetkit-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .modal-container .click-btn button:hover,
					 {{WRAPPER}} .modal-container .click-btn .button-normal:hover,
					 {{WRAPPER}} .modal-container .btn-line a:hover p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'modal_button_background_hover_color',
			[
				'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default' => '#056ddc',
				'selectors' => [
					'{{WRAPPER}} .modal-container .btn-hover-default:hover,
					 {{WRAPPER}} .modal-container .btn-hover-lfr:before,
					 {{WRAPPER}} .modal-container .btn-hover-door:before, 
					 {{WRAPPER}} .modal-container .btn-hover-door:after,
					 {{WRAPPER}} .modal-container .btn-hover-fourcorner:before, 
					 {{WRAPPER}} .modal-container .btn-hover-fourcorner:after, 
					 {{WRAPPER}} .modal-container .btn-hover-fourcorner span:before, 
					 {{WRAPPER}} .modal-container .btn-hover-fourcorner span:after,
					 {{WRAPPER}} .modal-container .btn-line a:hover' => 'background-color: {{VALUE}};',

					'{{WRAPPER}} .modal-container .btn-hover-afl:before,
					 {{WRAPPER}} .modal-container .btn-hover-piramid:before, 
					 {{WRAPPER}} .modal-container .btn-hover-piramid:after' => 'border-bottom: 50px solid {{VALUE}};',

					  '{{WRAPPER}} .modal-container .btn-hover-ctm:before' => 'border-bottom: 50px solid {{VALUE}};',
					  '{{WRAPPER}} .modal-container .btn-hover-ctm:after' => ' border-top: 50px solid {{VALUE}};',

					'{{WRAPPER}} .modal-container .btn-hover-bfm:before, 
					 {{WRAPPER}} .modal-container .btn-hover-bfm:after, 
					 {{WRAPPER}} .modal-container .btn-hover-bfm span:before, 
					 {{WRAPPER}} .modal-container .btn-hover-bfm span:after' => 'background-color:{{VALUE}};',
					 '{{WRAPPER}} .modal-container .btn-hover-slice:after' => ' border-color: transparent {{VALUE}} transparent transparent;',
					 '{{WRAPPER}} .modal-container .btn-hover-slice:before' => ' border-color: transparent transparent transparent {{VALUE}};',
				],

			]
		);

		$this->add_control(
			'modal_button_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::COLOR,
				'default'   => '#056ddc',
				'selectors' => [
					'{{WRAPPER}} .modal-container .click-btn button:hover,
					{{WRAPPER}} .modal-container .click-btn .button-normal:hover,' => 'border-color: {{VALUE}};',
					
					'{{WRAPPER}} .modal-container .btn-line a span' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'modal_btn_border_width',
			[
				'label' => esc_html__( 'Border Width', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .modal-container .btn-line a span.top,
					{{WRAPPER}} .modal-container .btn-line a span.bottom' => 'height: {{TOP}}{{UNIT}} ;',
					'{{WRAPPER}} .modal-container .btn-line a span.left,
					{{WRAPPER}} .modal-container .btn-line a span.right' => 'width: {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_modal_hover_effect' => 'border',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


		$this->start_controls_section(
			'modal_hover_effcet',
			[
				'label' => esc_html__( 'Modal Effect', 'widgetkit-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'button_options_select' => 'modal',
				],
			]
		);



	    $this->add_control(
			'modal_effect',
				[
					'label'     => esc_html__( 'Modal Effect', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'animate-zoom',
					'options'   => [
						'animate-zoom'     => esc_html__( 'From Zoom', 'widgetkit-for-elementor' ),
						'animate-top'      => esc_html__( 'From Top', 'widgetkit-for-elementor' ),
						'animate-right'    => esc_html__( 'From Right ', 'widgetkit-for-elementor' ),
						'animate-bottom'   => esc_html__( 'From Bottom', 'widgetkit-for-elementor' ),
						'animate-left'     => esc_html__( 'From Left', 'widgetkit-for-elementor' ),
						'animate-opacity' => esc_html__( 'From Opacity', 'widgetkit-for-elementor' ),
						'animate-spin'     => esc_html__( 'From Spin', 'widgetkit-for-elementor' ),
					],
				]
		);

		$this->add_control(
            'modal_top_heading',
            [
                'label' => esc_html__( 'Top Title', 'widgetkit-for-elementor' ),
                'type'  => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

		$this->add_control(
			'modal_title_top_color',
			[
				'label'   => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#444',
				'selectors' => [
					'{{WRAPPER}} .tgx-teal h2' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'    => 'modal_title_typography',
				'label'   => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
				'scheme'  => Typography::TYPOGRAPHY_4,
				'selector'=> '{{WRAPPER}} .tgx-teal h2',
			]
		);


		$this->add_control(
			'modal_popup_bg_color',
			[
				'label'   => esc_html__( 'Bg Color', 'widgetkit-for-elementor' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .tgx-modal-content' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'modal_popup_width_size',
			[
				'label' => esc_html__( 'Modal Width', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
                       'size' =>30,
                ],
				'range'  => [
					'%' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tgx-modal-content' => 'width:{{SIZE}}%;',
				],
			]
		);



		$this->end_controls_section();
	}


	protected function render() {
		require WK_PATH . '/elements/button-modal/template/view.php';
	}


}
