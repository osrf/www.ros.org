<?php


class PLAccountPanel{

	function __construct(){

		if( pl_use_editor() )
			add_filter( 'pl_toolbar_config', array( $this, 'toolbar' ) );

		add_action( 'wp_ajax_pl_account_actions', array( $this, 'pl_account_actions' ) );
		add_action( 'admin_init', array( $this, 'activation_check_function' ) );
		add_filter('pl_ajax_account', array($this, 'account_testing_function'), 10, 2); 
		
	}
	
	
	function account_testing_function($response, $postdata){

		$run = $postdata['run']; 
		
		add_filter('wp_mail_content_type', array( $this, 'mail_content_type' ) );
		
		if($run == 'email_invites'){
			$invites = $postdata['invites'];
			$link = $postdata['link'];
			$name = $postdata['name'];
			$emails = array();
			$html_email = $this->get_invite_email($link, $name);
			$by_newline = explode ( "\n", $invites );
			
			$title = sprintf('%s invited you to check out PageLines DMS.', $name);
			
			foreach($by_newline as $newline){
				$by_comma = explode ( ",", trim($newline) );
				foreach($by_comma as $eml){
					$emails[] = $eml;
				}
			}
			
			foreach( $emails as $eml ){
				wp_mail($eml, $title, $html_email );
			}
			
		}
		
		// Dont want to mess w standard behavior
		remove_filter('wp_mail_content_type', array( $this, 'mail_content_type' ) );
		
		return $response;

	}
	
	function get_invite_email( $link, $name = '' ){
		ob_start();
			?>

	<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />


			<title>Check out DMS</title>
			<style type="text/css">
				a{
					color: #1996fc;
				}
				hr{
					border: none;
					border-bottom: 1px solid #eee;
					margin: 1.5em 0;
				}
				p{
					padding-left: 10px;
				}
				li{
					margin-bottom: 1em;
				}
			</style>
		</head>
		<body>

			<div style="width: 600px; margin: 0 auto; font-family: helvetica, arial; font-size: 16px; line-height: 1.5em;">

				<div style="padding: 15px;">
					<p>Hey there,</p>
					<p>Your friend <strong><?php echo $name; ?></strong> wants you to try PageLines DMS!</p> 
					<p>DMS is a free "design management system" for websites. It's built on WordPress and lets you build and manage a website easily and professionally using drag and drop.</p>

					<p>Plus, you both get <strong>50 points credit</strong> towards premium stuff if you get started through this email.</p>
				
					<p><a href="<?php echo $link;?>">Accept Invite and Get Points</a></p>
				</div>
			</div>
			</body>
			</html>
			<?php

		$out = ob_get_clean();
		return $out;
	}
	
	function mail_content_type(){
		return 'text/html';
	}

	function activation_check_function() {

		if( defined( 'DOING_AJAX' ) && true == DOING_AJAX )
			return;

		if ( ! current_user_can( 'edit_theme_options' ) )
			return;

		if( ! pl_is_pro() ) // no need if were not activated
			return;

		$data = get_option( 'dms_activation', array( 'active' => false, 'key' => '', 'message' => '', 'email' => '' ) );

		if( ! isset( $data['date'] ) ) {
			$data['date'] = date( 'Y-m-d' );
		}

		if( isset( $data['date'] ) && $data['date'] > date( 'Y-m-d' ) && $data['date'] <= date('Y-m-d', strtotime('+7 days', strtotime( $data['date'] ) ) ) )
			return false;
		else
			$data['date'] = date( 'Y-m-d' );

		$key = (isset($data['key'])) ? $data['key'] : false;
		$email = (isset($data['email'])) ? $data['email'] : false;

		
		$url = sprintf( 'http://www.pagelines.com/index.php?wc-api=software-api&request=%s&product_id=dmspro&licence_key=%s&email=%s&instance=%s', 'check', $key, $email, site_url() );


		$result = wp_remote_get( $url );

		// if wp_error save error and abort.
		if( is_wp_error($result) ) {
			$data['last_error'] = $result->get_error_message();
			update_option( 'dms_activation', $data );
			return false;
		} else {
			$data['last_error'] = '';
			update_option( 'dms_activation', $data );
		}

		// do a couple of sanity checks..
		if( ! is_array( $result ) )
			return false;

		if( ! isset( $result['body'] ) )
			return false;

		$rsp = json_decode( $result['body'] );

		if( ! is_object( $rsp ) )
			return false;

		if( ! isset( $rsp->success ) )
			return false;

		// if success is true means the key was valid, move along nothing to see here.
		if( true == $rsp->success ) {

			$data['date'] = date('Y-m-d', strtotime('+7 days', strtotime( $data['date'] ) ) );
			update_option( 'dms_activation', $data );

			return;
		}

		if( isset( $rsp->error ) && isset( $rsp->code ) ) {
			// lets try again tomorrow
			$data['date'] = date('Y-m-d', strtotime('+1 days', strtotime( $data['date'] ) ) );
			$data['trys'] = ( isset( $data['trys'] ) ) ? $data['trys'] + 1 : 1;
			update_option( 'dms_activation', $data );

			if( $data['trys'] < 3 ) // try 2 times.
				return;

			self::send_email( $rsp, $data );
		}
	}

