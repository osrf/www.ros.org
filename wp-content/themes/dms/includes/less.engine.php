<?php




class PageLinesLESSEngine {
	
	// Might need this later.
	// $start_time = microtime(true);
	// $end_time = microtime(true);
	// $the_time = round( ($end_time - $start_time), 5 );
	
	function __construct(){
		
		if( pl_less_dev() ){
			
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_footer', array( $this, 'render_less_components' ) );
			add_action( 'pagelines_head', array( $this, 'render_css_container' ) );
			
			add_action( 'pl_ajax_save_css', array( $this, 'save_site_css' ), 10, 2);
			
		}
		
	}
	
	function save_site_css( $response, $data ){
		
		$css = $data['store'];

		$global_settings = pl_settings();
		$global_settings['settings']['site_css'] = $css;
		pl_settings_update( $global_settings );
		
		
		return $response;
	}
	
	function enqueue_scripts(){
		wp_enqueue_script( 'pl-less-parser', PL_JS . '/utils.less.js', array( 'jquery' ), PL_CORE_VERSION, true );
		wp_enqueue_script( 'pl-less-handler', PL_JS . '/pl.less.js', array( 'jquery', 'pl-less-parser' ), PL_CORE_VERSION, true );
	}
	
	function render_css_container(){
	
		echo inline_css_markup( 'pagelines-draft-css', pl_setting('site_css') );
	}
	
	function render_less_components( ){
		
		$components = $this->less_components();

		foreach( $components as $comp => $less ){
		 	printf('%s<script id="pl-less-%s" type="text/plain" style="display: none;" >%s</script>', "\n", $comp, $less ); 
		}
		
	}
	
	function less_components(){
		
		
		
		$var_array = $this->core_less_vars();
		
		$tool_files = $this->less_tool_files();
		
		$core_files = get_core_lessfiles();
		
		
		$less = array(); 
		
		$less['vars'] = $this->lessify_vars( $var_array ); 
		
		$less['tools'] = $this->load_less_from_file_array( $tool_files );
		
		$less['core'] = $this->load_less_from_file_array( $core_files );
		
		$less['sections'] = get_all_active_sections();
		
		return apply_filters( 'pl_less_components', $less ); 
		
	}
	
	function load_less_from_file_array( $file_array ){
		
		$less = '';
		
		foreach( $file_array as $less_file ) {
			$less .= load_less_file( $less_file );
		}
		
		return $less;
		
	}
	
	/**
	 *  Load from .less files.
	 *  @uses  load_less_file
	 */
	function load_core_cssfiles( $files ) {

		$code = '';
		
		foreach( $files as $less ) {
			$code .= load_less_file( $less );
		}
		
		return apply_filters( 'pagelines_insert_core_less', $code );
	}
	
	function less_tool_files( ) {	
		
		return array( 'variables', 'colors', 'mixins' );

	}
	
	
	/* 
	 * Grab the core variables, filtered.
	 */ 
	function core_less_vars(){

		$constants = array(
			'plRoot'				=> sprintf( "\"%s\"", PL_PARENT_URL ),
			'plCrossRoot'			=> sprintf( "\"//%s\"", str_replace( array( 'http://','https://' ), '', PL_PARENT_URL ) ),
			'plSectionsRoot'		=> sprintf( "\"%s\"", PL_SECTION_ROOT ),
			'plPluginsRoot'			=> sprintf( "\"%s\"", WP_PLUGIN_URL ),
			'plChildRoot'			=> sprintf( "\"%s\"", PL_CHILD_URL ),
			'plExtendRoot'			=> sprintf( "\"%s\"", PL_EXTEND_URL ),
			'plPluginsRoot'			=> sprintf( "\"%s\"", plugins_url() ),
		);

		global $less_vars;
		
		if( isset( $less_vars ) && is_array( $less_vars ) )
			$constants = array_merge( $less_vars, $constants );


		return apply_filters( 'pless_vars', $constants );
	}
	
	/* 
	 * Convert variable array into LESS syntax
	 */
	function lessify_vars( $var_array ) {

		$less_vars = '';

		foreach( $var_array as $key => $value)
			$less_vars .= sprintf('@%s:%s;%s', $key, $value, "\n");

		return $less_vars;
	}
	

	
}
