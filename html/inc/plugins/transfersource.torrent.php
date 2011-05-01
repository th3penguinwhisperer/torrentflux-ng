<?php

function getPluginUi() {
	//getClientNames() // Put the following in this function
	$clientNames = array("Transmission-daemon");
	$pluginName = "transmission-daemon";
	
	$clienthtmlcode = "";
	foreach ( $clientNames as $clientName ) {
		$clienthtmlcode .= "\t<option value=$clientName>".$clientName."</option>\n";
	}
	
	$actions = array("Add");
	array_push($actions, "Add+Start");
	$actionsnames = array("add");
	array_push($actionsnames, "addstart");
	$actionhtmlcode = "";
	foreach ( $actions as $action ) {
		$actionhtmlcode .= "\t<option value=" . array_shift($actionsnames) . ">" . $action . "</option>\n";
	}
	
	print("\n<form name=addurl method=post action=dispatcher.php>Torrent URL: 
	<input type=text name=url>
		<select name=client>
$clienthtmlcode;
		</select>
		<select name=subaction>
$actionhtmlcode;
		</select>
	<input type=submit>
	<input type=hidden name=plugin value=$pluginName>
	<input type=hidden name=action value=add>
</form>
<form name=uploadmetafile method=post action=dispatcher.php enctype=\"multipart/form-data\">Metafile upload
	<input type=file name=metafile>
	<input type=submit>
	<input type=hidden name=plugin value=$pluginName>
	<input type=hidden name=action value=metafileupload>
</form>");
}

?>
