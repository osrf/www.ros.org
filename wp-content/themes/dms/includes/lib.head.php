<?php


/**
 * Creates a global page ID for reference in editing and meta options (no unset warnings)
 *
 */
add_action('pagelines_before_html', 'pagelines_id_setup', 5);

function pl_scripts_on_ready(){

echo pl_source_comment("On Ready"); ?>
<script> /* <![CDATA[ */
!function ($) {
jQuery(document).ready(function() {
<?php pagelines_register_hook('pl_scripts_on_ready'); // Hook ?>
})
}(window.jQuery);
/* ]]> */
</script>

<?php
}


/**
 *
 * Wraps in standard js on ready format
 *
 * @since 2.0.0
 */
function pl_js_wrap( $js ){

	return sprintf('<script type="text/javascript">/*<![CDATA[*/ jQuery(document).ready(function(){ %s }); /*]]>*/</script>', $js);

}

/**
 * Fixed element area at top of site page.
 *
 **/
add_action('pagelines_site_wrap', 'pl_fixed_top_area');
function pl_fixed_top_area(){
	?>
	<div id="fixed-top" class="pl-fixed-top" data-region="fixed-top">
		<?php pagelines_template_area('pagelines_fixed_top', 'fixed_top'); // Hook ?>
	</div>
	<div class="fixed-top-pusher"></div>
	<script> jQuery('.fixed-top-pusher').height( jQuery('.pl-fixed-top').height() ) </script>
	
	<?php 
}




/**
 *
 * @TODO document
 *
 */
add_filter( 'user_contactmethods', 'pagelines_add_google_profile', 10, 1);
function pagelines_add_google_profile( $contactmethods ) {
	// Add Google Profiles
	$contactmethods['google_profile'] = __( 'Google Profile URL', 'pagelines' );
	return $contactmethods;
}


add_action( 'wp_head', 'pagelines_google_author_head' );
function pagelines_google_author_head() {
	global $post;
	if( ! is_page() && ! is_single() && ! is_author() )
		return;
	$google_profile = get_the_author_meta( 'google_profile', $post->post_author );
	if ( '' != $google_profile )
		printf( '<link rel="author" href="%s" />%s', $google_profile, "\n" );
}





/**
 * Register website Javascript
 */
add_action( 'wp_enqueue_scripts', 'pagelines_register_js' );
function pagelines_register_js() {

	wp_enqueue_script( 'pagelines-bootstrap-all', PL_JS . '/script.bootstrap.min.js', array( 'jquery' ), '2.2.2', true );
	wp_enqueue_script( 'pagelines-resizer', PL_JS . '/script.resize.js', array( 'jquery' ), PL_CORE_VERSION, true );
	wp_enqueue_script( 'pagelines-viewport', PL_JS . '/script.viewport.js', array( 'jquery' ), PL_CORE_VERSION, true );
	wp_enqueue_script( 'pagelines-waypoints', PL_JS . '/script.waypoints.js', array( 'jquery' ), PL_CORE_VERSION, true );
	wp_enqueue_script( 'pagelines-easing', PL_JS . '/script.easing.js', array( 'jquery' ), PL_CORE_VERSION, true );
	wp_enqueue_script( 'pagelines-fitvids', PL_JS . '/script.fitvids.js', array( 'jquery' ), PL_CORE_VERSION, true );
	wp_enqueue_script( 'pagelines-parallax', PL_JS . '/parallax.js', array( 'jquery' ), PL_CORE_VERSION, true );
	wp_enqueue_script( 'pagelines-common', PL_JS . '/pl.common.js', array( 'jquery' ), PL_CORE_VERSION, true );

	// Load Supersize BG Script

}

add_action( 'wp_print_styles', 'pagelines_get_childcss', 99);
function pagelines_get_childcss() {
	if ( ! is_admin() && is_child_theme() ){
		wp_enqueue_style( 'DMS-theme', get_bloginfo('stylesheet_url'), array(), pagelines_get_style_ver(), 'all');
	}
}


/**
 * Add Main PageLines Header Information
 */
