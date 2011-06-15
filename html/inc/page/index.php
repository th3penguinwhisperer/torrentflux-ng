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


require_once("inc/generalfunctions.php");
require_once('inc/singleton/Configuration.php');



function printHtml() {
  $cfg = Configuration::get_instance()->get_cfg();
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
<body onload="javascript:gettransferlist(\'transferlist\'); reloadtransferlist(\'transferlist\'); gettransfersources(\'transfersources\');">
');

  //  Show plugins of type 'info' here
  require_once('inc/plugins/PluginHandler.php');
  $ph = new PluginHandler();
  $pluginNames = $ph->getAvailablePlugins(PluginHandler::PLUGINTYPE_INFO);
  foreach( $pluginNames as $plugin ) {
    $ph->getPlugin($plugin[0]);
  }

  print('
<div id="status_message"></div>
<img src=images/add.png onclick="javascript:pp.url(\'index.php?page=transfersources\'); pp.reposition();">
');

  print('
<img src=images/refresh.png onclick="javascript:gettransferlist(\'transferlist\');">
<div id=transferlist></div>
</body>
</html>');
}

printHtml();

?>
