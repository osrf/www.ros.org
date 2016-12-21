<?php

global $wpsf_settings;

$wpsf_settings[] = array(
    'section_id' => 'browsercss',
    'section_title' => 'Browser Specific CSS.',
    'section_order' => 1,
    'fields' => array(
	    array(
            'id' => 'enabled',
            'title' => 'Enable CSS Classes.',
            'desc' => '<p>This will add classes to the page, for example a desktop PC using firefox would add: <kbd>&lt;body class="home blog ... ... <strong>desktop firefox</strong>"&gt;</kbd><br />Also included are patches supplied by Anca for IE8.'  . dmspro_browsercss_compat() . '<br /><kbd>DISCLAMIER</kbd> These patches will <strong>NOT</strong> magically fix all sites to work with old unsupported browsers, but it helps.</p>',
            'type' => 'checkbox',
            'std' => 0
        ),
		array(
			'id' => 'css-type',
			'title' => 'Browser Detection',
			'desc' => 'Default is PHP scripts to detect browsers and add the class.<br />Some hosts use Uber caches so the jQuery method might give you better results.',
			'type' => 'radio',
			'std' => 'php',
			'choices' => array(
				'php' => 'PHP Mode',
				'js' => 'Jquery Mode',
					)
				),
    )
);

$wpsf_settings[] = array(
    'section_id' => 'cdn',
    'section_title' => 'CDN Settings',
    'section_order' => 2,
    'fields' => array(
	    array(
            'id' => 'cdn-enabled',
            'title' => 'CDN',
            'desc' => 'Enable the simple CDN.',
            'type' => 'checkbox',
            'std' => 0
        ),
        array(
            'id' => 'cdn-url',
            'title' => 'Your CDN PULL zone.',
            'desc' => sprintf( '<p>If you do not have a pull zone, or have no idea what a CDN or a pull zone is then dont enable this feature ;)<br />An example of your pullzone might be: <kbd>cdn.%s</kbd></p>', str_replace( 'http://', '', str_replace( 'www', '', str_replace( 'https://', '', site_url() ) ) ) ),
            'type' => 'text',
            'std' => site_url()
        ),
    )
);

$wpsf_settings[] = array(
    'section_id' => 'bulk_edit',
    'section_title' => 'Bulk Edit.',
    'section_order' => 3,
    'fields' => array(
	    array(
            'id' => 'enabled',
            'title' => 'Enable.',
            'desc' => 'Enable this to give you the option to bulk apply DMS templates to posts and pages.',
            'type' => 'checkbox',
            'std' => 0
        )
    )
);

$wpsf_settings[] = array(
    'section_id' => 'search',
    'section_title' => 'Enhanced Search.',
    'section_order' => 4,
    'fields' => array(
	    array(
            'id' => 'enabled',
            'title' => 'Search',
            'desc' => 'Enable WordPress search to look inside section content, eg. textbox/hero/etc. <strong>DMS2 Only</strong>',
            'type' => 'checkbox',
            'std' => 0
        )
    )
);

$wpsf_settings[] = array(
    'section_id' => 'actionmap',
    'section_title' => 'PageLines ActionMap Tool.',
    'section_order' => 5,
    'fields' => array(
	    array(
            'id' => 'enabled',
            'title' => 'ActionMap',
            'desc' => 'This tool is useful for highlighting WordPress and PageLines "actions"',
            'type' => 'checkbox',
            'std' => 0
        )
    )
);

$wpsf_settings[] = array(
    'section_id' => 'lazyload',
    'section_title' => 'PageLines Lazyload.',
    'section_order' => 6,
    'fields' => array(
	    array(
            'id' => 'enabled',
            'title' => 'LazyLoad Images',
            'desc' => 'This tool will use javascript to lazyload images, helping to increase overall user experience.',
            'type' => 'checkbox',
            'std' => 0
        )
    )
);

$wpsf_settings[] = array(
    'section_id' => 'user_sections',
    'section_title' => 'Allow Hidden Sections.',
    'section_order' => 6,
    'fields' => array(
	    array(
            'id' => 'enabled',
            'title' => 'Add Hide Options To Sections',
            'desc' => 'Adds special options to sections that allow you to show sections only to various users/groups.',
            'type' => 'checkbox',
            'std' => 0
        )
    )
);

$wpsf_settings[] = array(
    'section_id' => 'gfonts',
    'section_title' => 'Enable All Googlefonts.',
    'section_order' => 7,
    'fields' => array(
	    array(
            'id' => 'enabled',
            'title' => 'Enable All Googlefonts.',
            'desc' => get_gfont_desc(),
            'type' => 'checkbox',
            'std' => 0
        )
    )
);

$wpsf_settings[] = array(
    'section_id' => 'advanced',
    'section_title' => 'Advanced',
    'section_order' => 10,
    'fields' => array(
	    array(
            'id' => 'memtest',
            'title' => 'Memory Test',
            'desc' => PL_Memcheck::desc(),
            'type' => 'checkbox',
            'std' => 0
        ),
        array(
              'id' => 'sections_admin',
              'title' => 'Sections Admin',
              'desc' => 'List all sections in database, remove unused/broken sections.',
              'type' => 'checkbox',
              'std' => 0
          )
    )
);


function get_gfont_desc(){
	$a = 'This option will add 657 Googlefonts into the DMS typography options just like the old googlefonts plugin for PageLines Framework';
	if( class_exists( 'Google_Fonts' ) ) {
		$a .= '<br/><strong>WARNING We detected the old plugin, disable it before using this option!</strong>';
	}
	return $a;
}
