<?php

require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'Queue.php';
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'QueueItemDomain.php';
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'QueueItemRecord.php';
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'Domain.php';
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'Record.php';

class Services {

	/*
	 * This function returns an array with domain_id and domain_name usually used
	 * before adding a record to the queue
	 */
	private function get_DepsBeforeQueue($p = array()) {
		$domain_info = array("domain_id" => NULL, "domain_name" => NULL);
		$domain_id = NULL;
		$domain_name = NULL;
		/*
		 * Find out if we received var domain_id or domain_name
		 */
		if(isSet($p->domain_id)) {
			$domain_id = $p->domain_id;
			$d = Domain::find($domain_id);
			$domain_name = $d->name;
			$d = NULL;
		} elseif (isSet($p->domain_name)) {
			/*
			 * Set domain_id to init state
			 * If we have a pending (domain_add) change for same domain as this record is for.
			 * This way we can still use validate() on record object :)
			 */
			$domain_name = $p->domain_name;
			$d = Domain::find('first',  array(
				'conditions' => 'name='. Domain::quote($domain_name)));
			if($d->name === $domain_name) {
				$domain_id = $d->id;
			} elseif(Queue::is_pendingDomain($domain_name,'domain_add')) {
				$domain_id = "init";
				$domain_name = $p->domain_name;
			} else {
				throw new Exception("Can't find domain!");
			}
			$d = NULL;
		} else {
			throw new Exception("No domain_name or domain_id found!");
		}

		$domain_info["domain_id"] = $domain_id;
		$domain_info["domain_name"] = $domain_name;

		return $domain_info;
	}

	/*
	 * This function returns an array with domain_id and domain_name usually used
	 * before commiting a record from the queue and adding it to the live enviroment
	 */
	private function get_DepsBeforeCommit($p = array()) {
		$domain_info = array("domain_id" => NULL, "domain_name" => NULL);
		$domain_id = NULL;
		$domain_name = NULL;

		if ($p->domain_name != NULL) {
			$d = Domain::find('first', array('conditions' => "name = '$p->domain_name'"));
			/*
			 * Check if we didn't get an empty id for our domain Object.
			 * This could happen when you try to commit a new record, while
			 * changes for the new domain are still pending.
			 */
			if($d->id != NULL) {
				$domain_id = $d->id;
				$domain_name = $d->name;
			} else {
				throw new Exception("Domain not found!");
			}
		} else {
			/*
			 * Throw some error
			 */
			throw new Exception("No domain_name found!");
		}
		$domain_info["domain_id"] = $domain_id;
		$domain_info["domain_name"] = $domain_name;

		return $domain_info;
	}

	private function validate_record($domain_id, $p =  array()) {
		/*
		 * Create temp Record obj for validation
		 */
		$tempRecord = new Record(array(
			'domain_id' => $domain_id,
			'name' => $p->name,
			'type' => $p->type,
			'content' => $p->content,
			'ttl' => $p->ttl,
			'prio' => $p->prio));


		$result = $tempRecord->validate();
		if($result['is_ok'] === false) {
			throw new Exception($result['message']);
		}

		$tempRecord = NULL;
	}

	public function queue_count_all() {
		return count(Queue::find('all', array('conditions' => 'commit_date IS NULL AND archived = 0')));
	} 

	public function queue_commit($p) {
		$q = Queue::find($p);
		if($q != NULL) {
			foreach(array_merge($q->queue_item_domains,$q->queue_item_records) as $item) {
				$method = $item->function;
				if(method_exists($this, $method)) {
					if($item->commit_date === NULL) {
						$this->$method($item);
						$item->commit_date = date("Y-m-d\TH:i:s");
						$item->save();
					}
				} else {
					throw new Exception("Unknown method $item->function for $item->name type $item->type with id $item->id!!");
				}
			}

			$q->closed = 1;
			$q->user_id = 1;
			$q->commit_date = date("Y-m-d\TH:i:s");
			$q->save();

			/*
			 * Queue is finished update SOA record for domain
			 */
			$d = Domain::find('first', array('conditions' => "name = '$q->domain_name'"));
			$d->update_soa_record();
			return true;
		} else {
			return false;
		}
	}

