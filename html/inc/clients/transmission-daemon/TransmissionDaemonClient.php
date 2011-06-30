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
		$hash = addTransmissionTransfer( $_SESSION['uid'], $fullfilename, $cfg['download_path'] );
		
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
		deleteTransmissionTransfer($_SESSION['uid'], $transfer);
	}
	
	function deletewithdata($transfer) {
		$cfg = Configuration::get_instance()->get_cfg();
		deleteTransmissionTransferWithData($_SESSION['uid'], $transfer);
	}
	
	function add($url, $paused) {
		$cfg = Configuration::get_instance()->get_cfg();
		addTransmissionTransfer($_SESSION['uid'], $url, $cfg['download_path'], $paused);
	}
	
	function gettabs($tabname = "") {
		require_once('inc/clients/transmission-daemon/tabs/tabs.php');
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
			"metadataPercentComplete", "rateDownload", "rateUpload", "status", "files", "wanted", "trackerStats" );

		return new TransmissionDaemonTransfer( getTransmissionTransfer($hash, $fields) );
	}
	
	function setTransfer($transfer, $data) {
		require_once('inc/clients/transmission-daemon/functions.rpc.transmission.php');

		setTransmissionTransferProperties($transfer, $data);
	}
	
/*	function getTransfer($transfer, $fields = array()) {
		require_once("inc/clients/transmission-daemon/functions.rpc.transmission.php");
		
		return new TransmissionDaemonTransfer( getTransmissionTransfer($hash, $fields) );
	}
*/
}

?>
