<?php

print("The transfer hash is ". $transfer);

if ( isset($_REQUEST['subaction']) && $_REQUEST['subaction'] == "set" ) {
	require_once("inc/clients/transmission-daemon/TransmissionDaemonClient.php");
	foreach ($_REQUEST['files'] as $fileid ) {
		$selectedFiles[] = (int)$fileid;
	}
	set($transfer, $selectedFiles);
} else {
	require_once("inc/clients/transmission-daemon/TransmissionDaemonClient.php");
	get($transfer);
}

function set($transfer, $selectedFiles) {
	
	$td = new TransmissionDaemonClient();
	$transtransfer = $td->getTransfer($transfer);
	$data = $transtransfer->getData();
	//$allFiles = $data['files'];
	$wantedFiles = $data['wanted'];

	# Get files that are wanted or not for download, then we can compare.
	//$responseWantedFiles = $trans->get( $theTorrent[id], array('wanted') );
	//$wantedFiles = $responseWantedFiles[arguments][torrents][0][wanted];
	
	$thearray = array_fill(0, count($wantedFiles), 0);
	foreach ( $selectedFiles as $fileid ) {
		$thearray[$fileid] = 1;
	}
	
	$counter = 0;
	$includeFiles = array();
	$excludeFiles = array();
	foreach ( $wantedFiles as $fileid => $wanted ) {
		if ( $thearray[$counter] == 1 && $wantedFiles[$counter] == 0 ) { // the file is marked as selected in the gui but it has not been saved as "wanted"
				$includeFiles[]=(int)$counter; // deselect this files
		}
		if ( $thearray[$counter] == 0 && $wantedFiles[$counter] == 1 ) { // the file is not marked as selected in the gui but it has been saved as "wanted"
			$excludeFiles[]=(int)$counter; // deselect this files
		}
		$counter++;
	}
	
	if (count($includeFiles)>0) {
		$includeFiles = array_values($includeFiles);
		$response = $td->setTransfer( $transfer, array("files-wanted" => $includeFiles) );
	}
	if (count($excludeFiles)>0) {
		$excludeFiles = array_values($excludeFiles);
		$response = $td->setTransfer( $transfer, array("files-unwanted" => $excludeFiles) );
	}

}