	function send_email( $rsp, $data ) {

			$data = get_option( 'dms_activation' );
			$key = (isset($data['key'])) ? $data['key'] : '';
			
			$message = sprintf( "The DMS activation key %s failed to authenticate after 2 tries. Please log into your account and check your subscription at https://www.pagelines.com/my-account/\n\nThe keyserver error was: %s", $key, $rsp->error );
			wp_mail( get_bloginfo( 'admin_email' ), 'DMS Activation Failed', $message );
			update_option( 'dms_activation', array() );
	}

	

	function toolbar( $toolbar ){
		$toolbar['account'] = array(
			'name'	=> __( 'DMS', 'pagelines' ),
			'icon'	=> 'icon-pagelines',
			'pos'	=> 110,
			//'type'	=> 'btn-panel',
			'panel'	=> array(
				'heading'	=> "<i class='icon-pagelines'></i> PageLines",
				
				'pl_account'	=> array(
					'name'	=> __( 'Your Account', 'pagelines' ),
					'icon'	=> 'icon-pagelines',
					'call'	=> array( $this, 'pagelines_account'),
				),
				
				'getting_started'	=> array(
					'name'	=> __( 'Getting Started', 'pagelines' ),
					'icon'	=> 'icon-youtube-play',
					'call'	=> array($this, 'getting_started'),
				),
			
				'support'	=> array(
					'name'	=> __( 'Support', 'pagelines' ),
					'icon'	=> 'icon-comments',
					'call'	=> array( $this, 'pagelines_support'),
				),
			)
		);
		if( defined( 'DMS_DISABLE_ACCOUNT_PANEL' ) && DMS_DISABLE_ACCOUNT_PANEL && pl_is_pro() )
			unset( $toolbar['account']['panel']['pl_account']);
		if( defined( 'DMS_DISABLE_KARMA_PANEL' ) && DMS_DISABLE_KARMA_PANEL && pl_is_pro() )
			unset( $toolbar['account']['panel']['get_karma']);			
		return $toolbar;
	}
	
	function remote_key_request( $request, $key, $email ){
		
		$url = sprintf( 
			'http://www.pagelines.com/?wc-api=software-api&request=%s&product_id=dmspro&licence_key=%s&email=%s&instance=%s', 
			$request, 
			$key, 
			$email, 
			site_url() 
		);	
		
		$data = wp_remote_get( $url );

		$rsp = ( ! is_wp_error( $data ) && isset( $data['body'] ) ) ? (array) json_decode( $data['body'] ) : array();
		
		return $rsp;
		
	}
	
	function remote_user_request( $email, $type = 'std' ){
		
		$url = sprintf( 
				'%s&request=public_user&email=%s&type=%s', 
				PL_API_URL,
				$email,
				$type
			);		
		$data = wp_remote_get( $url, array( 'timeout' => 20 ) );		
		$rsp = ( ! is_wp_error( $data ) && isset($data['body'] ) ) ? (array) json_decode( $data['body'] ) : array();		
		return $rsp;
	}
	
