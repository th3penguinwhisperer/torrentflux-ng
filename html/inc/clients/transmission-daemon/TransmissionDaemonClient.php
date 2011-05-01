<?php

require_once("inc/clients/ClientInterface.php");
require_once("inc/clients/transmission-daemon/TransmissionDaemonTransfer.php");
require_once("inc/clients/transmission-daemon/functions.rpc.transmission.php");
require_once("inc/singleton/Configuration.php");

class TransmissionDaemonClient implements ClientInterface
{
	
	static private $instance;
	
	static function getInstance() {
		if ( ! isset(TransmissionDaemonClient::$instance) )
			TransmissionDaemonClient::$instance = new TransmissionDaemonClient();
		return TransmissionDaemonClient::$instance;
	}
	
	function getCapabilities() {
		$capabilities = array("start", "stop", "delete", "deletewithdata", "add", "upload");
		
		return $capabilities;
	}
	
	function executeAction($transfer, $action) {
		;
	}
	
	function fileUploaded($fullfilename) {
		$cfg = Configuration::get_instance()->get_cfg();
		$hash = addTransmissionTransfer( $cfg['uid'], $fullfilename, $cfg['path'].$cfg['user'] );
		
		unlink($fullfilename);
	}
	
	function start($transfer) {
		startTransmissionTransfer($transfer);
	}

	function stop($transfer) {
		stopTransmissionTransfer($transfer);
	}
	
	function delete($transfer) {
		$cfg = Configuration::get_instance()->get_cfg();
		deleteTransmissionTransfer($cfg['uid'], $transfer);
	}
	
	function deletewithdata($transfer) {
		$cfg = Configuration::get_instance()->get_cfg();
		deleteTransmissionTransferWithData($cfg['uid'], $transfer);
	}
	
	function add($url, $paused) {
		$cfg = Configuration::get_instance()->get_cfg();
		addTransmissionTransfer($cfg['uid'], $url, $cfg['path'].$cfg['user'], $paused);
	}
	
	
	function getTransferList($uid) {
		$result = getUserTransmissionTransfers($uid);
	
		$arUserTorrent = array();
	
		// eta
		//  -1 : Done
		//  -2 : Unknown
		foreach($result as $aTorrent)
		{
			$transfer = new TransmissionDaemonTransfer($aTorrent);
			array_push($arUserTorrent, $transfer);
		}

		return $arUserTorrent;
	}
	
	function getTransfer($hash) {
		require_once("inc/clients/transmission-daemon/functions.rpc.transmission.php");
		$fields = array("id", "name", "eta", "downloadedEver", "hashString", "fileStats", "totalSize", "percentDone", 
			"metadataPercentComplete", "rateDownload", "rateUpload", "status", "files", "trackerStats" );

		return new TransmissionDaemonTransfer( getTransmissionTransfer($hash, $fields) );
	}
}

?>