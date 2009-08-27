<?php
require_once('base.php');
require_once($class_root . 'Queue.php');

print $display->header();

if(! preg_match('/^\d+$/',$_GET['id'])) {
	print $display->error("You hacker!");
	print $display->footer();
	exit(1);
}

try {
	$q = Queue::find($_GET['id']);
	$qdomain = $q->queue_item_domains;
	$qrecord = $q->queue_item_records;
} catch (Exception $e) {
	print $e->getMessage();
	print $display->footer();
	exit(0);

}

?>


<script type="text/javascript">

function queueItem_delete(id, method) {
	new Ajax.Request('api/jsonrpc.php', {
			method: 'post',
			parameters: {"jsonrpc": "2.0", "method": method, "params": id , "id": 1},
			onSuccess: function(r) {
				var json = r.responseText.evalJSON();
                if(json.error) {
					$('feedback').update(json.error.message + ' (' + json.error.code + ')').
					setStyle({color: 'red', display: 'block'});
				} else {
					$('tr_entry' + id).toggleClassName('queue_commited');
					$('action_entry' + id).update('Removed from queue');
					$('feedback').update('Request deleted').setStyle({color: 'black', display: 'block'});
				}
			}});
}

</script>


<div id="feedback" style="display: none;"></div>
<br>

<?php

//$amount = count($qid
print '<div class="header">'.$q->count_pendingItems().' items found in queue for domain: "'.$q->domain_name.'"</div><br>';


if (count($qdomain) > 0) {
		print $display->queue_domain_header();
		foreach($qdomain as $domain) {
			print $display->queue_domain($domain);
		}
		print $display->queue_domain_footer();
}

print "<br>";


if (count($qrecord) > 0) {
		print $display->queue_record_header();
		foreach($qrecord as $record) {
			print $display->queue_record($record);
		}
		print $display->queue_record_footer();
}

print $display->footer();
?>
