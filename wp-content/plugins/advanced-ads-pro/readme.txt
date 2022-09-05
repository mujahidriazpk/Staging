=== Advanced Ads Pro ===
Requires at least: WP 4.9
Tested up to: 5.9
Requires PHP: 5.6
Stable tag: 2.16.0

Advanced Ads Pro is for those who want to perform magic on their ads.

== Distribution ==

The distribution of the software might be limited by copyright and trademark laws.
Copyright and trademark holder: Advanced Ads GmbH.
Please see also https://wpadvancedads.com/terms/.

== Description ==

Advanced Ads Pro extends the free version of Advanced Ads with additional features that help to increase revenue from ads.

Features:

* check delivered ads within the admin bar in the frontend
* cache-busting to lazy load ads on cached pages
* test placements against each other
* option to limit an ad to be displayed only once per page
* refresh ads without reloading the page
* select ad-related user role for users
* inject ads into any content which uses a filter hook
* click fraud protection
* alternative ads for ad block users
* lazy loading
* place custom code after an ad
* disable all ads by post type
* serve ads on other websites

placements

* use display and visitor conditions in placements
* pick any position for the ad in your frontend
* inject ads between posts on posts lists, e.g. home, archive, category
* inject ads based on images, tables, containers, quotes and any headline level in the content
* ads on random positions in posts (fighting ad blindness)
* ads above the main post headline
* ads in the middle of a post
* background / skin ads
* set a minimum content length before content injections are happending
* set a minimum amount of words between ads injected into the content
* dedicated placements for bbPress
* dedicated placements for BuddyPress
* show ads from another blog in a multisite
* repeat content placement injections
* allow Post List placement in any loop on static pages
* ad server to embed ads on other websites

display and visitor conditions:

* display ads based on where the user comes from (referrer)
* display ads based on the user agent (browser)
* display ads based on url parameters (request uri)
* display ads based on user capability
* display ads based on the browser language
* display ads based on number of previous page impressions
* display ads based on number of ad impressions per period
* display ads to new or recurring visitors only
* display ads based on a set cookie
* display ads based on page template
* display ads based on post meta data
* display ads based on post parent
* display ads based on the day of the week
* display ads based on language of the page set with WPML
* display ads based on GamiPress points, ranks, and achievements

== Installation ==

Advanced Ads Pro is based on the free Advanced Ads plugin, a simple and powerful ad management solution for WordPress.
You can use Advanced Ads along any other ad management plugin and don’t need to switch completely.

== Changelog ==

= 2.16.0 =

- Feature: add the "post content" display condition
- Improvement: update German and Arabic translations
- Fix: reset page impressions number when the cookie that stores it expires

= 2.15.0 =

- Feature: add GamiPress visitor conditions based on achievements, ranks, and points
- Improvement: add mobile click detection on Auto Ads to the Click fraud protection
- Fix: ensure compatibility with the Flex Mag theme when a custom position placement is used
- Fix: resolve a JS error on the ad edit pages when cache busting is enabled
- Fix: sanitize and verify additional user roles before saving
- Fix: show ads that do not use cache busting in "Ads" menu
- Fix: make it possible to use the Group Refresh feature without placements
- Fix: decode ads for TCF if cache busting is not enabled

= 2.14.1 =

- Fix: resolve a bug that prevents BuddyPress and bbPress modules from working

= 2.14.0 =

- Feature: suggest text for user’s privacy policy under Settings > Privacy > Policy Guide
- Improvement: minify Click Fraud Protection module's JavaScript file
- Improvement: update German and Danish translations
- Fix: correct typo in the handle when enqueuing "advanced-ads-pro.js"
- Fix: consider the `inline` attribute for ads added via shortcode if cache-busting wrapper is needed
- Fix: resolve a bug that prevents blog_id attributes from working

= 2.13.0 =

- Improvement: move JavaScript files that are used for more than cache-busting out of the cache-busting module folder
- Improvement: increase word counter precision of the "Minimum Content Length" feature
- Improvement: move script files to the footer by default to increase performance score
- Improvement: remove cache-busting script files when cache-busting is disabled and no Custom Position placement exists
- Fix: ensure that the Ad Admin role can save options

= 2.12.1 =

- remove `inline-css` filter for passive cache-busting

= 2.12.0 =

