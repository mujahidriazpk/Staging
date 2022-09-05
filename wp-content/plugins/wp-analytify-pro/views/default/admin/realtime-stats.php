<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

/*
 * View of RealTime Statistics
 */
function fetch_realtime_stats ( $current ) {

  ?>
  <h2 class="analytify_pull_left real_time_stats_hd"><?php esc_html_e( 'Real Time Stats', 'wp-analytify-pro' ); ?></h2>
	<div class="analytify_real_time_stats analytify_status_box_wraper">
			<div class="analytify_visitors_online analytify_real_time_stats_boxes">
					<div class="analytify_number" id="pa-online">0</div>
					<div class="analytify_label"><?php esc_html_e( 'Visitors online', 'wp-analytify-pro' ); ?></div>
			</div>
			<div class="analytify_referral analytify_real_time_stats_boxes">
					<div class="analytify_number" id="pa-referral">0</div>
					<div class="analytify_label"><?php esc_html_e( 'Referral', 'wp-analytify-pro' ); ?></div>
			</div>
			<div class="analytify_organic analytify_real_time_stats_boxes">
					<div class="analytify_number" id="pa-organic">0</div>
					<div class="analytify_label"><?php esc_html_e( 'ORGANIC', 'wp-analytify-pro' ); ?></div>
			</div>
			<div class="analytify_social analytify_real_time_stats_boxes">
					<div class="analytify_number" id="pa-social">0</div>
					<div class="analytify_label"><?php esc_html_e( 'social', 'wp-analytify-pro' ); ?></div>
			</div>
			<div class="analytify_direct analytify_real_time_stats_boxes">
					<div class="analytify_number" id="pa-direct">0</div>
					<div class="analytify_label"><?php esc_html_e( 'direct', 'wp-analytify-pro' ); ?></div>
			</div>
			<div class="analytify_new analytify_real_time_stats_boxes">
					<div class="analytify_number" id="pa-new">0</div>
					<div class="analytify_label"><?php esc_html_e( 'new', 'wp-analytify-pro' ); ?></div>
			</div>
			<div class="analytify_returning analytify_real_time_stats_boxes">
					<div class="analytify_number" id="pa-returning">0</div>
					<div class="analytify_label"><?php esc_html_e( 'Returning', 'wp-analytify-pro' ); ?></div>
			</div>
	</div>
  <?php

  $code = '<script type="text/javascript">

      var is_error = "";
      function get_fresh_stats() {

        if ( is_error == true ) {
          return;
        }
        jQuery.post(ajaxurl, {action: "analytify_load_online_visitors", pa_security: "' . wp_create_nonce( 'pa_get_online_data' ) . '"}, function(response){

          var data = jQuery.parseJSON(response);

          // dont send request if error generate once.
          if ( data == false ) {
            is_error = true;
            return;
          }

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

          var referral  = 0;
          var organic   = 0;
          var social    = 0;
          var direct    = 0;
          var new_users = 0;
          var returning = 0

          if( data["rows"] ) {
            for ( var i = 0; i < data["rows"].length; i = i + 1 ) {

              if( data["rows"][i][2] == "REFERRAL" ) {
                referral++;
              }
              if( data["rows"][i][2]== "ORGANIC" ) {
                organic++;
              }
              if( data["rows"][i][2] == "SOCIAL" ) {
                social++;
              }
              if( data["rows"][i][2] == "DIRECT" ) {
                direct++;
              }

              if( data["rows"][i][3] == "RETURNING" ) {
                returning++;
              }
              if( data["rows"][i][3] == "NEW" ) {
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

          });
        };

        get_fresh_stats();
        setInterval(get_fresh_stats, 30000);

    </script>';

  echo $code;

}
