<?php
/**
 * Deprecated functions
 *
 * @author		Simon Prosser
 * @copyright	2011 PageLines
 */



function pagelines_layout_mode( ){

	_pl_deprecated_function( __FUNCTION__, '1.1' );

}

class PageLinesColor{
	function c(){
		_pl_deprecated_function( __FUNCTION__, '1.1', 'an alternative as PageLinesColor has been removed from core DMS.' );
	}
}

class PageLinesMetaPanel{
	function register_tab(){
		_pl_deprecated_function( __FUNCTION__, '1.1', 'an alternative as PageLinesMetaPanel has been removed from core DMS. See <a href="https://github.com/pagelines/DMS/issues/448#issuecomment-26383046">this issue</a> for more details and possible solutions.' );
	}
}

function pl_link_color() {
	_pl_deprecated_function( __FUNCTION__, '1.0' );
}

/**
*
* @TODO do
*
*/
function register_metatab($settings, $option_array, $section = '', $location = 'bottom'){



	// Have to hack around this for version 3
	if(has_action('override_metatab_register')){

		do_action('override_metatab_register', $option_array);

	} 

}

/**
 *
 *  Returns Options Array
 *
 */
function get_option_array( $load_unavailable = true ){

	// _pl_deprecated_function( __FUNCTION__, '1.1' );

	return array();


}

/**
 * pagelines_register_section()
 *
 * @since 1.0
 * @deprecated 2.0
 * @deprecated Sections are now autoloaded and registered by the framework.
 */
function pagelines_register_section() {
	_pl_deprecated_function( __FUNCTION__, '2.0', 'the CHILDTHEME/sections/ folder' );
	return;
}

/**
 * cmath()
 *
 * @since 1.0
 * @deprecated 2.0
 * @deprecated A more useful function name
 */
function cmath( $color ) {
	_pl_deprecated_function( __FUNCTION__, '2.0', 'loadmath' );
	return new PageLinesColor( $color );
}

function pl_get_theme_data( $stylesheet = null, $header = 'Version') {

	if ( function_exists( 'wp_get_theme' ) ) {
		return wp_get_theme( basename( $stylesheet ) )->get( $header );
	} else {
		$data = get_theme_data( sprintf( '%s/themes/%s/style.css', WP_CONTENT_DIR, basename( $stylesheet ) ) );
		return $data[ $header ];
	}
}

function pl_get_themes() {

	if ( ! class_exists( 'WP_Theme' ) )
		return get_themes();

	$themes = wp_get_themes();

	foreach ( $themes as $key => $theme ) {
		$theme_data[$key] = array(
			'Name'			=> $theme->get('Name'),
			'URI'			=> $theme->display('ThemeURI', true, false),
			'Description'	=> $theme->display('Description', true, false),
			'Author'		=> $theme->display('Author', true, false),
			'Author Name'	=> $theme->display('Author', false),
			'Author URI'	=> $theme->display('AuthorURI', true, false),
			'Version'		=> $theme->get('Version'),
			'Template'		=> $theme->get('Template'),
			'Status'		=> $theme->get('Status'),
			'Tags'			=> $theme->get('Tags'),
			'Title'			=> $theme->get('Name'),
			'Template'		=> ( '' != $theme->display('Template', false, false) ) ? $theme->display('Template', false, false) : $key,
			'Stylesheet'	=> $key,
			'Stylesheet Files'	=> array(
				0 => sprintf( '%s/style.css' , $theme->get_stylesheet_directory() )
			)
		);
	}

	return $theme_data;
}

/**
 *  Determines if on a foreign integration page
 *
 * @since 2.0.0
 */
function pl_is_integration(){
	
	_pl_deprecated_function( __FUNCTION__, '1.1', 'integrations not supported' );
	
	global $pl_integration;

	return (isset($pl_integration) && $pl_integration) ? true : false;
}


/**
 *  returns the integration slug if viewing an integration page
 *
 * @since 2.0.0
 */
function pl_get_integration(){
	_pl_deprecated_function( __FUNCTION__, '1.1', 'integrations not supported' );
	
	global $pl_integration;

	return (isset($pl_integration) && $pl_integration) ? sprintf('%s', $pl_integration) : false;
}

/**
 *
 * @TODO document
 *
 */
function pagelines_special_pages(){
	_pl_deprecated_function( __FUNCTION__, '1.1', 'was used with failswith parameter which was deprecated' );
	return array('posts', 'search', 'archive', 'tag', 'category', '404');
}

/**
 * PageLines Background Cascade
 *
 * Sets background cascade for use in color mixing - default: White
 *
 * @since       2.0.b6
 *
 * @uses        ploption
 * @internal    uses filter background_cascade
 *
 * @return      mixed|void
 */
function pl_background_cascade(){
	_pl_deprecated_function( __FUNCTION__, '1.1', 'css method not supported' );
	$cascade = array(
		ploption('contentbg'),
		ploption('pagebg'),
		ploption('bodybg'),
		'#ffffff'
	);

	return apply_filters('background_cascade', $cascade);
}

/**
 * PageLines Body Background
 *
 * Body Background - default: White
 *
 * @uses        ploption
 * @internal    uses filter body_bg
 *
 * @since       2.0.b6
 *
 * @return      mixed|void
 */
function pl_body_bg(){
_pl_deprecated_function( __FUNCTION__, '1.1', 'css method not supported' );
	$cascade = array( ploption('bodybg'), '#ffffff' );

	return apply_filters('body_bg', $cascade);
}

/**
 * PageLines Nav Classes
 *
 * Returns nav menu class `sf-menu` which will allow the "superfish" JavaScript to work
 *
 * @package     PageLines Framework
 * @subpackage  Functions Library
 * @since       1.1.0
 *
 * @internal    see ..\sections\nav\script.superfish.js
 * @internal    see ..\sections\nav\style.superfish.css
 *
 * @return      string - CSS classes
 */
