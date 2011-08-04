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
require_once('inc/plugins/PluginHandler.php');
require_once('inc/lib/vlib/vlibTemplate.php');
require_once('inc/functions.core.tmpl.php');

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
  $ph = new PluginHandler();
  $pluginNames = $ph->getAvailablePlugins(PluginHandler::PLUGINTYPE_INFO);
  foreach( $pluginNames as $plugin ) {
    $pi = $ph->getPlugin($plugin[0]);
    $pi->show();
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

  if ( $_SESSION['uid'] == 1 ) // if administrator
    print("<a href=configure.php>Configure</a>");
}



$cfg = Configuration::get_instance()->get_cfg();
if (isset($_REQUEST['ajax_update'])) {
        $isAjaxUpdate = true;
        $ajaxUpdateParams = tfb_getRequestVar('ajax_update');
        // init template-instance
        tmplInitializeInstance($cfg["theme"], "inc.transferList.tmpl");
} else {
        $isAjaxUpdate = false;
        // init template-instance
        tmplInitializeInstance($cfg["theme"], "page.index.tmpl");
}

// language
/*
$tmpl->setvar('_STATUS', $cfg['_STATUS']);
$tmpl->setvar('_ESTIMATEDTIME', $cfg['_ESTIMATEDTIME']);
$tmpl->setvar('_RUNTRANSFER', $cfg['_RUNTRANSFER']);
$tmpl->setvar('_STOPTRANSFER', $cfg['_STOPTRANSFER']);
$tmpl->setvar('_DELQUEUE', $cfg['_DELQUEUE']);
$tmpl->setvar('_SEEDTRANSFER', $cfg['_SEEDTRANSFER']);
$tmpl->setvar('_DELETE', $cfg['_DELETE']);
$tmpl->setvar('_WARNING', $cfg['_WARNING']);
$tmpl->setvar('_NOTOWNER', $cfg['_NOTOWNER']);
$tmpl->setvar('_STOPPING', $cfg['_STOPPING']);
$tmpl->setvar('_TRANSFERFILE', $cfg['_TRANSFERFILE']);
$tmpl->setvar('_ADMIN', $cfg['_ADMIN']);
$tmpl->setvar('_USER', $cfg['_USER']);
$tmpl->setvar('_USERS', $cfg['_USERS']);

// username
$tmpl->setvar('user', $cfg["user"]);
*/

// language
$tmpl->setvar('_STATUS', "Status");
$tmpl->setvar('_ESTIMATEDTIME', "Est.");
$tmpl->setvar('_RUNTRANSFER', "Run");
$tmpl->setvar('_STOPTRANSFER', "Stop");
$tmpl->setvar('_DELQUEUE', "Delete from queue");
$tmpl->setvar('_SEEDTRANSFER', "Seed");
$tmpl->setvar('_DELETE', "Delete");
$tmpl->setvar('_WARNING', "Warning");
$tmpl->setvar('_NOTOWNER', "Not owner");
$tmpl->setvar('_STOPPING', "Stopping");
$tmpl->setvar('_TRANSFERFILE', "Transferfile");
$tmpl->setvar('_ADMIN', "Administrator");
$tmpl->setvar('_USER', "User");
$tmpl->setvar('_USERS', "Users");

// username
$tmpl->setvar('user', $cfg["user"]);

print('
<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/popup.js"></script>
	<script type="text/javascript">
pp = new Popup;

	// TODO (re)move this to an appropriate place. It is here solely to get the transfer listing working
	function actionClick(showlabel,labeltext) {
        if (actionInProgress) {
                actionRequestError();
                return false;
        }   
        actionRequest(showlabel,labeltext);
        return true;
	}
	</script>
	<script type="text/javascript" src="js/transferlist.js"></script>
');

$arUserTorrent = array();
$arListTorrent = array(); // TODO where is this array needed for?
if ($cfg["transmission_rpc_enable"]) {
	require_once('inc/clients/transmission-daemon/TransmissionDaemonClient.php');
	$td = new TransmissionDaemonClient();
	$transfers = $td->getTransferList($_SESSION['uid']);
	
	foreach($transfers as $transfer)
		array_push($arUserTorrent, $transfer->getTransferListItem());
}

$tmpl->setloop('arUserTorrent', $arUserTorrent);
$tmpl->setloop('arListTorrent', $arListTorrent);

if (sizeof($arUserTorrent) > 0) 
        $tmpl->setvar('are_user_transfer', 1);
$boolCond = true;
if ($cfg['enable_restrictivetview'] == 1)
        $boolCond = $cfg['isAdmin']; // TODO Err... what does this do? :)
$tmpl->setvar('are_transfer', (($boolCond) && (sizeof($arListTorrent) > 0)) ? 1 : 0);


$tmpl->pparse();

//printHtml();

?>
