=== Advanced Ads – Sticky Ads ===
Requires at least: Advanced Ads 1.22, Advanced Ads Pro 2.8.2, Tracking 1.19
Tested up to: 5.6
Stable tag: 1.8.3

Sticky Ad allows to stick an ad to a position in the browser window and scroll with the content.

== Distribution ==

The distribution of the software might be limited by copyright and trademark laws.
Copyright and trademark holder: Advanced Ads GmbH.
Please see also https://wpadvancedads.com/terms/.

== Description ==

Sticky Ads (or Anchor Ads for mobile) have proven to increase the income from click or performance based ads. This method is even more interesting on mobile devices, because users are already used to ads being fixed when using native apps.

The Sticky Ads plugin provides this functionality for those ads and ad networks that don’t offer ads that are fixed on the screen, but allow them.

Features

* position ads over the content so they don’t scroll
* header bar placement
* footer bar placement
* sidebar ads attached to the main wrapper placement
* sidebar ads attached to the window placement
* make ads sticky to the screen or let them be scrolled away
* add background color for header and footer bars
* allow users to close sticky placements for a specific time
* center side ads vertically
* fallback for fixed ads in case browsers don’t support this
* set delay in seconds after which the ad should show up
* display with effects (show, fade, slide)

== Installation ==

The Sticky Ads plugin is based on the free Advanced Ads plugin, a simple and powerful ad management solution for WordPress. Before using Sticky Ads download, install and activate Advanced Ads for free from http://wordpress.org/plugins/advanced-ads/.
You can use Advanced Ads along any other ad management plugin and don’t need to switch completely.

== Changelog ==

= 1.8.3 =

* Fix: add frontend prefix for close button

= 1.8.2 =

* add Arabic and Danish translations
* update French, Spanish and Vietnamese translations

= 1.8.1 =

* moved close button outside of the ad label

= 1.8.0 =

* prevented close button from being covered by expanded to full width GAM ad
* integrate with TCF 2.0 compatible consent management platforms

= 1.7.12 =

* use content picker introduced in Advanced Ads 1.19

= 1.7.11 =

* preparations for Advanced Ads 1.19

= 1.7.10 =

* removed warning if AdSense is used in a sticky position since most of them are allowed now
* Footer Bar placement: prevented ads having width more than half screen width from being small

= 1.7.9 =

* compatibility with "Reload ads on resize" feature of Responsive add-on
* scale down images having "max-width: 100%" and delivered through "Left Sidebar" placement

= 1.7.8 =

* randomized container ID to harden ad blocker detection
* consider paddings of "Parent Element" of sidebar placements when cache-busting is used

= 1.7.7 =

* made 'image' ads work correctly with fixed to window position placements

= 1.7.6 =

* prevented ads from being reloaded on mobile devices
* added Italian translation
* updated French translation

= 1.7.5 =

* added a warning when an AdSense ad is assigned to a sticky placement

= 1.7.4 =

* increased sticky z-index from 1000 to 10000
* updated German translation

= 1.7.3 =

* update position after browser width is changed
* made fixed ads visible on Opera Mini
* fixed German name of the plugin

= 1.7.2 =

* made compatible without "Safe mode" in WP Rocket

= 1.7.1 =

* allow to set trigger and effect for Left/Right Sidebar placement
* updated Spanish translation
* fixed issue with groups and passive cache-busting

= 1.7 =

* prevent JavaScript error when close button is used
* moved overview widget logic to basic plugin
* use top level wrapper id with passive cache-busting
* support new version of Group refresh feature

= 1.6.2 =

* track ads with Analytics method only after they show up
* fixed incorrect behavior of group ad type

= 1.6.1 =

* minor fixes for positioning ads and textdomains

= 1.6 =

* converted placement options to new format
* fixed empty timeout closing the ad only for the current page impression and not the session
* fixed element selector if Pro is enabled at the same time
* fixed effects
* do not require width for Left Sidebar and Right Sidebar placements
* add position: relative to a parent element when Left Sidebar and Right Sidebar placements are used

* 1.5 *

* show error in console when ad width is missing for some sticky placements
* adjusted text domain
* support Slider and groups with refresh interval enabled
* move empty cache-busting wrapper instead of ad/group wrapper
* fixed error message when all placements were removed

= 1.4.7 =

* fixed sticky ads with timeout
* added Spanish translation
* added French translation

= 1.4.6 =

* fixed waiting period ignored if there is a background color

= 1.4.5 =

* added events that trigger ad to show up
* added effects
* fixed ads with background not being centered

= 1.4.4 =

* fixed centered sticky not being centered for dynamic ads

= 1.4.3 =

* fixed repeated option fields for some placement
* fixed issue with z-index

= 1.4.2 =

* fixed issue with multiple can-display conditions for placements

= 1.4.1 =

* allow users to close the sticky placement for a specific time
* added option to center side ads vertically
* sticky ads now work with cache-busting

= 1.4 =

* fixed sidebar ads with cache-busting
* added frontend picker to select position for sidebar ads
* force advanced js file to be activated without bothering the user

= 1.3 =

* show warning if Advanced Ads is not installed
* added header bar and footer bar placement
* added placements to attach sticky to another element – sticky or floating
* added placements to stick ads to window
* added background color for header and footer bars
* added warning if advanced js is not enabled
* fixed wrong license key index
* updated plugin link
* translated into German
* applied WP coding standards

= 1.2.2 =

* updated all class names from "Advads_" to "Advanced_Ads_"
* moved licensing code to main plugin
* fixed plugin link
* added plugin link to license page

= 1.2.1 =

* fixed fallback method for incompatible browsers

= 1.2.0 =

* added license key
* added auto updates
* added main plugin class
* moved settings

= 1.1.3 =

* changed slug under which the settings are saved
* added constant for text domain
* changed link to plugin url
* updated the plugin overview widget

= 1.1.2 =

* fixed broken custom position
* fixed check for old deviced based on code no longer in sticky ads
* fixed issue if base plugin is not loaded before the add-on

= 1.1.1 =
* added check if Advanced Ads is installed
* removed some features not only needed for sticky ads

= 1.1.0 =

* setting layout optimized
* display ads only for a specific browser width
* display ads after user scrolls
* optional background overlay

= 1.0.1 =

* moved sticky ads parameters to own metabox
* fixed bug when making ad unsticky again

= 1.0 =
* first plugin version with sticky ads browser and browser test

Build: 2022-03-21d08b01