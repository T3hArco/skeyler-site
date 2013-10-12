<?php
require '../_.php';
$Page = new Page();

$forumId = (int)(isset($_GET['forumId']) ? $_GET['forumId'] : 0);

$forums = Forums::getForumAndSubforums($forumId);

if (!isset($forums[$forumId])) {
  Notice::error('Can\'t find that forum!');
  exit;
}

// marks the forum as seen at the current time by the current user
Forums::updateLastSeen($forumId);

$forumIds = eachField($forums, 'id');
$lastReadForumTimestamps = Forums::getLastSeen($forumIds);

$threads = Threads::loadFromForum($forumId);
$additionalThreadIds = eachField($forums, 'lastPostThreadId');
if (count($additionalThreadIds) != 0) {
  $additionalThreads = Threads::loadIds($additionalThreadIds);
  foreach ($additionalThreads as $id => $thread) {
    $threads[$id] = $thread;
  }
}

$threadIds = eachField($threads, 'id');
$lastReadThreadPostCounts = Threads::getLastReads($threadIds);

$userIds = eachField($forums, 'lastPostUserId');

$userIds = array_merge($userIds, eachField($threads, 'userId'));
$userIds = array_merge($userIds, eachField($threads, 'lastPostUserId'));

$users = User::loadIds($userIds);


$data['forums'] = $forums;
$data['forumId'] = $forumId;
$data['users'] = $users;
$data['threads'] = $threads;
$data['lastReadForumTimestamps'] = $lastReadForumTimestamps;
$data['lastReadThreadPostCounts'] = $lastReadThreadPostCounts;


$Page->header($forums[$forumId]['name']);
view($data);