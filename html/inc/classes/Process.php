<?php

require_once('inc/generalfunctions.php');
require_once('inc/classes/singleton/Configuration.php');

class Process
{
	static protected $pidfileext = ".pid";
	static protected $logfileext = ".error.log";
	static private $cfg;
	
	private $pid = -1;
	private $logfile = "";
	private $pidfile = "";
	private $cmd = "";
	
	static function generatelogfilename($piname, $dir, $filename)
	{
		if(starts_with($dir, "/"))
			return $dir . $filename . Process::$logfileext;
		else {
			$cfg = Configuration::get_instance()->get_cfg();
			return $cfg['rewrite_path'] . $dir . $filename . Process::$logfileext;
		}
	}
	
	static function generatepidfilename($piname, $dir, $filename)
	{
		if(starts_with($dir, "/"))
			return $dir . $filename . Process::$pidfileext;
		else {
			$cfg = Configuration::get_instance()->get_cfg();
			return $cfg['rewrite_path'] . $dir . $filename . Process::$pidfileext;
		}
	}
	
	function __construct($command, $pidfile, $logfile)
	{
		$this->cfg = Configuration::get_instance()->get_cfg();
		$this->cmd = $command;
		$this->pidfile = $pidfile;
		$this->logfile = $logfile;
	}
	
	function execute()
	{
		$shellcmd = "nohup ".$this->cmd." > " . tfb_shellencode($this->logfile) . " 2>&1 & echo $!";
		$pid = trim(shell_exec($shellcmd));
		AuditAction("RUNCMD", $this->cfg["constants"]["error"], "Command: ".$shellcmd);
		$this->setpid($pid);
	}
	
	function getlogfilename()
	{
		return $this->logfile;
	}
	
	function getlog()
	{
		return file_get_contents($this->logfile);
	}
	
	function getpidfilename()
	{
		return $this->pidfile;
	}
	
	function setpid($pid) {
		$fh = fopen($this->pidfile, 'w') or AuditAction("CREATEFILE", $this->cfg["constants"]["error"], "Can't create pid file $this->pidfile");
		fwrite($fh, $pid);
		fclose($fh);

		$this->pid = $this->getpid(); // I know we can set this directly however it guarantees that we were able to write the pid to the file
	}
	
	function getpid()
	{
		$pid = -1;
		if (file_exists($this->pidfile)) {
			$line = file_get_contents($this->pidfile);
			if (is_numeric($line))
				$pid = $line;
		}
		return $pid;
	}
	
	function cleanup()
	{
		if ($this->pidfile != "" && $this->logfile != "") {
			@unlink($this->pidfile);
			@unlink($this->logfile);
		}
	}
	
	/**
	 * is_running
	 *
	 * @param $PID
	 * @return
	 */
	function is_running(){
		$pid = $this->getpid(); // make sure we load it first
		if(is_numeric($pid) && $pid > 0) {
			$ProcessState = exec("ps ".tfb_shellencode($pid) . "| grep -o $pid", $output);
			return ( implode($output) =="$pid" );
		} else
			return false;
	}

	/**
	 * kill
	 *
	 * @param $PID
	 * @return
	 */
	function kill(){
	    exec("kill -KILL ".tfb_shellencode($this->pid));
	    return true;
	}
}

?>