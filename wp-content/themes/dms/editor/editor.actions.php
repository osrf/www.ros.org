<?php




add_action('wp_ajax_pl_editor_actions', 'pl_editor_actions');
function pl_editor_actions(){

	$postdata = $_POST;
	$response = array();
	$response['post'] = $postdata;
	$mode = $postdata['mode'];
	$run = $postdata['run'];
	$pageID = $postdata['pageID'];
	$typeID = $postdata['typeID'];

	if($mode == 'save'){
		
		$draft = new EditorDraft;
		$tpl = new EditorTemplates;
		$map = $postdata['map_object'] = new PageLinesTemplates( $tpl );

		if ( $run == 'map' || $run == 'all' || $run == 'draft' || $run == 'publish'){

			$draft->save_draft( $pageID, $typeID, $postdata['pageData'] );


		}

		elseif ( $run == 'revert' )
			$draft->revert( $postdata, $map );

		$response['state'] = $draft->get_state( $pageID, $typeID, $map );


	} elseif( $mode == 'sections'){

		if( $run == 'reload'){

			global $load_sections;
			$available = $load_sections->pagelines_register_sections( true, false );
			$response['result'] = $available;
			
		} elseif( $run == 'load' ){

			$section_object = $postdata['object'];
			$section_unique_id = $postdata['uniqueID'];

			global $pl_section_factory;

			if( is_object($pl_section_factory->sections[ $section_object ]) ){

				global $post;
			
				$post = get_post($postdata['pageID']);

				$s = $pl_section_factory->sections[ $section_object ];

				// needs to be set.. ??
				$s->meta['content'] = array();
				$s->meta['unique']	= '';
				
				
				$opts = $s->section_opts();

				$opts = (is_array($opts)) ? $opts : array();

				$response['opts'] = array_merge($opts, pl_standard_section_options());

				ob_start();
					$s->active_loading = true;
					$s->section_template();
				$section_template = ob_get_clean();

				ob_start();
					$s->section_head();
					$s->section_foot();
				$head_foot = ob_get_clean();
				
				if($head_foot)
					$response['notice'] = true; 
				else 
					$response['notice'] = false;

				$response['template'] = ($section_template == '') ? pl_blank_template() : $section_template;

			}



		}


	} elseif( $mode == 'themes'){

		$theme = new EditorThemeHandler;

		if( $run == 'activate' ){
			$response = $theme->activate( $response );
			pl_flush_draft_caches( false );
		}


	} elseif ( $mode == 'templates' ){

		$tpl = new EditorTemplates;

		if ( $run == 'load' ){

			$metaID = (isset($postdata['templateMode']) && $postdata['templateMode'] == 'type') ? $typeID : $pageID;

			$response['loaded'] = $tpl->load_template( $metaID, $postdata['key'] );

		} elseif ( $run == 'update'){

			$key = ( isset($postdata['key']) ) ? $postdata['key'] : false;

			$template_map = $postdata['map']['template'];

			$response['tpl'] = $tpl->update_template( $key, $template_map, $postdata['settings'], $pageID );

		} elseif ( $run == 'delete'){

			$key = ( isset($postdata['key']) ) ? $postdata['key'] : false;

			$tpl->delete_template( $key );

		} elseif ( $run == 'save' ){

			$template_map = $postdata['map']['template'];
			$settings = $postdata['settings'];

			$name = (isset($postdata['template-name'])) ? $postdata['template-name'] : false;
			$desc = (isset($postdata['template-desc'])) ? $postdata['template-desc'] : '';

			if( $name )
				$tpl->create_template($name, $desc, $template_map, $settings, $pageID);

		} elseif( $run == 'set_type' ){

			$field = 'page-template';
			$value = $postdata['value'];

			$previous_val = pl_local( $typeID, $field );

			if( $previous_val == $value )
				pl_local_update( $typeID, $field, false );
			else
				pl_local_update( $typeID, $field, $value );

			$response['result'] = pl_local( $typeID, $field );


		} elseif( $run == 'set_global' ){

			$field = 'page-template';
			$value = $postdata['value'];

			$previous_val = pl_global( $field );

			if($previous_val == $value)
				pl_global_update( $field, false );
			else
				pl_global_update( $field, $value );


			$response['result'] = pl_global( $field );

		}

	} elseif ( $mode == 'settings' ){

		$plpg = new PageLinesPage( array( 'mode' => 'ajax', 'pageID' => $pageID, 'typeID' => $typeID ) );
		$draft = new EditorDraft;
		$settings = new PageLinesOpts;

		if ($run == 'reset_global'){

			$response['settings'] = $settings->reset_global();

		} elseif( $run == 'reset_local' ){

			$settings->reset_local( $pageID );

		} elseif( $run == 'delete' ){

			// delete clone index by keys


		}elseif( $run == 'exporter' ) {

			$data = $postdata['formData'];
			$data = stripslashes_deep( $data );
			$fileOpts = new EditorFileOpts;
			$response['export'] = $fileOpts->init( $data );
			$response['export_data'] = $data;

		} elseif( $run == 'reset_global_child' ) {

			$opts = array();
			$opts['global_import'] = $_POST['global_import'];
			$opts['type_import'] = $_POST['type_import'];
			$opts['page_tpl_import'] = $_POST['page_tpl_import'];
			$settings->reset_global_child( $opts );

		} elseif( 'reset_cache' == $run ) {
			$settings->reset_caches();
		}

	} else {
	
		$response = apply_filters( 'pl_ajax_'.$mode, $response, $postdata ); 
	}


	// RESPONSE
	echo json_encode(  pl_arrays_to_objects( $response ) );

	die(); // don't forget this, always returns 0 w/o
}


