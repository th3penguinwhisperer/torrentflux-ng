<?php

require_once("inc/plugins/uncompress/UncompressBaseClass.php");
require_once('inc/classes/singleton/Configuration.php');
require_once('inc/classes/Process.php');

class Unrar extends UncompressBaseClass
{

	function checkstatus()
	{
		if ( $this->proc->haslogfile() ) {
			$lines = file( $this->proc->getlogfilename() );
			foreach($lines as $chkline) {
				if (strpos($chkline, 'already exists. Overwrite it ?') !== FALSE){
					$this->proc->kill();
					echo 'File has already been extracted, please delete extracted file if re-extraction is necessary.';
					break 1;
				}
				if (strpos($chkline, 'Cannot find volume') !== FALSE){
					$this->proc->kill();
					echo 'File has a missing volume and can not been extracted.';
					break 1;
				}
				if (strpos($chkline, 'ERROR: Bad archive') !== FALSE){
					$this->proc->kill();
					echo 'File has a bad volume and cannot be extracted.';
					break 1;
				}
				if (strpos($chkline, 'CRC failed') !== FALSE){
					$this->proc->kill();
					echo 'File extraction has failed with a CRC error and was not been extracted.';
					break 1;
				}
				if (strpos($chkline, 'User break') !== FALSE){
					echo 'The uncompress process has been killed by a user.';
					$this->cleanup(); // not the $proc cleanup! that is called in this class cleanup method
					break 1;
				}
			}
			if ($this->proc->is_running()) {
				// None of the above apply so extracting is still running. Filter out the percentage
				$filecontent = implode("",$lines);
				preg_match_all('/[0-9]{1,2}\%/', $filecontent, $res );
				echo "Extract running: ".end(end($res));
			}
		}
		if ($this->proc->haslogfile()) {
			$lines = file($this->proc->getlogfilename());
			foreach($lines as $chkline) {
				if (strpos($chkline, 'All OK') !== FALSE){
					echo 'File has successfully been extracted!';
				}
			}
		}
	}

	function uncompress()
	{
		$cfg = Configuration::get_instance()->get_cfg();
		
		if ($this->proc->haslogfile()) {
			$this->checkstatus();
		} else {
			$passcmdpart = ( $this->password == "" ? "" : "-p".tfb_shellencode($this->password) );
			$Command = tfb_shellencode($cfg['rewrite_bin_unrar'])." x -o+ $passcmdpart ". tfb_shellencode($this->dir.$this->filename) . " " . tfb_shellencode($this->dir);
			$this->proc->execute($Command);
			echo 'Uncompressing started...<BR>PID is: ' . $this->proc->getpid() . '<BR>';
			usleep(250000); // wait for 0.25 seconds
			$this->checkstatus();
		}
	}

	function cleanup()
	{
		parent::cleanup();

		$cfg = Configuration::get_instance()->get_cfg();
		exec(tfb_shellencode($cfg['rewrite_bin_unrar']) . " lb " . tfb_shellencode($this->dir . $this->filename), $output );
		if ( sizeof($output) > 0) // we can parse file entries
			foreach ( $output as $file ) {
				if (file_exists($this->dir . $file)) {
					print("Deleting $this->dir$file\n");
					@unlink($this->dir . $file);
				}
			}
	}
}

?>
