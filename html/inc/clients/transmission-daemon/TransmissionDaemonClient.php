<?php

require_once("inc/clients/ClientInterface.php");
//require_once("inc/clients/transmission-daemon/");

class TransmissionDaemonClient implements ClientInterface
{
	
	function getCapabilities() {
		$capabilities = array("start", "stop", "delete", "deletewithdata");
		
		return $capabilities;
	}
	
	function getActions() {
		$actions =  "<a href=\"dispatcher.php?client=transmission-daemon&action=delete&transfer=$transferhash\">Delete</a> ";
		$actions .= "<a href=\"dispatcher.php?client=transmission-daemon&action=start&transfer=$transferhash\">Start</a> ";
		$actions .= "<a href=\"dispatcher.php?client=transmission-daemon&action=stop&transfer=$transferhash\">Stop</a>";
	
		return $actions;
	}
	
	function executeAction($transfer, $action) {
		;
	}
	
	function getTransferList($uid) {
		require_once("inc/clients/transmission-daemon/functions.rpc.transmission.php");
		$result = getUserTransmissionTransfers($uid);
	
		$arUserTorrent = array();
	
		// eta
		//  -1 : Done
		//  -2 : Unknown
		foreach($result as $aTorrent)
		{
			// fill in eta
			if ( $aTorrent['eta'] == '-1' && $aTorrent['percentDone'] != 1 ) {
				$eta = 'n/a';
			} elseif ( $aTorrent['percentDone'] == 1 ) {
				$eta = 'Download Succeeded!';
			} elseif ( $aTorrent['eta'] == '-2' ) {
				$eta = 'Unknown';
			} else {
				$eta = convertTime( $aTorrent['eta'] );
			}
	
			$status = $aTorrent['status'];
			switch ($aTorrent['status']) {
			case 16:
				$transferRunning = false;
				if ( $aTorrent['percentDone'] >= 1 ) {
					$status = TransferStatus::STATUS_FINISHED;
					$eta = '';
				} else {
					$status = TransferStatus::STATUS_STOPPED;
					$eta = 'Torrent Stopped'; # this might be fixed in a cleaner way
					if ( $aTorrent['downloadedEver'] == 0 ) {
						$status = TransferStatus::STATUS_NEW;
						$eta = '';
					}
				}
				break;
			case 4:
				if ( $aTorrent['rateDownload'] == 0 ) {
					$status = TransferStatus::STATUS_IDLE;
				} else {
					$status = TransferStatus::STATUS_DOWNLOADING;
				}
				$transferRunning = true;
				break;
			case 8:
				$status = TransferStatus::STATUS_SEEDING;
				$transferRunning = true;
				break;
			case 2:
				//$status = "Checking data...";
				$status = TransferStatus::STATUS_CHECKING;
				$transferRunning = true;
				break;
			}
	
			if ($transferRunning) // Only for running torrents otherwhise seriously slows down listing
				$seeds = getTransmissionSeederCount($aTorrent['hashString']);
			else
				$seeds = 0;
	
			// TODO: transferowner is always admin... probably not what we want
			// Suppress error/warning messages(using the @ sign) otherwhise a shitload of warnings are shown
			$tArray = @array(
				'is_owner' => true,
				'transferRunning' => ($transferRunning ? 1 : 0),
				'url_entry' => $aTorrent['hashString'],
				'hd_image' => getTransmissionStatusImage($transferRunning, $seeds, $aTorrent['rateUpload']),
				'hd_title' => $nothing,
				'displayname' => $aTorrent['name'],
				'transferowner' => getTransmissionTransferOwner($aTorrent['hashString']),
				'format_af_size' => formatBytesTokBMBGBTB( $aTorrent['totalSize'] ),
				'format_downtotal' => $nothing,
				'format_uptotal' => $nothing,
				'statusStr' => $status,
				'graph_width' => ( $status==='New' ? -1 : floor($aTorrent['percentDone']*100) ),
				'percentage' => ( $status==='New' ? '' : floor($aTorrent['percentDone']*100) . '%' ),
				'progress_color' => '#22BB22',
				'bar_width' => 4,
				'background' => '#000000',
				'100_graph_width' => 100 - floor($aTorrent['percentDone']*100),
				'down_speed' => formatBytesTokBMBGBTB( $aTorrent['rateDownload'] ) . '/s',
				'up_speed' => formatBytesTokBMBGBTB( $aTorrent['rateUpload'] ) . '/s',
				'seeds' => $seeds,
				'peers' => $nothing,
				'estTime' => $eta,
				'clientType' => 'torrent',
				'upload_support_enabled' => 1,
				'client' => 'transmissionrpc',
				'url_path' => urlencode( $cfg['user'] . '/' . $aTorrent['name'] ),
				'datapath' => $aTorrent['name'],
				'is_no_file' => 1,
				'show_run' => 1,
				'entry' => $aTorrent['name']
			);
			
			array_push($arUserTorrent, $tArray);
	
			// Adds the transfer rate for this torrent to the total transfer rate
			// TODO: this should be in another place probably
	//		if ($transferRunning) {
	//			if (!isset($cfg["total_upload"]))
	//				$cfg["total_upload"] = 0;
	//			if (!isset($cfg["total_download"]))
	//				$cfg["total_download"] = 0;
	//			$cfg["total_upload"] = $cfg["total_upload"] + GetSpeedValue($aTorrent[rateUpload]/1000);
	//			$cfg["total_download"] = $cfg["total_download"] + GetSpeedValue($aTorrent[rateDownload]/1000);
	//		}
		}
		
		return $arUserTorrent;
	}
	
	/**
	 * get the list of transfers for this client in the format the index page requires
	 */
	//function getTransmissionTransferList($uid) {
	//	
	//}
}

?>