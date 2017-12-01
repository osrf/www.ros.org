<div class="wrap">
	<?php
	if( $current_plugin && isset( $this->responses[ $current_plugin ] ) && isset( $this->responses[ $current_plugin ]['register_code'] ) ){
		foreach( $this->responses[ $current_plugin ]['register_code'] as $message_type => $messages ){
		
			foreach( $messages as $message ){?>
				
				<?php switch( $message_type ){
					
					case 'success_message':?>
							<div class="updated"><p><?php echo $message ?></p></div>
						<?php break;
					case 'error_message':?>
							<div class="error"><p><?php echo $message ?></p></div>
						<?php break;
					default:?>
							<div class="otw_fc_lm_<?php echo $message_type ?> updated"><?php echo $message ?></div>
						<?php break;
				}
			}
		}
	}
	?>
	<div class="metabox-holder otw-factory-metabox-holder">
		<div class="postbox-container">
	<?php if( $current_plugin ){?>
		
		<?php foreach( $license_messages as $message ){?>
		<div class="meta-box-sortables">
			<div class="postbox">
				<h3 class="hndle"><span><?php echo $message['title'];?></span></h3>
				<div class="inside">
					<div class="main"><?php echo $message['text'];?></div>
				</div>
			</div>
		</div>
		<?php }?>
		<div class="meta-box-sortables">
			<div class="postbox">
				<h3 class="hndle"><span><?php echo $this->get_label( 'License Details' )?></span></h3>
				<div class="inside">
					<div class="main">
						<ul class="otw_factory-items">
							<li>
								<p class="otw-factory-litem_title"><?php echo $this->get_label( 'Domain' )?></p>
								<p class="otw-factory-litem_value"><?php echo $this->plugins[ $current_plugin ]['domain']?></p>
							</li>
							<li>
								<p class="otw-factory-litem_title"><?php echo $this->get_label( 'Version' )?></p>
								<?php if( isset( $this->plugins[ $current_plugin ]['info']['name'] ) ){?>
								<p class="otw-factory-litem_value"><?php echo $this->plugins[ $current_plugin ]['info']['name']?>(<?php echo $this->plugins[ $current_plugin ]['version']?>)</p>
								<?php }else{?>
								<p class="otw-factory-litem_value"><?php echo $this->get_label( 'No information available' )?></p>
								<?php }?>
							</li>
							<?php if( isset( $this->plugins[ $current_plugin ]['info']['keys'] ) ){?>
								
								<?php foreach( $this->plugins[ $current_plugin ]['info']['keys'] as $key_data ){?>
									<?php if( ( $key_data['in_use'] ) && isset( $key_data['expire_date'] ) && $key_data['expire_date'] ){?>
										<li>
											<p class="otw-factory-litem_title"><?php echo $this->get_label( 'Expires' )?></p>
											<p class="otw-factory-litem_value"><?php echo date( 'd M Y', strtotime( $key_data['expire_date'] ) )?></p>
										</li>
									<?php }?>
								<?php }?>
							<?php }?>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="meta-box-sortables">
			<div class="postbox">
				<h3 class="hndle"><span><?php echo $this->get_label( 'Product Code' )?></span></h3>
				<div class="inside">
					<div class="main">
					<?php
						if( $current_plugin && isset( $this->plugins[ $current_plugin ] ) && isset( $this->plugins[ $current_plugin ]['info']['keys'] ) ){?>
						<form method="post" action="">
							<input type="hidden" name="otw_fc_action" value="remove_pc_code" />
							<table width="100%" class="otw-factory-keys">
								<?php foreach( $this->plugins[ $current_plugin ]['info']['keys'] as $key_data ){?>
									<tr>
										<td><?php echo $key_data['external_code']?></td>
										<td width="60%"><?php echo $key_data['status_string']?></td>
										<td width="10%">
											<input type="submit" class="button" onclick="return confirm('<?php echo $this->get_label( 'Please confirm to deregister the code?' )?>');" name="remove_pc_code_<?php echo $key_data['id']?>" value="<?php echo $this->get_label( 'Delete Code' ) ?>" />
										</td>
									</tr>
								<?php }?>
							</table>
						</form>
						<?php } ?>
						
						<form method="post" action="">
						<input type="hidden" name="otw_fc_action" value="add_pc_code" />
						<p><?php echo $this->get_label( 'Have a code, paste it here' )?></p>
						<p><input type="text" name="otw_pc_code" value="" id="otw_pc_code" /><input type="submit" name=otw_submit_pc_code" class="otw_factory_button_action button button-primary" value="<?php echo $this->get_label('Submit Code')?>"</p>
						</form>
					</div>
				</div>
			</div>
		</div>
	<?php }else{?>
		<div class="meta-box-sortables">
			<div class="postbox">
				<h3 class="hndle"><span><?php echo $this->get_label( 'License Details' )?></span></h3>
				<div class="inside">
					<div class="main"><?php echo $this->get_label( 'No plugin information found.' )?></div>
				</div>
			</div>
		</div>
	<?php }?>
		</div>
	</div>
</div>