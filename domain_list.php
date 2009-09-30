<?php
require_once('base.php');
require_once($class_root . 'Domain.php');
require_once($class_root . 'Queue.php');

$rowamount = (int) $config->get('iface.rowamount');

# Get input vars (or set to some default)
$char = isSet($_GET["char"]) ? $_GET["char"] : 'a';
$type = isSet($_GET["type"]) ? $_GET["type"] : 'forward';
$offset = isSet($_GET["start"]) ? ($_GET["start"] - 1) * $rowamount : 0;
$start = isSet($_GET["start"]) ? $_GET["start"] : 1;

print $display->header();

# Input validation
if(	! preg_match('/^[0-9a-z]{1}$/i',$char) ||
	! preg_match('/^(?:forward|reverse)$/', $type) ||
	! preg_match('/^\d+$/', $offset) ||
	! preg_match('/^\d+$/', $start)) { 
	print $display->error("You hacker!");
	print $display->footer();
	exit(1);
}

?>

<script type="text/javascript">

function domain_delete(id,name) {

		var myhash = new Hash();
        myhash.set('id', id);
        myhash.set('name', name);

        var params = myhash.toJSON();


    new Ajax.Request('api/jsonrpc.php', {
            method: 'post',
            parameters: {"jsonrpc": "2.0", "method": 'queue_domain_delete', "params": params , "id": 1},
            onSuccess: function(r) {
                var json = r.responseText.evalJSON();
                if(json.error) {
                    $('feedback').update(json.error.message + ' (' + json.error.code + ')').
                    setStyle({color: 'red', display: 'block'});
                } else {
					$('tr_entry' + id).toggleClassName('domain');
					$('action_entry' + id).update('Added to queue');
					queue_counter();
					$('feedback').update('Domain added to queue for deletion').setStyle({color: 'black', display: 'block'});
                }
            }});
}


</script>

<div id="feedback" style="display: none;"></div>

<br>


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

if($dCount > $rowamount) {
	switch ($type) {
		case "reverse": 
			if(! preg_match('/^\d/', $char)) {
				die("Reverse only works with numeric values");
			}
			/*
			 * select char 'j' at a reverse domain where k and l are optional
			 * reverse domain: abc.def.ghi.jkl.in-addr.arpa
			 * select * from domains where name REGEXP '\\.j[[:digit:]]{0,2}\\.in-addr\\.arpa'
			 */
			$condition = "name REGEXP '\\\.".$char."[[:digit:]]{0,2}\\\.in-addr.arpa'";
			break;
		case "forward":
			$condition = "name LIKE '". $char ."%' AND NOT name LIKE '%in-addr.arpa'";
			break;
		# No need to define a default, because input is checked elsewhere
	}

	$dCount = count(Domain::find('all', array('conditions' => $conditions)));
	$dFindResult = Domain::find('all', array(
							'limit' => $rowamount,
							'offset' => $offset,
							'conditions' => $conditions));

	$domain_start_chars = Domain::domain_start_chars();
	foreach (array_merge(range(0,9), range('a','z')) as $char) {
		$fwlinks .= sprintf('[ %s ]', in_array($char, $domain_start_chars) ? 
					$display->link(sprintf('%s?char=%s&type=forward', $_SERVER["PHP_SELF"], $char), $char) : $char);
	}

	$reverse_start_chars = Domain::reverse_start_chars();
	foreach (range(0,9) as $char) {
		$rvlinks .= sprintf('[ %s ]', in_array($char, $reverse_start_chars) ? 
					$display->link(sprintf('%s?char=%s&type=forward', $_SERVER["PHP_SELF"], $char), $char) : $char);
	}
	printf("Forward: %s<br>Reverse: %s<br>", $fwlinks, $rvlinks);

	print "<hr>";
	print $display->show_pages($dCount, $rowamount,null,$char,$start,$type);
	print "<br><br>";

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
