<?php



class PLImportExport{

	function __construct(){

		add_filter('pl_settings_array', array( $this, 'add_settings'));

		$this->url = PL_PARENT_URL . '/editor';

	}

	function add_settings( $settings ){

		$settings['importexport'] = array(
			'name' 	=> __( 'Import + Export', 'pagelines' ),
			'icon'	=> 'icon-exchange',
			'pos'	=> 45,
			'opts' 	=> $this->option_interface()
		);
		
		return $settings;
	}
	
	function import_template(){
		ob_start();
		
		
		$fileOpts = new EditorFileOpts;
		
		$show_child_import = ($fileOpts->file_exists()) ? true : false;
		
		
		
		?>
		
		<label class="checklist-label media" for="page_tpl_import">
			<div class="img"><input name="page_tpl_import" id="page_tpl_import" type="checkbox" checked /></div>
			<div class="bd">
				<div class="ttl"><?php _e( 'Import Page Templates', 'pagelines' ); ?>
				</div>
				<p><?php _e( 'Add new templates and overwrite ones with the same name.', 'pagelines' ); ?>
				</p>
			</div>
		</label>
		<label class="checklist-label media" for="global_import">
			<div class="img"><input name="global_import" id="global_import" type="checkbox" checked /></div>
			<div class="bd">
				<div class="ttl"><?php _e( 'Import New Global Settings', 'pagelines' ); ?>
				</div>
				<p><?php _e( 'Overwrite global settings with ones from this import.', 'pagelines' ); ?>
				</p>
			</div>
		</label>
		<label class="checklist-label media" for="type_import">
			<div class="img"><input name="type_import" id="type_import" type="checkbox" checked /></div>
			<div class="bd">
				<div class="ttl"><?php _e( 'Import Post Type Settings', 'pagelines' ); ?>
				</div>
				<p><?php _e( 'Overwrite post type settings with ones from this import.', 'pagelines' ); ?>
				</p>
			</div>
		</label>
		
	
	
		
		<label><?php _e( 'DMS Config Import', 'pagelines' ); ?>
		</label>
		
		<span class="btn btn-success fileinput-button import-button">
	        <i class="icon-plus"></i>
	        <span><?php _e( 'Select config file (.json)', 'pagelines' ); ?>
	        </span>
	        <!-- The file input field used as target for the file upload widget -->
	        <input id="fileupload" type="file" name="files[]" multiple>
	    </span>
	
	
		<?php if($show_child_import):  ?>

			<label><?php _e( 'Child Theme Config Import', 'pagelines' ); ?>
			</label>

			<div class="child-import">
				<a href="#" data-action="reset_global_child" class="btn settings-action btn-warning"><i class="icon-download"></i> <?php _e( 'Load Child Theme Config', 'pagelines' ); ?></a>

				<div class="help-block">
					<?php _e( 'Reset theme settings using custom config file from child theme.<br />
					<strong>Note:</strong> Once you have completed this action, you may want to publish these changes to your live site.', 'pagelines' ); ?>
					
				</div>
			</div>
		<?php endif;?>
	
	
		<?php
		return ob_get_clean();
	}
	
	function export_template(){
		ob_start();
		
		$tpls = new EditorTemplates;
		?>
		<label><?php _e( 'Select User Templates', 'pagelines' ); ?></label>
		
		<?php
		
		$btns = sprintf(
			'<div class="checklist-btns">
				<button class="btn btn-mini checklist-tool" data-action="checkall"><i class="icon-ok"></i> %s</button> 
				<button class="btn btn-mini checklist-tool" data-action="uncheckall"><i class="icon-remove"></i> %s</button>
			</div>', __( 'Select All', 'pagelines' ), __( 'Deselect All', 'pagelines' ) );
		
		$tpl_selects = ''; 
		foreach( $tpls->get_user_templates() as $index => $template){
			
			$tpl_selects .= sprintf(
				'<label class="checklist-label media" for="%s">
					<div class="img"><input name="templates[]%s" id="%s" type="checkbox" checked /></div>
					<div class="bd"><div class="ttl">%s</div><p>%s</p></div>
				</label>', 
				$index,
				$index,
				$index, 
				$template['name'], 
				$template['desc']
			);
		}
		
		printf('<fieldset>%s%s</fieldset>', $btns, $tpl_selects );
		
		?>
		<label><?php _e( 'Global Settings', 'pagelines' ); ?></label>
		<label class="checklist-label media" for="export_global" name="export_global">
			<div class="img"><input name="export_global" id="export_global" type="checkbox" checked /></div>
			<div class="bd">
				<div class="ttl"><?php _e( 'Export Site Global Settings', 'pagelines' ); ?>
				</div>
				<p><?php _e( 'This will export your sites global settings. This includes everything in the options panel, as well as settings directed at sections in your "global" regions like your header and footer.', 'pagelines' ); ?>
				</p>
			</div>
		</label>
		
		<label><?php _e( 'Post Type Settings', 'pagelines' ); ?>
		</label>
		<label class="checklist-label media" for="export_types">
			<div class="img"><input name="export_types" id="export_types" type="checkbox" checked /></div>
			<div class="bd">
				<div class="ttl"><?php _e( 'Export Post Type Settings', 'pagelines' ); ?>
				</div>
				<p><?php _e( 'This exports settings such as the template defaults for various post types.', 'pagelines' ); ?>
				</p>
			</div>
		</label>
		
		<label><?php _e( 'Theme Config Publishing', 'pagelines' ); ?>
		</label>
		<?php
			
			$publish_active = (is_child_theme() || PL_LESS_DEV) ? true : false;
		
		?>
		<label class="checklist-label media <?php echo (!$publish_active) ? 'disabled': '';?>" for="publish_config">
			<div class="img"><input id="publish_config" name="publish_config" type="checkbox" <?php echo (!$publish_active) ? 'disabled="disabled"': '';?> /></div>
			<div class="bd">
				<div class="ttl"><?php echo (!$publish_active) ? __( '(Disabled! No child theme active)', 'pagelines' ): '';?> <?php _e( 'Publish Configuration to Child Theme (No Download File)', 'pagelines' ); ?>
				</div>
				<p><?php _e( 'Check this to publish your site configuration as a theme configuration file in your themes root directory. When a user activates your theme it will ask if it can overwrite their settings to attain a desired initial experience to the theme.', 'pagelines' ); ?>
				</p>
			</div>
		</label>
		
		<div class="center publish-button">
			<button class="btn btn-primary btn-large settings-action" data-action="opt_dump"><?php _e( 'Publish', 'pagelines' ); ?>
			 <span class="spamp">&amp;</span> <?php _e( 'Download DMS Config', 'pagelines' ); ?>
			 </button>
		</div>

		<?php
		
		return ob_get_clean();
	}
	
	function option_interface(){
	
		
		$settings = array(
			
			array(
				'type' 		=> 	'template',
				'title' 	=> __( 'Export DMS Config', 'pagelines' ),
		
				'template'	=> $this->export_template()
			),
			array(
				'type' 		=> 	'template',
				'title' 	=> __( 'Import DMS Config', 'pagelines' ),
				'col'		=> 2,
				'template'	=> $this->import_template()
			),
		
		

		);
		
		

		return $settings;
		
	}
	
	// we want to get all the meta from our special posts settings.
	function get_special_settings() {
		
	}

}
