<?php
/**
 *
 *
 *  Options Layout Class
 *
 *
 *  @package PageLines DMS
 *  @subpackage Options
 *  @since 4.0
 *
 */

class DMSOptionsUI {

/*
	Build The Layout
*/
	function __construct( $args = array() ) {

		$defaults = array(
				'title'			=> '',
				'callback'		=> false, 
				'config'	=> array()
			);

		$this->set = wp_parse_args( $args, $defaults );
			
		$this->config = (isset($this->set['callback'])) ? call_user_func( $this->set['callback'] ) : $this->set['config'];

		// Draw the thing
		$this->build_header();
		$this->build_body();
		$this->build_footer();

	}


		/**
		 * Option Interface Header
		 */
		function build_header(){?>
			
			<div class="wrap">
			
					
<?php }

		/**
		 * Option Interface Footer
		 */
		function build_footer(){?>
			<?php  // submit_button(); ?>
		
			</div>
		<?php }

		/**
		 * Option Interface Body, including vertical tabbed nav
		 */
		function build_body(){
			$option_engine = new DMSOptEngine();

?>
				<h2 class="nav-tab-wrapper">
					<?php
					
					$current = (isset($_GET['tab'])) ? $_GET['tab'] : 'default';
					$count = 1;
					foreach( $this->config as $tabs ){
						
						if( $tabs['slug'] == $current || ($current == 'default' && $count == 1) ){
							$class = 'nav-tab-active';
						} else 
							$class = '';
							
				        printf( '<a class="nav-tab %s" href="?page=PageLines-Admin&tab=%s">%s</a>', $class, $tabs['slug'], $tabs['title'] );
						$count++;
				    }
				   

					?>
				</h2>
<?php 			

		foreach( $this->config as $tabs ){
			// The tab container start....
			printf('<div id="%s" class="tabinfo">', $tabs['slug'] );
			
			foreach( $tabs['groups'] as $groups ){
				
				if( isset($groups['title']) && ! empty($groups['title']))
					printf('<h3>%s</h3>', $groups['title']); 
					
				if( isset($groups['desc']) && ! empty($groups['desc']))
					printf('<p>%s</p>', $groups['desc']);
					
				echo '<table class="form-table fix"><tbody>';
				
				foreach( $groups['opts'] as $oid => $o ){
					
					$option_engine->option_engine($oid, $o);
					
				}
				
				echo '</tbody></table>';
				
			}
			
			echo '<div class="clear"></div></div>';
	
		}
					
	}


} // End Class

/**
 * Option Engine Class
 *
 * Sorts and Draws options based on the 'option array'
 * Option array is loaded in config.option.php and through filters
 *
 */
class DMSOptEngine {

    /**
     * PHP5 Constructor
     *
     * @param   null $settings_field
     * @version 2.2 - alphabetized defaults listing; add 'docstitle' setting
     */
	function __construct() {
		

		$this->defaults = array(
			
			'placeholder'	=> '',
			'disabled'		=> false,
			'type'			=> 'text',
			'label'			=> false,
			'key'			=> '',
			'title'			=> false,
		);
		
	}

	/**
	 * Option generation engine
	 */
	function option_engine($oid, $o, $flag = null, $setting = null){
		
		$o = wp_parse_args( $o, $this->defaults );

		
		if($o['disabled'])
			return;
			
	 
		$o['placeholder'] = pl_html($o['placeholder']);
		
		?>
		
				<tr valign="top">
					<th scope="row" class="titledesc"><label for="<?php echo $o['key']; ?>"><?php echo $o['title']; ?></label></th>
					<td> <?php $this->option_breaker($oid, $o); ?> </td>
				</tr>

<?php  }
	

	/**
	 * 
	 * Option Breaker 
	 * Switches through an option array, generating the option handling and markup
	 *
	 */
	function option_breaker($oid, $o, $setting = '', $val = ''){

		switch ( $o['type'] ){

			default :
				do_action( 'pagelines_options_' . $o['type'] , $oid, $o);
				break;

		} 

	}
	
	


} // End of Class