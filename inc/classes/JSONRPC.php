<?php

require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'Queue.php';
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'Domain.php';
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'Record.php';

class JSONRPC {

	public function queue_count_all() { 
		return count(Queue::find('all', array('conditions' => 'commit_date IS NULL AND archived = 0')));
	} 

        public function queue_entry_commited($p) {
                $q = Queue::find($p);
                $q->commit_date = date("Y-m-d\TH:i:s");
                $q->save();
        }

	public function queue_delete($p) { 
		$q = Queue::find($p);
		return $q->destroy();
	}

	public function domain_add($p) { 
		$d = new Domain(array('name' => $p->name, 'type' => $p->type));
		$d->save();
		return $d->id;
	} 

	public function queue_domain_add($p) { 

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

		# Shouldn't be a pending (domain_add) change for this domain.
		$qFindResult = Queue::find('all', array('conditions' => 'commit_date IS NULL AND archived = 0 AND function="domain_add"'));
		foreach($qFindResult as $entry) { 
			if(with(json_decode($entry->change))->{'name'} == $p->name) { 
				throw new Exception('Already pending change for this domain!');
			} 
		} 

		$q = new Queue(array(
			'change_date' => date("Y-m-d\TH:i:s"),
			'archived' => 0,
			'user_id' => 1,
			'user_name' => 'henkie',
			'function' => 'domain_add',
			'change' => json_encode($p),
			));
		$q->save();
		return $q->id;
	} 

	public function queue_domain_delete($p) { 
		# Validate record (foreach ... bla bla)
		$q = new Queue(array(
			'change_date' => date("Y-m-d\TH:i:s"),
			'archived' => 0,
			'user_id' => 1,
			'user_name' => 'henkie',
			'function' => 'domain_delete',
			'change' => json_encode($p),
			));
		$q->save();
		return $q->id;
	}

	public function record_add($p) { 
		$domain_id = "";
		if(isSet($p->domain_id)) {
			$domain_id = $p->domain_id;
		} elseif (isSet($p->domain_name)) {
			$d = Domain::find('first', array('conditions' => "name = '$p->domain_name'"));
			/*
			 * Check if we didn't get an empty id for our domain Object. 
			 * This could happen when you try to commit a new record, while
			 * changes for the new domain are still pending.
			 */
			if($d->id != NULL) {
				$domain_id = $d->id;
			} else {
				throw new Exception("Domain not found!");
			}
		} else {
			/*
			 * Throw some error
			 */
			throw new Exception("No domain_id or domain_name found!");
		}

		$r = new Record(array(
			'domain_id'	=> $domain_id, 
			'name'		=> $p->name, 
			'type'		=> $p->type, 
			'content'	=> $p->content,
			'ttl'		=> $p->ttl,
			'prio'		=> $p->prio	
			));

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
		# Shouldn't be a existing record
		if(Record::find('first', array('conditions' => 
					'name = '.Record::quote($p->name) . 
					' AND type = '. Record::quote($p->type) . 
					' AND content = '. Record::quote($p->content)
					))) 
		{ 
			throw new Exception("Record already exists!");
		}

		# Shouldn't be a pending (record_add) change for this domain.
		$qFindResult = Queue::find('all', array('conditions' => 'commit_date IS NULL AND archived = 0 AND function="record_add"'));
		foreach($qFindResult as $entry) {
			if(with(json_decode($entry->change))->{'name'} == $p->name &&
				(with(json_decode($entry->change))->{'type'} == $p->type) &&
				(with(json_decode($entry->change))->{'content'} == $p->content) )
                        {
				throw new Exception('Already pending change for this record!');
                        }
                }


		/*
		 * Create temp Record obj for validation
		 */
		$tempRecord = new Record(array(
			'name' => $p->name,
			'type' => $p->type,
			'content' => $p->content,
			'ttl' => $p->ttl,
			'prio' => $p->prio
			));
		$result = $tempRecord->validate();
		if($result['is_ok'] === false) {
			throw new Exception($result['message']);
                }
		
		$tempRecord = null;

		/*
		 * Create queue for new record
		 */
			
		$q = new Queue(array(
			'change_date' => date("Y-m-d\TH:i:s"),
			'archived' => 0,
			'user_id' => 1,
			'user_name' => 'henkie',
			'function' => 'record_add',
			'change' => json_encode($p),
			));
		$q->save();
		return $q->id;
	} 
}

?>
