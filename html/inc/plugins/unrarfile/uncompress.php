<?php

/* $Id$ */

/*******************************************************************************

 LICENSE

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License (GPL)
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.

 To read the license please visit http://www.gnu.org/copyleft/gpl.html

*******************************************************************************/

// dummy
$_SESSION = array('cache' => false);

/******************************************************************************/

// core functions
//require_once('inc/functions/functions.core.php');
require_once('inc/generalfunctions.php');
require_once('inc/classes/singleton/Configuration.php');

/**
 * @author    R.D. Damron
 * @name      rar/zip uncompression
 * @usage	  ./uncompress.php "pathtofile" "extractdir" "typeofcompression" "uncompressor-bin" "password"
 */

$logfile = 'error.log';
$pidfile = 'pid';
$unrarbin = "/usr/local/bin/unrar";

function getpid($dir, $filename) {
	$pidfile = ".pid";
	$file = "$dir$filename$pidfile";
	$pid = -1;

	if (file_exists($file)) {
		$line = file_get_contents($file);
		if (is_numeric($line))
			$pid = $line;
	}
	return $pid;
}

function setpid($dir, $filename, $pid) {
	$pidfile = ".pid";
	$file = "$dir$filename$pidfile";
	$fh = fopen($file, 'w') or AuditAction($cfg["constants"]["error"], "Can't create uncompress pid file $file");
	fwrite($fh, $pid);
	fclose($fh);
}

function checkunzipstatus($dir, $filename, $pid) {
	$logfile = 'error.log';
	$pidfile = ".pid";

	if(is_running($pid))
		echo 'Unzipping file...';
	else {
		// check log file for errors
		echo "Unzipping finished\n";
		$output = file_get_contents($dir.$filename.".".$logfile);
		echo $output;
		
		//@unlink($dir.$filename.".". $pidfile);
		//@unlink($dir.$filename.".". $logfile);
	}
}

function checkunrarstatus($dir, $filename, $pid) {
	$logfile = 'error.log';

	$pid = getpid($dir, $filename);
	if (file_exists($dir.$filename.".".$logfile)) {
		$lines = file($dir.$filename.".".$logfile);
		foreach($lines as $chkline) {
			if (strpos($chkline, 'already exists. Overwrite it ?') !== FALSE){
				kill($pid);
				echo 'File has already been extracted, please delete extracted file if re-extraction is necessary.';
				break 2;
			}
			if (strpos($chkline, 'Cannot find volume') !== FALSE){
				kill($pid);
				echo 'File has a missing volume and can not been extracted.';
				break 2;
			}
			if (strpos($chkline, 'ERROR: Bad archive') !== FALSE){
				kill($pid);
				echo 'File has a bad volume and cannot be extracted.';
				break 2;
			}
			if (strpos($chkline, 'CRC failed') !== FALSE){
				kill($pid);
				echo 'File extraction has failed with a CRC error and was not been extracted.';
				break 2;
			}
		}
		if (is_running($pid)) {
			// None of the above apply so extracting is still running. Filter out the percentage
			$filecontent = implode("",$lines);
			preg_match_all('/[0-9]{1,2}\%/', $filecontent, $res );
			echo "Extract running: ".end(end($res));
		}
	}
	if (file_exists($dir.$filename.".".$logfile)) {
		$lines = file($dir.$filename.".".$logfile);
		foreach($lines as $chkline) {
			if (strpos($chkline, 'All OK') !== FALSE){
				echo 'File has successfully been extracted!';
				@unlink($dir.$filename.".".$logfile);
				// exit
				exit();
			}
		}
	}

}

// unrar file
function unrar($dir, $filename, $password) {
	$cfg = Configuration::get_instance()->get_cfg();
	$logfile = 'error.log';
	
	$pid = getpid($dir, $filename);
	if (file_exists($dir.$filename.".".$logfile)) {
		print("Unrar action for this file has already been run or is still running: $dir$filename.$logfile");
		checkunrarstatus($dir, $filename, $pid); //TODO write function that detects or retrieves the pid
		//@unlink($filename.$logfile);
	} else {
		$passcmdpart = ( $password == "" ? "" : "-p".tfb_shellencode($password) );
		$Command = tfb_shellencode($cfg['bin_unrar'])." x -o+ $passcmdpart ". tfb_shellencode($dir.$filename) . " " . tfb_shellencode($dir);
		$pid = trim(shell_exec("nohup ".$Command." > " . tfb_shellencode($dir.$filename.".".$logfile) . " 3>&1 & echo $!"));
		echo 'Uncompressing file...<BR>PID is: ' . $pid . '<BR>';
		setpid($dir, $filename, $pid);
		usleep(250000); // wait for 0.25 seconds
		checkunrarstatus($dir, $filename, $pid);
	}

	// exit
	exit();
}

// unzip
function unzip($dir, $filename) {
	$cfg = Configuration::get_instance()->get_cfg();
	$logfile = "error.log";

	if (file_exists($dir.$filename.".".$logfile)) {
		$pid = getpid($dir, $filename);
		checkunzipstatus($dir, $filename, $pid);
		//@unlink($filename.$logfile);
	} else {
		$Command = tfb_shellencode($cfg['rewrite_bin_unzip']).' -o ' . tfb_shellencode($dir.$filename) . ' -d ' . tfb_shellencode($dir);
print "$Command\n";
		$pid = trim(shell_exec("nohup ".$Command." > " . tfb_shellencode($dir.$filename.".".$logfile) . " 2>&1 & echo $!"));
		echo 'Uncompressing file...<BR>PID is: ' . $pid . '<BR>';
		usleep(250000); // wait for 0.25 seconds
		setpid($dir, $filename, $pid);
		checkunzipstatus($dir, $filename, $pid);

		// exit
		exit();
	}
}


function uncompress($dir, $filename, $password) {
	//convert and set variables
	$cfg = Configuration::get_instance()->get_cfg();
	$dir = $cfg['rewrite_path'].urldecode($dir);
	$filename = urldecode($filename);
	$fullname = tfb_shellencode($dir.$filename);

	if (!file_exists($dir . $filename)) { // TODO: create check if dir is ending with slash or not
		print("No such file!\n");
		exit();
	}

	if ( ends_with($filename, 'rar', false) )
		unrar($dir, $filename, $password);

	if ( ends_with($filename, 'zip', false) )
		unzip($dir, $filename);
}

/**
 * is_running
 *
 * @param $PID
 * @return
 */
function is_running($PID){
	if(is_numeric($PID)) {
		$ProcessState = exec("ps ".tfb_shellencode($PID));
		return (count($ProcessState) >= 2);
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

/**
 * del
 *
 * @param $file
 * @return
 */
function del($file){
    exec("rm -rf ".tfb_shellencode($file));
    return true;
}

?>
