<?php

/* $Id$ */

/*******************************************************************************

 LICENSE

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License (GPL)
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.

 To read the license please visit http://www.gnu.org/copyleft/gpl.html

*******************************************************************************/


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

require_once('inc/plugins/PluginHandler.php');
require_once("inc/generalfunctions.php");
require_once('inc/singleton/Configuration.php');

$cfg = Configuration::get_instance()->get_cfg();

function printHtml() {
  global $rowArray;

//	<script type="text/javascript" src="js/jquery.tablesorter.js"></script>
// TODO: path is now relative... this might have to be changed to an absolute path
  print( '<html>
<head>
	<script type="text/javascript" src="js/jquery.js"></script> 
	<script type="text/javascript" src="js/popup.js"></script>
	<script type="text/javascript">
pp = new Popup;
	</script>
	<script type="text/javascript" src="js/transferlist.js"></script>
</head>
<body onload="javascript:gettransferlist(); reloadtransferlist();">');

  getClientSelection();
  getActionSelection();

  print('<img onclick="javascript:gettransfersource();"> <img onclick="javascript:gettransfersource();">
<div id=transfersources>');

  $ph = new PluginHandler();
  $pluginNames = $ph->getAvailablePlugins(PluginHandler::PLUGINTYPE_TRANSFERSOURCE);
  foreach( $pluginNames as $plugin ) {
  	$ph->getPlugin($plugin[0]);
  }
  
  print('</div>
  
<img onclick="javascript:gettransferlist();">
<div id=transferlist>
</div>');

  print( "
</body>
</html>" );
}

printHtml();

?>
