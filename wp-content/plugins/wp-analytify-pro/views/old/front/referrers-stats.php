 <?php

function pa_include_referrers( $current, $referr_stats) {
?>
<div class="analytify_popup" id="referrer">
  <div class="analytify_popup_header">
    <h4><?php analytify_e( 'Top Referrers', 'wp-analytify'); ?></h4>
    <span class="analytify_popup_clsbtn">&times;</span>
  </div>
  <div class="analytify_popup_body">
  	<div class="table-responsive">
    	<table class="analytify_table analytify_table_hover">
        <thead>
          <tr>
            <th class="num" width="20"><?php esc_html_e( '#', 'wp-analytify'); ?></th>
            <th><?php esc_html_e( 'Social referrer', 'wp-analytify-pro'); ?></th>
            <th><?php analytify_e( 'Visits', 'wp-analytify'); ?></th>
          </tr>
        </thead>
        <tbody>
        <?php
             if (!empty( $referr_stats["rows"] ) ) {
            $i=1;
        ?>
        <?php foreach ( $referr_stats["rows"] as $r_stats ){ ?>
          <tr>
            <td class="num"><?php  echo $i; ?></td>

            <td>
                <i>
                    <?php echo $r_stats[0];?> / <?php echo $r_stats[1];?>
                </i>
            </td>
            <td><?php echo $current->wpa_number_format( $r_stats[2] ); ?></td>
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
    <span class="analytify_popup_info"></span><?php esc_html_e( 'These are the top referrers of this page.', 'wp-analytify'); ?>
  </div>
</div>
<?php }
