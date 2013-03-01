<?php

require_once("inc/plugins/PluginInterface.php");

abstract class PluginAbstract implements PluginInterface
{
	function show() { ; }

	function get() { ; }
	
	function handleRequest($requestdata) { ; }

	static function getConfiguration() { 
		print("<i>No configuration options for this plugin</i>"); 
	}

	static function setConfiguration($configArray) { ; }
}

?>
