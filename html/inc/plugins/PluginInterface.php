<?php

interface PluginInterface
{
	function show();

	function get();

	function getConfiguration();

	function setConfiguration($configArray);
}

?>
