<?php

require_once("inc/clients/TransferInterface.php");
require_once("inc/generalfunctions.php");
require_once('inc/classes/singleton/Configuration.php');

class TransmissionDaemonTransfer implements TransferInterface
{
	private $data;

	function __construct($data) {
		$this->data = $data;
	}

	function getTransferListItem() {
		$cfg = Configuration::get_instance()->get_cfg();
		
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

		// Statuses are described here: https://trac.transmissionbt.com/browser/trunk/libtransmission/transmission.h
		// typedef enum
		// {
		//    TR_STATUS_STOPPED        = 0, /* Torrent is stopped */
		//    TR_STATUS_CHECK_WAIT     = 1, /* Queued to check files */
		//    TR_STATUS_CHECK          = 2, /* Checking files */
		//    TR_STATUS_DOWNLOAD_WAIT  = 3, /* Queued to download */
		//    TR_STATUS_DOWNLOAD       = 4, /* Downloading */
		//    TR_STATUS_SEED_WAIT      = 5, /* Queued to seed */
		//    TR_STATUS_SEED           = 6  /* Seeding */
		// }
		// tr_torrent_activity;
		$status = $this->data['status'];
		switch ($this->data['status']) {
		case 0:
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
		case 3:
			$status = TransferStatus::STATUS_QUEUED;
			$transferRunning = true;
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
		case 6:
			$status = TransferStatus::STATUS_SEEDING;
			$transferRunning = true;
			break;
		case 2:
			$status = TransferStatus::STATUS_CHECKING;
			$transferRunning = true;
			break;
		case 0:
			$status = TransferStatus::STATUS_STOPPED;
			$transferRunning = false;
			break;
		default:
			$status = TransferStatus::STATUS_UNKNOWN;
			$transferRunning = false;
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
			'displayname' => htmlspecialchars($this->data['name'],ENT_QUOTES),
			'transferowner' => getTransmissionTransferOwner($this->data['hashString']),
			'format_af_size' => formatBytesTokBMBGBTB( $this->data['totalSize'] ),
			'format_downtotal' => formatBytesTokBMBGBTB( $this->data['downloadedEver'] ),
			'format_uptotal' => formatBytesTokBMBGBTB( $this->data['uploadedEver'] ),
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
			'url_path' => urlencode( str_replace($cfg['rewrite_path'], '', $this->data['downloadDir']) . '/' . $this->data['name'] ),
			'datapath' => htmlspecialchars( $this->data['downloadDir'] . '/' . $this->data['name'] ),
			'is_no_file' => 1,
			'show_run' => 1,
			'entry' => addslashes($this->data['name']),
			'downloaded' => formatBytesTokBMBGBTB( $this->data['downloadedEver'] ),
			'uploaded' => formatBytesTokBMBGBTB( $this->data['uploadedEver'] ),
			'details_action' => "loadpopup('Transfer Details', 'dispatcher.php?client=transmission-daemon&amp;action=transfertabs&amp;transfer=" . $this->data['hashString'] . "', ''); centerPopup(); loadPopup();",
			'start_action' => "headlessaction('dispatcher.php?client=transmission-daemon&amp;action=start&amp;transfer=" . $this->data['hashString'] . "', true, 'Torrent started');",
			'stop_action' => "headlessaction('dispatcher.php?client=transmission-daemon&amp;action=stop&amp;transfer=" . $this->data['hashString'] . "', true, 'Torrent stopped');",
			'delete_with_data_action' => "headlessaction('dispatcher.php?client=transmission-daemon&amp;action=deletewithdata&amp;transfer=" . $this->data['hashString'] . "', true, 'Torrent deleted with data');"
		);

		return $tArray;
	}

	function getData() {
		return $this->data;
	}
}

?>
