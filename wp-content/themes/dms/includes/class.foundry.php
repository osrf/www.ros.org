<?php

/**
 * class PageLinesFoundry
 *
 *
 */
class PageLinesFoundry {

	var $gfont_base_uri = 'http://fonts.googleapis.com/css?family=';
	var $foundry;

	/**
	 * PHP5 constructor
	 *
	 */
	function __construct( ) {

		// get users modified gfonts url and strip // if present
		$gurl = ltrim( apply_filters( 'pagelines_gfont_baseurl', $this->gfont_base_uri ), '/' );
		// strip http:// and https:// and prepend url with // so ssl works properly
		$this->gfont_base_uri = sprintf( '//%s', str_replace( array( 'http://','https://' ), '', $gurl ) );
		$this->foundry = $this->get_type_foundry();
	}

	function get_foundry(){

		foreach($this->foundry as $key => $f){
			if($f['google']){
				$gfont = $this->gfont_key($key);
				$this->foundry[$key]['gfont'] = $gfont;
				$this->foundry[$key]['gfont_uri'] = $this->gfont_base_uri . $gfont;
			}
		}
		return $this->foundry;
	}

	/**
	*
	* @TODO document
	*
	*/
	function get_type_foundry(){

		$thefoundry = array(
			'anton' => array(
				'name' => 'Anton',
				'family' => '"Anton", arial, serif',
				'web_safe' => true,
				'google' => true,
				'monospace' => false
			),
			'arial' => array(
				'name' 		=> 'Arial',
				'family' 	=> 'Arial, "Helvetica Neue", Helvetica, sans-serif',
				'web_safe' 	=> true,
				'google' 	=> false,
				'monospace' => false,
				'free'		=> true
			),
			'arial_black' => array(
				'name' => 'Arial Black',
				'family' => '"Arial Black", "Arial Bold", Arial, sans-serif',
				'web_safe' => true,
				'google' => false,
				'monospace' => false
			),
			'arial_narrow' => array(
				'name' => 'Arial Narrow',
				'family' => '"Arial Narrow", Arial, "Helvetica Neue", Helvetica, sans-serif',
				'web_safe' => true,
				'google' => false,
				'monospace' => false
			),
			'cabin' => array(
				'name' => 'Cabin',
				'family' => 'Cabin, Arial, Verdana, sans-serif',
				'web_safe' => true,
				'google' => array('regular', 'italic', 'bold', 'bolditalic'),
				'monospace' => false
			),
			'cantarell' => array(
				'name' => 'Cantarell',
				'family' => 'Cantarell, Candara, Verdana, sans-serif',
				'web_safe' => true,
				'google' => array('regular', 'italic', 'bold', 'bolditalic'),
				'monospace' => false
			),
			'cardo' => array(
				'name' => 'Cardo',
				'family' => 'Cardo, "Times New Roman", Times, serif',
				'web_safe' => true,
				'google' => true,
				'monospace' => false
			),
			'courier_new' => array(
				'name' => 'Courier',
				'family' => 'Courier, Verdana, sans-serif',
				'web_safe' => true,
				'google' => false,
				'monospace' => true,
				'free'		=> true
			),
			'crimson_text' => array(
				'name' => 'Crimson Text',
				'family' => '"Crimson Text", "Times New Roman", Times, serif',
				'web_safe' => true,
				'google' => true,
				'monospace' => false
			),
			'cuprum' => array(
				'name' => 'Cuprum',
				'family' => '"Cuprum", arial, serif',
				'web_safe' => true,
				'google' => true,
				'monospace' => false
			),
			'dancing_script' => array(
				'name' => 'Dancing Script',
				'family' => '"Dancing Script", arial, serif',
				'web_safe' => true,
				'google' => array('regular', 'bold'),
				'monospace' => false
			),
			'della_respira' => array(
				'name' => 'Della Respira',
				'family' => '"Della Respira", serif',
				'web_safe' => true,
				'google' => '400',
				'monospace' => false
			),
			'droid_sans' => array(
				'name' => 'Droid Sans',
				'family' => '"Droid Sans", "Helvetica Neue", Helvetica, sans-serif',
				'web_safe' => true,
				'google' => array('regular', 'bold'),
				'monospace' => false,
				'free'		=> true
			),

			'droid_mono' => array(
				'name' => 'Droid Sans Mono',
				'family' => '"Droid Sans Mono", Consolas, Monaco, Courier, sans-serif',
				'web_safe' => true,
				'google' => true,
				'monospace' => true
			),
			'droid_serif' => array(
				'name' => 'Droid Serif',
				'family' => '"Droid Serif", Calibri, "Times New Roman", serif',
				'web_safe' => true,
				'google' => array('regular', 'italic', 'bold', 'bolditalic'),
				'monospace' => false
			),
			'georgia' => array(
				'name' => 'Georgia',
				'family' => 'Georgia, "Times New Roman", Times, serif',
				'web_safe' => true,
				'google' => false,
				'monospace' => false,
				'free'		=> true
			),
			'happy_monkey' => array(
				'name' => 'Happy Monkey',
				'family' => '"Happy Monkey", serif',
				'web_safe' => true,
				'google' => '400',
				'monospace' => false,
			),
			'im_fell_dw_pica' => array(
				'name' => 'IM Fell DW Pica',
				'family' => '"IM Fell DW Pica", "Times New Roman", serif',
				'web_safe' => true,
				'google' => array('regular', 'italic'),
				'monospace' => false
			),
			'im_fell_english' => array(
				'name' => 'IM Fell English',
				'family' => '"IM Fell English", "Times New Roman", serif',
				'web_safe' => true,
				'google' => array('regular', 'italic'),
				'monospace' => false
			),
			'imprima' => array(
				'name' => 'Imprima',
				'family' => 'Imprima, sans-serif',
				'web_safe' => true,
				'google' => '400',
				'monospace' => false
			),
			'inconsolata' => array(
				'name' => 'Inconsolata',
				'family' => '"Inconsolata", Consolas, Monaco, Courier, sans-serif',
				'web_safe' => true,
				'google' => true,
				'monospace' => true
			),
			'josefin_sans' => array(
				'name' => 'Josefin Sans',
				'family' => '"Josefin Sans", "Century Gothic", Verdana, sans-serif',
				'web_safe' => true,
				'google' => true,
				'monospace' => false,
			),
			'kreon' => array(
				'name' 		=> 'Kreon',
				'family' 	=> '"Kreon", georgia, serif',
				'google' 	=> array('300', '400', '700'),
				'web_safe' 	=> true,
				'monospace' => false
			),
			'lato' => array(
				'name' => 'Lato',
				'family' => '"Lato", arial, serif',
				'web_safe' => true,
				'google' => true,
				'monospace' => false
			),
			'lobster' => array(
				'name' => 'Lobster',
				'family' => 'Lobster, Arial, sans-serif',
				'web_safe' => true,
				'google' => true,
				'monospace' => false,
				'free'		=> true
			),
			'lora' => array(
				'name' 		=> 'Lora',
				'family' 	=> '"Lora", georgia, serif',
				'google' 	=> array('400', '700', '400italic'),
				'web_safe' 	=> true,
				'monospace' => false
			),
			'merriweather' => array(
				'name' 		=> 'Merriweather',
				'family' 	=> 'Merriweather, georgia, times, serif',
				'web_safe' 	=> true,
				'google' 	=> true,
				'monospace' => false,
				'free'		=> true
			),
			'molengo' => array(
				'name' => 'Molengo',
				'family' => 'Molengo, "Trebuchet MS", Corbel, Arial, sans-serif',
				'web_safe' => true,
				'google' => true,
				'monospace' => false
			),
			'nobile' => array(
				'name' => 'Nobile',
				'family' => 'Nobile, Corbel, Arial, sans-serif',
				'web_safe' => true,
				'google' => array('regular', 'italic', 'bold', 'bolditalic'),
				'monospace' => false
			),
			'ofl_sorts_mill_goudy' => array(
				'name' => 'OFL Sorts Mill Goudy TT',
				'family' => '"OFL Sorts Mill Goudy TT", Georgia, serif',
				'web_safe' => true,
				'google' => array('regular', 'italic'),
				'monospace' => false
			),
			'old_standard' => array(
				'name' => 'Old Standard TT',
				'family' => '"Old Standard TT", "Times New Roman", Times, serif',
				'web_safe' => true,
				'google' => array('regular', 'italic', 'bold'),
				'monospace' => false
			),
			'source_sans_pro' => array(
				'name' => 'Source Sans Pro',
				'family' => '"Source Sans Pro", "Helvetica Neue", Helvetica, sans-serif',
				'web_safe' => true,
				'google' => array('400','600','700','900'),
				'monospace' => false,
				'free'		=> true
			),
			'open_sans' => array(
				'name' => 'Open Sans',
				'family' => '"Open Sans", "Helvetica Neue", Helvetica, sans-serif',
				'web_safe' => true,
				'google' => array('400','300','600','800'),
				'monospace' => false,
				'free'		=> true
			),
			'reenie_beanie' => array(
				'name' => 'Reenie Beanie',
				'family' => '"Reenie Beanie", Arial, sans-serif',
				'web_safe' => true,
				'google' => true,
				'monospace' => false
			),
			'tangerine' => array(
				'name' => 'Tangerine',
				'family' => 'Tangerine, "Times New Roman", Times, serif',
				'web_safe' => true,
				'google' => array('regular', 'bold'),
				'monospace' => false
			),
			'times_new_roman' => array(
				'name' => 'Times New Roman',
				'family' => '"Times New Roman", Times, Georgia, serif',
				'web_safe' => true,
				'google' => false,
				'monospace' => false,
				'free'		=> true
			),
			'trebuchet_ms' => array(
				'name' => 'Trebuchet MS',
				'family' => '"Trebuchet MS", "Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", Arial, sans-serif',
				'web_safe' => true,
				'google' => false,
				'monospace' => false,
				'free'		=> true
			),
			'verdana' => array(
				'name' => 'Verdana',
				'family' => 'Verdana, sans-serif',
				'web_safe' => true,
				'google' => false,
				'monospace' => false,
				'free'		=> true
			),
			'vollkorn' => array(
				'name' => 'Vollkorn',
				'family' => 'Vollkorn, Georgia, serif',
				'web_safe' => true,
				'google' => array('regular', 'bold'),
				'monospace' => false
			),
			'yanone' => array(
				'name' => 'Yanone Kaffeesatz',
				'family' => '"Yanone Kaffeesatz", Arial, sans-serif',
				'web_safe' => true,
				'google' => array('200', '300', '400', '700'),
				'monospace' => false
			),
			'american_typewriter' => array(
				'name' => 'American Typewriter',
				'family' => '"American Typewriter", Georgia, serif',
				'web_safe' => false,
				'google' => false,
				'monospace' => false
			),
			'andale' => array(
				'name' => 'Andale Mono',
				'family' => '"Andale Mono", Consolas, Monaco, Courier, "Courier New", Verdana, sans-serif',
				'web_safe' => false,
				'google' => false,
				'monospace' => true
			),
			'baskerville' => array(
				'name' => 'Baskerville',
				'family' => 'Baskerville, "Times New Roman", Times, serif',
				'web_safe' => false,
				'google' => false,
				'monospace' => false
			),
			'bookman_old_style' => array(
				'name' => 'Bookman Old Style',
				'family' => '"Bookman Old Style", Georgia, "Times New Roman", Times, serif',
				'web_safe' => false,
				'google' => false,
				'monospace' => false
			),
			'calibri' => array(
				'name' => 'Calibri',
				'family' => 'Calibri, "Helvetica Neue", Helvetica, Arial, Verdana, sans-serif',
				'web_safe' => false,
				'google' => false,
				'monospace' => false,
				'free'		=> true
			),
			'cambria' => array(
				'name' => 'Cambria',
				'family' => 'Cambria, Georgia, "Times New Roman", Times, serif',
				'web_safe' => false,
				'google' => false,
				'monospace' => false
			),
			'candara' => array(
				'name' => 'Candara',
				'family' => 'Candara, Verdana, sans-serif',
				'web_safe' => false,
				'google' => false,
				'monospace' => false
			),
			'century_gothic' => array(
				'name' => 'Century Gothic',
				'family' => '"Century Gothic", "Apple Gothic", Verdana, sans-serif',
				'web_safe' => false,
				'google' => false,
				'monospace' => false,
				'free'		=> true
			),
			'century_schoolbook' => array(
				'name' => 'Century Schoolbook',
				'family' => '"Century Schoolbook", Georgia, "Times New Roman", Times, serif',
				'web_safe' => false,
				'google' => false,
				'monospace' => false,
				'free'		=> true
			),
			'consolas' => array(
				'name' => 'Consolas',
				'family' => 'Consolas, "Andale Mono", Monaco, Courier, "Courier New", Verdana, sans-serif',
				'web_safe' => false,
				'google' => false,
				'monospace' => true
			),
			'constantia' => array(
				'name' => 'Constantia',
				'family' => 'Constantia, Georgia, "Times New Roman", Times, serif',
				'web_safe' => false,
				'google' => false,
				'monospace' => false,
				'free'		=> true
			),
			'corbel' => array(
				'name' => 'Corbel',
				'family' => 'Corbel, "Lucida Grande", "Lucida Sans Unicode", Arial, sans-serif',
				'web_safe' => false,
				'google' => false,
				'monospace' => false
			),
			'franklin_gothic' => array(
				'name' => 'Franklin Gothic Medium',
				'family' => '"Franklin Gothic Medium", Arial, sans-serif',
				'web_safe' => false,
				'google' => false,
				'monospace' => false
			),
			'garamond' => array(
				'name' => 'Garamond',
				'family' => 'Garamond, "Hoefler Text", "Times New Roman", Times, serif',
				'web_safe' => false,
				'google' => false,
				'monospace' => false,
				'free'		=> true
			),
			'gill_sans' => array(
				'name' => 'Gill Sans',
				'family' => '"Gill Sans MT", "Gill Sans", Calibri, "Trebuchet MS", sans-serif',
				'web_safe' => false,
				'google' => false,
				'monospace' => false,
			),
			'helvetica' => array(
				'name' => 'Helvetica',
				'family' => '"Helvetica Neue", Helvetica, Arial, sans-serif',
				'web_safe' => false,
				'google' => false,
				'monospace' => false,
				'free'		=> true
			),
			'hoefler' => array(
				'name' => 'Hoefler Text',
				'family' => '"Hoefler Text", Garamond, "Times New Roman", Times, sans-serif',
				'web_safe' => false,
				'google' => false,
				'monospace' => false
			),
			'lucida_bright' => array(
				'name' => 'Lucida Bright',
				'family' => '"Lucida Bright", Cambria, Georgia, "Times New Roman", Times, serif',
				'web_safe' => false,
				'google' => false,
				'monospace' => false
			),
			'lucida_grande' => array(
				'name' 		=> 'Lucida Grande',
				'family' 	=> '"Lucida Grande", "Lucida Sans", "Lucida Sans Unicode", sans-serif',
				'web_safe' 	=> false,
				'google' 	=> false,
				'monospace' => false,
				'free'		=> true
			),
			'palatino' => array(
				'name' => 'Palatino',
				'family' => '"Palatino Linotype", Palatino, Georgia, "Times New Roman", Times, serif',
				'web_safe' => false,
				'google' => false,
				'monospace' => false
			),
			'rockwell' => array(
				'name' => 'Rockwell',
				'family' => 'Rockwell, "Arial Black", "Arial Bold", Arial, sans-serif',
				'web_safe' => false,
				'google' => false,
				'monospace' => false
			),
			'tahoma' => array(
				'name' 			=> 'Tahoma',
				'family' 		=> 'Tahoma, Geneva, Verdana, sans-serif',
				'web_safe' 		=> false,
				'google' 		=> false,
				'monospace' 	=> false,
				'free'			=> true
			)
		);

		ksort($thefoundry);
		return apply_filters('pagelines_foundry', $thefoundry);
	}

