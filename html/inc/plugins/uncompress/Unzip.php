<?php

require_once("inc/plugins/uncompress/UncompressBaseClass.php");
require_once('inc/classes/singleton/Configuration.php');

class Unzip extends UncompressBaseClass
{

	function checkstatus()
	{
		if( $this->proc->is_running() )
			echo 'Unzipping file...';
		else {
			// check log file for errors
			echo "Unzipping finished\n";
			$output = $this->proc->getlog();
			echo "<pre>".$output."</pre>";
		}
	}

	function uncompress()
	{
		$cfg = Configuration::get_instance()->get_cfg();

		if ($this->proc->haslogfile()) {
			//$pid = $this->getpid();
			$this->checkstatus();
		} else {
			$Command = tfb_shellencode($cfg['rewrite_bin_unzip']).' -o ' . tfb_shellencode($this->dir.$this->filename) . ' -d ' . tfb_shellencode($this->dir);
			$this->proc->execute($Command);
			echo 'Uncompressing file...<BR>PID is: ' . $this->proc->getpid() . '<BR>';
			usleep(250000); // wait for 0.25 seconds
			$this->checkstatus();
		}
	}

	function cleanup($full = true)
	{
		parent::cleanup();
		if ($full) {
			$cfg = Configuration::get_instance()->get_cfg();
			exec(tfb_shellencode($cfg['rewrite_bin_unzip']) . " -Z -1 " . tfb_shellencode($this->dir . $this->filename), $output );
			if ( sizeof($output) > 0) // we can parse file entries
				foreach ( $output as $file ) {
					if( file_exists($this->dir.$file) ) {
						print("Deleting $this->dir$file\n");
						@unlink($this->dir . $file);
					}
				}
		}
	}
}

?>
