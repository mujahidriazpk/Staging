<?php
   // Silence is golden.

    $settings = $this->get_settings();
    //$id = $this->get_id();


        $target_date = str_replace('-', '/', $settings['widgetkit_countdown_date_time'] );
        
        $formats = $settings['widgetkit_countdown_units'];
        $format = implode('', $formats );
        $time = str_replace('-', '/', current_time('mysql') );
        $serverSync = '';
        if( $settings['widgetkit_countdown_s_u_time'] == 'wp-time' ) : 
            $serverSync = 'serverSync : function() { return new Date(\'' .$time .'\') }';
        endif;
        
        // Singular labels set up
        $y = !empty( $settings['widgetkit_countdown_year_singular'] ) ? $settings['widgetkit_countdown_year_singular'] : 'Year';
        $m = !empty( $settings['widgetkit_countdown_month_singular'] ) ? $settings['widgetkit_countdown_month_singular'] : 'Month';
        $w = !empty( $settings['widgetkit_countdown_week_singular'] ) ? $settings['widgetkit_countdown_week_singular'] : 'Week';
        $d = !empty( $settings['widgetkit_countdown_day_singular'] ) ? $settings['widgetkit_countdown_day_singular'] : 'Day';
        $h = !empty( $settings['widgetkit_countdown_hour_singular'] ) ? $settings['widgetkit_countdown_hour_singular'] : 'Hour';
        $mi = !empty( $settings['widgetkit_countdown_minute_singular'] ) ? $settings['widgetkit_countdown_minute_singular'] : 'Minute';
        $s = !empty( $settings['widgetkit_countdown_second_singular'] ) ? $settings['widgetkit_countdown_second_singular'] : 'Second';
        $label = $y."," . $m ."," . $w ."," . $d ."," . $h ."," . $mi ."," . $s;

        // Plural labels set up
        $ys = !empty( $settings['widgetkit_countdown_year_plural'] ) ? $settings['widgetkit_countdown_year_plural'] : 'Years';
        $ms = !empty( $settings['widgetkit_countdown_month_plural'] ) ? $settings['widgetkit_countdown_month_plural'] : 'Months';
        $ws = !empty( $settings['widgetkit_countdown_week_plural'] ) ? $settings['widgetkit_countdown_week_plural'] : 'Weeks';
        $ds = !empty( $settings['widgetkit_countdown_day_plural'] ) ? $settings['widgetkit_countdown_day_plural'] : 'Days';
        $hs = !empty( $settings['widgetkit_countdown_hour_plural'] ) ? $settings['widgetkit_countdown_hour_plural'] : 'Hours';
        $mis = !empty( $settings['widgetkit_countdown_minute_plural'] ) ? $settings['widgetkit_countdown_minute_plural'] : 'Minutes';
        $ss = !empty( $settings['widgetkit_countdown_second_plural'] ) ? $settings['widgetkit_countdown_second_plural'] : 'Seconds';
        $labels1 = $ys."," . $ms ."," . $ws ."," . $ds ."," . $hs ."," . $mis ."," . $ss;
        
        $expire_text = addslashes($settings['widgetkit_countdown_expiry_text_']);
        
        $pcdt_style = $settings['widgetkit_countdown_style'] == 'd-u-s' ? ' side' : ' down';
        
        ?>
            <div class="wk-countdown">
                <div class="countdown-wrapper">
                    <div id="countDownContiner">
                        <div class="widgetkit-countdown<?php echo $pcdt_style; ?>" id="countdown-<?php echo esc_attr( $this->get_id() ); ?>"></div>
                    </div>     
                </div>
            </div>
            
            <script>
                ( function( $ ) {
                    $(document).ready( function() {
                        var label1 = '<?php echo $label; ?>',
                            label2 = '<?php echo $labels1; ?>',
                            newLabe1 = label1.split(','),
                            newLabe2 = label2.split(',');
                
                        $('#countdown-<?php echo esc_attr( $this->get_id() ); ?>').widgetkit_countdown({
                            labels      : newLabe2,
                            labels1     : newLabe1,
                            until       : new Date( '<?php echo $target_date; ?>'),
                            format      : '<?php echo $format; ?>',
                            padZeroes   : true,
                            <?php if( $expire_text ):  ?>
                            onExpiry    : function() {
                                $(this).html("<?php echo $expire_text; ?>");
                            },
                            <?php endif; ?>
                         
                            <?php echo $serverSync; ?>
                        });
                        times = $('#countdown-<?php echo esc_attr( $this->get_id() );?>').widgetkit_countdown('getTimes');
                        function runTimer( el ) {
                            return el == 0;
                        }
                        if( times.every( runTimer ) ) {
                            <?php if( $expire_text ):  ?>
                            $('#countdown-<?php echo esc_attr( $this->get_id() ); ?>').html("<?php echo $expire_text; ?>");
                            <?php endif; ?>
                        }
                            if(!$('body').hasClass('wk-countdown')){
                            $('body').addClass('wk-countdown');
                        };
                    });
                    
                })( jQuery );
                
            </script>

