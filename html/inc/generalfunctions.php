<?php


function tfb_getRequestVar($varName, $return = '') {
        if(array_key_exists($varName, $_REQUEST)){
                // If magic quoting on, strip magic quotes:
                /** 
                * TODO:
                * Codebase needs auditing to remove any unneeded stripslashes
                * calls before uncommenting this.  Also using this really means
                * checking any addslashes() calls to see if they're really needed
                * when magic quotes is on.
                if(ini_get('magic_quotes_gpc')){
                        tfb_strip_quotes($_REQUEST[$varName]);
                }
                */
                $return = htmlentities(trim($_REQUEST[$varName]), ENT_QUOTES);
                /*  
                disabled, need to fix deadeye's implementation
                if ($varName == 'transfer' && isHash($return)) {
                        $name = getTransferFromHash($return);
                        if (!empty($name))
                                return $name;
                        else
                                return $return;
                }
                */
        }   
        return $return;
}

/**
 * Returns a string in format of TB, GB, MB, or kB depending on the size
 *
 * @param $inBytes
 * @return string
 */
function formatBytesTokBMBGBTB($inBytes) {
	if(!is_numeric($inBytes)) return "";
	if ($inBytes > 1099511627776)
		return round($inBytes / 1099511627776, 2) . " TB";
	elseif ($inBytes > 1073741824)
		return round($inBytes / 1073741824, 2) . " GB";
	elseif ($inBytes > 1048576)
		return round($inBytes / 1048576, 1) . " MB";
	elseif ($inBytes > 1024)
		return round($inBytes / 1024, 1) . " kB";
	else
		return $inBytes . " B";
}

function getDownloadPath($shared = false)
{
	require_once('inc/singleton/Configuration.php');
	$cfg = Configuration::get_instance()->get_cfg();
	
	if ($shared) {
		return $cfg['path'] . 'incoming';
	} else {
		return $cfg['download_path'];
	}
}

/**
 * clean file-name, validate extension and make it lower-case
 *
 * @param $inName
 * @return string or false
 */
function tfb_cleanFileName($inName) {
		$cfg = Configuration::get_instance()->get_cfg();
		
        $outName = $inName;
        //$outName = tfb_clean_accents($inName); // TODO: import this function; not done yet because editor can't save strange characters
        $outName = preg_replace("/[^0-9a-zA-Z\.\-]+/",'_', $outName);
        $outName = str_replace("_-_", "-", $outName);
        $stringLength = strlen($outName);
        foreach ($cfg['file_types_array'] as $ftype) {
                $extLength = strlen($ftype);
                $extIndex = 0 - $extLength;
                if (($stringLength > $extLength) && (strtolower(substr($outName, $extIndex)) === ($ftype)))
                        return substr($outName, 0, $extIndex).$ftype;
        }   
        return false;
}

/**
 * processUpload
 */
function handleFileUpload($files, $client, $path, $paused) {
	// check if files exist
	if (empty($files)) {
		// log
		AuditAction($cfg["constants"]["error"], "no file in file-upload"); //TODO enable logging
		print("No file in file-upload");
		// return
		return;
	}
	// action-id
	//$actionId = tfb_getRequestVar('aid'); // TODO: implement "upload" and "uploadAndStart"
	// file upload
	$uploadMessages = array();
	// stack
	$tStack = array();
	// process upload
	while (count($files) > 0) {
		$upload = array_shift($files);
		if (is_array($upload['size'])) {
			foreach ($upload['size'] as $id => $size) {
				if ($size > 0) {
					_dispatcher_processUpload(
						$upload['name'][$id], $upload['tmp_name'][$id], $size,
						$actionId, $uploadMessages, $tStack, $client, $path, $paused);
				}
			}
		} else {
			if ($upload['size'] > 0) {
				_dispatcher_processUpload(
					$upload['name'], $upload['tmp_name'], $upload['size'],
					$actionId, $uploadMessages, $tStack, $client, $path, $paused);
			}
		}
	}
	// instant action ?
	// TODO: implement this so transfers can be started right away after uploading
	/*if (($actionId > 1) && (!empty($tStack))) {
		foreach ($tStack as $transfer) {
			$ch = ClientHandler::getInstance(getTransferClient($transfer));
			switch ($actionId) {
				case 3:
					$ch->start($transfer, false, true);
					break;
				case 2:
					$ch->start($transfer, false, false);
					break;
			}
			if (count($ch->messages) > 0)
       			$uploadMessages = array_merge($uploadMessages, $ch->messages);
		}
	}*/
	// messages
	if (count($uploadMessages) > 0) {
		//@error("There were Problems", "", "", $uploadMessages);
		print_r($uploadMessages); // TODO get this properly done
	}
}

