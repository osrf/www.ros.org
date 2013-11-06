<?php 


class PageLinesPageLoader{
	
	
	
	function __construct(){
		
		if( ! pl_draft_mode() )
			return;
		
		if( pl_less_dev() ){
			add_action('pagelines_head', array( $this, 'load_time_tracker_start')); 
			add_action('wp_footer', array( $this, 'load_time_tracker_stop'));
		}
		
		
		add_action('wp_footer', array( $this, 'loader_ready_script'), 20 );
		add_action('pagelines_head_last', array( $this, 'loader_inline_style') );
		add_action('pagelines_before_site', array( $this, 'loader_html') );
	}
	
	function load_time_tracker_start(){
		echo '<script>var start = new Date();</script>';
	}
	
	function load_time_tracker_stop(){
		echo '<script>jQuery(window).load(function() { console.log("editor load time (ms)"); console.log(new Date() - start); })</script>';
	}
	
	function loader_ready_script(){
		?>
		<script>
			jQuery( document ).ready(function() {
				jQuery(".pl-loader").fadeOut()
			})
		</script>
		<?php
	}
	
	function loader_inline_style(){
		?>
		<style>

				.no-js .pl-loader { display: none;  }
				body{margin: 0;}
				.pl-loader { display: block; position: fixed; top: 0; width: 100%; height: 100%; background: #fff; z-index: 100000; text-align: center;}
				.pl-loader, .pl-loader p{
					font-family: helvetica, arial, sans-serif; 
					color: #CCC !important; 
				}
				.pl-loader a{
					color: rgba(66, 133, 243,.8) !important;
				}
				.pl-spinner {
				   height:60px;
				   width:60px;
				   margin:0 auto;
				   position:relative;
				   -webkit-animation: pl-rotation .6s infinite linear;
				   border:6px solid rgba(66, 133, 243,.15);
				   border-radius:100%;
				}

				.pl-spinner:before {
				   content:"";
				   display:block;
				   position:absolute;
				   left:-6px;
				   top:-6px;
				   height:100%;
				   width:100%;
				   border-top:6px solid rgba(66, 133, 243,.8);
				   border-left:6px solid transparent;
				   border-bottom:6px solid transparent;
				   border-right:6px solid transparent;
				   border-radius:100%;
				}

				@-webkit-keyframes pl-rotation {
				   from {-webkit-transform: rotate(0deg);}
				   to {-webkit-transform: rotate(359deg);}
				}
				
				
			</style>
			
		<?php
	}

	function loader_html(){
		
		
		?>
		<div class="pl-loader">
			<div class="loader-text" style="padding: 200px 0;font-family: helvetica, arial, sans-serif; color: #CCC; font-size: 30px; line-height: 1.9em; font-weight: 300; ">
				<div class="pl-spinner"></div>
				<span style=""><?php _e('Loading DMS Editor', 'pagelines');?></span>
			</div>
			<div class="loader-sub" style="position: fixed; width: 100%; bottom: 15px; font-size: 11px; opacity: .7; text-align: center;">
				<?php _e('Issues loading? See the <a href="http://docs.pagelines.com/support-troubleshooting/common-issues" target="_blank">troubleshooting guide</a>.', 'pagelines');?>
			</div>
		</div>
		
		<?php
		
	}
}
