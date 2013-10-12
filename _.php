<?php

$s = microtime(1);

function assertCallback()
{
  echo 'YO! Check _.php:15+';
}

assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_BAIL, 1);
assert_options(ASSERT_CALLBACK, 'assertCallback');

// exit early if some PHP configs are broke!

assert("!function_exists('get_magic_quotes_gpc') || !get_magic_quotes_gpc()"); // YO! turn off get magic quotes
assert("!ini_get('register_globals');"); // YO! turn off register globals!
assert("function_exists('bcadd');"); // YO! enable/install BC Math

ini_set('max_execution_time', 60);

session_start();

//loads config data
require 'inc/config.inc.php';

//error_reporting(E_ALL);
ini_set('display_errors', $Config['displayErrors']);
//date_default_timezone_set($Config['tz']);

define('LF', "\n");
define('CRLF', "\n");
define('BR', '<br />' . LF);
define('CLR', '<br class="clr" />');
define('HR', '<hr />' . BR);
define('ROOT', realpath(dirname(__FILE__)));

// session stuff here

$startTime = microtime(true);
$now = time();

//loads base classes
require 'inc/notice.inc.php';
require 'inc/db.inc.php';
require 'inc/funcs.inc.php';
require 'inc/page.inc.php';
require 'inc/steam.inc.php';
require 'inc/user.inc.php';
require 'inc/forums.inc.php';
require 'inc/threads.inc.php';
require 'inc/posts.inc.php';

//connects to the db. If you don't do a try-catch it will literally echo the password on error
try {
  $dsn = $Config['db']['lang'] . ':dbname=' . $Config['db']['dbName'] . ';host=' . $Config['db']['host'];
  $DB = new DB($dsn, $Config['db']['user'], $Config['db']['pass']);
} catch (Exception $e) {
  header('HTTP/1.1 500 Internal Server Error');
  if (!isset($isJson) || !$isJson) {
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
  1,
  (int)filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_SCALAR),
  (int)filter_input(INPUT_POST, 'page', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_SCALAR)
);

$controllerName = $_SERVER['SCRIPT_NAME'];

// variables get passed to the view in the $local variable.
$local = array();

// init the $User
$isLoggedIn = false;
$User = array(
  'id' => 0,
  'steamId64' => 0,
  'name' => 'Guest',
  'rank' => -1,
  'money' => 0,
  'avatarUrl' => '',
);

// if there's already a session for the user, log them in
if (isset($_SESSION['userId']) && $_SESSION['userId']) {
  $User = User::load($_SESSION['userId']);
} else if (isset($_COOKIE['userId'], $_COOKIE['authKey'])) {
  //if there isnt a session, but they have an auth key, then tries to build their session from that
  $cookieInfo = array(
    'userId' => filter_input(INPUT_COOKIE, 'userId', FILTER_DEFAULT, FILTER_REQUIRE_SCALAR),
    'authKey' => filter_input(INPUT_COOKIE, 'authKey', FILTER_DEFAULT, FILTER_REQUIRE_SCALAR),
  );
  $query = 'SELECT id, authKey FROM users WHERE id = ? LIMIT 1;';
  $keyInfo = $DB->q($query, $cookieInfo['userId'])->fetch();
  if (
    $keyInfo
    && sha2($keyInfo['authKey']) === $cookieInfo['authKey']
    && $cookieInfo['authKey']
    && $keyInfo['authKey']
    && $keyInfo['id'] == $cookieInfo['userId']
  ) {
    $_SESSION['userId'] = $keyInfo['id'];
    $User = User::load($_SESSION['userId']);
  }
  unset($cookieInfo, $keyInfo);
}

$isLoggedIn = (!!$User['id']);


