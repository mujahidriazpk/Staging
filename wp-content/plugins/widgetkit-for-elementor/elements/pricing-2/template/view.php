  <?php
      $settings = $this->get_settings();
  ?>


      <div class="tgx-pricing-2">
          <div class="tgx-single-pricing">
              <div class="tgx-single-wrapper">
                  <div class="tgx-single-heading">
                      <div class="price">

                          <?php if ( ! empty( $settings['single_currency_symbol'] ) ) : ?>
                              <span class="curency">
                                  <?php echo $settings['single_currency_symbol'];  ?>
                              </span>
                          <?php endif; ?>

                          <?php if ( ! empty( $settings['pricing_2_price'] ) ) : ?>
                              <span class="amount"><?php echo $settings['pricing_2_price']; ?></span>
                          <?php endif; ?>

                          <?php if ( ! empty( $settings['pricing_2_period'] ) ) : ?>
                              <span class="period">
                                  <?php echo $settings['pricing_2_period']; ?>
                              </span>
                          <?php endif; ?>

                      </div> <!-- .price -->

                      <?php if ( ! empty( $settings['pricing_2_icon_image']['url'] ) ) : ?>
                          <div class="tgx-single-image">
                            <img src="<?php echo $settings['pricing_2_icon_image']['url']; ?>" alt="<?php echo $settings['pricing_2_pricing_title']; ?>" />
                          </div><!-- .table-image -->
                      <?php endif; ?>

                </div> <!-- .tx-table-heading -->


                <div class="tgx-single-about">
                    <?php if ( ! empty( $settings['pricing_2_pricing_title'] ) ) : ?>
                        <h3 class="tgx-single-title"> 
                            <?php echo $settings['pricing_2_pricing_title']; ?> 
                        </h3>
                    <?php endif; ?>

                    <?php if ( ! empty( $settings['pricing_2_pricing_about'] ) ) : ?>
                       <div class="tgx-single-about">
                           <?php echo $settings['pricing_2_pricing_about']; ?>
                       </div><!-- .single-about -->
                    <?php endif; ?>

                </div><!-- .tgx-single about -->

                <?php if ( ! empty( $settings['features_list_2'] ) ) : ?>
                  <ul class="tgx-single-features-list">
                    <?php foreach ( $settings['features_list_2'] as $item ) : ?>
                        <li class="tgx-feature-item-2">
                            <div class="tgx-price-table__feature-inner_2">
                                <?php if ( ! empty( $item['item_icon_2'] ) ) : ?>
                                    <i class="<?php echo esc_attr($item['item_icon_2']); ?>"></i>
                                <?php endif; ?>
                                <?php if ( ! empty( $item['item_text_2'] ) ) :
                                    echo $item['item_text_2'];
                                else :
                                    echo '&nbsp;';
                                endif;
                                ?>
                            </div><!-- .feature-inner 2 -->
                        </li><!-- .li -->
                    <?php endforeach; ?>
                </ul><!-- ul -->
            <?php endif; ?>
        </div><!-- .tgx-single-wrapper -->

        <?php if ( ! empty( $settings['single_button_text'] ) ) : ?>
            <div class="tgx-single-footer"> 
                <a class="tgx-single-btn" href="<?php echo $settings['single_link']['url']; ?>">
                    <?php echo $settings['single_button_text']; ?>
                </a> 
            </div><!-- .tgx-single-footer -->
        <?php endif; ?>

      </div> <!-- .tgx-single-pricing -->
  </div><!-- .tgx-pricing-2 -->

    <script type="text/javascript">
          jQuery(function($){
              if(!$('body').hasClass('wk-pricing-icon')){
                  $('body').addClass('wk-pricing-icon');
              }
          });

    </script>