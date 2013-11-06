<?php

/**
 * Copyright (c) 2013 iControlWP <support@icontrolwp.com>
 * All rights reserved.
 *
 * "WordPress Twitter Bootstrap CSS" (formerly "WordPress Bootstrap CSS") is
 * distributed under the GNU General Public License, Version 2,
 * June 1991. Copyright (C) 1989, 1991 Free Software Foundation, Inc., 51 Franklin
 * St, Fifth Floor, Boston, MA 02110, USA
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

include_once( dirname(__FILE__).ICWP_DS.'worpit_options_helper.php' );
include_once( dirname(__FILE__).ICWP_DS.'widgets'.ICWP_DS.'bootstrapcss_widgets.php' );

?>
<div class="wrap">
	<style type="text/css">
		.bootstrap-wpadmin .row.row_number_1 {
			padding-top: 18px;
		}
		.bootstrap-wpadmin .span4 {
			width: 325px;
		}
		.bootstrap-wpadmin .control-group {
			border: 1px dashed #DDDDDD;
			border-radius: 8px;
			padding: 10px;
		}
		.bootstrap-wpadmin .control-group:hover {
			background-color: #f8f8f8;
		}
		.bootstrap-wpadmin .control-group .control-label {
			font-weight: bold;
			font-size: 12px;
			width: 110px;
		}
		.bootstrap-wpadmin .control-group span.label-less-name {
			font-weight: normal;
			font-size: 11px;
			display: block;
			margin-bottom: 2px;
			clear: both;
			float: left;
		}
		.bootstrap-wpadmin .control-group .controls {
			margin-left: 120px;
		}
		.bootstrap-wpadmin .control-group .option_section {
			border: 1px solid transparent;
		}
		.help_section  {
			padding-top: 8px;
			border-top: 1px solid #DDDDDD;
		}
		.toggle_checkbox {
			float:right;
		}
		.bootstrap-wpadmin .form-horizontal .form-actions {
			padding-left: 75px;
			padding-right: 75px;
		}
	</style>
	<script type="text/javascript">
		function triggerColor( inoEl ) {
			var $ = jQuery;
			
			var $oThis = $( inoEl );
			var aParts = $oThis.attr( 'id' ).split( '_' );
			
			var $oColorInput = $( '#<?php echo $worpit_var_prefix; ?>less_'+ aParts[3] );
			
			if ( $oThis.is( ':checked' ) ) {
				$oColorInput.miniColors( 'destroy' );
				$oColorInput.css( 'width', '130px' );
			}
			else {
				$oColorInput.miniColors();
				$oColorInput.css( 'width', '100px' );
			}
		}
	
		jQuery( document ).ready(
			function() {
				var $ = jQuery;

				$( 'input[name^=hlt_toggle_less]' ).on( 'click',
					function() {
						triggerColor( this );
					}
				);

				$( 'input[name^=hlt_toggle_less]' ).each(
					function( index, el ) {
						triggerColor( this );
					}
				);

			}
		);
	</script>
	
	<div class="bootstrap-wpadmin">
		<div class="page-header">
			<a href="http://www.icontrolwp.com/"><div class="icon32" id="icontrolwp-icon">&nbsp;</div></a>
			<h2><?php _hlt_e( 'LESS Compiler :: Twitter Bootstrap Plugin (from iControlWP)' ); ?></h2>
		</div>

		<div class="row">
			<div class="span12">
				<?php
				if ( !$worpit_compiler_enabled ) {
					?><div class="alert alert-error">You need to <a href="admin.php?page=<?php echo $worpit_page_link_options; ?>">enable the LESS compiler option</a> before using this section.</div><?php
				}
				else {
					?><div class="alert alert-info">Customize the twitter bootstrap options below to tweak the appearance of your website.</div><?php
				}
				?>
			</div>
		</div>
		<div class="row">
			<div class="<?php echo $worpit_fShowAds? 'span9' : 'span12'; ?> <?php echo ( $worpit_compiler_enabled? 'enabled_section': 'disabled_section' ); ?>">
				<form action="<?php echo ( $worpit_compiler_enabled? $worpit_form_action: '' ) ; ?>" method="post" class="form-horizontal">
				<?php
					wp_nonce_field( $worpit_nonce_field );
					printAllPluginOptionsForm( $worpit_aAllOptions, $worpit_var_prefix, 2 );
				?>
				<div class="form-actions">
					<input type="hidden" name="icwp_plugin_form_submit" value="Y" />
					<button type="submit" class="btn btn-primary" name="submit" <?php echo ($worpit_compiler_enabled ? '':' disabled'); ?>><?php _hlt_e( 'Compile CSS'); ?></button>
					<button type="submit" class="btn btn-danger" name="submit_reset" <?php echo ($worpit_compiler_enabled ? '':' disabled'); ?>><?php _hlt_e( 'Reset Defaults' ); ?></button>
					<button type="submit" class="btn btn-warning" name="submit_preserve" <?php echo ($worpit_compiler_enabled ? '':' disabled'); ?>><?php _hlt_e( 'Compile CSS (preserve customizations)'); ?></button>
					<a class="btn btn-inverse" name="download_less_css" <?php echo ( file_exists( $worpit_less_file_location[0] ) ? 'href="'.$worpit_less_file_location[1].'"' :' disabled'); ?>><?php _hlt_e( 'Download' ); ?></a>
					<p style="margin-top: 20px;"><strong>Note: </strong>If in doubt or having compile issues, use the 'Compile CSS' or 'Reset' buttons. If you've made any customizations to 'Variable.less', compile with preserve customizations.</p>
				</div>
				</form>
			</div><!-- / span9 -->
		
			<?php if ( $worpit_fShowAds ) : ?>
			<div class="span3" id="side_widgets">
		  		<?php echo getWidgetIframeHtml( 'side-widgets-wtb' ); ?>
			</div>
			<?php endif; ?>
		</div>
	</div><!-- / bootstrap-wpadmin -->
</div><!-- / wrap -->
			