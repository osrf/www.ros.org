<?php 
function getWidgetIframeHtml( $insSnippet ) {
	
	$sSubPageNow = isset( $_GET['page'] )? 'page='.$_GET['page'].'&': '';
	
	$sWidth = '100%';
	$sBackgroundColor = "#ffffff";
	$sIframeName = 'iframe-hlt-bootstrapcss-'.$insSnippet;
	
	if ( strpos( $insSnippet, 'side-widgets') !== false ) {
		$sHeight = '1200px';
	}
	elseif ( strpos( $insSnippet, 'dashboard-widget-developerchannel') !== false ) {
		$sHeight = '312px';
	}
	elseif ( strpos( $insSnippet, 'dashboard-widget-worpit') !== false ) {
		$sHeight = '230px';
			$sBackgroundColor = 'whiteSmoke';
	}
	
	return '<iframe name="'.$sIframeName.'"
		src="http://www.hostliketoast.com/custom/remote/plugins/hlt-bootstrapcss-plugin-widgets.php?'.$sSubPageNow.'snippet='.$insSnippet.'"
		width="'.$sWidth.'" height="'.$sHeight.'" frameborder="0" scrolling="no" style="background-color:'.$sBackgroundColor.';" ></iframe>
	';
	
}//getWidgetIframeHtml
?>
