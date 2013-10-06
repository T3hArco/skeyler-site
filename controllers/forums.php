<?php
require '../_.php';
$Page = new Page();

$forumId = (int)(isset($_GET['forumId']) ? $_GET['forumId'] : 0);

// get the forum and it's subforums
$query = '
  SELECT f.id, f.name, f.description, f.postCount, f.threadCount, f.visibleRank, f.lastPostUserId, f.lastPostTimestamp, f.lastPostThreadId
  FROM forums AS f
  LEFT JOIN forum_parents AS fp
    ON fp.forumId = f.id
  WHERE (fp.parentId = :forumId OR f.id = :forumId) AND f.visibleRank <= :rank
  ;
';

$binds = array(
  'forumId' => $forumId,
  'rank' => $User['rank'],
);
$forums = populateIds($DB->q($query, $binds)->fetchAll());

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