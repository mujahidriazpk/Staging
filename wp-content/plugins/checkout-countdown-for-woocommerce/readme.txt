=== Checkout Countdown for WooCommerce - FOMO Cart Abandonment ===

Contributors: morganhvidt
Tags: woocommerce countdown, woocommerce, sales timer, checkout countdown, cart abandonment, timer, countdown, product timer, product countdown, woocommmerce bar, cart timer, checkout timer, fomo, top bar
Author URI: https://puri.io
Author: Morgan Hvidt
Donate link: https://www.paypal.me/morganhvidt/
Requires at least: 4.0
Tested up to: 5.9
Requires PHP: 7.0
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

 Improve cart conversion with Checkout Countdown for WooCommerce. The countdown timer starts when products are added to the cart.

== Description ==

Checkout Countdown for WooCommerce improves cart conversion by introducing a countdown timer as soon as any product has been added to the cart. Lower your abandoned cart rate by letting your customers know exactly how long their order can be reserved. Easy to set up and customize. It's a full cart timer, not just a product timer!

### Checkout Countdown Features include

* WordPress 5.9+ and WooCommerce ready.
* Top bar and banner. Display the countdown across your whole site. The bar only appears once a product has been added to the customer’s cart.
* Set up a separate banner message for sales marketing that appear before any products have been added to cart.
* Multiple styles e.g WooCommerce cart/checkout notice countdown timer (adapts to your theme style).
* Easy shortcode for custom placement **[checkout_countdown]**.
* Customize countdown text and expired cart text.
* Customize banner background color and font color.
* Great for FOMO (fear of missing out) and to help lower cart abandonedment.


### Also great for WooCommerce ticket sales!

A checkout or cart countdown timer can help your store make those product sales quicker. It's a good addition to WooCommerce Event ticket sales and WooCommerce Bookings.

### Extended features available with Checkout Countdown Pro

 * Empty and clear the WooCommerce cart as a timed event while customers are active on your site.
 * Restart and loop the countdown after it's finished.
 * Reset the countdown when another product is added to cart.
 * Recalculate the cart and checkout totals with every timer loop. Totals are reloaded live without the page refreshing, so customers don’t lose any details in the checkout. Useful for WooCommerce stores with live market rates.
 * AJAX Support for starting the timer when a product is added to the cart without reloading the page.
 * Custom redirect after countdown timer has finished.
 * Change the amount of time the expiry message appears.


