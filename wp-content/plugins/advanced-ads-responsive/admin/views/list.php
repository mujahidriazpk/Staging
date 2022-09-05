<div class="wrap">
    <h1><?php _e('Responsive Ads', 'advanced-ads-responsive'); ?></h1>
    <?php if($max_columns <= 1) : ?>
    <p><?php _e('Not enough responsive ads to list them.', 'advanced-ads-responsive'); ?></p>
    <?php else : ?>
    <table id='advads-responsive-list'>
        <thead>
            <tr>
                <th><?php _e('ad group', 'advanced-ads-responsive'); ?></th>
                <th><?php _e('ad', 'advanced-ads-responsive'); ?></th>
                <?php foreach($widths as $_width => $_width_key) : ?>
                <th><?php echo $_width; ?>+</th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php $group_index = 0;
            foreach($sorted_ads as $_ad_group_id => $_ads) : ?>
            <?php if(!$group_index) : ?><tr><th rowspan="<?php echo count($_ads); ?>"><?php
                if(isset($groups[$_ad_group_id]->name)){
                    echo $groups[$_ad_group_id]->name;
                } else {
                    _e('(ads without groups)', 'advanced-ads-responsive');
                }

                ?></th><?php endif; ?>
            <?php foreach($_ads as $_ad_id => $_ad) : ?>
                <?php if($group_index) : ?><tr><?php endif; ?>
                <th><?php edit_post_link( $_ad->post_title, null, null, $_ad->ID ); ?></th><?php

		$_from = 0;
		$_to = 0;

		if( isset( $_ad->ad_options['visitors'] ) ){

			foreach( $_ad->ad_options['visitors'] as $_condition ){
				// get span
				if( 'device_width' === $_condition['type'] ){
					switch( $_condition['operator'] ){
					    case 'is_higher' :
						$_from = absint( $_condition['value'] );
						break;
					    case 'is_lower' :
						$_to = absint( $_condition['value'] ) + 1;
						break;
					    default :
						$_from = absint( $_condition['value'] );
						$_to = absint( $_condition['value'] );
					}
				}
			}
		}

		$_fromkey = $widths[$_from];
		$_endkey = ($_to > 1) ? $widths[$_to] : $max_columns;

		$_colspan = $_endkey - $_fromkey;

		for($_i = 0; $_i < $max_columns; $_i++){
		    $_class = (($_i >= $_fromkey && $_i < $_endkey) || !$_colspan) ? 'filled' : '';
		    ?><td class="<?php echo $_class; ?>"></td><?php
		} ?>
            </tr>
            <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div>
        <dl class="advads-responsive-list-legend">
            <dt class="advads-responsive-list-legend-visible"></dt>
            <dd><?php _e('ad is visible', 'advanced-ads-responsive'); ?></dd>
            <dt></dt>
            <dd><?php _e('ad is NOT visible', 'advanced-ads-responsive'); ?></dd>
        </dl>
    </div>
    <?php endif; ?>
</div>
<style>
    #advads-responsive-list { border-collapse: collapse; border-spacing: 0; }
    #advads-responsive-list tr { border-top: solid 1px #aaa; }
    #advads-responsive-list th { padding-right: 1em; min-width: 50px; text-align: left;}
    #advads-responsive-list td { height: 30px; border-left: solid 1px #aaa; }
    #advads-responsive-list td:first-of-type { border-left: solid 1px #aaa; }
    .advads-responsive-list-legend-visible,
    #advads-responsive-list .filled { background: #0074a2; }
    #advads-responsive-list .filled + .filled { border-left: none; }
    #advads-responsive-list td:not(.filled) + td:not(.filled) { border-left: dotted 1px #aaa; }
    .advads-responsive-list-legend { overflow: hidden; }
    .advads-responsive-list-legend dt { float: left; width: 30px; height: 30px; border: 1px solid; }
    .advads-responsive-list-legend dd { float: left; margin-left: 10px; margin-right: 30px; line-height: 30px; }
</style>