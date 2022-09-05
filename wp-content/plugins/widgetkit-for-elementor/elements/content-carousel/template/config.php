<?php

use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
Use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor WidgetKit carousel
 *
 * Elementor widget for WidgetKit carousel
 *
 * @since 1.0.0
 */
class wkfe_content_carousel extends Widget_Base {

	public function get_name() {
		return 'widgetkit-for-elementor-content-carousel';
	}

	public function get_title() {
		return esc_html__( 'Content Carousel', 'widgetkit-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-slider-album wk-icon';
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
            //'owl-css',
            'widgetkit_main',
            'uikit',
        ];
    }
	/**
	 * A list of scripts that the widgets is depended in
	 **/
	public function get_script_depends() {
		return [ 
			//'owl-carousel',
			'widgetkit-main',
			'uikit-js',
            'uikit-icons',
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
	

#	region layout config
		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__( 'Layout', 'widgetkit-for-elementor' ),
			]
		);

			$this->add_control(
				'center_mode_enable',
					[
						'label'     => esc_html__( 'Center Mode', 'widgetkit-for-elementor' ),
						'description' => 'You must have at least 4 items',
						'type'      => Controls_Manager::SWITCHER,
						'default'   => 'no',
						'yes'    => esc_html__( 'Yes', 'widgetkit-for-elementor' ),
						'no'     => esc_html__( 'No', 'widgetkit-for-elementor' ),
					]
			);

			$this->add_responsive_control(
				'item_column',
				[
					'label'   => __( 'Number of Colum', 'widgetkit-for-elementor' ),
					'type'    => Controls_Manager::NUMBER,
					'default' => 3,
					'min'     => 1,
					'max'     => 6,
					'step'    => 1,
					'condition' => [
						'center_mode_enable!' => 'yes',
					],
				]
			);


			$this->add_control(
				'column_gap',
					[
						'label'       => __( 'Colum Gap', 'widgetkit-for-elementor' ),
						'type' => Controls_Manager::SELECT,
						'default' => 'medium',
						'options' => [
							'collapse'=> __( 'None', 'widgetkit-for-elementor' ),
							'small'   => __( 'Small', 'widgetkit-for-elementor' ),
							'medium'  => __( 'Medium', 'widgetkit-for-elementor' ),
							'large'   => __( 'Large', 'widgetkit-for-elementor' ),
						],
					]
			);

