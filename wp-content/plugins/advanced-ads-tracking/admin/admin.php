<?php

class Advanced_Ads_Tracking_Admin {

    const PLUGIN_LINK = 'https://wpadvancedads.com/add-ons/tracking/';
    const PUBLIC_STATS_DEFAULT = 'ad-stats';
    private $settings_page_hook = 'advanced-ads-tracking-settings-page';
    private $settings_page_id = 'advanced-ads_page_advanced-ads-settings';
    private $stat_page_hook;
    private $ajax_nonce;
	private $db_op_page_slug = 'advads-tracking-db-page';

    /**
     * @var Advanced_Ads_Tracking_Plugin
     */
    protected $plugin;

    /**
     *
     * @var Advanced_Ads_Tracking_Util
     */
    protected $util;

    /**
     * Initialize the plugin by loading admin scripts & styles and adding a
     * settings page and menu.
     *
     * @since     1.0.0
     */
	public function __construct() {
		$this->plugin = Advanced_Ads_Tracking_Plugin::get_instance();

		$this->ajax_nonce = wp_create_nonce( 'advads-tracking-public-stats' );

		if( ! class_exists( 'Advanced_Ads_Admin', false ) ) {
			// show admin notice
			add_action( 'admin_notices', array( $this, 'missing_plugin_notice' ) );

			return;
		}

		// print scripts in admin page
		add_action( 'admin_print_scripts', array( $this, 'admin_print_scripts' ) );

		// add styles
		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
		// add add-on settings to plugin settings page
		add_action('advanced-ads-settings-init', array($this, 'settings_init'), 10, 1);
		add_filter('advanced-ads-setting-tabs', array($this, 'setting_tabs'));
		// ad menu item
		add_action('advanced-ads-submenu-pages', array($this, 'add_menu_item'));
		// add stats page to array of pages that belong to Advanced Ads
		add_action( 'advanced-ads-dashboard-screens', array($this, 'add_menu_page_to_array'));
		// add setting whether to track or not to track this ad
		add_action('advanced-ads-ad-params-after', array($this, 'render_ad_tracking_options'), 10, 2);
		// add our new options using the options filter before saving
		add_filter('advanced-ads-save-options', array($this, 'save_options'), 10, 2);
		// add metabox
		add_action('admin_init', array($this, 'add_meta_box'));
		// show ad specific notices
		add_filter( 'advanced-ads-ad-notices', array($this, 'ad_notices'), 10, 3 );
		
		$this->check_cron_sched();

        $options = $this->plugin->options();

		// Check tables only when dbversion changed
		if ( !isset( $options['dbversion'] ) || $options['dbversion'] != Advanced_Ads_Tracking_Util::DB_VERSION ) {
			$this->check_tables();
        }
		
		// add message to support page
		add_filter( 'advanced-ads-support-messages', array( $this, 'support_message' ) );

		// add the stats column into custom columns white list
		add_filter( 'advanced-ads-ad-list-allowed-columns', array( $this, 'column_white_list' ) );

		// add custom column
		add_filter( 'manage_advanced_ads_posts_columns', array( $this, 'add_column' ) );

		// stats columns in ads list
		add_filter( 'manage_advanced_ads_posts_custom_column', array($this, 'ad_list_columns_content'), 10, 2 );

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		
		add_action( 'wp_loaded', array( $this, 'wp_loaded' ) );
		
		add_action( 'dp_duplicate_post', array( $this, 'on_duplicate_post' ), 20, 2 );
	}

	/**
	 *  Recreate public stats link on post duplication
	 */
	public function on_duplicate_post( $new_id, $post ) {
		$meta = get_post_meta( $new_id, 'advanced_ads_ad_options', true );
		if ( is_array( $meta ) && isset( $meta['tracking'] ) && isset( $meta['tracking']['public-id'] ) ) {
			$meta['tracking']['public-id'] = wp_generate_password( 48, false );
			update_post_meta( $new_id, 'advanced_ads_ad_options', $meta );
		}
	}
	
	/**
	 *  tasks to run when WP is fully loaded
	 */
	public function wp_loaded() {
		/**
		 *  create tracking tables
		 */
		if (
			isset( $_GET['action'] ) &&
			'create_track_tables' == $_GET['action'] &&
			isset( $_GET['nonce'] ) &&
			false !== wp_verify_nonce( $_GET['nonce'], 'advads-stats-page' )
		) {
			self::create_tables();
			$options = $this->plugin->options();
			$options['has-tables'] = true;
			$this->plugin->update_options( $options );
		}
	}
	
	/**
	 *  check for the presence of tracking tables and set an options if they are missing
	 *  
	 *  @return [bool]
	 */
	public function has_tables() {
		$options = $this->plugin->options();
		if ( isset( $options['has-tables'] ) ) {
			return $options['has-tables'];
		} else {
			global $wpdb;
			$util = Advanced_Ads_Tracking_Util::get_instance();
			$table = $util->get_impression_table();
			$query = "SELECT * FROM $table WHERE 1 LIMIT 1";
			$res = @$wpdb->query( $query );
			if ( false === $res ) {
				$options['has-tables'] = false;
				$this->plugin->update_options( $options );
				return false;
			} else {
				$options['has-tables'] = true;
				$this->plugin->update_options( $options );
				return true;
			}
		}
	}
	
	public function admin_menu(){
		$this->db_op_page_hook = add_submenu_page(
			'options.php',
			__( 'Tracking database', 'advanced-ads-tracking' ),
			null,
			advanced_ads_tracking_db_cap(),
			$this->db_op_page_slug,
			array( $this, 'db_operation_page_cb' )
		);
	}

	public function db_operation_page_cb() {
		include_once AAT_BASE_PATH . 'admin/views/db-operations.php';
	}

    /**
     *  admin print scripts
     */
    public function admin_print_scripts() {
        global $pagenow;
        /**
         *  if on the ad lists page
         */
        if ( 'edit.php' == $pagenow && isset( $_GET['post_type'] ) && 'advanced_ads' == $_GET['post_type'] ) {
            // target url can be long (very  very long). So display it in a tooltip like box
            ?>
            <style type="text/css">
                .target-link-div {
                    display: inline;
                }
                .target-link-div .target-link-text {
                    display:none;
                    position: absolute;
                    background-color: #fff;
                    border: 1px solid #d6d6d6;
                    padding: 0.5em;
                    max-width: 14%;
                }
                .target-link-div:hover .target-link-text {
                    display: block;
                }
            </style>
            <?php
        }
        if ( 'admin.php' == $pagenow && isset( $_GET['page'] ) && 'advanced-ads-stats' == $_GET['page'] ) {
            $gmt_offset = 3600 * 1000 * floatval( get_option( 'gmt_offset' ) );
            ?><script type="text/javascript">
            /* <![CDATA[ */
            var WPGmtOffset = <?php echo $gmt_offset; ?>;
            var _dataTableLang = {
                processing: '<?php esc_attr_e( 'processing...', 'advanced-ads-tracking' ); ?>',
                search: '<?php esc_attr_e( 'search:', 'advanced-ads-tracking' ); ?>',
                lengthMenu: '<?php esc_attr_e( 'show _MENU_ entries', 'advanced-ads-tracking' ); ?>',
                info: '<?php esc_attr_e( 'showing _START_ to _END_ of _TOTAL_ entries', 'advanced-ads-tracking' ); ?>',
                infoEmpty: '<?php esc_attr_e( 'no element to show', 'advanced-ads-tracking' ); ?>',
                infoFiltered: '<?php esc_attr_e( 'filtered from _MAX_ total entries', 'advanced-ads-tracking' ); ?>',
                infoPostFix: '',
                loadingRecords: '<?php esc_attr_e( 'Loading...', 'advanced-ads-tracking' ); ?>',
                zeroRecords: '<?php esc_attr_e( 'no matching records found', 'advanced-ads-tracking' ); ?>',
                emptyTable: '<?php esc_attr_e( 'no data available in table', 'advanced-ads-tracking' ); ?>',
                paginate: {
                    first: '<?php esc_attr_e( 'first', 'advanced-ads-tracking' ); ?>',
                    previous: '<?php esc_attr_e( 'previous', 'advanced-ads-tracking' ); ?>',
                    next: '<?php esc_attr_e( 'next', 'advanced-ads-tracking' ); ?>',
                    last: '<?php esc_attr_e( 'last', 'advanced-ads-tracking' ); ?>'
                },
                aria: {
                    sortAscending:  '<?php esc_attr_e( ': activate to sort column ascending', 'advanced-ads-tracking' ); ?>',
                    sortDescending: '<?php esc_attr_e( ': activate to sort column descending', 'advanced-ads-tracking' ); ?>'
                }
            };
            var _dateName = {
                shortMonths: [
                    '<?php advads_e( 'Jan' ); ?>',
                    '<?php advads_e( 'Feb' ); ?>',
                    '<?php advads_e( 'Mar' ); ?>',
                    '<?php advads_e( 'Apr' ); ?>',
                    '<?php echo advads_x( 'May', 'May abbreviation' ); ?>',
                    '<?php advads_e( 'Jun' ); ?>',
                    '<?php advads_e( 'Jul' ); ?>',
                    '<?php advads_e( 'Aug' ); ?>',
                    '<?php advads_e( 'Sep' ); ?>',
                    '<?php advads_e( 'Oct' ); ?>',
                    '<?php advads_e( 'Nov' ); ?>',
                    '<?php advads_e( 'Dec' ); ?>'
                ],
                longMonths: [
                    '<?php advads_e( 'January' ); ?>',
                    '<?php advads_e( 'February' ); ?>',
                    '<?php advads_e( 'March' ); ?>',
                    '<?php advads_e( 'April' ); ?>',
                    '<?php advads_e( 'May' ); ?>',
                    '<?php advads_e( 'June' ); ?>',
                    '<?php advads_e( 'July' ); ?>',
                    '<?php advads_e( 'August' ); ?>',
                    '<?php advads_e( 'September' ); ?>',
                    '<?php advads_e( 'October' ); ?>',
                    '<?php advads_e( 'November' ); ?>',
                    '<?php advads_e( 'December' ); ?>'
                ],
                shortDays: [
                    '<?php advads_e( 'Sun' ); ?>',
                    '<?php advads_e( 'Mon' ); ?>',
                    '<?php advads_e( 'Tue' ); ?>',
                    '<?php advads_e( 'Wed' ); ?>',
                    '<?php advads_e( 'Thu' ); ?>',
                    '<?php advads_e( 'Fri' ); ?>',
                    '<?php advads_e( 'Sat' ); ?>',
                ],
                longDays: [
                    '<?php advads_e( 'Sunday' ); ?>',
                    '<?php advads_e( 'Monday' ); ?>',
                    '<?php advads_e( 'Tuesday' ); ?>',
                    '<?php advads_e( 'Wednesday' ); ?>',
                    '<?php advads_e( 'Thursday' ); ?>',
                    '<?php advads_e( 'Friday' ); ?>',
                    '<?php advads_e( 'Saturday' ); ?>',
                ],
            };
            var adminUrl = '<?php echo admin_url(); ?>';
            var wpDateFormat = '<?php echo str_replace( '\\', '\\\\', get_option( 'date_format', 'Y/m/d' ) ); ?>';
            var wpDateTimeZoneName = '<?php echo Advanced_Ads_Admin::timezone_get_name( Advanced_Ads_Admin::get_wp_timezone() );?>';
            /* ]]> */
            </script><?php
        }
    }

