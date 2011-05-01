<?php

class Configuration
{
	static private $cfg;

	private $data; // Remove if unused
	private $handle;

	private function __construct() {
		$this->handle['uid'] = 0;
		$this->handle['transmission_rpc_enable'] = true;
		$this->handle['file_types_array'] = array('.torrent');
		$this->handle["file_types_label"] = ".torrent";
		$this->handle["transfer_file_path"] = "/usr/local/torrentflux/git/.transfers/";
		$this->handle['path'] = "/usr/local/torrentflux/git/"; // make sure it has a trailing slash
		$this->handle['user'] = "administrator";
		$this->handle['upload_limit'] = 100000;
		$this->handle['btclient'] = 'transmission-daemon'; // this represents the default torrent client
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
