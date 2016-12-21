<?php

class Browser_Pro_Specific_CSS {
	
	function __construct() {
		global $wp_scripts, $dmspro_plugin_url;
		// no dms? OH NOES!
		if( ! function_exists( 'pl_detect_ie' ) )
			return false;
		
		$this->urls = array( 
			$dmspro_plugin_url . 'libs/js/html5.min.js',
			$dmspro_plugin_url . 'libs/js/respond.min.js',
			$dmspro_plugin_url . 'libs/js/selectivizr-min.js'				
			);

		
		
		$this->ie_ver = pl_detect_ie();
		$this->useragent = ( isset($_SERVER['HTTP_USER_AGENT'] ) ) ? $_SERVER['HTTP_USER_AGENT'] : '';
		
		$settings = wpsf_get_settings( '../settings/settings-general.php' );
		
		if( isset( $settings['settingsgeneral_browsercss_css-type'] ) && 'js' == $settings['settingsgeneral_browsercss_css-type'] ) {
			wp_register_script( 'browser-detect', $dmspro_plugin_url . 'libs/js/browser.js', array( 'jquery' ) );		
			wp_enqueue_script( 'browser-detect');
			add_action( 'wp_head', array( $this, 'hack_scripts' ), 25 );			
		} else {
			add_filter( 'body_class', array( &$this, 'body_class' ) );
			add_action( 'the_html_tag', array( $this, 'add_ie_class' ) );
			add_action( 'wp_head', array( $this, 'hack_scripts' ), 25 );
		}
	}

	function hack_scripts() {
		foreach( $this->urls as $url ) {
			printf( "\n<!--[if lte IE 9]>\n<script type='text/javascript' src='%s'></script>\n<![endif]-->\n", $url );
		}
		echo "\n<!--[if lte IE 9]>\n<link rel='stylesheet' href='//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css' />\n<![endif]-->\n";
	}

	/***************************************************************
	* Function is_iphone
	* Detect the iPhone
	***************************************************************/

	function is_iphone() {
		
		return(preg_match('/iphone/i',$this->useragent));
	}

	/***************************************************************
	* Function is_ipad
	* Detect the iPad
	***************************************************************/

	function is_ipad() {

		return(preg_match('/ipad/i',$this->useragent));
	}

	/***************************************************************
	* Function is_ipod
	* Detect the iPod, most likely the iPod touch
	***************************************************************/

	function is_ipod() {
		
		return(preg_match('/ipod/i',$this->useragent));
	}

	/***************************************************************
	* Function is_android
	* Detect an android device. They *SHOULD* all behave the same
	***************************************************************/

	function is_android() {
		
		return(preg_match('/android/i',$this->useragent));
	}

	/***************************************************************
	* Function is_blackberry
	* Detect a blackberry device 
	***************************************************************/

	function is_blackberry() {
		
		return(preg_match('/blackberry/i',$this->useragent));
	}

	/***************************************************************
	* Function is_opera_mobile
	* Detect both Opera Mini and hopfully Opera Mobile as well
	***************************************************************/

	function is_opera_mobile() {
		
		return(preg_match('/opera\smobi/i',$this->useragent));
	}
	
	function is_opera_mini() {
		
		return(preg_match('/opera\smini/i',$this->useragent));
	}

	/***************************************************************
	* Function is_palm
	* Detect a webOS device such as Pre and Pixi
	***************************************************************/

	function is_palm() {
		
		return(preg_match('/webOS/i', $this->useragent));
	}

	/***************************************************************
	* Function is_symbian
	* Detect a symbian device, most likely a nokia smartphone
	***************************************************************/

	function is_symbian() {
		
		return(preg_match('/Series60/i', $this->useragent) || preg_match('/Symbian/i', $this->useragent));
	}

	/***************************************************************
	* Function is_windows_mobile
	* Detect a windows smartphone
	***************************************************************/

	function is_windows_mobile() {
		
		return(preg_match('/WM5/i', $this->useragent) || preg_match('/WindowsMobile/i', $this->useragent));
	}

	/***************************************************************
	* Function is_lg
	* Detect an LG phone
	***************************************************************/

	function is_lg() {
		
		return(preg_match('/LG/i', $this->useragent));
	}

	/***************************************************************
	* Function is_motorola
	* Detect a Motorola phone
	***************************************************************/

	function is_motorola() {
		
		return(preg_match('/\ Droid/i', $this->useragent) || preg_match('/XT720/i', $this->useragent) || preg_match('/MOT-/i', $this->useragent) || preg_match('/MIB/i', $this->useragent));
	}

	/***************************************************************
	* Function is_nokia
	* Detect a Nokia phone
	***************************************************************/

	function is_nokia() {
		
		return(preg_match('/Series60/i', $this->useragent) || preg_match('/Symbian/i', $this->useragent) || preg_match('/Nokia/i', $this->useragent));
	}

	/***************************************************************
	* Function is_samsung
	* Detect a Samsung phone
	***************************************************************/

	function is_samsung() {
		
		return(preg_match('/Samsung/i', $this->useragent));
	}

	/***************************************************************
	* Function is_samsung_galaxy_tab
	* Detect the Galaxy tab
	***************************************************************/

	function is_samsung_galaxy_tab() {
		
		return(preg_match('/SPH-P100/i', $this->useragent));
	}

	/***************************************************************
	* Function is_sony_ericsson
	* Detect a Sony Ericsson
	***************************************************************/

	function is_sony_ericsson() {
		
		return(preg_match('/SonyEricsson/i', $this->useragent));
	}

