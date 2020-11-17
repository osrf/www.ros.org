<?php

defined( 'ABSPATH' ) or die();

class Disable_Search_Test extends WP_UnitTestCase {

	/*
	 *
	 * HELPER FUNCTIONS
	 *
	 */


	public function output_search_form() {
		return '<form><label for="s"><input type="search" name="s" /></label><input type="submit" name="Submit" /></form>';
	}

	public function create_posts() {
		$posts = array();
		$posts[] = $this->factory->post->create( array( 'post_content' => 'the man from gallifrey', 'post_title' => 'gallifrey day' ) );
		$posts[] = $this->factory->post->create( array( 'post_content' => 'does not have the search word', 'post_title' => 'no search word here' ) );
		return $posts;
	}

	/**
	 * Check each of the WP_Query is_* functions/properties against expected boolean value.
	 *
	 * Any properties that are listed by name as parameters will be expected to be true; any others are
	 * expected to be false. For example, assertQueryTrue('is_single', 'is_feed') means is_single()
	 * and is_feed() must be true and everything else must be false to pass.
	 *
	 * @since 1.6.1
	 * @link https://unit-tests.svn.wordpress.org/trunk/tests/query/conditionals.php
	 *
	 * @param string $prop,... Any number of WP_Query properties that are expected to be true for the current request.
	 */
	public function assertQueryTrue( ...$prop ) {
		global $wp_query;
		$all = array(
			'is_single', 'is_preview', 'is_page', 'is_archive', 'is_date', 'is_year', 'is_month', 'is_day', 'is_time',
			'is_author', 'is_category', 'is_tag', 'is_tax', 'is_search', 'is_feed', 'is_comment_feed', 'is_trackback',
			'is_home', 'is_404', 'is_paged', 'is_admin', 'is_attachment', 'is_singular', 'is_robots',
			'is_posts_page', 'is_post_type_archive',
		);
		$true = func_get_args();

		$passed = true;
		$not_false = $not_true = array(); // properties that were not set to expected values

		foreach ( $all as $query_thing ) {
			$result = is_callable( $query_thing ) ? call_user_func( $query_thing ) : $wp_query->$query_thing;

			if ( in_array( $query_thing, $true ) ) {
				if ( ! $result ) {
					array_push( $not_true, $query_thing );
					$passed = false;
				}
			} else if ( $result ) {
				array_push( $not_false, $query_thing );
				$passed = false;
			}
		}

		$message = '';
		if ( count($not_true) )
			$message .= implode( $not_true, ', ' ) . ' should be true. ';
		if ( count($not_false) )
			$message .= implode( $not_false, ', ' ) . ' should be false.';
		$this->assertTrue( $passed, $message );
	}


	//
	//
	// DATA PROVIDERS
	//
	//


	public static function get_default_hooks() {
		return array(
			array( 'action', 'widgets_init',                 'disable_search_widget',   1 ),
			array( 'action', 'parse_query',                  'parse_query',             5 ),
			array( 'filter', 'get_search_form',              'get_search_form',       999 ),
			array( 'action', 'admin_bar_menu',               'admin_bar_menu',         11 ),
			array( 'filter', 'disable_wpseo_json_ld_search', '__return_true',          10, false ),
		);
	}


	//
	//
	// TESTS
	//
	//


	public function test_class_name() {
		$this->assertTrue( class_exists( 'c2c_DisableSearch' ) );
	}

	public function test_version() {
		$this->assertEquals( '1.8.1', c2c_DisableSearch::version() );
	}

	public function test_hooks_plugins_loaded() {
		$this->assertEquals( 10, has_action( 'plugins_loaded', array( 'c2c_DisableSearch', 'init' ) ) );
	}

	/**
	 * @dataProvider get_default_hooks
	 */
	public function test_default_hooks( $hook_type, $hook, $function, $priority, $class_method = true ) {
		$callback = $class_method ? array( 'c2c_DisableSearch', $function ) : $function;

		$prio = $hook_type === 'action' ?
			has_action( $hook, $callback ) :
			has_filter( $hook, $callback );

		$this->assertNotFalse( $prio );
		if ( $priority ) {
			$this->assertEquals( $priority, $prio );
		}
	}

