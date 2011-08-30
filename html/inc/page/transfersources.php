<?php


require_once('inc/plugins/PluginHandler.php');

	print('<div id=transfersources><a id="popup_close" onclick="disablePopup()">x</a>');

	$ph = new PluginHandler();
	$pluginNames = $ph->getAvailablePlugins(PluginHandler::PLUGINTYPE_TRANSFERSOURCE);
	
	foreach( $pluginNames as $plugin ) {
		print("<a href='#' onclick='gettransfersources(\"transfersources\",\"&source=" . $plugin[0] . "\");'>" . $plugin[1] . "</a> ");
	}
	print('<br>');
	
	if (!isset($_REQUEST['source']))
		$plugintoload = $pluginNames[0][0]; // First plugin if none specified
	else
		$plugintoload = $_REQUEST['source'];
	
	$pi = $ph->getPlugin($plugintoload);
	$pi->show();
	print('<script type="text/javascript">centerPopup();</script>');

	print('</div>');
?>
