<?php
class Google_Fonts_Pro {

	function __construct() {
		add_filter ( 'pagelines_foundry', array( &$this, 'google_fonts' ) );
	}

	function google_fonts( $thefoundry ) {

		if ( ! defined( 'PAGELINES_SETTINGS' ) )
			return;

		$fonts = $this->get_fonts();
		return array_merge( $thefoundry, $fonts );

	}

	function get_fonts( $count = false ) {

		$fcount = array( 
			'cached'	=> false,
			'number'	=> 0
			);
		$url = 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyAc-H7le81NcghctcW8jXoCaDR73ZyMwZY';
		if( $fonts = get_option( 'pl_gfonts' ) ) {

			$fonts = json_decode( $fonts );
			$fcount['cached'] = true;
			$fcount['number'] = count( $fonts->items );
			if( $count ) 
				return $fcount;
			else
				return $this->format( $fonts->items );
		}

		$response = wp_remote_get( $url );
		if ( $response !== false ) {
			if( ! is_array( $response ) || ( is_array( $response ) && $response['response']['code'] != 200 ) ) {
				$fonts = $this->legacy();
			} else {

			$fonts = wp_remote_retrieve_body( $response );
			update_option( 'pl_gfonts', $fonts );
			$fcount['cached'] = true;
			$fonts = json_decode( $fonts );
			}
		}
		$fcount['number'] = count( $fonts->items );
		if( $count )
			return $fcount;
		else
			return $this->format( $fonts->items );
	}


	function format( $fonts ) {

		$fonts = ( array ) $fonts;

		$out = array();

		foreach ( $fonts as $font ) {

			$out[ str_replace( ' ', '_', $font->family ) ] = array(
				'name'		=> $font->family,
				'family'	=> sprintf( '"%s"', $font->family ),
				'web_safe'	=> true,
				'google' 	=> $font->variants,
				'monospace' => ( preg_match( '/\sMono/', $font->family ) ) ? 'true' : 'false',
				'free'		=> true
			);
		}
		return $out;
	}


	function legacy() {

		$fonts = pl_file_get_contents( dirname(__FILE__) . '/fonts.json' );

		return json_decode( $fonts );
	}
}
