<?php
require_once('base.php');
require_once($class_root . 'Queue.php');
print $display->header();

try {
	$qFindResult = Queue::find('all', array('conditions' => 'commit_date IS NULL AND archived = 0'));
} catch (Exception $e) {
	print $e->getMessage();
	print $display->footer();
	exit(0);
}
?>

<script type="text/javascript">

function queue_commit(id) { 
	new Ajax.Request('api/jsonrpc.php', {
                          method: 'post',
				parameters: {"jsonrpc": "2.0", "method": 'queue_commit', "params": id , "id": 1},
					onSuccess: function(r) {
						var json = r.responseText.evalJSON();
						if(json.error) {
							$('feedback').update(json.error.message + ' (' + json.error.code + ')').
							setStyle({color: 'red', display: 'block'});
						} else {
							$('tr_entry' + id).toggleClassName('queue_commited');
							$('action_entry' + id).update('Done');
							queue_counter();
							$('feedback').update('Request commited').setStyle({color: 'black', display: 'block'});
						}
					}});
}

function queue_delete(id) {
	new Ajax.Request('api/jsonrpc.php', {
                          method: 'post',
			  parameters: {"jsonrpc": "2.0", "method": "queue_delete", "params": id , "id": 1},
					onSuccess: function(r) {
						var json = r.responseText.evalJSON();
						if(json.error) {
							$('feedback').update(json.error.message + ' (' + json.error.code + ')').
							setStyle({color: 'red', display: 'block'});
						} else {
							$('tr_entry' + id).toggleClassName('queue_delete');
							$('action_entry' + id).update('Removed from queue');
							queue_counter();
							$('feedback').update('Request deleted').setStyle({color: 'black', display: 'block'});
						}
					}});
}

function queue_close(id) {
	new Ajax.Request('api/jsonrpc.php', {
                          method: 'post',
			  parameters: {"jsonrpc": "2.0", "method": "queue_close", "params": id , "id": 1},
					onSuccess: function(r) {
						var json = r.responseText.evalJSON();
						if(json.error) {
							$('feedback').update(json.error.message + ' (' + json.error.code + ')').
							setStyle({color: 'red', display: 'block'});
						} else {
							$('tr_entry' + id).toggleClassName('queue_closed');
							//$('action_entry' + id).update('Queue closed!');
							$('feedback').update('Request closed').setStyle({color: 'black', display: 'block'});
                        }
					}});
}

function queue_open(id) {
	new Ajax.Request('api/jsonrpc.php', {
                          method: 'post',
			  parameters: {"jsonrpc": "2.0", "method": "queue_open", "params": id , "id": 1},
					onSuccess: function(r) {
						var json = r.responseText.evalJSON();
						if(json.error) {
							$('feedback').update(json.error.message + ' (' + json.error.code + ')').
							setStyle({color: 'red', display: 'block'});
						} else {
							$('tr_entry' + id).toggleClassName('queue_open');
							//$('action_entry' + id).update('Queue closed!');
							$('feedback').update('Request opened').setStyle({color: 'black', display: 'block'});
                        }
					}});
}
</script>


<div id="feedback" style="display: none;"></div>
<br>
<?php


print '<div class="header">Queue entries <span id="queue_count_all">('.count($qFindResult).')</span></div><br>';

if(count($qFindResult) > 0) {
		print $display->queue_header();
		foreach($qFindResult as $queue) {
				print $display->queue($queue);
		}
		print $display->queue_footer();
}
 


print $display->footer();
?>
