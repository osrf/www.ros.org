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
class EditorInterface {


	function __construct( 
		PageLinesPage $pg, 
		EditorSettings $siteset, 
		EditorDraft $draft, 
		EditorTemplates $templates, 
		PageLinesTemplates $map, 
		EditorExtensions $extensions, 
		EditorThemeHandler $theme 
		) {

		$this->theme = $theme;
		$this->page = $pg;
		$this->draft = $draft;
		$this->siteset = $siteset;
		$this->templates = $templates;
		$this->map = $map;
		$this->extensions = $extensions;

		global $is_chrome;
		if ( $this->editor_user() && $this->draft->show_editor() && $is_chrome){

			add_action( 'wp_footer', array( $this, 'pagelines_toolbox' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'pl_editor_scripts' ) );

		} elseif(current_user_can('edit_theme_options')) {

			add_action( 'wp_footer', array( $this, 'pagelines_editor_activate' ) );

		}


		$this->url = PL_PARENT_URL . '/editor';
		$this->images = $this->url . '/images';


	}

	function pl_editor_scripts(){

		// UTILITIES ----------------------------
		// --------------------------------------

			// Sprintf
			wp_enqueue_script( 'js-sprintf', PL_JS . '/utils.sprintf.js', array( 'jquery' ), PL_CORE_VERSION, true );
			

			// Forms handling
			wp_enqueue_script( 'form-params', PL_JS . '/form.params.js', array('jquery'), PL_CORE_VERSION, true );
			wp_enqueue_script( 'form-store', PL_JS . '/form.store.js', array('jquery'), PL_CORE_VERSION, true );

			wp_enqueue_script( 'form-fileupload', PL_JS . '/utils.fileupload.js', array('jquery', 'jquery-ui-widget'), PL_CORE_VERSION, true );


			// Bootbox Dialogs
			wp_enqueue_script( 'bootbox', PL_JS . '/utils.bootbox.js', array('jquery'), '3.0.0', true );
			// Images Loaded
			wp_enqueue_script( 'imagesloaded', PL_JS . '/utils.imagesloaded.js', array('jquery'), PL_CORE_VERSION, true);

		// PAGELINES CODE -----------------------
		// --------------------------------------
			wp_enqueue_script( 'pl-editor-js', $this->url . '/js/pl.editor.js', array( 'jquery' ), PL_CORE_VERSION , true);
			wp_enqueue_script( 'pl-toolbox-js', $this->url . '/js/pl.toolbox.js', array('pagelines-bootstrap-all' ), PL_CORE_VERSION, true );
			wp_enqueue_script( 'pl-optpanel', $this->url . '/js/pl.optpanel.js', array( 'jquery' ), PL_CORE_VERSION, true );
		
		
			// Saving 
			wp_enqueue_script( 'pl-ajax', $this->url . '/js/pl.ajax.js', array( 'jquery' ), PL_CORE_VERSION, true );
			wp_enqueue_script( 'pl-saving', $this->url . '/js/pl.saving.js', array( 'pl-ajax' ), PL_CORE_VERSION , true);
			
			
			wp_enqueue_script( 'pl-library', $this->url . '/js/pl.library.js', array( 'jquery' ), PL_CORE_VERSION, true );
			wp_enqueue_script( 'pl-layout', $this->url . '/js/pl.layout.js', array( 'jquery' ), PL_CORE_VERSION, true );
			
			wp_enqueue_script( 'js-hotkeys', PL_JS . '/utils.hotkeys.js', array( 'jquery'), PL_CORE_VERSION );

		// Action in to scripts here...
		pagelines_register_hook('pagelines_editor_scripts'); // Hook


		// JQUERY UI STUFF ----------------------------
		// --------------------------------------------
			wp_enqueue_script( 'jquery-ui-tabs');

			$dep = array('jquery-ui-core','jquery-ui-widget', 'jquery-ui-mouse');
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-widget' );
			wp_enqueue_script( 'jquery-ui-mouse' );

			wp_enqueue_script( 'jquery-ui-draggable' );
			wp_enqueue_script( 'jquery-ui-droppable' );
			wp_enqueue_script( 'jquery-ui-resizable' );
			wp_enqueue_script( 'jquery-ui-accordion' );
			
			// Older sortable needs to be used for now
			// 	https://github.com/jquery/jquery-ui/commit/bae06d2b1ef6bbc946dce9fae91f68cc41abccda#commitcomment-2141597
			//	http://bugs.jqueryui.com/ticket/8810
			wp_enqueue_script( 'pl-new-ui-sortable', PL_JS . '/new.jquery.sortable.js', array( 'jquery' ), PL_CORE_VERSION, true );
	
			wp_enqueue_script( 'jquery-mousewheel', PL_JS . '/utils.mousewheel.js', array('jquery'), PL_CORE_VERSION, true );


		// Global AjaxURL variable --> http://www.garyc40.com/2010/03/5-tips-for-using-ajax-in-wordpress/
			$ajax_url = admin_url( 'admin-ajax.php' );
			if ( has_action( 'pl_force_ssl' ) )
				$ajax_url = str_replace( 'http://', 'https://', $ajax_url );
			wp_localize_script( 'pl-editor-js', 'ajaxurl', array( $ajax_url ) );
	}