function pagelines_nav_classes(){

	_pl_deprecated_function( __FUNCTION__, '1.1', 'old style navigation from PL Framework' );

	$additional_menu_classes = '';

	if(ploption('enable_drop_down'))
		$additional_menu_classes .= ' sf-menu';

	return $additional_menu_classes;
}

/**
 *
 *  Fallback for navigation, if it isn't set up
 *
 *  @package PageLines DMS
 *  @subpackage Functions Library
 *  @since 1.1.0
 *
 */

// DEPRECATED for pl_nav_fallback
function pagelines_nav_fallback() {
	
	_pl_deprecated_function( __FUNCTION__, '1.1', 'old style navigation from PL Framework' );
	
	global $post; ?>

	<ul id="menu-nav" class="main-nav<?php echo pagelines_nav_classes();?>">
		<?php wp_list_pages( 'title_li=&sort_column=menu_order&depth=3'); ?>
	</ul><?php
}


function _pl_deprecated_function( $function, $version, $replacement = null ) {

	do_action( 'deprecated_function_run', $function, $replacement, $version );

	// Allow plugin to filter the output error trigger
	if ( WP_DEBUG && apply_filters( 'deprecated_function_trigger_error', true ) ) {
		if ( function_exists( '__' ) ) {
			if ( ! is_null( $replacement ) )
				trigger_error( sprintf( __('%1$s is <strong>deprecated</strong> since version %2$s of PageLines DMS! Use %3$s instead.', 'pagelines' ), $function, $version, $replacement ) );
			else
				trigger_error( sprintf( __('%1$s is <strong>deprecated</strong> since version %2$s of PageLines DMS with no alternative available.', 'pagelines' ), $function, $version ) );
		} else {
			if ( ! is_null( $replacement ) )
				trigger_error( sprintf( '%1$s is <strong>deprecated</strong> since version %2$s of PageLines DMS! Use %3$s instead.', $function, $version, $replacement ) );
			else
				trigger_error( sprintf( '%1$s is <strong>deprecated</strong> since version %2$s of PageLines DMS with no alternative available.', $function, $version ) );
		}
	}
}

/**
 * Deprecated constants, removing after a couple of revision, this will ensure store products get time to update.
 *
 */
define( 'CORE_VERSION', get_theme_mod( 'pagelines_version' ) );
define( 'THEMENAME', 'PageLines' );
define( 'CHILD_URL', get_stylesheet_directory_uri() );
define( 'CHILD_IMAGES', CHILD_URL . '/images' );
define( 'CHILD_DIR', get_stylesheet_directory() );
define( 'SECTION_ROOT', get_template_directory_uri() . '/sections');

class PageLinesOptionsUI {
	function __construct( $args ) {
		_pl_deprecated_function( 'PageLinesOptionsUI', '1.1', 'DMSOptionsUI' );
		
		return new DMSOptionsUI($args);
	}
}


/**
 * PageLines Option
 *
 * Uses controls to find and retrieve the appropriate option value
 *
 * @package PageLines DMS
 *
 * @since   ...
 *
 * @link    http://www.pagelines.com/wiki/Ploption
 *
 * @param   'key' the id of the option
 * @param   array $args
 *
 * @uses    is_pagelines_special
 * @uses    plspecial
 * @uses    plmeta
 * @uses    pldefault
 * @uses    get_ploption
 * @uses    plnewkey
 *
 * @return  bool|mixed
 */
function ploption( $key, $args = array() ){
	_pl_deprecated_function( __FUNCTION__, '1.1', 'pl_setting()' );
	$d = array(
		'subkey'	=> null, 	// Used as option key in special handling
		'post_id'	=> null, 	// Used for page/page/panel control
		'setting'	=> null, 	// Different types of serialized settings
		'clone_id'	=> null,
		'type'		=> '', 		// used for special meta tabs
		'translate'	=> false,
		'key'		=> $key
	);

	$o = wp_parse_args($args, $d);

	if ( has_filter( "ploption_{$key}" ) )
		return apply_filters( "ploption_{$key}", $key, $o );

	if( class_exists('PageLinesTemplateHandler') && pl_setting($key, $o))
		return pagelines_magic_parse( pl_setting($key, $o), $o );

	elseif(is_pagelines_special($o) && plspecial($key, $o))
		return pagelines_magic_parse( plspecial($key, $o), $o );

	elseif( isset( $o['post_id'] ) && plmeta( $key, $args ) )
		return pagelines_magic_parse( plmeta( $key, $o ), $o );

	elseif( pldefault( $key, $o ) )
		return pldefault( $key, $o );

	elseif( get_ploption($key, $o) )
		return pagelines_magic_parse( get_ploption( $key, $o ), $o );

	elseif( get_ploption($key, $o) === null )
		if ( $newkey = plnewkey( $key ) )
			return $newkey;

	else
		return false;
}

/**
 * Locates a meta option if it exists
 *
 * @param string $key the key of the option
 */
function plmeta( $key, $args ){

	$d = array(
		'subkey'	=> null,
		'post_id'	=> null,
		'setting'	=> null,
		'clone_id'	=> null,
	);

	$o = wp_parse_args($args, $d);

	$pid = $o['post_id'];

	if ( ! $pid )
		return false;

	$meta_global = "pl_meta_$pid";

	global ${$meta_global};
	$meta_opts = ${$meta_global};
	if( ! is_array( $meta_opts ) ) {
		$meta_opts = get_post_meta( $pid );
		${$meta_global} = $meta_opts;
	}

	// Deal with cloning options
	if( isset($args['clone_id']) && $args['clone_id'] != 1 && $args['clone_id'] != 0)
		$id_key = $key.'_'.$args['clone_id'];
	else
		$id_key = $key;

	// Deal w/ default checkbox/boolean stuff
	// If default is set, return if reversed

	if( isset($o['post_id']) && !empty($o['post_id']) ) {

		$default_value = ( isset( $meta_opts[ $id_key ][0] ) ) ? $meta_opts[ $id_key ][0] : false;

		$reverse = ( pldefault($key, $args, 'val') && isset( $meta_opts[ $key.'_reverse' ][0] ) ) ? $meta_opts[ $key.'_reverse' ][0] : false;

		if( (bool) $default_value && (bool) $reverse)
			return false;
		else
			return $default_value;

	} else
		return false;

}


