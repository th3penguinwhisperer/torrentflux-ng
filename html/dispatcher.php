<?php

require_once('inc/singleton/Configuration.php');
require_once('inc/generalfunctions.php');

$action = $_REQUEST['action'];
$plugin = (isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : "");
$client = (isset($_REQUEST['client']) ? $_REQUEST['client'] : "");
$transfer = (isset($_REQUEST['transfer']) ? $_REQUEST['transfer'] : "");
$url = (isset($_REQUEST['url']) ? $_REQUEST['url'] : "");

// TODO get these replaced by a config manager singleton class
$cfg = Configuration::get_instance()->get_cfg();

//$cfg['uid'] = 0;
//$path = "/usr/local/torrentflux/git/administrator";
//$cfg["transmission_rpc_enable"] = true;

if ( isset($action) ) {
	require_once('inc/classes/ClientHandler.php');
	$client = ClientHandler::getInstance(getTransferClient($transfer));
	
	if ($action == "start")				$client->start($transfer);
	if ($action == "stop")				$client->stop($transfer);
	if ($action == "delete")			$client->delete($transfer);
	if ($action == "deletewithdata")	$client->deletewithdata($transfer);
	if ($action == "add") addTransmissionTransfer($cfg['uid'], $url, $cfg['path'].$cfg['user'], ($subaction == "add" ? true : false) ); // addTransmissionTransfer($uid = 0, $url, $path, $paused=true)
	if ($action == "transferdetails") {	require_once("inc/page/transferdetails.php"); }
	if ($action == "metafileupload") 	handleFileUpload($_FILES);
}

?>