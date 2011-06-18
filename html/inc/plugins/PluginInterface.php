<?php

interface PluginInterface
{
	function show();

	function getConfiguration();

	function setConfiguration($configArray);
}

?>
