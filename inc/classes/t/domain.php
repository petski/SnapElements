<?php

if (PHP_SAPI !== 'cli') 
	die("Testscript may only run in CLI-mode");

set_include_path(get_include_path() . PATH_SEPARATOR . '..');

require_once('Domain.php');

$d = Domain::find(11);
print_r($d->records);

?>
