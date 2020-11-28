=== WP Intro.JS Plugin ===

Contributors: CFuze
Donate link: https://cfuze.com/donate.html
Tags: cfuze, introjs, intro.js, tour, welcome, tutorial, help, hint, guide, step
Requires at least: 3.6
Tested up to: 5.5
Stable tag: 1.1
Requires PHP: 5.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily add step-by-step guides and feature introduction to your site using the excellent Intro.JS library fully integrated with WordPress.

== Description ==

WP Intro.JS plugin allows you to easily add tours or tutorials to your site (front-end or back-end admin pages) using [Intro.JS](https://www.introjs.com/).  Additionaly, hint pips are available to be displayed.  Almost every feature within Intro.JS has been implemented.

WHY USE INTRO.JS?
When new users visit your website or product you should demonstrate your product features using a step-by-step guide. Even when you develop and add a new feature to your product, you should be able to represent them to your users using a user-friendly solution. Intro.js is developed to enable web and mobile developers to create a step-by-step introduction easily.

= General Information =

Tours can be associated to any page or post.  There are 2 shortcodes available to provide quick links to start the tour or show the hints.  Tours can auto-start as well as be disabled after completion per user.

= Shortcodes =

* `[wpintrojs_tour]` - Shows a Start Tour link
* `[wpintrojs_hint]` - Shows a Show Hints link

== Installation ==

= Requirements =

* PHP 5.3+ (7.3+ is preferred)
* WordPress 3.6+
* jQuery 1.10+

= Install Methods =

* Through WordPress Admin > Plugins > Add New, Search for "WP Intro.JS"
	* Find "WP Intro.JS"
	* Click "Install Now" of "WP Intro.JS"
* Download [`wpintrojs-plugin.zip`](http://downloads.wordpress.org/plugin/wpintrojs-plugin.zip) locally
	* Through WordPress Admin > Plugins > Add New
	* Click Upload
	* "Choose File" `wpintrojs-plugin.zip`
	* Click "Install Now"
* Download and unzip [`wpintrojs-plugin.zip`](http://downloads.wordpress.org/plugin/wpintrojs-plugin.zip)  locally
	* Using FTP, upload directory `wpintrojs` to your website's `/wp-content/plugins/` directory

= Activation =

* Click the "Activate" link for "WP Intro.JS" at WordPress Admin > Plugins

= Upgrading =

* Through WordPress
	* Via WordPress Admin > Dashboard > Updates, click "Check Again"
	* Select plugins for update, click "Update Plugins"
* Using FTP
	* Download and unzip [`wpintrojs-plugin.zip`](http://downloads.wordpress.org/plugin/wpintrojs-plugin.zip)
	* Upload directory `wpintrojs` to your website's `/wp-content/plugins/` directory
	* Be sure to overwrite your existing `wpintrojs` folder contents

= Deactivation =

* Click the "Deactivate" link for "WP Intro.JS" at WordPress Admin > Plugins

= Deletion =

* Click the "Delete" link for "WP Intro.JS" at WordPress Admin > Plugins
* Click the "Yes, Delete these files and data" button to confirm "WP Intro.JS" plugin removal

== Frequently Asked Questions ==

* How do I start the tour or hints via JS?  Just make a call to wpIntroJs_StartTour() or wpIntroJs_ShowHints().

== Changelog ==

* 1.0.0 Initial Commit

== Upgrade Notice ==

* N/A

== Screenshots ==

1. Tour Overview
2. Edit Tour
3. Add Steps
4. Add Hints

