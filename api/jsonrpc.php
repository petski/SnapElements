<?php
require_once(dirname(__FILE__) .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'base.php');
require_once($class_root . 'Services.php');

header('Content-Type: application/json');

$service = new Services;
$method = $_POST['method'];

$params = json_decode(stripslashes($_POST['params'])) ? json_decode(stripslashes($_POST['params'])) : stripslashes($_POST['params']);

if(method_exists($service, $_POST['method'])) { 
	try {
		$output = array(
				'jsonrpc' => '2.0',
				'result' => $service->$method($params),
				'id' => $_POST['id'],
		 );
	} catch (Exception $e) {
		$output = array(
				'jsonrpc' => '2.0',
				'error' => array('code' => $e->getCode(), 'message' => $e->getMessage()),
				'id' => $_POST['id'],
		);
	}
} 
else {
	$output = array(
			'jsonrpc' => '2.0',
			'error' => array('code' => -32601, 'message' => 'Procedure not found.'),
			'id' => $_POST['id'],
	);
}

print json_encode($output);

?>
