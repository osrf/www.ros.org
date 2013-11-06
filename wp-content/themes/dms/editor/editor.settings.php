<?php


define('PL_SETTINGS', 'pl-settings');
function pl_settings_default(){
	return array( 'draft' => array(), 'live' => array() );
}

function pl_setting( $key, $args = array() ){
	global $plopts;

	if(!is_object($plopts)){
		$plpg = new PageLinesPage;
		$pldraft = new EditorDraft;
		$plopts = new PageLinesOpts;
	}

	$setting = $plopts->get_setting( $key, $args );

	if( is_array( $setting) )
		return $setting; 
	else 
		return do_shortcode( $setting );

}

function pl_setting_update( $args_or_key, $value = false, $scope = 'global', $mode = 'draft' ){
	$settings_handler = new PageLinesSettings;

	if( is_array($args_or_key) ){
		$args = $args_or_key;
	} else {

		$args = array(
			'key' 	=> $args_or_key,
			'val'	=> $value,
			'mode'	=> $mode,
			'scope'	=> $scope
		);

	}

	$settings_handler->update_setting( $args );

}

function pl_global( $key ){
	
	$settings = pl_opt( PL_SETTINGS, pl_settings_default() );
	
 	return (isset($settings[pl_get_mode()][$key])) ? $settings[pl_get_mode()][$key] : false;
	
}

function pl_global_update( $key, $value ){
	
	$settings = pl_opt( PL_SETTINGS, pl_settings_default() );
	
	$settings[ pl_get_mode() ][$key] = $value; 
	
	pl_opt_update( PL_SETTINGS, $settings);
	
}

function pl_local( $metaID, $key ){
	
	$settings = pl_meta($metaID, PL_SETTINGS, pl_settings_default() );
	
 	return (isset($settings[pl_get_mode()][$key])) ? $settings[pl_get_mode()][$key] : false;
	
}

function pl_local_update( $metaID, $key, $value ){
	
	$settings = pl_meta($metaID, PL_SETTINGS, pl_settings_default() );
	
	$settings[ pl_get_mode() ][$key] = $value; 
	
	pl_meta_update($metaID, PL_SETTINGS, $settings);
		
}

function pl_meta($id, $key, $default = false){

	$data = new PageLinesData;
	return $data->meta($id, $key, $default);

}


function pl_meta_update($id, $key, $value){

	$data = new PageLinesData;
	return $data->meta_update($id, $key, $value);

}

/*
 * This class contains all methods for interacting with WordPress' data system
 * It has no dependancy so it can be used as a substitute for WordPress native functions
 * The options system inherits from it.
 */
class PageLinesData {

	function meta($id, $key, $default = false){

		$val = get_post_meta($id, $key, true);

		if( (!$val || $val == '') && $default ){

			$val = $default;

		} elseif( is_array($val) && is_array($default)) {

			$val = wp_parse_args( $val, $default );

		}

		return $val;

	}

	function meta_update($id, $key, $value){

		update_post_meta($id, $key, $value);

	}


	function opt( $key, $default = false, $parse = false ){

		$val = get_option($key);

		if( !$val ){

			$val = $default;

		} elseif( $parse && is_array($val) && is_array($default)) {

			$val = wp_parse_args( $val, $default );

		}

		return $val;

	}

	function opt_update( $key, $value ){

		update_option($key, $value);

	}

	function user( $user_id, $key, $default = false ){

		$val = get_user_meta($user_id, $key, true);

		if( !$val ){

			$val = $default;

		} elseif( is_array($val) && is_array($default)) {

			$val = wp_parse_args( $val, $default );

		}

		return $val;

	}

	function user_update( $user_id, $key, $value ){
		update_user_meta( $user_id, $key, $value );
	}



}

/*
 *  PageLines Settings Interface
 */
class PageLinesSettings extends PageLinesData {

	var $pl_settings = PL_SETTINGS;
	var $default = array( 'draft' => array(), 'live' => array() );

