<?php

  @session_start();

  require_once('inc/plugins/PluginHandler.php');

if ( empty($_REQUEST['action']) ) $action = "get"; 
else $action = $_REQUEST['action'];

if( $action == "get" ) {
  $ph = new PluginHandler();
  $pluginNames = $ph->getEnabledPlugins();
  foreach( $pluginNames as $plugin ) {
    print("$plugin[0]<br>");
    if( $plugin[0] == "rss-transfers" ) { // TODO: this is only temporarily until other plugins are implementing from PluginInterface as well
      $pi = $ph->getPlugin($plugin[0]);
      $pi->getConfiguration();
    }
  }
}

if ( $action == "set" ) {

  $pluginname = $_REQUEST['plugin'];

  $ph = new PluginHandler();
  $pi = $ph->getPlugin($pluginname);
  $pi->setConfiguration($_REQUEST);

  header('Location: configure.php');
}

?>
