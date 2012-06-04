<?php

require_once('inc/generalfunctions.php');

class UncompressBaseClass
{
	protected $proc;
	protected $dir;
	protected $filename;
	protected $password = "";

	function __construct($dir, $filename, $password="")
	{
		$this->dir = $dir;
		$this->filename = $filename;
		$this->password = $password;
		$this->proc = new Process( 
			Process::generatepidfilename("", $dir, $filename), 
			Process::generatelogfilename("", $dir, $filename) 
		);
	}

	function checkstatus() { echo "Not supported"; }

	function uncompress() { echo "Not supported"; }

	function cleanup() { 
		$this->proc->cleanup();
	}

}

?>
