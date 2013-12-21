<?php
require '../_.php';
$Page = new Page('forums');

$forumId = (int)getGet('forumId');

$forums = Forums::getForumAndSubforums($forumId);

if (!isset($forums[$forumId])) {
  Notice::error('Can\'t find that forum or you do not have permission to view it!');
  exit;
}

$local['breadcrumbs'] = array(
  'Forums' => '/forums.php',
  $forums[$forumId]['name'] => '/forums.php?forumId=' . $forumId,
);

if ($forumId == 0) {
  array_pop($local['breadcrumbs']);
}

// marks the forum as seen at the current time by the current user
Forums::updateLastSeen($forumId);

$forumIds = eachField($forums, 'id');
$lastReadForumTimestamps = Forums::getLastSeen($forumIds);

$threads = Threads::loadFromForum($forumId);
$additionalThreadIds = eachField($forums, 'lastPostThreadId');
$allThreads = $threads;

if (count($additionalThreadIds) != 0) {
  $additionalThreads = Threads::loadIds($additionalThreadIds);
  foreach ($additionalThreads as $id => $thread) {
    $allThreads[$id] = $thread;
  }
}

$threadIds = eachField($allThreads, 'id');

$lastReadThreadPostCounts = Threads::getLastReads($threadIds);

$userIds = eachField($forums, 'lastPostUserId');

$userIds = array_merge($userIds, eachField($allThreads, 'userId'));
$userIds = array_merge($userIds, eachField($allThreads, 'lastPostUserId'));

$users = User::loadIds($userIds);

$subforumsTemp = Forums::getSubforumsFromForumList(eachField($forums, 'id'));
$subforums = array();
foreach ($subforumsTemp as $forum) {
  if (!isset($subforums[$forum['parentId']])) {
    $subforums[$forum['parentId']] = array();
  }
  $subforums[$forum['parentId']][] = $forum;
}

$data['forums'] = $forums;
$data['forumId'] = $forumId;
$data['users'] = $users;
$data['threads'] = $threads;
$data['lastReadForumTimestamps'] = $lastReadForumTimestamps;
$data['lastReadThreadPostCounts'] = $lastReadThreadPostCounts;
$data['subforums'] = $subforums;

$Page->header($forums[$forumId]['name']);
view($data);