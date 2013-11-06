<?php
/**
 * PageLinesSection
 *
 * API for creating and using PageLines sections
 *
 * @package     PageLines Framework
 * @subpackage  Sections
 * @since       4.0
 */
class PageLinesSection {

	var $id;		// Root id for section.
	var $name;		// Name for this section.
	var $settings;	// Settings for this section
	var $base_dir;  // Directory for section
	var $base_url;  // Directory for section
	var $builder;  	// Show in section builder
	var $format;	// <section> format.
	var $classes;	// <section> classes.

	var $meta;

    /**
     * PHP5 constructor
     * @param   array $settings
     */
	function __construct( $settings = array() ) {


		/**
         * Assign default values for the section
         * @var $defaults string
         */
		$this->defaults = array(
				'markup'			=> null, // needs to be null for overriding
				'workswith'		 	=> array('content'),
				'description' 		=> null,
				'isolate'			=> array(),
				'required'			=> null,
				'version'			=> 'all',
				'base_url'			=> PL_SECTION_ROOT,
				'dependence'		=> '',
				'posttype'			=> '',
				'failswith'			=> array(),
				'cloning'			=> false,
				'map'				=> '',
				'tax_id'			=> '',
				'format'			=> 'textured',
				'classes'			=> '',
				'less'				=> false,
				'filter'			=> 'misc',
				'loading'			=> 'reload'
			);

		$this->settings = wp_parse_args( $settings, $this->defaults );

		$this->hook_get_view();

		$this->class_name = get_class($this);

		$this->set_section_info();

	}

	/**
     * Set Section Info
     *
     * Read information from the section header; assigns values found, or sets general default values if not
     *
     * @since   ...
     *
     * @uses    pagelines_register_sections
     * @uses    section_install_type
     * @uses    PL_ADMIN_ICONS
     * @uses    PL_ADMIN_IMAGES
     */
	function set_section_info(){

		global $load_sections;
		$available = $load_sections->pagelines_register_sections( false, true );

		$type = $this->section_install_type( $available );

		$this->sinfo = $available[$type][$this->class_name];

		// File location information
		$this->base_dir = $this->settings['base_dir'] = $this->sinfo['base_dir'];
		$this->base_file = $this->settings['base_file'] = $this->sinfo['base_file'];
		$this->base_url = $this->settings['base_url'] = $this->sinfo['base_url'];

		$this->images = $this->base_url . '/images';

		// Reference information
		$this->id = $this->settings['id'] = basename( $this->base_dir );

		$this->name = $this->settings['name'] = $this->sinfo['name'];
		$this->description = $this->settings['description'] = $this->sinfo['description'];
		$this->map = "";
		$this->filter = $this->settings['filter'] = ( !empty( $this->sinfo['filter'] ) ) ? $this->sinfo['filter'] : $this->settings['filter'];
		$this->loading = $this->settings['loading'] = ( !empty( $this->sinfo['loading'] ) ) ? $this->sinfo['loading'] : $this->settings['loading'];
		$this->settings['cloning'] = ( !empty( $this->sinfo['cloning'] ) ) ? $this->sinfo['cloning'] : $this->settings['cloning'];
		$this->settings['workswith'] = ( !empty( $this->sinfo['workswith'] ) ) ? $this->sinfo['workswith'] : $this->settings['workswith'];
		$this->settings['version'] = ( !empty( $this->sinfo['edition'] ) ) ? $this->sinfo['edition'] : $this->settings['version'];
		$this->settings['failswith'] = ( !empty( $this->sinfo['failswith'] ) ) ? $this->sinfo['failswith'] : $this->settings['failswith'];
		$this->settings['tax_id'] = ( !empty( $this->sinfo['tax'] ) ) ? $this->sinfo['tax'] : $this->settings['tax_id'];
		$this->settings['format'] = ( !empty( $this->sinfo['format'] ) ) ? $this->sinfo['format'] : $this->settings['format'];
		$this->settings['classes'] = ( !empty( $this->sinfo['classes'] ) ) ? $this->format_classes( $this->sinfo['classes'] ) : $this->settings['classes'];
		$this->settings['p_ver'] = $this->sinfo['version'];


		/*
		 * SECTION PAGE ISOLATION
		 */
			$this->isolate = ( !empty( $this->sinfo['isolate'] ) ) ? $this->sinfo['isolate'] : $this->settings['isolate'];
		/*
		 * SECTION WRAPPER CLASSES
		 */
			$this->wrapper_classes = array();

		/*
		 * STANDARD IMAGES
		 */
		$this->icon = $this->settings['icon'] = ( is_file( sprintf( '%s/icon.png', $this->base_dir ) ) ) ? sprintf( '%s/icon.png', $this->base_url ) : PL_ADMIN_ICONS . '/leaf.png';

		if( is_file( sprintf( '%s/thumb.png', $this->base_dir ) ) ){
			$this->screenshot = $this->settings['screenshot'] = sprintf( '%s/thumb.png', $this->base_url );
		} else {
		
			$scrn_url = ($this->sinfo['type'] == 'custom' && is_child_theme()) ? PL_CHILD_URL : PL_PARENT_URL; 
			
			$this->screenshot = $this->settings['screenshot'] = sprintf( '%s/screenshot.png', $scrn_url );
		
		}

		$this->thmb = $this->screenshot;

		if( is_file( sprintf( '%s/splash.png', $this->base_dir ) ) ){
			$this->splash = $this->settings['splash'] = sprintf( '%s/splash.png', $this->base_url );
		} else {
			$this->splash = $this->settings['splash'] = PL_IMAGES . '/thumb-missing.png';
		}

		$this->deprecated_setup();

		load_plugin_textdomain($this->id, false, sprintf( 'pagelines-sections/%s/lang', $this->id ) );
		
		// set to true before ajax load
		$this->active_loading = false; 


	}

