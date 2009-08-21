<?php

require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'Queue.php';
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'Domain.php';
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'Record.php';

class JSONRPC {

	public function queue_count_all() { 
		return count(Queue::find('all', array('conditions' => 'commit_date IS NULL AND archived = 0')));
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
		$errors = array();
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
				array_push($errors, 'Domain not found!');
			}
		} else {
			/*
			 * Throw some error
			 */
			array_push($errors, 'No domain_id or domain_name found!');
		}

		if(count($errors) == 0) {
			$r = new Record(array(
				'domain_id' => $domain_id, 
				'name' => $p->name, 
				'type' => $p->type, 
				'content' => $p->content,
				'ttl' => $p->ttl,
				'prio' => $p->prio	
				));
			$r->save();
			return $r->id;
		} else {
			return $errors;
		}
	}

	public function queue_entry_commited($p) { 
		$q = Queue::find($p);
		$q->commit_date = date("Y-m-d\TH:i:s");
		$q->save();
	}

	public function queue_record_add($p) { 
		# Validate record (foreach ... bla bla)
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