/**
*
* @TODO do
*
*/
function plspecial($key, $args){

	return false;
}

/**
 * Grab from global defaults panel
 *
 * @param 'key' the id of the option
 *
 **/
function pldefault( $key, $args = array(), $mode = '') {


	return false;

}



/**
 * Attempt to set default value if not found with ploption()
 *
 * @param 'key' the id of the option
 *
 **/
function plnewkey( $key ) {

	if ( !is_admin() )
		return false;
	$settings = get_option_array();

	foreach ($settings as $group)
		foreach($group as $name => $setting)
			if ($name == $key && isset( $setting['default'] ) ) {
				plupop( $key, $setting['default'] );
				return $setting['default'];
			}
		return false;
}



/**
*
* @TODO do
*
*/
function plupop($key, $val, $oset = array()){

	$d = array(
		'parent'	=> null,
		'subkey'	=> null,
		'setting'	=> PAGELINES_SETTINGS,
	);

	$o = wp_parse_args($oset, $d);

	$the_set = get_option($o['setting']);

	$new = array( $key => $val );

	$parent = ( isset($o['parent']) ) ? $o['parent'] : null;



	$child_option = ( isset($parent) && isset($the_set[$parent]) && is_array($the_set[$parent]) ) ? true : false;

	$parse_set = ( $child_option ) ? $the_set[ $parent ] : $the_set;

	$new_set = wp_parse_args($new, $parse_set);


	if($child_option)
		$the_set[ $parent ] = $new_set;
	else
		$the_set = $new_set;

	update_option( $o['setting'], $the_set );

}

/**
*
* @TODO do
*
*/
function get_ploption( $key, $args = array() ){

	$d = array(
		'subkey'	=> null,
		'post_id'	=> null,
		'setting'	=> null,
		'clone_id'	=> null,
		'special'	=> null
	);

	$o = wp_parse_args($args, $d);

	// get setting
	$setting = ( isset($o['setting']) && !empty($o['setting'])) ? $o['setting'] : PAGELINES_SETTINGS;

	if(!isset($setting) || $setting == PAGELINES_SETTINGS){

		global $global_pagelines_settings;

		if( is_array($global_pagelines_settings) && isset($global_pagelines_settings[$key])  )
			return $global_pagelines_settings[$key];

		else
			return false;

	} elseif ( isset($setting) ){

		$setting_options = get_option($setting);

		if( isset($o['subkey']) ){

			if(isset($setting_options[$key]) && is_array($setting_options[$key]) && isset($setting_options[$key][$o['subkey']]))
				return $setting_options[$key][$o['subkey']];
			else
				return false;

		}elseif( isset($setting_options[$key]) )
			return $setting_options[$key];

		else
			return false;

	} else
		return false;

}

/**
 * Parse the ploption strings.
 */
function pagelines_magic_parse( $string, $o ) {

	/**
	 * wpml check.
	 */
	if ( true == $o['translate'] ) {

		if( ! function_exists('icl_register_string') )
			return $string;

		$key = sprintf( '%s_%s_%s_%s', $o['group'], $o['key'], $o['post_id'], $o['clone_id'] );
		$group = sprintf( 'pagelines_%s', $o['group'] );
		icl_register_string( $group, $key, $string);

		return icl_t( $group, $key, $string );
	}

	/**
	 * Always return original string if all else fails.
	 */
	return $string;
}

/**
*
* @TODO do
*
*/
function plname($key, $a = array()){

	$set = (!isset($a['setting']) || empty($a['setting']) || $a['setting'] == PAGELINES_SETTINGS) ? PAGELINES_SETTINGS : $a['setting'];

	$subkey = (isset($a['subkey'])) ? $a['subkey'] : false;

	$grandkey = (isset($a['subkey']) && is_array($a['subkey']) && isset($a['subkey']['grandkey'])) ? $a['subkey']['grandkey'] : false;

	if( $grandkey )
		$output = $set . '['.$key.']['.$subkey.']['.$grandkey.']';
	elseif( $subkey )
		$output = $set . '['.$key.']['.$subkey.']';
	else
		$output = $set .'['.$key.']';

	return $output;

}


/**
*
* @TODO do
*
*/
function plid($key, $a){

	$set = (!isset($a['setting']) || empty($a['setting']) || $a['setting'] == PAGELINES_SETTINGS) ? PAGELINES_SETTINGS : $a['setting'];

	$subkey = (isset($a['subkey'])) ? $a['subkey'] : false;

	$grandkey = (isset($a['subkey']) && is_array($a['subkey']) && isset($a['subkey']['grandkey'])) ? $a['subkey']['grandkey'] : false;

	$clone_id = (isset($a['clone_id']) && $a['clone_id'] != 1) ? '_'.$a['clone_id'] : '';

	if( $grandkey )
		$output = array($set, $key, $subkey, $grandkey);
	elseif( $subkey )
		$output = array($set, $key, $subkey);
	else
		$output = array($set, $key);

	return join('_', $output) . $clone_id;
}


/**
*
* @TODO do
*
*/
function pl_um($key, $args = null){

	if(is_array($args)){

		$d = array(
			'user_id'	=> null
		);

		$o = wp_parse_args($args, $d);
	} else {

		$o['user_id'] = $args;

	}


	return get_user_meta( $o['user_id'], $key, true );
}

