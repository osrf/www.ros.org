<?php
class OTW_Component{

	/**
	 * Component url
	 * 
	 * @var  string 
	 */
	public $component_url;

	/**
	 * Component path
	 * 
	 * @var  string 
	 */
	public $component_path;
	
	/**
	 * Labels
	 * 
	 * @var  array
	 */
	public $labels = array();
	
	/**
	 * Errors
	 * 
	 * @var  array
	 */
	public $errors = array();
	
	/**
	 * has errors
	 * 
	 * @var  boolen
	 */
	public $has_error = false;
	
	/**
	 * mode
	 * 
	 * @var  string
	 */
	public $mode = 'production';
	
	
	/**
	 * js version
	 */
	public $js_version = '1.13';
	
	/**
	 * css version
	 */
	public $css_version = '1.13';
	
	/**
	 *  Set settings
	 */
	public function add_settings( $settings ){
		
		$this->component_url = $settings['url'];
		
		$this->component_path = $settings['path'];
	}
	
	/**
	 *  Get Label
	 */
	public function get_label( $label_key ){
		
		if( isset( $this->labels[ $label_key ] ) ){
		
			return $this->labels[ $label_key ];
		}
		
		if( $this->mode == 'dev' ){
			return strtoupper( $label_key );
		}
		
		return $label_key;
	}
	
	/**
	 *  add error
	 */
	public function add_error( $error_string ){
		
		$this->errors[] = $error_string;
		$this->has_error = true;
	}
	
	/**
	 * Replace WP autop formatting
	 */
	public function otw_shortcode_remove_wpautop($content){
		
		$content = do_shortcode( shortcode_unautop( $content ) );
		$content = preg_replace( '#^<\/p>|^<br \/>|<p>$#', '', $content);
		return $content;
	}
}
?>