<?php

class PL_Bulk_edit {
	
	function __construct() {
		
		if( version_compare( CORE_VERSION, '2.1.1', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'upgrade_required' ) );
			return false;
		}
		
		add_filter('manage_edit-page_columns', array( $this, 'pl_add_new_page_columns' ) );
		add_action('manage_page_posts_custom_column', array( $this, 'pl_manage_page_columns') , 10, 2 );
		
		add_filter('manage_edit-post_columns', array( $this, 'pl_add_new_page_columns' ) );
		add_action('manage_post_posts_custom_column', array( $this, 'pl_manage_page_columns' ), 10, 2 );
		add_action('admin_footer-edit.php', array( $this, 'pl_custom_bulk_admin_footer') );
		add_action('load-edit.php', array( $this, 'pl_custom_bulk_action') );
		add_action('admin_notices', array( $this, 'pl_custom_bulk_admin_notices') );
		add_filter( 'manage_edit-post_sortable_columns', array( $this, 'sortable_columns' ) );
		add_filter( 'manage_edit-page_sortable_columns', array( $this, 'sortable_columns' ) );
	}
	
	function sortable_columns( $sortable_columns ) {
		$sortable_columns[ 'pl-template' ] = 'pl-template';
		return $sortable_columns;
	}
	
	function upgrade_required() {
		echo '<div class="updated fade"><p>DMS Tools: Bulk edit functions require version 2.1.1 of DMS</p></div>';
	}
	
	function pl_manage_page_columns($column_name, $id) {

		switch ($column_name) {
			case 'template':
				return;
			case 'pl-template':
				$set = pl_meta($id, PL_SETTINGS);
				echo ( is_array( $set ) && isset( $set['live']['custom-map']['template']['ctemplate'] ) ) ? $set['live']['custom-map']['template']['ctemplate'] : 'None Set';
			break;
		} // end switch
	}

	function pl_add_new_page_columns($columns) {
		$columns['pl-template'] = __( 'PL Template', 'pagelines' );
		return $columns;
	}
	
	function pl_custom_bulk_admin_notices() {

		global $post_type, $pagenow;

		if($pagenow == 'edit.php' && ( $post_type == 'page' || $post_type == 'post' ) && isset($_REQUEST['pl-template']) && (int) $_REQUEST['pl-template']) {

			if( ! isset( $_REQUEST['selected-template-name'] ) || '' == $_REQUEST['selected-template-name'] ) {
				$message = sprintf( __( 'The PageLines DMS Template has been reset on <strong>%s</strong> pages.', 'pagelines' ), number_format_i18n( $_REQUEST['pl-template'] ) );
			} else {
				$name = $_REQUEST['selected-template-name'];
				$message = sprintf( __( 'The PageLines DMS Template <strong>"%s"</strong> has been applied to <strong>%s</strong> %ss.', 'pagelines' ), $name, number_format_i18n( $_REQUEST['pl-template'] ), $post_type );
			}
			echo "<div class='updated'><p>{$message}</p></div>";
		}
	}
	
	function pl_custom_bulk_action() {

		$wp_list_table = _get_list_table('WP_Posts_List_Table');
		$action = $wp_list_table->current_action();

		switch($action) {

	    case 'pl-template': 
			$done = 0;
			$post_ids = $_REQUEST['post'];
			$post_type = $_REQUEST['pl-post-type'];
			$template = $_REQUEST['selected-template'];
			if( ! $post_ids || ! $template )
				return false;

			foreach( $post_ids as $post_id ) {
				$set = pl_meta($post_id, PL_SETTINGS);
				$set['live']['custom-map']['template']['ctemplate'] = $template;
				$set['draft']['custom-map']['template']['ctemplate'] = $template;
				update_post_meta( $post_id, PL_SETTINGS, $set );
				if( 'post' == $post_type )
					update_post_meta( $post_id, 'pl_template_mode', 'local' );
				$done++;
			}
			$sendback = add_query_arg( array('pl-template' => $done, 'selected-template-name' => $_REQUEST['selected-template-name'], 'ids' => join(',', $post_ids) ), admin_url( 'edit.php?post_type=' . $post_type ) );
			break;
		default: return;
		}
		wp_redirect($sendback);
		exit();
	}
	
	function pl_custom_bulk_admin_footer() {

		global $post_type;

		$custom_template_handler = new PLCustomTemplates;
		if($post_type == 'page' || $post_type == 'post' ) {

		$templates = $custom_template_handler->get_all();

		ob_start(); ?>

		<select class="pl-template-selecter">
			<?php
			printf('<option class="pl-template-select" value="none">%s</option>', __( 'Select a PageLines Template', 'pagelines' ) );
			printf('<option class="pl-template-select" value="none">%s</option>', __( 'Unset Current Template', 'pagelines' ) );
			foreach( $custom_template_handler->get_all() as $index => $t){				
				printf('<option class="pl-template-select" data-nicename="%s" value="%s">%s</option>', $t['name'], $index, $t['name']);			
			}
			?>
		</select>	
		<?php 
		$select = str_replace( "\n", '', ob_get_clean() );
	    ?>
	    <script type="text/javascript">
	      jQuery(document).ready(function() {
	        jQuery('<option>').val('pl-template').text('<?php _e( 'Apply Template', 'pagelines' );?>').appendTo("select[name='action']");
	       	jQuery('<option>').val('pl-template').text('<?php _e( 'Apply Template', 'pagelines' );?>').appendTo("select[name='action2']");
			jQuery('<input type="hidden" class="selected-template" name="selected-template" value="none" />').appendTo( '#posts-filter')
			jQuery('<input type="hidden" class="selected-template-name" name="selected-template-name" value="none" />').appendTo( '#posts-filter')
			jQuery('<input type="hidden" class="pl-post-type" name="pl-post-type" value="<?php echo $post_type; ?>" />').appendTo( '#posts-filter')
	      });
		jQuery('.bulkactions').after('<?php echo $select; ?>')

		jQuery( '.pl-template-selecter').each( function(e) {
			jQuery(this).on('change', function() {
				var sel = jQuery(this).val()
				var name = jQuery('option:selected', this).attr('data-nicename');
				jQuery('.selected-template').val(sel)
				jQuery('.selected-template-name').val(name)
			})
		})		
	    </script>
	    <?php
	  }
	}
}