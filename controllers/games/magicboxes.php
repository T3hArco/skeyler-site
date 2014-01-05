<?php
require '../../_.php';
$Page = new Page('forums', 'games');
$Page->header('Magic Boxes');

$data = array();

$boxes = array();

for ($i = 1; $i <= 100; $i++) {
  $boxes[$i] = 0;
}

$boxes[11] = 12;
$boxes[7] = 12;
$boxes[24] = 12;

$userIds = array_unique($boxes);

$user = User::loadIds($userIds);

$data['canUseBox'] = true;
$data['boxes'] = $boxes;
$data['users'] = $user;

view($data);