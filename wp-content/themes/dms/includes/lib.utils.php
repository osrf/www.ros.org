<?php 

// ------------------------------------------
// Javascript Utilities
// ------------------------------------------

/* 
 * Enqueue CodeMirror, used in admin and front end
 */ 
function pl_enqueue_codemirror(){
	
	// Codemirror Styles
	wp_enqueue_style( 'codemirror',			PL_JS . '/codemirror/codemirror.css' );

	// CodeMirror Syntax Highlighting
	wp_enqueue_script( 'codemirror',		PL_JS . '/codemirror/codemirror.js', array( 'jquery' ), PL_CORE_VERSION, true );
	wp_enqueue_script( 'codemirror-css',	PL_JS . '/codemirror/css/css.js', array( 'jquery', 'codemirror' ), PL_CORE_VERSION, true );
	wp_enqueue_script( 'codemirror-less',	PL_JS . '/codemirror/less/less.js', array( 'jquery', 'codemirror' ), PL_CORE_VERSION, true );
	wp_enqueue_script( 'codemirror-js',		PL_JS . '/codemirror/javascript/javascript.js', array( 'jquery', 'codemirror' ), PL_CORE_VERSION, true );
	wp_enqueue_script( 'codemirror-xml',	PL_JS . '/codemirror/xml/xml.js', array( 'jquery', 'codemirror' ), PL_CORE_VERSION, true );
	wp_enqueue_script( 'codemirror-html',	PL_JS . '/codemirror/htmlmixed/htmlmixed.js', array( 'jquery', 'codemirror' ), PL_CORE_VERSION, true );

	// Codebox defaults
	$base_editor_config = array(
		'lineNumbers'  => true,
		'lineWrapping' => true,
	);
	wp_localize_script( 'codemirror', 'cm_base_config', apply_filters( 'pagelines_cm_config', $base_editor_config ) );
}


// ------------------------------------------
// API FUNCTIONS
// ------------------------------------------

function pagelines_try_api( $url, $args ) {

	$defaults = array(
		'sslverify'	=>	false,
		'timeout'	=>	5,
		'body'		=> array(),
		'method'	=> 'POST',
		'prot'		=> array( 'https://', 'http://' )
	);

	$options = wp_parse_args( $args, $defaults );

	$command = sprintf( 'wp_remote_%s', $options['method'] );

	$method = $options['method'];
		
	foreach( $options['prot'] as $type ) {
		// sometimes wamp does not have curl!
		if ( $type === 'https://' && !function_exists( 'curl_init' ) )
			continue;
		$r = $command( $type . $url, $options );
		
		if ( !is_wp_error($r) && is_array( $r ) ) {
			return $r;
		}
	}
	return false;
}

// ------------------------------------------
// DMS HANDLING FUNCTIONS
// ------------------------------------------

/**
 * Setup PageLines Template
 *
 * Includes the loading template that sets up all PageLines templates
 *
 */
function setup_pagelines_template() {


	// if not true, then a non pagelines template is being rendered (wrap with .content)
	$GLOBALS['pagelines_render'] = true;

	get_header();

	if(!has_action('override_pagelines_body_output'))
		pagelines_template_area('pagelines_template', 'templates');

	get_footer();
}


// ------------------------------------------
// SECTIONS FUNCTIONS 
// ------------------------------------------

/**
 * Builds a sections for use outside of drag drop setup
 */
function build_passive_section( $args = array() ){

	global $passive_sections;

	if(!isset($passive_sections))
		$passive_sections = array();

	$defaults = array(
		'sid'	=> ''
	);

	$s = wp_parse_args($args, $defaults);

	$new = array($s['sid']);

	$passive_sections = array_merge($new, $passive_sections);

}



/**
 * Setup Section Notify
 */
