<?php
require '../_.php';
$Page = new Page();

$forumId = (int)(isset($_GET['forumId']) ? $_GET['forumId'] : 0);

// get the forum and it's subforums
$query = '
  SELECT f.id, f.name, f.description, f.postCount, f.threadCount, f.visibleRank
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

var_dump($forums);

