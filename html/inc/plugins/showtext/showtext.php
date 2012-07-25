<?php

require_once('inc/plugins/FilePluginBase.php');
require_once('inc/generalfunctions.php');

class ShowText extends FilePluginBase
{
	function __construct($dir, $filename)
	{
		parent::__construct($dir, $filename);
	}

	static function isvalidaction($dir, $filename)
	{
		if($filename == "")
			return false;

		if ( ends_with($filename, ".txt") )
			return true;
		if ( ends_with($filename, ".nfo") )
			return true;
		if ( ends_with($filename, ".log") )
			return true;

		return false;
	}

	static function getaction($dir, $filename)
	{
		$cfg = Configuration::get_instance()->get_cfg();
		return "<a href=\"javascript:loadpopup('Show Text', 'dispatcher.php?plugin=showtext&amp;action=passplugindata&amp;subaction=filemanagement&amp;dir=".urlencode($dir)."&amp;filename=".urlencode($filename)."', 'Loading...');centerPopup();loadPopup();\"><img src=\"themes/".$cfg['theme']."/images/dir/nfo.png\" /></a>";
	}

	function fileaction()
	{
		if (!file_exists($this->fullfilename)) { // TODO: create check if dir is ending with slash or not
			AuditAction("SHOWTEXT", parent::$cfg["constants"]["error"], "Deleting item that does not exist: $this->filename");
			exit();
		} else {
			if ($this->filename == "") {
				AuditAction("SHOWTEXT", parent::$cfg["constants"]["error"], "The filename to show is empty");
				exit();
			}
			if (filesize($this->fullfilename)>parent::$cfg['rewrite_text_maxsize']) {
				print("The filename is too big to show in your browser. You might want to ask your administrator to higher the maximum file size limit for text files");
				AuditAction("SHOWTEXT", parent::$cfg["constants"]["error"], "The filename to show is too big to show in your browser");
				exit();
			}
			print("File $this->filename<br>");
			$contents = file_get_contents($this->fullfilename);
			print("<pre>".$contents."</pre>");
		}
	}
	
}

?>