add_action('wp_ajax_upload_config_file', 'pl_upload_config_file');
function pl_upload_config_file(){

	$fileOpts = new EditorFileOpts;
	$filename = $_FILES['files']['name'][0];

	$opts = array();
	$opts['global_import'] = $_POST['global_import'];
	$opts['type_import'] = $_POST['type_import'];
	$opts['page_tpl_import'] = $_POST['page_tpl_import'];

	if( preg_match( '/pl\-config[^\.]*\.json/', $filename ) ) {
		$file = $_FILES['files']['tmp_name'][0];

	$response['file'] = $file;

	if( isset( $file ) )
		$response['import_reponse'] = $fileOpts->import( $file, $opts );


		$response['import_file'] = $file;
		$response['post'] = $_POST;
	} else {
		$reponse['import_error'] = 'filename?';
	}

	echo json_encode(  pl_arrays_to_objects( $response ) );
	die();
}

add_action('wp_ajax_pl_editor_mode', 'pl_editor_mode');
function pl_editor_mode(){

	$postdata = $_POST;
	$key = 'pl_editor_state';
	$user_id = $postdata[ 'userID' ];

	$current_state = get_user_meta($user_id, $key, true);

	$new_state = (!$current_state || $current_state == 'on' || $current_state == '') ? 'off' : 'on';

	update_user_meta( $user_id, $key, $new_state );

	echo $new_state;

	die();
}

add_action('wp_ajax_pl_dms_admin_actions', 'pl_dms_admin_actions');
function pl_dms_admin_actions(){
	$response = array();
	$postdata = $_POST;
	$response['post'] = $_POST;
	$lessflush = ( isset( $postdata['flag'] ) ) ? $postdata['flag'] : false;

	$field = $postdata['setting'];
	$value = $postdata['value'];

	pl_setting_update($field, $value);

	echo json_encode(  pl_arrays_to_objects( $response ) );
	if( $lessflush )
		pl_flush_draft_caches( false );
	die();
}


