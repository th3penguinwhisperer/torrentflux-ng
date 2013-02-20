<?php

require_once('inc/plugins/PluginInterface.php');
require_once('inc/generalfunctions.php');
require_once('inc/classes/FormGenerator.php');

class TorrentMove implements PluginInterface
{

	function show() {
		$form = new FormGenerator("torrentmove");
		$form->add_textfield('destination');
		$form->add_argument('transfer', $_REQUEST['transfer']);
		$form->add_argument('subaction', $_REQUEST['subaction']);
		$form->add_direct_argument('client', $_REQUEST['client']);
		$form->add_direct_argument('action', $_REQUEST['action']);
		$form->add_direct_argument('plugin', $_REQUEST['plugin']);
		$form->add_submit_button();
		print($form->get());
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
				$this->moveFile($_REQUEST['torrentmove_transfer'], $_REQUEST['torrentmove_destination']);
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
