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
		$fulldir = $cfg['rewrite_path'].urldecode($dir);
		$filename = urldecode($filename);
		$fullname = tfb_shellencode($dir.$filename);

		if (!file_exists($fulldir . $filename)) { // TODO: create check if dir is ending with slash or not
			AuditAction($cfg["constants"]["error"], "Uncompress file that does not exist: $filename");
			exit();
		}

		if ( ends_with($filename, 'rar', false) )
			$inst = new Unrar($fulldir, $filename);

		if ( ends_with($filename, 'zip', false) )
			$inst = new Unzip($fulldir, $filename);

		if ( is_object($inst) ) {
			if (isset($_REQUEST['controlcleanup']))
				$inst->cleanup(false);
			elseif (isset($_REQUEST['fullcleanup']))
				$inst->cleanup();
			else {
				$inst->uncompress();
				print("<br><a href=\"javascript:loadpopup('Uncompress', 'dispatcher.php?plugin=unrarfile&action=passplugindata&subaction=filemanagement&controlcleanup=true&dir=" .urlencode($dir). "&filename=" .urlencode($filename). "','Loading...');\">Cleanup control files</a>");
				print("<br><a href=\"javascript:loadpopup('Uncompress', 'dispatcher.php?plugin=unrarfile&action=passplugindata&subaction=filemanagement&fullcleanup=true&dir=" .urlencode($dir). "&filename=" .urlencode($filename). "','Loading...');\">Cleanup extracted and control files</a>");
			}
		} else
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
