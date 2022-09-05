=== YITH WooCommerce Subscription  ===

Tags: checkout page, recurring billing, subscription billing, subscription box, Subscription Management, subscriptions, paypal subscriptions
Requires at least: 4.0
Tested up to: 5.2
Stable tag: 1.5.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


== Description ==
It allows you to manage recurring payments for product subscription that grant you constant periodical income


== Installation ==
Important: First of all, you have to download and activate WooCommerce plugin, which is mandatory for YITH WooCommerce Subscription to be working.

1. Unzip the downloaded zip file.
2. Upload the plugin folder into the `wp-content/plugins/` directory of your WordPress site.
3. Activate `YITH WooCommerce Subscription` from Plugins page.


= Configuration =
YITH WooCommerce Subscription will add a new tab called "Subscription" in "YIT Plugins" menu item.
There, you will find all YITH plugins with quick access to plugin setting page.

== Changelog ==
= 1.5.9 - Released on 29 May, 2019 =
Update: Plugin Core 3.2.1
Update: Language files
Fix: Fixed with trial subscription and PayPal
Fix: Fixed option name in subscriptions-related template
Fix: Check renew order before payment
Dev: Added meta is_a_renew on Multi Vendor suborder
Dev: Save subscr_id meta when a ipn is received
Dev: Cancel the subscription if parent_order is null

= 1.5.8 - Released on 16 Apr, 2019 =
Tweak: Added a delay before cancel the subscriptions don't renewed
Fix: Warning errors at backend under specific conditions

= 1.5.7 - Released on 09 Apr, 2019 =
New: Support to WooCommerce 3.6.0 RC1
Update: Plugin Core 3.1.28
Update: Language files
Update: Translation for the string 'Cancel subscription now'
Fix: Set order_args in WooCommerce session, to fix Stripe issue
Fix: Coupon amount
Fix: Numeric issues
Dev: Added filter 'ywsbs_use_date_format'

= 1.5.6 - Released on 26 Feb, 2019 =
Update: Plugin Core 3.1.21
Update: Language files
Fix: Subscription status translation
Fix: Fixed typo
Fix: Subscription field show if check virtual or downloadable checkbox at first time
Fix: Check if subscription has renews in the payment done email
Fix: Recurring payment total in renew order email

= 1.5.5 - Released on 05 Feb, 2019 =
Update: Plugin Core 3.1.17
Update: Language files
Tweak: added check if order contains subscriptions to not execute ywcsb_after_calculate_totals
Fix: Fixed possible error under specific conditions
Fix: Fixed add to cart validation
Fix: $max_lenght array with localized index,
Fix: Fixed a non numeric value on max_lenght field
Fix: Fixed recurrent coupon on variation
Dev: Added the filter 'ywsbs_register_panel_position'

= 1.5.4 - Released on 06 Dec, 2018 =
New: Support to WordPress 5.0
Update: Plugin Core 3.1.5
Fix: Fixed wrong total subscription amount if price per is greater then one
Fix: Moved transaction check to valid IPN handling

= 1.5.3 - Released on 23 Oct, 2018 =
New: Integration with YITH WooCommerce Account Funds from version 1.1.2
New: Integration with WooCommerce Stripe Gateway 4.1.12
Update: Language files
Update: Plugin Framework version 3.0.34

= 1.5.2 - Released on 23 Oct, 2018 =
Update: Language files
Update: Plugin Framework version 3.0.27
Fix: Possible fatal error with YITH WooCommerce Event Tickets

= 1.5.1 - Released on 12 Oct, 2018 =
New: Support to WooCommerce 3.5.0 RC1
Dev: Added actions 'ywsbs_before_add_to_cart_subscription' and 'ywsbs_after_add_to_cart_subscription'
Fix: Deleting meta data not save the changes in the order
Fix: Delete subscription error

= 1.5.0 - Released on 05 Oct, 2018 =
Fix: Possible fatal error under particular conditions.

= 1.4.9 - Released on 26 Sep, 2018 =
Update: Plugin Framework version 3.0.23
Fix: Some code notices

= 1.4.8 - Released on 10 Sep, 2018 =
Fix: Fixed standard PayPal transaction id registration on renew order.
Fix: Issues with PHP 7.2.
Fix: Issues with Renew Reminder email.
Fix: Fixed pause and cancellation issue.

= 1.4.7 - Released on 28 Aug, 2018 =
* Update: Language files
* Update: Plugin Framework version 3.0.21
* Dev: Added filter 'ywsbs_add_shipping_cost_order_renew'
* Fix: Multiple subscription products on cart
* Fix: Renew Reminder cron
* Fix: Email templates
* Fix: Show subscription total on the checkout page if setting max length option.
* Fix: IPN log of posted arguments

= 1.4.6 - Released on 30 Jul, 2018 =
* New: Integration with YITH Stripe Connect for WooCommerce from version 1.1.0
* New: Added payment method column and filter on Subscription List Table
* New: Added new option to show "Renew Now" button on My Account > Orders: if a renew order has at least one failed payment (not for all gateways)
* Update: Language files
* Fixed: The "Pause" button was only being displayed to admin.