    /**
     *  get the url to the admin stats for the last 30 days for a given ad ID
     */
    public static function admin_30days_stats_url( $id ) {
        $id = absint( $id );
        $stat_url = 'page=advanced-ads-stats&advads-stats[period]=custom&advads-stats[groupby]=day&advads-stats[ads]=all-ads';
        $today = time();
        $_30_days_ago = time() - ( 29 * 24 * 60 * 60 );
		$wptz = Advanced_Ads_Admin::get_wp_timezone();
        $stat_from = date_create( '@' . $_30_days_ago, $wptz );
        $stat_to = date_create( '@' . $today, $wptz );
        $stat_url .= '&advads-stats[from]=' . $stat_from->format( 'm/d/Y' );
        $stat_url .= '&advads-stats[to]=' . $stat_to->format( 'm/d/Y' );
        $stat_url .= '&advads-stats-filter[]=' . $id;
        return admin_url( 'admin.php?' . $stat_url );
    }

    /**
     *  add custom column
     */
    public function add_column( $columns ) {
        $columns['ad_stats'] = esc_attr__( 'Statistics', 'advanced-ads-tracking' );
        return $columns;
    }

    /**
     *  filter the column white list
     */
    public function column_white_list( $list ) {
        $list[] = 'ad_stats';
        return $list;
    }

    /**
     *  draw the content of stat column in ads list
     */
    public function ad_list_columns_content( $column_name, $ad_id ) {
        if ( 'ad_stats' == $column_name ) {
            include AAT_BASE_PATH . 'admin/views/ad-list-stats-column.php';
        }
    }

    /**
     *  add message to support page if using default permalink structure
     */
    public function support_message( $messages ) {
        $perm = get_option( 'permalink_structure', false );
        if ( ! $perm ) {
            $perm_link = admin_url( 'options-permalink.php' );
            $messages[] = sprintf( __( 'You must set the <a href="%s">permalink structure</a> to anything else than the default one to get the tracking of clicks to work', 'advanced-ads-tracking' ), $perm_link );
        }
        return $messages;
    }

    /**
     *  check all CRON jobs
     */
    public function check_cron_sched() {
        $options = $this->plugin->options();

        $now = time();
        $TZ = Advanced_Ads_Tracking::get_wp_timezone();
		
        $now += ( 24 * 60 * 60 );
        $date = date_create( '@' . $now );
		
		$local_now = date_create( 'now', $TZ );
		$offset = $local_now->getOffset();
		
        // next day at 00:15 AM UTC
        $_00h15 = date_create( $date->format( "Y-m-dT00:15:00" ), new DateTimeZone( 'UTC' ) );
		
		// add GMT offset
		$_00h15 = intval( $_00h15->format( 'U' ) ) - $offset;
		
		/**
		 *  schedule for admin report
		 */
        $admin_recip = isset( $options['email-addresses'] )? $options['email-addresses'] : '';
        $admin_schedule = wp_get_schedule( 'advanced_ads_daily_email' );
		
        if ( empty( $admin_recip ) ) {
            // no recipient, remove CRON job
            if ( false !== $admin_schedule ) {
				wp_clear_scheduled_hook( 'advanced_ads_daily_email' );
            }
        } else {
            // no CRON job yet but admin email recipient is set, append the job
            if ( false === $admin_schedule ) {
				wp_schedule_event( $_00h15, 'daily', 'advanced_ads_daily_email' );
            }
        }
		
		/**
		 *  individual ad report
		 */
		$individual_ad_schedule = wp_get_schedule( 'advanced_ads_daily_report' );
		
		if ( false === $individual_ad_schedule ) {
			wp_schedule_event( $_00h15, 'daily', 'advanced_ads_daily_report' );
		}
		
		/**
		 * 	automatic data compression
		 */
		$compression_schedule = wp_get_schedule( 'advanced_ads_auto_comp' );
		if ( false === $compression_schedule ) {
			wp_schedule_event( ( time() + 1800 ), 'daily', 'advanced_ads_auto_comp' );
		}
    }

    /**
     * @return Advanced_Ads_Tracking_Util
     */
    public function get_util() {
	    if ($this->util === null) {
	    $this->util = Advanced_Ads_Tracking_Util::get_instance();
	}

	return $this->util;
    }

    /**
    * show warning if Advanced Ads js is not activated
    */
    public function missing_plugin_notice(){
	    echo '<div class="error"><p>' . sprintf( __( '<strong>Advanced Ads – Tracking</strong> is an extension for the Advanced Ads plugin. Please visit <a href="%s" target="_blank" >wpadvancedads.com</a> to download it for free.', 'advanced-ads-tracking' ), 'https://wpadvancedads.com' ) . '</p></div>';
    }

