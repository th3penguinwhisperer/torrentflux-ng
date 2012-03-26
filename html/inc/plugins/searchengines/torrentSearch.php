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

// prevent direct invocation
// TODO: remove?
/*if ((!isset($cfg['user'])) || (isset($_REQUEST['cfg']))) {
	@ob_end_clean();
	@header("location: ../../index.php");
	exit();
}
*/

require_once('inc/lib/vlib/vlibTemplate.php');
require_once('inc/functions.core.tmpl.php');

require_once('inc/classes/singleton/Configuration.php');
require_once('inc/generalfunctions.php');
$cfg = Configuration::get_instance()->get_cfg();

/******************************************************************************/

// is enabled ?
// TODO: enable again
//if ($cfg["enable_search"] != 1) {
if (false) {
	AuditAction($cfg["constants"]["error"], "ILLEGAL ACCESS: ".$cfg["user"]." tried to use search");
	@error("search is disabled", "index.php?iid=index", "");
}

// common functions
// Temporarily disabled
//require_once('inc/functions/functions.common.php');

// require
require_once("inc/plugins/searchengines/SearchEngineBase.php");

print_r($_REQUEST);

$pg = tfb_getRequestVar('pg');

$searchEngine = tfb_getRequestVar('searchEngine');
if (empty($searchEngine))
	$searchEngine = "PirateBay";

if (!is_file('inc/plugins/searchengines/'.$searchEngine.'Engine.php')) {
	$tmpl->setvar('sEngine_error', 1);
	$tmpl->setvar('sEngine_msg', "Search Engine not installed.");
} else {
	include_once('inc/plugins/searchengines/'.$searchEngine.'Engine.php');
	$sEngine = new SearchEngine(serialize($cfg));
	if (!$sEngine->initialized) {
		$tmpl->setvar('sEngine_error', 1);
		$tmpl->setvar('sEngine_msg', $sEngine->msg);
	} else {
		doSearch($sEngine);
	}
}
return;

function doSearch($sEngine) {
	// init template-instance
	$cfg = Configuration::get_instance()->get_cfg();
	global $tmpl;
	tmplInitializeInstance($cfg["theme"], "page.torrentSearch.tmpl");
	$tmpl->setloop('Engine_List', tmplSetSearchEngineDDL($searchEngine));

	// if maingenre is not set but subGenre is, then determine maingenre
	if ( !array_key_exists("mainGenre", $_REQUEST) && array_key_exists("subGenre", $_REQUEST) ) {
		getSubCategories($sEngine, $_REQUEST['subGenre']/100);
		$tmpl->setvar('show_subgenre', 1);
	}
		
	// if maingenre is set
	if ( array_key_exists("mainGenre", $_REQUEST) ) {
		getSubCategories($sEngine, $_REQUEST['mainGenre']);
		$tmpl->setvar('show_subgenre', 1);
	}
	$tmpl->setloop('link_list', getCategories($sEngine));

	$tmpl->setvar('show_search', 0);
	// if user wants to browse either by category or by subcategory
	if ( (array_key_exists("subGenre", $_REQUEST) || array_key_exists("mainGenre", $_REQUEST)) && $_REQUEST['query'] == "" ) {
print("latest search");
		$tmpl->setvar('show_search', 1);
		$searchResult = $sEngine->getLatest();
		$tmpl->setvar('performSearch', $searchResult);
	}
	// user actually used query
	elseif( isset($_REQUEST['query']) && !$_REQUEST['query'] == "" ) {
print("normal search");
		$tmpl->setvar('show_search', 1);
		$searchResult = $sEngine->performSearch($_REQUEST['query']);
		$tmpl->setvar('performSearch', $searchResult);
	}

	//
	$tmpl->setvar('_SEARCH', "Search");
	//$tmpl->setvar('_SEARCH', $cfg['_SEARCH']); // TODO get the correct text in the $cfg or another way to fix this
	//
	tmplSetTitleBar("Torrent "."Search"); // TODO get the correct text in the $cfg or another way to fix this. This might not be necessary at all
	//tmplSetTitleBar("Torrent ".$cfg['_SEARCH']);
	tmplSetFoot();
	tmplSetIidVars();

	// parse template
	global $pageContent;
	$pageContent = $tmpl->grab();

}

function getCategories($sEngine) {
	$link_list = array();
	foreach ($sEngine->getMainCategories() as $mainId => $mainName) {
		array_push($link_list, array(
			'searchEngine' => $sEngine->engineName,
			'mainId' => $mainId,
			'mainName' => $mainName
			)
		);
	}

	return $link_list;
}

