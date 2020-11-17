=== Disable Search ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: search, disable, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.6
Tested up to: 5.5
Stable tag: 1.8.1

Disable the built-in front-end search capabilities of WordPress.


== Description ==

This plugin prevents WordPress from allowing and handling any search requests from the front-end of the site. Specifically, this plugin:

* Prevents the search form from appearing if the theme uses the standard `get_search_form()` function to display the search form.
* Prevents the search form from appearing if the theme uses a searchform.php template
* Prevents the search item from appearing in the admin tool bar when shown on the front-end.
* Disables the search widget.
  * Removes the Search widget from the list of available widgets
  * Deactivates any search widgets currently in use in any sidebars (they are hidden, not deleted; they'll still be in the proper locations if this plugin gets deactivated)
* With or without the search form, the plugin prevents any direct or manual requests by visitors, via either GET or POST requests, from actually returning any search results.
* Submitted attempts at a search will be given a 404 File Not Found response, rendered by your site's 404.php template, if present.
* Disables output of `SearchAction` in SEO schema by the [Yoast SEO](https://wordpress.org/plugins/wordpress-seo/) plugin.

The plugin only affects search on the front-end of the site. It does not disable searching in the admin section of the site.

Links: [Plugin Homepage](https://coffee2code.com/wp-plugins/disable-search/) | [Plugin Directory Page](https://wordpress.org/plugins/disable-search/) | [GitHub](https://github.com/coffee2code/disable-search/) | [Author Homepage](https://coffee2code.com)


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

= 1.8.1 (2020-09-07) =
* Change: Restructure unit test file structure
    * New: Create new subdirectory `phpunit/` to house all files related to unit testing
    * Change: Move `bin/` to `phpunit/bin/`
    * Change: Move `tests/bootstrap.php` to `phpunit/`
    * Change: Move `tests/` to `phpunit/tests/`
    * Change: Rename `phpunit.xml` to `phpunit.xml.dist` per best practices
* Change: Note compatibility through WP 5.5+

= 1.8 (2020-06-02) =
* New: Disable output of `SearchAction` in SEO schema by Yoast SEO. Props @galengidman.
* New: Add TODO.md and move existing TODO list from top of main plugin file into it (and add to it)
* Change: Use HTTPS for link to WP SVN repository in bin script for configuring unit tests
* Change: Note compatibility through WP 5.4+
* Change: Update links to coffee2code.com to be HTTPS
* Unit tests:
    * New: Add tests for hooking actions and filters
    * New: Add test for backend searches not being affected
    * Change: Remove unnecessary unregistering of hooks and thusly delete `tearDown()`

= 1.7.2 (2019-12-12) =
* Change: Note compatibility through WP 5.3+
* Change: Unit tests: Change method signature of `assertQueryTrue()` to match parent's update to use the spread operator
* Change: Update copyright date (2020)

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/disable-search/blob/master/CHANGELOG.md)._


== Upgrade Notice ==

= 1.8.1 =
Trivial update: Restructured unit test file structure and noted compatibility through WP 5.5+.

= 1.8 =
Minor update: Disabled output of SearchAction from schema output by the Yoast SEO plugin, added TODO.md file, updated a few URLs to be HTTPS, added more unit tests, and noted compatibility through WP 5.4+

= 1.7.2 =
Trivial update: noted compatibility through WP 5.3+, fixed minor unit test warning, and updated copyright date (2020).

= 1.7.1 =
Trivial update: modernized unit tests and noted compatibility through WP 5.2+

= 1.7 =
Minor update: tweaked plugin initialization, noted compatibility through WP 5.1+, created CHANGELOG.md to store historical changelog outside of readme.txt, and updated copyright date (2019)

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
