<?php
require '../_.php';
$Page = new Page('forum');
$Page->header('Search');

$userId = (int)getGet('userId');

if($userId) {
  $posts = User::getPostsByUser($userId);
  $data['posts'] = $posts;
  view($data);
  return;
}

