/**
 * Block dependencies
 */
import classnames from 'classnames';
import './style.scss';

/**
 * Internal block libraries
 */
const { __ } = wp.i18n;
const {
  registerBlockType,
} = wp.blocks;
const {
  RichText,
  InspectorControls,
} = wp.blockEditor;
const {
  Button,
  PanelBody,
  PanelRow,
  SelectControl,
  DateTimePicker,
  TextControl,
} = wp.components;

/**
  * Register block
 */
export default registerBlockType(
  'wp-analytify-pro/analytify-shortcodes',
  {
    title: __('Analytify Shortcodes', 'wp-analytify-pro'),
    description: __('Create shorcodes for Analytify to show Google Analytics reports on frontend.', 'wp-analytify-pro'),
    category: 'analytify-pro-blocks',
    icon: 'editor-code',
    keywords: [
      __('Analytify', 'wp-analytify-pro'),
      __('Shortcodes', 'wp-analytify-pro'),
    ],
    attributes: {
      shortcodeRichtext: {
        type: 'string',
        source: 'html',
        selector: '.analytify-shortcodes-block',
        default: '',
      },
      metrics: {
        type: 'string',
        default: ''
      },
      visibleTo: {
        type: 'string',
        default: '',
      },
      metricsMultiple: {
        type: 'array',
        default: ['ga:users'],
      },
      visibleToMultiple: {
        type: 'array',
        default: '',
      },
      dimensions: {
        type: 'array',
        default: ['ga:userType'],
      },
      sortBy: {
        type: 'string',
        default: ''
      },
      analyticsFor: {
        type: 'string',
        default: 'current'
      },
      togglePostidField: {
        type: 'boolean',
        default: true,
      },
      customPostID: {
        type: 'number',
        default: ''
      },
      startDate: {
        type: 'string',
      },
      endDate: {
        type: 'string',
      },
      maxRecords: {
        type: 'number',
        default: '5',
      },
    },
    edit: props => {
      const { 
        attributes: { 
          shortcodeRichtext, 
          metrics, 
          visibleTo, 
          metricsMultiple, 
          visibleToMultiple, 
          dimensions, 
          startDate, 
          endDate, 
          sortBy,
          analyticsFor, 
          togglePostidField, 
          customPostID, 
          maxRecords 
        },
        className, 
        setAttributes 
      } = props;
      
      const generateSimpleShortcode = () => {
				let statsMetrics = (metrics.length == 0) ? 'ga:users' : metrics;
        let appendShortcode = shortcodeRichtext + '[analytify-stats metrics="' + statsMetrics + '" permission_view="' + visibleTo + '"]';
			
				setAttributes({ shortcodeRichtext: appendShortcode });
				
				// Reset attributes.
        setAttributes({ metrics: 'ga:users' });
        setAttributes({ visibleTo: '' });
      }

      const toggleClasses = classnames(
        className,
        { 'toggle-postid-field': togglePostidField },
      );

      const setAnalyticsFor = (e) => {
        let analyticsForVal = e;

        ('page_id' == analyticsForVal) ? setAttributes({ togglePostidField: false }) : setAttributes({ togglePostidField: true });
        
        setAttributes({ analyticsFor: analyticsForVal });
      }

      const generateAdvancedShortcode = () => {
				const date = new Date();
				
        let currDate = ('0' + date.getDate()).slice(-2);
        let currMonth = ('0' + date.getMonth()).slice(-2);
        let currYear = date.getFullYear();
        let currentDate = currYear + "-" + currMonth + "-" + currDate;
        let startDateOnly = currentDate;
        let endDateOnly = currentDate;
        let pageId = '';        
        let visibleToMultipleval = visibleToMultiple;				

        if (typeof startDate !== 'undefined') {
					if ( startDate.length == 0 ) {
						startDateOnly = currentDate;
					} else {
						startDateOnly = startDate.toString().split("T");
						startDateOnly = startDateOnly[0];
					}
				}
				
        if (typeof endDate !== 'undefined') {
					if ( endDate.length == 0 ) {
						endDateOnly = currentDate;
					} else {
						endDateOnly = endDate.toString().split("T");
						endDateOnly = endDateOnly[0];
					}
				}

        if ('page_id' == analyticsFor) {
          pageId = customPostID;
        }

        // Empty permissions array if everyone is allowed view.
        if (typeof visibleToMultipleval[0] !== 'undefined' && visibleToMultipleval[0].length == 0) {
          visibleToMultipleval = [];          
				}				
        
        let appendShortcode = shortcodeRichtext + '[analytify-stats metrics="' + metricsMultiple + '" permission_view="' + visibleToMultipleval + '" dimensions="' + dimensions + '" date_type="custom" start_date="' + startDateOnly + '" end_date="' + endDateOnly + '" max_results="' + maxRecords + '" sort="' + sortBy + '" analytics_for="' + analyticsFor + '" custom_page_id="' + pageId + '"]';

				setAttributes({ shortcodeRichtext: appendShortcode });
				console.log(metricsMultiple);
				
				// Reset attributes.
				setAttributes({ metrics: 'ga:users' });
				setAttributes({ visibleTo: '' });
				setAttributes({ metricsMultiple: ['ga:users'] });
				setAttributes({ visibleToMultiple: '' });
				setAttributes({ dimensions: ['ga:userType'] });
				setAttributes({ startDate: '' });
				setAttributes({ endDate: '' });
				setAttributes({ sortBy: '' });
				setAttributes({ analyticsFor: 'current' });
				setAttributes({ togglePostidField: true });
				setAttributes({ customPostID: '' });
				setAttributes({ maxRecords: 5 });
      }

      return [
        <InspectorControls>
          
          {/*  Simple Shortcodes Section */}
          <PanelBody
            title={__('Simple Shortcode', 'wp-analytify-pro')}
            className="analytify-shortcodes-block-simple-panel"
            initialOpen={false}
          >
            <PanelRow>
              <SelectControl
                id="analytify-simple-shortcode-metics"
                label={__('Metrics', 'wp-analytify-pro')}
                value={metrics}
                onChange={(metrics) => setAttributes({ metrics })}
                options={[
                  // { value: null, label: 'Select matrics', disabled: true },
                  { value: 'ga:users', label: __('Users', 'wp-analytify-pro') },
                  { value: 'ga:newUsers', label: __('New Users', 'wp-analytify-pro') },
                  { value: 'ga:percentNewSessions', label: __('Percent New Sessions', 'wp-analytify-pro') },
                  { value: 'ga:sessions', label: __('Sessions', 'wp-analytify-pro') },
                  { value: 'ga:bounces', label: __('Bounces', 'wp-analytify-pro') },
                  { value: 'ga:bounceRate', label: __('Bounce Rate', 'wp-analytify-pro') },
                  { value: 'ga:sessionDuration', label: __('Session Duration', 'wp-analytify-pro') },
                  { value: 'ga:avgSessionDuration', label: __('Avg SessionDuration', 'wp-analytify-pro') },
                  { value: 'ga:hits', label: __('Hits', 'wp-analytify-pro') },
                  { value: 'ga:pageValue', label: __('Page Value', 'wp-analytify-pro') },
                  { value: 'ga:entrances', label: __('Entrances', 'wp-analytify-pro') },
                  { value: 'ga:pageviews', label: __('Pageviews', 'wp-analytify-pro') },
                  { value: 'ga:uniquePageviews', label: __('Unique Pageviews', 'wp-analytify-pro') },
                  { value: 'ga:exits', label: __('Exits', 'wp-analytify-pro') },
                  { value: 'ga:exitRate', label: __('Exit Rate', 'wp-analytify-pro') },
                  { value: 'ga:entranceRate', label: __('Entrance Rate', 'wp-analytify-pro') },
                  { value: 'ga:pageviewsPerSession', label: __('Pageviews Per Session', 'wp-analytify-pro') },
                  { value: 'ga:timeOnPage', label: __('Time On Page', 'wp-analytify-pro') },
                  { value: 'ga:avgTimeOnPage', label: __('Avg Time On Page', 'wp-analytify-pro') },
                ]}
              />
            </PanelRow>
            <PanelRow>
              <SelectControl
                id="analytify-simple-shortcode-visibleto"
                label={__('Visible to', 'wp-analytify-pro')}
                value={visibleTo}
                onChange={(visibleTo) => setAttributes({ visibleTo })}
                options={[
                  { value: '', label: __('Everyone', 'wp-analytify-pro') },
                  { value: 'admin', label: __('Admininstrator', 'wp-analytify-pro') },
                  { value: 'editor', label: __('Editor', 'wp-analytify-pro') },
                  { value: 'author', label: __('Author', 'wp-analytify-pro') },
                  { value: 'contributer', label: __('Contributer', 'wp-analytify-pro') },
                ]}
              />
            </PanelRow>
            <PanelRow>
              <Button 
                isDefault
                onClick={generateSimpleShortcode}
              >
              {__('Make Shortcode', 'wp-analytify-pro')}
              </Button>,
            </PanelRow>
          </PanelBody>

          {/*  Advanced Shortcodes Section */}
          <PanelBody
            title={__('Advanced Shortcode', 'wp-analytify-pro')}
            initialOpen={false}
            className="analytify-shortcodes-block-advanced-panel"
          >
            <PanelRow>
              <SelectControl
                multiple
                help="command+click to select multiple values"
                id="analytify-advanced-shortcode-metics"
                label={__('Metrics', 'wp-analytify-pro')}
                value={metricsMultiple}
                onChange={(metricsMultiple) => setAttributes({ metricsMultiple })}
                options={[
                  { value: 'ga:users', label: __('Users', 'wp-analytify-pro') },
                  { value: 'ga:newUsers', label: __('New Users', 'wp-analytify-pro') },
                  { value: 'ga:percentNewSessions', label: __('Percent New Sessions', 'wp-analytify-pro') },
                  { value: 'ga:sessions', label: __('Sessions', 'wp-analytify-pro') },
                  { value: 'ga:bounces', label: __('Bounces', 'wp-analytify-pro') },
                  { value: 'ga:bounceRate', label: __('Bounce Rate', 'wp-analytify-pro') },
                  { value: 'ga:sessionDuration', label: __('Session Duration', 'wp-analytify-pro') },
                  { value: 'ga:avgSessionDuration', label: __('Avg SessionDuration', 'wp-analytify-pro') },
                  { value: 'ga:hits', label: __('Hits', 'wp-analytify-pro') },
                  { value: 'ga:pageValue', label: __('Page Value', 'wp-analytify-pro') },
                  { value: 'ga:entrances', label: __('Entrances', 'wp-analytify-pro') },
                  { value: 'ga:pageviews', label: __('Pageviews', 'wp-analytify-pro') },
                  { value: 'ga:uniquePageviews', label: __('Unique Pageviews', 'wp-analytify-pro') },
                  { value: 'ga:exits', label: __('Exits', 'wp-analytify-pro') },
                  { value: 'ga:exitRate', label: __('Exit Rate', 'wp-analytify-pro') },
                  { value: 'ga:entranceRate', label: __('Entrance Rate', 'wp-analytify-pro') },
                  { value: 'ga:pageviewsPerSession', label: __('Pageviews Per Session', 'wp-analytify-pro') },
                  { value: 'ga:timeOnPage', label: __('Time On Page', 'wp-analytify-pro') },
                  { value: 'ga:avgTimeOnPage', label: __('Avg Time On Page', 'wp-analytify-pro') },
                ]}
              />
            </PanelRow>
            <PanelRow>
              <SelectControl
                multiple
                help="command+click to select multiple values"
                id="analytify-advanced-shortcode-dimensions"
                label={__('Dimensions', 'wp-analytify-pro')}
                value={dimensions}
                onChange={(dimensions) => setAttributes({ dimensions })}
                options={[
                  { value: 'ga:userType', label: __('User Type', 'wp-analytify-pro') },
                  { value: 'ga:sessionCount', label: __('Session Count', 'wp-analytify-pro') },
                  { value: 'ga:daysSinceLastSession', label: __('Days Since Last Session', 'wp-analytify-pro') },
                  { value: 'ga:sessionDurationBucket', label: __('Session Duration Bucket', 'wp-analytify-pro') },
                  { value: 'ga:referralPath', label: __('Referral Path', 'wp-analytify-pro') },
                  { value: 'ga:fullReferrer', label: __('Full Referral Path', 'wp-analytify-pro') },
                  { value: 'ga:source', label: __('Source', 'wp-analytify-pro') },
                  { value: 'ga:medium', label: __('Medium', 'wp-analytify-pro') },
                  { value: 'ga:sourceMedium', label: __('Source Medium', 'wp-analytify-pro') },
                  { value: 'ga:keyword', label: __('Keyword', 'wp-analytify-pro') },
                  { value: 'ga:socialNetwork', label: __('Social Network', 'wp-analytify-pro') },
                  { value: 'ga:country', label: __('Country', 'wp-analytify-pro') },
                  { value: 'ga:countryIsoCode', label: __('Country Iso Code', 'wp-analytify-pro') },
                  { value: 'ga:browser', label: __('Browser', 'wp-analytify-pro') },
                  { value: 'ga:browserVersion', label: __('Browser Version', 'wp-analytify-pro') },
                  { value: 'ga:operatingSystem', label: __('Operating System', 'wp-analytify-pro') },
                  { value: 'ga:operatingSystemVersion', label: __('Operating System Version', 'wp-analytify-pro') },
                  { value: 'ga:mobileDeviceBranding', label: __('Mobile Device Branding', 'wp-analytify-pro') },
                  { value: 'ga:mobileDeviceModel', label: __('Mobile Device Model', 'wp-analytify-pro') },
                  { value: 'ga:mobileInputSelector', label: __('Mobile Input Selector', 'wp-analytify-pro') },
                  { value: 'ga:mobileDeviceInfo', label: __('Mobile Device Info', 'wp-analytify-pro') },
                  { value: 'ga:mobileDeviceMarketingName', label: __('Mobile Device Marketing Name', 'wp-analytify-pro') },
                  { value: 'ga:deviceCategory', label: __('Device Category', 'wp-analytify-pro') },
                  { value: 'ga:flashVersion', label: __('Flash Version', 'wp-analytify-pro') },
                  { value: 'ga:javaEnabled', label: __('Java Enabled', 'wp-analytify-pro') },
                  { value: 'ga:language', label: __('Language', 'wp-analytify-pro') },
                  { value: 'ga:screenColors', label: __('Screen Colors', 'wp-analytify-pro') },
                  { value: 'ga:screenResolution', label: __('Screen Resolution', 'wp-analytify-pro') },
                  { value: 'ga:hostname', label: __('Hostname', 'wp-analytify-pro') },
                  { value: 'ga:pagePath', label: __('Page Path', 'wp-analytify-pro') },
                  { value: 'ga:pageTitle', label: __('Page Title', 'wp-analytify-pro') },
                  { value: 'ga:landingPagePath', label: __('Landing Page Path', 'wp-analytify-pro') },
                  { value: 'ga:secondPagePath', label: __('Second Page Path', 'wp-analytify-pro') },
                  { value: 'ga:exitPagePath', label: __('Exit Page Path', 'wp-analytify-pro') },
                  { value: 'ga:previousPagePath', label: __('Previous Page Path', 'wp-analytify-pro') },
                  { value: 'ga:nextPagePath', label: __('Next Page Path', 'wp-analytify-pro') },
                  { value: 'ga:pageDepth', label: __('Page Depth', 'wp-analytify-pro') },
                ]}
              />
            </PanelRow>
            <PanelRow>
              <SelectControl
                multiple
                help="command+click to select multiple values"
                id="analytify-advanced-shortcode-visibleto"
                label={__('Visible to', 'wp-analytify-pro')}
                value={visibleToMultiple}
                onChange={(visibleToMultiple) => setAttributes({ visibleToMultiple })}
                options={[
                  { value: '', label: __('Everyone', 'wp-analytify-pro') },
                  { value: 'admin', label: __('Admininstrator', 'wp-analytify-pro') },
                  { value: 'editor', label: __('Editor', 'wp-analytify-pro') },
                  { value: 'author', label: __('Author', 'wp-analytify-pro') },
                  { value: 'contributer', label: __('Contributer', 'wp-analytify-pro') },
                ]}
              />
            </PanelRow>
            <PanelRow>
              <SelectControl
                id="analytify-advanced-shortcode-sortby"
                label={__('Sort By', 'wp-analytify-pro')}
                value={sortBy}
                onChange={(sortBy) => setAttributes({ sortBy })}
                options={[
                  { value: '', label: __('None', 'wp-analytify-pro') },
                  { value: 'current', label: __('Current Page', 'wp-analytify-pro') },
                  { value: '-ga:sessions', label: __('Sessions', 'wp-analytify-pro') },
                  { value: '-ga:users', label: __('Users', 'wp-analytify-pro') },
                  { value: '-ga:newUsers', label: __('New Users', 'wp-analytify-pro') },
                  { value: '-ga:percentNewSessions', label: __('Percent New Sessions', 'wp-analytify-pro') },
                  { value: '-ga:bounces', label: __('Bounces', 'wp-analytify-pro') },
                  { value: '-ga:bounceRate', label: __('Bounce Rate', 'wp-analytify-pro') },
                  { value: '-ga:hits', label: __('Hits', 'wp-analytify-pro') },
                  { value: '-ga:pageValue', label: __('Page Value', 'wp-analytify-pro') },
                  { value: '-ga:entrances', label: __('Entrances', 'wp-analytify-pro') },
                  { value: '-ga:pageviews', label: __('Pageviews', 'wp-analytify-pro') },
                  { value: '-ga:uniquePageviews', label: __('Unique Pageviews', 'wp-analytify-pro') },
                  { value: '-ga:exits', label: __('Exits', 'wp-analytify-pro') },
                  { value: '-ga:entranceRate', label: __('Entrance Rate', 'wp-analytify-pro') },
                  { value: '-ga:pageviewsPerSession', label: __('Pageviews Per Session', 'wp-analytify-pro') },
                  { value: '-ga:entranceRate', label: __('Entrance Rate', 'wp-analytify-pro') },
                  { value: '-ga:timeOnPage', label: __('Time On Page', 'wp-analytify-pro') },
                ]}
              />
            </PanelRow>
            <PanelRow>
              <SelectControl
                id="analytify-advanced-shortcode-analyticsfor"
                label={__('Analytics For', 'wp-analytify-pro')}
                value={analyticsFor}
                onChange={setAnalyticsFor}
                options={[
                  { value: 'current', label: __('Current Page', 'wp-analytify-pro') },
                  { value: 'full', label: __('Full Site', 'wp-analytify-pro') },
                  { value: 'page_id', label: __('Page ID', 'wp-analytify-pro') },
                ]}
              />
            </PanelRow>
            <PanelRow>
              <TextControl
                className={toggleClasses}
                type='number'
                placeholder="Enter post ID"
                value={customPostID}
                onChange={(customPostID) => setAttributes({ customPostID })}
              />
            </PanelRow>
            <PanelRow>
              <label className="analytify-date-picker-label">
                {__('Start Date', 'wp-analytify-pro')}
              </label>
            </PanelRow>
            <PanelRow>
              <DateTimePicker
                id="high-contrast-form-toggle"
                currentDate={startDate}
                onChange={(startDate) => setAttributes({ startDate })}
                is12Hour={true}
              />
            </PanelRow>
            <PanelRow>
              <label className="analytify-date-picker-label">
                {__('End Date', 'wp-analytify-pro')}
              </label>
            </PanelRow>
            <PanelRow>
              <DateTimePicker
                currentDate={endDate}
                onChange={(endDate) => setAttributes({ endDate })}
                is12Hour={true}
              />
            </PanelRow>
            <PanelRow>
              <TextControl
                label="Max Records"
                type="number"
                value={maxRecords}
                onChange={(maxRecords) => setAttributes({ maxRecords })}
              />
            </PanelRow>
            <PanelRow>
              <Button
                isDefault
                onClick={generateAdvancedShortcode}
              >
                {__('Make Shortcode', 'wp-analytify-pro')}
              </Button>
            </PanelRow>
          </PanelBody>
        </InspectorControls>,
        <div>
          <RichText
            tagName="div"
            placeholder={__('Create shortcodes from the sidebar')}
            value={shortcodeRichtext}
            onChange={(shortcodeRichtext) => setAttributes({ shortcodeRichtext })}
          />
        </div>
      ];
    },
    save: props => {
      const { shortcodeRichtext } = props.attributes;
      return (
        <div>
          <div className="analytify-shortcodes-block">
            {shortcodeRichtext}
          </div>
        </div>
      );
    },
  },
);
