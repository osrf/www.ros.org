<?php
/**
 *  This file adds the main menu, free version must be under appearance, this file is NOT in the free version.
 *  @package PageLines DMS
 *  @since 1.0.1
 *
 */
add_action( 'pagelines_max_mem', create_function('',"@ini_set('memory_limit',WP_MAX_MEMORY_LIMIT);") );

function pagelines_add_admin_menus() {

	global $_pagelines_account_hook;
	
	$_pagelines_account_hook = pagelines_insert_menu( PL_MAIN_DASH, __( 'Dashboard', 'pagelines' ), 'edit_theme_options', PL_MAIN_DASH, 'pagelines_build_account_interface' );

}

/**
 *
 * PageLines menu wrapper
 */
function pagelines_insert_menu( $page_title, $menu_title, $capability, $menu_slug, $function ) {

	return add_submenu_page( PL_MAIN_DASH, $page_title, $menu_title, $capability, $menu_slug, $function );

}

/**
 * Full version menu wrapper.
 *
 */
function pagelines_add_admin_menu() {
		global $menu;

		// Create the new separator
		$menu['2.995'] = array( '', 'edit_theme_options', 'separator-pagelines', '', 'wp-menu-separator' );

		// Create the new top-level Menu
		add_menu_page( 'PageLines', 'PageLines', 'edit_theme_options', PL_MAIN_DASH, 'pagelines_build_account_interface', 'div', '2.996' );
}