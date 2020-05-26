<?php


class PageLinesAreas {

	var $settings_panel = 'area_settings';

	function __construct(){


		add_action('pagelines_editor_scripts', array( $this, 'scripts'));
		add_filter('pl_toolbar_config', array( $this, 'toolbar'));

		$this->url = PL_PARENT_URL . '/editor';
	}

	function scripts(){
		wp_enqueue_script( 'pl-js-areas', $this->url . '/js/pl.areas.js', array( 'jquery' ), PL_CORE_VERSION, true );
	}

	function toolbar( $toolbar ){

		$toolbar[ $this->settings_panel ] = array(
			'name'	=> 'Area Settings',
			'icon'	=> 'icon-paste',
			'type'	=> 'hidden',
			'flag'	=> 'area-opts',
			'pos'	=> 1000,
			'panel'	=> $this->options_panel()
		);

		return $toolbar;
	}

	function options_panel(){
		global $plpg;

		$tabs = array();
		$tabs['heading'] = "<i class='icon-reorder'></i> Area Settings";

		$tabs[ $this->settings_panel ] = array( 'name'	=> 'Area Settings', 'icon' => 'icon-reorder');


		return $tabs;
	}


	function area_controls($a){

		if( !pl_draft_mode() )
			return '';
			
		ob_start();
		?>

		<div class="pl-area-controls">
			<span class="area-control area-delete area-hide" data-area-action="delete" title="<?php _e( 'Delete', 'pagelines' ) ?>">
				<i class="icon-remove"></i>
			</span><span class="area-control area-clone area-hide <?php echo pl_pro_disable_class();?>" data-area-action="clone" title="<?php _e( 'Clone', 'pagelines' ) ?> <?php echo pl_pro_text();?>">
				<i class="icon-copy"></i>
			</span><span class="area-control area-reorder area-hide" data-area-action="reorder" title="<?php _e( 'Move', 'pagelines' ) ?>">
				<i class="icon-reorder"></i>
			</span><span class="area-control area-edit section-edit" data-area-action="settings" title="<?php _e( 'Edit', 'pagelines' ) ?>">
				<i class="icon-pencil"></i>
			</span>
		</div>
		<?php

		return ob_get_clean();
	}


	function area_start($a){

		$name = (isset($a['name'])) ? $a['name'] : '';
		$class = (isset($a['class'])) ? $a['class'] : '';
		$id = (isset($a['id']) && $a['id'] != '') ? $a['id'] : 'area_'.uniqid();
		$styles = (isset($a['styles'])) ? $a['styles'] : '';

		printf(
			'<div id="%s" data-name="%s" data-class="%s" class="pl-area pl-area-sortable area-tag %s"  data-area-number="%s">%s<div class="pl-content"><div class="pl-inner area-region pl-sortable-area editor-row">%s',
			$id,
			$name,
			$class,
			$class,
			$a['area_number'],
			$this->area_controls($a),
			$this->area_sortable_buffer()
		);

	}

	function area_end(){
		printf('%s</div></div></div>', $this->area_sortable_buffer());
	}

	/*
	 * Used to allow for dropping at top of area, gets around floated element problems
	 */
	function area_sortable_buffer(){

		return ( pl_draft_mode() ) ? sprintf('<div class="pl-sortable pl-sortable-buffer"></div>') : '';
		
	}


}
