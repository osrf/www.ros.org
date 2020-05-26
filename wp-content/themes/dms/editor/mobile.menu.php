<?php 



class PageLinesMobileMenu {
	
	
	
	function __construct(){
		
		add_action('pagelines_before_site', array( $this, 'menu_template'));
		
	}
	
	function menu_template(){
		
		$menu = ( pl_setting( 'primary_navigation_menu' ) ) ? pl_setting( 'primary_navigation_menu' ) : false;
		$menu2 = ( pl_setting( 'secondary_navigation_menu' ) ) ? pl_setting( 'secondary_navigation_menu' ) : false;
		?>
		<div class="pl-mobile-menu">
			<div class="mm-holder">
				<div class="mm-close">
					<i class="icon-remove icon-large"></i>
				</div>
				<?php
				
				if ( is_array( wp_get_nav_menu_items( $menu ) ) || has_nav_menu( 'primary' ) ) {
					
					wp_nav_menu(
						array(
							'menu_class'		=> 'mobile-menu primary-menu',
							'menu'				=> $menu,
							'container'			=> null,
							'container_class'	=> '',
							'depth'				=> 2,
							'fallback_cb'		=> ''
						)
					);
					
				} else
					pl_nav_fallback( 'mobile-menu primary-menu' );
					
				if ( is_array( wp_get_nav_menu_items( $menu2 ) ) ) {
					
					wp_nav_menu(
						array(
							'menu_class'		=> 'mobile-menu secondary-menu',
							'menu'				=> $menu2,
							'container'			=> null,
							'container_class'	=> '',
							'depth'				=> 1,
							'fallback_cb'		=> ''
						)
					);
					
				} 
				
				
				$twitter = pl_setting('twittername'); 
				$facebook = pl_setting('facebook_name');
				
				?>
				<div class="social-menu">
					
					<?php 
					
						if($facebook)
							printf('<a href="http://www.facebook.com/%s"><i class="mm-icon icon-large icon-facebook"></i></a>', $facebook);
						
						if($twitter)
							printf('<a href="http://www.twitter.com/%s"><i class="mm-icon icon-large icon-twitter"></i></a>', $twitter); 
							
						printf('<a href="%s"><i class="mm-icon icon-large icon-rss"></i></a>', get_bloginfo( 'rss2_url' ) );?>
				</div>
			</div>
		</div>
		<?php 
		
	}
	
	
	
}
