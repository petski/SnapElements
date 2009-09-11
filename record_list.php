<?php
require_once('base.php');
require_once($class_root . 'Domain.php');
require_once($class_root . 'Record.php');

$offset = 0;
$rowamount = (int) $config->get('iface.rowamount');
$start = 1;
if(isSet($_GET["start"])) {
    $offset = (($_GET["start"] - 1) * $rowamount);
    $start = $_GET["start"];
}


print $display->header();

if(! preg_match('/^\d+$/',$_GET['id'])) {
	print $display->error("You hacker!");
	print $display->footer();
	exit(1);
}

try {
	$d = Domain::find($_GET['id']);
	$result = ActiveRecord::query("SELECT COUNT(*) AS count FROM records WHERE domain_id=$d->id");
	$rCount = (int) $result[0]['count'];
	if($rCount > $rowamount) {
		$findResult = Record::find('all', array(
								'limit' => "$rowamount",
								'offset' => "$offset",
								'conditions' => 'domain_id = '.Record::quote($d->id),
								'order' => 'name'));
	} else {
		$findResult = Record::find('all', array('conditions' => 'domain_id = '.Record::quote($d->id), 'order' => 'name'));
	}
} catch (Exception $e) {
	print $e->getMessage();
	print $display->footer();
	exit(0);

}
print sprintf('<div class="header">%s records found for domain %s</div><br>', $rCount, $d->name);

print $display->link('record_add.php?domain_id='.$d->id,'Add record');

if($rCount > $rowamount) {
	print "<br><br>";
	print $display->show_pages($rCount, $rowamount,$d->id,null,$start);
	print "<br><br>";
}

print $display->records_header();
foreach($findResult as $record) {
	print $display->record($record);
}
print $display->records_footer();

print $display->footer();
?>
