<?php

// View of Operating System wise Statistics

function pa_include_operating( $current, $os_stats ) {
?>
<div class="analytify_popup" id="os">
  <div class="analytify_popup_header">
    <h4><?php analytify_e( 'Operating System Statistics', 'wp-analytify'); ?></h4>
    <span class="analytify_popup_clsbtn">&times;</span>
  </div>
  <div class="analytify_popup_body">
  	<div class="table-responsive">
    	<table class="analytify_table analytify_table_hover">
        <thead>
          <tr>
            <th class="num" width="20">#</th>
            <th><?php esc_html_e( 'Operating System (Version)', 'wp-analytify-pro'); ?></th>
            <th><?php analytify_e( 'Sessions', 'wp-analytify'); ?></th>
          </tr>
        </thead>
        <tbody>
        <?php
            if (! empty( $os_stats["rows"] ) ) {
            $i = 1;
        ?>
        <?php foreach ($os_stats["rows"] as $rows){ ?>
          <tr>
            <td class="num"><?php  echo $i; ?></td>
            <td><?php  echo $rows[0];?> (<?php  echo $rows[1];?>)</td>
            <td><?php  echo $current->wpa_number_format( $rows[2] );    ?></td>
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
    <span class="analytify_popup_info"></span><?php esc_html_e( 'Listing statistics of top Operating Systems.', 'wp-analytify'); ?>
  </div>
</div>
<?php }
