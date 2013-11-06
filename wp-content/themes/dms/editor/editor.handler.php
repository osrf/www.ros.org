<?php
/**
 *
 *
 *  PageLines Front End Template Class
 *
 *
 *  @package PageLines DMS
 *  @subpackage Sections
 *  @since 3.0.0
 *
 *
 */
class PageLinesTemplateHandler {

	var $section_list = array();
	var $section_list_unique = array();
	var $opts_list	= array();
	var $area_number = 1;
	var $row_width = array();
	var $section_count = 0;

	function __construct(
		EditorInterface $interface,
		PageLinesAreas $areas,
		PageLinesPage $pg,
		EditorSettings $siteset,
		PageLinesFoundry $foundry,
		PageLinesTemplates $map,
		EditorDraft $draft,
		PageLinesOpts $opts,
		EditorLayout $layout,
		EditorExtensions $extensions
	) {


		global $pl_section_factory;

		$this->factory = $pl_section_factory->sections;

		// Dependancy Injection (^^)
		$this->editor = $interface;
		$this->areas = $areas;
		$this->page = $pg;
		$this->siteset = $siteset;
		$this->foundry = $foundry;
		$this->draft = $draft;
		$this->optset = $opts;
		$this->layout = $layout;
		$this->extensions = $extensions;
		$this->map_handler = $map;
		
		$this->regions = new PageLinesRegions;
		

		$this->map = $this->map_handler->get_map( $this->page );

		$this->parse_config();

		$this->opts_config = $this->get_options_config();

		$this->setup_processing();

		if( $this->draft->show_editor() ){
			add_action( 'wp_footer', array( $this, 'json_blob' ) );
		}

	}

	function json_blob(){
		?><script>
			!function ($) {

				$.pl = {
					data: {
						local:  <?php echo json_encode( pl_arrays_to_objects( $this->current_page_data('local') ) ); ?>
						
						
						, type:  <?php echo json_encode( pl_arrays_to_objects( $this->current_page_data('type') ) ); ?>
						
						
						, global:  <?php echo json_encode( pl_arrays_to_objects( $this->current_page_data('global') ) ); ?>
					}
					
					, map: { header: {}, footer: {}, template: {} }
					
					, flags: {
							refreshOnSave: false
						,	savingDialog: 'Saving'
						,	refreshingDialog: 'Success! Reloading page'
						,	layoutMode: '<?php echo $this->layout->get_layout_mode();?>'
						,	saving: false
					}
					
					, config: {
						userID: '<?php echo $this->get_user_id();?>'
						, currentURL: '<?php echo $this->current_url();?>'
						, siteURL: '<?php echo site_url();?>'
						, nonce: '<?php echo wp_create_nonce( "tgmpa-install" ); ?>'
						, pageTemplate: '<?php echo $this->page->template; ?>'
						, templateMode: '<?php echo $this->get_template_mode(); ?>'
						, pageID: '<?php echo $this->page->id; ?>'
						, typeID: '<?php echo $this->page->typeid; ?>'
						, pageTypeID: '<?php echo $this->page->type; ?>'
						, pageTypeName: '<?php echo $this->page->type_name; ?>'
						, devMode: <?php echo $this->get_dev_mode();?>
						, CacheKey: '<?php echo pl_get_cache_key(); ?>'
						, isSpecial: '<?php echo $this->page->is_special(); ?>'
						, opts: <?php echo json_encode( pl_arrays_to_objects( $this->get_options_config() ) ); ?>
						, settings: <?php echo json_encode( pl_arrays_to_objects( $this->siteset->get_set('site') ) ); ?>
						, panels: <?php echo json_encode( pl_arrays_to_objects( $this->get_panels_settings() ) ); ?>
						, fonts: <?php echo json_encode( pl_arrays_to_objects( $this->foundry->get_foundry() ) ); ?>
						, menus: <?php echo json_encode( pl_arrays_to_objects( $this->get_wp_menus() ) ); ?>
						, extensions: <?php echo json_encode( pl_arrays_to_objects( $this->extensions->get_list() ) ); ?>
						, icons: <?php echo json_encode( pl_arrays_to_objects( pl_icon_array() ) ); ?>
						, btns: <?php echo json_encode( pl_arrays_to_objects( pl_button_classes() ) ); ?>
						, imgSizes: <?php echo json_encode( pl_arrays_to_objects( pl_get_image_sizes() ) ); ?>
						, animations: <?php echo json_encode( pl_arrays_to_objects( pl_animation_array() ) ); ?>
						, urls: {
							adminURL: '<?php echo admin_url(); ?>'
							, editPost: '<?php echo $this->edit_post_link(); ?>'
							, menus: '<?php echo admin_url( "nav-menus.php" );?>'
							, widgets: '<?php echo $this->edit_post_link();?>'
							, StyleSheetURL: '<?php echo get_stylesheet_directory_uri(); ?>'
							, ParentStyleSheetURL: '<?php echo get_template_directory_uri(); ?>'
							, siteURL: '<?php echo home_url(); ?>'
							, mediaLibrary: '<?php echo $this->media_library_link(); ?>'
						}
					}

				}


			}(window.jQuery);
		</script>
		<?php

	}
	
