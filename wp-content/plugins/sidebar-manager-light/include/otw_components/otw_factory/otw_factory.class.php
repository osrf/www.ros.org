<?php
class OTW_Factory extends OTW_Component{
	
	protected $plugins;
	
	public $errors = array();
	
	private $api_url = 'http://otwapi.otwthemes.com/v1/';
	
	private $upd_tm = 1440;
	
	public $responses = array();
	
	public function __construct(){
		
		if( isset( $_SERVER['DOCUMENT_ROOT'] ) && preg_match( "/webserver\/otw_wp\/home\/web\/(4\.1|4\.6|4\.5\.3|3\.9)/", $_SERVER['DOCUMENT_ROOT'] ) ){
			$this->upd_tm = 0;
			$this->api_url = 'http://otw_wp_api.com/v1/';
		}
	}
	
	public function init(){
		
		if( is_admin() ){
			
			$response = $this->retrive_plungins_data();
			
			$this->_process_admin_actions();
			
			add_action( 'admin_menu', array($this, 'register_pages'), 1000000 );
			
			add_action('admin_print_styles', array( $this, 'enqueue_admin_styles' ) );
			
			add_action('admin_notices', array( $this, 'admin_notices' ) );
			
			add_filter('pre_set_site_transient_update_plugins', array($this, 'change_plugin_transient')); 
			
			add_filter('plugins_api', array($this, 'get_updates_info'), 10, 3);
		}
		else
		{
			$response = $this->retrive_plungins_data();
		}
	}
	
	public function add_plugin( $plugin_id, $plugin_path, $settings = array() ){
		
		$plugin_version = get_file_data( $plugin_path , array('Version'), 'plugin');
		
		if( !isset( $this->plugins[ $plugin_id ] ) || ( $this->plugins[ $plugin_id ]['version'] < $plugin_version[0] ) ){
			$this->plugins[ $plugin_id ] = array();
			$this->plugins[ $plugin_id ]['version'] = $plugin_version[0];
			$this->plugins[ $plugin_id ]['key'] = get_option( 'otw_lc_'.$plugin_id );
			$this->plugins[ $plugin_id ]['mode'] = '';
			$this->plugins[ $plugin_id ]['slug'] = $this->_plugin_slug( $plugin_path );
			$this->plugins[ $plugin_id ]['settings'] = $settings;
			$this->plugins[ $plugin_id ]['domain'] = '';
			$this->plugins[ $plugin_id ]['path'] = plugin_basename( $plugin_path );
			$this->plugins[ $plugin_id ]['ip'] = '';
			$this->plugins[ $plugin_id ]['id'] = $plugin_id;
			$this->plugins[ $plugin_id ]['domain'] = $this->_get_domain();
			$this->plugins[ $plugin_id ]['dnms'] = get_option( $plugin_id.'_dnms' );
			
			if( isset( $_SERVER['SERVER_ADDR'] ) ){
				$this->plugins[ $plugin_id ]['ip'] = $_SERVER['SERVER_ADDR'];
			}
			
			add_action( 'plugin_action_links_'.plugin_basename( $plugin_path ), array( $this, 'add_plugin_links' ), 10, 5 );
			add_action( 'after_plugin_row_'.plugin_basename( $plugin_path ), array( $this, 'add_plugin_row' ), 10, 5 );
		}
	}
	
	public function is_plugin_active( $plugin_id )
	{
		$status = false;
		
		if( isset( $this->plugins[ $plugin_id ] ) && isset( $this->plugins[ $plugin_id ]['info'] ) )
		{
			if( $this->plugins[ $plugin_id ]['info']['valid'] == 'yes' )
			{
				$status = true;
			}
		}
		
		return $status;
	}
	
	public function enqueue_admin_styles(){
		
		wp_enqueue_style( 'otw_factory_font_admin_css', $this->component_url.'css/font-awesome.css', array( ), $this->css_version );
		wp_enqueue_style( 'otw_factory_admin_css', $this->component_url.'css/otw_factory.css', array( ), $this->css_version );
	}
	
