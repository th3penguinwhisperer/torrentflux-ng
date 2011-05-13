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

/******************************************************************************/

// general function
require_once('inc/generalfunctions.php');

// readrss functions
require_once('inc/plugins/rss/functions.readrss.php');

// require
require_once("inc/plugins/rss/lastRSS.php");

require_once('inc/singleton/Configuration.php');
$cfg = Configuration::get_instance()->get_cfg();

// Just to be safe ;o)
if (!defined("ENT_COMPAT")) define("ENT_COMPAT", 2);
if (!defined("ENT_NOQUOTES")) define("ENT_NOQUOTES", 0);
if (!defined("ENT_QUOTES")) define("ENT_QUOTES", 3);

// Get RSS feeds from Database
$arURL = GetRSSLinks();

// create lastRSS object
$rss = new lastRSS();

// setup transparent cache
$cacheDir = $cfg['rss_cache_path'];

if (!checkDirectory($cacheDir, 0777)) {
	//@error("Error with rss-cache-dir", "index.php?page=index", "", array($cacheDir));
	print("The rss_cache_path does not exist: " . $cfg['rss_cache_path']);
	exit();
}
$rss->cache_dir = $cacheDir;
$rss->cache_time = $cfg["rss_cache_min"] * 60; // 1200 = 20 min.  3600 = 1 hour
$rss->strip_html = false; // don't remove HTML from the description
$rss->CDATA = 'strip'; // TODO: these variables should be defined by default in lastRSS. Some of them are used in the code but not initialized by the class

// set vars
// Loop through each RSS feed
$rss_list = array();
foreach ($arURL as $rid => $url) {
	if (isset($_REQUEST["debug"]))
		$rss->cache_time=0;
	$rs = $rss->Get($url);
	if ($rs !== false) {
		if (!empty( $rs["items"])) {
			// Check this feed has a title tag:
			if (!isset($rs["title"]) || empty($rs["title"]))
				$rs["title"] = "Feed URL ".htmlentities($url, ENT_QUOTES)." Note: this feed does not have a valid 'title' tag";

			// Check each item in this feed has link, title and publication date:
			for ($i=0; $i < count($rs["items"]); $i++) {
				// Don't include feed items without a link:
				if (!isset($rs["items"][$i]["link"]) || empty($rs["items"][$i]["link"])){
					array_splice ($rs["items"], $i, 1);
					// Continue to next feed item:
					continue;
				}

				// Set the label for the link title (<a href="foo" title="$label">)
				$rs["items"][$i]["label"] = $rs["items"][$i]["title"];

				// Check item's pub date:
				if (!isset($rs["items"][$i]["pubDate"]) || empty($rs["items"][$i]["pubDate"]))
					$rs["items"][$i]["pubDate"] = "Unknown publication date";

				// Check item's title:
				if (!isset($rs["items"][$i]["title"]) || empty($rs["items"][$i]["title"])) {
					// No title found for this item, create one from the link:
					$link = html_entity_decode($rs["items"][$i]["link"]);
					if (strlen($link) >= 45)
						$link = substr($link, 0, 42)."...";
					$rs["items"][$i]["title"] = "Unknown feed item title: $link";
				} elseif(strlen($rs["items"][$i]["title"]) >= 67){
					// if title string is longer than 70, truncate it:
					// Note this is a quick hack, link titles will also be truncated as well
					// as the feed's display title in the table.
					$rs["items"][$i]["title"] = substr($rs["items"][$i]["title"], 0, 64)."...";
				}
				// decode html entities like &amp; -> & , and then uri_encode them them & -> %26
				// This is needed to get Urls with more than one GET Parameter working
				$rs["items"][$i]["link"] = rawurlencode(html_entity_decode($rs["items"][$i]["link"]));
			}
			$stat = 1;
		} else {
			// feed URL is valid and active, but no feed items were found:
			$stat = 2;
		}
	} else {
		// Unable to grab RSS feed, must of timed out
		$stat = 3;
	}
	array_push($rss_list, array(
		'stat' => $stat,
		'rid' => $rid,
		'title' => (isset($rs["title"]) ? $rs["title"] : ""),
		'url' => $url,
		'feedItems' => $rs['items']
		)
	);
}

//print_r($rss_list);

// TODO: remove this, just to point out the array structure
//Array
//(
//    [0] => Array
//        (
//            [stat] => 1
//            [rid] => 0
//            [title] => ezRSS - Search Results
//            [url] => http://www.ezrss.it/search/index.php?show_name=family+guy&date=&quality=&release_group=&mode=rss
//            [feedItems] => Array
//                (
//                    [0] => Array
//                        (
//                            [title] => <![CDATA[Family Guy 9x17 [HDTV - REPACK - 2HD]]]>
//                            [link] => http%3A%2F%2Ftorrent.zoink.it%2FFamily.Guy.S09E17.REPACK.HDTV.XviD-2HD.%5Beztv%5D.torrent
//                            [description] => <![CDATA[Show Name: Family Guy; Episode Title: N/A; Season: 9; Episode: 17]]>
//                            [category] => <![CDATA[TV Show / Family Guy]]>
//                            [comments] => http://eztv.it/forum/discuss/27290/
//                            [guid] => http://eztv.it/ep/27290/family-guy-s09e17-repack-hdtv-xvid-2hd/
//                            [pubDate] => Sun, 08 May 2011 21:01:22 -0500
//                            [label] => <![CDATA[Family Guy 9x17 [HDTV - REPACK - 2HD]]]>
//                        )



function getRssList($rss_list)
{
	print('
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript">
function addRssTransfer(url) 
{
    // get other values
    var client = $("#client").val();
    var subaction = $("#subaction").val();

    // validate and process form here
    var dataString = \'url=\' + url + \'&client=\' + client + \'&action=add\' + \'&subaction=\' + subaction;

    $.ajax({
      type: "POST",
      url: "dispatcher.php",
      data: dataString,
      success: function() {
        $("#status_message").html("New transfer is added");
        $("#status_message").show();
        var refreshId = setTimeout(
            function() {
                $("#status_message").val("");
				$("#status_message").hide();
                $("#url").val("");
            }, 
            5000
        );
        gettransferlist();
      }
    });
}

</script>
'); // get this in a seperate javascript file
	
	foreach($rss_list as $rss_source)
	{
		print("<img src=\"images/rss.png\">RSS Title: " . $rss_source['title'] . "<br>\n");
	
		print('<table>');
		foreach($rss_source['feedItems'] as $feedItem)
		{
			print("<tr>");
			print("<td><img src=\"images/add.png\" onclick=\"javascript:addRssTransfer('" . $feedItem['link'] . "');\">".$feedItem['title']."</td>");
			print("</tr>");
		}
		print('</table>');
	}

}

?>
