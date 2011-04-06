<?php

function getPluginUi() {
	//getClientNames() // Put the following in this function
	$clientNames = array("Transmission-daemon");
	$pluginName = "transmission-daemon";
	
	$htmlcode = "";
	foreach ( $clientNames as $clientName ) {
		$htmlcode .= "<option value=$clientName>".$clientName."</option>";
	}
	
	print("<form method=post action=dispatcher.php>Torrent URL: 
<input type=text name=url>
<select name=client>
	$htmlcode;
</select>
<input type=submit>
<input type=hidden name=plugin value=$pluginName>
<input type=hidden name=action value=upload>
</form>");
}

?>
