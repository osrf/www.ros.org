<?php

// ====================================
// = Build PageLines Option Interface =
// ====================================

// Add our menus where they belong.
add_action( 'admin_menu', 'pagelines_add_admin_menu' );

add_action('admin_menu', 'pagelines_add_admin_menus');

if( ! function_exists( 'pagelines_add_admin_menu' ) ) {
	
	function pagelines_add_admin_menus() {}
	
	function pagelines_add_admin_menu() {
		global $_pagelines_account_hook;
		$_pagelines_account_hook = add_theme_page( PL_MAIN_DASH, __( 'DMS Tools', 'pagelines' ), 'edit_theme_options', PL_MAIN_DASH, 'pagelines_build_account_interface' );
	}
}

// Build option interface


/**
 * Build Extension Interface
 * Will handle adding additional sections, plugins, child themes
 */
function pagelines_build_account_interface(){
	
	$dms_tools = new EditorAdmin;

	$args = array(
		'title'			=> __( 'PageLines DMS', 'pagelines' ),
		'callback'		=> array( $dms_tools, 'admin_array' ),
	);
	$optionUI = new DMSOptionsUI( $args );
}

// Admin CSS
add_action( 'admin_head', 'load_head' );
function load_head(){

	printf( '<link rel="stylesheet" href="%s/admin.css?ver=%s" type="text/css" media="screen" />', PL_ADMIN_URI, PL_CORE_VERSION );

}


/**
 * This is a necessary go-between to get our scripts and boxes loaded
 * on the theme settings page only, and not the rest of the admin
 */
add_action( 'admin_menu', 'pagelines_theme_settings_init' );
function pagelines_theme_settings_init() {
	global $_pagelines_account_hook;
	
	add_action( "admin_print_scripts-{$_pagelines_account_hook}", 'pagelines_theme_settings_scripts' );


}



// JS
function pagelines_theme_settings_scripts() {

	
	wp_enqueue_script( 'pagelines-admin', PL_JS . '/admin.pagelines.js', array( 'jquery' ), PL_CORE_VERSION );
	
	pl_enqueue_codemirror();

}



add_action( 'admin_init', 'pagelines_check_customizer' );
function pagelines_check_customizer() {
	
	global $pagenow;
	
	if($pagenow == 'customize.php')
		wp_redirect( PL_ACTIVATE_URL );
		
	if( isset($_GET['activated'] ) && $pagenow == "themes.php" )
		wp_redirect( PL_ACTIVATE_URL );
}


/**
 * Checks if PHP5
 *
 * Tests for installed version of PHP higher than 5.0 and prints message if version is found to be lower.
 *
 * @package PageLines DMS
 * @subpackage Functions Library
 * @since 4.0.0
 */
add_action( 'pagelines_before_optionUI', 'pagelines_check_php' );
function pagelines_check_php(){
	
	if( floatval( phpversion() ) < 5.0 ){
		printf( __( "<div class='config-error'><h2>PHP Version Problem</h2>Looks like you are using PHP version: <strong>%s</strong>. To run this framework you will need PHP <strong>5.0</strong> or better...<br/><br/> Don't worry though! Just check with your host about a quick upgrade.</div>", 'pagelines' ), phpversion() );
	}
	
}


/**
 * Setup Versions and flush caches.
 *
 * @package PageLines DMS
 * @since   2.2
 */
add_action( 'admin_init', 'pagelines_set_versions' );
function pagelines_set_versions() {
	if ( current_user_can( 'edit_themes' ) ) {
		if( defined( 'PL_LESS_DEV' ) && PL_LESS_DEV ) {
			PageLinesRenderCSS::flush_version( false );
			delete_transient( 'pagelines_sections_cache' );
		}
	}
	set_theme_mod( 'pagelines_version', pl_get_theme_data( get_template_directory(), 'Version' ) );
	set_theme_mod( 'pagelines_child_version', pl_get_theme_data( get_stylesheet_directory(), 'Version' ) );
}

// make sure were running out of 'pagelines' folder.
add_action( 'admin_notices', 'pagelines_check_folders' );
function pagelines_check_folders() {

		$folder = basename( get_template_directory() );

		if( 'dms' == $folder )
			return;

		echo '<div class="updated">';
		printf( "<p><h3>Install Error!</h3><br />PageLines DMS must be installed in a folder called 'dms' to work with child themes and extensions.<br /><br />Current path: %s<br /></p>", get_template_directory() );
		echo '</div>';
}

add_action( 'activate_plugin', 'pagelines_purge_sections_cache' );
add_action( 'deactivate_plugin', 'pagelines_purge_sections_cache' );
function pagelines_purge_sections_cache() {
	delete_transient( 'pagelines_sections_cache' );
}

