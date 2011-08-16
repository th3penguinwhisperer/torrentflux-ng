<?php

require_once('inc/classes/singleton/Configuration.php');

$cfg = Configuration::get_instance()->get_cfg();

if ($cfg["transmission_rpc_enable"]) {
	require_once('inc/clients/transmission-daemon/TransmissionDaemonClient.php');
	$td = new TransmissionDaemonClient();
	$arUserTorrent = $td->getTransferList($_SESSION['uid']);
}

function getTable($data) {
	return "<table>
$data
</table>";
}

$rowshtml = "";
function addRow ( $transfer ) {
	global $rowshtml;
	$data = $transfer->getTransferListItem();
	$rowshtml = $rowshtml . "\r<tr><td><img src=images/edit.png onclick=\"javascript:pp.url('dispatcher.php?action=transfertabs&transfer=" . $data['url_entry'] . "');\">".$data['displayname']. "</td><td>". $data['estTime'] . "</td><td>" . $data['percentage'] . "</td><td>" . $data['statusStr'] . "</td><td>" . $data['down_speed'] ."</td><td>". $data['up_speed'] . "</td><td>" . $transfer->getActions()."</td></tr>"; // TODO get this retrieved in a nicer way
}

foreach ($arUserTorrent as $aTorrent) {
	addRow($aTorrent);
}

print( getTable($rowshtml) );

?>
