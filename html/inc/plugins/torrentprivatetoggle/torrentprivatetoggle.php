<?php

require_once('inc/plugins/PluginAbstract.php');
require_once('inc/generalfunctions.php');
require_once('inc/classes/FormGenerator.php');

class TorrentPrivateToggle extends PluginAbstract
{

	function show() {
		// nothing to show
	}
	
	function get() {
		;
	}
	
	function moveFile($transfer, $destination) {
		print("Moving file $transfer to $destination");
		
		require_once('inc/classes/ClientHandler.php');
		$client = ClientHandler::getInstance(getTransferClient($transfer));
		$client->move($transfer, $destination);
	}
	
	function handleRequest($requestdata)
	{
		if ( is_request_set('transfer') ) { // TODO: rewrite this so developer doesn't need to know the exact field name that is generated in the form
			$cfg = Configuration::get_instance()->get_cfg();
			$privatepath = getDownloadPath();
			$sharedpath = getDownloadPath($shared = true);

			$transferhash = $_REQUEST['transfer'];
			$client = ClientHandler::getInstance(getTransferClient($transferhash));
			$transfer = $client->getTransfer($transferhash);
			$data = $transfer->getTransferListItem();
			
			// Let's check if this is a transfer already in the tf_transfer table
			$db = DB::get_db()->get_handle();
			$sql = "SELECT public FROM tf_transfers WHERE tid='$transferhash'";
			$row = $db->GetRow($sql);
			if ($db->ErrorNo() != 0) dbError($sql);
			
			if ( sizeof($row) == 0 ) { // If it's not, make an entry for it and make it private
				AuditAction("TORRENTPRIVATETOGGLE", "INFO", "Transfer $transferhash is foreign. Will try to import");
				$sql = "INSERT INTO tf_transfers (tid, client, public) VALUES ('$transferhash', '" . getTransferClient($transferhash) . "', '0')";
				$db->Execute($sql);
				if ($db->ErrorNo() != 0) dbError($sql);
				print("Imported transfer $transferhash<br>");
				$import = true;
			} else {
				$import = false;
			}

			if ( $cfg['uid'] == 1 || $data['is_owner'] ) { // if admin or owner
				if ( strpos($data['datapath'], $privatepath) === FALSE || $import ) {
					AuditAction("TORRENTPRIVATETOGGLE", "INFO", "Making Transfer $transferhash private");
					$client->move($transferhash, $privatepath);
					print("Transfer made private<br>");
				} else {
					AuditAction("TORRENTPRIVATETOGGLE", "INFO", "Making Transfer $transferhash public");
					$client->move($transferhash, $sharedpath);
					print("Transfer made public<br>");
				}
			}
		} else {
			$this->show(); // SHOW
		}
	}
	
	/* NOT IN USE */

	
	static function getConfiguration() {
		;
	}
	
	static function setConfiguration($configArray) {
		;
	}
}

?>
