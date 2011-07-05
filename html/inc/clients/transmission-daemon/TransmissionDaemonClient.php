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

	function getConfiguration() {
		require_once('inc/clients/transmission-daemon/functions.rpc.transmission.php');
		$sessiondata = getSessionInfo();
	
/*
Array
        (
            [alt-speed-down] => 50
            [alt-speed-enabled] => 
            [alt-speed-time-begin] => 540
            [alt-speed-time-day] => 127
            [alt-speed-time-enabled] => 
            [alt-speed-time-end] => 1020
            [alt-speed-up] => 50
            [blocklist-enabled] => 
            [blocklist-size] => 0
            [config-dir] => /usr/local/transmission
            [dht-enabled] => 1
            [download-dir] => /usr/home/torrentflux/transmission/home/Downloads
            [encryption] => preferred
            [incomplete-dir] => /root/Downloads
            [incomplete-dir-enabled] => 
            [lpd-enabled] => 
            [peer-limit-global] => 240
            [peer-limit-per-torrent] => 60
            [peer-port] => 51413
            [peer-port-random-on-start] => 0
            [pex-enabled] => 1
            [port-forwarding-enabled] => 1
            [rename-partial-files] => 1
            [rpc-version] => 9
            [rpc-version-minimum] => 1
            [script-torrent-done-enabled] => 1
            [script-torrent-done-filename] => /usr/local/www/data-dist/nonssl/git/torrentflux/html/bin/transmissionfinished.php &
            [seedRatioLimit] => 2
            [seedRatioLimited] => 
            [speed-limit-down] => 16
            [speed-limit-down-enabled] => 
            [speed-limit-up] => 20
            [speed-limit-up-enabled] => 1
            [start-added-torrents] => 1
            [trash-original-torrent-files] => 
            [version] => 2.04 (11151)
        )

*/
		require_once('inc/clients/transmission-daemon/functions.rpc.transmission.php');
		$sessiondata = getSessionInfo();
	
		print("<form method=post action=configure.php>");
		print("<input type=hidden name=plugin value=transmission-daemon>");
		print("<input type=hidden name=action value=set>");
		print("Upload-rate <input type=text name=speed-limit-up value=".$sessiondata['speed-limit-up'].">");
		print("<input type=checkbox name=speed-limit-up-enabled>Enable upload rate limit<BR>");
		print("Download-rate <input type=text name=speed-limit-down value=". $sessiondata['speed-limit-down'] .">");
		print("<input type=checkbox name=speed-limit-down-enabled>Enable download rate limit<BR>");
		print("<input type=submit text=Configure>");
		print("</form>");

	}

	function setConfiguration($configArray) {
		require_once('inc/clients/transmission-daemon/functions.rpc.transmission.php');
		$sessiondata = getSessionInfo();

		print_r($_REQUEST);
		$changedParameters = array();
		if ( $sessiondata['speed-limit-up'] != $_REQUEST['speed-limit-up'] )
			$changedParameters['speed-limit-up'] = (int)$_REQUEST['speed-limit-up'];
		if ( $sessiondata['speed-limit-down'] != $_REQUEST['speed-limit-down'] )
			$changedParameters['speed-limit-down'] = (int)$_REQUEST['speed-limit-down'];
		//$_REQUEST['speed-limit-down'];
		//$_REQUEST['speed-limit-up-enabled'];
		//$_REQUEST['speed-limit-down-enabled'];

		foreach ( $changedParameters as $parametername => $parametervalue ) {
			//print("I found a parameter: " . $parametername . " with value " . $parametervalue);
			setSessionParameter($parametername, $parametervalue);
		}
	}

}

?>
