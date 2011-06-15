<?php

require_once('inc/singleton/db.php');

class PluginHandler
{
	const PLUGINTYPE_TRANSFERSOURCE = 'transfersource';
	const PLUGINTYPE_TRANSFERCLIENT = 'transferclient';
	const PLUGINTYPE_INFO = 'info';
	
	private $db;
	
	function __construct()
	{
		$this->db = DB::get_db()->get_handle();
	}
	
	function getPlugin($pluginname)
	{
		$sql = "SELECT plugininclude, pluginclass FROM tf_plugins WHERE `pluginname`='" . $pluginname . "' AND `pluginenabled`='1' AND `pluginconfigured`='1'";
		$rs = $this->db->Execute($sql);
		if ($this->db->ErrorNo() != 0) print("THERE WAS AN ERROR WITH THIS QUERY: " . $sql); // TODO: Copy over dbError($sql) method 
		if ($rs)
			while ($arr = $rs->FetchRow()) {
				//print_r($arr);
				require_once($arr[0]);
				$className = $arr[1];
		  		$inst = new $className;
		  		$inst->show();
			}
	}
	
	function getAvailablePlugins($plugintype)
	{
		$sql = "SELECT pluginname, plugindisplayname FROM tf_plugins WHERE `plugintype`='" . $plugintype . "' AND `pluginenabled`='1' AND `pluginconfigured`='1' ORDER BY pluginorder";
		$rs = $this->db->Execute($sql);
		if ($this->db->ErrorNo() != 0) print("THERE WAS AN ERROR WITH THIS QUERY: " . $sql); // TODO: Copy over dbError($sql) method 
		$pluginNames = array();
		if ($rs) {
			while ($arr = $rs->FetchRow()) {
				array_push($pluginNames, $arr);
			}
		}
		return $pluginNames;
	}
}

?>