/**
 * getTransferClient, returns a string that holds the name of the client that handles this transfer
 * @param $transfer
 * @return string
 */
function getTransferClient($transfer) {
	return "transmission-daemon"; // TODO: implement this
}

/**
 * _dispatcher_processUpload
 *
 * @param $name
 * @param $tmp_name
 * @param $size
 * @param $actionId
 * @param &$uploadMessages
 * @param &$tStack
 * @return bool
 */
function _dispatcher_processUpload($name, $tmp_name, $size, $actionId, &$uploadMessages, &$tStack, $client, $path, $paused) {
	$cfg = Configuration::get_instance()->get_cfg();
	
	$filename = tfb_cleanFileName(stripslashes($name));
	if ($filename === false) {
		// invalid file
		array_push($uploadMessages, "The type of file ".stripslashes($name)." is not allowed.");
		array_push($uploadMessages, "\nvalid file-extensions: ");
		array_push($uploadMessages, $cfg["file_types_label"]);
		return false;
	} else {
		// file is valid
		/*if (substr($filename, -5) == ".wget") {
			// is enabled ?
			if ($cfg["enable_wget"] == 0) {
				AuditAction($cfg["constants"]["error"], "ILLEGAL ACCESS: ".$cfg["user"]." tried to upload wget-file ".$filename);
				array_push($uploadMessages, "wget is disabled  : ".$filename);
				return false;
			} else if ($cfg["enable_wget"] == 1) {
				if (!$cfg['isAdmin']) {
					//AuditAction($cfg["constants"]["error"], "ILLEGAL ACCESS: ".$cfg["user"]." tried to upload wget-file ".$filename);
					array_push($uploadMessages, "wget is disabled for users : ".$filename);
					return false;
				}
			}
		} else if (substr($filename, -4) == ".nzb") {
			// is enabled ?
			if ($cfg["enable_nzbperl"] == 0) {
				//AuditAction($cfg["constants"]["error"], "ILLEGAL ACCESS: ".$cfg["user"]." tried to upload nzb-file ".$filename);
				array_push($uploadMessages, "nzbperl is disabled  : ".$filename);
				return false;
			} else if ($cfg["enable_nzbperl"] == 1) {
				if (!$cfg['isAdmin']) {
					//AuditAction($cfg["constants"]["error"], "ILLEGAL ACCESS: ".$cfg["user"]." tried to upload nzb-file ".$filename);
					array_push($uploadMessages, "nzbperl is disabled for users : ".$filename);
					return false;
				}
			}
		}*/
		if ($size <= $cfg["upload_limit"] && $size > 0) {
			//FILE IS BEING UPLOADED
			if (@is_file($cfg["transfer_file_path"].$filename)) {
				// Error
				array_push($uploadMessages, "the file ".$filename." already exists on the server.");
				return false;
			} else {
				$fullfilename = $cfg["transfer_file_path"].$filename;
				if (@move_uploaded_file($tmp_name, $fullfilename)) {
					@chmod($fullfilename, 0644);
					//AuditAction($cfg["constants"]["file_upload"], $filename);

					//$client = ClientHandler::getInstance();
					$hash = $client->fileUploaded($fullfilename, $path);
					
					//if ( $actionId > 1 ) {
						//startTransmissionTransfer( $hash );
						//array_push($tStack,$filename);
					//}
					//return true;
					// instant action ?
					//if ($actionId > 1)
					//	array_push($tStack,$filename);
					// return
					
					if (!$paused)
						$client->start($hash);

					return true;
				} else {
					array_push($uploadMessages, "File not uploaded, file could not be found or could not be moved: ".$cfg["transfer_file_path"].$filename);
					return false;
			  	}
			}
		} else {
			array_push($uploadMessages, "File not uploaded, file size limit is ".$cfg["upload_limit"].". file has ".$size);
			return false;
		}
	}
}