	function deprecated_setup(){

		$this->special_classes = ''; //--> deprecated in v3, used in NavBar

		$this->optionator_default = array(
			'clone_id'	=> 1,
			'active'	=> true,
			'mode'		=> null,
			'type'		=> ''
		);
		
		$this->oset = '';
		$this->tset = '';

	}

	function prefix( $clone_id = false ){

		if( pl_has_editor() && isset($this->meta[ 'clone' ]) )
			$prefix = sprintf('.section-%s[data-clone="%s"]', $this->id, $this->meta[ 'clone' ]);
		elseif( $clone_id && $clone_id != '')
			$prefix = sprintf('.section-%s.clone_%s', $this->id, $clone_id);
		else
			$prefix = '';

		return $prefix;
	}

	function get_the_id() {
		return ( isset( $this->meta['clone'] ) ) ? $this->meta['clone'] : '';
	}

	function opt( $key, $args = array() ){

		global $plpg;
		
		$d = array(
			'default'	=> false,
			'scope'		=> 'cascade'
		);

		$a = wp_parse_args($args, $d);
		
		if( $a['scope'] == 'global' || $a['scope'] == 'type' || $a['scope'] == 'local' ){
			
			$val = pl_setting( $key, array( 'clone_id' => $this->meta[ 'clone' ], 'scope' => $a['scope'] ) );
			
		} else {
							
			if(
				property_exists($this, 'meta')
				&& isset($this->meta[ 'set' ])
				&& isset($this->meta[ 'set' ])
				&& isset($this->meta[ 'set' ][ $key ] )
				&& $this->meta[ 'set' ][ $key ] != ''
			){
								
				$val = $this->meta[ 'set' ][ $key ];
			
			} elseif( pl_setting( $key, $args) ){
			
			
				$val = pl_setting( $key, $args);
			 
			} else
				$val = $a['default'];
			
		}

		if( $val == '' )
			return false; 
		elseif( is_array( $val) )
			return $val;
		else
			return do_shortcode( $val );

	}
	
	function opt_update( $key, $value, $scope = 'global' ){
		
		
		global $plpg;
		
		if( is_array( $value ) ){
			foreach( $value as $sxi => $setindex){
				if( is_array( $setindex ) ){
					foreach( $setindex as $sk => $setkey){
						if( !$setkey || $setkey == 'false' ){
							unset( $value[$sxi][$sk] );
						}
					}
				}
			
			}
		
		}
		
		$args = array(
			'key'	=> $key, 
			'val'	=> $value, 
			'scope'	=> $scope, 
			'uid'	=> $this->meta[ 'clone' ]
		);
		
		pl_setting_update( $args );
		
		
	}
	
