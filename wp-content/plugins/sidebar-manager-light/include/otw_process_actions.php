<?php
/**
 * Process otw actions
 *
 */

if( isset( $_POST['otw_sml_action'] ) ){
	
	require_once( ABSPATH . WPINC . '/pluggable.php' );
	
	switch( $_POST['otw_sml_action'] ){
		
		case 'delete_otw_sidebar':
				if( isset( $_POST['cancel'] ) ){
					wp_redirect( 'admin.php?page=otw-sml' );
				}else{
					
					$otw_sidebars = get_option( 'otw_sidebars' );
					
					if( isset( $_GET['sidebar'] ) && isset( $otw_sidebars[ $_GET['sidebar'] ] ) ){
						$otw_sidebar_id = $_GET['sidebar'];
						
						$new_sidebars = array();
						
						//remove the sidebar from otw_sidebars
						foreach( $otw_sidebars as $sidebar_key => $sidebar ){
						
							if( $sidebar_key != $otw_sidebar_id ){
							
								$new_sidebars[ $sidebar_key ] = $sidebar;
							}
						}
						update_option( 'otw_sidebars', $new_sidebars );
						
						//remove sidebar from widget
						$widgets = get_option( 'sidebars_widgets' );
						
						if( isset( $widgets[ $otw_sidebar_id ] ) ){
							
							$new_widgets = array();
							foreach( $widgets as $sidebar_key => $widget ){
								if( $sidebar_key != $otw_sidebar_id ){
								
									$new_widgets[ $sidebar_key ] = $widget;
								}
							}
							update_option( 'sidebars_widgets', $new_widgets );
						}
						
						wp_redirect( admin_url( 'admin.php?page=otw-sml&message=2' ) );
					}else{
						wp_die( __( 'Invalid sidebar' ) );
					}
				}
			break;
		case 'manage_otw_sidebar':
				global $validate_messages, $wp_sml_int_items;
				
				$otw_widget_settings = get_option( 'otw_widget_settings' );
				
				$validate_messages = array();
				$valid_page = true;
				if( !isset( $_POST['sbm_title'] ) || !strlen( trim( $_POST['sbm_title'] ) ) ){
					$valid_page = false;
					$validate_messages[] = __( 'Please type valid sidebar title' );
				}
				if( !isset( $_POST['sbm_replace'] ) || !strlen( trim( $_POST['sbm_replace'] ) ) ){
					$valid_page = false;
					$validate_messages[] = __( 'Please select sidebar to replace' );
				}
				if( $valid_page ){
					
					$otw_sidebars = get_option( 'otw_sidebars' );
					
					if( !is_array( $otw_sidebars ) ){
						$otw_sidebars = array();
					}
					$items_to_remove = array();
					if( isset( $_GET['sidebar'] ) && isset( $otw_sidebars[ $_GET['sidebar'] ] ) ){
						$otw_sidebar_id = $_GET['sidebar'];
						$sidebar = $otw_sidebars[ $_GET['sidebar'] ];
						$items_to_remove = $sidebar['validfor'];
					}else{
						$sidebar = array();
						$otw_sidebar_id = false;
					}
					
					$sidebar['title'] = (string) $_POST['sbm_title'];
					$sidebar['description'] = (string) $_POST['sbm_description'];
					$sidebar['replace'] = (string) $_POST['sbm_replace'];
					$sidebar['status'] = 'active';
					$sidebar['widget_alignment'] = 'vertical';
					
					//save selected items
					$otw_sbi_items = array_keys( $wp_sml_int_items );
					
					foreach( $otw_sbi_items as $otw_sbi_item ){
						
						if( isset( $items_to_remove[ $otw_sbi_item ] ) ){
							unset( $items_to_remove[ $otw_sbi_item ] );
						}
						
						if( isset( $_POST['otw_smb_'.$otw_sbi_item.'_validfor'] ) && strlen( trim( $_POST['otw_smb_'.$otw_sbi_item.'_validfor'] ) ) ){
							
							$sidebar['validfor'][ $otw_sbi_item ] = array();
							
							$posted_items = explode( ',', $_POST['otw_smb_'.$otw_sbi_item.'_validfor'] );
							
							foreach( $posted_items as $item_id ){
								$sidebar['validfor'][ $otw_sbi_item ][ $item_id ] = array();
								$sidebar['validfor'][ $otw_sbi_item ][ $item_id ]['id'] = $item_id;
							}
							
						}else{
							$sidebar['validfor'][ $otw_sbi_item ] = array();
						}
					}
					
					//remove any not selected items
					if( is_array( $items_to_remove ) && count( $items_to_remove ) ){
					
						foreach( $items_to_remove as $item_type => $item_data ){
							
							foreach( $item_data as $item_id => $item_info ){
								if( isset( $sidebar['validfor'][ $item_type ][ $item_id ] ) ){
									unset( $sidebar['validfor'][ $item_type ][ $item_id ] );
								}
							}
						}
					}
					
					if( $otw_sidebar_id === false ){
						
						$otw_sidebar_id = 'otw-sidebar-'.( get_next_otw_sml_sidebar_id() );
						$sidebar['id'] = $otw_sidebar_id;
					}
					$otw_sidebars[ $otw_sidebar_id ] = $sidebar;
					
					update_option( 'otw_sidebars', $otw_sidebars );
					
					wp_redirect( 'admin.php?page=otw-sml&message=1' );
				}
			break;
	}
}
function get_next_otw_sml_sidebar_id(){

	$next_id = 0;
	$existing_sidebars = get_option( 'otw_sidebars' );
	
	if( is_array( $existing_sidebars ) && count( $existing_sidebars ) ){
	
		foreach( $existing_sidebars as $key => $s_data ){
			
			if( preg_match( "/^otw\-sidebar\-([0-9]+)$/", $key, $matches ) ){
			
				if( $matches[1] > $next_id ){
					$next_id = $matches[1];
				}
			}
		}
	}
	return $next_id + 1;
}
?>