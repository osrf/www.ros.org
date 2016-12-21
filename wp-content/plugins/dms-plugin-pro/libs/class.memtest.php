<?php

class PL_Memcheck {

	function __construct() {
		add_action( 'admin_init', array( $this, 'memcheck' ) );
	}
	function memcheck() {

		if( ! isset( $_GET['pl_memcheck'] ) )
			return;

		register_shutdown_function( array( $this, "fatal_handler" ) );
		set_error_handler(array( $this, "fatal_handler" ) );
		$step = 1;
		global $ram;
		while(TRUE) {
			$chunk = str_repeat('0123456789', 128*1024*$step++);
			$ram = round(memory_get_usage()/(1024));
			unset($chunk);
		}
	}

	function fatal_handler() {
		global $ram;
		echo sprintf( 'Ram test Results: %sKb or approx %sMb', $ram, round( $ram / 1024 ) );
	}

	static function desc() {
		if ( '1' == wpsf_get_setting( wpsf_get_option_group( '../settings/settings-general.php' ), 'advanced', 'memtest' ) )
			return sprintf( 'Use this link to test your actual server ram. <a href="%s">CLICK HERE TO TEST NOW</a><br /><kbd>DO NOT LEAVE THIS ENABLED!!!</kbd>', add_query_arg( array('pl_memcheck' => 1), admin_url() ) );
		else
			return '<p>Most cheap hosts although they say 256M of RAM is allocated to your account in reality it is limited in other ways.<br />This simple test will reveal your true account limitations.</p>';

	}
}