	/*
	 * Upgrade options from old one off option format to a new array format
	 * All parameters are required and care should be taken to make sure its non destructive
	 */ 
	function upgrade_to_array_format( $new_key, $array, $mapping, $number ){
		
		$scopes = array('local', 'type', 'global');
		
		if( ! $number )
			return $array;
		
		// Maybe Upgrade
		if( !$array || $array == 'false' || empty( $array ) ){
			

			for($i = 1; $i <= $number; $i++){
				
				// Set up new output for viewing
				foreach( $mapping as $new_index_key => $old_option_key ){
					$old_settings[ $i ][ $new_index_key ] = $this->opt( sprintf($old_option_key, $i) );
				}
				
				// Load up old values using cascade
				foreach( $scopes as $scope ){
					
					foreach( $mapping as $new_index_key => $old_option_key ){

						$upgrade_array[$scope]['item'.$i][ $new_index_key ] = $this->opt( sprintf($old_option_key, $i), array('scope' => $scope) );
					
					}
					
				}

			}

			// Setup in new format & update
			foreach($scopes as $scope){
				$this->opt_update( $new_key, $upgrade_array[$scope], $scope ); 

			}
			
			return $old_settings; 
			
		} else 
			return $array;
		
	}

	function format_classes( $classes ) {

		$classes = str_replace( ',', ' ', str_replace( ' ', '', $classes ) );

		return $classes;
	}

    /**
     * Section Install Type
     *
     * @since   ...
     *
     * @param   $available string
     *
     * @return  string
     */
	function section_install_type( $available ){

		if ( isset( $available['custom'][$this->class_name] ) )
			return 'custom';
		elseif ( isset( $available['child'][$this->class_name] ) )
			return 'child';
		elseif ( isset( $available['parent'][$this->class_name] ) )
			return 'parent';
		else {

			/**
			 * We dont know the type, could be a 3rd party plugin.
			 */
			$results = array_search_ext($available, $this->class_name, true);
			if ( is_array( $results ) && isset( $results[0]['keys']))
				return $results[0]['keys'][0];
		}

	}

    /**
     * Section Template
     *
     * The 'section_template()' function is the most important section function.
     * Use this function to output all the HTML for the section on pages/locations where it's placed.
     *
     * Subclasses should over-ride this function to generate their section code.
     *
     * @since   ...
     */
	function section_template() {
		die('function PageLinesSection::section_template() must be over-ridden in a sub-class.');
	}

	/**
     * Passive Section Load Template
  	 * If a section is loaded through a hook use this builder instead of the one
     * inside of the template class.
 	 *
     * @since   2.1.6
     */
	function passive_section_template( $hook_name = false ){

		$this->passive_hook = $hook_name;

		$location = 'passive';

		$markup = ( isset( $this->settings['markup'] ) ) ? $this->settings['markup'] : 'content';

		$this->before_section_template( $location );

		$this->before_section( $markup );

		$this->section_template( $location );

		$this->after_section( $markup );

		$this->after_section_template();
	}
	

	function before_section( $markup = 'content', $clone_id = null, $classes = ''){


		pagelines_register_hook('pagelines_before_'.$this->id, $this->id); // hook
			
			$section_id = $this->id;
			
			$classes .= sprintf(" section-%s %s", $section_id, $this->special_classes);
	
			$span = (isset($this->meta['span'])) ? 'span'.$this->meta['span'] : 'span12';
			$offset = (isset($this->meta['offset'])) ? 'offset'.$this->meta['span'] : 'offset0';

			$classes .= ' '.$span.' '.$offset;

			printf('<section id="%s" data-sid="%s" data-clone="%s" class="pl-section fix %s">', $this->id.$clone_id, $this->id, $clone_id, trim($classes));

			pagelines_register_hook('pagelines_outer_'.$this->id, $this->id); // hook
		

		pagelines_register_hook('pagelines_inside_top_'.$this->id, $this->id); // hook
 	}
	
	function after_section(){
		echo '</section>';
	}
	
	
    /**
     * Before Section Template
     *
     * For template code that should show before the standard section markup
     *
     * @since   ...
     *
     * @param   null $clone_id
     */
	function before_section_template( $clone_id = null ){}

