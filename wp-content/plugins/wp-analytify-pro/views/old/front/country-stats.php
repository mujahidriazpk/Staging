<?php
// View of Country wise Statistics

function pa_include_country( $current, $country_stats ) {
	//print_r($country_stats);

	?>
	<div class="analytify_popup" id="country">
		<div class="analytify_popup_header">
			<h4><?php analytify_e( 'Top Countries', 'wp-analytify' ); ?></h4>
			<span class="analytify_popup_clsbtn">&times;</span>
		</div>
		<div class="analytify_popup_body">
			<div class="table-responsive">
				<table class="analytify_table analytify_table_hover">
					<thead>
						<tr>
							<th class="num" width="20">#</th>
							<th><?php analytify_e( 'Country', 'wp-analytify' ); ?></th>
							<th><?php analytify_e( 'Sessions', 'wp-analytify '); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						if (! empty( $country_stats["rows"] ) ) {
							$i = 1;
							?>
							<?php foreach ($country_stats["rows"] as $c_stats){ ?>
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
		<span class="analytify_popup_info"></span><?php analytify_e( 'Listing statistics of top five countries.', 'wp-analytify'); ?>
	</div>
</div>
<?php }
