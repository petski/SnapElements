<?php

require_once("../JSONRPC.php");
$jsonrpc = new JSONRPC;
$p = array('name' => 'poef.nl', 'type' => 'NATIVE');
$jsonrpc->queue_domain_add((object)$p);

?>
