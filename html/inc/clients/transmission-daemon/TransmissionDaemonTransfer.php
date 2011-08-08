<?php

require_once("inc/clients/TransferInterface.php");
require_once("inc/generalfunctions.php");


class TransmissionDaemonTransfer implements TransferInterface
{
	private $data;

	function __construct($data) {
		$this->data = $data;
	}

	function getTransferListItem() {
// TODO: get this moved to a settings class
$cfg['user'] = "administrator";
		// fill in eta
		if ( $this->data['eta'] == '-1' && $this->data['percentDone'] != 1 ) {
			$eta = 'n/a';
		} elseif ( $this->data['percentDone'] == 1 ) {
			$eta = ''; // Download succeeded
		} elseif ( $this->data['eta'] == '-2' ) {
			$eta = 'Unknown';
		} else {
			$eta = convertTime( $this->data['eta'] );
		}

		$status = $this->data['status'];
		switch ($this->data['status']) {
		case 16:
			$transferRunning = false;
			if ( $this->data['percentDone'] >= 1 ) {
				$status = TransferStatus::STATUS_FINISHED;
				$eta = '';
			} else {
				$status = TransferStatus::STATUS_STOPPED;
				$eta = ''; # this might be fixed in a cleaner way // Transfer stopped
				if ( $this->data['downloadedEver'] == 0 ) {
					$status = TransferStatus::STATUS_NEW;
					$eta = '';
				}
			}
			break;
		case 4:
			if ( $this->data['rateDownload'] == 0 ) {
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
			$seeds = getTransmissionSeederCount($this->data['hashString']);
		else
			$seeds = 0;

		// TODO: transferowner is always admin... probably not what we want
		// Suppress error/warning messages(using the @ sign) otherwhise a shitload of warnings are shown
		// Remove the $nothing variables
		$nothing = "";
		$tArray = array(
			'is_owner' => true,
			'transferRunning' => ($transferRunning ? 1 : 0),
			'url_entry' => $this->data['hashString'],
			'hd_image' => getTransmissionStatusImage($this->data['percentDone'], $transferRunning, $seeds, $this->data['rateUpload']),
			'hd_title' => $nothing,
			'displayname' => htmlspecialchars($this->data['name']),
			'transferowner' => getTransmissionTransferOwner($this->data['hashString']),
			'format_af_size' => formatBytesTokBMBGBTB( $this->data['totalSize'] ),
			'format_downtotal' => $nothing,
			'format_uptotal' => $nothing,
			'statusStr' => $status,
			'graph_width' => ( $status==='New' ? -1 : floor($this->data['percentDone']*100) ),
			'percentage' => ( $status==='New' ? '' : floor($this->data['percentDone']*100) . '%' ),
			'progress_color' => '#22BB22',
			'bar_width' => 4,
			'background' => '#000000',
			'100_graph_width' => 100 - floor($this->data['percentDone']*100),
			'down_speed' => formatBytesTokBMBGBTB( $this->data['rateDownload'] ) . '/s',
			'up_speed' => formatBytesTokBMBGBTB( $this->data['rateUpload'] ) . '/s',
			'seeds' => $seeds,
			'peers' => $nothing,
			'estTime' => $eta,
			'clientType' => 'torrent',
			'upload_support_enabled' => 1,
			'client' => 'transmissionrpc',
			'url_path' => urlencode( $cfg['user'] . '/' . $this->data['name'] ),
			'datapath' => htmlspecialchars($this->data['name']),
			'is_no_file' => 1,
			'show_run' => 1,
			'entry' => htmlspecialchars($this->data['name']),
			'downloaded' => formatBytesTokBMBGBTB( $this->data['downloadedEver'] ),
			'start_action' => "headlessaction('dispatcher.php?client=transmission-daemon&action=start&transfer=" . $this->data['hashString'] . "', true, 'Torrent started');",
			'stop_action' => "headlessaction('dispatcher.php?client=transmission-daemon&action=stop&transfer=" . $this->data['hashString'] . "', true, 'Torrent stopped');",
			'delete_with_data_action' => "headlessaction('dispatcher.php?client=transmission-daemon&action=deletewithdata&transfer=" . $this->data['hashString'] . "', true, 'Torrent deleted with data');"
		);

		return $tArray;
	}

	function getActions() {
		$actions =  "<img src=images/delete.png onclick=\"headlessaction('dispatcher.php?client=transmission-daemon&action=delete&transfer=" . $this->data['hashString'] . "', true, 'Torrent deleted');\"> ";
		$actions .= "<img src=images/start.png onclick=\"headlessaction('dispatcher.php?client=transmission-daemon&action=start&transfer=" . $this->data['hashString'] . "', true, 'Torrent started');\"> ";
		$actions .= "<img src=images/stop.png onclick=\"headlessaction('dispatcher.php?client=transmission-daemon&action=stop&transfer=" . $this->data['hashString'] . "', true, 'Torrent stopped');\"> ";
		$actions .= "<img src=images/deletewithdata.png onclick=\"headlessaction('dispatcher.php?client=transmission-daemon&action=deletewithdata&transfer=" . $this->data['hashString'] . "', true, 'Torrent deleted with data');\"> ";

		return $actions;
	}

	function getData() {
		return $this->data;
	}
}

?>