= 1.4.5 - Released on 09 Jul, 2018 =
* New: Edit details from subscription admin edit page ( only for subscription paid with YITH PayPal Express Checkout for WooCommerce )
* New: Added subscription number in order list
* Update: Plugin Framework version 3.0.17
* Update: Language files
* Fix: My Account Errors
* Fix: HTML Price when Max Length option is set
* Fix: My account endpoint
* Fix: Fixed columns in order list
* Fix: Fix api handler + refund for recurring payment standard PayPal

= 1.4.4 - Released on 29 May, 2018 =
* Update: Plugin Framework
* Fix: The user can't cancel its subscription from My account page

= 1.4.3 - Released on 25 May, 2018 =
Tweak: Support to GDPR compliance
Update: Update Core Framework 3.0.16
Update: Localization files

= 1.4.2 - Released on 21 May, 2018 =
Fix: Status of Subscription color in backend
Fix: Subscription Email settings

= 1.4.1 - Released on 15 May, 2018 =
Fix: Javascript Error on single product page
Fix: Text domain wrong in two string

= 1.4.0 - Released on 15 May, 2018 =
New: Support to WordPress 4.9.6 RC1
New: Support to WooCommerce 3.4.0 RC1
New: Privacy settings option
New: Retain pending subscriptions option
New: Retain cancelled subscriptions option
New: One time shipping option in product editor Shipping tab
New: Billing and Shipping Customer information editable by Administrator
New: Shipping Customer information editable by Customer
Dev: New actions 'ywcsb_admin_subscription_data_after_billing_address' & 'ywcsb_admin_subscription_data_after_shipping_address'
Update: Update Core Framework 3.0.15
Update: Localization files
Fix: Order total calculation
Fix: Activity log
Fix: YITH WooCommerce Multi Vendor integration - fix for YITH WooCommerce Multi Vendor shipping method
Fix: YITH WooCommerce Multi Vendor integration - vendor suborders *missing* the order note for new subscriptions
Fix: YITH WooCommerce Multi Vendor integration - Added shipping cost in renew order (for vendors)
Fix: Renew subscription from suborder

= 1.3.2 - Released on Apr 06, 2018 =
New: Integration with YITH WooCommerce Affiliate 1.2.4
New: Dutch translation
Fix: Fixed get_price_html
Fix: Order meta data
Fix: Fixed taxes calculation when switching subscription
Fix: Restore cart after cancel payment
Tweak: Change hook prettyPhoto
Update: Update Core Framework 3.0.13


= 1.3.1 - Released on Jan 31, 2018 =
New: Support to WooCommerce 3.3.0
Fix: Issue when PayPal payment is cancelled
Update: Update Core Framework 3.0.11

= 1.3.0 - Released on Jan 29, 2018 =
New: Support to WooCommerce 3.3.0 RC2
Dev: Added hook 'ywcsb_after_calculate_totals'
Dev: Added hook 'ywsbs_change_prices'
Dev: Added hook 'ywsbs_change_price_in_cart_html'
Dev: Added hook 'ywsbs_change_price_current_in_cart_html'
Dev: Added hook 'ywsbs_change_subtotal_price_in_cart_html'
Dev: Added hook 'ywsbs_change_subtotal_price_current_in_cart_html'
Dev: Added hook 'ywsbs_signup_fee_label'
Fix: Shipping taxes removed form WC settings
Fix: PayPal IPN issue with PHP 7.1
Fix: Prevent fatal error for WooCommerce < 3.0.0
Update: Update Core Framework

= 1.2.9 - Released on Nov 15, 2017 =
Fix: Javascript error in single product page
Fix: WooCommerce Coupon when a subscription is on cart

= 1.2.8 - Released on Nov 7, 2017 =
New: Support to WooCommerce 3.2.3
Fix: Discount calculation when a Custom Coupon is applied
Fix: Shipping Calculation for renew orders


= 1.2.7 - Released on Oct 17, 2017 =
New: Support to WooCommerce 3.2.1
Fix: Label on subscription product price

= 1.2.6 - Released on Oct 12, 2017 =
New: Support to WooCommerce 3.2.0 Rc2
New: German translation
New: Added option ''Disable the reduction of stock in the renew order'
Dev: Filter 'ywsbs_price_check'
Dev: Added a check on content cart item key after order processed
Fix: Order item prices with YITH WooCommerce Product Add-Ons
Fix: Prettyphoto.css removed font rules
Fix: Subscription Status localization
Fix: Removed the vendor taxonomy box in subscription CTP page
Fix: Multiple coupons on cart

= 1.2.5 - Released on Aug 19, 2017 =
Fix: Create renew order manually
Fix: changed plain text to html Subscription Status email

