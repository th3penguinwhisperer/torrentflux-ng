<?php

interface ClientInterface
{
	function getCapabilities();
	
	function executeAction($transfer, $action);
	
	function getTransferList($uid);
	
	function fileUploaded($fullfilename);
}

?>
