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
		$result = array('is_ok' => false, 'messages' => "");

		if(!preg_match('/^\d+$/', $this->domain_id)) {
			$result['is_ok'] = false;
			$result['message'] = "Record doesn't belong to a domain or domain_id not set!";
			return $result;
		}

		foreach($this->attributes as $key => $value) { 
			$result = $this->validate_attribute($key);
			if($result['is_ok'] === false) { 
				return $result;
			} 
		} 

		$result = $this->is_unique();

		return $result;
	}

	static function valid_types() { 
		return array('A','AAAA','CNAME','HINFO','MX','NS','PTR','SOA','SRV','TXT','MBOXFW','NAPTR','URL');
	} 

	/*
	 * Check if record is unique at current domain
	 */
	public function is_unique() {
		$result = array('is_ok' => true, 'message' => 'Record is unique!');
		if(Record::find('first', array('conditions' =>
					'name = '.Record::quote($this->name) .
					' AND type = '. Record::quote($this->type) .
					' AND content = '. Record::quote($this->content)
					)))
		{
			$result['is_ok'] = false;
			$result['message'] = "Record already exists!";
			return $result;
		}

		/*
		 * Consistany check for SOA records, only allowed once at a domain
		 */
		if($this->type === SOA) {
			$result['is_ok'] = false;
			$result['message'] = "found SOA type!";
			if(Record::find('first', array('conditions' =>
						'domain_id = '.Record::quote($this->domain_id) .
						' AND type = '. Record::quote($this->type)
						)))
			{
				$result['is_ok'] = false;
				$result['message'] = "Soa record only allowed once at a domain!";
				return $result;
			}
		}
		return $result;
	}

	public function validate_attribute($name = null) {
		$errors = array(
				'is_ok' => false,
				'message' => "Initial errors array"
			);
		switch ($name) { 
				case "domain_id":
					$errors['is_ok'] = true;
					$errors['message'] = "Domain_id not validated!";
					return $errors;
				case "name":
					$errors['is_ok'] = strlen($this->$name) < 255 ? true : false;
					$errors['message'] = "Record name too long!";
					return $errors;
				case "type": 
					$errors['is_ok'] = in_array($this->$name,Record::valid_types()) ? true : false;
					$errors['message'] = "Invalid record type!";
					return $errors;
				case "content":
					switch ($this->type) { 
						case "A":
							# Stolen from pear/Net_IPv4/IPv4.php
							$errors['is_ok'] = $this->$name == long2ip(ip2long($this->$name)) ? true : false;
							$errors['message'] = "Content contains not a valid IPV4 IP-Address!"; 
							return $errors;
						case "CNAME":
							# Stolen from pear/Net_IPv4/IPv4.php, note check is inverted. 
							$errors['is_ok'] = $this->$name == long2ip(ip2long($this->$name)) ? false : true;
							$errors['message'] = "Content contains IP-Address, not allowed for $this->type records!"; 
							return $errors;
						case "MX":
							# Stolen from pear/Net_IPv4/IPv4.php, note check is inverted. 
							$errors['is_ok'] = $this->$name == long2ip(ip2long($this->$name)) ? false : true;
							$errors['message'] = "Content contains IP-Address, not allowed for $this->type records!"; 
							return $errors;
						case "SOA":
							# Do magic
							$errors['is_ok'] = true;
							$errors['message'] = "Content not checked for $this->type!"; 
							return $errors;
						default: 
							$errors['is_ok'] = false;
							$errors['message'] =  "Don't know how to validate the content for a $this->type yet!";
							return $errors;
					}
				case "ttl":
                                        # Do magic
					$errors['is_ok'] =  preg_match('/^\d+$/', $this->ttl) ? true : false;
					$errors['message'] = "TTL must be a positive int";
					return $errors;
				case "prio":
					switch ($this->type) {
						case "MX":
							$errors['is_ok'] =  preg_match('/^\d+$/', $this->prio) ? true : false;
							$errors['message'] = "Prio must be a positive int";	
							return $errors;
					}
                                        # Remove this once all checks are in place
                                        $errors['is_ok'] = true;
                                        $errors['message'] = "TTL is not validated!";
                                        return $errors;
                                        # END Remove this once all checks are in place

				default: 
					
					$errors['is_ok'] = false;
					$errors['message'] = "Don't know how to validate the ${name} attribute";
					return $errors;
		}
		return $errors;
	} 
}

?>
