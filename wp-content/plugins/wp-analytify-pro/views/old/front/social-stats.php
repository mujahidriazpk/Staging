<?php
// View of Social Statistics

function pa_include_social( $current, $social_stats ) {
?>
<div class="analytify_popup" id="social">
  <div class="analytify_popup_header">
    <h4><?php esc_html_e( 'Social media Statistics', 'wp-analytify-pro'); ?></h4>
    <span class="analytify_popup_clsbtn">&times;</span>
  </div>
  <div class="analytify_popup_body">
  	<div class="table-responsive">
    	<table class="analytify_table analytify_table_hover">
        <thead>
          <tr>
            <th class="num" width="20">#</th>
            <th><?php esc_html_e( 'Social', 'wp-analytify-pro'); ?></th>
            <th><?php analytify_e( 'Visits', 'wp-analytify'); ?></th>
          </tr>
        </thead>
        <tbody>
        <?php
          if (!empty( $social_stats["rows"] ) ) {
          $i = 1;
        ?>
        <?php foreach ( $social_stats["rows"] as $s_stats ){ ?>
          <tr>
            <td class="num"><?php  echo $i; ?></td>
            <td><?php  echo $s_stats[0];    ?></td>
            <td><?php  echo $current->wpa_number_format( $s_stats[1] );    ?></td>
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
    <span class="analytify_popup_info"></span><?php esc_html_e( 'These are the social media statistics of this page.', 'wp-analytify-pro'); ?>
  </div>
</div>
<?php }
