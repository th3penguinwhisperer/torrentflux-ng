<?php

require_once('settings/db.config.php');

class DB
{
	static private $db;

	private $handle;

	private function __construct() {
		require_once('inc/adodb/adodb.inc.php');
		$dbinst = NewADOConnection('mysql');
		try {
			$dbinst->Connect(DbSettings::$server, DbSettings::$user, DbSettings::$pwd, DbSettings::$database);
		} catch (exception $e) {
			print_r($e); // TODO make logging/error print mechanism
		}
		$this->handle = $dbinst;
	}
   
	static function get_db()
	{
		if ( ! isset(DB::$db) )
			DB::$db = new DB();
		return DB::$db;
	}
 
	function get_handle()
	{
		return $this->handle;
	}
}

?>
