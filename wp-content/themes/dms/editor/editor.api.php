<?php

/*
 * Storefront should fetch and organize the latest items from the PL store.
 * It extends the PageLines API class and has access to its methods.
 **/
class EditorStoreFront extends PageLinesAPI {

	function __construct(){
		$this->data_url = $this->base_url . '/v5/store.json';
		$this->username = get_pagelines_credentials( 'user' );
		$this->password = get_pagelines_credentials( 'pass' );
		$this->bootstrap();
	}

	/**
	 *
	 *  Bootstrap draft data, must load before page does.
	 *
	 *  @package PageLines DMS
	 *  @since 3.0
	 */
	function bootstrap(){
		global $pldraft;
		if( is_object( $pldraft ) && 'draft' == $pldraft->mode )
			$this->get_latest();
	}

	/**
	 *
	 *  Get all store data for json head data.
	 *  @TODO make paginated??
	 *
	 *  @package PageLines DMS
	 *  @since 3.0
	 */
	function get_latest(){

			$data = $this->get( 'store_mixed', array( $this, 'json_get' ), array( $this->data_url ), 86400 );
			if( '' == $data || empty( $data ) ) {
				// empty data, or server error, retry in 10 mins? 
				$this->put( json_encode( array() ), 'store_mixed', 900 );
				return json_encode( array() );
			}
			
			$data = $this->sort( $this->make_array( json_decode( $data ) ) );
			return $data;
	}

	/**
	 *
	 *  Unused as yet.
	 *
	 *  @package PageLines DMS
	 *  @since 3.0
	 */
	function sort( $data ){

		return $data;
	}
}

/*
 * This class handles all interaction with the PageLines APIs
 * !IMPORTANT - This class can be EXTENDED by sub classes that use the API. e.g. the store, account management, etc..
 **/
class PageLinesAPI {

	var $base_url = 'api.pagelines.com';

	/**
	 *
	 *  Write cache data
	 *
	 */
	function put( $data, $id, $timeout = 3600 ) {
		if( $data && $id )
			set_transient( sprintf( 'plapi_%s', $id ), $data, $timeout );
	}

	/**
	 *
	 *  Get cache data
	 *
	 */
	function get( $id, $callback = false, $args = array() , $timeout = 3600 ){

		$data = '';

		if( false === ( $data = get_transient( sprintf( 'plapi_%s', $id ) ) ) && $callback ) {

			$data = call_user_func_array( $callback, $args );
			if( '' != $data )
				$this->put( $data, $id, $timeout );
		}
		return $data;
	}
	/**
	 *
	 *  Delete cache data
	 *
	 */
	function del( $id ) {
		delete_transient( sprintf( 'plapi_%s', $id ) );
	}

	/**
	 *
	 *  Make sure something is an array.
	 *
	 *  @package PageLines DMS
	 *  @since 3.0
	 */
	function make_array( $data ) {

		if( is_array( $data ) )
			return $data;

		if( is_object( $data ) )
			return json_decode( json_encode( $data ), true );

		return array();
	}

	/**
	 *
	 *  Fetch remote json from API server.
	 *
	 *  @package PageLines DMS
	 *  @since 3.0
	 */
	function json_get( $url ) {
		$options = array(
			'timeout'	=>	15,
			'method'	=> 'GET',
			'prot'		=> array( 'http://' )
		);
		$f  = $this->try_api( $url, $options );
		return wp_remote_retrieve_body( $f );
	}

	/**
	 *
	 *  Get remote object with POST.
	 *
	 *  @package PageLines DMS
	 *  @since 3.0
	 */
	function try_api( $url, $args ) {

		$defaults = array(
			'sslverify'	=>	false,
			'timeout'	=>	5,
			'body'		=> array(),
			'method'	=> 'POST',
			'prot'		=> array( 'https://', 'http://' )
		);
		$options = wp_parse_args( $args, $defaults );
		$command = sprintf( 'wp_remote_%s', $options['method'] );

		if( 'get' == $options['method'] )
			$options = array();

		foreach( $options['prot'] as $type ) {
			// sometimes wamp does not have curl!
			if ( $type === 'https://' && ! function_exists( 'curl_init' ) )
				continue;
			$r = $command( $type . $url, $options );
			if ( !is_wp_error($r) && is_array( $r ) ) {
				return $r;
			}
		}
		return false;
	}

	/**
	 *  Convert objects into arrays
	 *
	 *	@since 3.0
	 */
	public function object_to_array( $data ) {

		if ( is_array( $data ) || is_object( $data ) ) {
			$result = array();
			foreach ( $data as $key => $value ) {
				$result[$key] = self::object_to_array( $value );
			}
		return $result;
		}
	return $data;
	}

	/**
	 *  Search keys in an array.
	 *
	 *	@since 3.0
	 */
	public function preg_grep_keys( $pattern, $input, $flags = 0 ) {
		$keys = preg_grep( $pattern, array_keys( $input ), $flags );
		$vals = array();
		foreach ( $keys as $key ) {
			$vals[$key] = $input[$key];
		}
	return $vals;
	}

	/**
	 *  Fuzzy search keys in an array.
	 *
	 *	@since 3.0
	 */
	function api_search( $string, $data) {

		$data = $this->object_to_array( $data );

		$search = $this->preg_grep_keys( "/{$string}/i", $data );
		return array(
			'results'	=> count( $search),
			'data'		=> $search
			);
	}
}

// API wrapper functions.

/**
 *  Get data from cache.
 *
 *	@since 3.0
 */
function pl_cache_get( $id, $callback = false, $args = array(), $timeout = 3600 ) {
	global $storeapi;
	if( ! is_object( $storeapi ) )
		$storeapi = new EditorStoreFront;

	if( is_object( $storeapi ) )
		return $storeapi->get( $id, $callback, $args, $timeout );
	else
		return false;
}

/**
 *  Write data to cache.
 *
 *	@since 3.0
 */
function pl_cache_put( $data, $id, $time = 3600 ) {
	global $storeapi;
	if( ! is_object( $storeapi ) )
		$storeapi = new EditorStoreFront;
	if( $id && $data && is_object( $storeapi ) )
		$storeapi->put( $data, $id, $time );
}

/**
 *  Delete from cache.
 *
 *	@since 3.0
 */
function pl_cache_del( $id ) {
	delete_transient( sprintf( 'plapi_%s', $id ) );
}

/**
 *  Clear draft caches.
 *
 *	@since 3.0
 */
function pl_flush_draft_caches( $file = false ) {

	$caches = array( 'draft_core_raw', 'draft_core_compiled', 'draft_sections_compiled' );
	foreach( $caches as $key ) {
		pl_cache_del( $key );
	}
	if( false == $file )
		$file = sprintf( '%s%s', trailingslashit( pl_get_css_dir( 'path' ) ), 'editor-draft.css' ); 
	if( is_file( $file ) )
		unlink( $file );
}

/**
 *  Search the store.
 *
 *	@since 3.0
 */
function pl_store_search( $string ) {
	global $storeapi;
	if( ! is_object( $storeapi ) )
		$storeapi = new EditorStoreFront;

	return $storeapi->api_search( $string, $storeapi->get_latest() );
}