<?php

/**
 * PageLines Sections Registration Class
 *
 * An object that is used to scan and load sections, then add them to the factory object.
 *
 * @author 		PageLines
 * @category 	Core
 * @package 	DMS
 * @version     1.0.0
 */

class PLSectionsRegister {
	
	function __construct( ){}

	// list plugins filter out non PageLines
	function get_all_plugins() {

		$default_headers = array(
			'External'		=> 'External',
			'Demo'			=> 'Demo',
			'tags'			=> 'Tags',
			'version'		=> 'Version',
			'author'		=> 'Author',
			'authoruri'		=> 'Author URI',
			'description'	=> 'Description',
			'classname'		=> 'Class Name',
			'depends'		=> 'Depends',
			'workswith'		=> 'workswith',
			'isolate'		=> 'isolate',
			'edition'		=> 'edition',
			'cloning'		=> 'cloning',
			'failswith'		=> 'failswith',
			'tax'			=> 'tax',
			'persistant'	=> 'Persistant',
			'format'		=> 'Format',
			'classes'		=> 'Classes',
			'filter'		=> 'Filter',
			'loading'		=> 'Loading',
			'PageLines'		=> 'PageLines',
			'Section'		=> 'Section',
			'Plugin Name'	=> 'Plugin Name',
		);

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
		$installed_plugins = get_plugins();
		
		$pl_plugins = array();
		foreach( $installed_plugins as $path => $plugin ) {
					
			if ( ! is_plugin_active( $path ) )
				continue;

			$fullpath = sprintf( '%s%s', trailingslashit( WP_PLUGIN_DIR ), $path );
			
			$data = get_file_data( $fullpath, $default_headers );
			
			if( ! $data['PageLines'] || ! $data['Section'] )
				unset( $installed_plugins[$path] );
			else {
				
				$base_dir = dirname( $fullpath );
				$base_url = untrailingslashit( plugins_url( '', $path ) );
				
				$section_paths = array(
						'class'			=> $data['classname'],
						'type'			=> 'editor',
						'tags'			=> $data['tags'],
						'author'		=> $data['author'],
						'version'		=> $data['version'],
						'authoruri'		=> ( isset( $data['authoruri'] ) ) ? $data['authoruri'] : '',
						'description'	=> $data['description'],
						'name'			=> $data['Plugin Name'],
						'base_url'		=> $base_url,
						'base_dir'		=> $base_dir,
						'base_file'		=> $fullpath,
						'workswith'		=> ( $data['workswith'] ) ? array_map( 'trim', explode( ',', $data['workswith'] ) ) : '',
						'isolate'		=> ( $data['isolate'] ) ? array_map( 'trim', explode( ',', $data['isolate'] ) ) : '',
						'edition'		=> $data['edition'],
						'cloning'		=> ( 'true' === $data['cloning'] ) ? true : '',
						'failswith'		=> ( $data['failswith'] ) ? array_map( 'trim', explode( ',', $data['failswith'] ) ) : '',
						'tax'			=> $data['tax'],
						'demo'			=> $data['Demo'],
						'external'		=> $data['External'],
						'persistant'	=> $data['persistant'],
						'format'		=> $data['format'],
						'classes'		=> $data['classes'],
						'screenshot'	=> ( is_file( $base_dir . '/thumb.png' ) ) ? $base_url . '/thumb.png' : '',
						'splash'		=> ( is_file( $base_dir . '/splash.png' ) ) ? $base_url . '/splash.png' : '',
						'less'			=> ( is_file( $base_dir . '/style.less' ) ) ? true : false,
						'loadme'		=> true,
						'price'			=> '',
						'purchased'		=> true,
						'uid'			=> '',
						'filter'		=> $data['filter'],
						'loading'		=> $data['loading']
				);
							
				$data = wp_parse_args( $section_paths, $data );
				$pl_plugins[$data['classname']] = $data;		
			}				
		}
		return $pl_plugins;
	}
}