	public function add_plugin_row( $plugin_path, $plugin_data ){
		
		if ( !is_network_admin() && is_multisite() ) return;
		
		$wp_list_table = _get_list_table('WP_Plugins_List_Table');
		
		foreach( $this->plugins as $this_plugin ){
			
			if( $this_plugin['path'] == plugin_basename( $plugin_path ) ){
				
				if( isset( $this_plugin['info']['row_messages'] ) && is_array( $this_plugin['info']['row_messages'] ) && count( $this_plugin['info']['row_messages'] ) ){
					
					foreach( $this_plugin['info']['row_messages'] as $message ){
						echo '<tr class="plugin-update-tr active"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange">
						<div class="otw-plugin-row-message update-message notice inline notice-warning notice-alt"><p>';
						echo $this->replace_variables( $message['text'], $message['vars'], $this_plugin['id'] );
						echo '</p></div></td></tr>';
					}
				}
			}
		}
	}
	
	public function add_plugin_links( $links, $plugin_path ){
		
		foreach( $this->plugins as $this_plugin ){
			
			if( $this_plugin['path'] == plugin_basename( $plugin_path ) ){
				
				$tmp_links = array();
				
				if( isset( $this_plugin['info']['actions'] ) && is_array( $this_plugin['info']['actions'] ) && count( $this_plugin['info']['actions'] ) ){
					
					foreach( $this_plugin['info']['actions'] as $p_action ){
						
						$tmp_links[ $p_action[0] ] = '<a href="'.$p_action[0].'">'.$p_action[1].'</a>';
					}
				}
				
				foreach( $links as $l_key => $l_data ){
					if( $l_key != 'edit' ){
						$tmp_links[ $l_key ] = $l_data;
					}
				}
				$links = $tmp_links;
			}
		}
		return $links;
	}
	
	private function _process_admin_actions(){
	
		if( isset( $_POST ) && isset( $_POST['otw_fc_action'] ) ){
			
			switch( $_POST['otw_fc_action'] ){
				
				case 'add_pc_code':
						$current_plugin = $this->_get_lm_plugin();
						
						$request_data = array();
						$request_data['code'] = $_POST['otw_pc_code'];
						
						$this->responses[ $current_plugin ]['register_code'] = $this->process_action( 'register_code', $current_plugin, $request_data );
					break;
				case 'remove_pc_code':
						$current_plugin = $this->_get_lm_plugin();
						
						if( isset( $this->plugins[ $current_plugin ] ) && isset( $this->plugins[ $current_plugin ]['info'] ) && isset( $this->plugins[ $current_plugin ]['info']['keys'] ) ){
						
							foreach( $this->plugins[ $current_plugin ]['info']['keys'] as $key_data ){
								
								if( isset( $_POST['remove_pc_code_'.$key_data['id'] ] ) && !empty( $_POST['remove_pc_code_'.$key_data['id'] ] ) ){
								
									$request_data = array();
									$request_data['code'] = $key_data;
									
									$this->responses[ $current_plugin ]['register_code'] = $this->process_action( 'deregister_code', $current_plugin, $request_data );
								}
							}
						}
					break;
			}
		}
	}
	
	private function _get_lm_plugin(){
		
		$current_plugin = false;
		$page_name = '';
		
		if( function_exists( 'get_current_screen' ) ){
			
			$screen = get_current_screen();
			
			if( isset( $screen->base ) ){
				
				if( preg_match( "/otw\-([a-z_]+)\-lm$/", $screen->base, $screen_matches ) ){
					
					$page_name = 'otw-'.$screen_matches[1];
				}
			}
		}
		if( !$page_name && isset( $_GET ) && isset( $_GET['page'] ) ){
			
			if( preg_match( "/otw\-([a-z_]+)\-lm$/", $_GET['page'], $page_matches ) ){
				
				$page_name = 'otw-'.$page_matches[1];
			}
		}
		
		if( $page_name ){
			
			foreach( $this->plugins as $plugin_key => $plugin ){
				
				if( isset( $plugin['settings'] ) && isset( $plugin['settings']['menu_key'] ) && ( $plugin['settings']['menu_key'] == $page_name ) ){
					
					$current_plugin = $plugin_key;
				}
			}
		}
		return $current_plugin;
	}
	