	function toolbar_config(){

		// actions show up in a dropup
		$actions = apply_filters('pl_toolbar_actions', array());

		$data = array(
			'pl-toggle' => array(
				'icon'	=> 'icon-off',
				'type'	=> 'btn',
				'pos'	=> 1, 
				'tip'	=> __('Deactivate Editor (View Live)', 'pagelines')
			),

			'pl-actions' => array(
				'name'	=> '',
				'icon'	=> '',
				'type'	=> 'dropup',
				'panel'	=> $actions,
				'pos'	=> 200

			),
			'toggle-grid' => array(
				'icon'	=> 'icon-screenshot',
				'tip'	=> __( 'Preview (alt+a)', 'pagelines' ),
				'type'	=> 'btn',
				'pos'	=> 199
			),

		);

		return $data;

	}


	function get_toolbar_config( ){


		$toolbar_config =  apply_filters('pl_toolbar_config', $this->toolbar_config());

		$default = array(
			'pos'	=> 100,
			'tip'	=> ''
		);


		foreach( $toolbar_config as $key => &$info ){
			$info = wp_parse_args( $info, $default );
		}
		unset($info); // set by reference ^^

		uasort( $toolbar_config, array( $this, "cmp_by_position") );

		return apply_filters( 'pl_sorted_toolbar_config', $toolbar_config );
	}

	function cmp_by_position($a, $b) {

	  return $a["pos"] - $b["pos"];

	}

	function pagelines_editor_activate(){
		global $wp;
		global $is_chrome;

		if( ! $this->editor_user() )
			return;

		if($is_chrome){

			$activate_url = pl_add_query_arg( array( 'edtr' => 'on' ) );

			$text = 'Activate PageLines Editor';

			$target = "";
		} else {
			$target = "target='_blank'";
			$activate_url = 'http://www.google.com/chrome';
			$text = 'Chrome is required to use PageLines Editor';

		}
		?>
			<span id="toolbox-activate" data-href="<?php echo $activate_url;?>" class="toolbox-activate pl-make-link" <?php echo $target;?>>
				<i class="icon-off transit"></i> <span class="txt"><?php echo $text; ?></span></span>
			</span>

		<?php
	}

