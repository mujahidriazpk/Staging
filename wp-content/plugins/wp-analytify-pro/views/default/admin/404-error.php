<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }


function fetch_error( $current, $stats ) { ?>


  <table class="analytify_data_tables">
    <thead>
      <tr>
        <th class="analytify_num_row">#</th>
        <th class="analytify_txt_left"><?php esc_html_e( 'URL', 'wp-analytify-pro' ); ?></th>
        <th class="analytify_value_row"><?php esc_html_e( 'Hits', 'wp-analytify-pro' ); ?></th>
      </tr>
    </thead>
    <tbody>

      <?php

      if ( isset( $stats['rows'] ) && $stats['rows'] > 0 ) {

        $i = 1;
        foreach ( $stats['rows'] as $errors ) :
          ?>
          <tr>
            <td class="analytify_txt_center"><?php echo $i; ?></td>
            <td><?php echo $errors[1]; ?></td>
            <td class="analytify_txt_center"><?php echo WPANALYTIFY_Utils::pretty_numbers( $errors[2] ); ?></td>
          </tr>
          <?php
          $i++;
        endforeach;
      } else {
        echo ' <tr> <td  class="analytify_td_error_msg" colspan="3">';
                 $current->no_records();
        echo  '</td> </tr>';
      }
      ?>

    </tbody>
  </table>

  <?php

}
