=== Advanced Ads Slider ===
Contributors: webzunft
Tags: ads, ad, banner, adverts, advertisement, slider
Requires at least: 3.5, Advanced Ads 1.8.18, Advanced Ads Pro 2.0.3
Tested up to: 4.9
Stable tag: 1.4.3

Create a slider from your ads.
Add-on for https://wpadvancedads.com

== Copyright ==

Copyright 2015-2018, Thomas Maier, webgilde.com

This plugin is not to be distributed after purchase. Arrangements to use it in themes and plugins can be made individually.
The plugin is distributed in the hope that it will be useful,
but without any warrenty, even the implied warranty of
merchantability or fitness for a specific purpose.

== Description ==

This plugins adds a new ad group whose frontend output happens as a slider with rotating banners.

The slider is built on http://idiot.github.io/unslider/.

Other scripts used:

* https://github.com/stephband/jquery.event.swipe
* https://github.com/stephband/jquery.event.move

== Installation ==

The Slider plugin is based on the free Advanced Ads plugin, a simple and powerful ad management solution for WordPress. Before using this plugin download, install and activate Advanced Ads for free from http://wordpress.org/plugins/advanced-ads/.
You can use Advanced Ads along any other ad management plugin and don’t need to switch completely.

== Changelog ==

= 1.4.3 =

* updated scripts to latest versions

= 1.4.2 =

* made compatible without "Safe mode" in WP Rocket

= 1.4.1 =

* added more rules to prevent themes from overriding slider CSS

= 1.4 =

* changed loop animation to infinite
* removed old overview widget logic
* converted group options to new option format of Advanced Ads 1.7.26
* added more rules to prevent themes from overriding slider CSS

= 1.3.2 =

* "fade" animation type is now working
* minor css fix for some layouts

= 1.3.1 =

* slides are hidden until the script is loaded to prevent output of all slides by default

= 1.3 =

* introduced ADVANCED_ADS_SLIDER_USE_CDN constant to use main unslider scripts from CDN
* changed textdomain
* updated German translation

= 1.2.1 =

* compatibility with PHP prior 5.3

= 1.2 =

* added option to randomize order of ads
* stop slider on mouseover
* added French translation

= 1.1.2 =

* allows multiple instances of the same slider on the same page

= 1.1.1 =

* compatibility with Pro passive cache-busting

= 1.1 =

* updated unslider script and options
* added swipe feature
* set a fixed slider id
* use ID prefix for slider id and class
* added Spanish translation
* added German translation

= 1.0.6 =

* hide number of ads setting in the group, because it confuses too much

= 1.0.5 =

* updated plugin link
* show warning if Advanced Ads is not installed
* made css global for every slider

= 1.0.4 =

* removed unneeded js file
* fixes to now display all slides before whole slider is loaded

= 1.0.3 =

* added class attribute to slider box
* change content of slider add-on widget on overview page if add-on is installed

= 1.0.2 =

* test ad group type before building the slider

= 1.0.1 =

* fixed issue when group has just one item

= 1.0.0 =
* first plugin version