/**
 * Get the option, if its not set, set it.
 * @todo make usable with different settings types.
 *
 **/
function pl_getset_option($key, $default = false) {

	global $global_pagelines_settings;

	if( is_array($global_pagelines_settings) && isset($global_pagelines_settings[$key]) )
		return $global_pagelines_settings[$key];

	else{
		plupop( $key, $default );
		return $default;
	}
}


/**
 * Sets up option name for saving of option settings
 *
 **/
function pagelines_option_name( $oid, $sub_oid = null, $grand_oid = null, $setting = PAGELINES_SETTINGS){
	echo get_pagelines_option_name( $oid, $sub_oid, $grand_oid, $setting );
}


/**
*
* @TODO do
*
*/
function get_pagelines_option_name( $oid, $sub_oid = null, $grand_oid = null, $setting = PAGELINES_SETTINGS ){

	$set = (!isset($setting) || $setting == PAGELINES_SETTINGS) ? PAGELINES_SETTINGS : $setting;

	if( isset($grand_oid) )
		$name = $set . '['.$oid.']' . '['.$sub_oid.']' . '['.$grand_oid.']';
	elseif( isset($sub_oid) )
		$name = $set . '['.$oid.']' . '['.$sub_oid.']';
	else
		$name = $set .'['.$oid.']';

	return $name;
}


/**
*
* @TODO do
*
*/
function meta_option_name( $array, $hidden = true ){

	$prefix = ($hidden) ? '_' : '';

	return $prefix.join('_', $array);

}


/**
*
* @TODO do
*
*/
function pagelines_option_id( $oid, $sub_oid = null, $grand_oid = null, $namespace = 'pagelines'){
	echo get_pagelines_option_id($oid, $sub_oid, $grand_oid, $namespace);
}


/**
*
* @TODO do
*
*/
function get_pagelines_option_id( $oid, $sub_oid = null, $grand_oid = null, $namespace = 'pagelines'){

	$nm = (!isset($namespace) || $namespace == 'pagelines') ? 'pagelines' : $namespace;

	if( isset($grand_oid) )
		$a = array($nm, $oid, $sub_oid, $grand_oid);
	elseif( isset($sub_oid) )
		$a = array($nm, $oid, $sub_oid);
	else
		$a = array($nm, $oid);

	return join('_', $a);
}

/**
 * Sanitize user input
 *
 **/
function pagelines_settings_callback( $input ) {

	// We whitelist some of the settings, these need to have html/js/css.
	$whitelist = array( 'excerpt_tags', 'headerscripts', 'footerscripts', 'asynch_analytics', 'typekit_script', 'footer_terms', 'footer_more' );

	if(is_array($input)){

		// We run through the $input array, if it is not in the whitelist we run it through the wp filters.
		foreach ($input as $name => $value){
			if ( !is_array( $value ) && !in_array( $name, apply_filters( 'pagelines_settings_whitelist', $whitelist ) ) )
				if ( 'customcss' == $name)
					$input[$name] = wp_strip_all_tags( $value, false );
				else
					$input[$name] = wp_filter_nohtml_kses( $value );
		}

	}
	// Return our safe $input array.
	return $input;
}

/**
 * These functions pull options/settings
 * from the options database.
 *
 **/
function get_pagelines_option($key, $setting = null, $default = null) {
	// get setting
	$setting = $setting ? $setting : PAGELINES_SETTINGS;

	if(!isset($setting) || $setting == PAGELINES_SETTINGS){

		global $global_pagelines_settings;

		if( is_array($global_pagelines_settings) && isset($global_pagelines_settings[$key]) )
			return $global_pagelines_settings[$key];

		else
			if ( $default ) {
				plupop( $key, $default );
				return $default;
			}
		return false;
	}
}




/**
*
* @TODO do
*
*/
function pagelines_option( $key, $post_id = null, $setting = null){

	if(isset($post_id) && get_post_meta($post_id, $key, true))
		return get_post_meta($post_id, $key, true); //if option is set for a page/post

	elseif( get_pagelines_option($key, $setting) )
		return get_pagelines_option($key, $setting);

	else
		return false;

}


/**
*
* @TODO do
*
*/
function pagelines_sub_option( $key, $subkey, $post_id = '', $setting = null){

	$primary_option = pagelines_option($key, $post_id, $setting);

	if(is_array($primary_option) && isset($primary_option[$subkey]))
		return $primary_option[$subkey];
	else
		return false;

}

// Need to keep until the forums are redone, or don't check for it.

/**
*
* @TODO do
*
*/
function pagelines( $key, $post_id = null, $setting = null ){
	return pagelines_option($key, $post_id, $setting);
}


/**
*
* @TODO do
*
*/
function e_pagelines($key, $alt = null, $post_id = null, $setting = null){
	print_pagelines_option( $key, $alt, $post_id, $setting);
}



/**
*
* @TODO do
*
*/
function pagelines_pro($key, $post_id = null, $setting = null){

	if(VPRO)
		return pagelines_option($key, $post_id, $setting);
	else
		return false;
}


/**
*
* @TODO do
*
*/
function print_pagelines_option($key, $alt = null, $post_id = null, $setting = null) {

	echo load_pagelines_option($key, $alt, $post_id, $setting);

}


/**
*
* @TODO do
*
*/
function load_pagelines_option($key, $alt = null, $post_id = null, $setting = null) {

		if($post_id && get_post_meta($post_id, $key, true) && !is_home()){

			//if option is set for a page/post
			return get_post_meta($post_id, $key, true);

		}elseif(pagelines_option($key, $post_id, $setting)){

			return pagelines_option($key, $post_id, $setting);

		}else{
			return $alt;
		}

}


/**
*
* @TODO do
*
*/
function pagelines_update_option($optionid, $optionval){

		$theme_options = get_option(PAGELINES_SETTINGS);
		$new_options = array(
			$optionid => $optionval
		);

		$settings = wp_parse_args($new_options, $theme_options);
		update_option(PAGELINES_SETTINGS, $settings);
}



