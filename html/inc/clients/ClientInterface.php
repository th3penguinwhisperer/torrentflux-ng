<?php

interface ClientInterface
{
	function getCapabilities();
	
	function executeAction($transfer, $action);
	
	function getTransferList($uid);
	
	function fileUploaded($fullfilename, $path);
	
	function start($transfer);
	
	function stop($transfer);
	
	function delete($transfer);
	
	function deletewithdata($transfer);
	
	function add($url, $path, $paused);
	
	function gettabs($tabname);

	function getstats();

	function getConfiguration();

	function setConfiguration($configArray);
	
	function move($transfer, $destination);
}

?>
