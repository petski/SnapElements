<?php

require_once("../Service.php");
$jsonrpc = new Service;
$p = array('name' => 'poef.nl', 'type' => 'NATIVE');
$jsonrpc->queue_domain_add((object)$p);

?>
