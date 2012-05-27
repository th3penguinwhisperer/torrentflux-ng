<?php

require_once("inc/plugins/uncompress/UncompressBaseClass.php");
require_once('inc/classes/singleton/Configuration.php');

class Sfv
{
	static protected $pidfile = ".pid";
	static protected $logfile = "error.log";

	protected $pid = -1;

	function getpid() {
		$file = $this->dir . $this->filename . Sfv::$pidfile;

		$pid = -1;
		if (file_exists($file)) {
			$line = file_get_contents($file);
			if (is_numeric($line))
				$pid = $line;
		}
		return $pid;
	}

	function setpid($pid) {
		$cfg = Configuration::get_instance()->get_cfg();
		$file = $this->dir . $this->filename . Sfv::$pidfile;

		$fh = fopen($file, 'w') or AuditAction($cfg["constants"]["error"], "Can't create uncompress pid file $file");
		fwrite($fh, $pid);
		fclose($fh);

		$this->pid = $this->getpid();
	}

	function __construct($dir, $filename)
	{
		$this->dir = $dir;
		$this->filename = $filename;
		$this->pid = $this->getpid();
	}
	
	function checkstatus()
	{
		if($this->is_running($this->getpid())) {
			echo 'Still SFV checking...';
			$output = file_get_contents($this->dir.$this->filename.".".Sfv::$logfile);
			echo "<pre>".$output."</pre>";
		} else {
			// check log file for errors
			echo "SFV Check Finished<br>";
			$output = file_get_contents($this->dir.$this->filename.".".Sfv::$logfile);
			
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

		if (file_exists($this->dir.$this->filename.".".Sfv::$logfile)) {
			$pid = $this->getpid();
			$this->checkstatus();
		} else {
			$Command = tfb_shellencode($cfg['rewrite_bin_cksfv']).' -b -f ' . tfb_shellencode($this->dir.$this->filename) . ' -C ' . tfb_shellencode($this->dir);
			$pid = trim(shell_exec("nohup ".$Command." > " . tfb_shellencode($this->dir.$this->filename.".".Sfv::$logfile) . " 2>&1 & echo $!"));
			echo 'Checking SFV file...<BR>PID is: ' . $pid . '<BR>';
			usleep(250000); // wait for 0.25 seconds
			$this->setpid($pid);
			$this->checkstatus();
		}
	}

	function cleanup()
	{
		if ($this->is_running($this->getpid())) {
			$this->kill($this->getpid());
			print("Killing cksfv process<br>");
		}
		print("Deleting control files<br>");
		@unlink($this->dir.$this->filename . "." . Sfv::$logfile);
		@unlink($this->dir.$this->filename . Sfv::$pidfile);
	}

	/**
	 * is_running
	 *
	 * @param $PID
	 * @return
	 */
	function is_running($PID){
		if(is_numeric($PID) && $PID > 0) {
			$ProcessState = exec("ps ".tfb_shellencode($PID) . "| grep -o $PID", $output);
			return ( implode($output) =="$PID" );
		} else
			return false;
	}

	/**
	 * kill
	 *
	 * @param $PID
	 * @return
	 */
	function kill($PID){
	    exec("kill -KILL ".tfb_shellencode($PID));
	    return true;
	}
}

?>
