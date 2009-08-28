<?php

if (PHP_SAPI !== 'cli') 
	die("Testscript may only run in CLI-mode");

set_include_path(get_include_path() . PATH_SEPARATOR . '..');


require_once('Services.php');

$service = new Services();

$queueID = 25;

print "Output from Services: \n";
try {
	var_dump($service->queue_entry_commit($queueID));
} catch (Exception $e) { 
	print $e->getMessage();
}
?>
