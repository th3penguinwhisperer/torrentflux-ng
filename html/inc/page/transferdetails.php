<?php

require_once("inc/clients/transmission-daemon/TransmissionDaemonClient.php");

$transfer = $_REQUEST['transfer'];
print("The transfer hash is ". $transfer);

if ( ! isset($_REQUEST['transfer']) ) {
	exit();
}

$td = new TransmissionDaemonClient();
$trtransfer = $td->getTransfer($transfer);

$item = array();
$arraydata = array();
$item['name'] = "Name";
$item['value'] = $trtransfer['name'];
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
