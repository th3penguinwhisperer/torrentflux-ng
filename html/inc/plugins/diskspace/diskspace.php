<?php

class DiskspaceInfo
{

	function __construct()
	{
		;
	}
	
	function show()
	{
		print( $this->getDiskspaceUi() );
	}
	
	function getDiskspaceUi() {
		$cfg = Configuration::get_instance()->get_cfg();

		$output = "";
		$userpath = $cfg['path'] . $cfg['user'];
		$diskspaceusage = getDriveSpace( $userpath );
		if ( $diskspaceusage > $cfg['diskusagewarninglevel'] ) $diskspacecolor = "#ff0000";
		else $diskspacecolor = '#33cc33';
		$output = $diskspaceusage . '% disk usage (' . formatBytesTokBMBGBTB( disk_free_space($userpath) ) . "/" . formatBytesTokBMBGBTB( disk_total_space($userpath) ) . ")";
		$output .= '<script type="text/javascript" src="js/diskspace.js"></script>';
		$output .= '<script type="text/javascript">drawProgressBar(\'' . $diskspacecolor . '\', 300, ' . $diskspaceusage . ');</script>';
		$output .= '<link rel="stylesheet" type="text/css" href="css/diskspace.css" />';
		
		return $output;
	}

}

?>
