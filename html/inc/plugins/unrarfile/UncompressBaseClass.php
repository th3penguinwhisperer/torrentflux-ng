<?php

require_once('inc/generalfunctions.php');

class UncompressBaseClass
{
	static protected $pidfile = ".pid";
	static protected $logfile = "error.log";

	protected $dir;
	protected $filename;
	protected $pid = -1;
	protected $password = "";

	function __construct($dir, $filename, $password="")
	{
		$this->dir = $dir;
		$this->filename = $filename;
		$this->password = $password;
		$this->pid = $this->getpid();
	}

	function checkstatus() { echo "Not supported"; }

	function uncompress() { echo "Not supported"; }

	function cleanup() { 
		@unlink($this->dir.$this->filename . "." . UncompressBaseClass::$logfile);
		@unlink($this->dir.$this->filename . UncompressBaseClass::$pidfile);
	 }

	function getpid() {
		$file = $this->dir . $this->filename . UncompressBaseClass::$pidfile;

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
		$file = $this->dir . $this->filename . UncompressBaseClass::$pidfile;

		$fh = fopen($file, 'w') or AuditAction($cfg["constants"]["error"], "Can't create uncompress pid file $file");
		fwrite($fh, $pid);
		fclose($fh);

		$this->pid = $this->getpid();
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
			var_dump($output);
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
