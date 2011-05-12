<?php

class TransfersourceFileupload
{

	static function getPluginUi() {
		print("
<form name=uploadmetafile method=post action=dispatcher.php enctype=\"multipart/form-data\">Metafile upload
	<input type=file name=metafile>
	<input type=submit class=\"upload_file_button\">
	<input type=hidden name=action value=metafileupload>
</form>");
	}

}

?>