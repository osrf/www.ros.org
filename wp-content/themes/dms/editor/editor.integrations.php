<?php


class PageLinesIntegrationHandler {

	function __construct(){
		$this->region = new PageLinesRegions;
		$this->url = PL_PARENT_URL . '/editor';
	}


	function start_new_integration(){
		
		add_action( 'pagelines_start_footer', array( $this, 'get_integration_output') );
		ob_start( );
		
	}
	
	function get_integration_output(){
		
		global $integration_out;
		
		$integration_out = ob_get_clean();
		
		pagelines_template_area('pagelines_template', 'templates');
		
	}


}