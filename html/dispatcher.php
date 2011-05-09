<?php

require_once('inc/singleton/Configuration.php');
require_once('inc/generalfunctions.php');

$action = $_REQUEST['action'];
$plugin = (isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : "");
$client = (isset($_REQUEST['client']) ? $_REQUEST['client'] : "");
$transfer = (isset($_REQUEST['transfer']) ? $_REQUEST['transfer'] : "");
$url = (isset($_REQUEST['url']) ? $_REQUEST['url'] : "");

$cfg = Configuration::get_instance()->get_cfg();

if ( isset($action) ) {
	require_once('inc/classes/ClientHandler.php');
	$client = ClientHandler::getInstance(getTransferClient($transfer));
	
	if ($action == "start")				$client->start($transfer);
	if ($action == "stop")				$client->stop($transfer);
	if ($action == "delete")			$client->delete($transfer);
	if ($action == "deletewithdata")	$client->deletewithdata($transfer);
	if ($action == "add") 				$client->add($url, ($subaction == "add" ? true : false));
	if ($action == "transfertabs") {	$client->gettabs(); exit(); }
	if ($action == "metafileupload") 	handleFileUpload($_FILES);
}

header('Location: index.php');

?>
