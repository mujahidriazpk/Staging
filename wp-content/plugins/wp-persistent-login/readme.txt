=== WordPress Persistent Login ===
Contributors: lukeseager, freemius
Donate link: 
Tags: login, active logins, sessions, session management
Requires at least: 5.0
Tested up to: 6.0.0
Stable tag: 2.0.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Persistent Login keeps users logged into your website securely, unless they explicitly log out. Also limit the number of active logins users are allowed to have at one time.

== Description ==

Persistent Login is a simple plugin that keeps users logged into your website unless they explicitly choose to log-out. 

You can also limit the number of active login sessions your users are allowed to have at one time.

It requires no set-up, just install and save your users time by keeping them logged into your website securely, avoiding the annoyance of forgetting usernames & passwords.

For added security, users can visit their Profile page in the WP Admin area to see how many sessions they have, what device was used and when they were last active. The user can choose to end any session with the click of a button.

## Persistent Login Features
* Selects the 'Remember Me' box by default. 
  * If left checked, users will be kept logged in for 1 year
* Each time a user revisits your website, their login is extended to 1 year again
* Dashboard stats show you how many users are being kept logged in
* Force log-out all users with the click of a button
* Users can manage their active sessions from the Profile page in the admin area
* Support for common plugins out of the box
* Secure, fast and simple to use!

## NEW: Active Logins
* Option to limit the number of active logins to 1 per user
* Old logins will end automatically if the limit is reached, forcing only one active login at a time

### Top Tip
Once the plugin is installed, click the **End all Sessions** button on the plugin settings page to encourage users to login again and be kept logged in forever!

### Supported Plugins
* PeepSo
* Theme My Login
* Ultimate Member
* Ultimate Member - Terms and Conditions Extension

These plugins are supported out of the box. No hassle and no settings to change!

### Note
This plugin honours the 'Remember Me' checkbox. It is checked by default, but if it is unchecked the user won't be remembered.

### Premium Version
There is a premium version of the plugin for those who want more control. 

The premium plan offers the following features:

##### Premium Persistent Login Features
* Manage which user roles have persistent login
* Set how long users are kept logged in for (up to 1 year)
* Hide the 'Remember Me' checkbox, so that users are always remembered
* Session management for users: Users can see all logins. Block Editor and Shortcode support
* Session management for admins: End any users session from the admin area quickly and easily
* Priority Support direct from within WP admin
* Support for WooCommerce Social Login
* Support for Ultimate Member Social Login Extension
* Support for Nextend Social Login

##### Premium Active Login Features
* Control which roles have active login limits applied
* Select aexactly how many active logins users are allowed
* Auto-logout old logins when the limit is reached, or let the user decide

== Installation ==

1. Download and install the plugin onto your WordPress website
2. Activate the plugin
3. Click the End all Sessions button on the plugin settings page to force all users to login again


== Frequently Asked Questions ==

= How long will it keep users logged in? =

If a user visits your website more than once a year, they will be kept logged in forever. 

The only way for them to be logged out is if they clear their cookies, click logout, or don't return within 1 year. 

= What is an active login? =

Sometimes called concurrent logins, this is the number of devices or browsers one user is logged into. If you limit the number of active logins, users can only be logged into your website once. 

If a user logs in to a second device, the first device will automatically be logged out.

= The Remember Me box isn't checked =

If the Remember Me box on a login form isn't checked by default, please open a support request on the Plugin Directory. 

It is most likely a conflict with another plugin or theme, which can usually be fixed. 

= Can I hide the Remember Me box? =

On the free version, no. You can write your own CSS or JavaScript to remove the Remember Me box from a page if you'd like. You will need FTP access to achieve this.

The premium version has a simple setting to hide the Remember Me box by default, and it also works with supported plugins like Theme My Login!

= I don't stay logged in on multiple devices =

If you're not being kept logged in on multiple devices, try turning on 'Allow duplicate sessions' from the settings page. 

This is most common if you're trying to login to two machines with the same operating system and browser on the same network.

= Can I limit the number of logins each user is allowed? Like Netflix? =

Yes, you can now control **active logins** with WordPress Persistent Login. Just visit the Active Logins tab on the settings page and enable active logins. 

