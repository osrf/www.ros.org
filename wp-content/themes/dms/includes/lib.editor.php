<?php

/*
 *	Get index value in array, does shortcodes or default
 */
function pl_array_get( $key, $array, $default = false ){
	
	if( isset( $array[$key] ) && $array[$key] != '' )
		$val = $array[$key];
	else
		$val = $default;
	
	return do_shortcode( $val );
}

/*
 *	Editor functions - Always loaded
 */

function pl_has_editor(){

	return (class_exists('PageLinesTemplateHandler')) ? true : false;

}


// Function to be used w/ compabibility mode to de
function pl_deprecate_v2(){

	if(pl_setting('enable_v2'))
		return false;
	else 
		return true;

}


function pl_use_editor(){
	return true;
}

function pl_less_dev(){	
	if( defined( 'PL_LESS_DEV' ) && PL_LESS_DEV )
		return false; 
	else
		return false;
	
}


function pl_is_pro(){	
	// AP stop putting return true here!!
	$status = get_option( 'dms_activation', array( 'active' => false, 'key' => '', 'message' => '', 'email' => '' ) );
	
	$pro = (isset($status['active']) && true === $status['active']) ? true : false;
	
	return $pro;
	
}

function pl_has_dms_plugin(){	
	
	if( class_exists( 'DMSPluginPro' ) )
		return true;
	else 
		return false;
	
}

function pl_pro_text(){
	
	return (!pl_is_pro()) ? __('(Pro Edition Only)', 'pagelines') : '';
	
}

function pl_pro_disable_class(){
	
	return (!pl_is_pro()) ? 'pro-only-disabled' : ''; 
	
}



// Process old function type to new format
function process_to_new_option_format( $old_options ){

	$new_options = array();

	foreach($old_options as $key => $o){

		if($o['type'] == 'multi_option' || $o['type'] == 'text_multi'){

			$sub_options = array();
			foreach($o['selectvalues'] as $sub_key => $sub_o){
				$sub_options[ ] = process_old_opt($sub_key, $sub_o, $o);
			}
			$new_options[ ] = array(
				'type' 	=> 'multi',
				'title'	=> $o['title'],
				'opts'	=> $sub_options
			);
		} else {
			$new_options[ ] = process_old_opt($key, $o);
		}

	}

	return $new_options;
}

function process_old_opt( $key, $old, $otop = array()){

	if(isset($otop['type']) && $otop['type'] == 'text_multi')
		$old['type'] = 'text';

	$defaults = array(
        'type' 			=> 'check',
		'title'			=> '',
		'inputlabel'	=> '',
		'exp'			=> '',
		'shortexp'		=> '',
		'count_start'	=> 0,
		'count_number'	=> '',
		'selectvalues'	=> array(),
		'taxonomy_id'	=> '',
		'post_type'		=> '',
		'span'			=> 1,
		'col'			=> 1,
		'default'		=> ''
	);

	$old = wp_parse_args($old, $defaults);

	$exp = ($old['exp'] == '' && $old['shortexp'] != '') ? $old['shortexp'] : $old['exp'];

	if($old['type'] == 'text_small'){
		$type = 'text';
	} elseif($old['type'] == 'colorpicker'){
		$type = 'color';
	} elseif($old['type'] == 'check_multi'){
		$type = 'multi';
		
		foreach($old['selectvalues'] as $key => &$info){
			$info['type'] = 'check';
		}
	} else
		$type = $old['type'];

	$new = array(
		'key'			=> ( !isset($old['key']) ) ? $key : $old['key'],
		'title'			=> $old['title'],
		'label'			=> ( !isset($old['label']) && isset($old['inputlabel'])) ? $old['inputlabel'] : $old['label'],
		'type'			=> $type,
		'help'			=> $exp,
		'opts'			=> ( !isset($old['opts']) && isset($old['selectvalues'])) ? $old['selectvalues'] : $old['opts'],
		'span'			=> $old['span'],
		'col'			=> $old['col'],
	);

	if ( isset( $old['template'] ) )
		$new['template'] = $old['template'];

	if($old['type'] == 'count_select'){
		$new['count_start'] = $old['count_start'];
		$new['count_number'] = $old['count_number'];
	}

	if($old['taxonomy_id'] != ''){
		$new['taxonomy_id'] = $old['taxonomy_id'];
	}	

	if($old['post_type'] != '')
		$new['post_type'] = $old['post_type'];
		
	if($old['default'] != '')
		$new['default'] = $old['default'];

	return $new;
}

