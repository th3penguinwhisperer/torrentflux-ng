<?php

require_once('inc/plugins/PluginInterface.php');
require_once("inc/generalfunctions.php");

class SearchEngines implements PluginInterface
{

	function __construct()
	{
		;
	}
	
	function show()
	{
		if ( ! isset($_REQUEST['action']) ) $_REQUEST['action'] = '';
		if ( $_REQUEST['action'] != 'passplugindata' ) {
			print('
<form name=searchengine action="">Torrent Search Query:
<script type="text/javascript">

$(function() {
  $(".search_button").click( function() {
    // Copy this part as much as necessary
    var query = escape( $("input#query").val() );
    if (query == "") {
      //$("label#name_error").show();
      $("#status_message").show();
      $("#status_message").html("The search field is empty");
      $("input#query").focus();
      return false;
    }

    // get other values
    var client = $("#client").val();
    var action = $("#action").val();
    var subaction = $("#subaction").val();
    var plugin = $("#plugin").val();
    if ( $("#publictorrent").is(":checked") ) {
		var publictorrent = "on";
	} else {
		var publictorrent = "off";
	}

    // validate and process form here
    var dataString = "query=" + query + "&client=" + client + "&action=" + action + "&subaction=" + subaction + "&plugin=" + plugin + "&publictorrent=" + publictorrent;

    $.ajax({
      type: "POST",
      url: "dispatcher.php",
      data: dataString,
      success: function(data) {
	$("#query").val("");
        $("#searchresult").html(data);
      },
      error: function() {
        showstatusmessage("Error while searching for torrents!");
      }
    });
    return false;
  });
});

	function addTransfer(url)
	{
	    // get other values
	    var client = $("#client").val();
	    var subaction = $("#subaction").val();
	    if ( $("#publictorrent").is(":checked") ) {
		var publictorrent = "on";
	    } else {
		var publictorrent = "off";
	    }
	
	    // validate and process form here
	    // TODO: all plugins jquery have to be edited/implemented when an option is added, which is error prone and might be fixed in a cleaner way
	    var dataString = \'url=\' + url + \'&client=\' + client + \'&action=add\' + \'&subaction=\' + subaction + \'&publictorrent=\' + publictorrent;
	
	    $.ajax({
	      type: "POST",
	      url: "dispatcher.php",
	      data: dataString,
	      success: function() {
		showstatusmessage("Transfer added");
		reloadtransferlist();
	      }
	    });
	}


</script>

	<input type=text name=query id=query>
	<br><input type=checkbox id=publictorrent name=publictorrent checked=checked> Public torrent
');

	getClientSelection();
	getActionSelection();
	print('<input type=submit class="search_button">
	<input type=hidden name=action id=action value=passplugindata>
	<input type=hidden name=plugin id=plugin value=searchengines>
</form>
<div id=searchresult name=searchresult />
');
		} else {
			require_once('inc/plugins/searchengines/torrentSearch.php');
			print( getPage() ); // prints the string
		}
	}

	function get()
	{
		require_once('inc/plugins/searchengines/torrentSearch.php');
		return getPage(); // returns string
	}

	function getConfiguration()
	{
		;
	}
	
	function setConfiguration($configArray)
	{
		;
	}

}

?>
