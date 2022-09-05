<?php

function pa_include_city( $current, $city_stats) {
?>
<div class="analytify_popup" id="city">
  <div class="analytify_popup_header">
    <h4><?php esc_html_e( 'Top Cities', 'wp-analytify-pro'); ?></h4>
    <span class="analytify_popup_clsbtn">&times;</span>
  </div>
  <div class="analytify_popup_body">
  	<div class="table-responsive">
    	<table class="analytify_table analytify_table_hover">
        <thead>
          <tr>
            <th class="num" width="20">#</th>
            <th><?php analytify_e( 'City', 'wp-analytify'); ?></th>
            <th><?php analytify_e( 'Sessions', 'wp-analytify'); ?></th>
          </tr>
        </thead>
        <tbody>
        <?php
            if (! empty( $city_stats["rows"] ) ) {
            $i = 1;
        ?>
        <?php foreach ($city_stats["rows"] as $c_stats){ ?>
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
    <span class="analytify_popup_info"></span><?php analytify_e( 'Listing statistics of top five cities.', 'wp-analytify' ); ?>
  </div>
</div>

<?php }
