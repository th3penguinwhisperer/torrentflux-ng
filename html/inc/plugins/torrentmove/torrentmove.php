<?php

require_once('inc/plugins/PluginInterface.php');
require_once('inc/generalfunctions.php');
require_once('inc/classes/FormGenerator.php');

class TorrentMove implements PluginInterface
{

	function show() {
		$form = new FormGenerator("torrentmove");
		// Lets generate a dropdown that shows all subdirectories
		$form->add_dropdown('destination', $this->getDirectoryList());
		$form->add_argument('transfer', $_REQUEST['transfer']);
		$form->add_argument('subaction', $_REQUEST['subaction']);
		$form->add_direct_argument('client', $_REQUEST['client']);
		$form->add_direct_argument('action', $_REQUEST['action']);
		$form->add_direct_argument('plugin', $_REQUEST['plugin']);
		$form->add_submit_button();
		print($form->get());
	}
	
	private function traverseDirTree($dir, &$tree_array, $depth = 0) {
		if ( is_dir($dir) ) {
			$depth++;
			$dh = opendir($dir);
			
			if ($dh = opendir($dir)) {
				while (($entry = readdir($dh)) !== false) {
					if ( substr($entry, 0, 1) !== "." && is_dir($dir . "/" . $entry) ) {
						array_push( $tree_array, $dir . "/" . $entry );
						if ($depth < 2)
							$this->traverseDirTree($dir . "/" . $entry, $tree_array, $depth);
					}
				}
				closedir($dh);
			}
			
		}
	}
	
	private function getDirectoryList() {
		$cfg = Configuration::get_instance()->get_cfg();
		$tree_array = array();
		
		if ( $cfg['isAdmin'] ) {
			$dir = $cfg['rewrite_path'];
			$this->traverseDirTree($dir, $tree_array);
		} else {
			print("Not yet implemented for normal users");
		}
		
		return $tree_array;
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
		if ( ! is_request_set('torrentmove_subaction') || ! is_request_set('torrentmove_destination') ) { // TODO: rewrite this so developer doesn't need to know the exact field name that is generated in the form
			$this->show(); // SHOW
		} elseif ( $_REQUEST['torrentmove_subaction'] === "move" ) {
			if ( is_request_set('torrentmove_destination')  && is_request_set('torrentmove_transfer') ) {
				$this->moveFile( $_REQUEST['torrentmove_transfer'], urldecode($_REQUEST['torrentmove_destination']) );
			} else {
				require_once('inc/classes/singleton/Configuration.php');
				$cfg = Configuration::get_instance()->get_cfg();
				AuditAction('TORRENT MOVE', $cfg["constants"]["error"], 'Not enough arguments passed to execute torrent move!');
				print("<b>Some data is not set</b>");
			}
		}
	}
	
	/* NOT IN USE */

	
	function getConfiguration() {
		;
	}
	
	function setConfiguration($configArray) {
		;
	}
}

?>
