<?php

require_once('inc/classes/singleton/Configuration.php');
require_once('inc/classes/singleton/db.php');

class ClientHandler
{	
	static private $ch;

	private $handle;
	private $clients = array();

	private function __construct() {
		require_once('inc/classes/singleton/db.php');
		$db = DB::get_db()->get_handle();

		// get the clients that are saved in the database
		$sql = "SELECT pluginname, plugininclude, pluginclass FROM tf_plugins WHERE plugintype='transferclient' AND pluginenabled='1'";
		$recordset = $db->Execute($sql);
		if ($db->ErrorNo() != 0) dbError($sql);
		$clients = array();
		while($clientinfo = $recordset->FetchRow()) {
			require_once($clientinfo['plugininclude']);
			$client = $clientinfo['pluginclass']::getInstance();
			$clients[$clientinfo['pluginname']] = $client;
		}
		$this->clients = $clients;
	}
   
	static function get_ch()
	{
		if ( ! isset(ClientHandler::$ch) )
			ClientHandler::$ch = new ClientHandler();
		return ClientHandler::$ch;
	}
 
	function get_handle()
	{
		return $this->handle;
	}


	/**
	 * get ClientHandler-instance
	 *
	 * @param $client client-type
	 * @return ClientHandler
	 */
	function getInstance($client = "") {
		$cfg = Configuration::get_instance()->get_cfg();

		if( isset($this->clients[$client]) ) {
			return $this->clients[$client];
		} elseif( isset($cfg["rewrite_btclient"]) ) {
			return $this->getInstance($cfg["rewrite_btclient"]);
		}

		AuditAction("CLIENTHANDLER", $cfg["constants"]["error"], "ClientHandler does not have an instance of client $client");

		return false;
	}
	
}

?>