/**
*
* @TODO do
*
*/
function get_pagelines_meta($option, $post){
	$meta = get_post_meta($post, $option, true);
	if(isset($meta))
		return $meta;
	else
		return false;
}

	/* Deprecated in favor of get_pagelines_meta */
	function m_pagelines($option, $post){
		return get_pagelines_meta($option, $post);
	}



	/**
	*
	* @TODO document
	*
	*/
	function em_pagelines($option, $post, $alt = ''){
		$post_meta = m_pagelines($option, $post);

		if(isset($post_meta)){
			echo $post_meta;
		}else{
			echo $alt;
		}
	}


/**
 * Used to register and handle new plugin options
 * Use with register_activation_hook()
 * @since 2.0
 **/
function pagelines_register_addon_options( $addon_name, $addon_options ) {

	$addon_saved_options = get_option( 'pagelines_addons_options' );
	if ( !is_array( $addon_saved_options ) ) $addon_saved_options = array();
	if ( !isset($addon_saved_options[$addon_name] ) ) {
		$addon_saved_options[$addon_name] = $addon_options;
		update_option( 'pagelines_addons_options', $addon_saved_options );
	}
}

/**
 * Used to remove options when addons are deleted.
 * Use with register_deactivation_hook()
 * @since 2.0
 **/
function pagelines_remove_addon_options( $addon_name ) {
	$options = get_option( 'pagelines_addons_options' );
	if (is_array($options) && isset( $options[$addon_name] ) ) {
		unset($options[$addon_name]);
		update_option( 'pagelines_addons_options', $options );
	}
}

/**
 * This function registers the default values for pagelines theme settings
 */
function pagelines_settings_defaults() {

	$default_options = array();

		foreach(get_option_array( true ) as $menuitem => $options ){

			foreach($options as $oid => $o ){

				if( isset( $o['type'] ) &&  'layout' == $o['type'] ){

					$dlayout = new PageLinesLayout;
					$default_options['layout'] = $dlayout->default_layout_setup();

				}elseif( pagelines_is_multi_option($oid, $o) ){

					foreach($o['selectvalues'] as $multi_optionid => $multi_o)
						if(isset($multi_o['default'])) $default_options[$multi_optionid] = $multi_o['default'];


				}else{
					if(!VPRO && isset($o['version_set_default']) && $o['version_set_default'] == 'pro')
						$default_options[$oid] = null;
					elseif(!VPRO && isset($o['default_free']))
						$default_options[$oid] = $o['default_free'];
					elseif(isset($o['default']))
						$default_options[$oid] = $o['default'];
				}

			}
		}

	return apply_filters('pagelines_settings_defaults', $default_options);
}




/**
*
* @TODO do
*
*/
function pagelines_process_reset_options( $option_array = null ) {



	if(isset($_POST['pl_reset_settings']) && current_user_can('edit_themes')){

		do_action( 'extend_flush' );

		if(isset($_POST['the_pl_setting']) && !isset($_POST['reset_callback']))
			update_option($_POST['the_pl_setting'], array());

		if(isset($_POST['reset_callback']))
			call_user_func( $_POST['reset_callback'] );
	}


	$option_array = (isset($option_array)) ? $option_array : get_option_array();

	foreach($option_array as $menuitem => $options ){
		foreach($options as $oid => $o ){
			if( isset( $o['type'] ) && $o['type'] == 'reset' && ploption($oid) ){

					call_user_func($o['callback']);

					// Set the 'reset' option back to not set !important
					pagelines_update_option($oid, null);

					wp_redirect( admin_url( PL_SETTINGS_URL.'&reset=true&opt_id='.$oid ) );
					exit;

			}

		}
	}

}


/**
*
* @TODO do
*
*/
function pagelines_is_multi_option( $oid, $o ){

	if ( ! isset( $o['type'] ) )
		return false;

	if( $o['type'] == 'text_multi'
		|| $o['type'] == 'check_multi'
		|| $o['type'] == 'color_multi'
		|| $o['type'] == 'image_upload_multi'
		|| $o['type'] == 'multi_option'
	){
		return true;
	} else
		return false;
}


/**
*
* @TODO do
*
*/
function pagelines_is_boolean_option($oid, $o){

	if(
		$o['type'] == 'check'
		|| $o['type'] == 'check_multi'
	){
		return true;
	} else
		return false;

}