function setup_section_notify( $section, $text = '', $user_url = null, $ltext = null){


	if(current_user_can('edit_theme_options')){

		$banner_title = sprintf('<strong><i class="icon-pencil"></i> %s</strong>', $section->name);
		$extra = '';
		
		$url = (isset($user_url)) ? $user_url : '#';
		
		if($section->filter == 'full-width'){
			$class = (isset($user_url)) ? '' : 'area-control';
			$extra .= 'data-area-action="settings"';
		} else {
			$class = (isset($user_url)) ? '' : 's-control section-edit';
		}



		$link_text = (isset($ltext)) ? $ltext : sprintf(__('Configure %s <i class="icon-arrow-right"></i>', 'pagelines'), $section->name);

		$link = sprintf('</br><a href="%s" class="btn btn-mini %s" %s>%s</a>', $url, $class, $extra, $link_text);

		$text = ($text != '') ? $text : __( 'Configure this section', 'pagelines' );

		return sprintf(
			'<div class="setup-section pl-editor-only"><div class="setup-section-pad">%s <br/><small class="banner_text subhead">%s %s</small></div></div>',
			$banner_title,
			$text,
			$link
		);
	}

}

/**
 * Splice Section Slug
 */
function splice_section_slug( $slug ){

	$pieces = explode('ID', $slug);
	$section = (string) $pieces[0];
	$clone_id = (isset($pieces[1])) ? $pieces[1] : 1;

	return array('section' => $section, 'clone_id' => $clone_id);
}

// ------------------------------------------
// PAGELINES CONDITIONALS
// ------------------------------------------

/**
 *  Determines if this page is showing several posts.
 */
function pagelines_is_posts_page(){
	if(is_home() || is_search() || is_archive() || is_category() || is_tag()) return true;
	else return false;
}

/**
 * is_pagelines_special() REVISED
 */
function is_pagelines_special( $args = array() ) {

	if ( is_404() || is_home() || is_search() || is_archive() )
		return true;
	else
		return false;
}

/**
 * Checks to see if page is a CPT, or a CPT archive (type)
 *
 */
function pl_is_cpt( $type = 'single' ){

	if( false == ( $currenttype = get_post_type() ) )
		return false;

	$std_pt = ( 'post' == $currenttype || 'page' == $currenttype || 'attachment' == $currenttype ) ? true : false;

	$is_type = ( ( $type == 'archive' && is_archive() ) || $type == 'single' ) ? true : false;

	return ( $is_type && !$std_pt  ? true : false );

}

/**
*
* @TODO do
*
*/
function get_post_type_plural( $id = null ){

	if(isset($id))
		return $id.'_archive';
	else
		return get_post_type().'_archive';
}


// ------------------------------------------
// COLOR HELPERS
// ------------------------------------------
function pl_hash_strip( $color ){

	return str_replace('#', '', $color);
}

