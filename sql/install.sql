SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Table structure for table `tf_cookies`
--

CREATE TABLE IF NOT EXISTS `tf_cookies` (
  `cid` int(10) NOT NULL auto_increment,
  `uid` int(10) NOT NULL default '0',
  `host` varchar(255) default NULL,
  `data` varchar(255) default NULL,
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tf_cookies`
--


-- --------------------------------------------------------

--
-- Table structure for table `tf_links`
--

CREATE TABLE IF NOT EXISTS `tf_links` (
  `lid` int(10) NOT NULL auto_increment,
  `url` varchar(255) NOT NULL default '',
  `sitename` varchar(255) NOT NULL default 'Old Link',
  `sort_order` tinyint(3) unsigned default '0',
  PRIMARY KEY  (`lid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tf_links`
--

INSERT INTO `tf_links` (`lid`, `url`, `sitename`, `sort_order`) VALUES
(1, 'http://www.torrentflux-ng.org/', 'TorrentFlux-NG', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tf_log`
--

CREATE TABLE IF NOT EXISTS `tf_log` (
  `cid` int(14) NOT NULL auto_increment,
  `user_id` varchar(32) NOT NULL default '',
  `file` varchar(200) NOT NULL default '',
  `action` varchar(200) NOT NULL default '',
  `level` varchar(15) NOT NULL,
  `message` varchar(500) NOT NULL,
  `ip` varchar(15) NOT NULL default '',
  `ip_resolved` varchar(200) NOT NULL default '',
  `user_agent` varchar(200) NOT NULL default '',
  `time` varchar(14) NOT NULL default '0',
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=155691 ;

-- --------------------------------------------------------

--
-- Table structure for table `tf_messages`
--

CREATE TABLE IF NOT EXISTS `tf_messages` (
  `mid` int(10) NOT NULL auto_increment,
  `to_user` varchar(32) NOT NULL default '',
  `from_user` varchar(32) NOT NULL default '',
  `message` text,
  `IsNew` int(11) default NULL,
  `ip` varchar(15) NOT NULL default '',
  `time` varchar(14) NOT NULL default '0',
  `force_read` tinyint(1) default '0',
  PRIMARY KEY  (`mid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=345 ;

-- --------------------------------------------------------

--
-- Table structure for table `tf_plugins`
--

CREATE TABLE IF NOT EXISTS `tf_plugins` (
  `pluginid` int(11) NOT NULL auto_increment,
  `plugintype` enum('transfersource','transferclient') NOT NULL,
  `pluginname` varchar(50) NOT NULL,
  `plugindisplayname` varchar(50) NOT NULL,
  `plugininclude` varchar(100) NOT NULL,
  `pluginclass` varchar(50) NOT NULL,
  `pluginenabled` tinyint(1) NOT NULL,
  `pluginconfigured` tinyint(1) NOT NULL,
  `pluginorder` int(3) NOT NULL,
  PRIMARY KEY  (`pluginid`),
  UNIQUE KEY `pluginname` (`pluginname`,`plugininclude`,`pluginclass`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `tf_plugins`
--

INSERT INTO `tf_plugins` (`pluginid`, `plugintype`, `pluginname`, `plugindisplayname`, `plugininclude`, `pluginclass`, `pluginenabled`, `pluginconfigured`, `pluginorder`) VALUES
(1, 'transfersource', 'rss-transfers', 'RSS Tranfers', 'inc/plugins/rss/readrss.php', 'RssReader', 1, 1, 1),
(2, 'transfersource', 'basictransferadd', 'Url + Upload', 'inc/plugins/basictransferadd/basictransferadd.php', 'BasicTransferAdd', 1, 1, 0),
(3, 'transferclient', 'transmission-daemon', 'Transmission-daemon', 'inc/clients/transmission-daemon/TransmissionDaemonClient.php', 'TransmissionDaemonClient', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tf_rss`
--

CREATE TABLE IF NOT EXISTS `tf_rss` (
  `rid` int(10) NOT NULL auto_increment,
  `url` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`rid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `tf_settings`
--

CREATE TABLE IF NOT EXISTS `tf_settings` (
  `tf_key` varchar(255) NOT NULL default '',
  `tf_value` text NOT NULL,
  PRIMARY KEY  (`tf_key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tf_settings`
--

INSERT INTO `tf_settings` (`tf_key`, `tf_value`) VALUES
('path', '/usr/local/torrentflux/git/'),
('max_upload_rate', '10'),
('max_download_rate', '0'),
('max_uploads', '4'),
('minport', '49160'),
('maxport', '49300'),
('superseeder', '0'),
('rerequest_interval', '1800'),
('enable_search', '1'),
('show_server_load', '1'),
('loadavg_path', '/proc/loadavg'),
('days_to_keep', '30'),
('minutes_to_keep', '3'),
('rss_cache_min', '20'),
('page_refresh', '60'),
('default_theme', 'default'),
('default_language', 'lang-english.php'),
('debug_sql', '1'),
('die_when_done', 'False'),
('sharekill', '0'),
('pythonCmd', '/usr/local/bin/python'),
('searchEngine', 'TorrentSpy'),
('TorrentSpyGenreFilter', 'a:1:{i:0;s:0:"";}'),
('TorrentBoxGenreFilter', 'a:1:{i:0;s:0:"";}'),
('TorrentPortalGenreFilter', 'a:1:{i:0;s:0:"";}'),
('enable_metafile_download', '1'),
('enable_file_priority', '1'),
('searchEngineLinks', 'a:5:{s:7:"isoHunt";s:11:"isohunt.com";s:7:"NewNova";s:11:"newnova.org";s:10:"TorrentBox";s:14:"torrentbox.com";s:13:"TorrentPortal";s:17:"torrentportal.com";s:10:"TorrentSpy";s:14:"torrentspy.com";}'),
('maxcons', '40'),
('showdirtree', '1'),
('maxdepth', '0'),
('enable_multiops', '1'),
('enable_wget', '2'),
('enable_multiupload', '1'),
('enable_xfer', '1'),
('enable_public_xfer', '1'),
('btclient', 'transmission'),
('btclient_tornado_options', ''),
('btclient_transmission_bin', '/usr/local/bin/transmissioncli'),
('btclient_transmission_options', ''),
('metainfoclient', 'btshowmetainfo.py'),
('enable_restrictivetview', '1'),
('perlCmd', '/usr/bin/perl'),
('ui_displayfluxlink', '1'),
('ui_dim_main_w', '900'),
('enable_bigboldwarning', '1'),
('enable_goodlookstats', '1'),
('ui_displaylinks', '1'),
('ui_displayusers', '1'),
('xfer_total', '0'),
('xfer_month', '0'),
('xfer_week', '0'),
('xfer_day', '0'),
('enable_bulkops', '1'),
('week_start', 'Monday'),
('month_start', '1'),
('hack_multiupload_rows', '6'),
('hack_goodlookstats_settings', '63'),
('enable_dereferrer', '1'),
('auth_type', '1'),
('index_page_connections', '1'),
('index_page_stats', '1'),
('index_page_sortorder', 'dd'),
('index_page_settings', '1266'),
('nice_adjust', '0'),
('xfer_realtime', '1'),
('skiphashcheck', '0'),
('enable_umask', '0'),
('enable_sorttable', '1'),
('drivespacebar', 'tf'),
('debuglevel', '0'),
('docroot', '/usr/local/www/data-dist/nonssl/git/torrentflux/html/'),
('enable_index_ajax_update_silent', '0'),
('enable_index_ajax_update_users', '1'),
('wget_ftp_pasv', '0'),
('wget_limit_retries', '3'),
('wget_limit_rate', '0'),
('enable_index_ajax_update_title', '1'),
('enable_index_ajax_update_list', '1'),
('enable_index_meta_refresh', '0'),
('enable_index_ajax_update', '0'),
('index_ajax_update', '10'),
('transferStatsType', 'ajax'),
('transferStatsUpdate', '5'),
('auth_basic_realm', 'torrentflux-b4rt'),
('servermon_update', '5'),
('enable_home_dirs', '1'),
('path_incoming', 'incoming'),
('enable_tmpl_cache', '0'),
('btclient_mainline_options', ''),
('bandwidthbar', 'tf'),
('display_seeding_time', '1'),
('ui_displaybandwidthbars', '1'),
('bandwidth_down', '10240'),
('bandwidth_up', '10240'),
('webapp_locked', '0'),
('enable_btclient_chooser', '1'),
('transfer_profiles', '3'),
('transfer_customize_settings', '2'),
('transferHosts', '0'),
('pagetitle', 'torrentflux-b4rt'),
('enable_sharekill', '1'),
('transfer_window_default', 'transferStats'),
('index_show_seeding', '1'),
('enable_personal_settings', '1'),
('enable_nzbperl', '0'),
('nzbperl_badAction', '0'),
('nzbperl_server', ''),
('nzbperl_user', ''),
('nzbperl_pw', ''),
('nzbperl_threads', '0'),
('nzbperl_conn', '1'),
('nzbperl_rate', '0'),
('nzbperl_create', '0'),
('nzbperl_options', ''),
('fluazu_host', 'localhost'),
('fluazu_port', '6884'),
('fluazu_secure', '0'),
('fluazu_user', ''),
('fluazu_pw', ''),
('fluxd_dbmode', 'php'),
('fluxd_loglevel', '0'),
('fluxd_Fluxinet_enabled', '0'),
('fluxd_Qmgr_enabled', '0'),
('fluxd_Rssad_enabled', '0'),
('fluxd_Watch_enabled', '0'),
('fluxd_Trigger_enabled', '0'),
('fluxd_Maintenance_enabled', '0'),
('fluxd_Fluxinet_port', '3150'),
('fluxd_Qmgr_interval', '15'),
('fluxd_Qmgr_maxTotalTransfers', '5'),
('fluxd_Qmgr_maxUserTransfers', '2'),
('fluxd_Rssad_interval', '1800'),
('fluxd_Rssad_jobs', ''),
('fluxd_Watch_interval', '120'),
('fluxd_Watch_jobs', ''),
('fluxd_Maintenance_interval', '600'),
('fluxd_Maintenance_trestart', '0'),
('fluxd_Trigger_interval', '600'),
('bin_grep', '/usr/bin/grep'),
('bin_netstat', '/usr/bin/netstat'),
('bin_php', '/usr/local/bin/php'),
('bin_awk', '/usr/bin/awk'),
('bin_du', '/usr/bin/du'),
('bin_wget', '/usr/local/bin/wget'),
('bin_unrar', '/usr/local/bin/unrar'),
('bin_unzip', '/usr/local/bin/unzip'),
('bin_cksfv', '/usr/local/bin/cksfv'),
('bin_sockstat', '/usr/bin/sockstat'),
('bin_vlc', '/usr/local/bin/vlc'),
('bin_uudeview', '/usr/local/bin/uudeview'),
('enable_torrent', '2'),
('vuze_rpc_host', ''),
('btclient_transmission_enable', '1'),
('nzbperl_ssl', '0'),
('nzbperl_port', ''),
('transmission_rpc_enable', '1'),
('vuze_rpc_enable', '0'),
('vuze_rpc_port', ''),
('vuze_rpc_user', ''),
('vuze_rpc_password', ''),
('transmission_rpc_host', '127.0.0.1'),
('transmission_rpc_port', '9091'),
('transmission_rpc_user', ''),
('transmission_rpc_password', ''),
('server_name', 'http://192.168.1.10'),
('server_root', '/git/torrentflux/html/');

-- --------------------------------------------------------

--
-- Table structure for table `tf_settings_dir`
--

CREATE TABLE IF NOT EXISTS `tf_settings_dir` (
  `tf_key` varchar(255) NOT NULL default '',
  `tf_value` text NOT NULL,
  PRIMARY KEY  (`tf_key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tf_settings_dir`
--

INSERT INTO `tf_settings_dir` (`tf_key`, `tf_value`) VALUES
('dir_public_read', '1'),
('dir_public_write', '0'),
('dir_enable_chmod', '1'),
('enable_dirstats', '1'),
('enable_maketorrent', '1'),
('dir_maketorrent_default', 'tornado'),
('enable_file_download', '1'),
('enable_view_nfo', '1'),
('package_type', 'tar'),
('enable_sfvcheck', '1'),
('enable_rar', '1'),
('enable_move', '0'),
('enable_rename', '1'),
('move_paths', ''),
('dir_restricted', 'lost+found:CVS:Temporary Items:Network Trash Folder:TheVolumeSettingsFolder'),
('enable_vlc', '1'),
('vlc_port', '8080');

-- --------------------------------------------------------

--
-- Table structure for table `tf_settings_stats`
--

CREATE TABLE IF NOT EXISTS `tf_settings_stats` (
  `tf_key` varchar(255) NOT NULL default '',
  `tf_value` text NOT NULL,
  PRIMARY KEY  (`tf_key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tf_settings_stats`
--

INSERT INTO `tf_settings_stats` (`tf_key`, `tf_value`) VALUES
('stats_enable_public', '0'),
('stats_show_usage', '1'),
('stats_deflate_level', '9'),
('stats_txt_delim', ';'),
('stats_default_header', '0'),
('stats_default_type', 'all'),
('stats_default_format', 'xml'),
('stats_default_attach', '0'),
('stats_default_compress', '0');

-- --------------------------------------------------------

--
-- Table structure for table `tf_settings_user`
--

CREATE TABLE IF NOT EXISTS `tf_settings_user` (
  `uid` int(10) NOT NULL,
  `tf_key` varchar(255) NOT NULL default '',
  `tf_value` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tf_settings_user`
--


-- --------------------------------------------------------

--
-- Table structure for table `tf_transfers`
--

CREATE TABLE IF NOT EXISTS `tf_transfers` (
  `transfer` varchar(255) NOT NULL default '',
  `type` enum('torrent','wget','nzb') NOT NULL default 'torrent',
  `client` enum('tornado','transmission','mainline','azureus','wget','nzbperl') NOT NULL default 'tornado',
  `hash` varchar(40) NOT NULL default '',
  `datapath` varchar(255) NOT NULL default '',
  `savepath` varchar(255) NOT NULL default '',
  `running` enum('0','1') NOT NULL default '0',
  `rate` smallint(4) NOT NULL default '0',
  `drate` smallint(4) NOT NULL default '0',
  `maxuploads` tinyint(3) unsigned NOT NULL default '0',
  `superseeder` enum('0','1') NOT NULL default '0',
  `runtime` enum('True','False') NOT NULL default 'False',
  `sharekill` smallint(4) unsigned NOT NULL default '0',
  `minport` smallint(5) unsigned NOT NULL default '0',
  `maxport` smallint(5) unsigned NOT NULL default '0',
  `maxcons` smallint(4) unsigned NOT NULL default '0',
  `rerequest` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`transfer`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tf_transfers`
--

-- --------------------------------------------------------

--
-- Table structure for table `tf_transfer_totals`
--

CREATE TABLE IF NOT EXISTS `tf_transfer_totals` (
  `tid` varchar(40) NOT NULL default '',
  `uptotal` bigint(80) NOT NULL default '0',
  `downtotal` bigint(80) NOT NULL default '0',
  PRIMARY KEY  (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tf_transfer_totals`
--

-- --------------------------------------------------------

--
-- Table structure for table `tf_transmission_user`
--

CREATE TABLE IF NOT EXISTS `tf_transmission_user` (
  `uid` int(10) NOT NULL COMMENT 'This is the user id from tf_users',
  `tid` varchar(40) NOT NULL COMMENT 'This is the transfer id from tf_transfers'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tf_transmission_user`
--

-- --------------------------------------------------------

--
-- Table structure for table `tf_trprofiles`
--

CREATE TABLE IF NOT EXISTS `tf_trprofiles` (
  `id` mediumint(8) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `owner` int(10) NOT NULL default '0',
  `public` enum('0','1') NOT NULL default '0',
  `rate` smallint(4) NOT NULL default '0',
  `drate` smallint(4) NOT NULL default '0',
  `maxuploads` tinyint(3) unsigned NOT NULL default '0',
  `superseeder` enum('0','1') NOT NULL default '0',
  `runtime` enum('True','False') NOT NULL default 'False',
  `sharekill` smallint(4) unsigned NOT NULL default '0',
  `minport` smallint(5) unsigned NOT NULL default '0',
  `maxport` smallint(5) unsigned NOT NULL default '0',
  `maxcons` smallint(4) unsigned NOT NULL default '0',
  `rerequest` mediumint(8) unsigned NOT NULL default '0',
  `savepath` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tf_trprofiles`
--


-- --------------------------------------------------------

--
-- Table structure for table `tf_users`
--

CREATE TABLE IF NOT EXISTS `tf_users` (
  `uid` int(10) NOT NULL auto_increment,
  `user_id` varchar(32) character set latin1 collate latin1_bin NOT NULL default '',
  `password` varchar(34) NOT NULL default '',
  `hits` int(10) NOT NULL default '0',
  `last_visit` varchar(14) NOT NULL default '0',
  `time_created` varchar(14) NOT NULL default '0',
  `user_level` tinyint(1) NOT NULL default '0',
  `hide_offline` tinyint(1) NOT NULL default '0',
  `theme` varchar(100) NOT NULL default 'default',
  `language_file` varchar(60) default 'lang-english.php',
  `state` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `tf_users`
--

INSERT INTO `tf_users` (`uid`, `user_id`, `password`, `hits`, `last_visit`, `time_created`, `user_level`, `hide_offline`, `theme`, `language_file`, `state`) VALUES
(1, 'administrator', '098f6bcd4621d373cade4e832627b4f6', 145637, '1305661955', '1276626795', 2, 0, 'default', 'lang-english.php', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tf_xfer`
--

CREATE TABLE IF NOT EXISTS `tf_xfer` (
  `user_id` varchar(32) NOT NULL default '',
  `date` date NOT NULL default '0000-00-00',
  `download` bigint(80) NOT NULL default '0',
  `upload` bigint(80) NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tf_xfer`
--

