<?php

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
function handleFileUpload($files) {
	// check if files exist
	if (empty($files)) {
		// log
		//AuditAction($cfg["constants"]["error"], "no file in file-upload"); //TODO enable logging
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
						$actionId, $uploadMessages, $tStack);
				}
			}
		} else {
			if ($upload['size'] > 0) {
				_dispatcher_processUpload(
					$upload['name'], $upload['tmp_name'], $upload['size'],
					$actionId, $uploadMessages, $tStack);
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
function _dispatcher_processUpload($name, $tmp_name, $size, $actionId, &$uploadMessages, &$tStack) {
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

					$client = ClientHandler::getInstance();
					$client->fileUploaded($fullfilename);
					
					//if ( $actionId > 1 ) {
						//startTransmissionTransfer( $hash );
						//array_push($tStack,$filename);
					//}
					//return true;
					// instant action ?
					if ($actionId > 1)
						array_push($tStack,$filename);
					// return
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

?>
