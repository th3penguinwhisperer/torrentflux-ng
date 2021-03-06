<?php

require_once('inc/plugins/PluginAbstract.php');

class DiskspaceInfo extends PluginAbstract
{

	function __construct()
	{
		;
	}
	
	function show()
	{
		print( $this->getDiskspaceUi() );
	}

	function get()
	{
		return $this->getDiskspaceUi();
	}

	function getDiskspaceUi() {
		$cfg = Configuration::get_instance()->get_cfg();
		
		$output = "";
		$diskspaceusage = getDriveSpace( $cfg['rewrite_download_path'] ); // NOTE: in percentage!
		if ( $diskspaceusage > $cfg['rewrite_diskusagewarninglevel'] ) $diskspacecolor = "#ff0000";
		else $diskspacecolor = '#33cc33';
		$diskfreespace = disk_free_space($cfg['rewrite_download_path']);
		$disktotalspace = disk_total_space($cfg['rewrite_download_path']);
		$output .= '<script type="text/javascript" src="js/diskspace.js"></script>';
		$output .= '<script type="text/javascript">drawProgressBar(\'' . $diskspacecolor . '\', 300, ' . $diskspaceusage . ',' . $diskfreespace . ',' . $disktotalspace . ');</script>';
		$output .= '<link rel="stylesheet" type="text/css" href="css/diskspace.css" />';
		
		return $output;
	}

	function getAjaxData() {
		$cfg = Configuration::get_instance()->get_cfg();
		$diskspaceusage = getDriveSpace( $cfg['rewrite_download_path'] ); // NOTE: in percentage!
		$diskfreespace = disk_free_space($cfg['rewrite_download_path']);
		$disktotalspace = disk_total_space($cfg['rewrite_download_path']);
		$diskspacecolor = ($diskspaceusage > $cfg['rewrite_diskusagewarninglevel'] ? '#ff0000' : '#33cc33' );

		$output = "$diskfreespace;$disktotalspace;$diskspacecolor";

		return $output;
	}
	
	function handleRequest($requestdata) {
		$this->show();
	}

}

?>
