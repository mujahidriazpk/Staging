=== Advanced Ads – Selling Ads ===
Requires at least: 5.0, Advanced Ads 1.21.0
Tested up to: 5.7
Stable tag: 1.3.1

Let users purchase ads directly on the frontend of your site.

** Features **

* define “ad products” advertisers can choose from
* sell ads per day, impressions, clicks, or custom conditions
* set the placement the ad product will be visible on
* allow advertisers to upload their data after the purchase, to improve conversion
* informs publishers when all ad details are uploaded and the ad can be reviewed
* advertisers have their own account and see their purchases and links to their ad stats
* built on WooCommerce and therefore extendable with most of their add-ons
* pay with PayPal, Stripe, invoice, or any other payment method available with WooCommerce

** Dependencies **

* Tracking add-on to sell per impressions or clicks
* WooCommerce as the underlying e-commerce solution

== Copyright ==

Copyright 2014-2021, Thomas Maier, Advanced Ads GmbH, https://wpadvancedads.com/

This plugin is not to be distributed after purchase. Arrangements to use it in themes and plugins can be made individually.
The plugin is distributed in the hope that it will be useful,
but without any warrenty, even the implied warranty of
merchantability or fitness for a specific purpose.

== Description ==

With the Selling Ads add-on you can allow visitors to purchase ad space on your site directly.

== Installation ==

Selling Ads is based on the free Advanced Ads plugin, a simple and powerful ad management solution for WordPress. Before using this plugin download, install and activate Advanced Ads for free from http://wordpress.org/plugins/advanced-ads/.
You can use Advanced Ads along any other ad management plugin and don’t need to switch completely.

== Changelog ==

= 1.3.1 =

- Improvement: import and export add-on options
- Improvement: compatibility with Beaver Builder to show the ad setup form correctly
- Fix: Ensure Ad Admin can save plugin options.
- Fix: prevent infinite loop when using a dedicated ad setup page

= 1.3.0 =

- added `advanced-ads-selling-upload-file-size` to change the max image upload file size
- added note on ad setup page if it was visited without a connection to an order
- fixed JavaScript check for image ad upload file size
- fixed empty plain text ad field error showing on a blank page
- updated translation files

= 1.2.10 =

- hotfix to prevent unwanted output on pages that don’t show an ad setup form

= 1.2.9 =

- added note on ad setup page if it was visited without a connection to an order
- fixed missing index issue on ad edit screen caused by broken products

= 1.2.8 =

- fixed decimals cut off when selecting the price in the frontend

= 1.2.7 =

* set the ADVANCED_ADS_SELLING_SHOW_SETUP_FORM_AFTER_CONTENT constant to show the ad setup form after the content when a specific page is selected
* added option to have an "Ads" tab in the customer account (/my-account) where advertisers can review their ad performance if the Tracking add-on (v1.8.18 and higher) is enabled
* fixed currency symbol position option not being honored when selecting a price
* fixed setup form not switching between image and HTML input fields
* made missed label translatable

= 1.2.6 =

* adjust the label and description for "WooCommerce fixes" to make more clear what it does
* fixed issue on ad setup page when HTML and image ads are purchased at the same time
* fixed warning on ad setup page due to using a filter that was too early

= 1.2.5 =

* enabled upsells and cross-sells for ad products
* added compatibility with Advanced Ads 1.12
* added French, Italian, and Dutch translation

= 1.2.4 =

* fixed last change to make ad products virtual to not affect physical products

= 1.2.3 =

* make ad products virtual by default – needs to re-save already existing ads
* fixed translation typos

= 1.2.2 =

* fixed coding issue that causes an error message when an ad is published
* added option to hide ad setup from clients
* updated German translation

= 1.2.1 =

* swapped getimagesize() function for a more reliable solution
* added hooks to allow to purchase custom ad types
* removed overview widget logic

= 1.2 =

* made the add-on compatible with WooCommerce 3.0

= 1.1.4 =

* fixed default setup page not showing up when home_url() and site_url() are different

= 1.1.3 =

* fixed page content being empty when WooCommerce is not enabled

= 1.1.2 =

* updated German translation
* fixed issue with publish ad setup page and content retrieved in wp_head

= 1.1.1 =

* only show ad setup for ad product type
* hide all ad setup page related information when order does not contain an ad product
* fixed error on ad setup page

= 1.1 =

* added option to use an existing page for the ad setup process
* added `advanced-ads-selling-email-options` to allow to customize emails
* made compatible with Advanced Ads 1.7.16

= 1.0.2 =

* allow to upload gif files
* fixed notifications not containing the ad edit link
* fixed missing array error

= 1.0.1 =

* fixed prices not updating correctly
* fixes for new installations and ones without WooCommerce activated

= 1.0.0 =

* first plugin version

Build: 2021-12-46e321d9