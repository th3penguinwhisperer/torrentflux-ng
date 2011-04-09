<?php

interface ClientInterface
{
	function getCapabilities();
	
	function getActions($transferhash);
	
	function executeAction($transfer, $action);
	
	function getTransferList($uid);
}

?>