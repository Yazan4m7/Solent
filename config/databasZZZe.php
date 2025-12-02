<?php return array (
  'default' => 'mysql',
  'connections' =>
  array (
    'sqlite' =>
    array (
      'driver' => 'sqlite',
      'url' => NULL,
      'database' => 'alrazi
',
      'prefix' => '',
      'foreign_key_constraints' => true,
    ),
    'mysql' =>
    array (
      'driver' => 'mysql',
      'url' => NULL,
      'host' => '127.0.0.1',
      'port' => '3306',
      'database' => 'alrazi',
      'username' => 'yazan',
      'password' => 'Y@azan777',
      'unix_socket' => '',
      'charset' => 'utf8mb4',
      'collation' => 'utf8mb4_unicode_ci',
      'prefix' => '',
      'prefix_indexes' => true,
      'strict' => false,
      'engine' => NULL,
      'options' =>
      array (
      ),
    ),
    'pgsql' =>
    array (
      'driver' => 'pgsql',
      'url' => NULL,
      'host' => '127.0.0.1',
      'port' => '3306',
      'database' => 'Tect
',
      'username' => 'root',
      'password' => '',
      'charset' => 'utf8',
      'prefix' => '',
      'prefix_indexes' => true,
      'schema' => 'public',
      'sslmode' => 'prefer',
    ),
    'sqlsrv' =>
    array (
      'driver' => 'sqlsrv',
      'url' => NULL,
      'host' => '127.0.0.1',
      'port' => '3306',
      'database' => 'Tect
',
      'username' => 'root',
      'password' => '',
      'charset' => 'utf8',
      'prefix' => '',
      'prefix_indexes' => true,
    ),
  ),
  'migrations' => 'migrations',
  'redis' =>
  array (
    'client' => 'phpredis',
    'options' =>
    array (
      'cluster' => 'redis',
      'prefix' => 'laravel_database_',
    ),
    'default' =>
    array (
      'url' => NULL,
      'host' => '127.0.0.1',
      'password' => NULL,
      'port' => '6379',
      'database' => '0',
    ),
    'cache' =>
    array (
      'url' => NULL,
      'host' => '127.0.0.1',
      'password' => NULL,
      'port' => '6379',
      'database' => '1',
    ),
  ),
);