function get($transfer) {
	$td = new TransmissionDaemonClient();
	$transtransfer = $td->getTransfer($transfer);
	$data = $transtransfer->getData();
	$allFiles = $data['files'];
	$wantedFiles = $data['wanted'];
	$dirnum = count($allFiles); // make sure this is in here otherwhise you will loose alot of time debugging your code on what is missing (the filetree selection is not displayed)
	$files = array();

	$tree = new dir("/",$dirnum, -1);
	foreach($allFiles as $file) {
		$fileparts = explode("/", $file['name']);
		$filesize = $file['length'];
		$fileprops = array( 'length' => $filesize, 'path' => $fileparts );
		array_push($files, $fileprops);
	}
	$filescount = count($files);

	foreach( $files as $filenum => $file) {
		$depth = count($file['path']);
		$branch =& $tree;
		for ($i=0; $i < $depth; $i++) {
			if ($i != $depth - 1) {
				$d =& $branch->findDir($file['path'][$i]);
				if ($d) {
					$branch =& $d;
				} else {
					$dirnum++;
			$d =& $branch->addDir(new dir($file['path'][$i], $dirnum, -1));
			$branch =& $d;
				}
			} else {
				$branch->addFile(new file($file['path'][$i]." (".$file['length'].")", $filenum,$file['length'], ($wantedFiles[$filenum] == 1? 1 : -1) ));
			}
		}
	}

	$retVal = "";
	$retVal .= "<div id=\"sel\">0</div>"; // This is a placeholder updated by the javascript dtree
	$retVal .= "\n
<script type=\"text/javascript\" src=\"js/jquery.js\"></script>
<script type=\"text/javascript\">
$(function() {
  $(\".save_file_selection\").click( function() {
    var tab = $(\"#file_selection .tab\").val();
    var count = $(\"#file_selection .count\").val();
    var file_query_string = '';
    $(\"[@id=files]:checked\").each(
    function()
    {
        if (this.checked)
        {
            file_query_string += \"&files[]=\" + this.value;
        }
    });
    var filecount = $(\"#file_selection .filecount\").val();
    var transfer = $(\"#file_selection .transfer\").val();
    var subaction = $(\"#file_selection .subaction\").val();
    var action = $(\"#file_selection .action\").val();

    // validate and process form here
    var dataString = 'tab=' + tab + '&transfer=' + transfer + '&subaction=' + subaction + '&action=' + action + '&filecount=' + filecount + '&count=' + count + '&files=' + file_query_string;

    $.ajax({
      type: \"POST\",
      url: \"dispatcher.php\",
      data: dataString,
      success: function() {
        showstatusmessage(\"Selected files have been updated\");
	disablePopup();
      },
      error: function() {
        showstatusmesage(\"File selection has not been updated\");
      }
    });
    return false;
  });
});
</script>";
	$withForm = true; //TODO: get this in a setting
	if ($withForm) {
		$retVal .= "<form name=\"file_selection\" id=\"file_selection\" action=\"\" >";
		//$retVal .= "<form name=\"priority\" action=\"inc/clients/transmission-daemon/tabs/transferfiles.php\" method=\"POST\" >"; // TODO: get this fixed in a nicer way
	}
	$retVal .= "<div id=\"filelist\"></div>";
	$retVal .= "<script type=\"text/javascript\">\n";
	$retVal .= "var sel = 0;\n";
	$retVal .= "d = new dTree('d');\n";
	$retVal .= $tree->draw(-1);
	//$retVal .= "document.write(d);\n";
	$retVal .= "var filelistDiv = document.getElementById(\"filelist\");\n";
	$retVal .= "filelistDiv.innerHTML=d;\n";
	$retVal .= "sel = getSizes();\n";
	$retVal .= "drawSel();\n";
	$retVal .= "</script>\n";
	$retVal .= "<input type=\"hidden\" name=\"filecount\" class=\"filecount\" value=\"".$filescount."\">";
	$retVal .= "<input type=\"hidden\" name=\"count\" class=\"count\" value=\"".$dirnum."\">";
	$retVal .= "<br>";
	if ($withForm) {
		$retVal .= "<input type=\"hidden\" name=\"transfer\" class=\"transfer\" value=\"".$transfer."\" >";
		$retVal .= "<input type=\"hidden\" name=\"subaction\" class=\"subaction\" value=\"set\" >";
		$retVal .= "<input type=\"hidden\" name=\"tab\" class=\"tab\" value=\"files\" >";
		$retVal .= "<input type=\"hidden\" name=\"action\" class=\"action\" value=\"transfertabs\">";
		$retVal .= '<input type="submit" class="save_file_selection" value="Save" >';
		$retVal .= "<br>";
		$retVal .= "</form>";
	}

	print('<link rel="StyleSheet" href="css/dtree.css" type="text/css" />');
	print('<script type="text/javascript">var dtree_path_images = "images/dtree/";</script>'); // without this variable no tree is shown
	print('<script type="text/javascript" src="js/dtree.js"></script>');

	print($retVal);
}

/**
 * dir
 */
class dir {

        var $name;
        var $subdirs;
        var $files;
        var $num;
        var $prio;

        function dir($name,$num,$prio) {
                $this->name = $name;
                $this->num = $num;
                $this->prio = $prio;
                $this->files = array();
                $this->subdirs = array();
        }

        function &addFile($file) {
                $this->files[] =& $file;
                return $file;
        }

        function &addDir($dir) {
                $this->subdirs[] =& $dir;
                return $dir;
        }

        // code changed to support php4
        // thx to Mistar Muffin
        function &findDir($name) {
                foreach (array_keys($this->subdirs) as $v) {
                        $dir =& $this->subdirs[$v];
                        if($dir->name == $name)
                                return $dir;
                }
                $retVal = false;
                return $retVal;
        }

        function draw($parent) {
                $draw = ("d.add(".$this->num.",".$parent.",\"".$this->name."\",".$this->prio.",0);\n");
                foreach($this->subdirs as $v)
                        $draw .= $v->draw($this->num);
                foreach($this->files as $v) {
                        if(is_object($v))
                          $draw .= ("d.add(".$v->num.",".$this->num.",\"".$v->name."\",".$v->prio.",".$v->size.");\n");
                }
                return $draw;
        }

}

/**
 * file
 */
class file {

        var $name;
        var $prio;
        var $size;
        var $num;

        function file($name,$num,$size,$prio) {
                $this->name = $name;
                $this->num      = $num;
                $this->size = $size;
                $this->prio = $prio;
        }

}

?>
