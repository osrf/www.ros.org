<?php



class PageLinesSettingsPanel{

	function __construct(){

		add_filter('pl_toolbar_config', array( $this, 'toolbar'));
		add_action('pagelines_editor_scripts', array( $this, 'scripts'));

		$this->url = PL_PARENT_URL . '/editor';
	}

	function scripts(){
		// Colorpicker
		wp_enqueue_script( 'css3colorpicker', PL_JS . '/colorpicker/colorpicker.js', array('jquery'), '1.3.1', true );

		// Image Uploader
		wp_enqueue_script( 'fineupload', PL_JS . '/fineuploader/jquery.fineuploader-3.2.min.js', array('jquery'), PL_CORE_VERSION, true );

	}

	function toolbar( $toolbar ){

		$toolbar[ 'settings' ] = array(
			'name'	=> __( 'Settings', 'pagelines' ),
			'icon'	=> 'icon-globe',
			'pos'	=> 35,
			'panel'	=> $this->get_settings_tabs()
		);

		$toolbar[ 'section-options' ] = array(
			'name'	=> __( 'Section Options', 'pagelines' ),
			'icon'	=> 'icon-paste',
			'type'	=> 'hidden',
			'flag'	=> 'section-opts',
			'pos'	=> 1000,
			'panel'	=> $this->section_options_panel()
		);

		return $toolbar;
	}

	function get_settings_tabs( $panel = 'site' ){

		$settings_object = new EditorSettings;

		$tabs = array();

		$tabs['heading'] = __( 'Global Settings', 'pagelines' );

		foreach( $settings_object->get_set('site') as $tabkey => $tab ){

			$tabs[ $tabkey ] = array(
				'key' 	=> $tabkey,
				'name' 	=> $tab['name'],
				'icon'	=> isset($tab['icon']) ? $tab['icon'] : ''
			);
		}


		return $tabs;

	}

	function section_options_panel(){
		global $plpg;

		$current_page = ($plpg->is_special()) ? $plpg->type_name : $plpg->id;

		$tabs = array();
		$tabs['heading'] = __( "Section Options", 'pagelines' );

		$basic_scope = (is_page() || $plpg->is_special()) ? 'local' : 'type';
		
		$array = array('global', 'type', 'local'); 
		
		foreach($array as $scope){
			$tabs[ $scope ] = array( 
				'name'	=> sprintf( '%s <span class="label">%s</span>', __( 'Section Options', 'pagelines' ), $scope ), 
				'scope' => $scope
			);
		}
		

		return $tabs;

	}


}