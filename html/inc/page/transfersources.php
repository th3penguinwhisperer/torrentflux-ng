<?php


require_once('inc/plugins/PluginHandler.php');

function getClientSelection()
{
	$clients = array("transmission-daemon");
	$clientNames = array("Transmission-daemon");
	$clienthtmlcode = "";
	foreach ( $clientNames as $clientName ) {
		$clienthtmlcode .= "\t\t<option value=" . array_shift($clients) . ">".$clientName."</option>\n";
	}

	print("
	<select name=client id=client>
$clienthtmlcode	</select>");	
}

function getActionSelection()
{
	$actions = array("Add");
	array_push($actions, "Add+Start");
	$actionsnames = array("add");
	array_push($actionsnames, "addstart");
	$actionhtmlcode = "";
	foreach ( $actions as $action ) {
		$actionhtmlcode .= "\t<option value=" . array_shift($actionsnames) . ">" . $action . "</option>\n";
	}
	
	print("		<select name=subaction id=subaction>
$actionhtmlcode
		</select>");
}

	print('<div id=transfersources>');
	getClientSelection();
	getActionSelection();
	print('<br>');

	$ph = new PluginHandler();
	$pluginNames = $ph->getAvailablePlugins(PluginHandler::PLUGINTYPE_TRANSFERSOURCE);
	
	foreach( $pluginNames as $plugin ) {
		print("<img onclick='gettransfersources(\"transfersources\",\"&source=" . $plugin[0] . "\");'>" . $plugin[1] . " ");
	}
	print('<br>');
	
	if (!isset($_REQUEST['source']))
		$plugintoload = $pluginNames[0][0]; // First plugin if none specified
	else
		$plugintoload = $_REQUEST['source'];
	
	$ph->getPlugin($plugintoload);

	print('</div>');
?>