	public function queueItem_domain_commit($p) {
		$qDomain = QueueItemDomain::find($p);
		if($qDomain != NULL) {
			$method = $qDomain->function;
			if(method_exists($this, $method)) {
				if($qDomain->commit_date === NULL) {
					$this->$method($qDomain);
					$qDomain->commit_date = date("Y-m-d\TH:i:s");
					$qDomain->save();
				} else {
					throw new Exception("Unknown method $qDomain->function for $qDomain->name type $qDomain->type with id $qDomain->id!!");
				}
			}
			return true;
		} else {
			return false;
		}
	}

	public function queueItem_record_commit($p) {
		$qRecord = QueueItemRecord::find($p);
		if($qRecord != NULL) {
			$method = $qRecord->function;
			if(method_exists($this, $method)) {
				if($qRecord->commit_date === NULL) {
					$this->$method($qRecord);
					$qRecord->commit_date = date("Y-m-d\TH:i:s");
					$qRecord->save();
				} else {
					throw new Exception("Unknown method $qRecord->function for $qRecord->name type $qRecord->type with id $qRecord->id!!");
				}
			}
			return true;
		} else {
			return false;
		}
	}

	public function queue_close($p) {
		$q = Queue::find($p);
		if($q != NULL) {
			$q->closed = 1;
			$q->user_id = 1;
			$q->save();
			return true;
		} else {
			return false;
		}
	}

	public function queue_open($p) {
		$q = Queue::find($p);
		if($q != NULL) {
			$q->closed = 0;
			$q->user_id = 1;
			$q->save();
			return true;
		} else {
			return false;
		}
	}
	public function queue_delete($p) { 
		$q = Queue::find($p);
		return $q->destroy();
	}

	public function queueItem_domain_delete($p) { 
		$qDomain = QueueItemDomain::find($p);
		return $qDomain->destroy();
	}

	public function queueItem_record_delete($p) { 
		$qRecord = QueueItemRecord::find($p);
		return $qRecord->destroy();
	}

	public function domain_add($p) { 
		//TODO validate domain attributes..
		$d = new Domain(array('name' => $p->name, 'type' => $p->type));
		$d->save();
		return $d->id;
	}

	public function queue_domain_add($p) {
		$function = "domain_add";

		if(! Domain::is_valid('name', $p->name)) { 
			throw new Exception("Name is not valid");
		} 

		if(! Domain::is_valid('type', $p->type)) { 
			throw new Exception("You hacker!");
		}
		
		# Shouldn't be a existing domain
		if(Domain::find('first', array('conditions' => 'name = '.Domain::quote($p->name)))) {
			throw new Exception("Domain already exists!");
		} 

		/*
		 * Shouldn't be a pending (domain_add) change for this domain.
		 */
		if(Queue::is_pendingDomain($p->name, $function)) { 
			throw new Exception('Already pending change for this domain!');
		}

		$qid = new QueueItemDomain(array(
			'ch_date' =>  date("Y-m-d\TH:i:s"),
			'user_id' => '1',
			'function' => $function,
			'name' => $p->name,
			'master' => $p->master,
			'type' => $p->type));

		$q = Queue::get_pendingQueue($p->name);

		if(!isSet($q)) {
			$q = new Queue(array(
				'ch_date' => date("Y-m-d\TH:i:s"),
				'domain_name' => $p->name,
				'archived' => '0',
				'closed' => '0',
				'comment' => 'Creation of new domain: '. $p->name));
        }
		
		$q->queue_item_domains_push($qid);
		$q->save();
		return $q->id;
	}

	public function record_add($p) {
		$dDeps = $this->get_DepsBeforeCommit($p);
		$domain_id = $dDeps["domain_id"];

		$r = new Record(array(
			'domain_id'	=> $domain_id, 
			'name'		=> $p->name, 
			'type'		=> $p->type, 
			'content'	=> $p->content,
			'ttl'		=> $p->ttl,
			'prio'		=> $p->prio));

		/*
		 * TODO create nicer way to fix killing white spaces for SOA records
		 */
		switch($r->type) {
			case "SOA":
				$r->content = (str_replace('+',' ',$r->content));	
			default: 
		}

		/*
		 * Last validation check before saving the new record
		 */
		$result = $r->validate();
		if($result['is_ok'] === false) {
			throw new Exception($result['message']);
		}

		$r->save();
		return $r->id;
	}