	function global_settings(){

		$set = $this->opt( PL_SETTINGS );

		// Have to move this to an action because ploption calls pl_setting before all settings are loaded
		if( !$set || empty($set['draft']) || empty($set['live']) )
			add_action('pl_after_settings_load', array( $this, 'set_default_settings'));

		return $this->get_by_mode($set);

	}

	/*
	 *  Resets global options using custom child theme config file.
	 */
	function reset_global_child( $opts ){

		$fileOpts = new EditorFileOpts;		
		if( $fileOpts->file_exists() )
			$fileOpts->import( $fileOpts->file_exists() , $opts);
	}


	/*
	 *  Resets global options to an empty set
	 */
	function reset_global(){

		$set = $this->opt( PL_SETTINGS, $this->default );
		
		$set['draft'] = $this->default['draft'];
		
		$this->opt_update( PL_SETTINGS, $set );
		
		$this->set_default_settings();
	
	
		return $set;
	}
	/*
	 *  Resets all cached data including any detected cache plugins.
	 */
	function reset_caches() {
		do_action( 'extend_flush' );
		pl_flush_draft_caches( false );
		$cache_key = substr(uniqid(), -6);
		set_theme_mod( 'pl_cache_key', $cache_key );
	}

	/*
	 *  Resets local options to an empty set based on ID (works for type ID)
	 */
	function reset_local( $metaID ){

		$set = $this->meta( $metaID, PL_SETTINGS, $this->default );

		$set['draft'] = $this->default['draft'];

		$this->meta_update( $metaID, PL_SETTINGS, $set );

	}

	/*
	 *  Sets default values for global settings
	 */
	function set_default_settings(){

		$set = $this->opt( $this->pl_settings );

		$settings_defaults = $this->get_default_settings();

		if( !$set )
			$set = $this->default;

		if(empty($set['draft']))
			$set['draft']['settings'] = $settings_defaults;

		if(empty($set['live']))
			$set['live']['settings'] = $settings_defaults;

		$this->opt_update( $this->pl_settings, $set);

	}

	/*
	 *  Grabs global settings engine array, and default values (set in array)
	 */
	function get_default_settings(){
		$settings_object = new EditorSettings;

		$settings = $settings_object->get_set();


		$defaults = array();
		foreach($settings as $tab => $tab_settings){
			foreach($tab_settings['opts'] as $index => $opt){
				if($opt['type'] == 'multi'){
					foreach($opt['opts'] as $subi => $sub_opt){
						if(isset($sub_opt['default'])){
							$defaults[ $sub_opt['key'] ] = $sub_opt['default'];
						}
					}
				}
				if(isset($opt['default'])){
					$defaults[ $opt['key'] ] = $opt['default'];
				}
			}
		}

		return $defaults;
	}



