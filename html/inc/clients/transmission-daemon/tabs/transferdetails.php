<?php

require_once("inc/clients/transmission-daemon/TransmissionDaemonClient.php");

$transfer = $_REQUEST['transfer'];
print("The transfer hash is ". $transfer);

if ( ! isset($_REQUEST['transfer']) ) {
	exit();
}

$td = new TransmissionDaemonClient();
$transtransfer = $td->getTransfer($transfer);
$transdata = $transtransfer->getTransferListItem();

$item = array();
$arraydata = array();
$item['name'] = "Name";
$item['value'] = $transdata['displayname'];
array_push($arraydata, $item);
$item['name'] = "Size";
$item['value'] = $transdata['format_af_size'];
array_push($arraydata, $item);
$item['name'] = "Status";
$item['value'] = $transdata['statusStr'];
array_push($arraydata, $item);
$item['name'] = "Download rate";
$item['value'] = $transdata['down_speed'];
array_push($arraydata, $item);
$item['name'] = "Upload rate";
$item['value'] = $transdata['up_speed'];
array_push($arraydata, $item);
$item['name'] = "Downloaded";
$item['value'] = $transdata['downloaded'];
array_push($arraydata, $item);

getDetailsPage($arraydata);

function getDetailsPage($data) {
	print("<table>");
	foreach ($data as $item) {
		print("<tr><td>" . $item['name'] . "</td><td>" . $item['value'] . "</td></tr>" );
	}
	print("</table>");
}

?>
