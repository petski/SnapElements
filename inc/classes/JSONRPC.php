<?php

require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'Queue.php';
require_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'Domain.php';

class JSONRPC {

	public function queue_count_all() { 
		return count(Queue::find('all', array('conditions' => 'commit_date IS NULL AND archived = 0')));
	} 

#	public function domain_add($p) { 
#		$d = new Domain(array('name' => $p->name, 'type' => $p->type));
#		$d->save();
#		return $d->id;
#	} 

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