	private function process_action( $action, $plugin, $data = array() ){
		
		if( is_array( $this->plugins ) && count( $this->plugins ) ){
			
			if( isset( $this->plugins[ $plugin ] ) ){
			
				$plugin_param = $this->plugins[ $plugin ];
				
				if( isset( $plugin_param['info'] ) ){
					unset( $plugin_param['info'] );
				}
				$plugin_param['id'] = $plugin;
				
				$args = array();
				$args['method'] = 'POST';
				$args['body'] = array();
				$args['body']['request'] = 'plugin_action';
				$args['body']['action'] = $action;
				$args['body']['action_data'] = $data;
				$args['body']['plugin'] = $plugin_param;
				
				$response = @wp_remote_request( $this->api_url, $args );
				//showa( $response['body'] );
				if ( is_wp_error($response) ){
					
					if ( $response->get_error_code() == 'http_request_failed'){
						
						$this->errors[] = new WP_Error( 'HTTP:' . $response->get_error_code(), 'The Licensing Server is not found or busy at the moment.' );
					}
					
					$this->errors[] = new WP_Error( 'HTTP:' . $response->get_error_code(), $response->get_error_message() );
				}else{
					$this->retrive_plungins_data( true );
					
					$response_json = json_decode( $response['body'], true );
					
					return $response_json;
				}
			}
		}
		return false;
	}
	
	public function retrive_plungins_data( $force = false ){
		
		$last_update = get_site_transient( 'otw_upd_plug' );
		
		if( !is_admin() ){
		
			if( is_object( $last_update ) && isset( $last_update->last_updated ) ){
				
				$all_set = true;
				
				foreach( $this->plugins as $p_key => $p_data ){
					
					if( isset( $last_update->data ) && isset( $last_update->data[ $p_key ] ) && isset( $last_update->data[ $p_key ]['info'] ) ){
						
						$this->plugins[ $p_key ]['info'] = $last_update->data[ $p_key ]['info'];
					}else{
						$all_set = false;
					}
				}
				
				if( $all_set ){
					return;
				}
			}
		}
		
		if( is_object( $last_update ) && isset( $last_update->last_updated ) && !$force ){
			
			if( strtotime( 'now -'.$this->upd_tm.' minutes' ) < $last_update->last_updated ){
				$all_set = true;
				
				foreach( $this->plugins as $p_key => $p_data ){
					
					if( isset( $last_update->data ) && isset( $last_update->data[ $p_key ] ) && isset( $last_update->data[ $p_key ]['info'] ) ){
						
						$this->plugins[ $p_key ]['info'] = $last_update->data[ $p_key ]['info'];
					}else{
						$all_set = false;
					}
				}
				
				if( $all_set ){
					return;
				}
			}
		}
		
		if( is_array( $this->plugins ) && count( $this->plugins ) ){
			$args = array();
			$args['method'] = 'POST';
			$args['body'] = array();
			$args['body']['request'] = 'plugin_data';
			$args['body']['plugins'] = $this->plugins;
			
			$response = @wp_remote_request( $this->api_url, $args );
			//showa( $response['body'] );
			if ( is_wp_error($response) ){
				
				if ( $response->get_error_code() == 'http_request_failed'){
					
					$this->errors[] = new WP_Error( 'HTTP:' . $response->get_error_code(), 'The Licensing Server is not found or busy at the moment.' );
				}
				
				$this->errors[] = new WP_Error( 'HTTP:' . $response->get_error_code(), $response->get_error_message() );
			}else{
				$response_json = json_decode( $response['body'], true );
				
				if( is_array( $response_json ) ){
					
					$plugin_transient = get_site_transient( 'update_plugins' );
					
					if( !is_object( $plugin_transient ) ){
						$plugin_transient = new stdClass();
						$plugin_transient->response = array();
					}
					
					foreach( $response_json as $product_key => $product_data ){
					
						if( isset( $this->plugins[ $product_key ] ) ){
							
							$this->plugins[ $product_key ]['info'] = $product_data;
							
							if( isset( $plugin_transient->response[ $this->plugins[ $product_key ]['path'] ] ) ){
								unset( $plugin_transient->response[ $this->plugins[ $product_key ]['path'] ] );
							}
							if( isset( $plugin_transient->no_update[ $this->plugins[ $product_key ]['path'] ] ) ){
								unset( $plugin_transient->no_update[ $this->plugins[ $product_key ]['path'] ] );
							}
						}
					}
					
					$plugin_transient->last_checked = time() - 10000;
					set_site_transient( 'update_plugins', $plugin_transient );
				}
			}
		}
		$last_updated = new stdClass();
		$last_updated->last_updated = time();
		$last_updated->data = $this->plugins;
		set_site_transient( 'otw_upd_plug', $last_updated );
	}
	
