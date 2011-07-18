<?php

require_once('inc/singleton/db.php');
require_once('inc/plugins/PluginInterface.php');
require_once('inc/generalfunctions.php');
require_once('inc/singleton/Configuration.php');

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
		if ($_REQUEST['subaction'] == "delete")
			$this->deleteUser($_REQUEST['uid']);
	}

	function deleteUser($uid)
	{
		if($uid != 1) // if not administrator
		{
			$db = DB::get_db()->get_handle();
			$sql = "DELETE FROM tf_users WHERE uid=" . $_REQUEST['uid'];
			$result = $db->Execute($sql);

			if ($db->ErrorNo() != 0) dbError($sql);

			// TODO: transfer data from this user should be deleted as well (data on disk), rows in db for transmission, ...
		} else {
			$cfg = Configuration::get_instance()->get_cfg();
			AuditAction($cfg["constants"]["error"], $cfg['constants']['error'], "User with ID 1 cannot be deleted", $_SERVER['PHP_SELF'], $_SESSION['uid']);
			print("User with ID 1 cannot be removed!"); // TODO: this should have its own method for showing errors
			exit();
		}
	}



}

?>