	function media_library_link(){
		
		
		global $post;

		if( empty($post->ID) )
			$post_id = 0; 
		else 
			$post_id = $post->ID;

		$image_library_url = add_query_arg( 'post_id', (int) $post_id, admin_url('media-upload.php') );
		$image_library_url = add_query_arg( 'type', 'image', $image_library_url );
		$image_library_url = add_query_arg( 'tab', 'library', $image_library_url);
		
		$image_library_url = add_query_arg( array( 'context' => 'pl-custom-attach', 'TB_iframe' => 1), $image_library_url );
		
		return $image_library_url;
	}
	
	function get_panels_settings(){
		global $pl_user_theme_tabs; 
		
		$settings = array(); 
		
		if(!empty($pl_user_theme_tabs) && is_array($pl_user_theme_tabs))
			$settings = array_merge($settings, $pl_user_theme_tabs);
		
		return $settings;
	}
	
	function get_template_mode(){
		
		if(is_page() || $this->page->is_special()){
			return 'local';
		} else {
			return 'type';
		}
		
	}
	
	function get_dev_mode(){
		return ( is_pl_debug() ) ? 'true' : 'false';
	}


	function current_url(){
		global $wp;
		$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );

		return $current_url;
	}


	function edit_post_link(){
		if($this->page->is_special())
			$url = admin_url( 'edit.php' );
		else
			$url = get_edit_post_link( $this->page->id );

		return $url;
	}

	function get_wp_menus(){
		$menus = wp_get_nav_menus( array('orderby' => 'name') );
		return $menus;
	}

	function meta_defaults($key){

		$p = splice_section_slug($key);
		
		$defaults = array(
			'id'		=> $key,
			'object'	=> $key,
			'offset'	=> 0,
			'clone'		=> substr(uniqid(), -6),
			'unique'	=> substr(uniqid(), -6),
			'content'	=> array(),
			'span'		=> 12,
			'newrow'	=> 'false',
		
		);

		return $defaults;
	}

	function get_user_id(){
		$current_user = wp_get_current_user();
		return $current_user->ID;
	}

	function parse_config(){
		
		$clone_was_set = false;
		
		
		foreach($this->map as $group => &$g){

			if( !isset($g) || !is_array($g) )
				continue;

			foreach($g as $area => &$a){
			
			
				if( !isset( $a['object'] ) || !$a['object'] ){
					$a['object'] = 'PLSectionArea';
				}
			
				$a = wp_parse_args( $a, $this->meta_defaults( $area ) );
				
				$a['set'] = $this->optset->get_set( $a['clone'] ); 
				
				// Lets get rid of the number based clone system
				if( strlen( $a['clone'] ) < 3 ){
					$a['clone'] = pl_new_clone_id();
					$clone_was_set = true;
				}
					
				
				$this->section_list[ ] = $a;
				$this->section_list_unique[ $a['object'] ] = $a;

				if( !isset($a['content']) || !is_array($a['content']) )
					continue;

				foreach($a['content'] as $key => &$meta){

					$meta = wp_parse_args($meta, $this->meta_defaults($key));
					$meta['set'] = $this->optset->get_set( $meta['clone'] ); 

					if( strlen( $meta['clone'] ) < 3 ){
						$meta['clone'] = pl_new_clone_id();
						$clone_was_set = true;
					}

					if(!empty($meta['content'])){
						foreach($meta['content'] as $subkey => &$sub_meta){
							
							$sub_meta = wp_parse_args($sub_meta, $this->meta_defaults($subkey));
							$sub_meta['set'] = $this->optset->get_set( $sub_meta['clone'] ); 
							
							if( strlen( $sub_meta['clone'] ) < 3 ){
								$sub_meta['clone'] = pl_new_clone_id();
								$clone_was_set = true;
							}
							
							$this->section_list[  ] = $sub_meta;
							$this->section_list_unique[$sub_meta['object']] = $sub_meta;
						}
						unset($sub_meta); // set by reference

						$this->section_list[  ] = $meta;
						$this->section_list_unique[ $meta['object'] ] = $meta;
					} else {
						$this->section_list[  ] = $meta;
						$this->section_list_unique[ $meta['object'] ] = $meta;
					}

				}
				unset($meta); // set by reference
			}
			unset($a); // set by reference
		}

		
		// This sets a map for the page, if it isn't set with new clone IDs the options wont
		// work until a user action causes the map to be saved, non-ideal
		if( $clone_was_set )
			$this->map_handler->save_map_draft( $this->page->id, $this->page->typeid, $this->map, $this->get_template_mode() ); 
		
	

		// add passive sections (not in drag drop but added through options/hooks)
		global $passive_sections;

		if(is_array($passive_sections) && !empty($passive_sections)){
			foreach($passive_sections as $key){
				 
				$meta = wp_parse_args(array(), $this->meta_defaults($key));
				$meta['set'] = $this->optset->get_set( $meta['clone'] ); 
				
				$this->section_list[  ] = $meta;
				$this->section_list_unique[ $meta['object'] ] = $meta;
			}
		}
	}

	function setup_processing(){

		global $pl_section_factory;

		foreach($this->section_list as $key => &$meta){

		//	$meta['set'] = $this->load_section_settings( $meta );

			if( $this->in_factory( $meta['object'] ) ){
				$this->factory[ $meta['object'] ]->meta = $meta;

			}else
				unset($this->section_list[$key]);

		}
		unset($meta);
		
		


	}

	function get_options_config(){

		$opts_config = array();


		// BACKWARDS COMPATIBILITY
		add_action('override_metatab_register', array( $this, 'get_opts_from_optionator'), 10, 2);

		foreach($this->section_list as $key => $meta){

			if($this->in_factory( $meta['object'] )) {

				$s = $this->factory[ $meta['object'] ];
				
				$s->meta = $meta;

				$opts_config[ $s->meta['clone'] ] = array(
					'name'	=> $s->name
				);

			
				$opts = array();

				// Grab the options
				$opts = $s->section_opts();


				// Deal with special case flags...
				if(is_array($opts)){
					foreach($opts as $index => $opt){
						if(isset($opt['case'])){
							// Special Page Only Option (e.g. used in post loop)
							if($opt['case'] == 'special' && !$this->page->is_special())
								unset($opts[$index]);

							if($opt['case'] == 'page' && !is_page())
								unset($opts[$index]);

							if($opt['case'] == 'post' && !is_post())
								unset($opts[$index]);
						}
					}
				}


				// For backwards compatibility with the older optionator format
				// It works by using a hook to hijack the 'register_metapanel' function
				// The hook then sets an attribute of this class to the array of options from the section
				if(!$opts || empty($opts)){

					$this->current_option_array = array();

					// backwards comp
					$s->section_optionator( array() );

					if(isset( $this->current_option_array ))
						$opts = process_to_new_option_format( $this->current_option_array );


				}

				// deals with legacy special stuff
				if(!empty($opts)){
					foreach($opts as $okey => &$o){


						if($o['type'] == 'multi'){
							if(isset($o['opts']) && is_array($o['opts'])){
								foreach($o['opts'] as $okeysub => &$osub){
									if(!isset($osub['key']))
										$osub['key'] = $okeysub;

									$this->opts_list[] = $osub['key'];
								}
								unset($osub); // set by reference
							}

						} else {

							if(!isset($o['key']))
								$o['key'] = $okey;

							$this->opts_list[] = $o['key'];
						}
					}
					unset($o); // set by reference
				}

				$opts = array_merge($opts, pl_standard_section_options());

				$opts_config[ $s->meta['clone'] ][ 'opts' ] = $opts;

				
			}


		}

		remove_action('override_metatab_register', array( $this, 'get_opts_from_optionator'), 10, 2);


		foreach($opts_config as $item => &$i){
			$i['opts'] = $this->opts_add_values( $i['opts'] );
		}
		unset($i);


		return $opts_config;
	}



	function opts_add_values( $opts ){

		if( is_array($opts) ){
			foreach($opts as $index => &$o){

				if($o['type'] == 'multi'){
					$o['opts'] = $this->opts_add_values( $o['opts'] );
				} else {

					if($o['type'] == 'select_taxonomy'){

						$taxonomy_id = isset( $o['taxonomy_id'] ) ?  $o['taxonomy_id'] : 'category'; 
						
						$terms_array = get_terms( $taxonomy_id );

						if( $taxonomy_id == 'category')
							$o['opts'][] = array('name' => '*Show All*');

						foreach($terms_array as $term){
							if(is_object($term))
								$o['opts'][ $term->slug ] = array('name' => $term->name);
						}


					}

					// Add the value
					$o['val'] = ( isset($this->optset->set[ $o['key'] ]) ) ? $this->optset->set[ $o['key'] ] : array();

				}

			}
			unset($o);
		}


		return $opts;
	}



	function get_opts_from_optionator( $array ){

		$this->current_option_array = $array;

	}



	function current_page_data( $scope = 'local' ){
		$d = array();

		if($scope == 'local'){

			$d = pl_settings( $this->draft->mode, $this->page->id );
			
			// ** Backwards Compatible Stuff ** //
			if(!is_pagelines_special()){
				foreach($this->opts_list as $key => $opt){

					$val = plmeta( $opt, array('post_id' => $this->page->id) );

					if( !isset($d[ $opt ]) && $val != '')
						$d[$opt] = array( pl_html($val) );
				}
			}

		} elseif($scope == 'type'){

			$d = pl_settings( $this->draft->mode, $this->page->typeid );

			// ** Backwards Compatible Stuff **
			$old_special = get_option('pagelines-special');

			if( isset( $old_special[ $this->page->type ] ) ){
				foreach($this->opts_list as $key => $opt){

					if( !isset($d[ $opt ]) && isset($old_special[ $this->page->type ][ $opt ]) && !empty($old_special[ $this->page->type ][ $opt ]) )
						$d[$opt] = array( pl_html($old_special[ $this->page->type ][ $opt ]));

				}
			}

		} else {

			$d = pl_settings( $this->draft->mode );

			// ** Backwards Compatible Stuff **
			$old_special = get_option('pagelines-special');

			if( isset( $old_special[ 'default' ] ) ){
				foreach($this->opts_list as $key => $opt){

					if(!isset($d[ $opt ]) && isset($old_special[ 'default' ][ $opt ]) && !empty($old_special[ 'default' ][ $opt ]) )
						$d[ $opt ] = array( pl_html($old_special[ 'default' ][ $opt ]) );

				}
			}

		}


		return ($d) ? $d : array();
	}








	function process_styles(){



		/*
			TODO add !has_action('override_pagelines_css_output')
		*/
		foreach($this->section_list as $key => $meta){

			if($this->in_factory( $meta['object'] )) {

				$s = $this->factory[ $meta['object'] ];

				$s->meta = $meta;

				$s->section_styles();
				
				$s->section_scripts();

				// Auto load style.css for simplicity if its there.
				if( is_file( $s->base_dir . '/style.css' ) ){

					wp_register_style( $s->id, $s->base_url . '/style.css', array(), $s->settings['p_ver'], 'screen');
			 		wp_enqueue_style( $s->id );

				}
			}
		}
	}

	function process_head(){


		foreach($this->section_list as $key => $meta){

			if( $this->in_factory( $meta['object'] ) ){

				$s = $this->factory[ $meta['object'] ];

				$s->meta = $meta;
				
			
				$s->setup_oset( $meta['clone'] ); // refactor

				ob_start();

					$s->section_head( $meta['clone'] );

				$out = ob_get_clean();

				if($out != '')
					echo pl_source_comment($s->name.' | Section Head') . $out;


			}
		}
	}
	
	function process_foot(){


		foreach($this->section_list as $key => $meta){

			if( $this->in_factory( $meta['object'] ) ){

				$s = $this->factory[ $meta['object'] ];

				$s->meta = $meta;
				
				ob_start();

					$s->section_foot( );

				$out = ob_get_clean();

				if($out != '')
					echo pl_source_comment($s->name.' | Section Foot') . $out;


			}
		}
	}

	function process_region( $region = 'template' ){

		if(!isset($this->map[ $region ]))
			return;

		if(pl_draft_mode())
			$this->regions->region_start( $region );


		if( is_array( $this->map[ $region ] ) ){

			$area_count = 0;
			$area_total = count( $this->map[ $region ] );

			foreach( $this->map[ $region ] as $area => $a ){
			

				if( isset($a['object']) && !empty($a['object']) && $this->in_factory( $a['object'] ) ){

					$area_count++;
					$this->render_section( $a, $area_count, $area_total, 0 );
					
				} else {
					
					// THIS MIGHT NOT BE USED ANYMORE AS ALL SECTIONS USED A NESTED FUNCTION

					// deprecated - this isnt used i dont think
					$a['area_number'] = $this->area_number++;

					$this->areas->area_start($a);

					if( isset($a['content']) && !empty($a['content'])){

						$section_count = 0;
						$sections_total = count($a['content']);

						foreach($a['content'] as $key => $meta){
							
							$section_count++;
							$this->render_section( $meta, $section_count, $sections_total );
						}
					}
					$this->areas->area_end($a);
				}
			}
		}
	}

	function render_section( $meta, $count = false, $total = false, $level = 1 ){
			
		if( $this->in_factory( $meta['object'] ) ){
			
			$s = $this->factory[ $meta['object'] ];

			$s->meta = $meta;
			$s->level = $level;

			$s->setup_oset( $meta['clone'] ); // refactor

			if( has_filter( 'pagelines_render_section' ) ) {
				$output = apply_filters( 'pagelines_render_section', $s, $this );
			} else {
				ob_start();
				$this->section_template_load( $s );
				$output = ob_get_clean();	
			}
	
			$render = (!isset($output) || $output == '') ? false : true;

			if( ! $render && current_user_can( 'edit_theme_options' ) ){
				$output = pl_blank_template(); 
				$render = true;
			}

			if( $level >= 1 )
				$this->grid_row_start( $s, $count, $total, $render, $level );

			if( $render ){
				
				$s->wrapper_classes['user_classes'] = $s->opt('pl_area_class');
				
				$s->before_section_template( );

				$this->before_section( $s );

				echo $output;

				$this->after_section( $s );

				$s->after_section_template( );
				
			}

			if( $level >= 1 )
				$this->grid_row_stop( $s, $count, $total, $render, $level );

			wp_reset_postdata(); // Reset $post data
			wp_reset_query(); // Reset wp_query

		}

	}

	function grid_row_start( $s, $count, $total, $render = true, $level = 1 ){

		if( $this->draft->show_editor() )
			return;

		if( !isset($this->row_width[ $level ]) ){
			$this->row_width[ $level ] = 0;
		}
		

		if( $count == 1 ){
			
			$this->row_width[ $level ] = 0;
			printf('<div class="row grid-row">');
		}

		if( $render ){

			$section_width = $s->meta['span'] + $s->meta['offset'];

			$this->row_width[ $level ] +=  $section_width;

			if( $this->row_width[ $level ] > 12 || $s->meta['newrow'] == 'true' ){

				$this->row_width[ $level ] = $section_width;

				printf('</div>%s<div class="row grid-row">', "\n\n");
			}

		}


	}

	function grid_row_stop( $s, $count, $total, $render, $level = 1 ){

		if($this->draft->show_editor())
			return;

		if( $count == $total ){
			$this->row_width[ $level ] = 0;
			printf('</div>');
		}
	}

	function before_section( $s ){
		echo pl_source_comment($s->name . ' | Section Template', 2); // Add Comment

		pagelines_register_hook('pagelines_before_'.$s->id, $s->id); // hook

		// Rename to prevent conflicts
		// TODO remove this or check to remove this strange non-algorhythmic code
		if ( 'comments' == $s->id )
			$sid = 'wp-comments';
		elseif ( 'content' == $s->id )
			$sid = 'content-area';
		else
			$sid = $s->id;

		$clone 	= $s->meta['clone'];

		$edition = $s->sinfo['edition'];
		
		if(!pl_is_pro()){
			$edition = $s->sinfo['edition'];
			
			if($edition == 'pro'){
				$class[] = 'pro-section';
			}
		}
		
		if($s->level == 0){
			$class[] = 'pl-area pl-area-sortable area-tag';
			$controls = $this->areas->area_controls( $s );
			$pad_class = 'pl-area-pad';
		} else {
			// Content Section Stuff
			$span 	= (isset($s->meta['span'])) ? sprintf('span%s', $s->meta['span']) : 'span12';
			$offset = (isset($s->meta['offset'])) ? sprintf('offset%s', $s->meta['offset']) : 'offset0';
			$newrow = ( isset($s->meta['newrow']) && $s->meta['newrow'] == 'true' ) ? 'force-start-row' : '';
			$class[] = 'pl-section';
			$class[] = $span;
			$class[] = $offset;
			$class[] = $newrow;
			$controls = $this->editor->section_controls( $s );
			$pad_class = 'pl-section-pad';
		}


		$class = array_merge( $class, $s->wrapper_classes, (array) explode( ' ', $s->special_classes ) );
		$class = array_unique( array_filter( $class ) ); // ensure no empties or duplicates

		printf(
			'<section id="%s" data-object="%s" data-sid="%s" data-clone="%s" class="%s section-%s">%s<div class="%s fix">',
			$s->id.$clone,
			$s->class_name,
			$s->id,
			$clone,
			implode(" ", $class),
			$sid,
			$controls,
			$pad_class
		);

		pagelines_register_hook('pagelines_outer_'.$s->id, $s->id); // hook
		pagelines_register_hook('pagelines_inside_top_'.$s->id, $s->id); // hook

 	}

	function after_section( $s ){

		pagelines_register_hook('pagelines_inside_bottom_'.$s->id, $s->id);

		printf('</div></section>');

		pagelines_register_hook('pagelines_after_'.$s->id, $s->id);
	}

	function section_template_load( $s ) {

		// Variables for override
		$override_template = 'template.' . $s->id .'.php';
		$override = ( '' != locate_template(array( $override_template), false, false)) ? locate_template(array( $override_template )) : false;

		if( $override != false)
			require( $override );
		else
			$s->section_template();
			
			

	}

	/**
	 * Tests if the section is in the factory singleton
	 */
	function in_factory( $section ){
		return ( isset($this->factory[ $section ]) && is_object($this->factory[ $section ]) ) ? true : false;
	}

}


/**
 * For use inside of sections
 */
function render_nested_sections( $sections, $level = 2){
	ob_start(); 
	
	global $pagelines_editor;

	if( !empty( $sections ) ){

		$section_count = 0;
		$sections_total = count($sections);

		foreach( $sections as $key => $meta )
			$pagelines_editor->handler->render_section( $meta, ++$section_count, $sections_total, $level);

	}
	
	return ob_get_clean();

}