    /**
     * Register and enqueue admin-specific scripts and stylesheets.
     *
     * @since 1.0.0
     */
    public function enqueue_admin_scripts( $hook ) {

        $screen = get_current_screen();
        // ad edit screen
        if ( Advanced_Ads::POST_TYPE_SLUG == $screen->id ) {
			
            // jplot files
            wp_enqueue_script('jplot-js', plugins_url('assets/jqplot/jquery.jqplot.min.js', __FILE__), array('jquery'), AAT_VERSION);
            wp_enqueue_script('jplot-date-js', plugins_url('assets/jqplot/plugins/jqplot.dateAxisRenderer.min.js', __FILE__), array('jplot-js'), AAT_VERSION);
            wp_enqueue_script('jplot-highlighter-js', plugins_url('assets/jqplot/plugins/jqplot.highlighter.min.js', __FILE__), array('jplot-js'), AAT_VERSION);
            wp_enqueue_script('jplot-cursor-js', plugins_url('assets/jqplot/plugins/jqplot.cursor.min.js', __FILE__), array('jplot-js'), AAT_VERSION);
            wp_enqueue_style('jplot-css', plugins_url('assets/jqplot/jquery.jqplot.min.css', __FILE__), AAT_VERSION);
			
			wp_register_script(AAT_SLUG . '-admin-scripts', plugins_url('assets/js/script.js', __FILE__), array('jquery','jplot-cursor-js'), AAT_VERSION);
			
			$inline_script = 'var advads_tracking_clickable_ad_types = ' . wp_json_encode( Advanced_Ads_Tracking_Plugin::$types_using_click_tracking ) . ';';
			wp_add_inline_script( AAT_SLUG . '-admin-scripts', $inline_script, 'before' );
			
			$trackingStatsLocale = array(
				'impressions' => __( 'impressions', 'advanced-ads-tracking' ),
				'clicks' => __( 'clicks', 'advanced-ads-tracking' ),
			);
			wp_localize_script( AAT_SLUG . '-admin-scripts', 'advadsStatsLocale', $trackingStatsLocale );
			wp_enqueue_script(AAT_SLUG . '-admin-scripts');
        }

        // admin stats page
        if ( $this->stat_page_hook == $hook ) {
			wp_enqueue_media();
            wp_enqueue_style(AAT_SLUG . '-admin-styles', plugins_url('assets/css/admin.css', __FILE__), array(), AAT_VERSION);
            // add date picker from WP core
            wp_enqueue_script('jquery-ui-datepicker', null, array(), 0, true);
			
            // jplot files
            wp_enqueue_script('jplot-js', plugins_url('assets/jqplot/jquery.jqplot.min.js', __FILE__), array('jquery'), 0, true);
            wp_enqueue_script('jplot-date-js', plugins_url('assets/jqplot/plugins/jqplot.dateAxisRenderer.min.js', __FILE__), array('jplot-js'), 0, true);
            wp_enqueue_script('jplot-highlighter-js', plugins_url('assets/jqplot/plugins/jqplot.highlighter.min.js', __FILE__), array('jplot-js'), 0, true);
            wp_enqueue_script('jplot-cursor-js', plugins_url('assets/jqplot/plugins/jqplot.cursor.min.js', __FILE__), array('jplot-js'), 0, true);
            wp_enqueue_style('jplot-css', plugins_url('assets/jqplot/jquery.jqplot.min.css', __FILE__), null, 0);
            wp_enqueue_style('dtable', AAT_BASE_URL . 'admin/assets/datatables/css/datatables.min.css', array(), 0);

            wp_enqueue_script( 'dtable', AAT_BASE_URL . 'admin/assets/datatables/js/datatables.min.js', array('jquery'), null, true);
            wp_enqueue_script( 'date-format', AAT_BASE_URL . 'admin/assets/date.format/date.format.min.js', array('jquery'), null, true);

			wp_register_script( 'advads-media-frame', AAT_BASE_URL .'admin/assets/js/wp-media-frame.js', array( 'jquery' ), null, true );
			$media_locale = array(
				'selectFile' => esc_attr__( 'Select file', 'advanced-ads-tracking' ),
				'button' => advads__( 'select' ),
				'invalidFileType' => esc_attr__( 'invalid file type', 'advanced-ads-tracking' ),
			);
			wp_localize_script( 'advads-media-frame', 'advadsMediaFrameLocale', $media_locale );
			wp_enqueue_script( 'advads-media-frame' );

			wp_register_script( 'advads-stats', AAT_BASE_URL . 'admin/assets/js/stats.js', array( 'jplot-cursor-js', 'dtable', 'jquery-ui-autocomplete', 'date-format', 'advads-media-frame' ), null, true);
            $stats_translations = array(
                'statsPerDate' => esc_attr__( 'Stats per date', 'advanced-ads-tracking' ),
                'statsPerAd' => esc_attr__( 'Stats per ad', 'advanced-ads-tracking' ),
                'clicks' => esc_attr__( 'clicks', 'advanced-ads-tracking' ),
                'clicksFor' => esc_attr__( 'clicks for "%s"', 'advanced-ads-tracking' ),
                'Clicks' => esc_attr__( 'Clicks', 'advanced-ads-tracking' ),
                'impressions' => esc_attr__( 'impressions', 'advanced-ads-tracking' ),
                'impressionsFor' => esc_attr__( 'impressions for "%s"', 'advanced-ads-tracking' ),
                'Impressions' => esc_attr__( 'Impressions', 'advanced-ads-tracking' ),
                'prevDay' => esc_attr__( 'previous day', 'advanced-ads-tracking' ),
                'nextDay' => esc_attr__( 'next day', 'advanced-ads-tracking' ),
                'prevMonth' => esc_attr__( 'previous month', 'advanced-ads-tracking' ),
                'nextMonth' => esc_attr__( 'next month', 'advanced-ads-tracking' ),
                'prevYear' => esc_attr__( 'previous year', 'advanced-ads-tracking' ),
                'nextYear' => esc_attr__( 'next year', 'advanced-ads-tracking' ),
                'prev%dDays' => esc_attr__( 'previous %d days', 'advanced-ads-tracking' ),
                'next%dDays' => esc_attr__( 'next %d days', 'advanced-ads-tracking' ),
                'clicksFromTo' => esc_attr__( 'clicks from %1$s to %2$s', 'advanced-ads-tracking' ),
                'imprFromTo' => esc_attr__( 'impressions from %1$s to %2$s', 'advanced-ads-tracking' ),
                'noDataFor' => esc_attr__( 'There is no data for %1$s to %2$s', 'advanced-ads-tracking' ),
                'ad' => esc_attr__( 'ad', 'advanced-ads-tracking' ),
                'ctr' => esc_attr__( 'ctr', 'advanced-ads-tracking' ),
                'deletedAds' => esc_attr__( 'deleted ads', 'advanced-ads-tracking' ),
                'date' => esc_attr__( 'date', 'advanced-ads-tracking' ),
                'aTob' => esc_attr__( '%1$s to %2$s', 'advanced-ads-tracking' ),
                'total' => esc_attr__( 'total', 'advanced-ads-tracking' ),
                'noRecords' => esc_attr__( 'There is no record for this period :(', 'advanced-ads-tracking' ),
                'periodNotConsistent' => esc_attr__( 'The period you have chosen is not consistent', 'advanced-ads-tracking' ),
                'customPeriodMissing' => esc_attr__( 'Some fields are missing for the custom period', 'advanced-ads-tracking' ),
				'invalidRecord' => esc_attr__( 'One or more invalid records have been found in the database', 'advanced-ads-tracking' ),
				'noFile' => esc_attr__( 'no file selected', 'advanced-ads-tracking' ),
				'group' => esc_attr__( 'group', 'advanced-ads-tracking' ),
            );
            wp_localize_script( 'advads-stats', 'statsLocale', $stats_translations );
            wp_enqueue_script( 'advads-stats' );
            wp_enqueue_script( AAT_SLUG . '-period', AAT_BASE_URL . 'admin/assets/js/period-select.js', array( 'jquery', 'jquery-ui-datepicker' ), null, true );
			wp_register_script( 'advads-stats-file', AAT_BASE_URL . 'admin/assets/js/stats-from-file.js', array( 'advads-stats', 'advads-media-frame', AAT_SLUG . '-period' ), null, true );
			$stats_file_locale = array(
				'unknownError' => esc_attr__( 'An unexpected error occurred.', 'advanced-ads-tracking' ),
				'statsFrom' => esc_attr__( 'stats from %1$s to %2$s', 'advanced-ads-tracking' ),
                'periodNotConsistent' => esc_attr__( 'The period you have chosen is not consistent', 'advanced-ads-tracking' ),
				'statsNotFoundInFile' => __( 'No stats found in file', 'advanced-ads-tracking' ),
                'prev%dDays' => esc_attr__( 'previous %d days', 'advanced-ads-tracking' ),
                'next%dDays' => esc_attr__( 'next %d days', 'advanced-ads-tracking' ),
                'prevMonth' => esc_attr__( 'previous month', 'advanced-ads-tracking' ),
                'nextMonth' => esc_attr__( 'next month', 'advanced-ads-tracking' ),
			);wp_localize_script( 'advads-stats-file', 'statsFileLocale', $stats_file_locale );
			wp_enqueue_script( 'advads-stats-file' );
        }

        // settings page
        if ( $screen->id == $this->settings_page_id ) {
            wp_register_script( AAT_SLUG . 'settings', AAT_BASE_URL . 'admin/assets/js/settings.js', array( 'jquery' ), null, true );
            $tracking_locale = array(
                'serverFail' => esc_attr__( 'The server failed to respond to your request. Link structure not available.', 'advanced-ads-tracking' ),
                'unknownError' => esc_attr__( 'An unexpected error occurred. Link structure not available.', 'advanced-ads-tracking' ),
                'linkAvailable' => esc_attr__( 'Link structure available.', 'advanced-ads-tracking' ),
                'emailSent' => esc_attr__( 'email sent', 'advanced-ads-tracking' ),
                'emailNotSent' => esc_attr__( 'email not sent. Please check your server configuration', 'advanced-ads-tracking' ),
            );
            wp_localize_script( AAT_SLUG . 'settings', 'trackingSettingsLocale', $tracking_locale );
            wp_enqueue_script( AAT_SLUG . 'settings' );
        }

		// db operations page
		if ( current_user_can( advanced_ads_tracking_db_cap() ) && isset( $_GET['page'] ) && 'advads-tracking-db-page' == $_GET['page'] ) {
            wp_enqueue_script( AAT_SLUG . '-period', AAT_BASE_URL . 'admin/assets/js/period-select.js', array( 'jquery', 'jquery-ui-datepicker' ), null, true );
            wp_register_script( AAT_SLUG . 'dbop', AAT_BASE_URL . 'admin/assets/js/db-operations.js', array( AAT_SLUG . '-period' ), null, true );
            $dbop_locale = array(
                'serverFail' => esc_attr__( 'The server failed to respond to your request.', 'advanced-ads-tracking' ),
                'unknownError' => esc_attr__( 'An unexpected error occurred.', 'advanced-ads-tracking' ),
                'resetNoAd' => esc_attr__( 'Please choose an ad', 'advanced-ads-tracking' ),
                'resetConfirm' => esc_attr__( 'Are you sure you want to reset the stats for', 'advanced-ads-tracking' ),
				'SQLFailure' => esc_attr__( 'The plugin was not able to perform some requests on the database', 'advanced-ads-tracking' ),
				'optimizeFailure' => esc_attr__( 'Data were compressed but the tracking tables can not be optimized automatically. Please ask the server&#39;s admin on how to proceed.', 'advanced-ads-tracking' ),
            );
            wp_localize_script( AAT_SLUG . 'dbop', 'trackingDbopLocale', $dbop_locale );
            wp_enqueue_script( AAT_SLUG . 'dbop' );

			wp_enqueue_style( 'advads-jquery-ui', ADVADS_BASE_URL . 'admin/assets/jquery-ui/jquery-ui.min.css' );
		}
    }
    
	/**
	 * add meta box for stata
	 *
	 * @since 1.2.6
	 */
	public function add_meta_box() {
		add_meta_box(
			'tracking-ads-box', esc_attr__( 'Stats', 'advanced-ads-tracking' ), array( $this, 'render_metabox' ), Advanced_Ads::POST_TYPE_SLUG, 'normal', 'low'
		);
	}

