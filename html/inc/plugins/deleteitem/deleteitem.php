<?php

require_once('inc/plugins/FilePluginBase.php');

class DeleteItem extends FilePluginBase
{
	function __construct($dir, $filename)
	{
		parent::__construct($dir, $filename);
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
		if ( $this->dir == "" or $this->dir == ".." or $this->dir == "." or !is_dir($this->fulldir) ) {
			AuditAction("DELETE", $this->cfg["constants"]["error"], "Deleting item: dir argument does not exist, is not allowed or invalid: $this->dir $this->filename");
			exit();
		}
		if ( $this->filename == ".." or $this->filename == "." or $this->filename == "" ) {
			AuditAction("DELETE", $this->cfg["constants"]["error"], "Deleting item: file argument does not exist, is not allowed or invalid: $this->dir $this->filename");
			exit();
		}

		if ( is_file($this->fullfilename) ) { // deleting file
			print("Deleting file $this->filename");
			AuditAction("DELETE", $this->cfg["constants"]["info"], "Deleting file: $this->dir $this->filename");
			@unlink($this->fullfilename);
		} elseif ( is_dir($this->fullfilename) ) { // deleting dir 
			print("Deleting directory $this->filename");
			AuditAction("DELETE", $this->cfg["constants"]["info"], "Deleting directory: $ithis->dir $this->filename");
			$this->unlinkRecursive($this->fullfilename, true); // !!! Be VERY careful with this! You need the filename variable as it is filled with the directory to delete
		} else {
			AuditAction("DELETE", $this->cfg["constants"]["error"], "Deleting item: could not handle these arguments: $this->dir $this->filename");
			exit();
		}
	
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
