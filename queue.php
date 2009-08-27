<?php
require_once('base.php');
require_once($class_root . 'Queue.php');
print $display->header();
?>

<script type="text/javascript">

function commit(id, method, change) { 
        //
	//TODO find a nicer way of doing unescape, since it kills white spaces 
        //which are needed for SOA,NAPTR,etc.. records.
        //Not using unescape kills adding A records :(
	//
	var params = unescape(change);
	new Ajax.Request('api/jsonrpc.php', {
                          method: 'post',
			  parameters: {"jsonrpc": "2.0", "method": method, "params": params , "id": 1},
                          onSuccess: function(r) {
				var json = r.responseText.evalJSON();
				if(json.error) {
					$('feedback').update(json.error.message + ' (' + json.error.code + ')').
					setStyle({color: 'red', display: 'block'});
				} else {
					commit_successfull(id);
				}
                          }
                        });
}

function queue_delete(id) {
	new Ajax.Request('api/jsonrpc.php', {
                          method: 'post',
			  parameters: {"jsonrpc": "2.0", "method": "queue_delete", "params": id , "id": 1},
                          onSuccess: function(r) {
				$('tr_entry' + id).toggleClassName('queue_commited');
				$('action_entry' + id).update('Removed from queue');
				queue_counter();
				$('feedback').update('Request deleted').setStyle({color: 'black', display: 'block'});
                          }
                        });
}

function commit_successfull(id) { 
	new Ajax.Request('api/jsonrpc.php', {
                          method: 'post',
			  parameters: {"jsonrpc": "2.0", "method": 'queue_entry_commited', "params": id , "id": 1},
                          onSuccess: function(r) {
				$('tr_entry' + id).toggleClassName('queue_commited');
				$('action_entry' + id).update('Done');
				queue_counter();
				$('feedback').update('Request commited').setStyle({color: 'black', display: 'block'});
                          }
                        });
}

</script>


<div id="feedback" style="display: none;"></div>
<br>
<?php

try {
	$qFindResult = Queue::find('all', array('conditions' => 'commit_date IS NULL AND archived = 0'));
} catch (Exception $e) {
	print $e->getMessage();
	print $display->footer();
	exit(0);
}



print '<div class="header">Queue entries <span id="queue_count_all">('.count($qFindResult).')</span></div><br>';

print $display->queue_header();
foreach($qFindResult as $queue) {
        print $display->queue($queue);
}
print $display->queue_footer();
 


print $display->footer();
?>
