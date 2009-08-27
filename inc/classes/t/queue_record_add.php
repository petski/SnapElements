<?php

if (PHP_SAPI !== 'cli') 
	die("Testscript may only run in CLI-mode");

set_include_path(get_include_path() . PATH_SEPARATOR . '..');


require_once('Services.php');
require_once('Queue.php');

#$str_json = '{"domain_name":"example.com","name":"bla.example.com","type":"A","content":"1.1.1.1","ttl":"86400","prio":""}';
$str_json = '{"domain_id":"1","name":"blaat.example.com","type":"A","content":"2.1.1.1","ttl":"86400","prio":""}';
$Obj_json = json_decode($str_json);

print "Output from Services: \n";
try {
	var_dump(Services::queue_record_add($Obj_json));
} catch (Exception $e) { 
	print $e->getMessage();
}
?>
