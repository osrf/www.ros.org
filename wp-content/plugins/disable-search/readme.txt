=== Disable Search ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: search, disable, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.6
Tested up to: 5.2
Stable tag: 1.7.1

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

= 1.7.1 (2019-06-17) =
* Unit tests:
    * Change: Update unit test install script and bootstrap to use latest WP unit test repo
    * New: Test that the plugin hooks `plugins_loaded` for initialization
* Change: Note compatibility through WP 5.2+
* Change: Add link to CHANGELOG.md in README.md

= 1.7 (2019-03-27) =
* New: Add CHANGELOG.md file and move all but most recent changelog entries into it
* Change: Initialize plugin on 'plugins_loaded' action instead of on load
* Change: Merge `do_init()` into `init()`
* Unit tests:
    * Fix: Discontinue testing deprecated `is_comments_popup` condition
    * Fix: Use `file_exists()` instead of `locate_template()` to verify presence of file in theme (the latter is unreliable since it is based on constants)
* Change: Note compatibility through WP 5.1+
* Change: Add README.md link to plugin's page in Plugin Directory
* Change: Update copyright date (2019)
* Change: Update License URI to be HTTPS
* Change: Split paragraph in README.md's "Support" section into two

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

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/disable-search/blob/master/CHANGELOG.md)._


== Upgrade Notice ==

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