add_action('pagelines_head', 'pagelines_head_common');
function pagelines_head_common(){
	global $pagelines_ID;
	$oset = array('post_id' => $pagelines_ID);

	pagelines_register_hook('pagelines_code_before_head'); // Hook

	printf('<meta http-equiv="Content-Type" content="%s; charset=%s" />',  get_bloginfo('html_type'),  get_bloginfo('charset'));

	pagelines_source_attribution();

	echo pl_source_comment('Title');

	// Draw Page <title> Tag. We use a filter to apply the actual titles.

	printf( '<title>%s</title>', wp_title( '',false ) );

	// Allow for extension deactivation of all css
	if(!has_action('override_pagelines_css_output')){

		// RTL Language Support

		// wordpress autoloads from child theme so if child theme has no rtl we need to load ours.
		if( 
			( is_rtl() 
				&& is_child_theme() 
				&& ! is_file( sprintf( '%s/rtl.css', get_stylesheet_directory() ) ) 
			) || ( is_rtl() && ! is_child_theme() ) 
		){
			add_action( 'wp_print_styles', create_function( '', 'pagelines_load_css_relative( "rtl.css", "pagelines-rtl" );' ), 99 );
		}
			
	}

	if ( pl_setting( 'facebook_headers' ) && ! has_action( 'disable_facebook_headers' ) && VPRO )
		pagelines_facebook_header();

	if(pl_setting('load_prettify_libs')){
		pagelines_add_bodyclass( 'prettify-on' );
		wp_enqueue_script( 'prettify', PL_JS . '/prettify/prettify.js' );
		wp_enqueue_style( 'prettify', PL_JS . '/prettify/prettify.css' );
		add_action( 'wp_head', create_function( '',  'echo pl_js_wrap("prettyPrint()");' ), 14 );
	}
	
	add_action( 'wp_head', create_function( '',  'echo pl_source_comment("Start >> Meta Tags and Inline Scripts", 2);' ), 0 );

	add_action( 'wp_print_styles', create_function( '',  'echo pl_source_comment("Styles");' ), 0 );

	add_action( 'wp_print_scripts', create_function( '',  'echo pl_source_comment("Scripts");' ), 0 );

	add_action( 'wp_print_footer_scripts', create_function( '',  'echo pl_source_comment("Footer Scripts");' ), 0 );

	add_action( 'admin_bar_menu', create_function( '',  'echo pl_source_comment("WordPress Admin Bar");' ), 0 );

	add_action( 'wp_head', 'pagelines_meta_tags', 9 );

	add_action( 'wp_head', 'pl_scripts_on_ready', 10 );

	// Headerscripts option > custom code
	if ( pl_setting( 'headerscripts' ) && pl_setting( 'headerscripts' ) != default_headerscript() )
		add_action( 'wp_head', create_function( '',  'print_pagelines_option("headerscripts");' ), 25 );

	if( pl_setting('asynch_analytics'))
		add_action( 'pagelines_head_last', create_function( '',  'echo pl_setting("asynch_analytics");' ), 25 );
}


function pagelines_meta_tags(){

	global $pagelines_ID;
	$oset = array('post_id' => $pagelines_ID);

	// Meta Images
	if(pl_setting('pagelines_favicon') && VPRO)
		printf('<link rel="shortcut icon" href="%s" type="image/x-icon" />%s', pl_setting('pagelines_favicon'), "\n");

	if(pl_setting('pagelines_touchicon'))
		printf('<link rel="apple-touch-icon" href="%s" />%s', pl_setting('pagelines_touchicon'), "\n");

	// Meta Data Profiles
	if(!apply_filters( 'pagelines_xfn', '' ))
		echo '<link rel="profile" href="http://gmpg.org/xfn/11" />'."\n";

	// Removes viewport scaling on Phones, Tablets, etc.
	if(!pl_setting('disable_mobile_view', $oset) && !apply_filters( 'disable_mobile_view', '' ))
		echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />';

}


function pagelines_source_attribution() {

	echo "\n\n<!-- ";
	printf ( "Site Crafted Using PageLines v%s - WordPress - HTML5 ( %s ) - www.PageLines.com ", PL_CORE_VERSION, get_pagelines_credentials( 'licence' ) );

	echo "-->\n";
}

