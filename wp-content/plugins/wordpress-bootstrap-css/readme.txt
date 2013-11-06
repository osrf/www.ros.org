=== Plugin Name ===
Contributors: paultgoodchild, dlgoodchild
Donate link: http://icwp.io/q
Tags: CSS, WordPress Admin, Twitter Bootstrap, Twitter Bootstrap Javascript, Bootstrap CSS, WordPress Bootstrap, normalize, reset, YUI
Requires at least: 3.2.0
Tested up to: 3.6
Stable tag: 3.0.0-7

WordPress Twitter Bootstrap CSS lets you include the latest Twitter Bootstrap CSS and Javascript libraries in your WordPress site.

== Description ==

Support The Plugin: [Go Ad free](http://www.icontrolwp.com/2013/05/remove-ads-support-wordpress-twitter-bootstrap-plugin/)

What is Twitter Bootstrap?
It's a CSS and Javascript framework that helps boost your site design and functionality quickly.

We love Twitter Bootstrap on our [WordPress sites at iControlWP](http://icwp.io/2 "iControlWP: Secure WordPress Management").
And we wanted a way to quickly link the latest Bootstrap CSS and Javascript to all pages, regardless of the WordPress Theme.

With this plugin, now you can!

*	Works with *any* Wordpress Theme without ever editing Theme files and NO programming needed.
*	Now fully customizable with built-in LESS Compiler.
*	Handy WordPress [SHORTCODES] to add Twitter Bootstrap elements to your site quickly
*	Add your own custom CSS reset file
*	Option to add JavaScript to the [HEAD] (defaults to end of [BODY] as is good practice)
*	and more..

To get the latest news and support go here: [WordPress Twitter Bootstrap CSS Plugin Home](http://icwp.io/8 "WordPress Twitter Bootstrap CSS Plugin Home") to see the release article on our site.

**Why use Twitter Bootstrap?** 
It's good practice to have a core, underlying CSS definition so that your website appears and acts consistently across all
browsers as far as possible.

Twitter Bootstrap does this extremely well.

From Twitter Bootstrap:
*Bootstrap is a toolkit from Twitter designed to kickstart development of webapps and sites.
It includes base CSS and HTML for typography, forms, buttons, tables, grids, navigation, and more*

The problem?
Many themes do not allow you to add custom CSS files easily. Even the Thesis Framework! So we take
another approach and inject the CSS as one of the FIRST items in the HTML HEAD section. This way, no
other CSS interferes first so you can be sure these bootstrap file can be used as a foundation/reset CSS.

The CSS is only part of the solution. Twitter Bootstrap also have Javascript libraries
to complement their Bootstrap CSS. These may also be added to your site with the option to
add them to the HEAD of your site - by default they are added to the end of the BODY.

We also wanted the option to alternatively include YUI "reset.css" and "normalize.css".  These both form related roles to bootstrap, but are lighter.

You could look at the difference between the styles as:

*	reset.css - used to *strip/remove* the differences and reduce browser inconsistencies. It is typically generic and
will not be any use alone. It is to be treated as a starting point for your styling.
*	normalize.css - aims to make built-in browser styling consistent across browsers and adds *basic* styles for modern
expectations of certain elements. E.g. H1-6 will all appear bold.
*	bootstrap.css - is a level above normalize where it adds much more styling but retains consistency across modern
browsers. It makes site and web application development much faster.

**Some References**:

Bootstrap, from Twitter: http://twitter.github.com/bootstrap/

Yahoo Reset CSS, YUI 2: http://developer.yahoo.com/yui/2/

Normalize CSS: http://necolas.github.com/normalize.css/

== Frequently Asked Questions ==

= How can I install the plugin? =

This plugin should install as any other WordPress.org respository plugin.

1.	Browse to Plugins -> Add Plugin
1.	Search: Wordpress Bootstrap CSS
1.	Click Install
1.	Click to Activate.

Alternatively using FTP:

1.	Download the zip file using the download link to the right.
1.	Extract the contents of the file and locate the folder called 'wordpress-bootstrap-css' containing the plugin files.
1.	Upload this whole folder to your '/wp-content/plugins/' directory
1.	From the plugins page within Wordpress locate the plugin 'Wordpress Bootstrap CSS' and click Activate

A new menu item will appear on the left-hand side called 'Twitter Bootstrap'.  Click this menu and select
Bootstrap CSS.

Select the CSS file as desired.

= How can I remove the ads and marketing links? =

Our bread and butter is iControlWP multiple WordPress site management, which costs a few cents a month. Whenever you use iControlWP
to manage your sites and your site is connected to the service, the plugin will automatically remove all marketing / ads from the admin interface.

So by using our services, you get a great service for one, and also support us by contributing and helping with the ongoing development of our plugins.

To understand why it makes sense to financially support free plugin development, please read: http://blog.pinboard.in/2011/12/don_t_be_a_free_user/

= How can I use the WordPress Twitter Bootstrap Shortcodes? =

I've put a full demo page of all the fully support shortcodes in this plugin:
[Complete WordPress Twitter Bootstrap Shortcodes demo page](http://bit.ly/OFYCh8 "Complete WordPress Twitter Bootstrap Shortcodes demo page")

= What are all the parameters for all the shortcodes? =

For all the shortcodes simply type help="y" and preview your post - it will print a box for you showing all parameters, their default values
and an explanation where appropriate.

= The WordPress Shortcodes aren't getting processed properly - why? =

You need to enable the shortcodes feature in the options page. This is a performance optimization so that people who don't need
it don't have to process it.  Also, some require the Bootstrap Javascript library to be loaded so enable that also if you require it. 

= Why was Twitter ("Legacy") Bootstrap v1.4.0 support dropped from the plugin in v2.0.3? =

Time and resources. The work to maintain it and ensure it's bug-free was getting too great.

I've explained a bit more in this [support forum post here](http://wordpress.org/support/topic/plugin-wordpress-twitter-bootstrap-css-legacy-support-removed).

= Can I link more than one CSS? =

No. There's no point in doing that and serves only to add a performance penalty to your page loads.

With version 0.4+, you can now add your own custom reset CSS that will follow the standard reset/Twittter Bootstrap CSS. 

= What happens if uninstall this plugin after I design a site with it installed? =

In all likelihood your site design/layout will change. How much so depends on which CSS you used and how much of
your own customizations you have done.

= Why does my site not look any different? =

There are severals reasons for this, most likely it is that you or your Wordpress Theme has defined all the styles
already in such a manner that the CSS applied with this plugin is overwritten.

CSS is hierarchical. This means that any styles defined that apply to an element that *already* has
styles applied to it will take precedence over any previous styles.

= Is WordPress Twitter Bootstrap CSS compatible with caching plugins? =

The only caching plugin that iControlWP recommends, and has decent experience with, is W3
Total Cache.

This plugin will automatically flush your W3TC cache when you save changes on this plugin (assuming you have
the other plugin installed).

Otherwise, consult your caching program's documentation.

= Do you make any other plugins? =

We also created the [Multiple WordPress Site Manager (iControlWP) ](http://icwp.io/3) to make it easier for you manage all your WordPress sites in one place.

Yes, we created [Custom Content By Country](http://wordpress.org/extend/plugins/custom-content-by-country/ "Custom Content By Country WordPress Plugin")
plugin that lets you display content to users in specific regions.

= Is the CSS "minified"? =

Yes, but only in the case of Yahoo! YUI 2/3, and Twitter Bootstrap CSS.

You now have the option to enable minified CSS or not.

= My Popover/Tooltip doesn't seem to work and it's generating Javascript errors in the console =

This is likely due to you not linking to the latest version of JQuery. Twitter Bootstrap requires the latest
version (v1.7.2 at the time of writing). There is now ( plugin v2.0.3.1+ ) an option to replace the
JQuery of your WordPress installation with the latest version served from Google CDN. Try this if you're
having issues with Popovers etc., or better yet upgrade your WordPress to the latest version.

== Screenshots ==

1. Here you select which CSS to use.

2. If you prefer you can specifiy your own custom reset CSS file. You could use this if you wanted to use a Twitter Bootstrap
CSS library that you have created yourself (useful until we implement a LESS compiler into the plugin).

3. Assuming you select Twitter Bootstrap CSS, you may now select which Twitter Bootstrap Javascript libraries to include

4. You have the option of including any selected Javascript libraries in the HEAD of your WordPress document. This is not recommended
for various performance reason.  You can also selected to enable our WordPress Shortcode library.

5. A new feature as of version 2.0.2.1. Plugin/Theme Developers can now include Twitter Bootstrap CSS in the WordPress Admin screen.
Don't select this unless you know you need it - no harm if you do, but no point otherwise.

6. As of version 2.0.2.1, we've included a news feed on the Dashboard. If you don't want it displayed, select this to hide it.

== Changelog ==

[See Full Demos Of All Shortcodes Available In This Plugin](http://icwp.io/o).

= !WARNING! =

As of Twitter Bootstrap 3.0.0 and the release of the plugin with this Bootstrap library version, several things have changed which
you should be aware of before you upgrade!

*	The Twitter Bootstrap v3+ library is quite different to previous versions. If your current WordPress theme is heavily customized
	and built around Bootstrap v2+ you should take great care before upgrading. Always have a [valid WordPress backup](http://www.worpdrive.com)
	before upgrading your sites.
*	Javascript-based Bootstrap components have also changed quite a bit. I've attempted to maintain the shortcodes, but they might
	break. If something breaks for your site, please report them in the forums.
*	Upgrade code I kept in previous versions for those that moved from v1 to v2 of the plugin has been completely removed.
*	Future versions of the plugin v3+ will have an in-built automatic upgrade system for *minor* releases. Much the same as I've done
	for the [WordPress Simple Firewall](http://wordpress.org/plugins/wp-simple-firewall/).

= TODO =

* Provide better upgrade support for customized Variable.less files. Currently if you've customized your Variables.less file manually
you'll need to back it up before you upgrade your Bootstrap plugin.
* Allow for a free entry LESS section for users to add their own completely custom variables.

= 3.0.0-7 =

* FIX:		LESS compiler for v2.0 of Bootstrap

= 3.0.0-6 =

* FIX:		For Multisite and wpDirAuth compatibility.
* FIX:		Some other smaller tweaks and fixes.

= 3.0.0-5 =

* UPDATED:	Twitter Bootstrap LESS Compiler now better supports Bootstrap v3.0.  There are a lot of changes in this release - ensure to backup your
			variable.less configuration and review the changes.

= 3.0.0-4 =

* UPDATED:	Normalize.css to v2.1.3
* FIX:		Crash for links to Normalize and Yahoo Reset, due to variables not defined.

= 3.0.0-3 =

* UPDATED:	Given the [courteous](http://wordpress.org/support/topic/can-you-add-back-bootstrap-v2-as-an-option-in-the-new-plugin) and
			[not-so-courteous](http://wordpress.org/support/topic/beware-of-short-codes) feedback that you'd like to have the option for
			both Bootstrap v2.3.x and Bootstrap v3+ I've added the option for both.
			It adds more complexity to the plugin and I cannot guarantee for how long both branches of the Bootstrap libraries will
			remain in this v3 plugin branch.
* UPDATED:	The plugin now employs our new Options Object system, so in place of 15+ WordPress options it now uses a single WordPress Option
			to store everything. TODO: apply the same to the LESS compiler options.
* UPDATED:	The version of the JQuery library included if you use the CDNJS option (to align with WordPress 3.6)
* UPDATED:	Support for the latest W3 Total Cache plugin version.
* CHANGED:	A lot of code refactoring to improve its reliability and performance (and also to cater for v2 and v3 of the Bootstrap libraries)
* FIX:		LESS Compiler for Bootstrap v3.0

= 3.0.0-2 =

Skipped.

= 3.0.0-1 =

* UPDATED: Twitter Bootstrap library to version 3.0.0
* UPDATED: Less compiler to v0.4.0 (although still doesn't work with Bootstrap v3.0.0)
* CHANGED: Refactored a lot of code (with more to come!)

= 2.3.2-2 =

* UPDATED: Removed dashboard ad link.
* UPDATED: Normalize CSS to version 2.1.2
* FIX: Undefined variable warning.

= 2.3.2-1 =
* UPDATED: Bootstrap version to latest release v2.3.2

= 2.3.1-3 =
* ADDED: The ability/option to hide all ads - this is simply achieved by purchasing an iControlWP subscription. See FAQ.

= 2.3.1-2 =
* ADDED: The option to enqueue the CSS styles using the WordPress styles enqueue option. There's no guarantee that this will add the files so that they're first CSS, so not recommended.
* CHANGED: Vastly simplified the logic for CSS files being included so it's a little easier to maintain.
* CHANGED: references to Worpit are now iControlWP.
* FIX: Using use the plugin's original method to include the CSS will only ever make the necessary code replacement on the head element once. (http://wordpress.org/support/topic/css-added-outside-the-head)
* UPDATED: Normalize to version 2.1.1
* CHANGED: Yahoo! Reset to version 3.10.0.

= 2.3.1-1 =
* FIX: CDNJS changed their path for their Bootstrap Javscript files since version 2.3.0 so the code now reflects this.

= 2.3.1.b =
* UPDATED: LESS PHP to version 0.3.9 for full compatibility with Twitter Bootstrap 2.3

= 2.3.1.a =
* UPDATED: Bootstrap version to latest release 2.3.1
* ADDED: New shortcodes: TBS_THUMBNAILS and TBS_THUMBNAIL - use in conjuction.

= 2.3.0.b =
* UPDATED: less.php library with a fix from the lesscphp author for an error generated due to unhandled less syntax.

= 2.3.0.a =
* UPDATED: Bootstrap version to latest release 2.3.0
* UPDATED: Normalize CSS to latest release 2.1.0 (https://github.com/necolas/normalize.css/blob/v2.1.0/CHANGELOG.md)
* ADDED: Optimization where the list of CSS links to be included is only created once and then saved in the options table. This is a nice optimization so it's not done on every page load.
* FIXED: Bug where accordion content was not processing shortcodes (http://wordpress.org/support/topic/doshortcode-inside-bootstrap).
* FIXED: No longer a fatal error if another less.php library was included.  Minimum must be 0.3.8 though, otherwise compiling will outright fail. 

= 2.2.2.b =
* ADDED: option to include Bootstrap CSS in the WP Editor (request: http://wordpress.org/support/topic/include-twitter-bootstrap-to-wp-editor)
* ADDED: Now uses CDNJS for the source of the jQuery CDN option is selected.
* ADDED: The option to use [CDNJS](http://wordpress.org/extend/plugins/cdnjs/) for the main Twitter Bootstrap library files - note, if you're using the LESS compiler, then it will use files on your server as it cannot store your personal customizations on CDNJS.
* CHANGED: Requirements changed - if you opt to use CDNJS, you must have WordPress 3.5+

= 2.2.2.a =
* UPDATED: Twitter Bootstrap library to version 2.2.2
* UPDATED: Changed Google CDN for Jquery to use 1.8.3 (inline with WordPress 3.5)
* FIXED: Reported bug: http://wordpress.org/support/topic/tbs_span-size3-offset4-generates-wrong-code

= 2.2.1.2 =

* UPDATED: Twitter Bootstrap library version to 2.2.1
* ADDED: New option to use shortcodes within sidebar widgets.
* ADDED: New shortcode: TBS_TEXT [See emphasis classes](http://twitter.github.com/bootstrap/base-css.html#typography)
* ADDED: New shortcode: TBS_ABBR [See abbreviations](http://twitter.github.com/bootstrap/base-css.html#typography)
* ADDED: Option to set TBS_ICON to the white version of the icon using parameter: white="y"
* UPDATED: LESS PHP Compiler to latest release 0.3.8
* UPDATED: Using new LESS PHP compiler to write CSS files.
* UPDATED: Using new LESS PHP compiler to create minified CSS files.
* UPDATED: Google CDN JQuery library now links to version 1.8.2
* CHANGED: Using plugin_url() instead of PLUGIN_URL because it seems SSL is ignored with the latter
* CHANGED: Directory separator to WORPIT_DS, moved it to a base class, and ensured there'd be no attempt to redefine it if it already exists.
* (v2.2.1.2 includes a fix for a PHP error)

= 2.1.1.1 =

* Added a guard around class declarations to prevents fatal errors if you have the plugin installed twice(?).

= 2.1.1.0 =
* UPDATED: Twitter Bootstrap library to latest release v2.1.1
* ADDED: option to Popover to allow you to set the activation 'trigger' and defaulted it to pre-Bootstrap 2.1.0 behaviour - i.e. on hover!
* ADDED: option to Tooltip to allow you to set the activation 'trigger' and defaulted it to pre-Bootstrap 2.1.0 behaviour - i.e. on hover!
* ADDED: btn-block to the shortcode help for buttons.
* FIX: RSS Feed Widget urls

= 2.1.0.0 =
* UPDATED: Twitter Bootstrap library to latest release of 2.1.0
* UPDATED: Normalize CSS upgraded to version 2.0.1
* FIX: Valid XHMTL http://wordpress.org/support/topic/plugin-wordpress-twitter-bootstrap-css-xhtml-validation

= 2.0.4.8 =
* ADDED: Shortcode [TBS_PROGRESS_BAR] for Twitter Bootstrap Progress Bars (http://twitter.github.com/bootstrap/components.html#progress)
* ADDED: MUCH more verbose help on ALL shortcodes. Simply type: help="y" and it will print the help box on your post.
* ADDED: 'target' parameter to the TBS_BUTTON shortcode so you can open in new window if you want. i.e. target="_blank"

= 2.0.4.7 =
* ADDED: Shortcode for Twitter Bootstrap accordions - collapsable blocks (http://twitter.github.com/bootstrap/javascript.html#collapse)
The shortcodes are: [TBS_COLLAPSE] (parent) and {TBS_COLLAPSE_GROUP]. You need to nest the "GROUPS" within the parent.
* ADDED: "help=y" parameter to all shortcodes so you can quickly print out all available shortcode parameters.
* ADDED: : [Complete WordPress Twitter Bootstrap Shortcodes demo page](http://bit.ly/OFYCh8 "Complete WordPress Twitter Bootstrap Shortcodes demo page")

= 2.0.4.6 =
* FIXED: (again) Fatal error reported- http://wordpress.org/support/topic/plugin-wordpress-twitter-bootstrap-css-cant-activate-the-plugin-because-of-fatal-error
* FIXED: a few minor plugin interface bugs.
* UPDATED: Normalize.css to latest version (2012-07-07) at time of release
* ADDED: Shortcode TBS_SPAN - this is just an alias for TBS_COLUMN added previously.
* ADDED: offset parameter to the TBS_SPAN (and TBS_COLUMN) to reflect offset option in Twitter Bootstrap.
* ADDED: Responsive CSS is automatically recompiled when CSS is recompiled (regardless of whether responsive is enabled or not)

= 2.0.4.5 =
* ADDED: NONCE to form submissions to improve the security of the plugin.
* ADDED: A new compile button - compile CSS from Original or customized Variable.less an option (http://wordpress.org/support/topic/plugin-wordpress-twitter-bootstrap-css-make-compile-variablesless-from-original-an-option)
* FIXED: Fatal error reported- http://wordpress.org/support/topic/plugin-wordpress-twitter-bootstrap-css-cant-activate-the-plugin-because-of-fatal-error

= 2.0.4.4 =
* FIXED: Further attempt to fix string escape issues (thanks Troy!).
* FIXED: Bug with Grid Columns field being appended with 'px' in LESS compiler.
* UPDATED: LESS PHP compiler to latest release (v0.3.5)

= 2.0.4.3 =
* FIXED: An attempt to fix problems some people have with the LESS compiler and escaping double-quoted fonts.
** IF you have had problems, do a RESET first, then attempt to compile your customizations. **

= 2.0.4.2 =

* FIXED: Wasn't properly linking to Google Prettify CSS and JS files when the option was enabled.
* UPDATED: Yahoo! YUI v3 to version 3.5.1.
* UPDATED: Uses serialized data for the LESS CSS plugin options - greatly reducing database calls on the admin section and database usage.
* UPDATED: Plugin now uses iControlWP's standard plugin structure for dynamically creating plugin options pages. The whole plugin is more stable and more reliable.
* UPDATED: Now flushes W3 Total Cache (if installed) when you update your LESS CSS options also.
* ADDED: iControlWP feed to the news feed.

= 2.0.4.1 =

* FIXED: Reported Bug (thanks Claudio!) with Responsive CSS includes - there was a typo in the code and the CSS wasn't linked to correctly.

= 2.0.4 =
* UPDATED: Twitter Bootstrap version 2.0.4
* ADDED: Option - to replace WordPress JQuery library with the latest (at the time of plugin release) as served from Google CDN
This is useful if your WordPress version isn't the latest and has an incompatible JQuery library.
* IMPROVED much of the plugin code.
* IMPROVED variable.less integrity. Now always uses the original copy for LESS compilation in case it becomes corrupted.
* IMPROVED Upgrade handling in terms of LESS compiled CSS. Now automatically recompiles CSS upon upgrade where applicable.
* IMPROVED [TBS_ROW] shortcode to allow fluid rows/containers and also to allow option of creating a container or not. Default to NOT creating container.
* FIXED: A few reported bugs.

= 2.0.3 =
* ADDED: LESS Compiler for some of the most common Bootstrap style options! ( [thanks to LESSCPHP for PHP LESS compiler](http://leafo.net/lessphp/) )
* ADDED: Option - toggle use of minimized or non-minized Bootstrap CSS
* ADDED: Option - toggle delete all plugin settings upon plugin deactivation
* ADDED: Option - enable LESS compiler and include less-compiled CSS
* ADDED: Now enqueues native WordPress JQuery Javascript when Bootstrap Javascript is enabled.
* ADDED: Yahoo YUI! reset.css v3.4.1
* UPDATED: Plugin upgrade handling is much improved
* UPDATED: Normalize CSS updated to the latest version
* REMOVED: support Twitter Bootstrap v1.4.0 ("legacy") !
* REMOVED: support for Individual Twitter Bootstrap Javascript Libraries !
* REMOVED: support for shortcodes [TBS_BLOCK] and [TBS_TWIPSY] !

= 2.0.2.3 =
* FIX: Fixed a bug where the plugin would error and WordPress may deactivate the plugin.
* UPDATED: By default when the plugin deactivates, all plugin settings are removed from the database. I have stopped this
for now (so all settings remain upon deactivation). Version 2.0.3 will have the option for the user to toggle this setting.
* ADDED: A notice in the dashboard about removal of Javascript library changes coming in version 2.0.3

= 2.0.2.2 =
Skipped.

= 2.0.2.1 =
* ADDED: *Ability to include Twitter Bootstrap CSS in WP Admin (along with some CSS fixes to accomodate)*
* ADDED: WordPress Admin notices for upgrades and success settings operations.
* ADDED: New Shortcode: TBS_BADGE
* ADDED: Host Like Toast RSS News feed on Dashboard + option to hide (hlt-rssfeed-widget.php)
* UPDATED: Settings page now uses a new Twitter Bootstrap layout/design
* UPDATED: The screenshots for the docs
* STARTED: The process of Internationalisation (I18n) for the plugin. Anyone who wants to help out, please get in touch.

= 2.0.2 =
* UPDATED: Updated Twitter Bootstrap library to v2.0.2
* ADDED: Ability to include Responsive CSS stylesheet that comes with Twitter Bootstrap version 2.0+
* ADDED: Reorg'd some of the interface to be a little more logical
* FIXED: serious oversight with including individual Javascript libraries.

= 2.0.1c =
* ADDED: Ability to add the "disabled" option to Twitter Bootstrap button components.
* FIXED: a couple of bugs in the shortcodes

= 2.0.1b =
* ADDED: New shortcode [TBS_ICON](http://bit.ly/zmGUeD "Twitter Bootstrap Glyph Icon WordPress Shortcode") to allow you to easily make use of [Twitter Bootstrap Glyphicons](http://bit.ly/AxCdQj)
* ADDED: New shortcode [TBS_BUTTONGROUP] to allow you to easily make use of [Twitter Bootstrap Button Groups](http://bit.ly/z13ICu)
* CHANGED: Rewrote [TBS_BUTTON]. Now you can add "toggle" option, and specify the exact html element type, eg [a], [button], [input]
* CHANGED: Rewrote [TBS_ALERT]. Now you can add the Alert Heading using the parameter: heading="my lovely heading"
* With [TBS_ALERT], parameter "type" is no longer supported - use parameter "class" instead
* CHANGED: Added inline Javascript for activating Popover and Tooltips - nice page-loading optimization and also only execute JS code necessary
* Throughout, attempted to retain support for Twitter Bootstrap 1.4.0. But no guarantees - you should upgrade and convert asap.
* TODO: necessary javascript snippet to enable button toggling - couldn't get it working.

= 2.0.1a =
* Skipped due to missing elements in [TBS_ICON] shortcode.

= 2.0.1 =
* Twitter Bootstrap library upgraded to v2.0.1

= 2.0.0 =
* Added the options for Twitter Bootstrap Library 2.0.0
* Maintained compatibility with Twitter Bootstrap Library 1.4.0
* Removed option to HotLink to resources
* Added more Javascript libraries for 1.4.0 and 2.0.0
* Fixed several bugs.
* Keeping plugin version numbering in-line with Twitter Bootstrap versioning.
* References to "Twipsy" renamed to "Tooltips" to be inline with version 2.0.0
* Most SHORTCODES work between both versions. [Latest Notes](http://bit.ly/wLkYjf "Host Like Toast WordPress Twitter Bootstrap plugin release notes v2.0")

= 0.9.1 =
* Restructured and centralized CSS on admin side.
* Revamped the Host Like Toast Developer Channel subscription box - the previous one wasn't working so well.

= 0.9 =
* Fixed bug where styles were being reapplied when HTML [HEADER] element was defined (thanks to Matt Sims!) 
* Improved compatibility with WordPress 3.3 with more correct enqueue of scripts/stylesheets.

= 0.8.6 =
* [TBS_TWIPSY] and [TBS_POPOVER] are now by default SPAN elements (There may be an option at a later date to specify the element)

= 0.8.5 =
* Made some functional improvements to [TBS_TWIPSY]
* Fixed [TBS_POPOVER]

= 0.8.4 =
* Fixed a quoting bug in [TBS_BLOCK]
* Added [TBS_ALERT] shortcode (see guide below for TBS_BLOCK)

= 0.8.3 =
* Added option to inline "style" labels, blocks, and code.
* Added Shortcode [TBS_BLOCKQUOTE] : produces a Twitter Bootstrap styled BLOCKQUOTE with parameter "source" for citing source 
[Guide on Blockquote shortcode here](http://www.hostliketoast.com/2011/12/master-twitter-bootstrap-using-wordpress-shortcodes-part-3-blockquotes/ "Master Twitter Bootstrap Blockquotes using WordPress Shortcodes")

= 0.8.2 =
* Added option to "style" buttons inline.
* Some bug fixes with shortcodes.

= 0.8 =
* This is a huge release. We have implemented some of the major Twitter Bootstrap feature through [Wordpress Shortcodes](http://www.hostliketoast.com/2011/12/how-extend-wordpress-powerful-shortcodes/ "What are WordPress Shortcodes?").
* Shortcode [TBS_BUTTON] : produces a Twitter Bootstrap styled BUTTON [Guide on Button shortcode here](http://www.hostliketoast.com/2011/12/master-twitter-bootstrap-using-wordpress-shortcodes-part-1-buttons/ "Master Twitter Bootstrap Buttons using WordPress Shortcodes")
* Shortcode [TBS_LABEL] : produces a Twitter Bootstrap styled LABEL [Guide on Label shortcode here](http://www.hostliketoast.com/2011/12/master-twitter-bootstrap-using-wordpress-shortcodes-part-2-labels/ "Master Twitter Bootstrap Labels using WordPress Shortcodes")
* Shortcode [TBS_BLOCK] : produces a Twitter Bootstrap styled BLOCK Messages [Guide on Blockquote shortcode here](http://www.hostliketoast.com/2011/12/master-twitter-bootstrap-using-wordpress-shortcodes-part-4-alerts-and-block-messages/ "Master Twitter Bootstrap Labels using WordPress Shortcodes")
* Shortcode [TBS_CODE] : produces a Twitter Bootstrap styled CODE BLOCK
* Shortcode [TBS_TWIPSY] : produces a Twitter Bootstrap TWIPSY mouse over effect [Guide on Twipsy shortcode here](http://www.hostliketoast.com/2011/12/master-twitter-bootstrap-using-wordpress-shortcodes-part-5-twipsy-rollovers/ "Master Twitter Bootstrap Labels using WordPress Shortcodes")
* Shortcode [TBS_POPOVER] : produces a Twitter Bootstrap POPOVER window
* Shortcode [TBS_DROPDOWN] + [TBS_DROPDOWN_OPTION] : produces a Twitter Bootstrap styled DROPDOWN MENU
* Shortcode [TBS_TABGROUP] + [TAB] : produces a Twitter Bootstrap TAB! Now you can create TAB'd content in your posts!
* More documentation will be forthcoming in the [iControlWP WordPress Plugins Page](http://icwp.io/p "iControlWP WordPress Twitter Bootstrap Plugin").

= 0.7 =
* Quick fix for Login and Register pages - for now there is no Bootstrap added to the login/register pages whatsoever.

= 0.6 =
* Updated to account for the latest version of Twitter Bootsrap version 1.4.0

= 0.5 =
* Re-added the attempt utilize W3 Total Cache "flush all" if the plugin is active (compatible with W3 Total Cache v0.9.2.4)
* Added some more screenshots to the docs

= 0.4 =
* Added the ability to include your own custom CSS file using a URL for the source. This custom CSS
file will be linked immediately after the bootstrap CSS (if you add it).

= 0.3 =
* Added support for 'Bootstrap, from Twitter' Javascript libraries. You can now select which of the invididual JS libraries to include.
* Inclusion of Javascript libraries is dependent upon selection of Twitter Bootstrap CSS. If this is not selected, no Javascript is added.
* Option to load Javascript files in the "HEAD" (using wp_head). The default, and recommended, is just before the closing html "BODY" (using wp_footer).

= 0.2 =
* Updated Twitter Bootstrap CSS link to version 1.3.0.

= 0.1.2 =
* Removed support for automatic W3 Total Cache flushing as the author of the other plugin has altered his code. This
is temporary until we fix.

= 0.1.1 =
* bugfix for 'None' option. Update recommended.

= 0.1 =
* First public release
* Allows you to select 1 of 3 possible styles: YUI 2 Reset; normalize CSS; or Twitter Bootstrap CSS.
* YUI 2 version 2.9.0
* Normalize CSS version 2011-08-31
* Twitter Bootstrap version 1.2.0

== Upgrade Notice ==

= 2.3.2-1 =
* UPDATED: Bootstrap version to latest release v2.3.2