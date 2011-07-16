<?php

require_once('inc/singleton/db.php');
require_once('inc/plugins/PluginInterface.php');

class TorrentfluxngConfiguration implements PluginInterface
{

	function __construct()
	{
		;
	}
	
	function show()
	{
		;
	}

	function getConfiguration()
	{
		// show users
		require_once('inc/singleton/db.php');
		$db = DB::get_db()->get_handle();
		
		$sql = "SELECT * FROM tf_users ORDER BY uid";
		$recordset = $db->Execute($sql);
	
		if ($db->ErrorNo() != 0) dbError($sql);
		print("<table>");
		while($transfer = $recordset->FetchRow())
			$this->showUser($transfer);
		print("</table>");
	}

	function showUser($transfer) {
		print("<tr>");
		print("<td>$transfer[user_id]</td>");
		print("<td><a href=\"configure.php?action=set&subaction=delete&plugin=torrentfluxng&uid=$transfer[uid]\"><img src=images/delete.png></a></td>");
		print("</tr>");
	}
	
	function setConfiguration($configArray)
	{
		;
	}

}

?>
