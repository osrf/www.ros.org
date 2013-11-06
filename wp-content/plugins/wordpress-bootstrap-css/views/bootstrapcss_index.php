<?php
include_once( dirname(__FILE__).ICWP_DS.'worpit_options_helper.php' );
include_once( dirname(__FILE__).ICWP_DS.'widgets'.ICWP_DS.'bootstrapcss_widgets.php' );
?>
<div class="wrap">
	<div class="bootstrap-wpadmin">

		<div class="page-header">
			<a href="http://wwwicontrolwp.com/"><div class="icon32" id="icontrolwp-icon"><br /></div></a>
			<h2><?php _hlt_e( 'Bootstrap Options :: Twitter Bootstrap Plugin (from iControlWP)' ); ?></h2><?php _hlt_e( '' ); ?>
		</div>
		
		<div class="row">
			<div class="<?php echo $worpit_fShowAds? 'span9' : 'span12'; ?>">
			
				<form action="<?php echo $worpit_form_action; ?>" method="post" class="form-horizontal">
				<?php
					wp_nonce_field( $worpit_nonce_field );
					printAllPluginOptionsForm( $worpit_aAllOptions, $worpit_var_prefix, 1 );
				?>
				<div class="form-actions">
					<input type="hidden" name="<?php echo $worpit_var_prefix; ?>all_options_input" value="<?php echo $worpit_all_options_input; ?>" />
					<input type="hidden" name="icwp_plugin_form_submit" value="Y" />
					<button type="submit" class="btn btn-primary" name="submit"><?php _hlt_e( 'Save All Settings'); ?></button>
					</div>
				</form>
				
			</div><!-- / span9 -->
		
			<?php if ( $worpit_fShowAds ) : ?>
			<div class="span3" id="side_widgets">
		  		<?php echo getWidgetIframeHtml('side-widgets-wtb'); ?>
			</div>
			<?php endif; ?>
		</div><!-- / row -->
	
	</div><!-- / bootstrap-wpadmin -->
	<?php include_once( dirname(__FILE__).'/bootstrapcss_js.php' ); ?>
</div>