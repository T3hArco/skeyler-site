<?php

$s = microtime(1);

function assertCallback(){
  echo 'YO! Check _.php:15+';
}

assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_BAIL, 1);
assert_options(ASSERT_CALLBACK, 'assertCallback');

// exit early if some PHP configs are broke!

assert("!function_exists('get_magic_quotes_gpc') || !get_magic_quotes_gpc()"); // YO! turn off get magic quotes
assert("!ini_get('register_globals');"); // YO! turn off register globals!
assert("function_exists('bc_add');"); // YO! enable/install BC Math

ini_set('max_execution_time', 60);

//loads config data
require 'inc/config.inc.php';

//error_reporting(E_ALL);
ini_set('display_errors', $Config['displayErrors']);
date_default_timezone_set($Config['tz']);

define('LF', "\n");
define('CRLF', "\n");
define('BR', '<br />' . LF);
define('CLR', '<br class="clr" />');
define('HR', '<hr />' . BR);
define('ROOT', dirname(__FILE__).'/');

// session stuff here

$startTime = microtime(1);
$now       = time();

//loads base classes
require 'inc/db.inc.php';
require 'inc/funcs.inc.php';
require 'inc/page.inc.php';

//connects to the db. If you don't do a try-catch it will literally echo the password on error
try {
  $dsn = $Config['db']['lang'] . ':dbname=' . $Config['db']['dbName'] . ';host=' . $Config['db']['host'];
  $DB = new DB($dsn, $Config['db']['user'], $Config['db']['pass']);
} catch(Exception $e) {
  header('HTTP/1.1 500 Internal Server Error');
  if(!$isJson) {
    echo 'Failed to connect to database. Oh no!';
  } else {
    $err = array(
      'status' => 'failed',
      'message' => 'Database connection failed',
    );
    echo json_encode($err);
  }
  exit;
}

// pageId is gonna be the most common $_GET, so might as well put it here
$pageId = max(
  0,
  (int)filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_SCALAR),
  (int)filter_input(INPUT_POST, 'page', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_SCALAR)
);


