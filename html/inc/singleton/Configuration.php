<?php

require_once('inc/singleton/db.php');

class Configuration
{
	static private $cfg;

	private $data; // Remove if unused
	private $handle;

	private function __construct() {
		$db = DB::get_db()->get_handle();

		$sql = "SELECT * FROM tf_settings WHERE tf_key LIKE 'rewrite_%'";
		$settings_array = $db->GetAssoc($sql);
		if ($db->ErrorNo() != 0) dbError($sql);
		
		foreach ($settings_array as $key => $value) {
			$this->handle[$key] = unserialize($value);
		}
		$this->handle['ip'] = $_SESSION['ip'];
		$this->handle['ip_resolved'] = $_SESSION['ip_resolved'];
		$this->handle['user_agent'] = $_SESSION['user_agent'];
		//$this->handle['user_agent'] = 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2.16) Gecko/20110330 Gentoo Firefox/3.6.16 '; // TODO get this removed
		$this->handle['user'] = $_SESSION['user']; // TODO: Should be removed -> is in $_SESSION['user']
		$this->handle['uid'] = $_SESSION['uid']; // TODO: Should be removed -> is in $_SESSION['uid']

		if ( $this->handle['rewrite_enable_home_dirs'] == '1' )
			$this->handle['rewrite_download_path'] = $this->handle['rewrite_path'] . $this->handle['user'];
		else
			$this->handle['rewrite_download_path'] = $this->handle['rewrite_path'] . $this->handle['rewrite_shared_download_dir'];
		
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