/**
*
* @TODO do
*
*/
function pagelines_facebook_header() {

	if ( is_home() || is_archive() )
		return;

	if ( function_exists( 'is_bbpress' ) && is_bbpress() )
		return;

	global $pagelines_ID;

	if ( ! $pagelines_ID )
		return;

	$fb_img = apply_filters('pl_opengraph_image', pl_the_thumbnail_url( $pagelines_ID, 'full' ) );

	echo pl_source_comment('Facebook Open Graph');

	printf( "<meta property='og:title' content='%s' />\n", get_the_title($pagelines_ID));
	printf( "<meta property='og:url' content='%s' />\n", get_permalink($pagelines_ID));
	printf( "<meta property='og:site_name' content='%s' />\n", get_bloginfo( 'name' ));
	$fb_content = get_post( $pagelines_ID );
	if ( ! function_exists( 'sharing_plugin_settings' ) )
		printf( "<meta property='og:description' content='%s' />\n", pl_short_excerpt( $fb_content, 15 ) );
	printf( "<meta property='og:type' content='%s' />", (is_home()) ? 'website' : 'article');
	if($fb_img)
		printf( "\n<meta property='og:image' content='%s' />", $fb_img);
}





/**
*
* @TODO do
*
*/
function pagelines_runtime_supersize(){

	if ( has_action( 'pl_no_supersize' ) )
    return;

	global $pagelines_ID;
	$oset = array('post_id' => $pagelines_ID);
	$url = pl_setting('page_background_image_url', $oset);
	?>

	<script type="text/javascript"> /* <![CDATA[ */
	jQuery(document).ready(function(){
		jQuery.supersized({ slides  :  	[ { image : '<?php echo $url; ?>' } ] });
	});/* ]]> */
	</script>

<?php
}



/**
 * PageLines Title Tag ( deprecated )
 *
 * Checks for AIO or WPSEO functionality, if they both do not exist then this will define the HTML <title> tag for the theme.
 *
 * @TODO deleteme
 *
 * @internal filter pagelines_meta_title provided for over-writing the default title text.
 */
function pagelines_title_tag(){
	echo "<title>";

	if ( !function_exists( 'aiosp_meta' ) && !function_exists( 'wpseo_get_value' ) ) {
	// Pagelines seo titles.
		global $page, $paged;
		$title = wp_title( '|', false, 'right' );

		// Add the blog name.
		$title .= get_bloginfo( 'name' );

		// Add the blog description for the home/front page.
		$title .= ( ( is_home() || is_front_page() ) && get_bloginfo( 'description', 'display' ) ) ? ' | ' . get_bloginfo( 'description', 'display' ) : '';

		// Add a page number if necessary:
		$title .= ( $paged >= 2 || $page >= 2 ) ? ' | ' . sprintf( __( 'Page %s', 'pagelines' ), max( $paged, $page ) ) : '';
	} else
		$title = trim( wp_title( '', false ) );

	// Print the title.
	echo apply_filters( 'pagelines_meta_title', $title );

	echo "</title>";
}

/**
 * PageLines Title Tag Filter
 *
 * Filters wp_title so SEO plugins can override.
 *
 * @since 2.2.2
 *
 * @internal filter pagelines_meta_title provided for over-writing the default title text.
 */
add_filter( 'wp_title', 'pagelines_filter_wp_title' );
function pagelines_filter_wp_title( $title ) {
	global $wp_query, $s, $paged, $page;
	$sep = __( '|','pagelines' );
	$new_title = get_bloginfo( 'name' ) . ' ';
	$bloginfo_description = get_bloginfo( 'description' );
	if( is_feed() ) {
		$new_title = $title;
	} elseif ( ( is_home () || is_front_page() ) && ! empty( $bloginfo_description ) ) {
		$new_title .= $sep . ' ' . $bloginfo_description;
	} elseif ( is_category() ) {
		$new_title .= $sep . ' ' . single_cat_title( '', false );
	} elseif ( is_single() || is_page() ) {
		$new_title .= $sep . ' ' . single_post_title( '', false );
	} elseif ( is_search() ) {
		$new_title .= $sep . ' ' . sprintf( __( 'Search Results: %s','pagelines' ), esc_html( $s ) );
	} else
		$new_title .= $sep . ' ' . $title;
	if ( $paged >= 2 || $page >= 2 ) {
		$new_title .= ' ' . $sep . ' ' . sprintf( __( 'Page: %s', 'pagelines' ), max( $paged, $page ) );
	}
    return apply_filters( 'pagelines_meta_title', $new_title );
}




