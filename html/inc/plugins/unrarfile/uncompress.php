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
	$myfile = "$dir$filename$pidfile";
	$pid = -1;

	if (file_exists($myfile)) {
		$line = file_get_contents($myfile);
		if (is_numeric($line))
			$pid = $line;
	}
	return $pid;
}

function setpid($dir, $filename, $pid) {
	$pidfile = ".pid";
	$myFile = "$dir$filename$pidfile";
	echo "Set PID $pid to pidfile $myFile\n";
	$fh = fopen($myFile, 'w') or die("can't open file");
	fwrite($fh, $pid);
	fclose($fh);
}

function checkunrarstatus($dir, $filename, $pid) {
	$unrarbin = "/usr/local/bin/unrar"; // TODO: set unrar  executable path from database settings
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
	$unrarbin = "/usr/local/bin/unrar"; // TODO: set unrar  executable path from database settings
	$logfile = 'error.log';
	
	$pid = getpid($dir, $filename);
	if (file_exists($dir.$filename.".".$logfile)) {
		print("Unrar action for this file has already been run or is still running: $dir$filename.$logfile");
		checkunrarstatus($dir, $filename, $pid); //TODO write function that detects or retrieves the pid
		//@unlink($filename.$logfile);
	} else {
		$passcmdpart = ( $password == "" ? "" : "-p".tfb_shellencode($password) );
		$Command = tfb_shellencode($unrarbin)." x -o+ $passcmdpart ". tfb_shellencode($dir.$filename) . " " . tfb_shellencode($dir);
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
	if (file_exists($arg2.$logfile))
		@unlink($arg2.$logfile);
    $Command = tfb_shellencode($arg4).' -o ' . tfb_shellencode($arg1) . ' -d ' . tfb_shellencode($arg2);
	$unzippid = trim(shell_exec("nohup ".$Command." > " . tfb_shellencode($arg2.$logfile) . " 2>&1 & echo $!"));
	echo 'Uncompressing file...<BR>PID is: ' . $unzippid . '<BR>';
	usleep(250000); // wait for 0.25 seconds
	while (is_running($unzippid)) {
		usleep(250000); // wait for 0.25 seconds
		/* occupy time to cause popup window load bar to load in conjunction with unzip progress */
	}
	// exit
	exit();
}


function uncompress($dir, $filename, $password) {
	//convert and set variables
	$dir = "/usr/home/torrentflux/git/".urldecode($dir);
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
    $ProcessState = exec("ps ".tfb_shellencode($PID) . "|grep -v grep");
    return (count($ProcessState) == 1);
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
