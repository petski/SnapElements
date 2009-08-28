<?php
require_once('base.php');
require_once($class_root . 'Domain.php');
require_once($class_root . 'Queue.php');

print $display->header();
?>

<script type="text/javascript">
	function domain_delete(id, name) {
		new Ajax.Request('api/jsonrpc.php', {
			method: 'post',
			parameters: {"jsonrpc": "2.0", "method": 'queue_domain_delete', "params": new Hash({'name' : name}).toJSON() , "id": 1},
			onSuccess: function(r) {
					$('tr_entry' + id).toggleClassName('queue_commited');
					$('action_entry' + id).update('Added removal request to queue');
					queue_counter();
			}
		});
	}
</script>

<?php

#
#
#

$qFindResult = Queue::find('all', array('conditions' => 'commit_date IS NULL'));

print '<div class="header">'.Queue::count_all_pendingDomains().' pending domain changes</div><br>';

if(count($qFindResult) > 0) { 

	print '<table><tr><th>Function</th><th>Name</th><th>Type</th><th>By</th></tr>';
	foreach($qFindResult as $entry) {
		foreach($entry->queue_item_domains as $item) {
			print '<tr class="domain"><td>'.$item->function.'</td><td>'.$item->name.'</td><td>'.$item->type.'</td><td>'.$item->user_id.'</td></tr>';
		}
	}
	print '</table><br><br>';

}

#
#
#

$dFindResult = Domain::find('all');

print '<div class="header">'.count($dFindResult).' domains found</div><br>';


if(count($dFindResult) > 0) {
		print $display->domains_header();
		foreach($dFindResult as $domain) {
				print $display->domain($domain);
		}
		print $display->domains_footer();
}

print $display->footer();
?>