/**
*
* @TODO do
*
*/
function pagelines_import_export(){

		if ( isset( $_POST['form_submitted']) && $_POST['form_submitted'] == 'export_settings_form' ) {

			$pagelines_settings = ( array ) get_option(PAGELINES_SETTINGS);
			$pagelines_template_map = ( array ) get_option( PAGELINES_TEMPLATE_MAP );
			$pagelines_templates = ( array ) get_option( PAGELINES_TEMPLATES );
			$pagelines_special = ( array ) get_option( PAGELINES_SPECIAL );

			$options['pagelines_templates'] = $pagelines_templates;
			$options['pagelines_template_map'] = $pagelines_template_map;
			$options['pagelines_settings'] = $pagelines_settings;
			$options['pagelines_special'] = $pagelines_special;


			if ( isset($options) && is_array( $options) ) {

				header('Cache-Control: public, must-revalidate');
				header('Pragma: hack');
				header('Content-Type: text/plain');
				header( 'Content-Disposition: attachment; filename="' . PL_THEMENAME . '-Settings-' . date('Ymd') . '.dat"' );
				echo json_encode( $options );
				exit();
			}

	}

	if ( isset($_POST['form_submitted']) && $_POST['form_submitted'] == 'import_settings_form') {
		if (strpos($_FILES['file']['name'], 'Settings') === false && strpos($_FILES['file']['name'], 'settings') === false){
			wp_redirect( admin_url(PL_IMPORT_EXPORT_URL.'&pageaction=import&error=wrongfile') );
		} elseif ($_FILES['file']['error'] > 0){
			$error_type = $_FILES['file']['error'];
			wp_redirect( admin_url(PL_IMPORT_EXPORT_URL.'&pageaction=import&error=file&'.$error_type) );
		} else {
			$raw_options = pl_file_get_contents( $_FILES['file']['tmp_name'] );
			$all_options = json_decode(json_encode(json_decode($raw_options)), true);

			if ( !isset( $_POST['pagelines_layout'] ) && is_array( $all_options) && isset( $all_options['pagelines_settings'] ) && is_array( $all_options['pagelines_settings'] ) )
				unset( $all_options['pagelines_settings']['layout'] );

			if ( isset( $_POST['pagelines_settings'] ) && is_array( $all_options) && isset( $all_options['pagelines_settings'] )  && is_array( $all_options['pagelines_settings'] ) ) {
				update_option( PAGELINES_SETTINGS, array_merge( get_option( PAGELINES_SETTINGS ), $all_options['pagelines_settings'] ) );
				$done = 1;
			}

			if ( isset( $_POST['pagelines_special'] ) && is_array( $all_options) && isset( $all_options['pagelines_special'] ) && is_array( $all_options['pagelines_special'] ) ) {
				$special = ( array ) get_option( PAGELINES_SPECIAL );
				update_option( PAGELINES_SPECIAL, array_merge( $special, $all_options['pagelines_special'] ) );
				$done = 1;
			}

			if ( isset( $_POST['pagelines_templates'] ) && is_array( $all_options) && isset( $all_options['pagelines_template_map'] ) && is_array( $all_options['pagelines_template_map'] ) ) {
				$template_map = ( array ) get_option( PAGELINES_TEMPLATE_MAP );
				$template_settings = ( array ) get_option( PAGELINES_TEMPLATES );

				$template_settings_new = ( isset( $all_options['pagelines_templates'] ) && is_array( $all_options['pagelines_templates'] ) ) ? $all_options['pagelines_templates'] : array();
				$template_map_new = ( isset( $all_options['pagelines_template_map'] ) && is_array( $all_options['pagelines_template_map'] ) ) ? $all_options['pagelines_template_map'] : array();

				update_option( PAGELINES_TEMPLATE_MAP, array_merge( $template_map, $template_map_new ) );
				update_option( PAGELINES_TEMPLATES, array_merge( $template_settings, $template_settings_new ) );
				$done = 1;
			}
				if (function_exists('wp_cache_clean_cache')) {
					global $file_prefix;
					wp_cache_clean_cache($file_prefix);
				}
				if ( isset($done) ) {
				wp_redirect( admin_url( PL_IMPORT_EXPORT_URL.'&pageaction=import&imported=true' ) );
			} else {
				wp_redirect( admin_url( PL_IMPORT_EXPORT_URL.'&pageaction=import&error=wrongfile') );
			}
		}
	}
}

/*
 * Set user/pass using md5()
 *
 */
function set_pagelines_credentials( $user, $pass ) {

	if ( !empty( $user ) && !empty( $pass ) )
		update_option( 'pagelines_extend_creds', array( 'user' => $user, 'pass' => md5( $pass ) ) );
}

/*
 * Add persistant licence info
 *
 */
function update_pagelines_licence( $licence ) {

	$creds = get_option( 'pagelines_extend_creds' );

	$creds['licence'] = $licence;

	update_option( 'pagelines_extend_creds', $creds );
}


/*
 * Get username or password
 *
 */
function get_pagelines_credentials( $t ) {

	$creds = get_option( 'pagelines_extend_creds', array( 'user' => '', 'pass' => '' ) );

	switch( $t ) {

		case 'user':
			return ( isset( $creds['user'] ) ) ? $creds['user'] : null;
		break;

		case 'pass':
			return ( isset( $creds['pass'] ) ) ? $creds['pass'] : false;
		break;

		case 'licence':
			return ( isset( $creds['licence'] ) ) ? $creds['licence'] : 'not logged in';
		break;

	}
}

/*
 * Check updates status including errors and licence information.
 *
 */
function pagelines_check_credentials( $type = 'setup' ) {

	return true; 
	
	switch( $type ) {

		case 'setup':
			if ( is_array( $a = get_transient( EXTEND_UPDATE ) ) && isset($a['credentials']) && $a['credentials'] === 'true' )
				return true;
			else
				return false;
		break;

		case 'licence':
			if ( is_array( $a = get_transient( EXTEND_UPDATE ) ) && isset($a['licence']) )
				return $a['licence'];
		break;

		case 'error':
			if ( is_array( $a = get_transient( EXTEND_UPDATE ) ) && isset($a['api_error']) )
				return $a['api_error'];
		break;

		case 'ssl':
			if ( is_array( $a = get_transient( EXTEND_UPDATE ) ) && isset($a['ssl']) )
				return true;
		break;

		case 'echo':
			return get_transient( EXTEND_UPDATE );
		break;

		case 'plus':
			if ( is_array( $a = get_transient( EXTEND_UPDATE ) ) && isset($a['plus']) )
				return $a['plus'];
		break;

		case 'message':
		if ( is_array( $a = get_transient( EXTEND_UPDATE ) ) && isset($a['message']) )
			return $a['message'];

		case 'vchat':
		if ( is_array( $a = get_transient( EXTEND_UPDATE ) ) && isset($a['chat_url']) )
			return $a['chat_url'];
		else
			return false;
	}
}



