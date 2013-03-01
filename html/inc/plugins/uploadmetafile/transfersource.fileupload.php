<?php

require_once("inc/generalfunctions.php");
require_once("inc/plugins/PluginAbstract.php");

class TransfersourceFileupload extends PluginAbstract
{

	function show() {
		self::printIframe();
	}

	/* TODO: check how this issue can be resolved in a more generic way (the upload file form issue) */
	static function printIframe() {
		print("
<iframe width=100% frameborder=0 src=\"dispatcher.php?action=metafileupload&plugin=metafileupload\">
");
	}
	
	static function printForm() {
		print("
<form name=uploadmetafile method=post action=dispatcher.php enctype=\"multipart/form-data\">Metafile upload
	<input type=hidden name=plugin value=metafileupload>
	<input type=file name=metafile>
	<br><input type=checkbox id=publictorrent name=publictorrent checked=checked> Public torrent
");
		getClientSelection();
		getActionSelection();
		print("<input type=submit class=\"upload_file_button\">
	<input type=hidden name=action value=metafileupload>
</form>");
	}

	public function handleRequest($data) { ; }
}

?>