/**
 * checks a dir. recursive process to emulate "mkdir -p" if dir not present
 *
 * @param $dir the name of the dir
 * @param $mode the mode of the dir if created. default is 0755
 * @return boolean if dir exists/could be created
 */
function checkDirectory($dir, $mode = 0755, $depth = 0) {
	if ($depth > 32)
		return false;
	if ((@is_dir($dir) && @is_writable($dir)) || @mkdir($dir, $mode))
		return true;
	if ($dir == '/')
		return false;
	if (!@checkDirectory(dirname($dir), $mode, ++$depth))
		return false;
	return @mkdir($dir, $mode);
}

function getClientSelection()
{
	$clients = array("transmission-daemon");
	$clientNames = array("Transmission-daemon");
	$clienthtmlcode = "";
	foreach ( $clientNames as $clientName ) {
		$clienthtmlcode .= "\t\t<option value=" . array_shift($clients) . ">".$clientName."</option>\n";
	}

	print("
	<select name=client id=client>
$clienthtmlcode	</select>");	
}

function getActionSelection()
{
	$actions = array("Add");
	array_push($actions, "Add+Start");
	$actionsnames = array("add");
	array_push($actionsnames, "addstart");
	$actionhtmlcode = "";
	foreach ( $actions as $action ) {
		$actionhtmlcode .= "\t<option value=" . array_shift($actionsnames) . ">" . $action . "</option>\n";
	}
	
	print("		<select name=subaction id=subaction>
$actionhtmlcode
		</select>");
}

/**
 * check if path is valid
 *
 * @param $path
 * @param $ext
 * @return boolean
 */
function tfb_isValidPath($path, $ext = "") {
        if (preg_match("/\\\/", $path)) return false;
        if (preg_match("/\.\.\//", $path)) return false;
        if ($ext != "") {
                $extLength = strlen($ext);
                if (strlen($path) < $extLength) return false;
                if ((strtolower(substr($path, -($extLength)))) !== strtolower($ext)) return false;
        }   
        return true;
}

/**
 * Audit Action
 *
 * @param $action
 * @param $file
 */
function AuditAction($action, $level, $message, $file = "", $user = "") {
	require_once('inc/singleton/Configuration.php');
	require_once('inc/singleton/db.php');
	$cfg = Configuration::get_instance()->get_cfg();
	$db = DB::get_db()->get_handle();
	
	if ($user == "") {
		$user = $cfg['user'];
	}
	
    // add entry to the log
    $db->Execute("INSERT INTO tf_log (user_id,file,action,ip,ip_resolved,user_agent,time,level,message)"
        ." VALUES ("
        . $db->qstr($user).","
        . $db->qstr($file).","
        . $db->qstr(($action != "") ? $action : "unset").","
        . $db->qstr($cfg['ip']).","
        . $db->qstr($cfg['ip_resolved']).","
        . $db->qstr($cfg['user_agent']).","
        . $db->qstr(time()).","
        . $db->qstr($level).","
        . $db->qstr($message)
        .")"
    );  
    //if ($action != 'HIT')
    //    addGrowlMessage('Audit',"$action $file");
}

/**
 * Returns the drive space used as a percentage i.e 85 or 95
 *
 * @param $drive
 * @return int
 */
function getDriveSpace($drive) {
	if (@is_dir($drive)) {
		$dt = disk_total_space($drive);
		$df = disk_free_space($drive);
		return round((($dt - $df) / $dt) * 100);
	}
	return 0;
}

?>
