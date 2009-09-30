<?php

if (PHP_SAPI !== 'cli') 
	die("Testscript may only run in CLI-mode");

set_include_path(get_include_path() . PATH_SEPARATOR . '..');

require_once('Domain.php');

if(1) { 
	$d = Domain::find(1);
	print_r($d->record_count());
}

if(0) {
	$startchars = Domain::domain_start_chars();
	print_r($startchars);
}

if(0) {
	$startchars = Domain::reverse_start_chars();
	print_r($startchars);
}

?>
