<?php

abstract class FilePluginBase
{
	protected $cfg;
	protected $filename;
	protected $dir;
	protected $fulldir;
	protected $fullfilename;

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

}

interface FilePluginInterface
{
	static function isvalidaction($dir, $filename);

	static function getaction($dir, $filename);

	function fileaction();
}

?>
