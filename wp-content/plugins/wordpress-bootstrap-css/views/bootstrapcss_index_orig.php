<?php
	include_once( dirname(__FILE__).ICWP_DS.'worpit_options_helper.php' );
	include_once( dirname(__FILE__).ICWP_DS.'widgets'.ICWP_DS.'bootstrapcss_widgets.php' );
	
	$iWidthColumnOne = 8;
?>
<div class="wrap">
	
<div class="bootstrap-wpadmin">

	<div class="page-header">
		<a href="http://worpit.com/"><div class="icon32" id="icontrolwp-icon"><br /></div></a>
		<h2><?php _hlt_e( 'Bootstrap Options :: Twitter Bootstrap Plugin from Worpit' ); ?></h2><?php _hlt_e( '' ); ?>
	</div>
	
	<div class="row">
		<div class="span9">
	
			<form class="form-horizontal" method="post" action="<?php echo $hlt_form_action; ?>">
				<div class="row">
					<div class="span<?php echo $iWidthColumnOne; ?>">
						<fieldset>
							<legend><?php _hlt_e( 'Choose which type of bootstrap CSS you would like installed' ); ?></legend>
						
							<div class="control-group">
								<label class="control-label" for="hlt_bootstrap_option"><?php _hlt_e( 'Desired Bootstrap CSS' ); ?><br/><span class="label">Default: None</span></label>
								<div class="controls">
									<label class="select">
										<select id="hlt_bootstrap_option" name="hlt_bootstrap_option" style="width:250px">
											<option value="none" id="hlt-none" <?php if ( $hlt_option == 'none' ): ?>selected="selected"<?php endif; ?> >
												<?php _hlt_e( 'None' ); ?>
											</option>
											<option value="twitter" id="hlt-twitter" <?php if ( $hlt_option == 'twitter' ): ?>selected="selected"<?php endif; ?>>
												<?php _hlt_e( 'Twitter Bootstrap CSS' ); ?>
											</option>
											<option value="yahoo-reset-3" id="hlt-yahoo-reset-3" <?php if ( $hlt_option == 'yahoo-reset-3' ): ?>selected="selected"<?php endif;?>>
												<?php _hlt_e( 'Yahoo UI Reset CSS v3.4.1' ); ?>
											</option>
											<option value="yahoo-reset" id="hlt-yahoo-reset" <?php if ( $hlt_option == 'yahoo-reset' ): ?>selected="selected"<?php endif;?>>
												<?php _hlt_e( 'Yahoo UI Reset CSS v2.9.0' ); ?>
											</option>
											<option value="normalize" id="hlt-normalize" <?php if ( $hlt_option == 'normalize' ): ?>selected="selected"<?php endif; ?>>
												<?php _hlt_e( 'Normalize CSS' ); ?>
											</option>
										</select>
									</label>
									<div id="desc_block">
										<div id="desc_none" class="desc <?php if ( $hlt_option != 'none' ): ?>hidden<?php endif; ?>">
											<p class="help-block"><?php _hlt_e('No reset CSS will be applied'); ?></p>
										</div>
										<div id="desc_twitter" class="desc <?php if ( $hlt_option != 'twitter' ): ?>hidden<?php endif; ?>">
											<p class="help-block"><?php _hlt_e('Bootstrap, from Twitter (latest release:'); ?>  v2.0.4) <a href="http://twitter.github.com/bootstrap/index.html" target="_blank"><span class="label label-info">more info</span></a></p>
										</div>
										<div id="desc_yahoo-reset" class="desc <?php if ( $hlt_option != 'yahoo-reset' ): ?>hidden<?php endif; ?>">
											<p class="help-block"><?php _hlt_e('YahooUI Reset CSS is a basic reset for all browsers'); ?></p>
										</div>
										<div id="desc_yahoo-reset-3" class="desc <?php if ( $hlt_option != 'yahoo-reset' ): ?>hidden<?php endif; ?>">
											<p class="help-block"><?php _hlt_e('YahooUI Reset CSS is a basic reset for all browsers'); ?></p>
										</div>
										<div id="desc_normalize" class="desc <?php if ( $hlt_option != 'normalize' ): ?>hidden<?php endif; ?>">
											<p class="help-block"><?php _hlt_e('Normalize CSS.'); ?></p>
										</div>
									</div>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="hlt-option-customcss"><?php _hlt_e( 'Custom Reset CSS' ); ?><br/><span class="label"><?php _hlt_e( 'Default: Off' ); ?></span></label>
								<div class="controls">
									<div class="option_section <?php if ( $hlt_option_customcss == 'Y' ): ?>selected_item<?php endif; ?>" id="section-hlt-option-customcss">
										<label class="checkbox">
											<input type="checkbox" name="hlt_bootstrap_option_customcss" value="Y" id="hlt-option-customcss" <?php if ( $hlt_option_customcss == 'Y' ): ?>checked="checked"<?php endif; ?> />
											<?php _hlt_e( 'Enable custom CSS link' ); ?>
										</label>
										<p class="help-block"><?php _hlt_e('(note: linked after any bootstrap/reset CSS selected above)' ); ?>
										
										<label for="hlt-text-customcss-url">
											<br class="clear" /><?php _hlt_e( 'Custom CSS URL' ); ?>:
											<input class="span5" type="text" name="hlt_bootstrap_text_customcss_url" id="hlt-text-customcss-url" size="100" value="<?php echo $hlt_text_customcss_url; ?>" style="margin-left:20px;" />
										</label>
										<p class="help-block">
											<?php _hlt_e( "Provide the <strong>full</strong> URL path." ); ?>
										</p>
									</div>
								</div>
							</div>
						
						</fieldset>
					</div>
				</div><!-- / row -->
	
				<div class="row" id="BootstrapJavascript">
					<div class="span<?php echo $iWidthColumnOne; ?>">
						<fieldset>
						  <legend><?php _hlt_e( 'Twitter Bootstrap Javascript Library Options' ); ?></legend>
						  <div class="row" style="height:20px"></div>
	
							<div class="twitter_extra">
								<div class="control-group" id="controlAllJavascriptLibraries">
									<label class="control-label" for="hlt-all-js"><?php _hlt_e( 'All Javascript Libraries' ); ?><br/><span class="label"><?php _hlt_e( 'Default: Off' ); ?></span></label>
									<div class="controls">
										<div class="option_section <?php if ( $hlt_option_all_js == 'Y' ): ?>selected_item<?php endif; ?>">
											<label class="checkbox" for="hlt-all-js">
												<input type="checkbox" name="hlt_bootstrap_option_all_js" value="Y" id="hlt-all-js" <?php if ( $hlt_option_all_js == 'Y' ): ?>checked="checked"<?php endif; ?> />
												<?php _hlt_e('Include ALL Bootstrap Javascript libraries.' ); ?>
											</label>
											<p class="help-block">
												<?php _hlt_e( "Support for selecting individual libraries was removed from v2.0.3." ); ?>
												<br />
												<?php _hlt_e('This will also include the jQuery library if it is not already included.' ); ?>
											</p>
										</div>
									</div>
								</div>
	
								<div id="controlIndividualLibrariesList" class="hidden"></div>
							</div>
							<div class="control-group">
								<label class="control-label" for="hlt-js-head"><?php _hlt_e('Javascript Placement'); ?><br/><span class="label"><?php _hlt_e( 'Default: Off' ); ?></span></label>
								<div class="controls">
									<div class="option_section <?php if ( $hlt_option_js_head == 'Y' ): ?>selected_item<?php endif; ?>" id="section-hlt-js-head">
										<label class="checkbox" for="hlt-js-head">
											<input type="checkbox" name="hlt_bootstrap_option_js_head" value="Y" id="hlt-js-head" <?php if ( $hlt_option_js_head == 'Y' ): ?>checked="checked"<?php endif; ?> />
											<?php _hlt_e( 'Place Javascript in &lt;HEAD&gt;' ); ?>
										</label>
										<p class="help-block">
											<?php _hlt_e( 'Only check this option if know you need it.' ); ?>
										</p>
									</div>
								</div>
							</div>
						</fieldset>
						<fieldset>
							<legend><?php _hlt_e( 'Extra Twitter Bootstrap Options' ); ?></legend>
	
							<div class="control-group">
								<label class="control-label" for="hlt-option-useshortcodes"><?php _hlt_e( 'Bootstrap Shortcodes' ); ?><br/><span class="label"><?php _hlt_e( 'Default: Off' ); ?></span></label>
								<div class="controls">
									<div class="option_section <?php if ( $hlt_option_useshortcodes == 'Y' ): ?>selected_item<?php endif; ?>" id="section-hlt-option-useshortcodes">
										<label class="checkbox" for="hlt-option-useshortcodes">
											<input type="checkbox" name="hlt_bootstrap_option_useshortcodes" value="Y" id="hlt-option-useshortcodes" <?php if ( $hlt_option_useshortcodes == 'Y' ): ?>checked="checked"<?php endif; ?> />
											<?php _hlt_e('Enable Twitter Bootstrap Shortcodes' ); ?>
										</label>
										<p class="help-block">
											<?php _hlt_e('Loads WordPress shortcodes for fast use of Twitter Bootstrap Components.' ); ?>
										</p>
									</div>
								</div>
							</div>
	
							<div class="control-group">
								<label class="control-label" for="hlt_bootstrap_option_use_minified_css"><?php _hlt_e( 'Use Minified CSS' ); ?><br/><span class="label"><?php _hlt_e( 'Default: Off' ); ?></span></label>
								<div class="controls">
									<div class="option_section <?php if ( $hlt_option_use_minified_css == 'Y' ): ?>selected_item<?php endif; ?>" id="section_hlt_bootstrap_option_use_minified_css">
										<label class="checkbox" for="hlt_bootstrap_option_use_minified_css">
											<input type="checkbox" name="hlt_bootstrap_option_use_minified_css" value="Y" id="hlt_bootstrap_option_use_minified_css" <?php if ( $hlt_option_use_minified_css == 'Y' ): ?>checked="checked"<?php endif; ?> />
											<?php _hlt_e('Use Minified CSS' ); ?>
										</label>
										<p class="help-block">
											<?php _hlt_e('Uses minified CSS libraries where available.' ); ?>
										</p>
									</div>
								</div>
							</div>
	
							<div class="control-group">
								<label class="control-label" for="hlt_bootstrap_option_replace_jquery_cdn"><?php _hlt_e( 'Replace JQuery' ); ?><br/><span class="label"><?php _hlt_e( 'Default: Off' ); ?></span></label>
								<div class="controls">
									<div class="option_section <?php if ( $hlt_option_replace_jquery_cdn == 'Y' ): ?>selected_item<?php endif; ?>" id="section_hlt_bootstrap_option_replace_jquery_cdn">
										<label class="checkbox" for="hlt_bootstrap_option_replace_jquery_cdn">
											<input type="checkbox" name="hlt_bootstrap_option_replace_jquery_cdn" value="Y" id="hlt_bootstrap_option_replace_jquery_cdn" <?php if ( $hlt_option_replace_jquery_cdn == 'Y' ): ?>checked="checked"<?php endif; ?> />
											<?php _hlt_e('Replace JQuery library link with Google CDN link' ); ?>
										</label>
										<p class="help-block">
											<?php _hlt_e('In case your WordPress version is too old and doesn\'t have the necessary JQuery version, this will replace your JQuery with a compatible version served from Google CDN.' ); ?>
										</p>
									</div>
								</div>
							</div>

							<div class="control-group">
								<label class="control-label" for="hlt_bootstrap_option_use_compiled_css"><?php _hlt_e( 'Enable LESS Compiler' ); ?><br/><span class="label"><?php _hlt_e( 'Default: Off' ); ?></span></label>
								<div class="controls">
									<div class="option_section <?php if ( $hlt_option_use_compiled_css == 'Y' ): ?>selected_item<?php endif; ?>" id="section_hlt_bootstrap_option_use_compiled_css">
										<label class="checkbox" for="hlt_bootstrap_option_use_compiled_css">
											<input type="checkbox" name="hlt_bootstrap_option_use_compiled_css" value="Y" id="hlt_bootstrap_option_use_compiled_css" <?php if ( $hlt_option_use_compiled_css == 'Y' ): ?>checked="checked"<?php endif; ?> />
											<?php _hlt_e('Enables LESS Compiler Section' ); ?>
										</label>
										<p class="help-block">
											<?php _hlt_e('Use the LESS Compiler to customize your Twitter Bootstrap CSS.' ); ?>
										</p>
									</div>
								</div>
							</div>
	
						</fieldset>
					</div><!-- / span8 -->
				</div><!-- / row -->
	
				<div class="row" id="MiscOptionBox">
					<div class="span<?php echo $iWidthColumnOne; ?>">
						<fieldset>
							<legend><?php _hlt_e( 'Enable or Disable any of the following options as desired' ); ?></legend>
						
							<div class="control-group">
								<label class="control-label" for="hlt-option-inc_bootstrap_css_wpadmin"><?php _hlt_e( 'Admin Bootstrap CSS' ); ?><br/><span class="label"><?php _hlt_e( 'Default: Off' ); ?></span></label>
								<div class="controls">
									<div class="option_section <?php if ( $hlt_option_inc_bootstrap_css_wpadmin == 'Y' ): ?>selected_item<?php endif; ?>" id="section-hlt-option-inc_bootstrap_css_wpadmin">
										<label class="checkbox" for="hlt-option-inc_bootstrap_css_wpadmin">
											<input type="checkbox" name="hlt_bootstrap_option_inc_bootstrap_css_wpadmin" value="Y" id="hlt-option-inc_bootstrap_css_wpadmin" <?php if ( $hlt_option_inc_bootstrap_css_wpadmin == 'Y' ): ?>checked="checked"<?php endif; ?> />
											<?php _hlt_e( 'Include Twitter Bootstrap CSS in the WordPress Admin' ); ?>
										</label>
										<p class="help-block">
											<?php _hlt_e( 'Not a standard Twitter Bootstrap CSS. <a href="http://bit.ly/HgwlZI" target="_blank"><span class="label label-info">more info</span></a>' ); ?>
										</p>
									</div>
								</div>
							</div>
	
							<div class="control-group">
								<label class="control-label" for="hlt-option-hide_dashboard_rss_feed"><?php _hlt_e('Hide HLT News Feed'); ?><br/><span class="label"><?php _hlt_e( 'Default: Off' ); ?></span></label>
								<div class="controls">
									<div class="option_section <?php if ( $hlt_option_hide_dashboard_rss_feed == 'Y' ): ?>selected_item<?php endif; ?>" id="section-hlt-option-dashboard_rss_feed">
										<label class="checkbox" for="hlt-option-hide_dashboard_rss_feed">
											<input type="checkbox" name="hlt_bootstrap_option_hide_dashboard_rss_feed" value="Y" id="hlt-option-hide_dashboard_rss_feed" <?php if ( $hlt_option_hide_dashboard_rss_feed == 'Y' ): ?>checked="checked"<?php endif; ?> />
											<?php _hlt_e( 'Hide the Host Like Toast news feed from the Dashboard' ); ?>
										</label>
										<p class="help-block">
											<?php _hlt_e( 'Hides our news feed from inside your Dashboard.' ); ?>
										</p>
									</div>
								</div>
							</div>
	
	
							<div class="control-group">
								<label class="control-label" for="hlt_bootstrap_option_delete_on_deactivate"><?php _hlt_e( 'Delete Plugin Settings' ); ?><br/><span class="label"><?php _hlt_e( 'Default: Off' ); ?></span></label>
								<div class="controls">
									<div class="option_section <?php if ( $hlt_option_delete_on_deactivate == 'Y' ): ?>selected_item<?php endif; ?>" id="section_hlt_bootstrap_option_delete_on_deactivate">
										<label class="checkbox" for="hlt_bootstrap_option_delete_on_deactivate">
											<input type="checkbox" name="hlt_bootstrap_option_delete_on_deactivate" value="Y" id="hlt_bootstrap_option_delete_on_deactivate" <?php if ( $hlt_option_delete_on_deactivate == 'Y' ): ?>checked="checked"<?php endif; ?> />
											<?php _hlt_e('Delete All Plugin Setting Upon Plugin Deactivation' ); ?>
										</label>
										<p class="help-block">
											<?php _hlt_e('Careful: Removes all plugin options when you deactivite the plugin.' ); ?>
										</p>
									</div>
								</div>
							</div>
	
							<div class="control-group">
								<label class="control-label" for="hlt-option-prettify"><?php _hlt_e( 'Display Code Snippets' ); ?><br/><span class="label"><?php _hlt_e( 'Default: Off' ); ?></span></label>
								<div class="controls">
									<div class="option_section <?php if ( $hlt_option_prettify == 'Y' ): ?>selected_item<?php endif; ?>" id="section-hlt-option-prettify">
										<label class="checkbox" for="hlt-option-prettify">
											<input type="checkbox" name="hlt_bootstrap_option_prettify" value="Y" id="hlt-option-prettify" <?php if ( $hlt_option_prettify == 'Y' ): ?>checked="checked"<?php endif; ?> />
											<?php _hlt_e( 'Include Google Prettify/Pretty Links Javascript' ); ?>
										</label>
										<p class="help-block">
											<?php _hlt_e( 'If you display code snippets or similar on your site, enabling this option will include the
											Google Prettify Javascript library for use with these code blocks.' ); ?>
										</p>
									</div>
								</div>
							</div>
	
							<div class="form-actions">
								<button type="submit" class="btn btn-primary" name="submit"><?php _hlt_e( 'Save all changes' ); ?></button>
								<?php echo ( class_exists( 'W3_Plugin_TotalCacheAdmin' )? '<span> and flush W3 Total Cache</span>' : '' ); ?>
							</div>
						</fieldset>
					</div><!-- / span<?php echo $iWidthColumnOne; ?> -->
				</div><!-- / row -->
		
			</form>
	
		</div><!-- / span9 -->
	
		<div class="span3" id="side_widgets">
	  		<?php echo getWidgetIframeHtml('side-widgets'); ?>
		</div>
	</div><!-- / row -->
	
	</div><!-- / bootstrap-wpadmin -->

	<?php include_once( dirname(__FILE__).'/bootstrapcss_js.php' ); ?>
</div>