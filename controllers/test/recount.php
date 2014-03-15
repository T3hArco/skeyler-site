<?php
require '_test.php';
$Page = new Page();
$Page->header('Recount threads or something');

$forums = Forums::getAllVisibleForums();
foreach ($forums as $forum) {
  Forums::recountCounts($forum['id']);
  Notice::success('Wowza! Fixed up counts on forum: <strong>' . ent($forum['name']) . '</strong>!!');
}

Threads::recountAll();
Notice::success('Damn, son, you crazy. All threads have been recounted');

view(array(), '/test/recount.php');