<?php

require_once('inc/singleton/Configuration.php');

class ClientHandler
{	
	/**
	 * get ClientHandler-instance
	 *
	 * @param $client client-type
	 * @return ClientHandler
	 */
	static function getInstance($client = "") {
		
		// create and return object-instance
		switch ($client) {
		case "transmission-daemon":
			require_once('inc/clients/transmission-daemon/TransmissionDaemonClient.php');
			$handler = TransmissionDaemonClient::getInstance();
			break;
		default:
			$cfg = Configuration::get_instance()->get_cfg();
			$handler = ClientHandler::getInstance($cfg["rewrite_btclient"]);
		}
		return $handler;
		
	}
	
}

?>