	function needs_import( $font_id ){
		return ( isset($this->foundry[$font_id]) && $this->foundry[$font_id]['google'] ) ? true : false;
	}

	/**
	 * Creates the import URL for google fonts
	 *
	 */
	function google_import( $gfonts, $type = 'import' ) {
		$family = array();
		if( is_array($gfonts) && !empty($gfonts) ){

			foreach( array_unique($gfonts) as $id ){

				if( isset($this->foundry[$id]) && $this->foundry[$id]['google'] ){

					$faces = ( is_array($this->foundry[$id]['google']) ) ? sprintf( ':%s', implode(',', $this->foundry[$id]['google']) ) : '';

					$family[] = urlencode( $this->foundry[$id]['name'] ) . $faces;
				}


			}
		}


		if(is_array($family) && !empty($family)){

			$this->gfont_key = implode('|', $family);

			$this->gfont_uri = $this->gfont_base_uri . $this->gfont_key;

			if( 'import' == $type )
			 	return sprintf('@import url(%s);%s', $this->gfont_uri, "\n");
			else
				return $this->gfont_key;
				

		} else
			return false;

	}


	/**
	*
	* @TODO document
	*
	*/
	function get_the_import( $font ){

		$family[] = urlencode($this->foundry[$font]['name']) . (is_array($this->foundry[$font]['google']) ? ':' . implode(',', $this->foundry[$font]['google']) : '');

		$this->gfont_key = implode('|', $family);
		$this->gfont_uri = $this->gfont_base_uri . $this->gfont_key;
		$this->gfont_import = sprintf('@import url(%s);', $this->gfont_uri);

		return $this->gfont_import;

	}


