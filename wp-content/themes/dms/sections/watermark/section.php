<?php
/*
	Section: Watermark
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Displays your most popular, and latest posts as well as comments and tags in a tabbed format.
	Class Name: PLWatermark
	Filter: widgetized
	Loading: active
*/

class PLWatermark extends PageLinesSection {

	function section_persistent(){
	
	}

	function section_opts(){
		$opts = array(
			array(
				'type' 	=> 	'multi',
				'title' 		=> __( 'Website Watermark', 'pagelines' ),
				'help' 		=> __( 'The website watermark is a small version of your logo for your footer. Recommended width/height is 90px.', 'pagelines' ),

				'opts'	=> array(
					array(
						'key'			=> 'watermark_image',
						'type' 			=> 'image_upload',
						'label' 		=> __( 'Watermark Image', 'pagelines' ),
						'default'		=> $this->base_url . '/default-watermark.png',
						'imgsize'			=> '44'
					),
					array(
						'key'			=> 'watermark_link',
						'type' 			=> 'text',
						'label'			=> __( 'Watermark Link (Blank for None)', 'pagelines' ),
						'default' 		=> 'http://www.pagelines.com'
					),
					array(
						'key'			=> 'watermark_alt',
						'type' 			=> 'text',
						'label' 		=> __( 'Watermark Link alt text', 'pagelines' ),
						'default' 		=> __( 'Build a website with PageLines', 'pagelines' )
					),
					array(
						'key'			=> 'watermark_hide',
						'type' 			=> 'check',
						'label'		 	=> __( "Hide Watermark", 'pagelines' )
					)
				),

			),
			array(
				'type' 	=> 	'help',
				'title' 		=> __( 'Setting Up Social Shares', 'pagelines' ),
				'help' 		=> __( 'To set up social, you need to set your global social user names under "options" > "social &amp; local"<br/><br/> After you have done that, these values will fill automatically.', 'pagelines' ),


			),
		); 
		
		return $opts;
	}


   function section_template() {
	
		add_action('wp_footer', array( $this, 'socializer_scripts'));
	
		$home = home_url();
		$twitter = $this->opt('twittername'); 
		$facebook = $this->opt('facebook_name');
		
		$twitter = ($twitter) ? $twitter : 'pagelines';
		$facebook = ($facebook) ? $facebook : 'pagelines';
	
		$twitter_url = sprintf('https://twitter.com/%s', $twitter); 
		$facebook_url = sprintf('https://www.facebook.com/%s', $facebook); 
	
		$powered = sprintf(
			'%s %s <a href="http://www.pagelines.com">PageLines DMS</a>',
			get_bloginfo('name'), 
			__('was created with', 'pagelines')
		
		); 
		
		$watermark_image = $this->opt('watermark_image') ? $this->opt('watermark_image') : $this->base_url.'/default-watermark.png'; 
		$watermark_link = $this->opt('watermark_link') ? $this->opt('watermark_link') : 'http://www.pagelines.com'; 
		$watermark_alt = $this->opt('watermark_alt') ? $this->opt('watermark_alt') : 'Build a website with PageLines'; 
		
		if(!$this->opt('watermark_hide')){
			$watermark = sprintf(
				'<div class="the-watermark stack-element"><a href="%s"><img src="%s" alt="%s"/></a></div>', 
				$watermark_link,
				$watermark_image, 
				$watermark_alt
			);
		} else 
			$watermark = '';
		
		
	?>
	<div class="pl-watermark">
		<div class="pl_global_social stack-element">

			<div class="fb-like" data-href="<?php echo $facebook_url;?>" data-send="false" data-layout="button_count" data-width="90" data-show-faces="false" data-font="arial" style="vertical-align: top"></div>

			<div class="g-plusone" data-size="medium" data-width="80" data-href="<?php echo $home; ?>"></div>

			<a href="<?php echo $twitter_url;?>" class="twitter-follow-button" data-width="150px" data-show-count="true" data-lang="en" data-show-screen-name="false">&nbsp;</a>

		</div>
		<?php if(!pl_is_pro()):?>
		<div class="powered-by stack-element" style="display: block; visibility: visible; opacity: 1;">
			<?php echo $powered;?>
		</div>
		<?php endif; ?>
		<?php echo $watermark; ?>
	</div>
	<?php
	
	
	


	}
	
	function socializer_scripts(){
		
		$app_id = '';
		if( $this->opt( 'facebook_app_id' ) )
			$app_id = sprintf( '&appId=%s', $this->opt( 'facebook_app_id' ) );
		?>

		<div id="fb-root"></div>
		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1<?php echo $app_id; ?>";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>


		<!-- Place this render call where appropriate -->
		<script type="text/javascript">
		  (function() {
		    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		    po.src = 'https://apis.google.com/js/plusone.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
		  })();
		</script>

		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

		<?php 
	}

}