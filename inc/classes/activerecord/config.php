<?php

define('AR_ADAPTER', 'MySQL'); // could be 'PDO'
define('AR_DRIVER',  'mysql');
define('AR_HOST',    '<your_host>');
define('AR_DB',      '<your_db>');
define('AR_USER',    '<your_dbuser>');
define('AR_PASS',    '<your_secret>');

#define('AR_PREFIX', 'prefix_');

/* used in generate.php to determine which tables we want models for
  remove or unset if all tables in a db are wanted */
$AR_TABLES = array(
  'records',
  'domains',
  'queue',
  'queue_item',
  'zones',
  'users',
);

?>
