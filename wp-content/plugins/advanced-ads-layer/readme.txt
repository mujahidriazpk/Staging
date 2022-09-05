=== Advanced Ads – PopUp and Layer Ads ===
Requires at least: Advanced Ads 1.22, Advanced Ads Pro 2.10
Tested up to: 5.6
Stable tag: 1.7.4

Create Ads with a popup and layer effect.

== Distribution ==

The distribution of the software might be limited by copyright and trademark laws.
Copyright and trademark holder: Advanced Ads GmbH.
Please see also https://wpadvancedads.com/terms/.

== Description ==

Create Ads with a popup and layer effect.

*Layer and PopUp*

* display the ad after the user scrolls
* popup the ad as a layer over the content
* optional background overlay
* display the ad when the users wants to leave
* display the ad after x seconds
* display effects (show, fade, slide)
* hide the ad after x seconds
* choose between different positions for the popup

*Close Button*

* allow users to close an ad (not only layers)
* add timeout for closed ads
* choose between different positions for the close button

== Installation ==

The Layer Ads plugin is based on the free Advanced Ads plugin, a simple and powerful ad management solution for WordPress. Before using Layer Ads download, install and activate Advanced Ads for free from http://wordpress.org/plugins/advanced-ads/.
You can use Advanced Ads along any other ad management plugin and don’t need to switch completely.

== Changelog ==

= 1.7.4 =

- Fix: add frontend prefix for pop-up/layer close button

= 1.7.3 =

- Improvement: add Arabic, Danish, Polish, Spanish (Argentinia, Colombia, Mexico, Spain, Venezuela) translations
- Fix: remove warning about multiple popup placements when the Fancybox option is disabled

= 1.7.2 =

* moved close button outside of the ad label

= 1.7.1 =

* fix undefined `advads` JS global if privacy module is not enabled

= 1.7.0 =

* integrate with TCF 2.0 compatible consent management platforms

= 1.6.6 =

* prepared placement order for Advanced Ads 1.19

= 1.6.5 =

* fixed Ad Health notice disappearing on notices page
* prevented Fancybox close button from being placed beyond the screen when ad width is >= screen width

= 1.6.4 =

* integrate with Ad Health notices in the backend introduced in Advanced Ads 1.12
* updated message when the Advanced Ads basic plugin is not activated
* fixed CSS issue in placement settings
* fixed missing index issue

= 1.6.3 =

* fixed ad blocker detection – using dynamic classes for PopUp containers now
* added Italian translation
* updated French, German and Spanish translations

= 1.6.2 =

* added support for jQuery versions much different from WordPress core

= 1.6.1 =

* added a warning when an AdSense ad is assigned to the layer placement

= 1.6 =

* allowed to display ads only after x seconds
* allowed to automatically close after x seconds 
* allowed to close with click on the background

= 1.5.5 =

* made compatible WP Rocket’s script defer option without "Safe mode" enabled
* made close button work with passive cache-busting and groups
* fixed JavaScript error in Internet Explorer 11

= 1.5.4 =

* load layer placement also on AJAX calls in WP Admin
* fixed Fancybox layout for images and HTML codes
* removed old overview widget logic

= 1.5.3 =

* track ads with Analytics method only after they show up
* fix issues when cache-busting is set to 'auto' and ajax' fallback is used

= 1.5.2 =

* fix issue when frontend prefix equals 'advads-'
* make close button work with groups

= 1.5.1 =

* hotfix missing key issue

= 1.5 =

* converted placement options to new format
* center ads even when weight and height are not sent
* fixed empty timeout closing the ad only for the current page impression and not the session

= 1.4.1 =

* fixed issue when cache-busting module (Pro add-on) is disabled

= 1.4 =

* please update your license in Advanced Ads > Settings > Licenses to fix a license issue
* support Slider and groups with refresh interval enabled
* load JavaScript after JavaScript from cache-busting
* fixed error message when all placements were removed

= 1.3.2 =

* fixed fancybox being too large on small devices
* added French translation

= 1.3.1.3 =

* fix fancybox sometimes not displaying images in Firefox

= 1.3.1.2 =

* Spanish translation

= 1.3.1.1 =

* added German translation

= 1.3.1 =

* fixed empty wrapper causing layer not to show up
* removed unnecessary logging

= 1.3 =

* moved popup from ad settings to its own placement
* added fancybox support
* display the ad when the users wants to leave
* choose between different positions for the popup
* removed unneeded error log

= 1.2.3 =

* made close button independed from layer settings
* updated plugin links
* added plugin link to license page
* show warning if Advanced Ads is not installed

= 1.2.2 =

* moved license code to main plugin
* updated plugin link

= 1.2.1 =

* renamed class from main plugin
* fixed issue when close button appeared when layer was disabled

= 1.2.0 =

* added license key
* added auto updates
* added main plugin class

= 1.1.2 =

* added timeout for closed ads
* added constant for text domain
* changed link to plugin url
* updated the plugin overview widget

= 1.1.1 =

* moved layer js code from main plugin to here
* fixed js code
* fixed issue when main plugin is not loaded before the add-on

= 1.1.0 =

* added check if Advanced Ads is installed
* removed some features not only needed for layer ads to the main plugin
* use position settings from sticky ads plugin, if enabled
* don’t display background only if not yet exists
* added display effects (show, fade, slide)

= 1.0.0 =

* display ads after user scrolls
* optional background overlay

Build: 2022-03-1b115a36