add_action( 'wp_ajax_pl_up_image', 'pl_up_image' );
function pl_up_image (){

	global $wpdb;

	$files_base = $_FILES[ 'qqfile' ];

	$arr_file_type = wp_check_filetype( basename( $files_base['name'] ));

	$uploaded_file_type = $arr_file_type['type'];

	// Set an array containing a list of acceptable formats
	$allowed_file_types = array( 'image/jpg','image/jpeg','image/gif','image/png', 'image/x-icon');

	if( in_array( $uploaded_file_type, $allowed_file_types ) ) {

		$files_base['name'] = preg_replace( '/[^a-zA-Z0-9._\-]/', '', $files_base['name'] );

		$override['test_form'] = false;
		$override['action'] = 'wp_handle_upload';

		$uploaded_file = wp_handle_upload( $files_base, $override );

	//	$upload_tracking[] = $button_id;

		// ( if applicable-Update option here)

		$name = 'PageLines- ' . addslashes( $files_base['name'] );

		$attachment = array(
						'guid'				=> $uploaded_file['url'],
						'post_mime_type'	=> $uploaded_file_type,
						'post_title'		=> $name,
						'post_content'		=> '',
						'post_status'		=> 'inherit'
					);

		$attach_id = wp_insert_attachment( $attachment, $uploaded_file['file'] );
		$attach_data = wp_generate_attachment_metadata( $attach_id, $uploaded_file['file'] );
		wp_update_attachment_metadata( $attach_id,  $attach_data );

	} else
		$uploaded_file['error'] = __( 'Unsupported file type!', 'pagelines' );

	if( !empty( $uploaded_file['error'] ) )
		echo sprintf( __('Upload Error: %s', 'pagelines' ) , $uploaded_file['error'] );
	else{
		
		$url = pl_shortcodize_url( $uploaded_file['url'] );
		 
		echo json_encode( array( 'url' => $url, 'success' => TRUE, 'attach_id' => $attach_id ) );

	}
	
	die(); // don't forget this, always returns 0 w/o
	
}

add_filter( 'pagelines_global_notification', 'pagelines_check_folders_dms');
function pagelines_check_folders_dms( $note ) {
	
	
	$folder = basename( get_template_directory() );

	if( 'dms' != $folder ){
		
		ob_start(); ?>

			<div class="alert editor-alert">
				<button type="button" class="close" data-dismiss="alert" href="#">&times;</button>
			  	<strong><i class="icon-warning-sign"></i> Install Problem!</strong><p>it looks like you have DMS installed in the wrong folder.<br />DMS must be installed in wp-content/themes/<strong>dms</strong>/ and not wp-content/themes/<strong><?php echo $folder; ?></strong>/</p>

			</div>

			<?php 

		$note .= ob_get_clean();
		
	} 
	if( pl_is_pro() && !pl_has_dms_plugin() ){
		
		ob_start(); ?>

			<div class="editor-alert alert">
				
			  	<strong><i class="icon-cogs"></i> <?php _e( 'Install DMS Utilities', 'pagelines' ); ?>
			  	</strong><p><?php _e( 'Your site is "Pro activated" but you have not installed the DMS Pro tools plugin. Grab the plugin on <a href="http://www.pagelines.com/my-account" >PageLines.com &rarr; My-Account</a>.', 'pagelines' ); ?>
			  	</p>

			</div>

			<?php 

		$note .= ob_get_clean();
		
	} 
	if ( ! pl_is_pro() ){
		
		ob_start(); ?>
		
		<div class="alert editor-alert">
			<button type="button" class="close" data-dismiss="alert" href="#">&times;</button>
		  	<strong><i class="icon-star"></i> <?php _e( 'Upgrade to Pro!', 'pagelines' ); ?>
		  	</strong> <br/>
			<?php _e( 'You are currently using the basic DMS version. Pro activate this site for tons more features and support.', 'pagelines' ); ?>
			
			<a href="http://www.pagelines.com/DMS" class="btn btn-mini" target="_blank"><i class="icon-thumbs-up"></i> <?php _e( 'Learn More About Pro', 'pagelines' ); ?>
			</a>
			&mdash; <em><?php _e( 'Already a Pro?', 'pagelines' ); ?>
			</em> <a href="#" class="btn btn-mini" data-tab-link="account" data-stab-link="pl_account"><i class="icon-star"></i> <?php _e( 'Activate Site', 'pagelines' ); ?>
			</a> 
		</div>
		
		<?php 
		
		$note .= ob_get_clean();
	}
		
	
	
	return $note;
		
}

