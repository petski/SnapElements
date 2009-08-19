<?php

define('AR_ADAPTER', 'MySQL'); // could be 'PDO'
define('AR_DRIVER',  'mysql');
define('AR_HOST',    '127.0.0.1');
define('AR_DB',      'pdns_ext');
define('AR_USER',    'poweradmin');
define('AR_PASS',    'KJjdmsk767&^2jddgjhsdf');

#define('AR_PREFIX', 'prefix_');

/* used in generate.php to determine which tables we want models for
  remove or unset if all tables in a db are wanted */
$AR_TABLES = array(
  'records',
  'domains',
  'queue',
  'zones',
  'users',
);

?>
