<?php

if (PHP_SAPI !== 'cli') 
	die("Testscript may only run in CLI-mode");

set_include_path(get_include_path() . PATH_SEPARATOR . '..');


require_once('Service.php');

#$str_json = '{"domain_name":"bla.com","name":"bla.com","type":"A","content":"1.1.1.1","ttl":"86400","prio":""}';
$str_json = '{"domain_id":"3","name":"bla.com","type":"AA","content":"1.1.1.1","ttl":"86400","prio":""}';
$Obj_json = json_decode($str_json);
$data = json_encode($Obj_json);

#print $str_json->domain_name;
print $Obj_json->domain_name;
print "\n";
var_dump($data);
print "\n";
var_dump($Obj_json);
print "\n";

print "Output from JSONRPC: \n";
#var_dump(JSONRPC::record_add($Obj_json));
try {
	var_dump(JSONRPC::queue_record_add($Obj_json));
} catch (Exception $e) { 
	print $e->getMessage();
}
?>
