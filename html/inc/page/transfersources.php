<?php


require_once('inc/plugins/PluginHandler.php');

	print('<div id=transfersources>');

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
	
	$ph->getPlugin($plugintoload);

	print('</div>');
?>