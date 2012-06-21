<?php

require_once('inc/plugins/FilePluginBase.php');
require_once('inc/generalfunctions.php');

class DownloadDirFile extends FilePluginBase
{
	function __construct($dir, $filename)
	{
		parent::__construct($dir, $filename);
	}

	static function isvalidaction($dir, $filename)
	{
		$cfg = Configuration::get_instance()->get_cfg();

		if($filename == "")
			return false;

		if ( file_exists($cfg['rewrite_path'] . $dir . $filename) )
			return true;

		return false;
	}

	static function getaction($dir, $filename)
	{
		$cfg = Configuration::get_instance()->get_cfg();

		return "<a href=\"javascript:loadpopup('Download dir/file', 'dispatcher.php?plugin=downloaddirfile&amp;action=passplugindata&amp;subaction=filemanagement&amp;dir=".urlencode($dir)."&amp;filename=".urlencode($filename)."', 'Loading download ...'); centerPopup(); loadPopup();\"><img src=\"themes/".$cfg['theme']."/images/dir/download_file.png\" /></a>
";
	}

	function getdownloadcode() {
		$ret = "<iframe id='downloadframe' style='display:none;'></iframe>";
		$ret .= "
Starting download...
<script type=text/javascript>
var objFrame=document.getElementById('downloadframe');
objFrame.src='dispatcher.php?plugin=downloaddirfile&action=passplugindata&subaction=filemanagement&download=yes&dir=".urlencode($dir)."&filename=". urlencode($filename) ."';

setTimeout(
	function () {
		disablePopup();
	},
	2000);
</script>
";
		return $ret;
	}

	function fileaction()
	{
		if( !isset($_REQUEST['download']) ) {
			print($this->getdownloadcode());
			return;
		}
		
		if (!file_exists($this->fullfilename)) { // TODO: create check if dir is ending with slash or not
			AuditAction('DOWNLOAD_FILE', $this->cfg["constants"]["error"], "Downloading item that does not exist: $this->fullfilename");
		} else {
			if ($this->filename == "") {
				AuditAction('DOWNLOAD_FILE', $this->cfg["constants"]["error"], "The filename is empty");
			}
			
			/*if ($cfg["enable_file_download"] != 1) {
			AuditAction($cfg["constants"]["error"], "ILLEGAL ACCESS: ".$cfg["user"]." tried to use download (".$down.")");
			@error("download is disabled", "index.php?iid=index", "");
			}
			// only valid entry with permission
			if ((isValidEntry(basename($down))) && (hasPermission($down, $cfg["user"], 'r'))) {
			@ ini_set("zlib.output_compression","Off");
			$current = downloadFile($down);
			} else {
			AuditAction($cfg["constants"]["error"], "ILLEGAL DOWNLOAD: ".$cfg["user"]." tried to download ".$down);
			$current = $down;
			
			if (tfb_isValidPath($down)) {
				$path = $cfg["path"].$down;
				$p = explode(".", $path);
				$pc = count($p);
				$f = explode("/", $path);
				$file = array_pop($f);
				$arTemp = explode("/", $down);
				if (count($arTemp) > 1) {
					array_pop($arTemp);
					$current = implode("/", $arTemp);
				}
			}
			}*/
			
			// 
			@ ini_set("zlib.output_compression","Off");
			$this->downloadFile();
		}
	}
	
