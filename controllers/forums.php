<?php
require '../_.php';
$Page = new Page();

$forumId = (int)(isset($_GET['forumId']) ? $_GET['forumId'] : 0);

// get the forum and it's subforums
$query = '
  SELECT f.id, f.name, f.description, f.postCount, f.threadCount, f.visibleRank, f.lastPostUserId, f.lastPostTimestamp
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


$userIds = eachField($forums, 'lastPostUserId');

$users = User::loadIds($userIds);

$data['forums'] = $forums;
$data['forumId'] = $forumId;
$data['users'] = $users;

if (!isset($forums[$forumId])) {
  Notice::error('Can\'t find that forum!');
  exit;
}

$Page->header($forums[$forumId]['name']);
view($data);