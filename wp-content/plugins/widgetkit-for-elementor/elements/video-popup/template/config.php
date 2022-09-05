<?php
Use Elementor\Core\Schemes\Typography;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor WidgetKit Video Popup
 *
 * Elementor widget for WidgetKit video popup
 *
 * @since 1.0.0
 */

class wkfe_video_popup extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-video-popup';
	}

	public function get_title() {
		return esc_html__( 'Video Popup', 'widgetkit-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-video-camera';
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
            'uikit',
        ];
    }
	/**
	 * A list of scripts that the widgets is depended in
	 **/
	public function get_script_depends() {
		return [ 
			'widgetkit-main',
            // 'vanilla-tilt',
            'youtube-popup',
            // 'magnific-popup',
            'uikit-js',
            'uikit-icons',
		 ];
	}

protected function _register_controls()
    {

    $this->start_controls_section(

        'section_video',
        [
            'label' => __('Video', 'widgetkit-for-elementor'),
        ]
    );

        $this->add_control(
            'video_style',
            [
                'label'   => __('Choose Style', 'widgetkit-for-elementor'),
                'type'    => Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '1'   => __('Icon', 'widgetkit-for-elementor'),
                    '2'   => __('Icon With Text', 'widgetkit-for-elementor'),
                    '3'   => __('Short Brive', 'widgetkit-for-elementor'),
                    '4'   => __('Long Brive', 'widgetkit-for-elementor'),
                    '5'   => __('Border', 'widgetkit-for-elementor'),
                ],
            ]
        );
        $this->add_control(
            'popup_text',
            [
                'label'       => __('Video PopUp Text', 'widgetkit-for-elementor'),
                'type'        => Controls_Manager::TEXT,
                'default'     => __('Play Video', 'widgetkit-for-elementor'),
                'placeholder' => __('Play Video', 'widgetkit-for-elementor'),
                'condition' => [
                    'video_style' => '2',
                ],
            ]
        );
        $this->add_control(
            'video_type',
            [
                'label'   => __('Video Source', 'widgetkit-for-elementor'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'youtube',
                'options' => [
                    'youtube' => __('YouTube', 'widgetkit-for-elementor'),
                    'vimeo'   => __('Vimeo', 'widgetkit-for-elementor'),
                ],
            ]
        );

        $this->add_control(
            'video_link',
            [
                'label'       => __('Link', 'widgetkit-for-elementor'),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => __('Enter your YouTube link', 'widgetkit-for-elementor'),
                'default'     => 'https://www.youtube.com/watch?v=9uOETcuFjbE',
                'label_block' => true,
                'condition'   => [
                    'video_type' => 'youtube',
                ],
            ]
        );

        $this->add_control(
            'vimeo_link',
            [
                'label'       => __('Link', 'widgetkit-for-elementor'),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => __('Enter your Vimeo link', 'widgetkit-for-elementor'),
                'default'     => 'https://vimeo.com/235215203',
                'label_block' => true,
                'condition'   => [
                    'video_type' => 'vimeo',
                ],
            ]
        );

        $this->add_control(
            'image_overlay',
            [
                'label'   => __('Image', 'widgetkit-for-elementor'),
                'type'    => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'video_style' => array('wk-video-popup-left', 'wk-video-popup-right', 'wk-video-popup-round'),
                ],
            ]
        );
    $this->end_controls_section();

    $this->start_controls_section(

        'section_video_control',
        [
            'label' => __('Control', 'widgetkit-for-elementor'),
        ]
    );


        // YouTube.
        $this->add_control(
            'yt_autoplay',
            [
                'label' => __('Autoplay', 'widgetkit-for-elementor'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );
        $this->add_responsive_control(
            'align',
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
                    '{{WRAPPER}} .wk-video-popup-wrapper' => 'text-align: {{VALUE}};',
                ],
                'default'   => 'center',
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_video_style',
            [
                'label' => __('Video', 'widgetkit-for-elementor'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label'     => __('Color', 'widgetkit-for-elementor'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wk-video-popup-wrapper .video i'                  => 'color: {{VALUE}};',
                    '{{WRAPPER}} .wk-video-popup-wrapper .style-2, {{WRAPPER}} .wk-video-popup-wrapper .style-2 i'                  => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .wk-video-popup-wrapper a.play-icon-text'          => 'color: {{VALUE}};',
                    '{{WRAPPER}} .wk-video-popup-wrapper .play-btn::after'          => 'border-left-color: {{VALUE}};',
                    '{{WRAPPER}} .wk-video-popup-wrapper .play-btn:before'          => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .wk-video-popup-wrapper .play-btn'                 => 'Background: radial-gradient(rgba(255, 188, 0, 0.94) 60%, {{VALUE}} 62%);',
                    '{{WRAPPER}} .wk-video-popup-wrapper .video-play-button span'   => 'border-left-color: {{VALUE}};',
                ],
                // 'condition' => [
                //     'video_style' => array('1', '2', '3', '4', '5'),
                // ],
            ]
        );
        $this->add_control(
            'video_border_color',
            [
                'label'     => __('Background', 'widgetkit-for-elementor'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wk-video-popup::before'                  => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .wk-video-popup a.icon-video'             => 'color: {{VALUE}};',
                    '{{WRAPPER}} .wk-video-popup a.icon-video:hover'       => 'background: {{VALUE}};',

                    '{{WRAPPER}} .wk-video-popup-round'                    => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .wk-video-popup-round a.icon-video'       => 'color: {{VALUE}};',
                    '{{WRAPPER}} .wk-video-popup-round a.icon-video:hover' => 'background: {{VALUE}};',

                    '{{WRAPPER}} .wk-video-popup-left::before'             => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .wk-video-popup-left a.icon-video'        => 'color: {{VALUE}};',
                    '{{WRAPPER}} .wk-video-popup-left a.icon-video:hover'  => 'background: {{VALUE}};',
                    '{{WRAPPER}} .wk-video-popup-wrapper .video i'  => 'background: {{VALUE}};',
                    '{{WRAPPER}} .wk-video-popup-wrapper .play-btn'  => 'background: {{VALUE}};',
                ],
                'condition' => [
                    'video_style' => array('1', '3', '4'),
                ],
            ]
        );


        $this->add_control(
            'play_bg_size',
            [
                'label'     => __('Play Background Size', 'widgetkit-for-elementor'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 250,
                    ],
                ],
                'selectors' => [
                    // '{{WRAPPER}} .wk-video-popup a.icon-video'       => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
                    // '{{WRAPPER}} .wk-video-popup-round a.icon-video' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
                    // '{{WRAPPER}} .wk-video-popup-left a.icon-video'  => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .wk-video-popup-wrapper .video.style-1 i'  => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'video_style' => array('1'),
                ],
            ]
        );

        $this->add_control(
            'play_icon_size',
            [
                'label'     => __('Play Icon Size', 'widgetkit-for-elementor'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 80,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wk-video-popup-wrapper .video.style-1 i'       => 'font-size: {{SIZE}}{{UNIT}};',
                    // '{{WRAPPER}} .wk-video-popup-round i.icofont-ui-play' => 'font-size: {{SIZE}}{{UNIT}};',
                    // '{{WRAPPER}} .wk-video-popup-left i.icofont-ui-play'  => 'font-size: {{SIZE}}{{UNIT}};',
                    // '{{WRAPPER}} .wk-video-popup-wrapper .video i'  => 'font-size: {{SIZE}}{{UNIT}};',
                    // '{{WRAPPER}} .wk-video-popup-left a.icon-video'  => 'font-size: {{SIZE}}{{UNIT}};',
                    // '{{WRAPPER}} .wk-video-popup-wrapper .play-icon-text.style-2 i'  => 'font-size: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'video_style' => array('1'),
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'Content_typography',
                'selector' => '{{WRAPPER}} .wk-video-popup-wrapper a.play-icon-text',
                'scheme' => Typography::TYPOGRAPHY_3,
                'condition' => [
                    'video_style' => array('2'),
                ],
            ]
        );
        $this->end_controls_section();

    }


	protected function render() {
        require WK_PATH . '/elements/video-popup/template/view.php';

    }


    public function _content_template() {
    }
}