	/*
	 *  Update a PageLines setting using arguments
	 */
	function update_setting( $args = array() ){

		$defaults = array(
			'key'	=> '',
			'val'	=> '',
			'mode'	=> 'draft',
			'scope'	=> 'global', 
			'uid'	=> 'settings'
		);

		$a = wp_parse_args( $args, $defaults );

		$scope = $a['scope'];
		$mode = $a['mode'];
		$key = $a['key'];
		$val = $a['val'];
		$uid = $a['uid'];

		$parse_value = array( $key => $val );

		if( $scope == 'global'){

			$settings = $this->opt( PL_SETTINGS, pl_settings_default() );
			
			$old_settings = (isset($settings[ $mode ][ $uid ])) ? $settings[ $mode ][ $uid ] : array();
	
			$settings[ $mode ][ $uid ] = wp_parse_args(  $parse_value, $old_settings);

			pl_opt_update( PL_SETTINGS, $settings );
			
		} elseif ( $scope == 'local' || $scope == 'type' ){
			global $plpg;
			
			$theID = ($scope == 'local') ? $plpg->id : $plpg->typeid;
			
			$settings = $this->meta( $theID, PL_SETTINGS, pl_settings_default() );
			
			$old_settings = (isset($settings[ $mode ][ $uid ])) ? $settings[ $mode ][ $uid ] : array();
		
			$settings[ $mode ][ $uid ] = wp_parse_args(  $parse_value, $old_settings);

			
			pl_meta_update( $theID, PL_SETTINGS, $settings );
		}
	

	}



	
	/*
	 *  Parse settings taking the top values over the bottom
	 * 	Deep parsing: Parses arguments on nested arrays then deals with overriding
	 *  Checkboxes: Handles checkboxes by using 'flip' value settings to toggle the value
	 */
	function settings_cascade( $top, $bottom ){


		if(!is_array( $bottom ))
			return $top;

		// Parse Args Deep
		foreach($bottom as $id => $settings){

			if( !isset( $top[ $id ]) )
				$top[ $id ] = $settings;

			elseif( is_array($settings) ){
				
				foreach( $settings as $key => $value ){
					
					if( !isset( $top[ $id ][ $key ] ) )
						$top[ $id ][ $key ] = $value;
						
				}
				
			}

		}

		$parsed_args = $top;

		foreach($parsed_args as $id => &$settings){

			if( is_array($settings) ){
				foreach($settings as $key => &$value){

					if(
						( !isset($value) || $value == '' || !$value )
						&& isset( $bottom[ $id ][ $key ] )
					)
						$value = $bottom[ $id ][ $key ];

					$flipkey = $key.'-flip';

					// flipping checkboxes
					if( isset( $parsed_args[$id] ) && isset( $parsed_args[$id][$flipkey] ) && isset( $bottom[$id][$key] ) ){

						$flip_val = $parsed_args[ $id ][ $flipkey ];
						$bottom_val = $bottom[ $id ][ $key ];

						if( $flip_val && $bottom_val ){
							$value = '';
						}


					}



				}
			}

		}
		unset($set);
		unset($value);

		return $parsed_args;
	}

}



/**
 *  PageLines *Page Specific* Settings Interface
 * 	Has a dependancy on the PageLinesPage object and EditorDraft object
 */
class PageLinesOpts extends PageLinesSettings {

	function __construct( ){

		global $plpg; 
		$this->page = (isset($plpg)) ? $plpg : new PageLinesPage;
	

		$this->local = $this->local_settings();
		$this->type = $this->type_settings();
		$this->global = $this->global_settings();
		$this->regions = (isset($this->global['regions'])) ? $this->global['regions'] : array();
		$this->set = $this->page_settings();

	}
	
	function get_set( $uniqueID ){
		
		if( isset($this->set[ $uniqueID ]) )
			return $this->set[ $uniqueID ]; 
		else 	
			return array();
		
	}


	function page_settings(){

		$set = $this->settings_cascade( $this->local, $this->settings_cascade($this->type, $this->global));
		
		//$set = wp_parse_args( $this->local, $this->global );
		 
		return $set;

	}



	function local_settings(){


		// if a template is active, lets use that.
		
		$set = $this->meta( $this->page->id, PL_SETTINGS );
		
		return $this->get_by_mode($set);

	}

	function type_settings(){

		$set = $this->meta( $this->page->typeid, PL_SETTINGS );

		return $this->get_by_mode($set);

	}

	function get_setting( $key, $args = array() ){

		$scope = (isset($args['scope'])) ? $args['scope'] : 'cascade';
		
		if( $scope == 'local' ){
		
			$settings = $this->local; 
		
		} elseif( $scope == 'type' ){
		
			$settings = $this->type; 
		
		} elseif( $scope == 'global' ){
		
			$settings = $this->global; 
		
		}else 
			$settings = $this->set; 
		
		$not_set = (isset($args['default'])) ? $args['default'] : false;
		
		$index = ( isset( $args['clone_id']) ) ? $args['clone_id'] : 'settings';

		return ( isset( $settings[ $index ][ $key ] ) ) ? $settings[ $index ][ $key ] : $not_set;

	}


	function get_by_mode( $set ){

		$set = wp_parse_args( $set, $this->default );

		$mode = (pl_draft_mode()) ? 'draft' : 'live';

		return $set[ $mode ];
	}




}

































