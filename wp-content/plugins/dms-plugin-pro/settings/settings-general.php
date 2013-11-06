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
        )
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
            'desc' => sprintf( '<p>An example of your pullzone might be: <kbd>cdn.%s</kbd></p>', str_replace( 'http://', '', str_replace( 'www', '', str_replace( 'https://', '', site_url() ) ) ) ),
            'type' => 'text',
            'std' => site_url()
        ),
    )
);

$wpsf_settings[] = array(
    'section_id' => 'section_cache',
    'section_title' => 'Section Caching.',
    'section_order' => 3,
    'fields' => array(
	    array(
            'id' => 'cache-enabled',
            'title' => 'Section Cache',
            'desc' => Sections_Cache::cache_desc(),
            'type' => 'checkbox',
            'std' => 0
        )
    )
);

$wpsf_settings[] = array(
    'section_id' => 'memtest',
    'section_title' => 'Memory Test.',
    'section_order' => 10,
    'fields' => array(
	    array(
            'id' => 'enabled',
            'title' => 'MemTest',
            'desc' => PL_Memcheck::desc(),
            'type' => 'checkbox',
            'std' => 0
        )
    )
);
