<?php

require_once("inc/generalfunctions.php");
require_once("inc/plugins/basictransferadd/transfersource.torrent.php");
require_once("inc/plugins/basictransferadd/transfersource.fileupload.php");

class BasicTransferAdd
{
	function __construct()
	{
		;
	}
	
	function show()
	{
		print('<br>');
		
		$fileUpload = new TransfersourceFileupload();
		$transfersource = new TransfersourceTorrent();
		
		$fileUpload->show();
		$transfersource->show();
	}
}

?>
