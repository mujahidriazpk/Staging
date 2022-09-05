<?php
// Display World Map at front-side.
function pa_include_worldmap( $current, $worldmap_stats ) {
    ?>
    <div class="data_boxes">
        <div class="data_boxes_title"><?php //echo _e( 'Top Countries', 'wp-analytify'); ?> <div class="arrow_btn"></div></div>
        <div class="data_container">
          <!--  <script type="text/javascript" src="https://www.google.com/jsapi"></script> -->
            <?php
            if (! empty( $worldmap_stats["rows"] ) ) {

              $map_script = "  google.load('visualization', '1', {'packages': ['geochart'],'callback': drawRegionsMap});
                google.setOnLoadCallback(drawRegionsMap);
                function drawRegionsMap() {
                    var data = google.visualization.arrayToDataTable([
                      ['Country', 'Visitors'],";

                foreach ( $worldmap_stats["rows"] as $c_stats ):
                  $map_script .= "['" . $c_stats[0] ."',".  $c_stats[1] . "],";
                endforeach;

               $map_script .= "
                    ]);
                      var options = {displayMode: 'regions'};
                      var formatter = new google.visualization.NumberFormat({fractionDigits: 0});
                      formatter.format(data, 1);

                      var chart = new google.visualization.GeoChart(document.getElementById('wm_chart_div'));
                      chart.draw(data, options);
                    }";


              // Compatibility for < WordPress 4.5
              if ( !function_exists( 'wp_add_inline_script' ) ) {

                  echo '<script type="text/javascript" src="https://www.google.com/jsapi"></script>';
                  echo '<script>'. $map_script .'</script>';
              } else {
                
                wp_enqueue_script( 'jsapi' );
                wp_add_inline_script( 'jsapi', $map_script, 'after' );
              }

              ?>
            <div id="wm_chart_div" style="width: 600px; height: 450px; margin:0 auto;" ></div>

            <?php } ?>
        </div>
    </div>
    <?php }
    