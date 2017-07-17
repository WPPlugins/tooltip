=== Plugin Name ===
Contributors: nutomic
Donate link: 
Tags: posts, pages, preview, links
Requires at least: 3.0
Tested up to: 3.0.1
Stable tag: trunk

This plugin displays a small preview image of the target site whenever you hover over a link.

== Description ==

This plugin displays a small preview image of the target site whenever you hover over a link.

Tooltips are fully customizable via css, and can either be displayed for all links, or only links to external sites.

It is very simple, all the work is done automatically for you, you only have to install and set you preferences.

== Installation ==

1. Upload 'plugin-name.php' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Change the settings in 'Settings/Tooltip' as you like and click save

== Frequently Asked Questions ==

= Why are there not tooltips for my old posts? =

This plugin inserts javascript calls into each post as the post is saved. An option to do this for all existing posts 
is planned, but not yet added. 
As a workaround you could update each post once to let the plugin do the work, or wait for a new plugin version.

= Where do you get the images from? =

Im using a wordpress function I randomly stumbled upon:

Call 'http://s.wordpress.com/mshots/v1/http%3A%2F%2F' with any url (no 'http://') appended, and it will return a 
screenshot of that page.

== Screenshots ==

1. screenshot-1.png
2. screenshot-2.png

== Changelog ==

= 1.0 =
* First release