// clear draft css on plugin activate/deactivate
if( is_admin() && isset( $_REQUEST['plugin'] ) ) {
	add_action( 'activate_' . $_REQUEST['plugin'], 'pl_flush_draft_caches' );
	add_action( 'deactivate_' . $_REQUEST['plugin'], 'pl_flush_draft_caches' );
}

$custom_attach = new PLImageUploader();

class PLImageUploader{
	function __construct() {
		if ( isset( $_REQUEST['context'] ) && $_REQUEST['context'] == 'pl-custom-attach' ) {

			$this->option_id = (isset( $_REQUEST['oid'] )) ? $_REQUEST['oid'] : '';

			add_filter( 'attachment_fields_to_edit', array( $this, 'attachment_fields_to_edit' ), 15, 2 );
			add_filter( 'media_upload_tabs', array( $this, 'filter_upload_tabs' ) );
			add_filter( 'media_upload_mime_type_links', '__return_empty_array' );
			add_action( 'media_upload_library' , array( $this, 'the_js' ), 15 );
		}
	}


	function the_js(){
		?>

		<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery('.pl-frame-button').on('click', function(){
			
				var oSel = parent.jQuery.pl.iframeSelector
				,	optID = '#' + oSel
				,	previewSel = '.pre_' + oSel
				,	editorPrevew = '.opt-upload-thumb-' + oSel
				,	imgURL = jQuery(this).data('imgurl')
				,	imgURLShort = jQuery(this).data('short-img-url')
				, 	theOption = '[id="'+oSel+'"]'
				
				console.log(theOption)

				jQuery( theOption, top.document).val( imgURLShort )
				
				parent.jQuery( '.lstn' ).first().trigger('blur')
				
				jQuery(previewSel, top.document).attr('src', imgURL)
				
				jQuery( editorPrevew, top.document).html( '<div class="img-wrap"><img style="max-width:200px;" src="'+ imgURL +'" /></div>' )
				
				parent.eval('jQuery(".bootbox").modal("hide")')
			
			});
		});
		</script>

		<?php
	}

	/**
	 * Replace default attachment actions with "Set as header" link.
	 *
	 * @since 3.4.0
	 */
	function attachment_fields_to_edit( $form_fields, $post ) {

		$form_fields = array();

		$attach_id = $post->ID;

		
		$image_url = wp_get_attachment_url( $attach_id );
		$short_img_url = pl_shortcodize_url( $image_url );

		$form_fields['buttons'] = array(
			'tr' => sprintf(
						'<tr class="submit"><td></td>
							<td>
							<span class="pl-frame-button admin-blue button" data-selector="%s" data-imgurl="%s" data-short-img-url="%s">%s</span>
							</td></tr>',
							$this->option_id,
							$image_url,
							$short_img_url,
							__( 'Select This Image For Option', 'pagelines' )
					)
		);
		$form_fields['context'] = array(
			'input' => 'hidden',
			'value' => 'pl-custom-attach'
		);
		$form_fields['oid'] = array(
			'input' => 'hidden',
			'value' => $this->option_id
		);

		return $form_fields;
		
	}

	/**
	 * Leave only "Media Library" tab in the uploader window.
	 *
	 * @since 3.4.0
	 */
	function filter_upload_tabs() {
		return array( 'library' => __('Media Library', 'pagelines') );
	}
}
