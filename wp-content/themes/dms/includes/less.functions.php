<?php 

function get_core_lessfiles(){

	$files = array(
		'reset',
		'pl-structure',
		'pl-editor',
		'pl-wordpress',
		'pl-plugins',
		'grid',
		'alerts',
		'labels-badges',
		'tooltip-popover',
		'buttons',
		'typography',
		'dropdowns',
		'accordion',
		'carousel',
		'navs',
		'modals',
		'thumbnails',
		'component-animations',
		'utilities',
		'pl-objects',
		'pl-tables',
		'wells',
		'forms',
		'breadcrumbs',
		'close',
		'pager',
		'pagination',
		'progress-bars',
		'icons',
		'responsive'
	);

	return $files;
}

function get_all_active_sections() {

	$out = '';
	global $load_sections;
	$available = $load_sections->pagelines_register_sections( true, true );

	$disabled = get_option( 'pagelines_sections_disabled', array() );

	/*
	* Filter out disabled sections
	*/
	foreach( $disabled as $type => $data )
		if ( isset( $disabled[$type] ) )
			foreach( $data as $class => $state )
				unset( $available[$type][ $class ] );

	/*
	* We need to reorder the array so sections css is loaded in the right order.
	* Core, then pagelines-sections, followed by anything else.
	*/
	$sections = array();
	$sections['parent'] = $available['parent'];
	$sections['child'] = array();
	unset( $available['parent'] );
	if( isset( $available['custom'] ) && is_array( $available['custom'] ) ) {
		$sections['child'] = $available['custom']; // load child theme sections that override.
		unset( $available['custom'] );	
	}
	// remove core section less if child theme has a less file
	foreach( $sections['child'] as $c => $cdata) {
		if( isset( $sections['parent'][$c] ) && is_file( $cdata['base_dir'] . '/style.less' ) )
			unset( $sections['parent'][$c] );
	}
	
	if ( is_array( $available ) ) {
		foreach( $available as $type => $data ) {
			if( ! empty( $data ) )
				$sections[$type] = $data;
		}
	}
	foreach( $sections as $t ) {
		foreach( $t as $key => $data ) {
			if ( $data['less'] && $data['loadme'] ) {
				if ( is_file( $data['base_dir'] . '/style.less' ) )
					$out .= pl_file_get_contents( $data['base_dir'] . '/style.less' );
				elseif( is_file( $data['base_dir'] . '/color.less' ))
					$out .= pl_file_get_contents( $data['base_dir'] . '/color.less' );
			}
		}
	}
	return apply_filters('pagelines_lesscode', $out);
}


function pl_set_css_headers(){
	header( 'Content-type: text/css' );
	header( 'Expires: ' );
	header( 'Cache-Control: max-age=604100, public' );
}

function pl_get_css_dir( $type = '' ) {

	$folder = apply_filters( 'pagelines_css_upload_dir', wp_upload_dir() );

	if( 'path' == $type )
		return trailingslashit( $folder['basedir'] ) . 'pagelines';
	else
		return trailingslashit( $folder['baseurl'] ) . 'pagelines';
}

/**
 *
 *  Get all core less as uncompiled code.
 *
 *  @package PageLines DMS
 *  @since 3.0
 *  @uses  load_core_cssfiles
 */
function get_core_lesscode( $lessfiles ) {

	return load_core_cssfiles( apply_filters( 'pagelines_core_less_files', $lessfiles ) );
}

/**
 *
 *  Load from .less files.
 *
 *  @package PageLines DMS
 *  @since 3.0
 *  @uses  load_less_file
 */
function load_core_cssfiles( $files ) {

	$code = '';
	foreach( $files as $less ) {
		$code .= load_less_file( $less );
	}
	return apply_filters( 'pagelines_insert_core_less', $code );
}

/**
 *
 *  Fetch less file from theme folders.
 *
 */
function load_less_file( $file ) {

	$file 	= sprintf( '%s.less', $file );
	$parent = sprintf( '%s/%s', PL_CORE_LESS, $file );
	$child 	= sprintf( '%s/%s', PL_CHILD_LESS, $file );

	// check for child 1st if not load the main file.

	$load = ( is_file( $child ) ) ? $child : $parent;

	return pl_file_get_contents( $load );		
		
}

/**
 *
 *  Simple minify.
 *
 */
function pl_css_minify( $css ) {


	$data = $css;

    $data = preg_replace( '#/\*.*?\*/#s', '', $data );
    // remove new lines \\n, tabs and \\r
    $data = preg_replace('/(\t|\r|\n)/', '', $data);
    // replace multi spaces with singles
    $data = preg_replace('/(\s+)/', ' ', $data);
    //Remove empty rules
    $data = preg_replace('/[^}{]+{\s?}/', '', $data);
    // Remove whitespace around selectors and braces
    $data = preg_replace('/\s*{\s*/', '{', $data);
    // Remove whitespace at end of rule
    $data = preg_replace('/\s*}\s*/', '}', $data);
    // Just for clarity, make every rules 1 line tall
    $data = preg_replace('/}/', "}\n", $data);
    $data = str_replace( ';}', '}', $data );
    $data = str_replace( ', ', ',', $data );
    $data = str_replace( '; ', ';', $data );
    $data = str_replace( ': ', ':', $data );
    $data = preg_replace( '#\s+#', ' ', $data );

	if ( ! preg_last_error() )
		return $data;
	else
		return $css;
}




function pagelines_insert_core_less( $file ) {

	global $pagelines_raw_lesscode_external;

	if( !is_array( $pagelines_raw_lesscode_external ) )
		$pagelines_raw_lesscode_external = array();

	$pagelines_raw_lesscode_external[] = $file;
}

/*
 * Add Less Variables
 *
 * Must be added before header.
 **************************/
function pagelines_less_var( $name, $value ){

	global $less_vars;

	$less_vars[$name] = $value;
}

// Save last error to a theme option.
function pl_less_save_last_error( $error_text, $return = false ) {
	
	if( '' == $error_text ) 
		remove_theme_mod( 'less_last_error' );
	else
		set_theme_mod( 'less_last_error', $error_text );

	return $return;
}