	/**
	 * render options for tracking meta box
	 *
	 * @since 1.2.6
	 */
    public function render_metabox(){
		global $post;
		$ad = new Advanced_Ads_Ad($post->ID);
		$options = $ad->options();

		$ad_options = isset( $options['tracking'] ) ? $options['tracking'] : array();
		
		 // set options
		$impression_limit = isset($ad_options['impression_limit']) ? absint($ad_options['impression_limit']) : '';
		$click_limit = isset($ad_options['click_limit']) ? absint($ad_options['click_limit']) : '';
		$sums = Advanced_Ads_Tracking_Util::get_instance()->get_sums();

		// public stats
		$tracking_options = Advanced_Ads_Tracking_Plugin::get_instance()->options();
		$public_stats_slug = ( isset( $tracking_options['public-stats-slug'] ) )? $tracking_options['public-stats-slug'] : self::PUBLIC_STATS_DEFAULT;
		$public_id = ( isset( $ad_options['public-id'] ) && ! empty( $ad_options['public-id'] ) )? $ad_options['public-id'] : false;
		$hash_length = 48;
		
		$public_name = ( isset( $ad_options['public-name'] ) && ! empty( $ad_options['public-name'] ) )? stripslashes( $ad_options['public-name'] ) : '';

		$clicks_display = ( in_array( $ad->type, Advanced_Ads_Tracking_Plugin::$types_using_click_tracking ) ) ? '' : 'display:none; ';
		
		$report_recip = isset( $ad_options['report-recip'] ) ? $ad_options['report-recip'] : '';
		$report_period = (
			isset( $ad_options['report-period'] ) &&
			in_array( $ad_options['report-period'], array( 'last30days', 'lastmonth', 'last12months' ) )
		)? $ad_options['report-period'] : 'last30days';
		
		$report_frequency = (
			isset( $ad_options['report-frequency'] ) &&
			in_array( $ad_options['report-frequency'], array( 'never', 'daily', 'weekly', 'monthly' ) )
		)? $ad_options['report-frequency'] : 'never';

		$billing_email = false;
		
		$order_id = get_post_meta( $post->ID, 'advanced_ads_selling_order', true );
		if ( $order_id ) {
			// if ad was sold via WooCommerce
			$order = wc_get_order( $order_id );
			global $woocommerce;
			if ( isset( $woocommerce->version ) && version_compare( $woocommerce->version, '3.0', ">=" ) ) {
				$billing_email = $order->get_billing_email();
			} else {
				$billing_email = $order->billing_email;
			}
		}
		
		/**
		 * load warnings, if any
		 */
		$warnings = false;
		
		// add warning if we are tracking with Analytics
		if ( 'ga' === $this->plugin->get_tracking_method() ){
			$warnings[] = array(
				'text' => esc_attr__( 'These features are not available with the Google Analytics tracking method.', 'advanced-ads-tracking' ),
			);
		}
      
		require ( AAT_BASE_PATH . 'admin/views/metabox.php' );
    }

    /**
     * add settings to settings page
     *
     * @param string $hook settings page hook
     * @since 1.0.0
     */
    public function settings_init($hook) {

        // don’t initiate if main plugin not loaded
        if ( ! class_exists( 'Advanced_Ads_Admin', false ) ) {
            return;
        }

        // get settings page hook
        $hook = $this->settings_page_hook;

        register_setting( $this->plugin->options_slug, $this->plugin->options_slug, array($this, 'sanitize_settings') );

        // add tracking settings section
        add_settings_section(
            'advanced_ads_tracking_setting_section',
            __('Tracking', 'advanced-ads-tracking'),
            array($this, 'render_settings_section_callback'),
            $hook
        );

        // add settings section for email reports
        add_settings_section(
            'advanced_ads_tracking_reports_setting_section',
            __('Email Reports', 'advanced-ads-tracking'),
            array($this, 'render_reports_settings_section_callback'),
            $hook
        );

        // add license key field to license section
        add_settings_field(
            'tracking-license',
            __('Tracking', 'advanced-ads-tracking'),
            array($this, 'render_settings_license_callback'),
            'advanced-ads-settings-license-page',
            'advanced_ads_settings_license_section'
        );

        // add setting fields
        add_settings_field(
            'tracking-method',
            __('Choose tracking method', 'advanced-ads-tracking'),
            array($this, 'render_settings_tracking_method_callback'),
            $hook,
            'advanced_ads_tracking_setting_section'
        );

        $options = $this->plugin->options();
		if ( ( isset( $options['method'] ) && 'ga' == $options['method'] ) || defined( 'ADVANCED_ADS_TRACKING_FORCE_ANALYTICS' ) && ADVANCED_ADS_TRACKING_FORCE_ANALYTICS ) {
			add_settings_field(
				'ga-settings',
				__('Google Analytics', 'advanced-ads-tracking'),
				array($this, 'render_settings_ga_callback'),
				$hook,
				'advanced_ads_tracking_setting_section'
			);
		}
        add_settings_field(
            'tracking-everything',
            __('What to track by default', 'advanced-ads-tracking'),
            array($this, 'render_settings_tracking_everything_callback'),
            $hook,
            'advanced_ads_tracking_setting_section'
        );
         add_settings_field(
            'link-base',
            __('Click-link base', 'advanced-ads-tracking'),
            array($this, 'render_settings_link_base_callback'),
            $hook,
            'advanced_ads_tracking_setting_section'
        );
        add_settings_field(
            'link-nofollow',
            __('Add nofollow', 'advanced-ads-tracking'),
            array($this, 'render_settings_link_nofollow_callback'),
            $hook,
            'advanced_ads_tracking_setting_section'
        );

        // timeout for impressions sum transient
         add_settings_field(
            'sum-timeout',
            __('Recalculate sums', 'advanced-ads-tracking'),
            array($this, 'render_settings_sum_timeout'),
            $hook,
            'advanced_ads_tracking_setting_section'
        );

        // link base for public stats
        add_settings_field(
            'public-stat',
            __( 'Link base for public stats', 'advanced-ads-tracking' ),
            array( $this, 'render_settings_public_stats' ),
            $hook,
            'advanced_ads_tracking_setting_section'
        );

        // tracking for bots
        add_settings_field(
            'tracking-bots',
            __( 'Track bots', 'advanced-ads-tracking' ),
            array( $this, 'render_settings_track_bots' ),
            $hook,
            'advanced_ads_tracking_setting_section'
        );

	// add setting fields
        add_settings_field(
            'tracking-uninstall',
            __( 'Delete data on uninstall', 'advanced-ads-tracking' ),
            array( $this, 'render_settings_tracking_uninstall_callback'),
            $hook,
            'advanced_ads_tracking_setting_section'
        );

        // scheduled reports recipients
        add_settings_field(
            'email-report-recipient',
            __( 'Recipients', 'advanced-ads-tracking' ),
            array( $this, 'render_settings_email_report_recip' ),
            $hook,
            'advanced_ads_tracking_reports_setting_section'
        );

        // scheduled reports frequency
        add_settings_field(
            'email-report-frequency',
            __( 'Frequency', 'advanced-ads-tracking' ),
            array( $this, 'render_settings_email_freq' ),
            $hook,
            'advanced_ads_tracking_reports_setting_section'
        );

        // scheduled reports stats period
        add_settings_field(
            'email-report-period',
            __( 'Statistics period', 'advanced-ads-tracking' ),
            array( $this, 'render_settings_email_stats_period' ),
            $hook,
            'advanced_ads_tracking_reports_setting_section'
        );

        // scheduled reports sender name
        add_settings_field(
            'email-report-sender-name',
            __( 'From name', 'advanced-ads-tracking' ),
            array( $this, 'render_settings_email_sender_name' ),
            $hook,
            'advanced_ads_tracking_reports_setting_section'
        );

        // scheduled reports sender address
        add_settings_field(
            'email-report-sender-address',
            __( 'From address', 'advanced-ads-tracking' ),
            array( $this, 'render_settings_email_sender_address' ),
            $hook,
            'advanced_ads_tracking_reports_setting_section'
        );

        // scheduled reports subject
        add_settings_field(
            'email-report-subject',
            __( 'Email subject', 'advanced-ads-tracking' ),
            array( $this, 'render_settings_email_subject' ),
            $hook,
            'advanced_ads_tracking_reports_setting_section'
        );

        // scheduled reports test email
        add_settings_field(
            'email-report-test-email',
            __( 'Send test email', 'advanced-ads-tracking' ),
            array( $this, 'render_settings_email_test_email' ),
            $hook,
            'advanced_ads_tracking_reports_setting_section'
        );

    }

