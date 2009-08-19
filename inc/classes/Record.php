<?php

/*
 *  CREATE TABLE `records` (
 *   `id` int(11) NOT NULL auto_increment,
 *   `domain_id` int(11) default NULL,
 *   `name` varchar(255) collate latin1_general_ci default NULL,
 *   `type` varchar(6) collate latin1_general_ci default NULL,
 *   `content` varchar(255) collate latin1_general_ci default NULL,
 *   `ttl` int(11) default NULL,
 *   `prio` int(11) default NULL,
 *   `change_date` int(11) default NULL,
 *   PRIMARY KEY  (`id`),
 *   KEY `rec_name_index` (`name`),
 *   KEY `nametype_index` (`name`,`type`),
 *   KEY `domain_id` (`domain_id`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
 * 
 */

require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'generated_models' .DIRECTORY_SEPARATOR. 'RecordBase.php';

class Record extends RecordBase {
	protected $belongs_to = array('domain');
	
	public function validate() { 
		foreach($this->attributes as $key => $value) { 
			if($this->validate_attribute($key) === false) { 
				return false;
			} 
		} 
		return true;
	}

	static function valid_types() { 
		return array('A','AAAA','CNAME','HINFO','MX','NS','PTR','SOA','SRV','TXT','MBOXFW','NAPTR','URL');
	} 

	public function validate_attribute($name = null) {
		switch ($name) { 
				case "name":
					return strlen($this->$name) < 255 ? true : false;
				case "type": 
					return in_array($this->$name,Record::valid_types()) ? true : false;
				case "content":
					switch ($this->type) { 
						case "A":
							# Stolen from pear/Net_IPv4/IPv4.php
							return $this->$name == long2ip(ip2long($this->$name)) ? true : false;
						default: 
							print "Don't know how to validate the content for a $this->type yet";
							return false;
					}
				default: 
					# print ("Don't know how to validate the ${name} attribute\n");
					return true;
		}
		return true;
	} 
}

?>
