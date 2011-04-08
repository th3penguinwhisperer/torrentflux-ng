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

$cfg['uid'] = "administrator";
$cfg['transmission_rpc_enable'] = true;

/**
 * Returns a string in format of TB, GB, MB, or kB depending on the size
 *
 * @param $inBytes
 * @return string
 */
function formatBytesTokBMBGBTB($inBytes) {
	if(!is_numeric($inBytes)) return "";
	if ($inBytes > 1099511627776)
		return round($inBytes / 1099511627776, 2) . " TB";
	elseif ($inBytes > 1073741824)
		return round($inBytes / 1073741824, 2) . " GB";
	elseif ($inBytes > 1048576)
		return round($inBytes / 1048576, 1) . " MB";
	elseif ($inBytes > 1024)
		return round($inBytes / 1024, 1) . " kB";
	else
		return $inBytes . " B";
}

$rowshtml = "";
function addRow ( $data ) {
	global $rowshtml;
	$rowshtml = $rowshtml . "\r<tr><td>".$data['displayname']. "</td><td>". $data['percentage'] . "</td><td>" . $data['statusStr'] . "</td><td>" . $data['down_speed'] ."</td><td>". $data['up_speed'] . "</td><td>".getActions($data['url_entry'])."</td></tr>";
}

function getTable($data) {
	return "<table>
$data
</table>";
}

function printHtml($html) {
  global $rowArray;

  print( "<html>\n<head></head>\n<body>");

  print($html);

  print( "
</body>
</html>" );
}

/**
 * Get actions that are specific and non specific for this client
 */
function getActions($transferhash) {
	$actions =  "<a href=\"dispatcher.php?client=transmission-daemon&action=delete&transfer=$transferhash\">Delete</a> ";
	$actions .= "<a href=\"dispatcher.php?client=transmission-daemon&action=start&transfer=$transferhash\">Start</a> ";
	$actions .= "<a href=\"dispatcher.php?client=transmission-daemon&action=stop&transfer=$transferhash\">Stop</a>";
	
	return $actions;
}

getPluginUi();

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
