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
require_once('inc/classes/singleton/db.php');
require_once('inc/classes/singleton/Configuration.php');
require_once('inc/generalfunctions.php');

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

function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

// Start session
@session_start();

function loginUser($user, $pass) {
	$db = DB::get_db()->get_handle();
	
  $md5_pass = md5($pass);
  $sql = "select * from tf_users WHERE user_id='" . strtolower($user) . "' AND password='$md5_pass'";
  //$rs = $db->Execute($sql);
  $rs = $db->GetRow($sql);
  addRow("Hash is $md5_pass"); // TODO delete this

  if (sizeof($rs) > 0) {
    $_SESSION['user'] = $user;
    $_SESSION['uid'] = $rs['uid'];
    $_SESSION['ip'] = getRealIpAddr();
    $_SESSION['ip_resolved'] = @gethostbyaddr($_SESSION['ip']);
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    
    $cfg = Configuration::get_instance()->get_cfg();
    AuditAction($cfg["constants"]["info"], $cfg["constants"]["info"], "Successful login", $_SERVER['PHP_SELF']);
  } else {
  	$cfg = Configuration::get_instance()->get_cfg();
    AuditAction($cfg["constants"]["error"], $cfg["constants"]["error"], "Unsuccessful login", $_SERVER['PHP_SELF'], $user);
  }
}

// General Logic //



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