	/***************************************************************
	* Function is_nintendo
	* Detect a Nintendo DS or DSi
	***************************************************************/

	function is_nintendo() {
		
		return(preg_match('/Nintendo DSi/i', $this->useragent) || preg_match('/Nintendo DS/i', $this->useragent));
	}

	/***************************************************************
	* Function is_handheld
	* Wrapper function for detecting ANY handheld device
	***************************************************************/

	function is_handheld() {
		return($this->is_iphone() || $this->is_ipad() || $this->is_ipod() || $this->is_android() || $this->is_blackberry() || $this->is_opera_mobile() || $this->is_opera_mini() || $this->is_palm() || $this->is_symbian() || $this->is_windows_mobile() || $this->is_lg() || $this->is_motorola() || $this->is_nokia() || $this->is_samsung() || $this->is_samsung_galaxy_tab() || $this->is_sony_ericsson() || $this->is_nintendo());
	}

	/***************************************************************
	* Function is_mobile
	* Wrapper function for detecting ANY mobile phone device
	***************************************************************/

	function is_mobile() {
		if ($this->is_tablet()) { return false; }  // this catches the problem where an Android device may also be a tablet device
		return($this->is_iphone() || $this->is_ipod() || $this->is_android() || $this->is_blackberry() || $this->is_opera_mobile() || $this->is_opera_mini() || $this->is_palm() || $this->is_symbian() || $this->is_windows_mobile() || $this->is_lg() || $this->is_motorola() || $this->is_nokia() || $this->is_samsung() || $this->is_sony_ericsson() || $this->is_nintendo());
	}

	/***************************************************************
	* Function is_ios
	* Wrapper function for detecting ANY iOS/Apple device
	***************************************************************/

	function is_ios() {
		return($this->is_iphone() || $this->is_ipad() || $this->is_ipod());

	}

	/***************************************************************
	* Function is_tablet
	* Wrapper function for detecting tablet devices
	***************************************************************/

	function is_tablet() {
		return($this->is_ipad() || $this->is_samsung_galaxy_tab());
	}
	
	
	function body_class($classes) 
	{

		global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_safari, $is_chrome;

		// top level
		if ($this->is_handheld()) { $classes[] = 'handheld'; };
		if ($this->is_mobile()) { $classes[] = 'mobile'; };
		if ($this->is_ios()) { $classes[] = 'ios'; };
		if ($this->is_tablet()) { $classes[] = 'tablet'; };

		// specific 
		if ($this->is_iphone()) { $classes[] = 'iphone'; };
		if ($this->is_ipad()) { $classes[] = 'ipad'; };
		if ($this->is_ipod()) { $classes[] = 'ipod'; };
		if ($this->is_android()) { $classes[] = 'android'; };
		if ($this->is_blackberry()) { $classes[] = 'blackberry'; };
		if ($this->is_opera_mobile()) { $classes[] = 'opera-mobile';}
		if ($this->is_opera_mini()) { $classes[] = 'opera-mini';}
		if ($this->is_palm()) { $classes[] = 'palm';}
		if ($this->is_symbian()) { $classes[] = 'symbian';}
		if ($this->is_windows_mobile()) { $classes[] = 'windows-mobile'; }
		if ($this->is_lg()) { $classes[] = 'lg'; }
		if ($this->is_motorola()) { $classes[] = 'motorola'; }
		if ($this->is_nokia()) { $classes[] = 'nokia'; }
		if ($this->is_samsung()) { $classes[] = 'samsung'; }
		if ($this->is_samsung_galaxy_tab()) { $classes[] = 'samsung-galaxy-tab'; }
		if ($this->is_sony_ericsson()) { $classes[] = 'sony-ericsson'; }
		if ($this->is_nintendo()) { $classes[] = 'nintendo'; }

		// bonus
		if (!$this->is_handheld()) { $classes[] = 'desktop'; }
		if ($is_lynx) { $classes[] = 'lynx'; }
		if ($is_gecko) { $classes[] = 'firefox'; }
		if ($is_opera) { $classes[] = 'opera'; }
		if ($is_safari) { $classes[] = 'safari'; }
		if ($is_chrome) { $classes[] = 'chrome'; }
		if ($is_IE) { 
			$classes[] = 'ie ie' . $this->ie_ver;
		 }

		return $classes;
	}
	
	function add_ie_class() {
		global $is_IE;
		if ( ! $is_IE )
			return;
		
		$classes = '';
		$this->ie_ver;
		if( $this->ie_ver <= 8 )  {
			$classes = ' class="ie ie8 lte9 lte8"';
		} elseif( $this->ie_ver == 9 ) {
			$classes = ' class="ie ie9 lte9"';
		}
		echo $classes;
	}
} // end class

function dmspro_browsercss_compat() {
	if ( class_exists( 'Browser_Specific_CSS' ) ) {
		
		return '<style>
		@-webkit-keyframes blinker {  
		  from { opacity: 1.0; }
		  to { opacity: 0.0; }
		}
		.css3-blink {
		  -webkit-animation-name: blinker;  
		  -webkit-animation-iteration-count: infinite;  
		  -webkit-animation-timing-function: cubic-bezier(1.0,0,0,1.0);
		  -webkit-animation-duration: 1s;
		</style>
		<br /><strong><kbd>!!!<span class="css3-blink" style="color:red">WARNING</span>!!!</kbd> You appear to have the old Browser CSS plugin activated, please deactivate it. <kbd>!!!<span class="css3-blink" style="color:red">WARNING</span>!!!</kbd></strong>';
		
	}
}