<?php

if ( !isset($_REQUEST['transfer'] ) ) {
	exit();
} else {
	$transfer = $_REQUEST['transfer'];
	$tab = (isset($_REQUEST['tab']) ? $_REQUEST['tab'] : "");
}

print("<div id=tabcontent>");

getTabLinks($transfer);
function getTabLinks($transfer) {
	print('
<script type="text/javascript">
function update(tab, transfer) {
                $(\'#tabcontent\').html(\'Loading..\');
                $.get(\'dispatcher.php?action=transfertabs&tab=\' + tab + \'&transfer=\' + transfer, function(data) {
                        $(\'#tabcontent\').html(data);
			reposition();
                //window.setTimeout(update, 10000);
                });
};
</script>
<img onclick="javascript:update(\'details\', \'' . $transfer . '\');">
<img onclick="javascript:update(\'files\', \'' . $transfer . '\');">
<br>');
}

if ($tab == "" || $tab == "details") {
	require_once('inc/clients/transmission-daemon/tabs/transferdetails.php');
} else if ($tab == "files") {
	require_once('inc/clients/transmission-daemon/tabs/transferfiles.php');
}
print("<script type=text/javascript>centerPopup();</script>");

print("</div>");

?>
