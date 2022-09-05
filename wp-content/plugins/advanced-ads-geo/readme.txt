=== Advanced Ads – Geo Targeting ===
Requires at least: 4.9, Advanced Ads 1.23
Tested up to: 5.6
Stable tag: 1.3.3

Display ads based on the geo location of the visitor.

== Copyright ==

Copyright 2014-2021, Advanced Ads GmbH, https://wpadvancedads.com/

This plugin is not to be distributed after purchase. Arrangements to use it in themes and plugins can be made individually.
The plugin is distributed in the hope that it will be useful,
but without any warrenty, even the implied warranty of
merchantability or fitness for a specific purpose.

== Description ==

Using the Geo Targeting add-on you can display ads based on the location of your visitors.

**Features**

* adds a visitor condition to select the geo location a visitor must come from in order to see / not see an ad
* target visitors by country
* target visitors by region/state
* target visitors by city
* target visitors by continent
* target visitors from European Union
* target visitors in a given radius around a location

**Methods**

* MaxMind DB (default)
* Sucuri Header (if Sucuri product is used)

**Attributions**

* GeoLite2 data by MaxMind
* coordinates lookup by Nominatim, https://nominatim.openstreetmap.org/

== Installation ==

Geo Targeting is based on the free Advanced Ads plugin, a simple and powerful ad management solution for WordPress. Before using this plugin download, install and activate Advanced Ads for free from http://wordpress.org/plugins/advanced-ads/.
You can use Advanced Ads along any other ad management plugin and don’t need to switch completely.

== Changelog ==

= 1.3.3 =

- removed UK from the "European Union" geo-location due to Brexit
- updated strings to indicate that there are two database files needed

= 1.3.2 =

* fixed usage of unprefixed database names
* fixed downloading the databases even when the default /tmp folder does not have enough space

= 1.3.1 =

* fixed JavaScript issue preventing the "distance" option for geo-targeting from working

= 1.3 =

After MaxMind changed their terms, it is now needed to create a free MaxMind account to download the database. See https://wpadvancedads.com/manual/geo-targeting-condition/#Enabling_Geo-Targeting

* updated download form
* fixed wide 'select' elements in conditions that broke layout

= 1.2.2 =

* renamed continent "Australia" to "Oceania". You must resave ads using that condition
* fixed issue when IP consists of two numbers

= 1.2.1 =

* made compatible with improved cache-busting in Advanced Ads Pro 2.3
* link to the geo lookup page for radius option if API doesn’t work
* prevent wrong IP format set by CloudFlare

= 1.2 =

* added radius option to select a place around certain coordinates
* added translations (Italian, French)
* updated translations (German, German formal, Spanish)

= 1.1.10 =

* prevented conflict with other geo targeting plugin

= 1.1.9 =

* prevent conflict with WooCommerce using MaxMind now

= 1.1.8 =

* prevent MaxMind PHP library from loading if already loaded by another plugin or theme

= 1.1.7 =

* introduced `advanced-ads-geo-upload-dir` filter to change the upload directory
* download Geo DB in HTTPS only
* added Sucuri Header method
* handle cases in which multiple IPv6 addresses are given at the same time

= 1.1.6 =

* fixed DB update check
* removed old overview widget logic

= 1.1.5 =

* fixed minor errors when data is not available for a position

= 1.1.4 ==

* upper/lower case doesn’t matter anymore when checking regions or cities
* add constant `ADVANCED_ADS_GEO_CHECK_DEBUG` to `wp-config.php` in order to log all tests in `wp-content/geo-check.log`
* fixed bug not checking regions

= 1.1.3 =

* don’t throw error message when IP was not found
* made the plugin compatible with Advanced Ads 1.7.16
* updated Spanish translation

= 1.1.2 =

* filter IP address for valid format
* prevent errors when IP address is empty

= 1.1.1 =

* added link to settings when database is missing in visitor conditions
* updated German translation

= 1.1 =

* implemented check for states/regions
* allow state/region and city names in different languages
* added one click installation for Advanced Ads
* updated German translation

= 1.0.6 =

* fixed static var error message

= 1.0.5 =

* request users location only once, even when there are multiple geo checks on a page
* removed code deprecated with Advanced Ads 1.7.1

= 1.0.4 =

* made IP check compatible with CloudFlare

= 1.0.3 =

* moved error logging to debug.log file
* added Spanish translation

= 1.0.2 =

* fixed issue with cache-busting

= 1.0.1 =

* fixed license validation and database update error

= 1.0.0 =

* first plugin version
* added geo targeting by country

Build: 2021-12-d6ba5c4f