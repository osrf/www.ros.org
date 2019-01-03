<?php
/**
 * Plugin Name: Disable Search
 * Version:     1.6.1
 * Plugin URI:  http://coffee2code.com/wp-plugins/disable-search/
 * Author:      Scott Reilly
 * Author URI:  http://coffee2code.com/
 * Text Domain: disable-search
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Description: Disable the built-in front-end search capabilities of WordPress.
 *
 * Compatible with WordPress 4.6 through 4.9+.
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/plugins/disable-search/
 *
 * @package Disable_Search
 * @author  Scott Reilly
 * @version 1.6.1
 */

/*
 * TODO:
 * - Rather than responding to search requests with a 404 error, allow response to be configurable:
 *     404
 *     404 with custom error message (e.g. Search has been disabled)
 *     Redirect to a post or page
 *     Redirect back home (but set some sort of flag that can be detected so the theme can display a message)
 *     Act as if search was performed but no results were found
 * - Filter to allows searching to be conditionally enabled (query obj as arg)
 * - Allow front-end searches for admins (and/or all logged in users?), via a Reading option and/or filters
 */

/*
	Copyright (c) 2008-2018 by Scott Reilly (aka coffee2code)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'c2c_DisableSearch' ) ) :

class c2c_DisableSearch {

	/**
	 * Returns version of the plugin.
	 *
	 * @since 1.3
	 */
	public static function version() {
		return '1.6.1';
	}

	/**
	 * Prevent instantiation.
	 *
	 * @since 1.6
	 */
	private function __construct() {}

	/**
	 * Prevent unserializing an instance.
	 *
	 * @since 1.6
	 */
	private function __wakeup() {}

	/**
	 * Initializes the plugin.
	 */
	public static function init() {
		add_action( 'plugins_loaded', array( __CLASS__, 'do_init' ) );
	}

	/**
	 * Performs actual initialization tasks.
	 *
	 * @since 1.5
	 */
	public static function do_init() {
		// Load textdomain.
		load_plugin_textdomain( 'disable-search' );

		// Register hooks.
		add_action( 'widgets_init',    array( __CLASS__, 'disable_search_widget' ), 1 );
		if ( ! is_admin() ) {
			add_action( 'parse_query', array( __CLASS__, 'parse_query' ), 5 );
		}
		add_filter( 'get_search_form', array( __CLASS__, 'get_search_form' ), 999 );

		add_action( 'admin_bar_menu',  array( __CLASS__, 'admin_bar_menu' ), 11 );
	}

	/**
	 * Disables the built-in WP search widget.
	 */
	public static function disable_search_widget() {
		unregister_widget( 'WP_Widget_Search' );
	}

	/**
	 * Returns nothing as the search form.
	 *
	 * @param  string $form The search form to be displayed.
	 *
	 * @return string Always returns an empty string.
	 */
	public static function get_search_form( $form ) {
		return '';
	}

	/**
	 * Unsets all search-related variables in WP_Query object and sets the
	 * request as a 404 if a search was attempted.
	 *
	 * @param WP_Query $obj A query object.
	 */
	public static function parse_query( $obj ) {
		if ( $obj->is_search && $obj->is_main_query() ) {
			unset( $_GET['s'] );
			unset( $_POST['s'] );
			unset( $_REQUEST['s'] );
			unset( $obj->query['s'] );
			$obj->set( 's', '' );
			$obj->is_search = false;
			$obj->set_404();
			status_header( 404 );
			nocache_headers();
		}
	}

	/**
	 * Removes the search item from the admin bar.
	 *
	 * @since 1.6
	 *
	 * @param WP_Admin_Bar $wp_admin_bar The WP admin bar object.
	 */
	public static function admin_bar_menu( $wp_admin_bar ) {
		$wp_admin_bar->remove_menu( 'search' );
	}

} // end c2c_DisableSearch


c2c_DisableSearch::init();

endif; // end if !class_exists()
