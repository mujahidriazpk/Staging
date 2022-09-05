<?php
// Silence is golden.

    $settings = $this->get_settings();
    $id = $this->get_id();
    $portfolio_hover_effect = widgetkit_for_elementor_array_get($settings, 'portfolio_hover_effect');
    $hover_4_action = $portfolio_hover_effect == 'hover_4' ? widgetkit_for_elementor_array_get($settings, 'hover_4_action') : '';

?>

    <div class="tgx-portfolio">
    
        <?php
            $data = [];
            $terms = get_terms('filters');
            //$count = count($terms);
            if ($settings['filter_enable']): 
          
        ?>
        <?php if ($settings['filter_layout'] == 'border'):?>
            <ul class="portfolio-filter border text-center">
                <li>
                    <a  class="mixitup-control-active" href="#" data-filter="*"><?php echo $settings['portfolio_filter_show_title'];?></a>
                </li>
                <?php
                  $data = [];

                     foreach ( $settings['portfolio_content'] as $filter ) {
                        $termname = strtolower($filter['portfolio_filter']);  
                        $termname = str_replace(" ", "", $termname);
                        $termname = str_replace("", "", $termname);
                        if(!in_array($termname, $data)){
                                $data[] = $termname;
                            }
                        ?>  
                        <?php if (!empty($filter['portfolio_filter'])): ?>
                            
                        <li><a href="#" data-filter=".<?php echo $termname.'-'.$id;?>"><?php echo $filter['portfolio_filter']; ?></a></li>
                        <?php endif; ?>
                        <?php
                    }
                ?>
            </ul><!-- /.portfolio-filter .border -->

        <?php elseif($settings['filter_layout'] == 'round'): ?>

            <ul class="portfolio-filter round text-center">
                <li>
                    <a  class="mixitup-control-active" href="#" data-filter="*"><?php echo $settings['portfolio_filter_show_title'];?></a>
                </li>
                <?php
                  $data = [];

                     foreach ( $settings['portfolio_content'] as $filter ) {
                        $termname = strtolower($filter['portfolio_filter']);  
                        $termname = str_replace(" ", "", $termname);
                        $termname = str_replace("", "", $termname);
                        if(!in_array($termname, $data)){
                                $data[] = $termname;
                            }
                        ?>  
                        <?php if (!empty($filter['portfolio_filter'])): ?>
                            
                            <li><a href="#" data-filter=".<?php echo $termname.'-'.$id;?>"><?php echo $filter['portfolio_filter']; ?></a></li>
                        <?php endif; ?>
                        <?php
                    }
                ?>
            </ul><!-- /.portfolio-filter .round-->
        <?php elseif($settings['filter_layout'] == 'slash'): ?>
           <ul class="portfolio-filter slash text-center">
                <li>
                    <a  class="mixitup-control-active" href="#" data-filter="*"><?php echo $settings['portfolio_filter_show_title'];?></a>
                </li>
                <?php
                  $data = [];

                     foreach ( $settings['portfolio_content'] as $filter ) {
                        $termname = strtolower($filter['portfolio_filter']);  
                        $termname = str_replace(" ", "", $termname);
                        $termname = str_replace("", "", $termname);
                        if(!in_array($termname, $data)){
                                $data[] = $termname;
                            }
                        ?>  

                        <?php if (!empty($filter['portfolio_filter'])): ?>
                            
                            <li><span class="filter-slash"><?php esc_html_e('/', 'widgetkit-for-elementor');?></span><a href="#" data-filter=".<?php echo $termname.'-'.$id;?>"><?php echo $filter['portfolio_filter']; ?></a></li>
                        <?php endif; ?>
                        <?php
                    }
                ?>
            </ul><!-- /.portfolio-filter .slash -->
        <?php else: ?>
            <ul class="portfolio-filter round text-center">
                <li>
                    <a  class="mixitup-control-active" href="#" data-filter="*"><?php echo $settings['portfolio_filter_show_title'];?></a>
                </li>
                <?php
                  $data = [];

                     foreach ( $settings['portfolio_content'] as $filter ) {
                        $termname = strtolower($filter['portfolio_filter']);  
                        $termname = str_replace(" ", "", $termname);
                        $termname = str_replace("", "", $termname);
                        if(!in_array($termname, $data)){
                                $data[] = $termname;
                            }
                        ?>  
                        <?php if (!empty($filter['portfolio_filter'])): ?>
                            <li><a href="#" data-filter=".<?php echo $termname.'-'.$id;?>"><?php echo $filter['portfolio_filter']; ?></a></li>
                        <?php endif; ?>
                        <?php
                    }
                ?>
            </ul><!-- /.portfolio-filter .round -->
        <?php endif; ?>

        <?php endif; ?>

            <?php if ($settings['portfolio_hover_effect'] == 'hover_1'): ?>
                <div id="hover-1" class="hover-1">
            <?php elseif($settings['portfolio_hover_effect'] == 'hover_2'): ?>
                <div class="hover-2">
            <?php elseif($settings['portfolio_hover_effect'] == 'hover_3'): ?>
               <div class="hover-3">
            <?php elseif($settings['portfolio_hover_effect'] == 'hover_4'): ?>
               <div class="hover-4">
            <?php else: ?>
                <div id="hover-1" class="hover-1">
            <?php endif; ?>
            <div class="row">

                    <?php foreach ( $settings['portfolio_content'] as $portfolio ) : ?>
                        <?php $tags = explode(',', $portfolio['filter_tag']);
                        ?>
                        <div class="col-md-<?php echo $settings['colmun_layout'];?> col-sm-6 mix mix-<?php echo $id?> portfolio-item <?php foreach($tags as $tag ): echo strtolower($tag.'-'.$id).' '; endforeach;?>">


                            <?php if ($settings['portfolio_hover_effect'] == 'hover_1'): ?>

                                <?php if($portfolio['portfolio_thumb_image']):?>
                                    <span>
                                        <img src="<?php echo $portfolio['portfolio_thumb_image']['url'];?>" alt="<?php echo $portfolio['portfolio_title']; ?>">
                                        <div></div>    
                                    </span>
                                <?php endif; ?>   

                            <span class="portfolio-buttons">

                                <?php if($portfolio['portfolio_full_image']):?>
                                    <a class="test-popup-link" href="<?php echo $portfolio['portfolio_full_image']['url'];?>" >
                                        <i class="fa fa-search"></i>
                                    </a>
                                <?php endif; ?>

                                 <?php if($portfolio['portfolio_demo_link']):  ?>
                                    <a href="<?php echo $portfolio['portfolio_demo_link'];?>" target="_blank">
                                        <i class="fa fa fa-link"></i>
                                    </a>
                                <?php endif; ?>

                            </span><!-- /effect 1 .portfolio buttons -->


                        
                        <?php elseif($settings['portfolio_hover_effect'] == 'hover_2'): ?>

                            <div class="overlay overlay-hover">
                                <div class="overlay-spin" >
                                    <?php if($portfolio['portfolio_thumb_image']):?>
                                        <img src="<?php echo $portfolio['portfolio_thumb_image']['url'];?>" alt="<?php echo $portfolio['portfolio_title']; ?>">
                                    <?php endif; ?>                                                             
                                </div><!-- .overlay-spin -->

                                <div class="overlay-panel">
                                    <div class="portfolio-btn text-center">
                                        <?php if($portfolio['portfolio_full_image']):?>
                                            <a class="icon-search" 
                                                href="<?php echo $portfolio['portfolio_full_image']['url'];?>">
                                                <i class='fa fa-plus'></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if($portfolio['portfolio_demo_link']):  ?>
                                            <a target="_blank" href="<?php echo $portfolio['portfolio_demo_link'];?>" class="icon-link">
                                                <i class='fa fa-link'></i>
                                            </a>
                                        <?php endif; ?>
                                    </div> <!-- /.portfolio-btn -->                      
                                </div><!-- /.portfolio-filter -->

                                <div class="portfolio-content text-left">
                                    <?php if ($portfolio['portfolio_title']): ?>   
                                        <h4 class="title"><?php echo $portfolio['portfolio_title']; ?></h4>
                                    <?php endif ?>

                                    <?php if($portfolio['portfolio_desc']): ?>
                                        <p class="desc"><?php echo $portfolio['portfolio_desc'];?></p>
                                    <?php endif; ?>
                                </div><!-- /.portfolio-content -->
                            </div><!-- /.effcet 2 overlay -->

                    
                        <?php elseif($settings['portfolio_hover_effect'] == 'hover_3'): ?>

                            <div class="effect-3">
                                <ul class="external-link">
                                    <li>
                                        <a href="<?php echo $portfolio['portfolio_full_image']['url'];?>">
                                            <i class="fa fa-search"></i>
                                        </a>
                                    </li>
                                    <li> 
                                        <?php if($portfolio['portfolio_demo_link']):  ?>
                                            <a target="_blank" href="<?php echo $portfolio['portfolio_demo_link'];?>" class="icon-link">
                                                <i class='fa fa-link'></i>
                                            </a>
                                        <?php endif; ?>
                                    </li>
                                </ul>
                                 <img src="<?php echo $portfolio['portfolio_thumb_image']['url'];?>" alt="<?php echo $portfolio['portfolio_title']; ?>">
                                <figcaption class="info">
                                    <?php if ($portfolio['portfolio_title']): ?>   
                                        <h4 class="title"><?php echo $portfolio['portfolio_title']; ?></h4>
                                    <?php endif ?>

                                    <?php if($portfolio['portfolio_desc']): ?>
                                        <p class="desc"><?php echo $portfolio['portfolio_desc'];?></p>
                                    <?php endif; ?>
                                </figcaption>
                            </div> <!-- /.effect 3 -->

                            <?php elseif ($settings['portfolio_hover_effect'] == 'hover_4'): ?>


                            <div class="overlay overlay-hover">
                                <div class="overlay-spin" >
                                    <?php if($portfolio['portfolio_thumb_image']):?>
                                        <img src="<?php echo $portfolio['portfolio_thumb_image']['url'];?>" alt="<?php echo $portfolio['portfolio_title']; ?>">
                                    <?php endif; ?>                                                             
                                </div><!-- .overlay-spin -->
                                <div class="portfolio-content text-left <?php echo $hover_4_action; ?>">
                                    <?php if($portfolio['portfolio_full_image']):?>
                                        <?php if($hover_4_action == 'demo_link' ): ?>
                                            <a class="icon-search" href="<?php echo $portfolio['portfolio_demo_link'] ;?>">
                                                <?php if ($portfolio['portfolio_title']): ?>   
                                                    <h4 class="title">
                                                        <?php echo $portfolio['portfolio_title'];?> 
                                                    </h4>
                                                <?php endif ?>
                                            </a>
                                        <?php else:?>
                                            <a class="icon-search" data-rel="lightcase:slideshow" 
                                                href="<?php echo $portfolio['portfolio_full_image']['url'];?>">
                                                <?php if ($portfolio['portfolio_title']): ?>   
                                                    <h4 class="title">
                                                        <?php echo $portfolio['portfolio_title'];?> 
                                                    </h4>
                                                <?php endif ?>
                                            </a>
                                        <?php endif;?>
                                    <?php endif; ?>
                                </div> <!-- /.portfolio-btn -->                      
                            </div><!-- /.effcet 4 overlay -->


                        <?php else: ?>

                            <?php if($portfolio['portfolio_thumb_image']):?>
                                <a href="">
                                    <img src="<?php echo $portfolio['portfolio_thumb_image']['url'];?>" alt="<?php echo $portfolio['portfolio_title']; ?>">
                                    <div></div>    
                                </a>
                            <?php endif; ?>   

                            <span class="portfolio-buttons">

                                <?php if($portfolio['portfolio_full_image']):?>
                                    <a class="test-popup-link" href="<?php echo $portfolio['portfolio_thumb_image']['url'];?>" >
                                        <i class="fa fa-search"></i>
                                    </a>
                                <?php endif; ?>

                                 <?php if($portfolio['portfolio_demo_link']):  ?>
                                    <a href="<?php echo $portfolio['portfolio_demo_link'];?>" target="_blank">
                                        <i class="fa fa fa-link"></i>
                                    </a>
                                <?php endif; ?>

                            </span><!-- /.portfolio buttons -->
                            
                        <?php endif; ?>
                    </div> <!-- /.col-md-->
                <?php endforeach; ?>
            </div><!-- /.row -->  
        </div><!-- /.hover-effect -->
    </div> <!-- /.tgx-portfolio -->

    <script type="text/javascript">
          jQuery(function($){
              if(!$('body').hasClass('wk-portfolio')){
                  $('body').addClass('wk-portfolio');
              }
          });

    </script>