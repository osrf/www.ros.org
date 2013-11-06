<?php



class PageLinesSectionsPanel{

	function __construct(){

		add_filter('pl_toolbar_config', array( $this, 'toolbar'));
		add_action('pagelines_editor_scripts', array( $this, 'scripts'));

		$this->url = PL_PARENT_URL . '/editor';
	}

	function scripts(){
		wp_enqueue_script( 'pl-js-sections', $this->url . '/js/pl.sections.js', array( 'jquery' ), PL_CORE_VERSION, true );
	}

	function toolbar( $toolbar ){
		$toolbar['add-new'] = array(
			'name'	=> __( 'Add To Page', 'pagelines' ),
			'icon'	=> 'icon-plus-sign',
			'pos'	=> 20,
			'panel'	=> array(
				'heading'	=> __( "<i class='icon-random'></i> Drag to Add", 'pagelines' ),
				'add_section'	=> array(
					'name'	=> __( 'Your Sections', 'pagelines' ),
					'icon'	=> 'icon-random',
					'clip'	=> __( 'Drag on to page to add', 'pagelines' ),
					'tools'	=> sprintf( '<button class="btn btn-mini btn-reload-sections"><i class="icon-repeat"></i> %s</button>', __( 'Reload Sections', 'pagelines' ) ),
					'type'	=> 'call',
					'call'	=> array( $this, 'add_new_callback'),
					'filter'=> '*'
				),
				'more_sections'	=> array(
					'name'	=> __( 'Get More Sections', 'pagelines' ),
					'icon'	=> 'icon-download',
					'flag'	=> 'link-storefront'
				),
				'heading2'	=> __( "<i class='icon-filter'></i> Filters", 'pagelines' ),
				'components'		=> array(
					'name'	=> __( 'Components', 'pagelines' ),
					'href'	=> '#add_section',
					'filter'=> '.component',
					'icon'	=> 'icon-circle-blank'
				),
				'layouts'		=> array(
					'name'	=> __( 'Layouts', 'pagelines' ),
					'href'	=> '#add_section',
					'filter'=> '.layout',
					'icon'	=> 'icon-columns'
				),
				'full-width'	=> array(
					'name'	=> __( 'Full Width', 'pagelines' ),
					'href'	=> '#add_section',
					'filter'=> '.full-width',
					'icon'	=> 'icon-resize-horizontal'
				),
				'formats'		=> array(
					'name'	=> __( 'Post Layouts', 'pagelines' ),
					'href'	=> '#add_section',
					'filter'=> '.format',
					'icon'	=> 'icon-th'
				),
				'galleries'		=> array(
					'name'	=> __( 'Galleries', 'pagelines' ),
					'href'	=> '#add_section',
					'filter'=> '.gallery',
					'icon'	=> 'icon-camera'
				),
				'navigation'	=> array(
					'name'	=> __( 'Navigation', 'pagelines' ),
					'href'	=> '#add_section',
					'filter'=> '.nav',
					'icon'	=> 'icon-circle-arrow-right'
				),
				'sliders'		=> array(
					'name'	=> __( 'Sliders', 'pagelines' ),
					'href'	=> '#add_section',
					'filter'=> '.slider',
					'icon'	=> 'icon-picture'
				),
				'social'	=> array(
					'name'	=> __( 'Social', 'pagelines' ),
					'href'	=> '#add_section',
					'filter'=> '.social',
					'icon'	=> 'icon-comments'
				),
				'widgets'	=> array(
					'name'	=> __( 'Widgetized', 'pagelines' ),
					'href'	=> '#add_section',
					'filter'=> '.widgetized',
					'icon'	=> 'icon-retweet'
				),
				'misc'		=> array(
					'name'	=> __( 'Miscellaneous', 'pagelines' ),
					'href'	=> '#add_section',
					'filter'=> '.misc',
					'icon'	=> 'icon-star'
				),
			)
		);

		return $toolbar;
	}

	function add_new_callback(){
		$this->xlist = new EditorXList;
		$this->extensions = new EditorExtensions;
		$this->page = new PageLinesPage;

		$sections = $this->extensions->get_available_sections();

		$list = '';
		$count = 1;
		foreach($sections as $key => $s){

			$img = sprintf('<img src="%s" style=""/>', $s->screenshot);

			if($s->map != ''){
				$map = json_encode( $s->map );
				$special_class = 'section-plcolumn';
			} else {
				$map = '';
				$special_class = '';
			}

			if($s->filter == 'deprecated')
				continue;

			if( strpos($s->filter,'full-width') !== false ){
				$section_classes = 'pl-area-sortable area-tag';
			} else {
				$section_classes = 'pl-sortable span12 sortable-first sortable-last';
			}

			$name = $s->name; 
			$desc = ucwords($s->filter); 
			
			

			$class = array('x-add-new', $section_classes, $special_class);

			$filters = explode(',', $s->filter);
			
			foreach($filters as $f){
				$class[] = $f;
			}

			$number = $count++;

			if( !empty($s->isolate) ){
				$disable = true;
				foreach($s->isolate as $isolation){
					if($isolation == 'posts_pages' && $this->page->is_posts_page()){
						$disable = false;
					} elseif ($isolation == '404_page' && is_404()){
						$disable = false;
					} elseif ( $isolation == 'single' && !$this->page->is_special() ){
						$disable = false;
					}
				}

				if( $disable ) {
					$class[] = 'x-disable';
					$number += 100;
				}

			}
			
			if( !pl_is_pro() && !empty($s->sinfo['edition']) && !empty($s->sinfo['edition']) == 'pro' ){
				
				$class[] = 'x-disable';
				$desc = '<span class="badge badge-important">PRO ONLY</span>'; 
			}

			if( !empty($s->loading) ){
				
				$class[] = 'loading-'.$s->loading;

			}
			
			

			$args = array(
				'id'			=> $s->id,
				'class_array' 	=> $class,
				'data_array'	=> array(
					'object' 	=> $s->class_name,
					'sid'		=> $s->id,
					'name'		=> $name,
					'image'		=> $s->screenshot,
					
					'template'	=> $map,
					'clone'		=> pl_new_clone_id(),
					'number' 	=> $number,
				),
				'thumb'			=> $s->screenshot,
				'splash'		=> $s->splash,
				'name'			=> $name,
				'sub'			=> $desc
			);

			$list .= $this->xlist->get_x_list_item( $args );

		}

		printf('<div class="x-list x-sections" data-panel="x-sections">%s</div>', $list);

	}
	
	


}