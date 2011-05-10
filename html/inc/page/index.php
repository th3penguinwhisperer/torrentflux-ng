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

require_once("inc/plugins/transfersource.torrent.php");
require_once("inc/generalfunctions.php");
require_once('inc/singleton/Configuration.php');

$cfg = Configuration::get_instance()->get_cfg();

function printHtml() {
  global $rowArray;

//	<script type="text/javascript" src="js/jquery.tablesorter.js"></script>
// TODO: path is now relative... this might have to be changed to an absolute path
  print( '<html>
<head>
	<script type="text/javascript" src="js/jquery.js"></script> 
	<script type="text/javascript" src="js/popup.js"></script>
	<script type="text/javascript">
pp = new Popup;
	</script>
	<script type="text/javascript" src="js/transferlist.js"></script>
</head>
<body onload="javascript:gettransferlist(); reloadtransferlist();">');

  getPluginUi();

  print('<img onclick="javascript:gettransferlist();">');
  print('<div id=transferlist>');
  print('</div>');

  print( "
</body>
</html>" );
}

printHtml();

?>
