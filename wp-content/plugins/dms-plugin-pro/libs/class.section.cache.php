<?php

class Sections_Cache {
	
	function __construct() {
		add_filter( 'pagelines_render_section', array( $this, 'section_cache_init' ), 10, 2 );
	}


	
	function section_cache_init( $s, $class ) {
	
		global $post;
		
		$nocache = apply_filters( 'pl_section_cache_nocache', array() );
		if( is_user_logged_in() || 'navbar' == $s->id || 'navi' == $s->id || 'pl_area' == $s->id || 'plcolumn' == $s->id || defined( 'PL_WPORG' ) || ! isset( $post->ID ) || in_array( $s->id, $nocache ) ) {
			ob_start();
			$class->section_template_load( $s );
			return ob_get_clean();
		}
	return $this->section_cache( $s, 3600, $class );
	}

	function section_cache( $s, $ttl = 3600, $class ) {

		$id = $s->meta['clone'];
		$name = $s->id;

		$key = sprintf( 'section_cache_%s', $id );

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
		set_transient( $key, $output, 3600 );
		return $output;
	}
	
	static function cache_desc() {
		return sprintf( 'By clicking this I understand it is experimental and not really supported.<br /><p>This simple <strong style="color:red">EXPERIMENTAL</strong> cache uses wp_transients to store rendered sections HTML saving a few db queries.<br />If you are using a PHP OP Cache like APC/Memcached this can make quite a difference.<br .><kbd><strong style="color:red">!!DISCLOSURE!!</strong> THIS IS NOT GOING TO BE A MAGIC FIX FOR SERVERS MADE FROM CHEESE</kbd><br /><strong>This will only work with DMS 1.0.4 and above.</strong><br />%s</p>', self::cache_stats() );
	}
	
	static function cache_stats() {
		
		if( ! is_admin() )
			return;

		if ( '1' == wpsf_get_setting( wpsf_get_option_group( '../settings/settings-general.php' ), 'section_cache', 'cache-enabled' ) ) {
			global $wpdb;
			
			$threshold = time() - 60;
			
			// count transient expiration records, total and expired
			$sql = "select count(*) as `total`, count(case when option_value < '$threshold' then 1 end) as `expired`
					from {$wpdb->options}
					where (option_name like '\_transient\_timeout\_section\_cache\_%')";
					$counts = $wpdb->get_row($sql);
			
			$query = $wpdb->get_row($sql);
			$expired = $query->expired;
			$count = $query->total;

			// delete expired transients, using the paired timeout record to find them
					$sql = "
						delete from t1, t2
						using {$wpdb->options} t1
						join {$wpdb->options} t2 on t2.option_name = replace(t1.option_name, '_timeout', '')
						where (t1.option_name like '\_transient\_timeout\_section\_cache%')
						and t1.option_value < '$threshold';
					";
					$wpdb->query($sql);
			
			$out = sprintf( '<br /><strong>%s</strong> total cached sections found.<br /><strong>%s</strong> stale sections were detected and deleted from the db.', $count, $expired );
			return $out;
		}
	}
}

	function dms_pro_cache_delete_all() {

			global $wpdb;

			// delete all transients
			// including NextGEN Gallery 2.0.x display cache
			$sql = "
				delete from {$wpdb->options}
				where option_name like '\_transient\_section\_cache\_%'
				or option_name like '\_transient\_timeout\_section\_cache\_%'
			";
			$wpdb->query($sql);
	}
