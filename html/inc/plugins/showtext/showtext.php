<?php

require_once('inc/plugins/PluginInterface.php');
require_once('inc/generalfunctions.php');

class ShowText implements PluginInterface
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
		if($filename == "")
			return false;

		if ( ends_with($filename, ".txt") )
			return true;
		if ( ends_with($filename, ".nfo") )
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
		//convert and set variables
		$cfg = Configuration::get_instance()->get_cfg();
		$fulldir = $cfg['rewrite_path'].urldecode($this->dir);
		$filename = urldecode($this->filename);
		$fullname = tfb_shellencode($this->dir.$this->filename);

		if (!file_exists($fulldir . $filename)) { // TODO: create check if dir is ending with slash or not
			AuditAction($cfg["constants"]["error"], "Deleting item that does not exist: $filename");
			exit();
		} else {
			if ($filename == "") {
				AuditAction($cfg["constants"]["error"], "The filename to show is empty");
				exit();
			}
			if (filesize($fulldir . $filename)>$cfg['rewrite_text_maxsize']) {
				AuditAction($cfg["constants"]["error"], "The filename to show is to big to show in your browser");
				exit();
			}
			print("File $filename<br>");
			$contents = file_get_contents($fulldir . $filename);
			print("<pre>".$contents."</pre>");
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

}

?>