function pl_create_id( $string ){

	$string = str_replace( ' ', '_', trim( strtolower( $string ) ) );
	$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);

	return $string;
}

function pl_new_clone_id(){
	return substr(uniqid(), -6);
}


function pl_create_int_from_string( $str ){
	
	return (int) substr( preg_replace("/[^0-9,.]/", "", md5( $str )), -6);
}


/*
 * Lets document utility functions
 */
function pl_add_query_arg( $args ) {

	global $wp;
	$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
	return add_query_arg( $args, $current_url );
}

/*
 * This function recursively converts an multi dimensional array into a multi layer object
 * Needed for json conversion in < php 5.2
 */
function pl_arrays_to_objects( array $array ) {

	$objects = new stdClass;

	if( is_array($array) ){
		foreach ( $array as $key => $val ) {

			if($key === ''){
				$key = 0;
			}

	        if ( is_array( $val ) && !empty( $val )) {


				$objects->{$key} = pl_arrays_to_objects( $val );

	        } else {

	            $objects->{$key} = $val;

	        }
	    }

	}

    return $objects;
}

function pl_animation_array(){
	$animations = array(
		'no-anim'			=> 'No Animation',
		'pla-fade'			=> 'Fade',
		'pla-scale'			=> 'Scale',
		'pla-from-left'		=> 'From Left',
		'pla-from-right'	=> 'From Right', 
		'pla-from-bottom'	=> 'From Bottom', 
		'pla-from-top'		=> 'From Top', 
	); 
	
	return $animations;
}