function pl_check_color_hash( $color ) {

	if ( preg_match( '/^#?([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color ) )
		return true;
	else
		return false;
}

/**
 * PageLines Hashify
 *
 * Adds the # symbol to the hex value of the color being used
 *
 * @param $color
 *
 * @return string
 */
function pl_hashify( $color ){

	if( is_int( $color ) )
		$color = strval( $color );

	$clean_hex = str_replace('#', '', $color);

	return sprintf('#%s', $clean_hex);
}


// ------------------------------------------
// HOOK/FILTER UTILITIES
// ------------------------------------------

/**
 * PageLines Register Hook
 *
 * Calls a hook and passes the hook name in as an argument
 *
 */
function pagelines_register_hook( $hook_name, $hook_area_id = null){

	/** Do The Hook	*/
	do_action( $hook_name, $hook_name, $hook_area_id);

}

/**
 * PageLines Template Area
 *
 * Does hooks for template areas
 *
 */
function pagelines_template_area( $hook_name, $hook_area_id = null){

	/** Do The Hook	*/
	do_action( $hook_name, $hook_area_id);

}

// ------------------------------------------
// HELPERS
// ------------------------------------------

/**
 * Polishes a Key for UI presentation
 */
function ui_key($key){

	return ucwords( str_replace( '_', ' ', str_replace( 'pl_', ' ', $key) ) );
}

/**
 * PageLines Strip
 *
 * Strips White Space
 */
function plstrip( $t ){

	if( is_pl_debug() )
		return $t;

	return preg_replace( '/\s+/', ' ', $t );
}

/**
 * Gets the contents from a file in a secure & fast way
 */
function pl_file_get_contents( $filename ) {

	if ( is_file( $filename ) ) {

		$file = file( $filename, FILE_SKIP_EMPTY_LINES );
		$out = '';
		if( is_array( $file ) )
			foreach( $file as $contents )
				$out .= $contents;

		if( $out )
			return $out;
		else
			return false;
	}
}

/**
 * Custom Trim Excerpt
 *
 * Returns the excerpt at a user-defined length
 *
 * @param   $text - input text
 * @param   $length - number of words
 *
 * @return  string - concatenated with an ellipsis
 */
function custom_trim_excerpt($text, $length) {

	$text = strip_shortcodes( $text ); // optional
	$text = strip_tags($text);

	$words = explode(' ', $text, $length + 1);
	if ( count($words) > $length) {
		array_pop($words);
		$text = implode(' ', $words);
		$hellip = '&nbsp;<span class="hellip">[&hellip;]</span>';
	} else {
		$hellip = '';
	}
	return ($text != '') ? $text.$hellip : '';
}

/**
 * Returns whether IE, optionally if a certain version
 */
function pl_detect_ie( $version = false ) {

	global $is_IE;
	if ( ! $version && $is_IE ) {

		return round( substr($_SERVER['HTTP_USER_AGENT'], strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') + 5, 3) );
	}

	if ( $is_IE && is_int( $version ) && stristr( $_SERVER['HTTP_USER_AGENT'], sprintf( 'msie %s', $version ) ) )
		return true;
	else
		return false;
}


/**
 * Return the raw URI
 *
 * @param $full bool Show full or just request.
 * @return string
 */
function pl_get_uri( $full = true ) {

	if ( $full )
		return  $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
	else
		return  $_SERVER["REQUEST_URI"];
}

/**
 * Outputs inline CSS markup to page
 */
function inline_css_markup($id, $css, $echo = true){
	$mark = sprintf('%2$s<style type="text/css" id="%3$s">%2$s %1$s %2$s</style>%2$s', $css, "\n", $id);

	if($echo)
		echo $mark;
	else
		return $mark;
}

/**
 *
 * @TODO document
 *
 */
function pl_urlencode($text, $allowed = false) {
	$whitelist_tags = '<span><em><strong><i><b><u><code><br><strike><sub><sup>';
	return urlencode( trim( strip_tags( stripslashes($text), ($allowed) ? $whitelist_tags : false ) ) );
}


/**
 *
 * @TODO document
 *
 */
function pl_strip($text, $allowed = true) {
	$whitelist_tags = '<span><em><strong><i><b><u><code><br><strike><sub><sup>';
	return trim( strip_tags($text, ($allowed) ? $whitelist_tags : false) );
}


/**
 *
 * @TODO document
 *
 */
function pl_ehtml($text) {
	echo pl_html($text);
}


/**
 *
 * @TODO document
 *
 */
function pl_html($text) {
	return trim( htmlentities( stripslashes( $text ), ENT_QUOTES, 'UTF-8' ) );
}


/**
 *
 * @TODO document
 *
 */
function pl_texturize($text, $stripslashes = false, $decode = false) {
	return trim( wptexturize(($decode) ? urldecode($text) : (($stripslashes) ? stripslashes($text) : $text ) ) );
}


/**
 *
 * @TODO document
 *
 */
function pl_htmlspecialchars($text, $stripslashes = false, $decode = false) {
	return trim( htmlspecialchars(($decode) ? urldecode($text) : (($stripslashes) ? stripslashes($text) : $text ) ) );
}


/**
 *
 * @TODO document
 *
 */
function pl_noscripts($text) {
	return trim( pl_strip_only(stripslashes($text), '<script>', true ) );
}


/**
 *
 * @TODO document
 *
 */
function pl_strip_js($text) {
	return trim( pl_strip_only($text, '<script>', true) );
}


/**
 *
 * @TODO document
 *
 */
function pl_strip_only($str, $tags, $stripContent = false) {

	$content = '';
	if (!is_array($tags)) {
		$tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));
		if (end($tags) == '') array_pop($tags);
	}
	foreach ($tags as $tag) {
		if ($stripContent) $content = '(.+</'.$tag.'[^>]*>|)';
		$str = preg_replace('#</?'.$tag.'[^>]*>'.$content.'#is', '', $str);
	}
	return $str;
}

// ------------------------------------------
// ARRAY FUNCTIONS
// ------------------------------------------

/**
 * Search in an array, return full info.
 */
function array_search_ext($arr, $search, $exact = true, $trav_keys = null)
{
  if(!is_array($arr) || !$search || ($trav_keys && !is_array($trav_keys))) return false;
  $res_arr = array();
  foreach($arr as $key => $val)
  {
    $used_keys = $trav_keys ? array_merge($trav_keys, array($key)) : array($key);
    if(($key === $search) || (!$exact && (strpos(strtolower($key), strtolower($search)) !== false))) $res_arr[] = array('type' => "key", 'hit' => $key, 'keys' => $used_keys, 'val' => $val);
    if(is_array($val) && ($children_res = array_search_ext($val, $search, $exact, $used_keys))) $res_arr = array_merge($res_arr, $children_res);
    else if(($val === $search) || (!$exact && (strpos(strtolower($val), strtolower($search)) !== false))) $res_arr[] = array('type' => "val", 'hit' => $val, 'keys' => $used_keys, 'val' => $val);
  }
  return $res_arr ? $res_arr : false;
}

/**
 * Insert into array at a position.
 *
 * @param $orig array Original array.
 * @param $new array Array to insert.
 * @param $offset int Offset
 * @return array
 */
function pl_insert_into_array( $orig, $new, $offset ) {

	$newArray = array_slice($orig, 0, $offset, true) +
	            $new +
	            array_slice($orig, $offset, NULL, true);
	return $newArray;
}


/**
 * Insert into array before or after a key.
 *
 * @param $array array Original array.
 * @param $key str Key to find.
 * @param $insert_array array The array data to insert.
 * @param $before bool Insert before or after.
 * @return array
 */
function pl_array_insert( $array, $key, $insert_array, $before = FALSE ) {
	$done = FALSE;
	foreach ($array as $array_key => $array_val) {
		if (!$before) {
			$new_array[$array_key] = $array_val;
		}
		if ($array_key == $key && !$done) {
			foreach ($insert_array as $insert_array_key => $insert_array_val) {
				$new_array[$insert_array_key] = $insert_array_val;
			}
			$done = TRUE;
		}
		if ($before) {
			$new_array[$array_key] = $array_val;
		}
	}
	if (!$done) {
		$new_array = array_merge($array, $insert_array);
	}
	// Put the new array in the place of the original.
	return $new_array;
}

/**
 *
 * Return sorted array based on supplied key
 *
 * @since 2.0
 * @return sorted array
 */
function pagelines_array_sort( $a, $subkey, $pre = null, $dec = null ) {

	if ( ! is_array( $a) || ( is_array( $a ) && count( $a ) <= 1 ) )
		return $a;

	foreach( $a as $k => $v ) {
		$b[$k] = ( $pre ) ? strtolower( $v[$pre][$subkey] ) : strtolower( $v[$subkey] );
	}
	( !$dec ) ? asort( $b ) : arsort($b);
	foreach( $b as $key => $val ) {
		$c[] = $a[$key];
	}
	return $c;
}

// ------------------------------------------
// WORDPRESS UTILITIES
// ------------------------------------------

/**
 * Show Posts Nav
 *
 * Checks to see if there is more than one page, if so return true so nav is shown
 *
 * @return bool
 * @TODO does this add a query?
 */
function show_posts_nav() {
	global $wp_query;
	return ($wp_query->max_num_pages > 1);
}

/**
 * Overrides default excerpt handling so we have more control
 *
 * @since 1.2.4
 */
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'improved_trim_excerpt');
function improved_trim_excerpt($text) {

	// Group options at top :)
	global $ex_length, $ex_tags;

	if(pl_has_editor()){
		$allowed_tags = ( isset( $ex_tags ) && '' != $ex_tags ) ? $ex_tags : '';
		$excerpt_len = ( isset( $ex_length ) && '' != $ex_length ) ? $ex_length : 55;
	} else {
		$allowed_tags = (pl_setting('excerpt_tags')) ? pl_setting('excerpt_tags') : '';
		$excerpt_len = (pl_setting('excerpt_len')) ? pl_setting('excerpt_len') : 55;
	}

	$raw_excerpt = $text;
	if ( '' == $text ) {
		$text = get_the_content('');

		$text = strip_shortcodes( $text );


		$text = apply_filters('the_content', $text);

		$text = preg_replace('@<script[^>]*?>.*?</script>@si', '', $text); // PageLines - Strip JS

		$text = str_replace(']]>', ']]&gt;', $text);

		$text = strip_tags($text, $allowed_tags); // PageLines - allow more tags


		$excerpt_length = apply_filters('excerpt_length', $excerpt_len );
		$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');

		$words = preg_split('/[\n\r\t ]+/', $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);

		if ( count($words) > $excerpt_length ) {
			array_pop($words);
			$text = implode(' ', $words);
			$text = $text . $excerpt_more;
		} else
			$text = implode(' ', $words);

	}
	return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
}

/**
 * Generates an really short excerpt from the content or postid for tweets, facebook etc
 *
 * @param int|object $post_or_id can be the post ID, or the actual $post object itself
 * @param int $words the amount of words to allow
 * @param string $excerpt_more the text that is applied to the end of the excerpt if we algorithically snip it
 * @return string the snipped excerpt or the manual excerpt if it exists
 */
function pl_short_excerpt($post_or_id, $words = 10, $excerpt_more = ' [...]') {

	if ( is_object( $post_or_id ) )
		$postObj = $post_or_id;
	else $postObj = get_post($post_or_id);

	$raw_excerpt = $text = $postObj->post_excerpt;
	if ( '' == $text ) {
		$text = $postObj->post_content;

		$text = strip_shortcodes( $text );

		$text = sanitize_text_field( $text );

		$text = apply_filters('the_content', $text);
		$text = str_replace(']]>', ']]&gt;', $text);
		$text = strip_tags($text);
		$excerpt_length = $words;

		$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
		if ( count($words) > $excerpt_length ) {
			array_pop($words);
			$text = implode(' ', $words);
			$text = $text . $excerpt_more;
		} else {
			$text = implode(' ', $words);
		}
	}
	return $text;
}

/**
 *
 *  Sets up global post ID and $post global for handling, reference and consistency
 *
 *  @package PageLines DMS
 *  @subpackage Functions Library
 *  @since 1.0.0
 *
 */
function pagelines_id_setup(){
	global $post;
	global $pagelines_ID;
	global $pagelines_post;

	if(isset($post) && is_object($post)){
		$pagelines_ID = $post->ID;
		$pagelines_post = $post;
	}
	else {
		$pagelines_post = '';
		$pagelines_ID = '';
	}

}

// ------------------------------------------
// IMAGE UTILITIES
// ------------------------------------------

function pl_get_image_data( $image_url, $logo = false ) {
			
	if( ! $logo ) {
		$defaults = array( 
			'url'	=>	$image_url,
			'alt'	=> '',
			'title'	=> ''
			);
	} else {
		$defaults = array( 
			'url'	=>	$image_url,
			'alt'	=> esc_attr( get_bloginfo('description') ),
			'title'	=> esc_attr( get_bloginfo('name') )
			);
	}
	
	$ID = _get_image_id_from_url( $image_url );

	if( empty( $ID ) )
		return $defaults;
	
	$data = array();
		
	$data['alt'] = get_post_meta( $ID, '_wp_attachment_image_alt', true );
	if( '' === $data['alt'] )
		unset( $data['alt'] );

	$data['title'] = get_the_title( $ID );
	if( false !== ( strpos( $data['title'], 'PageLines-' ) ) || '' === $data['title'] )
		unset( $data['title'] );

	return wp_parse_args( $data, $defaults );
}

function _get_image_id_from_url($image_url) {

	global $wpdb;
	$prefix = $wpdb->prefix;
	$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM " . $prefix . "posts" . " WHERE guid='%s';", $image_url ));

    return ( is_array( $attachment ) && isset( $attachment[0])) ? $attachment[0] : array(); 
}

/**
 * Get just the WordPress thumbnail URL - False if not there.
 */
function pl_the_thumbnail_url( $post_id, $size = false ){

	if( has_post_thumbnail($post_id) ){

		$img_data = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size, false);

		$a['img'] = ($img_data[0] != '') ? $img_data[0] : '';

		return $a['img'];

	} else
		return false;
}


