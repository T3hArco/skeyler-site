<?php
require '_mod.php';

// check permission
validateRank(User::RANK_ADMIN);

$threadId = (int)getPost('threadId');
$forumId = (int)getPost('forumId');

$thread = Threads::load($threadId);

if(!$thread) {
  Notice::error('Could not find the thread you want to move!!!!');
  exit;
}

$forum = Forums::load($forumId);

if(!$forum || !$forumId) {
  Notice::error('Could not find the forum you want to move the thread to!!!');
  exit;
}

Threads::move($threadId, $forumId);

Notice::success('You moved a thread! Well done!');

$data = array(
  'threadId' => $threadId,
  'forumId' => $forumId,
  'success' => true,
);

view($data);