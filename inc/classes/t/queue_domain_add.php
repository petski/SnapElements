<?php

if (PHP_SAPI !== 'cli') 
	die("Testscript may only run in CLI-mode");

set_include_path(get_include_path() . PATH_SEPARATOR . '..');


require_once('Services.php');

$str_json = '{"name":"example.com","type":"NATIVE"}';
$Obj_json = json_decode($str_json);

print "Output from Services: \n";
try {
	var_dump(Services::queue_domain_add($Obj_json));
} catch (Exception $e) { 
	print $e->getMessage();
}
?>
