<?php
// View of Browser Statistics
function pa_include_browser( $current, $browser_stats ) {
?>

<div class="analytify_popup" id="browser">
  <div class="analytify_popup_header">
    <h4><?php esc_html_e( 'Top Browsers', 'wp-analytify'); ?></h4>
    <span class="analytify_popup_clsbtn">&times;</span>
  </div>
  <div class="analytify_popup_body">
  	<div class="table-responsive">
    	<table class="analytify_table analytify_table_hover">
        <thead>
          <tr>
            <th class="num" width="20">#</th>
            <th><?php esc_html_e( 'Browser', 'wp-analytify-pro'); ?></th>
            <th><?php esc_html_e( 'OS', 'wp-analytify-pro'); ?></th>
            <th><?php analytify_e( 'Sessions', 'wp-analytify'); ?></th>
          </tr>
        </thead>
        <tbody>
        <?php
             if (!empty( $browser_stats["rows"] ) ) {
            $i=1;
        ?>
        <?php foreach ( $browser_stats["rows"] as $b_stats ){ ?>
          <tr>
            <td class="num"><?php  echo $i; ?></td>
            <td><?php  echo $b_stats[0];    ?></td>
            <td><?php  echo $b_stats[1];    ?></td>
            <td><?php  echo $current->wpa_number_format( $b_stats[2] );    ?></td>
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
  	<span class="analytify_popup_info"></span> <?php esc_html_e( 'These are the top browsers of this page.', 'wp-analytify-pro'); ?>
  </div>
</div>

<?php }
