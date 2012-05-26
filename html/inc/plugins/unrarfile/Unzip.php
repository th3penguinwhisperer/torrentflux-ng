<?php

require_once("inc/plugins/unrarfile/UncompressBaseClass.php");
require_once('inc/classes/singleton/Configuration.php');

class Unzip extends UncompressBaseClass
{

	function checkstatus()
	{
		if($this->is_running($this->getpid()))
			echo 'Unzipping file...';
		else {
			// check log file for errors
			echo "Unzipping finished\n";
			$output = file_get_contents($this->dir.$this->filename.".".Unzip::$logfile);
			echo $output;
			
			//@unlink($dir.$filename.".". Unzip::$pidfile);
			//@unlink($dir.$filename.".". Unzip::$logfile);
		}
	}

	function uncompress()
	{
		$cfg = Configuration::get_instance()->get_cfg();

		if (file_exists($this->dir.$this->filename.".".parent::$logfile)) {
			$pid = $this->getpid();
			$this->checkstatus();
			//@unlink($filename.Unzip::$logfile);
		} else {
			$Command = tfb_shellencode($cfg['rewrite_bin_unzip']).' -o ' . tfb_shellencode($this->dir.$this->filename) . ' -d ' . tfb_shellencode($this->dir);
			$pid = trim(shell_exec("nohup ".$Command." > " . tfb_shellencode($this->dir.$this->filename.".".Unzip::$logfile) . " 2>&1 & echo $!"));
			echo 'Uncompressing file...<BR>PID is: ' . $pid . '<BR>';
			usleep(250000); // wait for 0.25 seconds
			$this->setpid($pid);
			$this->checkstatus();

			exit();
		}
	}

	function cleanup()
	{
		parent->cleanup();
	}
}

?>
