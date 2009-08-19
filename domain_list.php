<?php
require_once('base.php');
require_once($class_root . 'Domain.php');
require_once($class_root . 'Queue.php');

print $display->header();

#
#
#

$qFindResult = Queue::find('all', 
			array('conditions' => 'function LIKE "domain_%" AND commit_date IS NULL and archived = 0'));

print '<div class="header">'.count($qFindResult).' pending changes</div><br>';

if(count($qFindResult) > 0) { 
	print '<table><tr><th>Function</th><th>Name</th><th>Type</th><th>By</th></tr>';
	foreach($qFindResult as $entry) {
		$c = json_decode($entry->change);
		print '<tr class="domain"><td>'.$entry->function.'</td><td>'.$c->name.'</td><td>'.$c->type.'</td><td>'.$entry->user_name.'</td></tr>';
	}
	print '</table><br><br>';
}

#
#
#

$dFindResult = Domain::get_all();

print '<div class="header">'.count($dFindResult).' domains found</div><br>';

print $display->domains_header();
foreach($dFindResult as $domain) {
        print $display->domain($domain);
}
print $display->domains_footer();

print $display->footer();
?>