function grid( $data, $args = array() ){

	$defaults = array(
		'data'			=> 'query',
		'per_row'		=> 3,
		'format'		=> 'img_grid',
		'paged'			=> false,
		'has_img'		=> true,
		'image_field'	=> false,
		'img_default'	=> null,
		'img_width'		=> '100%',
		'title'			=> '',
		'title_link'	=> '',
		'class'			=> 'pagelines-grid',
		'row_class'		=> 'gridrow',
		'content_len'	=> 10,
		'callback'		=> false,
		'margin'		=> true,
		'hovercard'		=> false
	);

	$a = wp_parse_args($args, $defaults);

	if( $a['data'] == 'users' || $a['data'] == 'array_callback'){

		$posts = $data;

	}else{
		// The Query
		global $wp_query;

		$wp_query = $data;

		$posts = $data->posts;

		if( !is_array( $posts ) )
			return;

	}

	// Standard Variables
	$out = '';
	$total = count($posts);
	$count = 1;
	$default_img = ( isset($a['img_default']) ) ? sprintf('<img src="%s" alt="%s"/>', $a['img_default'], __('No Image', 'pagelines')) : '';

	$margin_class = ($a['margin']) ? '' : 'ppfull';


	if($a['hovercard'])
		$out .= pl_js_wrap(sprintf('jQuery(".vignette").hover(function(){jQuery(this).find(".hovercard").fadeIn();}, function(){jQuery(this).find(".hovercard").fadeOut();});'));


	// Grid loop
	foreach($posts as $pid => $p){

		// Grid Stuff
		$start = (grid_row_start( $count, $total, $a['per_row'])) ? sprintf('<div class="pprow grid-row fix %s">', $margin_class) : '';
		$end = (grid_row_end( $count, $total, $a['per_row'])) ? '</div>' : '';
		$last_class = (grid_row_end( $count, $total, $a['per_row'])) ? 'pplast' : '';


		// Content
		$content = '';

		if($a['callback'])
			$content = call_user_func( $a['callback'], $p, $a );
		else {

			setup_postdata($p);

			$oset = array('post_id' => $p->ID);

			// The Image
			if( $a['image_field'] && ploption($a['image_field'], $oset) )
				$thumb = sprintf('<img src="%s" alt="thumb" />', ploption($a['image_field'], $oset) );
			elseif( has_post_thumbnail( $p->ID ) )
				$thumb = get_the_post_thumbnail( $p->ID );
			else
				$thumb = $default_img;

			$hovercard = ($a['hovercard']) ? sprintf('<div class="hovercard"><span>%s</span></div>', $p->post_title) : '';

			$image = sprintf(
				'<a href="%s" class="img grid-img" style="width: %s"><div class="grid-img-pad"><div class="grid-img-frame"><div class="vignette">%s%s</div></div></div></a>',
				get_permalink($p->ID),
				$a['img_width'],
				$thumb,
				$hovercard
			);

			$content .= $image;

			// Text

			if($a['format'] == 'media'){

				$content .= sprintf(
					'<div class="bd grid-content"><h4><a href="%s">%s</a></h4> <p>%s %s %s</p></div>',
					get_permalink($p->ID),
					$p->post_title,
					custom_trim_excerpt($p->post_content, $a['content_len']),
					sprintf('<a href="%s" >More &rarr;</a>', get_permalink($p->ID)),
					pledit( $p->ID )

				);

			}

		}

		// Column Box Wrapper
		$out .= sprintf(
			'%s<div class="grid-element pp%s %s %s"><div class="grid-element-pad">%s</div></div>%s',
			$start,
			$a['per_row'],
			$a['format'],
			$last_class,
			$content,
			$end
			);

		$count++;
	}

	if( $a['paged'] ){
		ob_start();
		pagelines_pagination();
		$pages = ob_get_clean();
	} else
		$pages = '';

	$title_link = ($a['title_link'] != '') ? sprintf('<a href="%s" class="button title-link">See All</a>', $a['title_link']) : '';

	$title = ($a['title'] != '') ? sprintf('<div class="grid-title"><div class="grid-title-pad fix"><h4 class="gtitle">%s</h4>%s</div></div>', $a['title'], $title_link) : '';

	$wrap = sprintf('<div class="plgrid %s"><div class="plgrid-pad">%s%s%s</div></div>', $a['class'], $title, $out, $pages);

	return $wrap;

}


/**
 *  Returns true on the first element in a row of elements
 **/
function grid_row_start( $count, $total_count, $perline){

	$row_count = $count + ( $perline - 1 );

	$grid_row_start = ( $row_count % $perline == 0 ) ? true : false;

	return $grid_row_start;

}

/**
 *  Returns false on the last element in a row of elements
 **/
function grid_row_end( $count, $total_count, $perline){


	$row_count = $count + ( $perline - 1 );

	$box_row_end = ( ( $row_count + 1 ) % $perline == 0 || $count == $total_count ) ? true : false;

	return $box_row_end;
}



/**
 *
 *
 *  PageLines Custom Post Type Class
 *
 *
 *  @package PageLines DMS
 *  @subpackage Post Types
 *  @since 4.0
 *
 */
class PageLinesPostType {

	var $id;		// Root id for section.
	var $settings;	// Settings for this section


	/**
	 * PHP5 constructor
	 *
	 */
	function __construct($id, $settings, $taxonomies = array(), $columns = array(), $column_display_function = '') {

		$this->id = $id;
		$this->taxonomies = $taxonomies;
		$this->columns = $columns;
		$this->columns_display_function = $column_display_function;

		$defaults = array(
				'label' 			=> 'Posts',
				'singular_label' 	=> 'Post',
				'description' 		=> null,
				'public' 			=> false,
				'show_ui' 			=> true,
				'capability_type'	=> 'post',
				'hierarchical' 		=> false,
				'rewrite' 			=> false,
				'supports' 			=> array( 'title', 'editor', 'thumbnail', 'revisions' ),
				'menu_icon' 		=> PL_ADMIN_IMAGES . '/favicon-pagelines.ico',
				'taxonomies'		=> array(),
				'menu_position'		=> 20,
				'featured_image'	=> false,
				'has_archive'		=> false,
				'map_meta_cap'		=> false,
				'dragdrop'			=> true,
				'load_sections'		=> false,
				'query_var'			=> true
			);

		$this->settings = wp_parse_args($settings, $defaults); // settings for post type

		$this->register_post_type();
		$this->register_taxonomies();
		$this->register_columns();
		$this->featured_image();
		$this->section_loading();

	}