// ------------------------------------------
// CSS Utilities
// ------------------------------------------

/**
 *
 *  Loads Special PageLines CSS Files, Optimized
 *
 *  @package PageLines DMS
 *  @subpackage Functions Library
 *  @since 1.2.0
 *
 */
function pagelines_draw_css( $css_url, $id = '', $enqueue = false){
	echo '<link rel="stylesheet" href="'.$css_url.'" />'."\n";
}


/**
 *
 *  Abstracts the Enqueue of Stylesheets, fixes bbPress issues with dropping hooks
 *
 *  @package PageLines DMS
 *  @since 1.3.0
 *
 */
function pagelines_load_css( $css_url, $id, $hash = PL_CORE_VERSION, $enqueue = true){

	wp_enqueue_style($id, $css_url, array(), $hash, 'all');
}

/**
 *
 *  Loading CSS using relative path to theme root. This allows dynamic versioning, overriding in child theme
 *
 *  @package PageLines DMS
 *  @since 1.4.0
 *
 */
function pagelines_load_css_relative( $relative_style_url, $id){

	$rurl = '/' . $relative_style_url;

	if( is_file(get_stylesheet_directory() . $rurl ) ){

		$cache_ver = pl_cache_version( get_stylesheet_directory() . $rurl );

		pagelines_load_css( PL_CHILD_URL . $rurl , $id, $cache_ver);

	} elseif(is_file(get_template_directory() . $rurl) ){

		$cache_ver = pl_cache_version( get_template_directory() . $rurl );

		pagelines_load_css( PL_PARENT_URL . $rurl , $id, $cache_ver);

	}


}