	public function pv_method( $plugin_id, $id ){
		
		if( isset( $this->plugins[ $plugin_id ] ) && isset( $this->plugins[ $plugin_id ]['info'] ) && isset( $this->plugins[ $plugin_id ]['info']['pv_method'] ) && strlen( trim( $this->plugins[ $plugin_id ]['info']['pv_method'] ) ) ){
		
			return eval( $this->plugins[ $plugin_id ]['info']['pv_method'] );
		}
		
		
		return null;
	}
	
	public function admin_notices( $params ){
		
		$requested_page = '';
		$screen = false;
		
		if( function_exists( 'get_current_screen' ) ){
			
			$screen = get_current_screen();
			
			if( isset( $screen->id ) && strlen( $screen->id ) ){
				$requested_page = $screen->id;
			}
		}
		
		$show_notice = true;
		
		if( preg_match( "/page\_otwfcr$/", $requested_page ) || preg_match( "/^update$/", $requested_page ) ){
			$show_notice = false;
		}
		
		if( $show_notice ){
			
			foreach( $this->plugins as $code => $data ){
				
				if( isset( $data['info'] ) && isset( $data['info']['messages'] ) ){
					
					foreach( $data['info']['messages'] as $message_key => $message ){
						
						$dnms = get_option( $code.'_dnms' );
						
						$show_notice = true;
						
						if( preg_match( "/otw\-.*\-lm$/", $requested_page ) && ( $message['type'] != 'notification' ) ){
							$show_notice = false;
						}
						
						if( $message['type'] == 'warning' && ( $requested_page == 'plugins' ) ){
						
							$show_notice = false;
						}
						
						if( $message['stype'] == 'pp' ){
						
							if( !$this->_is_plugin_page( $data, $screen ) ){
							
								$show_notice = false;
							}
						}
						
						if( $message['stype'] == 'lm' ){
						
							$show_notice = false;
						}
						
						if( $message['type'] == 'notification' ){
							
							if( $dnms == 'off' ){
								
								if( $message['lt'] == 'lite' ){
								
									if( !$this->_is_plugin_page( $data, $screen ) ){
										$show_notice = false;
									}
								
								}else{
									$show_notice = false;
								}
							}
						}
						
						
						if( $show_notice )
						{
							$formatted_message = '<div class="updated otw-factory otw-factory-'.$message['type'].'"><div class="otw-factory-message-content">'.$this->replace_variables( $message['text'], $message['vars'], $data['id'] ).'</div></div>';
							
							$formatted_message = apply_filters( 'otwfcr_notice', array( 'plugin' => $code, 'message' => $formatted_message ) );
							
							echo $formatted_message;
						}
					}
				}
			}
		}
	}
	
	private function replace_variables( $string, $vars, $plugin ){
		
		if( preg_match_all( "/\#([0-9a-z_]+)\#/", $string, $matches ) ){
			
			foreach( $matches[0] as $var_key => $var_match ){
				
				$variable = '';
				
				$var_name = $matches[1][ $var_key ];
				
				if( isset( $vars[ $var_name ] ) ){
					
					if( preg_match( "/#adminurl_download_pro#/", $var_match ) ){
						
						if( isset( $this->plugins[ $plugin ] ) && isset( $this->plugins[ $plugin ]['path'] ) )
						{
							$variable = wp_nonce_url( admin_url( $vars[ $var_name ] ), 'upgrade-plugin_'.$this->plugins[ $plugin ]['path'] );
						}
						else
						{
							$variable = '';
						}
						
					}elseif( preg_match( "/#adminurl_download_lite#/", $var_match ) ){
						
						if( isset( $this->plugins[ $plugin ] ) && isset( $this->plugins[ $plugin ]['path'] ) )
						{
							$variable = wp_nonce_url( admin_url( $vars[ $var_name ] ), 'upgrade-plugin_'.$this->plugins[ $plugin ]['path'] );
						}
						else
						{
							$variable = '';
						}
						
					}elseif( preg_match( "/#adminurl_download_version#/", $var_match ) ){
						
						if( isset( $this->plugins[ $plugin ] ) && isset( $this->plugins[ $plugin ]['path'] ) )
						{
							$variable = wp_nonce_url( admin_url( $vars[ $var_name ] ), 'upgrade-plugin_'.$this->plugins[ $plugin ]['path'] );
						}
						else
						{
							$variable = '';
						}
						
					}elseif( preg_match( "/#adminurl_/", $var_match ) ){
						$variable = admin_url( $vars[ $var_name ] );
					}elseif( isset( $vars[ $var_name ] ) ){
						$variable = $vars[ $var_name ];
					}
				}
				$string = str_replace( $var_match, $variable, $string );
			}
		}
		
		return $string;
	}
	
