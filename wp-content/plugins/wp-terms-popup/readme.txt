=== WP Terms Popup - Terms and Conditions and Privacy Policy WordPress Popups ===
Contributors: linksoftware, tentenbiz
Tags: terms and conditions, terms of service, privacy policy, age verification, popup
Requires at least: 5.0
Tested up to: 5.9
Requires PHP: 5.6
Stable tag: 2.5.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Use WP Terms Popup to ask visitors to agree to your terms and conditions or privacy policy before they are allowed to view your site.

== Description ==

Control access to your WordPress site with a popup. WP Terms Popup gives you the ability to use a popup to restrict users from accessing your website. You can use the plugin to ask visitors to agree to your terms and conditions, terms of service, or privacy policy before they are allowed to view your site.

## How Does WP Terms Popup Work?

WP Terms Popup gives your users a simple three-step process for gaining access to your website. Use this as a way to make sure your visitors are presented with your terms and conditions, terms of service, or even a privacy policy before viewing your WordPress site.

**Step #1: Your user must read the popup first.**

You decide what your popup shows to visitors: terms of service, a privacy policy, etc. You can include any content, such as text or images, that you would include in a typical WordPress post.

**Step #2: The user agrees to your conditions.**

Each popup contains two buttons: one to show acceptance and another that redirects away from your site. The popup will not go away until your user clicks the accept button to agree to the content of your popup.

**Step #3: Website access is granted to your user.**

When the user accepts your popup they are immediately taken to your site without any further interaction. They will not see the popup again until your agreement expiration has expired.

## Getting Started

After installing and activating WP Terms Popup, go to "WP Terms Popup" in your WordPress admin menu and select "Add New" to create your first popup.

Go to "Settings" in the same "WP Terms Popup" menu to create the global settings that will apply to all popups. You can override some of these settings by editing each individual popup. 

From the "Settings" screen you can assign a popup to be shown sitewide or you can assign popups to individual pieces of content using the standard post editing screen.

## Premium Add-Ons

You can extend the feature set of WP Terms Popup with one of our premiums add-ons:

* <strong>[WP Terms Popup Designer](https://termsplugin.com/designer?utm_source=readme&utm_medium=plugin-repository&utm_content=designer)</strong>
Adjust the appearance of your popups without writing code or modifying your WordPress theme.
* <strong>[WP Terms Popup Collector](https://termsplugin.com/collector?utm_source=readme&utm_medium=plugin-repository&utm_content=collector)</strong>
Store information about your website’s visitors after they agree to your popups.
* <strong>[WP Terms Popup Age Verification](https://termsplugin.com/age-verification?utm_source=readme&utm_medium=plugin-repository&utm_content=age)</strong>
Confirm a visitor’s age before they can agree to your popup and view your site.

== Frequently Asked Questions ==

= Can I adjust the look and feel of the popups? =

If you are familiar with CSS, you can make changes to the popup's appearance. We recommend adding your CSS changes to a child theme or using the WordPress Customizer. For anyone not comfortable, or unfamiliar, with the safest way to make changes like this we have an add-on called [WP Terms Popup Designer](https://termsplugin.com/designer?utm_source=readme&utm_medium=plugin-repository&utm_content=faq) that can help.

= Does WP Terms Popup store any details about my users? =

WP Terms Popup uses a cookie to monitor when a user has agreed to a popup. The only information in the cookie is an identifier to track which of your popups they have agreed to. For more detailed information, we have an add-on called [WP Terms Popup Collector](https://termsplugin.com/collector?utm_source=readme&utm_medium=plugin-repository&utm_content=faq) that stores extra data about visitors who agree to your popups.

= Can you add an age verification drop down to a popup? =

This feature is not available in the base plugin. We have an add-on called [WP Terms Popup Age Verification](https://termsplugin.com/age-verification?utm_source=readme&utm_medium=plugin-repository&utm_content=age) that gives you the ability to add age verification rules to each of your popups.

= How do I use a single popup for my entire WordPress site? =

After you've created your popup:

1. Go to the "Settings" page and select the checkbox for "Enable only one popup sitewide?"

2. Select your "Sitewide Terms Popup" from the dropdown.

3. Set an "Agreement Expiration" time if the default isn't right for you.

4. Press the "Save Settings" button.

= Can I have popups on different pages and posts? =

1. Create as many popups as you want.

2. Go to the edit screen of the post or page you want to show a popup on.

3. On the right-hand side of the edit screen, you will see the "WP Terms Popup" option box. Configure according to your needs.

4. Save the post or page.

= How do I disable a sitewide popup from appearing on a page or post? =

1. Go to the edit screen of the post or page you want to disable the sitewide popup on.

2. On the right-hand side of the edit screen, you will see the "WP Terms Popup" option box. Check the "Disable Popup?" box.

3. Save the post or page and your sitewide popup will no longer appear.

= Is the popup responsive and viewable on mobile devices? =

Yes, the popup is responsive and will resize according to a device's browser dimensions.

= Will my visitors be able to see the popup if they have Javascript disabled on their browser? =

Yes, on the condition that you are not using the "Load popups with JavaScript" option available in the settings for WP Terms Popup. That setting uses Javascript to load popups and to help deal with caching plugins and solutions.

== Screenshots ==

1. WP Terms Popup Example
2. Create and Manage Popups
3. WP Terms Popup Settings
4. Enable a Popup on an Individual Post/Page
5. Disable a Popup on an Individual Post/Page

== Changelog ==

= 2.5.1 =
* Bug fixes.

= 2.5.0 =
* WP Terms Popup 2.5.0 contains major changes to the HTML markup of the popup and how the "Load popups with JavaScript?" feature functions. Please check any custom CSS you might be using and, if your site has a caching solution, reset/flush the cache after updating.
* Changes to the markup of the popup.
* Changes to the "Load with Javascript?" feature's implementation.

= 2.4.1 =
* Bug fixes.

= 2.4.0 =
* New "Buttons Always Visible?" setting.
* Added support for new features in the Designer add-on.
* Interface changes.
* Bug fixes.

= 2.3.1 =
* Interface changes.

= 2.3.0 =
* Localization changes.
* Added support for new features in the Collector add-on.

= 2.2.0 =
* Support for the Age Verification add-on.

= 2.1.0 =
* Improved support for public custom Post Types.
* Re-arranged some HTML elements for new features in add-ons.
* Interface changes.
* Bug fixes.

= 2.0.1 =
* Bug fixes.

= 2.0.0 =
* Refactored code base.
* WordPress 5.5 compatibility changes.
* New "User Visibility" setting.

== Upgrade Notice ==

= 2.5.0 =
WP Terms Popup 2.5.0 contains major changes to the HTML markup of the popup and how the "Load popups with JavaScript?" feature functions. Please check any custom CSS you might be using and, if your site has a caching solution, reset/flush the cache after updating.