<?php
require '_mod.php';

// check permission
validateRank(User::RANK_ADMIN);

$threadId = (int)getPost('threadId');
$forumId = -1; // move it to the deleted forum

$thread = Threads::load($threadId);

if(!$thread) {
  Notice::error('Could not find the thread you want to move!!!!');
  exit;
}

$forum = Forums::load($forumId);

if(!$forum) {
  Notice::error('The deleted forum is screwed up! Tell a developer that forumId -1 is missing!!!');
  exit;
}

Threads::move($threadId, $forumId);

Notice::success('You deleted a thread! Well done!');

$data = array(
  'threadId' => $threadId,
  'success' => true,
);

view($data);