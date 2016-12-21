<?php

class Sections_User {

	function __construct() {
		add_filter( 'pagelines_render_section', array( $this, 'section_user_check' ), 10, 2 );
		add_filter( 'pl_standard_section_options', array( $this, 'add_option' ) );
	}

	function section_user_check( $s, $class ) {

		if( ! is_object( $s ) )
			return false;

		$hide = false;

		$roles = $s->opt( 'pl_role_hide' );
		
		if( false !== $roles ) {
			
			if( ! is_user_logged_in() && in_array( 'none', $roles ) )
				$hide = true;
						
			if( is_user_logged_in() ) {
				global $current_user;

				$user_roles = $current_user->roles;
				$user_role = array_shift($user_roles);
				if( in_array( $user_role, $roles ) )
					$hide = true;
			}
		}

		if( '1' === $s->opt( 'pl_standard_nouser_hide' ) && ! is_user_logged_in() )
			$hide = true;

		if( '1' === $s->opt( 'pl_standard_user_hide' ) && is_user_logged_in() )
			$hide = true;

		if( '1' === $s->opt( 'pl_standard_mobile_hide' ) && wp_is_mobile() )
			$hide = true;

		if( '1' === $s->opt( 'pl_standard_desktop_hide' ) && ! wp_is_mobile() )
			$hide = true;

		if( true === $hide )
			return false;

		ob_start();
		$class->section_template_load( $s );
		return ob_get_clean();
	}

	function add_option( $options ) {

		$extra = array();
		$opts = $options['standard']['opts'];
		$roles = array();
		global $wp_roles;
		
		$all_roles = $wp_roles->roles;
		$all_roles = apply_filters('editable_roles', $all_roles);
		unset( $all_roles['administrator'] );
		foreach( $all_roles as $k => $data ) {
			$roles[$k] = array(
				'name'	=> $data['name']
			);
		}	
		
		$roles['none'] = array( 'name' => 'Guest' );

		$extra[] = array(
				'key'	=> 'pro_extra_standard_opts',
				'help'	=> 'Extra Options (Pro Tools)<br />These extra options will <strong>NOT</strong> work properly if you are using a cache plugin and have not configured it correctly.',
				'type'	=> 'multi',
				'opts'	=> array (
					array(
						'key'		=> 'pl_standard_nouser_hide',
						'type' 		=> 'check',
						'default'	=> false,
						'label' 	=> __( 'Hide this section for logged out users', 'pagelines' ),
					),
					array(
						'key'		=> 'pl_standard_user_hide',
						'type' 		=> 'check',
						'default'	=> false,
						'label' 	=> __( 'Hide this section for logged in users', 'pagelines' ),
					),
					array(
						'key'		=> 'pl_standard_mobile_hide',
						'type' 		=> 'check',
						'default'	=> false,
						'label' 	=> __( 'Hide this section for mobile users', 'pagelines' ),
					),
					array(
						'key'		=> 'pl_standard_desktop_hide',
						'type' 		=> 'check',
						'default'	=> false,
						'label' 	=> __( 'Hide this section for desktop users', 'pagelines' ),
					),
					array(
						'key'		=> 'pl_role_hide',
						'type' 		=> 'select_multi',
						'label' 	=> __( 'Select usergroups to have section hidden. (admin will always see it)', 'pagelines' ),
						'opts'		=> $roles
					),
					// array(
					// 	'key'		=> 'pl_hide_date_start',
					// 	'type' 		=> 'select_date',
					// 	'label' 	=> __( 'Start showing from this date', 'pagelines' ),
					// ),
					// array(
					// 	'key'		=> 'pl_hide_date_finish',
					// 	'type' 		=> 'select_date',
					// 	'label' 	=> __( 'Stop showing at this date', 'pagelines' ),
					// )
				)
			);
		$opts = array_merge( $extra, $opts );
		$options['standard']['opts'] = $opts;
		return $options;
	}
}