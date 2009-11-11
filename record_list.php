<?php
require_once('base.php');
require_once($class_root . 'Domain.php');
require_once($class_root . 'Record.php');

$rowamount = (int) $config->get('iface.rowamount');

# Get input vars (or set to some default)
$offset = isSet($_GET["start"]) ? ($_GET["start"] - 1) * $rowamount : 0;
$start = isSet($_GET["start"]) ? $_GET["start"] : 1;
$domain_id = isSet($_GET['id']) ? $_GET['id'] : 0;



print $display->header();

# Input validation
if(     ! preg_match('/^\d+$/', $domain_id) ||
        ! preg_match('/^\d+$/', $offset) ||
        ! preg_match('/^\d+$/', $start)) {
        print $display->error("You hacker!");
        print $display->footer();
        exit(1);
}

try {
	$d = Domain::find($domain_id);
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
print "<br>";
print $display->link('record_edit.php?id='.$d->id,'Edit record(s)');

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