	function pl_account_actions() {
		
		$postdata = $_POST;
	
		$key = $postdata['key'];
		$email = $postdata['email'];	
		$reset = ($postdata['reset'] == "true") ? true : false ;
		$update = ($postdata['update'] == "true") ? true : false ;
		
		
		$response = array( 
			'key'	=> $key, 
			'email'	=> $email, 
			'reset'	=> $reset
		);
		$rsp = '';
		
		$default_activation = array( 
						'active' 			=> false,
						'message' 			=> '',  
						'key' 				=> '', 
						'email' 			=> $email, 
						'date'				=> date( 'Y-m-d' ),
						'name'				=> '', 
						'description'		=> '', 
						'karma'				=> '', 
						'lifetime_karma'	=> '', 
						'avatar'			=> '',
						
					);
		
		
		// DEACTIVATION
		
		// grab erroneous output
		ob_start();
		
		$old_activation = get_option( 'dms_activation' ); 

		$old_activation = wp_parse_args( $old_activation, $default_activation);

		$currently_active = $old_activation['active'];
		
		if( $reset && $currently_active ){
			
			$current_key = ( isset( $old_activation['key'] ) ) ? $old_activation['key'] : false;
			$current_email = ( isset( $old_activation['email'] ) ) ? $old_activation['email'] : false;
			
			$rsp = $this->remote_key_request( 'deactivation', $key, $email );
			
			$response['deactivation_response'] = $rsp; 
			
			$response['messages'][] = (isset($rsp['error'])) ? $rsp['error'] : '<i class="icon-remove"></i> Deactivated!';
			$response['messages'][] = (isset($rsp['message'])) ? $rsp['message'] : '';
			$message = ( isset( $rsp[ 'message' ] ) ) ? $rsp[ 'message' ] : '';
			$instance = ( isset( $rsp[ 'instance' ] ) ) ? $rsp[ 'instance' ] : '';
			
			$new = array(
				'key'		=> '',
				'active'	=> false,
				'message'	=> $message,
			); 	
			
			$data_to_store = wp_parse_args( $new, $old_activation ); 
			
			update_option( 'dms_activation', $data_to_store );
			
			$response['refresh'] = true;
		}
		
		// ACCOUNT
		if( $email != '' && ! $reset ){
			
			$new_install = get_option('pl_new_install');
			
			$type = ( !$new_install || $new_install == 'yes' ) ? 'new_activation' : 'std';
			
			$rsp = $this->remote_user_request( $email, $type );
			
			$rsp['email'] = $email; // not passed back on error
			
			// Email doesn't exist
			if( isset($rsp['error']) ){
				
				$rsp['real_user'] = false;
				$updated_user = wp_parse_args( $rsp, $default_activation ); 
			
			} else {
				$rsp['real_user'] = true;
				$updated_user = wp_parse_args( $rsp, $old_activation ); 
				
				update_option('pl_new_install', 'no');
			}
		
			$response[ 'user_data' ] = $updated_user;
			
			$response['messages'][] = (isset($rsp['error'])) ? $rsp['error'] : '<i class="icon-user"></i> User Updated!';
			$response['messages'][] = (isset($rsp['message'])) ? $rsp['message'] : '';
		
			// SET KEY
			
			// ACTIVATION OR DEACTIVATION 
			
				// If currently active, and key is blank that means they want to deactivate
				// If not set and blank, who cares? 
				// If set, and error, update message?
				// If email unset and key, then it will error. I guess deactivate.
				
			
			
			if( $key != '' && ! $currently_active ){
				
				$request = 'activation';
				$response['request'] = $request;
				
				$rsp = $this->remote_key_request( $request, $key, $email );
				
				$response[ 'data' ] = $rsp;
				
				$message = ( isset( $rsp[ 'message' ] ) ) ? $rsp[ 'message' ] : '';
				
				$instance = ( isset( $rsp[ 'instance' ] ) ) ? $rsp[ 'instance' ] : '';
				
				// Set messages for quick JS response 
				$response['messages'][] = (isset($rsp['error'])) ? $rsp['error'] : '<i class="icon-star"></i> Site Activated!';
				$response['messages'][] = (isset($rsp['message'])) ? $rsp['message'] : '';
				
				
				if( isset( $rsp['activated'] ) && $rsp['activated'] == true ){
					
					$new = array(
						'key'		=> $key,
						'active'	=> true,
						'instance'	=> $instance,
						'message'	=> $message,
					); 	
					
					$data_to_store = wp_parse_args( $new, $updated_user ); 
					
					update_option( 'dms_activation', $data_to_store );
					
					
				}
				
				
					
			} else {
				$response['refresh'] = true;
				update_option( 'dms_activation', $updated_user );
			}	
			
		
			
		} elseif( ! $reset ) {
			
			$response['messages'][] = 'No email set.';
				 	
			
		}
		
		if( ! isset( $rsp['error'] ) || $rsp['error'] == '' ){
			$response['refresh'] = true;
		}

		$response['erroneous_output'] = ob_get_clean();
		
		echo json_encode(  pl_arrays_to_objects( $response ) );

		exit();
	}

