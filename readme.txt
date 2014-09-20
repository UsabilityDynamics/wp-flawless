== Description ==
* Flawless WordPress Theme By Usability Dynamics, Inc.
* Theme Homepage - http://usabilitydynamics.com/products/flawless/

== Changelog ==

= 0.3.6 =
* Added Mobile Navbar UI.
* Fixes to permalink rewrites to resolve issues with Shopp plugin.
* Carrington Build update to version 1.2.2.
* Removed 4-column CB row.
* Relocated Carrington Build modules into flawless/modules

= 0.3.5 =
* Logo handling updated - uploaded logos are now loaded and stored in the Media Library.
* Added a Header Navbar management panel which lets you select the type of Navbar, if any, to display on the front-end.  In addition to the Navbar itself, you may also add optional components such as a User Login form, a collapsible menu expander for mobile resolutions, as well as a displaying your brand.
* Added full Navbar support for the BuddyPress Admin Bar.

= 0.3.4 =
* Added splash screen to notify when theme has been updated, with a changelog.
* Restructured the way extra assets (e.g. fancybox, prettify, form helper) are loaded.  They are now registered automatically and then enqueued later on if enabled.  This way they can be enqueued manually since they are always registered.
* Added Google Prettify and some language styles we will use.
* Added "Users" to BuddyPress Navbar "Manage" dropdown.

= 0.3.3 =
* Global variable $fs replaced for $flawless. (old one still works, but should be phased out)
* Added a top Navbar which can be used to render a custom menu.
* Added responsive styles which only affect the Navbar.
* Added option to disable the BuddyPress navbar.
* Changed the theme settings page UI to match the WP Appearance / Plugins pages.
* Fixed bug with "Add Row" not working on back-end.

= 0.3.2 =
* Updated Carrington Build to 1.2.1
* Added shortcodes: [image_url] and [button]
* Created flawless_theme::extra_local_assets() function for loading things like Fancybox.
* Numerous BuddyPress updates, and new shortcodes: group_meta, group_description.
* Updated to BuddyPress Groups Carrington Build module to execute shortcodes.
* Added 4-column Carrington Build row.
* Added UD Loop, a branch of the Carrington Build Loop, although kept inactive.
* Removed the Ajax Pages Carrington Build module.
* Reactivated the Carrington Build Text module.

= 0.3.1 =
* Added flawless_theme::extra_local_assets() to handle loading of extra local assets, such as Fancybox.

= 0.2.5 =
* .inner_content_wrapper and all references changed to .container
* .post_listing_inner class added to CB Loop module excerpt and full content listings
* [button] shortcode added

= 0.1.0 =
* Nothing yet.

