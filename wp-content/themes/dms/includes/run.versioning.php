<?php 


/**
 * Check if version has changed.
 */

$installed = get_theme_mod( 'pagelines_version' );
$actual = pl_get_theme_data( get_template_directory(), 'Version' );

// if new version do some housekeeping.
if ( version_compare( $actual, $installed ) > 0 ) {

		delete_transient( 'pagelines_theme_update' );
		delete_transient( 'pagelines_extend_themes' );
		delete_transient( 'pagelines_extend_sections' );
		delete_transient( 'pagelines_extend_plugins' );
		delete_transient( 'pagelines_extend_integrations' );
		delete_transient( 'pagelines_sections_cache' );
		remove_theme_mod( 'available_updates' );
		remove_theme_mod( 'pending_updates' );
		define( 'PL_CSS_FLUSH', true );
}
set_theme_mod( 'pagelines_version', $actual );
set_theme_mod( 'pagelines_child_version', pl_get_theme_data( get_stylesheet_directory(), 'Version' ) );
