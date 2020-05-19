<?php
/**
 * hook function for activation of the plugin
 */
function otw_sml_plugin_activate(){
	
	if( function_exists( 'otwrem_is_active_sidebar' ) ){
		return;
	}
	
	$error = '';
	if( !file_exists( ABSPATH.'/'.WPINC.'/widgets.php' ) ){
		$error =  ABSPATH."/".WPINC."/widgets.php Doesn't exists. The plugin may not work property.";
	}else{
		$sidebar_content = file_get_contents(ABSPATH.'/'.WPINC.'/widgets.php');
		
		if( !preg_match_all( "/tion\s+is_active_sidebar/", $sidebar_content, $matches) && !preg_match_all( "/tion\s+otwrem_is_active_sidebar/", $sidebar_content, $matches ) ){
			$error = "function is_active_sidebar() is not located at ".WPINC."/widgets.php. The plugin may not work property.";
		}elseif( !preg_match_all( "/tion\s+dynamic_sidebar/", $sidebar_content, $matches ) && !preg_match_all( "/tion\s+otwrem_dynamic_sidebar/", $sidebar_content, $matches ) ){
			$error = "function dynamic_sidebar() is not located at ".WPINC."/widgets.php. The plugin may not work property.";
		}else{
			$new_widgets_content = preg_replace( "/tion\s+is_active_sidebar/", "tion otwrem_is_active_sidebar", $sidebar_content );
			$new_widgets_content = preg_replace( "/tion\s+dynamic_sidebar/", "tion otwrem_dynamic_sidebar", $new_widgets_content );
			$fp = @fopen( ABSPATH.'wp-includes/widgets.php', 'w' );
			
			if( !$fp ){
				$error =  "Can not open ".WPINC."/widgets.php for write. The plugin may not work property.";
			}else{
				fwrite( $fp, $new_widgets_content );
				fclose( $fp );
			}
		}
	}
	update_option( 'otw_sml_plugin_error', __( $error ) );
}
/**
 * hook function for deactivation of the plugin
 */
function otw_sml_plugin_deactivate(){
	
	$error = '';
	if( !file_exists( ABSPATH.'/'.WPINC.'/widgets.php' ) ){
		$error =  ABSPATH."/".WPINC."/widgets.php Doesn't exists. The plugin may not work property.";
	}else{
		$sidebar_content = file_get_contents(ABSPATH.'/'.WPINC.'/widgets.php');
		
		if( !preg_match_all( "/tion\s+is_active_sidebar/", $sidebar_content, $matches) && !preg_match_all( "/tion\s+otwrem_is_active_sidebar/", $sidebar_content, $matches ) ){
			$error = "function is_active_sidebar() is not located at ".WPINC."/widgets.php. The plugin may not work property.";
		}elseif( !preg_match_all( "/tion\s+dynamic_sidebar/", $sidebar_content, $matches ) && !preg_match_all( "/tion\s+otwrem_dynamic_sidebar/", $sidebar_content, $matches ) ){
			$error = "function dynamic_sidebar() is not located at ".WPINC."/widgets.php. The plugin may not work property.";
		}else{
		
			if( !preg_match_all( "/tion\s+is_active_sidebar/", $sidebar_content, $matches ) && !preg_match_all( "/tion\s+dynamic_sidebar/", $sidebar_content, $matches )  ){
				
				$new_widgets_content = preg_replace( "/tion\s+otwrem_is_active_sidebar/", "tion is_active_sidebar", $sidebar_content );
				$new_widgets_content = preg_replace( "/tion\s+otwrem_dynamic_sidebar/", "tion dynamic_sidebar", $new_widgets_content );
				
				$fp = @fopen( ABSPATH.'wp-includes/widgets.php', 'w' );
				
				if( !$fp ){
					$error =  "Can not open ".WPINC."/widgets.php for write. The plugin can't be deactivated.";
				}else{
					fwrite( $fp, $new_widgets_content );
					fclose( $fp );
				}
			}
		}
	}
	
	if( $error ){
		wp_die( $error );
	}
}
?>