	function pagelines_karma(){
		$data = $this->get_account_data();
		
		$url = (isset($data['url']) && $data['url'] != '') ? $data['url'] : '';
		$name = (isset($data['name']) && $data['name'] != '') ? $data['name'] : '';
		
		?>
		<h2><i class="icon-sun"></i> <?php _e( 'Get PRO stuff free with Karma.', 'pagelines' ); ?>
		</h2>
		<p><?php _e( "For every friend you invite who joins and installs PageLines, we'll give you and your friend 50 karma points! Karma points can be redeemed as cash with PageLines.", 'pagelines' ); ?>
		</p>
		<div class="row">
			<div class="span4">
				<h4><?php _e( 'Invite Friends by Email', 'pagelines' ); ?>
				</h4>
				<?php if($url != '' && $name != ''): ?>
					<textarea class="karma-email-invites pl-textarea-input" placeholder="<?php _e( 'Add emails (Comma separated)', 'pagelines' ); ?>
					"></textarea>
					<button class="btn btn-primary submit-invites" data-link="<?php echo $url; ?>" data-name="<?php echo $name;?>"><i class="icon-share"></i> <?php _e( 'Invite', 'pagelines' ); ?>
					</button>
				<?php else: ?>
					<a href="#" class="btn" data-tab-link="account" data-stab-link="pl_account"><i class="icon-user"></i><?php _e( ' Add/Update Account Info', 'pagelines' ); ?>
					</a>
					<p><small><?php _e( 'Name and invite link needed.', 'pagelines' ); ?>
					</small></p>
				<?php endif; ?>
			</div>
			<div class="span4">
				<h4><?php _e( 'Your Invite Link', 'pagelines' ); ?>
				</h4>
				<?php if($url != '' ): ?>
					<input type="text" class="pl-text-input" value="<?php echo $url; ?>" />
				<?php else: ?>
					<a href="#" class="btn" data-tab-link="account" data-stab-link="pl_account"><i class="icon-user"></i><?php _e( ' Add/Update Account Info', 'pagelines' ); ?>
					</a>
				<?php endif; ?>
				
				
			</div>
			<div class="span4">
				<?php $this->karma_counter(); ?>
			</div>
		</div>
		<?php
	}

