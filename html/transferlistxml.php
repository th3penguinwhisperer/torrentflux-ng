<?php

session_start();

require_once("inc/generalfunctions.php");
require_once('inc/classes/singleton/Configuration.php');
require_once('inc/plugins/PluginHandler.php');
require_once('inc/lib/vlib/vlibTemplate.php');
require_once('inc/functions.core.tmpl.php');

//$page = $_GET['page']; // get the requested page
//$limit = $_GET['rows']; // get how many rows we want to have into the grid
//$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
//$sord = $_GET['sord']; // get the direction
//if(!$sidx) $sidx =1;
$sidx = 1;
$limit = 5;
$sord = 0;
$page = 0;

$cfg = Configuration::get_instance()->get_cfg();
tmplInitializeInstance($cfg["theme"], "page.transferListXml.tmpl");

$transferlist_torrents = array();

if ($cfg["rewrite_transmission_rpc_enable"]) {
	require_once('inc/clients/transmission-daemon/TransmissionDaemonClient.php');
	$td = new TransmissionDaemonClient();
	$transfers = $td->getTransferList($_SESSION['uid']);

	foreach($transfers as $transfer) {
		array_push($transferlist_torrents, $transfer->getTransferListItem());
	}
}

$count = sizeof($transferlist_torrents);
if( $count ) {
	$total_pages = ceil($count/$limit);
} else {
	$total_pages = 0;
}
if ($page > $total_pages) $page=$total_pages;
$start = $limit*$page - $limit; // do not put $limit*($page - 1)

if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	header("Content-type: application/xhtml+xml;charset=utf-8");
} else {
	header("Content-type: text/xml;charset=utf-8");
}

$tmpl->setvar('transferlist_page', $page);
$tmpl->setvar('transferlist_total_pages', $total_pages);
$tmpl->setvar('transferlist_count', $count);
$tmpl->setvar('transferlist_xmlopen', "<?");

$tmpl->setloop('transferlist_torrents', $transferlist_torrents);
$tmpl->pparse();

?>