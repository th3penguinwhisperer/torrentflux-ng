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

