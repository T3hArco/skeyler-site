<?php
require '_mod.php';

// check permission
validateRank(User::RANK_ADMIN);

$threadId = (int)getPost('threadId');

$thread = Threads::load($threadId);

if(!$thread) {
  Notice::error('Could not find the thread you want to open!!!!');
  exit;
}

Threads::open($threadId);

Notice::success('You opened a thread! Well done!');

$data = array(
  'threadId' => $threadId,
  'success' => true,
);

view($data);