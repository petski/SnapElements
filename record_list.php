<?php
require_once('base.php');
require_once($class_root . 'Domain.php');
require_once($class_root . 'Record.php');

print $display->header();

if(! preg_match('/^\d+$/',$_GET['id'])) {
	print $display->error("You hacker!");
	print $display->footer();
	exit(1);
}

$d = Domain::find($_GET['id']);
$findResult = Record::find('all', array('conditions' => 'domain_id = '.Record::quote($d->id), 'order' => 'name'));

print '<div class="header">'.count($findResult).' records found in domain "'.$d->name.'"</div><br>';

print $display->link('record_add.php?domain_id='.$d->id,'Add record');

print $display->records_header();
foreach($findResult as $record) {
	print $display->record($record);
}
print $display->records_footer();

print $display->footer();
?>
