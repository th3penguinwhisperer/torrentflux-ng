var currentactions = 0;
var autorefresh = 1;

function loadcontent(divname, url, loadingmsg) {
	if (loadingmsg != null)
		$('#' + divname).html(loadingmsg);
	centerPopup();
    	$.get(url, function(data) {
	$('#' + divname).html("" + data + "");
        //window.setTimeout(update, 10000);
    });
}

function loadpopup(wintitle, url, loadingmsg) {
	$("#" + poptitle).html(wintitle);
	loadcontent(popbody, url, loadingmsg);
}

function gettransferlist(divname) {
	loadcontent(divname, 'index.php?page=transferlist');
};

function gettransfersources(parameters) {
	if (parameters == null) parameters = "";
	loadpopup("Transfer Sources", 'index.php?page=transfersources' + parameters, "Loading transfer source plugin ...<br><img src=images/ajax-loader.gif>");
};

// TODO: Function should get another function as it is a general ajax data reload instead of just the transfers
function refreshajaxdata() {
    if (indexTimer) clearTimeout(indexTimer); // Stop countdown timer

    setTimeout(ajax_update,200); // wait 300msec to reload the transferlist

    if ( autorefresh == 1 )
    	indexTimer = setTimeout(ajax_pageUpdate, 2000); // Start the countdown timer again
};

function toggleajaxupdate() {
	if (indexTimer) {
		clearTimeout(indexTimer);
		indexTimer = null;
		autorefresh = 0;
		$("#index_ajax_refresh_text").html("Turn Auto Refresh On");
	} else {
		autorefresh = 1;
		indexTimer = setTimeout(ajax_pageUpdate, 1000);
		setTimeout(ajax_update,200);
		$("#index_ajax_refresh_text").html("Turn Auto Refresh Off");
	}
}

function showerrormessage(message) {
	var type = 'error';
	showmessage(message, type);
}

function showmessage(message, atype) {
	var noti = noty({
	  text: 'noty - a jquery notification library!', 
	  type: atype,
	  timeout: 5000
	});
}

function showstatusmessage(message) {
	showmessage(message, 'information');
}

function headlessaction(action, reload, message) {
	currentactions++;
	$.get(action, function(data) {
		showstatusmessage(message);
		currentactions--;
		if (reload == true && currentactions < 1) refreshajaxdata();
	});
};
