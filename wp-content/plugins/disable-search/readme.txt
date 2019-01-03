=== Disable Search ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: search, disable, coffee2code
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.6
Tested up to: 4.9
Stable tag: 1.6.1

Disable the built-in front-end search capabilities of WordPress.


== Description ==

Prevent WordPress from allowing and handling any search requests for the site. Specifically, this plugin:

* Prevents the search form from appearing if the theme uses the standard `get_search_form()` function to display the search form.
* Prevents the search form from appearing if the theme uses a searchform.php template
* Prevents the search item from appearing in the admin tool bar when shown on the front-end.
* Disables the search widget.
  * Removes the Search widget from the list of available widgets
  * Deactivates any search widgets currently in use in any sidebars (they are hidden, not deleted; they'll still be in the proper locations if this plugin gets deactivated)
* With or without the search form, the plugin prevents any direct or manual requests by visitors, via either GET or POST requests, from actually returning any search results.
* Submitted attempts at a search will be given a 404 File Not Found response, rendered by your site's 404.php template, if present.

The plugin only affects search on the front-end of the site. It does not disable searching in the admin section of the site.

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/disable-search/) | [Plugin Directory Page](https://wordpress.org/plugins/disable-search/) | [GitHub](https://github.com/coffee2code/disable-search/) | [Author Homepage](http://coffee2code.com)


== Installation ==

1. Install via the built-in WordPress plugin installer. Or download and unzip `disable-search.zip` inside the plugins directory for your site (typically `wp-content/plugins/`)
1. Activate the plugin through the 'Plugins' admin menu in WordPress


== Frequently Asked Questions ==

= Will this disable the search capabilities in the admin section of the blog? =

No.

= Will this prevent Google and other search engines from searching my site? =

No. This only disables WordPress's capabilities with regards to search.

= Why do I still see a search form on my site despite having activated this plugin? =

The most likely cause for this is a theme that has the markup for the search form hardcoded into one or more of the theme's template files (excluding searchform.php). This is generally frowned upon nowadays (the theme should be calling `get_search_form()` or using searchform.php to get the search form). There is no way for this plugin to prevent this hardcoded form from being displayed.

However, even if this is the case, the form won't work (thanks to this plugin), but it will still be displayed.

= Is this plugin GDPR-compliant? =

Yes. This plugin does not collect, store, or disseminate any information from any users or site visitors.

= Does this plugin include unit tests? =

Yes.


== Changelog ==

= 1.6.1 (2018-05-19) =
* New: Add README.md
* New: Add FAQ indicating that the plugin is GDPR-compliant
* Unit tests:
    * Change: Make local copy of `assertQueryTrue()`; apparently it's (now?) a test-specific assertion and not a globally aware assertion
    * Change: Enable and update `test_no_search_form_appears_even_if_searchform_php_exists()` to use TwentySeventeen theme, since it has searchform.php
    * Change: Minor whitespace tweaks to bootstrap
* Change: Add GitHub link to readme
* Change: Note compatibility through WP 4.9+
* Change: Update copyright date (2018)
* Change: Update installation instruction to prefer built-in installer over .zip file

= 1.6 (2017-02-21) =
* New: Disable search item from front-end admin bar
* Change: Prevent object instantiation
    * Add private `__construct()`
    * Add private `__wakeup()`
* Change: Update unit test bootstrap
    * Default `WP_TESTS_DIR` to `/tmp/wordpress-tests-lib` rather than erroring out if not defined via environment variable
    * Enable more error output for unit tests
* Change: Note compatibility through WP 4.7+
* Change: Remove support for WordPress older than 4.6 (should still work for earlier versions back to WP 3.6)
* Change: Update copyright date (2017)
* New: Add LICENSE file

= 1.5.1 (2016-01-15) =
* Bugfix: Declare `do_init()` as public.

= 1.5 (2016-01-14) =
* Add: Set 404 HTTP status header for disabled search requests.
* Add: Define 'Text Domain' in plugin header and load it.
* Add: Create empty index.php to prevent files from being listed if web server has enabled directory listings.
* Change: Perform all hook registering during plugins_loaded action.
* Change: Explicitly declare methods in unit tests as public.
* Change: Note compatibility through WP 4.4+.
* Change: Update copyright date (2016).

= 1.4.2 (2015-08-23) =
* Change: Note compatibility through WP 4.3+.
* Change: Minor inline docs changes.

= 1.4.1 (2015-02-15) =
* Add trivial unit tests for plugin version and class name
* Note compatibility through WP 4.1+
* Update copyright date (2015)
* Add plugin icon

= 1.4 (2013-12-15) =
* Change to hook 'get_search_form' at lower priority so it runs after anything else also using the filter
* Change to only affect main query
* Remove admin nag for alerting about the presence of searchform.php in a theme since this no longer matters
* Add unit tests
* Note compatibility through WP 3.8+
* Change minimum required compatibility to WP 3.6
* Update copyright date (2014)
* Add banner
* Many changes to readme.txt documentation (namely to pare out a lot of stuff relating to suppression of searchform.php which has since been made possible in WP core)
* Change description
* Change donate link

= 1.3.1 (unreleased) =
* Don't show searchform.php admin nag if user doesn't have 'edit_themes' cap
* Add check to prevent execution of code if file is directly accessed
* Re-license as GPLv2 or later (from X11)
* Add 'License' and 'License URI' header tags to readme.txt and plugin file
* Remove ending PHP close tag
* Note compatibility through WP 3.5+
* Update copyright date (2013)

= 1.3 =
* Add notice to main themes and plugins admin pages if active theme has searchform.php template
* Note compatibility through WP 3.3+
* Add version() to return plugin version
* Add more documentation and FAQ questions to readme.txt
* Add link to plugin directory page to readme.txt
* Update copyright date (2012)

= 1.2.1 =
* Note compatibility through WP 3.2+
* Tiny code formatting change (spacing)
* Fix plugin homepage and author links in description in readme.txt

= 1.2 =
* Switch from object instantiation to direct class function invocation
* Explicitly declare all functions public static
* Add development note
* Add additional FAQ question
* Note compatibility through WP 3.1+
* Update copyright date (2011)

= 1.1.1 =
* Fix disabling of search widget
* Move class instantiation inside of if(!class_exists()) check
* Rename class from 'DisableSearch' to 'c2c_DisableSearch'
* Store object instance in global variable 'c2c_disable_search' for possible external manipulation
* Note compatibility with WP 3.0+
* Minor code reformatting (spacing)
* Remove documentation and instructions from top of plugin file (all of that and more are contained in readme.txt)
* Add Upgrade Notice section to readme.txt

= 1.1 =
* Disable/unregister search widget
* Add PHPDoc documentation
* Minor formatting tweaks
* Note compatibility with WP 2.9+
* Drop compatibility with WP older than 2.8
* Update copyright date
* Update readme.txt (including adding Changelog)

= 1.0 =
* Initial release


== Upgrade Notice ==

= 1.6.1 =
Minor update: fixed unit tests, added README.md, noted GDPR compliance, noted compatibility through WP 4.9+. and updated copyright date (2018)

= 1.6 =
Minor update: disabled search item from front-end admin bar, compatibility is now WP 4.6 through 4.7+, and other minor tweaks

= 1.5.1 =
Bugfix release for bug introduced in v1.5.

= 1.5 =
Minor update: set 404 HTTP status for requests to disabled search requests, verified compatibility through WP 4.4, updated copyright date (2016)

= 1.4.2 =
Trivial update: noted compatibility through WP 4.3+

= 1.4.1 =
Trivial update: noted compatibility through WP 4.1+ and updated copyright date (2015)

= 1.4 =
Recommended update: removed admin nag about presence of searchform.php; only affect main query; added unit tests; noted compatibility through WP 3.8+

= 1.3.1 =
Trivial update: only show admin notice for users with 'edit_themes' capability; noted compatibility through WP 3.5+; explicitly stated license

= 1.3 =
Minor update: add notice to main themes and plugins admin pages if active theme has searchform.php template; noted compatibility through WP 3.3+.

= 1.2.1 =
Trivial update: noted compatibility through WP 3.2+ and minor documentation tweaks.

= 1.2 =
Trivial update: slight implementation change; noted compatibility through WP 3.1+ and updated copyright date

= 1.1.1 =
Minor update. Highlights: renamed class and other back-end tweaks; added note about searchform.php; noted compatibility with WP 3.0+.
