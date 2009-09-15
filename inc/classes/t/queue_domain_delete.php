<?php

if (PHP_SAPI !== 'cli') 
	die("Testscript may only run in CLI-mode");

set_include_path(get_include_path() . PATH_SEPARATOR . '..');


require_once('Services.php');

$services = new Services();
$str_json = '{"id": "5", "name":"example.com", "type": "NATIVE"}';
$Obj_json = json_decode($str_json);

print "Output from Services: \n";
try {
	var_dump($services->queue_domain_delete($Obj_json));
} catch (Exception $e) { 
	print $e->getMessage();
}
?>
