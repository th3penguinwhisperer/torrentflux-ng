<?php

require_once('inc/plugins/PluginInterface.php');

class DiskspaceInfo implements PluginInterface
{

	function __construct()
	{
		;
	}
	
	function show()
	{
		print( $this->getDiskspaceUi() );
	}

	function getConfiguration()
	{
		;
	}
	
	function setConfiguration($configArray)
	{
		;
	}

	function getDiskspaceUi() {
		$cfg = Configuration::get_instance()->get_cfg();
		
		$output = "";
		$diskspaceusage = getDriveSpace( $cfg['rewrite_download_path'] );
		if ( $diskspaceusage > $cfg['rewrite_diskusagewarninglevel'] ) $diskspacecolor = "#ff0000";
		else $diskspacecolor = '#33cc33';
		$diskfreespace = disk_free_space($cfg['rewrite_download_path']);
		$disktotalspace = disk_total_space($cfg['rewrite_download_path']);
		$output = $diskspaceusage . '% disk usage (' . formatBytesTokBMBGBTB( $disktotalspace - $diskfreespace ) . "/" . formatBytesTokBMBGBTB( $disktotalspace ) . ")";
		$output .= '<script type="text/javascript" src="js/diskspace.js"></script>';
		$output .= '<script type="text/javascript">drawProgressBar(\'' . $diskspacecolor . '\', 300, ' . $diskspaceusage . ');</script>';
		$output .= '<link rel="stylesheet" type="text/css" href="css/diskspace.css" />';
		
		return $output;
	}

}

?>
