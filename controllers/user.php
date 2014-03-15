<?php
require '../_.php';
$Page = new Page('forums');

$userId = (int) getGet('userId');


$user = User::getId($userId);

if(!$user) {
  Notice::error('That user does not exist! They may be a ghost! oOOooO0Ooo0OOOoo!');
  exit;
}

$Page->header($user['name'] . '\'s profile');

$data = array(
  'user' => $user,
);

view($data);

