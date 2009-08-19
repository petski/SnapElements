<?php

if (PHP_SAPI !== 'cli') 
	die("Testscript may only run in CLI-mode");

set_include_path(get_include_path() . PATH_SEPARATOR . '..');

require_once('Record.php');
require_once('Domain.php');

$test_domain	= mt_rand() . '.example.com';
$test_name	= 'record' . mt_rand();
$test_content	= '127.0.0.1';

$d = new Domain(array(
		'name' => $test_domain,
		'master' => null,
		'last_check' => null,
		'type' => 'NATIVE',
		'notified_serial' => null,
		'account' => null,
		));
$d->save();

$d->type = 'MASTER';
$d->save();

$r = new Record(array(
		'name' => $test_name,
		'type' => 'A',
		'content' => $test_content,
		'ttl' => '3600',
		'prio' => '0',
		'change_date' => date("U"),
	      ));

$d->records_push($r);

foreach(Record::find('all', array('conditions' => 
					'name LIKE '.ActiveRecord::quote($test_name.'%').' AND '.
					'content = '.ActiveRecord::quote($test_content))) as $f) { 
	$f->update_attributes(array('name' => $f->name.'1'));
	$f->destroy();
}

$d->destroy();

?>
