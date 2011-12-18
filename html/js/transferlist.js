function loadcontent(divname, url, loadingmsg) {
	if (loadingmsg != null)
		$('#' + divname).html(loadingmsg);
		centerPopup();
    	$.get(url, function(data) {
        	$('#' + divname).html("<div id=popup_titlebar><div id=popup_windowtitle>Window</div><div id=popup_close onclick='disablePopup()' >x</div></div><div id=popup_body>" + data + "</div>");
        //window.setTimeout(update, 10000);
    });
}

function gettransferlist(divname) {
	loadcontent(divname, 'index.php?page=transferlist');
};

function gettransfersources(divname, parameters) {
	if (parameters == null) parameters = "";
    loadcontent(divname, 'index.php?page=transfersources' + parameters, "Loading transfer source plugin ...<br><img src=images/ajax-loader.gif>");
};

function reloadtransferlist(divname) {
    var refreshId = setInterval(
        function() { gettransferlist(divname); },
        9000);
};

function showmessage(message) {
	$("#status_message").hide();
	$('#status_message').html(message);
	$("#status_message").css("background", "#33CC33");
	$('#status_message').fadeIn('slow', function() {
		// Animation complete
	});

	//$("#status_message").show();
	var refreshId = setTimeout(
	    function() {
		//$("#status_message").val("");
		$('#status_message').fadeOut('slow', function() {
			// Animation complete
		});
		//$('#status_message').html('');
		//$("#status_message").css("background", "");
		//$("#status_message").hide();
	    },
	    5000
	);
}

function headlessaction(action, reload, message) {
	$.get(action, function(data) {
		showmessage(message);
	});
	if (reload == true) reloadtransferlist("transferlist");
};