	function downloadFile() {
        $current = ""; 
        // we need to strip slashes twice in some circumstances
        // Ex.  If we are trying to download test/tester's file/test.txt
        // $down will be "test/tester\\\'s file/test.txt"
        // one strip will give us "test/tester\'s file/test.txt
        // the second strip will give us the correct
        //      "test/tester's file/test.txt"
        $fulldir = stripslashes(stripslashes($this->fulldir));
        $filename = stripslashes(stripslashes($this->filename));
	$fullfilename = stripslashes(stripslashes($this->fulldir . $this->filename));
        if (tfb_isValidPath($fullfilename)) {
		// why the hell is this here? :p
		/*
                $path = $cfg["path"].$down;
                $p = explode(".", $path);
                $pc = count($p);
                $f = explode("/", $path);
                $file = array_pop($f);
                $arTemp = explode("/", $down);
                if (count($arTemp) > 1) {
                        array_pop($arTemp);
                        $current = implode("/", $arTemp);
                } */
                if (file_exists($this->fullfilename)) {
                        // size
                        $filesize = sprintf("%.0f",filesize($this->fullfilename));
                        // filenames in IE containing dots will screw up the filename
                        $headerName = (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
                                ? preg_replace('/\./', '%2e', $filename, substr_count($this->filename, '.') - 1)
                                : $this->filename;
                        // partial or full ?
                        if (isset($_SERVER['HTTP_RANGE'])) {
                                // Partial download
                                $bufsize = 32768;
                                if (preg_match("/^bytes=(\\d+)-(\\d*)$/D", $_SERVER['HTTP_RANGE'], $matches)) {
                                        $from = $matches[1];
                                        $to = $matches[2];
                                        if (empty($to))
                                                $to = $filesize - 1;
                                        $content_size = $to - $from + 1;
                                        @header("HTTP/1.1 206 Partial Content");
                                        @header("Content-Range: $from - $to / $filesize");
                                        @header("Content-Length: $content_size");
                                        @header("Content-Type: application/force-download");
                                        @header("Content-Disposition: attachment; filename=\"".$headerName."\"");
                                        @header("Content-Transfer-Encoding: binary");
                                        // write the session to close so you can continue to browse on the site.
                                        @session_write_close();
                                        $fh = fopen($this->fullfilename, "rb");
                                        fseek($fh, $from);
                                        $cur_pos = ftell($fh);
                                        while ($cur_pos !== FALSE && ftell($fh) + $bufsize < $to + 1) {
                                                $buffer = fread($fh, $bufsize);
                                                echo $buffer;
                                                $cur_pos = ftell($fh);
                                        }   
                                        $buffer = fread($fh, $to + 1 - $cur_pos);
                                        echo $buffer;
                                        fclose($fh);
                                } else {
                                        AuditAction('DOWNLOAD_FILE', $this->cfg["constants"]["error"], "Partial download : ".$this->cfg["user"]." tried to download ".$this->fullfilename);
                                        @header("HTTP/1.1 500 Internal Server Error");
                                        exit();
                                }   
			} else {
				// standard download
                                @header("Content-transfer-encoding: binary");
                                @header("Content-length: " . $filesize . "");
                                //$fileExt = getExtension($headerName);
$fileExt = "txt"; // TODO: do this decently
                                $is_image = preg_match("#(jpg|gif|png)#",$fileExt);
                                if (!$is_image) {
                                        @header("Content-type: application/octet-stream");
                                        @header("Content-disposition: attachment; filename=\"".$headerName."\"");
                                        @header("Accept-Ranges: bytes");
                                } else {
                                        @header("Content-type: image/$fileExt");
                                }
                                // write the session to close so you can continue to browse on the site.
                                @session_write_close();

$this->cfg["enable_xsendfile"] = 0; // TODO: Checkout why I might need this
                                if (!$is_image && $this->cfg["enable_xsendfile"] == 1)
                                        @header('X-Sendfile: '.$fullfilename);
                                else {
                                        $fp = popen("cat ".tfb_shellencode($fullfilename), "r");
                                        if(!fpassthru($fp)) print("something failed!");
                                        pclose($fp);
                                }
                        }
                        // log
                        AuditAction('DOWNLOAD_FILE', $this->cfg["constants"]["info"], "Sending file $this->fullfilename to user " . $this->cfg['user']);
                } else {
                        AuditAction('DOWNLOAD_FILE', $this->cfg["constants"]["error"], "File Not found for download: ".$this->cfg["user"]." tried to download ".$this->fullfilename);
                }
        } else {
                AuditAction('DOWNLOAD_FILE', $this->cfg["constants"]["error"], "ILLEGAL DOWNLOAD: ".$this->cfg["user"]." tried to download ".$this->fullfilename);
        }
        return $current;
	}
	
	
}

?>
