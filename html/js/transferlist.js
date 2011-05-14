function loadcontent(divname, url, loadingmsg) {
	if (loadingmsg != null)
		$('#' + divname).html(loadingmsg);
    $.get(url, function(data) {
        $('#' + divname).html(data);
        //window.setTimeout(update, 10000);
    });
}

function gettransferlist(divname) {
    loadcontent(divname, 'index.php?page=transferlist');
};

function gettransfersources(divname, parameters) {
	if (parameters == null) parameters = "";
    loadcontent(divname, 'index.php?page=transfersources' + parameters, "Loading transfer source plugin ...");
};

function reloadtransferlist(divname) {
    var refreshId = setInterval(
        function() { gettransferlist(divname); },
        9000);
};