[Learn more about Checkout Countdown Pro](https://puri.io/plugin/checkout-countdown-woocommerce/)

### Request a feature

Got an idea? [let us know!](https://puri.io/support/)

== Installation ==

Checkout Countdown for WooCommerce plugin Installation Instructions:

Note: This plugin requires WooCommerce to be installed.

1. Upload the Checkout Countdown for WooCommerce plugin to your /wp-content/plugin/ directory or through the plugin admin section under "add new".,
2. Activate the plugin through the ‘Plugins’ menu in WordPress.,
3. Configure your settings by going to WooCommerce settings, then the Checkout countdown tab.,
4. That's it!

== Frequently Asked Questions ==

= Can Checkout Countdown clear the customer cart? =

The Pro version allows you to clear the customer cart once the countdown has finished. Please note that the cart will only be cleared while the customer is active on your site. If you require the cart to be cleared regardless of customer activity, you can read more about how to [automatically clear the cart here.](https://puri.io/docs/general/faqs/which-plugin-is-best-for-clearing-the-cart/)


= How do I place the Checkout Countdown in a custom location? =

We've made a super simple shortcode for you that you can place anywhere in your template files. Check out an example of the
[custom placement of Checkout Countdown here.](https://puri.io/docs/checkout-countdown-for-woocommerce/shortcode/place-shortcode-templates-or-hooks/)

= The top bar doesn't appear on my site  =

The Checkout Countdown top bar works with any theme that doesn't use a **fixed header**. The fixed header takes over the space. If you have a fixed header than you can use the shortcode [checkout_countdown] or Checkout and Cart Notices. You can read more about [customizing the checkout countdown design.](https://puri.io/docs/general/faqs/which-plugin-is-best-for-clearing-the-cart/)


== Screenshots ==
1. WooCommerce Countdown Banner for a sale
2. WooCommerce clear cart when timer expired
3. Countdown displayed a WooCommerce notice on checkout and cart
4. Checkout Countdown Settings page
5. Matches your theme, Divi Theme in this case.

== Changelog ==

= 3.1.6 =
* Tested and ready for WooCommerce 6.2+
* Tested and ready for WordPress 5.9+

= 3.1.3 =
* Tested and ready for WooCommerce 4.8+
* Tested and ready for WordPress 5.6+

= 3.1.1 =
* Improvement: Get the correct cart url when the cart page is something other than /cart/, when using the redirect feature.

= 3.1.0 =
* New: Pro option to change the amount of time the expiry message appears.
* Update: The countdown banner won't hide while showing the expired the message if no default message is set.
* Update: Ready and tested with WooCommerce 4.3
* Update: Ready and tested with WordPress 5.5


= 3.0.7 =
* Ready and tested with WooCommerce 4.2
* Added WPML support for translating the countdown strings. Look for the context "Frontend:" on the strings.

= 3.0.6 =
* Ready and tested with WooCommerce 4.1

= 3.0.5 =

* Added new tags for the countdown. You can now use {days} and {hours} as well.
* Updated coding style.
* Ready and tested with WooCommerce 4.0
* Ready and tested with WordPress 5.4

= 3.0.4 =

* Ready and tested with WooCommerce 3.9

= 3.0.3 =

* Ensures all enqueued scripts are loading in the footer.
* Updated Translation files.
* Pro - Fixed reset time when adding to cart while using add to cart redirection.
* Pro - Fixed bug that prevented the cart from clearing time when customers returned, after their cart expired.

= 3.0.2 =

* Added developer function for restarting the countdown via the ccfwooController.

Checkout Countdown Pro support:
* Clearing WooCommerce Bookings in cart.
* Reset counter when a product is added to cart.

= 3.0.1 =

* Speed of starting/loading the frontend countdown is much faster.
* Ready for WordPress 5.3+
* Ready for WooCommerce 3.8+

= 3.0.0 =

**Important! Big update to both Checkout Countdown Free & Pro 3.0**
We highly recommend that you check your site if you've done custom integrations and read more about the 3.0 release!

[Read Release Notes](https://puri.io/blog/checkout-countdown-3-0-release-notes/)

= 2.4.3 =

- Added option to remove all settings from the database when uninstalling.

= 2.4.2 =

- Tested with WooCommerce 3.7
- Added Minified Javascript files.

= 2.4.1 =

- Added version control to JS to fix aggressive browser caching. We still recommend that your clear any cache from caching plugins!

= 2.4.0 =

**You must install this 2.4.0 plugin to use Checkout Countdown Pro 2.40!**

- Improved setting explanations and design.
- Improved checking of cart contents.
- Improved settings performance.
- Improved performance with [Checkout Countdown Pro](https://puri.io/plugin/checkout-countdown-woocommerce/?utm_source=active_plugin&utm_medium=changelog&utm_campaign=checkout_countdown).
- Added support for translations via Loco Translate and WPML.
- Removed Unused code.

= 2.3.6 =

Updated author details and support links.

= 2.3.5 =

Fixed initial loading height of the banner and shortcode be consistent.
Improved shortcode handling. Now works with correctly with Gutenberg.
Improved checking of products in cart.

We recommend switching to new [cc_countdown] rather than the old [cc-countdown].

= 2.3.4 =

Fixed the console error "innerHTML of undefined" when using the checkout notices or shortcode setting.

= 2.3.3 =

Tested and ready for WordPress 5.2

= 2.3.2 =

Tested and ready for WordPress 5.1

= 2.3.1 =

Fixed an issues which causes the countdown 'expired' message to disappear instantly.
Minor clean up  of unused code.

= 2.3.0 =
Added support the new Ajax feature for the [Checkout Countdown Pro Add-on](https://puri.io/plugin/checkout-countdown-woocommerce/) feature. AJAX support starts the timer without reloading the page. It uses AJAX events, such as when adding a product to cart or removing the last item.

= 2.2.3 =
Tested with WordPress 5.0 and Gutenberg

= 2.2.2 =
Updated - Assets and minor cleanup.

= 2.2.1 =
Tested with the latest WooCommerce 3.5.

= 2.2 =
Improved - The countdown now works even if the browser/tab is closed, instead of the countdown not counting when closed.

= 2.1 =
Added - "minutes" and "seconds" are now translatable into anything.
Improved - array_merge
Fixed - Some Users experienced undefined innerHTML

= 2.0.3 =
* Added - Automatically cleans up the database when plugin is uninstalled.
* Fixed - a typo

= 2.0.2 =
* Improved - Cleaned up code

= 2.0.1 =
* Fixed - A bug that could cause issues when saving timer settings.

= 2.0 =
* Checkout Countdown now uses it's own setting API.
* Complete redesign and rework of the plugin.
* Settings moved from WooCommerce settings to it's own **Countdown** menu in the WordPress sidebar.
* Added Banner Message feature to the free Version.
* Complete integration with Checkout Countdown for WooCommerce Pro Addon.

= 1.2 =
* Improved cart and counter checks for better stability
* Added a few descriptions

= 1.0 =
* Initial release on wordpress.org
