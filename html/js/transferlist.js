var currentactions = 0;

function loadcontent(divname, url, loadingmsg) {
	if (loadingmsg != null)
		$('#' + divname).html(loadingmsg);
		centerPopup();
    	$.get(url, function(data) {
        	$('#' + divname).html("" + data + "");
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

function reloadtransferlist() {
    if (indexTimer) clearTimeout(indexTimer); // Stop countdown timer

    setTimeout(ajax_update,500); // wait 300msec to reload the transferlist

    indexTimer = setTimeout(ajax_pageUpdate, 1000); // Start the countdown timer again
};

function showstatusmessage(message) {
	$("#status_message").hide();
	$('#status_message').html(message);
	$("#status_message").css("background", "#33CC33");
	$('#status_message').fadeIn('slow', function() {
		// Animation complete
	});

	var refreshId = setTimeout(
	    function() {
		$('#status_message').fadeOut('slow', function() {
			// Animation complete
		});
	    },
	    5000
	);
}

function headlessaction(action, reload, message) {
	currentactions++;
	$.get(action, function(data) {
		showstatusmessage(message);
		currentactions--;
		if (reload == true && currentactions < 1) reloadtransferlist();
	});
};
