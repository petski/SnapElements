<?php

/*

  DROP TABLE IF EXISTS `queue`;
  CREATE TABLE `queue` (
   `id` int(11) NOT NULL auto_increment,
   `change_date` datetime NOT NULL,
   `commit_date` datetime default NULL,
   `archived` tinyint(1) default '0' NOT NULL,
   `user_id` int(11) default NULL,
   `user_name` varchar(16) collate latin1_general_ci NOT NULL default '',
   `function` varchar(50) NOT NULL, -- i.e. domain_add, record_edit
   `change` text collate latin1_general_ci,
   PRIMARY KEY  (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci

*/

require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'generated_models' .DIRECTORY_SEPARATOR. 'QueueBase.php';
class Queue extends QueueBase {

}

?>