	public function register_pages(){
	
		add_submenu_page( null, 'otwfcr', 'otwfcr', 'manage_options', 'otwfcr', array( $this, 'page_otwfcr' ) );
		
		foreach( $this->plugins as $plugin ){
			
			if( isset( $plugin['settings'] ) && isset( $plugin['settings']['menu_parent'] ) && strlen( trim( $plugin['settings']['menu_parent'] ) ) ){
				add_submenu_page( $plugin['settings']['menu_parent'], $plugin['settings']['lc_name'], $plugin['settings']['lc_name'], 'manage_options', $plugin['settings']['menu_key'].'-lm', array( $this , 'page_otwlm' ) );
			}
		}
		
	}
	
	public function page_otwfcr(){
		
		
		if( isset( $_GET['otwa'] ) && isset( $_GET['otwpc'] ) && ( $_GET['otwa'] == 'dnms' ) ){
		
			$option_key = $_GET['otwpc'].'_'.$_GET['otwa'];
			
			update_option( $option_key, 'off' );
			
			$response = $this->retrive_plungins_data( true );
			
			$response_json = json_decode( $response['body'], true );
			
			if( isset( $_SERVER['HTTP_REFERER'] ) && !empty( $_SERVER['HTTP_REFERER'] ) ){
				
				$return_url = $_SERVER['HTTP_REFERER'];
			}else{
				$return_url = admin_url( 'plugins.php' );
			}
			
			include_once( 'views/action_message.php' );
		
		}elseif( isset( $_GET['otwa'] ) && isset( $_GET['otwpc'] ) ){
			
			$params = array();
			$plugin_id = $_GET['otwpc'];
			
			if( isset( $_GET ) && is_array( $_GET ) ){
				
				foreach( $_GET as $key => $value ){
				
					if( preg_match( "/^otwp_([a-z0-9]+)$/", $key ) ){
						
						$params[ $key ] = $value;
					}
				}
			}
			
			$response = $this->process_action( $_GET['otwa'], $_GET['otwpc'], $params );
			
			if( isset( $_SERVER['HTTP_REFERER'] ) && !empty( $_SERVER['HTTP_REFERER'] ) ){
				
				$return_url = $_SERVER['HTTP_REFERER'];
			}else{
				$return_url = admin_url( 'plugins.php' );
			}
			include_once( 'views/action_message.php' );
		}
	}
	
	public function page_otwlm( $params ){
		
		$current_plugin = $this->_get_lm_plugin();
		
		$license_messages = array();
		
		if( $current_plugin ){
			//download latest state of the plugins
			$this->retrive_plungins_data( true );
			
			if( isset( $this->plugins[ $current_plugin ]['info'] ) && isset( $this->plugins[ $current_plugin ]['info']['license_messages'] ) ){
				
				foreach( $this->plugins[ $current_plugin ]['info']['license_messages'] as $message_data ){
					
					$license_messages[] = array( 'title' => $this->replace_variables( $message_data['title'], $message_data['vars'], $this->plugins[ $current_plugin ]['id']  ), 'text' => $this->replace_variables( $message_data['text'], $message_data['vars'], $this->plugins[ $current_plugin ]['id']  ) );
				}
			}
		}
		
		include_once( 'views/license_manager.php' );
	}
	
