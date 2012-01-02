function drawProgressBar(color, width, percent, freespace, totalspace) {
    var pixels = width * (percent / 100);
    var diskspacehtml = '<div id="diskspaceplugin">';
    diskspacehtml += '<div id="diskspaceTitleText">' + getDiskspaceText(freespace, totalspace) + '</div>';
    diskspacehtml += '<div id="diskspaceFullbar" class="smallish-progress-wrapper" style="width: ' + width + 'px">';
    diskspacehtml += '<div id="diskspaceUsed" class="smallish-progress-bar" style="width: ' + pixels + 'px; background-color: ' + color + ';"></div>';
    diskspacehtml += '<div id="diskspaceText" class="smallish-progress-text" style="width: ' + width + 'px">' + percent + '%</div>';
    diskspacehtml += '</div>';
    diskspacehtml += '</div>';
    document.write(diskspacehtml);
}

function getDiskspaceText(freespace, totalspace) {
    return bytesToSize(freespace, 2) + ' free space (' +  bytesToSize(totalspace - freespace, 2) + "/" + bytesToSize(totalspace, 2) + ')';
}

function ajaxParseDiskspaceinfo(content) {
	// parsing the data
	var data = content.split(";");
	var freespace = data[0];
	var totalspace = data[1];

	// calculating the new values
	var usedspace = totalspace - freespace;
	var bar = document.getElementById('diskspaceUsed');
	var procent = (usedspace / totalspace) * 100;
	var fullbar = document.getElementById('diskspaceFullbar');
	var width = fullbar.style.width;
	
	// doing the changes
	bar.style.width = procent.toFixed(0) * (width/100);
	$('#diskspaceText').html(procent.toFixed(0) + "%");
	$('#diskspaceTitleText').html(getDiskspaceText(freespace, totalspace));
	//ajax_updateContent('diskspaceText', content);
}

function bytesToSize(bytes, precision) {
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    var posttxt = 0;
     if (bytes == 0) return '0 '+sizes[posttxt];
    while( bytes >= 1024 ) {
	posttxt++;
	bytes = bytes / 1024;
    }
    return (bytes).toFixed(precision) + " " + sizes[posttxt];
};
