<?php



class PageLinesSave {
	
	
	function __construct(){
		
		
		add_filter( 'pl_ajax_fast_save', array( $this, 'fast_save' ), 10, 2 );
		
	}
	
	
	function fast_save( $response, $data ){
		
		if( $data['run'] == 'map' ){
			$response = $this->save_map( $response, $data );
		} elseif (  $data['run'] == 'form' ){
			$response = $this->save_form( $response, $data );
		} elseif (  $data['run'] == 'publish' ){
			$response = $this->publish( $response, $data );
		} else 
			$response['error'] = "No save operation set for ".$data['run'];

		$response['state'] = $this->get_state( $data );
		
		return $response;
		
	}
	
	function get_state( $data ){
		
		$state = array();
		$settings = array();
		$default = array('live'=> array(), 'draft' => array());

		$pageID = $data['pageID'];
		$typeID = $data['typeID'];

		// Local
		$settings['local'] = pl_meta( $pageID, PL_SETTINGS );

		if($typeID != $pageID)
			$settings['type'] = pl_meta( $typeID, PL_SETTINGS );

		$settings['global'] = pl_opt( PL_SETTINGS );

		foreach( $settings as $scope => $set ){

			$set = wp_parse_args($set, $default);

			$scope = str_replace('map-', '', $scope);

			if( $set['draft'] != $set['live'] ){
				$state[$scope] = $scope;
			}

		}

		if( count($state) > 1 )
			$state[] = 'multi';

		return $state;
		
	}
	
	function publish( $response, $data ){
		
		$pageID = $data['pageID'];
		$typeID = $data['typeID'];
		
		$settings = array();

		$settings['local'] = pl_meta( $pageID, PL_SETTINGS );
		$settings['type'] = pl_meta( $typeID, PL_SETTINGS );
		$settings['global'] = pl_opt( PL_SETTINGS  );

		foreach($settings as $scope => $set){

			$set = wp_parse_args($set, array('live'=> array(), 'draft' => array()));

			$set['live'] = $set['draft'];

			$settings[ $scope ] = $set;

		}


		set_theme_mod( 'pl_cache_key', substr(uniqid(), -6) );

		pl_meta_update( $pageID, PL_SETTINGS, $settings['local'] );
		pl_meta_update( $typeID, PL_SETTINGS, $settings['type'] );
		pl_opt_update( PL_SETTINGS, $settings['global'] );


		// Flush less
		do_action( 'extend_flush' );
		
		
		return $response;
	}
	
	/* 
	 * Saves only Map Data based on template mode (local or type)
	 */ 
	function save_map( $response, $data ){
	
		$global_map = array(
			'header' => $data['store']['header'],
			'footer' => $data['store']['footer'],
		);
		
		$local_map = array(
			'template' => $data['store']['template']
		);
		
		$template_mode = $data['templateMode']; 
		
		$metaID = ( $template_mode == 'type' ) ? $data['typeID'] : $data['pageID'];
		
		
		$global_settings = pl_settings();
		$global_settings['regions'] = $global_map;
		pl_settings_update( $global_settings );
		
		$local_settings = pl_settings( 'draft', $metaID );
		$local_settings['custom-map'] = $local_map;
		pl_settings_update( $local_settings, 'draft', $metaID );
		
	
		return $response;
	}
	
	function save_form( $response, $data ){
		
		$form = $data['store'];
		$scope = $data['scope'];
		
		if( $scope == 'global' ){
			
			$global_settings = pl_settings();
			
			// First parse sub settings field
			if( isset($form['settings']) )
				$form['settings']  = wp_parse_args( $form['settings'], $global_settings['settings'] );
			
			$response['form'] = $form;
			$global_settings = wp_parse_args( $form, $global_settings );
			pl_settings_update( $global_settings );
			
		} elseif( $scope == 'type' || $scope == 'local' ){
			
			$metaID = ( $scope == 'type' ) ? $data['typeID'] : $data['pageID'];
			
			$meta_settings = pl_settings( 'draft', $metaID );
			$meta_settings = wp_parse_args( $form, $meta_settings );
			pl_settings_update( $meta_settings, 'draft', $metaID );
			
		}
		
		return $response;
		
	}
}