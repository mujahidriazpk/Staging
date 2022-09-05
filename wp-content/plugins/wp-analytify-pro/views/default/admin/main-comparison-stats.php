<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

/*
 * View of Visitors and Views Comparison Statistics
 */

function fetch_visitors_views_comparison ( $current, $this_month_stats, $previous_month_stats, $this_year_stats, $previous_year_stats, $is_three_month = false, $this_month_start_date, $this_month_end_date, $previous_month_start_date, $previous_month_end_date, $this_year_start_date, $this_year_end_data, $previous_year_start_date, $previous_year_end_date  ) {

  $code                      = '';
  $this_month_users_data     = array();
  $previous_month_users_data = array();
  $date_data                 = array();

  $this_year_users_data     = array();
  $previous_year_users_data = array();
  $month_data               = array();

  $this_month_views_data     = array();
  $previous_month_views_data = array();

  $this_year_views_data     = array();
  $previous_year_views_data = array();

  $this_month_total_users     = $this_month_stats['totalsForAllResults']['ga:users'];
  $this_month_total_views     = $this_month_stats['totalsForAllResults']['ga:pageviews'];
  $previous_month_total_users = $previous_month_stats['totalsForAllResults']['ga:users'];
  $previous_month_total_views = $previous_month_stats['totalsForAllResults']['ga:pageviews'];

  $this_year_total_users     = $this_year_stats['totalsForAllResults']['ga:users'];
  $this_year_total_views     = $this_year_stats['totalsForAllResults']['ga:pageviews'];
  $previous_year_total_users = $previous_year_stats['totalsForAllResults']['ga:users'];
  $previous_year_total_views = $previous_year_stats['totalsForAllResults']['ga:pageviews'];

  $graph_colors = apply_filters( 'analytify_compare_graph_colors', array(
    'visitors_this_year'  => '#03a1f8',
    'visitors_last_year'  => '#00c852',
    'visitors_this_month' => '#03a1f8',
    'visitors_last_month' => '#00c852',
    'views_this_year'     => '#03a1f8',
    'views_last_year'     => '#00c852',
    'views_this_month'    => '#03a1f8',
    'views_last_month'    => '#00c852'
  ) );

  foreach ( $this_month_stats['rows'] as  $value ) {
    $this_month_users_data[] = $value['1'];
    $this_month_views_data[] = $value['2'];
    $date_data[]             = date( "j-M", strtotime( $value['0'] ) );
  }

  foreach ( $previous_month_stats['rows'] as $value ) {
    $previous_month_users_data[] = $value['1'];
    $previous_month_views_data[] = $value['2'];
  }

  foreach ( $this_year_stats['rows'] as  $value ) {
    $this_year_users_data[] = $value['1'];
    $this_year_views_data[] = $value['2'];

    if ( $is_three_month ) {
      $month_data[]           =  date( "j-M-Y", strtotime( $value['0'] ) );
    } else {
      $month_data[]           = date( "M-Y", strtotime( $value['0'] ."01" ) );

    }

  }

  foreach ( $previous_year_stats['rows'] as  $value ) {
    $previous_year_users_data[] = $value['1'];
    $previous_year_views_data[] = $value['2'];
  }

  if ( isset( $_POST['view_data'] ) ) {

     $visitors_this_year_legend  = date('F d, Y', strtotime( $this_year_start_date ) ) . ' to ' . date('F d, Y',  strtotime( $this_year_end_data ) );
     $visitors_last_year_legend  = date('F d, Y', strtotime( $previous_year_start_date ) ) . ' to ' .  date('F d, Y',  strtotime( $previous_year_end_date ) );

     $visitors_this_month_legend = date('F d, Y',  strtotime( $this_month_start_date ) ) . ' to ' . date('F d, Y',  strtotime( $this_month_end_date  ) );
     $visitors_last_month_legend = date('F d, Y',  strtotime( $previous_month_start_date  ) ) . ' to ' . date('F d, Y',  strtotime( $previous_month_end_date  ) );

     $views_this_year_legend     = date('F d, Y', strtotime( $this_year_start_date ) ) . ' to ' . date('F d, Y',  strtotime( $this_year_end_data  ) );
     $views_last_year_legend     = date('F d, Y',  strtotime( $previous_year_start_date  ) ) . ' to ' . date('F d, Y',  strtotime( $previous_year_end_date  ) );

     $views_this_month_legend    = date('F d, Y',  strtotime( $this_month_start_date  ) ) . ' to ' . date('F d, Y',  strtotime( $this_month_end_date  ) ) ;
     $views_last_month_legend    = date('F d, Y',  strtotime( $previous_month_start_date  ) ) . ' to ' . date('F d, Y',  strtotime( $previous_month_end_date  ) );

  } else{

    $visitors_this_year_legend  = 'Visitors this year';
    $visitors_last_year_legend  = 'Visitors last year';
    $visitors_this_month_legend = 'Visitors this month';
    $visitors_last_month_legend = 'Visitors last month';

    $views_this_year_legend     = 'Views this year';
    $views_last_year_legend     = 'Views last year';
    $views_this_month_legend    = 'Views this month';
    $views_last_month_legend    = 'Views last month';
  }


  ?>
  <script>
  jQuery(document).ready(function ($) {
    is_three_month = '<?php echo $is_three_month ?>';

      // configure for module loader
      require.config({
          paths: {
              echarts: 'js/dist/'
          }
      });

      // use
      require(
          [
              'echarts',
              'echarts/chart/bar', // require the specific chart type
              'echarts/chart/line' // require the specific chart type
          ],
          function (ec) {
              // Initialize after dom ready
              var years_graph_by_visitors = ec.init(document.getElementById('analytify_years_graph_by_visitors'));
              var months_graph_by_visitors = ec.init(document.getElementById('analytify_months_graph_by_visitors'));
              var years_graph_by_view = ec.init(document.getElementById('analytify_years_graph_by_view'));
              var months_graph_by_view = ec.init(document.getElementById('analytify_months_graph_by_view'));

              var years_graph_by_visitors_option = {
                  tooltip: {
                    position : function(p) {
                      if($('#analytify_years_graph_by_visitors').width() - p[0] <= 200){
                        return [p[0] - 170, p[1]];
                      }
                    },
                    formatter: function (params,ticket,callback) {

                      var year_name = ''
                      var seriesName = params.seriesName + "<br />" ;
                      // if ( is_three_month == '1' ) {
                      //     seriesName = 'Visitors <br />';
                      // }

                      if ( params.seriesIndex == '1' ) {

                        if ( is_three_month == '1' ) {
                          var s_date = moment(params.name, 'D-MMM-YYYY', true).format("MMM DD"),
                          year_name = moment(s_date, 'MMM DD', true).add(-1, 'years').format("D-MMM-YYYY");
                        } else {
                          var s_date = moment(params.name, 'MMM-YYYY', true).format("MMM YYYY"),
                          year_name = moment(s_date, 'MMM YYYY', true).add(-1, 'years').format("MMM-YYYY");
                        }

                      } else {
                        year_name = params.name;
                      }
                      return  seriesName + year_name + " : " + params.value;
                    },
                      show: true
                  },
                  color: [
                      '<?php echo $graph_colors['visitors_this_year']; ?>', '<?php echo $graph_colors['visitors_last_year']; ?>'
                  ],
                  legend: {
                      data:['<?php _e( $visitors_this_year_legend, 'wp-analytify-pro' ) ?>','<?php _e( $visitors_last_year_legend ) ?>'],
                      orient : 'vertical',
                  },
                  toolbox: {
                      show : true,
                      color:["#444444","#444444","#444444","#444444"],
                      feature : {
                          magicType : {show: true, type: ['line', 'bar'],  title : {
                              line : "<?php _e( 'Line', 'wp-analytify-pro' ) ?>",
                              bar  :  "<?php _e( 'Bar', 'wp-analytify-pro' ) ?>"
                          } },
                          restore : {show: true,  title : "<?php _e( 'Restore', 'wp-analytify-pro' ) ?>"},
                          saveAsImage : {show: true,  title : "<?php _e( 'Save As Image', 'wp-analytify-pro' ) ?>"}
                      }
                  },
                  xAxis : [
                      {
                          type : 'category',
                          boundaryGap : false,
                          data : <?php echo json_encode( $month_data ) ?>
                      }
                  ],
                  yAxis : [
                      {
                          type : 'value'
                      }
                  ],
                  series : [
                      {
                          "name":"<?php _e( $visitors_this_year_legend , 'wp-analytify-pro' ) ?>",
                          "type":"line",
                          smooth: true,
                          itemStyle: {
                            normal: {
                              areaStyle: {
                                type: 'default'
                              }
                            }
                          },
                          "data": <?php echo json_encode( $this_year_users_data ) ?>
                      }
                      ,{
                          "name":"<?php _e( $visitors_last_year_legend, 'wp-analytify-pro' ) ?>",
                          "type":"line",
                          smooth: true,
                          itemStyle: {
                            normal: {
                              areaStyle: {
                                type: 'default'
                              }
                            }
                          },
                          "data": <?php echo json_encode( $previous_year_users_data ) ?>
                      }
                  ]
              };

              var months_graph_by_visitors_option = {
                  tooltip: {
                    position : function(p) {
                      if($('#analytify_months_graph_by_visitors').width() - p[0] <= 200){
                        return [p[0] - 170, p[1]];
                      }
                    },
                    formatter: function (params,ticket,callback) {
                      var month_name = ''
                      if ( params.seriesIndex == '1' ) {

                        var s_date = moment(params.name, 'D-MMM', true).format("MMM DD"),
                        month_name = moment(s_date, 'MMM DD', true).add(-1, 'months').format("D-MMM");

                      } else {
                        month_name = params.name;
                      }
                      return  params.seriesName + "<br />" + month_name + " : " + params.value;
                    },
                      show: true
                  },
                  color: [
                    '<?php echo $graph_colors['visitors_this_month'] ?>', '<?php echo $graph_colors['visitors_last_month'] ?>'
                  ],
                  legend: {
                      data:['<?php _e( $visitors_this_month_legend, 'wp-analytify-pro' ) ?>','<?php _e( $visitors_last_month_legend, 'wp-analytify-pro' ) ?>'],
                      orient : 'vertical',
                  },
                  toolbox: {
                      show : true,
                      color:["#444444","#444444","#444444","#444444"],
                      feature : {
                          magicType : {show: true, type: ['line', 'bar'],  title : {
                              line : "<?php _e( 'Line', 'wp-analytify-pro' ) ?>",
                              bar  :  "<?php _e( 'Bar', 'wp-analytify-pro' ) ?>"
                          } },
                          restore : {show: true,  title : "<?php _e( 'Restore', 'wp-analytify-pro' ) ?>"},
                          saveAsImage : {show: true,  title : "<?php _e( 'Save As Image', 'wp-analytify-pro' ) ?>"}
                      }
                  },
                  xAxis : [
                      {
                          type : 'category',
                          boundaryGap : false,
                          data : <?php echo json_encode( $date_data ) ?>
                      }
                  ],
                  yAxis : [
                      {
                          type : 'value'
                      }
                  ],
                  series : [
                      {
                          "name":"<?php _e( $visitors_this_month_legend, 'wp-analytify-pro' ) ?>",
                          "type":"line",
                          smooth: true,
                          itemStyle: {
                            normal: {
                              areaStyle: {
                                type: 'default'
                              }
                            }
                          },
                          "data": <?php echo json_encode( $this_month_users_data ) ?>
                      }
                      ,{
                          "name":"<?php _e( $visitors_last_month_legend, 'wp-analytify-pro' ) ?>",
                          "type":"line",
                          smooth: true,
                          itemStyle: {
                            normal: {
                              areaStyle: {
                                type: 'default'
                              }
                            }
                          },
                          "data": <?php echo json_encode( $previous_month_users_data ) ?>
                      }
                  ]
              };

              var years_graph_by_view_option = {
                  tooltip: {
                    position : function(p) {
                      if($('#analytify_years_graph_by_view').width() - p[0] <= 200){
                        return [p[0] - 170, p[1]];
                      }
                    },
                    formatter: function (params,ticket,callback) {

                      var year_name = ''
                      var seriesName = params.seriesName + "<br />" ;
                      // if ( is_three_month == '1' ) {
                      //     seriesName = 'Views <br />';
                      // }
                      if ( params.seriesIndex == '1' ) {

                        if ( is_three_month == '1' ) {
                          var s_date = moment(params.name, 'D-MMM-YYYY', true).format("MMM DD"),
                          year_name = moment(s_date, 'MMM DD', true).add(-1, 'years').format("D-MMM-YYYY");
                        } else {
                          var s_date = moment(params.name, 'MMM-YYYY', true).format("MMM YYYY"),
                          year_name = moment(s_date, 'MMM YYYY', true).add(-1, 'years').format("MMM-YYYY");
                        }

                      } else {
                        year_name = params.name;
                      }
                      return  seriesName + year_name + " : " + params.value;
                    },
                      show: true
                  },
                  color: [
                    '<?php echo $graph_colors['views_this_year']; ?>', '<?php echo $graph_colors['views_last_year']; ?>'
                  ],
                  legend: {
                      data:['<?php _e(  $views_this_year_legend, 'wp-analytify-pro' ) ?>','<?php _e( $views_last_year_legend, 'wp-analytify-pro' ) ?>'],
                      orient : 'vertical',
                  },
                  toolbox: {
                      show : true,
                      color:["#444444","#444444","#444444","#444444"],
                      feature : {
                          magicType : {show: true, type: ['line', 'bar'],  title : {
                              line : "<?php _e( 'Line', 'wp-analytify-pro' ) ?>",
                              bar  :  "<?php _e( 'Bar', 'wp-analytify-pro' ) ?>"
                          } },
                          restore : {show: true,  title : "<?php _e( 'Restore', 'wp-analytify-pro' ) ?>"},
                          saveAsImage : {show: true,  title : "<?php _e( 'Save As Image', 'wp-analytify-pro' ) ?>"}
                      }
                  },
                  xAxis : [
                      {
                          type : 'category',
                          boundaryGap : false,
                          data : <?php echo json_encode( $month_data ) ?>
                      }
                  ],
                  yAxis : [
                      {
                          type : 'value'
                      }
                  ],
                  series : [
                      {
                          "name":"<?php _e(  $views_this_year_legend, 'wp-analytify-pro' ) ?>",
                          "type":"line",
                          smooth: true,
                          itemStyle: {
                            normal: {
                              areaStyle: {
                                type: 'default'
                              }
                            }
                          },
                          "data": <?php echo json_encode( $this_year_views_data ) ?>
                      }
                      ,{
                          "name":"<?php _e( $views_last_year_legend, 'wp-analytify-pro' ) ?>",
                          "type":"line",
                          smooth: true,
                          itemStyle: {
                            normal: {
                              areaStyle: {
                                type: 'default'
                              }
                            }
                          },
                          "data": <?php echo json_encode( $previous_year_views_data ) ?>
                      }
                  ]
              };

              var months_graph_by_view_option = {
                  tooltip: {
                    position : function(p) {
                      if($('#analytify_months_graph_by_view').width() - p[0] <= 200){
                        return [p[0] - 170, p[1]];
                      }
                    },
                    formatter: function (params,ticket,callback) {
                      var month_name = ''
                      if ( params.seriesIndex == '1' ) {
                        var s_date = moment(params.name, 'D-MMM', true).format("MMM DD"),
                        month_name = moment(s_date, 'MMM DD', true).add(-1, 'months').format("D-MMM");

                      } else {
                        month_name = params.name;
                      }
                      return  params.seriesName + "<br />" + month_name + " : " + params.value;
                    },
                      show: true
                  },
                  color: [
                    '<?php echo $graph_colors['views_this_month']; ?>', '<?php echo $graph_colors['views_last_month']; ?>'
                  ],
                  legend: {
                      data:['<?php _e( $views_this_month_legend, 'wp-analytify-pro' ) ?>','<?php _e( $views_last_month_legend, 'wp-analytify-pro' ) ?>'],
                      orient : 'vertical',
                  },
                  toolbox: {
                      show : true,
                      color:["#444444","#444444","#444444","#444444"],
                      feature : {
                          magicType : {show: true, type: ['line', 'bar'],  title : {
                              line : "<?php _e( 'Line', 'wp-analytify-pro' ) ?>",
                              bar  :  "<?php _e( 'Bar', 'wp-analytify-pro' ) ?>"
                          } },
                          restore : {show: true,  title : "<?php _e( 'Restore', 'wp-analytify-pro' ) ?>"},
                          saveAsImage : {show: true,  title : "<?php _e( 'Save As Image', 'wp-analytify-pro' ) ?>"}
                      }
                  },
                  xAxis : [
                      {
                          type : 'category',
                          boundaryGap : false,
                          data : <?php echo json_encode( $date_data ) ?>
                      }
                  ],
                  yAxis : [
                      {
                          type : 'value'
                      }
                  ],
                  series : [
                      {
                          "name": '<?php _e( $views_this_month_legend, 'wp-analytify-pro' ) ?>',
                          "type":"line",
                          smooth: true,
                          itemStyle: {
                            normal: {
                              areaStyle: {
                                type: 'default'
                              }
                            }
                          },
                          "data": <?php echo json_encode( $this_month_views_data ) ?>
                      }
                      ,{
                          "name": '<?php _e( $views_last_month_legend, 'wp-analytify-pro' ) ?>',
                          "type":"line",
                          smooth: true,
                          itemStyle: {
                            normal: {
                              areaStyle: {
                                type: 'default'
                              }
                            }
                          },
                          "data": <?php echo json_encode( $previous_month_views_data ) ?>
                      }
                  ]
              };

              // Load data into the ECharts instance
              years_graph_by_visitors.setOption(years_graph_by_visitors_option);
              months_graph_by_visitors.setOption(months_graph_by_visitors_option);
              years_graph_by_view.setOption(years_graph_by_view_option);
              months_graph_by_view.setOption(months_graph_by_view_option);


              window.onresize = function () {
                  years_graph_by_visitors.resize();
                  months_graph_by_visitors.resize();
                  years_graph_by_view.resize();
                  months_graph_by_view.resize();
              }
          }
      );
  });

  </script>
  <div class="analytify_general_status analytify_status_box_wraper">
      <ul class="analytify_status_tab_header">
          <li class="analytify_active_stats analytify_visitors" data-tab="analytify_visitors"><span><?php esc_html_e( 'Visitors', 'wp-analytify-pro' ); ?></span></li>
          <li data-tab="analytify_views" class="analytify_views"><span><?php esc_html_e( 'Views', 'wp-analytify-pro' ); ?></span></li>
      </ul>
      <div class="analytify_status_body">
          <div id="analytify_visitors" class="analytify_panels_data analytify_active_panel">
              <div class="analytify_stats_setting_bar">
                  <div class="analytify_pull_right">
                      <div class="analytify_select_month analytify_stats_setting">
                          <button data-graphType="analytify_months_graph_by_visitors"><?php esc_html_e( 'Months', 'wp-analytify-pro' ); ?></button>
                      </div>
                      <div class="analytify_select_year analytify_stats_setting analytify_disabled">
                          <button data-graphType="analytify_years_graph_by_visitors"><?php esc_html_e( 'Years', 'wp-analytify-pro' ); ?></button>
                      </div>
                  </div>
                  <div class="analytify_pull_left total_month_users">
                      <span class="analytify_current_value"><?php echo WPANALYTIFY_Utils::pretty_numbers( $this_month_total_users ); ?></span>
                      <span class="analytify_compare_value">vs</span>
                      <span class="analytify_previous_value"><?php echo WPANALYTIFY_Utils::pretty_numbers( $previous_month_total_users ); ?></span>
                  </div>
                  <div class="analytify_pull_left total_year_users" style="display: none;">
                      <span class="analytify_current_value"><?php echo WPANALYTIFY_Utils::pretty_numbers( $this_year_total_users ); ?></span>
                      <span class="analytify_compare_value">vs</span>
                      <span class="analytify_previous_value"><?php echo WPANALYTIFY_Utils::pretty_numbers( $previous_year_total_users ); ?></span>
                  </div>
              </div>
              <div class="analytify_txt_center analytify_graph_wraper analytify_years_graph_by_visitors">
                  <div id="analytify_years_graph_by_visitors" style="height:400px"></div>
              </div>
              <div class="analytify_txt_center analytify_graph_wraper analytify_months_graph_by_visitors analytify_active_graph">
                  <div id="analytify_months_graph_by_visitors" style="height:400px"></div>
              </div>
          </div>
          <div id="analytify_views" class="analytify_panels_data">
              <div class="analytify_stats_setting_bar">
                  <div class="analytify_pull_right">
                      <div class="analytify_select_month analytify_stats_setting">
                          <button data-graphType="analytify_months_graph_by_view"><?php esc_html_e( 'Months', 'wp-analytify-pro' ); ?></button>
                      </div>
                      <div class="analytify_select_year analytify_stats_setting analytify_disabled">
                          <button data-graphType="analytify_years_graph_by_view"><?php esc_html_e( 'Years', 'wp-analytify-pro' ); ?></button>
                      </div>
                  </div>
                  <div class="analytify_pull_left total_month_views">
                      <span class="analytify_current_value"><?php echo WPANALYTIFY_Utils::pretty_numbers( $this_month_total_views ); ?></span>
                      <span class="analytify_compare_value">vs</span>
                      <span class="analytify_previous_value"><?php echo WPANALYTIFY_Utils::pretty_numbers( $previous_month_total_views ); ?></span>
                  </div>
                  <div class="analytify_pull_left total_year_views" style="display: none;">
                      <span class="analytify_current_value"><?php echo WPANALYTIFY_Utils::pretty_numbers( $this_year_total_views ); ?></span>
                      <span class="analytify_compare_value">vs</span>
                      <span class="analytify_previous_value"><?php echo WPANALYTIFY_Utils::pretty_numbers( $previous_year_total_views ); ?></span>
                  </div>
              </div>
              <div class="analytify_txt_center analytify_graph_wraper analytify_years_graph_by_view">
                  <div id="analytify_years_graph_by_view" style="height:400px"></div>
              </div>
              <div class="analytify_txt_center analytify_graph_wraper analytify_months_graph_by_view analytify_active_graph">
                  <div id="analytify_months_graph_by_view" style="height:400px"></div>
              </div>
          </div>
      </div>
      <div class="analytify_status_footer">
          <span class="analytify_info_stats"><?php esc_html_e( 'Detailed Visitors and Views breakdown in months and years', 'wp-analytify-pro' ); ?></span>
      </div>
  </div>
  <?php
}