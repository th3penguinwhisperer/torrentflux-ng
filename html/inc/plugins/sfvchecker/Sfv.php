<?php

require_once("inc/plugins/uncompress/UncompressBaseClass.php");
require_once('inc/classes/singleton/Configuration.php');
require_once("inc/classes/Process.php");

class Sfv
{

	private $proc;
	
	function __construct($dir, $filename)
	{
		$this->dir = $dir;
		$this->filename = $filename;
		$this->proc = new Process( 
			Process::generatepidfilename("", $this->dir, $this->filename), 
			Process::generatelogfilename("", $this->dir, $this->filename) 
		);
	}
	
	function checkstatus()
	{
		if($this->proc->is_running()) {
			echo 'Still SFV checking...';
			$output = $this->proc->getlog();
			echo "<pre>".$output."</pre>";
		} else {
			// check log file for errors
			echo "SFV Check Finished<br>";
			$output = $this->proc->getlog();
			
			if( strpos($output, "Everything OK") !== FALSE ) {
				print("All files were ok!");
				return;
			}
			if( strpos($output, "Errors Occured") !== FALSE ) {
				print("SFV Check was not successful!");
				echo "<pre>".$output."</pre>";
				return;
			}
			print("Unkown condition<br>");
			echo "<pre>".$output."</pre>";
		}
	}

	function startsfvcheck()
	{
		$cfg = Configuration::get_instance()->get_cfg();

		if ($this->proc->haslogfile()) {
			$this->checkstatus();
		} else {
			$Command = tfb_shellencode($cfg['rewrite_bin_cksfv']).' -b -f ' . tfb_shellencode($this->dir.$this->filename) . ' -C ' . tfb_shellencode($this->dir);
			$this->proc->execute($Command);
			echo 'Checking SFV file...<BR>PID is: ' . $this->proc->getpid() . '<BR>';
			usleep(250000); // wait for 0.25 seconds
			$this->checkstatus();
		}
	}

	function cleanup()
	{
		if ($this->proc->is_running()) {
			$this->proc->kill();
			print("Killing process " . $this->proc->getpid() . "<br>");
		}
		print("Deleting control files<br>");
		$this->proc->cleanup();
	}
	
}

?>
