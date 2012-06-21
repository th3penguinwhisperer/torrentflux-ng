<?php

require_once('inc/plugins/PluginInterface.php');

class DeleteItem implements PluginInterface
{
	private $cfg;
	private $filename;
	private $dir;
	private $fulldir;
	private $fullfilename;

	function __construct($dir, $filename)
	{
		// init configuration singleton
		$this->cfg = Configuration::get_instance()->get_cfg();

		// Decode and set basic variables
		$this->filename = urldecode($filename);
		$this->dir = urldecode($dir);

		// generate derived variables
		$this->fulldir = $this->cfg['rewrite_path'].urldecode($dir);
		$this->fullfilename = $this->fulldir.$this->filename;
	}

	static function isvalidaction($dir, $filename)
	{
		$cfg = Configuration::get_instance()->get_cfg();

		if(is_dir($cfg['rewrite_path']. $dir . $filename) && is_dir($cfg['rewrite_path'].$dir))
			return true;

		if ( is_file($cfg['rewrite_path'] . $dir . $filename) ) {
			return true;
		}

		return false;
	}

	static function getaction($dir, $filename)
	{
		$cfg = Configuration::get_instance()->get_cfg();
		return "<a href=\"javascript:loadpopup('Delete Item', 'dispatcher.php?plugin=deleteitem&amp;action=passplugindata&amp;subaction=filemanagement&amp;dir=".urlencode($dir)."&amp;filename=".urlencode($filename)."', 'Loading...');centerPopup();loadPopup();\"><img src=\"themes/".$cfg['theme']."/images/dir/delete_on.png\" /></a>";
	}

	function fileaction()
	{
		//convert and set variables
		$cfg = Configuration::get_instance()->get_cfg();
		$fulldir = $cfg['rewrite_path'].urldecode($this->dir);
		$filename = urldecode($this->filename);
		$fullname = tfb_shellencode($this->dir.$this->filename);

		if ( $this->dir == "" or $this->dir == ".." or $this->dir == "." or !is_dir($this->fulldir) ) {
			AuditAction("DELETE", $cfg["constants"]["error"], "Deleting item: dir argument does not exist, is not allowed or invalid: $dir $filename");
			exit();
		}
		if ( $filename == ".." or $filename == "." or $filename == "" ) {
			AuditAction("DELETE", $cfg["constants"]["error"], "Deleting item: file argument does not exist, is not allowed or invalid: $dir $filename");
			exit();
		}

		if ( is_file($fulldir.$filename) ) { // deleting file
			print("Deleting file $filename");
			AuditAction("DELETE", $cfg["constants"]["info"], "Deleting file: $this->dir $this->filename");
			@unlink($fulldir . $filename);
		} elseif ( is_dir($fulldir.$filename) ) { // deleting dir 
			print("Deleting directory $filename");
			AuditAction("DELETE", $cfg["constants"]["info"], "Deleting directory: $dir $filename");
			$this->unlinkRecursive($fulldir . $filename, true); // !!! Be VERY careful with this! You need the filename variable as it is filled with the directory to delete
		} else {
			AuditAction("DELETE", $cfg["constants"]["error"], "Deleting item: could not handle these arguments: $dir $filename");
			exit();
		}
	
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

	/**
	 * Recursively delete a directory
	 *
	 * @param string $dir Directory name
	 * @param boolean $deleteRootToo Delete specified top-level directory as well
	 */
	function unlinkRecursive($dir, $deleteRootToo = false)
	{
		if(!$dh = @opendir($dir))
		{
			return;
		}
		while (false !== ($obj = readdir($dh)))
		{
			if($obj == '.' || $obj == '..')
			{
				continue;
			}
			
			if (!@unlink($dir . '/' . $obj))
			{
			    $this->unlinkRecursive($dir.'/'.$obj, true);
			}
		}
		
		closedir($dh);
		
		if ($deleteRootToo)
		{
			@rmdir($dir);
		}
		
		return;
	} 

}

?>
