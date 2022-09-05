<?php 
    class WKFE_Dashboard_Changelog{
        private static $instance; 
        private $api_url = 'https://widgetkit.themesgrove.com/wp-json/wk/changelog';
        private $transient_changelog_data;

        public static function init(){
            if(null === self::$instance){
                self::$instance = new self;
            }
            return self::$instance;
        }

        public function __construct(){
            $this->transient_changelog_data = get_transient('changelog_data');
            $this->wkfe_dashboard_changelog_content();
        }
        public function wkfe_dashboard_changelog_content(){
            ?>
            <!-- WooCommerce -->
            <div class="wk-padding-remove">

                <?php 
                $Parsedown = new Parsedown();
                $get_changelog_data = $this->widgetkit_get_changelog_data();
                // $changelog_data = $get_changelog_data ? $get_changelog_data : $_SESSION['changes'];
                $changelog_data = $get_changelog_data ?: $this->transient_changelog_data;
                ?>

                <div class="wk-changelog-list">
                    <div class="wk-changes">
                        <ul class="version version-2.3.14.1 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.3.14.1</h4><span>14-09-2021</span></li>
                            <li><span class="wk-text-success">New </span> – LearnDash Course Content element key added.</li>
                            <li><span class="wk-text-success">New </span> – Event List element key added.</li>
                        </ul>
                        <ul class="version version-2.3.14 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.3.14</h4><span>17-06-2021</span></li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Tutor LMS key and panel added.</li>
                        </ul>
                        <ul class="version version-2.3.13 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.3.13</h4><span>30-05-2021</span></li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Namespace issue in Testimonial element.</li>
                        </ul>
                        <ul class="version version-2.3.12 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.3.12</h4><span>18-05-2021</span></li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Live editing issue.</li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Content Carousel responsive issue.</li>
                        </ul>
                        <ul class="version version-2.3.11 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.3.11</h4><span>03-05-2021</span></li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Elementor\Scheme_Typography Deprecation error fixed.</li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Elementor\Scheme_Color Deprecation error fixed.</li>
                        </ul>
                        <ul class="version version-2.3.10 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.3.10</h4><span>25-03-2021</span></li>
                            <li><span class="wk-text-improved">Improved </span> – Elementor compatible tag added for Content Carousel element.</li>
                            <li><span class="wk-text-improved">Improved </span> – Elementor compatible tag added for Slider Animation element.</li>
                            <li><span class="wk-text-improved">Improved </span> – Elementor compatible tag added for Slider Content element.</li>
                            <li><span class="wk-text-improved">Improved </span> – Elementor compatible tag added for Slider Box element.</li>
                            <li><span class="wk-text-improved">Improved </span> – Elementor compatible tag added for Team element.</li>
                            <li><span class="wk-text-improved">Improved </span> – Elementor compatible tag added for Testimonial element.</li>
                            <li><span class="wk-text-improved">Improved </span> – Slider Content Style issue.</li>
                        </ul>
                        <ul class="version version-2.3.9 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.3.9</h4><span>29-12-2020</span></li>
                            <li><span class="wk-text-success">New </span> – Mailchimp Element.</li>
                            <li><span class="wk-text-improved">Improved </span> – Testimonial Element.</li>
                        </ul>
                        <ul class="version version-2.3.8 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.3.8</h4><span>04-12-2020</span></li>
                            <li><span class="wk-text-improved">Improved </span> – Image size option added in Content Carousel Element.</li>
                            <li><span class="wk-text-improved">Improved </span> – Image size option added in Testimonial Element.</li>
                            <li><span class="wk-text-improved">Improved </span> – Image size option added in Team Element.</li>
                        </ul>
                        <ul class="version version-2.3.7.4 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.3.7.4</h4><span>26-11-2020</span></li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Body padding issue for wk contact form element.</li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Thank you notice always visibility in dashboard.</li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Update Testimonial layout 4 image alignment issue.</li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Filterable porfolio padding issue.</li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Title spacing issue fixed for Testimonial element.</li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Placeholder image added for Testimonial element.</li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Quote color updated for Testimonial element.</li>
                        </ul>
                        <ul class="version version-2.3.7.3 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.3.7.3</h4><span>19-11-2020</span></li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – widgetkit template library page style issue in dashboard.</li>
                        </ul>
                        <ul class="version version-2.3.7.2 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.3.7.2</h4><span>12-11-2020</span></li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Fix slug conflict with wishlist member plugin.</li>
                        </ul>
                        <ul class="version version-2.3.7.1 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.3.7.1</h4><span>08-11-2020</span></li>
                            <li><span class="wk-text-improved">Improved </span> – Nonce verification added for security measurements.</li>
                            <li><span class="wk-text-improved">Improved </span> – Check remote call response for changelog data before store it. </li>
                        </ul>
                        <ul class="version version-2.3.7 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.3.7</h4><span>27-09-2020</span></li>
                            <li><span class="wk-text-improved">Improved </span> – Dynamic option added for contact element</li>
                            <li><span class="wk-text-improved">Improved </span> – Demo link option  added for full image in filterable portfolio element</li>
                        </ul>
                        <ul class="version version-2.3.6 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.3.6</h4><span>12-08-2020</span></li>
                            <li><span class="wk-text-improved">Improved </span> – Optimize resourse loading issue</li>
                        </ul>
                        <ul class="version version-2.3.5 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.3.5</h4><span>21-07-2020</span></li>
                            <li><span class="wk-text-success">New </span> – Search Element.</li>
                            <li><span class="wk-text-success">New </span> – Site Social Element.</li>
                            <li><span class="wk-text-improved">Improved </span> – Responsive config added for Content Carousel Element</li>
                        </ul>
                        <ul class="version version-2.3.4 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.3.4</h4><span>11-06-2020</span></li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Fix button markup if there is no button text for slider content widget.</li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Fix Font Awesome Icon not loading in slider content element.</li>
                        </ul>
                        <ul class="version version-2.3.3 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.3.3</h4><span>31-05-2020</span></li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Fix Pricing Tab Element selected tab.</li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Fix Filterable Portfolio Element word spacing.</li>
                        </ul>
                        <ul class="version version-2.3.2 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.3.2</h4><span>21-04-2020</span></li>
                            <li><span class="wk-text-success">New </span> – Lottie Animaiton Widget.</li>
                            <li><span class="wk-text-success">New </span> – Lottie Animaiton Library Integration.</li>
                        </ul>
                        <ul class="version version-2.3.1 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.3.1</h4><span>25-02-2020</span></li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Fix session conflict with other plugin.</li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Fix conflict with Easy Digital Download plugin.</li>
                        </ul>
                        <ul class="version version-2.3 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.3</h4><span>12-02-2020</span></li>
                            <li><span class="wk-text-success">New </span> – Video element added</li>
                            <li><span class="wk-text-improved">Improved </span> – URL option added in Team Element</li>
                            <li><span class="wk-text-improved">Improved </span> – URL option added in Tilt box Element</li>
                            <li><span class="wk-text-improved">Improved </span> – Responsive style in Gallery Element</li>
                            <li><span class="wk-text-improved">Improved </span> – Border Radius option added in Content Carousel</li>
                            <li><span class="wk-text-improved">Improved </span> – Countdown element</li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Session issue while active the plugin</li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Site health warning</li>
                        </ul>
                        <ul class="version version-2.2.1 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.2.1</h4><span>09-01-2020</span></li>
                            <li><span class="wk-text-success">New </span> – Changelog Tab added in Dashboard</li>
                            <li><span class="wk-text-improved">Improved </span> – Demo link updated in dashboard</li>
                        </ul>
                        <ul class="version version-2.2.0 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.2.0</h4><span>17-12-2019</span></li>
                            <li><span class="wk-text-success">New </span> – Tilt Box Element</li>
                            <li><span class="wk-text-success">New </span> – Image Compare Element</li>
                            <li><span class="wk-text-success">New </span> – Contact Form Element</li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Element disable enable issue in Widgetkit Dashboard</li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – Conflict with weDocs plugin</li>
                            <li><span class="wk-text-improved">Improved </span> – Animation Headline element spacing</li>
                        </ul>
                        <ul class="version version-2.1.1 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.1.1</h4><span>28-11-2019</span></li>
                            <li><span class="wk-text-improved">Improved </span> – 1 new layout options for the Gallery element</li>
                            <li><span class="wk-text-improved">Improved </span> – Added discount option to Pricing Single element</li>
                            <li><span class="wk-text-improved">Improved </span> – Responsive update to Testimonial element</li>
                        </ul>
                        <ul class="version version-2.1.0 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.1.0</h4><span>21-11-2019</span></li>
                            <li><span class="wk-text-success">New </span> – Gallery Element</li>
                            <li><span class="wk-text-improved">Improved </span> – Pros & Cons Element</li>
                            <li><span class="wk-text-improved">Improved </span> – Filterable Portfolio elementt</li>
                            <li><span class="wk-text-improved">Improved </span> – Pricing Single element</li>
                            <li><span class="wk-text-improved">Improved </span> – Team element</li>
                            <li><span class="wk-text-improved">Improved </span> – Feature Icon box elementt</li>
                            <li><span class="wk-text-improved">Improved </span> – Hover Image element</li>
                            <li><span class="wk-text-improved">Improved </span> – Slider Animation element</li>
                            <li><span class="wk-text-improved">Improved </span> – Blog Carousel element</li>
                            <li><span class="wk-text-improved">Improved </span> – Blog Image element</li>
                        </ul>
                        <ul class="version version-2.0 wk-background-muted wk-padding-small wk-margin-small-bottom">
                            <li class="release-version-date"><h4>2.0</h4><span>20-11-2019</span></li>
                            <li><span class="wk-text-success">New </span> – Content Carousel element</li>
                            <li><span class="wk-text-success">New </span> – Team element</li>
                            <li><span class="wk-text-success">New </span> – Testimonial element</li>
                            <li><span class="wk-text-bugfix">Bug Fix </span> – WooCommerce Integration error</li>
                            <li><span class="wk-text-improved">Improved </span> – Dashboard</li>
                            <li><span class="wk-text-improved">Improved </span> – Pricing Element</li>
                            <li><span class="wk-text-improved">Improved </span> – Button + Modal Element</li>
                            <li><span class="wk-text-improved">Improved </span> – Pros & Const Element</li>
                            <li><span class="wk-text-improved">Improved </span> – Slider Element</li>
                            <li><span class="wk-text-improved">Improved </span> – Portfolio Element</li>
                        </ul>
                    </div>
                    <div class="wkp-changes wk-background-muted wk-pro-changelog">
                        <h3 class="wkp-headline">WidgetKit Pro</h3>

                        <?php if($changelog_data): ?>
                            <?php foreach($changelog_data as $data): ?>
                                <div class="version version-<?php echo $data['plugin_version'];?> wk-background-muted wk-padding-small wk-margin-small-bottom">
                                    <div class="release-version-date"> 
                                        <h4><?php echo $data['plugin_version']; ?> </h4>
                                        <span> <?php echo $data['publish_date'] ?>  </span>
                                    </div>
                                    <?php echo $Parsedown->text($data['plugin_changelog']); ?>
                                </div>
                            <?php endforeach;  ?>
						<?php else: ?>
							<div class="wk-background-muted wk-padding-small wk-margin-small-bottom">
                                <div class="release-version-date"> 
                                    <h4>Something went wrong to retrive data.</h4>
                                </div>
                                <?php echo $Parsedown->text($data['plugin_changelog']); ?>
                            </div>
						<?php endif; ?>

                    </div>
                </div>
                </div>
            <?php 
        }
        public function widgetkit_get_changelog_data(){
        
            $remote_api_data = wp_remote_get($this->api_url);
            $changes_data = '';

            if ( is_wp_error( $remote_api_data ) ) {
				return false;
			}
            
            if($remote_api_data['response']['code'] == 200){
                $response_data = wp_remote_retrieve_body($remote_api_data);
                $data_arr = json_decode($response_data, true);
                $changes_data = $data_arr['changes'];
            }
    
            if(NULL === $this->transient_changelog_data){
                set_transient('changelog_data', $changes_data, 0);
                return $changes_data;
            }else{
                return $changes_data ?? $this->transient_changelog_data ;
            }
            
        }
    }
?>