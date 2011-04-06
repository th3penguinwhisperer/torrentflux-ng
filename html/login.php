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

// Requires //
require_once("inc/singleton/db.php");


// Functions //
$rowArray = array();

function addRow ( $data ) {
  global $rowArray;
  array_push($rowArray, $data."<br>");
}

function printHtml($html) {
  global $rowArray;

  print( "<html>\n<head></head>\n<body>");

  print($html);

  print( "
</body>
</html>" );
}

/* This is just for testing purposes. TODO: delete this part */
function showLoginRow() {
  global $rowArray;

  foreach ($rowArray as $rowdata) {
    $html .= $rowdata;
  }

  printHtml($html);
}

function showLoginForm($text){
  $formhtml = "$text<br>
<form action=login.php method=post>
<input type=text name=user>
<input type=password name=pass>
<input type=submit name=login>
</form>";
  printHtml($formhtml);
}

function checkSession() {

}

function loginUser($user, $pass) {
	$db = DB::get_db()->get_handle();
	
  $md5_pass = md5($pass);
  $sql = "select * from tf_users WHERE user_id='" . strtolower($user) . "' AND password='$md5_pass'";
  $rs = $db->Execute($sql);
  addRow("Hash is $md5_pass"); // TODO delete this

  if ($rs->_numOfRows == '1') {
    $_SESSION['user'] = $user;
  } else {
    foreach ($rs as $row) {
      $printableRow = print_r($row, true);
      addRow($printableRow);
    }
  }
}

// General Logic //

// Start session
@session_start();

  //unset($_SESSION['user']);
  //exit();


// already got a session ?
if (isset($_SESSION['user'])) {
	@header("location: index.php?page=index");
    unset($_SESSION['user']);
	exit();
}

if ( isset($_REQUEST['user']) && sizeof($_REQUEST['user'])>0 ) {
  $db = DB::get_db()->get_handle();
  loginUser($_REQUEST['user'], $_REQUEST['pass']);
  
  if (isset($_SESSION['user'])) { // logging worked :D
    @header("location: index.php?page=index");
    exit();
  }
  showLoginForm("Login failed: ");
} else {
  showLoginForm("Login: ");
}


// Get user info

//printHtml();

?>
