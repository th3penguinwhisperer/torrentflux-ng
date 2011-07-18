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
		print("<form method=post action=configure.php>
<input type=text name=username>
<input type=password name=password>
<input type=hidden name=action value=set>
<input type=hidden name=subaction value=add>
<input type=hidden name=plugin value=torrentfluxng>
<input type=submit value=\"Add user\">
</form>");
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
		if ($_REQUEST['subaction'] == "add")
			$this->addUser($_REQUEST['username'], $_REQUEST['password']);
	}


	function addUser($username, $password)
	{
		$db = DB::get_db()->get_handle();
		$user = mysql_real_escape_string($username);
		$pass = md5($password);
		$sql = "INSERT INTO tf_users (user_id, password) VALUES ('$user', '$pass' )";
		$result = $db->Execute($sql);

		if ($db->ErrorNo() != 0) dbError($sql);

		//create download dir
		$cfg = Configuration::get_instance()->get_cfg();
		$newuserdir = $cfg['path'] . $user;
		if( !is_dir($newuserdir) && !is_file($newuserdir) )
		{
			if( !mkdir($newuserdir, 755) ) // TODO check what appropriate mode would be
			{
				AuditAction($cfg["constants"]["error"], $cfg['constants']['error'], 'Failed to create directory ' . $newuserdir, $_SERVER['PHP_SELF']);
				print("Failed to create directory " . $newuserdir);
				exit();
			}
		} else { 
			$msg = "Directory $newuserdir already exists or is a file";
			AuditAction($cfg['constants']['error'], $cfg['constants']['error'], $msg);
			print($msg);
		}
	}

	function deleteUser($uid)
	{
		if($uid != 1) // if not administrator
		{
			$db = DB::get_db()->get_handle();

			// Get the user first before deleting its row from the db ;)
			$sql = "SELECT user_id FROM tf_users WHERE uid=" . $_REQUEST['uid'];
			$result = $db->Execute($sql);
			$rs = $result->FetchRow();
			$user = $rs['user_id'];

			$sql = "DELETE FROM tf_users WHERE uid=" . $_REQUEST['uid'];
			$result = $db->Execute($sql);

			if ($db->ErrorNo() != 0) dbError($sql);

			// TODO: transfer data from this user should be deleted as well (data on disk), rows in db for transmission, ...

			
			if ($user != "")
			{
				$cfg = Configuration::get_instance()->get_cfg();
				if( !rmdir($cfg['path'].$user) ) {
					$cfg = Configuration::get_instance()->get_cfg();
					AuditAction($cfg["constants"]["error"], $cfg['constants']['error'], "User with ID 1 cannot be deleted", $_SERVER['PHP_SELF'], $_SESSION['uid']);
					print("User with ID 1 download directory cannot be deleted or it didn't exist!"); // TODO: this should have its own method for showing errors
					exit();
				}
			}
		} else {
			$cfg = Configuration::get_instance()->get_cfg();
			AuditAction($cfg["constants"]["error"], $cfg['constants']['error'], "User with ID 1 cannot be deleted", $_SERVER['PHP_SELF'], $_SESSION['uid']);
			print("User with ID 1 cannot be removed!"); // TODO: this should have its own method for showing errors
			exit();
		}
	}



}

?>
