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

@session_start();

function redirectInvalidLogin() {
  if ( !isset($_SESSION['user']) ) {
    @header("location: login.php");
    exit();
  }
}

redirectInvalidLogin();

// iid-check
if (!isset($_REQUEST["page"])) {
	$_REQUEST["page"] = "index";
}

// include page
if (preg_match('/^[a-zA-Z]+$/D', $_REQUEST["page"])) {
	require_once("inc/page/".$_REQUEST["page"].".php");
} else {
	/* TODO: Get this fixed so we have good error handling */
	//$_iid = tfb_getRequestVar('page');
	//AuditAction($cfg["constants"]["error"], "INVALID PAGE: ".$_iid);
	//@error("Invalid Page", "index.php?iid=index", "Home", array($_iid));
}

?>
