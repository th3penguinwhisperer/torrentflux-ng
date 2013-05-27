/* $Id$ */

// fields
var ajax_fieldIds = new Array(
	"speedDown",
	"speedUp",
	"speedTotal",
	"cons",
	"freeSpace",
	"loadavg"
);
var ajax_idCount = ajax_fieldIds.length;
var ajax_fieldIdsXfer = new Array(
	"xferGlobalTotal",
	"xferGlobalMonth",
	"xferGlobalWeek",
	"xferGlobalDay",
	"xferUserTotal",
	"xferUserMonth",
	"xferUserWeek",
	"xferUserDay"
);
var ajax_idCountXfer = ajax_fieldIdsXfer.length;
//
var silentEnabled = 0;
var titleChangeEnabled = 0;
var pageTitle = "TorrentFlux-NG";
var queueActive = 0;
var xferEnabled = 0;
var usersEnabled = 0;
var usersHideOffline = 0;
var userList = "";
var sortTableEnabled = 0;
var driveSpaceBarStyle = "tf";
var indexTimer = null;
var indexTimer2 = null;
var updateTimeLeft = 0;
var ajaxScriptEnabled = 1;
var lastAjaxScript = "";

/**
 * ajax_initialize
 *
 * @param timer
 * @param delim
 * @param sEnabled
 * @param tChangeEnabled
 * @param pTitle
 * @param glsEnabled
 * @param glsSettings
 * @param bsEnabled
 * @param qActive
 * @param xEnabled
 * @param uEnabled
 * @param uHideOffline
 * @param tEnabled
 * @param sortEnabled
 * @param dsBarStyle
 * @param bwBarsEnabled
 * @param bwBarsStyle
 */
function ajax_initialize(timer, delim, sEnabled, tChangeEnabled, pTitle, glsEnabled, glsSettings, bsEnabled, qActive, xEnabled, uEnabled, uHideOffline, tEnabled, sortEnabled, dsBarStyle, bwBarsEnabled, bwBarsStyle) {
	ajax_updateTimer = timer;
	ajax_txtDelim = delim;
	silentEnabled = sEnabled;
	titleChangeEnabled = tChangeEnabled;
	pageTitle = pTitle;
	queueActive = qActive;
	xferEnabled = xEnabled;
	usersEnabled = uEnabled;
	usersHideOffline = uHideOffline;
	sortTableEnabled = sortEnabled;
	driveSpaceBarStyle = dsBarStyle;
	// url + params
	ajax_updateUrl = "index.php?page=index";
	// state
	ajax_updateState = 1;
	// http-request
	ajax_httpRequest = ajax_getHttpRequest();
	// start update-thread
	updateTimeLeft = ajax_updateTimer / 1000;
	ajax_pageUpdate();
}

/**
 * page ajax-update
 *
 */
function ajax_pageUpdate() {
	var obj;
	if (ajax_updateState == 1) {
		if (updateTimeLeft > 0) {
			obj = document.getElementById("span_update");
			if (silentEnabled == 0) {
				if (obj) obj.innerHTML = "Next AJAX-Update in " + String(updateTimeLeft) + " seconds";
			} else {
				if (obj) obj.innerHTML = "AJAX-Update enabled";
			}
			updateTimeLeft--;
		}
		else if (updateTimeLeft == 0) {
			updateTimeLeft = -1;
			if (silentEnabled == 0) {
				obj = document.getElementById("span_update");
				if (obj) obj.innerHTML = "Update in progress...";
			}
			if ((titleChangeEnabled == 1) && (silentEnabled == 0)) {
				document.title = "Update in progress... - "+ pageTitle;
			}
			if (typeof(ajax_update) != 'undefined') {
				//if (indexTimer2) window.clearTimeout(indexTimer2);
				//indexTimer2 = setTimeout(ajax_update, 100);
				ajax_update();
			}
		}
		if (indexTimer) clearTimeout(indexTimer);
		indexTimer = setTimeout(ajax_pageUpdate, 1000);
	} else {
		obj = document.getElementById("span_update");
		if (obj) obj.innerHTML = "AJAX-Update disabled";
	}
	if (obj) obj = null;
}

/**
 * process XML-response
 *
 * @param content
 */
function ajax_processXML(content) {
	alert(content);
}

function ajaxParseTransferlist(content) {
	$('#list1').jqGrid('setGridParam', { url:'transferlistxml.php', datatype:'xml', page:1 }).trigger('reloadGrid');
}

function ajaxParseRates(content) {
	// parsing the data
	var data = content.split(";");
	var totaldownrate = data[0];
	var totaluprate = data[1];

	document.title = 'Down: ' + bytesToSize(totaldownrate,2) + '/s | Up: ' + bytesToSize(totaluprate,2) + '/s';
}

function ajaxParseStats(content) {
	// TODO FILL IN BODY
	var data = content.split(";");

	document.getElementById('plugin_stats_totaldownloadedbytes').innerHTML = bytesToSize(data[0], 2);
	document.getElementById('plugin_stats_totaluploadedbytes').innerHTML = bytesToSize(data[1], 2);
	document.getElementById('plugin_stats_totaldownloadrate').innerHTML = bytesToSize(data[2], 2) + '/s';
	document.getElementById('plugin_stats_totaluploadrate').innerHTML = bytesToSize(data[3], 2) + '/s';
	document.getElementById('plugin_stats_totaltransfercount').innerHTML = data[4];
}

/**
 * process text-response
 *
 * @param content
 */
function ajax_processText(content) {
	var aryCount = 0;

	var ajaxBlocDelim = new RegExp('[\|\#]{3}');
	var tempAry = content.split(ajaxBlocDelim);
	
	while(tempAry.length>=2) {
	  var strParam = tempAry.pop();
	  var strFun = tempAry.pop();
	  //Create the function call from function name and parameter.
	  var funcCall = strFun + "(strParam);";
	  //Call the function
	  var ret = eval(funcCall);
	}
	
	if (aryCount > 0) {
		var ajaxBlocDelim = new RegExp('[\|\#]{3}');
		var tempAry = content.split(ajaxBlocDelim);
		
		// theme specific event
		if (typeof(beforeAjaxUpdate) != 'undefined') {
			beforeAjaxUpdate();
		}
		
		// update
		ajax_updateContent(transferList);
		
		// theme specific event
		if (typeof(afterAjaxUpdate) != 'undefined') {
			afterAjaxUpdate();
		}
		
		if (ajaxScript != lastAjaxScript) {
			try {
				eval(ajaxScript);
				lastAjaxScript = ajaxScript;
			} catch(e) {
				if (typeof(console) != 'undefined') {
					console.log("something wrong in ajax script after updateContent() :");
					console.log(e);
				}
			}
		}
	}
	// timer
	updateTimeLeft = ajax_updateTimer / 1000;
}


/**
 * update page contents from response
 *
 * @param transferListStr
 */
function ajax_updateContent(element, dataStr) {
	// transfer-list
	if (element != null) {
		// update content
		document.getElementById(element).innerHTML = dataStr;
		// re-init sort-table
		if (sortTableEnabled == 1)
			sortables_init();
	}
	// transfer-list
	// TODO: 2 times same code execution?
	/*
	if (ajaxScriptEnabled == 1) {
		// update content
		document.getElementById("transferList").innerHTML = transferListStr;
		// re-init sort-table
		if (sortTableEnabled == 1)
			sortables_init();
	}
	*/
}

/**
 * unload
 */
function ajax_unload() {
	if(indexTimer) window.clearTimeout(indexTimer);
	if(indexTimer2) window.clearTimeout(indexTimer2);
}
