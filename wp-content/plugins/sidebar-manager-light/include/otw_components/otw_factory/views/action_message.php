<div class="wrap">
	<?php if( isset( $response['title'] ) && strlen( $response['title'] ) ){ ?>
	<h2><?php echo $response['title']?></h2>
	<?php }else{?>
	<script type="text/javascript">
		location.href='<?php echo $return_url;?>';
	</script>
	<?php }?>
	<?php if( isset( $response['success_message'] ) && count( $response['success_message'] ) ){?>
		<div class="updated"><p><?php echo implode( '<br />', $response['success_message'] )?></p></div>
	<?php }?>
	<?php if( isset( $response['error_message'] ) && count( $response['error_message'] ) ){?>
		<div class="updated"><p><?php echo implode( '<br />', $response['error_message'] )?></p></div>
	<?php }?>
	<?php if( isset( $response['info_message'] ) && count( $response['info_message'] ) ){?>
		<?php foreach( $response['info_message'] as $message ){?>
			<?php
			echo $formatted_message = '<div class="updated otw-factory otw-factory-'.$message['type'].'"><div class="otw-factory-message-content">'.$this->replace_variables( $message['text'], $message['vars'], $plugin_id ).'</div></div>';
			?>
		<?php }?>
	<?php }?>
</div>
