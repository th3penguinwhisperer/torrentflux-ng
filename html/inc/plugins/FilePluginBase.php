<?php

abstract class FilePluginBase implements FilePluginInterface
{
	static protected $cfg;
	protected $filename;
	protected $dir;
	protected $fulldir;
	protected $fullfilename;

	function __construct($dir, $filename)
	{
		// init configuration singleton
		static::$cfg = Configuration::get_instance()->get_cfg();

		// Decode and set basic variables
		$this->filename = urldecode($filename);
		$this->dir = urldecode($dir);

		// generate derived variables
		$this->fulldir = static::$cfg['rewrite_path'].urldecode($dir);
		$this->fullfilename = $this->fulldir.$this->filename;
	}

	static function getConfiguration() { print("<i>No configuration options for this plugin</i>"); }
	static function setConfiguration($configArray) { ; }
}

interface FilePluginInterface
{
	static function isvalidaction($dir, $filename);

	static function getaction($dir, $filename);

	function fileaction();

	static function getConfiguration();
	static function setConfiguration($configArray);
}

?>
