<?php

/**
 * REQUIRE THE FRAMEWORK FILES
 * --- This is some checking, and you should not have to touch this code (unless directed) ---
 * 3.4 compat added
 * Fix for php5.2
 */

if ( function_exists( 'wp_get_theme' ) ) {
	$parent_folder = wp_get_theme( basename( dirname( __FILE__ ) ) )->get( 'Template' );		
} else {
	$theme_data = get_theme_data(STYLESHEETPATH . '/style.css');
	$parent_folder = $theme_data['Template'];
}
$init_file = WP_CONTENT_DIR .'/themes/' . $parent_folder . '/functions.php';

if ( file_exists( $init_file ) )
	require_once( $init_file );
else
	add_action( 'template_redirect', create_function( '', 'echo "<p>Unable to locate the main framework files! Make sure the parent theme folder is named correctly.</p>";' ) );