	function pagelines_toolbox(){

		$state = $this->draft->get_state( $this->page->id, $this->page->typeid, $this->map );

		$state_class = '';
		foreach($state as $st){
			$state_class .= ' '.$st;
		}
	?>

	<div class="pl-toolbox-pusher">
	</div>
	<div id="PageLinesToolbox" class="pl-toolbox">
		<div class="resizer-handle"></div>
		<div class="toolbox-handle fix">

			<ul class="unstyled controls">
				<li ><span class="btn-toolbox btn-closer" title="Close [esc]"><i class="icon-remove"></i></span></li>

				<?php

					foreach($this->get_toolbar_config() as $key => $tab){

						if(!isset($tab['type']))
							$tab['type'] = 'panel';

						if( $tab['type'] == 'hidden' || ( $tab['type'] == 'dropup' && empty($tab['panel']) ) )
							continue;

						$data = '';
						$suffix = '';
						$content = '';
						$li_class = array();
						$li_class[] = 'type-'.$tab['type'];

						if($tab['type'] == 'dropup' && !empty($tab['panel'])){

							$data = 'data-toggle="dropdown"';
							$suffix = ' <i class="uxi icon-caret-right"></i>';
							$li_class[] = 'dropup';
							$menu = '';

							foreach($tab['panel'] as $key => $i){
								$menu .= sprintf('<li><a href="#" class="btn-action" data-action="%s">%s</a></li>', $key, $i['name']);
							}
							$content = sprintf('<ul class="dropdown-menu">%s</ul>', $menu);
						}

						$li_classes = join(' ', $li_class);

						$class = array();

						$class[] = ($tab['type'] == 'panel' ) ? 'btn-panel' : '';
						$class[] = ($tab['type'] == 'btn-panel' ) ? 'btn-panel' : '';
						$class[] = ($tab['type'] == 'btn') ? 'btn-action' : '';

						$class[] = 'btn-'.$key;

						$classes = join(' ', $class);

						$the_name = (isset($tab['name']) && $tab['type'] != 'btn-panel') ? $tab['name'] : '';

						$name = sprintf('<span class="txt">%s</span>', $the_name);
						$icon = (isset($tab['icon'])) ? sprintf('<i class="uxi %s"></i> ', $tab['icon']) : '';
						
						$tip = (isset($tab['tip']) && $tab['tip'] != '') ? $tab['tip'] : $the_name;
						
						$title = sprintf('title="%s"', $tip);

						printf(
							'<li class="%s"><span class="btn-toolbox %s" data-action="%s" %s %s>%s%s%s</span>%s</li>',
							$li_classes,
							$classes,
							$key,
							$data,
							$title,
							$icon,
							$name,
							$suffix,
							$content
						);

					}
					
					$show_unload = ($state_class != '') ? 'yes' : ''
				?>
				<li id="stateTool" class="dropup <?php echo $state_class;?>" data-show-unload="<?php echo $show_unload;?>">
					<span class="btn-toolbox btn-state " data-toggle="dropdown">
						<span id="update-state" class="state-draft state-tag">&nbsp;</span>
					</span>
					<ul class="dropdown-menu pull-right state-list">
						<li class="li-state-multi"><a class="btn-revert" data-revert="all"><span class="update-state state-draft multi">&nbsp;</span>&nbsp; <?php _e( 'Undo All Unpublished Changes', 'pagelines' ); ?>
						</a></li>
						<li class="li-state-global"><a class="btn-revert" data-revert="global"><span class="update-state state-draft global">&nbsp;</span>&nbsp; <?php _e( 'Undo Unpublished Global Changes', 'pagelines' ); ?>
						</a></li>

						<li class="li-state-type"><a class="btn-revert" data-revert="type"><span class="update-state state-draft type">&nbsp;</span>&nbsp; <?php _e( 'Undo Unpublished Post Type Changes', 'pagelines' ); ?>
						</a></li>
						<li class="li-state-local"><a class="btn-revert" data-revert="local"><span class="update-state state-draft local">&nbsp;</span>&nbsp; <?php _e( 'Undo Unpublished Local Changes', 'pagelines' ); ?>
						</a></li>
						<li class="li-state-clean disabled"><a class="txt"><span class="update-state state-draft clean">&nbsp;</span>&nbsp; <?php _e( 'No Unpublished Changes', 'pagelines' ); ?>
						</a></li>
					</ul>
				</li>
			</ul>
			<ul class="unstyled controls send-right">
				
				<li class="li-refresh type-btn"><span class="btn-toolbox btn-save btn-refresh" data-mode="pagerefresh" title="<?php _e( 'Refresh needed to view changes.', 'pagelines' ); ?>
				"><i class="icon-refresh"></i></li>
				<li class="li-publish"><span class="btn-toolbox btn-save btn-publish" data-mode="publish" title="<?php _e( 'Publish Live', 'pagelines' ); ?>
				 (alt+s)"><i class="icon-ok"></i> <span class="txt"><?php _e( 'Publish', 'pagelines' ); ?>
				 </span></li>

			</ul>
			<ul class="unstyled controls not-btn send-right">
				<li class="switch-btn btn-saving"><span class="btn-toolbox not-btn"><i class="icon-refresh icon-spin"></i> <span class="txt"><?php _e( 'Saving', 'pagelines' ); ?>
				</span></li>
				<li class="switch-btn btn-layout-resize"><span class="btn-toolbox  not-btn">
					<i class="icon-fullscreen"></i> <span class="txt"><?php _e( 'Width', 'pagelines' ); ?>
					: <span class="resize-px"></span> / <span class="resize-percent"></span></span>
				</li>
			</ul>
		</div>
		<?php pagelines_register_hook('before_toolbox_panel'); // Hook ?>
		<div class="toolbox-panel-wrap">
			<div class="toolbox-panel">
				<div class="toolbox-content fix">
					<div class="toolbox-content-pad option-panel">
						<?php
						foreach($this->get_toolbar_config() as $key => $tab){

							if(isset($tab['panel']) && !empty($tab['panel']))
								$this->panel($key, $tab['panel']);
							else
								printf('<div class="panel-%s tabbed-set error-panel"><i class="icon-spinner icon-spin"></i></div>', $key);

						}
							 ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	}