	public function queue_record_add($p) {
		/*
		 * Find out if we received var domain_id or domain_name
		 */
		$dDeps = $this->get_DepsBeforeQueue($p);
		$domain_id = $dDeps["domain_id"];
		$domain_name = $dDeps["domain_name"];

		/*
		 * Check for same pending record change
		 */
		if(Queue::is_pendingRecord($domain_name,$p)) {
			throw new Exception('Already pending change for this record!');
		}
		/*
		 * validate input
		 */
		$this->validate_record($domain_id, $p);

		/*
		 * Create queueItemRecord for new record
		 */
		$qir = new QueueItemRecord(array(
			'ch_date' => date("Y-m-d\TH:i:s"),
			'user_id' => '1',
			'function' => 'record_add',
			'domain_name' => $domain_name,
			'name' => $p->name,
			'type' => $p->type,
			'content' => $p->content,
			'ttl' => $p->ttl,
			'prio' => $p->prio));
		/*
		 * Get a pending queue for same domain if there isn't any create new Queue.
		 */
		$q = Queue::get_pendingQueue($domain_name);

		if(!isSet($q)) {
			$q = new Queue(array(
				'ch_date' => date("Y-m-d\TH:i:s"),
				'domain_name' => $domain_name,
				'archived' => '0',
				'closed' => '0',
				'comment' => 'Adding record to domain: '. $domain_name));
		}

		$q->queue_item_records_push($qir);
		$q->save();
		return $q->id;
	}

	public function record_edit($p) {
		$dDeps = $this->get_DepsBeforeCommit($p);
		$domain_id = $dDeps["domain_id"];

		/*
		 * Check if we received record_id else it's not possible to edit this record
		 */
		if($p->record_id != NULL) {
			$record_id = $p->record_id;
		} else {
			throw new Exception('Unable to edit this record, no valid record_id!');
		}

		$r = Record::find($p->record_id);
		$r->name = $p->name;
		$r->type = $p->type;
		$r->content = $p->content;
		$r->ttl = $p->ttl;
		$r->prio = $p->prio;

		/*
		 * Last validation check before saving the new record
		 */
		$result = $r->validate();
		if($result['is_ok'] === false) {
			throw new Exception($result['message']);
		}

		if($r->is_modified()) {
			$r->save();
		}

		return $r->id;
	}

	public function queue_record_edit($p) {
		/*
		 * Find out if we received var domain_id or domain_name
		 */
		$dDeps = $this->get_DepsBeforeQueue($p);
		$domain_id = $dDeps["domain_id"];
		$domain_name = $dDeps["domain_name"];
		$record_id = NULL;

		/*
		 * Check if we received record_id else it's not possible to edit this record
		 */
		if($p->record_id != NULL) {
			$record_id = $p->record_id;
		} else {
			throw new Exception('Unable to queue this record, no valid record_id!');
		}

		/*
		 * Check for same pending record change
		 */
		if(Queue::is_pendingRecord($domain_name,$p)) {
			throw new Exception('Already pending change for this record!');
		}

		/*
		 * validate input
		 */
		$this->validate_record($domain_id, $p);

		/*
		 * Create queueItemRecord for new record
		 */
		$qir = new QueueItemRecord(array(
			'ch_date' => date("Y-m-d\TH:i:s"),
			'user_id' => '1',
			'function' => 'record_edit',
			'domain_name' => $domain_name,
			'record_id' => $record_id,
			'name' => $p->name,
			'type' => $p->type,
			'content' => $p->content,
			'ttl' => $p->ttl,
			'prio' => $p->prio));
		/*
		 * Get a pending queue for same domain if there isn't any create new Queue.
		 */
		$q = Queue::get_pendingQueue($domain_name);

		if(!isSet($q)) {
			$q = new Queue(array(
				'ch_date' => date("Y-m-d\TH:i:s"),
				'domain_name' => $domain_name,
				'archived' => '0',
				'closed' => '0',
				'comment' => 'Editing record for domain: '. $domain_name));
		}

		$q->queue_item_records_push($qir);
		$q->save();
		return $q->id;
	}
}

?>