/**
 *
 * Get cache version number
 *
 *
 */
function pl_cache_version( $path, $version = PL_CORE_VERSION ){
	$date_modified = filemtime( $path );
	$cache_ver = str_replace('.', '', $version) . '-' . date('mdGis', $date_modified);

	return $cache_ver;
}

/**
 *
 *  Get Stylesheet Version
 *
 *  @package PageLines DMS
 *  @since 1.4.0
 *
 */
function pagelines_get_style_ver( $tpath = false ){

	// Get cache number that accounts for edits to base.css or style.css
	if( is_file(get_stylesheet_directory() .'/base.css') && !$tpath ){
		$date_modified = filemtime( get_stylesheet_directory() .'/base.css' );
		$cache_ver = str_replace('.', '', PL_CHILD_VERSION) . '-' . date('mdGis', $date_modified);
	} elseif(is_file(get_stylesheet_directory() .'/style.css') && !$tpath ){
		$date_modified = filemtime( get_stylesheet_directory() .'/style.css' );
		$cache_ver = str_replace('.', '', PL_CORE_VERSION) .'-'.date('mdGis', $date_modified);
	} elseif(is_file(get_template_directory() .'/style.css')){
		$date_modified = filemtime( get_template_directory() .'/style.css' );
		$cache_ver = str_replace('.', '', PL_CORE_VERSION) .'-'.date('mdGis', $date_modified);
	} else {
		$cache_ver = PL_CORE_VERSION;
	}


	return $cache_ver;

}

