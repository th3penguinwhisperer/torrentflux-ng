<?php

interface PluginInterface
{
	function show();

	function get();
	
	function handleRequest($requestdata);

	function getConfiguration();

	function setConfiguration($configArray);
}

?>
