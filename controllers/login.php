<?php
require '../_.php';
require '../inc/lightopenid.inc.php';

global $Config;
$Page = new Page();
$Page->title = 'Login';

// on log in page, it must do this stuff. In the response, you need to check $openid->validate();
// if it's true, then they need to have their account cookie thing created; this openid thing will not create a cookie.
// $openid->identity contains the string http://steamcommunity.com/openid/id/76561198010087850, so use:
// preg_match('#^http:\/\/steamcommunity\.com\/openid\/id\/\d{17}$#', $openid->identity, $matches);

try {
  if (!isset($_GET['openid_mode'])) {
    if (isset($_GET['login'])) {
      $openid = new LightOpenID($Config['server']);
      $openid->identity = 'http://steamcommunity.com/openid';
      header('Location: ' . $openid->authUrl());
    }
    $Page->header();
    view(array('loginButton' => true));
  } elseif ($_GET['openid_mode'] == 'cancel') {
    view(array('err' => 'User has canceled authentication!'));
  } else {
    $openid = new LightOpenID($Config['server']);
    if ($openid->validate()) {
      if (!preg_match('#^http:\/\/steamcommunity\.com\/openid\/id\/(\d{17})$#', $openid->identity, $match)) {
        view(array('err' => 'There was a problem authenticating you. Please try again later.'));
      }
      $steamId64 = $match[1];
      $steamUserId = User::login($steamId64);

      $steamUser = User::load($steamUserId);

      if (!$steamUser) {
        echo('There was a problem authenticating you. Please try again later.');
        exit;
      }
      $_SESSION['userId'] = $steamUser['id'];

      //sets extended login cookies
      setcookie('userId', $steamUser['id'], $now + 60 * 60 * 24 * 365);
      setcookie('authKey', sha2($steamUser['authKey']), $now + 60 * 60 * 24 * 365);

      //marks this page as logged in
      $User = $steamUser;
      $isLoggedIn = true;

      // redirect to index so the user doesn't have their openid info in the url
      // (some people will paste this link into websites and then googlebot will log in as them)
      header('Location: ' . $Config['server'] . '/forums/');
      exit;
    }
  }
} catch (ErrorException $e) {
  $Page->header();
  echo $e->getMessage();
}