	/**
	 * sanitize plugin settings
	 *
	 * @since 1.2.6
	 * @param array $options all the options
	 */
	public function sanitize_settings( $options ){

		// reset sums if settings are resaved
		Advanced_Ads_Tracking_Util::delete_sums_transient();

		if ( isset( $options['linkbase'] ) ) {
		    $options['linkbase'] = sanitize_title( $options['linkbase'] );
		}

		if ( isset( $options['public-stats-slug'] ) && ! empty( $options['public-stats-slug'] ) ) {
		    $options['public-stats-slug'] = stripslashes( $options['public-stats-slug'] );
		}

		// email reports addresses
		if ( isset( $options['email-addresses'] ) && !empty( $options['email-addresses'] ) ) {
		    $emails = explode( ',', stripslashes( $options['email-addresses'] ) );
		    $valid_addresses = array();
		    if ( is_array( $emails ) ) {
			foreach( $emails as $email ) {
			    $clean_email = sanitize_email( $email );
			    if ( !empty( $clean_email ) ) {
				$valid_addresses[] = $clean_email;
			    }
			}
		    }
		    $options['email-addresses'] = implode( ',', $valid_addresses );

		} else {
		    $options['email-addresses'] = '';
		}

		// email sender address
		if ( isset( $options['email-sender-address'] ) ) {
		    $sender_adr = stripslashes( $options['email-sender-address'] );
		    $options['email-sender-address'] = sanitize_email( $sender_adr );
		    if ( false == $options['email-sender-address'] ) {
			$options['email-sender-address'] = 'noreply@' . $_SERVER['SERVER_NAME'];
		    }
		} else {
		    $options['email-sender-address'] = 'noreply@' . $_SERVER['SERVER_NAME'];
		}

		// email sender name
		if ( isset( $options['email-sender-name'] ) && !empty( $options['email-sender-name'] ) ) {
		    $options['email-sender-name'] = stripslashes( $options['email-sender-name'] );
		} else {
		    $options['email-sender-name'] = bloginfo( 'name' );
		}

		// email subject
		if ( isset( $options['email-subject'] ) && !empty( $options['email-subject'] ) ) {
		    $options['email-subject'] = stripslashes( $options['email-subject'] );
		} else {
		    $options['email-subject'] = __( 'Ads Statistics', 'advanced-ads-tracking' );
		}
		
		// sanitize Analytics UID
		if ( isset( $options['ga-UID'] ) ) {
			$options['ga-UID'] = trim( $options['ga-UID'], ' /][)(#' );
		}
		
        // remove options on uninstall
        if ( isset( $options['uninstall'] ) ) {
            $options['uninstall'] = '1';
        }

		return $options;
       }

    /**
     * add tracking options to ad edit page
     *
     * @param obj $ad ad object
     * @param arr $types ad types
     */
    public function render_ad_tracking_options($ad, $types) {

        if (!isset($ad->id) || empty($ad->id)) return;

        $ad = new Advanced_Ads_Ad($ad->id);
        $options = $ad->options();
		$ad_options = isset( $options['tracking'] ) ? $options['tracking'] : array();
		
        $enabled = isset($ad_options['enabled']) ? $ad_options['enabled'] : 'default';
        $target = ( isset($ad_options['target']) ) ? $ad_options['target'] : 'default';
        $nofollow = ( isset($ad_options['nofollow']) ) ? $ad_options['nofollow'] : 'default';
        
		$link = Advanced_Ads_Tracking_Util::get_link( $ad );

		$tracking_choices = array(
			'default' => __( 'default', 'advanced-ads-tracking' ),
			'disabled' => __( 'disabled', 'advanced-ads-tracking' ),
		);
		
		if ( in_array( $ad->type, Advanced_Ads_Tracking_Plugin::$types_using_click_tracking ) ) {
			$tracking_choices['clicks'] = __( 'clicks only', 'advanced-ads-tracking' );
			$tracking_choices['impressions'] = __( 'impressions only', 'advanced-ads-tracking' );
			$tracking_choices['enabled'] = __( 'impressions & clicks', 'advanced-ads-tracking' );
		} else {
			$tracking_choices['enabled'] = __( 'enabled', 'advanced-ads-tracking' );
		}
		
        include AAT_BASE_PATH . 'admin/views/ad_tracking_options.php';
		
    }
    
    /**
     * show AdSense ad specific notices in parameters box
     */
    public function ad_notices( $notices, $box, $post ){

	$ad = new Advanced_Ads_Ad( $post->ID );

	// $content = json_decode( stripslashes( $ad->content ) );
	
	$ad_options = $ad->options();
	$ad_tracking = isset($ad_options['tracking']['enabled']) ? $ad_options['tracking']['enabled'] : 'default';
	$options = $this->plugin->options();
        $method = isset($options['everything']) ? $options['everything'] : true;
	switch ($box['id']){
	    case 'ad-parameters-box' :
		    // add warning if this is an AdSense ad unit
		    if ( 'adsense' == $ad->type ) {
			$notices[] = array(
				'text' => __( 'Please note: Clicks are not tracked for AdSense ads.', 'advanced-ads-tracking' ),
				'class' => 'advads-ad-notice-tracking-adsense',
			);
		    } elseif ( 'plain' == $ad->type && false !== strpos( $ad->content, 'window.adsbygoogle' ) 
			    && ( 'enabled' === $ad_tracking || ( 'default' === $ad_tracking && 'true' === $method ) ) ) {
			// add warning about tracking if plain text code contains AdSense code and tracking is enabled
			$notices[] = array(
				'text' => __( 'Please note: Click tracking should not be enabled for AdSense ads.', 'advanced-ads-tracking' ),
				'class' => 'advads-ad-notice-tracking-plain-text-adsense error',
			);
		    }
		    // general check for following conditions
		    $content_contains_a = strpos( $ad->content, 'href=' );
		    
		    // warning, if there is not %link% placeholder, but an `a` tag in the code
		    $link = Advanced_Ads_Tracking_Util::get_link( $ad );
		    $link_error_show = ( $link && $content_contains_a && ! strpos( $ad->content, '%link%' ) );
		    $text = __('Replace the <code>href</code> attribute of your link with <code>%link%</code> in order to track it. E.g. <code>&lt;a href="%link%"&gt;</code>', 'advanced-ads-tracking');
		    // only show reply link in WP 4.9 after we adjusted it to work with CodeMirror
		    global $wp_version;
		    if( 0 <= version_compare( $wp_version, '4.9' ) ){
				$exchange_link_show = ( $link && ( strpos( $ad->content, '"' . $link . '"' ) || strpos( $ad->content, "'" . $link . "'" ) ) && ! strpos( $ad->content, '%link%' ) ); 
				if ( $exchange_link_show ){ // show the exchange link only if $link is actually found in the ad content
					$text .= "&nbsp;" . sprintf( __( 'Click <a href="#" id="%s">here</a> to replace it', 'advanced-ads-tracking' ), 'advads-tracking-link-exchange' );
				}
		    }
		    
		    $class = 'advads-ad-notice-tracking-link-placeholder-missing error';
		    if( ! $link_error_show ) { $class .= ' hidden'; }
		    $notices[] = array(
			    'text' => $text,
			    'class' => $class,
		    );
		    // notice, if ad can not open in new window due to existing link attribute and does not have such code in it already
		    if( $content_contains_a
			    && ! strpos( $ad->content, '_blank' )
			    && Advanced_Ads_Tracking_Util::get_target( $ad )
			    ){
			    $notices[] = array(
				    'text' => __('Add <code>target="_blank"</code> to the ad code in order to open it in a new window. E.g. <code>&lt;a href="%link%" target="_blank"&gt;</code>', 'advanced-ads-tracking'),
				    'class' => 'advads-ad-notice-tracking-new-window',
			    );
		    }
		break;
	    case 'tracking-ads-box' :
		    // die();
		break;
	}


	return $notices;
    }    

    /**
     * save ad tracking options
     *
     * @since 1.0.0
     */
    public function save_options($options = array(), $ad = 0) {
		
        $options['tracking']['enabled'] = isset($_POST['advanced_ad']['tracking']['enabled']) ? $_POST['advanced_ad']['tracking']['enabled'] : 'default';
        $options['url'] = isset( $_POST['advanced_ad']['url'] ) ? trim( $_POST['advanced_ad']['url'] ) : '';
        $options['tracking']['impression_limit'] = isset($_POST['advanced_ad']['tracking']['impression_limit']) ? absint( $_POST['advanced_ad']['tracking']['impression_limit'] ) : '';
        $options['tracking']['click_limit'] = isset($_POST['advanced_ad']['tracking']['click_limit']) ? absint( $_POST['advanced_ad']['tracking']['click_limit'] ) : '';
        $options['tracking']['public-id'] = isset( $_POST['advanced_ad']['tracking']['public-id'] )? stripslashes( $_POST['advanced_ad']['tracking']['public-id'] ) : '';
        $options['tracking']['public-name'] = isset( $_POST['advanced_ad']['tracking']['public-name'] )? stripslashes( $_POST['advanced_ad']['tracking']['public-name'] ) : '';

		$target_values = array( 'default', 'same', 'new' );
		$nofollow_values = array( 'default', 1, 0 );
		$options['tracking']['target'] = ( isset( $_POST['advanced_ad']['tracking']['target'] ) && in_array( $_POST['advanced_ad']['tracking']['target'], $target_values ) )? $_POST['advanced_ad']['tracking']['target'] : 'default';
		$options['tracking']['nofollow'] = ( isset( $_POST['advanced_ad']['tracking']['nofollow'] ) && in_array( $_POST['advanced_ad']['tracking']['nofollow'], $nofollow_values ) )? $_POST['advanced_ad']['tracking']['nofollow'] : 'default';
		
		/**
		 *  email reports
		 */
		$options['tracking']['report-recip'] = isset( $_POST['advanced_ad']['tracking']['report-recip'] )? esc_textarea( $_POST['advanced_ad']['tracking']['report-recip'] ) : '';
		
		$options['tracking']['report-period'] = (
			isset( $_POST['advanced_ad']['tracking']['report-period'] ) &&
			in_array( $_POST['advanced_ad']['tracking']['report-period'], array(
				'last30days',
				'lastmonth',
				'last12months',
			) )
		)? $_POST['advanced_ad']['tracking']['report-period'] : 'last30days';
		
		$options['tracking']['report-frequency'] = (
			isset( $_POST['advanced_ad']['tracking']['report-frequency'] ) &&
			in_array( $_POST['advanced_ad']['tracking']['report-frequency'], array(
				'never',
				'daily',
				'weekly',
				'monthly',
			) )
		)? $_POST['advanced_ad']['tracking']['report-frequency'] : 'never';
		
		return $options;
    }