function pl_icon_array(){

	$icons = array(
		'glass',
		'music',
		'search',
		'envelope-alt',
		'heart',
		'star',
		'star-empty',
		'user',
		'film',
		'th-large',
		'th',
		'th-list',
		'ok',
		'remove',
		'zoom-in',
		'zoom-out',
		'power-off',
		'off',
		'signal',
		'gear',
		'cog',
		'trash',
		'home',
		'file-alt',
		'time',
		'road',
		'download-alt',
		'download',
		'upload',
		'inbox',
		'play-circle',
		'rotate-right',
		'repeat',
		'refresh',
		'list-alt',
		'lock',
		'flag',
		'headphones',
		'volume-off',
		'volume-down',
		'volume-up',
		'qrcode',
		'barcode',
		'tag',
		'tags',
		'book',
		'bookmark',
		'print',
		'camera',
		'font',
		'bold',
		'italic',
		'text-height',
		'text-width',
		'align-left',
		'align-center',
		'align-right',
		'align-justify',
		'list',
		'indent-left',
		'indent-right',
		'facetime-video',
		'picture',
		'pencil',
		'map-marker',
		'adjust',
		'tint',
		'edit',
		'share',
		'check',
		'move',
		'step-backward',
		'fast-backward',
		'backward',
		'play',
		'pause',
		'stop',
		'forward',
		'fast-forward',
		'step-forward',
		'eject',
		'chevron-left',
		'chevron-right',
		'plus-sign',
		'minus-sign',
		'remove-sign',
		'ok-sign',
		'question-sign',
		'info-sign',
		'screenshot',
		'remove-circle',
		'ok-circle',
		'ban-circle',
		'arrow-left',
		'arrow-right',
		'arrow-up',
		'arrow-down',
		'mail-forward',
		'share-alt',
		'resize-full',
		'resize-small',
		'plus',
		'minus',
		'asterisk',
		'exclamation-sign',
		'gift',
		'leaf',
		'fire',
		'eye-open',
		'eye-close',
		'warning-sign',
		'plane',
		'calendar',
		'random',
		'comment',
		'magnet',
		'chevron-up',
		'chevron-down',
		'retweet',
		'shopping-cart',
		'folder-close',
		'folder-open',
		'resize-vertical',
		'resize-horizontal',
		'bar-chart',
		'twitter-sign',
		'facebook-sign',
		'camera-retro',
		'key',
		'gears',
		'cogs',
		'comments',
		'thumbs-up-alt',
		'thumbs-down-alt',
		'star-half',
		'heart-empty',
		'signout',
		'linkedin-sign',
		'pushpin',
		'external-link',
		'signin',
		'trophy',
		'github-sign',
		'upload-alt',
		'lemon',
		'phone',
		'unchecked',
		'check-empty',
		'bookmark-empty',
		'phone-sign',
		'twitter',
		'facebook',
		'github',
		'unlock',
		'credit-card',
		'rss',
		'hdd',
		'bullhorn',
		'bell',
		'certificate',
		'hand-right',
		'hand-left',
		'hand-up',
		'hand-down',
		'circle-arrow-left',
		'circle-arrow-right',
		'circle-arrow-up',
		'circle-arrow-down',
		'globe',
		'wrench',
		'tasks',
		'filter',
		'briefcase',
		'fullscreen',
		'group',
		'link',
		'cloud',
		'beaker',
		'cut',
		'copy',
		'paperclip',
		'paper-clip',
		'save',
		'sign-blank',
		'reorder',
		'list-ul',
		'list-ol',
		'strikethrough',
		'underline',
		'table',
		'magic',
		'truck',
		'pinterest',
		'pinterest-sign',
		'google-plus-sign',
		'google-plus',
		'money',
		'caret-down',
		'caret-up',
		'caret-left',
		'caret-right',
		'columns',
		'sort',
		'sort-down',
		'sort-up',
		'envelope',
		'linkedin',
		'rotate-left',
		'undo',
		'legal',
		'dashboard',
		'comment-alt',
		'comments-alt',
		'bolt',
		'sitemap',
		'umbrella',
		'paste',
		'lightbulb',
		'exchange',
		'cloud-download',
		'cloud-upload',
		'user-md',
		'stethoscope',
		'suitcase',
		'bell-alt',
		'coffee',
		'food',
		'file-text-alt',
		'building',
		'hospital',
		'ambulance',
		'medkit',
		'fighter-jet',
		'beer',
		'h-sign',
		'plus-sign-alt',
		'double-angle-left',
		'double-angle-right',
		'double-angle-up',
		'double-angle-down',
		'angle-left',
		'angle-right',
		'angle-up',
		'angle-down',
		'desktop',
		'laptop',
		'tablet',
		'mobile-phone',
		'circle-blank',
		'quote-left',
		'quote-right',
		'spinner',
		'circle',
		'mail-reply',
		'reply',
		'github-alt',
		'folder-close-alt',
		'folder-open-alt',
		'expand-alt',
		'collapse-alt',
		'smile',
		'frown',
		'meh',
		'gamepad',
		'keyboard',
		'flag-alt',
		'flag-checkered',
		'terminal',
		'code',
		'reply-all',
		'mail-reply-all',
		'star-half-full',
		'star-half-empty',
		'location-arrow',
		'crop',
		'code-fork',
		'unlink',
		'question',
		'info',
		'exclamation',
		'superscript',
		'subscript',
		'eraser',
		'puzzle-piece',
		'microphone',
		'microphone-off',
		'shield',
		'calendar-empty',
		'fire-extinguisher',
		'rocket',
		'maxcdn',
		'chevron-sign-left',
		'chevron-sign-right',
		'chevron-sign-up',
		'chevron-sign-down',
		'html5',
		'css3',
		'anchor',
		'unlock-alt',
		'bullseye',
		'ellipsis-horizontal',
		'ellipsis-vertical',
		'rss-sign',
		'play-sign',
		'ticket',
		'minus-sign-alt',
		'check-minus',
		'level-up',
		'level-down',
		'check-sign',
		'edit-sign',
		'external-link-sign',
		'share-sign',
		'compass',
		'collapse',
		'collapse-top',
		'expand',
		'euro',
		'eur',
		'gbp',
		'dollar',
		'usd',
		'rupee',
		'inr',
		'yen',
		'jpy',
		'renminbi',
		'cny',
		'won',
		'krw',
		'bitcoin',
		'btc',
		'file',
		'file-text',
		'sort-by-alphabet',
		'sort-by-alphabet-alt',
		'sort-by-attributes',
		'sort-by-attributes-alt',
		'sort-by-order',
		'sort-by-order-alt',
		'thumbs-up',
		'thumbs-down',
		'youtube-sign',
		'youtube',
		'xing',
		'xing-sign',
		'youtube-play',
		'dropbox',
		'stackexchange',
		'instagram',
		'flickr',
		'adn',
		'bitbucket',
		'bitbucket-sign',
		'tumblr',
		'tumblr-sign',
		'long-arrow-down',
		'long-arrow-up',
		'long-arrow-left',
		'long-arrow-right',
		'apple',
		'windows',
		'android',
		'linux',
		'dribbble',
		'skype',
		'foursquare',
		'trello',
		'female',
		'male',
		'gittip',
		'sun',
		'moon',
		'archive',
		'bug',
		'vk',
		'weibo',
		'renren',
		'pagelines',
	);
	
	$r = asort($icons);
	$icons = array_values($icons);
	return apply_filters( 'pl_icon_array', $icons );
}

