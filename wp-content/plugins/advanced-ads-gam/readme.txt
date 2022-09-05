=== Advanced Ads – Google Ad Manager Integration ===
Requires at least: 5.0, Advanced Ads 1.26.0
Tested up to: 5.8
Requires PHP: 5.6
Stable tag: 1.4.2

Load and place ads from Google Ad Manager on your website.

== Copyright ==

Copyright 2021 Advanced Ads GmbH, https://wpadvancedads.com/

This plugin is not to be distributed after purchase. Arrangements to use it in themes and plugins can be made individually.
The plugin is distributed in the hope that it will be useful,
but without any warranty, even the implied warranty of
merchantability or fitness for a specific purpose.

== Description ==

The Google Ad Manager integration for WordPress connects your site with the popular ad server by Google. It allows you to place the ad units on your website without touching any codes or taking care of the complicated mix of header and body tags they normally ask you to implement.

If you don’t know, Google Ad Manager (previously called Google DoubleClick for Publishers, DFP) handles ad management in the cloud and is free for small and medium websites.

For more details see https://wpadvancedads.com/add-ons/google-ad-manager/

== Changelog ==

= 1.4.2 =

- Fix: import button missing directly after connecting the account
- Fix: connect to GAM accounts that use multiple networks

= 1.4.1 =

- Improvement: update translations (German, Italian, Danish)
- Fix: add two Ad sizes rows instead of one in the backend form

= 1.4.0 =

- Feature: import multiple ad units from the GAM account at the same time
- Feature: new option to specify ad unit sizes for AMP pages
- Improvement: change description for Post Meta key/values targeting to avoid confusion with same terms in GAM and WordPress
- Fix: GAM ads now load correctly when switching from the AdSense ad type

= 1.3.1 =

- updated GAM API version
- show a notice when an ad unit was previously loaded from a different network

= 1.3.0 =

- show a warning when fluid ads are using floated position
- added note when the GAM account size exceeds 1500 ads

= 1.2.0 =

- switch to use web authentication to connect with the GAM account
- add basic responsive behavior to the key-values option table
- fixed adding a new line to ad sizes when the last one was removed

= 1.1.1 =

- fixed wrongly escaped output for automatically filtered sizes

= 1.1.0 =

- implemented interface for Ad Sizes to create responsive Google Ad Manager ads
- implemented interface for Key-values Targeting
- added support for native (fluid) ads
- show the date of the last update below the ad unit list
- fixed issue when switching from plain text ad type to GAM ad type

= 1.0.3 =

- fixed missing variable
- fixed type casting bug
- fixed occasional critical issue while registering the "connect" menu item

= 1.0.2 =

- added relevant links after the user enables the add-on for the first time
- updated GAM API version

= 1.0.1 =

- improved wording of missing API error message
- fixed license check

= 1.0 =

- allow GAM connection on servers without soap module
- added German translations
- fix an error for Google accounts without access to GAM

= 0.9 =

- first plugin version

Build: 2021-12-ff131751