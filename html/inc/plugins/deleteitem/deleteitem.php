<?php

require_once('inc/plugins/PluginInterface.php');

class DeleteItem implements PluginInterface
{

	function __construct()
	{
		;
	}

	function isvalidaction($dir, $filename)
	{
		$cfg = Configuration::get_instance()->get_cfg();

		if($filename == "")
			return false;

		if ( is_file($cfg['rewrite_path'] . $dir . $filename) ) {
			return true;
		}

		return false;
	}

	function getaction($dir, $filename)
	{
		$cfg = Configuration::get_instance()->get_cfg();
		return "<a href=\"javascript:loadpopup('Delete Item', 'dispatcher.php?plugin=deleteitem&amp;action=passplugindata&amp;subaction=filemanagement&amp;dir=".urlencode($dir)."&amp;filename=".urlencode($filename)."', 'Loading...');centerPopup();loadPopup();\"><img src=\"themes/".$cfg['theme']."/images/dir/delete_on.png\" /></a>";
	}

	function fileaction($dir, $filename)
	{
		//convert and set variables
		$cfg = Configuration::get_instance()->get_cfg();
		$fulldir = $cfg['rewrite_path'].urldecode($dir);
		$filename = urldecode($filename);
		$fullname = tfb_shellencode($dir.$filename);

		if (!file_exists($fulldir . $filename)) { // TODO: create check if dir is ending with slash or not
			AuditAction($cfg["constants"]["error"], "Deleting item that does not exist: $filename");
			exit();
		} else {
			if ($filename != "") {
				print("Deleting file $filename");
				@unlink($fulldir . $filename);
			}
		}
	}
	
	// TODO: remove this function from File management plugin specific interface?
	function show()
	{
		//print( $this->getDiskspaceUi() );
	}

	function get()
	{
		return $this->getDiskspaceUi();
	}

	function getConfiguration()
	{
		;
	}
	
	function setConfiguration($configArray)
	{
		;
	}

}

?>