	/**
	*
	* @TODO document
	*
	*/
	function gfont_key($font_id){
		if(isset($this->foundry[$font_id]['name'])){
			$gfont_key_array[] = urlencode($this->foundry[$font_id]['name']) . (is_array($this->foundry[$font_id]['google']) ? ':' . implode(',', $this->foundry[$font_id]['google']) : '');
			return implode('|', $gfont_key_array);
		}
		else return '';


	}


	/**
	*
	* @TODO document
	*
	*/
	function get_stack($font_id){
		if( '' == $font_id || ! array_key_exists( $font_id, $this->foundry ) )
			$font_id = 'helvetica';

		return $this->foundry[$font_id]['family'];
	}




}


/**
*
* @TODO do
*
*/
function get_font_stack($font_slug){

	$foundry = new PageLinesFoundry;

	if ( '' == $font_slug || ! array_key_exists( $font_slug, $foundry->foundry ) )
		$font_slug = 'helvetica';

	return $foundry->foundry[$font_slug]['family'];
}

/**
*
* @TODO do
*
*/
function load_custom_font($font, $selectors){


	if( $font ){

		$foundry = new PageLinesFoundry;

		if ( !isset($foundry->foundry[$font]) )
			return '';

		$rule = sprintf('%s{font-family: %s;}', $selectors, $foundry->foundry[$font]['family']);

		$import = ($foundry->foundry[$font]['google']) ? $foundry->get_the_import($font) : '';

		return sprintf('<style type="text/css">%s%s</style>', $import, $rule);

	}else
		return '';

}

