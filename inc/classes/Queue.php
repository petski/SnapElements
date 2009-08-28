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
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'QueueItemDomain.php';
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'QueueItemRecord.php';

class Queue extends QueueBase {
	protected $has_many = array('queue_item_domains','queue_item_records');

	/*
	 * Cleanup all queue_item_domains and queue_item_records that depend on this Queue Obj
	 */
	protected function before_destroy() {
		foreach(array_merge($this->queue_item_domains,$this->queue_item_records) as $o) { 
			$o->destroy(); 
		}
	}

	public function count_pendingItems() {
		$amount = 0;
		$amount += count($this->queue_item_domains);
		$amount += count($this->queue_item_records);
		return $amount;
	}

	static function count_all_pendingDomains() {
		$amount = 0;
		$qFindResult = Queue::find('all', array('conditions' => 'commit_date IS NULL'));
		foreach($qFindResult as $q) {
			foreach($q->queue_item_domains as $qdomain) {
				$amount += count($qdomain); 
			}
		}
		return $amount;
	}

	static function is_pendingDomain($domain_name = null) {
		$awnser = false;
		$qFindResult = Queue::find('first', array(
			'conditions' => 'commit_date IS NULL AND closed = 0 AND domain_name='
			.Domain::quote($domain_name)));

		if($qFindResult->queue_item_domains[0]->name === $domain_name) {
			$awnser = true;
		}
		return $awnser;
	}

	static function get_pendingQueue($domain_name = null) {
		$awnser = null;
		$qFindResult = Queue::find('first', array(
			'conditions' => 'commit_date IS NULL AND closed = 0 AND domain_name='
			.Domain::quote($domain_name)));

		if(isSet($qFindResult)) {
			$awnser = $qFindResult;
		}
		return $awnser;
	}

	//TODO finish is_pendingRecord
	static function is_pendingRecord($domain_name = null, $r = array()) {
		$awnser = false;
		if(count($r) > 0) {
			$qFindResult = Queue::find('first', array(
				'conditions' => 'commit_date IS NULL AND closed = 0 AND domain_name='
				.Domain::quote($domain_name)));
			foreach($qFindResult->queue_item_records as $key) {
				if($key->name === $r->name && $key->content === $r->content && $key->type === $r->type) {
					return $awnser = true;
				}
			}
		}
		return $awnser;
	}
}

?>
