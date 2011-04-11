<?php

$action = $_REQUEST['action'];
$plugin = (isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : "");
$client = (isset($_REQUEST['client']) ? $_REQUEST['client'] : "");
$transfer = (isset($_REQUEST['transfer']) ? $_REQUEST['transfer'] : "");
$url = (isset($_REQUEST['url']) ? $_REQUEST['url'] : "");

$cfg["transmission_rpc_enable"] = true;

$cfg['uid'] = 0;
$path = "/usr/local/torrentflux/git/administrator";

if ( $cfg["transmission_rpc_enable"] && isset($action)) {
	require_once('inc/clients/transmission-daemon/functions.rpc.transmission.php');
	
	if ($action == "start")	startTransmissionTransfer($transfer);
	if ($action == "stop")	stopTransmissionTransfer($transfer);
	if ($action == "delete")	deleteTransmissionTransfer($cfg['uid'], $transfer);
	if ($action == "deletewithdata")	deleteTransmissionTransferWithData($cfg['uid'], $transfer);
	if ($action == "upload")	addTransmissionTransfer($cfg['uid'], $url, $path); // addTransmissionTransfer($uid = 0, $url, $path, $paused=true)
	if ($action == "transferdetails") {
		require_once("inc/page/transferdetails.php");
	}
}

?>
