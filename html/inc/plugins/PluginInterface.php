<?php

interface PluginInterface
{
	function show();

	function get();
	
	function handleRequest($requestdata);

	static function getConfiguration();

	static function setConfiguration($configArray);
}

?>
