<?php


//$db = null;

//function getDbInstance() {
//	if (!isset($db)) {
//		$server = "hostnameOrIp";
//		$user = "username";
//		$pwd = "password";
//		$database = "database";
//		$db = NewADOConnection('mysql');
//		$db->Connect($server, $user, $pwd, $database);
//	}
//	return $db;
//}

class DB
{
	static private $db;
	static private $server = "hostnameOrIp";
	static private $user = "username";
	static private $pwd = "password";
	static private $database = "database";

	private $handle;

	private function __construct() {
		require_once('inc/adodb/adodb.inc.php');
		$dbinst = NewADOConnection('mysql');
		try {
			$dbinst->Connect(DB::$server, DB::$user, DB::$pwd, DB::$database);
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
