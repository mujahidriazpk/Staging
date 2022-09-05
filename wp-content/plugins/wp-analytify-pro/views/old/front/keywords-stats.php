<?php function pa_include_keywords($current,$keyword_stats)
{
?>
<div class="analytify_popup" id="keywords">
  <div class="analytify_popup_header">
    <h4><?php esc_html_e( 'TOP KEYWORDS', 'wp-analytify'); ?></h4>
    <span class="analytify_popup_clsbtn">&times;</span>
  </div>
  <div class="analytify_popup_body">
  	<div class="table-responsive">
    	<table class="analytify_table analytify_table_hover">
        <thead>
          <tr>
            <th class="num" width="20">#</th>
            <th><?php analytify_e( 'Keywords', 'wp-analytify'); ?></th>
            <th><?php analytify_e( 'Sessions', 'wp-analytify'); ?></th>
          </tr>
        </thead>
        <tbody>
        <?php
             if (!empty($keyword_stats["rows"])) {
            $i=1;
        ?>
        <?php foreach ( $keyword_stats["rows"] as $k_stats ){ ?>
          <tr>
            <td class="num"><?php  echo $i; ?></td>
            <td><?php  echo $k_stats[0];    ?></td>
            <td><?php  echo $current->wpa_number_format( $k_stats[1] );    ?></td>
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
    <span class="analytify_popup_info"></span><?php esc_html_e( 'These are the keywords people are searching for this page.', 'wp-analytify-pro' ); ?>
  </div>
</div>
<?php }
