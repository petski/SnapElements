<?php
require_once(dirname(__FILE__) .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'base.php');
require_once($class_root . 'JSONRPC.php');

header('Content-Type: application/json');

$jsonrpc = new JSONRPC;
$method = $_POST['method'];

$params = json_decode(stripslashes($_POST['params'])) ? json_decode(stripslashes($_POST['params'])) : stripslashes($_POST['params']);

if(method_exists($jsonrpc, $_POST['method'])) { 
	$output = array(
			'jsonrpc' => '2.0',
			'result' => $jsonrpc->$method($params),
			'id' => $_POST['id'],
		  );
} 

print json_encode($output);

?>