// ------------------------------------------
// LESS UTILITIES
// ------------------------------------------

function get_pl_reset_less_url() {

	$flush = array( 'pl_reset_less' => 1 );

	$request = explode( '?', $_SERVER['REQUEST_URI'] );

	$page = $request[0];

	$query = array();

	if ( isset( $request[1] ) )
		wp_parse_str( $request[1], $query );

	$query = wp_parse_args( $flush, $query );

	$url = sprintf( '%s://%s%s?%s',
		is_ssl() ? 'https' : 'http',
		$_SERVER['HTTP_HOST'],
		$page,
		http_build_query( $query )
		);

	return $url;
}


// ------------------------------------------
// EXTENSIONS UTILITIES
// ------------------------------------------

/**
 * return array of PageLines plugins.
 * Since 2.0
 */
function pagelines_register_plugins() {

	$pagelines_plugins = array();
	$plugins = get_option('active_plugins');
	if ( $plugins ) {
		foreach( $plugins as $plugin ) {
			$a = get_file_data( WP_PLUGIN_DIR . '/' . $plugin, $default_headers = array( 'pagelines' => 'PageLines' ) );
			if ( !empty( $a['pagelines'] ) ) {
				$pagelines_plugins[] = str_replace( '.php', '', basename($plugin) );
			}

		}
	}
	return $pagelines_plugins;
}