    /**
     * add stats submenu item
     *
     * @since 1.0.0
     * @param string $plugin_slug
     */
    public function add_menu_item($plugin_slug = ''){

	$cap = method_exists( 'Advanced_Ads_Plugin', 'user_cap' ) ?  Advanced_Ads_Plugin::user_cap( 'advanced_ads_edit_ads') : 'manage_options';

        $this->stat_page_hook = add_submenu_page(
            $plugin_slug, __('Advertisement Statistics', 'advanced-ads-tracking'), __('Stats', 'advanced-ads-tracking'), $cap, $plugin_slug . '-stats', array($this, 'display_stats_page')
        );
    }

    /**
     * add menu page to the array of pages that belong to Advanced Ads
     *
     * @since 1.2.4
     * @param arr $pages array with screen ids that already belong to Advanced Ads
     * @return arr $pages array with screen ids that already belong to Advanced Ads
     */
    public function add_menu_page_to_array( array $pages ){
	    $pages[] = 'advanced-ads_page_advanced-ads-stats';
	    $pages[] = 'advanced-ads_page_advanced-ads-tracking-events';
	    $pages[] = 'advads-tracking-db-page';
	    return $pages;
    }

    /**
     * render the stats page
     *
     * @since    1.0.0
     */
    public function display_stats_page() {

        // load all ads
        $all_ads = Advanced_Ads::get_ads( array( 'post_status' => array( 'publish', 'future', 'draft', 'pending' ) ) );

        $ads = array();

        foreach ( $all_ads as $ad ) {
            $ads[] = $ad->ID;
        }

        $util = Advanced_Ads_Tracking_Util::get_instance();

        // array with return messages
        $messages = array();
		
        // load default values
        $period = isset($_REQUEST['advads-stats']['period']) ? $_REQUEST['advads-stats']['period'] : null;
        $from   = isset($_REQUEST['advads-stats']['from']) ? $_REQUEST['advads-stats']['from'] : null;
        $to     = isset($_REQUEST['advads-stats']['to']) ? $_REQUEST['advads-stats']['to'] : null;
        $groupby = isset($_REQUEST['advads-stats']['groupby']) ? $_REQUEST['advads-stats']['groupby'] : null;

        $display_filter = ( isset( $_REQUEST['advads-stats-filter'] ) )? wp_unslash( $_REQUEST['advads-stats-filter'] ) : 'all-ads';

        $dateFormat = 'Y-m-d';
        $groupFormat = 'Y-m-d';

        // load period options
        $periods = array(
            'today' => __('today', 'advanced-ads-tracking'),
            'yesterday' => __('yesterday', 'advanced-ads-tracking'),
            'last7days' => __('last 7 days', 'advanced-ads-tracking'),
            'thismonth' => __('this month', 'advanced-ads-tracking'),
            'lastmonth' => __('last month', 'advanced-ads-tracking'),
            'thisyear' => __('this year', 'advanced-ads-tracking'),
            'lastyear' => __('last year', 'advanced-ads-tracking'),
            // -TODO this is not fully supported for ranges of more than ~200 points; should be reviewed before 2015-09-01
            'custom' => __('custom', 'advanced-ads-tracking'),
        );
        // load groupby options
        $groupbys = array(
            // group format, axis label, value conversion for graph
            'day' => array('Y-m-d', __('day', 'advanced-ads-tracking'), _x('Y-m-d', 'date format on stats page', 'advanced-ads-tracking')),
            'week' => array('o-\WW', __('week', 'advanced-ads-tracking'), _x('Y-m-d', 'date format on stats page', 'advanced-ads-tracking')),
            'month' => array('Y-m', __('month', 'advanced-ads-tracking'), _x('Y-m', 'date format on stats page', 'advanced-ads-tracking')),
        );

        // -TODO handle undefined options (should not occur)
        if (!isset($periods[$period])) {
            $period = null;
        }
        if (!isset($groupbys[$groupby])) {
            $groupby = null;
        } else {
            $groupFormat = $groupbys[$groupby][0];
            $dateFormat = $groupbys[$groupby][2];
        }

        $impression_stats = null;
        $click_stats = null;

        // load stats
        // if (is_array($ads) && $ads !== array()){
        if (isset($_REQUEST['advads-stats']['ads'])){
            $stat_args = array(
                // 'ad_id'       => $ads === array('all-ads') ? array() : $ads,
                'ad_id'       => $ads,
                'period'      => $period,
                'groupby'     => $groupby,
                'groupFormat' => $groupFormat,
                'from'        => $from,
                'to'          => $to,
            );
            $impression_stats = $this->load_stats($stat_args, $util->get_impression_table());
            $click_stats = $this->load_stats($stat_args, $util->get_click_table());
        }

        // convert array for use in template
        foreach ($groupbys as &$groupbyX) {
            $groupbyX = $groupbyX[1];
        }

        // display update messages
        if (count($messages)) {
            include AAT_BASE_PATH . 'admin/views/stats_messages.php';
        }
        // display stats view
        include AAT_BASE_PATH . 'admin/views/stats.php';
    }

    /**
     * load stats from the tracking tables
     *
     * @since 1.0.0
     * @param arr $args argument to load stats
     * @param str $table name of the table
     *
     * @return arr $stats array with stats sorted by date
     * @link http://codex.wordpress.org/Class_Reference/wpdb#SELECT_Generic_Results
     */
    public function load_stats($args = array(), $table){
        global $wpdb;

        if (!isset($args['ad_id']) || !is_array($args['ad_id'])) {
            return ;
        }


        $util = Advanced_Ads_Tracking_Util::get_instance();

        // sanitize
        $table = ' `' . $wpdb->_real_escape( str_replace( '`', '_', $table ) ) . '`';

        $adIds = array_values($args['ad_id']);

        $select = 'SQL_NO_CACHE `ad_id`, SUM(`count`) as `impressions`, %s as `date`';

        $groupby = '`timestamp`';
        $select_timestamp = null;
        $dateFormat = isset( $args['groupFormat'] ) ? $args['groupFormat'] : 'Y-m-d';
        $groupIncrement = ' + 1 day';

        if (isset($args['groupby'])){
            // group by day
            $groupByDayClause = '`timestamp` - `timestamp` % ' . Advanced_Ads_Tracking_Util::MOD_HOUR;
            switch ($args['groupby']) {
                case 'day' :
                    $groupby = $groupByDayClause;
                    $groupIncrement = ' + 1 day';
                    break;

                case 'week' :
                    // rather complex to mind weeks overlapping month and year while keeping proper display dates
                    // Y + W + MW == 0152 | 1201 ?
                    // Year + 00 + Week + 00 + 0 + ( MW == 0152 || MW == 1201 )
                    $groupby =
                        '(`timestamp` - `timestamp` % ' . Advanced_Ads_Tracking_Util::MOD_MONTH // year
                        . ') + (`timestamp` - `timestamp` % ' . Advanced_Ads_Tracking_Util::MOD_DAY // year + month + week
                        . ') - (`timestamp` - `timestamp` % ' . Advanced_Ads_Tracking_Util::MOD_WEEK // - year - month
                        . ') + ('
                        . '(`timestamp` - `timestamp` % ' . Advanced_Ads_Tracking_Util::MOD_DAY // + year + month + week
                        . '- `timestamp` % ' . Advanced_Ads_Tracking_Util::MOD_MONTH // - year
                        . ') IN (1520000, 12010000))'
                    ;
                    $select_timestamp = '`timestamp` - `timestamp` % ' . Advanced_Ads_Tracking_Util::MOD_HOUR;
                    $groupIncrement = ' + 1 week';
                    break;

                case 'month' :
                    $groupby = '`timestamp` - `timestamp` % ' . Advanced_Ads_Tracking_Util::MOD_WEEK;
                    $groupIncrement = ' + 1 month';
                    break;
            }
        }
        // select range
        if (isset($args['period'])) {
            // time handling; blog time offset in seconds

            $gmt_offset = 3600 * floatval( get_option( 'gmt_offset', 0 ) );

            // day start in seconds
            $now = $util->get_timestamp();
			
            $today_start = $now - $now % Advanced_Ads_Tracking_Util::MOD_HOUR;

            $start = null;
            $end = null;


            switch ($args['period']) {
                case 'today' :
                    $start = $today_start;
                    break;
                case 'yesterday' :
                    $start = $util->get_timestamp( time() - DAY_IN_SECONDS );
					$start -= $start % Advanced_Ads_Tracking_Util::MOD_HOUR;
                    $end = $today_start;
                    break;
                case 'last7days' :
                    // last seven full days // -TODO might do last or current week as well
                    $start = $util->get_timestamp( time() - WEEK_IN_SECONDS );
                    $start -= $start % Advanced_Ads_Tracking_Util::MOD_HOUR;
                    break;
                case 'thismonth' :
                    // timestamp from first day of the current month
                    $start = $now - $now % Advanced_Ads_Tracking_Util::MOD_WEEK;
                    break;
                case 'lastmonth' :
                    // timestamp from first day of the last month
                    $start = $util->get_timestamp( mktime(0, 0, 0, date("m") - 1, 1, date("Y")) );
                    $end = $now - $now % Advanced_Ads_Tracking_Util::MOD_WEEK;
                    break;
                case 'thisyear' :
                    // timestamp from first day of the current year
                    $start = $now - $now % Advanced_Ads_Tracking_Util::MOD_MONTH;
                    break;
                case 'lastyear' :
                    // timestamp from first day of previous year
                    $start = $util->get_timestamp( mktime(0, 0, 0, 1, 1, date('Y') - 1) );
                    $end = $now - $now % Advanced_Ads_Tracking_Util::MOD_MONTH;
                    break;
                case 'custom' :
                    $start  = $util->get_timestamp( strtotime( $args['from'] ) - $gmt_offset  );
                    $end    = $util->get_timestamp( strtotime( $args['to'] ) - $gmt_offset + ( 24 * 3600 ) );
                    break;
            }
        }
        // TODO limit range (mind groupIncrement/ granularity)
        // values might be null (not set) or false (error in input)

        $where = '';
        if (isset($start) && $start) {
            $where .= "WHERE `timestamp` >= $start";
        }
        if (isset($end) && $end) {
            if ( $where ) {
                $where .= " AND `timestamp` < $end";
            } else {
                $where .= "WHERE `timestamp` < $end";
            }
        }
		
		/**
		 * Select only one ad stats 
		 */
		if ( 1 == count( $args['ad_id'] ) ) {
			if ( $where ) {
				$where .= ' AND `ad_id` = ' . $args['ad_id'][0];
			} else {
				$where .= 'WHERE `ad_id` = ' . $args['ad_id'][0];
			}
		}
		
        // order
        $orderby = ''; #'ORDER BY `timestamp` ASC'; // this is implicit for current model

        // get results
        $stats = array();
        $select = sprintf( $select, isset( $select_timestamp ) ? $select_timestamp : $groupby );

        $groupby .= ', `ad_id`';

        $query = "SELECT $select FROM $table $where $orderby GROUP BY $groupby";
		
        $numRows = $wpdb->query($query);

        if ($numRows > 0) {
            $rows = $wpdb->last_result;
			
            $statBase = array();
            if ($adIds !== array()) {
                foreach ($adIds as $adId) {
                    $statBase[$adId] = null;
                }
            }

            foreach ($rows as $row) {
                $time = $util->get_date_from_db( $row->date, $dateFormat );
                if ( ! isset($stats[$time])) {
                    $stats[$time] = $statBase;
                }
                // -TODO may select ad_id from row, if defined
                // -TODO click table currently also has "impressions" instead of "clicks" in order to handle both tables equally
                if ( isset( $stats[$time][$row->ad_id] ) ) {
                    $stats[$time][$row->ad_id] += $row->impressions;
                } else {
                    $stats[$time][$row->ad_id] = $row->impressions;
                }
            }
        }

        // prepare results
        if ($stats === array()) { // TODO
            return false;
        } else {
            return $this->prepare_stats_array($stats, $statBase, $dateFormat, $groupIncrement, $start, $end);
        }
    }

