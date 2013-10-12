<?php
require '../_.php';
$Page = new Page();

$forumId = (int)(isset($_GET['forumId']) ? $_GET['forumId'] : 0);

$forums = Forums::getForumAndSubforums($forumId);

$threads = Threads::loadFromForum($forumId);
$additionalThreadIds = eachField($forums, 'lastPostThreadId');
if (count($additionalThreadIds) != 0) {
  $additionalThreads = Threads::loadIds($additionalThreadIds);
  foreach($additionalThreads as $id => $thread) {
    $threads[$id] = $thread;
  }
}

$userIds = eachField($forums, 'lastPostUserId');

$userIds = array_merge($userIds, eachField($threads, 'userId'));
$userIds = array_merge($userIds, eachField($threads, 'lastPostUserId'));

$users = User::loadIds($userIds);




$data['forums'] = $forums;
$data['forumId'] = $forumId;
$data['users'] = $users;
$data['threads'] = $threads;

if (!isset($forums[$forumId])) {
  Notice::error('Can\'t find that forum!');
  exit;
}

$Page->header($forums[$forumId]['name']);
view($data);