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
require_once('inc/classes/singleton/Configuration.php');
require_once('inc/plugins/PluginHandler.php');
require_once('inc/lib/vlib/vlibTemplate.php');
require_once('inc/functions.core.tmpl.php');

$cfg = Configuration::get_instance()->get_cfg();
if (isset($_REQUEST['ajax_update'])) {
        $isAjaxUpdate = true;
        // init template-instance
        tmplInitializeInstance($cfg["theme"], "inc.transferList.tmpl");
} else {
        $isAjaxUpdate = false;

	$ph = new PluginHandler();
	$pluginNames = $ph->getAvailablePlugins(PluginHandler::PLUGINTYPE_INFO);
	
	$pi_container_content = "";
	foreach( $pluginNames as $plugin ) {
		$pi_container_content .= file_get_contents('inc/plugins/'. $plugin[0] .'/'. $plugin[0] . '.tmpl');
	}

        // init template-instance
        tmplInitializeInstance($cfg["theme"], "page.index.tmpl");
	tmplSetTitleBar($cfg["pagetitle"].' - '.$cfg['_DIRECTORYLIST']);
	$onLoad = "refreshajaxdata();"; // Makes the onLoad function immediately call the reloadtransferlist javascript method which loads all ajax data
	$tmpl->setvar('onLoad', $onLoad);
	$tmpl->setvar('isAdmin', $_SESSION['isAdmin']);
	$tmpl->setvar('plugin_container_content', $pi_container_content);
        printJavascriptHtml(); // TODO should be fixed in another way

	$start = startTimer();
	$tmpl->pparse();
	getTimeTaken($start);

	exit();
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

// TODO: create classes for the languages so this doesn't have to be filled in on each page
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
$tmpl->setvar('_ADMINISTRATION', "Administration");

// username
$tmpl->setvar('user', $cfg["user"]);

$tmpl->setvar('settings_0', '0');
$tmpl->setvar('settings_1', '1');
$tmpl->setvar('settings_2', '0');
$tmpl->setvar('settings_3', '0');
$tmpl->setvar('settings_4', '1');
$tmpl->setvar('settings_5', '1');
$tmpl->setvar('settings_6', '1');
$tmpl->setvar('settings_7', '1');
$tmpl->setvar('settings_8', '0');
$tmpl->setvar('settings_9', '0');
$tmpl->setvar('settings_10', '1');
$tmpl->setvar('settings_11', '0');


// Enabling AJAX update -> this generates the function call
// ajax_initialize(10000,';',0,1,'torrentflux-b4rt',1,'1:1:1:1:1:1',1,0,1,1,0,1,1,'tf',1,'tf');
$_SESSION['settings']['index_ajax_update'] = 10;
$cfg['index_ajax_update'] = 10;
$cfg['enable_index_ajax_update_silent'] = 0;
$cfg['enable_index_ajax_update_title'] = 1;
$cfg['pagetitle'] = 'pagetitle';
$cfg['index_page_stats'] = 0;
$cfg['enable_goodlookstats'] = 0;
$cfg['enable_xfer'] = 0;
$cfg['xfer_realtime'] = 0;
$cfg['ui_displayusers'] = 0;
$cfg['stats_txt_delim'] = ';';
$cfg['enable_index_ajax_update_users'] = 0;
$cfg["hide_offline"] = 0;
$cfg["enable_index_ajax_update_list"] = 1;
$cfg['enable_sorttable'] = 0;
$cfg['drivespacebar'] = 'tf';
$cfg['ui_displaybandwidthbars'] = 0;
$cfg['bandwidthbar'] = 'tf';
if ($_SESSION['settings']['index_ajax_update'] != 0) {
        $tmpl->setvar('index_ajax_update', $cfg["index_ajax_update"]);
        $ajaxInit = "ajax_initialize(";
        $ajaxInit .= (intval($cfg['index_ajax_update']) * 1000);
        $ajaxInit .= ",'".$cfg['stats_txt_delim']."'";
        $ajaxInit .= ",".$cfg["enable_index_ajax_update_silent"];
        $ajaxInit .= ",".$cfg["enable_index_ajax_update_title"];
        $ajaxInit .= ",'".$cfg['pagetitle']."'";
        $ajaxInit .= ",".$cfg["enable_goodlookstats"];
        if ($cfg["enable_goodlookstats"] != "0") 
                $ajaxInit .= ",'".$settingsHackStats[0].':'.$settingsHackStats[1].':'.$settingsHackStats[2].':'.$settingsHackStats[3].':'.$settingsHackStats[4].':'.$settingsHackStats[5]."'";
        else 
                $ajaxInit .= ",'0:0:0:0:0:0'";
        $ajaxInit .= ",".$cfg["index_page_stats"];
        //if (FluxdQmgr::isRunning())
        //        $ajaxInit .= ",1";
        //else 
                $ajaxInit .= ",0";
        if (($cfg['enable_xfer'] == 1) && ($cfg['xfer_realtime'] == 1))
                $ajaxInit .= ",1";
        else 
                $ajaxInit .= ",0";
        if (($cfg['ui_displayusers'] == 1) && ($cfg['enable_index_ajax_update_users'] == 1))
                $ajaxInit .= ",1";
        else 
                $ajaxInit .= ",0";
        $ajaxInit .= ",".$cfg["hide_offline"];
        $ajaxInit .= ",".$cfg["enable_index_ajax_update_list"];
        $ajaxInit .= ",".$cfg["enable_sorttable"];
        $ajaxInit .= ",'".$cfg['drivespacebar']."'";
        $ajaxInit .= ",".$cfg["ui_displaybandwidthbars"];
        $ajaxInit .= ",'".$cfg['bandwidthbar']."'";
        $ajaxInit .= ");onbeforeunload = ajax_unload;";

        $onLoad = $ajaxInit;
} 

// onLoad
$cfg['_SECONDS'] = 'Seconds';
$cfg['_TURNOFFREFRESH'] = 'Turn off refresh';
if ($onLoad != "") {
        $tmpl->setvar('onLoad', $onLoad);
        $tmpl->setvar('_SECONDS', $cfg['_SECONDS']);
        $tmpl->setvar('_TURNOFFREFRESH', $cfg['_TURNOFFREFRESH']);
}


function printJavascriptHtml()
{
	print('
		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="js/popup.js"></script>
		<script type="text/javascript">
	
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
		<script type="text/javascript" src="js/ajax.js"></script>
		<script type="text/javascript" src="js/ajax_index.js"></script>
		<script type="text/javascript" src="js/diskspace.js"></script>
			
		<link rel="stylesheet" href="css/popup.css" type="text/css" media="screen" />
	');
}

$totaluprate = 0;
$totaldownrate = 0;
$arUserTorrent = array();
$arListTorrent = array(); // TODO where is this array needed for?

if ($cfg["rewrite_transmission_rpc_enable"]) {
	require_once('inc/clients/transmission-daemon/TransmissionDaemonClient.php');
	$td = new TransmissionDaemonClient();
	$transfers = $td->getTransferList($_SESSION['uid']);
	
	foreach($transfers as $transfer) {
		array_push($arUserTorrent, $transfer->getTransferListItem());
		$torrentdata = $transfer->getData();
		$totaldownrate += $torrentdata['rateDownload'];
		$totaluprate += $torrentdata['rateUpload'];
	}
}

$tmpl->setloop('arUserTorrent', $arUserTorrent);
$tmpl->setloop('arListTorrent', $arListTorrent);

if (sizeof($arUserTorrent) > 0) 
        $tmpl->setvar('are_user_transfer', 1);
$boolCond = true;
$cfg['enable_restrictivetview'] = 0; // TODO should be removed/modified later
if ($cfg['enable_restrictivetview'] == 1)
        $boolCond = $cfg['isAdmin']; // TODO Err... what does this do? :)
$tmpl->setvar('are_transfer', (($boolCond) && (sizeof($arListTorrent) > 0)) ? 1 : 0);

if (!$isAjaxUpdate) {
	require_once('inc/plugins/PluginHandler.php');
	$ph = new PluginHandler();
	$pluginNames = $ph->getAvailablePlugins(PluginHandler::PLUGINTYPE_INFO);
	$pluginshtml = "";

	foreach( $pluginNames as $plugin ) {
		$pi = $ph->getPlugin($plugin[0]);
		$pluginshtml .= $pi->get();
	}
	
	$tmpl->setvar('generalplugins', $pluginshtml);
}

// =============================================================================
// ajax-index
// =============================================================================

if ($isAjaxUpdate) {
	$ajax_delim = "|#|";
	$content = "";
	$isFirst = true;
	
	$content .= "ajaxParseTransferlist";
	$content .= $ajax_delim;
	$content .= $tmpl->grab();

	$content .= $ajax_delim;
	$content .= "ajaxParseRates";
	$content .= $ajax_delim;
	$content .= "$totaldownrate;$totaluprate";

	require_once('inc/plugins/PluginHandler.php');
	$ph = new PluginHandler();
	$pluginNames = $ph->getAvailablePlugins(PluginHandler::PLUGINTYPE_INFO);

	foreach( $pluginNames as $plugin ) {
		$pi = $ph->getPlugin($plugin[0]);
		$content .= $ajax_delim;
		$content .= "ajaxParse" . ucfirst($plugin[0]);
		$content .= $ajax_delim;
		$content .= $pi->getAjaxData();
	}


	//$ajaxUpdateParams{3} = 1; // TODO this should be deleted later: just for testing
	// server stats
	/*
	$ajaxUpdateParams{0} = "0";
	if ($ajaxUpdateParams{0} == "1") {
		$isFirst = false;
		$serverStats = getServerStats();
		$serverCount = count($serverStats);
		for ($i = 0; $i < $serverCount; $i++) {
			$content .= $serverStats[$i];
			if ($i < ($serverCount - 1))
				$content .= $cfg['stats_txt_delim'];
		}
	}
	// xfer
	if ($ajaxUpdateParams{1} == "1") {
		if ($isFirst)
			$isFirst = false;
		else
			$content .= $ajax_delim;
		$xferStats = Xfer::getStatsFormatted();
		$xferCount = count($xferStats);
		for ($i = 0; $i < $xferCount; $i++) {
			$content .= $xferStats[$i];
			if ($i < ($xferCount - 1))
				$content .= $cfg['stats_txt_delim'];
		}
	}
	// users
	if ($ajaxUpdateParams{2} == "1") {
		if ($isFirst)
			$isFirst = false;
		else
			$content .= $ajax_delim;
		$countUsers = count($cfg['users']);
		$arOnlineUsers = array();
		$arOfflineUsers = array();
		for ($i = 0; $i < $countUsers; $i++) {
			if (IsOnline($cfg['users'][$i]))
				array_push($arOnlineUsers, $cfg['users'][$i]);
			else
				array_push($arOfflineUsers, $cfg['users'][$i]);
		}
		$countOnline = count($arOnlineUsers);
		for ($i = 0; $i < $countOnline; $i++) {
			$content .= $arOnlineUsers[$i];
			if ($i < ($countOnline - 1))
				$content .= $cfg['stats_txt_delim'];
		}
		if ($cfg["hide_offline"] == 0) {
			$content .= "+";
			$countOffline = count($arOfflineUsers);
			for ($i = 0; $i < $countOffline; $i++) {
				$content .= $arOfflineUsers[$i];
				if ($i < ($countOffline - 1))
					$content .= $cfg['stats_txt_delim'];
			}
		}
	}
	*/

	// transfer list
/*
	if ($ajaxUpdateParams{3} == "1") {
		if ($isFirst)
			$isFirst = false;
		else
			$content .= $ajax_delim;
		$content .= $tmpl->grab();
	}
*/
	// javascript
	if (true) {
		if ($isFirst)
			$isFirst = false;
		else
			$content .= $ajax_delim;
			//$content .= $ajax_delim . "test" . $ajax_delim . "test2" . $ajax_delim . "test3" . $ajax_delim . "test4";

		//Messages jGrowl
		$jGrowls = "";
		if (!empty($cfg['growl'])) {
			$jGrowls .= getGrowlMessages();
			clearGrowlMessages();
		}
		//Growl message on ajax refresh
		if (!empty($msgGrowl)) {
			$jGrowls .= "jQuery.jGrowl('".addslashes($msgGrowl)."',{sticky:".($msgSticky ?'true':'false')."});";
		}
		$content .= $jGrowls;

	}
	// send and out
	@header("Cache-Control: no-cache");
	@header("Pragma: no-cache");
	@header("Content-Type: text/plain");
	echo $content;
	exit();
}


$start = startTimer();
$tmpl->pparse();
getTimeTaken($start);

?>