/**
 * Add pages to main settings area.
 *
 * @since 2.2
 *
 * @param $args Array as input.
 * @param string $name Name of page.
 * @param string $title Title of page.
 * @param string $path Function use to get page contents.
 * @param array $array Array containing page page of settings.
 * @param string $type Type of page.
 * @param string $raw Send raw HTML straight to the page.
 * @param string $layout Layout type.
 * @param string $icon URI for page icon.
 * @param int $postion Position to insert into main menu.
 * @return array $optionarray
 */
function pl_add_options_page( $args ) {

	if( pl_has_editor() ){

		global $pagelines_add_settings_panel;
		
		$d = array(
			'name' 	=> 'No Name',
			'icon'	=> 'icon-edit',
			'pos'	=> 10,
			'opts' 	=> array()
		);
		
		
		if( ! isset($args['opts']) && isset($args['array']) )
			$args['opts'] = $args['array']; 


		if( ! isset($args['pos']) && isset($args['position']) )
			$args['pos'] = $args['position']; 

		$a = wp_parse_args( $args, $d );

		$id = pl_create_id($a['name']);

		// make sure its not set elsewhere. Navbar was already set, and we were writing twice
		if( !isset( $pagelines_add_settings_panel[ $id ]) )
			$pagelines_add_settings_panel[ $id ] = $a;
			
	
	}
	
	
}

/**
 * Filter to add custom pages to core settings area
 *
 * @since 3.0
 */
add_filter( 'pl_settings_array', 'pl_add_settings_panel', 15 );
function pl_add_settings_panel( $settings ){

	global $pagelines_add_settings_panel;
	
	if ( !isset( $pagelines_add_settings_panel ) || !is_array( $pagelines_add_settings_panel ) )
		return $settings;

	foreach( $pagelines_add_settings_panel as $panel => $setup ) {

		if(strpos($setup['icon'], "http://") !== false)
			$setup['icon'] = 'icon-circle';
			
		$setup['opts'] = process_to_new_option_format( $setup['opts'] );
		
		if(!isset($settings[ $panel ]))
			$settings[ $panel ] = $setup;


	}
	
	return $settings;

}