function pl_button_classes(){
	$array = array(
		''			 		=> 'Default',
		'btn-primary'		=> 'Dark Blue',
		'btn-info'			=> 'Light Blue',
		'btn-success'		=> 'Green',
		'btn-warning'		=> 'Orange',
		'btn-important'		=> 'Red',
		'btn-inverse'		=> 'Black',
	); 
	return $array;
}

function get_sidebar_select(){


	global $wp_registered_sidebars;
	$allsidebars = $wp_registered_sidebars;
	ksort($allsidebars);

	$sidebar_select = array();
	foreach($allsidebars as $key => $sb){

		$sidebar_select[ $sb['id'] ] = array( 'name' => $sb['name'] );
	}

	return $sidebar_select;
}

function pl_count_sidebar_widgets( $sidebar_id ){

	$total_widgets = wp_get_sidebars_widgets();

	if(isset($total_widgets[ $sidebar_id ]))
		return count( $total_widgets[ $sidebar_id ] );
	else
		return false;
}

function pl_enqueue_script(  $handle, $src = false, $deps = array(), $ver = false, $in_footer = false ){
	
	global $wp_scripts;
	
	wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
}

function pl_add_theme_tab( $array ){
	
	global $pl_user_theme_tabs;
	
	if(!isset($pl_user_theme_tabs) || !is_array($pl_user_theme_tabs))
		$pl_user_theme_tabs = array(); 
		
		
	$pl_user_theme_tabs = array_merge($array, $pl_user_theme_tabs); 
	
	
	
}

function pl_default_thumb(){
	return PL_IMAGES.'/default-thumb.jpg';
}
function pl_default_image(){
	return PL_IMAGES.'/default-image.jpg';
}

function pl_dms_settings_link( $panel, $tab, $class = 'btn btn-edit btn-mini', $content = '<i class="icon-pencil"></i>'){
	
	return sprintf('<span data-tab-link="%s" data-stab-link="%s" class="%s">%s</span>', $panel, $tab, $class, $content);
	
}


function pl_blank_template(){
	if ( current_user_can( 'edit_theme_options' ) )
		return sprintf('<div class="alert pl-editor-only"><strong>Admin Notice:</strong><br/>This template returned blank and visitors will not see it. Refresh, settings, or data changes may be needed for output.</div>');
	else 
		return '';
	
}


function pl_shortcodize_url( $full_url ){
	$url = str_replace(home_url(), '[pl_site_url]', $full_url);
	
	return $url;
}

function pl_get_cache_key() {

	if ( '' != get_theme_mod( 'pl_cache_key' ) ) {
		return get_theme_mod( 'pl_cache_key' );
	} else { 	
		$cache_key = substr(uniqid(), -6);
		set_theme_mod( 'pl_cache_key', $cache_key );
		return $cache_key;
	}
}

function pl_get_image_sizes() {
	$sizes = get_intermediate_image_sizes();
	$sizes[] = 'full';
	return $sizes;
}