////////////////////////////////////////////////////////////////////
//
// TODO rewrite all this to use the ^^ above classes methods...
//
////////////////////////////////////////////////////////////////////
function pl_opt( $key, $default = false, $parse = false ){

	$val = get_option($key);

	if( !$val ){

		$val = $default;

	} elseif( $parse && is_array($val) && is_array($default)) {

		$val = wp_parse_args( $val, $default );

	}

	return $val;

}

function pl_opt_update( $key, $value ){

	update_option($key, $value);

}








function pl_meta_setting( $key, $metaID ){

	global $pldrft;

	$mode = $pldrft->mode;

	$set = pl_meta( $metaID, PL_SETTINGS );

	$settings = ( isset($set[ $mode ]) ) ? $set[ $mode ] : array();

	return ( isset( $settings[ $key ] ) ) ? $settings[ $key ] : false;

}

function pl_global_setting( $key ){

	global $pldrft;

	$mode = $pldrft->mode;

	$set = pl_opt( PL_SETTINGS );

	$settings = ( isset($set[ $mode ]) ) ? $set[ $mode ] : array();

	return ( isset( $settings[ $key ] ) ) ? $settings[ $key ] : false;
}

/*
 *
 * Local Option
 *
 */
function pl_settings( $mode = 'draft', $metaID = false ){

	$default = array( 'draft' => array(), 'live' => array() );

	if( $metaID ){

		$set = pl_meta( $metaID, PL_SETTINGS, $default );

	} else {

		$set = pl_opt(PL_SETTINGS, $default);

	}

	$settings = ( isset($set[ $mode ]) ) ? $set[ $mode ] : $default;

	return $settings;

}

function pl_settings_update( $new_settings, $mode = 'draft', $metaID = false ){

	$default = array( 'draft' => array(), 'live' => array() );


	if( $metaID )
		$settings = pl_meta( $metaID, PL_SETTINGS );
	else
		$settings = pl_opt(PL_SETTINGS);

	// in case of empty, use live/draft default
	$settings = wp_parse_args($settings, pl_settings_default());

	// forgot why we stripslashes, if you remember, comment!
	$settings[ $mode ] = stripslashes_deep( $new_settings );

	// lets do some clean up
	// Gonna clear out all the empty values and arrays
	// Also, needs to be array or... deletehammer
	foreach($settings[$mode] as $uniqueID => $the_settings){
		
		if(is_array($the_settings)){
			foreach($the_settings as $setting_key => $val){
				if( $val === '' && $val !== 0 )
					unset( $settings[ $mode ][ $uniqueID ][ $setting_key ] );
			}
		}
		
		
	}

	if( $metaID )
		pl_meta_update( $metaID, PL_SETTINGS, $settings );
	else
		pl_opt_update( PL_SETTINGS, $settings );

	return $settings;
}

function pl_revert_settings( $metaID = false ){

	if( $metaID ){
		$set = pl_meta( $metaID, PL_SETTINGS, pl_settings_default() );

	} else {
		$set = pl_opt(PL_SETTINGS, pl_settings_default());
	}

	$set['draft'] = $set['live'];

	if( $metaID )
		pl_meta_update( $metaID, PL_SETTINGS, $set );
	else
		pl_opt_update( PL_SETTINGS, $set );

}


/*
 *
 * Global Option
 *
 */
function pl_opt_global( $mode = 'draft' ){
	$default = array( 'draft' => array(), 'live' => array() );

	$option_set = pl_opt(PL_SETTINGS, $default);

	return $option_set[ $mode ];
}

function pl_opt_update_global( $set, $mode = 'draft'){

	$default = array( 'draft' => array(), 'live' => array() );

	$option_set = pl_opt(PL_SETTINGS, $default);

	if($mode == 'draft'){
		$option_set['draft'] = wp_parse_args($set, $option_set['draft']);
	}

	pl_opt_update( PL_SETTINGS, $option_set );

}