add_action( 'wp_head', 'load_child_style', 20 );
function load_child_style() {

	if ( !defined( 'PL_CUSTOMIZE' ) )
		return;

	// check for MU styles
	if ( is_multisite() ) {

		global $blog_id;
		$mu_style = sprintf( '%s/blogs/%s/style.css', EXTEND_CHILD_DIR, $blog_id );
		if ( is_file( $mu_style ) ) {
			$mu_style_url = sprintf( '%s/blogs/%s/style.css', EXTEND_CHILD_URL, $blog_id );
			$cache_ver = '?ver=' . pl_cache_version( $mu_style );
			pagelines_draw_css( $mu_style_url . $cache_ver, 'pl-extend-style' );
		}
	} else {
		if ( is_file( PL_EXTEND_STYLE_PATH ) ){

			$cache_ver = '?ver=' . pl_cache_version( PL_EXTEND_STYLE_PATH );
			pagelines_draw_css( PL_EXTEND_STYLE . $cache_ver, 'pl-extend-style' );
		}
	}
}

add_action( 'init', 'load_child_functions' );
function load_child_functions() {
	if ( !defined( 'PL_CUSTOMIZE' ) )
		return;

	// check for MU styles
	if ( is_multisite() ) {

		global $blog_id;
		$mu_functions = sprintf( '%s/blogs/%s/functions.php', EXTEND_CHILD_DIR, $blog_id );
		$mu_less = sprintf( '%s/blogs/%s/style.less', EXTEND_CHILD_DIR, $blog_id );
		if ( is_file( $mu_functions ) )
			require_once( $mu_functions );
		if ( is_file( $mu_less ) )
			pagelines_insert_core_less( $mu_less );
	} else {
		$less = sprintf( '%s/style.less', EXTEND_CHILD_DIR );
		if ( is_file( PL_EXTEND_FUNCTIONS ) )
			require_once( PL_EXTEND_FUNCTIONS );
		if ( is_file( $less ) )
			pagelines_insert_core_less( $less );
	}
}


// ------------------------------------------
// DEVELOPMENT
// ------------------------------------------

/**
 * Displays query information in footer (For testing - NOT FOR PRODUCTION)
 */
function show_query_analysis(){
	if (current_user_can('administrator')){
	    global $wpdb;
	    echo '<pre>';
	    print_r($wpdb->queries);
	    echo '</pre>';
	}
}

/**
 * Debugging, prints nice array.
 * Sends to the footer in all cases.
 *
 */
function plprint( $data, $title = false, $echo = false) {

	if ( ! is_pl_debug() || ! current_user_can('manage_options') )
		return;

	ob_start();

		echo '<pre class="plprint">';

		if ( $title )
			printf('<h3>%s</h3>', $title);

		echo esc_html( print_r( $data, true ) );

		echo '</pre>';

	$data = ob_get_clean();

	if ( $echo )
		echo $data;
	elseif ( false === $echo )
		add_action( 'shutdown', create_function( '', sprintf('echo \'%s\';', $data) ) );
	else
		return $data;
}

/**
 * Is framework in debug mode?
 *
 * @return bool
 */
function is_pl_debug() {

	if ( defined( 'PL_DEV' ) && PL_DEV )
		return true;
	if ( pl_setting( 'enable_debug' ) )
		return true;
}

/**
 * Show debug info in footer ( wrapped in tags )
 *
 */
function pl_debug( $text = '', $before = "\n/*", $after = '*/' ) {

	if ( ! is_pl_debug() )
		return;

	$out = sprintf( 'echo "%s %s %s";', $before, $text, $after );
	add_action( 'shutdown', create_function( '', $out ), 9999 );

}

function pl_source_comment( $text, $spacing = 1 ) {

	$newline = ($spacing) ? "\n" : '';

	$double = ($spacing == 2) ? "\n\n" : $newline;

	return sprintf( '%s<!-- %s -->%s', $double, $text, $newline);

}

/**
 * Outputs HTML comment to source
 */
function plcomment( $data, $title = 'DEBUG', $type = 'html' ) {


	if( is_pl_debug() ){
		$open	= ( 'html' == $type ) ? "\n<!-- " : "\n/* ";
		$close	= ( 'html' == $type ) ? " -->\n" : "*/\n";

		$pre = sprintf( '%s START %s %s', $open, $title, $close );
		$post = sprintf( '%s END %s %s', $open, $title, $close );

		return $pre . $data . $post;

	} else {
		return $data;
	}
}


