<?php

require_once('inc/plugins/PluginInterface.php');
require_once('inc/plugins/unrarfile/uncompress.php');

class UnrarFile implements PluginInterface
{

	function __construct()
	{
		;
	}

	// TODO: create new interface extending PluginInterface for file managment plugins?
	function fileaction($dir, $filename)
	{
		uncompress($dir, $filename, "");
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