	/**
	 * The register_post_type() function is not to be used before the 'init'.
	 */
	function register_post_type(){
		add_action( 'init', array( $this,'init_register_post_type') );
	}


	/**
	*
	* @TODO document
	*
	*/
	function init_register_post_type(){

		$capability = 'moderate_comments';

		register_post_type( $this->id , array(
				'labels' => array(
							'name' 			=> $this->settings['label'],
							'singular_name' => $this->settings['singular_label'],
							'add_new'		=> __('Add New ', 'pagelines') . $this->settings['singular_label'],
							'add_new_item'	=> __('Add New ', 'pagelines') . $this->settings['singular_label'],
							'edit'			=> __('Edit ', 'pagelines') . $this->settings['singular_label'],
							'edit_item'		=> __('Edit ', 'pagelines') . $this->settings['singular_label'],
							'view'			=> __('View ', 'pagelines') . $this->settings['singular_label'],
							'view_item'		=> __('View ', 'pagelines') . $this->settings['singular_label'],
						),

	 			'label' 			=> $this->settings['label'],
				'singular_label' 	=> $this->settings['singular_label'],
				'description' 		=> $this->settings['description'],
				'public' 			=> $this->settings['public'],
				'show_ui' 			=> $this->settings['show_ui'],
				'capability_type'	=> $this->settings['capability_type'],
				'hierarchical' 		=> $this->settings['hierarchical'],
				'rewrite' 			=> $this->settings['rewrite'],
				'supports' 			=> $this->settings['supports'],
				'menu_icon' 		=> $this->settings['menu_icon'],
				'taxonomies'		=> $this->settings['taxonomies'],
				'menu_position'		=> $this->settings['menu_position'],
				'has_archive'		=> $this->settings['has_archive'],
				'map_meta_cap'		=> $this->settings['map_meta_cap'],
				'query_var'			=> $this->settings['query_var'],
				'capabilities' => array(
			        'publish_posts' 		=> $capability,
			        'edit_posts' 			=> $capability,
			        'edit_others_posts' 	=> $capability,
			        'delete_posts' 			=> $capability,
			        'delete_others_posts' 	=> $capability,
			        'read_private_posts' 	=> $capability,
			        'edit_post' 			=> $capability,
			        'delete_post' 			=> $capability,
			        'read_post' 			=> $capability,
			    ),

			));

	}


	/**
	*
	* @TODO document
	*
	*/
	function register_taxonomies(){

		if( !empty($this->taxonomies) ){

			foreach($this->taxonomies as $tax_id => $tax_settings){

				$defaults = array(
					'hierarchical' 		=> true,
					'label' 			=> '',
					'singular_label' 	=> '',
					'rewrite' 			=> true
				);

				$a = wp_parse_args($tax_settings, $defaults);

				register_taxonomy( $tax_id, array($this->id), $a );
			}

		}

	}


	/**
	*
	* @TODO document
	*
	*/
	function register_columns(){

		add_filter( "manage_edit-{$this->id}_columns", array( $this, 'set_columns' ) );
		add_action( "manage_{$this->id}_posts_custom_column",  array( $this, 'set_column_values' ) );
	}


	/**
	*
	* @TODO document
	*
	*/
	function set_columns( $columns ){

		return $this->columns;
	}


	/**
	*
	* @TODO document
	*
	*/
	function set_column_values( $wp_column ){

		call_user_func( $this->columns_display_function, $wp_column );

	}


	/**
	*
	* @TODO document
	*
	*/
	function set_default_posts( $callback, $object = false){

		if(!get_posts('post_type='.$this->id)){

			if($object)
				call_user_func( array($object, $callback), $this->id);
			else
				call_user_func($callback, $this->id);
		}

	}




	/**
	*
	* @TODO document
	*
	*/
	function section_loading(){

		if( ! $this->settings['dragdrop'] )
			add_filter('pl_cpt_dragdrop', array( $this, 'remove_dragdrop'), 10, 2);

		if ( true === $this->settings['load_sections'] || is_array( $this->settings['load_sections'] ) )
			add_filter('pl_template_sections', array( $this, 'load_sections_for_type'), 10, 3);

	}


		/**
		*
		* @TODO document
		*
		*/
		function load_sections_for_type( $sections, $template_type, $hook ){

			if( $template_type == $this->id || $template_type == get_post_type_plural( $this->id ) )
				return $this->settings['load_sections'];
			else
				return $sections;

		}


		/**
		*
		* @TODO document
		*
		*/
		function remove_dragdrop( $bool, $post_type ){
			if( $post_type == $this->id )
				return false;
			else
				return $bool;
		}


	/**
	 * Is the WP featured image supported
	 */
	function featured_image(){

		if( $this->settings['featured_image'] )
			add_filter('pl_support_featured_image', array( $this, 'add_featured_image'));

	}


		/**
		*
		* @TODO document
		*
		*/
		function add_featured_image( $support_array ){

			$support_array[] = $this->id;
			return $support_array;

		}

}



/*
 * Set runtime licence types
 *
 */
if ( !defined( 'VDEV') )
	define( 'VDEV', ( get_pagelines_credentials( 'licence' ) === 'dev' ) ? true : false );

if( !defined( 'VPRO' ) )
	define( 'VPRO', true );
if ( !defined( 'VPLUS' ) )
	define( 'VPLUS', ( pagelines_check_credentials( 'plus' ) ) ? true : false );
