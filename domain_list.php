<?php
require_once('base.php');
require_once($class_root . 'Domain.php');
require_once($class_root . 'Queue.php');
$char = a;
$type = "forward";
$offset = 0;
$rowamount = (int) $config->get('iface.rowamount');
$start = 1;

print $display->header();

if(isSet($_GET["char"])) {
	if(! preg_match('/^([0-9]|[a-z]){1}$/',$_GET["char"])) {
		print $display->error("You hacker!");
		print $display->footer();
		exit(1);
	} else {
		$char = $_GET["char"];
	}
}

if(isSet($_GET["type"])) {
	$type = $_GET["type"];
}

if(isSet($_GET["start"])) {
	$offset = (($_GET["start"] - 1) * $rowamount);
	$start = $_GET["start"];
}

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

$result = ActiveRecord::query("SELECT COUNT(*) AS count FROM domains");
$dCount = (int) $result[0]['count'];

print '<div class="header">'.$dCount.' domains found</div><br>';

$dFindResult = null;

if($dCount > $rowamount) {
	$query = null;
	if($type === "reverse") {
		if (preg_match('/^\d/', $char)){
			/*
			 * select char 'j' at a reverse domain where k and l are optional
			 * reverse domain: abc.def.ghi.jkl.in-addr.arpa
			 * select * from domains where name REGEXP '\\.j[[:digit:]]{0,2}\\.in-addr\\.arpa'
			 */
			$query = "name REGEXP '\\\.".$char."[[:digit:]]{0,2}\\\.in-addr.arpa'";
		}
	} elseif($type === "forward") {
		$query = "name LIKE '". $char ."%' AND NOT name LIKE '%in-addr.arpa'";
	}

	if($query != null) {
		$dCount = count(Domain::find('all', array('conditions' => "$query")));
		$dFindResult = Domain::find('all', array(
								'limit' => "$rowamount",
								'offset' => "$offset",
								'conditions' => "$query"));
		print $display->show_chars($char);
		print "<hr>";
		print $display->show_pages($dCount, $rowamount,null,$char,$start,$type);
		print "<br><br>";
	} else {
		print "Something went wrong...";
	}
} else {
	$dFindResult = Domain::find('all');
}

if(count($dFindResult) > 0) {
		print $display->domains_header();
		foreach($dFindResult as $domain) {
				print $display->domain($domain);
		}
		print $display->domains_footer();
}

print $display->footer();
?>
