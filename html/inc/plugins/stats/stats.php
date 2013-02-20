<?php

require_once('inc/generalfunctions.php');
require_once('inc/plugins/PluginInterface.php');
require_once('inc/plugins/PluginHandler.php');

class Stats implements PluginInterface
{
	private $totaldownloadedbytes = 0;
	private $totaluploadedbytes = 0;
	private $totaldownloadrate = 0;
	private $totaluploadrate = 0;
	private $totaltransfercount = 0;

	function __construct()
	{
		$this->calculatedata();
	}

	function show()
	{
		;
	}

	function calculatedata()
	{
		$ph = new PluginHandler();
		$pluginNames = $ph->getAvailablePlugins(PluginHandler::PLUGINTYPE_TRANSFERCLIENT);

		foreach ( $pluginNames as $pn ) {
			$pi = $ph->getPlugin($pn['pluginname']);
			$stats = $pi->getstats();
			
			$this->totaldownloadedbytes += $stats['downloadedtotal'];
			$this->totaluploadedbytes += $stats['uploadedtotal'];
			$this->totaluploadrate += $stats['uprate'];
			$this->totaldownloadrate += $stats['downrate'];
			$this->totaltransfercount += $stats['transfercount'];
		}
	}

	function get()
	{

		$output = "<table>";
		$output .= "<tr><td>Total Downloaded Size: </td><td id=plugin_stats_totaldownloadedbytes>" . formatBytesTokBMBGBTB( $this->totaldownloadedbytes ) . "</td><td>Download rate: </td><td id=plugin_stats_totaldownloadrate>" . formatBytesTokBMBGBTB( $this->totaldownloadrate ) . "/s</td></tr>";
		$output .= "<tr><td>Total Uploaded Size: </td><td id=plugin_stats_totaluploadedbytes>" . formatBytesTokBMBGBTB( $this->totaluploadedbytes ) . "</td><td>Upload rate: </td><td id=plugin_stats_totaluploadrate>" . formatBytesTokBMBGBTB( $this->totaluploadrate ) . "/s</td></tr>";
		$output .= "<tr><td>Total Transfer Count: </td><td id=plugin_stats_totaltransfercount>" . $this->totaltransfercount . "</td></tr>";
		$output .= "</table>";

		return $output;
	}

	function getAjaxData()
	{
		return "$this->totaldownloadedbytes;$this->totaluploadedbytes;$this->totaldownloadrate;$this->totaluploadrate;$this->totaltransfercount";
	}

	function getConfiguration()
	{
		;
	}

	function setConfiguration($configArray)
	{
		;
	}
	
	function handleRequest($requestdata) {
		$this->show();
	}
}

?>
