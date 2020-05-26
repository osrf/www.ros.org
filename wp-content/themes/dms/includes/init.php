<?php
/**
 * This file initializes the PageLines framework
 *
 * @package PageLines DMS
 *
*/

/**
 * Run the starting hook
 */
do_action('pagelines_hook_pre', 'core'); // Hook

define('PL_INCLUDES', get_template_directory() . '/includes');

// Removed in Free/WPORG Version
if ( is_file( PL_INCLUDES . '/library.pagelines.php' ) )
	require_once( PL_INCLUDES . '/library.pagelines.php');

// Load deprecated functions
require_once( PL_INCLUDES.'/deprecated.php' );

// Run version checks and setup
require_once( PL_INCLUDES . '/run.versioning.php');

// Setup Globals
require_once( PL_INCLUDES . '/init.globals.php');

// LOCALIZATION - Needs to come after config_theme and before localized config files
require_once( PL_INCLUDES . '/run.I18n.php');

// Utility functions and hooks/filters
require_once( PL_INCLUDES . '/lib.utils.php' );

// Applied on load
require_once( PL_INCLUDES . '/lib.load.php' );

// Various elements and WP utilities
require_once( PL_INCLUDES . '/lib.elements.php' );

// Applied in head
require_once( PL_INCLUDES . '/lib.head.php' );

// Applied in body
require_once( PL_INCLUDES . '/lib.body.php' );

// Start the editor
require_once( PL_INCLUDES . '/init.editor.php' );

// V3 Editor functions --- > always load
require_once( PL_INCLUDES . '/lib.editor.php' );

// LESS Functions
require_once( PL_INCLUDES . '/less.functions.php' );

// LESS Handling -> Legacy Approach
require_once( PL_INCLUDES . '/less.legacy.php' );

// LESS Handling -> DMS Approach
require_once( PL_INCLUDES . '/less.engine.php' );

// Shortcodes
require_once( PL_INCLUDES . '/class.shortcodes.php');

// Base Section Class
require_once( PL_INCLUDES . '/class.sections.php' );

// Typography Foundry / Fonts
require_once( PL_INCLUDES . '/class.foundry.php' );

// BUILD Controller
require_once( PL_INCLUDES . '/version.php' );
	
// Run the pagelines_init Hook
pagelines_register_hook('pagelines_hook_init'); // Hook





