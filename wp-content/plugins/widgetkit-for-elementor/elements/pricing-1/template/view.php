<?php
        $settings = $this->get_settings();
        $symbol = '';

        if ( ! empty( $settings['currency_symbol'] ) ) {
            if ( 'custom' !== $settings['currency_symbol'] ) {
                $symbol = $this->get_currency_symbol( $settings['currency_symbol'] );
            } else {
                $symbol = $settings['currency_symbol_custom'];
            }
        }

        $price = explode( '.', $settings['price'] );
        $intpart = $price[0];
        $fraction = '';
        if ( 2 === sizeof( $price ) ) {
            $fraction = $price[1];
        }



        $this->add_render_attribute( 'button', 'class', [
                'tgx-price-table__button',
                'tgx-button',
            ]
        );

        if ( ! empty( $settings['link']['url'] ) ) {
            $this->add_render_attribute( 'button', 'href', $settings['link']['url'] );

            if ( ! empty( $settings['link']['is_external'] ) ) {
                $this->add_render_attribute( 'button', 'target', '_blank' );
            }
        }

        ?>
        <div class="tgx-price-table text-<?php echo $settings['layout_position'];?>">
            <?php if ( $settings['title_position'] == 'top' ) : ?>
                <?php if ( $settings['heading'] ) : ?>
                    <div class="tgx-price-table__header">
                        <?php if ( ! empty( $settings['heading'] ) ) : ?>
                            <h4 class="tgx-price-table__heading"><?php echo esc_attr($settings['heading']); ?></h4>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="tgx-price-table__price">

             
                <?php if ($settings['discount_price_switcher'] == 'yes'): ?>
                    <?php if ($settings['discount_price'] && $settings['currency_position'] == 'before') : ?>
                        <del><span class="tgx-price-table__discount-currency"><span><?php echo esc_attr($symbol); ?></span></span><span class="tgx-price-table__discount-part"><span><?php echo esc_attr($settings['discount_price']); ?></span></span></del>
                    <?php endif; ?>
                
                <?php endif ;?>
            

                <?php if ( ! empty( $symbol ) ) : ?>
                    <span class="tgx-price-table__currency"><span><?php echo esc_attr($symbol); ?></span></span>
                <?php endif; ?>

                <?php if ( ! empty( $settings['price'] ) ) : ?>
                    <span class="tgx-price-table__integer-part"><span><?php echo esc_attr($settings['price']); ?></span></span>
                <?php endif; ?>

                <?php if ($settings['discount_price_switcher'] == 'yes'): ?>
                    <?php if ($settings['discount_price'] && $settings['currency_position'] == 'after') : ?>
                        <del><span class="tgx-price-table__discount-currency"><span><?php echo esc_attr($symbol); ?></span></span><span class="tgx-price-table__discount-part"><span><?php echo esc_attr($settings['discount_price']); ?></span></span></del>
                    <?php endif; ?>
                
                <?php endif ;?>


                <?php if ( ! empty( $settings['period'] ) ) : ?>
                    <span class="tgx-price-table__period"><span><?php echo esc_attr($settings['period']); ?></span></span>
                <?php endif; ?>
             
            </div>

            <?php if ( $settings['title_position'] == 'bottom' ) : ?>
                <?php if ( $settings['heading'] ) : ?>
                    <div class="tgx-price-table__header">
                        <?php if ( ! empty( $settings['heading'] ) ) : ?>
                            <h4 class="tgx-price-table__heading"><?php echo esc_attr($settings['heading']); ?></h4>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>


            <?php if ( ! empty( $settings['features_list'] ) ) : ?>
                <ul class="tgx-price-table__features-list">
                    <?php foreach ( $settings['features_list'] as $item ) : ?>
                        <li class="tgx-repeater-item-<?php echo esc_attr($item['_id']); ?>">
                            <div class="tgx-price-table__feature-inner">
                                <?php if ( ! empty( $item['item_icon'] ) ) : ?>
                                    <i class="<?php echo esc_attr($item['item_icon']); ?>"></i>
                                <?php endif; ?>
                                <?php if ( ! empty( $item['item_text'] ) ) :
                                    echo $item['item_text'];
                                else :
                                    echo '&nbsp;';
                                endif;
                                ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if ( ! empty( $settings['button_text'] ) || ! empty( $settings['footer_additional_info'] ) ) : ?>
                <div class="tgx-price-table__footer">
                    <?php if ( ! empty( $settings['button_text'] ) ) : ?>
                        <a <?php echo $this->get_render_attribute_string( 'button' ); ?>>
                            <?php esc_html_e($settings['button_text'], 'widgetkit-for-elementor'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <script type="text/javascript">
              jQuery(function($){
                  if(!$('body').hasClass('wk-pricing-single')){
                      $('body').addClass('wk-pricing-single');
                  }
              });

        </script>