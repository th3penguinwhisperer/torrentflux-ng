<?php

/* $Id$ */

/*******************************************************************************

 LICENSE

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License (GPL)
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.

 To read the license please visit http://www.gnu.org/copyleft/gpl.html

*******************************************************************************/

require_once("inc/plugins/transfersource.torrent.php");
require_once("inc/generalfunctions.php");
require_once('inc/singleton/Configuration.php');

$cfg = Configuration::get_instance()->get_cfg();

$rowshtml = "";
function addRow ( $transfer ) {
	global $rowshtml;
	$data = $transfer->getTransferListItem();
	$rowshtml = $rowshtml . "\r<tr><td><img onclick=\"javascript:pp.url('dispatcher.php?action=transfertabs&transfer=" . $data['url_entry'] . "');\">".$data['displayname']. "</td><td>". $data['estTime'] . "</td><td>" . $data['percentage'] . "</td><td>" . $data['statusStr'] . "</td><td>" . $data['down_speed'] ."</td><td>". $data['up_speed'] . "</td><td>" . $transfer->getActions()."</td></tr>";
}

function getTable($data) {
	return "<table>
$data
</table>";
}

function printHtml($html) {
  global $rowArray;

//	<script type="text/javascript" src="/js/jquery.tablesorter.js"></script>
// TODO: path is now relative... this might have to be changed to an absolute path
  print( '<html>
<head>
	<script type="text/javascript" src="js/jquery.js"></script> 
	<script type="text/javascript" src="js/popup.js"></script>
	<script type="text/javascript">
pp = new Popup;
	</script>
</head>
<body>');

  getPluginUi();

  print($html);

  print( "
</body>
</html>" );
}


if ($cfg["transmission_rpc_enable"]) {
	require_once('inc/clients/transmission-daemon/TransmissionDaemonClient.php');
	$td = new TransmissionDaemonClient();
	$arUserTorrent = $td->getTransferList($cfg['uid']);
}

//print_r($arUserTorrent);

foreach ($arUserTorrent as $aTorrent) {
	addRow($aTorrent);
}

printHtml(getTable($rowshtml));

?>
