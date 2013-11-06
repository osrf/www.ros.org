<?php

class Sections_Cache {
	
	function __construct() {
		add_filter( 'pagelines_render_section', array( $this, 'section_cache_init' ), 10, 2 );
	}

	function section_cache_init( $s, $class ) {
	
		global $post;
		if( is_user_logged_in() || 'pl_area' == $s->id || 'plcolumn' == $s->id || defined( 'PL_WPORG' ) || ! isset( $post->ID )) {
			ob_start();
			$class->section_template_load( $s );
			return ob_get_clean();
		}
	return $this->section_cache( $s, 3600, $class );
	}

	function section_cache( $s, $ttl = 3600, $class ) {
	
		global $post;
		$cache_key = pl_get_cache_key();
		$id = $s->meta['clone'];
		$name = $s->id;

		$key = sprintf( 'section_cache_%s_%s_%s', $cache_key, $id, $post->ID );
	
		// do cache...
		$output = get_transient( $key );

		if( '' != $output  ) {
			echo "<!-- section cache hit -->\n";
			return $output;
		} 

		echo "<!-- sections cache miss -->\n";
		ob_start();
		$class->section_template_load( $s );
		$output = ob_get_clean();
		set_transient( $key, $output, $ttl );
		return $output;
	}
	
	static function cache_desc() {
		return sprintf( '<p>This simple cache uses wp_transients to store rendered sections HTML saving a few db queries.<br />If you are using a PHP OP Cache like APC/Memcached this can make quite a difference.<br .><kbd>DISCLOSURE: THIS IS NOT GOING TO BE A MAGIC FIX FOR SERVERS MADE FROM CHEESE</kbd><br /><strong>This will only work with DMS 1.0.4 and above.</strong><br />%s</p>', self::cache_stats() );
	}
	
	static function cache_stats() {
		if ( '1' == wpsf_get_setting( wpsf_get_option_group( '../settings/settings-general.php' ), 'section_cache', 'cache-enabled' ) ) {
			global $wpdb;
			
			
			$stale = 0;

			$where = "option_name LIKE '\_transient_section_cache%'"; 
			
			$transients = $wpdb->get_col( "SELECT option_name FROM $wpdb->options WHERE $where" );
			
			$count = count( $transients );
			
			$cache_key = self::pl_get_cache_key();
			
			foreach( $transients as $k => $name ) {
				$key = str_replace( '_transient_section_cache_', '', $name );
				$key = explode( '_', $key );
				if( $key[0] <> $cache_key ) {
					$stale++;
					delete_transient( str_replace( '_transient_', '', $name ) );
				}
			}
			
			$out = sprintf( '<br /><strong>%s</strong> total cache sections found.<br /><strong>%s</strong> stale sections were detected and deleted from the db.', $count, $stale );
			return $out;
		}
	}
	static function pl_get_cache_key() {

		if ( '' != get_theme_mod( 'pl_cache_key' ) ) {
			return get_theme_mod( 'pl_cache_key' );
		} else { 	
			$cache_key = substr(uniqid(), -6);
			set_theme_mod( 'pl_cache_key', $cache_key );
			return $cache_key;
		}
	}
}
