<?php

use Elementor\Group_Control_Border;
use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
Use Elementor\Core\Schemes\Typography;
use Elementor\Controls_Stack;


if ( ! defined( 'ABSPATH' ) ) exit;

class WKFE_Feature_Click_Tweet_Config extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-click-to-tweet';
	}

	public function get_title() {
		return esc_html__( 'Click To Tweet', 'widgetkit-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-twitter-feed wk-icon';
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
            'fontawesome',
            'widgetkit_main',
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

	protected function register_controls() {

		/**
		 * Text input config
		 */
		$this->start_controls_section(
			'tweet_text_input_layout', 
			[
				'label' => esc_html__('Layout', 'widgetkit-for-elementor')
			]
		);
			$this->add_control(
				'tweet_text',
					[
					'label' => esc_html__( 'Text', 'widgetkit-for-elementor' ),
					'type'  => Controls_Manager::TEXTAREA,
					'default' => esc_html__( 'Pick your favourite quotes and use them as Tweets, or write your own Tweets for your reads to share ', 'widgetkit-for-elementor' ),
					]
			);	
		$this->end_controls_section();

		/**
		 * Button input config
		 */
		$this->start_controls_section(
			'tweet_button_input_layout', 
			[
				'label' => esc_html__('Button', 'widgetkit-for-elementor')
			]
		);
		
		$this->add_control(
			'tweet_button_icon',
				[
				'label' => esc_html__( 'Button Icon', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::ICON,
				'default' => esc_html__( 'fa fa-twitter', 'widgetkit-for-elementor' ),
				]
		);
		$this->add_control(
			'tweet_button_text',
				[
				'label' => esc_html__( 'Button Text', 'widgetkit-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
				'default' => esc_html__( 'Tweet', 'widgetkit-for-elementor' ),
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
					'selector' => '{{WRAPPER}} .wkfe-click-to-tweet .tweet-text',
				]
		);
		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333',
				'selectors' => [
					'{{WRAPPER}} .wkfe-click-to-tweet .tweet-text' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/**
		 * button style
		 */
		$this->start_controls_section(
			'button_style_layout', 
			[
				'label' => esc_html__('Button', 'widgetkit-for-elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			$this->add_responsive_control(
				'button_icon_spacing',
				[
					'label'   => esc_html__( 'Icon spacing', 'widgetkit-for-elementor' ),
					'type'    => Controls_Manager::SLIDER,
					'selectors' => [
						'{{WRAPPER}} .wkfe-click-to-tweet .button-wrapper button span.icon-wrapper' => 'margin-right: {{SIZE}}{{UNIT}};',
					],
				]
			);
			$this->add_responsive_control(
				'button_align',
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
					],
					'selectors' => [
						'{{WRAPPER}} .wkfe-click-to-tweet .button-wrapper' => 'text-align: {{VALUE}};',
					],
				]
			);
			/**
			 * Normal Style
			 */
			$this->start_controls_tabs( 'tabs_button_style' );
			$this->start_controls_tab(
				'tweet-button-style-normal',
				[
					'label' => __('Normal', 'widgetkit-for-elementor'),
				]
			);
				$this->add_group_control(
					Group_Control_Typography::get_type(),
						[
							'name'     => 'button_typography',
							'label'    => esc_html__( 'Font', 'widgetkit-for-elementor' ),
							'scheme'   => Typography::TYPOGRAPHY_4,
							'selector' => '{{WRAPPER}} .wkfe-click-to-tweet .button-wrapper button',
						]
				);
				$this->add_control(
					'button_color',
					[
						'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '#fff',
						'selectors' => [
							'{{WRAPPER}} .wkfe-click-to-tweet .button-wrapper button' => 'color: {{VALUE}};',
						],
					]
				);
				$this->add_control(
					'button_background',
					[
						'label'     => esc_html__( 'Background', 'widgetkit-for-elementor' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '#00acee',
						'selectors' => [
							'{{WRAPPER}} .wkfe-click-to-tweet .button-wrapper button' => 'background-color: {{VALUE}};',
						],
					]
				);
				$this->add_group_control(
					Group_Control_Border::get_type(), 
					[
						'name'          => 'Border',
						'selector'      => '{{WRAPPER}} .button-wrapper button',
					]
				);
				$this->add_control(
					'button_border_radius',
					[
						'label'   => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
						'type'    => Controls_Manager::SLIDER,
						'selectors' => [
							'{{WRAPPER}} .wkfe-click-to-tweet .button-wrapper button' => 'border-radius: {{SIZE}}{{UNIT}};',
						],
					]
				);
				$this->add_responsive_control(
					'button_padding',
					[
						'label'         => __('Padding', 'widgetkit-for-elementor'),
						'type'          => Controls_Manager::DIMENSIONS,
						'size_units'    => ['px','%'],
						'separator'     => 'before',
						'selectors'     => [
							'{{WRAPPER}} .wkfe-click-to-tweet .button-wrapper button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
						],
					]
				);
				$this->add_responsive_control(
					'button_margin',
					[
						'label'         => __('Margin', 'widgetkit-for-elementor'),
						'type'          => Controls_Manager::DIMENSIONS,
						'size_units'    => ['px','%'],
						'selectors'     => [
							'{{WRAPPER}} .wkfe-click-to-tweet .button-wrapper button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
						],
					]
				);
			$this->end_controls_tab();
			/**
			 * Hover style
			 */
			$this->start_controls_tab(
				'tweet-button-style-hover',
				[
					'label' => __('Hover', 'widgetkit-for-elementor'),
				]
			);
				$this->add_control(
					'button_hover_color',
					[
						'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '#00acee',
						'selectors' => [
							'{{WRAPPER}} .wkfe-click-to-tweet .button-wrapper button:hover' => 'color: {{VALUE}};',
						],
					]
				);
				$this->add_control(
					'button_hover_background',
					[
						'label'     => esc_html__( 'Background', 'widgetkit-for-elementor' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '#fff',
						'selectors' => [
							'{{WRAPPER}} .wkfe-click-to-tweet .button-wrapper button:hover' => 'background-color: {{VALUE}};',
						],
					]
				);
			$this->end_controls_tab();
			$this->end_controls_tabs();

		$this->end_controls_section();

	

	}

	protected function render() {
		require WK_PATH . '/elements/click-tweet/template/view.php';
	}


}
