<?php

function pa_include_mobile( $current, $mobile_stats) {
?>
<div class="analytify_popup" id="mobile">
  <div class="analytify_popup_header">
    <h4><?php esc_html_e( 'Mobile device Statistics', 'wp-analytify'); ?></h4>
    <span class="analytify_popup_clsbtn">&times;</span>
  </div>
  <div class="analytify_popup_body">
  	<div class="table-responsive">
    	<table class="analytify_table analytify_table_hover">
        <thead>
          <tr>
            <th class="num" width="20"><?php esc_html_e( '#', 'wp-analytify'); ?></th>
            <th><?php esc_html_e( 'Device', 'wp-analytify'); ?></th>
            <th><?php analytify_e( 'Visits', 'wp-analytify'); ?></th>
          </tr>
        </thead>
        <tbody>
        <?php
            if (! empty( $mobile_stats["rows"] ) ) {
            $i = 1;
        ?>
        <?php foreach ($mobile_stats["rows"] as $c_stats){ ?>
          <tr>
            <td class="num"><?php  echo $i; ?></td>
            <td><?php  echo $c_stats[0];    ?></td>
            <td><?php  echo $current->wpa_number_format( $c_stats[1] );    ?></td>
          </tr>
        <?php
            $i++;
            }
        }
        ?>
        </tbody>
      </table>
  	</div>
  </div>
  <div class="analytify_popup_footer">
    <span class="analytify_popup_info"></span><?php esc_html_e( 'Listing statistics of Mobile usage.', 'wp-analytify'); ?>
  </div>
</div>
<?php }
