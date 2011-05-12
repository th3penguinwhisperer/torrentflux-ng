<?php

class TransfersourceTorrent
{

	static function getPluginUi() {
		$pluginName = "transmission-daemon"; // TODO: can probably be removed as plugins in this context are not linked to a client
	
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
	<input type=submit class=\"add_url_button\">
	<input type=hidden name=action id=action value=add>
</form>");

	}
	
}

?>