The premium version allows you to customise the number of active logins, which user roles they apply to and whether users can select which logins they end when they reach the limit. 

= Is it compatible with WordPress Multisite =

No. WordPress Persistent login isn't compatible with multisite installations at the moment.

= Is it secure? =

You bet! 

WP Persistent Login uses core WordPress methods to ensure that we're logging in the right user. 

= Support =

Support for a bug can be requested from the WordPress Plugin Directory. Premium users can request support directly from the WP Admin area.


= Is it free? =

Yes. The free forever version is and always will be free. All of your users will be kept logged-in when they revisit your website. 

A premium version of the plugin is available if you want to:
* Manage which user roles have persistent login and active login limits
* Set how long users are kept logged in for (up to 1 year)
* Control the number of active logins users are allowed to have
* Allow users to end specific sessions when they reach their maximum login limit
* Allows you to hide the 'Remember Me' checkbox, so that users are always remembered
* Session management for users: Users can see all logins. Block Editor and Shortcode support
* Session management for admins: End any users session from the admin area quickly and easily
* Free localhost licence
* All future features and updates (with a valid licence)
* Priority Support direct from within WP admin

== Screenshots ==

1. Dashboard stats of logged in users
2. Persistent Login settings (free forever)
3. Active Login settings (free forever)
4. Persistent Login settings (premium)
5. Active Login settings (premium)

== Changelog == 

= 2.0.8 =
* Minor performance improvements.
* Premium: Improved support for hiding labels that wrap 'remember me' checkboxes without the correct name attribute.  
* Premium Patch for Nextend Social Login user login error.

= 2.0.7 =
* Minor performance improvements.
* Updating plugin link to new dedicated website.
* Improved responsive design of settings page.
* Premium Fix: Added support for Nextend Social Login with Google integration.

= 2.0.6 =
* Fix: Removing .gitignore file from plugin directory. 

= 2.0.5 =
* Premium Fix: Fixed 'hide remember me' issue with compatability with Restrict Content pliugin.

= 2.0.4 = 
* Removed: Unrequired dependency.

= 2.0.3 = 
* Improved: Added support for auto-checking remember me boxes on Restrict Content plugin login forms.
* Improved: Resetting user count after ending all sessions.

= 2.0.2 =
* Improved: User count messages are now clearer and always show the last completed count breakdown.
* Improved: Wrapped remember me checkbox logic in a JS function for other themes/plugins to use. 

= 2.0.1 =
* Updated browser detection library
* Fix: Fixed PHP error when using Manage Sessions block.
* Premium Fix: Including user sessions correctly on Edit User admin page.

= 2.0.0 =
* Improvement: Entirely re-written plugin in OOP format for improved speed and reliability
* Improvement: Moved Peresistent Login settings to the Users menu
* Improvement: Greatly improved WP Admin interface
* New Feature: Added Active Logins to restrict the number of concurrent logins to one per user
* New Feature: Improved WooCommerce Support - persistent login is enabled by default when users register
* Fix: Security update from dependancy
* **Premium Updates:**
  * Control the number of active logins allowed
  * Control which user roles the active logins limit applies to
  * Control the logic when users reach the active login limit - auto logout a session or allow the user to select which logins to end
  * New Block: Maximum Logins Control lets your users decide which logins to end when they reach their limit

= 1.3.23 =
* Fix: Removing code changes to auto-login process.

= 1.3.22 =
* Update: Updating Freemius SDK to latest version
* Fix: Fixed PHP warning during user count
* Improvement: Improved logic and performance of auto-logging in users
* Improvement: Updated admin area layout

= 1.3.21 =
* Fix: Rollback of 1.3.20 update to fix issue for users with wp-admin area access issues. 

= 1.3.20 = 
* Improvement: Improved session management logic - keeps track of active sessions more accurately. 
* Fix: Fixed PHP warning of Invalid argument supplied for foreach() during user login count.
* Update: Updating Freemius SDK version.

= 1.3.18 =
* Fix: Fixed user count issue for Persistent Login Free Forever users. 

= 1.3.17 =
* Minor fix for user count message on settings page.

= 1.3.16 =
* Fixing update issue with incorrect files uploaded.

= 1.3.15 =
* Performance improvement: Improved performance of logged in user count for websites with large user bases. Tested with 200,000 users. 

