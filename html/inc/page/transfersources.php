<?php


require_once('inc/plugins/PluginHandler.php');

	$ph = new PluginHandler();
	$pluginNames = $ph->getAvailablePlugins(PluginHandler::PLUGINTYPE_TRANSFERSOURCE);
	
	foreach( $pluginNames as $plugin ) {
		print("<img onclick='gettransfersources(\"transfersources\",\"&source=" . $plugin[0] . "\")'>" . $plugin[1] . " ");
	}
	
	if (!isset($_REQUEST['source']))
		$plugintoload = $pluginNames[0][0]; // First plugin if none specified
	else
		$plugintoload = $_REQUEST['source'];
	
	$ph->getPlugin($plugintoload);

?>