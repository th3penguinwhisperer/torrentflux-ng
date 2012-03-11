<?php

require_once('inc/plugins/PluginInterface.php');

class SearchEngines implements PluginInterface
{

	function __construct()
	{
		;
	}
	
	function show()
	{
		require_once('inc/plugins/searchengines/torrentSearch.php');
		print( getPage() ); // prints the string
	}

	function get()
	{
		require_once('inc/plugins/searchengines/torrentSearch.php');
		return getPage(); // returns string
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
