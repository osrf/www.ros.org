<?php
class DMS_Hacks {

	function __construct() {
		
		add_action( 'wp_before_admin_bar_render', array( $this, 'show_template' ) );
		add_action( 'dmspro_extra_settings', array( $this, 'hacks_included' ) );
		add_filter('posts_where', array( $this, 'advanced_search_query' ) );
		
	//	add_action( 'after_setup_theme', array( $this, 'maybe_load_scripts' ) );		
	}

	function validator_init( $s, $class ) {
		
		add_action( 'pagelines_outer_' . $s->id, array( $this, 'hidden_h' ) );
		ob_start();
		$class->section_template_load( $s );
		return ob_get_clean();
	}
	
	function hidden_h() {
		echo '<h6 class="hidden">&nbsp;</h6>';
	}

	function maybe_load_scripts() {
		
		if( ! has_filter( 'pagelines_render_section' ) )
			add_filter( 'pagelines_render_section', array( $this, 'validator_init' ), 999, 2 );		
	}

	function hacks_included() {

		ob_start();
		?>
		<h2>Extra Hacks enabled by this plugin</h2>
		<ul>
		<li><kbd>Template: Feature</kbd> Show last loaded template name in WP adminbar.</li>
		<li><kbd>Sections: HTML5</kbd> Hidden H6 inserted to validate pages.</li>
		</ul>

		<?php
		echo ob_get_clean();
	}
	function show_template() {

		if( is_admin() )
			return false;

		global $wp_admin_bar, $pldraft, $plpg, $pl_custom_template, $post;
			
		
		
		if( version_compare( CORE_VERSION, '2.1', '<' ) ) {

			if( ! is_object( $pldraft ) || 'live' == $pldraft->mode || is_admin() )
				return;

			if( class_exists( 'PLDeveloperTools' ) )
				$template = ( isset( $pl_custom_template['name'] ) ) ? $pl_custom_template['name'] : 'None';
			else
				$template = ( false != $plpg->template && '' != $plpg->template ) ? $plpg->template : 'None';


			$wp_admin_bar->add_menu( array(
				'parent' => false,
				'id' => 'page_template',
				'title' => sprintf( 'Template: %s',  $template ),
				'href'	=> sprintf( '%s?tablink=page-setup', site_url() ),
				'meta'	=> false
			));
		} else {

			if( ! class_exists( 'PageLinesPage' ) )
				return false;

			$page_handler = new PageLinesPage;
			if( $page_handler->is_special() )
				$id = $page_handler->special_index_lookup();
			else
				$id = $post->ID;

			$mode = get_post_meta( $id, 'pl_template_mode', true );

			if( ! $mode )
				$mode = 'type';

			if( 'local' != $mode )
				$id = $page_handler->special_index_lookup();

			$type_name = $page_handler->type_name;

			$set = pl_meta( $id, PL_SETTINGS );
			$template = ( is_array( $set ) && isset( $set['live']['custom-map']['template']['ctemplate'] ) ) ? $set['live']['custom-map']['template']['ctemplate'] : 'Default';

			$template = strlen( $template ) > 15
			? substr( $template,0,15 ) . '...'
			: $template;

			$meta = sprintf( 'Mode: %s | Current Type: %s | Template: %s', ucwords( $mode ), $type_name, ucwords( $template ) );

			$wp_admin_bar->add_menu( array(
				'parent' => false,
				'id' => 'page_template',
				'title' => $meta,
				'href'	=> false,
				'meta'	=> false
			));
		}
	}

	function advanced_search_query( $where ) {

		if( ! defined( 'CORE_VERSION' ) || version_compare( CORE_VERSION, '2.1', '<' ) ) {
			return $where;
		}

		if( is_search() && '1' === wpsf_get_setting( wpsf_get_option_group( '../settings/settings-general.php' ), 'search', 'enabled' )) {

	    global $wpdb;
	    $query = get_search_query();
	    $query = like_escape( $query );

		$result = $this->search_sections( $query );		
		$where .=" OR {$wpdb->posts}.ID IN ( '$result' )";
		
		}
	    return $where;
	}
	
	
	function search_sections( $term ) {
		
		$result = $this->search_sections_query( $term );	
		return implode( ',', $result );
	}
	
	function search_sections_query( $term ) {

		global $wpdb;
		$query = sprintf( "SELECT post_id from %spostmeta where meta_key = 'pl-settings' and meta_value LIKE concat( '%%', ( SELECT uid FROM %spl_data_sections WHERE live LIKE '%s'  order by id DESC ), '%%')", $wpdb->prefix, $wpdb->prefix, '%' . $term . '%' );

		$result = $wpdb->get_results( $query );
	
		$results = array();

		foreach( $result as $k => $id ) {			
			if( $id->post_id < 70000000 )
				$results[] = $id->post_id;
		}		
		return $results;
	}
}
