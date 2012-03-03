<?php

@session_start();

require_once('inc/classes/singleton/Configuration.php');
require_once('inc/generalfunctions.php');

$action = $_REQUEST['action'];
$plugin = (isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : "");
$client = (isset($_REQUEST['client']) ? $_REQUEST['client'] : "");
$transfer = (isset($_REQUEST['transfer']) ? $_REQUEST['transfer'] : "");
$url = (isset($_REQUEST['url']) ? urldecode($_REQUEST['url']) : "");
$subaction = (isset($_REQUEST['subaction']) ? $_REQUEST['subaction'] : "");

$cfg = Configuration::get_instance()->get_cfg();

if ( isset($action) ) {
	require_once('inc/classes/ClientHandler.php');
	$client = ClientHandler::getInstance(getTransferClient($transfer));
	
	if ($action == "start")				$client->start($transfer);
	if ($action == "stop")				$client->stop($transfer);
	if ($action == "delete")			$client->delete($transfer);
	if ($action == "deletewithdata")	$client->deletewithdata($transfer);
	if ($action == "add") { 			$client->add($url, ( isset($_REQUEST['publictorrent']) && $_REQUEST['publictorrent'] == 'on' ?  getDownloadPath(true): getDownloadPath(false) ), ($subaction == "add" ? true : false)); exit(); }
	if ($action == "transfertabs") {	$client->gettabs(); exit(); }
	if ($action == "metafileupload") {
		if ( sizeof($_FILES) > 0 ) { // if a file is actually uploaded
			handleFileUpload($_FILES, $client, ( isset($_REQUEST['publictorrent']) && $_REQUEST['publictorrent'] == 'on' ?  getDownloadPath(true): getDownloadPath(false) ), ($subaction == "add" ? true : false));
			print("File was handled");
		}
			require_once('inc/plugins/PluginHandler.php');
			$ph = new PluginHandler();
			$plugin = $ph->getPlugin("metafileupload");
			$plugin->printForm();
	}
}

//header('Location: index.php'); // TODO: probably can be removed as actions are not shown

?>
