<?php

interface ClientInterface
{
	function getCapabilities();
	
	function getActions();
	
	function executeAction($transfer, $action);
	
	function getTransferList($uid);
}

?>