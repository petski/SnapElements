--
-- Begin PowerDNS MySQL table structure
--

--
-- Table structure for table `domains`
--

DROP TABLE IF EXISTS `domains`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `domains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `master` varchar(128) DEFAULT NULL,
  `last_check` int(11) DEFAULT NULL,
  `type` varchar(6) NOT NULL,
  `notified_serial` int(11) DEFAULT NULL,
  `account` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_index` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `records`
--

DROP TABLE IF EXISTS `records`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(6) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `ttl` int(11) DEFAULT NULL,
  `prio` int(11) DEFAULT NULL,
  `change_date` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rec_name_index` (`name`),
  KEY `nametype_index` (`name`,`type`),
  KEY `domain_id` (`domain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `supermasters`
--

DROP TABLE IF EXISTS `supermasters`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `supermasters` (
  `ip` varchar(25) NOT NULL,
  `nameserver` varchar(255) NOT NULL,
  `account` varchar(40) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- End PowerDNS MySQL table structure
--


--
-- Begin our Custom MySQL table structure
--

--
-- Table structure for table `queue`
--

DROP TABLE IF EXISTS `queue`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `change_date` datetime NOT NULL,
  `commit_date` datetime DEFAULT NULL,
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(16) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `queue_item`
--

DROP TABLE IF EXISTS `queue_item`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `queue_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `queue_id` int(11) DEFAULT NULL,
  `function` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `change` text COLLATE latin1_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
SET character_set_client = @saved_cs_client;


--
-- End our Custom MySQL table structure
--