= 1.3.14 =
* Fixing update issue with incorrect files uploaded.

= 1.3.13 =
* Updating Freemius SDK to latest version.

= 1.3.12 =
* Bug fix for some users experiencing login retention issues.
* Improved performance of login validation for users.

= 1.3.11 =
* Performance improvement: Created wp cron job to check the number of logged in users periodically.

= 1.3.10 =
* Fixed PHP Notice error
* Updated how settings are stored in database
* Minor improvements to code
* Tested support for WP 5.4.1

= 1.3.9 =
* Updated browser detection package
* Tested with WP 5.4
* Updated minimum required WP version to 5.4
* Plugin support: Added support for AR Member login forms - auto-tick remember me boxes
* Premium support: Added support for hiding AR Member remember me checkboxes

= 1.3.8 =
* New feature: Admin option to allow duplicate sessions. Useful if you're having trouble staying logged in on multiple devices.

= 1.3.7 =
* Improving settings page UI
* General bug fixes
* New premium option: Hide Remember Me boxes from users **(premium)**

= 1.3.6 =
* Minor bug fixes
* Improvement to compatibility with WooCommerce - Social Login Plugin **(premium)**

= 1.3.5 =
* Added support for Ultimate Member Terms & Conditions Extension
* Improved Remember Me box detection

= 1.3.4 =
* Security patch
* Users can now manage their own sessions from their Profile page in the WP Admin area
* Premium: Admins can manage all user sessions from the WP Admin area

= 1.3.3 = 
* Support for Ultimate Member plugin
* Support for Ultimate Member - Social Login Extension **(premium)**
* Added option to disable dashboard 'at a glance' stats to improve dashboard page speed

= 1.3.2 =
* Added fix for where Remember Me box wasn't auto checked on certain themes

= 1.3.1 =
* Improved login form detection
* Minor bug fixes
* **Premium:** Updated browser detection definitions

= 1.3.0 =
* **Major update:** Removed the dependancy of an additional database table & re-writing of plugin
* Big improvements to stability and performance
* **New premium feature:** Front end session management with Gutenberg & Shortcode support

= 1.2.3 =
* Logic to handle removal of data for users that are deleted from WordPress
* Added login timestamps to database in preparation for future feature
* Fixed a bug related to auto-login on Linux operating systems (thanks Paul)
* Minor bug fixes

= 1.2.2 =
* Important GDPR compliance update
* Added usage stats based on user roles to settings page
* Improved settings page for free users
  * De-cluttered the settings page
  * Removed a lot of sales messages
  * 7 day free trial added
* Improved settings page for paid users
  * Improved look and feel
  * Easier to differentiate between information & updatable settings
* Minor bug fixes

= 1.2.1 =
* Added usage stats to the 'at a glance' box on the Dashboard homepage
* Fixed auto-install upgrade bug
* Minor fixes to the admin area pages

= 1.2.0 =
* New Premium Feature: Allow admin to set maximum time persistent login lasts before the user has to login again
* New Premium Feature: Allow admin to end all persistent login sessions from the Dashboard
* New Premium Feature: Added support for "WooCommerce - Social Login" plugin
* Added usage figures to admin area: Allows admins to see how many users are logged in using Persistent Login
* Fixed issue with cookies not being set across the entire domain
* Fixed issue with removing individual users information from the database when failing to login correctly

= 1.1.4 =
* Fixing database column creation bug

= 1.1.3 =
* Fixing minor bugs
* Added multi-device persistent login support. Users can now stay logged in on more than one device
* Added security notification email to user if suspicious login attempts are made
* Added functionality to track user IP addresses (prep-work for future update)

= 1.1.2 =
* Fixing minor bugs
* Improved upgrade process to premium version

= 1.1.1 =
* Fixing minor bugs

= 1.1.0 =
* Plugin re-launch
* Updated logic to improve security
* Uninstall features to remove database table and all data correctly
* Freemium model adopted

= 1.0.2 =
* Updates plugin to be compatible with WP 4.1
* Fixes login/logout redirect issues

= 1.0.1 =
* Removes requirement to have ACF installed, please disable ACF if you don’t use it for anything else
* Updated logic
* General bug fixing

= 1.0.0 =
* WordPress Persistent Login Plugin launch