			$this->add_control(
			'thumbnail_position',
				[
					'label' => __( 'Image Position', 'widgetkit-for-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'default' => 'top',
					'options' => [
						'top' => [
							'title' => __( 'Top', 'widgetkit-for-elementor' ),
							'icon' => 'eicon-v-align-top',
						],
						'bottom' => [
							'title' => __( 'Bottom', 'widgetkit-for-elementor' ),
							'icon' => 'eicon-v-align-bottom',
						],
					],
					'toggle' => false,
					'separator' => 'before',
				]
			);
			$this->add_group_control(
                Group_Control_Image_Size::get_type(),
                [
                    'label' => esc_html__('Image size', 'widgetkit-for-elementor'),
                    'name' => 'cc_image',
                    'default' => 'large',
                    'separator' => 'none',
                ]
            );

		$this->end_controls_section();
#	end region layout config


#	region content config
	$this->start_controls_section(
		'section_content',
		[
			'label' => esc_html__( 'Content', 'widgetkit-for-elementor' ), 
		]
	);

		$this->add_control(
			'item_option',
				[
					'label'     => esc_html__( 'Choose Content', 'widgetkit-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'custom_post',
					'options'   => [
						'custom_post'    => esc_html__( 'Custom', 'widgetkit-for-elementor' ),
						'blog_post'      => esc_html__( 'Post', 'widgetkit-for-elementor' ),
					],
				]
		);


		$repeater = new Repeater();

            $repeater->add_control(
               'content_thumb_image',
                    [
                      'label' => esc_html__( 'Image', 'widgetkit-for-elementor' ),
                      'type'  => Controls_Manager::MEDIA,
                      'default' => [
                        'url'   => Utils::get_placeholder_image_src(),
                      ],
                    ]
            );

            $repeater->add_control(
                'content_meta',
                    [
                      'label'   => esc_html__( 'Meta', 'widgetkit-for-elementor' ),
                      'type'    => Controls_Manager::TEXT,
                      'default' => esc_html__( 'Business', 'widgetkit-for-elementor' ),
                    ]
            );

            $repeater->add_control(
                'content_title',
                    [
                      'label'   => esc_html__( 'Title', 'widgetkit-for-elementor' ),
                      'type'    => Controls_Manager::TEXT,
                      'default' => esc_html__( 'Healthcare giant overcomes', 'widgetkit-for-elementor' ),
                    ]
            );

            $repeater->add_control(
                'content_content',
                    [
                      'label'   => esc_html__( 'Content', 'widgetkit-for-elementor' ),
                      'type'    => Controls_Manager::WYSIWYG,
                      'default' => esc_html__( 'The image of a company is very important. Would you want to work with a consultation company whose office was in shambles', 'widgetkit-for-elementor' ),
                    ]
            );

	        $repeater->add_control(
				'content_demo_link',
				[
					'label' => __( 'Link', 'widgetkit-for-elementor' ),
					'type'  => Controls_Manager::URL,
					'dynamic' => [
						'active'  => true,
					],
					'placeholder' => __( 'https://your-link.com', 'widgetkit-for-elementor' ),
					'separator'   => 'before',
				]
			);



        $this->add_control(
            'custom_content',
              [
                  'label'       => esc_html__( 'Custom Contents', 'widgetkit-for-elementor' ),
                  'type'        => Controls_Manager::REPEATER,
                  'fields'      => $repeater->get_controls(),
                  'show_label'  => true,
                   'separator'  => 'before',
                  'default'     => [
                      [
                        'content_meta'    => esc_html__( 'Business', 'widgetkit-for-elementor' ),
                        'content_title'       => esc_html__( 'Healthcare giant', 'widgetkit-for-elementor' ),
                        'content_content'     => esc_html__( 'The image of a company is very important. Would you want to work with a consultation company whose office was in shambles', 'widgetkit-for-elementor' ),
                        'content_thumb_image' => '',
                        'content_demo_link'   => '#',
         
                      ],
                      [
                        'content_meta'    => esc_html__( 'Consumer', 'widgetkit-for-elementor' ),
                        'content_title'       => esc_html__( 'A technology company', 'widgetkit-for-elementor' ),
                        'content_content'     => esc_html__( 'The image of a company is very important. Would you want to work with a consultation company whose office was in shambles', 'widgetkit-for-elementor' ),
                        'content_thumb_image' => '',
                        'content_demo_link'   => '#',
         
                      ],
                      [
                        'content_meta'    => esc_html__( 'Travel', 'widgetkit-for-elementor' ),
                        'content_title'       => esc_html__( 'Focus on core delivers', 'widgetkit-for-elementor' ),
                        'content_content'     => esc_html__( 'The image of a company is very important. Would you want to work with a consultation company whose office was in shambles', 'widgetkit-for-elementor' ),
                        'content_thumb_image' => '',
                        'content_demo_link'   => '#',
     
                        ],
                        [
                        'content_meta'    => esc_html__( 'Corporate', 'widgetkit-for-elementor' ),
                        'content_title'       => esc_html__( 'Focus on core delivers', 'widgetkit-for-elementor' ),
                        'content_content'     => esc_html__( 'The image of a company is very important. Would you want to work with a consultation company whose office was in shambles', 'widgetkit-for-elementor' ),
                        'content_thumb_image' => '',
                        'content_demo_link'   => '#',
     
                        ],
                  ],
                  'title_field' => '{{{content_title}}}',
                  'condition'   => [
                        'item_option' => 'custom_post',
                    ],
              ]
            );

	        $this->add_control(
				'custom_header_tag',
				[
					'label' => __( 'Title HTML Tag', 'widgetkit-for-elementor' ),
					'type'  => Controls_Manager::SELECT,
					'options' => [
						'h1' => 'H1',
						'h2' => 'H2',
						'h3' => 'H3',
						'h4' => 'H4',
						'h5' => 'H5',
						'h6' => 'H6',
						'div' => 'div',
						'span' => 'span',
						'p' => 'p',
					],
					'default' => 'h2',
					'condition' => [
                        'item_option' => 'custom_post',
                    ],
				]
			);

	        $this->add_control(
	            'cat_multiple_id',
	            [
	                'label' => __( 'From Category', 'widgetkit-for-elementor' ), 
	                'type' => Controls_Manager::SELECT2,
	                'options' => $cat_names,
	                'multiple' => true,
	                 'condition' => [
                        'item_option' => 'blog_post',
                    ],
	            ]
	        );

	        $this->add_control(
	            'post_show',
	            [
	                'label'   => __( 'Number of Post', 'widgetkit-for-elementor' ),
	                'type'    => Controls_Manager::NUMBER,
	                'default' => 4,
	                'min'     => -1,
	                'max'     => 100,
	                'step'    => 1,
	                'condition' => [
                        'item_option' => 'blog_post',
                    ],
	            ]
	        );

            $this->add_control(
                'items_order',
                [
                    'label' => esc_html__( 'Order', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::SELECT,
                    'default'  => 'ASC',
                    'options'  => [
                        'ASC'  => esc_html__( 'Asscending', 'widgetkit-for-elementor' ),
                        'DSC'  => esc_html__( 'Descending', 'widgetkit-for-elementor' ),
                    ],
                    'condition' => [
                        'item_option' => 'blog_post',
                    ],
                ]
            );
            $this->add_control(
                'items_orderby',
                [
                    'label' => esc_html__( 'Orderby', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::SELECT,
                    'default' => 'title',
                    'options' => [
                        'title'  => esc_html__( 'Title', 'widgetkit-for-elementor' ),
                        'date'   => esc_html__( 'Date', 'widgetkit-for-elementor' ),
                        'rand'   => esc_html__( 'Count', 'widgetkit-for-elementor' ),
                        'ID'     => esc_html__( 'Id', 'widgetkit-for-elementor' ),
                        'name'   => esc_html__( 'Name', 'widgetkit-for-elementor' ),
                        'comment_count'  => esc_html__( 'Comment Count', 'widgetkit-for-elementor' ),
                        'meta_value'     => esc_html__( 'Meta Value', 'widgetkit-for-elementor' ),
                    ],
                    'condition' => [
                        'item_option' => 'blog_post',
                    ],
                ]
            );


            $this->add_control(
                'meta_enable_heading',
                [
                    'label' => __( 'Meta', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'item_option' => 'blog_post',
                    ],
                ]
            );
            $this->add_control(
                'meta_enable',
                    [
                        'label'     => esc_html__( 'Display', 'widgetkit-for-elementor' ),
                        'type'      => Controls_Manager::SWITCHER,
                        'default'   => 'yes',
                        'yes'    => esc_html__( 'Yes', 'widgetkit-for-elementor' ),
                        'no'     => esc_html__( 'No', 'widgetkit-for-elementor' ),
                        'condition' => [
                            'item_option' => 'blog_post',
                        ],
                    ]
            );

            $this->add_control(
                'title_enable_heading',
                [
                    'label' => __( 'Title', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'item_option' => 'blog_post',
                    ],
                ]
            );

            $this->add_control(
                'title_word',
                [
                    'label'       => __( 'Word Count', 'widgetkit-for-elementor' ),
                    'type'    => Controls_Manager::NUMBER,
                    'default' => 5,
                    'min'     => 1,
                    'max'     => 100,
                    'step'    => 1,
                    'condition' => [
                        'item_option' => 'blog_post',
                    ],
                   
                ]
            );

           	$this->add_control(
				'post_header_tag',
				[
					'label' => __( 'Title HTML Tag', 'widgetkit-for-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'h1' => 'H1',
						'h2' => 'H2',
						'h3' => 'H3',
						'h4' => 'H4',
						'h5' => 'H5',
						'h6' => 'H6',
						'div' => 'div',
						'span' => 'span',
						'p' => 'p',
					],
					'default' => 'h2',
					'condition' => [
                        'item_option' => 'blog_post',
                    ],
				]
			);


            $this->add_control(
                'content_enable_heading',
                [
                    'label' => __( 'Content', 'widgetkit-for-elementor' ),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'item_option' => 'blog_post',
                    ],
                ]
            );

            $this->add_control(
                'content_enable',
                    [
                        'label'     => esc_html__( 'Display', 'widgetkit-for-elementor' ),
                        'type'      => Controls_Manager::SWITCHER,
                        'default'   => 'no',
                        'yes'    => esc_html__( 'Yes', 'widgetkit-for-elementor' ),
                        'no'     => esc_html__( 'No', 'widgetkit-for-elementor' ),
                        'condition' => [
                            'item_option' => 'blog_post',
                        ],
                    ]
            );


            $this->add_control(
                'content_word',
                [
                    'label'   => __( 'Word Count', 'widgetkit-for-elementor' ),
                    'type'    => Controls_Manager::NUMBER,
                    'default' => 20,
                    'min'     => 1,
                    'max'     => 100,
                    'step'    => 1,
                    'condition' => [
                        'content_enable' => 'yes',
                        'item_option' => 'blog_post',
                    ],
                ]
            );



        $this->end_controls_section();
#	end region content config

#	region controls config
		$this->start_controls_section(
			'section_controls',
			[
				'label' => esc_html__( 'Controls', 'widgetkit-for-elementor' ),
			]
		);

			$this->add_control(
				'set_mode_enable',
					[
						'label'     => esc_html__( 'Sets', 'widgetkit-for-elementor' ),
						'type'      => Controls_Manager::SWITCHER,
						'default'   => 'no',
						'yes'    => esc_html__( 'Yes', 'widgetkit-for-elementor' ),
						'no'     => esc_html__( 'No', 'widgetkit-for-elementor' ),
					]
			);


			$this->add_control(
				'autoplay_mode_enable',
					[
						'label'     => esc_html__( 'Autoplay', 'widgetkit-for-elementor' ),
						'type'      => Controls_Manager::SWITCHER,
						'default'   => 'no',
						'yes'    => esc_html__( 'Yes', 'widgetkit-for-elementor' ),
						'no'     => esc_html__( 'No', 'widgetkit-for-elementor' ),
					]
			);


			$this->add_control(
				'content_interval_option',
				[
					'label'   => __( 'Set Interval', 'widgetkit-for-elementor' ),
					'type'    => Controls_Manager::NUMBER,
					'default' => 5000,
					'min'     => 100,
					'max'     => 10000,
					'step'    => 10,
				]
			);


		$this->end_controls_section();
#	end region content config

#	region naviagation config			
		$this->start_controls_section(
			'navs_content',
				[
					'label' => esc_html__( 'Navigation', 'widgetkit-for-elementor' ),
				]
		);

				$this->add_control(
					'content_arrow_heading',
					[
						'label' => __( 'Arrow', 'widgetkit-for-elementor' ),
						'type'  => Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					'arrow_enable',
						[
							'label'     => esc_html__( 'Display', 'widgetkit-for-elementor' ),
							'type'      => Controls_Manager::SWITCHER,
							'default'   => 'no',
							'yes'    => esc_html__( 'Yes', 'widgetkit-for-elementor' ),
							'no'     => esc_html__( 'No', 'widgetkit-for-elementor' ),
						]
				);
				$this->add_control(
					'content_nav_heading',
					[
						'label' => __( 'Dot', 'widgetkit-for-elementor' ),
						'type'  => Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					'dot_enable',
						[
							'label'     => esc_html__( 'Display', 'widgetkit-for-elementor' ),
							'type'      => Controls_Manager::SWITCHER,
							'default'   => 'yes',
							'yes'    => esc_html__( 'Yes', 'widgetkit-for-elementor' ),
							'no'     => esc_html__( 'No', 'widgetkit-for-elementor' ),
						]
				);


		$this->end_controls_section();
#	end region navigation config

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
						'type'  => Controls_Manager::CHOOSE,
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
            'section_style_thumbnail',
            [
                'label' => esc_html__( 'Image', 'widgetkit-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

    
	           

	            $this->add_control(
	                'thumbnail_size',
	                [
	                    'label'       => __( 'Size', 'widgetkit-for-elementor' ),
	                    'type' => Controls_Manager::SELECT,
	                    'default' => 'large',
	                    'options' => [
	                    	'thumbnail'  => __( 'Thumbnail', 'widgetkit-for-elementor' ),
	                        'medium'     => __( 'Medium', 'widgetkit-for-elementor' ),
	                        'medium_large'  => __( 'Medium Large', 'widgetkit-for-elementor' ),
	                        'large'  => __( 'Large', 'widgetkit-for-elementor' ),
	                        'full'   => __( 'Full', 'widgetkit-for-elementor' ),
	                    ],
	                    'condition'  => [
	                        'item_option' => 'blog_post',
	                    ],
	                ]
	            );

	           $this->add_responsive_control(
	            'image_size',
	                [
	                    'label'   => esc_html__( 'Size(%)', 'widgetkit-for-elementor' ),
	                    'type'    => Controls_Manager::SLIDER,
	                    'default' => [
	                    'size'    => 100,
	                    ],
	                    'range'   => [
	                        '%'   => [
	                            'min' => 10,
	                            'max' => 100,
	                        ],
	                    ],
	                    'selectors' => [
	                        '{{WRAPPER}} .content-carousel .wk-card .wk-card-media-top img, {{WRAPPER}} .content-carousel .wk-card .wk-card-media-bottom img' => 'width: {{SIZE}}%;',
	                    ],
	                    'condition'  => [
	                        'item_option' => 'custom_post',
	                    ],
	                ]
	            );


	            $this->add_responsive_control(
	                'thumbnail_height_custom',
	                    [
	                        'label'   => esc_html__( 'Height(px)', 'widgetkit-for-elementor' ),
	                        'type'    => Controls_Manager::SLIDER,
	                        'default' => [
	                           'size' =>'',
	                        ],
	                        'range'  => [
	                            'px' => [
	                                'min' => 100,
	                                'max' => 2000,
	                            ],
	                        ],
	                        'selectors' => [
	                            '{{WRAPPER}} .content-carousel .wk-card .wk-card-media-top' => 'max-height: {{SIZE}}{{UNIT}};',
	                            '{{WRAPPER}} .content-carousel .wk-card .wk-card-media-bottom' => 'max-height: {{SIZE}}{{UNIT}};',
	                        ],
	                    ]
	                );


         $this->end_controls_section();

	

	    $this->start_controls_section(
            'section_style',
            [
                'label' => esc_html__( 'Content', 'widgetkit-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

	        $this->add_control(
	            'meta_heading',
	            [
	                'label' => __( 'Meta', 'widgetkit-for-elementor' ),
	                'type'  => Controls_Manager::HEADING,
	                'separator' => 'before',
	            ]
	        );

	        $this->add_control(
	            'meta_color',
	            [
	                'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
	                'type'      => Controls_Manager::COLOR,
	                'default'   => '#777',
	                'selectors' => [
	                    '{{WRAPPER}} .content-carousel .wk-card .wk-card-body span a, {{WRAPPER}} .content-carousel .wk-card .wk-card-body span ' => 'color: {{VALUE}};',
	                ],
	            ]
	        );

	        $this->add_group_control(
	            Group_Control_Typography::get_type(),
	                [
	                    'name'     => 'meta_typography',
	                    'label'    => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
	                    'scheme'   => Typography::TYPOGRAPHY_4,
	                    'selector' => '{{WRAPPER}} .content-carousel .wk-card .wk-card-body span',
	                ]
	        );

	            $this->add_control(
	                'meta_hover_color',
	                [
	                    'label'     => esc_html__( 'Hover Color', 'widgetkit-for-elementor' ),
	                    'type'      => Controls_Manager::COLOR,
	                    'default'   => '',
	                    'selectors' => [
	                        '{{WRAPPER}} .content-carousel .wk-card .wk-card-body span a:hover,
	                        {{WRAPPER}} .content-carousel .wk-card .wk-card-body span:hover' => 'color: {{VALUE}};',
	                    ],
	                ]
	            );


	        $this->add_responsive_control(
	            'meta_spacing',
	                [
	                    'label'   => esc_html__( 'Spacing', 'widgetkit-for-elementor' ),
	                    'type'    => Controls_Manager::SLIDER,
	                    'default' => [
	                    'size'    => 0,
	                    ],
	                    'range'   => [
	                        'px'  => [
	                            'min' => -10,
	                            'max' => 100,
	                        ],
	                    ],
	                    'selectors' => [
	                        '{{WRAPPER}} .content-carousel .wk-card .wk-card-body span' => 'margin: {{SIZE}}{{UNIT}} 0 0;',
	                    ],
	                ]
	            );

            $this->add_control(
                'title_heading',
                [
                    'label' => __( 'Title', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->add_control(
                'title_color',
                [
                    'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
                    'type'      => Controls_Manager::COLOR,
                    'default'   => '#222',
                    'selectors' => [
                        '{{WRAPPER}} .content-carousel .wk-card .wk-card-body .wk-card-title a' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                    [
                        'name'     => 'title_typography',
                        'label'    => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
                        'scheme'   => Typography::TYPOGRAPHY_4,
                        'selector' => '{{WRAPPER}} .content-carousel .wk-card .wk-card-body .wk-card-title',
                    ]
            );

            $this->add_control(
                'title_hover_color',
                [
                    'label'     => esc_html__( 'Hover Color', 'widgetkit-for-elementor' ),
                    'type'      => Controls_Manager::COLOR,
                    'default'   => '#0073aa',
                    'selectors' => [
                        '{{WRAPPER}} .content-carousel .wk-card .wk-card-body .wk-card-title a:hover' => 'color: {{VALUE}}; text-decoration:none;',
                    ],
                ]
            );

            $this->add_responsive_control(
                'title_spacing',
                    [
                        'label'   => esc_html__( 'Spacing', 'widgetkit-for-elementor' ),
                        'type'    => Controls_Manager::SLIDER,
                        'default' => [
                        'size'    => 0,
                        ],
                        'range'  => [
                            'px' => [
                                'min' => 0,
                                'max' => 100,
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .content-carousel .wk-card .wk-card-body .wk-card-title' => 'padding: {{SIZE}}{{UNIT}} 0;',
                        ],
                    ]
            );

	            $this->add_control(
	                'content_heading',
	                [
	                    'label' => __( 'Content', 'widgetkit-for-elementor' ),
	                    'type'  => Controls_Manager::HEADING,
	                    'separator' => 'before',
	                ]
	            );

	            $this->add_control(
	                'content_color',
	                [
	                    'label'     => esc_html__( 'Color', 'widgetkit-for-elementor' ),
	                    'type'      => Controls_Manager::COLOR,
	                    'default'   => '#777',
	                    'selectors' => [
	                        '{{WRAPPER}} .content-carousel .wk-card .wk-card-body p' => 'color: {{VALUE}};',
	                    ],
	                ]
	            );

	            $this->add_group_control(
	                Group_Control_Typography::get_type(),
	                    [
	                        'name'     => 'content_typography',
	                        'label'    => esc_html__( 'Typography', 'widgetkit-for-elementor' ),
	                        'scheme'   => Typography::TYPOGRAPHY_4,
	                        'selector' => '{{WRAPPER}} .content-carousel .wk-card .wk-card-body p',
	                    ]
	            );

                $this->add_control(
                    'global_heading',
                        [
                            'label' => __( 'Global', 'widgetkit-for-elementor' ),
                            'type'  => Controls_Manager::HEADING,
                            'separator' => 'before',
                        ]
                );


	            $this->add_control(
	                'content_bg_color',
	                [
	                    'label'     => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
	                    'type'      => Controls_Manager::COLOR,
	                    'default'   => '',
	                    'selectors' => [
	                        '{{WRAPPER}} .content-carousel .wk-card' => 'background: {{VALUE}};',
	                    ],
	                ]
	            );


	            $this->add_group_control(
	                Group_Control_Box_Shadow::get_type(),
	                [
	                    'name' => 'content_box_shadow',
	                    'exclude' => [
	                        'box_shadow_position',
	                    ],
	                    'selector' => '{{WRAPPER}} .content-carousel .wk-card',
	                ]
	            );

		    $this->add_control(
				'content_layout_align',
				[
					'label' => esc_html__( 'Alignment', 'widgetkit-for-elementor' ),
					'type'  => Controls_Manager::CHOOSE,
					'default'   => 'center',
					'options'   => [
						'left'    => [
							'title' => esc_html__( 'Left', 'widgetkit-for-elementor' ),
							'icon'  => 'eicon-text-align-left',
						],
						'center' => [
							'title' => esc_html__( 'Center', 'widgetkit-for-elementor' ),
							'icon'  => 'eicon-text-align-center',
						],
						'right' => [
							'title' => esc_html__( 'Right', 'widgetkit-for-elementor' ),
							'icon'  => 'eicon-text-align-left',
						],
					],
					'selectors' => [
						'{{WRAPPER}} .content-carousel .wk-card' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_control(
	            'content_padding',
	            [
	                'label' => esc_html__( 'Padding', 'widgetkit-for-elementor' ),
	                'type'  => Controls_Manager::DIMENSIONS,
	                'size_units' => [ 'px', '%' ],
	                'selectors'  => [
	                    '{{WRAPPER}} .content-carousel .wk-card .wk-card-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );

            $this->add_control(
                'content_border_radius',
                [
                    'label' => esc_html__( 'Radius', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors'  => [
                        '{{WRAPPER}} .content-carousel .wk-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
                    ],
                ]
            );

        $this->end_controls_section();

		// Navigation options Start
        $this->start_controls_section(
            'navs_style',
                [
                    'label' => esc_html__( 'Navigation', 'widgetkit-for-elementor' ),
                    'tab'   => Controls_Manager::TAB_STYLE,
                    // 'condition' => [
                    //     'dot_enable' => 'yes',
                        
                    // ],
                ]
        );

            $this->add_control(
                'arrow_heading',
                [
                    'label' => __( 'Arrow', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'arrow_enable' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'arrow_color',
                    [
                        'label' => esc_html__( 'Color', 'widgetkit-for-elementor' ),
                        'type'  => Controls_Manager::COLOR,
                        'default'   => '#fff',
                        'selectors' => [
                          '{{WRAPPER}} .content-carousel .wk-slidenav svg' => 'color: {{VALUE}};',
                        ],

                       'condition' => [
                            'arrow_enable' => 'yes',
                        ],
                    ]
            );

            $this->add_responsive_control(
	            'arrow_font_size',
	                [
	                    'label'  => esc_html__( 'Size', 'widgetkit-for-elementor' ),
	                    'type'   => Controls_Manager::SLIDER,
	                    'default'  => [
	                        'size' =>10,
	                    ],
	                    'range'  => [
	                        'px' => [
	                            'min' => 16,
	                            'max' => 24,
	                        ],
	                    ],
	                    'selectors' => [
	                        '{{WRAPPER}} .content-carousel .wk-slidenav svg' => 'width: {{SIZE}}{{UNIT}}; height:30px;',
	                    ],
	                    'condition' => [
                        	'arrow_enable' => 'yes',
                    	],
	                ]
            );


            $this->add_control(
                'arrow_background_color',
                    [
                        'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
                        'type'  => Controls_Manager::COLOR,
                        'default'   => '#ddd',
                        'selectors' => [
                          '{{WRAPPER}} .content-carousel .wk-slidenav' => 'background: {{VALUE}}; transition:all 0.3s ease;',
                        ],

                       'condition' => [
                            'arrow_enable' => 'yes',
                        ],
                    ]
            );

            $this->add_control(
                'arrow_hover_background_color',
                    [
                        'label' => esc_html__( 'Hover Background Color', 'widgetkit-for-elementor' ),
                        'type'  => Controls_Manager::COLOR,
                        'default'   => '#0073aa',
                        'selectors' => [
                          '{{WRAPPER}} .content-carousel .wk-slidenav:hover' => 'background: {{VALUE}}; transition:all 0.3s ease;',
                        ],

                       'condition' => [
                            'arrow_enable' => 'yes',
                        ],
                    ]
            );

            $this->add_control(
                'arrow_position',
                [
                    'label'       => __( 'Position', 'widgetkit-for-elementor' ),
                    'type' => Controls_Manager::SELECT,
                    'default'  => 'in',
                    'options'  => [
                        'in'   => __( 'In', 'widgetkit-for-elementor' ),
                        'out'  => __( 'Out', 'widgetkit-for-elementor' ),
                    ],
                   'condition' => [
                        'arrow_enable' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'arrow_on_hover',
                    [
                        'label'     => esc_html__( 'On Hover Mode', 'widgetkit-for-elementor' ),
                        'type'      => Controls_Manager::SWITCHER,
                        'default'   => 'no',
                        'yes'    => esc_html__( 'Yes', 'widgetkit-for-elementor' ),
                        'no'     => esc_html__( 'No', 'widgetkit-for-elementor' ),
                    	'condition' => [
							'arrow_enable' => 'yes',
							'arrow_position' => 'in',
						],
                    ]
	        );

            $this->add_control(
	            'arrow_border_radius',
	            [
	                'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
	                'type'  => Controls_Manager::DIMENSIONS,
	                'size_units' => [ 'px', '%' ],
	                'selectors'  => [
	                    '{{WRAPPER}} .content-carousel .wk-slidenav' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	                'condition' => [
                        'arrow_enable' => 'yes',
                    ],
                    'separator' => 'after',
	            ]
	        );


            $this->add_control(
                'dot_heading',
                [
                    'label' => __( 'Dot', 'widgetkit-for-elementor' ),
                    'type'  => Controls_Manager::HEADING,
                    'condition' => [
                        'dot_enable' => 'yes',
                    ],
                ]
            );



		    $this->start_controls_tabs( 'tabs_nav_style' );

		    $this->start_controls_tab(
		        'tab_nav_normal',
		          [
		            'label' => esc_html__( 'Normal', 'widgetkit-for-elementor' ),
		            'condition' => [
                        'dot_enable' => 'yes',
                    ],
		          ]
		    );

            $this->add_responsive_control(
	            'dot_normal_size',
	                [
	                    'label'  => esc_html__( 'Size', 'widgetkit-for-elementor' ),
	                    'type'   => Controls_Manager::SLIDER,
	                    'default'  => [
	                        'size' =>10,
	                    ],
	                    'range'  => [
	                        'px' => [
	                            'min' => 16,
	                            'max' => 24,
	                        ],
	                    ],
	                    'selectors' => [
	                        

	                        '{{WRAPPER}} .content-carousel .wk-dotnav li a' => 'width: {{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}}; transition: all 0.3s ease;',
	                    ],
	                    'condition' => [
                        	'dot_enable' => 'yes',
                    	],
	                ]
            );

            $this->add_control(
                'dot_background_color',
                    [
                        'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
                        'type'  => Controls_Manager::COLOR,
                        'default'   => '#777',
                        'selectors' => [
                          '{{WRAPPER}} .content-carousel .wk-dotnav li a' => 'background-color: {{VALUE}};',
                        ],
                        'condition' => [
                        	'dot_enable' => 'yes',
                    	],
                    ]
            );


	        $this->add_group_control(
	            Group_Control_Border::get_type(),
	            [
	                'name'  => 'dot_border',
	                'label' => esc_html__( 'Border', 'widgetkit-for-elementor' ),
	                'placeholder' => '1px',
	                'default'  => '1px',
	                'selector' => '
	                    {{WRAPPER}} .content-carousel .wk-dotnav li a',
	                'separator' => 'before',
	                'condition' => [
                        'dot_enable' => 'yes',
                    ],
	            ]
	        );

        	$this->add_control(
	            'dot_border_radius',
	            [
	                'label' => esc_html__( 'Border Radius', 'widgetkit-for-elementor' ),
	                'type'  => Controls_Manager::DIMENSIONS,
	                'size_units' => [ 'px', '%' ],
	                'selectors'  => [
	                    '{{WRAPPER}} .content-carousel .wk-dotnav li a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	                'condition' => [
                        'dot_enable' => 'yes',
                    ],
	            ]
	        );

		    $this->add_control(
				'dot_nav_align',
				[
					'label' => esc_html__( 'Alignment', 'widgetkit-for-elementor' ),
					'type'  => Controls_Manager::CHOOSE,
					'default'   => 'center',
					'options'   => [
						'left'    => [
							'title' => esc_html__( 'Left', 'widgetkit-for-elementor' ),
							'icon'  => 'eicon-text-align-left',
						],
						'center' => [
							'title' => esc_html__( 'Center', 'widgetkit-for-elementor' ),
							'icon'  => 'eicon-text-align-center',
						],
						'right' => [
							'title' => esc_html__( 'Right', 'widgetkit-for-elementor' ),
							'icon'  => 'eicon-text-align-left',
						],
					],
					'condition' => [
                       	'dot_enable' => 'yes',
                   	],
				]
			);

		    $this->end_controls_tab();


		    $this->start_controls_tab(
		        'tab_nav_hover',
		            [
		                'label' => esc_html__( 'Active', 'widgetkit-for-elementor' ),
		                'condition' => [
                        	'dot_enable' => 'yes',
                    	],
		            ]
		    );

       		$this->add_responsive_control(
	            'dot_active_size',
	                [
	                    'label'  => esc_html__( 'Size', 'widgetkit-for-elementor' ),
	                    'type'   => Controls_Manager::SLIDER,
	                    'default'  => [
	                        'size' =>20,
	                    ],
	                    'range'  => [
	                        'px' => [
	                            'min' => 10,
	                            'max' => 100,
	                        ],
	                    ],
	                    'selectors' => [
	                        

	                        '{{WRAPPER}} .content-carousel .wk-dotnav .wk-active a' => 'width: {{SIZE}}{{UNIT}}; transition: all 0.3s ease; border-radius: 10px;',
	                    ],
	                    'condition' => [
                        	'dot_enable' => 'yes',
                    	],
	                ]
            );

		    $this->add_control(
		        'dot_background_hover_color',
		            [
		                'label' => esc_html__( 'Background Color', 'widgetkit-for-elementor' ),
		                'type'  => Controls_Manager::COLOR,
		                'default'   => '#0073aa',
		                'selectors' => [
		                  '{{WRAPPER}} .content-carousel .wk-dotnav .wk-active a' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
		                ],
		                'condition' => [
                        	'dot_enable' => 'yes',
                    	],
		            ]
		    );

	    	$this->end_controls_tab();


	    $this->end_controls_tabs();

	$this->end_controls_section();

	// endif;

// Button options End
	}

	protected function render() {
		require WK_PATH . '/elements/content-carousel/template/view.php';
	}
	protected function _content_template()
    {
    }


}