function getSubCategories($sEngine, $mainGenre) {
	global $tmpl;

	$mainGenreName = $sEngine->GetMainCatName($mainGenre);
	$subCats = $sEngine->getSubCategories($mainGenre);
	$tmpl->setvar('mainGenreName', $mainGenreName);
	$list_cats = array();
	foreach ($subCats as $subId => $subName) {
		array_push($list_cats, array(
			'subId' => $subId,
			'subName' => $subName
			)
		);
	}
	$tmpl->setloop('list_cats', $list_cats);

}


// Go get the if this is a search request. go get the data and produce output.
$hideSeedless = tfb_getRequestVar('hideSeedless');
if (!empty($hideSeedless))
	$_SESSION['hideSeedless'] = $hideSeedless;
if (!isset($_SESSION['hideSeedless']))
	$_SESSION['hideSeedless'] = 'no';
$hideSeedless = $_SESSION['hideSeedless'];
$pg = tfb_getRequestVar('pg');
	//$searchEngine = $cfg["searchEngine"];
if (!preg_match('/^[a-zA-Z0-9]+$/D', $searchEngine))
	error("Invalid SearchEngine", "", "");
$searchterm = tfb_getRequestVar('searchterm');
if (empty($searchterm))
	$searchterm = tfb_getRequestVar('query');
$searchterm = str_replace(" ", "+",$searchterm);
if (empty($searchterm)) {
	// no searchterm set the get latest flag.
	$_REQUEST["LATEST"] = "1";
}
$tmpl->setvar('searchterm', str_replace("+", " ",$searchterm));
$tmpl->setloop('Engine_List', tmplSetSearchEngineDDL($searchEngine));
$tmpl->setvar('searchEngine', $searchEngine);
// Check if Search Engine works properly
if (!is_file('inc/plugins/searchengines/'.$searchEngine.'Engine.php')) {
	$tmpl->setvar('sEngine_error', 1);
	$tmpl->setvar('sEngine_msg', "Search Engine not installed.");
} else {
	include_once('inc/plugins/searchengines/'.$searchEngine.'Engine.php');
	$sEngine = new SearchEngine(serialize($cfg));
	if (!$sEngine->initialized) {
		$tmpl->setvar('sEngine_error', 1);
		$tmpl->setvar('sEngine_msg', $sEngine->msg);
	} else {
		// Search Engine ready to go
		$mainStart = true;
		$catLinks = '';
		$tmpCatLinks = '';
		$tmpLen = 0;
		$link_list = array();
		foreach ($sEngine->getMainCategories() as $mainId => $mainName) {
			array_push($link_list, array(
				'searchEngine' => $searchEngine,
				'mainId' => $mainId,
				'mainName' => $mainName
				)
			);
		}
		$tmpl->setloop('link_list', $link_list);
		$mainGenre = tfb_getRequestVar('mainGenre');
		$subCats = $sEngine->getSubCategories($mainGenre);
		if ((empty($mainGenre) && array_key_exists("subGenre", $_REQUEST)) || (count($subCats) <= 0)) {
			$tmpl->setvar('no_genre', 1);
			$tmpl->setvar('performSearch', (array_key_exists("LATEST", $_REQUEST) && $_REQUEST["LATEST"] == "1")
				? $sEngine->performSearch($searchterm)
				: $sEngine->performSearch($searchterm)
			);
		} else {
			$mainGenreName = $sEngine->GetMainCatName($mainGenre);
			$tmpl->setvar('mainGenreName', $mainGenreName);
			$list_cats = array();
			foreach ($subCats as $subId => $subName) {
				array_push($list_cats, array(
					'subId' => $subId,
					'subName' => $subName
					)
				);
			}
			$tmpl->setloop('list_cats', $list_cats);
		}
	}
}
//
$tmpl->setvar('_SEARCH', "Search");
//$tmpl->setvar('_SEARCH', $cfg['_SEARCH']); // TODO get the correct text in the $cfg or another way to fix this
//
tmplSetTitleBar("Torrent "."Search"); // TODO get the correct text in the $cfg or another way to fix this. This might not be necessary at all
//tmplSetTitleBar("Torrent ".$cfg['_SEARCH']);
tmplSetFoot();
tmplSetIidVars();

// parse template
global $pageContent;
$pageContent = $tmpl->grab();

function getPage() {
	global $pageContent;
	return $pageContent;
}

?>
