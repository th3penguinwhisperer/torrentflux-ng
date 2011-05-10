<?php

function getPluginUi() {
	//getClientNames() // Put the following in this function
	$clientNames = array("Transmission-daemon");
	$pluginName = "transmission-daemon";
	
	$clienthtmlcode = "";
	foreach ( $clientNames as $clientName ) {
		$clienthtmlcode .= "\t<option value=$clientName>".$clientName."</option>\n";
	}
	
	$actions = array("Add");
	array_push($actions, "Add+Start");
	$actionsnames = array("add");
	array_push($actionsnames, "addstart");
	$actionhtmlcode = "";
	foreach ( $actions as $action ) {
		$actionhtmlcode .= "\t<option value=" . array_shift($actionsnames) . ">" . $action . "</option>\n";
	}
	
	print("\n
<script type=\"text/javascript\">
$(function() {
  $(\".add_url_button\").click( function() {
    // Copy this part as much as necessary
    var url = escape( $(\"input#url\").val() );
    if (url == \"\") {
      //$(\"label#name_error\").show();
      $(\"#status_message\").show();
      $(\"#status_message\").html(\"The url field is empty\");
      $(\"input#url\").focus();
      return false;
    }

    // get other values
    var client = $(\"#client\").val();
    var action = $(\"#action\").val();
    var subaction = $(\"#subaction\").val();
    var plugin = $(\"#plugin\").val();

    // validate and process form here
    var dataString = 'url=' + url + '&client=' + client + '&action=' + action + '&subaction=' + subaction + '&plugin=' + plugin;

    $.ajax({
      type: \"POST\",
      url: \"dispatcher.php\",
      data: dataString,
      success: function() {
        $('#status_message').html(\"New transfer is added\");
        $(\"#status_message\").show();
        var refreshId = setTimeout(
            function() {
                $(\"#status_message\").val(\"\");
		$(\"#status_message\").hide();
                $(\"#url\").val(\"\");
            }, 
            5000
        );
        gettransferlist();
      }
    });
    return false;
  });
});
</script>
<div id=\"status_message\">
</div>
<form name=addurl action=\"\">Torrent URL: 
	<input type=text name=url id=url>
		<select name=client id=client>
$clienthtmlcode
		</select>
		<select name=subaction id=subaction>
$actionhtmlcode
		</select>
	<input type=submit class=\"add_url_button\">
	<input type=hidden name=plugin id=plugin value=$pluginName>
	<input type=hidden name=action id=action value=add>
</form>");
/*print("
<form name=uploadmetafile method=post action=dispatcher.php enctype=\"multipart/form-data\">Metafile upload
	<input type=file name=metafile>
	<input type=submit class=\"upload_file_button\">
	<input type=hidden name=plugin value=$pluginName>
	<input type=hidden name=action value=metafileupload>
</form>");*/
}

?>
