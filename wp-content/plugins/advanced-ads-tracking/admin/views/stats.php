<?php
$ad_titles = array();
$autocomplete_src = array();
foreach( $all_ads as $_ad ) {
   //Mujahid code edit
	$advanced_ads_ad_options = maybe_unserialize(get_post_meta($_ad->ID, 'advanced_ads_ad_options', true ));
	$attach_id = $advanced_ads_ad_options['output']['image_id'] ;
	$img_atts = wp_get_attachment_image_src($attach_id, 'thumbnail');
	$src = $img_atts[0];
	$ad_titles[ $_ad->ID ] = "<img src='".$src."' style='height:26px;'> &nbsp;".nl2br(str_replace("&nbsp;","&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$_ad->post_title));
    $autocomplete_src[] = array(
        'label' => $_ad->post_title,
        'value' => $_ad->ID,
    );
}
$ad_titles['length'] = count( $ad_titles );
$nonce = wp_create_nonce( 'advads-stats-page' );
$has_tables = $this->has_tables();
$analytics_home_url = 'https://analytics.google.com/analytics/web/';
$analytics_notice = sprintf( __( 'You are currently tracking ads with Google Analytics. The statistics can be viewed only within your <a href="%s" target="_blank">Analytics account</a>.', 'advanced-ads-tracking' ), $analytics_home_url );
$missing_tables_notice = sprintf(
	__( 'The tracking tables were not found in database. %sCreate tables%s', 'advanced-ads-tracking' ),
	'<a href="' . admin_url( 'admin.php?page=advanced-ads-stats&action=create_track_tables&nonce=' . $nonce . '" class="button-secondary">' ),
	'</a>'
);

/**
 *  ad groups
 */
global $wpdb;
$ad_model = new Advanced_Ads_Model( $wpdb );
$terms = $ad_model->get_ad_groups( array( 'post_status' => array( 'publish', 'future', 'draft', 'pending' ) ) );
$groups_to_ads = array();
$ads_to_groups = array();
$groups_autocomplete = array();
foreach ( $terms as $term ) {
	$_group = new Advanced_Ads_Group( $term->term_id, array( 'post_status' => array( 'publish', 'future', 'draft', 'pending' ) ) );
	$_group_ads = $_group->get_all_ads();
	$__ads = array();
	foreach ( $_group_ads as $__ad ) {
		$__ads[$__ad->ID] = array(
			'ID' => $__ad->ID,
			'title' => $__ad->post_title,
		);
		if ( !isset( $ads_to_groups[$__ad->ID] ) ) {
			$ads_to_groups[$__ad->ID] = array();
		}
		$ads_to_groups[$__ad->ID][] = $term->term_id;
	}
	$groups_to_ads[$term->term_id] = array(
		'ID' => $term->term_id,
		'slug' => $term->slug,
		'name' => $term->name,
		'ads' => $__ads,
	);
	$groups_autocomplete[] = array(
		'label' => $term->name,
		'value' => $term->term_id,
	);
}
$group_count = count( $groups_to_ads );
$groups_to_ads['length'] = $group_count;
?>
<script type="text/javascript">
	var groupsToAds = <?php echo json_encode( $groups_to_ads ); ?>;
	var adsToGroups = <?php echo json_encode( $ads_to_groups ); ?>;
	var groupAutoCompSrc = <?php echo json_encode( $groups_autocomplete ); ?>;
	jQuery(function($){
				// Print page.
				$(document).on('click', '.print', function(e) {
					e.preventDefault();
					window.print();
				});
				jQuery("#ad-filter").on( "blur", function() {
				  	var title = jQuery(".display-filter-elem").text();
					var tmp = title.split(/\s+/);
					if(tmp[0] !=""){ jQuery("#CompanyName").html('<h3>'+tmp[0]+'</h3>');}
				});
				setTimeout(function(){jQuery("#adTable-table_length select").val(100).change();}, 1000);
		});
</script>
<style type="text/css">
table.dataTable thead th, table.dataTable thead td {
    padding: 5px 18px;
    border-bottom: 1px solid #111;
    background: #000;
    color: #fff;
    font-size: 12px;
}
@media print {
		.entry-title{width:100% !important;}
				#adTable h4,#adTable-table_length,#adTable-table_info,#adTable-table_paginate{
					visibility: hidden;
					display:none;
				  }
				  .only_print{
					visibility:visible;
					display:block;
				  }
				  #wpcontent, #wpfooter {
    margin-left: 0;
}
				table.dataTable thead th:first-child{width:50%;}
				table.dataTable thead .sorting,table.dataTable thead .sorting_asc{background-image:none !important;padding-left:0 !important;}
				  #wpbody {
					background-image: url(/wp-content/themes/dokan-child/watermark.png) !important;
					background-repeat: no-repeat !important;
					background-color: #F2F2F2 !important;
					background-position: center top !important;
					/*background-attachment: fixed !important;*/
					background-size: auto;
					-webkit-backface-visibility: visible !important;
					overflow: hidden !important;
				}
}
#adTable h4,#display-filter-list{display:none !important;}
.dataTables_wrapper .dataTables_length{float:right;}
</style>
<div class="wrap">
    <!--<h1><?php _e('Advertisement Statistics', 'advanced-ads-tracking'); ?></h1>-->
    <h1 class="entry-title" style="float:left;width:33%;">ShopADoc Analytics</h1>
    <form action="" method="post" id="stats-form" class="not_print" style="float:left;">
    <input type="hidden" id="all-ads" value="<?php echo implode( '-', $ads ); ?>" />
        <table id="period-table">
            <thead style="text-align:left;">
             <th><strong><?php _e( 'Filter by ad', 'advanced-ads-tracking' ); ?></strong></th>
                <th><strong><?php _e('Period', 'advanced-ads-tracking'); ?></strong></th>
                <th><strong><?php _e('Group by:', 'advanced-ads-tracking'); ?></strong></th>
                <th><?php if ( current_user_can( advanced_ads_tracking_db_cap() ) ) :?><strong><?php _e('Data source:', 'advanced-ads-tracking'); ?></strong><?php endif; ?></th>
				<?php if ( current_user_can( advanced_ads_tracking_db_cap() ) ) : ?>
				<th style="padding-left:6em;"></th>
				<?php endif; ?>
            </thead>
            <tbody>
            <tr>
            	<td>
               
				<input id="ad-filter" class="donotreversedisable" type="text" value="" <?php if ( 2 > count( $ad_titles ) ) echo 'disabled' ;?> />
				<script type="text/javascript">
					var adTitles = <?php echo json_encode( $ad_titles ); ?>;
					var adTitlesDB = <?php echo json_encode( $ad_titles ); ?>;
					var autoCompSrc = <?php echo json_encode( $autocomplete_src ); ?>;
				</script>
                </td>
                <td>
                    <fieldset class="load-from-db-fields">
                        <label>
                            <select name="advads-stats[period]" class="advads-stats-period">
                                <?php foreach($periods as $_period_key => $_period) : ?>
                                <option value="<?php echo $_period_key; ?>" <?php selected($_period_key, $period); ?>><?php echo $_period; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <input type="text" name="advads-stats[from]" class="advads-stats-from<?php
                            if($period !== 'custom') echo ' hidden'; ?>" value="<?php
                            echo $from; ?>" size="10" maxlength="10" placeholder="<?php _e( 'from', 'advanced-ads-tracking' ); ?>"/>
                        <input type="text" name="advads-stats[to]" class="advads-stats-to<?php
                            if($period !== 'custom') echo ' hidden'; ?>" value="<?php
                            echo $to; ?>" size="10" maxlength="10" placeholder="<?php _e( 'to', 'advanced-ads-tracking' ); ?>"/>
                        <button class="button button-primary" id="load-simple"><?php _e( 'load stats', 'advanced-ads-tracking'); ?></button>
                    </fieldset>
                    <fieldset class="load-from-file-fields" style="display:none;"><?php
					if ( current_user_can( advanced_ads_tracking_db_cap() ) ) :
					$load_from_file_period_args = array(
						'period-options' => array(
							'latestmonth' => __( 'latest month', 'advanced-ads-tracking' ),
							'firstmonth' => __( 'first month', 'advanced-ads-tracking' ),
						),
						'period' => array( 'stats-file-period', '' ),
						'from' => array( 'stats-file-from', '' ),
						'to' => array( 'stats-file-to', '' ),
					);
					Advanced_Ads_Tracking_Dbop::period_select_inputs( $load_from_file_period_args );
					?>
					<button class="button button-primary" disabled id="load-stats-from-file"><?php _e( 'load stats', 'advanced-ads-tracking' ); ?></button>
					<?php endif; ?>
					</fieldset>
                </td>
                <td>
                    <label>
                        <select name="advads-stats[groupby]">
                            <?php foreach($groupbys as $_groupby_key => $_groupby) : ?>
                            <option value="<?php echo $_groupby_key; ?>" <?php selected($_groupby_key, $groupby); ?>><?php echo $_groupby; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="ajax-spinner-placeholder" id="statsA-spinner"></span>
                    </label>
                </td>
				<td>
					<select id="data-source" <?php if ( !current_user_can( advanced_ads_tracking_db_cap() ) ) echo 'style="display:none;"'; ?>>
						<option value="db"><?php _e( 'Database', 'advanced-ads-tracking' ); ?></option>
						<?php if ( current_user_can( advanced_ads_tracking_db_cap() ) ) : ?>
						<option value="file"><?php _e( 'File', 'advanced-ads-tracking' ); ?></option>
						<?php endif; ?>
					</select>
					<?php if ( current_user_can( advanced_ads_tracking_db_cap() ) ) : ?>
					<span class="load-from-file-fields" style="display:none;">
						<button class="button button-secondary" id="select-file"><?php _e( 'select file', 'advanced-ads-tracking' ); ?></button>
						<span class="ajax-spinner-placeholder" id="file-spinner"></span>
						<span class="description" id="stats-file-description"><?php _e( 'no file selected', 'advanced-ads-tracking' ); ?></span>
						<input type="hidden" id="stats-attachment-id" value="" />
						<input type="hidden" id="stats-attachment-firstdate" value="" />
						<input type="hidden" id="stats-attachment-lastdate" value="" />
						<input type="hidden" id="stats-attachment-adIDs" value="" />
					</span>
					<?php endif; ?>
				</td>
				<?php if ( current_user_can( advanced_ads_tracking_db_cap() ) ) : ?>
				<td style="padding-left:6em;"><a href="<?php echo admin_url( 'admin.php?page=' . $this->db_op_page_slug ); ?>"><?php _e( 'Open database management', 'advanced-ads-tracking' ); ?></a></td>
				<?php endif; ?>
            </tr>
            <tr><td colspan="3" id="period-td"></td></tr>
            <tr id="compare-tr" class="hide" <?php echo ( isset( $_REQUEST['advads-stats']['period2'] ) )? '' : 'style="display:none;"'; ?>>
                <td colspan="3" style="padding-top:1.5em;">
                <strong><?php _e( 'Compare with', 'advanced-ads-tracking' ); ?></strong>
                    <fieldset>
                        <button class="button button-secondary donotreversedisable" id="compare-prev-btn"><?php _e( 'previous period', 'advanced-ads-tracking' ); ?></button>
                        &nbsp;&nbsp;
                        <button class="button button-secondary donotreversedisable" id="compare-next-btn"><?php _e( 'next period', 'advanced-ads-tracking' ); ?></button>
                        <input id="compare-offset" value="0" type="hidden" />
                        <input id="compare-from-prev" value="" type="hidden" />
                        <input id="compare-to-prev" value="" type="hidden" />
                        <input id="compare-from-next" value="" type="hidden" />
                        <input id="compare-to-next" value="" type="hidden" />
                    </fieldset>
                </td>
				<?php if ( current_user_can( advanced_ads_tracking_db_cap() ) ) : ?>
				<td></td>
				<?php endif; ?>
            </tr>
            </tbody>
        </table>
        <table style="width:100%;"><tbody><tr>
			<td style="width:50%">
            	<?php /*?>
				<label><?php _e( 'Filter by ad', 'advanced-ads-tracking' ); ?></label>
				<input id="ad-filter" class="donotreversedisable" type="text" value="" <?php if ( 2 > count( $ad_titles ) ) echo 'disabled' ;?> />
				<script type="text/javascript">
					var adTitles = <?php echo json_encode( $ad_titles ); ?>;
					var adTitlesDB = <?php echo json_encode( $ad_titles ); ?>;
					var autoCompSrc = <?php echo json_encode( $autocomplete_src ); ?>;
				</script>
				<?php */?>
			</td>
			<td style="width:50%">
				<div id="group-filter-wrap">
				<?php if ( 1 < $groups_to_ads['length'] ) : ?>
				<label><?php _e( 'Filter by group', 'advanced-ads-tracking' );?></label>
				<input id="group-filter" class="donotreversedisable" type="text" value="" />
				<?php endif; ?>
				</div>
			</td>
        </tr></tbody></table>
        <div id="display-filter-list" style="visibility:hidden;">
            <br>
            <span id="filter-head"><?php _e( 'current filters', 'advanced-ads-tracking' ); ?>:&nbsp;</span>
        </div>
    </form>
    <a href="javascript:" class="not_print print" style="float:right;"><img src="/wp-content/plugins/WP_GA/print.png" align="right" title="print" width="22px"/></a>
	<?php if ( 'ga' === $this->plugin->get_tracking_method() ) : ?>
	<div style="background-color:#ffffff;padding:10px; border-left: 5px solid #00bcd4;display:none;"><span><?php echo $analytics_notice; ?></span></div>
	<?php endif; ?>
	<?php if ( !$has_tables ) : ?>
	<div style="background-color:#ffffff;padding:10px; border-left:5px solid #dc3232"><?php echo $missing_tables_notice; ?></div>
	<?php return; endif; ?>
    
    <!---------------Mujahid Code to repositiion graph------------------>
    <br class="clear" />
    <div id="table-area">
        <div id="adTable"></div>
        <div id="CompanyName" class="only_print"></div>
        <div id="dateTable" class="not_print"></div>
        <br class="clear" />
    </div>
       <!---------------Mujahid Code to repositiion graph------------------>
    <br class="clear" />
    <hr class="not_print" />
    <div id="advads-stats-graph" class="not_print"></div>
    <div id="advads-graph-legend" class="not_print"  style="display:none;">
        <div class="legend-item donotremove">
            <div id="solid-line-legend">
            </div><span><?php _e( 'impressions', 'advanced-ads-tracking' ); ?></span>
        </div>
        <div class="legend-item donotremove not_print">
            <div id="dashed-line-legend">
            </div><span><?php _e( 'clicks', 'advanced-ads-tracking' ); ?></span>
        </div>
    </div>
    <script type="text/javascript">
        var advadsStatPageNonce = '<?php echo $nonce; ?>';
    </script>
    <br class="clear" />
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery("select[name='adTable-table_length']").val(100);
	jQuery("select[name='adTable-table_length']").trigger('change');
});
</script>