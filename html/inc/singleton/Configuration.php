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
		$this->handle['upload_limit'] = 500000;
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
		$this->handle['enable_shared_downloads'] = true;

		if ( $this->handle['uid'] == 1 ) // TODO: use other method to define an admin user
			$this->handle['isAdmin'] = true;
		else
			$this->handle['isAdmin'] = false;
		
		$this->handle['enable_home_dirs'] = '1';
		if ( $this->handle['enable_home_dirs'] == '1' )
			$this->handle['download_path'] = $this->handle['path'] . $this->handle['user'];
		else
			$this->handle['download_path'] = $this->handle['path'] . 'incoming';
		
		$this->handle['constants'] = array(
			'error' => 'ERROR',
			'debug' => 'DEBUG',
			'info' => 'INFO'
		);

		// Temporarily added for testing dir page through ajax
		$this->handle['ui_dim_main_w'] = "";
		$this->handle['theme'] = "RedRound";
		$this->handle['ui_displayfluxlink'] = "";
		$this->handle['dir_public_read'] = "";
		$this->handle['dir_public_write'] = "";
		$this->handle['enable_tmpl_cache'] = "";
		$this->handle['enable_dirstats'] = "";
		$this->handle['enable_view_nfo'] = "";
		$this->handle['enable_rar'] = "";
		$this->handle['enable_sfvcheck'] = "";
		$this->handle['dir_enable_chmod'] = "";
		$this->handle['enable_rename'] = "";
		$this->handle['enable_move'] = "";
		$this->handle['enable_vlc'] = "";
		$this->handle['enable_file_download'] = "";
		$this->handle['enable_maketorrent'] = "";
		$this->handle['drivespacebar'] = "";
		$this->handle['driveSpace'] = "";
		$this->handle['_CHARSET'] = "";
		$this->handle['freeSpaceFormatted'] = "";
		$this->handle['hit'] = "";
		$this->handle['_DIRECTORYLIST'] = "";
		$this->handle['_TORRENTS'] = "";
		$this->handle['_UPLOADHISTORY'] = "";
		$this->handle['_MYPROFILE'] = "";
		$this->handle['_MESSAGES'] = "";
		$this->handle['_ADMINISTRATION'] = "";
		$this->handle['_STORAGE'] = "";
		$this->handle['_RETURNTOTRANSFERS'] = "";
		$this->handle['_ABOUTTODELETE'] = "";
		$this->handle['_BACKTOPARRENT'] = "";
		$this->handle['_DELETE'] = "";
		$this->handle['pagetitle'] = "";
		$this->handle['version'] = "";
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