	function defaults(){
		$d = array(
			'name'		=> '',
			'scope'		=> 'global',
			'icon'		=> '',
			'hook'		=> '',
			'href'		=> '',
			'filter'	=> '',
			'type'		=> 'opts',
			'mode'		=> '',
			'class'		=> '',
			'call'		=> false,
			'flag'		=> '',
			'tab'		=> '', 
			'stab'		=> ''
		);
		return $d;
	}

	function panel($key, $panel){

		?>
		<div class="<?php echo 'panel-'.$key;?> tabbed-set" data-key="<?php echo $key;?>">
			<div class="tabs-wrap">
				<ul class="tabs-nav unstyled">

					<?php
						foreach($panel as $tab_key => $t){

							if($tab_key == 'optPageType' && ($this->page->id == $this->page->type))
								continue;

							if( substr($tab_key, 0, 7) == 'heading'){
								printf('<lh>%s</lh>', $t);

							} else {


								$t = wp_parse_args($t, $this->defaults());

								$href = ($t['href'] != '') ? $t['href'] : '#'.$tab_key;

								$hook = ($t['hook'] != '') ? sprintf('data-hook="%s"', $t['hook']) : '';

								$filter = ($t['filter'] != '') ? sprintf('data-filter="%s"', $t['filter']) : '';

								$flag = ($t['flag'] != '') ? sprintf('data-flag="%s"', $t['flag']) : '';

								$class = ($t['class'] != '') ? $t['class'] : '';

								$icon = ($t['icon'] != '') ? sprintf('<i class="%s"></i> ', $t['icon']) : '';
								
								$link_tab = ($t['tab'] != '') ? sprintf('data-tab-link="%s"', $t['tab']) : '';
								
								$link_sub_tab = ($t['stab'] != '') ? sprintf('data-stab-link="%s"', $t['stab']) : '';

								$tab_action = sprintf('data-tab-action="%s"', $tab_key);
								
								$tab_meta = (isset($t['opts']) && !empty($t['opts'])) ? sprintf('data-tab-meta="%s"', 'options') : '';

								printf(
									'<li class="%s" %s %s %s %s %s %s %s><a href="%s">%s%s</a></li>', 
									$class, 
									$tab_action, 
									$tab_meta, 
									$link_tab,
									$link_sub_tab,
									$hook, 
									$filter, 
									$flag, 
									$href, 
									$icon, 
									$t['name']
								);
								
							}

						}

					?>

				</ul>
			</div>
			<div class="panels-wrap">
			<?php
			
				echo apply_filters( 'pagelines_global_notification', '' );
				
				foreach($panel as $tab_key => $t){

					$t = wp_parse_args($t, $this->defaults());

					if( substr($tab_key, 0, 7) == 'heading' || $t['href'] != '' )
						continue;

					if($tab_key == 'optPageType' && ($this->page->id == $this->page->type))
						continue;

					$content = '';

					if(isset($t['call']) && $t['call'] != ''){
						ob_start();
						call_user_func($t['call']);
						$content = ob_get_clean();
					} else {
						$content = sprintf('<div class="error-panel"><i class="icon-refresh icon-spin"></i></div>', rand());
					}

					$clip = ( isset($t['clip']) ) ? sprintf('<span class="clip-desc">%s</span>', $t['clip']) : '';

					$tools = ( isset($t['tools']) ) ? sprintf('<span class="clip-tools">%s</span>', $t['tools']) : '';

				
					printf(
						'<div id="%s" class="tab-panel" data-panel="%s" data-type="%s" data-scope="%s">
							<div class="tab-panel-inner">
								
								<legend>%s %s %s</legend>
								<div class="panel-tab-content"> %s</div>
							</div>
						</div>',
						$tab_key,
						$tab_key,
						$t['type'],
						$t['scope'],
						$t['name'],
						$clip,
						$tools,
						
						$content
					);
				}
			?>
			</div>
		</div>
		<?php
	}