	function karma_counter(){
		$data = $this->get_account_data();
		?>
		<h4><i class="icon-sun"></i><?php _e( ' Your Karma', 'pagelines' ); ?>
		</h4>
		
		<div class="row karma-row">
		
			<div class="span6 kcol">
				<div class="big-karma"><?php echo $data['karma'];?><strong><i class="icon-sun"></i><?php _e( ' Current', 'pagelines' ); ?>
				</strong></div>
			
			</div>
			<div class="span6 kcol">
				<div class="big-karma">
					<?php echo $data['lifetime_karma'];?>
					<strong><i class="icon-sun"></i><?php _e( ' Lifetime', 'pagelines' ); ?>
					</strong>
				</div>
				
			</div>
			
			
		</div>
		<div class="karma-nav">
			<a href="#" data-tab-link="account" data-stab-link="get_karma" class="btn btn-mini btn-primary"><i class="icon-sun"></i> <?php _e( 'Get karma', 'pagelines' ); ?>
			 </a>
			<a href="http://www.pagelines.com/shop/" class="btn btn-mini btn-success"><i class="icon-shopping-cart"></i> <?php _e( 'Use karma', 'pagelines' ); ?>
			 </a>
			<a href="http://www.pagelines.com/the-karma-system/" class="btn btn-mini"><?php _e( 'Learn more about karma', 'pagelines' ); ?>
			 <i class="icon-external-link"></i></a>
		</div>
		
		<?php 
	}
	
	function get_account_data(){
		
		$data = array(
			'email'		=> '', 
			'key'		=> '',
			'message'	=> '', 
			'avatar'	=> '', 
			'name'		=> '',
			'description'	=> '',
			'active'	=> false, 
			'real_user'	=> false,
			'url'		=> '',
			'karma'		=> 0,
			'lifetime_karma'	=> 0
			
		);
		
		$activation_data = (get_option( 'dms_activation' ) && is_array(get_option( 'dms_activation' ))) ? get_option( 'dms_activation' ) : array();
		
		$data = wp_parse_args( $activation_data, $data);
		
		return $data;
		
	}

	function pagelines_account(){

		$disabled = '';
		$email = '';
		$key = '';
		$activate_text = '<i class="icon-star"></i> Activate Pro';
		$activate_btn_class = 'btn-primary'; 
		
		
		$data = $this->get_account_data();
		
		$active = $data['active'];
		
		$disable = ($active) ? 'disabled' : '';

		$activation_message = ($data['message'] == '') ? 'Site not activated.' : $data['message'];
	
		?>
		<div class="account-creds">
			<div class="account-saving alert">
				<i class="icon-spin icon-refresh"></i> <?php _e( 'Saving', 'pagelines' ); ?>
				
			</div>
			<div class="account-details alert alert-warning" style="<?php if(! $active) echo 'display: block;';?>">
				<?php if( ! $active || $active == ''):  ?>
					<strong><i class="icon-star-half-empty"></i> <?php _e( 'Site Not Activated', 'pagelines' ); ?>
					</strong>
				<?php endif; ?>
			</div>
			<?php if( $active ):  ?>
			
				<div class="account-field alert">
			
					<label for="pl_activation">
						<i class="icon-star"></i> <?php _e( 'Pro Activated!', 'pagelines' ); ?>
						 
						<small><?php printf($activation_message);  ?></small>
					</label>
					<button class="btn settings-action refresh-user btn-primary" data-action="pagelines_account"><i class="icon-refresh" ></i> <?php _e( 'Update Info', 'pagelines' ); ?>
					</button>
					<button class="btn settings-action deactivate-key" data-action="pagelines_account"><i class="icon-remove" style="color: #ff0000;"></i> <?php _e( 'Deactivate', 'pagelines' ); ?>
					</button>
			
				</div>
			
			<?php endif; ?>
			
			
			
			<div class="account-field">
				<label for="pl_activation"><i class="icon-pagelines"></i> <?php _e( 'PageLines Account', 'pagelines' ); ?>
				</label>
		
				<input type="text" class="pl-text-input" name="pl_email" id="pl_email" placeholder="Enter Account Email" value="<?php echo $data['email']; ?>" <?php echo $disable; ?> />
				
			</div>
		
			<div class="account-field">
				<label for="pl_activation"><i class="icon-key"></i> <?php _e( 'Pro Activation Key', 'pagelines' ); ?>
				 <span class="sbtl">(<?php _e( 'optional', 'pagelines' ); ?>)</span></label>
		
				<input type="password" class="pl-text-input" name="pl_activation" id="pl_activation" placeholder="<?php _e( 'Enter Pro Key', 'pagelines' ); ?>
				" value="<?php echo $data['key']; ?>" <?php echo $disable; ?> />
			
			</div>
			<?php if( ! $active ): ?>
			<div class="submit-area account-field">
				<button class="btn btn-primary settings-action" data-action="pagelines_account"><?php _e( 'Update', 'pagelines' ); ?>
				 <i class="icon-chevron-sign-right"></i></button>
				
			</div>
			<?php endif; ?>
			
		</div>
		<div class="account-overview">
			<div class="account-overview-pad">
				<label><?php _e( 'PageLines Account Info', 'pagelines' ); ?>
				</label>
				<div class="account-info">
					<?php ?>
					
 					<?php if( !$data[ 'real_user' ] ): ?>
					<div class="alert alert-info">
						<i class="icon-hand-left"></i> <strong><?php _e( 'PageLines account not added or incorrect.', 'pagelines' ); ?>
						</strong><br/> <em><?php _e( 'Add account to configure PageLines APIs, karma system and store access.', 'pagelines' ); ?>
						</em>
					</div>
					<?php else: ?>
					<div class="row">
						<div class="span6 ">
							<h4><?php _e( 'Profile', 'pagelines' ); ?>
							</h4>
							<div class="account-profile media">
								<div class="img">
									<?php echo $data['avatar'];?>
								</div>
								<div class="bd">
									<h5><?php echo $data['name'];?></h5>
									<p><?php echo $data['description'];?></p>
									<p>
										<a class="btn btn-mini" href="http://www.pagelines.com/my-account/"><?php _e( 'My Account', 'pagelines' ); ?>
										</a>
										<a class="btn btn-mini" href="http://www.pagelines.com/wp-admin/"><?php _e( 'Edit Profile', 'pagelines' ); ?>
										</a>
									</p>
								</div>
							</div>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		
		<?php

	}
	
