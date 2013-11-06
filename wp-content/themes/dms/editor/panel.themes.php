<?php



class EditorThemeHandler {

	var $preview_slug = 'pl-theme-preview';

	function __construct(  ){

		add_action('pagelines_editor_scripts', array( $this, 'scripts'));
		add_filter('pl_toolbar_config', array( $this, 'toolbar'));

		$this->url = PL_PARENT_URL . '/editor';
	}

	function scripts(){
		wp_enqueue_script( 'pl-js-themes', $this->url . '/js/pl.themes.js', array( 'jquery' ), PL_CORE_VERSION, true );
	}
	
	function toolbar( $toolbar ){
		$toolbar['theme'] = array(
			'name'	=> __( 'Theme', 'pagelines' ),
			'icon'	=> 'icon-picture',
			'pos'	=> 40,
			'panel'	=> $this->pl_get_settings()

		);
		
		return apply_filters('pl_themes_tabs_final', $toolbar);
	}

	function pl_get_settings(){
		
		$settings = array(
			
			'avail_themes'	=> array(
				'pos'	=> 30,
				'name'	=> __( 'Your Themes', 'pagelines' ),
				'call'	=> array( $this, 'themes_dashboard'),
				'icon'	=> 'icon-picture',
				'filter'=> '*'
			),
			'export_themes'	=> array(
				'pos'	=> 50,
				'name'	=> __( 'Import Config', 'pagelines' ),
				'tab'	=> 'settings',
				'stab'	=> 'importexport',	
				'icon'	=> 'icon-th-large'
			),
			'more_themes'	=> array(
				'pos'	=> 120,
				'name'	=> __( 'Get More Themes', 'pagelines' ),
				'flag'	=> 'link-storefront',
				'icon'	=> 'icon-download'
			)
		); 
		
		$settings = $this->user_theme_tabs( $settings );
		
		$default = array(
			'icon'		=> 'icon-edit',
			'pos'		=> 100,
			'filter'	=> '*'
		);
		
		foreach($settings as $key => &$info){
			$info = wp_parse_args( $info, $default );
		}
		unset($info);

		uasort($settings, array( $this, "cmp_by_position") );
	
		$settings = array_merge( array( 'heading' => __( 'Theme Options', 'pagelines' ) ), $settings );
	
		return $settings;
	}
	
	function user_theme_tabs($settings){
		global $pl_user_theme_tabs; 
		
		if( isset( $pl_user_theme_tabs ) && !empty( $pl_user_theme_tabs ) && is_array( $pl_user_theme_tabs ) )
			$settings = array_merge($pl_user_theme_tabs, $settings); 
			
		return $settings;
		
	}


	function cmp_by_position($a, $b) {

		if( isset( $a['pos'] ) && is_int( $a['pos'] ) && isset( $b['pos'] ) && is_int( $b['pos'] ) )
			return $a['pos'] - $b['pos'];
		else
			return 0;
	}
	
	

	function themes_dashboard(){
		$this->xlist = new EditorXList;

		$args = array();

		if( is_multisite() ) {
			global $blog_id;
			$args['allowed'] = 'network';
			$args['blog_id'] = $blog_id;
		}

		$themes = wp_get_themes( $args );

		$active_theme = wp_get_theme();

		$list = '';
		$count = 1;
		if(is_array($themes)){

			foreach($themes as $theme => $t){
				$class = array();

				if($t->get_template() != 'dms')
					continue;

				if($active_theme->stylesheet == $t->get_stylesheet()){
					$class[] = 'active-theme';
					$active = ' <span class="badge badge-info"><i class="icon-ok"></i> Active</span>';
					$number = 0;
				}else {
					$active = '';
					$number = $count++;
				}

				if( is_file( sprintf( '%s/splash.png', $t->get_stylesheet_directory() ) ) )
				 	$splash = sprintf( '%s/splash.png', $t->get_stylesheet_directory_uri()  );
				else
					$splash = $t->get_stylesheet();

				$class[] = 'x-item-size-10';

				$args = array(
					'id'			=> $theme,
					'class_array' 	=> $class,
					'data_array'	=> array(
						'number' 		=> $number,
						'stylesheet'	=> $t->get_stylesheet()
					),
					'thumb'			=> $t->get_screenshot( ),
					'splash'		=> $t->get_screenshot( ),
					'name'			=> $t->name . $active
				);

				$list .= $this->xlist->get_x_list_item( $args );


			}

		}


		printf('<div class="x-list x-themes" data-panel="x-themes">%s</div>', $list);
	}

	// AJAX ACTIONS

	function activate( $response ){

		$new = $response['post']['stylesheet'];

		$theme = wp_get_theme( $new );

		if ( !$new || !$theme->exists() || !$theme->is_allowed() ){
			$response['error'] = __( 'Theme does not exist or is not allowed', 'pagelines' );
			return $response;
		}


		switch_theme( $theme->get_stylesheet() );

		$response['success'] = 'Theme Switched!';
		$response['new'] = $new;

		return $response;
	}

	function set_preview(){

		$new = $response['post']['stylesheet'];

		$theme = wp_get_theme( $new );

		if ( !$new || !$theme->exists() || !$theme->is_allowed() ){
			$response['error'] = __( 'Theme does not exist or is not allowed', 'pagelines' );
			return $response;
		} else {
			
//			pl_update_setting($this->preview_slug, $new);

			return $response;

		}

	}

	function maybe_load_preview( $active_stylesheet ){

		$preview_theme = $this->determine_theme( $active_stylesheet );

		if ( $preview_theme ){

			$preview_theme_object = wp_get_theme( $preview_theme );

			add_action('before_toolbox_panel', array( $this, 'add_preview_banner'));

			return $preview_theme_object->get_stylesheet();

		} else
			return $active_stylesheet;



	}

	function determine_theme( $active_stylesheet ){
		$preview_stylesheet = pl_setting( $this->preview_slug );

		if( $preview_stylesheet && $preview_stylesheet != $active_stylesheet )
			return $preview_stylesheet;
		else
			return false;
	}

	function add_preview_banner(){

		echo __( ' this is the end of the world.... >> ', 'pagelines' );
	}


}