	function section_controls( $s ){

		if(!$this->draft->show_editor())
			return;

		$sid = $s->id;
		ob_start();
		
		?>
		<div class="pl-section-controls fix" >
			<div class="controls-left">
				<a title="<?php _e( 'Decrease Width', 'pagelines' ) ?>" href="#" class="s-control s-control-icon section-decrease"><i class="icon-caret-left"></i></a>
				<span title="<?php _e( 'Column Width', 'pagelines' ) ?>" class="s-control section-size"></span>
				<a title="<?php _e( 'Increase Width', 'pagelines' ) ?>" href="#" class="s-control s-control-icon section-increase"><i class="icon-caret-right"></i></a>
				<a title="<?php _e( 'Offset Left', 'pagelines' ) ?> <?php echo pl_pro_text();?>" href="#" class="s-control s-control-icon section-offset-reduce <?php echo pl_pro_disable_class();?>"><i class="icon-angle-left"></i></a>
				<span title="<?php _e( 'Offset Amount', 'pagelines' ) ?>" class="s-control offset-size"></span>
				<a title="<?php _e( 'Offset Right', 'pagelines' ) ?> <?php echo pl_pro_text();?>" href="#" class="s-control s-control-icon section-offset-increase <?php echo pl_pro_disable_class();?>"><i class="icon-angle-right"></i></a>
				<a title="<?php _e( 'Force to New Row', 'pagelines' ) ?> <?php echo pl_pro_text();?>" href="#" class="s-control s-control-icon section-start-row <?php echo pl_pro_disable_class();?>"><i class="icon-double-angle-left"></i></a>
			</div>
			<div class="controls-right">
				<a title="<?php _e( 'Edit', 'pagelines' ) ?>" href="#" class="s-control s-control-icon section-edit s-loaded"><i class="icon-pencil"></i></a>
				<a title="<?php _e( 'Clone', 'pagelines' ) ?> <?php echo pl_pro_text();?>" href="#" class="s-control s-control-icon section-clone s-loaded <?php echo pl_pro_disable_class();?>"><i class="icon-copy"></i></a>
				<a title="<?php _e( 'Delete', 'pagelines' ) ?>" href="#" class="s-control s-control-icon section-delete"><i class="icon-remove"></i></a>
			</div>
			<div class="controls-title"><span class="ctitle"><?php echo $s->name;?></span></div>
		</div>
		<?php

		return ob_get_clean();

	}
	
	// checks if PL_EDITOR_LOCK is enabled and if so that the user is allowed to edit.
	// example:
	// define( 'PL_EDITOR_LOCK', 'admin' ); // only allow 'admin' to use editor.
	// define( 'PL_EDITOR_LOCK', 'simon,stefan,andrew' ); // allow 3 users to use the editor.
	// define('PL_EDITOR_LOCK','7,admin,11'); // allows User IDs 7 and 11 and username admin to use the editor.
	// If not defined all users with edit_theme_options role have access to the editor.
	function editor_user() {

		// defined (single user and multi user)
		if( defined( 'PL_EDITOR_LOCK' ) && '' != PL_EDITOR_LOCK ) {

			// get current users info
			$user_data = wp_get_current_user();
			$user = $user_data->user_login;
			$userid = $user_data->ID;

			// explode the alowed users, if its a single name explode still returns an array.
			$users = explode( ',', PL_EDITOR_LOCK );

			// if current user is not in the array of allowed users return false.
			if( ! in_array( $user, $users ) && ! in_array( $userid, $users ) )
				return false;
		}
		//	If we get this far either PL_EDITOR_LOCK is not defined or is not a string or the user is allowed so we just return true.
		return true;
	}
}
