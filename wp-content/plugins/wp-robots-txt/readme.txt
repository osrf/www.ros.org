=== WP Robots Txt ===
Contributors: chrisguitarguy
Donate link: http://www.pwsausa.org/give.htm
Tags: robots.txt, robots, seo
Requires at least: 3.3
Tested up to: 3.6
Stable tag: 1.1

WP Robots Txt Allows you to edit the content of your robots.txt file.

== Description ==

WordPress, by default, includes a simple robots.txt file that's dynamically generated from within the WP application.  This is great! but maybe you want to change the content.

Enter WP Robots Txt, a plugin that adds an additional field to the "Reading" admin page where you can do just that.

== Installation ==

1. Download the plugin
2. Unzip it
3. Upload the unzipped folder to `wp-content/plugins` directory
4. Activate and enjoy!

Or you can simply install it through the admin area plugin installer.

== Screenshots ==

1. A view of the admin option

== Frequently Asked Questions ==

= I totally screwed up my `robots.txt` file. How can I restore the default version? =

Delete all the content from the *Robots.txt Content* field and save the privacy options.

= Could I accidently block all search bots with this? =

Yes.  Be careful! That said, `robots.txt` files are suggestions. They don't really *block* bots as much as they *suggest* that bots don't crawl portions of a site.  That's why the options on the Privacy Settings page say "Ask search engines not to index this site."

= Where can I learn more about `robots.txt` files? =

[Here](https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt).

== Changelog ==

= 1.0 =
* Initial version

= 1.1 =
* Moved the settings field "officially" to the reading page
* General code clean up

== Upgrade Notice ==

= 1.0 =
* Everyone wants to edit their `robots.txt` files.

= 1.1 =
* Should actually work in 3.5+ now
