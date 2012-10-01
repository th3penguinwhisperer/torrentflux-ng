<?php

require_once('inc/generalfunctions.php');
require_once('inc/plugins/PluginInterface.php');
require_once('inc/plugins/PluginHandler.php');

class Stats implements PluginInterface
{

	function __construct()
	{
		;
	}

	function show()
	{
		;
	}

	function get()
	{
		$ph = new PluginHandler();
		$pluginNames = $ph->getAvailablePlugins(PluginHandler::PLUGINTYPE_TRANSFERCLIENT);

		$totaldownloadedbytes = 0;
		$totaluploadedbytes = 0;
		$uploadrate = 0;
		$downloadrate = 0;
		$transfercount = 0;
		foreach ( $pluginNames as $pn ) {
			$pi = $ph->getPlugin($pn['pluginname']);
			$stats = $pi->getstats();
			
			$totaldownloadedbytes += $stats['downloadedtotal'];
			$totaluploadedbytes += $stats['uploadedtotal'];
			$uploadrate += $stats['uprate'];
			$downloadrate += $stats['downrate'];
			$transfercount += $stats['transfercount'];
		}
	}

	function getAjaxData()
	{
		;
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
