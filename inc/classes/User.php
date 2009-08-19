<?php

/*
 *   CREATE TABLE `users` (
 *   `id` int(11) NOT NULL auto_increment,
 *   `username` varchar(16) collate latin1_general_ci NOT NULL default '0',
 *   `password` varchar(34) collate latin1_general_ci NOT NULL default '0',
 *   `fullname` varchar(255) collate latin1_general_ci NOT NULL default '0',
 *   `email` varchar(255) collate latin1_general_ci NOT NULL default '0',
 *   `description` varchar(1024) collate latin1_general_ci NOT NULL default '0',
 *   `perm_templ` tinyint(4) NOT NULL default '0',
 *   `active` tinyint(4) NOT NULL default '0',
 *   PRIMARY KEY  (`id`)
 *   ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
 * 
 */

require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'generated_models' .DIRECTORY_SEPARATOR. 'UserBase.php';
class User extends UserBase {
	protected $belongs_to  = array('zone');
}

?>