    /**
     * After Section Template
     *
     * For template code that should show after the standard section markup
     *
     * @since   ...
     *
     * @param   null $clone_id
     */
	function after_section_template( $clone_id = null ){}

    /**
     * Section Template Load
     *
     * Checks for overrides and loads section template function
     *
     * @since   ...
     *
     * @param   $clone_id
     *
     * @uses    section_template
     */
	function section_template_load( $clone_id ) {

		// Variables for override
		$override_template = 'template.' . $this->id .'.php';
		$override = ( '' != locate_template(array( $override_template), false, false)) ? locate_template(array( $override_template )) : false;

		if( $override != false) require( $override );
		else{
			$this->section_template();
		}

	}



    /**
     * Section Persistent
     *
     * Use this function to add code that will run on every page in your site & admin
     * Code here will run ALL the time, and is useful for adding post types, options etc.
     *
     * @since   ...
     */
	function section_persistent(){}


    /**
     * Section Init
     *
     * @since 2.2
     *
     * @TODO Add section varible defaults. Used in __consruct()
     */
	function section_init() {

		$this->format	= ( $this->format ) ? $this->format : 'textured';
		$this->classes	= ( $this->classes ) ? sprintf( ' %s', ltrim( $this->classes ) )  : '';
	}

	/**
     * Scripts to be loaded inline after page load
     *
     */
	function section_on_ready(){}

    /**
     * Section Admin
     *
     * @since   ...
     * @TODO document
     */
	function section_admin(){}


    /**
     * Section Head
     *
     * Code added in this function will be run during the <head> element of the
     * site's 'front-end' pages. Use this to add custom Javascript, or manually
     * add scripts and meta information. It will *only* be loaded if the section
     * is present on the page template.
     *
     * @since   ...
     */
	function section_head(){}
		
	/**
     * Section Foot
     *
     * Code added in this function will be run during the wp_footer hook
     *
     */
	function section_foot(){}


    /**
     * Section Styles
     *
     * @since   ...
     * @TODO document
     */
	function section_styles(){}


    /**
     * Section Options
     *
     * @since   ...
     * @TODO document
     */
	function section_options(){}

    /**
     * Section Optionator
     *
     * Handles section options
	 *
     */
	function section_optionator( $settings ){}


	/**
     * Section Opts
     *
     * Loads section options simply
	 *
	 * @since b3.0.0
     */
	function section_opts(){ return array(); }


    /**
     * Section Scripts
     *
     * @since   ...
     * @TODO document
     */
    function section_scripts(){}


    /**
     * Hook Get View
     *
     * @since   ...
     *
     * @TODO document
     */
	function hook_get_view(){

		add_action('wp_head', array( $this, 'get_view'), 10);
	}

	/**
     * Get View
     *
     * @since   ...
     * @TODO document
     */
	function get_view(){

		if(is_single())
			$view = 'single';
		elseif(is_archive())
			$view = 'archive';
		elseif( is_page_template() )
			$view = 'page';
		else
			$view = 'default';

		$this->view = $view;
	}


    /**
     * Runs before any html loads, but in the page.
     *
     * @package     PageLines Framework
     * @subpackage  Sections
     * @since       1.0.0
     *
     * @param       $clone_id
     */
	function setup_oset( $clone_id ){

		global $pagelines_ID;

		$view = ( isset( $this->view ) ) ? $this->view : '';
		// Setup common option configuration, considering clones and page ids
		$this->oset = array(
			'post_id'		=> $pagelines_ID,
			'clone_id'		=> $clone_id,
			'group'			=> $this->id,
			'view'			=> $view
			);
		$this->tset = $this->oset;
		$this->tset['translate'] = true;
	}

}
/********** END OF SECTION CLASS  **********/

/**
 * PageLines Section Factory (class)
 *
 * Singleton that registers and instantiates PageLinesSection classes.
 *
 * @package     PageLines Framework
 * @subpackage  Sections
 * @since       1.0.0
 */
class PageLinesSectionFactory {
	var $sections  = array();
	var $unavailable_sections  = array();

    /**
     * Register
     */
	function register($section_class, $args) {

		if(class_exists($section_class))
			$this->sections[$section_class] = new $section_class( $args );

	}


    /**
     * Unregister
     */
	function unregister($section_class) {
		if ( isset($this->sections[$section_class]) )
			unset($this->sections[$section_class]);
	}

}

