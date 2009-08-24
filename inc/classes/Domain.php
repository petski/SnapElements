<?php

/* 
 * CREATE TABLE `domains` (
 *   `id` int(11) NOT NULL auto_increment,
 *   `name` varchar(255) collate latin1_general_ci NOT NULL,
 *   `master` varchar(128) collate latin1_general_ci default NULL,
 *   `last_check` int(11) default NULL,
 *   `type` varchar(6) collate latin1_general_ci NOT NULL,
 *   `notified_serial` int(11) default NULL,
 *   `account` varchar(40) collate latin1_general_ci default NULL,
 *   PRIMARY KEY  (`id`),
 *   UNIQUE KEY `name_index` (`name`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci
 *
 */

require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'generated_models' .DIRECTORY_SEPARATOR. 'DomainBase.php';

class Domain extends DomainBase {
	protected $has_many = array('records');

	public function get_serial() { 
		require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR. 'string.php');
		# This would be cooler: $this->records->find etc etc and leave the domain condition away
		return dindex(preg_split('/\s+/',Record::find('first',array('conditions' => 
							'type = '.Domain::quote('SOA').' AND '.
							'domain_id = '.Domain::quote($this->id)
							 ))->content), 2 , null);
	} 

	// See TODO

        protected $former_attributes = array();

        protected function after_create() {
                $output = array();
                foreach(array_keys($this->attributes) as $key) {
                        $output[$key]['n'] = $this->attributes[$key];
                }
                $this->after_care($output);
        }

        protected function before_update() {
                $this->former_attributes = $this->find($this->id)->attributes;
        }

        protected function after_update() {
                $changes = array_diff_assoc($this->former_attributes,$this->attributes);
                foreach($changes as $key => $value) {
                        $output[$key] = array('o' => $value,
                                              'n' => $this->attributes[$key]);
                }
                $this->after_care($output);
        }

        protected function after_destroy() {
                $output = array();
                foreach(array_keys($this->attributes) as $key) {
                        $output[$key]['o'] = $this->attributes[$key];
                }
                $this->after_care($output);
        }

        private function after_care($changes) {
                # Update serial if needed (see configfile)
        }

	static function get_all($args = array()) { 
		return Domain::query("SELECT d.id, d.name, d.type, COUNT(DISTINCT r.id) AS record_count ".
				     " FROM domains d LEFT JOIN records r ON r.domain_id=d.id ".
				     ( $args['conditions'] ? 'WHERE '.$args['conditions'] : '' ).
				     " GROUP BY d.name, d.id, d.type ORDER BY d.name");
	} 

#	static function get_those_that_need_soa_change() {
#		return Domain::query("SELECT d.name , d.id AS domain_id, r.id AS record_id, ".
#			"SUBSTR(SUBSTR(RTRIM(r.content), LOCATE(' ',r.content,LOCATE(' ',r.content) + 1) + 1),1,10) AS current_serial, ".
#			"(SELECT MAX(l.serial) FROM logs l WHERE l.domain_id = d.id) AS log_serial ".
#			"FROM domains d LEFT JOIN records r ON (r.domain_id = d.id AND r.type='SOA') ".
#			"HAVING current_serial IS NULL OR current_serial <= log_serial ".
#			"ORDER BY d.name");
#	} 

	static function valid_types() { 
		return array('NATIVE','MASTER','SLAVE');
	} 

        public function is_valid($key = null, $value = null) {
                switch ($key) {
                                case "name":
                                        return (strlen($value) > 0 && strlen($value) <= 255) ? true : false;

                                case "type":
                                        return in_array($value, Domain::valid_types());
                                default:
                                        return false;
                }
        }
	
}

?>
