<?php

require_once("inc/clients/ClientInterface.php");
require_once("inc/clients/transmission-daemon/TransmissionDaemonTransfer.php");
require_once("inc/clients/transmission-daemon/functions.rpc.transmission.php");
require_once("inc/classes/singleton/Configuration.php");

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
	
	function fileUploaded($fullfilename, $path) {
		$hash = addTransmissionTransfer( $_SESSION['uid'], $fullfilename, $path );
		
		unlink($fullfilename);
		
		return $hash;
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
	
	function move($transfer, $location) {
		$cfg = Configuration::get_instance()->get_cfg();
		move($transfer, $location);
	}
	
	function deletewithdata($transfer) {
		$cfg = Configuration::get_instance()->get_cfg();
		deleteTransmissionTransferWithData($_SESSION['uid'], $transfer);
	}
	
	function add($url, $path, $paused) {
		$cfg = Configuration::get_instance()->get_cfg();
		addTransmissionTransfer($_SESSION['uid'], $url, $path, $paused);
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
		$fields = array("uploadedEver", "id", "name", "eta", "downloadedEver", "hashString", "downloadDir", "fileStats", "totalSize", "percentDone", 
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

	static function getConfiguration() {
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
		//print_r($sessiondata); // For debugging
		print("<form method=post action=configure.php>");
		print("<input type=hidden name=plugin value=transmission-daemon>");
		print("<input type=hidden name=action value=set>");
		print("Upload-rate <input type=text name=speed-limit-up value=".$sessiondata['speed-limit-up'].">");
		print("<input type=checkbox name=speed-limit-up-enabled " . ($sessiondata['speed-limit-up-enabled'] ? "checked" : "") . ">Enable upload rate limit<BR>");
		print("Download-rate <input type=text name=speed-limit-down value=". $sessiondata['speed-limit-down'] .">");
		print("<input type=checkbox name=speed-limit-down-enabled " . ($sessiondata['speed-limit-down-enabled'] ? "checked" : "") . ">Enable download rate limit<BR>");
		print("Encryption <select name=encryption>");
		print("		<option value=required " . ($sessiondata['encryption'] == "required" ? "selected=selected" : "") . ">required</option>");
		print("		<option value=preferred " . ($sessiondata['encryption'] == "preferred" ? "selected=selected" : "") . ">preferred</option>");
		print("		<option value=tolerated " . ($sessiondata['encryption'] == "tolerated" ? "selected=selected" : "") . ">tolerated</option>");
		print("</select><br>");
		print("Pex: <input type=checkbox name=pex-enabled " . ($sessiondata['pex-enabled'] ? "checked" : "") . "><BR>");
		print("DHT: <input type=checkbox name=dht-enabled " . ($sessiondata['dht-enabled'] ? "checked" : "") . "><BR>");
		print("Rename partial files: <input type=checkbox name=rename-partial-files " . ($sessiondata['rename-partial-files'] ? "checked" : "") . "><BR>");
		print("<input type=submit text=Configure>");
		print("</form>");

	}

	static function setConfiguration($configArray) {
		require_once('inc/clients/transmission-daemon/functions.rpc.transmission.php');
		$sessiondata = getSessionInfo();

		print_r($_REQUEST);
		$changedParameters = array();
		$this->getChangedTextfieldParameter($sessiondata, $changedParameters, 'speed-limit-up');
		$this->getChangedTextfieldParameter($sessiondata, $changedParameters, 'speed-limit-down');
		$this->getChangedTextfieldParameter($sessiondata, $changedParameters, 'encryption');

		$this->getChangedBooleanParameter($sessiondata, $changedParameters, 'speed-limit-up-enabled');
		$this->getChangedBooleanParameter($sessiondata, $changedParameters, 'speed-limit-down-enabled');
		$this->getChangedBooleanParameter($sessiondata, $changedParameters, 'pex-enabled');
		$this->getChangedBooleanParameter($sessiondata, $changedParameters, 'dht-enabled');
		$this->getChangedBooleanParameter($sessiondata, $changedParameters, 'rename-partial-files');

		foreach ( $changedParameters as $parametername => $parametervalue ) {
			//print("I found a parameter: " . $parametername . " with value " . $parametervalue);
			setSessionParameter($parametername, $parametervalue);
		}
	}

	function getChangedTextfieldParameter($sessiondata, &$changedParameters, $parametername) {
		$parametervalue = $_REQUEST[$parametername];
		if ( $sessiondata[$parametername] != $parametervalue ) {
			if( is_numeric($parametervalue) )
				$changedParameters[$parametername] = (int)$parametervalue;
			else
				$changedParameters[$parametername] = $parametervalue;
		}
	}
	
	function getChangedBooleanParameter($sessiondata, &$changedParameters, $parametername) {
		if( ! isset($_REQUEST[$parametername]) ) {
			$enabled = false;
			$parametervalue = "off";
		} else {
			$parametervalue = $_REQUEST[$parametername];
		}
		if( $parametervalue == "on" ) $enabled = true; else $enabled = false;
		if ( $sessiondata[$parametername] != $enabled )
			$changedParameters[$parametername] = $enabled;
	}

/*

array(2) {
  ["arguments"]=>
  array(7) {
    ["activeTorrentCount"]=>
    int(25)
    ["cumulative-stats"]=>
    array(5) {
      ["downloadedBytes"]=>
      float(1575288974808)
      ["filesAdded"]=>
      int(205725)
      ["secondsActive"]=>
      int(56638213)
      ["sessionCount"]=>
      int(55)
      ["uploadedBytes"]=>
      float(1840867931566)
    }
    ["current-stats"]=>
    array(5) {
      ["downloadedBytes"]=>
      float(128996754699)
      ["filesAdded"]=>
      int(26811)
      ["secondsActive"]=>
      int(3093490)
      ["sessionCount"]=>
      int(1)
      ["uploadedBytes"]=>
      float(887467960731)
    }
    ["downloadSpeed"]=>
    int(0)
    ["pausedTorrentCount"]=>
    int(21)
    ["torrentCount"]=>
    int(46)
    ["uploadSpeed"]=>
    int(300000)
  }
  ["result"]=>
  string(7) "success"
}

*/

	function getstats() {
		require_once('inc/clients/transmission-daemon/Transmission.class.php');
		$t = new Transmission();
		$ret= $t->session_stats();
		$stats = $ret['arguments'];
		
		$returnstats = array( 'downloadedtotal' => $stats['cumulative-stats']['downloadedBytes'], 'uploadedtotal' => $stats['cumulative-stats']['uploadedBytes'], 'transfercount' => $stats['torrentCount'], 'uprate' => $stats['uploadSpeed'], 'downrate' => $stats['downloadSpeed']);

		return $returnstats;
	}

}

?>
