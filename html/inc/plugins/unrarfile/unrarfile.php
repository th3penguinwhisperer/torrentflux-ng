<?php

require_once('inc/plugins/PluginInterface.php');
require_once('inc/plugins/unrarfile/Unrar.php');
require_once('inc/plugins/unrarfile/Unzip.php');

class UnrarFile implements PluginInterface
{

	function __construct()
	{
		;
	}

	function fileaction($dir, $filename)
	{
		//convert and set variables
		$cfg = Configuration::get_instance()->get_cfg();
		$dir = $cfg['rewrite_path'].urldecode($dir);
		$filename = urldecode($filename);
		$fullname = tfb_shellencode($dir.$filename);

		if (!file_exists($dir . $filename)) { // TODO: create check if dir is ending with slash or not
			AuditAction($cfg["constants"]["error"], "Uncompress file that does not exist: $filename");
			exit();
		}

		if ( ends_with($filename, 'rar', false) )
			$inst = new Unrar($dir, $filename);

		if ( ends_with($filename, 'zip', false) )
			$inst = new Unzip($dir, $filename);

		if ( is_object($inst) )
			$inst->uncompress();
		else
			AuditAction($cfg["constants"]["error"], "Uncompression for this file is not supported: $filename");
	
	}
	
	// TODO: remove this function from File management plugin specific interface?
	function show()
	{
		//print( $this->getDiskspaceUi() );
	}

	function get()
	{
		return $this->getDiskspaceUi();
	}

	function getConfiguration()
	{
		;
	}
	
	function setConfiguration($configArray)
	{
		;
	}

}

?>
