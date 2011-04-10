<?php

require_once("inc/clients/TransferInterface.php");

class TransmissionDaemonTransfer implements TransferInterface
{
	private $data;

	function __construct($data) {
		$this->data = $data;
	}

	function getTransferListItem() {
		// fill in eta
		if ( $this->data['eta'] == '-1' && $this->data['percentDone'] != 1 ) {
			$eta = 'n/a';
		} elseif ( $this->data['percentDone'] == 1 ) {
			$eta = 'Download Succeeded!';
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
				$eta = 'Torrent Stopped'; # this might be fixed in a cleaner way
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
		$tArray = @array(
			'is_owner' => true,
			'transferRunning' => ($transferRunning ? 1 : 0),
			'url_entry' => $this->data['hashString'],
			'hd_image' => getTransmissionStatusImage($transferRunning, $seeds, $this->data['rateUpload']),
			'hd_title' => $nothing,
			'displayname' => $this->data['name'],
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
			'datapath' => $this->data['name'],
			'is_no_file' => 1,
			'show_run' => 1,
			'entry' => $this->data['name']
		);
		return $tArray;
	}

	function getActions() {
		$actions =  "<a href=\"dispatcher.php?client=transmission-daemon&action=delete&transfer=" . $data['hashString'] . "\">Delete</a> ";
		$actions .= "<a href=\"dispatcher.php?client=transmission-daemon&action=start&transfer=" . $data['hashString'] . "\">Start</a> ";
		$actions .= "<a href=\"dispatcher.php?client=transmission-daemon&action=stop&transfer=" . $data['hashString'] . "\">Stop</a>";
	
		return $actions;
	}


}

?>
