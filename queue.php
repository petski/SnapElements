<?php
require_once('base.php');
require_once($class_root . 'Queue.php');
print $display->header();
?>

<script type="text/javascript">

function commit(id, method, change) { 
        //
	//TODO find a nicer way of doing unescape, since it kills white spaces 
        //which are needed for SOA,NAPTR,etc.. records 
	//
	//var params = unescape(change);
	var params = change;
	new Ajax.Request('api/jsonrpc.php', {
                          method: 'post',
			  parameters: {"jsonrpc": "2.0", "method": method, "params": params , "id": 1},
                          onSuccess: function(r) {
					commit_successfull(id);
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
                          }
                        });
}

</script>


<?php
$qFindResult = Queue::find('all', array('conditions' => 'commit_date IS NULL AND archived = 0'));

print '<div class="header">Queue entries <span id="queue_count_all">('.count($qFindResult).')</span></div><br>';

if(count($qFindResult) > 0) { 
	print <<< __EOS__
	<table>
	<tr>
	 <th>Date</th>
	 <th>Username</th>
	 <th>Function</th>
	 <th>Change</th>
	 <th>Action</th>
	</tr>
__EOS__;

	foreach($qFindResult as $entry) { 

		preg_match('/^(domain|record).*$/', $entry->function, $m);
		$changed_item = $m[1];

		$change_encoded = urlencode($entry->change);

		print <<< __EOS__
		<tr class="{$changed_item}" id="tr_entry{$entry->id}">
			<td>$entry->change_date</td>
			<td>$entry->user_name</td>
			<td>$entry->function</td>
			<td>$entry->change</td>
			<td id="action_entry{$entry->id}">
				<button onclick="commit($entry->id, '{$entry->function}','{$change_encoded}'); return false;">Commit</button>
			</td>
		</tr>
__EOS__;
	}
	print '</table>';
} 

print $display->footer();
?>