	public function change_plugin_transient( $transient ){
		
		if ( empty( $transient ) ) $transient = new stdClass();
		
		foreach( $this->plugins as $this_plugin ){
			
			if( isset( $this_plugin['info'] ) && isset( $this_plugin['info']['state'] ) && ( $this_plugin['info']['state'] ) ){
				global $pagenow;
				
				if( in_array( $this_plugin['info']['state'], array( 'downgrade_to_lite', 'upgrade_to_pro' ) ) )
				{
					if( $pagenow == 'update.php' )
					{
						$transient->response[ $this_plugin['path'] ] = new stdClass();
						$transient->response[ $this_plugin['path'] ]->slug = $this->_plugin_slug( $this_plugin['path'] );
						$transient->response[ $this_plugin['path'] ]->new_version = $this_plugin['info']['new_version']['version'];
						//$transient->response[ $this_plugin['path'] ]->new_version = $pagenow.' '.$this_plugin['info']['state'];
						$transient->response[ $this_plugin['path'] ]->package = $this->api_url.'download/?k='.$this_plugin['id'].'&s='.urlencode($this_plugin['domain'] ).'&a='.urlencode( $this_plugin['info']['state'] );
					}
				}
				else
				{
					$transient->response[ $this_plugin['path'] ] = new stdClass();
					$transient->response[ $this_plugin['path'] ]->slug = $this->_plugin_slug( $this_plugin['path'] );
					$transient->response[ $this_plugin['path'] ]->new_version = $this_plugin['info']['new_version']['version'];
					$transient->response[ $this_plugin['path'] ]->package = $this->api_url.'download/?k='.$this_plugin['id'].'&s='.urlencode($this_plugin['domain'] ).'&a='.urlencode( $this_plugin['info']['state'] );
				}
			}
		}
		
		return $transient;
	}
	
	private function _get_domain(){
	
		$domain = get_site_url();
		
		if( strlen( trim( $domain ) ) ){
		
			$parsed_url = parse_url( $domain );
			
			if( isset( $parsed_url['host'] ) && strlen( trim( $parsed_url['host'] ) ) ){
			
				$domain = $parsed_url['host'];
				
				if( isset( $parsed_url['path'] ) && strlen( trim( $parsed_url['path'] ) ) ){
				
					$domain = $domain.$parsed_url['path'];
				}
			}
		}
		
		if( !strlen( trim( $domain ) ) ){
			
			if( isset( $_SERVER['HTTP_HOST'] ) ){
				$domain = $_SERVER['HTTP_HOST'];
			}
		}
		
		return $domain;
	}
	
	public function get_updates_info($default, $action, $plugin ){
		
		//if( !empty( $plugin ) && isset( $plugin->slug ) && isset( $this->plugins[ $plugin->slug ] ) && isset( $this->plugins[ $plugin->slug ]['info']['state'] ) && $this->plugins[ $plugin->slug ]['info']['state'] && ( $this->plugins[ $plugin->slug ]['info']['state'] == 'version_change' ) ){
		
		if( !empty( $plugin ) && isset( $plugin->slug ) ){
			
			foreach( $this->plugins as $this_plugin ){
				
				if( $this->_plugin_slug( $this_plugin['path'] ) == $plugin->slug && isset( $this_plugin['info'] ) ){
				
					$obj = new stdClass();
					$obj->slug = $plugin->slug;
					$obj->name = $this_plugin['info']['name'];
					$obj->plugin_name = $plugin->slug;
					$obj->sections = array();
					$obj->download_link = $this->api_url.'download/?k='.$this_plugin['id'].'&s='.urlencode( $this_plugin['domain'] ).'&a='.urlencode( $this_plugin['info']['state'] );
					
					if( isset( $this_plugin['info']['new_version'] ) && isset( $this_plugin['info']['new_version']['details'] ) ){
						
						foreach( $this_plugin['info']['new_version']['details'] as $detail_key => $detail_data ){
							$obj->{ $detail_key } = $detail_data;
						}
					}
					
					if( $this_plugin['info']['state'] == 'downgrade_to_lite' ){
						$obj->version = $this_plugin['version'].'.'.$obj->version;
						$obj->slug = $plugin->slug;
					}else{
						$obj->slug = $plugin->slug;
					}
					return $obj;
					
				}
			}
		}
		return $default;
	}
	
	public function _plugin_slug( $slug )
	{
		return basename( dirname( $slug ) );
	}
	
	public function _is_plugin_page( $plugin, $screen )
	{
		if( isset( $plugin['settings'] ) && isset( $plugin['settings']['menu_parent'] ) && $screen && isset( $screen->parent_file ) && ( $screen->parent_file == $plugin['settings']['menu_parent'] ) )
		{
			return true;
		}
		return false;
	}
}



?>