=== Float to Top Button ===
Contributors: CAGE Web Design | Rolf van Gelder
Donate link: http://cagewebdev.com/donations-fttb/
Plugin Name: Float to Top Button
Plugin URI: http://cagewebdev.com/float-to-top-button
Tags: jQuery, floating button, button, to top, scroll to top, scrolling, scrollup
Author URI: http://rvg.cage.nl
Author: CAGE Web Design | Rolf van Gelder, Eindhoven, The Netherlands
Requires at least: 2.8
Tested up to: 5.5.2
Stable tag: 2.3.6
Version: 2.3.6
License: GPLv2 or later

== Description ==

= This plugin automatically adds a sticky 'Go to Top' button to posts and pages =

It's a simple, configurable wrapper for Mark Goodyear's 'scrollUp' jQuery plugin:<br>
https://github.com/markgoodyear/scrollup<br>

You can configure it via:<br>
WP Admin Panel &raquo; Settings &raquo; Float to Top Button

= Author =
CAGE Web Design | Rolf van Gelder, Eindhoven, The Netherlands - http://cagewebdev.com - http://rvg.cage.nl

= Plugin URL =
http://cagewebdev.com/float-to-top-button

= Download URL =
http://wordpress.org/plugins/float-to-top-button/

= Supported languages =
* Czech   [cz_CZ] - translated by Tomas Tucek - http://www.tomoviny.cz/
* Dutch   [nl_NL] - translated by Rolf van Gelder, CAGE Web Design - http://cagewebdev.com
* English [en_US] - translated by Rolf van Gelder, CAGE Web Design - http://cagewebdev.com
* German  [de_DE] - translated by Lutz Dausend - http://www.proaspecto.com/
* Hebrew  [he_IL] - translated by Ahrale Shrem, atar4u.com - http://atar4u.com

= Disclaimer =
NO WARRANTY, USE IT AT YOUR OWN RISK!

= Plugins by CAGE Web Design | Rolf van Gelder =
WordPress plugins created by CAGE Web Design | Rolf van Gelder<br>
http://cagewebdev.com/category/news-tech-art/wordpress/

== Installation ==

* Upload the Plugin to the '/wp-content/plugins/' directory
* Activate the plugin in the WP Admin Panel &raquo; Plugins 

== Frequently asked questions ==

= How can I change the settings of this plugin? =
* WP Admin Panel &raquo; Settings &raquo; Float to Top Button

= How do I hide the button on a specific post / page? =
* Add the following custom field to the post / page: fieldname = 'hide_fttb', value = 'Y'

== Changelog ==
= v2.3.6 [10/21/2020] =
* BUG FIX: Added a noConflict() for jQuery

= v2.3.5 [09/16/2020] =
* BUG FIX: Settings bug fixed

= v2.3.4 [05/10/2020] =
* NEW: Option for disabling the button on desktops / laptops

= v2.3.3 [06/09/2018] =
* CHANGE: Several minor changes

= v2.3.2 [02/06/2018] =
* NEW: German translation added [de_DE]

= v2.3.1 [11/15/2017] =
* CHANGE: Detailed the horizontal and vertical spacing (settings page)

= v2.3 [09/15/2017] =
* NEW: Hide the button on specific posts / pages using the custom field 'hide_fttb'
* CHANGE: Some code cleanup

= v2.2 [06/29/2017] =
* BUG FIX: Fixed the 'distance from top setting' issue
* CHANGE: Upgraded the ScrollUp jQuery plugin to v2.4.1
* CHANGE: Some code cleanup

= v2.1.2 [11/29/2016] =
* BUG FIX: Fixed the title attribute of the button
* CHANGE: Some minor changes

= v2.1.1 [04/17/2016] =
* NEW: Czech translation added [cz_CZ] (thank you, Tomas Tucek!)

= v2.1 [04/15/2016] =
* BUG FIX: Bug fix for IE 9 (opacity while hovering over the button)

= v2.0.9 [12/17/2015] =
* CHANGE: Some minor changes / updates

= v2.0.8 [09/12/2015] =
* BUG FIX: Bug fix for the custom image url

= v2.0.7 [09/07/2015] =
* NEW: Custom arrow image (via an URL)
* NEW: Option to set the Z-index of the overlay
* CHANGE: Inline JavaScript has been localized now (thanks, summatix!)

= v2.0.6 [08/22/2015] =
* CHANGE: Minified the .js and .css file

= v2.0.5 [07/24/2015] =
* CHANGE: The spacing option has been split up into horizontal and vertical spacing

= v2.0.4 [07/10/2015] =
* BUG FIX: Under certain circumstances the button didn't show up

= v2.0.3 [07/09/2015] =
* NEW: Configurable position, spacing, opacity mouseout, opacity mouseover
* NEW: More images added
* CHANGE: Images moved from css/img to images
* CHANGE: Image size doesn't need to be 38x38px anymore
* CHANGE: Image name doesn't have to be arrowXXX.png (must be .png but any name is fine)

= v2.0.2 [07/06/2015] =
* CHANGE: Sanitized the Settings Form + other minor changes

= v2.0.1 [06/17/2015] =
* BUG FIX: Default settings were not initialized on first run

= v2.0 [06/12/2015] =
* MAJOR UPGRADE: Total revamp of the code (thanks Matt!)

= v1.2.1 [06/08/2015] =
* CHANGE: Styles won't load on a mobile device when mobile devices are disabled

= v1.2.0 [06/07/2015] =
* NEW: Added a new option for disabling the button on mobile devices

= v1.1.6 [05/23/2015] =
* NEW: Form validation added to the settings form
* NEW: Settings link on main plugin page
* CHANGE: Updated the layout of the settings page
* CHANGE: Several (minor) changes

= v1.1.5 [05/11/2015] =
* NEW: MouseOver added
* CHANGE: Several (minor) changes

= v1.1.4 [03/15/2015] =
* NEW: Localization added
* NEW: Dutch translation (nl_NL) added
* NEW: Hebrew translation (he_IL) added
* NEW: more arrow icons added
* CHANGE: several minor changes / improvements

= v1.1.3 [11/23/2014] =
* BUG FIX: scripts are now loaded with jQuery dependency

= v1.1.2 [11/17/2014] =
* BUG FIX: scripts and stylesheets won't be loaded on the login page

= v1.1.1 [11/10/2014] =
* Some minor bug fixes

= v1.1 [11/08/2014] =
* Initial release