	public function test_no_search_form_apppears_even_if_searchform_php_exists() {
		$old_theme = get_stylesheet();
		$theme = wp_get_theme( 'twentyseventeen' );
		$this->assertTrue( $theme->exists() );
		switch_theme( $theme->get_stylesheet() );
		$this->assertEquals( 'twentyseventeen', get_stylesheet() );
		// Verify that the searchform.php file actually exists.
		$this->assertTrue( file_exists( $theme->theme_root . '/twentyseventeen/searchform.php' ) );
		// Now verify that the plugin prevents it from being used.
		$this->assertEmpty( get_search_form( false ) );
		// Ensure we restored the original theme.
		switch_theme( $old_theme );
		$this->assertEquals( $old_theme, get_stylesheet() );
	}

	public function test_no_search_form_appears_if_filter_is_used_to_show_one() {
		add_filter( 'get_search_form', array( $this, 'output_search_form' ) );

		$this->assertEmpty( get_search_form( false ) );
	}

	/**
	 * NOTE: Hacky as hell! Delves directly into internal handling of widgets by WP
	 * by accessing an implicitly public class variable. WP could change this at
	 * any release. Better if is_widget_registered() gets created.
	 */
	public function test_search_widget_is_no_longer_available() {
		$this->assertNotContains( 'WP_Widget_Search', array_keys( $GLOBALS['wp_widget_factory']->widgets ) );
	}

	public function test_search_request_returns_no_results_for_main_query() {
		$this->create_posts();

		$this->go_to( '?s=gallifrey' );

		$this->assertTrue( $GLOBALS['wp_query']->is_main_query() );
		$this->assertEmpty( get_query_var( 's' ) );
		$this->assertQueryTrue( 'is_404' );
	}

	public function test_search_request_returns_results_for_main_query_if_plugin_disabled() {
		// Remove query altering hook
		remove_action( 'parse_query', array( 'c2c_DisableSearch', 'parse_query' ), 5 );

		$this->create_posts();

		$this->go_to( '?s=gallifrey' );

		$this->assertTrue( $GLOBALS['wp_query']->is_main_query() );
		$this->assertEquals( get_query_var( 's' ), 'gallifrey' );
		$this->assertQueryTrue( 'is_search' );
		$this->assertEquals( 1, count( $GLOBALS['wp_query']->posts ) );

		// Restore query altering hook
		add_action( 'parse_query', array( 'c2c_DisableSearch', 'parse_query' ), 5 );
	}

	public function test_search_on_custom_query_returns_results() {
		list( $post_id1, $post_id2 ) = $this->create_posts();

		$query = new WP_Query;
		$posts = $query->query( 's=gallifrey' );

		$this->assertFalse( $query->is_main_query() );
		$this->assertTrue( $query->is_search );
		$this->assertNotEmpty( $posts );
		$this->assertEquals( 1, count( $posts ) );
		$this->assertEquals( $post_id1, $posts[0]->ID );
	}

	/*
	 * TESTS AFTER THIS SHOULD ASSUME THEY ARE IN THE ADMIN AREA
	 */

	// This should be the first of the true admin area tests and is
	// necessary to set the environment to be the admin area.
	public function test_in_admin_area() {
		define( 'WP_ADMIN', true );

		$this->assertTrue( is_admin() );
	}

	public function test_does_not_hook_parse_query_in_admin() {
		// Remove query altering hook.
		remove_action( 'parse_query', array( 'c2c_DisableSearch', 'parse_query' ), 5 );
		// Also remove a hook as a way to verify hooks are otherwise re-registered.
		remove_action( 'widgets_init', array( 'c2c_DisableSearch', 'disable_search_widget' ), 1 );

		// Refire registration of hooks.
		c2c_DisableSearch::init();

		$this->assertFalse( has_action( 'parse_query', array( 'c2c_DisableSearch', 'parse_query' ) ) );
		$this->assertEquals( 1, has_action( 'widgets_init', array( 'c2c_DisableSearch', 'disable_search_widget' ) ) );
	}

	public function test_backend_search_unaffected() {
		// Remove query altering hook.
		remove_action( 'parse_query', array( 'c2c_DisableSearch', 'parse_query' ), 5 );
		// Refire registration of hooks.
		c2c_DisableSearch::init();

		list( $post_id1, $post_id2 ) = $this->create_posts();

		$this->go_to( 'wp-admin/edit.php?s=gallifrey&post_status=all&post_type=post' );

		$this->assertTrue( $GLOBALS['wp_query']->is_main_query() );
		$this->assertEquals( get_query_var( 's' ), 'gallifrey' );
		$this->assertQueryTrue( 'is_admin', 'is_search' );
		$this->assertEquals( 1, count( $GLOBALS['wp_query']->posts ) );
		$this->assertEquals( $post_id1, $GLOBALS['wp_query']->posts[0]->ID );
	}

}
