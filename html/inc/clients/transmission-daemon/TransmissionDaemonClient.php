<?php

require_once("inc/clients/ClientInterface.php");
require_once("inc/clients/transmission-daemon/TransmissionDaemonTransfer.php");

class TransmissionDaemonClient implements ClientInterface
{
	
	function getCapabilities() {
		$capabilities = array("start", "stop", "delete", "deletewithdata", "add", "upload");
		
		return $capabilities;
	}
	
	function executeAction($transfer, $action) {
		;
	}
	
	function fileUploaded($fullfilename) {
		//require_once('inc/functions/functions.rpc.transmission.php');
		// TODO create config manager to replace these variable definitions here
		$cfg['uid'] = 0;
		$cfg['path'] = "/usr/local/torrentflux/git/administrator";
		$cfg['user'] = "administrator";
		
		$hash = addTransmissionTransfer( $cfg['uid'], $fullfilename, $cfg['path'].$cfg['user'] );
		
		unlink($fullfilename);
		//unlink($cfg['transfer_file_path'].$filename);
	}
	
	function getTransferList($uid) {
		require_once("inc/clients/transmission-daemon/functions.rpc.transmission.php");
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
