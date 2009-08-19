<?php

$class_root = dirname(__FILE__) .DIRECTORY_SEPARATOR. 'inc' .DIRECTORY_SEPARATOR. 'classes' .DIRECTORY_SEPARATOR;

require_once($class_root . 'Config.php');
require_once($class_root . 'Display.php');

require_once(dirname(__FILE__) .DIRECTORY_SEPARATOR. 'config.php');

$config = new Config($settings);
$display = new Display(array('config' => $config));

?>
