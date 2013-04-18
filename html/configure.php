<?php

  @session_start();

  require_once('inc/generalfunctions.php');
  redirect_unallowed_visitor();
  
  require_once('inc/plugins/PluginHandler.php');

if ( empty($_REQUEST['action']) ) $action = "get"; 
else $action = $_REQUEST['action'];

if( $action == "get" ) {
  $ph = new PluginHandler();
  
  print("==== Enabled plugins ===<br>");
  $pluginNames = $ph->getEnabledPlugins();
  foreach( $pluginNames as $plugin ) {
    getConfigurationUi($ph, $plugin);
    print("<hr>");
  }

  print("=== Disabled plugins ===<br>");
  $pluginNames = $ph->getDisabledPlugins();
  foreach( $pluginNames as $plugin ) {
    getConfigurationUi($ph, $plugin);
    print("<hr>");
  }
}

function getConfigurationUi($ph, $plugin)
{
    print("$plugin[1] ");
    
    if ($plugin[4] == 1) // TODO: if enabled
      print("<a href=\"configure.php?action=disable&plugin=$plugin[0]\"><img src=images/disable.png></a><br>");
    else
      print("<a href=\"configure.php?action=enable&plugin=$plugin[0]\"><img src=images/enable.png></a><br>");

    $pi = $ph->getPluginClass($plugin[0]);
    if ( class_exists($pi) ) {
      $pi::getConfiguration();
    }

}

if ( $action == "set" ) {

  $pluginname = $_REQUEST['plugin'];

  $ph = new PluginHandler();
  $pi = $ph->getPlugin($pluginname);
  $pi->setConfiguration($_REQUEST);
}

if ( $action == "enable" ) {
  $pluginname = $_REQUEST['plugin'];

  $ph = new PluginHandler();
  $ph->enablePlugin($_REQUEST['plugin']);
}

if ( $action == "disable" ) {
  $pluginname = $_REQUEST['plugin'];

  $ph = new PluginHandler();
  $ph->disablePlugin($_REQUEST['plugin']);

}

if ( $action != "get" )
  header('Location: configure.php');


?>
