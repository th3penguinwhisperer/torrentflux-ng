<?php

class Configuration
{
	static private $cfg;

	private $data; // Remove if unused
	private $handle;

	private function __construct() {
		$this->handle['transmission_rpc_enable'] = true;
		$this->handle['file_types_array'] = array('.torrent');
		$this->handle["file_types_label"] = ".torrent";
		$this->handle['path'] = "/usr/local/torrentflux/git/"; // make sure it has a trailing slash
		$this->handle["transfer_file_path"] = $this->handle['path'] . ".transfers/";
		$this->handle['upload_limit'] = 100000;
		$this->handle['rss_cache_min'] = 60; // cache time in minutes
		$this->handle['btclient'] = 'transmission-daemon'; // this represents the default torrent client
		$this->handle['rss_cache_path'] = $this->handle['path'] . '.rsscache';
		$this->handle['db_type'] = 'mysql';
		$this->handle['ip'] = $_SESSION['ip'];
		$this->handle['ip_resolved'] = $_SESSION['ip_resolved'];
		$this->handle['user_agent'] = $_SESSION['user_agent'];
		//$this->handle['user_agent'] = 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2.16) Gecko/20110330 Gentoo Firefox/3.6.16 '; // TODO get this removed
		$this->handle['user'] = $_SESSION['user']; // TODO: Should be removed -> is in $_SESSION['user']
		$this->handle['uid'] = $_SESSION['uid']; // TODO: Should be removed -> is in $_SESSION['uid']
		$this->handle['diskusagewarninglevel'] = 90;
		
		$this->handle['constants'] = array(
			'error' => 'ERROR',
			'debug' => 'DEBUG',
			'info' => 'INFO'
		);
	}
   
	static function get_instance()
	{
		if ( ! isset(Configuration::$cfg) )
			Configuration::$cfg = new Configuration();
		return Configuration::$cfg;
	}
 
	function get_cfg()
	{
		return $this->handle;
	}
}

?>
