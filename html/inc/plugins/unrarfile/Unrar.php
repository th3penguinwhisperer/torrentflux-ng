<?php

require_once("inc/plugins/unrarfile/UncompressBaseClass.php");
require_once('inc/classes/singleton/Configuration.php');

class Unrar extends UncompressBaseClass
{

	function checkstatus()
	{
		if (file_exists($this->dir.$this->filename.".".Unrar::$logfile)) {
			$lines = file($this->dir.$this->filename.".".Unrar::$logfile);
			foreach($lines as $chkline) {
				if (strpos($chkline, 'already exists. Overwrite it ?') !== FALSE){
					$this->kill($this->pid);
					echo 'File has already been extracted, please delete extracted file if re-extraction is necessary.';
					break 2;
				}
				if (strpos($chkline, 'Cannot find volume') !== FALSE){
					$this->kill($this->pid);
					echo 'File has a missing volume and can not been extracted.';
					break 2;
				}
				if (strpos($chkline, 'ERROR: Bad archive') !== FALSE){
					$this->kill($this->pid);
					echo 'File has a bad volume and cannot be extracted.';
					break 2;
				}
				if (strpos($chkline, 'CRC failed') !== FALSE){
					$this->kill($this->pid);
					echo 'File extraction has failed with a CRC error and was not been extracted.';
					break 2;
				}
				if (strpos($chkline, 'User break') !== FALSE){
					echo 'The uncompress process has been killed by a user.';
					$this->cleanup();
					break 1;
				}
			}
			if ($this->is_running($this->pid)) {
				// None of the above apply so extracting is still running. Filter out the percentage
				$filecontent = implode("",$lines);
				preg_match_all('/[0-9]{1,2}\%/', $filecontent, $res );
				echo "Extract running: ".end(end($res));
			}
		}
		if (file_exists($this->dir.$this->filename.".".Unrar::$logfile)) {
			$lines = file($this->dir.$this->filename.".".Unrar::$logfile);
			foreach($lines as $chkline) {
				if (strpos($chkline, 'All OK') !== FALSE){
					echo 'File has successfully been extracted!';
					@unlink($this->dir.$this->filename.".".Unrar::$logfile);
					@unlink($this->dir.$this->filename.Unrar::$pidfile);

					exit();
				}
			}
		}
	}

	function uncompress()
	{
		$cfg = Configuration::get_instance()->get_cfg();
		
		$pid = $this->getpid();
		if (file_exists($this->dir.$this->filename.".".Unrar::$logfile)) {
			print("Unrar action for this file has already been run or is still running: $this->dir$this->filename.".Unrar::$logfile);
			$this->checkstatus();
			//@unlink($filename.Unrar::$logfile);
		} else {
			$passcmdpart = ( $this->password == "" ? "" : "-p".tfb_shellencode($this->password) );
			$Command = tfb_shellencode($cfg['rewrite_bin_unrar'])." x -o+ $passcmdpart ". tfb_shellencode($this->dir.$this->filename) . " " . tfb_shellencode($this->dir);
			$pid = trim(shell_exec("nohup ".$Command." > " . tfb_shellencode($this->dir.$this->filename.".".Unrar::$logfile) . " 3>&1 & echo $!"));
			echo 'Uncompressing started...<BR>PID is: ' . $pid . '<BR>';
			$this->setpid($pid);
			usleep(250000); // wait for 0.25 seconds
			$this->checkstatus();
		}

		exit();
	}

	function cleanup()
	{
		parent::cleanup();
		$cfg = Configuration::get_instance()->get_cfg();
		exec(tfb_shellencode($cfg['rewrite_bin_unrar']) . " lb " . tfb_shellencode($this->dir . $this->filename), $output );
		if ( sizeof($output) > 0) // we can parse file entries
			foreach ( $output as $file ) {
				print("Deleting $this->dir$file\n");
				@unlink($this->dir . $file);
			}
	}
}

?>
