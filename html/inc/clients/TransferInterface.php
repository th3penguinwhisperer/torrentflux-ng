<?php

interface TransferInterface
{
	function __construct($data);
	
	/**
	 * Returns the array with data in the format necessary to build the transfer list
	 */
	function getTransferListItem();
	
}

?>
