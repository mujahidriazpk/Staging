<style media="screen">
.pika-single.is-bound{
  z-index: 999999;
}
#TB_window{
  background-color: transparent;
}
.is-selected .pika-button{
  box-shadow: none;
}
</style>
<div class="shortcode-content">
    <div class="analytify-shortcode-edit-popup">
        <h3 class="popup-title"><?php esc_html_e( 'Select metrics and dimensions, sort them with metrics and show stats to your visitors (roles).', 'wp-analytify-pro' ); ?></h3>
        <div class="analytify-shortcode-edit-ui">
            <div class="analytify-shortcode-edit-ui-selector">
                <div id="response"></div>
                <div class="selector-item">
                    <label class="matrix-label" for="metrics"><?php esc_html_e( 'Metrics: ', 'wp-analytify-pro' ); ?></label>
                        <select name="metrics[]" data-placeholder="<?php esc_html_e( 'Select at least one Metric', 'wp-analytify-pro' ); ?>" id="metrics" multiple style="width:306px;">
                            <optgroup label="<?php esc_html_e( 'user', 'wp-analytify-pro' ); ?>">
                                <option value="ga:users"><?php esc_html_e( 'Users', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:newUsers"><?php analytify_e( 'New Users', 'wp-analytify' ); ?></option>
                                <option value="ga:percentNewSessions"><?php esc_html_e( 'Percent New Sessions', 'wp-analytify-pro' ); ?></option>
                            </optgroup>
                            <optgroup label="<?php esc_html_e( 'Session', 'wp-analytify-pro' ); ?>">
                                <option value="ga:sessions"><?php analytify_e( 'Sessions', 'wp-analytify' ); ?></option>
                                <option value="ga:bounces"><?php esc_html_e( 'Bounces', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:bounceRate"><?php analytify_e( 'Bounce Rate', 'wp-analytify' ); ?></option>
                                <option value="ga:sessionDuration"><?php esc_html_e( 'sessionDuration', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:avgSessionDuration"><?php esc_html_e( 'Avg SessionDuration', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:hits"><?php esc_html_e( 'Hits', 'wp-analytify-pro' ); ?></option>
                            </optgroup>
                            <optgroup label="<?php esc_html_e( 'Page Tracking', 'wp-analytify-pro' ); ?>">
                                <option value="ga:pageValue"><?php esc_html_e( 'Page Value', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:entrances"><?php esc_html_e( 'Entrances', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:pageviews"><?php esc_html_e( 'Pageviews', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:uniquePageviews"><?php esc_html_e( 'Unique Pageviews', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:exits"><?php esc_html_e( 'Exits', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:exitRate"><?php esc_html_e( 'Exit Rate', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:entranceRate"><?php esc_html_e( 'Entrance Rate', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:pageviewsPerSession"><?php esc_html_e( 'Pageviews Per Session', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:timeOnPage"><?php esc_html_e( 'Time On Page', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:avgTimeOnPage"><?php esc_html_e( 'Avg Time On Page', 'wp-analytify-pro' ); ?></option>
                            </optgroup>
                        </select>
                </div>
                <div class="selector-item">
                    <label class="matrix-label" for="dimensions"><?php esc_html_e( 'Dimensions: ', 'wp-analytify-pro' ); ?></label>
                        <select name="dimensions[]" data-placeholder="<?php esc_html_e( 'Select at least one Dimension', 'wp-analytify-pro' ); ?>" id="dimensions" multiple style="width:306px;">
                            <optgroup label="<?php esc_html_e( 'user', 'wp-analytify-pro' ); ?>">
                                <option value="ga:userType"><?php esc_html_e( 'User Type', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:sessionCount"><?php esc_html_e( 'Session Count', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:daysSinceLastSession"><?php esc_html_e( 'Days Since Last Session', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:sessionDurationBucket"><?php esc_html_e( 'Session Duration Bucket', 'wp-analytify-pro' ); ?></option>
                            </optgroup>
                            <optgroup label="<?php esc_html_e( 'Traffic Sources', 'wp-analytify-pro' ); ?>">
                                <option value="ga:referralPath"><?php esc_html_e( 'Referral Path', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:fullReferrer"><?php esc_html_e( 'Full Referrer', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:source"><?php esc_html_e( 'Source', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:medium"><?php esc_html_e( 'Medium', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:sourceMedium"><?php esc_html_e( 'Source Medium', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:keyword"><?php esc_html_e( 'Keyword', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:socialNetwork"><?php esc_html_e( 'Social Network', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:country"><?php esc_html_e( 'Country', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:countryIsoCode"><?php esc_html_e( 'Country ISO Code', 'wp-analytify-pro' ); ?></option>
                            </optgroup>
                            <optgroup label="<?php esc_html_e( 'Platform or Device', 'wp-analytify-pro' ); ?>">
                                <option value="ga:browser"><?php esc_html_e( 'Browser', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:browserVersion"><?php esc_html_e( 'Browser Version', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:operatingSystem"><?php esc_html_e( 'Operating System', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:operatingSystemVersion"><?php esc_html_e( 'Operating System Version', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:mobileDeviceBranding"><?php esc_html_e( 'Mobile Device Branding', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:mobileDeviceModel"><?php esc_html_e( 'Mobile Device Model', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:mobileInputSelector"><?php esc_html_e( 'Mobile Input Selector', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:mobileDeviceInfo"><?php esc_html_e( 'Mobile Device Info', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:mobileDeviceMarketingName"><?php esc_html_e( 'Mobile Device MarketingName', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:deviceCategory"><?php esc_html_e( 'Device Category', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:flashVersion"><?php esc_html_e( 'Flash Version', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:javaEnabled"><?php esc_html_e( 'Java Enabled', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:language"><?php esc_html_e( 'Language', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:screenColors"><?php esc_html_e( 'ScreenColors', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:screenResolution"><?php esc_html_e( 'ScreenResolution', 'wp-analytify-pro' ); ?></option>
                            </optgroup>
                            <optgroup label="<?php esc_html_e( 'Page Tracking', 'wp-analytify-pro' ); ?>">
                                <option value="ga:hostname"><?php esc_html_e( 'Hostname', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:pagePath"><?php esc_html_e( 'Page Path', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:pageTitle"><?php esc_html_e( 'Page Title', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:landingPagePath"><?php esc_html_e( 'Landing Page Path', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:secondPagePath"><?php esc_html_e( 'secondPagePath', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:exitPagePath"><?php esc_html_e( 'Exit Page Path', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:previousPagePath"><?php esc_html_e( 'Previous Page Path', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:nextPagePath"><?php esc_html_e( 'Next Page Path', 'wp-analytify-pro' ); ?></option>
                                <option value="ga:pageDepth"><?php esc_html_e( 'Page Depth', 'wp-analytify-pro' ); ?></option>
                            </optgroup>
                        </select>
                </div>
                <div class="selector-item">
                    <label for="permission_view"><?php esc_html_e( 'Visible to (roles) : ', 'wp-analytify-pro' ); ?></label>
                        <select name="permission_view" data-placeholder="Show Stats (roles wise)" id="permission_view" multiple style="width:306px">
                            <option value=""><?php esc_html_e( 'Everyone', 'wp-analytify-pro' ); ?></option>
                            <?php
                                if ( !isset( $wp_roles ) )
                                {

                                    $wp_roles = new WP_Roles();
                                }

                              foreach ( $wp_roles->role_names as $role => $name ) {

                            ?>
                                <option value="<?php echo $role; ?>"><?php esc_html_e(  $name, 'wp-analytify-pro' ); ?></option>
                            <?php } ?>
                        </select>
                </div>
                <div style="clear:both"></div>
                <div class="selector-item">
                    <label for="sort_by"><?php esc_html_e( 'Sort By: ', 'wp-analytify-pro' ); ?></label>
                        <select name="sort_by" id="sort_by">
                            <option value="-ga:sessions"><?php analytify_e( 'Sessions', 'wp-analytify' ); ?></option>
                            <option value="-ga:users"><?php analytify_e( 'Users', 'wp-analytify' ); ?></option>
                            <option value="-ga:newUsers"><?php analytify_e( 'New Users', 'wp-analytify' ); ?></option>
                            <option value="-ga:percentNewSessions"><?php esc_html_e( 'Percent New Sessions', 'wp-analytify-pro' ); ?></option>
                            <option value="-ga:bounces"><?php esc_html_e( 'Bounces', 'wp-analytify-pro' ); ?></option>
                            <option value="-ga:bounceRate"><?php esc_html_e( 'BounceRate', 'wp-analytify-pro' ); ?></option>
                            <option value="-ga:hits"><?php esc_html_e( 'Hits', 'wp-analytify-pro' ); ?></option>
                            <option value="-ga:pageValue"><?php esc_html_e( 'Page Value', 'wp-analytify-pro' ); ?></option>
                            <option value="-ga:entrances"><?php esc_html_e( 'Entrances', 'wp-analytify-pro' ); ?></option>
                            <option value="-ga:pageviews"><?php esc_html_e( 'Pageviews', 'wp-analytify-pro' ); ?></option>
                            <option value="-ga:uniquePageviews"><?php esc_html_e( 'Unique Pageviews', 'wp-analytify-pro' ); ?></option>
                            <option value="-ga:exits"><?php esc_html_e( 'Exits', 'wp-analytify-pro' ); ?></option>
                            <option value="-ga:entranceRate"><?php esc_html_e( 'EntranceRate', 'wp-analytify-pro' ); ?></option>
                            <option value="-ga:pageviewsPerSession"><?php esc_html_e( 'Pageviews Per Session', 'wp-analytify-pro' ); ?></option>
                            <option value="-ga:entranceRate"><?php esc_html_e( 'Entrance Rate', 'wp-analytify-pro' ); ?></option>
                            <option value="-ga:timeOnPage"><?php esc_html_e( 'Time On Page', 'wp-analytify-pro' ); ?></option>
                        </select>
                </div>
                <div class="selector-item">
                    <label for="analytics_for"><?php esc_html_e( 'Analytics for: ', 'wp-analytify-pro' ); ?></label>
                        <select name="analytics_for" id="analytics_for">
                            <option value="current" selected="selected"><?php esc_html_e( 'Current page', 'wp-analytify-pro' ); ?></option>
                            <option value="full"><?php esc_html_e( 'Full Site', 'wp-analytify-pro' ); ?></option>
                            <option value="page_id"><?php esc_html_e( 'Page ID', 'wp-analytify-pro' ); ?></option>
                        </select>
                </div>
                <div style="clear:both"></div>
                <div class="selector-item custom_page_id_stats" style="display: none;">
                    <label for="custom_page_id_stats"><?php esc_html_e( 'Page ID: ', 'wp-analytify-pro' ); ?></label>
                    <input type="number" value="" placeholder="Enter Page ID of the other page" id="custom_page_id_stats" />
                </div>
                <div class="selector-item">
                    <label for="date_range"><?php esc_html_e( 'Date range: ', 'wp-analytify-pro' ); ?></label>
                    <select name="analytify_date_type" id="analytify_date_type">
                            <option value="custom"><?php esc_html_e( 'Custom', 'wp-analytify-pro' ); ?></option>
                            <option value="today"><?php esc_html_e( 'Today', 'wp-analytify-pro' ); ?></option>
                            <option value="- 1 days"><?php esc_html_e( 'Yesterday', 'wp-analytify-pro' ); ?></option>
                            <option value="- 15 days"><?php esc_html_e( 'Last 15 days', 'wp-analytify-pro' ); ?></option>
                            <option value="- 7 days"><?php esc_html_e( 'Last 7 Days', 'wp-analytify-pro' ); ?></option>
                            <option value="- 30 days"><?php esc_html_e( 'Last 30 Days', 'wp-analytify-pro' ); ?></option>
                            <option value="year-to-date"><?php esc_html_e( 'This Year', 'wp-analytify-pro' ); ?></option>
                            <option value="- 365 days"><?php esc_html_e( 'Last Year', 'wp-analytify-pro' ); ?></option>
                    </select>
                </div>

                <div class="selector-item analytify_start_date">
                    <label for="start_date"><?php esc_html_e( 'Start Date: ', 'wp-analytify-pro' ); ?></label>
                    <input type="text" id="analytify_start_date" />
                </div>
                <div class="selector-item analytify_end_date">
                    <label for="analytify_end_date"><?php esc_html_e( 'End Date: ', 'wp-analytify-pro' ); ?></label>
                    <input type="text" id="analytify_end_date" />
                </div>
                <div style="clear:both"></div>
                <div class="selector-item">
                    <label for="analytify_max_result"><?php esc_html_e( 'Max records: ', 'wp-analytify-pro' ); ?></label>
                    <input type="number" value="5" id="analytify_max_result" />
                </div>
                <div style="clear:both"></div>
                <div class="selector-item right-side">
                    <button id="insert_stats" class="button-primary" style="padding:0 18px;"><?php esc_html_e( 'Ok', 'wp-analytify-pro' ); ?></button>
                    <button id="cancel" class="button"><?php esc_html_e( 'Cancel', 'wp-analytify-pro' ); ?></button>
                </div>
            </div>
        </div>

    </div>

</div>

<script type="text/javascript">

    jQuery(function($) {

        $('#analytify_date_type').on( "change", function(){

            if($(this).val() != 'custom') {
                $('.analytify_start_date, .analytify_end_date').hide();
            }else{
                $('.analytify_start_date, .analytify_end_date').show();
            }
        });

        $('#analytics_for').on( "change", function(){
            if($(this).val() != 'page_id') {
                $('.custom_page_id_stats').hide();
            }else{
                $('.custom_page_id_stats').show();
            }
        });

    });

    jQuery( '#metrics' ).chosen({max_selected_options: 10});
    jQuery( '#dimensions' ).chosen({max_selected_options: 7});
    jQuery( '#sort_by,#permission_view' ).chosen();


    new Pikaday({
      field: document.getElementById('analytify_start_date'),
      defaultDate: moment().toDate(),
      setDefaultDate: true,
    });

    new Pikaday({
      field: document.getElementById('analytify_end_date'),
      defaultDate: moment().toDate(),
      setDefaultDate: true,
    });

    jQuery( '#cancel' ).click( function(){

        tb_remove();
    });

    jQuery( '#insert_stats' ).click( function(){

        var all_metrics,all_dimensions,dimensions,start_date,end_date,
            max_result,sort,shorcode,permission_view,metrics,analytics_for,custom_page_id;

        metrics         =  jQuery( '#metrics' ).val();
        dimensions      =  jQuery( '#dimensions' ).val();
        date_type       =  jQuery( '#analytify_date_type' ).val();
        start_date      =  jQuery( '#analytify_start_date') .val();
        end_date        =  jQuery( '#analytify_end_date' ).val();
        max_result      =  jQuery( '#analytify_max_result' ).val();
        sort            =  jQuery( '#sort_by' ).val();
        analytics_for   =  jQuery( '#analytics_for' ).val();
        custom_page_id  =  jQuery( '#custom_page_id_stats' ).val();
        permission_view =  jQuery( '#permission_view' ).val();

        if( metrics == null ){
            jQuery( '#response' ).html( '<div id="error" class="error below-h2" style="clear: both;">At least one Metric is required.</div>' );
        }
        else{
            all_metrics = metrics.join();
        }

        if( dimensions == null ){
            jQuery( '#response' ).html( '<div id="error" class="error below-h2" style="clear: both;">At least one Dimension is required.</div>' );

        }else{
            all_dimensions = dimensions.join();
        }

        if( permission_view == null ){
            permission_view = '';
        }

        if( dimensions != null && permission_view != null ){
             all_dimensions = dimensions.join();

            if( date_type == 'custom'){
                shortcode = '[analytify-stats metrics="'+all_metrics+'" dimensions="'+all_dimensions+'" date_type="'+date_type+'" start_date="'+start_date+'" end_date="'+end_date+'" max_results="'+max_result+'" sort="'+sort+'" analytics_for="'+analytics_for+'" custom_page_id = "' + custom_page_id + '" permission_view="'+permission_view+'"]';

            }
            else{
                shortcode = '[analytify-stats metrics="'+all_metrics+'" dimensions="'+all_dimensions+'" date_type="'+date_type+'" max_results="'+max_result+'" sort="'+sort+'" analytics_for="'+analytics_for+'" custom_page_id = "' + custom_page_id + '" permission_view="'+permission_view+'"]';
            }
        }
        else{
            shortcode= '[analytify-stats metrics="'+all_metrics+'" dimensions="" start_date="'+start_date+'" end_date="'+end_date+'" max_results="'+max_result+'" sort="'+sort+'" analytics_for="'+analytics_for+'" permission_view=""]';
        }

        if( metrics != null && dimensions != null ){
            tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, shortcode );
            tb_remove();
        }
    });
</script>