    /**
     * Prepare the stats array for templating.
     *
     * Especially add empty dates.
     *
     * @param array  $stats stats    Graph values by timestamp (grouped)
     * @param array  $statsBase      Empty stat row
     * @param string $groupFormat    Date format string (x-axis labels)
     * @param string $groupIncrement Date increment string
     *
     * @return array $stats input with filled in dates.
     */
    protected function prepare_stats_array($stats, $statBase, $groupFormat, $groupIncrement, $minDate, $maxDate) {

        if ( $stats == array() ) {
            return;
        }

        if ( ! isset( $maxDate ) ) {
            $maxDate = date( 'Y-m-d H:i:s' );
        }
        $maxDate = strtotime( $maxDate );

        // add missing dates
        $oldTime = null;
        $time = null;

        // ensure order // hurray for PHP 5.4+ that does this in a single line!
        $statKeys = array_keys( $stats );
        natsort( $statKeys );
        $sortedStats = array();

        $_increment_interval = array(
            ' + 1 day' => 'P1D',
            ' + 1 week' => 'P1W',
            ' + 1 month' => 'P1M',
        );

        $prevDate = null;

        $dateFormat = 'Y-m-d';
        if ( ' + 1 month' == $groupIncrement ) {
            $dateFormat = $groupFormat;
        }

        if ( version_compare( PHP_VERSION, '5.3.0', '<' ) ) {
            // if PHP earlier than 5.3.0 return result directly
            return $sortedStats;
        }

        foreach ( $statKeys as $statKey ) {
            $currentDate = date_create( $statKey );
            /**
             *  Fill missing entry for date w/o records
             */
            if ( null !== $prevDate ) {
                // not the first
                $nextDate = clone $prevDate;
                $nextDate->add( new DateInterval( $_increment_interval[ $groupIncrement ] ) );

                if ( $statKey != $nextDate->format( $dateFormat ) && ( $nextDate < $currentDate ) ) {
                    // current date ( $statKey ) differs from $prevDate + increment ( $nextDate )
                    while ( $nextDate->format( $dateFormat ) != $statKey && ! ( $nextDate > $currentDate ) ) {
                        // no record for this date, fill it
                        $sortedStats[ $nextDate->format( $dateFormat ) ] = $statBase;
                        $nextDate->add( new DateInterval( $_increment_interval[ $groupIncrement ] ) );
                    }
                }
            }

        	$sortedStats[$statKey] = $stats[$statKey];
            $prevDate = clone $currentDate;

        }
        return $sortedStats;
    }

    /**
     * resets stats for ads
     *
     * @param str/int $ad_id ad id or string "all-ads"
     * @return string $error message
     */
    public function reset_stats($ad_id = 0) {
        $util = Advanced_Ads_Tracking_Util::get_instance();
        $affected_rows = $util->reset_stats( $ad_id );

        if ( null === $affected_rows ) {
            return array( 'status' => true );
        } elseif ( true === $affected_rows ) {
            return array(
				'status' => true ,
				'msg' => esc_attr__( 'All impressions and clicks removed.', 'advanced-ads-tracking' )
			);
        } elseif ( ! $affected_rows ) {
            return array(
				'status' => false,
				'msg' => esc_attr__( 'No stats removed', 'advanced-ads-tracking' )
			);
        }
		return array(
			'status' => true,
			'msg' => sprintf( esc_attr__( 'Impressions and clicks for ad ID %d removed', 'advanced-ads-tracking' ), $ad_id ),
		);
    }

    /**
     * render tracking settings section
     *
     * @since 1.0.0
     */
    public function render_settings_section_callback(){
        _e('Settings for the Ad Tracking add-on', 'advanced-ads-tracking');
        // add hidden field to also save db version and not to override it
        $options = $this->plugin->options();
        $dbversion = isset($options['dbversion']) ? $options['dbversion'] : 0;
        ?><input type="hidden" name="<?php echo $this->plugin->options_slug; ?>[dbversion]" value="<?php echo $dbversion; ?>"/><?php
    }

    /**
     * render tracking settings section for email reports
     *
     * @since 1.2.8
     */
    public function render_reports_settings_section_callback(){
	    if( 'ga' === $this->plugin->get_tracking_method() ){
		?><p class="advads-error-message"><?php _e( 'These features are not available with the Google Analytics tracking method.', 'advanced-ads-tracking' ); ?></p><?php
	    }
    }

    /**
     * render license key section
     *
     * @since 1.2.0
     */
    public function render_settings_license_callback(){
	    $licenses = get_option(ADVADS_SLUG . '-licenses', array());
	    $license_key = isset($licenses['tracking']) ? $licenses['tracking'] : '';
	    $license_status = get_option($this->plugin->options_slug . '-license-status', false);
	    $index = 'tracking';
	    $plugin_name = AAT_PLUGIN_NAME;
	    $options_slug = $this->plugin->options_slug;
	    $plugin_url = self::PLUGIN_LINK;

	    // template in main plugin
	    include ADVADS_BASE_PATH . 'admin/views/setting-license.php';
    }

	/**
	 *  Render Google Analytics settings
	 */
	public function render_settings_ga_callback() {
		$options = $this->plugin->options();
		$UID = ( isset( $options['ga-UID'] ) )? $options['ga-UID'] : '';
		include AAT_BASE_PATH . 'admin/views/setting_ga.php';
	}
	
    /**
     *  render tracking uninstall settings
     */
    public function render_settings_tracking_uninstall_callback(){
	    $options = $this->plugin->options();
	    $uninstall = ( isset( $options['uninstall'] ) )? '1' : '0';
	    include AAT_BASE_PATH . 'admin/views/setting_uninstall.php';
    }

    /**
     * render tracking method setting
     *
     * @since 1.0.0
     */
    public function render_settings_tracking_method_callback(){
        $options = $this->plugin->options();
        $method = isset($options['method']) ? $options['method'] : 'onrequest';
        include AAT_BASE_PATH . 'admin/views/setting_method.php';
    }

    /**
     * render tracking-everything setting
     *
     * @since 1.0.0
     */
    public function render_settings_tracking_everything_callback(){
        $options = $this->plugin->options();
        $method = isset($options['everything']) ? $options['everything'] : 'true';
		
        include AAT_BASE_PATH . 'admin/views/setting_everything.php';
    }

    /**
     * render link-nofollow setting
     *
     * @since 1.1.0
     */
    public function render_settings_link_base_callback(){
        $options = $this->plugin->options();
        $linkbase = isset($options['linkbase']) ? $options['linkbase'] : 'linkout';
        include AAT_BASE_PATH . 'admin/views/setting_linkbase.php';
    }

    /**
     * render link-nofollow setting
     *
     * @since 1.1.0
     */
    public function render_settings_link_nofollow_callback(){
        $options = $this->plugin->options();
        $nofollow = isset($options['nofollow']) ? $options['nofollow'] : true;
        include AAT_BASE_PATH . 'admin/views/setting_nofollow.php';
    }

