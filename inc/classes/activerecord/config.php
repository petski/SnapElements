<?php

require_once(dirname(__FILE__) .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'base.php');

define('AR_ADAPTER', 'MySQL'); // could be 'PDO'
define('AR_DRIVER',  'mysql');
define('AR_HOST',    $config->get('db.host'));
define('AR_DB',      $config->get('db.name'));
define('AR_USER',    $config->get('db.user'));
define('AR_PASS',    $config->get('db.pass'));

#define('AR_PREFIX', 'prefix_');

/* used in generate.php to determine which tables we want models for
  remove or unset if all tables in a db are wanted */
$AR_TABLES = array(
  'records',
  'domains',
  'queue',
  'queue_item_domains',
  'queue_item_records',
  'zones',
  'users',
);

?>