= 1.2.4 - Released on Aug 16, 2017 =
New: Support to WooCommerce 3.1.0
Fix: Renew order shipping rate
Fix: Compatibility with YITH WooCommerce Product Add-ons Premium 1.2.6
Fix: Trial status after order complete
Fix: Variation subscription price
Fix: customer search on subscriptions list table
Fix: wrong text domain in string
Fix: subscription meta containing one subscription only
Fix: Misspelled strings
Dev: Added hook 'ywsbs_get_recurring_totals'
Update: Update Core Framework

= 1.2.3 - Released on May 26, 2017 =
Fix: Check on ipn_track_id due to Paypal issue
Fix: Renew order fix

= 1.2.2 - Released on May 05, 2017 =
Fix: Fix renew shipping costs

= 1.2.1 - Released on Apr 28, 2017 =
New: Support to WooCommerce 3.0.4
New: Set customer notes in the renew from parent order
Fix: Custom billing and shipping address in the renew order
Fix: Compatibility with php 5.4
Dev: Changed endpoint hook
Dev: Changed start time of cron jobs
Update: Plugin Core

= 1.2.0 - Released on Mar 31, 2017 =
New: Support to WooCommerce 3.0 RC 2
Fix: Subtotal price on cart
Dev: Added 'ywsbs_renew_subscription' action
Update: Plugin Core

= 1.1.7 - Released on Dec 19, 2016 =
Added: A new method of class 'YITH_WC_Subscription' that return the list of user's subscription
Added: Compatibility with YITH WooCommerce Product Add-ons Premium 1.2.0.8
Added: Support to WordPress 4.7
Added: Date picker in the metabox of subscription info in the backend
Added: Filter 'ywsbs_order_formatted_line_subtotal'
Tweak: Price with taxes in the subscription related to an order
Fixed: The switch of subscriptions from the free to premium version
Fixed: "Max duration of pauses days" isn't saved properly
Fixed: Issues when the order of a subscription is deleted

= 1.1.6 - Released on Oct 04, 2016 =
Added: Filter 'ywsbs_trigger_email_before' to change the time to send the email before the expiration
Added: Italian translation
Added: Spanish translation
Fixed: Coupons behavior when there's a trial period
Fixed: String localization
Updated: Plugin framework


= 1.1.5 - Released on Juy 20, 2016 =
Added: Action to create a renewal order in subscription administrator details
Tweak: Failed register payments
Fixed: Localization strings for trial period
Fixed: Activity log timestamp


= 1.1.4 - Released on Jun 13, 2016 =
Added: hook for actions on my subscriptions table
Added: Support to WooCommerce 2.6 RC1
Updated: Plugin framework

= 1.1.3 =
Added: Option Delete Subscription is extend also if the main order is deleted
Added: Email to customer when a payment fails
Tweak: Changed method to retrieve billing and shipping info of a subscription
Tweak: Customer info in the email
Fixed: Method is add_params_to_available_variation() and can_be_cancelled()
Fixed: Downgrade/upgrade variations


= 1.1.2 =
Fixed: Paypal IPN Request Fix when the renew order is not present
Fixed: Minor bugs


= 1.1.1 =
Fixed: Few missing and unknown textdomains
Fixed: Minor bugs

= 1.1.0 =
Added: Compatibility with YITH WooCommerce Stripe
Added: Paypal IPN validation amounts
Added: In On-hold orders list failed attempts
Added: Failed Attempts in Subscription list
Added: Enabled possibility to move in Trash or Delete subscriptions
Added: Options Overdue pending payment subscriptions after x hour(s)
Added: Suspend pending payment subscriptions after x hour(s)
Added: Option to Suspend a subscription if a recurring payment fail
Added: In Administrator Subscription Detail added the action "Active Subscription"
Added: In Administrator Subscription Detail added the action "Suspend Subscription"
Added: In Administrator Subscription Detail added the action "Overdue Subscription"
Added: In Administrator Subscription Detail added the action "Cancel Subscription"
Added: In Administrator Subscription Detail added the action "Cancel Subscription Now"
Added: In Administrator Subscriptions List Table added the views of subscription status
Added: Search box in Administrator Subscriptions List Table to search for ID or Product Name
Added: Option Delete subscription if the main order is cancelled
Update: Dates in subscription details
Fixed: Admin can't add subscription if YITH WooCommerce Multi Vendor is enabled
Fixed: Start date if a Paypal Payment Method is 'echeck'
Fixed: Localization issue

= 1.0.1 =
Added: Compatibility with Wordpress 4.4
Added: Compatibility with WooCommerce 2.5 beta 3
Updated: Plugin framework
Fixed: Minor bugs

= 1.0.0 =
Initial release

== Suggestions ==
If you have any suggestions concerning how to improve YITH WooCommerce Subscription, you can [write to us](mailto:plugins@yithemes.com "Your Inspiration Themes"), so that we can improve YITH WooCommerce Subscription.

== Upgrade notice ==
= 1.0.0 =
Initial release