    /**
     * render sum-timeout setting
     *
     * @since 1.2.6
     */
    public function render_settings_sum_timeout(){
        $options = $this->plugin->options();
        $timeout = isset($options['sum-timeout']) ? absint( $options['sum-timeout'] ) : Advanced_Ads_Tracking_Util::SUM_TIMEOUT;
        include AAT_BASE_PATH . 'admin/views/setting_sum_timeout.php';
    }

    /**
     *  render public stats setting
     *
     *  @since 1.2.7
     */
    public function render_settings_public_stats(){
        $options = $this->plugin->options();
        $public_stats_slug = isset( $options['public-stats-slug'] )? $options['public-stats-slug'] : self::PUBLIC_STATS_DEFAULT;
        include AAT_BASE_PATH . 'admin/views/setting_public_stats.php';
    }

    /**
     *  render settings email recipient
     *
     *  @since 1.2.8
     */
    public function render_settings_email_report_recip(){
        $options = $this->plugin->options();
        $recipients = isset( $options['email-addresses'] )? $options['email-addresses'] : '';
        include AAT_BASE_PATH . 'admin/views/setting_email_report_recip.php';
    }

    /**
     *  render settings email frequency
     *
     *  @since 1.2.8
     */
    public function render_settings_email_freq(){
        $options = $this->plugin->options();
        $sched = isset( $options['email-sched'] )? $options['email-sched'] : 'daily';
        include AAT_BASE_PATH . 'admin/views/setting_email_report_frequency.php';
    }

    /**
     *  render settings email stats period
     *
     *  @since 1.2.8
     */
    public function render_settings_email_stats_period(){
        $options = $this->plugin->options();
        $period = isset( $options['email-stats-period'] )? $options['email-stats-period'] : 'last30days';
        include AAT_BASE_PATH . 'admin/views/setting_email_report_stats_period.php';
    }

    /**
     *  render settings email sender name
     *
     *  @since 1.2.8
     */
    public function render_settings_email_sender_name(){
        $options = $this->plugin->options();
        $sender_name = ( isset( $options['email-sender-name'] ) && !empty( $options['email-sender-name'] ) )? stripslashes( $options['email-sender-name'] ) : 'Advanced Ads';
        include AAT_BASE_PATH . 'admin/views/setting_email_report_sender_name.php';
    }

    /**
     *  render settings email sender address
     *
     *  @since 1.2.8
     */
    public function render_settings_email_sender_address(){
        $options = $this->plugin->options();
        $sender_address = ( isset( $options['email-sender-address'] ) && ! empty( $options['email-sender-address'] ) )? stripslashes( $options['email-sender-address'] ) : false;
        $sender_address = sanitize_email( $sender_address );
        if ( false === $sender_address && isset( $_SERVER['SERVER_NAME'] ) ) {
            $sender_address = 'noreply@' . $_SERVER['SERVER_NAME'];
        }
        include AAT_BASE_PATH . 'admin/views/setting_email_report_sender_address.php';
    }

    /**
     *  render settings email subject
     *
     *  @since 1.2.8
     */
    public function render_settings_email_subject(){
        $options = $this->plugin->options();
        $email_subject = ( isset( $options['email-subject'] ) && !empty( $options['email-subject'] ) )? stripslashes( $options['email-subject'] ) : __( 'Ads Statistics', 'advanced-ads-tracking' );

        include AAT_BASE_PATH . 'admin/views/setting_email_report_subject.php';
    }

    /**
     *  render settings email subject
     *
     *  @since 1.2.8
     */
    public function render_settings_email_test_email(){
        $options = $this->plugin->options();
        $recipients = isset( $options['email-addresses'] )? $options['email-addresses'] : '';
        $sched = isset( $options['email-sched'] )? $options['email-sched'] : 'daily';
        include AAT_BASE_PATH . 'admin/views/setting_email_test_email.php';
    }

    /**
     *  render settings tracking bot
     *
     *  @since to be defined
     */
    public function render_settings_track_bots(){
        $options = $this->plugin->options();
        $track_bots = isset( $options['track-bots'] )? $options['track-bots'] : '0';
        include AAT_BASE_PATH . 'admin/views/setting_tracking_bots.php';
    }

    /**
    * add tracking settings tab
    *
    * @since 1.2.0
    * @param arr $tabs existing setting tabs
    * @return arr $tabs setting tabs with AdSense tab attached
    */
    public function setting_tabs(array $tabs){

        $tabs['tracking'] = array(
            'page' => $this->settings_page_hook,
            'group' => $this->plugin->options_slug,
            'tabid' => 'tracking',
            'title' => __( 'Tracking', 'advanced-ads-tracking' )
        );

        return $tabs;
    }

    /**
     * Create table on installation
     *
     * @since 1.0.0
     * @link http://codex.wordpress.org/Creating_Tables_with_Plugins
     */
	public static function create_tables(){
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$impressions_table = $wpdb->prefix . 'advads_impressions';
		$clicks_table = $wpdb->prefix . 'advads_clicks';
		$sql = array();
		$sql[] = "CREATE TABLE IF NOT EXISTS $impressions_table (
			`timestamp` INT UNSIGNED NOT NULL,
			`ad_id` INT UNSIGNED NOT NULL,
			`count` MEDIUMINT UNSIGNED NOT NULL,
			PRIMARY KEY (`timestamp`, `ad_id`)
		) COLLATE $charset_collate";
		$sql[] = "CREATE TABLE IF NOT EXISTS $clicks_table (
			`timestamp` INT UNSIGNED NOT NULL,
			`ad_id` INT UNSIGNED NOT NULL,
			`count` MEDIUMINT UNSIGNED NOT NULL,
			PRIMARY KEY (`timestamp`, `ad_id`)
		) COLLATE $charset_collate";
		foreach ($sql as $query) {
			$wpdb->query( $query );
		}
	}

    /**
     * Check tables on update
     *
     */
    public function check_tables(){
        global $wpdb;

        $util = Advanced_Ads_Tracking_Util::get_instance();
        $impressions_table = $util->get_impression_table();
        $clicks_table = $util->get_click_table();
        $charset_collate = $wpdb->get_charset_collate();
	
        /**
         *  Hotfix for missing stats on new year
         */
        $__tables_results = $wpdb->get_results( "SHOW TABLES LIKE '$impressions_table'" );
        if ( 0 < count( $__tables_results ) ) {
            $corrupted_impr = $wpdb->get_results( "SELECT * FROM $impressions_table WHERE `timestamp` BETWEEN 1601530100 AND 1601530323" );
            if ( 0 < count( $corrupted_impr ) ) {
                foreach( $corrupted_impr as $row ) {
                    $ts = str_replace( '53', '01', $row->timestamp );
                    $wpdb->query( "UPDATE $impressions_table SET `timestamp` = $ts WHERE `timestamp` = $row->timestamp AND `ad_id` = $row->ad_id" );
                }
            }
            $corrupted_clicks = $wpdb->get_results( "SELECT * FROM $clicks_table WHERE `timestamp` BETWEEN 1601530100 AND 1601530323" );
            if ( 0 < count( $corrupted_clicks ) ) {
                foreach( $corrupted_clicks as $row ) {
                    $ts = str_replace( '53', '01', $row->timestamp );
                    $wpdb->query( "UPDATE $clicks_table SET `timestamp` = $ts WHERE `timestamp` = $row->timestamp AND `ad_id` = $row->ad_id" );
                }
            }
        }

        // there was a serious issue with non-initialised base plugin
        // the upgrade process must skip if this happens
        // otherwise information is lost for all tracked ads
        $options = $this->plugin->options();
        if (!is_array($options)) {
            return false;
        }

        $sql = array();
        if ( ! isset( $options['dbversion'] ) ) {
            $options['dbversion'] = '0';
        }

        // handle diffs incrementally
        switch ($options['dbversion']) {
            case '0':
                $sql[] = "CREATE TABLE IF NOT EXISTS $impressions_table (
                    `timestamp` INT UNSIGNED NOT NULL,
                    `ad_id` INT UNSIGNED NOT NULL,
                    `count` MEDIUMINT UNSIGNED NOT NULL,
                    PRIMARY KEY (`timestamp`, `ad_id`)
                ) ENGINE = MyISAM $charset_collate";
            case '1.0':
                $sql[] = "CREATE TABLE IF NOT EXISTS $clicks_table (
                    `timestamp` INT UNSIGNED NOT NULL,
                    `ad_id` INT UNSIGNED NOT NULL,
                    `count` MEDIUMINT UNSIGNED NOT NULL,
                    PRIMARY KEY (`timestamp`, `ad_id`)
                ) ENGINE = MyISAM $charset_collate";
            case '1.1':
            case '1.2':
            case '1.3':
		// update INT(10) to BIGINT(20) since this is the max size for WordPress post IDs
		$sql[] = "ALTER TABLE $clicks_table CHANGE `ad_id` `ad_id` BIGINT(20) UNSIGNED NOT NULL";
		$sql[] = "ALTER TABLE $impressions_table CHANGE `ad_id` `ad_id` BIGINT(20) UNSIGNED NOT NULL";
        }

        // execute upgrade if not empty
        if ($sql !== array()) {
            foreach ($sql as $query) {
                $wpdb->query( $query );
            }
            // dbDelta is not capable to handle complex actions..
            #require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            #dbDelta( $sql );

            // add database version number to options
            $options['dbversion'] = Advanced_Ads_Tracking_Util::DB_VERSION;
            $this->plugin->update_options( $options );
        }
    }
}