- warn if an Advanced Ads widget does not use cache-busting
- remove `cursor: pointer` for background placements on AMP
- refresh ads on the same spot: hide current and subsequent ads after clicking on close button of Sticky placement
- respect wp_timezone settings when displaying ads on certain days only
- made Click Fraud Protection work for Google AdSense Auto Ads
- further minimized AJAX cache-busting footprint in footer
- BuddyBoss: added BuddyBoss Group display condition
- BuddyBoss placement: made possible to customize activity type
- BuddyBoss placement: made possible to repeat position
- made "url parameters" condition work in AJAX requests initiated by third party plugins
- made possible to import and export options

= 2.11.0 =

- disabled cache-busting when not needed for groups
- removed deprecated Flash module
- replaced deprecated jQuery functions
- fixed passive cache-busting for Specific Days and CFP
- fixed ad label when "Visitor profile" is enabled
- Click Fraud Protection: allow using the same ad multiple times on page
- Click Fraud Protection: remove ad after first click when only one click is allowed
- removed duplicated entries from AJAX cache-busting array in footer


= 2.10.3 =

- send placement tests email from admin email when "SERVER_NAME" is undefined
- added `advanced-ads-output-wrapper-before-content-group` hook used by other add-ons
- fire event after dynamically inserted ads (e.g. lazy-loading, infinite-scroll) have been decoded

= 2.10.2 =

- made Background placement work with AJAX cache-busting
- cache-busting wrappers now have static placement classes

= 2.10.1 =

- decode ads that are loaded with infinite scroll and need consent
- fixed some combinations of cache-busting, tracking, and TCF consent
- fixed "advadsProCfp is not defined" error when clicking on a click-protected ad before the page fully loaded
- fixed compatibility between "Words Between Ads" and the TCF v2.0 integration

= 2.10.0 =

- auto hide all ads after Click Fraud Protection is triggered
- Click Fraud Protection: use module-wide or individual ad click limit, whichever is more strict
- prevented displaying some warnings by amp validator
- integrate with TCF 2.0 compatible consent management platforms

= 2.9 =

- added more string compare options to the Cookie visitor condition
- added BuddyBoss placement to inject ads into the activity stream
- switched element picker for Custom Position placement when using Advanced Ads 1.19
- auto-save placement page after parent element was selected for Custom Position

= 2.8.2 =

- backend UI improvements to module activation and date fields
- prepare for Advanced Ads 1.19
- removed unneeded debug line from Browser Console
- fixing incorrect symbols in numeric fields automatically

= 2.8.1 =

* open ads loaded through the Ad Server automatically in a new window to prevent loading the target page in an iframe
* changed behavior of injection based on img tags to look for any images in the content except within tables
* Cache busting: made possible to use html attributes that contain JSON strings
* fixed error that happened when applying Random Paragraph placement to one-paragraph text
* don't take into account the "Words Between Ads" setting when inserting a first ad

= 2.8 =

* New: Ad Server placement to embed ads on other websites
* New: show Post List placement on archive pages created by the AMP for WP plugin
* made placements of type other than "Header Code" work with "Thrive Theme Builder" theme
* shift ads from bottom when "repeat the position" and "words between ads" settings are in use
* marked Flash module as deprecated. New users can no longer enable it. Find the schedule [here](https://wpadvancedads.com/manual/deprecated-features/#Pro_%3E_Flash_ad_type)
* removed legacy code for URL Parameter visitor conditions since it moved to display conditions in 2016
* removed legacy code for minimum content length option as set before 2016 in the main plugin settings
* disallowed ad insertion into the header of the WP File Manager's admin page

= 2.7.1 =

* Group Refresh feature: prevented impression tracking when it is disabled in the Tracking add-on
* fixed Custom Position placement showing in the footer when selector does not exist
* fixed broken link in the description of the User Agent condition

= 2.7 =

* use Display and Visitor Conditions in placements
* allow content injection based on iframe tag
* set minimum amount of words between ads injected into the content
* show the link to duplicate an ad only when the ad was already saved once
* moved output of "Custom Code" outside the link
* fixed clearfix option of Custom Position placement
* fixed wide 'select' elements in conditions that broke layout
* fixed possible bug that prevented Pro settings from being saved

= 2.6.2 =

* added `advanced_ads_pro_output_custom_code` filter to manipulate the Custom Code option
* prevented returning default language in the WPML plugin when AJAX cache-busting is used
* prevented reset of the "Disable ads for post types" option when saving Pro settings
* fix "Disable ads for post types" option when using AJAX cache-busting
* fixed possible PHP warning

= 2.6.1 =

* fixed a minify-related bug that prevented some Custom Position placement from working

= 2.6 =

* new feature: duplicate ads
* load group name to Cache Busting code as per request by a customer

Build: 2022-02-4e35a006