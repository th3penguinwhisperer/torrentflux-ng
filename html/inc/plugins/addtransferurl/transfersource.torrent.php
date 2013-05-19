<?php

require_once("inc/generalfunctions.php");

class TransfersourceTorrent
{
	
	function __construct()
	{
		;
	}

	function show() {
		$pluginName = "transmission-daemon"; // TODO: can probably be removed as plugins in this context are not linked to a client
	
		print("\n
<form name=addurl action=\"\">Torrent URL: 
<script type=\"text/javascript\" src=\"js/jquery.js\"></script>
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
    if ( $('#publictorrent').is(':checked') ) {
		var publictorrent = 'on';
	} else {
		var publictorrent = 'off';
	}

    // validate and process form here
    var dataString = 'url=' + url + '&client=' + client + '&action=' + action + '&subaction=' + subaction + '&plugin=' + plugin + '&publictorrent=' + publictorrent;

    $.ajax({
      type: \"POST\",
      url: \"dispatcher.php\",
      data: dataString,
      success: function() {
	$(\"#url\").val(\"\");
        showstatusmessage(\"New transfer is added\");
        refreshajaxdata();
      },
      error: function() {
        showstatusmessage(\"Adding the transfer was not successful\");
      }
    });
    return false;
  });
});
</script>

	<input type=text name=url id=url>
	<br><input type=checkbox id=publictorrent name=publictorrent checked=checked> Public torrent
");
	getClientSelection();
	getActionSelection();
	print("<input type=submit class=\"add_url_button\">
	<input type=hidden name=action id=action value=add>
</form>");

	}
	
}

?>