	function getting_started(){
		?>
		<div></div>
		<?php 
	}

	function pagelines_welcome(){
		?>

		<h3><i class="icon-pagelines"></i> <?php _e( 'Congratulations!', 'pagelines' ); ?>
		</h3>
		<p>
		 	<strong>Hello! Welcome to DMS.</strong><br/> A drag <span class="spamp">&amp;</span> drop design management system for building, managing, and <em>evolving</em> your website.<br/> To get started please visit the links below &darr; 
		</p>
		<div class="alignleft well welcome-well">
			<?php if( defined( 'DMS_DISABLE_ACCOUNT_PANEL' ) && DMS_DISABLE_ACCOUNT_PANEL && pl_is_pro() ){} else { ?>
			<a href="#" class="dms-tab-link btn btn-primary" data-tab-link="account" data-stab-link="pl_account"><i class="icon-pagelines"></i> <?php _e( 'Setup PageLines Account', 'pagelines' ); ?>
			 <i class="icon-angle-right"></i></a>
			<?php } ?>
			<a href="#" class="dms-tab-link btn" data-tab-link="account" data-stab-link="getting_started"><i class="icon-youtube-play"></i> <?php _e( 'Getting Started Video', 'pagelines' ); ?>
			 <i class="icon-angle-right"></i></a>
		</div>
		

		<?php
	}
	

	function pagelines_support(){
		?>
		<h3><i class="icon-thumbs-up"></i> <?php _e( 'The PageLines Experience', 'pagelines' ); ?>
		</h3>
		<p>
			<?php _e( 'We want you to have a most amazing time as a PageLines customer.', 'pagelines' ); ?>
			 <br/>
			<?php _e( "That's why we have a ton of people standing by to make you happy.", 'pagelines' ); ?>
			
		</p>
		<p>
			<a href="http://forum.pagelines.com" class="btn" target="_blank"><i class="icon-comments"></i> <?php _e( 'PageLines Forum', 'pagelines' ); ?>
			</a>
			<a href="http://docs.pagelines.com" class="btn" target="_blank"><i class="icon-file"></i> <?php _e( 'DMS Documentation', 'pagelines' ); ?>
			</a>
		</p>

		<?php
	}
}

