<?php
require '_mod.php';

// check permission
validateRank(User::RANK_ADMIN);

$threadId = (int)getPost('threadId');

$thread = Threads::load($threadId);

if(!$thread) {
  Notice::error('Could not find the thread you want to close!!!!');
  exit;
}

Threads::close($threadId);

Notice::success('You closed a thread! Well done!');

$data = array(
  'threadId' => $threadId,
  'success' => true,
);

view($data);