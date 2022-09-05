
<?php function pa_include_detail_realtime () {
	$wp_analytify   = $GLOBALS['WP_ANALYTIFY'];

	$code = '';
	$dashboard_profile_ID = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'profile_for_dashboard','wp-analytify-profile' );

	$is_access_level =  $GLOBALS['WP_ANALYTIFY']->settings->get_option('show_analytics_roles_dashboard','wp-analytify-dashboard');
	$acces_token  = get_option( "post_analytics_token" );

	$version = defined( 'ANALYTIFY_PRO_VERSION' ) ? ANALYTIFY_PRO_VERSION : ANALYTIFY_VERSION; ?>

	<div class="wpanalytify analytify-dashboard-nav">
		<div class="wpb_plugin_wraper">
			<div class="wpb_plugin_header_wraper">
				<div class="graph"></div>
					<div class="wpb_plugin_header">
						<div class="wpb_plugin_header_title"></div>
						<div class="wpb_plugin_header_info">
								<a href="https://analytify.io/changelog/" target="_blank" class="btn">Changelog - v<?php echo $version; ?></a>
						</div>
						<div class="wpb_plugin_header_logo">
								<img src="<?php echo ANALYTIFY_PLUGIN_URL . '/assets/images/logo.svg'?>" alt="Analytify">
						</div>
					</div>
				</div>
						
				<div class="analytify-dashboard-body-container">
					<div class="wpb_plugin_body_wraper">
						<div class="wpb_plugin_body">
							<div class="wpa-tab-wrapper">	<?php echo $GLOBALS['WP_ANALYTIFY']->dashboard_navigation(); ?> </div>
							<div class="wpb_plugin_tabs_content analytify-dashboard-content">
										
								<div class="analytify_wraper">
									<div class="analytify_main_title_section">
										<div class="analytify_dashboard_title">

											<h1 class="analytify_pull_left analytify_main_title"><?php esc_html_e( 'Real-Time Traffic Dashboard', 'wp-analytify' ); ?></h1>

											<?php 
											$_analytify_profile = get_option( 'wp-analytify-profile' );

											if ( $acces_token && isset( $_analytify_profile['profile_for_dashboard'] ) && ! empty( $_analytify_profile['profile_for_dashboard'] ) ) : ?>

												<span class="analytify_stats_of"><a href="<?php echo WP_ANALYTIFY_FUNCTIONS::search_profile_info( $dashboard_profile_ID, 'websiteUrl' ) ?>" target="_blank"><?php echo WP_ANALYTIFY_FUNCTIONS::search_profile_info( $dashboard_profile_ID, 'websiteUrl' ) ?></a> (<?php echo WP_ANALYTIFY_FUNCTIONS::search_profile_info( $dashboard_profile_ID, 'name' ) ?>)</span>

											<?php endif; ?>
										
										</div>
										<!-- <div class="analytify_select_dashboard analytify_pull_right"><?php // do_action( 'analytify_dashboad_dropdown' ); ?></div> -->
									</div>

									<?php 
									if ( ! WP_ANALYTIFY_FUNCTIONS::wpa_check_profile_selection('Analytify') ) {

									/*
									* Check with roles assigned at dashboard settings.
									*/
									$is_access_level = $wp_analytify->settings->get_option( 'show_analytics_roles_dashboard','wp-analytify-dashboard' );
									
									// Show dashboard to admin incase of empty access roles.
									if ( empty( $is_access_level ) ) { $is_access_level = array( 'Administrator' ); }

									if ( $wp_analytify->pa_check_roles( $is_access_level ) ) {

										if ( $acces_token ) { ?>

											  <div class="analytify_real_time_stats analytify_status_box_wraper">
												<div class="analytify_visitors_online analytify_real_time_stats_boxes">
													<div class="analytify_number" id="pa-online">0</div>
													<div class="analytify_label"><?php _e( 'Visitors online' , 'wp-analytify-pro' ) ?></div>
												</div>
												<div class="analytify_referral analytify_real_time_stats_boxes">
													<div class="analytify_number" id="pa-referral">0</div>
													<div class="analytify_label"><?php _e( 'Referral' , 'wp-analytify-pro' ) ?></div>
												</div>
												<div class="analytify_organic analytify_real_time_stats_boxes">
													<div class="analytify_number" id="pa-organic">0</div>
													<div class="analytify_label"><?php _e( 'ORGANIC' , 'wp-analytify-pro' ) ?></div>
												</div>
												<div class="analytify_social analytify_real_time_stats_boxes">
													<div class="analytify_number" id="pa-social">0</div>
													<div class="analytify_label"><?php _e( 'social' , 'wp-analytify-pro' ) ?></div>
												</div>
												<div class="analytify_direct analytify_real_time_stats_boxes">
													<div class="analytify_number" id="pa-direct">0</div>
													<div class="analytify_label"><?php _e( 'direct' , 'wp-analytify-pro' ) ?></div>
												</div>
												<div class="analytify_new analytify_real_time_stats_boxes">
													<div class="analytify_number" id="pa-new">0</div>
													<div class="analytify_label"><?php _e( 'new' , 'wp-analytify-pro' ) ?></div>
												</div>
												<div class="analytify_returning analytify_real_time_stats_boxes">
													<div class="analytify_number" id="pa-returning">0</div>
													<div class="analytify_label"><?php _e( 'Returning' , 'wp-analytify-pro' ) ?></div>
												</div>
											</div>

											<div class="analytify_general_status analytify_status_box_wraper">
												<div class="analytify_status_header">
													<h3><?php _e( 'RealTime Stats' , 'wp-analytify-pro' ) ?></h3>
													<div class="analytify_top_page_detials analytify_tp_btn">
														<a id='refresh-realtime-stats'  class="analytify_tooltip" href="#"> 
														<span class="analytify_tooltiptext">Refresh Stats</span>
													</a>
													</div>
												</div>
												<div class="analytify_status_body">
													<div id="analytify_real_time_visitors" style="height:400px"></div>
												</div>
											</div>
									
											<div class="analytify_general_status analytify_status_box_wraper">
												<div class="analytify_status_header">
													<h3><?php _e( 'Top Active posts and pages' , 'wp-analytify-pro' ) ?></h3>
												</div>
												<div class="analytify_status_body">
													<div class="analytify_top_pages_boxes_wraper" id="pa-pages">

													</div>
												</div>
												<div class="analytify_status_footer">
													<span class="analytify_info_stats"><?php _e( 'Top active pages and posts users are currently at' , 'wp-analytify-pro' ) ?>.</span>
												</div>
												
											</div>

										<?php
										echo pa_realtime();
									
				
									} else {
										esc_html_e( 'You must be authenticated to see the Analytics Dashboard.', 'wp-analytify' );
									}
								} else {
									esc_html_e( 'You don\'t have access to Analytify Dashboard.', 'wp-analytify' );
								}
							} ?>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php }

 function pa_realtime() {

   $dashboard_profile_ID = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'profile_for_dashboard','wp-analytify-profile' );
    ?>

    <script>
  jQuery(document).ready(function ($) {



      var time_data = [];
       analytics_data = [];
        for (var i = 600 ; i > -1 ; i = i-30 ) {
          time_data.push(  i + 's'   );
          analytics_data.push(0);
        }




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
               years_graph_by_visitors = ec.init(document.getElementById('analytify_real_time_visitors'));


              var years_graph_by_visitors_option = {
                  tooltip: {

                      show: true
                  },
                  color: [
                      '#03a1f8'
                  ],
                  toolbox: {
                      show : false,
                      color:["#444444","#444444","#444444","#444444"],
                      feature : {
                          magicType : {show: true, type: ['line', 'bar']},
                          saveAsImage : {show: true}
                      }
                  },
                  xAxis : [
                      {
                          type : 'category',
                          boundaryGap : false,
                          data : time_data
                      }
                  ],
                  yAxis : [
                      {
                          type : 'value'
                      }
                  ],
                  series : [
                      {
                          "name":"Real Time",
                          "type":"line",
                          smooth: true,
                          itemStyle: {
                            normal: {
                              areaStyle: {
                                type: 'default'
                              }
                            }
                          },
                          "data": analytics_data
                      }

                  ]
              };



          // Load data into the ECharts instance
          years_graph_by_visitors.setOption(years_graph_by_visitors_option);


          }
      );
  });

  </script>


    <?php
   $code = '
   <script type="text/javascript">
   function onlyUniqueValues(value, index, self) {
     return self.indexOf(value) === index;
   }

   function countvisits(data, searchvalue) {
     var count = 0;
     if(data["rows"]){
       for ( var i = 0; i < data["rows"].length; i = i + 1 ) {
         if (jQuery.inArray(searchvalue, data["rows"][ i ])>-1){
           count += parseInt(data["rows"][ i ][6]);
         }
       }
     }
     return count;
   }

   function pa_generatetooltip(data) {
     var count = 0;
     var table = "";
     for ( var i = 0; i < data.length; i = i + 1 ) {
       count += parseInt(data[ i ].count);
       table += "<td>"+data[i].value+"</td><td class=\'pa-pgdetailsr\'>"+data[ i ].count+"</td></tr>";
     };
     if (count){
       return("<table>"+table+"</table>");
     }else{
       return("");
     }
   }

   function pa_pagedetails(data, searchvalue) {
     var newdata = [];
     for ( var i = 0; i < data["rows"].length; i = i + 1 ){
       var sant=1;
       for ( var j = 0; j < newdata.length; j = j + 1 ){
         if (data["rows"][i][0]+data["rows"][i][1]+data["rows"][i][2]+data["rows"][i][3]==newdata[j][0]+newdata[j][1]+newdata[j][2]+newdata[j][3]){
           newdata[j][6] = parseInt(newdata[j][6]) + parseInt(data["rows"][i][6]);
           sant = 0;
         }
       }
       if (sant){
         newdata.push(data["rows"][i].slice());
       }
     }

     var countrfr = 0;
     var countkwd = 0;
     var countdrt = 0;
     var countscl = 0;
     var tablerfr = "";
     var tablekwd = "";
     var tablescl = "";
     var tabledrt = "";
     for ( var i = 0; i < newdata.length; i = i + 1 ) {
       if (newdata[i][0] == searchvalue){
         var pagetitle = newdata[i][5]
         switch (newdata[i][3]){
           case "REFERRAL":    countrfr += parseInt(newdata[ i ][6]);
           tablerfr += "<tr><td class=\'pa-pgdetailsl\'>"+newdata[i][1]+"</td><td class=\'pa-pgdetailsr\'>"+newdata[ i ][6]+"</td></tr>";
           break;
           case "ORGANIC":     countkwd += parseInt(newdata[ i ][6]);
           tablekwd += "<tr><td class=\'pa-pgdetailsl\'>"+newdata[i][2]+"</td><td class=\'pa-pgdetailsr\'>"+newdata[ i ][6]+"</td></tr>";
           break;
           case "SOCIAL":      countscl += parseInt(newdata[ i ][6]);
           tablescl += "<tr><td class=\'pa-pgdetailsl\'>"+newdata[i][1]+"</td><td class=\'pa-pgdetailsr\'>"+newdata[ i ][6]+"</td></tr>";
           break;
           case "DIRECT":      countdrt += parseInt(newdata[ i ][6]);
           break;
         };
       };
     };
     if (countrfr){
       tablerfr = "' . esc_html__( "REFERRALS", 'wp-analytify-pro' ) . ' ("+countrfr+")"+tablerfr+"";
     }
     if (countkwd){
       tablekwd = "' . esc_html__( "KEYWORDS", 'wp-analytify-pro' ) . ' ("+countkwd+")"+tablekwd+"";
     }
     if (countscl){
       tablescl = "' . esc_html__( "SOCIAL", 'wp-analytify-pro' ) . ' ("+countscl+")"+tablescl+"";
     }
     if (countdrt){
       tabledrt = "' . esc_html__( "DIRECT", 'wp-analytify-pro' ) . ' ("+countdrt+")";
     }
     return (pagetitle);
   }

   function online_refresh(){
     jQuery.post(ajaxurl, {action: "analytify_load_detail_realtime_stats", pa_security: "'. wp_create_nonce('pa_get_online_data'). '"}, function(response){

       var data = jQuery.parseJSON(response);
       jQuery("#refresh-realtime-stats").prop("disabled", false);
       if (data == "") return;

       if ( data["totalsForAllResults"]["ga:activeVisitors"] !== document.getElementById( "pa-online" ).innerHTML ) {
         jQuery( "#pa-online" ).fadeOut( "slow" );
         jQuery( "#pa-online" ).fadeOut( 500 );
         jQuery( "#pa-online" ).fadeOut( "slow", function() {
           document.getElementById( "pa-online" ).innerHTML = data["totalsForAllResults"]["ga:activeVisitors"];
         } );
         jQuery( "#pa-online" ).fadeIn( "slow" );
         jQuery( "#pa-online" ).fadeIn( 500 );
         jQuery( "#pa-online" ).fadeIn( "slow", function() {

         });


       };

       // Add data to graph
       analytics_data.shift();
       analytics_data.push( data["totalsForAllResults"]["ga:activeVisitors"] );
       years_graph_by_visitors.setOption({
              series: [{
                  data: analytics_data
              }]
          });

       var referral  = 0;
       var organic   = 0;
       var social    = 0;
       var direct    = 0;
       var new_users = 0;
       var returning = 0

       if( data["rows"] ) {
         for ( var i = 0; i < data["rows"].length; i = i + 1 ) {

           if( data["rows"][i][3] == "REFERRAL" ) {
             referral++;
           }
           if( data["rows"][i][3]== "ORGANIC" ) {
             organic++;
           }
           if( data["rows"][i][3] == "SOCIAL" ) {
             social++;
           }
           if( data["rows"][i][3] == "DIRECT" ) {
             direct++;
           }

           if( data["rows"][i][4] == "RETURNING" ) {
             returning++;
           }
           if( data["rows"][i][4] == "NEW" ) {
             new_users++;
           }

         }
       }

       document.getElementById("pa-referral").innerHTML  = referral;
       document.getElementById("pa-organic").innerHTML   = organic;
       document.getElementById("pa-social").innerHTML    = social;
       document.getElementById("pa-direct").innerHTML    = direct;
       document.getElementById("pa-returning").innerHTML = returning;
       document.getElementById("pa-new").innerHTML       = new_users;

       // if (!data["totalsForAllResults"]["ga:activeVisitors"]){
         // 	location.reload();
         // }

         var pagepath = [];
         var referrals = [];
         var keywords = [];
         var social = [];
         var visittype = [];
         if(data["rows"]){
           for ( var i = 0; i < data["rows"].length; i = i + 1 ) {
             pagepath.push( data["rows"][ i ][0] );
             if (data["rows"][i][3]=="REFERRAL"){
               referrals.push( data["rows"][ i ][1] );
             }
             if (data["rows"][i][3]=="ORGANIC"){
               keywords.push( data["rows"][ i ][2] );
             }
             if (data["rows"][i][3]=="SOCIAL"){
               social.push( data["rows"][ i ][1] );
             }
             visittype.push( data["rows"][ i ][3] );
           }
         }

         var upagepath = pagepath.filter(onlyUniqueValues);
         var upagepathstats = [];
         for ( var i = 0; i < upagepath.length; i = i + 1 ) {
           upagepathstats[i]={"pagepath":upagepath[i],"count":countvisits(data,upagepath[i])};
         }
         upagepathstats.sort( function(a,b){ return b.count - a.count } );

         var pgstatstable = "";
         for ( var i = 0; i < upagepathstats.length; i = i + 1 ) {
           if (i < 10 ){
             pgstatstable += "<tr class=\"pa-pline\"><td class=\"pa-pright\">"+(i+1)+"</td><td class=\"pa-pleft\"><a href=\"'. WP_ANALYTIFY_FUNCTIONS::search_profile_info( $dashboard_profile_ID, 'websiteUrl' ).'"+upagepathstats[i].pagepath.substring(0,70)+"\" title=\""+pa_pagedetails(data, upagepathstats[i].pagepath)+"\" target=\"_blank\">"+pa_pagedetails(data, upagepathstats[i].pagepath)+"</a></td><td class=\"pa-pright\">"+upagepathstats[i].count+"</td></tr>";
           }
         }
         document.getElementById("pa-pages").innerHTML="<table class=\"pa-pg analytify_data_tables\"><tr><th class=\"wd_1 analytify_txt_left\">#</th><th>'. esc_html__( "Page Title", 'wp-analytify-pro' ) . '</th><th class=\"wd_2\">'. esc_html__( "Visitors", 'wp-analytify-pro' ) . '</th></tr>"+pgstatstable+"</table>";

         var ureferralsstats = [];
         var ureferrals = referrals.filter(onlyUniqueValues);
         for ( var i = 0; i < ureferrals.length; i = i + 1 ) {
           ureferralsstats[i]={"value":ureferrals[i],"count":countvisits(data,ureferrals[i])};
         }
         ureferralsstats.sort( function(a,b){ return b.count - a.count } );

         var ukeywordsstats = [];
         var ukeywords = keywords.filter(onlyUniqueValues);
         for ( var i = 0; i < ukeywords.length; i = i + 1 ) {
           ukeywordsstats[i]={"value":ukeywords[i],"count":countvisits(data,ukeywords[i])};
         }
         ukeywordsstats.sort( function(a,b){ return b.count - a.count } );

         var usocialstats = [];
         var usocial = social.filter(onlyUniqueValues);
         for ( var i = 0; i < usocial.length; i = i + 1 ) {
           usocialstats[i]={"value":usocial[i],"count":countvisits(data,usocial[i])};
         }
         usocialstats.sort( function(a,b){ return b.count - a.count } );

         var uvisittype = ["REFERRAL","ORGANIC","SOCIAL"];

         var uvisitortype = ["DIRECT","NEW","RETURNING"];




       });
     };
     online_refresh();
     setInterval(online_refresh, 30000);

     // Refresh Stats
     jQuery("#refresh-realtime-stats").on("click", function(e){
       e.preventDefault();
       analytics_data =  [];
       for (var i = 600 ; i > -1 ; i = i-30 ) {
         analytics_data.push(0);
       }
       years_graph_by_visitors.setOption({
              series: [{
                  data: analytics_data
              }]
          });
      jQuery("#pa-pages").empty();
      jQuery("#pa-online, #pa-referral, #pa-organic, #pa-social, #pa-direct, #pa-new, #pa-returning").html(0);
      online_refresh();
      jQuery("#refresh-realtime-stats").prop("disabled", true);
     });
     </script>';

return $code;
}