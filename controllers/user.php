<?php
require '../_.php';
$Page = new Page('forums');

$userId = (int) getGet('userId');


$user = User::getId($userId);
$Page->header($user['name'] . '\'s profile');

//var_dump($user);

$data = array(
  'user' => $user,
);

view($data);

